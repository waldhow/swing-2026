<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Bokningsintegrationen — enda bytespunkten för bokningsprovider (STR-01, STR-02).
 *
 * Under 2026 bokar gästerna i Sweetspot. När det egna bokningssystemet lanseras
 * (planerat hösten 2027) byts provider, länkar och instruktionstexter här, utan
 * att sidornas innehåll behöver skrivas om. Lansera aldrig ett nytt system
 * automatiskt — ägaren byter efter verifiering.
 *
 * Tom $bookingUrl betyder att ingen verifierad bokningslänk finns: alla
 * boka-knappar leder då till /boka/ i stället för till en extern adress.
 */
class Booking extends BaseConfig
{
    public string $provider = 'Sweetspot';

    /** Verifierad 2026-10-11: används skarpt på swinggolf.se och svarar 200. */
    public string $bookingUrl = 'https://book.sweetspot.io/clubs/swing-golf-lounge';

    /** Bokningslänkar öppnas i ny flik så att besökaren har kvar sajten (ägarens önskemål). */
    public bool $openInNewTab = true;

    /**
     * Providerns app (inte en egen Swing-app). Visas i appkortet på start,
     * boka och inför besöket. Länkar bekräftade av ägaren 2026-10-11.
     */
    public array $appLinks = [
        'App Store'   => 'https://apps.apple.com/se/app/sweetspot/id1234606187',
        'Google Play' => 'https://play.google.com/store/apps/details?id=com.hisweetspot.sweetspot',
    ];

    public string $appName = 'Sweetspot';

    public string $appRole = 'Boka tiden';

    /** Logotypen från swinggolf.se — svart symbol som läggs på Sweetspots gula ruta. */
    public string $appIcon = 'assets/apps/sweetspot-logo.png';

    public string $appIconBackground = '#fcf26e';

    public string $appText = 'Enklast bokar du i Sweetspot-appen. Där bokar och betalar du din tid i simulatorn och har din dörrkod nära till hands. Ladda ner appen och registrera en användare.';

    /** Kort uppmaning som visas vid butiksknapparna (t.ex. i startsidans hero). */
    public string $appPitch = 'Boka enklast i Sweetspot-appen';

    /** @var list<string> */
    public array $appFeatures = [
        'Boka och betala simulatortid',
        'Dörrkoden finns i appen och i bekräftelsemejlet',
        'Betala med spelpott eller presentkort via + Använd voucher',
    ];

    /** Så bokar du — visas på /boka/. Appen först, webbläsaren som alternativ. */
    public array $bookingSteps = [
        'Ladda ner Sweetspot-appen och sök efter Swing Golf & Lounge.',
        'Välj datum, tid och simulator.',
        'Slutför bokningen med tillgängligt betalningsalternativ.',
        'Ta emot din bokningsbekräftelse och personliga dörrkod.',
    ];

    /** Så löser du in presentkort eller voucher. */
    public array $voucherSteps = [
        'Öppna Sweetspot-appen och sök efter Swing Golf & Lounge.',
        'Välj tid och simulator.',
        'Gå till Mer (…) → Vouchers → + och ange presentkortsnumret.',
        'Välj + Använd voucher som betalningsmedel i kassan.',
    ];

    /** Knappen i kassan där spelpott och presentkort används. */
    public string $voucherLabel = '+ Använd voucher';

    /** Hur kunden får och lägger in spelpotten. */
    public string $creditLinkText = 'Du får en kod via e-post. Lägg in den i Sweetspot under Mer (…) → Vouchers.';

    /** Var gästen hanterar och avbokar sin bokning. */
    public string $manageText = 'Hantera bokningen via Sweetspot.';

    public function hasBookingUrl(): bool
    {
        return $this->bookingUrl !== '' && str_starts_with($this->bookingUrl, 'https://');
    }

    /** Målet för alla boka-knappar: verifierad extern länk, annars /boka/. */
    public function ctaUrl(): string
    {
        return $this->hasBookingUrl() ? $this->bookingUrl : site_url('boka/');
    }
}
