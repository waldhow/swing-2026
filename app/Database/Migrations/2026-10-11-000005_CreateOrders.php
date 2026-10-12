<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Lokala kopior av beställningar som skickats vidare till swing-order.
 * swing-order är huvudregistret; kopian finns för att inget ska gå förlorat
 * om vidarebefordran misslyckas, och rensas efter Config\Orders::$retentionDays.
 */
class CreateOrders extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'reference'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'public_token'        => ['type' => 'CHAR', 'constraint' => 32],
            'order_type'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'product_name'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'campaign_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'pay_amount'          => ['type' => 'INT', 'unsigned' => true],
            'value_amount'        => ['type' => 'INT', 'unsigned' => true],
            'buyer_name'          => ['type' => 'VARCHAR', 'constraint' => 120],
            'buyer_email'         => ['type' => 'VARCHAR', 'constraint' => 190],
            'personal_number_enc' => ['type' => 'TEXT', 'null' => true],
            'gift_card_name'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'gift_card_design'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'channel'             => ['type' => 'VARCHAR', 'constraint' => 10],
            'forwarded_at'        => ['type' => 'DATETIME', 'null' => true],
            'forward_attempts'    => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
            'forward_error'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'customer_mailed_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('reference');
        $this->forge->addUniqueKey('public_token');
        $this->forge->addKey('created_at');
        $this->forge->createTable('orders');
    }

    public function down(): void
    {
        $this->forge->dropTable('orders', true);
    }
}
