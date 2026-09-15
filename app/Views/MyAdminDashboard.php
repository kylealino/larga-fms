<?php
// =============================================
// FLEET MANAGEMENT - OPERATIONS DASHBOARD
// =============================================

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
                    <span class="real-time-badge">Real-time</span>
                </h2>
                <div class="user-meta">
                    <span><i class="bi bi-person-circle"></i> Juan Dela Cruz</span>
                    <span><i class="bi bi-building"></i> Operations Manager</span>
                    <span><i class="bi bi-calendar3"></i> <?= date('M d, Y') ?></span>
                    <span class="live-clock"><i class="bi bi-clock"></i> <span id="liveClock"><?= date('h:i A') ?></span></span>
                </div>
            </div>
        </div>

        <!-- FILTER -->
        <form method="GET" action="" class="header-filter" id="filterForm">
            <div class="filter-group">
                <label for="filter_date">Date</label>
                <input type="date" name="filter_date" id="filter_date" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="filter-group">
                <label for="filter_month">Month</label>
                <select name="filter_month" id="filter_month">
                    <option value="all">All</option>
                    <option value="01">Jan</option><option value="02">Feb</option>
                    <option value="03">Mar</option><option value="04">Apr</option>
                    <option value="05">May</option><option value="06">Jun</option>
                    <option value="07" selected>Jul</option>
                    <option value="08">Aug</option><option value="09">Sep</option>
                    <option value="10">Oct</option><option value="11">Nov</option>
                    <option value="12">Dec</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filter_year">Year</label>
                <select name="filter_year" id="filter_year">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026" selected>2026</option>
                </select>
            </div>
            <button type="submit" class="btn-filter-header"><i class="bi bi-check2"></i> Apply</button>
            <a href="#" class="btn-reset-header" onclick="document.getElementById('filterForm').reset(); return false;"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </form>
    </div>

    <!-- ============================================ -->
    <!-- OPERATIONAL KPI SUMMARY - COMPACT -->
    <!-- ============================================ -->
    <div class="row g-2 mb-2">
        <!-- Row 1: Fleet Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-truck"></i></div>
                <div class="stat-label"><i class="bi bi-truck"></i> Total Trucks</div>
                <div class="stat-value">25</div>
                <div class="stat-sub">Fleet size</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-label"><i class="bi bi-check-circle"></i> Available Trucks</div>
                <div class="stat-value">14 <span class="trend up">↑2</span></div>
                <div class="stat-sub"><span class="highlight">56%</span> of fleet</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-arrow-right"></i></div>
                <div class="stat-label"><i class="bi bi-arrow-right"></i> In Transit</div>
                <div class="stat-value">5 <span class="trend down">↓1</span></div>
                <div class="stat-sub"><span class="highlight">20%</span> on road</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-tools"></i></div>
                <div class="stat-label"><i class="bi bi-tools"></i> Maintenance</div>
                <div class="stat-value">2</div>
                <div class="stat-sub"><span class="highlight">8%</span> of fleet</div>
            </div>
        </div>
    </div>

    <!-- Row 2: Personnel Stats -->
    <div class="row g-2 mb-2">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                <div class="stat-label"><i class="bi bi-person-badge"></i> Total Drivers</div>
                <div class="stat-value">30</div>
                <div class="stat-sub">Active drivers</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                <div class="stat-label"><i class="bi bi-person-check"></i> Available Drivers</div>
                <div class="stat-value">18 <span class="trend up">↑3</span></div>
                <div class="stat-sub"><span class="highlight">60%</span> available</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-plus"></i></div>
                <div class="stat-label"><i class="bi bi-person-plus"></i> Total Helpers</div>
                <div class="stat-value">25</div>
                <div class="stat-sub">Active helpers</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                <div class="stat-label"><i class="bi bi-person-check"></i> Available Helpers</div>
                <div class="stat-value">15</div>
                <div class="stat-sub"><span class="highlight">60%</span> available</div>
            </div>
        </div>
    </div>

    <!-- Row 3: Trip Stats -->
    <div class="row g-2 mb-2">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                <div class="stat-label"><i class="bi bi-calendar-event"></i> Trips Today</div>
                <div class="stat-value">12 <span class="trend up">↑3</span></div>
                <div class="stat-sub">vs <span class="highlight">9</span> yesterday</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-label"><i class="bi bi-clock-history"></i> Pending Dispatch</div>
                <div class="stat-value">4</div>
                <div class="stat-sub">Awaiting assignment</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-play-circle"></i></div>
                <div class="stat-label"><i class="bi bi-play-circle"></i> Active Trips</div>
                <div class="stat-value">6</div>
                <div class="stat-sub">Currently on road</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-label"><i class="bi bi-check-circle-fill"></i> Completed Trips</div>
                <div class="stat-value">8 <span class="trend up">↑2</span></div>
                <div class="stat-sub">Today's completions</div>
            </div>
        </div>
    </div>

    <!-- Row 4: Billing & Maintenance -->
    <div class="row g-2 mb-3">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-label"><i class="bi bi-box-seam"></i> Pending Deliveries</div>
                <div class="stat-value">5</div>
                <div class="stat-sub">Awaiting delivery</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="stat-label"><i class="bi bi-file-earmark-text"></i> Pending DRs</div>
                <div class="stat-value">3</div>
                <div class="stat-sub">Delivery receipts</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                <div class="stat-label"><i class="bi bi-file-earmark-arrow-up"></i> Pending Billing</div>
                <div class="stat-value">7</div>
                <div class="stat-sub">Invoices to generate</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label"><i class="bi bi-cash-stack"></i> Outstanding Receivables</div>
                <div class="stat-value" style="font-size: 18px;">₱485,000</div>
                <div class="stat-sub">Total AR balance</div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MONTHLY FUEL CONSUMPTION & ANALYSIS -->
    <!-- ============================================ -->
    <div class="row g-3 mb-3">
        <div class="col-xl-8 col-lg-7">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-fuel-pump"></i> Monthly Fuel Consumption
                    <span class="badge-count primary">2026</span>
                    <span class="badge-count success">8,700 L</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="fuelChart"></canvas>
                </div>
                <div class="row g-1 mt-2">
                    <div class="col-4">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Total Fuel</div>
                        <div style="font-size: 14px; font-weight: 700; font-family: var(--mono); color: var(--text-primary);">8,700 L</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Total Cost</div>
                        <div style="font-size: 14px; font-weight: 700; font-family: var(--mono); color: var(--text-primary);">₱539,400</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Avg KM/L</div>
                        <div style="font-size: 14px; font-weight: 700; font-family: var(--mono); color: var(--text-primary);">4.8</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-speedometer2"></i> Fuel Efficiency
                    <span class="badge-count success">Monthly</span>
                </div>
                <div class="mb-2">
                    <div class="fuel-stat">
                        <span class="fuel-label">Fuel Cost per KM</span>
                        <span class="fuel-value">₱2.15</span>
                    </div>
                    <div class="fuel-stat">
                        <span class="fuel-label">Avg Fuel Consumption</span>
                        <span class="fuel-value">2.8 L/km</span>
                    </div>
                </div>

                <div style="font-size: 9px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                    <i class="bi bi-truck"></i> Fuel by Truck
                </div>
                <div style="font-size: 10px;">
                    <div class="fuel-stat">
                        <span class="fuel-label">TRK-001</span>
                        <span class="fuel-value">1,450 L</span>
                    </div>
                    <div class="fuel-stat">
                        <span class="fuel-label">TRK-002</span>
                        <span class="fuel-value">1,280 L</span>
                    </div>
                    <div class="fuel-stat">
                        <span class="fuel-label">TRK-003</span>
                        <span class="fuel-value">1,190 L</span>
                    </div>
                    <div class="fuel-stat">
                        <span class="fuel-label">TRK-004</span>
                        <span class="fuel-value">980 L</span>
                    </div>
                    <div class="fuel-stat">
                        <span class="fuel-label">TRK-005</span>
                        <span class="fuel-value">750 L</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- COMMON ROUTE ANALYSIS -->
    <!-- ============================================ -->
    <div class="row g-3 mb-3">
        <div class="col-xl-6">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-signpost-2"></i> Most Frequent Routes
                    <span class="badge-count primary">Top 5</span>
                </div>
                <div class="route-bar">
                    <span class="route-label">Laguna → Manila</span>
                    <div class="route-track">
                        <div class="route-fill" style="width: 85%;">45</div>
                    </div>
                    <span class="route-count">45</span>
                </div>
                <div class="route-bar">
                    <span class="route-label">Cavite → Manila</span>
                    <div class="route-track">
                        <div class="route-fill" style="width: 70%;">32</div>
                    </div>
                    <span class="route-count">32</span>
                </div>
                <div class="route-bar">
                    <span class="route-label">Manila → Batangas</span>
                    <div class="route-track">
                        <div class="route-fill" style="width: 55%;">28</div>
                    </div>
                    <span class="route-count">28</span>
                </div>
                <div class="route-bar">
                    <span class="route-label">Laguna → Cavite</span>
                    <div class="route-track">
                        <div class="route-fill" style="width: 40%;">21</div>
                    </div>
                    <span class="route-count">21</span>
                </div>
                <div class="route-bar">
                    <span class="route-label">Batangas → Manila</span>
                    <div class="route-track">
                        <div class="route-fill" style="width: 30%;">18</div>
                    </div>
                    <span class="route-count">18</span>
                </div>

                <div class="row g-1 mt-2">
                    <div class="col-4">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Most Frequent Origin</div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-primary);">Laguna</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Most Frequent Destination</div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-primary);">Manila</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size: 9px; font-weight: 600; color: var(--text-muted);">Avg Distance</div>
                        <div style="font-size: 12px; font-weight: 700; font-family: var(--mono); color: var(--text-primary);">85 km</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-bar-chart-steps"></i> Shipment & Cargo Analysis
                    <span class="badge-count primary">YTD</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Shipment Type</th>
                                <th>Trips</th>
                                <th>%</th>
                                <th style="text-align: right;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>FTL</strong></td>
                                <td>85</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <div style="flex: 1; height: 6px; background: var(--bg-primary); border-radius: 3px; overflow: hidden;">
                                            <div style="width: 42%; height: 100%; background: var(--accent); border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 10px; font-weight: 700; min-width: 35px; color: var(--text-primary);">42%</span>
                                    </div>
                                </td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱675K</td>
                            </tr>
                            <tr>
                                <td><strong>Containerized</strong></td>
                                <td>60</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <div style="flex: 1; height: 6px; background: var(--bg-primary); border-radius: 3px; overflow: hidden;">
                                            <div style="width: 30%; height: 100%; background: #60a5fa; border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 10px; font-weight: 700; min-width: 35px; color: var(--text-primary);">30%</span>
                                    </div>
                                </td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱480K</td>
                            </tr>
                            <tr>
                                <td><strong>General Cargo</strong></td>
                                <td>35</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <div style="flex: 1; height: 6px; background: var(--bg-primary); border-radius: 3px; overflow: hidden;">
                                            <div style="width: 17%; height: 100%; background: #f59e0b; border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 10px; font-weight: 700; min-width: 35px; color: var(--text-primary);">17%</span>
                                    </div>
                                </td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱280K</td>
                            </tr>
                            <tr>
                                <td><strong>LTL</strong></td>
                                <td>22</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <div style="flex: 1; height: 6px; background: var(--bg-primary); border-radius: 3px; overflow: hidden;">
                                            <div style="width: 11%; height: 100%; background: #10b981; border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 10px; font-weight: 700; min-width: 35px; color: var(--text-primary);">11%</span>
                                    </div>
                                </td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱176K</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="background: var(--bg-primary); font-weight: 700; border-top: 2px solid var(--border-color);">
                                <td style="color: var(--text-primary);">Total</td>
                                <td style="color: var(--text-primary);">202</td>
                                <td style="color: var(--text-primary);">100%</td>
                                <td style="font-family: var(--mono); text-align: right; color: var(--success); font-size: 12px;">₱1,611K</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div style="font-size: 9px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">
                    <i class="bi bi-info-circle"></i> Most Used: 40ft Container · Most Common: FTL
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- CUSTOMER ACTIVITY & PERFORMANCE -->
    <!-- ============================================ -->
    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-building"></i> Customer Activity & Performance
                    <span class="badge-count primary">Top 5</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Customer</th>
                                <th style="text-align:center;">Trips</th>
                                <th style="text-align:center;">Growth</th>
                                <th style="text-align:right;">Billed</th>
                                <th style="text-align:right;">Collected</th>
                                <th style="text-align:right;">Outstanding</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="display: inline-block; width: 22px; height: 22px; background: var(--accent); color: #fff; border-radius: 50%; text-align: center; line-height: 22px; font-size: 10px; font-weight: 700;">1</span></td>
                                <td><strong style="color: var(--text-primary);">ABC Manufacturing</strong></td>
                                <td style="text-align:center; font-weight:600;">45</td>
                                <td style="text-align:center;"><span class="trend up" style="font-size: 10px; font-weight: 700;">↑25%</span></td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱675K</td>
                                <td style="font-family: var(--mono); text-align: right; font-size: 11px; color: var(--text-secondary);">₱600K</td>
                                <td style="font-family: var(--mono); font-weight: 700; color: var(--warning); text-align: right; font-size: 11px;">₱75K</td>
                            </tr>
                            <tr>
                                <td><span style="display: inline-block; width: 22px; height: 22px; background: #60a5fa; color: #fff; border-radius: 50%; text-align: center; line-height: 22px; font-size: 10px; font-weight: 700;">2</span></td>
                                <td><strong style="color: var(--text-primary);">XYZ Trading</strong></td>
                                <td style="text-align:center; font-weight:600;">38</td>
                                <td style="text-align:center;"><span class="trend up" style="font-size: 10px; font-weight: 700;">↑11%</span></td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱570K</td>
                                <td style="font-family: var(--mono); text-align: right; font-size: 11px; color: var(--text-secondary);">₱570K</td>
                                <td style="font-family: var(--mono); font-weight: 700; color: var(--success); text-align: right; font-size: 11px;">₱0</td>
                            </tr>
                            <tr>
                                <td><span style="display: inline-block; width: 22px; height: 22px; background: #f59e0b; color: #fff; border-radius: 50%; text-align: center; line-height: 22px; font-size: 10px; font-weight: 700;">3</span></td>
                                <td><strong style="color: var(--text-primary);">DEF Logistics</strong></td>
                                <td style="text-align:center; font-weight:600;">31</td>
                                <td style="text-align:center;"><span style="font-size: 10px; font-weight: 700; color: var(--success);">⭐+70%</span></td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱465K</td>
                                <td style="font-family: var(--mono); text-align: right; font-size: 11px; color: var(--text-secondary);">₱300K</td>
                                <td style="font-family: var(--mono); font-weight: 700; color: var(--danger); text-align: right; font-size: 11px;">₱165K</td>
                            </tr>
                            <tr>
                                <td><span style="display: inline-block; width: 22px; height: 22px; background: #10b981; color: #fff; border-radius: 50%; text-align: center; line-height: 22px; font-size: 10px; font-weight: 700;">4</span></td>
                                <td><strong style="color: var(--text-primary);">GHI Enterprises</strong></td>
                                <td style="text-align:center; font-weight:600;">25</td>
                                <td style="text-align:center;"><span class="trend down" style="font-size: 10px; font-weight: 700;">↓5%</span></td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱375K</td>
                                <td style="font-family: var(--mono); text-align: right; font-size: 11px; color: var(--text-secondary);">₱350K</td>
                                <td style="font-family: var(--mono); font-weight: 700; color: var(--warning); text-align: right; font-size: 11px;">₱25K</td>
                            </tr>
                            <tr>
                                <td><span style="display: inline-block; width: 22px; height: 22px; background: #8b5cf6; color: #fff; border-radius: 50%; text-align: center; line-height: 22px; font-size: 10px; font-weight: 700;">5</span></td>
                                <td><strong style="color: var(--text-primary);">JKL Solutions</strong></td>
                                <td style="text-align:center; font-weight:600;">18</td>
                                <td style="text-align:center;"><span class="trend up" style="font-size: 10px; font-weight: 700;">↑8%</span></td>
                                <td style="font-family: var(--mono); font-weight: 700; text-align: right; font-size: 11px; color: var(--text-primary);">₱270K</td>
                                <td style="font-family: var(--mono); text-align: right; font-size: 11px; color: var(--text-secondary);">₱220K</td>
                                <td style="font-family: var(--mono); font-weight: 700; color: var(--warning); text-align: right; font-size: 11px;">₱50K</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-award"></i> Dashboard Insights
                    <span class="badge-count success">Highlights</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 3px;">
                    <div class="insight-item">
                        <span class="label">🏆 Top Customer</span>
                        <span class="value">ABC Manufacturing</span>
                    </div>
                    <div class="insight-item success">
                        <span class="label">⭐ Promising Client</span>
                        <span class="value">DEF Logistics</span>
                    </div>
                    <div class="insight-item">
                        <span class="label">📍 Most Frequent Route</span>
                        <span class="value">Laguna → Manila</span>
                    </div>
                    <div class="insight-item info">
                        <span class="label">📦 Most Common Shipment</span>
                        <span class="value">Full Truckload</span>
                    </div>
                    <div class="insight-item">
                        <span class="label">⛽ Monthly Fuel</span>
                        <span class="value">8,700 L</span>
                    </div>
                    <div class="insight-item warning">
                        <span class="label">💰 Outstanding Receivables</span>
                        <span class="value">₱485,000</span>
                    </div>
                    <div class="insight-item danger">
                        <span class="label">⚠️ Upcoming Maintenance</span>
                        <span class="value">5 Trucks</span>
                    </div>
                    <div class="insight-item">
                        <span class="label">📄 Expiring Documents</span>
                        <span class="value">3</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // =============================================
    // FUEL CONSUMPTION CHART
    // =============================================
    const fuelCtx = document.getElementById('fuelChart');
    if (fuelCtx) {
        new Chart(fuelCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Fuel (L)',
                    data: [8500, 8900, 9200, 8700, 8400, 8800, 9300, 9100, 8600, 8900, 8500, 8700],
                    backgroundColor: 'rgba(26, 107, 176, 0.6)',
                    borderColor: '#1a6bb0',
                    borderWidth: 2,
                    borderRadius: 3,
                    order: 1
                }, {
                    label: 'Cost (₱K)',
                    data: [527, 552, 570, 539, 521, 546, 577, 564, 533, 552, 527, 539],
                    type: 'line',
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.04)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1,
                    tension: 0.3,
                    fill: true,
                    order: 0,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 10,
                            padding: 6,
                            font: { size: 9, weight: '600' },
                            color: '#4a5568',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label === 'Fuel (L)') {
                                    return context.parsed.y + ' L';
                                } else {
                                    return '₱' + context.parsed.y + 'K';
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: {
                            font: { size: 8 },
                            color: '#718096',
                            callback: function(value) { return value + 'L'; }
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: {
                            font: { size: 8 },
                            color: '#f59e0b',
                            callback: function(value) { return '₱' + value + 'K'; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 8 },
                            color: '#718096'
                        }
                    }
                }
            }
        });
    }

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