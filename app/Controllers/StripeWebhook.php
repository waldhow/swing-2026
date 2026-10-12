<?php

namespace App\Controllers;

use App\Libraries\Payments\PaymentException;
use CodeIgniter\HTTP\ResponseInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * Stripes webhook (/stripe/webhook/). Verifierar signaturen och levererar
 * betalda ordrar. Svarar snabbt med 200; vid tillfälligt fel 500 så att
 * Stripe försöker igen. Registrera endpointen i Stripe Dashboard med:
 *   checkout.session.completed, checkout.session.async_payment_succeeded,
 *   checkout.session.async_payment_failed, checkout.session.expired
 */
class StripeWebhook extends BaseController
{
    public function receive(): ResponseInterface
    {
        $secret = config('Payments')->stripeWebhookSecret;
        if ($secret === '') {
            return $this->response->setStatusCode(503)->setJSON(['error' => 'Webhook är inte konfigurerad.']);
        }

        try {
            $event = Webhook::constructEvent(
                (string) $this->request->getBody(),
                $this->request->getHeaderLine('Stripe-Signature'),
                $secret,
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Ogiltig signatur eller payload.']);
        }

        $sessionId = (string) ($event->data->object->id ?? '');

        try {
            match ($event->type) {
                'checkout.session.completed',
                'checkout.session.async_payment_succeeded' => service('checkoutService')->fulfill($sessionId),
                'checkout.session.async_payment_failed'    => service('checkoutService')->markEnded($sessionId, 'failed'),
                'checkout.session.expired'                 => service('checkoutService')->markEnded($sessionId, 'expired'),
                default                                    => null,
            };
        } catch (PaymentException $e) {
            log_message('error', 'Stripe-webhook {type} kunde inte hanteras: {msg}', ['type' => $event->type, 'msg' => $e->getMessage()]);

            return $this->response->setStatusCode(500)->setJSON(['error' => 'Försök igen.']);
        }

        return $this->response->setJSON(['received' => true]);
    }
}
