<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tävlingar (CONTENT-07, AI-spec 4.4). Datum är valfria — inga datum får
 * hittas på. Endast status 'published' visas publikt.
 */
class CreateCompetitions extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 160],
            'title'             => ['type' => 'VARCHAR', 'constraint' => 160],
            'visibility'        => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'public'],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'draft'],
            'format'            => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'summary'           => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'description'       => ['type' => 'TEXT', 'null' => true],
            'starts_at'         => ['type' => 'DATETIME', 'null' => true],
            'ends_at'           => ['type' => 'DATETIME', 'null' => true],
            'registration_info' => ['type' => 'TEXT', 'null' => true],
            'registration_url'  => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'price_info'        => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'terms'             => ['type' => 'TEXT', 'null' => true],
            'terms_url'         => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'results_url'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['status', 'starts_at']);
        $this->forge->createTable('competitions');
    }

    public function down(): void
    {
        $this->forge->dropTable('competitions', true);
    }
}
