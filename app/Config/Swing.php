<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Verksamhetsfakta — den enda källan för namn, adress, öppettider, priser och
 * produkter (SEO-03). Allt som visas på sajten, i strukturerad data och i
 * llms.txt läses härifrån, så en ändring här slår igenom överallt.
 *
 * Värdena är bekräftade av ägaren (AI-specen avsnitt 2, "Låsta fakta").
 * Ändra inte belopp tyst — Epassi 2 500 → 2 700 rättades av ägaren 2026-10-11 (B03).
 */
class Swing extends BaseConfig
{
    public string $name = 'Swing Golf & Lounge';

    /** Bolaget bakom (köpvillkor, integritetspolicy). */
    public string $legalName = 'Pomada AB';

    public string $orgNumber = '559369-9019';

    /** Startår — bekräftat av ägaren 2026-10-11 (står även i logotypen: ESTB 2022). */
    public int $foundingYear = 2022;

    public string $street = 'Idrottsvägen 2B';

    public string $postalCode = '373 30';

    public string $locality = 'Nättraby';

    public string $municipality = 'Karlskrona';

    public string $region = 'Blekinge';

    public string $countryCode = 'SE';

    public string $phone = '0455-32 34 01';

    public string $phoneE164 = '+46455323401';

    public string $email = 'info@swinggolf.se';

    /** Öppettider, alla dagar. Lokalen är normalt obemannad. */
    public string $opens = '06:00';

    public string $closes = '00:00';

    public string $hoursText = 'Alla dagar 06.00–00.00';

    public string $hoursShort = '06–00';

    public array $socials = [
        'facebook'  => 'https://www.facebook.com/swinggolfsverige/',
        'instagram' => 'https://www.instagram.com/swinggolfsverige/',
    ];

    /** Extern kartlänk — ingen inbäddad karta som sätter cookies (AI-spec 4.5). */
    public string $mapUrl = 'https://www.google.com/maps/search/?api=1&query=Idrottsv%C3%A4gen+2B%2C+373+30+N%C3%A4ttraby';

    public int $simulatorCount = 4;

    public string $simulatorTech = 'TrackMan iO';

    /** Slagdukarnas ungefärliga storlek per simulator (bredd × höjd i meter). */
    public array $simulatorScreens = [
        1 => '5,5 × 3',
        2 => '4,5 × 3',
        3 => '5,5 × 3',
        4 => '4,5 × 3',
    ];

    /** Rekommendation, inte en maxgräns (ingen fast maxgräns per simulator). */
    public string $recommendedPlayers = '3–4';

    public string $eventGolfers = '16–20';

    public int $eventMaxGuests = 25;

    public int $doorCodeDigits = 4;

    /** Koden fungerar så här många minuter före och efter bokningens START. */
    public int $doorCodeWindowMinutes = 20;

    public int $freeCancellationHours = 3;

    /**
     * Pris per simulator och timme (inte per person). Dagar: 1 = måndag … 7 = söndag.
     * 'to' => '24:00' betyder midnatt. Perioderna måste täcka 06–24 alla dagar
     * utan överlapp — Pricing::validate() kontrollerar det.
     */
    public array $pricePeriods = [
        ['label' => 'Alla dagar 06.00–08.00',    'days' => [1, 2, 3, 4, 5, 6, 7], 'from' => '06:00', 'to' => '08:00', 'price' => 150],
        ['label' => 'Måndag–fredag 08.00–15.00', 'days' => [1, 2, 3, 4, 5],       'from' => '08:00', 'to' => '15:00', 'price' => 250],
        ['label' => 'Måndag–fredag 15.00–00.00', 'days' => [1, 2, 3, 4, 5],       'from' => '15:00', 'to' => '24:00', 'price' => 280],
        ['label' => 'Lördag–söndag 08.00–00.00', 'days' => [6, 7],                'from' => '08:00', 'to' => '24:00', 'price' => 280],
    ];

    /** Antal personer i räkneexemplet för pris per person (UX-03). */
    public int $priceExamplePlayers = 4;

    /**
     * Ordinarie spelpotter. Gäller ett år, levereras som kod via e-post, är
     * personliga och kan inte överlåtas (ägarens besked 2026-10-11/12).
     * Hålls alltid isär från Epassi-potterna (CONTENT-04).
     */
    public array $creditPots = [
        ['name' => 'Swing 1000', 'pay' => 1000, 'value' => 1150],
        ['name' => 'Swing 2000', 'pay' => 2000, 'value' => 2400],
        ['name' => 'Swing 3000', 'pay' => 3000, 'value' => 3750],
    ];

