<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Sidregistret — SEO-matrisen ur kravspecifikationen (SEO-01, SEO-02).
 *
 * Varje indexerbar sida har en egen URL med avslutande snedstreck, unik
 * titel och beskrivning. Routes, sitemap.xml, brödsmulor och navigeringens
 * aktiva markering läses härifrån.
 *
 * Platshållare i title/description fylls från Config\Swing så att fakta
 * aldrig skiljer sig mellan sidor: {hours}, {fromPrice}, {phone}, {email},
 * {street}.
 *
 * Fält:
 *  - view        vyn under app/Views/pages/
 *  - label       kort namn (brödsmulor, länkar)
 *  - nav         vilken toppmenypunkt som markeras aktiv
 *  - priority    sitemap-prioritet
 *  - schema      typ av strukturerad data utöver brödsmulor
 */
class Pages extends BaseConfig
{
    public array $pages = [
        '' => [
            'view'        => 'home',
            'label'       => 'Startsida',
            'title'       => 'Inomhusgolf i Karlskrona | Swing Golf & Lounge',
            'description' => 'Spela inomhusgolf med TrackMan iO hos Swing i Nättraby. Fyra simulatorer, skön lounge och öppet {hours} alla dagar. Boka din golftid.',
            'nav'         => 'start',
            'priority'    => '1.0',
            'schema'      => 'business',
        ],
        'inomhusgolf' => [
            'view'        => 'inomhusgolf',
            'label'       => 'Inomhusgolf',
            'title'       => 'Inomhusgolf Karlskrona | Spela året runt hos Swing',
            'description' => 'Spela inomhusgolf året runt i Karlskrona. Fyra TrackMan iO-simulatorer, ombonad lounge och öppet {hours} varje dag i Nättraby.',
            'nav'         => 'anlaggningen',
            'priority'    => '0.9',
        ],
        'priser' => [
            'view'        => 'priser',
            'label'       => 'Priser',
            'title'       => 'Priser för golfsimulator i Karlskrona | Swing',
            'description' => 'Se priser för inomhusgolf hos Swing Golf & Lounge. Från {fromPrice} per simulator och timme. Du betalar för simulatorn, inte per person.',
            'nav'         => 'priser',
            'priority'    => '0.9',
        ],
        'boka' => [
            'view'        => 'boka',
            'label'       => 'Boka golftid',
            'title'       => 'Boka golfsimulator i Karlskrona | Swing',
            'description' => 'Boka inomhusgolf hos Swing Golf & Lounge i Nättraby. Välj bland fyra TrackMan iO-simulatorer och spela alla dagar {hours}.',
            'nav'         => 'priser',
            'priority'    => '0.9',
        ],
        'trackman-io' => [
            'view'        => 'trackman',
            'label'       => 'TrackMan iO & simulatorer',
            'title'       => 'TrackMan iO & golfsimulatorer | Swing Karlskrona',
            'description' => 'Spela och träna i fyra simulatorer med TrackMan iO hos Swing Golf & Lounge i Nättraby. Läs om teknik, spel och våra simulatorbås.',
            'nav'         => 'anlaggningen',
            'priority'    => '0.8',
        ],
        'foretagsevent' => [
            'view'        => 'foretagsevent',
            'label'       => 'Företagsevent',
            'title'       => 'Företagsevent & After Work Karlskrona | Swing',
            'description' => 'Boka företagsevent med golf hos Swing i Karlskrona. Fyra TrackMan-simulatorer, lounge, catering och skräddarsydda upplägg på offert.',
            'nav'         => 'foretag',
            'priority'    => '0.8',
            'form'        => 'foretag',
        ],
        'gruppbokning' => [
            'view'        => 'gruppbokning',
            'label'       => 'Gruppbokning',
            'title'       => 'Gruppbokning & golf för sällskap | Swing',
            'description' => 'Boka inomhusgolf för kompisgäng, golfklubb, skola eller förening hos Swing i Nättraby. Fyra simulatorer och lounge för gemenskap.',
            'nav'         => 'foretag',
            'priority'    => '0.7',
            'form'        => 'grupp',
        ],
        'spelpotter' => [
            'view'        => 'spelpotter',
            'label'       => 'Spelpotter',
            'title'       => 'Spelpotter för inomhusgolf | Swing Golf & Lounge',
            'description' => 'Köp spelpott hos Swing Golf & Lounge och få extra spelvärde. Swing 1000, 2000 och 3000. Gäller ett år och används via Sweetspot.',
            'nav'         => 'spelpotter',
            'priority'    => '0.9',
            'order'       => 'spelpott',
        ],
        'friskvard' => [
            'view'        => 'friskvard',
            'label'       => 'Friskvård & Epassi',
            'title'       => 'Friskvård & Epassi – inomhusgolf | Swing',
            'description' => 'Använd ditt friskvårdsbidrag till inomhusgolf hos Swing i Karlskrona. Se Epassi-alternativ och information om hur du bokar.',
            'nav'         => 'friskvard',
            'priority'    => '0.8',
            'order'       => 'friskvard',
        ],
        'presentkort' => [
            'view'        => 'presentkort',
            'label'       => 'Presentkort',
            'title'       => 'Presentkort på golf i Karlskrona | Swing',
            'description' => 'Ge bort inomhusgolf med ett digitalt presentkort från Swing Golf & Lounge. Välj 100–1 000 kr och lös in det via Sweetspot.',
            'nav'         => 'presentkort',
            'priority'    => '0.8',
            'order'       => 'presentkort',
        ],
        'tavlingar' => [
            'view'        => 'tavlingar',
            'label'       => 'Tävlingar',
            'title'       => 'Golftävlingar & aktiviteter i Karlskrona | Swing',
            'description' => 'Delta i TrackMan-tävlingar och golfaktiviteter hos Swing i Nättraby. Öppna tävlingar, privata utmaningar, ligor och tourer.',
            'nav'         => 'tavlingar',
            'priority'    => '0.8',
            'form'        => 'tavling',
        ],
        'team-swing' => [
            'view'        => 'team-swing',
            'label'       => 'Team Swing',
            'title'       => 'Team Swing | Swing Golf & Lounge',
            'description' => 'Team Swing stöttar golfare som satsar på sin utveckling. Upptäck vårt engagemang för golfen och lär känna teamet när det presenteras.',
            'nav'         => 'tavlingar',
            'priority'    => '0.5',
        ],
        'om-oss' => [
            'view'        => 'om-oss',
            'label'       => 'Om oss',
            'title'       => 'Om Swing Golf & Lounge | Inomhusgolf Karlskrona',
            'description' => 'Lär känna Swing Golf & Lounge i Nättraby. Inspiration från skotska golfklubbar, ombonad lounge och fyra moderna TrackMan iO-simulatorer.',
            'nav'         => 'anlaggningen',
            'priority'    => '0.6',
        ],
        'kontakt' => [
            'view'        => 'kontakt',
            'label'       => 'Kontakt & hitta hit',
            'title'       => 'Kontakt & hitta hit | Swing Golf & Lounge',
            'description' => 'Hitta till Swing Golf & Lounge på {street} i Nättraby. Kontakt: {email}, {phone}. Öppet alla dagar {hours}.',
            'nav'         => 'kontakt',
            'priority'    => '0.8',
            'schema'      => 'business',
            'form'        => 'kontakt',
        ],
        'vanliga-fragor' => [
            'view'        => 'vanliga-fragor',
            'label'       => 'Vanliga frågor',
            'title'       => 'Vanliga frågor om inomhusgolf | Swing',
            'description' => 'Svar om bokning, priser, klädsel, entrékod, TrackMan, Epassi, presentkort och företag hos Swing Golf & Lounge i Karlskrona.',
            'nav'         => 'kontakt',
            'priority'    => '0.7',
            'schema'      => 'faq',
        ],
        'infor-besoket' => [
            'view'        => 'infor-besoket',
            'label'       => 'Inför ditt besök',
            'title'       => 'Inför besöket – regler & dörrkod | Swing',
            'description' => 'Allt inför ditt besök hos Swing: rena klubbor och skor, bokning, dörrkod, simulatorstart och vad du gör när du lämnar.',
            'nav'         => 'anlaggningen',
            'priority'    => '0.7',
        ],
        'kopvillkor' => [
            'view'        => 'kopvillkor',
            'label'       => 'Köpvillkor',
            'title'       => 'Köpvillkor | Swing Golf & Lounge',
            'description' => 'Villkor för köp av spelpotter och digitala presentkort hos Swing Golf & Lounge i Nättraby.',
            'nav'         => '',
            'priority'    => '0.2',
        ],
        'integritetspolicy' => [
            'view'        => 'integritetspolicy',
            'label'       => 'Integritetspolicy',
            'title'       => 'Integritetspolicy | Swing Golf & Lounge',
            'description' => 'Så behandlar Swing Golf & Lounge dina personuppgifter vid kontakt, köp, bokning, tävlingar, kamerabevakning och på webbplatsen – lagliga grunder, lagringstider och dina rättigheter.',
            'nav'         => '',
            'priority'    => '0.2',
        ],
    ];

