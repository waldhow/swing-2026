<?php

namespace App\Libraries\Orders;

use Config\Orders;

/**
 * Skickar beställningen som ett strukturerat mejl till brevlådan som
 * swing-order synkar via IMAP. Formatet måste matcha swing-orders parser
 * (server/lib/mailIngestion.ts): ämnet exakt "Order" och en rad per fält.
 */
final class MailOrderGateway implements OrderGateway
{
    public function __construct(private readonly Orders $config)
    {
    }

    public function name(): string
    {
        return 'mail';
    }

    public function send(array $order): void
    {
        if (! $this->config->isOperational()) {
            throw new OrderForwardException('Mejlkanalen saknar mottagare (orders.mailTo).');
        }

        $email = service('email')->clear(true);
        $email->setTo($this->config->mailTo);
        $email->setReplyTo($order['buyer_email'], $order['buyer_name']);
        $email->setSubject($this->config->mailSubject);
        $email->setMailType('text');
        $email->setMessage(self::body($order));

        if (! $email->send(false)) {
            log_message('error', 'Order {ref} kunde inte mejlas till swing-order: {debug}', [
                'ref'   => $order['reference'],
                'debug' => strip_tags($email->printDebugger(['headers'])),
            ]);

            throw new OrderForwardException('Mejlet till orderbrevlådan kunde inte skickas.');
        }
    }

    /**
     * Mejlets brödtext. Fälten type/price/email/name tolkas av swing-order;
     * övriga rader (referens, spelvärde, design) är information för personalen
     * och ignoreras av parsern.
     */
    public static function body(array $order): string
    {
        $lines = [
            'type:' . (OrderTypes::MAIL_TYPES[$order['order_type']] ?? $order['order_type']),
            'price:' . (int) $order['pay_amount'],
            'email:' . self::clean($order['buyer_email']),
            'name:' . self::clean($order['buyer_name']),
        ];

        if ($order['order_type'] === 'FRISKVARD' && ! empty($order['personal_number'])) {
            $lines[] = 'personnummer:' . self::clean($order['personal_number']);
        }

        if ($order['order_type'] === 'PRESENTKORT' && ! empty($order['gift_card_name'])) {
            $lines[] = 'name on giftcard:' . self::clean($order['gift_card_name']);
        }

        $lines[] = '';
        $lines[] = 'referens:' . $order['reference'];
        if (($order['payment_provider'] ?? '') === 'stripe' && ($order['payment_status'] ?? '') === 'paid') {
            // Information till personalen: betalningen är redan bekräftad via Stripe.
            $lines[] = 'betalning:Stripe (betald ' . date('Y-m-d H:i', strtotime((string) $order['paid_at'])) . ')';
            $lines[] = 'stripe:' . ($order['stripe_payment_intent'] ?? '');
        }
        $lines[] = 'produkt:' . self::clean($order['product_name']);
        $lines[] = 'spelvarde:' . (int) $order['value_amount'];
        $lines[] = 'kalla:swinggolf.se';

        return implode("\n", $lines) . "\n";
    }

    /** En rad per fält — radbrytningar i kunddata får aldrig skapa nya fält. */
    private static function clean(string $value): string
    {
        return trim(preg_replace('/[\r\n\t]+/', ' ', $value) ?? '');
    }
}
