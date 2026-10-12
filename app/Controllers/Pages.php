<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Sidorna i sidregistret (Config\Pages). Slugen tas från URL:en.
 */
class Pages extends BaseController
{
    public function show(): string
    {
        $slug = trim($this->request->getUri()->getPath(), '/');

        if (config('Pages')->find($slug) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->cacheInProduction();

        return service('pageRenderer')->render($slug);
    }

    /** 404 med sajtens layout och hjälpsamma länkar. */
    public function notFound(): string
    {
        $this->response->setStatusCode(404);

        return view('pages/404', [
            'meta' => [
                'title'       => 'Sidan hittades inte | Swing Golf & Lounge',
                'description' => 'Sidan du letade efter finns inte.',
                'canonical'   => null,
                'robots'      => 'noindex',
            ],
            'navActive' => '',
            'trail'     => [],
            'jsonLd'    => [],
        ]);
    }
}