    /**
     * Toppmenyn (rekommenderad i kraven). Punkter med 'children' får en undermeny;
     * själva punkten länkar till 'slug' (avsnittets huvudsida). Alla innehållssidor
     * ska finnas här så att de nås inom två klick (UX-02).
     */
    public array $mainNav = [
        ['label' => 'Anläggningen',      'slug' => 'inomhusgolf',   'children' => ['inomhusgolf', 'trackman-io', 'om-oss']],
        ['label' => 'Priser',            'slug' => 'priser'],
        ['label' => 'Spelpotter',        'slug' => 'spelpotter'],
        ['label' => 'Presentkort',       'slug' => 'presentkort'],
        ['label' => 'Företag & grupper', 'slug' => 'foretagsevent', 'children' => ['foretagsevent', 'gruppbokning']],
        ['label' => 'Tävlingar',         'slug' => 'tavlingar',     'children' => ['tavlingar', 'team-swing']],
        ['label' => 'Friskvård',         'slug' => 'friskvard'],
        ['label' => 'Kontakt',           'slug' => 'kontakt',       'children' => ['kontakt', 'infor-besoket', 'boka', 'vanliga-fragor']],
    ];

    /** Korta förklaringar under länkarna i undermenyernas paneler. */
    public array $menuBlurbs = [
        'inomhusgolf'    => 'Spela, träna och umgås året runt',
        'trackman-io'    => 'Fyra simulatorer och våra slagdukar',
        'om-oss'         => 'Klubbhuskänslan och människorna bakom',
        'foretagsevent'  => 'Kickoff, After Work och kundevent',
        'gruppbokning'   => 'Kompisgäng, klubbar och föreningar',
        'tavlingar'      => 'Öppna och privata tävlingar',
        'team-swing'     => 'Golfare som vågar satsa',
        'kontakt'        => 'Adress, telefon och e-post',
        'infor-besoket'  => 'Dörrkod, regler och utrustning',
        'boka'           => 'Så bokar du i appen eller på webben',
        'vanliga-fragor' => 'Svar på det mesta',
    ];

