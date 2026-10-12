<?php

namespace Tests\Feature;

use App\Models\AdminUserModel;
use App\Models\CompetitionModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @internal
 */
final class AdminTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';

    private const PASSWORD   = 'ett-langt-losenord-123';
    private const CSRF_TOKEN = 'testtoken0123456789abcdef01234567';

    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userId = (int) model(AdminUserModel::class)->insert([
            'email'         => 'admin@swinggolf.se',
            'name'          => 'Testadmin',
            'password_hash' => password_hash(self::PASSWORD, PASSWORD_DEFAULT),
        ]);

        // Delade tjänster får inte bära state mellan testerna: CSRF-hashen läses
        // in när Security skapas, och throttlern räknar inloggningsförsök.
        Services::resetSingle('security');
        Services::resetSingle('throttler');
    }

    /** Session som en inloggad admin + CSRF-token (sessionsbaserad CSRF). */
    private function loggedIn(): self
    {
        return $this->withSession([
            'admin_user_id'   => $this->userId,
            'admin_user_name' => 'Testadmin',
            'admin_last_seen' => time(),
            'csrf_swing'      => self::CSRF_TOKEN,
        ]);
    }

    private function guest(): self
    {
        return $this->withSession(['csrf_swing' => self::CSRF_TOKEN]);
    }

    public function testAdminRequiresLogin(): void
    {
        $result = $this->withSession([])->get('admin/');

        $result->assertRedirectTo(site_url('admin/login/'));
    }

    public function testAdminPagesAreNotCacheableOrIndexable(): void
    {
        $result = $this->loggedIn()->get('admin/');

        $result->assertOK();
        $result->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertStringContainsString('no-store', $result->response()->getHeaderLine('Cache-Control'));
        $result->assertSee('Översikt');
    }

    public function testLoginPageRenders(): void
    {
        $result = $this->withSession([])->get('admin/login/');

        $result->assertOK();
        $result->assertSee('Logga in');
    }

    /**
     * 404-sidan renderas av den publika sajten — ersätt den här så att testet
     * bara mäter att setup nekas.
     */
    private function plain404(): void
    {
        service('routes')->loadRoutes()->set404Override(static fn (): string => 'Sidan finns inte');
    }

    public function testSetupWithWrongTokenIsNotFound(): void
    {
        config('Admin')->setupToken = str_repeat('a', 32);
        $this->plain404();

        $this->withSession([])->get('admin/setup/?token=fel-token')->assertStatus(404);
    }

    public function testSetupIsDisabledWithoutToken(): void
    {
        config('Admin')->setupToken = '';
        $this->plain404();

        $this->withSession([])->get('admin/setup/?token=')->assertStatus(404);
    }

    public function testSetupWithValidTokenRuns(): void
    {
        $token                      = str_repeat('b', 32);
        config('Admin')->setupToken = $token;

        $result = $this->guest()->get('admin/setup/?token=' . $token);
        $result->assertOK();
        $result->assertSee('Kör migrationer');
        // Användaren finns redan — formuläret för första användaren ska inte visas.
        $result->assertDontSee('Upprepa lösenord');
    }

    public function testLoginWithCorrectPassword(): void
    {
        $result = $this->guest()->post('admin/login/', [
            'csrf_swing' => self::CSRF_TOKEN,
            'email'      => 'Admin@Swinggolf.se',
            'password'   => self::PASSWORD,
        ]);

        $result->assertRedirectTo(site_url('admin/'));
        $result->assertSessionHas('admin_user_id', $this->userId);
        $this->assertNotNull(model(AdminUserModel::class)->find($this->userId)['last_login_at']);
    }

    public function testLoginWithWrongPasswordShowsGenericError(): void
    {
        $result = $this->guest()->post('admin/login/', [
            'csrf_swing' => self::CSRF_TOKEN,
            'email'      => 'admin@swinggolf.se',
            'password'   => 'fel-losenord-123',
        ]);

        $result->assertOK();
        $result->assertSee('Fel e-postadress eller lösenord.');
        $result->assertSessionMissing('admin_user_id');
    }

    public function testPostWithoutCsrfTokenIsRejected(): void
    {
        $result = $this->guest()->post('admin/login/', [
            'email'    => 'admin@swinggolf.se',
            'password' => self::PASSWORD,
        ]);

        $result->assertSessionMissing('admin_user_id');
        $this->assertTrue($result->isRedirect());
    }

    public function testCreateCompetitionSavesRowAndClearsPageCache(): void
    {
        cache()->save('sidcache-test', 'html', 300);

        $result = $this->loggedIn()->post('admin/tavlingar/ny/', [
            'csrf_swing'        => self::CSRF_TOKEN,
            'title'             => 'Veckogolfen Vår 2027',
            'slug'              => '',
            'visibility'        => 'public',
            'status'            => 'published',
            'format'            => 'Nettoslag, TrackMan-handicap',
            'starts_at'         => '2027-01-11T08:00',
            'ends_at'           => '',
            'registration_url'  => 'https://example.org/anmalan',
        ]);

        $result->assertRedirectTo(site_url('admin/tavlingar/'));

        $row = model(CompetitionModel::class)->where('slug', 'veckogolfen-var-2027')->first();
        $this->assertNotNull($row);
        $this->assertSame('2027-01-11 08:00:00', $row['starts_at']);
        $this->assertNull($row['ends_at']);
        $this->assertNull(cache('sidcache-test'));
    }

    public function testUpdateCompetitionKeepsOwnSlug(): void
    {
        $id = (int) model(CompetitionModel::class)->insert([
            'title' => 'Juniortouren', 'slug' => 'juniortouren', 'visibility' => 'public', 'status' => 'draft',
        ]);

        $result = $this->loggedIn()->post("admin/tavlingar/{$id}/", [
            'csrf_swing' => self::CSRF_TOKEN,
            'title'      => 'Juniortouren omgång 2',
            'slug'       => 'juniortouren',
            'visibility' => 'public',
            'status'     => 'published',
        ]);

        $result->assertRedirectTo(site_url('admin/tavlingar/'));
        $this->assertSame('Juniortouren omgång 2', model(CompetitionModel::class)->find($id)['title']);
    }

    public function testInvalidCompetitionShowsErrors(): void
    {
        $result = $this->loggedIn()->post('admin/tavlingar/ny/', [
            'csrf_swing'       => self::CSRF_TOKEN,
            'title'            => '',
            'visibility'       => 'public',
            'status'           => 'published',
            'registration_url' => 'http://osaker.example',
        ]);

        $result->assertOK();
        $result->assertSee('Formuläret innehåller fel');
        $this->assertSame(0, model(CompetitionModel::class)->countAllResults());
    }

    public function testDeleteCompetition(): void
    {
        $id = (int) model(CompetitionModel::class)->insert([
            'title' => 'Radera mig', 'slug' => 'radera-mig', 'visibility' => 'public', 'status' => 'draft',
        ]);

        $this->loggedIn()->post("admin/tavlingar/{$id}/radera/", ['csrf_swing' => self::CSRF_TOKEN])
            ->assertRedirectTo(site_url('admin/tavlingar/'));

        $this->assertNull(model(CompetitionModel::class)->find($id));
    }

    public function testPublishedCampaignRequiresEndDate(): void
    {
        $result = $this->loggedIn()->post('admin/kampanjer/ny/', [
            'csrf_swing'   => self::CSRF_TOKEN,
            'title'        => 'Oktober-special',
            'pay_amount'   => '2500',
            'value_amount' => '4000',
            'valid_until'  => '',
            'status'       => 'published',
        ]);

        $result->assertOK();
        $result->assertSee('En publicerad kampanj måste ha ett slutdatum.');
    }

    public function testCreateCampaign(): void
    {
        $result = $this->loggedIn()->post('admin/kampanjer/ny/', [
            'csrf_swing'   => self::CSRF_TOKEN,
            'title'        => 'Oktober-special',
            'pay_amount'   => '2500',
            'value_amount' => '4000',
            'valid_from'   => '2026-10-01',
            'valid_until'  => '2026-10-31',
            'status'       => 'published',
            'show_on_home' => '1',
        ]);

        $result->assertRedirectTo(site_url('admin/kampanjer/'));
        $this->seeInDatabase('campaigns', ['title' => 'Oktober-special', 'show_on_home' => 1, 'pay_amount' => 2500]);
    }
}
