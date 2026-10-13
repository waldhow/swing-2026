<?php

/**
 * Vyhjälpare för Swing-sajten. Laddas globalt via Config\Autoload::$helpers.
 */

if (! function_exists('icon')) {
    /**
     * Inline-SVG-ikon renderad på servern (ingen JS krävs, TECH-01).
     * Ikonerna är dekorativa: aria-hidden, texten bredvid bär betydelsen.
     */
    function icon(string $name, int $size = 22, float $strokeWidth = 1.8, string $class = ''): string
    {
        static $paths = [
            'check' => '<path d="M5 12.5l4.5 4.5L19 7"/>',
            'arrowR' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'monitor' => '<rect x="2.5" y="3.5" width="19" height="13" rx="2.5"/><path d="M8 21h8M12 16.5V21"/>',
            'cup' => '<path d="M7 4h10v5a5 5 0 0 1-10 0V4z"/><path d="M17 5.5h2.5a2.5 2.5 0 0 1 0 5H17M7 5.5H4.5a2.5 2.5 0 0 0 0 5H7M9 19h6M12 14v5"/>',
            'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
            'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M17 11.5a3 3 0 0 0 0-6M21.5 20a5.5 5.5 0 0 0-4-5.3"/>',
            'pin' => '<path d="M12 21s-7-6.3-7-11a7 7 0 0 1 14 0c0 4.7-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
            'mail' => '<rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3.5 6.5l8.5 6 8.5-6"/>',
            'phone' => '<path d="M5 4h3.5l1.5 4.5-2.2 1.4a12 12 0 0 0 5.3 5.3l1.4-2.2 4.5 1.5V18a2 2 0 0 1-2.2 2A16 16 0 0 1 4 6.2 2 2 0 0 1 5 4z"/>',
            'ticket' => '<path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V7z"/><path d="M14 5v14" stroke-dasharray="2 2"/>',
            'gift' => '<rect x="3" y="8" width="18" height="13" rx="2"/><path d="M3 12.5h18M12 8v13"/><path d="M12 8s-1-4-4-4a2.5 2.5 0 0 0 0 5M12 8s1-4 4-4a2.5 2.5 0 0 1 0 5"/>',
            'heart' => '<path d="M12 20s-7-4.5-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 5c-2.5 4.5-9.5 9-9.5 9z"/>',
            'bolt' => '<path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/>',
            'star' => '<path d="M12 3l2.6 5.6 6.1.8-4.5 4.2 1.2 6L12 16.8 6.6 19.6l1.2-6L3.3 9.4l6.1-.8L12 3z"/>',
            'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4"/>',
            'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'fb' => '<path d="M14.5 8.5H17V5.2h-2.7c-2.4 0-3.8 1.5-3.8 3.9v1.4H8.5v3.3H10V21h3.5v-7.2h2.3l.4-3.3h-2.7V9.4c0-.6.3-.9 1-.9z"/>',
            'ig' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1.2"/>',
            'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'wallet' => '<rect x="2.5" y="6" width="19" height="13" rx="2.5"/><path d="M2.5 10.5h19M16 15h2"/>',
            'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12.5l2 2 4-4.5"/>',
            'coffee' => '<path d="M4 8h13v6a5 5 0 0 1-10 0V8z" /><path d="M17 9h2a2.5 2.5 0 0 1 0 5h-2M3 21h16"/>',
            'calendar' => '<rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M3.5 9.5h17M8 3v4M16 3v4"/>',
            'card' => '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="M2.5 9.5h19M6 14.5h4"/>',
            'map' => '<path d="M9 4L3 6.5v13L9 17l6 2.5 6-2.5v-13L15 7 9 4z"/><path d="M9 4v13M15 7v12.5"/>',
            'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.6h.01"/>',
            'close' => '<path d="M6 6l12 12M18 6L6 18"/>',
            'chevronDown' => '<path d="M6 9l6 6 6-6"/>',
            'flag' => '<path d="M5 21V4M5 4h11l-1.5 4L16 12H5"/>',
            'trophy' => '<path d="M8 4h8v5a4 4 0 0 1-8 0V4z"/><path d="M16 5.5h3a3 3 0 0 1-3 4M8 5.5H5a3 3 0 0 0 3 4M12 13v4M8.5 20h7M9.5 17h5"/>',
            'external' => '<path d="M14 4h6v6M20 4l-9 9M18 14v4.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 4 18.5v-11A1.5 1.5 0 0 1 5.5 6H10"/>',
        ];

        if (! isset($paths[$name])) {
            return '';
        }

        $fill   = $name === 'fb' ? 'currentColor' : 'none';
        $stroke = $name === 'fb' ? 'none' : 'currentColor';

        return sprintf(
            '<svg class="ic%s" width="%d" height="%d" viewBox="0 0 24 24" fill="%s" stroke="%s" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
            $class !== '' ? ' ' . esc($class) : '',
            $size,
            $size,
            $fill,
            $stroke,
            rtrim(rtrim(number_format($strokeWidth, 2, '.', ''), '0'), '.'),
            $paths[$name],
        );
    }
}

