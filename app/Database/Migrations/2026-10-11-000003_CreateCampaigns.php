<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Kampanjer (TECH-05, AI-spec 4.4): titel, villkor, giltighetsperiod och
 * publiceringsstatus. Med pay_amount + value_amount blir kampanjen en
 * tidsbegränsad spelpott som kan beställas på /spelpotter/.
 */
class CreateCampaigns extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 160],
            'summary'      => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'terms'        => ['type' => 'TEXT', 'null' => true],
            'pay_amount'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'value_amount' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'valid_from'   => ['type' => 'DATE', 'null' => true],
            'valid_until'  => ['type' => 'DATE', 'null' => true],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'draft'],
            'show_on_home' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sort_order'   => ['type' => 'INT', 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['status', 'valid_from', 'valid_until']);
        $this->forge->createTable('campaigns');
    }

    public function down(): void
    {
        $this->forge->dropTable('campaigns', true);
    }
}
