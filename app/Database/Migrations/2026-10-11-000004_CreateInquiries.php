<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Offert- och kontaktförfrågningar (TECH-04). Rensas automatiskt efter
 * Config\Inquiries::$retentionMonths.
 */
class CreateInquiries extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'type'             => ['type' => 'VARCHAR', 'constraint' => 20],
            'name'             => ['type' => 'VARCHAR', 'constraint' => 120],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 190],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'organisation'     => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'participants'     => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'preferred_date'   => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'package_interest' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'wants_food'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'message'          => ['type' => 'TEXT'],
            'status'           => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'new'],
            'mailed_at'        => ['type' => 'DATETIME', 'null' => true],
            'mail_error'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->createTable('inquiries');
    }

    public function down(): void
    {
        $this->forge->dropTable('inquiries', true);
    }
}
