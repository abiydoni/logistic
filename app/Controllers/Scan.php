<?php

namespace App\Controllers;

use App\Models\ItemModel;

class Scan extends BaseController
{
    public function index()
    {
        $data['title'] = 'Scan QR/Barcode - AppsBeem';
        $data['page_title'] = lang('App.scan');

        return view('scan', $data);
    }

    /**
     * Process scanned QR / Barcode code
     */
    public function process()
    {
        $code = $this->request->getPost('code');
        $itemModel = new ItemModel();

        // Query item joined with warehouse
        $item = $itemModel->select('items.*, warehouses.name as warehouse_name')
            ->join('warehouses', 'warehouses.id = items.warehouse_id', 'left')
            ->where('items.code', $code)
            ->first();

        if ($item) {
            if (! ItemModel::isActive($item['is_active'] ?? 1)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => lang('App.item_inactive'),
                ]);
            }

            $item['is_low_stock'] = (int) $item['current_stock'] <= (int) $item['min_stock'];
            $item['is_expired']   = ! empty($item['expired_date'])
                && strtotime($item['expired_date']) < strtotime('today');
            $item['is_near_expiry'] = ! empty($item['expired_date'])
                && ! $item['is_expired']
                && strtotime($item['expired_date']) <= strtotime('+30 days');

            return $this->response->setJSON([
                'status' => 'success',
                'item'   => $item,
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('App.item_not_found') . " (Kode: {$code})"
            ]);
        }
    }
}
