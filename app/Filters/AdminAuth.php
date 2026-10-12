<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Kräver inloggad admin för /admin/* (utom inloggning och setup), loggar ut
 * efter inaktivitet och märker alla admin-svar som ej cachebara och ej
 * indexerbara. Sessionen startas bara här — publika sidor sätter inga cookies.
 */
class AdminAuth implements FilterInterface
{
    private const PUBLIC_PATHS = ['admin/login', 'admin/setup'];

    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');

        if (in_array($path, self::PUBLIC_PATHS, true)) {
            return null;
        }

        $session = session();
        $userId  = $session->get('admin_user_id');

        if ($userId === null) {
            return $this->toLogin();
        }

        $timeout  = config('Admin')->sessionTimeoutMinutes * 60;
        $lastSeen = (int) $session->get('admin_last_seen');

        if ($lastSeen > 0 && time() - $lastSeen > $timeout) {
            $session->remove(['admin_user_id', 'admin_user_name', 'admin_last_seen']);
            $session->setFlashdata('error', 'Du har loggats ut efter en stunds inaktivitet. Logga in igen.');

            return $this->toLogin();
        }

        $session->set('admin_last_seen', time());

        return null;
    }

    private function toLogin(): ResponseInterface
    {
        return redirect()->to(site_url('admin/login/'))
            ->setHeader('Cache-Control', 'no-store, private')
            ->setHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * Körs bara om filtret även kopplas som after-filter i Config\Filters;
     * BaseAdminController sätter samma rubriker på alla controller-svar.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Cache-Control', 'no-store, private');
        $response->setHeader('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
