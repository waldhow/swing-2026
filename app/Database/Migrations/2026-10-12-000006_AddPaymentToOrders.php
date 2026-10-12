<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Betalstatus på ordrar (Stripe Checkout). Befintliga ordrar är manuell Swish.
 *   payment_status: pending (väntar), paid (betald), failed (misslyckad), expired (utgången)
 */
class AddPaymentToOrders extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', [
            'payment_provider'      => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'swish', 'after' => 'channel'],
            'payment_status'        => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'pending', 'after' => 'payment_provider'],
            'paid_at'               => ['type' => 'DATETIME', 'null' => true, 'after' => 'payment_status'],
            'stripe_session_id'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'paid_at'],
            'stripe_payment_intent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'stripe_session_id'],
            'checkout_attempts'     => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0, 'after' => 'stripe_payment_intent'],
        ]);
        $this->db->query('CREATE INDEX ' . $this->db->escapeIdentifiers($this->db->DBPrefix . 'orders_stripe_session') . ' ON ' . $this->db->escapeIdentifiers($this->db->DBPrefix . 'orders') . ' (stripe_session_id)');
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', ['payment_provider', 'payment_status', 'paid_at', 'stripe_session_id', 'stripe_payment_intent', 'checkout_attempts']);
    }
}