if (! function_exists('kr')) {
    /**
     * Belopp i svensk form med hårt mellanslag: 1 150 kr, 37,50 kr.
     */
    function kr(int|float $amount, bool $withUnit = true): string
    {
        $decimals = is_float($amount) && floor($amount) !== $amount ? 2 : 0;
        $text     = number_format($amount, $decimals, ',', "\u{00A0}");

        return $withUnit ? $text . "\u{00A0}kr" : $text;
    }
}

if (! function_exists('page_url')) {
    /**
     * Absolut, kanonisk URL till en sida — alltid med avslutande snedstreck.
     */
    function page_url(string $slug = ''): string
    {
        $slug = trim($slug, '/');

        return rtrim(base_url(), '/') . '/' . ($slug === '' ? '' : $slug . '/');
    }
}

if (! function_exists('page_path')) {
    /**
     * Relativ sökväg till en sida, t.ex. /priser/ — för interna länkar.
     */
    function page_path(string $slug = ''): string
    {
        $slug = trim($slug, '/');

        return '/' . ($slug === '' ? '' : $slug . '/');
    }
}

if (! function_exists('booking_url')) {
    /** Målet för alla boka-knappar (Config\Booking — verifierad länk eller /boka/). */
    function booking_url(): string
    {
        return config('Booking')->ctaUrl();
    }
}

if (! function_exists('is_external_url')) {
    /**
     * Absolut http(s)-länk till en annan webbplats. Egen domän (baseURL och
     * App::$allowedHostnames, med eller utan www) räknas inte som extern.
     */
    function is_external_url(string $url): bool
    {
        if (! preg_match('#^https?://#i', $url)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $own  = array_merge([parse_url(base_url(), PHP_URL_HOST)], config('App')->allowedHostnames);
        $own  = array_map(static fn ($h): string => preg_replace('/^www\./', '', strtolower((string) $h)), $own);

        return $host !== '' && ! in_array(preg_replace('/^www\./', '', $host), $own, true);
    }
}

if (! function_exists('external_attrs')) {
    /** target/rel för länkar som lämnar sajten (ägarens önskemål: alltid ny flik). */
    function external_attrs(string $url): string
    {
        return opens_new_tab($url) ? ' target="_blank" rel="noopener"' : '';
    }
}

if (! function_exists('opens_new_tab')) {
    /** Alla länkar till andra webbplatser öppnas i ny flik. Se även Filters\ExternalLinks. */
    function opens_new_tab(string $url): bool
    {
        return is_external_url($url);
    }
}

if (! function_exists('new_tab_hint')) {
    /** Dold text för skärmläsare när länken öppnas i ny flik. */
    function new_tab_hint(string $url): string
    {
        return opens_new_tab($url) ? '<span class="sr-only"> (öppnas i ny flik)</span>' : '';
    }
}

if (! function_exists('sv_date')) {
    /**
     * Datum på svenska, t.ex. "söndag 18 januari 2026" eller med klockslag.
     */
    function sv_date(DateTimeInterface|string $date, bool $withTime = false, bool $withWeekday = true): string
    {
        $date = is_string($date) ? new DateTimeImmutable($date) : $date;

        $days   = [1 => 'måndag', 'tisdag', 'onsdag', 'torsdag', 'fredag', 'lördag', 'söndag'];
        $months = [1 => 'januari', 'februari', 'mars', 'april', 'maj', 'juni', 'juli', 'augusti', 'september', 'oktober', 'november', 'december'];

        $text = ($withWeekday ? $days[(int) $date->format('N')] . ' ' : '')
            . $date->format('j') . ' ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');

        return $withTime ? $text . ' kl. ' . $date->format('H.i') : $text;
    }
}

