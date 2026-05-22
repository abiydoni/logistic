<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
  $userName = trim($user['full_name'] ?? session()->get('name') ?? 'User');
  $userInitials = strtoupper(substr($userName, 0, 2) ?: 'U');
  $currentLang = normalize_locale($user['language'] ?? session()->get('lang'));
  $currentTheme = normalize_theme($user['theme'] ?? session()->get('theme'));
  $isAdmin = \App\Models\UserModel::isAdminRole(session()->get('role'));
  $roleLabel = $isAdmin ? lang('App.admin') : 'Staff';
?>

<div class="app-page profile-page">

  <header class="profile-hero">
    <div class="profile-hero-inner">
      <div class="profile-hero-avatar" aria-hidden="true"><?= esc($userInitials) ?></div>
      <div class="profile-hero-body">
        <p class="profile-hero-kicker"><?= lang('App.profile') ?></p>
        <h1 class="profile-hero-name"><?= esc($userName) ?></h1>
        <div class="profile-hero-meta">
          <span class="profile-hero-user">@<?= esc($user['username'] ?? '') ?></span>
          <span class="profile-role-badge<?= $isAdmin ? ' is-admin' : '' ?>"><?= esc($roleLabel) ?></span>
        </div>
      </div>
    </div>
  </header>

  <form id="profile-form" action="<?= base_url('profile/update') ?>" method="post" class="profile-form">

    <section class="profile-card">
      <div class="profile-card-head">
        <div class="profile-card-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div>
          <div class="profile-card-title"><?= lang('App.account_section') ?></div>
          <div class="profile-card-desc"><?= lang('App.logged_in_as') ?> @<?= esc($user['username'] ?? '') ?></div>
        </div>
      </div>
      <div class="profile-fields">
        <div class="profile-field">
          <label for="full_name"><?= lang('App.full_name') ?></label>
          <input type="text" name="full_name" id="full_name" required maxlength="120" value="<?= esc($user['full_name'] ?? '') ?>" autocomplete="name">
        </div>
        <div class="profile-field">
          <label for="username_display"><?= lang('App.username') ?></label>
          <input type="text" id="username_display" readonly value="<?= esc($user['username'] ?? '') ?>" autocomplete="username">
          <p class="profile-field-hint"><?= lang('App.readonly_hint') ?></p>
        </div>
      </div>
    </section>

    <section class="profile-card">
      <div class="profile-card-head">
        <div class="profile-card-icon is-security">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
          <div class="profile-card-title"><?= lang('App.change_password') ?></div>
          <div class="profile-card-desc"><?= lang('App.change_password_hint') ?></div>
        </div>
      </div>
      <div class="profile-fields">
        <div class="profile-field">
          <label for="current_password"><?= lang('App.current_password') ?></label>
          <input type="password" name="current_password" id="current_password" autocomplete="current-password" placeholder="••••••••">
        </div>
        <div class="profile-field">
          <label for="new_password"><?= lang('App.new_password') ?></label>
          <input type="password" name="new_password" id="new_password" autocomplete="new-password" placeholder="••••••••">
        </div>
        <div class="profile-field">
          <label for="confirm_password"><?= lang('App.confirm_password') ?></label>
          <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" placeholder="••••••••">
        </div>
      </div>
    </section>

    <section class="profile-card">
      <div class="profile-card-head">
        <div class="profile-card-icon is-prefs">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        </div>
        <div>
          <div class="profile-card-title"><?= lang('App.settings') ?></div>
          <div class="profile-card-desc"><?= lang('App.language') ?> & <?= lang('App.theme') ?></div>
        </div>
      </div>
      <div class="profile-fields">
        <div>
          <p class="profile-prefs-label"><?= lang('App.language') ?></p>
          <div class="grid grid-cols-2 gap-2">
            <label class="choice-pill choice-pill--lang<?= $currentLang === 'id' ? ' is-active' : '' ?>" data-choice-group="language" data-choice-value="id">
              <input type="radio" name="language" value="id" <?= $currentLang === 'id' ? 'checked' : '' ?> class="absolute opacity-0 w-0 h-0">
              <span aria-hidden="true">🇮🇩</span>
              <span><?= lang('App.indonesian') ?></span>
            </label>
            <label class="choice-pill choice-pill--lang<?= $currentLang === 'en' ? ' is-active' : '' ?>" data-choice-group="language" data-choice-value="en">
              <input type="radio" name="language" value="en" <?= $currentLang === 'en' ? 'checked' : '' ?> class="absolute opacity-0 w-0 h-0">
              <span aria-hidden="true">🇬🇧</span>
              <span><?= lang('App.english') ?></span>
            </label>
          </div>
        </div>
        <div>
          <p class="profile-prefs-label"><?= lang('App.theme') ?></p>
          <div class="grid grid-cols-2 gap-2">
            <label class="choice-pill choice-pill--theme-light<?= $currentTheme === 'light' ? ' is-active' : '' ?>" data-choice-group="theme" data-choice-value="light">
              <input type="radio" name="theme" value="light" <?= $currentTheme === 'light' ? 'checked' : '' ?> class="absolute opacity-0 w-0 h-0">
              <span aria-hidden="true">☀️</span>
              <span><?= lang('App.light_mode') ?></span>
            </label>
            <label class="choice-pill choice-pill--theme-dark<?= $currentTheme === 'dark' ? ' is-active' : '' ?>" data-choice-group="theme" data-choice-value="dark">
              <input type="radio" name="theme" value="dark" <?= $currentTheme === 'dark' ? 'checked' : '' ?> class="absolute opacity-0 w-0 h-0">
              <span aria-hidden="true">🌙</span>
              <span><?= lang('App.dark_mode') ?></span>
            </label>
          </div>
        </div>
      </div>
    </section>

    <button type="submit" class="btn-primary profile-save-btn">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
      <?= lang('App.save') ?>
    </button>
  </form>

  <div class="profile-logout-card">
    <div class="profile-logout-icon">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
    </div>
    <div class="profile-logout-text flex-1 min-w-0">
      <h5><?= lang('App.sign_out') ?></h5>
      <p><?= lang('App.sign_out_desc') ?></p>
    </div>
    <a href="<?= base_url('auth/logout') ?>" class="profile-logout-btn"><?= lang('App.sign_out') ?></a>
  </div>

