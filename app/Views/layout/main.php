<!DOCTYPE html>
<html lang="<?= esc(app_locale()) ?>" class="<?= esc(app_theme()) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title><?= $title ?? 'AppsBeem Logistic' ?></title>
  
  <!-- Prevent theme flash — session/DB is source of truth when logged in -->
  <script>
    (function() {
      var serverTheme = <?= json_encode(app_theme()) ?>;
      var t = serverTheme === 'dark' ? 'dark' : 'light';
      document.documentElement.classList.remove('light', 'dark');
      document.documentElement.classList.add(t);
      try { localStorage.setItem('theme', t); } catch (e) {}
      window.APP_THEME = t;
      var serverLocale = <?= json_encode(app_locale()) ?>;
      var loc = serverLocale === 'en' ? 'en' : 'id';
      document.documentElement.lang = loc;
      try { localStorage.setItem('lang', loc); } catch (e) {}
      window.APP_LOCALE = loc;
    })();
  </script>

  <!-- Prevent FOUC (Flash of Unstyled Content) and loader jump -->
  <style>
    .page-loader {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.3);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }
    .page-loader.active {
      opacity: 1 !important;
      visibility: visible !important;
      pointer-events: auto !important;
    }
    .page-fade-out {
      opacity: 0 !important;
    }
    .app-shell {
      transition: opacity 0.15s ease;
    }
  </style>

  <!-- Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('css/app.css'); ?>">
  <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('icons/icon-32.png'); ?>">
  <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('icons/icon-192.png'); ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('icons/icon-180.png'); ?>">
  <meta name="theme-color" content="#4f46e5">
  <meta name="application-name" content="BeemLog">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="BeemLog">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

