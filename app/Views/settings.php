<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
  $companyName = trim($app['company_name'] ?? session()->get('company_name') ?? \App\Models\AppSettingsModel::DEFAULT_COMPANY);
  $companyInitials = strtoupper(substr(preg_replace('/\s+/', '', $companyName), 0, 2) ?: 'AB');
?>

<div class="app-page settings-page">

  <header class="settings-hero">
    <div class="settings-hero-inner">
      <div class="settings-hero-avatar" aria-hidden="true"><?= esc($companyInitials) ?></div>
      <div class="settings-hero-body">
        <p class="settings-hero-kicker"><?= lang('App.company_profile') ?></p>
        <h1 class="settings-hero-name"><?= esc($companyName) ?></h1>
        <p class="settings-hero-desc"><?= lang('App.company_profile_desc') ?></p>
      </div>
    </div>
  </header>

  <section class="profile-card">
    <div class="profile-card-head">
      <div class="profile-card-icon">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <div>
        <div class="profile-card-title"><?= lang('App.menu_setting') ?></div>
        <div class="profile-card-desc"><?= lang('App.company_name') ?></div>
      </div>
    </div>

    <?php if (empty($is_admin)): ?>
      <div class="settings-notice">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p><?= lang('App.settings_admin_only') ?></p>
      </div>
    <?php endif; ?>

    <form id="settings-form" action="<?= base_url('settings/update') ?>" method="post">
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
          <?php if (! empty($is_admin)): ?>
            <p class="profile-field-hint"><?= lang('App.company_profile_desc') ?></p>
          <?php endif; ?>
        </div>
      </div>

      <?php if (! empty($is_admin)): ?>
        <button type="submit" class="btn-primary profile-save-btn">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          <?= lang('App.save') ?>
        </button>
      <?php endif; ?>
    </form>
  </section>

  <a href="<?= base_url('profile') ?>" class="settings-link-card">
    <div class="settings-link-icon">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    </div>
    <div class="settings-link-text flex-1 min-w-0">
      <strong><?= lang('App.profile') ?></strong>
      <span><?= lang('App.profile_settings_hint') ?></span>
    </div>
    <svg class="settings-link-arrow" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
  </a>

</div>

<?php if (! empty($is_admin)): ?>
<script>
  document.getElementById('settings-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    var formData = new FormData(this);
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
