<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToItems extends Migration
{
    public function up(): void
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = $this->db;

        if ($db->tableExists('items') && ! $db->fieldExists('photo', 'items')) {
            $this->forge->addColumn('items', [
                'photo' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'is_active',
                ],
            ]);
        }
    }

    public function down(): void
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = $this->db;

        if ($db->fieldExists('photo', 'items')) {
            $this->forge->dropColumn('items', 'photo');
        }
    }
}