</head>
<body>
  
  <!-- Page Loader -->
  <div id="page-loader" class="page-loader active">
    <div class="loader-content">
      <div class="md-circular-progress"></div>
      <div class="mt-4 text-sm font-semibold text-indigo-400">Memuat...</div>
    </div>
  </div>

  <!-- ═══ APP SHELL ═══ -->
  <div class="app-shell page-fade-out">

    <!-- Sliding Sidebar Drawer (Restricted within the 480px app-shell) -->
    <div id="app-sidebar" class="app-sidebar">
      <div class="sidebar-backdrop" onclick="toggleSidebar()"></div>
      <div class="sidebar-drawer">
        <?php
          $companyDisplay = session()->get('company_name') ?: 'AppsBeem Logistic';
          $companyAvatar = strtoupper(substr(preg_replace('/\s+/', '', $companyDisplay), 0, 2) ?: 'AB');
        ?>
        <div class="sidebar-profile">
          <div class="sidebar-avatar">
            <?= esc($companyAvatar) ?>
          </div>
          <div class="sidebar-user-info">
            <p class="text-[9px] font-bold uppercase tracking-wider opacity-70 mb-0.5"><?= lang('App.company_profile') ?></p>
            <h4 class="sidebar-user-name"><?= esc($companyDisplay) ?></h4>
            <span class="sidebar-user-role"><?= esc(session()->get('name') ?? '') ?> · <?= session()->get('role') ?? 'Staff' ?></span>
          </div>
          <button onclick="toggleSidebar()" class="sidebar-close-btn" aria-label="Close menu">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="sidebar-nav">
          <div class="sidebar-nav-title"><?= lang('App.main_menu') ?></div>
          <a href="<?= base_url('dashboard') ?>" class="sidebar-nav-item <?= uri_string() === 'dashboard' ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span><?= lang('App.dashboard') ?></span>
          </a>
          <a href="<?= base_url('inventory/items') ?>" class="sidebar-nav-item <?= uri_string() === 'inventory/items' ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h2a2 2 0 012 2"></path></svg>
            <span><?= lang('App.items') ?></span>
          </a>
          <a href="<?= base_url('inventory/warehouses') ?>" class="sidebar-nav-item <?= uri_string() === 'inventory/warehouses' ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span><?= lang('App.warehouse') ?></span>
          </a>
          <a href="<?= base_url('scan') ?>" class="sidebar-nav-item <?= uri_string() === 'scan' ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-6 4h2m6 0v4m0-4h2m0 0v-4m-12 0h2M4 8V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m0 8v2a2 2 0 01-2 2h-2m-8 0H6a2 2 0 01-2-2v-2"></path></svg>
            <span><?= lang('App.scan') ?></span>
          </a>
          <?php if (\App\Models\UserModel::isAdminRole(session()->get('role'))): ?>
            <a href="<?= base_url('users') ?>" class="sidebar-nav-item <?= uri_string() === 'users' ? 'active' : '' ?>">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
              <span><?= lang('App.users') ?></span>
            </a>
          <?php endif; ?>
          <a href="<?= base_url('backup') ?>" class="sidebar-nav-item <?= uri_string() === 'backup' ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <span><?= lang('App.backup') ?></span>
          </a>
          <div class="sidebar-nav-title"><?= lang('App.account_section') ?></div>
          <a href="<?= base_url('profile') ?>" class="sidebar-nav-item <?= uri_string() === 'profile' ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span><?= lang('App.profile') ?></span>
          </a>
          <a href="<?= base_url('settings') ?>" class="sidebar-nav-item <?= uri_string() === 'settings' ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span><?= lang('App.menu_setting') ?></span>
          </a>
          <div class="sidebar-nav-divider"></div>
          <a href="<?= base_url('auth/logout') ?>" class="sidebar-nav-item logout-item">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="text-red-500 font-bold"><?= lang('App.sign_out') ?></span>
          </a>
        </div>
      </div>
    </div>

    <!-- ── TOP HEADER (Solid gradient, always correct) ── -->
    <header class="app-header">
      <div class="header-inner">
        <!-- Brand -->
        <div class="flex items-center gap-3">
          <button onclick="toggleSidebar()" class="header-menu-btn" aria-label="Open menu">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
          <div>
            <h2 class="header-title"><?= $page_title ?? 'AppsBeem' ?></h2>
            <span class="header-subtitle"><?= session()->get('role') ?></span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
          <!-- Language Switcher -->
          <div class="lang-switcher">
            <button type="button" class="lang-btn<?= app_locale() === 'id' ? ' active' : '' ?>" data-locale="id" onclick="switchAppLocale('id')">ID</button>
            <button type="button" class="lang-btn<?= app_locale() === 'en' ? ' active' : '' ?>" data-locale="en" onclick="switchAppLocale('en')">EN</button>
          </div>
          <!-- Theme Toggle -->
          <button onclick="toggleTheme()" class="header-icon-btn" aria-label="Toggle theme">
            <svg id="theme-icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
            <svg id="theme-icon-moon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
          </button>
        </div>
      </div>

      <!-- Header Decor Shapes -->
      <div class="header-decor-1"></div>
      <div class="header-decor-2"></div>
    </header>

    <!-- ── MAIN CONTENT ── -->
    <main class="app-main">
      <?= $this->renderSection('content') ?>
    </main>

    <!-- ── BOTTOM NAVIGATION ── -->
    <?php if (! ($hideBottomNav ?? false)): ?>
    <nav class="app-bottom-nav">
      <a href="<?= base_url('dashboard') ?>" class="nav-item <?= uri_string() === 'dashboard' ? 'active' : '' ?>">
        <div class="nav-pill">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        </div>
        <span><?= lang('App.dashboard') ?></span>
      </a>

      <a href="<?= base_url('inventory/items') ?>" class="nav-item <?= uri_string() === 'inventory/items' ? 'active' : '' ?>">
        <div class="nav-pill">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </div>
        <span><?= lang('App.items') ?></span>
      </a>

      <!-- Center FAB Scan -->
      <a href="<?= base_url('scan') ?>" class="nav-fab">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-6 4h2m6 0v4m0-4h2m0 0v-4m-12 0h2M4 8V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m0 8v2a2 2 0 01-2 2h-2m-8 0H6a2 2 0 01-2-2v-2"></path></svg>
      </a>

      <a href="<?= base_url('inventory/warehouses') ?>" class="nav-item <?= uri_string() === 'inventory/warehouses' ? 'active' : '' ?>">
        <div class="nav-pill">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <span><?= lang('App.warehouse') ?></span>
      </a>

      <a href="<?= base_url('profile') ?>" class="nav-item <?= uri_string() === 'profile' ? 'active' : '' ?>">
        <div class="nav-pill">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <span><?= lang('App.profile') ?></span>
      </a>
    </nav>
    <?php endif; ?>

  </div><!-- /app-shell -->

  <script>
    // Service Worker
    // if ('serviceWorker' in navigator) {
    //   window.addEventListener('load', () => {
    //     navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
    //       .catch(e => console.log('SW failed:', e));
    //   });
    // }

    // Theme — synced: html class, localStorage, session/DB
    window.applyAppTheme = function(theme, persist) {
      var t = theme === 'dark' ? 'dark' : 'light';
      document.documentElement.classList.remove('light', 'dark');
      document.documentElement.classList.add(t);
      try { localStorage.setItem('theme', t); } catch (e) {}
      window.APP_THEME = t;
      var sun = document.getElementById('theme-icon-sun');
      var moon = document.getElementById('theme-icon-moon');
      if (t === 'dark') {
        sun?.classList.remove('hidden');
        moon?.classList.add('hidden');
      } else {
        sun?.classList.add('hidden');
        moon?.classList.remove('hidden');
      }
      document.dispatchEvent(new CustomEvent('app:theme-changed', { detail: { theme: t } }));
      if (persist) {
        var body = 'theme=' + encodeURIComponent(t);
        fetch('<?= base_url('profile/theme') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
          body: body
        }).catch(function() {});
      }
    };

    function toggleTheme() {
      var next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
      window.applyAppTheme(next, true);
    }

    window.applyAppTheme(window.APP_THEME || 'light', false);

    window.updateLangButtons = function(locale) {
      var loc = locale === 'en' ? 'en' : 'id';
      document.querySelectorAll('.lang-switcher .lang-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.locale === loc);
      });
    };

    window.applyAppLocale = function(locale, persist) {
      var loc = locale === 'en' ? 'en' : 'id';
      document.documentElement.lang = loc;
      try { localStorage.setItem('lang', loc); } catch (e) {}
      window.APP_LOCALE = loc;
      window.updateLangButtons(loc);
      document.dispatchEvent(new CustomEvent('app:locale-changed', { detail: { locale: loc } }));
      if (persist) {
        var body = 'locale=' + encodeURIComponent(loc);
        return fetch('<?= base_url('profile/locale') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
          body: body
        }).catch(function() {});
      }
      return Promise.resolve();
    };

    window.switchAppLocale = function(locale) {
      var loc = locale === 'en' ? 'en' : 'id';
      if (loc === window.APP_LOCALE) return;
      var loader = document.getElementById('page-loader');
      loader?.classList.add('active');
      window.applyAppLocale(loc, true).then(function() {
        var url = new URL(location.href);
        url.searchParams.delete('lang');
        location.href = url.pathname + (url.search || '') + url.hash;
      });
    };

    window.applyAppLocale(window.APP_LOCALE || 'id', false);

    // Initialize History State for the initial page load
    if (!history.state) {
      history.replaceState({ url: location.href }, '', location.href);
    }

    // Page Loader for initial page entry
    window.addEventListener('load', () => {
      const loader = document.getElementById('page-loader');
      if (loader) setTimeout(() => loader.classList.remove('active'), 120);
      document.querySelector('.app-shell')?.classList.remove('page-fade-out');
      setTimeout(() => {
        document.dispatchEvent(new CustomEvent('app:page-loaded', { detail: { url: location.href } }));
        if (typeof window.scheduleDashboardChart === 'function') {
          window.scheduleDashboardChart();
        }
      }, 130);
    });

    /** Run page scripts in order (external src awaited before inline). */
    function executePageScripts(root) {
      const scripts = Array.from(root.querySelectorAll('script'));
      let chain = Promise.resolve();
      scripts.forEach(oldScript => {
        chain = chain.then(() => new Promise((resolve, reject) => {
          const el = document.createElement('script');
          Array.from(oldScript.attributes).forEach(attr => el.setAttribute(attr.name, attr.value));
          if (oldScript.src) {
            if (oldScript.src.includes('chart.js') && typeof Chart !== 'undefined') {
              resolve();
              return;
            }
            el.onload = () => resolve();
            el.onerror = reject;
            el.src = oldScript.src;
            document.body.appendChild(el);
            return;
          }
          let code = oldScript.textContent || '';
          code = code.replace(/\b(let|const)\s+([a-zA-Z_$][a-zA-Z0-9_$]*)\s*(=|;|,|\n)/g, 'var $2$3');
          el.textContent = code;
          document.body.appendChild(el);
          el.remove();
          resolve();
        }));
      });
      return chain;
    }

    function cleanAppUrl(url) {
      try {
        var u = new URL(url, location.origin);
        u.searchParams.delete('lang');
        return u.pathname + u.search + u.hash;
      } catch (e) {
        return url;
      }
    }

    // Premium PJAX Page Router
    function loadPage(url, pushToHistory = true) {
      url = cleanAppUrl(url);
      const loader = document.getElementById('page-loader');
      const shell = document.querySelector('.app-shell');
      
      // Close sidebar if open
      document.getElementById('app-sidebar')?.classList.remove('active');
      
      // Show loader instantly and fade out shell content
      loader?.classList.add('active');
      shell?.classList.add('page-fade-out');
      
      fetch(url)
        .then(response => {
          if (!response.ok) throw new Error('Response not OK');
          const ct = (response.headers.get('content-type') || '').toLowerCase();
          const cd = response.headers.get('content-disposition') || '';
          const isDownload = cd.includes('attachment') || (!ct.includes('text/html') && !ct.includes('application/xhtml'));
          if (isDownload) {
            return response.blob().then(blob => {
              let name = 'download';
              const m = cd.match(/filename\*?=(?:UTF-8''|")?([^";\n]+)/i);
              if (m) name = decodeURIComponent(m[1].replace(/"/g, ''));
              const link = document.createElement('a');
              link.href = URL.createObjectURL(blob);
              link.download = name;
              link.click();
              URL.revokeObjectURL(link.href);
              shell?.classList.remove('page-fade-out');
              loader?.classList.remove('active');
            });
          }
          return response.text();
        })
        .then(html => {
          if (typeof html !== 'string') return;
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          
          const currentShell = document.querySelector('.app-shell');
          const newShell = doc.querySelector('.app-shell');
          
          if (currentShell && newShell) {
            // Swap shell content & update document title
            document.title = doc.title;
            currentShell.innerHTML = newShell.innerHTML;
            currentShell.className = newShell.className;

            var fetchedLang = doc.documentElement.getAttribute('lang');
            if (fetchedLang && typeof window.applyAppLocale === 'function') {
              window.applyAppLocale(fetchedLang, false);
            }
            
            if (pushToHistory) {
              history.pushState({ url }, '', url);
            }
            
            window.scrollTo({ top: 0 });

            if (window.__dashboardTxChart) {
              window.__dashboardTxChart.destroy();
              window.__dashboardTxChart = null;
            }

            executePageScripts(newShell).then(() => {
              document.dispatchEvent(new Event('DOMContentLoaded'));
              window.dispatchEvent(new Event('load'));
              setTimeout(() => {
                document.querySelector('.app-shell')?.classList.remove('page-fade-out');
                loader?.classList.remove('active');
                document.dispatchEvent(new CustomEvent('app:page-loaded', { detail: { url } }));
                if (typeof window.scheduleDashboardChart === 'function') {
                  window.scheduleDashboardChart();
                }
              }, 120);
            }).catch(err => {
              console.error('Page scripts failed:', err);
              document.querySelector('.app-shell')?.classList.remove('page-fade-out');
              loader?.classList.remove('active');
            });
          } else {
            // Fallback: full navigation (downloads, login, etc.)
            shell?.classList.remove('page-fade-out');
            loader?.classList.remove('active');
            window.location.href = url;
          }
        })
        .catch(err => {
          console.error('PJAX navigation failed, falling back:', err);
          shell?.classList.remove('page-fade-out');
          loader?.classList.remove('active');
          window.location.href = url;
        });
    }

    // Intercept link clicks for AJAX dynamic routing
    document.addEventListener('click', function(e) {
      const a = e.target.closest('a');
      if (a?.href && !a.target && !a.hasAttribute('download') && a.dataset.noPjax !== 'true' && !a.classList.contains('no-pjax')) {
        try {
          const u = new URL(a.href);
          const h = a.getAttribute('href') || '';
          if (/\/backup\/download\//i.test(u.pathname)) {
            return;
          }
          if (u.origin === location.origin && !h.startsWith('#') && !h.startsWith('javascript:')) {
            e.preventDefault();
            loadPage(cleanAppUrl(a.href));
          }
        } catch(err) {}
      }
    });

    // Intercept popstate for browser back/forward buttons
    window.addEventListener('popstate', (e) => {
      if (e.state && e.state.url) {
        loadPage(e.state.url, false);
      } else {
        loadPage(location.href, false);
      }
    });

    // Form submit → show loader
    document.addEventListener('submit', e => {
      if (e.defaultPrevented) return;
      if (e.target.id !== 'login-form') document.getElementById('page-loader')?.classList.add('active');
    });

    // Toggle Sidebar Drawer
    function toggleSidebar() {
      document.getElementById('app-sidebar')?.classList.toggle('active');
    }
  </script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
