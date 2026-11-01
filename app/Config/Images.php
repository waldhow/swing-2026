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
        // Team Swing – bilder från spelarna (2026-11-01), beskurna till 3:4 utan EXIF/GPS.
        'team-wilhelm-ahlgren' => [
            'src'  => 'assets/team/wilhelm-ahlgren.jpg',
            'w'    => 720,
            'h'    => 960,
            'webp' => [480 => 'assets/team/wilhelm-ahlgren-480.webp', 960 => 'assets/team/wilhelm-ahlgren-960.webp'],
            'alt'  => 'Wilhelm Ahlgren i genomsvingen i en av simulatorerna hos Swing',
        ],
        'team-ola-johansson' => [
            'src'  => 'assets/team/ola-johansson.jpg',
            'w'    => 720,
            'h'    => 960,
            'webp' => [480 => 'assets/team/ola-johansson-480.webp', 960 => 'assets/team/ola-johansson-960.webp'],
            'alt'  => 'Ola Johansson i genomsvingen med järnklubba hos Swing',
        ],
        'team-oskar-johansson' => [
            'src'  => 'assets/team/oskar-johansson.jpg',
            'w'    => 720,
            'h'    => 960,
            'webp' => [480 => 'assets/team/oskar-johansson-480.webp', 960 => 'assets/team/oskar-johansson-960.webp'],
            'alt'  => 'Oskar Johansson i genomsvingen i en av simulatorerna hos Swing',
        ],
        'team-wilhelm-johansson' => [
            'src'  => 'assets/team/wilhelm-johansson.jpg',
            'w'    => 720,
            'h'    => 960,
            'webp' => [480 => 'assets/team/wilhelm-johansson-480.webp', 960 => 'assets/team/wilhelm-johansson-960.webp'],
            'alt'  => 'Wilhelm Johansson framför simulatorskärmen hos Swing',
        ],
        'team-anton-nilsson' => [
            'src'  => 'assets/team/anton-nilsson.jpg',
            'w'    => 720,
            'h'    => 960,
            'webp' => [480 => 'assets/team/anton-nilsson-480.webp', 960 => 'assets/team/anton-nilsson-960.webp'],
            'alt'  => 'Anton Nilsson i genomsvingen hos Swing, med sin golfbag i bakgrunden',
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
