<?php

namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\ItemTransactionModel;
use App\Models\WarehouseModel;
use App\Models\ItemBatchModel;

class Inventory extends BaseController
{
    /** @var \App\Models\WarehouseModel */
    protected $warehouseModel;
    /** @var \App\Models\ItemModel */
    protected $itemModel;
    /** @var \App\Models\ItemTransactionModel */
    protected $transactionModel;
    /** @var \App\Models\ItemBatchModel */
    protected $batchModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
        $this->itemModel = new ItemModel();
        $this->transactionModel = new ItemTransactionModel();
        $this->batchModel = new ItemBatchModel();
    }

    /**
     * Warehouse CRUD & Listing
     */
    public function warehouses()
    {
        if ($this->request->is('post')) {
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
        if ($this->request->is('post')) {
            $id = $this->request->getPost('id');
            
            // Delete operation
            if ($this->request->getPost('_method') === 'DELETE') {
                if (session()->get('role') !== 'admin') {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Hanya Admin yang dapat menghapus barang!']);
                }

                $password = $this->request->getPost('password');
                $userModel = new \App\Models\UserModel();
                $user = $userModel->find(session()->get('user_id'));

                if (!$user || !password_verify($password, $user['password'])) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Password salah! Penghapusan dibatalkan.']);
                }

                $item = $this->itemModel->find($id);
                if ($item && !empty($item['photo'])) {
                    $photoPath = FCPATH . 'uploads/items/' . $item['photo'];
                    if (is_file($photoPath)) {
                        unlink($photoPath);
                    }
                }
                
                // Delete related records (transactions and batches)
                $this->transactionModel->where('item_id', $id)->delete();
                $this->batchModel->where('item_id', $id)->delete();

                $this->itemModel->delete($id);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Barang berhasil dihapus beserta seluruh riwayat transaksinya!']);
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
            if ($photoFile && $photoFile->getError() !== UPLOAD_ERR_NO_FILE) {
                if (! $photoFile->isValid()) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Gagal mengunggah foto: ' . $photoFile->getErrorString(),
                    ]);
                }

                $validationRule = [
                    'photo' => [
                        'label' => 'Photo',
                        'rules' => 'uploaded[photo]|is_image[photo]|max_size[photo,2048]',
                    ],
                ];

                if (! $this->validate($validationRule)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Format file tidak valid atau ukuran melebihi 2MB! Pastikan gambar berformat JPG/PNG/GIF.',
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

                // Move new photo with exception handling
                try {
                    $uploadPath = FCPATH . 'uploads/items';
                    if (! is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $newPhotoName = $photoFile->getRandomName();
                    $photoFile->move($uploadPath, $newPhotoName);
                    $data['photo'] = $newPhotoName;
                } catch (\Exception $e) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Gagal menyimpan gambar di server (Masalah Izin Folder): ' . $e->getMessage()
                    ]);
                }
            }

            if ($id) {
                // Update
                $this->itemModel->update($id, $data);
            } else {
                // Insert new
                $data['initial_stock'] = $initialStock;
                $data['current_stock'] = $initialStock;
                $insertedId = $this->itemModel->insert($data);

                // Create initial transaction and batch
                if ($insertedId && $initialStock > 0) {
                    $batchId = $this->batchModel->insert([
                        'item_id' => $insertedId,
                        'expired_date' => $expiredDate,
                        'stock' => $initialStock
                    ]);

                    $this->transactionModel->insert([
                        'item_id' => $insertedId,
                        'batch_id' => $batchId,
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
            ->select('items.*, warehouses.name as warehouse_name, warehouses.requires_expiration')
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

        $data['items']              = $query->orderBy('items.name', 'ASC')->limit($perPage, $offset)->findAll();
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
        log_message('error', 'MUTATE POST DATA: ' . print_r($_POST, true));
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

        // Calculate new stock and handle batches
        $newStock = $item['current_stock'];
        if ($type === 'in') {
            $newStock += $quantity;
            $expiredDate = $this->request->getPost('expired_date') ?: null;

            // Find existing batch with same expired_date
            $batch = $this->batchModel->where('item_id', $itemId)
                                      ->where('expired_date', $expiredDate)
                                      ->first();
            
            if ($batch) {
                $this->batchModel->update($batch['id'], ['stock' => $batch['stock'] + $quantity]);
                $batchId = $batch['id'];
            } else {
                $batchId = $this->batchModel->insert([
                    'item_id' => $itemId,
                    'expired_date' => $expiredDate,
                    'stock' => $quantity
                ]);
            }

            $this->transactionModel->insert([
                'item_id' => $itemId,
                'batch_id' => $batchId,
                'type' => 'in',
                'quantity' => $quantity,
                'notes' => $notes
            ]);
        } else {
            if ($newStock - $quantity < 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Stok tidak mencukupi untuk pemakaian!']);
            }
            $newStock -= $quantity;
            
            // FEFO Out Logic
            $batches = $this->batchModel->getBatchesForFefo($itemId);
            $qtyRemaining = $quantity;

            foreach ($batches as $batch) {
                if ($qtyRemaining <= 0) break;

                $take = min($qtyRemaining, $batch['stock']);
                
                $this->batchModel->update($batch['id'], ['stock' => $batch['stock'] - $take]);
                
                $this->transactionModel->insert([
                    'item_id' => $itemId,
                    'batch_id' => $batch['id'],
                    'type' => 'out',
                    'quantity' => $take,
                    'notes' => $notes
                ]);

                $qtyRemaining -= $take;
            }
        }

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
     * Get active batches for an item (JSON endpoint)
     */
    public function getBatches($itemId)
    {
        $batches = $this->batchModel->getBatchesForFefo($itemId);
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $batches
        ]);
    }

    /**
     * Update expired date for a specific batch (JSON endpoint)
     */
    public function updateBatchDate()
    {
        if (!$this->request->is('post')) return;

        $batchId = $this->request->getPost('batch_id');
        $expiredDate = $this->request->getPost('expired_date');
        $qty = (int) $this->request->getPost('qty');
        
        if (empty($expiredDate)) {
            $expiredDate = null;
        }

        $batch = $this->batchModel->find($batchId);
        if (!$batch) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Batch tidak ditemukan!']);
        }

        if ($qty <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jumlah stok tidak valid!']);
        }

        // Limit qty to the maximum stock in the batch
        if ($qty > $batch['stock']) {
            $qty = $batch['stock'];
        }

        // Check if there is already another batch with the EXACT same item and new expired_date
        $existingBatch = $this->batchModel->where('item_id', $batch['item_id'])
                                          ->where('expired_date', $expiredDate)
                                          ->where('id !=', $batchId) // exclude self
                                          ->first();

        // 1. Modifying the FULL stock of this batch
        if ($qty == $batch['stock']) {
            if ($existingBatch) {
                // Merge into existing batch and delete this one
                $this->batchModel->update($existingBatch['id'], ['stock' => $existingBatch['stock'] + $batch['stock']]);
                $this->batchModel->delete($batchId);
            } else {
                // Just update this batch's date
                $this->batchModel->update($batchId, ['expired_date' => $expiredDate]);
            }
        } 
        // 2. Modifying PARTIAL stock (Splitting the batch)
        else {
            // Deduct qty from current batch
            $this->batchModel->update($batchId, ['stock' => $batch['stock'] - $qty]);

            if ($existingBatch) {
                // Add qty to existing batch
                $this->batchModel->update($existingBatch['id'], ['stock' => $existingBatch['stock'] + $qty]);
            } else {
                // Create a new batch
                $this->batchModel->insert([
                    'item_id' => $batch['item_id'],
                    'expired_date' => $expiredDate,
                    'stock' => $qty
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tanggal kedaluwarsa berhasil diperbarui!'
        ]);
    }

    /**
     * Item History (Bincard)
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
