<?= $this->extend('layout/main') ?>
<?php $this->setVar('hideBottomNav', true); ?>

<?= $this->section('content') ?>
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
  <div class="scan-viewport-wrap md-card" style="padding:0;border:none;overflow:hidden">
    <div id="reader"></div>
    <div class="scan-frame-overlay" id="scan-overlay">
      <div class="scan-corner tl"></div>
      <div class="scan-corner tr"></div>
      <div class="scan-corner bl"></div>
      <div class="scan-corner br"></div>
      <div class="scan-frame-box"></div>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="scan-toolbar">
    <button type="button" class="scan-tool-btn" id="btn-flash" hidden>
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      <span><?= lang('App.toggle_flash') ?></span>
    </button>
    <button type="button" class="scan-tool-btn" id="btn-switch-cam">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
      <span><?= lang('App.switch_camera') ?></span>
    </button>
    <button type="button" class="scan-tool-btn" id="btn-toggle-manual">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
      <span><?= lang('App.manual_entry') ?></span>
    </button>
  </div>
  <p class="text-[10px] text-center" style="color:var(--text-faint)"><?= lang('App.flash_tip') ?></p>

  <!-- Manual input -->
  <div class="md-card scan-manual-card hidden" id="manual-panel">
    <div class="scan-section-label"><?= lang('App.or_manual_input') ?></div>
    <div class="scan-manual-row">
      <input type="text" id="manual-code" placeholder="<?= lang('App.manual_code_placeholder') ?>" autocomplete="off" inputmode="text">
      <button type="button" class="btn-primary px-4 py-3 text-xs shrink-0" id="btn-manual-submit"><?= lang('App.process') ?></button>
    </div>
  </div>

  <!-- History -->
  <div class="md-card p-4">
    <div class="scan-section-label"><?= lang('App.recent_scans') ?></div>
    <ul class="scan-history-list" id="scan-history">
      <li class="scan-empty-history" id="history-empty"><?= lang('App.empty_data') ?></li>
    </ul>
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
  let userInteracted = false;

  const el = (id) => document.getElementById(id);

  /** Chrome requires a prior user gesture before navigator.vibrate / AudioContext */
  let scanAudioCtx = null;

  function getScanAudioContext() {
    if (scanAudioCtx) return scanAudioCtx;
    const Ctx = window.AudioContext || window.webkitAudioContext;
    if (!Ctx) return null;
    scanAudioCtx = new Ctx();
    return scanAudioCtx;
  }

  function unlockScanAudio() {
    const ctx = getScanAudioContext();
    if (ctx && ctx.state === 'suspended') {
      ctx.resume().catch(function() {});
    }
  }

  /**
   * Suara sukses scan — dibuat dengan Web Audio API (offline, tanpa file MP3/WAV).
   */
  function playScanSuccessSound() {
    if (!userInteracted) return;
    const ctx = getScanAudioContext();
    if (!ctx) return;
    try {
      if (ctx.state === 'suspended') {
        ctx.resume().then(function() { playScanSuccessSound(); }).catch(function() {});
        return;
      }
      const t0 = ctx.currentTime;
      const notes = [
        { freq: 523.25, at: 0, dur: 0.12 },
        { freq: 659.25, at: 0.1, dur: 0.14 },
        { freq: 783.99, at: 0.22, dur: 0.2 },
      ];
      notes.forEach(function(n) {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(n.freq, t0 + n.at);
        const start = t0 + n.at;
        const end = start + n.dur;
        gain.gain.setValueAtTime(0.0001, start);
        gain.gain.exponentialRampToValueAtTime(0.28, start + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, end);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(start);
        osc.stop(end + 0.02);
      });
    } catch (e) { /* autoplay policy or unsupported */ }
  }

  function safeVibrate(pattern) {
    if (!userInteracted || !navigator.vibrate) return;
    try {
      navigator.vibrate(pattern);
    } catch (e) { /* blocked or unsupported */ }
  }

  function markUserInteracted() {
    userInteracted = true;
    unlockScanAudio();
  }

  app.addEventListener('pointerdown', markUserInteracted, { once: true, passive: true });
  app.addEventListener('keydown', markUserInteracted, { once: true });
  app.addEventListener('touchstart', markUserInteracted, { once: true, passive: true });

  function qrbox(w, h) {
    return { width: Math.floor(w * 0.82), height: Math.floor(h * 0.42) };
  }

  const config = { fps: 12, qrbox, aspectRatio: 1.333 };

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
      <p style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:#fca5a5;margin-bottom:6px"><?= esc(lang('App.camera_access_failed')) ?></p>
      <p style="font-size:11px;color:#94a3b8;max-width:240px;line-height:1.5;margin-bottom:16px">${msg}</p>
      <button type="button" onclick="window.__scanStartCam && window.__scanStartCam()" style="padding:10px 18px;background:#6366f1;color:#fff;font-size:11px;font-weight:800;border-radius:12px;border:none"> <?= esc(lang('App.connect_camera')) ?></button>
    </div>`;
    setStatus('is-error', LANG.notFound, msg);
  }

  async function startCamera(facing) {
    facingMode = facing || facingMode;
    try {
      const readerEl = el('reader');
      if (!readerEl) return;
      if (readerEl.querySelector('video') === null && readerEl.innerHTML && !readerEl.querySelector('button')) {
        /* keep error UI */
      } else if (!readerEl.querySelector('video')) {
        readerEl.innerHTML = '';
      }

      el('btn-flash')?.classList.add('hidden');
      isFlashOn = false;

      if (!html5QrcodeScanner) {
        html5QrcodeScanner = new Html5Qrcode('reader', { verbose: false });
      } else if (isScanning) {
        await stopCamera();
        readerEl.innerHTML = '';
      }

      await html5QrcodeScanner.start(
        { facingMode },
        config,
        onScanSuccess,
        () => {}
      );
      isScanning = true;
      isPaused = false;
      updatePauseBtn();
      setStatus('', LANG.scanning, LANG.instruction);
      el('scan-overlay')?.classList.remove('hidden');

      setTimeout(() => {
        try {
          const caps = html5QrcodeScanner.getRunningTrackCapabilities();
          if (caps?.torch) el('btn-flash')?.classList.remove('hidden');
        } catch (e) {}
      }, 600);
    } catch (err) {
      console.warn('Camera error', err);
      if (facingMode === 'environment') {
        await startCamera('user');
      } else {
        showCameraError(err);
      }
    }
  }
  window.__scanStartCam = () => startCamera('environment');

  async function toggleFlash() {
    if (!html5QrcodeScanner || !isScanning) return;
    try {
      const caps = html5QrcodeScanner.getRunningTrackCapabilities();
      if (!caps?.torch) return;
      isFlashOn = !isFlashOn;
      await html5QrcodeScanner.applyVideoConstraints({ advanced: [{ torch: isFlashOn }] });
      el('btn-flash')?.classList.toggle('active', isFlashOn);
    } catch (e) { console.warn(e); }
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

  function showNotFound(message) {
    currentItem = null;
    closeResultModal();
    setStatus('is-error', LANG.notFound, message);
    swalScan({
      icon: 'error',
      title: LANG.notFound,
      text: message,
      confirmButtonText: 'OK',
    }).then(() => resumeScanning());
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
        showNotFound(data.message || LANG.notFound);
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
  el('btn-switch-cam')?.addEventListener('click', async () => {
    facingMode = facingMode === 'environment' ? 'user' : 'environment';
    await startCamera(facingMode);
  });
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

  document.addEventListener('DOMContentLoaded', async () => {
    renderHistory();
    // Explicitly ask for camera permission to trigger prompt early (helps on self‑signed HTTPS)
    try {
        const perm = await navigator.mediaDevices.getUserMedia({ video: true });
        perm.getTracks().forEach(t => t.stop());
    } catch (e) {
        console.warn('Camera permission request failed', e);
    }
    startCamera('environment');
    document.getElementById('page-loader')?.classList.remove('active');
    document.querySelector('.app-shell')?.classList.remove('page-fade-out');
});
})();
</script>
<?= $this->endSection() ?>
