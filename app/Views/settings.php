<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
  $companyName = trim($app['company_name'] ?? session()->get('company_name') ?? \App\Models\AppSettingsModel::DEFAULT_COMPANY);
  $companyInitials = strtoupper(substr(preg_replace('/\s+/', '', $companyName), 0, 2) ?: 'AB');
  $wa = $wa ?? [];
  $waEnabled = in_array(strtolower((string) ($wa['wa_notify_enabled'] ?? 'true')), ['1', 'true', 'yes', 'on'], true);
?>

<div class="app-page settings-page">

  <header class="settings-hero">
    <div class="settings-hero-inner">
      <div class="settings-hero-avatar" aria-hidden="true"><?= esc($companyInitials) ?></div>
      <div class="settings-hero-body">
        <p class="settings-hero-kicker"><?= lang('App.company_profile') ?></p>
        <h1 class="settings-hero-name"><?= esc($companyName) ?></h1>
        <p class="settings-hero-desc"><?= lang('App.settings_page_desc') ?></p>
      </div>
    </div>
  </header>

  <?php if (empty($is_admin)): ?>
    <div class="settings-notice profile-card">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <p><?= lang('App.settings_admin_only') ?></p>
    </div>
  <?php endif; ?>

  <?php if (empty($wa_ready)): ?>
    <div class="settings-notice profile-card settings-notice-warn">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <p><?= lang('App.wa_config_table_missing') ?></p>
    </div>
  <?php endif; ?>

  <form id="settings-form" action="<?= base_url('settings/update') ?>" method="post">

    <section class="profile-card">
      <div class="profile-card-head">
        <div class="profile-card-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
          <div class="profile-card-title"><?= lang('App.company_profile') ?></div>
          <div class="profile-card-desc"><?= lang('App.company_name') ?></div>
        </div>
      </div>

      <div class="profile-fields">
        <div class="profile-field">
          <label for="company_name"><?= lang('App.company_name') ?></label>
          <input
            type="text"
            name="company_name"
            id="company_name"
            <?= ! empty($is_admin) ? 'required maxlength="255"' : 'readonly' ?>
            value="<?= esc($companyName) ?>"
            placeholder="Contoh: PT Logistik Nusantara"
            autocomplete="organization"
          >
          <p class="profile-field-hint"><?= lang('App.company_profile_desc') ?></p>
        </div>
      </div>
    </section>

    <section class="profile-card settings-wa-card">
      <div class="profile-card-head">
        <div class="profile-card-icon settings-wa-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </div>
        <div>
          <div class="profile-card-title"><?= lang('App.wa_notify_section') ?></div>
          <div class="profile-card-desc"><?= lang('App.wa_notify_section_desc') ?></div>
        </div>
      </div>

      <div class="profile-fields">
        <div class="profile-field settings-check-field">
          <label class="settings-check-label">
            <input
              type="checkbox"
              name="wa_notify_enabled"
              value="1"
              <?= $waEnabled ? 'checked' : '' ?>
              <?= empty($is_admin) || empty($wa_ready) ? 'disabled' : '' ?>
            >
            <span><?= lang('App.wa_notify_enabled') ?></span>
          </label>
          <p class="profile-field-hint"><?= lang('App.wa_notify_enabled_hint') ?></p>
        </div>

        <div class="profile-field">
          <label for="wa_notify_days"><?= lang('App.wa_notify_days') ?></label>
          <input
            type="number"
            name="wa_notify_days"
            id="wa_notify_days"
            min="1"
            max="365"
            <?= ! empty($is_admin) && ! empty($wa_ready) ? 'required' : 'readonly' ?>
            value="<?= esc((int) ($wa['wa_notify_days'] ?? 30)) ?>"
          >
          <p class="profile-field-hint"><?= lang('App.wa_notify_days_hint') ?></p>
        </div>

        <div class="profile-field">
          <label for="wa_group_id"><?= lang('App.wa_group_id') ?></label>
          <input
            type="text"
            name="wa_group_id"
            id="wa_group_id"
            <?= ! empty($is_admin) && ! empty($wa_ready) ? '' : 'readonly' ?>
            value="<?= esc($wa['wa_group_id'] ?? '') ?>"
            placeholder="120363398680818900@g.us"
            autocomplete="off"
          >
          <p class="profile-field-hint"><?= lang('App.wa_group_id_hint') ?></p>
        </div>

        <div class="profile-field">
          <label for="api_url_group"><?= lang('App.wa_api_url') ?></label>
          <input
            type="url"
            name="api_url_group"
            id="api_url_group"
            <?= ! empty($is_admin) && ! empty($wa_ready) ? '' : 'readonly' ?>
            value="<?= esc($wa['api_url_group'] ?? '') ?>"
            placeholder="https://telebot.appsbee.my.id"
          >
          <p class="profile-field-hint"><?= lang('App.wa_api_url_hint') ?></p>
        </div>

        <div class="profile-field">
          <label for="report_expired"><?= lang('App.wa_report_file') ?></label>
          <input
            type="text"
            name="report_expired"
            id="report_expired"
            <?= ! empty($is_admin) && ! empty($wa_ready) ? '' : 'readonly' ?>
            value="<?= esc($wa['report_expired'] ?? 'ambil_data_expired.php') ?>"
          >
          <p class="profile-field-hint"><?= lang('App.wa_report_file_hint') ?></p>
        </div>
      </div>
    </section>

    <?php if (! empty($is_admin) && ! empty($wa_ready)): ?>
      <button type="submit" class="btn-primary profile-save-btn settings-save-all">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        <?= lang('App.save') ?>
      </button>
    <?php endif; ?>
  </form>

  <a href="<?= base_url('profile') ?>" class="settings-link-card" style="margin-bottom: 20px;">
    <div class="settings-link-icon">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    </div>
    <div class="settings-link-text flex-1 min-w-0">
      <strong><?= lang('App.profile') ?></strong>
      <span><?= lang('App.profile_settings_hint') ?></span>
    </div>
    <svg class="settings-link-arrow" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
  </a>

  <!-- Clear Cache Button -->
  <button type="button" onclick="clearAppCache()" class="settings-link-card" style="width: 100%; text-align: left; background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.3); border-radius: 12px;">
    <div class="settings-link-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </div>
    <div class="settings-link-text flex-1 min-w-0">
      <strong style="color: #ef4444;">Bersihkan Cache Aplikasi</strong>
      <span>Hapus cache & memori lokal jika terjadi error</span>
    </div>
    <svg class="settings-link-arrow" style="color: #ef4444;" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
  </button>

