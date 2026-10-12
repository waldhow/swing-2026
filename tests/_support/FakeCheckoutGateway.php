<?php

namespace Tests\Support;

use App\Libraries\Payments\CheckoutGateway;
use App\Libraries\Payments\PaymentException;

/**
 * Stripe Checkout utan nätverk. Håller sessionerna i minnet; pay(), expire()
 * och tamper() simulerar det som händer i Stripes kassa.
 */
final class FakeCheckoutGateway implements CheckoutGateway
{
    /** @var array<string, array<string, mixed>> */
    public array $sessions = [];

    /** @var list<array<string, string>> anropen till createSession */
    public array $created = [];

    public bool $failCreate = false;

    public function createSession(array $order, string $successUrl, string $cancelUrl, string $idempotencyKey): array
    {
        if ($this->failCreate) {
            throw new PaymentException('Stripe svarar inte.');
        }

        $id = 'cs_test_fake' . (count($this->sessions) + 1);

        $this->sessions[$id] = [
            'id'                  => $id,
            'url'                 => 'https://checkout.stripe.com/c/pay/' . $id,
            'status'              => 'open',
            'payment_status'      => 'unpaid',
            'client_reference_id' => $order['reference'],
            'amount_total'        => (int) $order['pay_amount'] * 100,
            'currency'            => 'sek',
            'payment_intent'      => null,
        ];
        $this->created[] = ['reference' => $order['reference'], 'successUrl' => $successUrl, 'cancelUrl' => $cancelUrl, 'idempotencyKey' => $idempotencyKey];

        return ['id' => $id, 'url' => $this->sessions[$id]['url']];
    }

    public function retrieveSession(string $sessionId): array
    {
        if (! isset($this->sessions[$sessionId])) {
            throw new PaymentException('No such checkout.session: ' . $sessionId);
        }

        return $this->sessions[$sessionId];
    }

    /** Kunden slutför kassan. 'unpaid' = fördröjt betalsätt som ännu inte gått igenom. */
    public function pay(string $sessionId, string $paymentStatus = 'paid'): void
    {
        $this->sessions[$sessionId]['status']         = 'complete';
        $this->sessions[$sessionId]['payment_status'] = $paymentStatus;
        $this->sessions[$sessionId]['payment_intent'] = 'pi_test_' . substr($sessionId, 8);
        $this->sessions[$sessionId]['url']            = null;
    }

    public function expire(string $sessionId): void
    {
        $this->sessions[$sessionId]['status'] = 'expired';
        $this->sessions[$sessionId]['url']    = null;
    }

    /** @param array<string, mixed> $fields */
    public function tamper(string $sessionId, array $fields): void
    {
        $this->sessions[$sessionId] = $fields + $this->sessions[$sessionId];
    }
}
