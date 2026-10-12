<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Gemensam bas för sajtens controllers.
 */
abstract class BaseController extends Controller
{
    /**
     * Hård sidcachning i produktion (rensas när admin sparar innehåll).
     * Lokalt och i test cachas inget så att ändringar syns direkt.
     */
    protected function cacheInProduction(int $ttl = HOUR): void
    {
        if (ENVIRONMENT === 'production') {
            // Kort webbläsarcache — sidcachen på servern töms när admin sparar.
            $this->response->setCache(['max-age' => 300, 'public']);
            $this->cachePage($ttl);
        }
    }

    /**
     * Honeypot: fältet "webbplats" är dolt för människor och ska vara tomt.
     */
    protected function isSpam(): bool
    {
        return trim((string) $this->request->getPost('webbplats')) !== '';
    }

    /**
     * Begränsar antal inskick per IP och timme. Nyckeln hashas — ingen IP sparas.
     */
    protected function isThrottled(string $bucket, int $maxPerHour): bool
    {
        if (ENVIRONMENT === 'testing') {
            return false;
        }

        $key = $bucket . '_' . hash('sha256', (string) $this->request->getIPAddress());

        return ! service('throttler')->check($key, $maxPerHour, HOUR);
    }
}
