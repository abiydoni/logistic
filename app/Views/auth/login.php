<!DOCTYPE html>
<html lang="<?= esc(app_locale()) ?>" class="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title><?= lang('App.login_title'); ?> - AppsBeem</title>
  
  <!-- Prevent theme flash (login: localStorage only; default light) -->
  <script>
    (function() {
      var t = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
      document.documentElement.classList.remove('light', 'dark');
      document.documentElement.classList.add(t);
      window.APP_THEME = t;
      var serverLocale = <?= json_encode(app_locale()) ?>;
      var loc = serverLocale === 'en' ? 'en' : 'id';
      document.documentElement.lang = loc;
      try { localStorage.setItem('lang', loc); } catch (e) {}
      window.APP_LOCALE = loc;
    })();
  </script>

  <!-- Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  
  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Custom Design System CSS -->
  <link rel="stylesheet" href="<?= base_url('css/app.css'); ?>">
  <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('icons/icon-32.png'); ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('icons/icon-180.png'); ?>">
  <meta name="theme-color" content="#4f46e5">
  <meta name="apple-mobile-web-app-title" content="BeemLog">
</head>
<body class="login-page-body">

  <!-- Page Loader (Premium Glassmorphic Screen) -->
  <div id="page-loader" class="page-loader active">
    <div class="loader-content">
      <div class="loader-visual">
        <div class="loader-orbit"></div>
        <div class="loader-pulse"></div>
        <div class="loader-icon">
          <svg class="w-8 h-8" style="color:var(--primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
        </div>
      </div>
      <div class="loader-text">
        <span>Memuat halaman</span><span class="dot-1">.</span><span class="dot-2">.</span><span class="dot-3">.</span>
      </div>
    </div>
  </div>

  <!-- Header Section with Language and Dark Mode Toggle -->
  <div class="login-top-bar">
    <div class="login-lang-group">
      <a href="<?= lang_switch_url('id') ?>" class="lang-switch-link login-lang-pill<?= app_locale() === 'id' ? ' is-active' : '' ?>">ID</a>
      <a href="<?= lang_switch_url('en') ?>" class="lang-switch-link login-lang-pill<?= app_locale() === 'en' ? ' is-active' : '' ?>">EN</a>
    </div>

    <button type="button" onclick="toggleTheme()" class="login-theme-btn" aria-label="Toggle theme">
      <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
      <svg id="theme-icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
    </button>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
  <div class="login-flash-error" style="width:100%;max-width:400px;margin-bottom:12px;padding:12px 14px;border-radius:12px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.35);color:#b91c1c;font-size:12px;font-weight:600;text-align:center">
    <?= esc(session()->getFlashdata('error')) ?>
  </div>
  <?php endif; ?>

  <div class="login-wrap animate-slide-up">
    <div class="login-card">
      <div class="login-brand-icon">
        <img src="<?= base_url('icons/icon-192.png'); ?>" alt="AppsBeem Logistic" width="56" height="56" style="border-radius:14px">
      </div>
      <h1 class="login-brand-title">AppsBeem Logistic</h1>
      <p class="login-brand-sub"><?= lang('App.login_subtitle'); ?></p>

    <form id="login-form" method="post" action="<?= base_url('auth/login'); ?>">
      <div class="login-field">
        <label for="username"><?= lang('App.username'); ?></label>
        <div class="login-field-inner">
          <span class="field-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </span>
          <input type="text" name="username" id="username" required placeholder="admin" autocomplete="username">
        </div>
      </div>

      <div class="login-field">
        <label for="password"><?= lang('App.password'); ?></label>
        <div class="login-field-inner">
          <span class="field-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </span>
          <input type="password" name="password" id="password" required placeholder="••••••••" autocomplete="current-password" style="padding-right:42px">
          <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-indigo-500 focus:outline-none" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-faint)" aria-label="Toggle password">
            <svg id="eye-icon-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            <svg id="eye-icon-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary profile-save-btn" style="margin-top:8px">
        <?= lang('App.sign_in'); ?>
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </button>
    </form>
    </div>
  </div>

  <div class="login-footer-note">
    &copy; <?= date('Y'); ?> AppsBeem Logistic PWA. All rights reserved.
  </div>

  <script>
    function swalTheme() {
      var d = document.documentElement.classList.contains('dark');
      return {
        background: d ? '#0f172a' : '#ffffff',
        color: d ? '#f1f5f9' : '#111827'
      };
    }

    function applyLoginTheme(theme) {
      var t = theme === 'dark' ? 'dark' : 'light';
      document.documentElement.classList.remove('light', 'dark');
      document.documentElement.classList.add(t);
      try { localStorage.setItem('theme', t); } catch (e) {}
      var sun = document.getElementById('theme-icon-sun');
      var moon = document.getElementById('theme-icon-moon');
      if (t === 'dark') {
        sun?.classList.remove('hidden');
        moon?.classList.add('hidden');
      } else {
        sun?.classList.add('hidden');
        moon?.classList.remove('hidden');
      }
    }

    document.getElementById('login-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      try {
        var response = await fetch(this.action, { method: 'POST', body: formData });
        var result = await response.json();
        var sw = swalTheme();

        if (result.status === 'success') {
          if (result.theme) {
            applyLoginTheme(result.theme);
          }
          if (result.lang) {
            var loc = result.lang === 'en' ? 'en' : 'id';
            document.documentElement.lang = loc;
            try { localStorage.setItem('lang', loc); } catch (e) {}
            window.APP_LOCALE = loc;
          }
          Swal.fire({
            icon: 'success',
            title: 'Welcome Back!',
            text: 'Redirecting to your Logistics Control Center...',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            background: sw.background,
            color: sw.color
          }).then(function() {
            var loader = document.getElementById('page-loader');
            if (loader) loader.classList.add('active');
            window.location.href = '<?= base_url('dashboard'); ?>';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: result.message,
            confirmButtonColor: '#4f46e5',
            background: sw.background,
            color: sw.color
          });
        }
      } catch (err) {
        console.error(err);
      }
    });

    function toggleTheme() {
      var next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
      applyLoginTheme(next);
    }

    applyLoginTheme(localStorage.getItem('theme') === 'dark' ? 'dark' : 'light');

    function togglePassword() {
      var passwordInput = document.getElementById('password');
      var eyeOpen = document.getElementById('eye-icon-open');
      var eyeClosed = document.getElementById('eye-icon-closed');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
      } else {
        passwordInput.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
      }
    }

    window.addEventListener('load', function() {
      var loader = document.getElementById('page-loader');
      if (loader) setTimeout(function() { loader.classList.remove('active'); }, 100);
    });

    document.addEventListener('click', function(e) {
      var a = e.target.closest('a');
      if (a && a.href) {
        if (a.target === '_blank' || a.hasAttribute('download')) return;
        try {
          var url = new URL(a.href);
          if (url.origin === window.location.origin) {
            var hrefAttr = a.getAttribute('href');
            if (hrefAttr && !hrefAttr.startsWith('#') && !hrefAttr.startsWith('javascript:')) {
              var loader = document.getElementById('page-loader');
              if (loader) loader.classList.add('active');
            }
          }
        } catch (err) {}
      }
    });
  </script>
</body>
</html>
