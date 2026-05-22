<?php

namespace App\Models;

class ItemModel extends AppModel
{
    protected $table            = 'items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'warehouse_id', 'code', 'name', 'unit',
        'initial_stock', 'current_stock', 'min_stock', 'expired_date', 'is_active',
        'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function isActive($value): bool
    {
        return (int) ($value ?? 1) === 1;
    }

    /**
     * Get items joined with warehouse name.
     */
    public function getWithWarehouse($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('items.*, warehouses.name as warehouse_name')
            ->join('warehouses', 'warehouses.id = items.warehouse_id', 'left');

        if ($id !== null) {
            return $builder->where('items.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get low stock items based on min_stock threshold.
     */
    public function getLowStock()
    {
        return $this->db->table($this->table)
            ->select('items.*, warehouses.name as warehouse_name')
            ->join('warehouses', 'warehouses.id = items.warehouse_id', 'left')
            ->where('items.is_active', 1)
            ->where('current_stock <= min_stock')
            ->get()->getResultArray();
    }

    /**
     * Get items close to expired or already expired.
     */
    public function getExpiredItems()
    {
        $currentDate = date('Y-m-d');
        $warningDate = date('Y-m-d', strtotime('+30 days'));

        return $this->db->table($this->table)
            ->select('items.*, warehouses.name as warehouse_name')
            ->join('warehouses', 'warehouses.id = items.warehouse_id', 'left')
            ->where('items.is_active', 1)
            ->where('expired_date IS NOT NULL')
            ->where('expired_date <=', $warningDate)
            ->orderBy('expired_date', 'ASC')
            ->get()->getResultArray();
    }
}
