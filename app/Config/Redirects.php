<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * 301-omdirigeringar från den tidigare WordPress-sajten (SEO-07).
 *
 * Inventerat 2026-10-11 från swinggolf.se/page-sitemap.xml samt länkar i
 * sidhuvud/sidfot. Nyckel = gammal sökväg utan inledande/avslutande snedstreck,
 * värde = ny sida (slug i Config\Pages). Sidor som behåller sin URL
 * (spelpotter, presentkort, friskvard, team-swing) behöver ingen rad.
 */
class Redirects extends BaseConfig
{
    public array $map = [
        'priser-medlemskap'                  => 'priser',
        'vara-simulatorer'                   => 'trackman-io',
        'trackman-io-gjord-for-inomhusgolf'  => 'trackman-io',
        'epassi-swing-golf-lounge'           => 'friskvard',
        'veckogolf'                          => 'tavlingar',
        'juniortouren-2026'                  => 'tavlingar',
        'bekraftelse-kop'                    => 'spelpotter',
        'boka-har'                           => 'boka',
        'foretag'                            => 'foretagsevent',
        'artiklar-anton-kiddy-nilsson-en-passion-for-inomhusgolf-och-trackman-next-tour' => 'team-swing',
        'wp-content/uploads/2024/01/Kopvillkor-for-Swing-Golf-Lounge-2023-12-15.pdf'      => 'kopvillkor',
        'wp-content/uploads/2024/01/Swing-Golf-Lounge-Integritetspolicy-2023-12-15.pdf'   => 'integritetspolicy',
    ];

    public function target(string $path): ?string
    {
        $path = trim($path, '/');

        return $this->map[$path] ?? null;
    }
}
