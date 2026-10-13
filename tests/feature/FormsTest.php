<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;

/**
 * Offertförfrågan och beställning (TECH-04, TECH-06, AI-spec 4.2–4.3):
 * validering, spamskydd, lagring, vidarebefordran till swing-order och tacksidor.
 */
final class FormsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $namespace = 'App';

    private MockEmail $email;

    protected function setUp(): void
    {
        parent::setUp();

        $this->email = new MockEmail(config('Email'));
        Services::injectMock('email', $this->email);

        $orders               = config('Orders');
        $orders->enabled      = true;
        $orders->channel      = 'mail';
        $orders->mailTo       = 'order-test@swinggolf.se';
        $orders->swishNumber  = '123 217 2559';
        config('Inquiries')->notifyTo = 'info@swinggolf.se';
        config('Encryption')->key = str_repeat('k', 32);
        Services::resetSingle('encrypter');

        Services::resetSingle('orderGateway');
        Services::resetSingle('orderService');
        Services::resetSingle('inquiryService');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Factories::reset('config');
        Services::reset(true);
    }

    private function body($result): string
    {
        return $result->response()->getBody();
    }

    // ---------- förfrågningar ----------

    public function testValidContactInquiryIsStoredAndMailed(): void
    {
        $result = $this->post('kontakt', ['name' => 'Anna Andersson', 'email' => 'Anna@Exempel.se', 'message' => 'Hej! Har ni öppet på julafton?']);

        $result->assertRedirectTo(page_path('tack/forfragan') . '?typ=kontakt');
        $this->seeInDatabase('inquiries', ['type' => 'kontakt', 'email' => 'anna@exempel.se', 'status' => 'new']);
        $row = $this->db->table('inquiries')->get()->getRowArray();
        $this->assertNotNull($row['mailed_at']);
        $this->assertStringContainsString('Anna Andersson', $this->email->archive['subject']);
    }

    public function testCompanyInquiryStoresEventDetails(): void
    {
        $this->post('foretagsevent', [
            'name' => 'Erik', 'organisation' => 'Exempelbolaget AB', 'email' => 'erik@foretag.se',
            'participants' => '18', 'preferred_date' => 'fredag 14 november', 'package_interest' => 'after-work', 'wants_food' => '1',
        ])->assertRedirectTo(page_path('tack/forfragan') . '?typ=foretag');

        $this->seeInDatabase('inquiries', ['type' => 'foretag', 'participants' => 18, 'package_interest' => 'after-work', 'wants_food' => 1]);
    }

    public function testInvalidInquiryShowsErrorsOnSamePage(): void
    {
        $result = $this->post('kontakt', ['name' => '', 'email' => 'inte-en-adress', 'message' => '']);

        $result->assertStatus(422);
        $html = $this->body($result);
        $this->assertStringNotContainsString('form-alert', $html, 'ingen samlad felruta – felen står vid fälten');
        $this->assertMatchesRegularExpression('#id="f-name-err"[^>]*>.*Fyll i namn\.#s', $html);
        $this->assertMatchesRegularExpression('#id="f-email-err"[^>]*>.*Ange en giltig e-postadress\.#s', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('value="inte-en-adress"', $html, 'ifyllda värden behålls');
        $this->dontSeeInDatabase('inquiries', ['email' => 'inte-en-adress']);
    }

    public function testHoneypotSilentlyDropsSpam(): void
    {
        $this->post('kontakt', ['name' => 'Bot', 'email' => 'bot@spam.test', 'message' => 'Köp', 'webbplats' => 'http://spam.test'])
            ->assertRedirectTo(page_path('tack/forfragan'));

        $this->dontSeeInDatabase('inquiries', ['email' => 'bot@spam.test']);
    }

    public function testInquiryIsKeptWhenMailFails(): void
    {
        $this->email->returnValue = false;

        $this->post('kontakt', ['name' => 'Lisa', 'email' => 'lisa@exempel.se', 'message' => 'Hej'])
            ->assertRedirectTo(page_path('tack/forfragan') . '?typ=kontakt');

        $row = $this->db->table('inquiries')->where('email', 'lisa@exempel.se')->get()->getRowArray();
        $this->assertNull($row['mailed_at']);
        $this->assertNotNull($row['mail_error']);
    }

    public function testFieldAttributesDoNotLeakBetweenPartials(): void
    {
        // Regression: CI:s delade renderare lät fält ärva type/required från föregående fält.
        $html = $this->body($this->get('foretagsevent/'));

        $this->assertMatchesRegularExpression('#<input type="text" id="f-organisation" name="organisation" class="inp" autocomplete="organization"#', $html);
        $this->assertMatchesRegularExpression('#<input type="text" id="f-preferred_date"#', $html);
        $this->assertDoesNotMatchRegularExpression('#id="f-phone"[^>]*required#', $html);
        $this->assertMatchesRegularExpression('#id="f-email"[^>]*required#', $html);
    }

    // ---------- beställningar ----------

    public function testCreditPotOrderIsForwardedAndShowsSwishInstructions(): void
    {
        $result = $this->post('spelpotter', ['produkt' => 'pott-1000', 'name' => 'Anna Andersson', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $order = $this->db->table('orders')->get()->getRowArray();
        $this->assertNotNull($order);
        $result->assertRedirectTo(page_path('tack/bestallning/' . $order['public_token']));
        $this->assertSame('SPELPOTT', $order['order_type']);
        $this->assertSame(1000, (int) $order['pay_amount']);
        $this->assertSame(1150, (int) $order['value_amount']);
        $this->assertNotNull($order['forwarded_at'], 'vidarebefordrad till swing-order');

        $thanks = $this->get('tack/bestallning/' . $order['public_token'] . '/');
        $thanks->assertStatus(200);
        $html  = $this->body($thanks);
        $swish = 'https://app.swish.nu/1/p/sw/?sw=1232172559&amp;amt=1000.00&amp;msg=' . $order['reference'];
        $this->assertStringContainsString('href="' . $swish . '"', $html, 'Betala med Swish med allt ifyllt');
        $this->assertMatchesRegularExpression('#<div class="swish-qr-box"><svg role="img" aria-label="QR-kod för Swish#', $html);
        $this->assertMatchesRegularExpression('#<a class="btn btn-swish" href="[^"]+" data-same-tab><img src="/assets/swish/swish-icon\.svg\?v=\d+" alt=""[^>]*>Betala med Swish</a>#', $html);
        $this->assertStringContainsString('123 217 2559', $html);
        $this->assertStringContainsString("1\u{00A0}000\u{00A0}kr", $html);
        $this->assertStringContainsString($order['reference'], $html);
        $this->assertStringContainsString('noindex', $html);
    }

    public function testOrderMailMatchesSwingOrderFormat(): void
    {
        // Kunden får kvittot sist — fånga mejlen i ordning via händelsen som MockEmail utlöser.
        $sent = [];
        \CodeIgniter\Events\Events::on('email', static function (array $archive) use (&$sent): void { $sent[] = $archive; });

        $this->post('presentkort', ['belopp' => '300', 'mottagare' => 'Pappa', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $this->assertCount(2, $sent, 'ett mejl till swing-order och ett kvitto till kunden');
        $this->assertSame('Order', $sent[0]['subject']);
        $this->assertStringStartsWith("type:Presentkort\nprice:300\nemail:anna@exempel.se\nname:Anna\n", $sent[0]['body']);
        $this->assertStringContainsString("name on giftcard:Pappa\n", $sent[0]['body']);
        $this->assertStringContainsString('123 217 2559', $sent[1]['body']);
        $this->assertMatchesRegularExpression('#^https://app\.swish\.nu/1/p/sw/\?sw=1232172559&amt=300\.00&msg=W\d{4,}$#m', $sent[1]['body'], 'kunden kan öppna Swish från mejlet');
    }

    /**
     * Webbläsaren visar texterna i data-msg-* (site.js). De ska vara exakt
     * samma som serverns felmeddelanden för samma fel.
     */
    public function testBrowserValidationMessagesMatchServer(): void
    {
        $cases = [
            'kontakt' => [
                ['name' => '', 'email' => 'inte-en-adress', 'phone' => 'abc', 'message' => ''],
                ['id="f-name"' => 'required', 'id="f-email"' => 'email', 'id="f-phone"' => 'pattern', 'id="f-message"' => 'required'],
            ],
            'foretagsevent' => [
                ['name' => 'Anna', 'email' => 'anna@exempel.se', 'participants' => '900'],
                ['id="f-participants"' => 'range'],
            ],
            'friskvard' => [
                ['produkt' => '', 'name' => '', 'email' => '', 'personnummer' => '19811218-9875'],
                ['id="f-produkt"' => 'required', 'id="f-name"' => 'required', 'id="f-email"' => 'required', 'id="f-personnummer"' => 'pnr-checksum', 'id="f-villkor"' => 'required'],
            ],
            'presentkort' => [
                ['belopp' => '', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1'],
                ['id="f-belopp"' => 'required'],
            ],
        ];

        foreach ($cases as $slug => [$post, $fields]) {
            $page   = $this->body($this->get($slug . '/'));
            $server = $this->body($this->post($slug, $post));

            foreach ($fields as $element => $rule) {
                $this->assertMatchesRegularExpression('#' . preg_quote($element, '#') . '[^>]*data-msg-' . $rule . '="([^"]+)"#', $page, "{$slug}: {$element} saknar data-msg-{$rule}");
                preg_match('#' . preg_quote($element, '#') . '[^>]*data-msg-' . $rule . '="([^"]+)"#', $page, $m);
                $this->assertStringContainsString('</svg>' . $m[1] . '</p>', $server, "{$slug}: webbläsarens text för {$element} ({$rule}) skiljer sig från serverns");
            }
        }
    }

    /** Statistikmarkeringar på tacksidorna: typ och belopp, aldrig personuppgifter. */
    public function testThankYouPagesMarkConversionsWithoutPersonalData(): void
    {
        $lead = $this->body($this->get('tack/forfragan/?typ=foretag'));
        $this->assertStringContainsString('data-track-event="generate_lead" data-track-params="{&quot;formular&quot;:&quot;foretag&quot;}"', $lead);
        $this->assertStringContainsString('{&quot;formular&quot;:&quot;okand&quot;}', $this->body($this->get('tack/forfragan/?typ=<script>')));

        $this->post('spelpotter', ['produkt' => 'pott-2000', 'name' => 'Anna Andersson', 'email' => 'anna@exempel.se', 'villkor' => '1']);
        $order = $this->db->table('orders')->get()->getRowArray();
        $html  = $this->body($this->get('tack/bestallning/' . $order['public_token'] . '/'));
        $this->assertStringContainsString('data-track-event="bestallning"', $html);
        $this->assertStringContainsString('{&quot;produkt&quot;:&quot;spelpott&quot;,&quot;value&quot;:2000,&quot;currency&quot;:&quot;SEK&quot;}', $html);
        preg_match('#<div hidden data-track-event="bestallning"[^>]*>#', $html, $marker);
        foreach (['anna', $order['reference'], $order['public_token']] as $private) {
            $this->assertStringNotContainsStringIgnoringCase($private, $marker[0]);
        }
    }

    public function testTermsOpenInNewTabOnOrderPages(): void
    {
        foreach (['spelpotter/', 'friskvard/', 'presentkort/'] as $path) {
            $html = $this->body($this->get($path));
            $this->assertStringContainsString('<a href="/kopvillkor/" target="_blank" rel="noopener">köpvillkoren<span class="sr-only"> (öppnas i ny flik)</span></a>', $html, "{$path}: köpvillkoren ska öppnas i ny flik så att formuläret ligger kvar");
            $this->assertStringContainsString('<a href="/integritetspolicy/" target="_blank" rel="noopener">', $html, "{$path}: integritetspolicyn i ny flik");
        }
    }

    public function testOrderFormsPromiseCodeByEmail(): void
    {
        $this->assertStringContainsString('Spelpotten levereras till den här adressen.', $this->body($this->get('spelpotter/')));
        $this->assertStringContainsString('Friskvårdspotten och kvittot levereras till den här adressen.', $this->body($this->get('friskvard/')));

        foreach (['spelpotter/', 'friskvard/', 'kopvillkor/'] as $path) {
            $this->assertStringNotContainsString('kopplas till', $this->body($this->get($path)), "{$path}: potten levereras som kod, inte via e-postkoppling i Sweetspot");
        }
    }

    public function testOrderReferenceIsShortAndUnique(): void
    {
        $this->post('spelpotter', ['produkt' => 'pott-1000', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);
        $this->post('presentkort', ['belopp' => '300', 'name' => 'Erik', 'email' => 'erik@exempel.se', 'villkor' => '1']);

        $orders = $this->db->table('orders')->orderBy('id')->get()->getResultArray();
        $this->assertSame('W' . (1000 + (int) $orders[0]['id']), $orders[0]['reference'], 'kort referens att skriva i Swish');
        $this->assertMatchesRegularExpression('/^W\d{4,}$/', $orders[1]['reference']);
        $this->assertNotSame($orders[0]['reference'], $orders[1]['reference']);
        $this->assertStringContainsString($orders[0]['reference'], $this->body($this->get('tack/bestallning/' . $orders[0]['public_token'] . '/')));
    }

    public function testGiftCardDesignIsHiddenUntilSwingOrderReadsIt(): void
    {
        $sent = [];
        \CodeIgniter\Events\Events::on('email', static function (array $archive) use (&$sent): void { $sent[] = $archive; });

        $this->assertStringNotContainsString('name="design"', $this->body($this->get('presentkort/')));
        $this->post('presentkort', ['belopp' => '300', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $this->assertNull($this->db->table('orders')->get()->getRowArray()['gift_card_design']);
        $this->assertStringContainsString("design:STANDARD\n", $sent[0]['body'], 'swing-order får alltid en design');
    }

    public function testChosenGiftCardDesignReachesSwingOrder(): void
    {
        config('Orders')->mailCarriesGiftCardDesign = true;
        $sent = [];
        \CodeIgniter\Events\Events::on('email', static function (array $archive) use (&$sent): void { $sent[] = $archive; });

        $html = $this->body($this->get('presentkort/'));
        foreach (['STANDARD', 'FARSDAG', 'MORSDAG', 'JUL'] as $design) {
            $this->assertStringContainsString('name="design" value="' . $design . '"', $html);
        }
        $this->assertStringContainsString('Skrivs på kortet med designen Standard.', $html);

        $this->post('presentkort', ['belopp' => '500', 'design' => 'JUL', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $this->assertSame('JUL', $this->db->table('orders')->get()->getRowArray()['gift_card_design']);
        $this->assertStringContainsString("design:JUL\n", $sent[0]['body']);
    }

    public function testTamperedPotIsRejected(): void
    {
        $result = $this->post('spelpotter', ['produkt' => 'pott-999', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $result->assertStatus(422);
        $html = $this->body($result);
        $this->assertStringContainsString('Välj vilken pott du vill beställa.', $html);
        $this->assertLessThan(strpos($html, 'class="offer pot-card'), strpos($html, 'id="f-produkt-err"'), 'pottfelet står ovanför korten');
        $this->assertStringContainsString('value="pott-1000" required aria-invalid="true"', $html);
        $this->assertSame(0, $this->db->table('orders')->countAllResults());
    }

    /** @return iterable<string, array{string}> */
    public static function provideInvalidGiftCardAmounts(): iterable
    {
        yield 'under minimum' => ['50'];
        yield 'över maximum' => ['1100'];
        yield 'inte jämnt hundratal' => ['150'];
        yield 'text' => ['mycket'];
    }

    /**
     * @dataProvider provideInvalidGiftCardAmounts
     */
    public function testGiftCardAmountMustBeInRange(string $amount): void
    {
        $result = $this->post('presentkort', ['belopp' => $amount, 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $result->assertStatus(422);
        $this->assertSame(0, $this->db->table('orders')->countAllResults());
    }

    public function testTermsMustBeAccepted(): void
    {
        $result = $this->post('spelpotter', ['produkt' => 'pott-2000', 'name' => 'Anna', 'email' => 'anna@exempel.se']);

        $result->assertStatus(422);
        $this->assertStringContainsString('Du behöver godkänna köpvillkoren.', $this->body($result));
    }

    public function testCampaignPotUsesCampaignAmounts(): void
    {
        $this->db->table('campaigns')->insert([
            'title' => 'Höstkampanj', 'status' => 'published', 'pay_amount' => 2500, 'value_amount' => 4000,
            'valid_from' => null, 'valid_until' => date('Y-m-d', strtotime('+3 days')), 'show_on_home' => 0, 'sort_order' => 0,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $id = $this->db->insertID();

        $this->post('spelpotter', ['produkt' => 'kampanj-' . $id, 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $this->seeInDatabase('orders', ['campaign_id' => $id, 'pay_amount' => 2500, 'value_amount' => 4000, 'product_name' => 'Höstkampanj']);
    }

    public function testExpiredCampaignCannotBeOrdered(): void
    {
        $this->db->table('campaigns')->insert([
            'title' => 'Gammal kampanj', 'status' => 'published', 'pay_amount' => 2500, 'value_amount' => 4000,
            'valid_from' => null, 'valid_until' => date('Y-m-d', strtotime('-1 day')), 'show_on_home' => 0, 'sort_order' => 0,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->post('spelpotter', ['produkt' => 'kampanj-' . $this->db->insertID(), 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1'])
            ->assertStatus(422);
    }

    public function testFailedForwardKeepsOrderForResend(): void
    {
        $this->email->returnValue = false;

        $this->post('spelpotter', ['produkt' => 'pott-1000', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1']);

        $order = $this->db->table('orders')->get()->getRowArray();
        $this->assertNull($order['forwarded_at']);
        $this->assertNotNull($order['forward_error']);
        $this->assertSame(1, (int) $order['forward_attempts']);
    }

    public function testNoOrderFormWhenChannelIsNotConfigured(): void
    {
        config('Orders')->mailTo = '';

        $html = $this->body($this->get('spelpotter/'));
        $this->assertStringNotContainsString('id="bestall"', $html);
        $this->assertStringContainsString('Beställ via e-post eller telefon', $html);

        $this->post('spelpotter', ['produkt' => 'pott-1000', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'villkor' => '1'])
            ->assertStatus(422);
        $this->assertSame(0, $this->db->table('orders')->countAllResults());
    }

    public function testFriskvardOrderWithPersonalNumber(): void
    {
        $sent = [];
        \CodeIgniter\Events\Events::on('email', static function (array $archive) use (&$sent): void { $sent[] = $archive; });

        $html = $this->body($this->get('friskvard/'));
        $this->assertLessThan(strpos($html, 'id="epassi"'), strpos($html, 'id="direkt"'), 'direktköpet ligger före Epassi');
        $this->assertStringContainsString('name="personnummer"', $html);

        $this->post('friskvard', ['produkt' => 'pott-1000', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'personnummer' => '19811218-9876', 'villkor' => '1']);

        $order = $this->db->table('orders')->get()->getRowArray();
        $this->assertSame('FRISKVARD', $order['order_type']);
        $this->assertSame('Friskvårdspott 1000', $order['product_name']);
        $this->assertSame(1150, (int) $order['value_amount']);
        $this->assertNotNull($order['personal_number_enc']);
        $this->assertStringNotContainsString('9876', $order['personal_number_enc'], 'personnumret sparas krypterat');
        $this->assertStringStartsWith("type:Friskvård\nprice:1000\n", $sent[0]['body']);
        $this->assertStringContainsString("personnummer:19811218-9876\n", $sent[0]['body']);
    }

    public function testFriskvardRejectsInvalidPersonalNumber(): void
    {
        $result = $this->post('friskvard', ['produkt' => 'pott-1000', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'personnummer' => '19811218-9875', 'villkor' => '1']);

        $result->assertStatus(422);
        $html = $this->body($result);
        $this->assertStringContainsString('Personnumret verkar inte stämma', $html);
        $this->assertStringNotContainsString('19811218-9875', $html, 'personnumret skrivs inte tillbaka i sidan');
        $this->assertSame(0, $this->db->table('orders')->countAllResults());
    }

    public function testFriskvardClosedWithoutEncryptionKey(): void
    {
        config('Encryption')->key = '';

        $this->post('friskvard', ['produkt' => 'pott-500', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'personnummer' => '19811218-9876', 'villkor' => '1'])
            ->assertRedirectTo(page_path('friskvard'));

        $this->assertSame(0, $this->db->table('orders')->countAllResults());
        $this->assertStringNotContainsString('name="personnummer"', $this->body($this->get('friskvard/')));
    }

    public function testThanksPageRequiresValidToken(): void
    {
        $this->get('tack/bestallning/' . str_repeat('a', 32) . '/')->assertStatus(404);
        $this->get('tack/bestallning/inte-en-token/')->assertStatus(404);
    }
}
