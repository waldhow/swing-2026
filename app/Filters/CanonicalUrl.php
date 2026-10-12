<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Kanoniska URL:er i ett enda hopp (SEO-01, SEO-07):
 *  - gamla WordPress-adresser → ny sida (301)
 *  - sidor utan avslutande snedstreck → med snedstreck (301), t.ex. /priser → /priser/
 *
 * Körs i PHP i stället för .htaccess så att samma regler gäller lokalt
 * (php spark serve) och på Loopia. Filer med ändelse (sitemap.xml, bilder)
 * och andra metoder än GET/HEAD lämnas orörda.
 */
class CanonicalUrl implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return null;
        }

        $path  = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        $trim  = trim($path, '/');

        $target = config('Redirects')->target($trim);
        if ($target !== null) {
            return $this->redirect(page_url($target), $query);
        }

        if ($trim === '' || str_ends_with($path, '/') || preg_match('/\.[a-z0-9]{1,5}$/i', $trim)) {
            return null;
        }

        return $this->redirect(page_url($trim), $query);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function redirect(string $url, string $query): ResponseInterface
    {
        return service('response')
            ->redirect($url . ($query !== '' ? '?' . $query : ''), 'auto', 301)
            ->setHeader('Cache-Control', 'public, max-age=86400');
    }
}
