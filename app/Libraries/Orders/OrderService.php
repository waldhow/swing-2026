<?php

namespace App\Libraries\Orders;

use App\Models\OrderModel;
use Config\Orders;
use Config\Payments;
use Config\Swing;
use RuntimeException;
use Throwable;

/**
 * Lägger beställningar: sparar lokalt, lämnar över till swing-order och
 * mejlar kunden. Lokalt sparad order går aldrig förlorad — misslyckas
 * överlämningen kan admin skicka igen.
 *
 * Manuell Swish: ordern lämnas över direkt och kunden får Swish-instruktioner.
 * Stripe: ordern väntar på betalning och lämnas över först när CheckoutService
 * har bekräftat betalningen (fulfillPaid).
 */
final class OrderService
{
    public function __construct(
        private readonly OrderModel $orders,
        private readonly OrderGateway $gateway,
        private readonly Orders $config,
        private readonly Swing $swing,
        private readonly Payments $payments,
    ) {
    }

    /**
     * @param array{order_type: string, product_name: string, pay_amount: int, value_amount: int,
     *              buyer_name: string, buyer_email: string, campaign_id?: int|null,
     *              personal_number?: string|null, gift_card_name?: string|null, gift_card_design?: string|null} $data
     *
     * @return array<string, mixed> den sparade ordern
     */
    public function place(array $data): array
    {
        $this->orders->purgeExpired($this->config->retentionDays);

        $personalNumber = $data['personal_number'] ?? null;
        unset($data['personal_number']);

        if ($personalNumber !== null && $personalNumber !== '') {
            $data['personal_number_enc'] = $this->encrypt($personalNumber);
        }

        $data['channel']          = $this->gateway->name();
        $data['payment_provider'] = $this->payments->stripeEnabled() ? 'stripe' : 'swish';
        $data['payment_status']   = 'pending';
        $order                    = $this->orders->createOrder($data);

        if ($order['payment_provider'] === 'stripe') {
            // Lämnas över först när betalningen är bekräftad.
            return $order;
        }

        $this->forward($order, $personalNumber);
        $this->sendCustomerReceipt($order);

        return $this->orders->find($order['id']);
    }

    /**
     * Betald Stripe-order: lämna över till swing-order och bekräfta för kunden.
     * Anropas bara av den som vann OrderModel::claimPaid(), alltså en gång per order.
     */
    public function fulfillPaid(array $order): void
    {
        $this->forward($order);
        $this->sendCustomerReceipt($this->orders->find($order['id']));
    }

    /**
     * Lämnar över ordern till swing-order och noterar utfallet.
     */
    public function forward(array $order, ?string $personalNumber = null): bool
    {
        if ($personalNumber === null && ! empty($order['personal_number_enc'])) {
            $personalNumber = $this->decrypt($order['personal_number_enc']);
        }

        $attempts = (int) $order['forward_attempts'] + 1;

        try {
            $this->gateway->send($order + ['personal_number' => $personalNumber]);
        } catch (OrderForwardException $e) {
            $this->orders->update($order['id'], [
                'forward_attempts' => $attempts,
                'forward_error'    => mb_substr($e->getMessage(), 0, 255),
            ]);

            return false;
        }

        $this->orders->update($order['id'], [
            'forward_attempts' => $attempts,
            'forward_error'    => null,
            'forwarded_at'     => date('Y-m-d H:i:s'),
            'channel'          => $this->gateway->name(),
        ]);

        return true;
    }

    public function resend(int $id): bool
    {
        $order = $this->orders->find($id);
        if ($order === null) {
            throw new RuntimeException('Ordern finns inte.');
        }
        if (OrderModel::awaitsPayment($order)) {
            throw new RuntimeException('Ordern är inte betald.');
        }

        return $this->forward($order);
    }

    private function sendCustomerReceipt(array $order): void
    {
        if (! $this->config->sendCustomerReceipt) {
            return;
        }

        try {
            $email = service('email')->clear(true);
            $email->setTo($order['buyer_email']);
            $email->setReplyTo($this->swing->email, $this->swing->name);
            $email->setSubject("Din beställning hos {$this->swing->name} ({$order['reference']})");
            $email->setMailType('text');
            $email->setMessage(view('emails/order_customer', [
                'order'  => $order,
                'swing'  => $this->swing,
                'config' => $this->config,
            ], ['debug' => false]));

            if ($email->send()) {
                $this->orders->update($order['id'], ['customer_mailed_at' => date('Y-m-d H:i:s')]);
            }
        } catch (Throwable $e) {
            log_message('error', 'Kvitto för order {ref} kunde inte mejlas: {msg}', ['ref' => $order['reference'], 'msg' => $e->getMessage()]);
        }
    }

    private function encrypt(string $value): string
    {
        return base64_encode(service('encrypter')->encrypt($value));
    }

    private function decrypt(string $value): ?string
    {
        try {
            return service('encrypter')->decrypt(base64_decode($value, true) ?: '');
        } catch (Throwable) {
            return null;
        }
    }
}