    /**
     * Epassi-alternativ (friskvård, personliga). 2 500 → 2 700 är rättat och
     * bekräftat av ägaren — ändra bara efter nytt besked (B03).
     */
    public array $epassiPots = [
        ['name' => 'Swing 500',  'pay' => 500,  'value' => 500],
        ['name' => 'Swing 1000', 'pay' => 1000, 'value' => 1000],
        ['name' => 'Swing 2000', 'pay' => 2000, 'value' => 2150],
        ['name' => 'Swing 2500', 'pay' => 2500, 'value' => 2700],
        ['name' => 'Swing 3000', 'pay' => 3000, 'value' => 3350],
    ];

    /**
     * Friskvårdspotter köpta direkt hos Swing med personnummer (samma belopp som
     * på tidigare swinggolf.se/friskvard). Personliga, giltiga i ett år.
     */
    public array $wellnessPots = [
        ['name' => 'Friskvårdspott 500',  'pay' => 500,  'value' => 500],
        ['name' => 'Friskvårdspott 1000', 'pay' => 1000, 'value' => 1150],
        ['name' => 'Friskvårdspott 2000', 'pay' => 2000, 'value' => 2400],
        ['name' => 'Friskvårdspott 3000', 'pay' => 3000, 'value' => 3750],
    ];

    /**
     * TrackMan Golf-appen — inloggning i simulatorn och slagdata. Frivillig:
     * man kan också spela som gäst. Länkar bekräftade av ägaren 2026-10-11.
     */
    public array $trackmanApp = [
        'name'     => 'TrackMan Golf',
        'role'     => 'Inloggning och analys',
        'icon'     => 'assets/apps/trackman-logo.png',
        'iconBackground' => 'transparent',
        'text'     => 'TrackMan Golf-appen använder du för att logga in i simulatorn. Genom appen får du också tillgång till detaljerad data om dina slag, kan följa din utveckling över tid och jämföra dina prestationer med andra golfare.',
        'features' => [
            'Logga in i simulatorn – eller spela som gäst',
            'Detaljerad data om dina slag',
            'Följ din utveckling över tid',
        ],
        'links' => [
            'App Store'   => 'https://apps.apple.com/se/app/trackman-golf/id1245413916',
            'Google Play' => 'https://play.google.com/store/apps/details?id=dk.TrackMan.Range&hl=sv',
        ],
    ];

    public string $epassiUrl = 'https://services.epassi.se/ui/merchant-search/site/183651';

    public int $creditValidityMonths = 12;

    /** Digitala presentkort, 100–1 000 kr i steg om 100 kr, giltiga ett år (CONTENT-03). */
    public int $giftCardMin = 100;

    public int $giftCardMax = 1000;

    public int $giftCardStep = 100;

    public int $giftCardValidityMonths = 12;

    /**
     * Presentkortsdesigner — nycklarna är swing-orders giftCardDesign-värden.
     * Jul, Mors dag och Fars dag kan bara väljas under sin säsong (App\Libraries\GiftCardDesigns).
     */
    public array $giftCardDesigns = [
        'STANDARD' => 'Standard',
        'FARSDAG'  => 'Fars dag',
        'MORSDAG'  => 'Mors dag',
        'JUL'      => 'Jul',
    ];

    /** Partners med egen webbplats — länkas i ny flik där de nämns (ägarens önskemål). */
    public array $partnerLinks = [
        'Nättraby Vägkrog' => 'https://www.nattrabyvagkrog.se/',
    ];

    /**
     * Tidsbegränsade erbjudanden som visas direkt under startsidans hero, från och
     * med 'from' till och med 'until' (svensk tid). Utgångna erbjudanden döljs av
     * sig själva – startsidan cachas en timme, så de försvinner senast 01.00.
     * 'titleEm' är rubrikens kursiva slut.
     */
    public array $promotions = [
        [
            'id'            => 'hostlov-2026',
            'from'          => '2026-10-25',
            'until'         => '2026-10-30',
            'eyebrow'       => 'Höstlov · 26–30 oktober',
            'title'         => 'Lovet blev just',
            'titleEm'       => 'varmare.',
            'text'          => 'Grått och blött ute? Hos oss är det varmt, torrt och skönt. Ta med barnen, kompisarna eller hela familjen och spela 20 % billigare måndag–fredag 26–30 oktober.',
            'discount'      => '−20 %',
            'discountLabel' => 'på golftid',
            'code'          => 'swing-hostlov',
        ],
    ];

    /** Partners som får nämnas med namn (inga logotyper utan godkännande, B09). */
    public array $partners = [
        'Trummenäs GK',
        'Carlskrona GK',
        'Aspero Gymnasium',
        'Nättraby Vägkrog',
    ];

    public function addressLine(): string
    {
        return $this->street . ', ' . $this->postalCode . ' ' . $this->locality;
    }
}
