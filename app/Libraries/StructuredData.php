<?php

namespace App\Libraries;

use Config\Swing;
use DateTimeImmutable;
use DateTimeZone;

/**
 * schema.org JSON-LD som speglar synligt innehåll (SEO-05). Endast verifierade
 * attribut: inga betyg, recensioner eller påhittade erbjudanden.
 */
final class StructuredData
{
    public function __construct(private readonly Swing $swing, private readonly Pricing $pricing)
    {
    }

    public function business(): array
    {
        $s      = $this->swing;
        $prices = $this->pricing->distinctPrices();

        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'SportsActivityLocation',
            '@id'         => page_url() . '#verksamhet',
            'name'        => $s->name,
            'legalName'   => $s->legalName,
            'foundingDate' => (string) $s->foundingYear,
            'url'         => page_url(),
            'telephone'   => $s->phoneE164,
            'email'       => $s->email,
            'image'       => base_url(config('Images')->ogImage),
            'logo'        => base_url('assets/logo.svg'),
            'description' => "Inomhusgolf med {$s->simulatorCount} {$s->simulatorTech}-simulatorer och lounge i {$s->locality}, {$s->municipality}.",
            'priceRange'  => min($prices) . '–' . max($prices) . ' kr per simulator och timme',
            'address'     => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $s->street,
                'postalCode'      => $s->postalCode,
                'addressLocality' => $s->locality,
                'addressRegion'   => $s->region,
                'addressCountry'  => $s->countryCode,
            ],
            'openingHoursSpecification' => [[
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'opens'     => $s->opens,
                // Midnatt anges som 23:59 — "00:00" tolkas annars som stängt.
                'closes'    => $s->closes === '00:00' ? '23:59' : $s->closes,
            ]],
            'sameAs' => array_values($s->socials),
        ];
    }

    /**
     * @param list<array{label: string, url: string}> $trail inklusive Start
     */
    public function breadcrumbs(array $trail): array
    {
        $items = [];
        foreach (array_values($trail) as $i => $crumb) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $crumb['label'],
                'item'     => $crumb['url'],
            ];
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    /** Artikel/guide (t.ex. TrackMan-guiden), utgiven av Swing. */
    public function article(string $title, string $description, string $url, ?string $image = null): array
    {
        $s = $this->swing;

        return array_filter([
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'headline'         => trim(explode('|', $title)[0]),
            'description'      => $description,
            'url'              => $url,
            'mainEntityOfPage' => $url,
            'inLanguage'       => 'sv-SE',
            'image'            => $image,
            'author'           => ['@type' => 'Organization', 'name' => $s->name, 'url' => page_url()],
            'publisher'        => ['@type' => 'Organization', 'name' => $s->name, 'url' => page_url(), 'logo' => ['@type' => 'ImageObject', 'url' => base_url('assets/logo.svg')]],
        ]);
    }

    /**
     * @param list<array{q: string, a: string}> $items svaren får innehålla enkel HTML
     */
    public function faq(array $items): array
    {
        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(static fn (array $item): array => [
                '@type'          => 'Question',
                'name'           => $item['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags($item['a']))],
            ], $items),
        ];
    }

    /**
     * Event bara för publicerade tävlingar med känt startdatum (AI-spec 6).
     */
    public function event(array $competition): ?array
    {
        if (($competition['status'] ?? '') !== 'published' || empty($competition['starts_at'])) {
            return null;
        }

        $tz    = new DateTimeZone(config('App')->appTimezone);
        $start = new DateTimeImmutable($competition['starts_at'], $tz);
        $s     = $this->swing;

        $event = [
            '@context'            => 'https://schema.org',
            '@type'               => 'SportsEvent',
            'name'                => $competition['title'],
            'url'                 => page_url('tavlingar/' . $competition['slug']),
            'startDate'           => $start->format(DATE_ATOM),
            'eventStatus'         => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location'            => [
                '@type'   => 'Place',
                'name'    => $s->name,
                'address' => [
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => $s->street,
                    'postalCode'      => $s->postalCode,
                    'addressLocality' => $s->locality,
                    'addressCountry'  => $s->countryCode,
                ],
            ],
            'organizer' => ['@type' => 'Organization', 'name' => $s->name, 'url' => page_url()],
        ];

        if (! empty($competition['ends_at'])) {
            $event['endDate'] = (new DateTimeImmutable($competition['ends_at'], $tz))->format(DATE_ATOM);
        }
        if (! empty($competition['summary'])) {
            $event['description'] = $competition['summary'];
        }

        return $event;
    }

    /** Event-markup för en lista tävlingar (de utan datum hoppas över). */
    public function events(array $competitions): array
    {
        return array_values(array_filter(array_map($this->event(...), $competitions)));
    }
}
