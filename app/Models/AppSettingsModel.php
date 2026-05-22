<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingsModel extends Model
{
    protected $table         = 'app_settings';
    protected $primaryKey      = 'id';
    protected $allowedFields   = ['company_name', 'updated_at'];
    protected $useTimestamps   = false;

    public const DEFAULT_COMPANY = 'AppsBeem Logistic';

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        try {
            if (! $this->db->tableExists($this->table)) {
                return ['id' => 1, 'company_name' => self::DEFAULT_COMPANY];
            }

            $row = $this->find(1);
            if ($row) {
                return $row;
            }

            $this->insert([
                'id'           => 1,
                'company_name' => self::DEFAULT_COMPANY,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            return $this->find(1) ?? ['id' => 1, 'company_name' => self::DEFAULT_COMPANY];
        } catch (\Throwable) {
            return ['id' => 1, 'company_name' => self::DEFAULT_COMPANY];
        }
    }

    public function getCompanyName(): string
    {
        $name = trim((string) ($this->getSettings()['company_name'] ?? ''));

        return $name !== '' ? $name : self::DEFAULT_COMPANY;
    }

    public function updateCompanyName(string $name): bool
    {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        $this->getSettings();

        return (bool) $this->update(1, [
            'company_name' => $name,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
