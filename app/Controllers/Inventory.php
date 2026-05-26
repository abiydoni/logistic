<?php

namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\ItemTransactionModel;
use App\Models\WarehouseModel;

class Inventory extends BaseController
{
    /** @var \App\Models\WarehouseModel */
    protected $warehouseModel;
    /** @var \App\Models\ItemModel */
    protected $itemModel;
    /** @var \App\Models\ItemTransactionModel */
    protected $transactionModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
        $this->itemModel = new ItemModel();
        $this->transactionModel = new ItemTransactionModel();
    }

    /**
     * Warehouse CRUD & Listing
     */
    public function warehouses()
    {
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            $id = $this->request->getPost('id');
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'requires_expiration' => $this->request->getPost('requires_expiration') ? 1 : 0
            ];

            if ($id) {
                $this->warehouseModel->update($id, $data);
            } else {
                $this->warehouseModel->insert($data);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => lang('App.update_stock_success')]);
        }

        $data['warehouses'] = $this->warehouseModel->findAll();
        $data['title'] = 'Manage Warehouses - AppsBeem';
        $data['page_title'] = lang('App.warehouse');

        return view('inventory/warehouses', $data);
    }

    /**
     * Items CRUD & Listing
     */
    public function items()
    {
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            $id = $this->request->getPost('id');
            
            // Delete operation
            if ($this->request->getPost('_method') === 'DELETE') {
                $item = $this->itemModel->find($id);
                if ($item && !empty($item['photo'])) {
                    $photoPath = FCPATH . 'uploads/items/' . $item['photo'];
                    if (is_file($photoPath)) {
                        unlink($photoPath);
                    }
                }
                $this->itemModel->deleteWithAudit($id);
                return $this->response->setJSON(['status' => 'success', 'message' => lang('App.update_stock_success')]);
            }

            $warehouseId = $this->request->getPost('warehouse_id');
            $code = $this->request->getPost('code');
            $name = $this->request->getPost('name');
            $unit = $this->request->getPost('unit');
            $initialStock = (int)$this->request->getPost('initial_stock');
            $minStock = (int)$this->request->getPost('min_stock');
            $expiredDate = $this->request->getPost('expired_date');
            if (empty($expiredDate)) {
                $expiredDate = null;
            }

            // Force null expiration date if warehouse does not require expiration
            $warehouse = $this->warehouseModel->find($warehouseId);
            if ($warehouse && isset($warehouse['requires_expiration']) && $warehouse['requires_expiration'] == 0) {
                $expiredDate = null;
            }

            $isActive = (int) $this->request->getPost('is_active') === 1 ? 1 : 0;

            $data = [
                'warehouse_id' => $warehouseId,
                'code'         => $code,
                'name'         => $name,
                'unit'         => $unit,
                'min_stock'    => $minStock,
                'expired_date' => $expiredDate,
                'is_active'    => $isActive,
            ];

            // Handle image upload
            $photoFile = $this->request->getFile('photo');
            if ($photoFile && $photoFile->isValid() && ! $photoFile->hasMoved()) {
                $validationRule = [
                    'photo' => [
                        'label' => 'Photo',
                        'rules' => 'uploaded[photo]|is_image[photo]|max_size[photo,2048]',
                    ],
                ];

                if (! $this->validate($validationRule)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Format file tidak valid atau ukuran file melebihi 2MB! Pastikan mengunggah file gambar (JPG/PNG/GIF).',
                    ]);
                }

                // If editing and has an old photo, delete it first
                if ($id) {
                    $oldItem = $this->itemModel->find($id);
                    if ($oldItem && !empty($oldItem['photo'])) {
                        $oldPhotoPath = FCPATH . 'uploads/items/' . $oldItem['photo'];
                        if (is_file($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }
                }

                // Create folder if not exists
                $uploadPath = FCPATH . 'uploads/items';
                if (! is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newPhotoName = $photoFile->getRandomName();
                $photoFile->move($uploadPath, $newPhotoName);
                $data['photo'] = $newPhotoName;
            }

            if ($id) {
                // Update
                $this->itemModel->update($id, $data);
            } else {
                // Insert new
                $data['initial_stock'] = $initialStock;
                $data['current_stock'] = $initialStock;
                $insertedId = $this->itemModel->insert($data);

                // Create initial transaction
                if ($insertedId && $initialStock > 0) {
                    $this->transactionModel->insert([
                        'item_id' => $insertedId,
                        'type' => 'in',
                        'quantity' => $initialStock,
                        'notes' => 'Stok Awal Pendaftaran Barang'
                    ]);
                }
            }

            return $this->response->setJSON(['status' => 'success', 'message' => lang('App.update_stock_success')]);
        }

        // Get filter inputs
        $warehouseFilter = $this->request->getGet('warehouse_id');
        $search          = $this->request->getGet('search');
        $lowStockFilter  = $this->request->getGet('low_stock'); // '1' if checked
        $expiredFilter   = $this->request->getGet('expired');   // '1' if checked
        $perPage         = 10;
        $currentPage     = (int)($this->request->getGet('page') ?? 1);
        $offset          = ($currentPage - 1) * $perPage;

        $query = $this->itemModel
            ->select('items.*, warehouses.name as warehouse_name')
            ->join('warehouses', 'warehouses.id = items.warehouse_id', 'left');

        if (!empty($warehouseFilter)) {
            $query->where('items.warehouse_id', $warehouseFilter);
        }
        if (!empty($search)) {
            $query->groupStart()
                  ->like('items.name', $search)
                  ->orLike('items.code', $search)
                  ->groupEnd();
        }
        // Low stock filter
        if (!empty($lowStockFilter)) {
            $query->where('items.current_stock <= items.min_stock');
        }
        // Expired filter
        if (!empty($expiredFilter)) {
            $query->where('items.expired_date IS NOT NULL')
                  ->where('items.expired_date < CURDATE()');
        }

        $totalItems = (clone $query)->countAllResults(false);
        $totalPages = (int)ceil($totalItems / $perPage);
        if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;

        $data['items']              = $query->limit($perPage, $offset)->findAll();
        $data['warehouses']         = $this->warehouseModel->findAll();
        $data['selected_warehouse'] = $warehouseFilter;
        $data['search_query']       = $search;
        $data['current_page']       = $currentPage;
        $data['total_pages']        = max(1, $totalPages);
        $data['total_items']        = $totalItems;
        $data['per_page']           = $perPage;

        $data['low_stock'] = $lowStockFilter;
        $data['expired'] = $expiredFilter;

        $data['title']      = 'Manage Items - AppsBeem';
        $data['page_title'] = lang('App.items');

        return view('inventory/items', $data);
    }

    /**
     * Update Stock Mutate (In / Out)
     */
    public function mutate()
    {
        $itemId = $this->request->getPost('item_id');
        $type = $this->request->getPost('type'); // 'in' or 'out'
        $quantity = (int)$this->request->getPost('quantity');
        $notes = $this->request->getPost('notes');

        $item = $this->itemModel->find($itemId);

        if (! $item) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.item_not_found')]);
        }

        if (! ItemModel::isActive($item['is_active'] ?? 1)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.item_inactive')]);
        }

        // Calculate new stock
        $newStock = $item['current_stock'];
        if ($type === 'in') {
            $newStock += $quantity;
        } else {
            $newStock -= $quantity;
            if ($newStock < 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Stok tidak mencukupi untuk pemakaian!']);
            }
        }

        // Save transaction
        $this->transactionModel->insert([
            'item_id' => $itemId,
            'type' => $type,
            'quantity' => $quantity,
            'notes' => $notes
        ]);

        // Update item stock
        $this->itemModel->update($itemId, [
            'current_stock' => $newStock
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => lang('App.update_stock_success'),
            'new_stock' => $newStock
        ]);
    }

    /**
     * Get item details and complete transaction history for stock/bincard visualization.
     *
     * @param int|string $itemId
     */
    public function bincard($itemId)
    {
        $item = $this->itemModel->select('items.*, warehouses.name as warehouse_name')
            ->join('warehouses', 'warehouses.id = items.warehouse_id', 'left')
            ->find($itemId);

        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.item_not_found')]);
        }

        // Fetch all transactions chronologically to calculate running balance correctly
        $transactions = $this->transactionModel->db->table('item_transactions')
            ->select('item_transactions.*, users.full_name as user_name')
            ->join('users', 'users.id = item_transactions.created_by', 'left')
            ->where('item_id', $itemId)
            ->orderBy('item_transactions.created_at', 'ASC')
            ->get()->getResultArray();

        $runningStock = 0;
        $history = [];
        foreach ($transactions as $tx) {
            $openBalance = $runningStock;

            if ($tx['type'] === 'in') {
                $runningStock += $tx['quantity'];
                $qtyIn = $tx['quantity'];
                $qtyOut = '—';
            } else {
                $runningStock -= $tx['quantity'];
                $qtyIn = '—';
                $qtyOut = $tx['quantity'];
            }

            $history[] = [
                'date'        => date('d/m/y H:i', strtotime($tx['created_at'])),
                'type'        => $tx['type'],
                'open'        => $openBalance,
                'qty_in'      => $qtyIn,
                'qty_out'     => $qtyOut,
                'balance'     => $runningStock,
                'notes'       => $tx['notes'] ?: '—',
                'operator'    => $tx['user_name'] ?: 'System',
            ];
        }

        // Reverse to show latest first
        $history = array_reverse($history);

        return $this->response->setJSON([
            'status' => 'success',
            'item' => $item,
            'history' => $history
        ]);
    }

    /**
     * Toggle item active status (AJAX).
     */
    public function toggleItemStatus()
    {
        $id   = (int) $this->request->getPost('id');
        $item = $this->itemModel->find($id);

        if (! $item) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.item_not_found')]);
        }

        $newStatus = ItemModel::isActive($item['is_active'] ?? 1) ? 0 : 1;
        $this->itemModel->update($id, ['is_active' => $newStatus]);

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => lang('App.status_updated'),
            'is_active' => $newStatus,
        ]);
    }
}
