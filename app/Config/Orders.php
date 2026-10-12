<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Beställning av spelpotter och presentkort (Swish, hanteras i swing-order).
 *
 * Flödet: kunden fyller i formuläret → ordern sparas i databasen → skickas
 * vidare till swing-order via vald kanal → kunden får Swish-instruktioner.
 * swing-order reserverar Sweetspot-koden och mejlar den när Swish-betalningen
 * är verifierad. Se docs/swing-order.md för formatet.
 *
 * Kanaler:
 *  - 'mail' — strukturerat mejl (ämne "Order") till brevlådan som swing-order
 *    synkar via IMAP. Fungerar utan ändringar i swing-order.
 *  - 'api'  — HTTP POST till swing-order. Kräver att endpointen finns där.
 *
 * Formulären visas bara när kanalen är fullständigt konfigurerad — annars
 * hänvisar sidorna till telefon/e-post (ingen död köpknapp, TECH-06).
 */
class Orders extends BaseConfig
{
    public bool $enabled = true;

    /** Direktköp av friskvårdspott med personnummer (öppnat av ägaren 2026-10-11, B04). */
    public bool $friskvardDirect = true;

    public string $channel = 'mail';

    /** Brevlådan som swing-order synkar. Sätts i .env: orders.mailTo */
    public string $mailTo = '';

    /** swing-order tolkar bara strukturerade fält när ämnet är exakt "Order". */
    public string $mailSubject = 'Order';

    /** API-kanalen (orders.apiUrl, orders.apiToken i .env). */
    public string $apiUrl = '';

    public string $apiToken = '';

    public int $apiTimeoutSeconds = 8;

    public string $swishNumber = '123 217 2559';

    /** Bekräftelse till kunden med Swish-instruktionerna. */
    public bool $sendCustomerReceipt = true;

    /** Lokala orderkopior rensas efter så här många dagar (swing-order är huvudregistret). */
    public int $retentionDays = 90;

    public function isOperational(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        return match ($this->channel) {
            'mail'  => filter_var($this->mailTo, FILTER_VALIDATE_EMAIL) !== false,
            'api'   => str_starts_with($this->apiUrl, 'https://') && $this->apiToken !== '',
            default => false,
        };
    }

    /**
     * Friskvårdspott direkt kräver dessutom krypteringsnyckel — personnummer
     * sparas aldrig okrypterat (encryption.key i .env).
     */
    public function canSellWellnessDirect(): bool
    {
        return $this->friskvardDirect && $this->isOperational() && config('Encryption')->key !== '';
    }

    /** Presentkortsdesign kan bara föras över via API-kanalen. */
    public function supportsGiftCardDesign(): bool
    {
        return $this->channel === 'api';
    }
}
