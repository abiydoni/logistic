<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="app-page users-page">

  <div class="page-toolbar">
    <span class="page-toolbar-title"><?= lang('App.users_list') ?></span>
    <button type="button" onclick="window.toggleForm()" class="btn-primary">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      <?= lang('App.new_user') ?>
    </button>
  </div>

  <div id="user-form-container" class="app-modal-overlay hidden">
    <div class="app-modal-sheet" onclick="event.stopPropagation()">
      <div class="app-modal-head">
        <h4 id="form-title" class="app-modal-title"><?= lang('App.add_user') ?></h4>
        <button type="button" onclick="window.toggleForm()" class="app-modal-close" aria-label="Close">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form id="user-form" action="<?= base_url('users') ?>" method="post" class="app-form-fields">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="user-id">
        <div class="profile-field">
          <label for="full_name"><?= lang('App.full_name') ?></label>
          <input type="text" name="full_name" id="full_name" required placeholder="Contoh: Budi Santoso">
        </div>
        <div class="profile-field">
          <label for="username"><?= lang('App.username') ?></label>
          <input type="text" name="username" id="username" required placeholder="Contoh: budi123">
        </div>
        <div class="profile-field">
          <label for="password"><?= lang('App.password') ?></label>
          <input type="password" name="password" id="password" placeholder="Min. 5 karakter">
          <p id="password-hint" class="profile-field-hint hidden">Biarkan kosong untuk mempertahankan sandi lama</p>
        </div>
        <div class="profile-field">
          <label><?= lang('App.role') ?></label>
          <div class="grid grid-cols-2 gap-2">
            <label class="role-pill role-pill--admin" id="label-role-admin">
              <input type="radio" name="role" value="admin" class="absolute opacity-0 w-0 h-0">
              <span aria-hidden="true">🛡️</span>
              <span><?= lang('App.admin') ?></span>
            </label>
            <label class="role-pill role-pill--staff is-active" id="label-role-staff">
              <input type="radio" name="role" value="staff" checked class="absolute opacity-0 w-0 h-0">
              <span aria-hidden="true">📦</span>
              <span>Staff</span>
            </label>
          </div>
        </div>
        <div class="profile-field">
          <label><?= lang('App.status') ?></label>
          <div class="grid grid-cols-2 gap-2">
            <label class="choice-pill choice-pill--status-active is-active" id="label-status-active">
              <input type="radio" name="is_active" value="1" checked class="absolute opacity-0 w-0 h-0">
              <span><?= lang('App.status_active') ?></span>
            </label>
            <label class="choice-pill choice-pill--status-inactive" id="label-status-inactive">
              <input type="radio" name="is_active" value="0" class="absolute opacity-0 w-0 h-0">
              <span><?= lang('App.status_inactive') ?></span>
            </label>
          </div>
        </div>
        <button type="submit" class="btn-primary profile-save-btn"><?= lang('App.save') ?></button>
      </form>
    </div>
  </div>

  <div class="users-grid">
    <?php if (empty($users)): ?>
      <div class="empty-state" style="grid-column:1/-1">
        <div class="empty-state__icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
        </div>
        <p class="empty-state__text"><?= lang('App.empty_data') ?></p>
      </div>
    <?php else: ?>
      <?php foreach ($users as $u): ?>
        <?php
          $isAdmin = \App\Models\UserModel::isAdminRole($u['role'] ?? '');
          $isActive = \App\Models\UserModel::isActive($u['is_active'] ?? 1);
        ?>
        <article class="entity-card user-card<?= $isActive ? '' : ' is-inactive' ?>">
          <div class="user-card__row">
            <div class="user-card__avatar <?= $isAdmin ? 'is-admin' : 'is-staff' ?>"><?= strtoupper(substr($u['full_name'] ?? 'U', 0, 2)) ?></div>
            <div class="min-w-0 flex-1">
              <h4 class="entity-card__title truncate"><?= esc($u['full_name']) ?></h4>
              <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:4px">
                <span class="badge <?= $isAdmin ? 'badge-indigo' : '' ?>"><?= $isAdmin ? lang('App.admin') : 'Staff' ?></span>
                <span class="badge <?= $isActive ? 'badge-emerald' : 'badge-rose' ?>"><?= $isActive ? lang('App.status_active') : lang('App.status_inactive') ?></span>
              </div>
              <p class="user-card__meta">@<?= esc($u['username']) ?> · <?= date('d M Y', strtotime($u['created_at'])) ?></p>
            </div>
          </div>
          <div class="entity-card__actions">
            <?php if ($u['id'] != session()->get('user_id')): ?>
            <button type="button" onclick="toggleUserStatus(<?= (int) $u['id'] ?>, <?= $isActive ? 1 : 0 ?>)" class="icon-btn icon-btn--status" title="<?= lang('App.toggle_status') ?>">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </button>
            <?php endif; ?>
            <button type="button" onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)" class="icon-btn icon-btn--edit" title="<?= lang('App.edit_user') ?>">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <?php if ($u['id'] != session()->get('user_id')): ?>
              <button type="button" onclick="deleteUser(<?= $u['id'] ?>, '<?= addslashes(esc($u['full_name'])) ?>')" class="icon-btn icon-btn--delete" title="<?= lang('App.delete') ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<script>
  window.updateStatusSelector = function(isActive) {
    var active = String(isActive) === '1' || isActive === 1 || isActive === true;
    document.querySelectorAll('#user-form input[name="is_active"]').forEach(function(r) {
      r.checked = (active ? r.value === '1' : r.value === '0');
    });
    var onLabel = document.getElementById('label-status-active');
    var offLabel = document.getElementById('label-status-inactive');
    if (onLabel) onLabel.classList.toggle('is-active', active);
    if (offLabel) offLabel.classList.toggle('is-active', !active);
  };

  window.updateRoleSelector = function(role) {
    var normalized = (role === 'admin') ? 'admin' : 'staff';
    document.querySelectorAll('#user-form input[name="role"]').forEach(function(r) {
      r.checked = (r.value === normalized);
    });
    var adminLabel = document.getElementById('label-role-admin');
    var staffLabel = document.getElementById('label-role-staff');
    if (adminLabel) adminLabel.classList.toggle('is-active', normalized === 'admin');
    if (staffLabel) staffLabel.classList.toggle('is-active', normalized === 'staff');
  };

  function bindStatusPills() {
    document.querySelectorAll('#user-form .choice-pill--status-active, #user-form .choice-pill--status-inactive').forEach(function(label) {
      label.onclick = function(e) {
        if (e.target.tagName === 'INPUT') return;
        var radio = label.querySelector('input[type="radio"]');
        if (radio) window.updateStatusSelector(radio.value);
      };
      var radio = label.querySelector('input[type="radio"]');
      if (radio) radio.onchange = function() { window.updateStatusSelector(radio.value); };
    });
    var checked = document.querySelector('#user-form input[name="is_active"]:checked');
    window.updateStatusSelector(checked ? checked.value : '1');
  }

  function bindRolePills() {
    document.querySelectorAll('#user-form .role-pill').forEach(function(label) {
      label.onclick = function(e) {
        if (e.target.tagName === 'INPUT') return;
        var radio = label.querySelector('input[type="radio"]');
        if (radio) window.updateRoleSelector(radio.value);
      };
      var radio = label.querySelector('input[type="radio"]');
      if (radio) {
        radio.onchange = function() { window.updateRoleSelector(radio.value); };
      }
    });
    var checked = document.querySelector('#user-form input[name="role"]:checked');
    window.updateRoleSelector(checked ? checked.value : 'staff');
  }

  bindRolePills();
  bindStatusPills();
  document.addEventListener('app:page-loaded', function() {
    bindRolePills();
    bindStatusPills();
  });

  window.toggleForm = function() {
    const container = document.getElementById('user-form-container');
    const form = document.getElementById('user-form');
    
    if (container) {
      if (container.classList.contains('hidden')) {
        if (form) form.reset();
        const userIdEl = document.getElementById('user-id');
        const hintEl = document.getElementById('password-hint');
        const passEl = document.getElementById('password');
        const titleEl = document.getElementById('form-title');
        const loader = document.getElementById('page-loader');
        
        if (userIdEl) userIdEl.value = '';
        if (hintEl) hintEl.classList.add('hidden');
        if (passEl) {
          passEl.placeholder = 'Min. 5 karakter';
          passEl.required = true;
        }
        if (titleEl) titleEl.innerText = '<?= lang('App.add_user') ?>';
        if (loader) loader.classList.remove('active');
        window.updateRoleSelector('staff');
        window.updateStatusSelector('1');
        container.classList.remove('hidden');
      } else {
        container.classList.add('hidden');
      }
    }
  };

  window.editUser = function(user) {
    const userIdEl = document.getElementById('user-id');
    const nameEl = document.getElementById('full_name');
    const userEl = document.getElementById('username');
    const passEl = document.getElementById('password');
    const hintEl = document.getElementById('password-hint');
    const titleEl = document.getElementById('form-title');

    if (userIdEl) userIdEl.value = user.id;
    if (nameEl) nameEl.value = user.full_name;
    if (userEl) userEl.value = user.username;
    
    if (passEl) {
      passEl.value = '';
      passEl.placeholder = '••••••••';
      passEl.required = false;
    }
    if (hintEl) hintEl.classList.remove('hidden');
    if (titleEl) titleEl.innerText = '<?= lang('App.edit_user') ?>';

    const role = (user.role || 'staff').toLowerCase();
    window.updateRoleSelector(role === 'admin' || role === 'administrator' ? 'admin' : 'staff');
    window.updateStatusSelector(user.is_active !== undefined ? user.is_active : 1);
    
    const container = document.getElementById('user-form-container');
    const loader = document.getElementById('page-loader');
    if (loader) loader.classList.remove('active');
    if (container) container.classList.remove('hidden');
  };

  // Close modal when clicking outside form card
  document.getElementById('user-form-container')?.addEventListener('click', function(e) {
    if (e.target === this) {
      window.toggleForm();
    }
  });


  window.toggleUserStatus = async function(id, currentlyActive) {
    const isDark = document.documentElement.classList.contains('dark');
    const bg = isDark ? '#0f172a' : '#ffffff';
    const fg = isDark ? '#ffffff' : '#111827';
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
      const res = await fetch('<?= base_url('users/toggle-status') ?>', { method: 'POST', body });
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
  };

  // Handle deletion
  window.deleteUser = async function(id, name) {
    const isDark = document.documentElement.classList.contains('dark');
    const bg = isDark ? '#0f172a' : '#ffffff';
    const fg = isDark ? '#ffffff' : '#111827';
    
    const result = await Swal.fire({
      title: `<span class="text-sm font-extrabold text-rose-500"><?= lang('App.delete_user_title') ?></span>`,
      html: `<p class="text-xs text-slate-500 mt-1">Apakah Anda yakin ingin menghapus pengguna <strong>${name}</strong>?<br><span class="text-[10px] text-rose-500 mt-2 block"><?= lang('App.delete_user_desc') ?></span></p>`,
      background: bg,
      color: fg,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
      customClass: { popup: 'swal-rounded' }
    });

    if (!result.isConfirmed) return;

    try {
      const body = new FormData();
      body.append('id', id);
      const res = await fetch('<?= base_url('users/delete') ?>', { method: 'POST', body });
      const data = await res.json();

      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: data.message,
          timer: 1200,
          showConfirmButton: false,
          background: bg,
          color: fg
        }).then(() => {
          if (typeof loadPage === 'function') {
            loadPage(location.href);
          } else {
            location.reload();
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: data.message,
          confirmButtonColor: '#6366f1',
          background: bg,
          color: fg
        });
      }
    } catch (e) {
      console.error(e);
    }
  }

  // Handle Form Submission via AJAX
  document.getElementById('user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isDark = document.documentElement.classList.contains('dark');
    const bg = isDark ? '#0f172a' : '#ffffff';
    const fg = isDark ? '#ffffff' : '#111827';
    
    try {
      const response = await fetch(this.action, {
        method: 'POST',
        body: formData
      });
      const result = await response.json();
      
      if (result.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: '<?= lang('App.success') ?>',
          text: result.message,
          timer: 1500,
          showConfirmButton: false,
          background: bg,
          color: fg
        }).then(() => {
          document.getElementById('user-form-container').classList.add('hidden');
          if (typeof loadPage === 'function') {
            loadPage(location.href);
          } else {
            location.reload();
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: '<?= lang('App.failed') ?>',
          text: result.message,
          confirmButtonColor: '#6366f1',
          background: bg,
          color: fg
        });
      }
    } catch (err) {
      console.error(err);
    }
  });
</script>

<?= $this->endSection() ?>
