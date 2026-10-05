<?php
// =============================================
// FLEET MANAGEMENT - OPERATIONS DASHBOARD
// =============================================
helper('permission');
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');
$role_name = $this->session->get('__xsys_myuserrolename__');

$meUser = $this->db->query("SELECT full_name, position FROM myua_user WHERE username = ?", [$this->cuser])->getRowArray();
$full_name = $meUser['full_name'] ?? $this->cuser;
$position = $meUser['position'] ?? '';

// ==============================
// FILTERS (Month + Year — KPI cards are current-state snapshots and are not
// affected by these; the Analytics widgets below are)
// ==============================
$filter_year = (int) ($this->request->getGet('filter_year') ?: date('Y'));
$filter_month = $this->request->getGet('filter_month') ?: 'all';
if ($filter_month !== 'all') { $filter_month = (int) $filter_month; }

// ==============================
// LIVE DATA
// ==============================
$dashboardModel = model('App\Models\FMS_Dashboard_Model');

$fleet = $dashboardModel->getFleetStats($filter_year, $filter_month);
$personnel = $dashboardModel->getPersonnelStats($filter_year, $filter_month);
$trips = $dashboardModel->getTripStats($filter_year, $filter_month);
$billing = $dashboardModel->getBillingStats($filter_year, $filter_month);
$monthlyFuel = $dashboardModel->getMonthlyFuelExpense($filter_year);
$fuelEfficiency = $dashboardModel->getFuelEfficiency($filter_year, $filter_month);
$routes = $dashboardModel->getFrequentRoutes($filter_year, $filter_month);
$services = $dashboardModel->getServiceTypeAnalysis($filter_year, $filter_month);
$customers = $dashboardModel->getCustomerActivity($filter_year, $filter_month);
$insights = $dashboardModel->getInsights($filter_year, $filter_month);

$maxRouteCount = $routes ? max(array_column($routes, 'total')) : 0;
$totalServiceRevenue = array_sum(array_column($services, 'revenue'));
$periodLabel = $filter_month === 'all' ? (string) $filter_year : date('M', mktime(0,0,0,$filter_month,1)) . ' ' . $filter_year;

echo view('templates/myheader.php');
?>

