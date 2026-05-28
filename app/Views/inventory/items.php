<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $items
 * @var array $warehouses
 * @var string|null $selected_warehouse
 * @var string|null $search_query
 * @var int $current_page
 * @var int $total_pages
 * @var int $total_items
 * @var int $per_page
 * @var string|null $low_stock
 * @var string|null $expired
 */
?>
<?php $this->extend('layout/main') ?>
<?php $this->section('content') ?>

<style>
.swal-rounded { border-radius: 12px !important; }

/* ── Responsive Card & Table Styles ── */
.items-container {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.table-header-grid {
  display: grid;
  grid-template-columns: 24px 20px 1fr 60px 48px 100px;
  gap: 4px;
  padding: 6px 8px;
  background: var(--surface-2);
  border-bottom: 1px solid var(--border);
  border-radius: 8px 8px 0 0;
}
.table-header-grid span {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--text-muted);
  letter-spacing: 0.04em;
}

.item-card-row {
  display: grid;
  grid-template-columns: 24px 20px 1fr 60px 48px 100px;
  gap: 4px;
  padding: 8px;
  border-bottom: 1px solid var(--border);
  align-items: center;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.1s ease;
}
.item-card-row:hover {
  background-color: var(--surface-2) !important;
}
.item-card-row:active {
  transform: scale(0.995);
}

.col-index {
  font-size: 10px;
  color: var(--text-faint);
  text-align: center;
  font-weight: 700;
}

.col-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
  padding-left: 2px;
}
.item-title-row {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-wrap: wrap;
}
.item-name-txt {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
  line-height: 1.25;
  letter-spacing: -0.02em;
}
.badges-row {
  display: flex;
  gap: 2px;
}

.item-sub-row {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-wrap: wrap;
}
.item-code-badge {
  font-size: 9px;
  color: var(--text-faint);
  background: var(--surface-2);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 0 4px;
  font-family: monospace;
  font-weight: 700;
}
.item-warehouse-txt {
  font-size: 9px;
  color: var(--text-faint);
}

.col-exp {
  text-align: center;
  font-size: 10px;
  font-weight: 600;
}
.col-exp .mobile-label {
  display: none;
}

.col-stock {
  text-align: center;
  display: flex;
  justify-content: center;
}
.stock-badge-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2px 4px;
  border-radius: 6px;
  background: var(--surface-2);
  min-width: 38px;
  border: 1px solid var(--border);
}
.stock-val-num {
  font-size: 11px;
  font-weight: 900;
  line-height: 1;
}
.stock-low {
  color: #ef4444;
}
.stock-normal {
  color: var(--primary);
}
.stock-unit-label {
  font-size: 8px;
  color: var(--text-faint);
  font-weight: 700;
  text-transform: uppercase;
}

.col-actions {
  display: flex;
  gap: 3px;
  justify-content: center;
}
.btn-action-mutate {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  border: none;
  background: rgba(16, 185, 129, 0.08);
  color: #10b981;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.1s ease;
}
.btn-action-mutate:hover {
  background: #10b981;
  color: #ffffff;
}
.btn-action-edit {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  border: none;
  background: var(--surface-2);
  color: var(--text-muted);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.1s ease;
  border: 1px solid var(--border);
}
.btn-action-edit:hover {
  background: var(--primary);
  color: #ffffff;
  border-color: var(--primary);
}
.btn-action-qr {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  border: none;
  background: rgba(139, 92, 246, 0.08);
  color: #8b5cf6;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.1s ease;
}
.btn-action-qr:hover {
  background: #8b5cf6;
  color: #ffffff;
}
.btn-action-delete {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  border: none;
  background: rgba(239, 68, 68, 0.08);
  color: #ef4444;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.1s ease;
}
.btn-action-delete:hover {
  background: #ef4444;
  color: #ffffff;
}
/* QR Tab styles */
.qr-tab {
  padding: 6px 4px;
  font-size: 11px;
  font-weight: 700;
  border-radius: 8px;
  border: 1.5px solid var(--border);
  background: var(--surface-2);
  color: var(--text-muted);
  cursor: pointer;
  transition: all 0.1s ease;
}
.qr-tab.active-tab {
  background: var(--primary);
  border-color: var(--primary);
  color: #ffffff;
}

/* ── Bincard Key-Value Layout Styles ── */
.bincard-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  margin-top: 10px;
  font-size: 11px;
  background: var(--surface-2);
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid var(--border);
}
.bincard-info-item {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.bincard-info-item .bc-lbl {
  color: var(--text-faint);
  font-size: 8px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.bincard-info-item .bc-val {
  font-weight: 700;
  color: var(--text);
  word-break: break-all;
}

.bincard-table {
  table-layout: fixed;
  width: 100%;
}
.bincard-table .bc-col-date {
  width: 68px;
  max-width: 68px;
  font-size: 8.5px;
  line-height: 1.25;
  white-space: normal;
  word-break: break-word;
  vertical-align: top;
}
.bincard-table .bc-col-num {
  width: 32px;
  text-align: center;
  white-space: nowrap;
}
.bincard-table .bc-col-saldo {
  width: 38px;
  text-align: center;
  font-weight: 800;
  color: var(--primary);
  white-space: nowrap;
}
.bincard-table .bc-col-notes {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 0;
}
.bincard-table .bc-col-op {
  width: 48px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-size: 8.5px;
  color: var(--text-muted);
}

/* ── Mobile Layout Fixes ── */
@media (max-width: 640px) {
  .table-header-grid {
    grid-template-columns: 24px 18px 1fr 50px 40px 96px;
    padding: 6px 4px;
  }
  .item-card-row {
    grid-template-columns: 24px 18px 1fr 50px 40px 96px;
    padding: 6px 4px;
    gap: 2px;
  }
  .col-index { font-size: 9px; }
  .item-name-txt { font-size: 11px; }
  .item-code-badge, .item-warehouse-txt { font-size: 8px; }
  .col-exp { font-size: 9px; }
  .stock-badge-container { min-width: 32px; padding: 1px 2px; }
  .stock-val-num { font-size: 10px; }
  .stock-unit-label { font-size: 7px; }
  .btn-action-mutate, .btn-action-edit, .btn-action-qr, .btn-action-delete {
    width: 22px;
    height: 22px;
  }
  .col-actions { gap: 2px; }
}

/* Print CSS */
@media print {
  header, 
  nav, 
  #page-loader,
  .no-print {
    display: none !important;
    visibility: hidden !important;
  }
  
  body, 
  .app-shell, 
  .app-main {
    background: #ffffff !important;
    color: #000000 !important;
    margin: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
    display: block !important;
  }
  
  #bincard-modal-container {
    display: block !important;
    position: relative !important;
    inset: auto !important;
    background: none !important;
    backdrop-filter: none !important;
    padding: 0 !important;
    margin: 0 !important;
    z-index: auto !important;
    visibility: visible !important;
  }
  
  #bincard-modal-content {
    max-width: 100% !important;
    max-height: none !important;
    overflow: visible !important;
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    background: #ffffff !important;
    color: #000000 !important;
    visibility: visible !important;
  }
  
  #bincard-modal-content * {
    visibility: visible !important;
    color: #000000 !important;
  }
}
</style>

