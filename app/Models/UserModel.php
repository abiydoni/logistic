<?php

namespace App\Models;

class UserModel extends AppModel
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username', 'password', 'full_name', 'role', 'is_active',
        'language', 'theme', 'created_at', 'updated_at',
        'created_by', 'updated_by', 'deleted_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Normalize role from DB or form to admin|staff.
     */
    public static function normalizeRole(?string $role): string
    {
        $r = strtolower(trim((string) $role));

        if ($r === 'admin' || $r === 'administrator') {
            return 'admin';
        }

        return 'staff';
    }

    public static function isAdminRole(?string $role): bool
    {
        return self::normalizeRole($role) === 'admin';
    }

    public static function isActive($value): bool
    {
        return (int) ($value ?? 1) === 1;
    }

    /**
     * Verify user password.
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