    /** Sidor som hör under en annan sida — ger brödsmulor som Start › Inomhusgolf › TrackMan iO. */
    public array $parents = [
        'trackman-io'  => 'inomhusgolf',
        'gruppbokning' => 'foretagsevent',
        'team-swing'   => 'tavlingar',
    ];

    /** Sidfotens länkkolumner (rubrik => sidor). */
    public array $footerNav = [
        'Boka & köp' => ['boka', 'priser', 'spelpotter', 'presentkort', 'friskvard'],
        'Swing'      => ['inomhusgolf', 'trackman-io', 'om-oss', 'tavlingar', 'team-swing'],
        'Hjälp'      => ['infor-besoket', 'vanliga-fragor', 'foretagsevent', 'gruppbokning', 'kontakt'],
    ];

    /** @return array<string, mixed>|null */
    public function find(string $slug): ?array
    {
        $slug = trim($slug, '/');

        return isset($this->pages[$slug]) ? ['slug' => $slug] + $this->pages[$slug] : null;
    }

    public function label(string $slug): string
    {
        return $this->pages[trim($slug, '/')]['label'] ?? $slug;
    }

    /** Fyller i faktaplatshållare från Config\Swing. */
    public function fill(string $text): string
    {
        $swing   = config('Swing');
        $pricing = service('pricing');

        return strtr($text, [
            '{hours}'     => $swing->hoursShort,
            '{fromPrice}' => $pricing->lowestPrice() . ' kr',
            '{phone}'     => $swing->phone,
            '{email}'     => $swing->email,
            '{street}'    => $swing->street,
        ]);
    }
}
