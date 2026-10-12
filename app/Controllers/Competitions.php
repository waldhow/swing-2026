<?php

namespace App\Controllers;

use App\Models\CompetitionModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

/**
 * Detaljsida för en publicerad tävling (/tavlingar/{slug}/).
 */
class Competitions extends BaseController
{
    public function show(string $slug): string
    {
        try {
            $competition = model(CompetitionModel::class)->findPublished($slug);
        } catch (Throwable $e) {
            log_message('error', 'Tävling kunde inte läsas: {msg}', ['msg' => $e->getMessage()]);
            $competition = null;
        }

        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->cacheInProduction();

        $url    = page_url('tavlingar/' . $competition['slug']);
        $trail  = service('pageRenderer')->trail('tavlingar', [['label' => $competition['title'], 'url' => $url]]);
        $schema = service('structuredData');
        $jsonLd = array_values(array_filter([$schema->breadcrumbs($trail), $schema->event($competition)]));

        return view('pages/tavling', [
            'competition' => $competition,
            'meta'        => [
                'title'       => $competition['title'] . ' | Tävlingar hos Swing',
                'description' => $competition['summary'] ?: 'Tävling hos Swing Golf & Lounge i Nättraby: ' . $competition['title'] . '.',
                'canonical'   => $url,
            ],
            'navActive' => 'tavlingar',
            'trail'     => $trail,
            'jsonLd'    => $jsonLd,
        ]);
    }
}