<style>
    /* ============================================ */
    /* FLEET MANAGEMENT DASHBOARD - COMPACT UX */
    /* ============================================ */
    :root {
        --bg-primary: #f0f4f8;
        --bg-card: #ffffff;
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --text-muted: #718096;
        --border-color: #e2e8f0;
        --accent: #1a6bb0;
        --accent-light: #ebf4ff;
        --success: #10b981;
        --success-light: #f0fff4;
        --warning: #f59e0b;
        --warning-light: #fffbeb;
        --danger: #dc2626;
        --danger-light: #fff5f5;
        --info: #3b82f6;
        --info-light: #eff6ff;
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 2px 8px rgba(0,0,0,0.06);
        --shadow-lg: 0 4px 16px rgba(0,0,0,0.08);
        --mono: 'SF Mono', 'Menlo', 'Monaco', monospace;
    }

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* ============================================ */
    /* HEADER - FULL WIDTH, NO EXTRA SPACE */
    /* ============================================ */
    .fleet-header {
        background: linear-gradient(135deg, #0f5a99 0%, #1a6bb0 100%);
        padding: 14px 24px;
        margin: -24px -24px 20px -24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .fleet-header .brand-section {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        min-width: 200px;
    }

    .fleet-header .brand-section h2 {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .fleet-header .brand-section h2 i {
        color: #90cdf4;
        font-size: 22px;
    }

    .fleet-header .brand-section h2 .real-time-badge {
        font-size: 10px;
        font-weight: 400;
        color: #90cdf4;
        background: rgba(255, 255, 255, 0.08);
        padding: 2px 12px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .fleet-header .brand-section .user-meta {
        color: #bee3f8;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 2px;
    }

    .fleet-header .brand-section .user-meta .live-clock {
        color: #ffffff;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.1);
        padding: 2px 12px;
        border-radius: 4px;
        font-size: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .header-filter {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        background: rgba(255, 255, 255, 0.06);
        padding: 4px 14px;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .header-filter .filter-group {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .header-filter .filter-group label {
        color: #bee3f8;
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        white-space: nowrap;
    }

    .header-filter .filter-group select,
    .header-filter .filter-group input[type="date"] {
        padding: 3px 10px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        font-size: 11px;
        font-weight: 500;
        outline: none;
        min-width: 80px;
        cursor: pointer;
        height: 28px;
    }

    .header-filter .filter-group select option {
        background: #1a365d;
        color: #ffffff;
        padding: 4px;
    }

    .header-filter .btn-filter-header {
        padding: 4px 16px;
        border-radius: 4px;
        border: none;
        background: #ffffff;
        color: #1a365d;
        font-weight: 600;
        font-size: 11px;
        cursor: pointer;
        height: 28px;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
    }

    .header-filter .btn-filter-header:hover {
        background: #90cdf4;
        transform: translateY(-1px);
    }

    .header-filter .btn-reset-header {
        padding: 4px 12px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: transparent;
        color: #bee3f8;
        font-weight: 500;
        font-size: 11px;
        cursor: pointer;
        height: 28px;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
    }

    .header-filter .btn-reset-header:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* ============================================ */
    /* STAT CARDS - COMPACT WITH VISIBLE LABELS */
    /* ============================================ */
    .stat-card {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        height: 100%;
        transition: all 0.15s;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .stat-card .stat-icon {
        position: absolute;
        top: 10px;
        right: 12px;
        font-size: 28px;
        opacity: 0.06;
        color: var(--accent);
    }

    .stat-label {
        font-size: 9px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-label i {
        color: var(--accent);
        font-size: 11px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        font-family: var(--mono);
        display: flex;
        align-items: center;
        gap: 6px;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }

    .stat-value .trend {
        font-size: 10px;
        font-weight: 600;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .stat-value .trend.up { color: var(--success); }
    .stat-value .trend.down { color: var(--danger); }

    .stat-sub {
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 2px;
        line-height: 1.3;
        font-weight: 500;
    }

    .stat-sub .highlight {
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ============================================ */
    /* SECTION TITLES - COMPACT */
    /* ============================================ */
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex-wrap: wrap;
    }

    .section-title i {
        color: var(--accent);
        font-size: 14px;
    }

    .section-title .badge-count {
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 8px;
        padding: 2px 10px;
        border-radius: 12px;
        font-weight: 700;
        border: 1px solid var(--border-color);
    }

    .section-title .badge-count.primary {
        background: var(--accent);
        color: #ffffff;
        border-color: var(--accent);
    }

    .section-title .badge-count.success {
        background: var(--success);
        color: #ffffff;
        border-color: var(--success);
    }

    .card-container {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 14px 16px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        height: 100%;
        transition: all 0.15s;
    }

    .card-container:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-md);
    }

    /* ============================================ */
    /* TABLES - COMPACT */
    /* ============================================ */
    .table td, .table th {
        padding: 5px 6px;
        vertical-align: middle;
        font-size: 11px;
        border-bottom: 1px solid var(--border-color);
    }

    .table thead th {
        background: var(--bg-primary);
        color: var(--text-muted);
        font-weight: 700;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
        padding: 5px 6px;
    }

    .table tbody tr:hover {
        background: var(--bg-primary);
    }

    .table .mono {
        font-family: var(--mono);
        font-weight: 600;
    }

    /* ============================================ */
    /* CHARTS - COMPACT */
    /* ============================================ */
    .chart-wrapper {
        position: relative;
        height: 130px;
        width: 100%;
    }

    /* ============================================ */
    /* ROUTE BARS - COMPACT */
    /* ============================================ */
    .route-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
    }

    .route-bar .route-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-secondary);
        min-width: 70px;
        white-space: nowrap;
    }

    .route-bar .route-track {
        flex: 1;
        height: 16px;
        background: var(--bg-primary);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .route-bar .route-track .route-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.8s ease;
        background: linear-gradient(90deg, var(--accent), #60a5fa);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 6px;
        font-size: 8px;
        font-weight: 700;
        color: #ffffff;
        min-width: 24px;
    }

    .route-bar .route-count {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-primary);
        min-width: 28px;
        text-align: right;
        font-family: var(--mono);
    }

    /* ============================================ */
    /* STATUS BADGES - COMPACT */
    /* ============================================ */
    .status-badge {
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 2px 10px;
        border-radius: 4px;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    .status-badge.success {
        background: var(--success-light);
        color: var(--success);
        border: 1px solid #c6f6d5;
    }

    .status-badge.warning {
        background: var(--warning-light);
        color: var(--warning);
        border: 1px solid #fef3c7;
    }

    .status-badge.danger {
        background: var(--danger-light);
        color: var(--danger);
        border: 1px solid #fecaca;
    }

    .status-badge.info {
        background: var(--info-light);
        color: var(--info);
        border: 1px solid #bfdbfe;
    }

    .status-badge.secondary {
        background: var(--bg-primary);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }

    /* ============================================ */
    /* INSIGHT ITEMS - COMPACT */
    /* ============================================ */
    .insight-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 11px;
        border-left: 3px solid var(--accent);
        background: var(--bg-primary);
        margin-bottom: 4px;
    }

    .insight-item .label {
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 600;
    }

    .insight-item .value {
        font-weight: 700;
        color: var(--text-primary);
        font-family: var(--mono);
        font-size: 12px;
    }

    .insight-item.success {
        background: var(--success-light);
        border-left-color: var(--success);
    }
    .insight-item.success .value { color: var(--success); }

    .insight-item.warning {
        background: var(--warning-light);
        border-left-color: var(--warning);
    }
    .insight-item.warning .value { color: var(--warning); }

    .insight-item.danger {
        background: var(--danger-light);
        border-left-color: var(--danger);
    }
    .insight-item.danger .value { color: var(--danger); }

    .insight-item.info {
        background: var(--info-light);
        border-left-color: var(--info);
    }
    .insight-item.info .value { color: var(--info); }

    /* ============================================ */
    /* FUEL EFFICIENCY - COMPACT */
    /* ============================================ */
    .fuel-stat {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 10px;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
    }

    .fuel-stat:last-child {
        border-bottom: none;
    }

    .fuel-stat .fuel-label {
        font-weight: 600;
        color: var(--text-muted);
    }

    .fuel-stat .fuel-value {
        font-weight: 700;
        color: var(--text-primary);
        font-family: var(--mono);
    }

    .empty-note {
        font-size: 11px;
        color: var(--text-muted);
        text-align: center;
        padding: 16px 8px;
    }

    /* ============================================ */
    /* RESPONSIVE - KEEP HEADER FULL WIDTH */
    /* ============================================ */
    @media (max-width: 992px) {
        .fleet-header {
            padding: 12px 20px;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .header-filter {
            padding: 6px 12px;
            justify-content: flex-start;
        }
        .header-filter .filter-group select,
        .header-filter .filter-group input[type="date"] {
            min-width: 70px;
            font-size: 10px;
            padding: 2px 8px;
            height: 26px;
        }
        .stat-value {
            font-size: 20px;
        }
    }

    @media (max-width: 768px) {
        .fleet-header {
            padding: 10px 16px;
        }
        .fleet-header .brand-section h2 {
            font-size: 16px;
        }
        .fleet-header .brand-section h2 .real-time-badge {
            font-size: 9px;
            padding: 1px 10px;
        }
        .fleet-header .brand-section .user-meta {
            font-size: 11px;
            gap: 8px;
        }
        .header-filter {
            padding: 4px 8px;
            gap: 4px;
        }
        .header-filter .filter-group select,
        .header-filter .filter-group input[type="date"] {
            min-width: 60px;
            font-size: 9px;
            padding: 2px 6px;
            height: 24px;
        }
        .header-filter .filter-group label {
            font-size: 8px;
        }
        .header-filter .btn-filter-header,
        .header-filter .btn-reset-header {
            font-size: 9px;
            padding: 3px 10px;
            height: 24px;
        }
        .stat-value {
            font-size: 18px;
        }
        .stat-label {
            font-size: 8px;
        }
        .stat-sub {
            font-size: 9px;
        }
        .card-container {
            padding: 10px 12px;
        }
        .stat-card {
            padding: 10px 12px;
        }
        .stat-card .stat-icon {
            font-size: 22px;
            top: 8px;
            right: 8px;
        }
        .route-bar .route-label {
            font-size: 9px;
            min-width: 55px;
        }
        .route-bar .route-count {
            font-size: 9px;
            min-width: 22px;
        }
        .chart-wrapper {
            height: 110px;
        }
        .section-title {
            font-size: 10px;
        }
        .section-title .badge-count {
            font-size: 7px;
            padding: 1px 8px;
        }
    }

    @media (max-width: 480px) {
        .fleet-header {
            padding: 8px 12px;
            margin: -16px -16px 16px -16px;
        }
        .fleet-header .brand-section h2 {
            font-size: 14px;
        }
        .fleet-header .brand-section h2 .real-time-badge {
            font-size: 8px;
            padding: 1px 8px;
        }
        .fleet-header .brand-section .user-meta {
            font-size: 10px;
            gap: 4px;
            flex-wrap: wrap;
        }
        .fleet-header .brand-section .user-meta .live-clock {
            font-size: 10px;
            padding: 1px 8px;
        }
        .header-filter {
            padding: 4px 6px;
            gap: 3px;
        }
        .header-filter .filter-group select,
        .header-filter .filter-group input[type="date"] {
            min-width: 50px;
            font-size: 8px;
            padding: 2px 5px;
            height: 22px;
        }
        .header-filter .filter-group label {
            font-size: 7px;
            letter-spacing: 0.3px;
        }
        .header-filter .btn-filter-header,
        .header-filter .btn-reset-header {
            font-size: 8px;
            padding: 2px 8px;
            height: 22px;
        }
        .stat-value {
            font-size: 16px;
        }
        .stat-label {
            font-size: 7px;
        }
        .stat-sub {
            font-size: 8px;
        }
        .stat-card .stat-icon {
            font-size: 18px;
        }
        .chart-wrapper {
            height: 90px;
        }
        .card-container {
            padding: 8px 8px;
        }
        .stat-card {
            padding: 8px 8px;
        }
        .route-bar .route-label {
            font-size: 8px;
            min-width: 45px;
        }
        .route-bar .route-track {
            height: 12px;
        }
        .route-bar .route-count {
            font-size: 8px;
            min-width: 18px;
        }
        .insight-item {
            padding: 4px 8px;
            font-size: 9px;
        }
        .insight-item .label {
            font-size: 8px;
        }
        .insight-item .value {
            font-size: 10px;
        }
    }
</style>

<div class="container-fluid px-0">

    <!-- ============================================ -->
    <!-- HEADER - FULL WIDTH -->
    <!-- ============================================ -->
    <div class="fleet-header">
        <div class="brand-section">
            <div>
                <h2>
                    <i class="bi bi-truck"></i>
                    Fleet Operations Dashboard
                    <span class="real-time-badge">Live Data</span>
                </h2>
                <div class="user-meta">
                    <span><i class="bi bi-person-circle"></i> <?= esc($full_name) ?></span>
                    <?php if ($position): ?><span><i class="bi bi-building"></i> <?= esc($position) ?></span><?php endif; ?>
                    <?php if ($role_name): ?><span><i class="bi bi-shield-lock"></i> <?= esc($role_name) ?></span><?php endif; ?>
                    <span><i class="bi bi-calendar3"></i> <?= date('M d, Y') ?></span>
                    <span class="live-clock"><i class="bi bi-clock"></i> <span id="liveClock"><?= date('h:i A') ?></span></span>
                </div>
            </div>
        </div>

        <!-- FILTER (drives the Analytics widgets below; KPI cards are current-state) -->
        <form method="GET" action="" class="header-filter" id="filterForm">
            <div class="filter-group">
                <label for="filter_month">Month</label>
                <select name="filter_month" id="filter_month">
                    <option value="all" <?= $filter_month === 'all' ? 'selected' : '' ?>>All</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $filter_month === $m ? 'selected' : '' ?>><?= date('M', mktime(0,0,0,$m,1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="filter_year">Year</label>
                <select name="filter_year" id="filter_year">
                    <?php for ($y = (int) date('Y') + 1; $y >= (int) date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $filter_year === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="btn-filter-header"><i class="bi bi-check2"></i> Apply</button>
            <a href="<?= current_url() ?>" class="btn-reset-header"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </form>
    </div>

    <?php if (widget_visible('fleet_stats')): ?>
    <!-- ============================================ -->
    <!-- FLEET STATS -->
    <!-- ============================================ -->
    <div class="row g-2 mb-2">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-truck"></i></div>
                <div class="stat-label"><i class="bi bi-truck"></i> Total Trucks</div>
                <div class="stat-value"><?= $fleet['total_trucks'] ?></div>
                <div class="stat-sub">Added in <?= esc($periodLabel) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-label"><i class="bi bi-check-circle"></i> Available Trucks</div>
                <div class="stat-value"><?= $fleet['available'] ?></div>
                <div class="stat-sub"><span class="highlight"><?= $fleet['total_trucks'] > 0 ? round($fleet['available'] / $fleet['total_trucks'] * 100) : 0 ?>%</span> of fleet</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-arrow-right"></i></div>
                <div class="stat-label"><i class="bi bi-arrow-right"></i> In Transit</div>
                <div class="stat-value"><?= $fleet['in_transit'] ?></div>
                <div class="stat-sub"><span class="highlight"><?= $fleet['total_trucks'] > 0 ? round($fleet['in_transit'] / $fleet['total_trucks'] * 100) : 0 ?>%</span> on road</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-tools"></i></div>
                <div class="stat-label"><i class="bi bi-tools"></i> Maintenance</div>
                <div class="stat-value"><?= $fleet['maintenance'] ?></div>
                <div class="stat-sub"><span class="highlight"><?= $fleet['total_trucks'] > 0 ? round($fleet['maintenance'] / $fleet['total_trucks'] * 100) : 0 ?>%</span> of fleet</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (widget_visible('personnel_stats')): ?>
    <!-- ============================================ -->
    <!-- PERSONNEL STATS -->
    <!-- ============================================ -->
    <div class="row g-2 mb-2">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                <div class="stat-label"><i class="bi bi-person-badge"></i> Total Drivers</div>
                <div class="stat-value"><?= $personnel['total_drivers'] ?></div>
                <div class="stat-sub">Hired in <?= esc($periodLabel) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                <div class="stat-label"><i class="bi bi-person-check"></i> Available Drivers</div>
                <div class="stat-value"><?= $personnel['available_drivers'] ?></div>
                <div class="stat-sub"><span class="highlight"><?= $personnel['total_drivers'] > 0 ? round($personnel['available_drivers'] / $personnel['total_drivers'] * 100) : 0 ?>%</span> available</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-plus"></i></div>
                <div class="stat-label"><i class="bi bi-person-plus"></i> Total Helpers</div>
                <div class="stat-value"><?= $personnel['total_helpers'] ?></div>
                <div class="stat-sub">Hired in <?= esc($periodLabel) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                <div class="stat-label"><i class="bi bi-person-check"></i> Available Helpers</div>
                <div class="stat-value"><?= $personnel['available_helpers'] ?></div>
                <div class="stat-sub"><span class="highlight"><?= $personnel['total_helpers'] > 0 ? round($personnel['available_helpers'] / $personnel['total_helpers'] * 100) : 0 ?>%</span> available</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (widget_visible('trip_stats')): ?>
    <!-- ============================================ -->
    <!-- TRIP STATS -->
    <!-- ============================================ -->
    <div class="row g-2 mb-2">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                <div class="stat-label"><i class="bi bi-calendar-event"></i> Total Trips</div>
                <div class="stat-value"><?= $trips['total_trips'] ?></div>
                <div class="stat-sub"><?= esc($periodLabel) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-label"><i class="bi bi-clock-history"></i> Pending Dispatch</div>
                <div class="stat-value"><?= $trips['pending_dispatch'] ?></div>
                <div class="stat-sub">Awaiting assignment</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-play-circle"></i></div>
                <div class="stat-label"><i class="bi bi-play-circle"></i> Active Trips</div>
                <div class="stat-value"><?= $trips['active_trips'] ?></div>
                <div class="stat-sub">Currently on road</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-label"><i class="bi bi-check-circle-fill"></i> Completed Trips</div>
                <div class="stat-value"><?= $trips['completed_trips'] ?></div>
                <div class="stat-sub"><?= esc($periodLabel) ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (widget_visible('billing_stats')): ?>
    <!-- ============================================ -->
    <!-- BILLING & DELIVERIES -->
    <!-- ============================================ -->
    <div class="row g-2 mb-3">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-label"><i class="bi bi-box-seam"></i> Ongoing Trips</div>
                <div class="stat-value"><?= $billing['ongoing_trips'] ?></div>
                <div class="stat-sub">Not yet completed</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="stat-label"><i class="bi bi-file-earmark-text"></i> Pending DRs</div>
                <div class="stat-value"><?= $billing['pending_drs'] ?></div>
                <div class="stat-sub">Delivery receipts</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                <div class="stat-label"><i class="bi bi-file-earmark-arrow-up"></i> Pending Billing</div>
                <div class="stat-value"><?= $billing['pending_billing'] ?></div>
                <div class="stat-sub">Invoices to generate</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label"><i class="bi bi-cash-stack"></i> Outstanding Receivables</div>
                <div class="stat-value" style="font-size: 18px;">₱<?= number_format($billing['outstanding_ar'], 0) ?></div>
                <div class="stat-sub">Total AR balance</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (widget_visible('fuel_chart') || widget_visible('fuel_efficiency')): ?>
    <!-- ============================================ -->
    <!-- FUEL EXPENSE & EFFICIENCY -->
    <!-- ============================================ -->
    <div class="row g-3 mb-3">
        <?php if (widget_visible('fuel_chart')): ?>
        <div class="col-xl-8 col-lg-7">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-fuel-pump"></i> Monthly Fuel Expense
                    <span class="badge-count primary"><?= $filter_year ?></span>
                    <span class="badge-count success">₱<?= number_format(array_sum($monthlyFuel), 0) ?></span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="fuelChart"></canvas>
                </div>
                <div class="row g-1 mt-2">
                    <div class="col-6">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Total Fuel Cost (<?= $filter_year ?>)</div>
                        <div style="font-size: 14px; font-weight: 700; font-family: var(--mono); color: var(--text-primary);">₱<?= number_format(array_sum($monthlyFuel), 2) ?></div>
                    </div>
                    <div class="col-6">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Avg Monthly Cost</div>
                        <div style="font-size: 14px; font-weight: 700; font-family: var(--mono); color: var(--text-primary);">₱<?= number_format(array_sum($monthlyFuel) / 12, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (widget_visible('fuel_efficiency')): ?>
        <div class="col-xl-4 col-lg-5">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-speedometer2"></i> Fuel Efficiency
                    <span class="badge-count success"><?= esc($periodLabel) ?></span>
                </div>
                <div class="mb-2">
                    <div class="fuel-stat">
                        <span class="fuel-label">Fuel Cost per KM</span>
                        <span class="fuel-value"><?= $fuelEfficiency['cost_per_km'] !== null ? '₱' . number_format($fuelEfficiency['cost_per_km'], 2) : 'No data yet' ?></span>
                    </div>
                    <div class="fuel-stat">
                        <span class="fuel-label">Avg Fuel Consumption</span>
                        <span class="fuel-value"><?= $fuelEfficiency['consumption_rate'] !== null ? number_format($fuelEfficiency['consumption_rate'], 2) . ' L/km' : 'No data yet' ?></span>
                    </div>
                </div>

                <div style="font-size: 9px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                    <i class="bi bi-truck"></i> Fuel Cost by Truck
                </div>
                <div style="font-size: 10px;">
                    <?php if ($fuelEfficiency['by_truck']): ?>
                        <?php foreach ($fuelEfficiency['by_truck'] as $t): ?>
                        <div class="fuel-stat">
                            <span class="fuel-label"><?= esc($t['truck']) ?></span>
                            <span class="fuel-value">₱<?= number_format($t['total_cost'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-note">No fuel expenses logged yet</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (widget_visible('route_analysis') || widget_visible('service_analysis')): ?>
    <!-- ============================================ -->
    <!-- ROUTE & SERVICE TYPE ANALYSIS -->
    <!-- ============================================ -->
    <div class="row g-3 mb-3">
        <?php if (widget_visible('route_analysis')): ?>
        <div class="col-xl-6">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-signpost-2"></i> Most Frequent Routes
                    <span class="badge-count primary">Top 5</span>
                </div>
                <?php if ($routes): ?>
                    <?php foreach ($routes as $r): ?>
                    <div class="route-bar">
                        <span class="route-label"><?= esc($r['origin']) ?> → <?= esc($r['destination']) ?></span>
                        <div class="route-track">
                            <div class="route-fill" style="width: <?= $maxRouteCount > 0 ? round($r['total'] / $maxRouteCount * 100) : 0 ?>%;"><?= $r['total'] ?></div>
                        </div>
                        <span class="route-count"><?= $r['total'] ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-note">No trips recorded for this period</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (widget_visible('service_analysis')): ?>
        <div class="col-xl-6">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-bar-chart-steps"></i> Service Type Analysis
                    <span class="badge-count primary"><?= $filter_month === 'all' ? $filter_year : date('M', mktime(0,0,0,$filter_month,1)) . ' ' . $filter_year ?></span>
                </div>
                <?php if ($services): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Trips</th>
                                <th>%</th>
                                <th style="text-align: right;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $s): ?>
                            <tr>
                                <td><strong><?= esc($s['service_type']) ?></strong></td>
                                <td><?= $s['trips'] ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <div style="flex: 1; height: 6px; background: var(--bg-primary); border-radius: 3px; overflow: hidden;">
                                            <div style="width: <?= $s['percent'] ?>%; height: 100%; background: var(--accent); border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 10px; font-weight: 700; min-width: 35px; color: var(--text-primary);"><?= $s['percent'] ?>%</span>
                                    </div>
                                </td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱<?= number_format($s['revenue'], 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: var(--bg-primary); font-weight: 700; border-top: 2px solid var(--border-color);">
                                <td style="color: var(--text-primary);">Total</td>
                                <td style="color: var(--text-primary);"><?= array_sum(array_column($services, 'trips')) ?></td>
                                <td style="color: var(--text-primary);">100%</td>
                                <td style="font-family: var(--mono); text-align: right; color: var(--success); font-size: 12px;">₱<?= number_format($totalServiceRevenue, 0) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                    <div class="empty-note">No trips recorded for this period</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (widget_visible('customer_activity') || widget_visible('dashboard_insights')): ?>
    <!-- ============================================ -->
    <!-- CUSTOMER ACTIVITY & INSIGHTS -->
    <!-- ============================================ -->
    <div class="row g-3 mb-3">
        <?php if (widget_visible('customer_activity')): ?>
        <div class="col-xl-8">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-building"></i> Customer Activity & Performance
                    <span class="badge-count primary">Top 5</span>
                </div>
                <?php if ($customers): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Customer</th>
                                <th style="text-align:center;">Trips</th>
                                <th style="text-align:right;">Billed</th>
                                <th style="text-align:right;">Collected</th>
                                <th style="text-align:right;">Outstanding</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rankColors = ['var(--accent)', '#60a5fa', '#f59e0b', '#10b981', '#8b5cf6']; ?>
                            <?php foreach ($customers as $i => $c): ?>
                            <tr>
                                <td><span style="display: inline-block; width: 22px; height: 22px; background: <?= $rankColors[$i] ?? 'var(--accent)' ?>; color: #fff; border-radius: 50%; text-align: center; line-height: 22px; font-size: 10px; font-weight: 700;"><?= $i + 1 ?></span></td>
                                <td><strong style="color: var(--text-primary);"><?= esc($c['customer_name']) ?></strong></td>
                                <td style="text-align:center; font-weight:600;"><?= $c['trips'] ?></td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱<?= number_format($c['billed'], 0) ?></td>
                                <td style="font-family: var(--mono); text-align: right; font-size: 11px; color: var(--text-secondary);">₱<?= number_format($c['collected'], 0) ?></td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: <?= $c['outstanding'] > 0 ? 'var(--warning)' : 'var(--success)' ?>;">₱<?= number_format($c['outstanding'], 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="empty-note">No customer billing activity for this period</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (widget_visible('dashboard_insights')): ?>
        <div class="col-xl-4">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-award"></i> Dashboard Insights
                    <span class="badge-count success">Highlights</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 3px;">
                    <div class="insight-item">
                        <span class="label">🏆 Top Customer</span>
                        <span class="value"><?= esc($insights['top_customer'] ?? '—') ?></span>
                    </div>
                    <div class="insight-item">
                        <span class="label">📍 Most Frequent Route</span>
                        <span class="value"><?= $insights['most_frequent_route'] ? esc($insights['most_frequent_route']['origin']) . ' → ' . esc($insights['most_frequent_route']['destination']) : '—' ?></span>
                    </div>
                    <div class="insight-item info">
                        <span class="label">📦 Most Common Service</span>
                        <span class="value"><?= esc($insights['most_common_service'] ?? '—') ?></span>
                    </div>
                    <div class="insight-item">
                        <span class="label">⛽ Fuel Cost</span>
                        <span class="value">₱<?= number_format($insights['period_fuel_cost'], 0) ?></span>
                    </div>
                    <div class="insight-item warning">
                        <span class="label">💰 Outstanding Receivables</span>
                        <span class="value">₱<?= number_format($insights['outstanding_ar'], 0) ?></span>
                    </div>
                    <div class="insight-item danger">
                        <span class="label">⚠️ Upcoming Maintenance</span>
                        <span class="value"><?= $insights['upcoming_maintenance'] ?> Scheduled</span>
                    </div>
                    <div class="insight-item">
                        <span class="label">📄 Expiring Documents</span>
                        <span class="value"><?= $insights['expiring_documents'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    <?php if (widget_visible('fuel_chart')): ?>
    // =============================================
    // FUEL EXPENSE CHART (live data)
    // =============================================
    const fuelCtx = document.getElementById('fuelChart');
    if (fuelCtx) {
        new Chart(fuelCtx, {
            type: 'bar',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [{
                    label: 'Fuel Cost (₱)',
                    data: <?= json_encode(array_values($monthlyFuel)) ?>,
                    backgroundColor: 'rgba(26, 107, 176, 0.6)',
                    borderColor: '#1a6bb0',
                    borderWidth: 2,
                    borderRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 10, padding: 6, font: { size: 9, weight: '600' }, color: '#4a5568', usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        callbacks: { label: function(context) { return '₱' + context.parsed.y.toLocaleString(); } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { font: { size: 8 }, color: '#718096', callback: function(value) { return '₱' + value; } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 8 }, color: '#718096' }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // =============================================
    // LIVE CLOCK UPDATE
    // =============================================
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const clockElement = document.getElementById('liveClock');
        if (clockElement) {
            clockElement.textContent = timeStr;
        }
    }
    updateClock();
    setInterval(updateClock, 30000);
</script>

<?php echo view('templates/myfooter.php'); ?>
