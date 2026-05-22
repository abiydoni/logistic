<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="app-page dashboard-page">

  <header class="page-head">
    <h1 class="page-head-title"><?= lang('App.halo_admin', ['name' => esc(session()->get('name') ?? 'Admin')]) ?> 👋</h1>
    <p class="page-head-desc"><?= lang('App.operational_summary') ?></p>
  </header>

  <div class="stat-grid stat-grid--slim" role="list">
    <div class="stat-card stat-card--indigo">
      <div class="stat-card__icon">
        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
      <div>
        <div class="stat-card__label"><?= lang('App.warehouse') ?></div>
        <div class="stat-card__value"><?= $total_warehouses ?></div>
      </div>
    </div>

    <div class="stat-card stat-card--green">
      <div class="stat-card__icon">
        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
      </div>
      <div>
        <div class="stat-card__label"><?= lang('App.items') ?></div>
        <div class="stat-card__value"><?= $total_items ?></div>
        <div class="stat-card__sub"><?= lang('App.stock') ?>: <?= $total_stock ?></div>
      </div>
    </div>

    <?php if ($low_stock_count > 0): ?>
    <div class="stat-card stat-card--amber">
      <div class="stat-card__icon">
        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <div>
        <div class="stat-card__label"><?= lang('App.low_stock') ?></div>
        <div class="stat-card__value"><?= $low_stock_count ?></div>
      </div>
    </div>
    <?php else: ?>
    <div class="stat-card stat-card--muted">
      <div class="stat-card__icon">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      </div>
      <div>
        <div class="stat-card__label"><?= lang('App.low_stock') ?></div>
        <div class="stat-card__value">0</div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($expired_count > 0): ?>
    <div class="stat-card stat-card--red">
      <div class="stat-card__icon">
        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <div class="stat-card__label"><?= lang('App.expired') ?></div>
        <div class="stat-card__value"><?= $expired_count ?></div>
      </div>
    </div>
    <?php else: ?>
    <div class="stat-card stat-card--muted">
      <div class="stat-card__icon">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      </div>
      <div>
        <div class="stat-card__label"><?= lang('App.expired') ?></div>
        <div class="stat-card__value">0</div>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($low_stock_count > 0 || $expired_count > 0): ?>
  <div class="app-page" style="gap:10px;padding-bottom:0">
    <?php if ($low_stock_count > 0): ?>
    <div class="alert-card alert-card--warn">
      <svg class="alert-card__icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div class="flex-1 min-w-0">
        <div class="alert-card__title"><?= lang('App.alert_low_stock_title') ?></div>
        <div class="alert-card__desc"><?= lang('App.alert_low_stock_desc', ['count' => $low_stock_count]) ?></div>
        <div class="alert-card__tags">
          <?php foreach ($low_stock_items as $item): ?>
            <span class="badge badge-amber"><?= esc($item['name']) ?> (<?= $item['current_stock'] ?>)</span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($expired_count > 0): ?>
    <div class="alert-card alert-card--danger">
      <svg class="alert-card__icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div class="flex-1 min-w-0">
        <div class="alert-card__title"><?= lang('App.alert_expired_title') ?></div>
        <div class="alert-card__desc"><?= lang('App.alert_expired_desc', ['count' => $expired_count]) ?></div>
        <div class="alert-card__tags">
          <?php foreach ($expired_items as $item): ?>
            <span class="badge badge-rose"><?= esc($item['name']) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <section>
    <div class="section-heading" style="justify-content:space-between">
      <div style="display:flex;align-items:center;gap:8px">
        <div class="section-heading-bar"></div>
        <span class="section-heading-text"><?= lang('App.tx_stats') ?></span>
      </div>
      <span style="font-size:11px;font-weight:600;color:var(--text-faint)"><?= lang('App.tx_stats_subtitle') ?></span>
    </div>
    <div class="chart-card">
      <div class="chart-card-wrap">
        <canvas id="transactionChart"></canvas>
      </div>
    </div>
  </section>

  <section>
    <div class="section-heading" style="justify-content:space-between">
      <div style="display:flex;align-items:center;gap:8px">
        <div class="section-heading-bar"></div>
        <span class="section-heading-text"><?= lang('App.recent_activity') ?></span>
      </div>
      <a href="<?= base_url('inventory/items') ?>" style="font-size:12px;font-weight:700;color:var(--primary);text-decoration:none"><?= lang('App.view_all') ?></a>
    </div>
    <div class="activity-card">
      <?php if (empty($recent_transactions)): ?>
        <div class="empty-state" style="border:none;box-shadow:none">
          <p class="empty-state__text"><?= lang('App.empty_data') ?></p>
        </div>
      <?php else: ?>
        <?php $i = 1; foreach ($recent_transactions as $tx): ?>
          <div class="activity-item">
            <div class="activity-item__left">
              <div class="activity-item__icon <?= $tx['type'] === 'in' ? 'is-in' : 'is-out' ?>">
                <svg width="16" height="16" fill="none" stroke="<?= $tx['type'] === 'in' ? '#10b981' : '#ef4444' ?>" stroke-width="2" viewBox="0 0 24 24">
                  <?php if ($tx['type'] === 'in'): ?>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                  <?php else: ?>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  <?php endif; ?>
                </svg>
              </div>
              <div>
                <div class="activity-item__title"><?= esc($tx['item_name']) ?></div>
                <div class="activity-item__meta"><?= date('d M H:i', strtotime($tx['created_at'])) ?> · <?= esc($tx['user_name'] ?? 'System') ?></div>
              </div>
            </div>
            <div>
              <div class="activity-item__qty <?= $tx['type'] === 'in' ? 'is-in' : 'is-out' ?>"><?= $tx['type'] === 'in' ? '+' : '-' ?><?= $tx['quantity'] ?></div>
              <div class="activity-item__unit"><?= esc($tx['unit']) ?></div>
            </div>
          </div>
        <?php if ($i++ == 5) break; endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

