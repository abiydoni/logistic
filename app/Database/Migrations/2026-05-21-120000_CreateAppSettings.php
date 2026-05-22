<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppSettings extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('app_settings')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'AppsBeem Logistic',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('app_settings', true);

        $this->db->table('app_settings')->insert([
            'id'           => 1,
            'company_name' => 'AppsBeem Logistic',
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function down(): void
    {
        if ($this->db->tableExists('app_settings')) {
            $this->forge->dropTable('app_settings', true);
        }
    }
}
