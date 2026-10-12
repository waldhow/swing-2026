<?php

namespace App\Libraries\Orders;

/**
 * Kanal som lämnar över en beställning till swing-order.
 */
interface OrderGateway
{
    /**
     * @param array<string, mixed> $order rad ur orders-tabellen (personnummer dekrypterat i 'personal_number')
     *
     * @throws OrderForwardException när överlämningen misslyckas
     */
    public function send(array $order): void;

    /** Kort namn för loggar och admin, t.ex. "mail". */
    public function name(): string;
}