</div>

<script>
  window.setLocalTheme = function(theme) {
    if (typeof window.applyAppTheme === 'function') {
      window.applyAppTheme(theme, false);
      return;
    }
    var t = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.classList.remove('light', 'dark');
    document.documentElement.classList.add(t);
    try { localStorage.setItem('theme', t); } catch (e) {}
  };

  window.updateChoicePills = function(group, value) {
    document.querySelectorAll('#profile-form input[name="' + group + '"]').forEach(function(r) {
      r.checked = (r.value === value);
    });
    document.querySelectorAll('#profile-form .choice-pill[data-choice-group="' + group + '"]').forEach(function(label) {
      label.classList.toggle('is-active', label.dataset.choiceValue === value);
    });
    if (group === 'theme') window.setLocalTheme(value);
  };

  function bindProfilePills() {
    document.querySelectorAll('#profile-form .choice-pill').forEach(function(label) {
      label.onclick = function() {
        var group = label.dataset.choiceGroup;
        var value = label.dataset.choiceValue;
        if (group) window.updateChoicePills(group, value);
      };
      var radio = label.querySelector('input[type="radio"]');
      if (radio) {
        radio.onchange = function() {
          window.updateChoicePills(label.dataset.choiceGroup, radio.value);
        };
      }
    });
    var langChecked = document.querySelector('#profile-form input[name="language"]:checked');
    if (langChecked) window.updateChoicePills('language', langChecked.value);
    var themeChecked = document.querySelector('#profile-form input[name="theme"]:checked');
    if (themeChecked) window.updateChoicePills('theme', themeChecked.value);
  }

  bindProfilePills();
  document.addEventListener('app:page-loaded', bindProfilePills);

  document.getElementById('profile-form').addEventListener('submit', async function(e) {
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
          text: result.message || '<?= lang('App.profile_updated_success') ?>',
          timer: 1500,
          showConfirmButton: false,
          background: isDark ? '#0f172a' : '#ffffff',
          color: isDark ? '#ffffff' : '#111827'
        }).then(function() {
          if (result.theme && typeof window.applyAppTheme === 'function') {
            window.applyAppTheme(result.theme, false);
          }
          if (result.lang && typeof window.applyAppLocale === 'function') {
            window.applyAppLocale(result.lang, false);
          }
          var url = new URL(location.href);
          url.searchParams.delete('lang');
          location.href = url.pathname + (url.search || '') + url.hash;
        });
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

<?= $this->endSection() ?>
