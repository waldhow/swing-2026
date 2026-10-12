<?php

namespace App\Controllers\Admin;

use App\Models\AdminUserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Inloggning, utloggning och eget konto.
 */
class Auth extends BaseAdminController
{
    private const LOGIN_ERROR = 'Fel e-postadress eller lösenord.';

    public function login(): string|RedirectResponse
    {
        if ($this->user !== null) {
            return redirect()->to(site_url('admin/'));
        }

        return $this->render('login', ['title' => 'Logga in']);
    }

    public function attempt(): string|RedirectResponse
    {
        $config    = config('Admin');
        $throttler = service('throttler');
        $key       = 'admin-login-' . md5((string) $this->request->getIPAddress());
        $window    = 15 * MINUTE;

        // Kostnad 0 = kontrollera utan att förbruka ett försök.
        if (! $throttler->check($key, $config->maxLoginAttempts, $window, 0)) {
            return $this->render('login', [
                'title' => 'Logga in',
                'error' => 'För många inloggningsförsök. Vänta en stund och försök igen.',
                'email' => (string) $this->request->getPost('email'),
            ]);
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');
        $user     = model(AdminUserModel::class)->findByEmail($email);

        if ($user === null || ! password_verify($password, $user['password_hash'])) {
            $throttler->check($key, $config->maxLoginAttempts, $window, 1);

            return $this->render('login', [
                'title' => 'Logga in',
                'error' => self::LOGIN_ERROR,
                'email' => $email,
            ]);
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            model(AdminUserModel::class)->update($user['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        $throttler->remove($key);
        $this->session->regenerate(true);
        $this->session->set([
            'admin_user_id'   => (int) $user['id'],
            'admin_user_name' => $user['name'],
            'admin_last_seen' => time(),
        ]);
        model(AdminUserModel::class)->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return redirect()->to(site_url('admin/'));
    }

    public function logout(): RedirectResponse
    {
        $this->session->remove(['admin_user_id', 'admin_user_name', 'admin_last_seen']);
        $this->session->regenerate(true);

        return redirect()->to(site_url('admin/login/'))->with('success', 'Du är utloggad.');
    }

    public function account(): string
    {
        return $this->render('account', ['title' => 'Konto', 'errors' => []], 'konto');
    }

    public function updateAccount(): string|RedirectResponse
    {
        $min    = config('Admin')->minPasswordLength;
        $errors = [];
        $name   = trim((string) $this->request->getPost('name'));
        $new    = (string) $this->request->getPost('new_password');
        $repeat = (string) $this->request->getPost('new_password_repeat');

        if ($name === '' || mb_strlen($name) > 120) {
            $errors['name'] = 'Ange ditt namn (högst 120 tecken).';
        }

        if ($new !== '') {
            if (! password_verify((string) $this->request->getPost('current_password'), $this->user['password_hash'])) {
                $errors['current_password'] = 'Nuvarande lösenord stämmer inte.';
            }
            if (mb_strlen($new) < $min) {
                $errors['new_password'] = "Lösenordet måste vara minst {$min} tecken.";
            } elseif ($new !== $repeat) {
                $errors['new_password_repeat'] = 'Lösenorden matchar inte.';
            }
        }

        if ($errors !== []) {
            return $this->render('account', ['title' => 'Konto', 'errors' => $errors], 'konto');
        }

        $data = ['name' => $name];
        if ($new !== '') {
            $data['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
        }

        model(AdminUserModel::class)->update($this->user['id'], $data);
        $this->session->set('admin_user_name', $name);

        if ($new !== '') {
            $this->session->regenerate(true);
        }

        return $this->back('konto', 'Kontot är uppdaterat.');
    }
}
