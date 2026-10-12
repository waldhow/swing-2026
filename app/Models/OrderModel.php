<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    /** Ordertyper — värdena är swing-orders typnamn i mejlformatet. */
    public const TYPES = [
        'SPELPOTT'    => 'Spelpott',
        'PRESENTKORT' => 'Presentkort',
        'FRISKVARD'   => 'Friskvård',
    ];

    protected $table         = 'orders';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'reference', 'public_token', 'order_type', 'product_name', 'campaign_id', 'pay_amount',
        'value_amount', 'buyer_name', 'buyer_email', 'personal_number_enc', 'gift_card_name',
        'gift_card_design', 'channel', 'forwarded_at', 'forward_attempts', 'forward_error',
        'customer_mailed_at', 'payment_provider', 'payment_status', 'paid_at', 'stripe_session_id',
        'stripe_payment_intent', 'checkout_attempts',
    ];

    public const PAYMENT_STATUSES = ['pending' => 'Väntar på betalning', 'paid' => 'Betald', 'failed' => 'Misslyckad', 'expired' => 'Utgången'];

    /** Ordrar som borde ha lämnats till swing-order men inte har det. Obetalda Stripe-ordrar räknas inte. */
    public function needsForward(): static
    {
        return $this->groupStart()->where('forward_error IS NOT NULL', null, false)->orWhere('forwarded_at', null)->groupEnd()
            ->groupStart()->where('payment_provider', 'swish')->orWhere('payment_status', 'paid')->groupEnd();
    }

    /** Stripe-order som inte är betald — får aldrig lämnas över. */
    public static function awaitsPayment(array $order): bool
    {
        return ($order['payment_provider'] ?? 'swish') === 'stripe' && ($order['payment_status'] ?? '') !== 'paid';
    }

    /**
     * Markerar ordern som betald — atomärt, så att bara ett anrop "vinner" även
     * om webhook och tacksida körs samtidigt. Returnerar true för vinnaren.
     */
    public function claimPaid(int $id, ?string $paymentIntent): bool
    {
        $this->builder()
            ->where('id', $id)
            ->where('payment_status !=', 'paid')
            ->update([
                'payment_status'        => 'paid',
                'paid_at'               => date('Y-m-d H:i:s'),
                'stripe_payment_intent' => $paymentIntent,
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);

        return $this->db->affectedRows() === 1;
    }

    /**
     * Skapar en order med unik referens (W-ÅÅMMDD-XXXX) och hemlig token för tacksidan.
     *
     * @return array<string, mixed> den sparade raden
     */
    public function createOrder(array $data): array
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $data['reference']    = 'W-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            $data['public_token'] = bin2hex(random_bytes(16));

            if ($this->where('reference', $data['reference'])->countAllResults() === 0) {
                $id = $this->insert($data, true);

                return $this->find($id);
            }
        }

        throw new \RuntimeException('Kunde inte skapa en unik orderreferens.');
    }

    public function findByToken(string $token): ?array
    {
        if (! preg_match('/^[a-f0-9]{32}$/', $token)) {
            return null;
        }

        return $this->where('public_token', $token)->first();
    }

    public function purgeExpired(int $days): int
    {
        $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))->delete();

        return $this->db->affectedRows();
    }
}
