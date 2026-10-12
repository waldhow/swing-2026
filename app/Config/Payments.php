<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Betalning av spelpotter, presentkort och friskvårdspotter.
 *
 *  - 'swish'  (standard) — manuell Swish: kunden får Swish-instruktioner och
 *             swing-order verifierar betalningen. Dagens flöde.
 *  - 'stripe' — Stripe Checkout: kunden betalar direkt (kort, Swish m.fl. enligt
 *             Stripe Dashboard). Ordern lämnas till swing-order först när
 *             betalningen är bekräftad via webhook.
 *
 * Stripe aktiveras bara när provider = stripe OCH nyckel och webhookhemlighet
 * är satta (fail closed, TECH-06). Nycklar sätts i .env, aldrig i koden:
 *   payments.provider = stripe
 *   payments.stripeSecretKey = rk_live_…   (begränsad nyckel rekommenderas)
 *   payments.stripeWebhookSecret = whsec_…
 */
class Payments extends BaseConfig
{
    public string $provider = 'swish';

    /** Begränsad nyckel (rk_) med rättigheter för Checkout Sessions — eller hemlig nyckel (sk_). */
    public string $stripeSecretKey = '';

    /** Signeringshemlighet för webhook-endpointen /stripe/webhook/ (whsec_…). */
    public string $stripeWebhookSecret = '';

    /** Etikett för att följa kassaflödet i Stripe Dashboard (integration_identifier). */
    public string $integrationIdentifier = 'swing-webb-kassa-qhtrwmzk';

    /** Hur länge en Checkout-session är öppen innan den löper ut (minst 30). */
    public int $checkoutExpiresMinutes = 60;

    /** Betalsätten i sajtens texter. Håll i synk med Stripe Dashboard → Betalmetoder. */
    public string $stripeMethodsText = 'kort eller Swish';

    public function stripeEnabled(): bool
    {
        return $this->provider === 'stripe'
            && preg_match('/^(rk|sk)_(live|test)_/', $this->stripeSecretKey) === 1
            && str_starts_with($this->stripeWebhookSecret, 'whsec_');
    }

    /** Hur kunden betalar, för löptext: "betala med …". */
    public function methodsText(): string
    {
        return $this->stripeEnabled() ? $this->stripeMethodsText : 'Swish';
    }

    public function isTestMode(): bool
    {
        return str_contains($this->stripeSecretKey, '_test_');
    }
}