if (! function_exists('picture')) {
    /**
     * <picture> med WebP-srcset och JPEG-reserv ur Config\Images.
     * $priority = bilden är LCP-elementet (eager + fetchpriority=high), annars lazy.
     */
    function picture(string $key, string $sizes = '100vw', bool $priority = false, ?string $alt = null, string $class = ''): string
    {
        $image = config('Images')->images[$key] ?? null;
        if ($image === null) {
            return '';
        }

        $srcset = [];
        foreach ($image['webp'] as $width => $path) {
            $srcset[] = '/' . $path . ' ' . $width . 'w';
        }

        return sprintf(
            '<picture><source type="image/webp" srcset="%s" sizes="%s" /><img src="/%s" alt="%s" width="%d" height="%d"%s%s decoding="async" /></picture>',
            esc(implode(', ', $srcset)),
            esc($sizes),
            esc($image['src']),
            esc($alt ?? $image['alt']),
            $image['w'],
            $image['h'],
            $priority ? ' fetchpriority="high"' : ' loading="lazy"',
            $class !== '' ? ' class="' . esc($class) . '"' : '',
        );
    }
}

if (! function_exists('picture_url')) {
    /** Fullständig adress till bildens JPEG-version ur Config\Images (t.ex. för strukturerad data). */
    function picture_url(string $key): ?string
    {
        $image = config('Images')->images[$key] ?? null;

        return $image === null ? null : base_url($image['src']);
    }
}

if (! function_exists('partial')) {
    /**
     * Renderar en delvy med en egen renderare så att variabler aldrig läcker
     * mellan anrop (CI:s delade renderare behåller data mellan view()-anrop,
     * vilket annars får t.ex. ett fält att ärva required/type från föregående).
     * Använd för alla nästlade vyer; controllers använder view().
     */
    function partial(string $name, array $data = []): string
    {
        return service('renderer', null, null, false)->setData($data, 'raw')->render($name, null, false);
    }
}

if (! function_exists('asset')) {
    /**
     * Sökväg till en fil i public/ med versionsparameter (filens ändringstid),
     * så att långtidscachade CSS/JS byts direkt efter en uppdatering.
     */
    function asset(string $path): string
    {
        $path = ltrim($path, '/');
        $file = FCPATH . $path;

        return '/' . $path . (is_file($file) ? '?v=' . filemtime($file) : '');
    }
}

if (! function_exists('sv_count')) {
    /** Litet tal som ord i löptext ("fyra simulatorer"); större tal som siffror. */
    function sv_count(int $n, bool $capitalize = false): string
    {
        $words = [1 => 'en', 'två', 'tre', 'fyra', 'fem', 'sex', 'sju', 'åtta', 'nio', 'tio', 'elva', 'tolv'];
        $word  = $words[$n] ?? (string) $n;

        return $capitalize ? mb_strtoupper(mb_substr($word, 0, 1)) . mb_substr($word, 1) : $word;
    }
}

if (! function_exists('partner')) {
    /** Partnernamn som länk i ny flik om partnern har en webbplats i Config\Swing::$partnerLinks. */
    function partner(string $name): string
    {
        $url = config('Swing')->partnerLinks[$name] ?? null;
        if ($url === null) {
            return esc($name);
        }

        return '<a href="' . esc($url) . '"' . external_attrs($url) . '>' . esc($name) . new_tab_hint($url) . '</a>';
    }
}
