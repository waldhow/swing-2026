<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Psr\Log\LoggerInterface;

/**
 * Gemensam grund för admin-controllers: session, inloggad användare och
 * rendering i admin-layouten.
 */
abstract class BaseAdminController extends BaseController
{
    protected Session $session;

    /** @var array<string, mixed>|null */
    protected ?array $user = null;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Admin-svar får aldrig cachas eller indexeras.
        $response->setHeader('Cache-Control', 'no-store, private');
        $response->setHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->session = session();

        $userId = $this->session->get('admin_user_id');
        if ($userId !== null) {
            $this->user = model(AdminUserModel::class)->find($userId);
        }
    }

    /**
     * Renderar en admin-vy. $nav markerar aktiv menypunkt.
     */
    protected function render(string $view, array $data = [], string $nav = ''): string
    {
        return view('admin/' . $view, $data + [
            'user'     => $this->user,
            'navAdmin' => $nav,
            'title'    => 'Admin',
        ]);
    }

    /** Redirect inom admin med flashmeddelande. */
    protected function back(string $path, string $message, string $type = 'success'): RedirectResponse
    {
        return redirect()->to(site_url('admin/' . trim($path, '/') . ($path === '' ? '' : '/')))->with($type, $message);
    }

    /** Tömmer den hårda sidcachen så att publika sidor visar ändringen direkt. */
    protected function clearPageCache(): void
    {
        cache()->clean();
    }

    /** Tomma strängar → null (nullbara kolumner). */
    protected function nullify(array $data): array
    {
        return array_map(static fn ($v) => is_string($v) && trim($v) === '' ? null : (is_string($v) ? trim($v) : $v), $data);
    }
}