<?php
/**
 * Helper: build query string preserving current filters
 *
 * @param int $page
 * @param string|null $search
 * @param string|null $warehouse
 * @return string
 */
function pageUrl($page, $search, $warehouse) {
    $q = http_build_query(array_filter([
        'page'         => $page > 1 ? $page : null,
        'search'       => $search,
        'warehouse_id' => $warehouse,
    ]));
    return base_url('inventory/items') . ($q ? '?' . $q : '');
}
?>

<div class="app-page items-page no-print-wrap">

<div class="page-toolbar no-print">
  <div>
    <span class="page-toolbar-title"><?= lang('App.items_list') ?></span>
    <p class="page-head-desc" style="margin-top:2px"><?= $total_items ?> <?= lang('App.items') ?></p>
  </div>
  <button type="button" onclick="toggleForm()" class="btn-primary">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    <?= lang('App.new_item') ?>
  </button>
  <!-- <button type="button" onclick="window.location.href='<?= base_url('scan') ?>'" class="btn-primary ml-2">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m6-2l-6 6-6-6"/></svg>
    <?= lang('App.scan_item') ?>
  </button> -->
</div>

<form class="filter-bar no-print" method="get" action="<?= base_url('inventory/items') ?>">
  <div class="filter-row">
    <div style="flex:0 0 130px;min-width:110px">
      <select name="warehouse_id" onchange="this.form.submit()">
        <option value=""><?= lang('App.all_warehouses') ?></option>
        <?php foreach ($warehouses as $wh): ?>
          <option value="<?= $wh['id'] ?>" <?= $selected_warehouse == $wh['id'] ? 'selected' : '' ?>><?= esc($wh['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <label class="filter-check">
      <input type="checkbox" name="low_stock" value="1" <?= $low_stock ? 'checked' : '' ?> onchange="this.form.submit()"> Low Stock
    </label>
    <label class="filter-check">
      <input type="checkbox" name="expired" value="1" <?= $expired ? 'checked' : '' ?> onchange="this.form.submit()"> Expired
    </label>
    <?php if ($search_query || $selected_warehouse || $low_stock || $expired): ?>
    <a href="<?= base_url('inventory/items') ?>" class="filter-reset" title="Reset">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </a>
    <?php endif; ?>
  </div>
  <div class="filter-search">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    <input type="text" name="search" value="<?= esc($search_query) ?>" placeholder="<?= lang('App.search_placeholder') ?>" onchange="this.form.submit()">
  </div>
</form>

<!-- ── Modal Add/Edit ── -->
<div id="item-form-container" class="no-print" style="display:none;position:fixed;inset:0;z-index:5000;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:16px">
  <div class="md-card" style="width:100%;max-width:380px;max-height:90vh;overflow-y:auto;padding:16px;animation:slideUp .3s cubic-bezier(.16,1,.3,1)">
    <!-- Modal Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;position:sticky;top:0;background:var(--surface);padding-bottom:6px;border-bottom:1px solid var(--border)">
      <h4 id="form-title" style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--primary)"><?= lang('App.add_new_item') ?></h4>
      <button type="button" onclick="toggleForm()" style="width:24px;height:24px;border-radius:6px;border:none;background:var(--surface-2);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form id="item-form" action="<?= base_url('inventory/items') ?>" method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:10px">
      <input type="hidden" name="id" id="item-id">

      <!-- Gudang + Kode -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        <div>
          <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px"><?= lang('App.warehouse') ?></label>
          <select name="warehouse_id" id="warehouse_id" required style="font-size:11px;padding:8px;border-radius:8px">
            <?php foreach ($warehouses as $wh): ?>
              <option value="<?= $wh['id'] ?>" data-requires-expiration="<?= $wh['requires_expiration'] ?? 1 ?>"><?= esc($wh['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px"><?= lang('App.code') ?></label>
          <input type="text" name="code" id="code" required placeholder="<?= lang('App.example_code') ?>" style="font-size:11px;padding:8px;border-radius:8px">
        </div>
      </div>

      <!-- Nama -->
      <div>
        <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px"><?= lang('App.item_name') ?></label>
        <input type="text" name="name" id="name" required placeholder="<?= lang('App.example_name') ?>" style="font-size:11px;padding:8px;border-radius:8px">
      </div>

      <!-- Satuan + Stok Awal + Min Stok -->
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px">
        <div>
          <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px"><?= lang('App.unit') ?></label>
          <input type="text" name="unit" id="unit" required placeholder="pcs" style="font-size:11px;padding:8px;border-radius:8px">
        </div>
        <div id="initial-stock-group">
          <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px"><?= lang('App.initial_stock') ?></label>
          <input type="number" name="initial_stock" id="initial_stock" value="0" min="0" style="font-size:11px;padding:8px;border-radius:8px">
        </div>
        <div>
          <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px"><?= lang('App.min_stock') ?></label>
          <input type="number" name="min_stock" id="min_stock" value="10" min="0" style="font-size:11px;padding:8px;border-radius:8px">
        </div>
      </div>

      <!-- Tanggal Kedaluwarsa -->
      <div id="expired-date-group">
        <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px"><?= lang('App.expired_date_optional') ?></label>
        <input type="date" name="expired_date" id="expired_date" style="font-size:11px;padding:8px;border-radius:8px">
      </div>

      <!-- Foto Item (Premium Upload Component) -->
      <div>
        <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:4px">Foto Barang</label>
        <div style="display:flex;align-items:center;gap:10px;background:var(--surface-2);padding:8px;border-radius:8px;border:1px solid var(--border)">
          <div id="photo-preview-container" style="width:50px;height:50px;border-radius:8px;overflow:hidden;border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;background:var(--surface);flex-shrink:0">
            <svg id="photo-preview-placeholder" width="20" height="20" fill="none" stroke="var(--text-faint)" stroke-width="1.8" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <img id="photo-preview-img" style="display:none;width:100%;height:100%;object-fit:cover" alt="Preview">
          </div>
          <div style="flex:1">
            <input type="file" name="photo" id="photo-input" accept="image/*" style="display:none" onchange="handlePhotoFile(this)">
            <button type="button" onclick="document.getElementById('photo-input').click()" class="btn-primary" style="padding:6px 10px;font-size:10px;border-radius:6px;width:auto;background:var(--surface);color:var(--text-muted);border:1px solid var(--border);gap:4px" title="Pilih foto atau ambil dari kamera">
              <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
              Pilih Foto
            </button>
            <p style="font-size:8px;color:var(--text-faint);margin-top:4px">Maksimal 2MB (JPG, PNG, GIF)</p>
          </div>
        </div>
      </div>

      <div>
        <label style="display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:var(--text-faint);margin-bottom:6px"><?= lang('App.status') ?></label>
        <div class="grid grid-cols-2 gap-2">
          <label class="choice-pill choice-pill--status-active is-active" id="label-item-status-active">
            <input type="radio" name="is_active" value="1" checked class="absolute opacity-0 w-0 h-0">
            <span><?= lang('App.status_active') ?></span>
          </label>
          <label class="choice-pill choice-pill--status-inactive" id="label-item-status-inactive">
            <input type="radio" name="is_active" value="0" class="absolute opacity-0 w-0 h-0">
            <span><?= lang('App.status_inactive') ?></span>
          </label>
        </div>
      </div>

      <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:10px;border-radius:8px;font-size:12px">
        <?= lang('App.save') ?>
      </button>
    </form>
  </div>
</div>

<!-- ── Items List (Ultra Compact Grid) ── -->
<?php if (empty($items)): ?>
  <div class="md-card no-print" style="padding:32px 16px;text-align:center">
    <svg width="32" height="32" fill="none" stroke="var(--text-faint)" stroke-width="1.2" viewBox="0 0 24 24" style="margin:0 auto 8px">
      <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
    </svg>
    <p style="font-size:12px;color:var(--text-faint);font-weight:600"><?= lang('App.empty_data') ?></p>
  </div>

<?php else: ?>

  <div class="items-container items-panel no-print">

    <!-- Header Row -->
    <div class="table-header-grid">
      <div style="display:flex;align-items:center;justify-content:center"><input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)" style="cursor:pointer"></div>
      <span style="text-align:center">#</span>
      <span><?= lang('App.item_name') ?></span>
      <span style="text-align:center">Exp</span>
      <span style="text-align:center"><?= lang('App.stock') ?></span>
      <span style="text-align:center"><?= lang('App.action') ?></span>
    </div>

    <!-- Rows -->
    <?php
    $offset_idx = ($current_page - 1) * $per_page;
    $idx = 1;
    foreach ($items as $item):
      $isLowStock = $item['current_stock'] <= $item['min_stock'];
      $isExpired  = false;
      $isSoonExp  = false;
      if (!empty($item['expired_date'])) {
          $diff = strtotime($item['expired_date']) - time();
          if ($diff < 0)           $isExpired = true;
          elseif ($diff < 30*86400) $isSoonExp = true;
      }
      $isActive = \App\Models\ItemModel::isActive($item['is_active'] ?? 1);
      $rowBg = 'var(--surface)';
      if ($isExpired)            $rowBg = 'rgba(239,68,68,.12)';
      elseif ($isSoonExp || $isLowStock) $rowBg = 'rgba(245,158,11,.12)';
    ?>
    <div onclick="showBinCard(<?= $item['id'] ?>)" 
         class="item-card-row<?= $isActive ? '' : ' is-inactive' ?>"
         style="background:<?= $rowBg ?>;">

      <!-- Checkbox -->
      <div style="display:flex;align-items:center;justify-content:center" onclick="event.stopPropagation()">
        <input type="checkbox" class="item-checkbox" value="<?= $item['id'] ?>" data-name="<?= esc($item['name']) ?>" data-code="<?= esc($item['code']) ?>" onchange="updateBulkActionState()" style="cursor:pointer">
      </div>

      <!-- # -->
      <div class="col-index">
        <?= $offset_idx + $idx ?>
      </div>

      <!-- Item Info -->
      <div class="col-details" style="display:flex; flex-direction:row; align-items:center; gap:8px;">
        <!-- Photo Thumbnail -->
        <div style="flex-shrink:0; display:flex; align-items:center;">
          <?php if (!empty($item['photo'])): ?>
            <img src="<?= base_url('uploads/items/' . $item['photo']) ?>" alt="<?= esc($item['name']) ?>" style="width:36px; height:36px; border-radius:8px; object-fit:cover; border:1.5px solid var(--border); box-shadow:0 1px 2px rgba(0,0,0,0.05);">
          <?php else: ?>
            <div style="width:36px; height:36px; border-radius:8px; background:linear-gradient(135deg, var(--surface-2), var(--surface-3, var(--border))); border:1.5px dashed var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-weight:bold; font-size:10px;">
              <?= strtoupper(substr(esc($item['name']), 0, 2)) ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Details Text -->
        <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:2px;">
          <div class="item-title-row">
            <span class="item-name-txt"><?= esc($item['name']) ?></span>
            <div class="badges-row">
              <?php if (! $isActive): ?>
                <span class="badge badge-rose"><?= lang('App.status_inactive') ?></span>
              <?php endif; ?>
              <?php if ($isExpired): ?>
                <span class="badge badge-rose">Exp</span>
              <?php elseif ($isSoonExp): ?>
                <span class="badge badge-amber">~Exp</span>
              <?php endif; ?>
            </div>
          </div>
          <div class="item-sub-row">
            <span class="item-code-badge"><?= esc($item['code']) ?></span>
            <span class="item-warehouse-txt"><?= esc($item['warehouse_name']) ?></span>
          </div>
        </div>
      </div>

      <!-- Exp -->
      <div class="col-exp">
        <?php if (!empty($item['expired_date'])): ?>
          <span style="color:<?= $isExpired ? '#ef4444' : ($isSoonExp ? '#f59e0b' : 'var(--text-faint)') ?>;">
            <?= date('d/m/y', strtotime($item['expired_date'])) ?>
          </span>
        <?php else: ?>
          <span style="color:var(--text-faint)">—</span>
        <?php endif; ?>
      </div>

      <!-- Stock -->
      <div class="col-stock">
        <div class="stock-badge-container">
          <span class="stock-val-num <?= $isLowStock ? 'stock-low' : 'stock-normal' ?>"><?= $item['current_stock'] ?></span>
          <span class="stock-unit-label"><?= esc($item['unit']) ?></span>
        </div>
      </div>

      <!-- Actions -->
      <div class="col-actions">
        <button onclick="event.stopPropagation(); toggleItemStatus(<?= (int) $item['id'] ?>, <?= $isActive ? 1 : 0 ?>)"
                class="icon-btn icon-btn--status"
                title="<?= lang('App.toggle_status') ?>"
                style="width:26px;height:26px;border-radius:6px;border:none;background:rgba(99,102,241,.1);color:var(--primary);cursor:pointer;display:inline-flex;align-items:center;justify-content:center">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </button>
        <button onclick="event.stopPropagation(); mutateStock(<?= htmlspecialchars(json_encode($item)) ?>)"
                class="btn-action-mutate<?= $isActive ? '' : ' is-disabled' ?>"
                title="<?= lang('App.mutate_stock') ?>"
                <?= $isActive ? '' : 'disabled' ?>>
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
        </button>
        <button onclick="event.stopPropagation(); showQRCode(<?= htmlspecialchars(json_encode($item)) ?>)"
                class="btn-action-qr"
                title="QR / Barcode">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-6 4h2m6 0v4m0-4h2m0 0v-4m-12 0h2M4 8V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m0 8v2a2 2 0 01-2 2h-2m-8 0H6a2 2 0 01-2-2v-2"/>
          </svg>
        </button>
        <button onclick="event.stopPropagation(); editItem(<?= htmlspecialchars(json_encode($item)) ?>)"
                class="btn-action-edit"
                title="<?= lang('App.edit_item') ?>">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
          </svg>
        </button>
        <button onclick="event.stopPropagation(); deleteItem(<?= $item['id'] ?>, '<?= esc(addslashes($item['name'])) ?>')"
                class="btn-action-delete"
                title="Hapus Barang">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>

    </div>
    <?php $idx++; endforeach; ?>

  </div>

  <?php if ($total_pages > 1): ?>
  <nav class="pagination-bar no-print" aria-label="Pagination">
    <span class="pagination-bar__info">
      <?= ($current_page - 1) * $per_page + 1 ?>–<?= min($current_page * $per_page, $total_items) ?> / <?= $total_items ?>
    </span>
    <div class="pagination-bar__pages">
      <?php if ($current_page > 1): ?>
        <a href="<?= pageUrl($current_page - 1, $search_query, $selected_warehouse) ?>" class="page-btn" aria-label="Previous">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
      <?php endif; ?>
      <?php
      $start = max(1, $current_page - 2);
      $end   = min($total_pages, $current_page + 2);
      for ($p = $start; $p <= $end; $p++):
      ?>
        <a href="<?= pageUrl($p, $search_query, $selected_warehouse) ?>" class="page-btn<?= $p === $current_page ? ' is-active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <?php if ($current_page < $total_pages): ?>
        <a href="<?= pageUrl($current_page + 1, $search_query, $selected_warehouse) ?>" class="page-btn" aria-label="Next">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
      <?php endif; ?>
    </div>
  </nav>
  <?php endif; ?>

<?php endif; ?>

</div><!-- .app-page.items-page -->

<!-- Floating Bulk Actions -->
<div id="bulk-action-bar" class="no-print" style="display:none;position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:var(--surface);box-shadow:0 10px 25px -5px rgba(0,0,0,0.2), 0 8px 10px -6px rgba(0,0,0,0.1);padding:12px 20px;border-radius:12px;z-index:4000;border:1px solid var(--border);align-items:center;gap:16px;">
  <span id="bulk-count" style="font-size:13px;font-weight:700;color:var(--text)">0 terpilih</span>
  <div style="display:flex;gap:8px;">
    <button onclick="printBulk('qr')" class="btn-primary" style="background:#8b5cf6;border-color:#8b5cf6;padding:6px 10px" title="Print QR Masal">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-6 4h2m6 0v4m0-4h2m0 0v-4m-12 0h2M4 8V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m0 8v2a2 2 0 01-2 2h-2m-8 0H6a2 2 0 01-2-2v-2"/></svg>
    </button>
    <button onclick="printBulk('bar')" class="btn-primary" style="background:#0ea5e9;border-color:#0ea5e9;padding:6px 10px" title="Print Barcode Masal">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h1M4 10h1M4 14h1M4 18h1M8 6h1v12H8zm4 0h1v12h-1zm4 0h1v12h-1zm4 0h1M20 10h-1M20 14h-1M20 18h-1"/></svg>
    </button>
  </div>
</div>

<!-- ── Modal Bin Card ── -->
<div id="bincard-modal-container" style="display:none;position:fixed;inset:0;z-index:5000;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:12px">
  <div class="md-card" id="bincard-modal-content" style="width:100%;max-width:620px;max-height:85vh;overflow-y:auto;padding:18px;position:relative;background:var(--surface)">
    
    <!-- Header (No Print buttons) -->
    <div class="no-print" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;border-bottom:1px solid var(--border);padding-bottom:8px">
      <h4 style="font-size:11px;font-weight:800;text-transform:uppercase;color:var(--primary);margin:0;display:flex;align-items:center;gap:4px">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Bin Card / Kartu Stok
      </h4>
      <div style="display:flex;gap:4px">
        <button onclick="printBinCard()" class="btn-primary" style="padding:4px 8px;background:#10b981;border-color:#10b981;border-radius:6px" title="Print Bin Card">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        </button>
        <button onclick="closeBinCard()" style="width:24px;height:24px;border-radius:6px;border:none;background:var(--surface-2);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </div>

    <!-- Print Header -->
    <div style="margin-bottom:12px;border-bottom:2px double var(--border);padding-bottom:8px">
      <div style="display:flex;justify-content:space-between;align-items:flex-start">
        <div>
          <h2 style="font-size:14px;font-weight:900;color:var(--text);margin:0;text-transform:uppercase"><?= esc(session()->get('company_name') ?: 'AppsBeem Logistic') ?></h2>
          <p style="font-size:9px;color:var(--text-faint);margin:1px 0 0">Warehouse Inventory Control System</p>
        </div>
        <div style="text-align:right">
          <span style="font-size:11px;font-weight:800;color:var(--primary);text-transform:uppercase;letter-spacing:0.5px">KARTU BIN / STOK</span>
        </div>
      </div>
      
      <div class="bincard-info-grid">
        <div class="bincard-info-item">
          <span class="bc-lbl">Kode Barang</span>
          <span class="bc-val" id="bc-code">—</span>
        </div>
        <div class="bincard-info-item">
          <span class="bc-lbl">Nama Barang</span>
          <span class="bc-val" id="bc-name">—</span>
        </div>
        <div class="bincard-info-item">
          <span class="bc-lbl">Gudang</span>
          <span class="bc-val" id="bc-warehouse">—</span>
        </div>
        <div class="bincard-info-item">
          <span class="bc-lbl">Satuan / Unit</span>
          <span class="bc-val" id="bc-unit">—</span>
        </div>
      </div>
    </div>

    <!-- History Table -->
    <div style="overflow-x:auto">
      <table class="bincard-table" style="border-collapse:collapse;font-size:9px;text-align:left">
        <thead>
          <tr style="border-bottom:2px solid var(--border);background:var(--surface-2)">
            <th class="bc-col-date" style="padding:4px 3px;color:var(--text-muted)"><?= lang('App.date') ?></th>
            <th class="bc-col-num" style="padding:4px 2px;color:var(--text-muted)"><?= lang('App.open_balance') ?></th>
            <th class="bc-col-num" style="padding:4px 2px;color:var(--text-muted)">In</th>
            <th class="bc-col-num" style="padding:4px 2px;color:var(--text-muted)">Out</th>
            <th class="bc-col-saldo" style="padding:4px 2px;color:var(--text-muted)">Saldo</th>
            <th class="bc-col-notes" style="padding:4px 3px;color:var(--text-muted)">Ket.</th>
            <th class="bc-col-op" style="padding:4px 3px;color:var(--text-muted)">Op.</th>
          </tr>
        </thead>
        <tbody id="bincard-rows">
          <!-- Dynamically populated -->
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div id="bincard-empty" style="display:none;text-align:center;padding:20px;color:var(--text-faint);font-size:11px">
      Tidak ada riwayat transaksi untuk barang ini.
    </div>

  </div>
</div>

<!-- ── Modal QR / Barcode ── -->
<div id="qr-modal-container" class="no-print" style="display:none;position:fixed;inset:0;z-index:5100;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:16px">
  <div class="md-card" style="width:100%;max-width:340px;padding:20px;animation:slideUp .3s cubic-bezier(.16,1,.3,1)">
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;border-bottom:1px solid var(--border);padding-bottom:10px">
      <h4 style="font-size:11px;font-weight:800;text-transform:uppercase;color:var(--primary);margin:0;display:flex;align-items:center;gap:5px">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-6 4h2m6 0v4m0-4h2m0 0v-4m-12 0h2M4 8V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m0 8v2a2 2 0 01-2 2h-2m-8 0H6a2 2 0 01-2-2v-2"/>
        </svg>
        QR Code / Barcode
      </h4>
      <button onclick="closeQRModal()" style="width:24px;height:24px;border-radius:6px;border:none;background:var(--surface-2);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <!-- Item Name -->
    <p id="qr-item-name" style="font-size:13px;font-weight:800;color:var(--text);text-align:center;margin-bottom:2px"></p>
    <p id="qr-item-code" style="font-size:10px;color:var(--text-faint);text-align:center;margin-bottom:14px;font-family:monospace"></p>
    <!-- Tabs -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:12px">
      <button onclick="switchQRTab('qr')" id="tab-qr" class="qr-tab active-tab">QR Code</button>
      <button onclick="switchQRTab('bar')" id="tab-bar" class="qr-tab">Barcode</button>
    </div>
    <!-- QR Canvas -->
    <div id="qr-panel" style="text-align:center">
      <div id="qr-canvas-wrapper" style="display:inline-block;padding:12px;background:#ffffff;border-radius:10px;border:1px solid var(--border)"></div>
    </div>
    <!-- Barcode Canvas -->
    <div id="bar-panel" style="text-align:center;display:none">
      <div style="display:inline-block;padding:12px 8px;background:#ffffff;border-radius:10px;border:1px solid var(--border)">
        <svg id="barcode-svg"></svg>
      </div>
    </div>
    <!-- Print button -->
    <button onclick="printQR()" class="btn-primary" style="width:100%;justify-content:center;margin-top:14px;padding:9px;font-size:11px;gap:5px">
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Print
    </button>
    <!-- Download button for QR/Barcode -->
    <button onclick="downloadQR()" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px;padding:9px;font-size:11px;gap:5px">
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-8m0 0l-3 3m3-3l3 3M5 20h14a2 2 0 002-2v-3a2 2 0 00-2-2H5a2 2 0 00-2 2v3a2 2 0 002 2z"/></svg>
      Download
    </button>
  </div>
</div>


<!-- ════════════════ SCRIPTS ════════════════ -->
<script>
  /* ── Expiration field toggle ── */
  function updateExpirationFieldVisibility() {
    const sel = document.getElementById('warehouse_id');
    const req = sel?.options[sel.selectedIndex]?.getAttribute('data-requires-expiration') === '1';
    const grp = document.getElementById('expired-date-group');
    const inp = document.getElementById('expired_date');
    if (grp) grp.style.display = req ? '' : 'none';
    if (!req && inp) inp.value = '';
  }
  document.getElementById('warehouse_id')?.addEventListener('change', updateExpirationFieldVisibility);

  function updateItemStatusSelector(isActive) {
    var active = String(isActive) === '1' || isActive === 1 || isActive === true;
    document.querySelectorAll('#item-form input[name="is_active"]').forEach(function(r) {
      r.checked = (active ? r.value === '1' : r.value === '0');
    });
    var onLabel = document.getElementById('label-item-status-active');
    var offLabel = document.getElementById('label-item-status-inactive');
    if (onLabel) onLabel.classList.toggle('is-active', active);
    if (offLabel) offLabel.classList.toggle('is-active', !active);
  }

  function bindItemStatusPills() {
    document.querySelectorAll('#item-form .choice-pill--status-active, #item-form .choice-pill--status-inactive').forEach(function(label) {
      label.onclick = function(e) {
        if (e.target.tagName === 'INPUT') return;
        var radio = label.querySelector('input[type="radio"]');
        if (radio) updateItemStatusSelector(radio.value);
      };
      var radio = label.querySelector('input[type="radio"]');
      if (radio) radio.onchange = function() { updateItemStatusSelector(radio.value); };
    });
  }
  bindItemStatusPills();

  /* ── Modal open/close ── */
  function toggleForm() {
    const c = document.getElementById('item-form-container');
    if (c.style.display === 'flex') {
      c.style.display = 'none';
    } else {
      document.getElementById('item-form')?.reset();
      document.getElementById('item-id').value = '';
      document.getElementById('initial-stock-group').style.display = '';
      document.getElementById('form-title').innerText = '<?= lang('App.add_new_item') ?>';
      updateItemStatusSelector('1');
      updateExpirationFieldVisibility();

      // Reset photo preview and file input
      const previewImg = document.getElementById('photo-preview-img');
      const placeholder = document.getElementById('photo-preview-placeholder');
      if (previewImg) {
        previewImg.src = '';
        previewImg.style.display = 'none';
      }
      if (placeholder) placeholder.style.display = 'block';
      const fileInput = document.getElementById('photo-input');
      if (fileInput) fileInput.value = '';

      c.style.display = 'flex';
    }
  }
  document.getElementById('item-form-container').addEventListener('click', function(e) {
    if (e.target === this) toggleForm();
  });

  /* ── Edit item ── */
  function editItem(item) {
    document.getElementById('item-id').value         = item.id;
    document.getElementById('warehouse_id').value    = item.warehouse_id;
    document.getElementById('code').value            = item.code;
    document.getElementById('name').value            = item.name;
    document.getElementById('unit').value            = item.unit;
    document.getElementById('min_stock').value       = item.min_stock;
    document.getElementById('expired_date').value    = item.expired_date || '';
    document.getElementById('initial-stock-group').style.display = 'none';
    document.getElementById('form-title').innerText  = '<?= lang('App.edit_item') ?>';
    updateItemStatusSelector(item.is_active !== undefined ? item.is_active : 1);
    updateExpirationFieldVisibility();

    // Set photo preview if exists
    const previewImg = document.getElementById('photo-preview-img');
    const placeholder = document.getElementById('photo-preview-placeholder');
    const fileInput = document.getElementById('photo-input');
    if (fileInput) fileInput.value = '';

    if (item.photo) {
      if (previewImg) {
        previewImg.src = '<?= base_url("uploads/items/") ?>/' + item.photo;
        previewImg.style.display = 'block';
      }
      if (placeholder) placeholder.style.display = 'none';
    } else {
      if (previewImg) {
        previewImg.src = '';
        previewImg.style.display = 'none';
      }
      if (placeholder) placeholder.style.display = 'block';
    }

    document.getElementById('item-form-container').style.display = 'flex';
  }

  /* ── Shared Photo Preview Handler ── */
  function handlePhotoFile(input) {
    const file = input.files[0];
    if (!file) return;
    const previewImg   = document.getElementById('photo-preview-img');
    const placeholder  = document.getElementById('photo-preview-placeholder');
    const reader = new FileReader();
    reader.onload = function(e) {
      if (previewImg) { previewImg.src = e.target.result; previewImg.style.display = 'block'; }
      if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
    // If triggered from camera input, transfer file to the named photo-input so form submits it
    if (input.id === 'photo-camera-input') {
      try {
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('photo-input').files = dt.files;
      } catch(e) { /* DataTransfer not supported – file still previews but won't upload on older browsers */ }
    }
  }

  async function toggleItemStatus(id, currentlyActive) {
    const dk = document.documentElement.classList.contains('dark');
    const bg = dk ? '#0f172a' : '#ffffff';
    const fg = dk ? '#f1f5f9' : '#111827';
    const nextLabel = currentlyActive ? '<?= lang('App.status_inactive') ?>' : '<?= lang('App.status_active') ?>';
    const confirm = await Swal.fire({
      title: '<?= lang('App.toggle_status') ?>',
      text: nextLabel + '?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#6366f1',
      cancelButtonColor: '#64748b',
      confirmButtonText: '<?= lang('App.save') ?>',
      cancelButtonText: '<?= lang('App.cancel') ?>',
      background: bg,
      color: fg
    });
    if (!confirm.isConfirmed) return;
    try {
      const body = new FormData();
      body.append('id', id);
      const res = await fetch('<?= base_url('inventory/items/toggle-status') ?>', { method: 'POST', body });
      const data = await res.json();
      if (data.status === 'success') {
        Swal.fire({ icon: 'success', title: '<?= lang('App.success') ?>', text: data.message, timer: 1200, showConfirmButton: false, background: bg, color: fg })
          .then(function() {
            if (typeof loadPage === 'function') loadPage(location.href);
            else location.reload();
          });
      } else {
        Swal.fire({ icon: 'error', title: '<?= lang('App.failed') ?>', text: data.message, confirmButtonColor: '#6366f1', background: bg, color: fg });
      }
    } catch (e) { console.error(e); }
  }

  /* ── Mutate stock ── */
  async function mutateStock(item) {
    if (String(item.is_active) === '0') {
      Swal.fire({ icon: 'warning', title: '<?= lang('App.failed') ?>', text: '<?= lang('App.item_inactive') ?>', confirmButtonColor: '#6366f1' });
      return;
    }
    const dk = document.documentElement.classList.contains('dark');
    const bg = dk ? '#1e293b' : '#ffffff';
    const fg = dk ? '#f1f5f9' : '#111827';
    const bg2 = dk ? '#0f172a' : '#f8fafc';
    const border = dk ? '#334155' : '#e2e8f0';

    const { value: fv } = await Swal.fire({
      title: `<span style="font-size:13px;font-weight:800;color:${fg};line-height:1.2;display:block;margin-bottom:2px">${item.name}</span><span style="font-size:10px;color:#6366f1;font-weight:700;text-transform:uppercase;letter-spacing:0.5px"><?= lang('App.mutate_stock') ?></span>`,
      background: bg,
      color: fg,
      html: `
        <div style="text-align:left;display:flex;flex-direction:column;gap:10px;padding:2px 0">
          <div style="background:rgba(99,102,241,.06);border-radius:8px;padding:8px 12px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:10px;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:0.3px"><?= lang('App.current_stock_label') ?></span>
            <span style="font-size:14px;font-weight:900;color:#6366f1">${item.current_stock} ${item.unit}</span>
          </div>
          <div>
            <label style="font-size:9px;font-weight:700;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:6px;letter-spacing:0.3px"><?= lang('App.mutation_type') ?></label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
              <label style="display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:8px;border-radius:8px;border:1.5px solid #10b981;background:rgba(16,185,129,.04);cursor:pointer;font-size:11px;font-weight:700;color:#10b981;white-space:nowrap;user-select:none">
                <input type="radio" name="swal-type" value="in" checked style="accent-color:#10b981;margin:0"> <?= lang('App.stock_in') ?>
              </label>
              <label style="display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:8px;border-radius:8px;border:1.5px solid ${border};background:${bg2};cursor:pointer;font-size:11px;font-weight:700;color:${fg};white-space:nowrap;user-select:none">
                <input type="radio" name="swal-type" value="out" style="accent-color:#ef4444;margin:0"> <span style="color:#ef4444"><?= lang('App.stock_out') ?></span>
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:9px;font-weight:700;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:5px;letter-spacing:0.3px"><?= lang('App.quantity') ?> (${item.unit})</label>
            <input id="swal-qty" type="number" min="1" value="1" style="width:100%;padding:8px 10px;border-radius:8px;border:1.5px solid ${border};background:${bg2};color:${fg};font-size:13px;font-weight:700;font-family:'Outfit',sans-serif">
          </div>
          <div>
            <label style="font-size:9px;font-weight:700;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:5px;letter-spacing:0.3px"><?= lang('App.notes') ?></label>
            <input id="swal-notes" type="text" placeholder="<?= lang('App.enter_notes_placeholder') ?>" style="width:100%;padding:8px 10px;border-radius:8px;border:1.5px solid ${border};background:${bg2};color:${fg};font-size:12px;font-family:'Outfit',sans-serif">
          </div>
        </div>`,
      didOpen: () => {
        const types = document.getElementsByName('swal-type');
        types.forEach(radio => {
          radio.addEventListener('change', () => {
            types.forEach(r => {
              const label = r.closest('label');
              if (r.checked) {
                if (r.value === 'in') {
                  label.style.borderColor = '#10b981';
                  label.style.background = 'rgba(16,185,129,.04)';
                  label.style.color = '#10b981';
                } else {
                  label.style.borderColor = '#ef4444';
                  label.style.background = 'rgba(239,68,68,.04)';
                  label.style.color = '#ef4444';
                }
              } else {
                label.style.borderColor = border;
                label.style.background = bg2;
                label.style.color = fg;
              }
            });
          });
        });
      },
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonColor: '#6366f1',
      cancelButtonColor: '#64748b',
      confirmButtonText: '<?= lang('App.process_mutation') ?>',
      cancelButtonText: '<?= lang('App.cancel') ?>',
      customClass: { popup: 'swal-rounded' },
      preConfirm: () => {
        const types = document.getElementsByName('swal-type');
        let type = 'in';
        for (let t of types) if (t.checked) type = t.value;
        return { type, quantity: document.getElementById('swal-qty').value, notes: document.getElementById('swal-notes').value };
      }
    });

    if (!fv) return;
    if (!fv.quantity || parseInt(fv.quantity) <= 0) {
      Swal.fire({ icon:'error', title:'<?= lang('App.failed') ?>', text:'<?= lang('App.qty_greater_than_zero') ?>', confirmButtonColor:'#6366f1', background:bg, color:fg });
      return;
    }

    const body = new FormData();
    body.append('item_id', item.id);
    body.append('type', fv.type);
    body.append('quantity', fv.quantity);
    body.append('notes', fv.notes);

    try {
      const res    = await fetch('<?= base_url('inventory/mutate') ?>', { method:'POST', body });
      const result = await res.json();
      if (result.status === 'success') {
        const el = document.getElementById(`stock-val-${item.id}`);
        if (el) el.innerText = result.new_stock;
        Swal.fire({ icon:'success', title:'<?= lang('App.success') ?>', text:'<?= lang('App.success_mutate') ?>', timer:1400, showConfirmButton:false, background:bg, color:fg })
            .then(() => location.reload());
      } else {
        Swal.fire({ icon:'error', title:'<?= lang('App.failed') ?>', text:result.message, confirmButtonColor:'#6366f1', background:bg, color:fg });
      }
    } catch(e) { console.error(e); }
  }

  /* ── Add/Edit form submit ── */
  document.getElementById('item-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const dk = document.documentElement.classList.contains('dark');
    const bg = dk ? '#1e293b' : '#ffffff';
    const fg = dk ? '#f1f5f9' : '#111827';
    try {
      const res    = await fetch(this.action, { method:'POST', body: new FormData(this) });
      const result = await res.json();
      if (result.status === 'success') {
        Swal.fire({ icon:'success', title:'<?= lang('App.success_modal') ?>', text:'<?= lang('App.success_save') ?>', timer:1400, showConfirmButton:false, background:bg, color:fg })
            .then(() => location.reload());
      } else {
        Swal.fire({ icon:'error', title:'<?= lang('App.failed') ?>', text:result.message, confirmButtonColor:'#6366f1', background:bg, color:fg });
      }
    } catch(e) { console.error(e); }
  });

  /* ── Show Bin Card Modal ── */
  async function showBinCard(itemId) {
    try {
      const response = await fetch(`<?= base_url('inventory/bincard') ?>/${itemId}`);
      const data = await response.json();
      
      if (data.status === 'success') {
        document.getElementById('bc-code').innerText = data.item.code;
        document.getElementById('bc-name').innerText = data.item.name;
        document.getElementById('bc-warehouse').innerText = data.item.warehouse_name;
        document.getElementById('bc-unit').innerText = data.item.unit;
        
        const tbody = document.getElementById('bincard-rows');
        const emptyDiv = document.getElementById('bincard-empty');
        tbody.innerHTML = '';
        
        if (data.history.length === 0) {
          emptyDiv.style.display = 'block';
        } else {
          emptyDiv.style.display = 'none';
          data.history.forEach(tx => {
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid var(--border)';
            
            tr.innerHTML = `
              <td class="bc-col-date" style="padding:4px 3px;color:var(--text)">${tx.date}</td>
              <td class="bc-col-num" style="padding:4px 2px;font-weight:700;color:var(--text-muted)">${tx.open ?? 0}</td>
              <td class="bc-col-num" style="padding:4px 2px;color:#10b981;font-weight:700">${tx.qty_in}</td>
              <td class="bc-col-num" style="padding:4px 2px;color:#ef4444;font-weight:700">${tx.qty_out}</td>
              <td class="bc-col-saldo" style="padding:4px 2px">${tx.balance}</td>
              <td class="bc-col-notes" style="padding:4px 3px;color:var(--text)" title="${(tx.notes || '').replace(/"/g, '&quot;')}">${tx.notes}</td>
              <td class="bc-col-op" style="padding:4px 3px" title="${(tx.operator || '').replace(/"/g, '&quot;')}">${tx.operator}</td>
            `;
            tbody.appendChild(tr);
          });
        }
        
        const container = document.getElementById('bincard-modal-container');
        container.style.display = 'flex';
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#6366f1' });
      }
    } catch (err) {
      console.error(err);
    }
  }

  function closeBinCard() {
    document.getElementById('bincard-modal-container').style.display = 'none';
  }

  document.getElementById('bincard-modal-container').addEventListener('click', function(e) {
    if (e.target === this) closeBinCard();
  });

  function printBinCard() {
    window.print();
  }

  /* ── Delete item ── */
  async function deleteItem(id, name) {
    const dk = document.documentElement.classList.contains('dark');
    const bg = dk ? '#1e293b' : '#ffffff';
    const fg = dk ? '#f1f5f9' : '#111827';

    const result = await Swal.fire({
      title: `<span style="font-size:14px;font-weight:800;color:#ef4444">Hapus Barang?</span>`,
      html: `<p style="font-size:12px;color:${fg};margin:0">Yakin ingin menghapus <strong>${name}</strong>?<br><span style="font-size:11px;color:#ef4444">Tindakan ini tidak bisa dibatalkan.</span></p>`,
      background: bg,
      color: fg,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
      customClass: { popup: 'swal-rounded' },
    });

    if (!result.isConfirmed) return;

    try {
      const body = new FormData();
      body.append('id', id);
      body.append('_method', 'DELETE');
      const res = await fetch('<?= base_url('inventory/items') ?>', { method: 'POST', body });
      const data = await res.json();
      if (data.status === 'success') {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Barang berhasil dihapus.', timer: 1200, showConfirmButton: false, background: bg, color: fg })
             .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#6366f1', background: bg, color: fg });
      }
    } catch(e) { console.error(e); }
  }

  /* ── Bulk Actions ── */
  function toggleCheckAll(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateBulkActionState();
  }

  function updateBulkActionState() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const bar = document.getElementById('bulk-action-bar');
    const count = document.getElementById('bulk-count');
    if (checkboxes.length > 0) {
      count.innerText = checkboxes.length + ' terpilih';
      bar.style.display = 'flex';
    } else {
      bar.style.display = 'none';
      const checkAll = document.getElementById('checkAll');
      if (checkAll) checkAll.checked = false;
    }
  }

  function printBulk(type) {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkboxes.length === 0) return;

    const items = Array.from(checkboxes).map(cb => ({
      name: cb.getAttribute('data-name'),
      code: cb.getAttribute('data-code')
    }));

    const win = window.open('', '_blank');
    let html = `
      <!DOCTYPE html><html><head><title>Print Masal</title>
      <style>
        *{margin:0;padding:0;box-sizing:border-box} 
        body{font-family:'Outfit',sans-serif;padding:20px;background:#fff;}
        .grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(160px, 1fr));gap:15px;align-items:start;justify-items:center}
        .item-card{display:flex;flex-direction:column;align-items:center;text-align:center;padding:12px;border:1.5px dashed #ccc;border-radius:12px;width:100%;}
        h2{font-size:12px;font-weight:800;margin-bottom:2px;width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        p{font-size:10px;color:#666;margin-bottom:10px;font-family:monospace;}
        .code-container{display:flex;justify-content:center;width:100%;min-height:80px;align-items:center;}
        @media print {
          .grid { gap: 10px; }
          .item-card { break-inside: avoid; border: 1.5px dashed #999; }
        }
      </style>
      <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"><\/script>
      <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>
      </head>
      <body>
      <div class="grid">
    `;

    items.forEach((item, idx) => {
      html += `
        <div class="item-card">
          <h2>${item.name}</h2>
          <p>${item.code}</p>
          <div class="code-container" id="code-${idx}">
            ${type === 'bar' ? `<svg id="svg-${idx}"></svg>` : ''}
          </div>
        </div>
      `;
    });

    html += `
      </div>
      <script>
        window.onload = function() {
          const items = ${JSON.stringify(items)};
          items.forEach((item, idx) => {
            if ('${type}' === 'qr') {
              new QRCode(document.getElementById('code-' + idx), { text: item.code, width: 100, height: 100, correctLevel: QRCode.CorrectLevel.M });
            } else {
              try {
                JsBarcode('#svg-' + idx, item.code, { format: 'CODE128', width: 1.5, height: 40, displayValue: true, fontSize: 10, margin: 4 });
              } catch(e) { console.warn(e); }
            }
          });
          setTimeout(() => { window.print(); window.close(); }, 800);
        };
      <\/script>
      </body></html>
    `;

    win.document.write(html);
    win.document.close();
  }

  /* ── QR / Barcode Modal ── */
  let _qrCurrentTab = 'qr';

  async function showQRCode(item) {
    document.getElementById('qr-item-name').innerText = item.name;
    document.getElementById('qr-item-code').innerText = item.code;

    // Render QR
    const wrapper = document.getElementById('qr-canvas-wrapper');
    wrapper.innerHTML = '';
    if (window.QRCode) {
      new QRCode(wrapper, { text: item.code, width: 160, height: 160, correctLevel: QRCode.CorrectLevel.M });
    }

    // Render Barcode
    if (window.JsBarcode) {
      try {
        JsBarcode('#barcode-svg', item.code, { format: 'CODE128', width: 2, height: 60, displayValue: true, fontSize: 12, margin: 6 });
      } catch(e) { console.warn('Barcode error:', e); }
    }

    switchQRTab('qr');
    document.getElementById('qr-modal-container').style.display = 'flex';
  }

  function switchQRTab(tab) {
    _qrCurrentTab = tab;
    document.getElementById('qr-panel').style.display  = tab === 'qr'  ? 'block' : 'none';
    document.getElementById('bar-panel').style.display = tab === 'bar' ? 'block' : 'none';
    document.getElementById('tab-qr').classList.toggle('active-tab',  tab === 'qr');
    document.getElementById('tab-bar').classList.toggle('active-tab', tab === 'bar');
  }

  function closeQRModal() {
    document.getElementById('qr-modal-container').style.display = 'none';
  }

  document.getElementById('qr-modal-container').addEventListener('click', function(e) {
    if (e.target === this) closeQRModal();
  });

  function printQR() {
    const itemName = document.getElementById('qr-item-name').innerText;
    const itemCode = document.getElementById('qr-item-code').innerText;
    const isQR = _qrCurrentTab === 'qr';

    let contentHTML = '';
    if (isQR) {
      const canvas = document.querySelector('#qr-canvas-wrapper canvas') || document.querySelector('#qr-canvas-wrapper img');
      const src = canvas?.tagName === 'CANVAS' ? canvas.toDataURL() : canvas?.src;
      contentHTML = src ? `<img src="${src}" style="width:180px;height:180px">` : 'QR tidak tersedia';
    } else {
      const svgEl = document.getElementById('barcode-svg');
      const serialized = new XMLSerializer().serializeToString(svgEl);
      const encoded = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(serialized)));
      contentHTML = `<img src="${encoded}" style="max-width:280px">`;
    }

    const win = window.open('', '_blank');
    win.document.write(`
      <!DOCTYPE html><html><head><title>Print - ${itemName}</title>
      <style>*{margin:0;padding:0;box-sizing:border-box} body{font-family:'Outfit',sans-serif;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;padding:20px;background:#fff}
      h2{font-size:14px;font-weight:800;margin-bottom:2px;text-align:center}
      p{font-size:11px;color:#666;margin-bottom:14px;font-family:monospace;text-align:center}
      img{display:block}</style></head>
      <body><h2>${itemName}</h2><p>${itemCode}</p>${contentHTML}</body></html>`);
    win.document.close();
    setTimeout(() => { win.print(); win.close(); }, 400);
    }
        // Download QR/Barcode as JPG image file
    // Download QR/Barcode as JPG image with name/code overlay and white background
    async function downloadQR() {
      const itemName = document.getElementById('qr-item-name').innerText.trim();
      const itemCode = document.getElementById('qr-item-code').innerText.trim();
      const isQR = _qrCurrentTab === 'qr';
      let dataUrl = '';
      let filename = '';
      if (isQR) {
        // Get source canvas or image element
        const src = document.querySelector('#qr-canvas-wrapper canvas') || document.querySelector('#qr-canvas-wrapper img');
        // Determine dimensions
        const padding = 20; // white margin
        const textHeight = 50; // increased space for name/code
        let imgWidth, imgHeight, drawImg;
        if (src?.tagName === 'CANVAS') {
          imgWidth = src.width;
          imgHeight = src.height;
          drawImg = async () => {
            const ctx = tempCanvas.getContext('2d');
            ctx.drawImage(src, padding, padding + textHeight, imgWidth, imgHeight);
          };
        } else if (src?.src) {
          // Load image into bitmap first
          const resp = await fetch(src.src);
          const blob = await resp.blob();
          const bitmap = await createImageBitmap(blob);
          imgWidth = bitmap.width;
          imgHeight = bitmap.height;
          drawImg = async () => {
            const ctx = tempCanvas.getContext('2d');
            ctx.drawImage(bitmap, padding, padding + textHeight, imgWidth, imgHeight);
          };
        } else {
          console.error('QR source not found');
          return;
        }
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = imgWidth + padding * 2;
        tempCanvas.height = imgHeight + padding * 2 + textHeight;
        const ctx = tempCanvas.getContext('2d');
        // White background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        // Text overlay (name then code)
        ctx.fillStyle = '#000000';
        ctx.font = '14px Outfit, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(itemName, tempCanvas.width / 2, padding + 12);
        ctx.fillText(itemCode, tempCanvas.width / 2, padding + 28);
        // Draw QR image
        await drawImg();
        dataUrl = tempCanvas.toDataURL('image/jpeg', 0.92);
        filename = `${itemName}_${itemCode}_QR.jpg`;
      } else {
        // Barcode (SVG) → JPEG with name overlay
        const svgEl = document.getElementById('barcode-svg');
        const serialized = new XMLSerializer().serializeToString(svgEl);
        const svgBlob = new Blob([serialized], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);
        await new Promise((resolve) => {
          const img = new Image();
          img.onload = () => {
            const padding = 20;
            const textHeight = 30;
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = img.width + padding * 2;
            tempCanvas.height = img.height + padding * 2 + textHeight;
            const ctx = tempCanvas.getContext('2d');
            // White background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
            // Name text
            ctx.fillStyle = '#000000';
            ctx.font = '14px Outfit, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(itemName, tempCanvas.width / 2, padding + 12);
            // Draw barcode image below text
            ctx.drawImage(img, padding, padding + textHeight);
            dataUrl = tempCanvas.toDataURL('image/jpeg', 0.92);
            URL.revokeObjectURL(url);
            resolve();
          };
          img.onerror = () => {
            console.error('Failed to load barcode SVG for conversion');
            resolve();
          };
          img.src = url;
        });
        filename = `${itemName}_${itemCode}_Barcode.jpg`;
      }
      if (dataUrl) {
        const a = document.createElement('a');
        a.href = dataUrl;
        a.download = filename;
        a.click();
      }
    }

    // Auto-open Add Item form if add_code parameter is present (redirected from scan page)
    window.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const addCode = urlParams.get('add_code');
      if (addCode) {
        const container = document.getElementById('item-form-container');
        if (container) {
          toggleForm();
          const codeInput = document.getElementById('code');
          if (codeInput) codeInput.value = addCode;
          // Clean up URL without reloading
          const url = new URL(window.location);
          url.searchParams.delete('add_code');
          window.history.replaceState({}, '', url);
        }
      }
    });
</script>

<!-- QRCode + JsBarcode libraries -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<?php $this->endSection() ?>
