<?php

namespace App\Models;

class ItemTransactionModel extends AppModel
{
    protected $table            = 'item_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'item_id', 'type', 'quantity', 'notes', 
        'created_at', 'updated_at', 'created_by', 'updated_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get recent transactions with item and user details.
     */
    public function getRecent($limit = 10)
    {
        return $this->db->table($this->table)
            ->select('item_transactions.*, items.name as item_name, items.unit, users.full_name as user_name')
            ->join('items', 'items.id = item_transactions.item_id', 'left')
            ->join('users', 'users.id = item_transactions.created_by', 'left')
            ->orderBy('item_transactions.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