</div>

<script>
  function clearAppCache() {
    Swal.fire({
      title: 'Bersihkan Cache?',
      text: 'Ini akan menghapus cache lokal dan memori aplikasi. Halaman akan dimuat ulang. Lanjutkan?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Ya, Bersihkan',
      cancelButtonText: 'Batal'
    }).then(async (result) => {
      if (result.isConfirmed) {
        localStorage.clear();
        sessionStorage.clear();
        if ('serviceWorker' in navigator) {
          try {
            const regs = await navigator.serviceWorker.getRegistrations();
            for (let r of regs) await r.unregister();
          } catch(e) {}
        }
        if ('caches' in window) {
          try {
            const keys = await caches.keys();
            for (let k of keys) await caches.delete(k);
          } catch(e) {}
        }
        Swal.fire({
          title: 'Berhasil!',
          text: 'Cache telah dibersihkan.',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          window.location.reload(true);
        });
      }
    });
  }
</script>

<?php if (! empty($is_admin) && ! empty($wa_ready)): ?>
<script>
  document.getElementById('settings-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    if (!formData.has('wa_notify_enabled')) {
      formData.append('wa_notify_enabled', '');
    }
    try {
      var response = await fetch(this.action, { method: 'POST', body: formData });
      var result = await response.json();
      var isDark = document.documentElement.classList.contains('dark');
      if (result.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: '<?= lang('App.success_modal') ?>',
          text: result.message || '<?= lang('App.settings_updated_success') ?>',
          timer: 1500,
          showConfirmButton: false,
          background: isDark ? '#0f172a' : '#ffffff',
          color: isDark ? '#ffffff' : '#111827'
        }).then(function() { window.location.reload(); });
      } else {
        Swal.fire({
          icon: 'error',
          title: '<?= lang('App.failed') ?>',
          text: result.message,
          confirmButtonColor: '#6366f1',
          background: isDark ? '#0f172a' : '#ffffff',
          color: isDark ? '#ffffff' : '#111827'
        });
      }
    } catch (err) { console.error(err); }
  });
</script>
<?php endif; ?>

<?= $this->endSection() ?>
