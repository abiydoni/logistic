<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixUsersAndBackups extends Migration
{
    public function up(): void
    {
        $db = $this->db;

        if (! $db->fieldExists('language', 'users')) {
            $this->forge->addColumn('users', [
                'language' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 5,
                    'default'    => 'id',
                    'after'      => 'role',
                ],
                'theme' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'default'    => 'light',
                    'after'      => 'language',
                ],
            ]);
        }

        $db->query("UPDATE users SET role = 'admin' WHERE LOWER(TRIM(role)) IN ('admin', 'administrator') OR role = 'Admin'");
        $db->query("UPDATE users SET role = 'staff' WHERE role = '' OR role IS NULL OR LOWER(TRIM(role)) IN ('staff', 'user') OR role = 'Staff' OR LOWER(TRIM(role)) NOT IN ('admin', 'administrator')");

        $db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin','staff') NOT NULL DEFAULT 'staff'");

        if (! $db->tableExists('backups')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'file_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'file_size' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('backups', true);
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('backups')) {
            $this->forge->dropTable('backups', true);
        }

        if ($this->db->fieldExists('language', 'users')) {
            $this->forge->dropColumn('users', 'language');
        }
        if ($this->db->fieldExists('theme', 'users')) {
            $this->forge->dropColumn('users', 'theme');
        }

        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('Admin','Staff') NOT NULL DEFAULT 'Staff'");
    }
}
