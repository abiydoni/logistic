<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActiveStatusToUsersAndItems extends Migration
{
    public function up(): void
    {
        $db = $this->db;

        if ($db->tableExists('users') && ! $db->fieldExists('is_active', 'users')) {
            $this->forge->addColumn('users', [
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'role',
                ],
            ]);
            $db->query('UPDATE users SET is_active = 1 WHERE is_active IS NULL');
        }

        if ($db->tableExists('items') && ! $db->fieldExists('is_active', 'items')) {
            $this->forge->addColumn('items', [
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'expired_date',
                ],
            ]);
            $db->query('UPDATE items SET is_active = 1 WHERE is_active IS NULL');
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('is_active', 'users')) {
            $this->forge->dropColumn('users', 'is_active');
        }
        if ($this->db->fieldExists('is_active', 'items')) {
            $this->forge->dropColumn('items', 'is_active');
        }
    }
}
