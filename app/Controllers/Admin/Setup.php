<?php

namespace App\Controllers\Admin;

use App\Models\AdminUserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

/**
 * Första uppsättningen på Loopia (ingen SSH): /admin/setup/?token=…
 *
 * Kör databasmigrationerna och — bara om det inte finns någon admin än —
 * skapar den första användaren. Kan köras igen efter en uppdatering för att
 * köra nya migrationer. Kräver admin.setupToken i .env; utan token finns
 * sidan inte (404).
 */
class Setup extends BaseAdminController
{
    public function index(): string
    {
        $this->guard();

        return $this->render('setup', [
            'title'     => 'Uppsättning',
            'token'     => (string) $this->request->getGet('token'),
            'needsUser' => $this->needsUser(),
            'errors'    => [],
        ]);
    }

    public function run(): string
    {
        $this->guard();

        $token = (string) $this->request->getGet('token');

        try {
            service('migrations')->setNamespace('App')->latest();
        } catch (Throwable $e) {
            log_message('error', 'Setup: migrationerna misslyckades: {msg}', ['msg' => $e->getMessage()]);

            return $this->render('setup', [
                'title'     => 'Uppsättning',
                'token'     => $token,
                'needsUser' => false,
                'errors'    => ['migrations' => 'Migrationerna kunde inte köras. Kontrollera databasuppgifterna i .env och se loggen i writable/logs.'],
            ]);
        }

        $created = false;

        if ($this->needsUser()) {
            $errors = $this->validateUser();

            if ($errors !== []) {
                return $this->render('setup', [
                    'title'     => 'Uppsättning',
                    'token'     => $token,
                    'needsUser' => true,
                    'errors'    => $errors,
                    'migrated'  => true,
                ]);
            }

            model(AdminUserModel::class)->insert([
                'email'         => mb_strtolower(trim((string) $this->request->getPost('email'))),
                'name'          => trim((string) $this->request->getPost('name')),
                'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            ]);
            $created = true;
        }

        return $this->render('setup_done', ['title' => 'Uppsättning klar', 'created' => $created]);
    }

    /** 404 om setup är avstängd eller token saknas/är fel. */
    private function guard(): void
    {
        $config = config('Admin');
        $token  = (string) $this->request->getGet('token');

        if (! $config->setupEnabled() || ! hash_equals($config->setupToken, $token)) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    /** Behövs en första användare? (Tabellen kan saknas före första migreringen.) */
    private function needsUser(): bool
    {
        $db = db_connect();

        if (! $db->tableExists('admin_users')) {
            return true;
        }

        return model(AdminUserModel::class)->countAllResults() === 0;
    }

    /** @return array<string, string> */
    private function validateUser(): array
    {
        $min    = config('Admin')->minPasswordLength;
        $errors = [];
        $email  = trim((string) $this->request->getPost('email'));
        $name   = trim((string) $this->request->getPost('name'));
        $pass   = (string) $this->request->getPost('password');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Ange en giltig e-postadress.';
        }
        if ($name === '' || mb_strlen($name) > 120) {
            $errors['name'] = 'Ange ditt namn (högst 120 tecken).';
        }
        if (mb_strlen($pass) < $min) {
            $errors['password'] = "Lösenordet måste vara minst {$min} tecken.";
        } elseif ($pass !== (string) $this->request->getPost('password_repeat')) {
            $errors['password_repeat'] = 'Lösenorden matchar inte.';
        }

        return $errors;
    }
}
