<?php

namespace App\Controllers;

use App\Models\CompetitionModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

/**
 * sitemap.xml, robots.txt och llms.txt — genererade ur samma källor som sidorna.
 */
class Seo extends BaseController
{
    /** Endast indexerbara, publicerade sidor (inga tack-, admin- eller utkastsidor). */
    public function sitemap(): ResponseInterface
    {
        $this->cacheInProduction();

        $urls = [];
        foreach (config('Pages')->pages as $slug => $page) {
            $urls[] = ['loc' => page_url($slug), 'priority' => $page['priority'] ?? '0.5', 'lastmod' => null];
        }

        try {
            foreach (model(CompetitionModel::class)->allPublished() as $competition) {
                $urls[] = [
                    'loc'      => page_url('tavlingar/' . $competition['slug']),
                    'priority' => '0.5',
                    'lastmod'  => $competition['updated_at'] ? date('Y-m-d', strtotime($competition['updated_at'])) : null,
                ];
            }
        } catch (Throwable $e) {
            log_message('error', 'Sitemap: tävlingar kunde inte läsas: {msg}', ['msg' => $e->getMessage()]);
        }

        return $this->response
            ->setContentType('application/xml', 'UTF-8')
            ->setBody(view('seo/sitemap', ['urls' => $urls], ['debug' => false]));
    }

    public function robots(): ResponseInterface
    {
        $lines = config('Seo')->indexable
            ? ['User-agent: *', 'Disallow: /admin/', 'Disallow: /tack/', '', 'Sitemap: ' . base_url('sitemap.xml')]
            // Testdomän eller ej lanserad: inget ska indexeras.
            : ['User-agent: *', 'Disallow: /'];

        return $this->response
            ->setContentType('text/plain', 'UTF-8')
            ->setBody(implode("\n", $lines) . "\n");
    }

    /**
     * llms.txt — kort sammanfattning i klartext av samma fakta som sajten visar.
     * Inget dolt innehåll: allt här finns även i vanlig HTML (SEO-04).
     */
    public function llms(): ResponseInterface
    {
        $this->cacheInProduction();

        return $this->response
            ->setContentType('text/plain', 'UTF-8')
            ->setBody(view('seo/llms', [
                'swing'   => config('Swing'),
                'booking' => config('Booking'),
                'pricing' => service('pricing'),
                'pages'   => config('Pages'),
            ], ['debug' => false]));
    }
}
