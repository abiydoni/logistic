<?php

namespace App\Models;

use CodeIgniter\Model;

class KonfigurasiModel extends Model
{
    protected $table         = 'tb_konfigurasi';
    protected $primaryKey      = 'id';
    protected $allowedFields   = ['nama', 'value', 'updated_at'];
    protected $useTimestamps   = false;

    /** @var list<string> */
    public const WA_KEYS = [
        'wa_notify_enabled',
        'wa_notify_days',
        'wa_group_id',
        'api_url_group',
        'report_expired',
    ];

    /** @var array<string, string> */
    public const WA_DEFAULTS = [
        'wa_notify_enabled' => 'true',
        'wa_notify_days'    => '30',
        'wa_group_id'       => '',
        'api_url_group'     => 'https://telebot.appsbee.my.id',
        'report_expired'    => 'ambil_data_expired.php',
    ];

    public function tableReady(): bool
    {
        try {
            return $this->db->tableExists($this->table);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, string>
     */
    public function getWaSettings(): array
    {
        $out = self::WA_DEFAULTS;

        if (! $this->tableReady()) {
            return $out;
        }

        try {
            $rows = $this->db->table($this->table)
                ->whereIn('nama', self::WA_KEYS)
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $nama = (string) ($row['nama'] ?? '');
                if ($nama !== '' && array_key_exists($nama, $out)) {
                    $out[$nama] = (string) ($row['value'] ?? '');
                }
            }
        } catch (\Throwable) {
        }

        return $out;
    }

    /**
     * @param array<string, string> $values
     */
    public function saveWaSettings(array $values): bool
    {
        if (! $this->tableReady()) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        foreach (self::WA_KEYS as $key) {
            if (! array_key_exists($key, $values)) {
                continue;
            }

            $value = trim((string) $values[$key]);
            $exists = $this->where('nama', $key)->first();

            if ($exists) {
                $this->update($exists['id'], [
                    'value'      => $value,
                    'updated_at' => $now,
                ]);
            } else {
                $this->insert([
                    'nama'       => $key,
                    'value'      => $value,
                    'updated_at' => $now,
                ]);
            }
        }

        return true;
    }
}
