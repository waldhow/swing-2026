<?php

namespace App\Libraries\Orders;

use Config\Orders;
use Throwable;

/**
 * Skickar beställningen till swing-order via HTTP. Kontraktet beskrivs i
 * docs/swing-order.md — endpointen finns inte i swing-order ännu, så kanalen
 * används först när den är byggd och orders.channel = api.
 *
 * Idempotens: orderreferensen skickas som Idempotency-Key, så ett omförsök
 * från admin skapar ingen dubblett.
 */
final class ApiOrderGateway implements OrderGateway
{
    public function __construct(private readonly Orders $config)
    {
    }

    public function name(): string
    {
        return 'api';
    }

    public function send(array $order): void
    {
        if (! $this->config->isOperational()) {
            throw new OrderForwardException('API-kanalen saknar adress eller token (orders.apiUrl, orders.apiToken).');
        }

        $payload = array_filter([
            'externalRef'    => $order['reference'],
            'source'         => 'WEB',
            'type'           => $order['order_type'],
            'amount'         => (int) $order['pay_amount'],
            'value'          => (int) $order['value_amount'],
            'name'           => $order['buyer_name'],
            'email'          => $order['buyer_email'],
            'personalNumber' => $order['order_type'] === 'FRISKVARD' ? ($order['personal_number'] ?? null) : null,
            'giftCardName'   => $order['gift_card_name'] ?? null,
            'giftCardDesign' => $order['gift_card_design'] ?? null,
            'productName'    => $order['product_name'],
        ], static fn ($v): bool => $v !== null && $v !== '');

        try {
            $response = service('curlrequest', ['timeout' => $this->config->apiTimeoutSeconds, 'http_errors' => false], null, false)
                ->post($this->config->apiUrl, [
                    'headers' => [
                        'Authorization'   => 'Bearer ' . $this->config->apiToken,
                        'Idempotency-Key' => $order['reference'],
                        'Accept'          => 'application/json',
                    ],
                    'json' => $payload,
                ]);
        } catch (Throwable $e) {
            log_message('error', 'Order {ref}: swing-order API svarade inte: {msg}', ['ref' => $order['reference'], 'msg' => $e->getMessage()]);

            throw new OrderForwardException('swing-order svarade inte.', 0, $e);
        }

        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            log_message('error', 'Order {ref}: swing-order API svarade {status}', ['ref' => $order['reference'], 'status' => $status]);

            throw new OrderForwardException("swing-order svarade med status {$status}.");
        }
    }
}
