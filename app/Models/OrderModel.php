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
        'customer_mailed_at',
    ];

    /**
     * Skapar en order med kort referens (W1001, W1002 …) och hemlig token för tacksidan.
     *
     * Referensen är det kunden skriver i Swish-meddelandet, så den ska vara kort
     * och lätt att skriva. W skiljer den från swing-orders egna ORD-nummer. Den
     * bygger på radens id, som aldrig återanvänds (inte heller efter gallring),
     * så den krockar inte med äldre ordrar i swing-order.
     *
     * @return array<string, mixed> den sparade raden
     */
    public function createOrder(array $data): array
    {
        $data['public_token'] = bin2hex(random_bytes(16));
        $data['reference']    = 'tmp-' . substr($data['public_token'], 0, 16);

        $this->db->transStart();
        $id = $this->insert($data, true);
        $this->builder()->where('id', $id)->update(['reference' => self::reference((int) $id)]);
        $this->db->transComplete();

        return $this->find($id);
    }

    public static function reference(int $id): string
    {
        return 'W' . (1000 + $id);
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
