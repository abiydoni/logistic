<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function() {
    return redirect()->to(base_url('dashboard'));
});

$routes->match(['GET', 'POST'], 'auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

$routes->get('dashboard', 'Dashboard::index');
$routes->match(['GET', 'POST'], 'inventory/warehouses', 'Inventory::warehouses');
$routes->match(['GET', 'POST', 'DELETE'], 'inventory/items', 'Inventory::items');
$routes->get('inventory/bincard/(:num)', 'Inventory::bincard/$1');
$routes->get('inventory/batches/(:num)', 'Inventory::getBatches/$1');
$routes->post('inventory/updateBatch', 'Inventory::updateBatchDate');
$routes->post('inventory/mutate', 'Inventory::mutate');
$routes->post('inventory/items/toggle-status', 'Inventory::toggleItemStatus');
$routes->get('scan', 'Scan::index');
$routes->post('scan/process', 'Scan::process');
$routes->get('backup', 'Backup::index');
$routes->get('backup/create', 'Backup::create');
$routes->get('backup/download/(:any)', 'Backup::download/$1');
$routes->get('backup/delete/(:any)', 'Backup::delete/$1');
$routes->get('settings', 'Settings::index');
$routes->post('settings/update', 'Settings::update');
$routes->get('profile', 'Profile::index');
$routes->post('profile/update', 'Profile::update');
$routes->post('profile/theme', 'Profile::updateTheme');
$routes->post('profile/locale', 'Profile::updateLocale');

$routes->match(['GET', 'POST'], 'users', 'Users::index');
$routes->post('users/delete', 'Users::delete');
$routes->post('users/toggle-status', 'Users::toggleStatus');
