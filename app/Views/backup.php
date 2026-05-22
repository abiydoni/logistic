<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="app-page backup-page">

  <section class="backup-hero">
    <div class="backup-hero-icon">
      <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
    </div>
    <h2><?= lang('App.backup_db') ?></h2>
    <p><?= lang('App.backup_desc') ?></p>
    <a href="<?= base_url('backup/create') ?>" class="backup-hero-btn">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      <?= lang('App.create_backup') ?>
    </a>
  </section>

  <section>
    <div class="section-heading">
      <div class="section-heading-bar"></div>
      <span class="section-heading-text"><?= lang('App.backup_history') ?></span>
    </div>

    <div class="entity-list">
      <?php if (empty($backups)): ?>
        <div class="empty-state">
          <div class="empty-state__icon">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
          </div>
          <p class="empty-state__text"><?= lang('App.empty_data') ?></p>
        </div>
      <?php else: ?>
        <?php foreach ($backups as $bk): ?>
          <div class="backup-file-card">
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="backup-file-card__icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              </div>
              <div class="min-w-0">
                <div class="backup-file-card__name"><?= esc($bk['file_name']) ?></div>
                <div class="backup-file-card__meta"><?= date('d M Y H:i', strtotime($bk['created_at'])) ?> · <?= esc($bk['file_size']) ?></div>
              </div>
            </div>
            <div class="entity-card__actions">
              <a href="<?= base_url('backup/download/' . $bk['file_name']) ?>" class="icon-btn icon-btn--edit no-pjax" download="<?= esc($bk['file_name']) ?>" title="Download">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
              </a>
              <button type="button" onclick="confirmDelete(<?= json_encode($bk['file_name']) ?>)" class="icon-btn icon-btn--delete" title="<?= lang('App.delete') ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

</div>

<?php if (session()->getFlashdata('success')): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({ icon: 'success', title: '<?= lang('App.success') ?>', text: '<?= session()->getFlashdata('success') ?>', timer: 1800, showConfirmButton: false, background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff', color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827' });
    });
  </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({ icon: 'error', title: '<?= lang('App.failed') ?>', text: '<?= session()->getFlashdata('error') ?>', confirmButtonColor: '#6366f1', background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff', color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827' });
    });
  </script>
<?php endif; ?>

<script>
  function confirmDelete(filename) {
    const isDark = document.documentElement.classList.contains('dark');
    Swal.fire({
      title: '<?= lang('App.delete_backup_title') ?>',
      text: '<?= lang('App.delete_backup_desc') ?>',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: '<?= lang('App.yes_delete') ?>',
      cancelButtonText: '<?= lang('App.cancel') ?>',
      background: isDark ? '#0f172a' : '#ffffff',
      color: isDark ? '#ffffff' : '#111827'
    }).then(function(result) {
      if (result.isConfirmed) {
        window.location.href = '<?= base_url('backup/delete/') ?>' + encodeURIComponent(filename);
      }
    });
  }
</script>

<?= $this->endSection() ?>