</div>

<script>
(function() {
  window.__dashboardChartData = {
    labels: <?= json_encode($chart_labels) ?>,
    in: <?= json_encode($chart_in) ?>,
    out: <?= json_encode($chart_out) ?>,
    labelIn: <?= json_encode(lang('App.stock_in')) ?>,
    labelOut: <?= json_encode(lang('App.stock_out')) ?>,
  };

  var chartInitTimer = null;

  window.initDashboardChart = function initDashboardChart() {
    if (typeof Chart === 'undefined') return;
    var canvas = document.getElementById('transactionChart');
    if (!canvas) return;

    var d = window.__dashboardChartData;
    if (!d) return;

    var existing = window.__dashboardTxChart;
    if (existing && existing.canvas === canvas) {
      existing.data.labels = d.labels;
      existing.data.datasets[0].data = d.in;
      existing.data.datasets[0].label = d.labelIn;
      existing.data.datasets[1].data = d.out;
      existing.data.datasets[1].label = d.labelOut;
      existing.update('none');
      existing.resize();
      return;
    }

    if (existing) {
      existing.destroy();
      window.__dashboardTxChart = null;
    }

    var ctx = canvas.getContext('2d');
    var dk = document.documentElement.classList.contains('dark');
    var tc = dk ? '#475569' : '#94a3b8';

    var gIn = ctx.createLinearGradient(0, 0, 0, 170);
    gIn.addColorStop(0, 'rgba(16,185,129,.25)');
    gIn.addColorStop(1, 'rgba(16,185,129,0)');

    var gOut = ctx.createLinearGradient(0, 0, 0, 170);
    gOut.addColorStop(0, 'rgba(239,68,68,.25)');
    gOut.addColorStop(1, 'rgba(239,68,68,0)');

    window.__dashboardTxChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: d.labels,
        datasets: [
          {
            label: d.labelIn,
            data: d.in,
            borderColor: '#10b981',
            backgroundColor: gIn,
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#10b981',
            pointHoverRadius: 6
          },
          {
            label: d.labelOut,
            data: d.out,
            borderColor: '#ef4444',
            backgroundColor: gOut,
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#ef4444',
            pointHoverRadius: 6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 850, easing: 'easeOutQuart' },
        animations: {
          x: { duration: 0 },
          colors: { duration: 0 },
          y: {
            type: 'number',
            duration: 850,
            easing: 'easeOutQuart',
            from: function(ctx) {
              if (ctx.type === 'data' && ctx.chart && ctx.chart.scales.y) {
                return ctx.chart.scales.y.getPixelForValue(0);
              }
            },
          },
        },
        transitions: {
          resize: { animation: { duration: 0 } },
          active: { animation: { duration: 150 } },
        },
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            align: 'end',
            labels: {
              boxWidth: 8,
              usePointStyle: true,
              pointStyle: 'circle',
              font: { size: 11, family: "'Outfit',sans-serif", weight: '700' },
              color: tc
            }
          },
          tooltip: {
            backgroundColor: dk ? '#1e293b' : '#ffffff',
            titleColor: dk ? '#f1f5f9' : '#0f172a',
            bodyColor: tc,
            borderColor: dk ? '#334155' : '#e2e8f0',
            borderWidth: 1,
            padding: 10,
            cornerRadius: 10
          }
        },
        scales: {
          x: {
            grid: { display: false, drawBorder: false },
            ticks: { font: { size: 10, family: "'Outfit',sans-serif" }, color: tc, maxRotation: 0 }
          },
          y: {
            beginAtZero: true,
            grid: { color: dk ? 'rgba(255,255,255,.04)' : 'rgba(0,0,0,.04)', drawBorder: false },
            ticks: { font: { size: 10, family: "'Outfit',sans-serif" }, color: tc, maxTicksLimit: 4 }
          }
        }
      }
    });

    if (!window.__dashboardChartThemeObserver) {
      window.__dashboardChartThemeObserver = new MutationObserver(function() {
        var chart = window.__dashboardTxChart;
        if (!chart) return;
        var d = document.documentElement.classList.contains('dark');
        var c = d ? '#475569' : '#94a3b8';
        chart.options.plugins.legend.labels.color = c;
        chart.options.scales.x.ticks.color = c;
        chart.options.scales.y.ticks.color = c;
        chart.options.scales.y.grid.color = d ? 'rgba(255,255,255,.04)' : 'rgba(0,0,0,.04)';
        chart.options.plugins.tooltip.backgroundColor = d ? '#1e293b' : '#ffffff';
        chart.options.plugins.tooltip.titleColor = d ? '#f1f5f9' : '#0f172a';
        chart.update('none');
      });
      window.__dashboardChartThemeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    }

    window.__dashboardTxChart.resize();
  };

  window.scheduleDashboardChart = function scheduleDashboardChart() {
    if (!document.getElementById('transactionChart')) return;
    clearTimeout(chartInitTimer);
    chartInitTimer = setTimeout(window.initDashboardChart, 50);
  };
})();
</script>

<?= $this->endSection() ?>
