<?php

namespace App\Libraries\Payments;

use Config\Payments;
use Config\Swing;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * Stripe Checkout (hostad sida). Följer Stripes rekommendationer:
 *  - StripeClient-instans, nyckel från .env, SDK:ns fasta API-version
 *  - inga payment_method_types — betalsätt (kort, Swish, Apple/Google Pay …)
 *    styrs från Stripe Dashboard
 *  - idempotensnyckel per kassaförsök
 *  - integration_identifier för att följa flödet i Dashboard
 */
final class StripeCheckoutGateway implements CheckoutGateway
{
    private StripeClient $client;

    public function __construct(private readonly Payments $config, private readonly Swing $swing)
    {
        $this->client = new StripeClient([
            'api_key'             => $config->stripeSecretKey,
            // Fast API-version (SDK:ns), så att Stripe inte ändrar beteende under oss.
            'stripe_version'      => \Stripe\Util\ApiVersion::CURRENT,
            'max_network_retries' => 2,
        ]);
    }

    public function createSession(array $order, string $successUrl, string $cancelUrl, string $idempotencyKey): array
    {
        try {
            $session = $this->client->checkout->sessions->create([
                'mode'                   => 'payment',
                'locale'                 => 'sv',
                'customer_email'         => $order['buyer_email'],
                'client_reference_id'    => $order['reference'],
                'success_url'            => $successUrl,
                'cancel_url'             => $cancelUrl,
                'expires_at'             => time() + max(30, $this->config->checkoutExpiresMinutes) * 60,
                'integration_identifier' => $this->config->integrationIdentifier,
                'line_items'             => [[
                    'quantity'   => 1,
                    'price_data' => [
                        'currency'     => 'sek',
                        'unit_amount'  => (int) $order['pay_amount'] * 100,
                        'product_data' => [
                            'name'        => $order['product_name'],
                            'description' => (int) $order['value_amount'] !== (int) $order['pay_amount']
                                ? 'Spelvärde ' . number_format((int) $order['value_amount'], 0, ',', ' ') . ' kr hos ' . $this->swing->name
                                : $this->swing->name,
                        ],
                    ],
                ]],
                'metadata'            => ['order_reference' => $order['reference'], 'order_type' => $order['order_type']],
                'payment_intent_data' => [
                    'description' => $order['product_name'] . ' – ' . $this->swing->name . ' (' . $order['reference'] . ')',
                    'metadata'    => ['order_reference' => $order['reference'], 'order_type' => $order['order_type']],
                ],
            ], ['idempotency_key' => $idempotencyKey]);
        } catch (ApiErrorException $e) {
            log_message('error', 'Stripe: kunde inte skapa Checkout-session för {ref}: {msg}', ['ref' => $order['reference'], 'msg' => $e->getMessage()]);

            throw new PaymentException('Betalningen kunde inte startas.', 0, $e);
        }

        return ['id' => $session->id, 'url' => $session->url];
    }

    public function retrieveSession(string $sessionId): array
    {
        try {
            $session = $this->client->checkout->sessions->retrieve($sessionId);
        } catch (ApiErrorException $e) {
            log_message('error', 'Stripe: kunde inte hämta session {id}: {msg}', ['id' => $sessionId, 'msg' => $e->getMessage()]);

            throw new PaymentException('Betalningen kunde inte kontrolleras.', 0, $e);
        }

        $intent = $session->payment_intent;

        return [
            'id'                  => $session->id,
            'url'                 => $session->url,
            'status'              => (string) $session->status,
            'payment_status'      => (string) $session->payment_status,
            'client_reference_id' => $session->client_reference_id,
            'amount_total'        => $session->amount_total,
            'currency'            => $session->currency,
            'payment_intent'      => is_string($intent) ? $intent : ($intent->id ?? null),
        ];
    }
}
