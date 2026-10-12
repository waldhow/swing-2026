<?php

namespace App\Libraries\Payments;

/**
 * Skapar och hämtar betalsessioner. StripeCheckoutGateway pratar med Stripe;
 * i tester ersätts den med en fejk (Services::injectMock('checkoutGateway', …)).
 */
interface CheckoutGateway
{
    /**
     * @param array<string, mixed> $order        rad ur orders
     * @param string               $successUrl   med {CHECKOUT_SESSION_ID}
     * @param string               $cancelUrl
     * @param string               $idempotencyKey
     *
     * @return array{id: string, url: string}
     *
     * @throws PaymentException
     */
    public function createSession(array $order, string $successUrl, string $cancelUrl, string $idempotencyKey): array;

    /**
     * @return array{id: string, url: ?string, status: string, payment_status: string, client_reference_id: ?string,
     *               amount_total: ?int, currency: ?string, payment_intent: ?string}
     *
     * @throws PaymentException
     */
    public function retrieveSession(string $sessionId): array;
}
