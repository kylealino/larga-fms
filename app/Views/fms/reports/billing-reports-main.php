<?php
echo view('templates/myheader.php');
?>
<style>
    :root {
        --primary: #1a6bb0; --primary-dark: #0f5a99; --primary-light: #e8f2fa;
        --danger: #dc2626; --danger-dark: #b91c1c; --success: #10b981; --warning: #f59e0b; --info: #3b82f6;
        --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0; --gray-300: #cbd5e1;
        --gray-400: #94a3b8; --gray-500: #64748b; --gray-600: #475569; --gray-700: #334155; --gray-800: #1e293b;
        --shadow: 0 1px 3px rgba(0,0,0,0.06); --shadow-md: 0 4px 12px rgba(0,0,0,0.05); --shadow-lg: 0 8px 24px rgba(0,0,0,0.08);
    }
    body { background: var(--gray-50); }
    .lrg-module-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 14px 24px; margin: -24px -24px 24px -24px; display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .lrg-module-header .header-left { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 200px; }
    .lrg-module-header .header-left .module-icon {
        width: 40px; height: 40px; background: rgba(255,255,255,0.12); border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 20px; color: #90cdf4; border: 1px solid rgba(255,255,255,0.08);
    }
    .lrg-module-header .header-left .module-info h4 { font-size: 17px; font-weight: 600; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .lrg-module-header .header-left .module-info h4 .module-badge {
        font-size: 9px; font-weight: 600; padding: 2px 12px; border-radius: 4px; background: rgba(255,255,255,0.12);
        color: #bee3f8; border: 1px solid rgba(255,255,255,0.08); text-transform: uppercase; letter-spacing: 0.5px;
    }
    .header-actions { display: flex; align-items: center; gap: 10px; }
    .btn-header {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: #ffffff; padding: 6px 16px;
        border-radius: 6px; font-size: 12px; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
    }
    .btn-header:hover { background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.25); color: #ffffff; transform: translateY(-1px); }

    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--gray-200); padding: 16px 20px;
        transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between;
        box-shadow: var(--shadow); position: relative; overflow: hidden;
    }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--primary), var(--primary-light)); opacity: 0; transition: opacity 0.3s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--gray-300); }
    .stat-card:hover::before { opacity: 1; }
    .stat-left .stat-label { font-size: 10px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .stat-left .stat-value { font-size: 18px; font-weight: 700; color: var(--gray-800); line-height: 1.2; }
    .stat-left .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    .stat-right { font-size: 30px; color: var(--primary); opacity: 0.08; line-height: 1; }

    .card { border-radius: 12px; border: 1px solid var(--gray-200); background: #ffffff; box-shadow: var(--shadow); margin-bottom: 20px; }
    .card-header { background: #ffffff; border-bottom: 1px solid var(--gray-200); padding: 14px 20px; border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .card-header h6 { font-size: 13px; font-weight: 600; margin: 0; color: var(--gray-700); }
    .card-body { padding: 20px; }

    .table-wrap { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .table thead th { font-size: 10px; font-weight: 600; color: var(--gray-500); background: var(--gray-50); border-bottom: 1.5px solid var(--gray-200); padding: 10px 12px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; white-space: nowrap; }
    .table tbody td { font-size: 12px; color: var(--gray-700); padding: 10px 12px; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
    .table-hover tbody tr:hover { background: var(--gray-50); }

    .badge { font-size: 10px; font-weight: 600; padding: 3px 12px; border-radius: 20px; letter-spacing: 0.3px; display: inline-block; }
    .badge-success { background: var(--success); color: #ffffff; }
    .badge-danger { background: var(--danger); color: #ffffff; }
    .badge-warning { background: var(--warning); color: #ffffff; }
    .badge-primary { background: var(--primary); color: #ffffff; }
    .badge-secondary { background: var(--gray-500); color: #ffffff; }
    .badge-info { background: var(--info); color: #ffffff; }

    .btn-toolbar { padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; border: 1px solid var(--gray-200); background: #ffffff; color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-toolbar-primary { background: var(--primary); color: #ffffff; border-color: var(--primary); }
    .btn-toolbar-primary:hover { background: var(--primary-dark); color: #ffffff; }

    .report-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
    .report-tab {
        padding: 8px 18px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1.5px solid var(--gray-200);
        background: #ffffff; color: var(--gray-600); cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
    }
    .report-tab:hover { border-color: var(--primary); color: var(--primary); }
    .report-tab.active { background: var(--primary); border-color: var(--primary); color: #ffffff; }

    .filter-bar { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px; margin-bottom: 20px; background: #ffffff; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px 20px; box-shadow: var(--shadow); }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 10px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-group input, .filter-group select { padding: 6px 12px; border: 1.5px solid var(--gray-200); border-radius: 8px; font-size: 12px; outline: none; min-width: 160px; }
    .filter-group input:focus, .filter-group select:focus { border-color: var(--primary); }
    .filter-actions { display: flex; gap: 8px; margin-left: auto; }

    .dataTables_wrapper { font-family: 'Inter', sans-serif; }
    .dataTables_filter { float: right; margin-bottom: 16px; }
    .dataTables_filter label { font-size: 12px; font-weight: 500; color: var(--gray-500); display: flex; align-items: center; gap: 8px; }
    .dataTables_filter input { width: 200px; padding: 6px 12px; border: 1.5px solid var(--gray-200); border-radius: 8px; font-size: 12px; transition: all 0.2s; outline: none; }
    .dataTables_paginate { float: right; margin-top: 16px; }
    .dataTables_paginate .paginate_button { padding: 4px 10px !important; margin: 0 2px !important; border-radius: 6px !important; border: 1px solid var(--gray-200) !important; background: #ffffff !important; color: var(--gray-600) !important; font-size: 11px !important; font-weight: 600 !important; }
    .dataTables_paginate .paginate_button.current { background: var(--primary) !important; border-color: var(--primary) !important; color: #ffffff !important; }
    .dataTables_info { float: left; font-size: 12px; color: var(--gray-500); margin-top: 16px; }

    @media (max-width: 992px) {
        .lrg-module-header { flex-direction: column; align-items: stretch; padding: 16px 20px; }
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .filter-actions { margin-left: 0; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="me-trp-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon"><i class="bi bi-cash-coin"></i></div>
        <div class="module-info">
            <h4>
                Billing Reports
                <span class="module-badge">Billing & AR</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- REPORT TABS -->
<!-- ============================================ -->
<div class="report-tabs">
    <div class="report-tab active" data-type="billing_summary"><i class="bi bi-receipt"></i> Billing Summary</div>
    <div class="report-tab" data-type="invoice_report"><i class="bi bi-file-earmark-text"></i> Invoice Report</div>
    <div class="report-tab" data-type="payment_report"><i class="bi bi-credit-card"></i> Payment Report</div>
    <div class="report-tab" data-type="accounts_receivable"><i class="bi bi-cash-stack"></i> Accounts Receivable</div>
    <div class="report-tab" data-type="ar_aging"><i class="bi bi-pie-chart"></i> AR Aging</div>
    <div class="report-tab" data-type="statement_of_account"><i class="bi bi-box-arrow-up-right"></i> Statement of Account</div>
</div>

<!-- ============================================ -->
<!-- FILTER BAR -->
<!-- ============================================ -->
<div class="filter-bar">
    <div class="filter-group">
        <label>Date From</label>
        <input type="date" id="filterDateFrom" value="<?=date('Y-m-01');?>" />
    </div>
    <div class="filter-group">
        <label>Date To</label>
        <input type="date" id="filterDateTo" value="<?=date('Y-m-d');?>" />
    </div>
    <div class="filter-group">
        <label>Customer</label>
        <select id="filterCustomer">
            <option value="">All Customers</option>
        </select>
    </div>
    <div class="filter-group" id="filterStatusWrap" style="display:none;">
        <label>Status</label>
        <select id="filterStatus">
            <option value="">All Statuses</option>
        </select>
    </div>
    <div class="filter-actions">
        <button class="btn-toolbar btn-toolbar-primary" onclick="__BillingReports.__applyFilters();">
            <i class="bi bi-funnel"></i> Apply Filters
        </button>
        <button class="btn-toolbar" onclick="__BillingReports.__printPDF();">
            <i class="bi bi-printer"></i> Print PDF
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STAT CARDS -->
<!-- ============================================ -->
<div class="stat-grid" id="statGrid"></div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Report Results</h6>
            </div>
            <div class="card-body">
                <div class="table-wrap">
                    <table id="reportTable" class="table table-hover">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
<script>
window.addEventListener('load', function() {
    var s = document.createElement('script');
    s.src = '<?=base_url('assets/js/apps/fms/reports/billing-reports.js');?>';
    document.body.appendChild(s);
});
</script>

<?php
echo view('templates/myfooter.php');
?>
