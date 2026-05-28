<?= $this->extend('layout/main') ?>
<?php $this->setVar('hideBottomNav', true); ?>

<?= $this->section('content') ?>
<style>
/* Premium Adaptive Scanner Theme */
.scan-page {
  height: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: radial-gradient(circle at 50% 30%, #f1f5f9, #cbd5e1);
  padding: 2rem 1rem 1.5rem;
  color: #0f172a;
  font-family: 'Outfit', sans-serif;
}
html.dark .scan-page {
  background: radial-gradient(circle at 50% 30%, #1e293b, #0f172a);
  color: #fff;
}
.scan-status-bar {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(0, 0, 0, 0.05);
  padding: 12px 20px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 500px;
  margin-bottom: 24px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}
html.dark .scan-status-bar {
  background: rgba(30, 41, 59, 0.7);
  border-color: rgba(255, 255, 255, 0.1);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}
.scan-status-dot {
  width: 12px; height: 12px; border-radius: 50%;
  background: #10b981; box-shadow: 0 0 10px #10b981;
  animation: pulse 1.5s infinite alternate;
}
@keyframes pulse {
  0% { transform: scale(0.9); opacity: 0.7; }
  100% { transform: scale(1.1); opacity: 1; }
}
.scan-status-title { font-size: 15px; font-weight: 800; color: #0f172a; letter-spacing: 0.5px; }
html.dark .scan-status-title { color: #fff; }
.scan-status-sub { font-size: 11px; color: #64748b; margin-top: 2px; }
html.dark .scan-status-sub { color: #94a3b8; }
.scan-pause-btn {
  background: rgba(0, 0, 0, 0.05); border: none; color: #0f172a;
  padding: 6px 12px; border-radius: 8px; font-size: 12px;
  font-weight: 600; cursor: pointer; transition: background 0.2s;
}
.scan-pause-btn:hover { background: rgba(0, 0, 0, 0.1); }
html.dark .scan-pause-btn { background: rgba(255, 255, 255, 0.1); color: #fff; }
html.dark .scan-pause-btn:hover { background: rgba(255, 255, 255, 0.2); }
.scan-viewport-wrap {
  position: relative; width: 100%; max-width: 500px;
  height: 400px; border-radius: 24px 24px 0 0; overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
  background: #000;
}
#reader { width: 100%; height: 100%; }
#reader video { object-fit: cover !important; width: 100% !important; height: 100% !important; }
.scan-frame-overlay {
  position: absolute; inset: 0; pointer-events: none;
  z-index: 10; display: flex; align-items: center; justify-content: center;
  isolation: isolate; /* GPU layer tersendiri untuk laser, terpisah dari video */
}
.scan-frame-box { width: 70%; height: 60%; position: relative; display: block !important; }
.scan-corner {
  position: absolute; width: 40px; height: 40px;
  border-color: #10b981; border-style: solid; border-width: 0;
  display: block !important; transition: all 0.3s ease;
}
.scan-corner.tl { top: 0; left: 0; border-top-width: 4px; border-left-width: 4px; border-top-left-radius: 12px; }
.scan-corner.tr { top: 0; right: 0; border-top-width: 4px; border-right-width: 4px; border-top-right-radius: 12px; }
.scan-corner.bl { bottom: 0; left: 0; border-bottom-width: 4px; border-left-width: 4px; border-bottom-left-radius: 12px; }
.scan-corner.br { bottom: 0; right: 0; border-bottom-width: 4px; border-right-width: 4px; border-bottom-right-radius: 12px; }
.scan-laser-track {
  position: absolute;
  top: 10%; height: 80%;
  left: 5%; right: 5%;
  pointer-events: none;
  /* Isolasi layer agar compositing efisien */
  contain: paint;
  overflow: hidden;
}
.scan-laser {
  position: absolute;
  top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent 0%, #10b981 20%, #34d399 50%, #10b981 80%, transparent 100%);
  box-shadow: 0 0 8px 2px rgba(16, 185, 129, 0.55);
  /* GPU compositor animation — tidak terpengaruh JS main thread scanner */
  will-change: transform;
  animation: scan-laser-anim 2s infinite ease-in-out;
}
/*
  Kalkulasi: viewport 400px × frame-box 60% = 240px × track 80% = 192px − 2px laser = 190px
  Menggunakan transform (GPU compositor thread) bukan top (layout main thread)
*/
@keyframes scan-laser-anim {
  0%, 100% { transform: translateY(0) translateZ(0); }
  50%       { transform: translateY(190px) translateZ(0); }
}
.scan-toolbar { margin-top: 0; display: flex; justify-content: center; width: 100%; max-width: 500px; }
.scan-tool-btn {
  width: 100%; display: flex; flex-direction: row;
  align-items: center; justify-content: center; gap: 10px;
  color: #0f172a; background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(0, 0, 0, 0.1); border-top: none;
  border-radius: 0 0 20px 20px; padding: 14px 20px;
  font-size: 13px; font-weight: 700; letter-spacing: 0.5px;
  cursor: pointer; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}
.scan-tool-btn:hover, .scan-tool-btn.active {
  background: rgba(99, 102, 241, 0.15);
  border-color: #6366f1; color: #4f46e5;
}
html.dark .scan-tool-btn {
  color: #e2e8f0; background: rgba(30, 41, 59, 0.9);
  border-color: rgba(255, 255, 255, 0.15); border-top: none;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
html.dark .scan-tool-btn:hover, html.dark .scan-tool-btn.active {
  background: rgba(99, 102, 241, 0.25); color: #fff;
}
.scan-tool-btn svg { width: 20px; height: 20px; opacity: 0.9; }
.scan-floating-actions {
  position: absolute; top: 16px; right: 16px;
  display: flex; flex-direction: row; gap: 12px; z-index: 20; pointer-events: auto;
}
.scan-float-btn {
  width: 44px; height: 44px; border-radius: 50%;
  background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.15); color: #fff;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
}
.scan-float-btn svg { width: 20px; height: 20px; }
/* Flash OFF — gelap/transparan */
.scan-float-btn.flash-off {
  background: rgba(0, 0, 0, 0.5);
  border-color: rgba(255, 255, 255, 0.15);
  color: rgba(255, 255, 255, 0.6);
}
/* Flash ON — kuning bersinar seperti senter nyala */
.scan-float-btn.flash-on {
  background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%);
  border: 2px solid #fef08a;
  color: #1a0a00;
  box-shadow: 0 0 20px rgba(250, 204, 21, 0.7), 0 4px 12px rgba(0,0,0,0.2);
  transform: scale(1.08);
}

/* Simple History List */
.scan-history-list { padding: 0; margin: 0; list-style: none; }
.scan-history-item {
  display: flex !important; align-items: center !important; justify-content: space-between !important;
  background: transparent !important; border: none !important; border-bottom: 1px dashed rgba(150,150,150,0.2) !important;
  border-radius: 0 !important; padding: 10px 4px !important; box-shadow: none !important;
}
.scan-history-item:last-child { border-bottom: none !important; }
.scan-history-item .name { font-size: 13px !important; font-weight: 600 !important; }
.scan-history-item .meta { font-size: 10px !important; opacity: 0.7 !important; }

/* Override: scan page — app-shell tidak perlu min-height 100vh */
#scan-app ~ * .app-shell,
.scan-page .app-shell { min-height: unset !important; height: auto !important; }
/* Cara lebih andal: set langsung di body saat halaman scan */
body:has(.scan-page) .app-shell { min-height: unset !important; height: auto !important; }
</style>

<div class="scan-page" id="scan-app"
  data-url-process="<?= base_url('scan/process') ?>"
  data-url-mutate="<?= base_url('inventory/mutate') ?>"
  data-url-bincard="<?= base_url('inventory/bincard') ?>"
  data-url-items="<?= base_url('inventory/items') ?>">

  <!-- Status -->
  <div class="scan-status-bar" id="scan-status">
    <div class="flex items-center gap-3 min-w-0">
      <div class="scan-status-dot"></div>
      <div class="min-w-0">
        <div class="scan-status-title" id="scan-status-title"><?= lang('App.scanning') ?></div>
        <div class="scan-status-sub" id="scan-status-sub"><?= lang('App.scan_instruction') ?></div>
      </div>
    </div>
    <button type="button" id="btn-pause-scan" class="scan-pause-btn" aria-label="Pause">
      <?= lang('App.scan_paused') ?>
    </button>
  </div>

  <!-- Camera -->
  <div class="scan-viewport-wrap md-card">
    <div id="reader"></div>
    <div class="scan-frame-overlay" id="scan-overlay">
      <div class="scan-frame-box">
        <div class="scan-corner tl"></div>
        <div class="scan-corner tr"></div>
        <div class="scan-corner bl"></div>
        <div class="scan-corner br"></div>
        <!-- Laser wrapper: top 10%-90% dari frame box, laser bergerak penuh di dalamnya -->
        <div class="scan-laser-track">
          <div class="scan-laser"></div>
        </div>
      </div>
      <!-- Floating action buttons -->
      <div class="scan-floating-actions">
        <button type="button" class="scan-float-btn flash-off" id="btn-flash" hidden title="<?= lang('App.toggle_flash') ?>">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </button>
      </div>
    </div>
  </div>  <!-- Toolbar -->

  <div class="scan-toolbar">
    <button type="button" class="scan-tool-btn" id="btn-toggle-manual">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
      <span><?= lang('App.manual_entry') ?></span>
    </button>
  </div>

  <div class="md-card scan-bottom-panel" style="width: 100%; max-width: 500px; padding: 0; margin-top: 12px; flex: 0 0 auto; overflow: hidden;">
    <!-- Manual input -->
    <div class="scan-manual-card hidden" id="manual-panel" style="border-bottom: 1px solid var(--border); background: var(--surface-2); padding: 12px 14px;">
      <div class="scan-section-label" style="margin-bottom: 8px;"><?= lang('App.or_manual_input') ?></div>
      <div class="scan-manual-row" style="margin-top: 0;">
        <input type="text" id="manual-code" placeholder="<?= lang('App.manual_code_placeholder') ?>" autocomplete="off" inputmode="text" style="background: var(--surface); border: 1px solid var(--border);">
        <button type="button" class="btn-primary px-4 py-3 text-xs shrink-0" id="btn-manual-submit"><?= lang('App.process') ?></button>
      </div>
    </div>

    <!-- History & Tip -->
    <div style="padding: 10px 14px 12px;">
      <div class="scan-section-label" style="margin-bottom: 6px;"><?= lang('App.recent_scans') ?></div>
      <ul class="scan-history-list" id="scan-history" style="margin-bottom: 0;">
        <li class="scan-empty-history" id="history-empty" style="padding: 6px 4px; font-size: 11px; color: var(--text-faint);"><?= lang('App.empty_data') ?></li>
      </ul>
    </div>
  </div>

  <!-- Hasil scan (modal) -->
  <div id="scan-result-modal" class="scan-modal-overlay" style="display:none" role="dialog" aria-modal="true" aria-labelledby="scan-modal-title">
    <div class="scan-modal-sheet md-card scan-result-card">
      <div class="scan-modal-handle"></div>
      <div class="scan-result-head" style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px">
        <div class="min-w-0 flex-1">
          <div class="scan-section-label" style="color:var(--primary);margin-bottom:4px"><?= lang('App.scan_found') ?></div>
          <div class="scan-result-name" id="res-name">—</div>
          <div class="scan-result-code" id="res-code">—</div>
        </div>
        <button type="button" id="btn-close-result" class="scan-modal-close" aria-label="Close">×</button>
      </div>
      <div class="scan-badge-row hidden" id="res-badges"></div>
      <div class="scan-result-grid">
        <div class="scan-result-cell">
          <span><?= lang('App.warehouse') ?></span>
          <strong id="res-warehouse">—</strong>
        </div>
        <div class="scan-result-cell">
          <span><?= lang('App.current_stock') ?></span>
          <strong id="res-stock">—</strong>
        </div>
        <div class="scan-result-cell">
          <span><?= lang('App.min_stock') ?></span>
          <strong id="res-min">—</strong>
        </div>
        <div class="scan-result-cell">
          <span><?= lang('App.unit') ?></span>
          <strong id="res-unit">—</strong>
        </div>
      </div>
      <div class="scan-mutate-panel">
        <div class="scan-section-label"><?= lang('App.mutate_stock') ?></div>
        <div class="scan-mutate-type">
          <button type="button" class="scan-type-btn in active" data-type="in"><?= lang('App.stock_in_short') ?> (+)</button>
          <button type="button" class="scan-type-btn out" data-type="out"><?= lang('App.stock_out_short') ?> (−)</button>
        </div>
        <div class="scan-qty-row">
          <button type="button" class="scan-qty-btn" id="qty-minus" aria-label="-">−</button>
          <input type="number" class="scan-qty-input" id="mutate-qty" value="1" min="1" max="99999" inputmode="numeric">
          <button type="button" class="scan-qty-btn" id="qty-plus" aria-label="+">+</button>
        </div>
        <textarea class="scan-notes" id="mutate-notes" placeholder="<?= lang('App.enter_notes_desc') ?>"></textarea>
        <button type="button" class="btn-primary w-full py-3.5 text-sm" id="btn-apply-mutate">
          <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <?= lang('App.apply_mutation') ?>
        </button>
      </div>
      <div class="scan-result-actions">
        <button type="button" class="scan-tool-btn" id="btn-scan-again" style="flex-direction:row;gap:6px">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          <?= lang('App.scan_again') ?>
        </button>
        <button type="button" class="scan-tool-btn" id="btn-bincard" style="flex-direction:row;gap:6px">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <?= lang('App.view_bincard') ?>
        </button>
      </div>
    </div>
  </div>

  <!-- Bincard modal -->
  <div id="bincard-modal" class="scan-modal-overlay" style="display:none">
    <div class="scan-modal-sheet md-card" style="max-height:78vh;overflow:hidden;display:flex;flex-direction:column">
      <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
        <h3 style="font-size:14px;font-weight:800" id="bc-title"><?= lang('App.view_bincard') ?></h3>
        <button type="button" id="bc-close" style="width:32px;height:32px;border-radius:10px;border:1px solid var(--border);background:var(--surface-2);font-weight:700">×</button>
      </div>
      <div style="padding:12px 16px;overflow-y:auto;flex:1">
        <table class="bincard-table-compact" style="font-size:9px;border-collapse:collapse">
          <thead>
            <tr style="color:var(--text-faint);text-transform:uppercase;font-size:8px">
              <th class="bc-col-date" style="padding:4px 2px"><?= lang('App.date') ?></th>
              <th class="bc-col-num" style="padding:4px 2px"><?= lang('App.open_balance') ?></th>
              <th class="bc-col-num" style="padding:4px 2px">In</th>
              <th class="bc-col-num" style="padding:4px 2px">Out</th>
              <th class="bc-col-saldo" style="padding:4px 2px">Saldo</th>
            </tr>
          </thead>
          <tbody id="bc-rows"></tbody>
        </table>
        <p id="bc-empty" class="hidden text-center py-6 text-xs" style="color:var(--text-faint)"><?= lang('App.empty_data') ?></p>
      </div>
    </div>
  </div>

  <a href="<?= base_url('dashboard') ?>" class="scan-fab-back">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    <?= lang('App.back') ?>
  </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
  const app = document.getElementById('scan-app');
  if (!app) return;

  const LANG = {
    scanning: <?= json_encode(lang('App.scanning')) ?>,
    instruction: <?= json_encode(lang('App.scan_instruction')) ?>,
    paused: <?= json_encode(lang('App.scan_paused')) ?>,
    found: <?= json_encode(lang('App.scan_found')) ?>,
    itemFound: <?= json_encode(lang('App.item_found')) ?>,
    notFound: <?= json_encode(lang('App.not_found')) ?>,
    mutateOk: <?= json_encode(lang('App.scan_mutate_success')) ?>,
    insufficient: <?= json_encode(lang('App.insufficient_stock')) ?>,
    qtyZero: <?= json_encode(lang('App.qty_greater_than_zero')) ?>,
    lowStock: <?= json_encode(lang('App.low_stock')) ?>,
    expired: <?= json_encode(lang('App.expired_badge')) ?>,
    almostExpired: <?= json_encode(lang('App.almost_expired_badge')) ?>,
    stockLabel: <?= json_encode(lang('App.current_stock_label')) ?>,
    stockIn: <?= json_encode(lang('App.stock_in')) ?>,
    stockOut: <?= json_encode(lang('App.stock_out')) ?>,
    confirmMutateTitle: <?= json_encode(lang('App.confirm_mutate_title')) ?>,
    confirmMutateHtml: <?= json_encode(lang('App.confirm_mutate_html')) ?>,
    yesApply: <?= json_encode(lang('App.yes_apply')) ?>,
    cancel: <?= json_encode(lang('App.cancel')) ?>,
    success: <?= json_encode(lang('App.success')) ?>,
    failed: <?= json_encode(lang('App.failed')) ?>,
  };

  const SWAL_SCAN = {
    target: document.body,
    customClass: { container: 'swal-scan-container', popup: 'swal-rounded' },
    buttonsStyling: true,
    confirmButtonColor: '#6366f1',
    cancelButtonColor: '#94a3b8',
  };

  function swalScan(opts) {
    return Swal.fire({ ...SWAL_SCAN, ...opts });
  }

  function showAppLoader() {
    document.getElementById('page-loader')?.classList.add('active');
    document.querySelector('.app-shell')?.classList.add('page-fade-out');
  }

  function hideAppLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) setTimeout(() => loader.classList.remove('active'), 120);
    document.querySelector('.app-shell')?.classList.remove('page-fade-out');
  }

  const URL_PROCESS = app.dataset.urlProcess;
  const URL_MUTATE = app.dataset.urlMutate;
  const URL_BINCARD = app.dataset.urlBincard;
  const HISTORY_KEY = 'appsbeem_scan_history';

  let html5QrcodeScanner = null;
  let isScanning = false;
  let isPaused = false;
  let isFlashOn = false;
  let facingMode = 'environment';
  let currentItem = null;
  let mutateType = 'in';
  let lastCode = '';
  let lastCodeAt = 0;
  let processing = false;
  let activeVideoTrack = null; // referensi track untuk flash

  const el = (id) => document.getElementById(id);

  /** Audio context untuk scan sound */
  let scanAudioCtx = null;

  /** Prime (unlock) AudioContext saat user pertama interaksi — persis seperti jimpitan */
  function primeAudio() {
    if (!scanAudioCtx) scanAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    if (scanAudioCtx.state === 'suspended') scanAudioCtx.resume();
    // Play silent pulse untuk unlock
    const osc = scanAudioCtx.createOscillator();
    const g = scanAudioCtx.createGain();
    g.gain.setValueAtTime(0.001, scanAudioCtx.currentTime);
    osc.connect(g); g.connect(scanAudioCtx.destination);
    osc.start(); osc.stop(scanAudioCtx.currentTime + 0.1);
    document.removeEventListener('click', primeAudio);
    document.removeEventListener('touchstart', primeAudio);
  }
  document.addEventListener('click', primeAudio);
  document.addEventListener('touchstart', primeAudio);

  /**
   * Suara "Triangle" — 2 osilator sine, identik dengan implementasi jimpitan.
   * Nada tinggi metalik yang beresonansi panjang.
   */
  function playScanSuccessSound() {
    try {
      if (!scanAudioCtx) scanAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
      
      // Jika suspended, resume() dulu lalu panggil ulang setelah aktif
      if (scanAudioCtx.state === 'suspended') {
        scanAudioCtx.resume().then(() => playScanSuccessSound());
        return;
      }

      const now = scanAudioCtx.currentTime;

      // Nada utama — tinggi & tajam
      const osc1 = scanAudioCtx.createOscillator();
      const gain1 = scanAudioCtx.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(3500, now);
      gain1.gain.setValueAtTime(0.5, now);
      gain1.gain.exponentialRampToValueAtTime(0.001, now + 1.5);

      // Nada overtone metalik — karakteristik triangle
      const osc2 = scanAudioCtx.createOscillator();
      const gain2 = scanAudioCtx.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(5200, now);
      gain2.gain.setValueAtTime(0.3, now);
      gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.0);

      osc1.connect(gain1); gain1.connect(scanAudioCtx.destination);
      osc2.connect(gain2); gain2.connect(scanAudioCtx.destination);

      osc1.start(now); osc1.stop(now + 1.5);
      osc2.start(now); osc2.stop(now + 1.0);
    } catch (e) { console.warn('Sound error:', e); }
  }

  function safeVibrate(pattern) {
    if (!navigator.vibrate) return;
    try { navigator.vibrate(pattern); } catch (e) {}
  }

  const config = { fps: 15, aspectRatio: 1.333 };

  function setStatus(mode, title, sub) {
    const bar = el('scan-status');
    bar?.classList.remove('is-found', 'is-error', 'is-paused');
    if (mode) bar?.classList.add(mode);
    if (el('scan-status-title')) el('scan-status-title').textContent = title;
    if (el('scan-status-sub')) el('scan-status-sub').textContent = sub;
  }

  function updatePauseBtn() {
    const btn = el('btn-pause-scan');
    if (btn) btn.textContent = isPaused ? LANG.scanning : LANG.paused;
  }

  async function stopCamera() {
    if (html5QrcodeScanner && isScanning) {
      try { await html5QrcodeScanner.stop(); } catch (e) {}
      isScanning = false;
    }
  }

  function showCameraError(err) {
    const readerEl = el('reader');
    if (!readerEl) return;
    let msg = 'Kamera tidak dapat diakses.';
    const n = err?.name || '';
    if (n === 'NotReadableError') msg = 'Kamera sedang digunakan aplikasi lain.';
    else if (n === 'NotAllowedError' || n === 'PermissionDeniedError') msg = 'Izin kamera ditolak. Aktifkan di pengaturan browser.';
    else if (n === 'NotFoundError') msg = 'Kamera tidak terdeteksi pada perangkat ini.';

    readerEl.innerHTML = `<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;text-align:center;background:#0f172a;color:#fff">
      <div style="width:48px;height:48px;border-radius:16px;background:rgba(239,68,68,.15);display:flex;align-items:center;justify-content:center;margin-bottom:12px;color:#f87171">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
      </div>
      <p style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:#fca5a5;margin-bottom:6px"><?= lang('App.camera_access_failed') ?></p>
      <p style="font-size:11px;color:#94a3b8;max-width:240px;line-height:1.5;margin-bottom:16px">${msg}</p>
      <button type="button" onclick="window.__scanStartCam && window.__scanStartCam()" style="padding:10px 18px;background:#6366f1;color:#fff;font-size:11px;font-weight:800;border-radius:12px;border:none"> <?= lang('App.connect_camera') ?></button>
    </div>`;
    setStatus('is-error', LANG.notFound, msg);
  }

  async function startCamera(facing) {
    facingMode = facing || facingMode;
    activeVideoTrack = null;
    isFlashOn = false;

    // Sembunyikan tombol flash saat memulai ulang, reset ke state OFF
    const flashBtn = el('btn-flash');
    if (flashBtn) {
      flashBtn.hidden = true;
      flashBtn.style.display = 'none';
      flashBtn.classList.remove('flash-on');
      flashBtn.classList.add('flash-off');
    }

    try {
      const readerEl = el('reader');
      if (!readerEl) return;

      // Bersihkan state sebelumnya
      if (html5QrcodeScanner && isScanning) {
        try { await html5QrcodeScanner.stop(); } catch (e) {}
        isScanning = false;
      }
      if (html5QrcodeScanner) {
        try { await html5QrcodeScanner.clear(); } catch (e) {}
        html5QrcodeScanner = null;
      }
      readerEl.innerHTML = '';

      html5QrcodeScanner = new Html5Qrcode('reader', { verbose: false });

      // Strategi 1: Coba kamera dengan device ID spesifik (back/belakang)
      // — lebih responsif dan mendukung flash lebih baik di mobile
      let started = false;
      if (facingMode === 'environment') {
        try {
          const devices = await Html5Qrcode.getCameras();
          let backId = null;
          if (devices && devices.length > 0) {
            for (const device of devices) {
              const label = (device.label || '').toLowerCase();
              if (label.includes('back') || label.includes('belakang') || label.includes('rear')) {
                backId = device.id;
              }
            }
            // Fallback: ambil kamera terakhir jika tidak ada label back
            if (!backId && devices.length > 1) backId = devices[devices.length - 1].id;
          }
          if (backId) {
            console.log('[Scanner] Strategi 1: Camera ID spesifik', backId);
            await html5QrcodeScanner.start(backId, config, onScanSuccess, () => {});
            started = true;
          }
        } catch (e1) {
          console.warn('[Scanner] Strategi 1 gagal, coba facingMode:', e1);
        }
      }

      // Strategi 2: Generic facingMode (fallback)
      if (!started) {
        console.log('[Scanner] Strategi 2: facingMode =', facingMode);
        await html5QrcodeScanner.start({ facingMode }, config, onScanSuccess, () => {});
      }

      isScanning = true;
      isPaused = false;
      updatePauseBtn();
      setStatus('', LANG.scanning, LANG.instruction);
      el('scan-overlay')?.classList.remove('hidden');

      // Tampilkan tombol flash segera setelah scanner aktif
      // dan coba deteksi/simpan video track untuk flash
      setTimeout(() => {
        try {
          const video = document.querySelector('#reader video');
          const track = video?.srcObject?.getVideoTracks()[0];
          if (track) {
            activeVideoTrack = track;
            // Tampilkan flash button — user bisa mencoba; jika gagal akan ada notif
            if (flashBtn) {
              flashBtn.hidden = false;
              flashBtn.removeAttribute('hidden');
              flashBtn.style.display = 'flex';
            }
          }
        } catch (e) { console.warn('[Scanner] Flash init:', e); }
      }, 600);

    } catch (err) {
      console.warn('[Scanner] Camera error', err);
      isScanning = false;
      html5QrcodeScanner = null;
      if (facingMode === 'environment') {
        facingMode = 'user';
        await startCamera('user');
      } else {
        showCameraError(err);
      }
    }
  }
  window.__scanStartCam = () => startCamera('environment');

  async function toggleFlash() {
    if (!isScanning) return;
    const targetState = !isFlashOn;
    let success = false;

    // Metode 1: Gunakan helper library Html5Qrcode
    if (html5QrcodeScanner) {
      try {
        await html5QrcodeScanner.applyVideoConstraints({ advanced: [{ torch: targetState }] });
        success = true;
        console.log('[Flash] Metode 1 (library) berhasil');
      } catch (e) {
        console.log('[Flash] Metode 1 gagal:', e);
      }
    }

    // Metode 2: Native track torch (lebih kuat)
    if (!success) {
      try {
        if (!activeVideoTrack || activeVideoTrack.readyState !== 'live') {
          const video = document.querySelector('#reader video');
          activeVideoTrack = video?.srcObject?.getVideoTracks()[0] || null;
        }
        if (activeVideoTrack) {
          await activeVideoTrack.applyConstraints({ advanced: [{ torch: targetState }] });
          success = true;
          console.log('[Flash] Metode 2 (native torch) berhasil');
        }
      } catch (e) {
        console.log('[Flash] Metode 2 gagal:', e);
      }
    }

    // Metode 3: fillLightMode (fallback beberapa perangkat)
    if (!success) {
      try {
        const video = document.querySelector('#reader video');
        const track = video?.srcObject?.getVideoTracks()[0];
        if (track) {
          await track.applyConstraints({ advanced: [{ fillLightMode: targetState ? 'flash' : 'off' }] });
          success = true;
          console.log('[Flash] Metode 3 (fillLightMode) berhasil');
        }
      } catch (e) {
        console.log('[Flash] Metode 3 gagal:', e);
      }
    }

    if (success) {
      isFlashOn = targetState;
      const flashBtn = el('btn-flash');
      if (flashBtn) {
        flashBtn.classList.toggle('flash-on', isFlashOn);
        flashBtn.classList.toggle('flash-off', !isFlashOn);
      }
    } else {
      // Semua metode gagal — beri tahu user
      const flashBtn = el('btn-flash');
      if (flashBtn) { flashBtn.hidden = true; flashBtn.style.display = 'none'; }
      Swal.fire({
        toast: true,
        position: 'top',
        icon: 'error',
        title: 'Flash tidak didukung perangkat ini.',
        showConfirmButton: false,
        timer: 2000,
      });
      console.warn('[Flash] Semua metode gagal.');
    }
  }

  async function pauseScanner() {
    if (!html5QrcodeScanner || !isScanning) return;
    try {
      if (isPaused) {
        await html5QrcodeScanner.resume();
        isPaused = false;
        setStatus('', LANG.scanning, LANG.instruction);
      } else {
        await html5QrcodeScanner.pause(true);
        isPaused = true;
        setStatus('is-paused', LANG.paused, LANG.instruction);
      }
      updatePauseBtn();
    } catch (e) { console.warn(e); }
  }

  function openResultModal() {
    const modal = el('scan-result-modal');
    if (modal) modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeResultModal() {
    const modal = el('scan-result-modal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
  }

  function renderBadges(item) {
    const row = el('res-badges');
    if (!row) return;
    row.innerHTML = '';
    const badges = [];
    if (item.is_expired) badges.push({ cls: 'danger', text: LANG.expired });
    else if (item.is_near_expiry) badges.push({ cls: 'warn', text: LANG.almostExpired });
    if (item.is_low_stock) badges.push({ cls: 'warn', text: LANG.lowStock });
    if (!badges.length) badges.push({ cls: 'ok', text: 'OK' });
    badges.forEach(b => {
      const s = document.createElement('span');
      s.className = 'scan-badge ' + b.cls;
      s.textContent = b.text;
      row.appendChild(s);
    });
    row.classList.toggle('hidden', false);
  }

  function showItem(item) {
    currentItem = item;
    mutateType = 'in';
    document.querySelectorAll('.scan-type-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.type === 'in');
    });
    if (el('mutate-qty')) el('mutate-qty').value = '1';
    if (el('mutate-notes')) el('mutate-notes').value = '';

    el('res-name').textContent = item.name || '—';
    el('res-code').textContent = item.code || '—';
    el('res-warehouse').textContent = item.warehouse_name || '—';
    el('res-stock').textContent = item.current_stock ?? '0';
    el('res-min').textContent = item.min_stock ?? '0';
    el('res-unit').textContent = item.unit || 'pcs';

    renderBadges(item);
    setStatus('is-found', LANG.found, item.code || '');
    openResultModal();

    if (html5QrcodeScanner && isScanning && !isPaused) {
      try { html5QrcodeScanner.pause(true); isPaused = true; updatePauseBtn(); } catch (e) {}
    }
  }

  function showNotFound(message, scannedCode) {
    currentItem = null;
    closeResultModal();
    setStatus('is-error', LANG.notFound, message);
    swalScan({
      icon: 'warning',
      title: LANG.notFound,
      text: message + (scannedCode ? '\n\nApakah Anda ingin menambahkan barang baru dengan kode ini?' : ''),
      showCancelButton: !!scannedCode,
      confirmButtonText: scannedCode ? 'Ya, Tambah Barang' : 'OK',
      cancelButtonText: 'Batal'
    }).then((res) => {
      if (res.isConfirmed && scannedCode) {
        window.location.href = '<?= base_url('inventory/items') ?>?add_code=' + encodeURIComponent(scannedCode);
      } else {
        resumeScanning();
      }
    });
  }

  function pushHistory(entry) {
    let list = [];
    try { list = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]'); } catch (e) {}
    list = list.filter(h => h.code !== entry.code);
    list.unshift(entry);
    list = list.slice(0, 12);
    localStorage.setItem(HISTORY_KEY, JSON.stringify(list));
    renderHistory();
  }

  function renderHistory() {
    const ul = el('scan-history');
    const empty = el('history-empty');
    if (!ul) return;
    let list = [];
    try { list = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]'); } catch (e) {}

    ul.querySelectorAll('.scan-history-item').forEach(n => n.remove());
    if (!list.length) {
      empty?.classList.remove('hidden');
      return;
    }
    empty?.classList.add('hidden');

    list.forEach(h => {
      const li = document.createElement('li');
      li.className = 'scan-history-item';
      li.innerHTML = `<div><div class="name">${escapeHtml(h.name || h.code)}</div><div class="meta">${escapeHtml(h.code)} · ${escapeHtml(h.time)}</div></div>
        <span class="text-[10px] font-bold ${h.ok ? 'text-emerald-500' : 'text-rose-500'}">${h.ok ? '✓' : '✗'}</span>`;
      li.addEventListener('click', () => processCode(h.code, true));
      ul.appendChild(li);
    });
  }

  function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  async function processCode(code, fromHistory = false) {
    const trimmed = (code || '').trim();
    if (!trimmed || processing) return;

    const now = Date.now();
    if (!fromHistory && trimmed === lastCode && now - lastCodeAt < 2500) return;
    lastCode = trimmed;
    lastCodeAt = now;
    processing = true;

    setStatus('', LANG.scanning, trimmed);

    try {
      const body = new FormData();
      body.append('code', trimmed);
      const res = await fetch(URL_PROCESS, { method: 'POST', body });
      const data = await res.json();

      if (data.status === 'success' && data.item) {
        showItem(data.item);
        pushHistory({
          code: trimmed,
          name: data.item.name,
          time: new Date().toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }),
          ok: true,
        });
        safeVibrate(80);
        playScanSuccessSound();
      } else {
        showNotFound(data.message || LANG.notFound, trimmed);
        pushHistory({
          code: trimmed,
          name: LANG.notFound,
          time: new Date().toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }),
          ok: false,
        });
      }
    } catch (e) {
      console.error(e);
      showNotFound('Gagal memproses kode. Periksa koneksi.');
    } finally {
      processing = false;
    }
  }

  function onScanSuccess(decodedText) {
    if (isPaused || processing) return;
    processCode(decodedText);
  }

  async function resumeScanning() {
    closeResultModal();
    currentItem = null;
    setStatus('', LANG.scanning, LANG.instruction);
    if (html5QrcodeScanner && isScanning) {
      try {
        await html5QrcodeScanner.resume();
        isPaused = false;
        updatePauseBtn();
      } catch (e) {
        await startCamera(facingMode);
      }
    } else {
      await startCamera(facingMode);
    }
  }

  async function applyMutation() {
    if (!currentItem) return;
    const qty = parseInt(el('mutate-qty')?.value, 10) || 0;
    const notes = el('mutate-notes')?.value?.trim() || '';

    if (qty < 1) {
      swalScan({ icon: 'warning', title: LANG.qtyZero, toast: true, position: 'top', timer: 2000, showConfirmButton: false });
      return;
    }

    const typeLabel = mutateType === 'in' ? LANG.stockIn : LANG.stockOut;
    const typeColor = mutateType === 'in' ? '#059669' : '#dc2626';
    const html = LANG.confirmMutateHtml
      .replace('{name}', escapeHtml(currentItem.name || '—'))
      .replace('{code}', escapeHtml(currentItem.code || '—'))
      .replace('{type}', `<span style="color:${typeColor};font-weight:800">${typeLabel}</span>`)
      .replace('{qty}', String(qty))
      .replace('{unit}', escapeHtml(currentItem.unit || 'pcs'))
      .replace('{stock}', String(currentItem.current_stock ?? 0));

    const confirm = await swalScan({
      icon: 'question',
      title: LANG.confirmMutateTitle,
      html,
      showCancelButton: true,
      confirmButtonText: LANG.yesApply,
      cancelButtonText: LANG.cancel,
      reverseButtons: true,
      focusCancel: true,
    });

    if (!confirm.isConfirmed) return;

    const body = new FormData();
    body.append('item_id', currentItem.id);
    body.append('type', mutateType);
    body.append('quantity', qty);
    body.append('notes', notes);

    showAppLoader();
    try {
      const res = await fetch(URL_MUTATE, { method: 'POST', body });
      const data = await res.json();
      hideAppLoader();

      if (data.status === 'success') {
        safeVibrate([50, 30, 50]);
        playScanSuccessSound();
        await resumeScanning();
        swalScan({
          icon: 'success',
          title: LANG.success,
          text: LANG.mutateOk + ' ' + LANG.stockLabel + ' ' + data.new_stock,
          toast: true,
          position: 'top',
          timer: 2000,
          showConfirmButton: false,
        });
      } else {
        swalScan({ icon: 'error', title: LANG.failed, text: data.message || LANG.insufficient, confirmButtonText: 'OK' });
      }
    } catch (e) {
      hideAppLoader();
      swalScan({ icon: 'error', title: 'Network error', text: String(e), confirmButtonText: 'OK' });
    }
  }

  /* Events */
  el('btn-pause-scan')?.addEventListener('click', pauseScanner);
  el('btn-flash')?.addEventListener('click', toggleFlash);
  el('btn-toggle-manual')?.addEventListener('click', () => {
    el('manual-panel')?.classList.toggle('hidden');
    el('btn-toggle-manual')?.classList.toggle('active');
  });
  el('btn-manual-submit')?.addEventListener('click', () => {
    const c = el('manual-code')?.value;
    if (c) processCode(c);
  });
  el('manual-code')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); el('btn-manual-submit')?.click(); }
  });

  document.querySelectorAll('.scan-type-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      mutateType = btn.dataset.type;
      document.querySelectorAll('.scan-type-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  el('qty-minus')?.addEventListener('click', () => {
    const inp = el('mutate-qty');
    const v = Math.max(1, (parseInt(inp.value, 10) || 1) - 1);
    inp.value = v;
  });
  el('qty-plus')?.addEventListener('click', () => {
    const inp = el('mutate-qty');
    inp.value = (parseInt(inp.value, 10) || 0) + 1;
  });

  async function openBincard() {
    if (!currentItem) return;
    const modal = el('bincard-modal');
    const tbody = el('bc-rows');
    const empty = el('bc-empty');
    if (!modal || !tbody) return;

    el('bc-title').textContent = currentItem.name + ' — ' + (currentItem.code || '');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:16px;color:var(--text-faint)">...</td></tr>';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    try {
      const res = await fetch(URL_BINCARD + '/' + currentItem.id);
      const data = await res.json();
      tbody.innerHTML = '';
      if (data.status === 'success' && data.history?.length) {
        empty?.classList.add('hidden');
        data.history.forEach(row => {
          const tr = document.createElement('tr');
          tr.style.borderTop = '1px solid var(--border)';
          tr.innerHTML = `<td class="bc-col-date" style="padding:4px 2px">${escapeHtml(row.date)}</td>
            <td class="bc-col-num" style="padding:4px 2px;font-weight:700;color:var(--text-muted)">${row.open ?? 0}</td>
            <td class="bc-col-num" style="padding:4px 2px;color:#059669">${row.qty_in}</td>
            <td class="bc-col-num" style="padding:4px 2px;color:#dc2626">${row.qty_out}</td>
            <td class="bc-col-saldo" style="padding:4px 2px">${row.balance}</td>`;
          tbody.appendChild(tr);
        });
      } else {
        empty?.classList.remove('hidden');
      }
    } catch (e) {
      tbody.innerHTML = '';
      empty?.classList.remove('hidden');
    }
  }

  el('btn-apply-mutate')?.addEventListener('click', applyMutation);
  el('btn-bincard')?.addEventListener('click', openBincard);
  function closeBincardModal() {
    el('bincard-modal').style.display = 'none';
    if (!el('scan-result-modal') || el('scan-result-modal').style.display === 'none') {
      document.body.style.overflow = '';
    }
  }

  el('bc-close')?.addEventListener('click', closeBincardModal);
  el('bincard-modal')?.addEventListener('click', (e) => {
    if (e.target === el('bincard-modal')) closeBincardModal();
  });
  el('btn-scan-again')?.addEventListener('click', resumeScanning);
  el('btn-close-result')?.addEventListener('click', resumeScanning);
  el('scan-result-modal')?.addEventListener('click', (e) => {
    if (e.target === el('scan-result-modal')) resumeScanning();
  });

  window.addEventListener('pagehide', stopCamera);
  window.addEventListener('beforeunload', stopCamera);

  // Inisialisasi kamera: tunggu library Html5Qrcode tersedia dulu
  // (PJAX bisa mengeksekusi script sebelum CDN selesai dimuat)
  (function initWhenReady() {
    if (typeof Html5Qrcode === 'undefined') {
      // Library belum ada — coba lagi setiap 100ms, timeout 8 detik
      let tries = 0;
      const check = setInterval(() => {
        tries++;
        if (typeof Html5Qrcode !== 'undefined') {
          clearInterval(check);
          runInit();
        } else if (tries > 80) {
          clearInterval(check);
          const readerEl = el('reader');
          if (readerEl) readerEl.innerHTML = '<div style="padding:20px;text-align:center;color:#f87171;font-size:12px;font-weight:700">Gagal memuat library scanner.<br><button onclick="location.reload()" style="margin-top:10px;padding:8px 16px;background:#6366f1;color:#fff;border:none;border-radius:10px;font-weight:700;cursor:pointer">Refresh Halaman</button></div>';
        }
      }, 100);
    } else {
      runInit();
    }
  })();

  async function runInit() {
    renderHistory();

    // Cek konteks aman (HTTPS/localhost) — kamera memerlukan secure context
    if (!window.isSecureContext) {
      Swal.fire({
        title: 'Akses Kamera Dibatasi',
        html: '<p class="text-sm">Browser memblokir akses kamera karena koneksi tidak aman (HTTP).</p><p class="text-sm font-bold mt-2">Gunakan HTTPS atau localhost.</p>',
        icon: 'warning',
        confirmButtonText: 'Saya Paham',
        confirmButtonColor: '#6366f1',
      });
      return;
    }

    await startCamera('environment');
  }

  // Restart kamera jika halaman kembali terlihat (misal: pindah tab lalu balik)
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && !isScanning && el('scan-app')) {
      startCamera(facingMode);
    }
  });
})();
</script>
<?= $this->endSection() ?>
