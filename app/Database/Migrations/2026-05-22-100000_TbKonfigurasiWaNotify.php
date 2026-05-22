<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbKonfigurasiWaNotify extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('tb_konfigurasi')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'value' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('nama');
            $this->forge->createTable('tb_konfigurasi', true);
        }

        if (! $this->db->tableExists('wa_notification_log')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'notify_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'daily_expired',
                ],
                'notify_date' => [
                    'type' => 'DATE',
                ],
                'expired_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'warning_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'sent_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['notify_type', 'notify_date']);
            $this->forge->createTable('wa_notification_log', true);
        }

        $defaults = [
            ['wa_notify_enabled', 'true'],
            ['wa_notify_days', '30'],
            ['wa_group_id', '120363398680818900@g.us'],
            ['api_url_group', 'https://telebot.appsbee.my.id'],
            ['report_expired', 'ambil_data_expired.php'],
        ];

        $table = $this->db->table('tb_konfigurasi');
        $now   = date('Y-m-d H:i:s');

        foreach ($defaults as [$nama, $value]) {
            $exists = $table->where('nama', $nama)->countAllResults();
            if ($exists === 0) {
                $table->insert([
                    'nama'       => $nama,
                    'value'      => $value,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('wa_notification_log')) {
            $this->forge->dropTable('wa_notification_log', true);
        }
        if ($this->db->tableExists('tb_konfigurasi')) {
            $this->forge->dropTable('tb_konfigurasi', true);
        }
    }
}
