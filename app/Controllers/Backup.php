<?php

namespace App\Controllers;

use App\Models\BackupModel;

class Backup extends BaseController
{
    protected $backupModel;
    protected $backupDir;

    public function __construct()
    {
        $this->backupModel = new BackupModel();
        $this->backupDir = WRITEPATH . 'backups/';
        
        // Ensure backups directory exists
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    public function index()
    {
        try {
            $data['backups'] = $this->backupModel->orderBy('created_at', 'DESC')->findAll();
        } catch (\Throwable $e) {
            $data['backups'] = $this->listBackupsFromDisk();
        }

        if (empty($data['backups'])) {
            $data['backups'] = $this->listBackupsFromDisk();
        }

        $data['title'] = 'Database Backup - AppsBeem';
        $data['page_title'] = lang('App.backup');

        return view('backup', $data);
    }

    /**
     * Generate pure PHP database backup
     */
    public function create()
    {
        $db = \Config\Database::connect();
        
        try {
            $tables = $db->listTables();
            $sql = "-- AppsBeem Logistic Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- --------------------------------------------------------\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // Skip backup table itself to avoid recursion or just back it up too (better to back it up!)
                
                // Get CREATE TABLE
                $query = $db->query("SHOW CREATE TABLE `{$table}`");
                $row = $query->getRowArray();
                $sql .= "\n\n-- Structure for table `{$table}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $row['Create Table'] . ";\n\n";

                // Get DATA
                $query = $db->query("SELECT * FROM `{$table}`");
                $rows = $query->getResultArray();
                
                if (!empty($rows)) {
                    $sql .= "-- Dumping data for table `{$table}`\n";
                    foreach ($rows as $item) {
                        $keys = array_map(function($key) {
                            return "`{$key}`";
                        }, array_keys($item));
                        
                        $values = array_map(function($val) use ($db) {
                            if ($val === null) {
                                return 'NULL';
                            }
                            return $db->escape($val);
                        }, array_values($item));

                        $sql .= "INSERT INTO `{$table}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                }
                $sql .= "\n-- --------------------------------------------------------\n";
            }
            
            $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

            // Save to file
            $filename = 'backup_' . date('Ymd_His') . '_' . uniqid() . '.sql';
            $filepath = $this->backupDir . $filename;
            
            file_put_contents($filepath, $sql);
            
            // Format size
            $filesize = filesize($filepath);
            $formattedSize = $this->formatBytes($filesize);

            // Save to database
            try {
                $this->backupModel->insert([
                    'file_name' => $filename,
                    'file_size' => $formattedSize
                ]);
            } catch (\Exception $e) {
                // Pass if backup table isn't fully migrated yet
            }

            return redirect()->to(base_url('backup'))->with('success', 'Backup database berhasil dibuat!');

        } catch (\Exception $e) {
            return redirect()->to(base_url('backup'))->with('error', 'Gagal membackup database: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file
     */
    public function download($filename)
    {
        $filepath = $this->backupDir . basename($filename);
        if (file_exists($filepath)) {
            return $this->response->download($filepath, null);
        } else {
            return redirect()->to(base_url('backup'))->with('error', 'File backup tidak ditemukan!');
        }
    }

    /**
     * Delete backup file
     */
    public function delete($filename)
    {
        $filename = basename($filename);
        $filepath = $this->backupDir . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete from database
        try {
            $this->backupModel->where('file_name', $filename)->delete();
        } catch (\Exception $e) {
            // Ignore DB error
        }

        return redirect()->to(base_url('backup'))->with('success', 'File backup berhasil dihapus.');
    }

    /**
     * Format bytes helper
     */
    private function formatBytes($bytes, $precision = 2) 
    { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }

    /**
     * List .sql backup files from disk when DB table is empty or unavailable.
     */
    private function listBackupsFromDisk(): array
    {
        $backups = [];
        $files = glob($this->backupDir . 'backup_*.sql') ?: [];

        foreach ($files as $path) {
            $name = basename($path);
            $backups[] = [
                'id'         => $name,
                'file_name'  => $name,
                'file_size'  => $this->formatBytes(filesize($path)),
                'created_at' => date('Y-m-d H:i:s', filemtime($path)),
            ];
        }

        usort($backups, static fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }
}
