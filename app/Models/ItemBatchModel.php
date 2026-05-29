<?php

namespace App\Models;

class ItemBatchModel extends AppModel
{
    protected $table            = 'item_batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'item_id', 'expired_date', 'stock',
        'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get batches for an item ordered by FEFO (First Expired First Out)
     */
    public function getBatchesForFefo(int $itemId)
    {
        // Custom order to put NULL expired_date last
        return $this->where('item_id', $itemId)
            ->where('stock >', 0)
            ->orderBy('CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END', 'ASC')
            ->orderBy('expired_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
