<?php

namespace App\Controllers;

use App\Models\WarehouseModel;
use App\Models\ItemModel;
use App\Models\ItemTransactionModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $warehouseModel = new WarehouseModel();
        $itemModel = new ItemModel();
        $transactionModel = new ItemTransactionModel();

        // Seed demo data if database is empty
        try {
            if ($warehouseModel->countAllResults() === 0) {
                // Insert default warehouses
                $fbId = $warehouseModel->insert([
                    'name' => 'Food & Beverage',
                    'description' => 'Gudang penyimpanan bahan makanan segar, kaleng, dan minuman.',
                ]);
                $genId = $warehouseModel->insert([
                    'name' => 'General Supply',
                    'description' => 'Gudang penyimpanan kebutuhan umum, perkakas, dan inventaris kantor.',
                ]);

                // Insert some sample items
                if ($fbId && $genId) {
                    $itemModel->insert([
                        'warehouse_id' => $fbId,
                        'code' => 'FB001',
                        'name' => 'Susu Kaleng Premium',
                        'unit' => 'kaleng',
                        'initial_stock' => 100,
                        'current_stock' => 85,
                        'min_stock' => 20,
                        'expired_date' => date('Y-m-d', strtotime('+45 days'))
                    ]);

                    $itemModel->insert([
                        'warehouse_id' => $fbId,
                        'code' => 'FB002',
                        'name' => 'Roti Tawar Gandum',
                        'unit' => 'pcs',
                        'initial_stock' => 50,
                        'current_stock' => 5,
                        'min_stock' => 10, // will show low stock!
                        'expired_date' => date('Y-m-d', strtotime('+3 days')) // will show expired warning!
                    ]);

                    $itemModel->insert([
                        'warehouse_id' => $genId,
                        'code' => 'GEN001',
                        'name' => 'Kertas A4 80gr',
                        'unit' => 'rim',
                        'initial_stock' => 200,
                        'current_stock' => 120,
                        'min_stock' => 30,
                        'expired_date' => null
                    ]);

                    // Add some dummy transactions to populate chart
                    $items = $itemModel->findAll();
                    foreach ($items as $item) {
                        $transactionModel->insert([
                            'item_id' => $item['id'],
                            'type' => 'in',
                            'quantity' => $item['initial_stock'],
                            'notes' => 'Stok awal sistem'
                        ]);
                        if ($item['initial_stock'] > $item['current_stock']) {
                            $transactionModel->insert([
                                'item_id' => $item['id'],
                                'type' => 'out',
                                'quantity' => $item['initial_stock'] - $item['current_stock'],
                                'notes' => 'Pemakaian operasional harian'
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error or pass quietly if tables aren't set up
        }

        // Metrik
        $data['total_warehouses'] = $warehouseModel->countAllResults();
        $data['total_items'] = $itemModel->where('is_active', 1)->countAllResults();
        
        $lowStockItems = $itemModel->getLowStock();
        $data['low_stock_count'] = count($lowStockItems);
        $data['low_stock_items'] = $lowStockItems;

        $expiredItems = $itemModel->getExpiredItems();
        $data['expired_count'] = count($expiredItems);
        $data['expired_items'] = $expiredItems;

        // Total stock sum
        $totalStock = $itemModel->where('is_active', 1)->selectSum('current_stock')->first();
        $data['total_stock'] = $totalStock['current_stock'] ?? 0;

        // Recent transactions
        $data['recent_transactions'] = $transactionModel->getRecent(5);

        // Chart Data — Real last 6 months from DB
        $db = \Config\Database::connect();
        $labels = [];
        $chartIn  = [];
        $chartOut = [];

        for ($m = 5; $m >= 0; $m--) {
            $monthStart = date('Y-m-01', strtotime("-{$m} months"));
            $monthEnd   = date('Y-m-t',  strtotime("-{$m} months"));
            $labels[]   = date('M', strtotime($monthStart));

            $qIn  = $db->table('item_transactions')
                        ->selectSum('quantity')
                        ->where('type', 'in')
                        ->where('created_at >=', $monthStart . ' 00:00:00')
                        ->where('created_at <=', $monthEnd   . ' 23:59:59')
                        ->get()->getRow();
            $qOut = $db->table('item_transactions')
                        ->selectSum('quantity')
                        ->where('type', 'out')
                        ->where('created_at >=', $monthStart . ' 00:00:00')
                        ->where('created_at <=', $monthEnd   . ' 23:59:59')
                        ->get()->getRow();

            $chartIn[]  = (int)($qIn->quantity  ?? 0);
            $chartOut[] = (int)($qOut->quantity ?? 0);
        }

        $data['chart_labels'] = $labels;
        $data['chart_in']     = $chartIn;
        $data['chart_out']    = $chartOut;

        // Page titles for layout
        $data['title'] = 'Dashboard - AppsBeem Logistic';
        $data['page_title'] = lang('App.dashboard');

        return view('dashboard', $data);
    }
}
