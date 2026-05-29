<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateItemBatches extends Migration
{
    public function up()
    {
        // 1. Create item_batches table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'expired_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        // We do not strictly enforce foreign key constraints if SQLite/MySQL has issues matching types,
        // we can skip it for now and handle integrity in code.
        // $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('item_batches', true);

        // 2. Add batch_id to item_transactions
        $fields = [
            'batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'item_id',
            ],
        ];
        $this->forge->addColumn('item_transactions', $fields);

        // 3. Data Seeding: Move existing stock into batches
        $db = \Config\Database::connect();
        $items = $db->table('items')->where('current_stock >', 0)->get()->getResultArray();
        
        $batchData = [];
        foreach ($items as $item) {
            $batchData[] = [
                'item_id'      => $item['id'],
                'expired_date' => $item['expired_date'],
                'stock'        => $item['current_stock'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($batchData)) {
            $db->table('item_batches')->insertBatch($batchData);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('item_transactions', 'batch_id');
        $this->forge->dropTable('item_batches', true);
    }
}
