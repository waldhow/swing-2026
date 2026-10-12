<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Bildregister. Endast bilder som ägaren godkänt för publicering får ligga
 * här (CONTENT-09, B07 — hero och galleri godkända 2026-10-11).
 *
 * Varje bild har en JPEG-reserv och WebP-varianter per bredd, genererade ur
 * originalet (se docs/bilder.md). Alt-texterna beskriver bilden sakligt.
 */
class Images extends BaseConfig
{
    public array $images = [
        'hero' => [
            'src'  => 'assets/hero-lounge.jpg',
            'w'    => 1920,
            'h'    => 1440,
            'webp' => [800 => 'assets/img/hero-lounge-800.webp', 1280 => 'assets/img/hero-lounge-1280.webp', 1920 => 'assets/img/hero-lounge-1920.webp'],
            'alt'  => 'Loungen hos Swing med baren i mitten och simulatorerna på båda sidor',
        ],
        'oversikt' => [
            'src'  => 'assets/gallery/01-oversikt.jpg',
            'w'    => 1024,
            'h'    => 768,
            'webp' => [640 => 'assets/img/01-oversikt-640.webp', 1024 => 'assets/img/01-oversikt-1024.webp'],
            'alt'  => 'Lokalen med baren i mitten och simulatorer på båda sidor',
        ],
        'lounge' => [
            'src'  => 'assets/gallery/02-lounge.jpg',
            'w'    => 1024,
            'h'    => 768,
            'webp' => [640 => 'assets/img/02-lounge-640.webp', 1024 => 'assets/img/02-lounge-1024.webp'],
            'alt'  => 'Loungen med chesterfieldsoffor och dämpat ljus',
        ],
        'simulator' => [
            'src'  => 'assets/gallery/03-simulator.jpg',
            'w'    => 1024,
            'h'    => 768,
            'webp' => [640 => 'assets/img/03-simulator-640.webp', 1024 => 'assets/img/03-simulator-1024.webp'],
            'alt'  => 'Simulatorbås med TrackMan-skärm och golfbag',
        ],
        'bar' => [
            'src'  => 'assets/gallery/04-bar.jpg',
            'w'    => 1024,
            'h'    => 768,
            'webp' => [640 => 'assets/img/04-bar-640.webp', 1024 => 'assets/img/04-bar-1024.webp'],
            'alt'  => 'Bardisken med barstolar och skärmar',
        ],
        'detalj' => [
            'src'  => 'assets/gallery/05-detalj.jpg',
            'w'    => 1024,
            'h'    => 768,
            'webp' => [640 => 'assets/img/05-detalj-640.webp', 1024 => 'assets/img/05-detalj-1024.webp'],
            'alt'  => 'Detalj från bardisken i varmt ljus',
        ],
    ];

    /** Bilder i galleriet på startsidan, i ordning, med layoutplats. */
    public array $gallery = [
        'oversikt'  => 'c8 h420',
        'lounge'    => 'c4 h420',
        'simulator' => 'c4 h260',
        'bar'       => 'c4 h260',
        'detalj'    => 'c4 h260',
    ];

    /** Delningsbild (Open Graph). */
    public string $ogImage = 'assets/img/hero-lounge-1280.webp';
}
