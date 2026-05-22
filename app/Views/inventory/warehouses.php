<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="app-page warehouses-page">

  <div class="page-toolbar">
    <span class="page-toolbar-title"><?= lang('App.warehouses_list') ?></span>
    <button type="button" onclick="toggleForm()" class="btn-primary">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      <?= lang('App.new_warehouse') ?>
    </button>
  </div>

  <div id="warehouse-form-container" class="app-modal-overlay hidden">
    <div class="app-modal-sheet" onclick="event.stopPropagation()">
      <div class="app-modal-head">
        <h4 id="form-title" class="app-modal-title"><?= lang('App.add_warehouse') ?></h4>
        <button type="button" onclick="toggleForm()" class="app-modal-close" aria-label="Close">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form id="warehouse-form" action="<?= base_url('inventory/warehouses') ?>" method="post" class="app-form-fields">
        <input type="hidden" name="id" id="warehouse-id">
        <div class="profile-field">
          <label for="name"><?= lang('App.warehouse_name') ?></label>
          <input type="text" name="name" id="name" required placeholder="<?= lang('App.example_warehouse') ?>">
        </div>
        <div class="profile-field">
          <label for="description"><?= lang('App.description_label') ?></label>
          <textarea name="description" id="description" rows="3" placeholder="<?= lang('App.description_placeholder') ?>" style="width:100%;padding:11px 13px;font-size:14px;font-weight:500;border:1.5px solid var(--border);border-radius:var(--r-sm);background:var(--surface-2);color:var(--text);resize:vertical;min-height:80px"></textarea>
        </div>
        <label class="filter-check">
          <input type="checkbox" name="requires_expiration" id="requires_expiration" value="1" checked>
          <?= lang('App.requires_expiration_checkbox') ?>
        </label>
        <button type="submit" class="btn-primary profile-save-btn"><?= lang('App.save') ?></button>
      </form>
    </div>
  </div>

  <div class="entity-list">
    <?php if (empty($warehouses)): ?>
      <div class="empty-state">
        <div class="empty-state__icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
        </div>
        <p class="empty-state__text"><?= lang('App.empty_warehouse') ?></p>
      </div>
    <?php else: ?>
      <?php foreach ($warehouses as $wh): ?>
        <article class="entity-card">
          <div class="entity-card__head">
            <div class="min-w-0 flex-1">
              <h4 class="entity-card__title"><?= esc($wh['name']) ?></h4>
              <p class="entity-card__desc"><?= esc($wh['description'] ?: lang('App.no_description')) ?></p>
              <div class="entity-card__meta">
                <?php if (isset($wh['requires_expiration']) && $wh['requires_expiration'] == 1): ?>
                  <span class="badge badge-indigo"><?= lang('App.requires_expiration_badge') ?></span>
                <?php else: ?>
                  <span class="badge"><?= lang('App.no_expiration_badge') ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="entity-card__actions">
              <button type="button" onclick="editWarehouse(<?= htmlspecialchars(json_encode($wh)) ?>)" class="icon-btn icon-btn--edit" title="<?= lang('App.edit_user') ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </button>
              <button type="button" onclick="deleteWarehouse(<?= $wh['id'] ?>, '<?= addslashes(esc($wh['name'])) ?>')" class="icon-btn icon-btn--delete" title="<?= lang('App.delete') ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<script>
  function toggleForm() {
    const container = document.getElementById('warehouse-form-container');
    const form = document.getElementById('warehouse-form');
    if (container.classList.contains('hidden')) {
      form.reset();
      document.getElementById('warehouse-id').value = '';
      document.getElementById('requires_expiration').checked = true;
      document.getElementById('form-title').innerText = '<?= lang('App.add_warehouse') ?>';
      container.classList.remove('hidden');
    } else {
      container.classList.add('hidden');
    }
  }

  document.getElementById('warehouse-form-container')?.addEventListener('click', function(e) {
    if (e.target === this) toggleForm();
  });

  function editWarehouse(wh) {
    document.getElementById('warehouse-id').value = wh.id;
    document.getElementById('name').value = wh.name;
    document.getElementById('description').value = wh.description;
    document.getElementById('requires_expiration').checked = wh.requires_expiration == 1;
    document.getElementById('form-title').innerText = '<?= lang('App.edit_warehouse') ?>';
    document.getElementById('warehouse-form-container').classList.remove('hidden');
  }

  window.deleteWarehouse = async function(id, name) {
    const dk = document.documentElement.classList.contains('dark');
    const bg = dk ? '#1e293b' : '#ffffff';
    const fg = dk ? '#f1f5f9' : '#111827';
    const result = await Swal.fire({
      title: 'Hapus Gudang?',
      html: 'Yakin ingin menghapus <strong>' + name + '</strong>?',
      background: bg,
      color: fg,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
    });
    if (!result.isConfirmed) return;
    try {
      const body = new FormData();
      body.append('id', id);
      body.append('_method', 'DELETE');
      const res = await fetch('<?= base_url('inventory/warehouses') ?>', { method: 'POST', body });
      const data = await res.json();
      if (data.status === 'success') {
        Swal.fire({ icon: 'success', title: '<?= lang('App.success') ?>', text: '<?= lang('App.success_save_warehouse') ?>', timer: 1200, showConfirmButton: false, background: bg, color: fg })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: '<?= lang('App.failed') ?>', text: data.message, confirmButtonColor: '#6366f1', background: bg, color: fg });
      }
    } catch (e) { console.error(e); }
  }

  document.getElementById('warehouse-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
      const response = await fetch(this.action, { method: 'POST', body: formData });
      const result = await response.json();
      if (result.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: '<?= lang('App.success') ?>',
          text: '<?= lang('App.success_save_warehouse') ?>',
          timer: 1500,
          showConfirmButton: false,
          background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
          color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827'
        }).then(() => window.location.reload());
      } else {
        Swal.fire({
          icon: 'error',
          title: '<?= lang('App.failed') ?>',
          text: result.message,
          confirmButtonColor: '#6366f1',
          background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
          color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827'
        });
      }
    } catch (err) { console.error(err); }
  });
</script>

<?= $this->endSection() ?>
