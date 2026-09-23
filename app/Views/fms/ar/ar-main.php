<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$receivables = $this->db->query("
    SELECT i.invoice_id, i.invoice_code, i.invoice_date, i.due_date,
           i.total_amount, i.amount_paid, i.outstanding_balance, i.invoice_status,
           c.customer_id, c.customer_name,
           DATEDIFF(CURDATE(), i.due_date) as days_overdue,
           CASE
               WHEN i.due_date IS NULL OR DATEDIFF(CURDATE(), i.due_date) <= 0 THEN 'CURRENT'
               WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 1 AND 30 THEN 'DAYS_1_30'
               WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 31 AND 60 THEN 'DAYS_31_60'
               WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 61 AND 90 THEN 'DAYS_61_90'
               WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 91 AND 120 THEN 'DAYS_91_120'
               ELSE 'OVER_120'
           END as aging_bucket
    FROM tbl_invoices i
    LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
    WHERE i.outstanding_balance > 0
      AND i.invoice_status NOT IN ('CANCELLED','VOID')
    ORDER BY i.due_date ASC
")->getResultArray();

// ==============================
// AGING TOTALS
// ==============================
$agingTotals = ['CURRENT' => 0, 'DAYS_1_30' => 0, 'DAYS_31_60' => 0, 'DAYS_61_90' => 0, 'DAYS_91_120' => 0, 'OVER_120' => 0];
$total_ar = 0;
foreach ($receivables as $row) {
    $agingTotals[$row['aging_bucket']] += $row['outstanding_balance'];
    $total_ar += $row['outstanding_balance'];
}

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

    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--gray-200); padding: 16px 20px;
        transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between;
        box-shadow: var(--shadow); position: relative; overflow: hidden; cursor: pointer;
    }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--primary), var(--primary-light)); opacity: 0; transition: opacity 0.3s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--gray-300); }
    .stat-card:hover::before { opacity: 1; }
    .stat-card.active { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(26,107,176,0.15), var(--shadow-md); }
    .stat-card.active::before { opacity: 1; }
    .stat-left .stat-label { font-size: 10px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .stat-left .stat-value { font-size: 20px; font-weight: 700; color: var(--gray-800); line-height: 1.2; }
    .stat-left .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    .stat-right { font-size: 32px; color: var(--primary); opacity: 0.08; line-height: 1; }
    .stat-card.aging-warn .stat-left .stat-value { color: var(--warning); }
    .stat-card.aging-danger .stat-left .stat-value { color: var(--danger); }

    .card { border-radius: 12px; border: 1px solid var(--gray-200); background: #ffffff; box-shadow: var(--shadow); margin-bottom: 20px; }
    .card-header { background: #ffffff; border-bottom: 1px solid var(--gray-200); padding: 14px 20px; border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: space-between; }
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

    .action-group { display: flex; align-items: center; gap: 4px; justify-content: center; }
    .btn-icon { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px; border: 1px solid transparent; background: transparent; cursor: pointer; transition: all 0.2s; color: var(--gray-500); font-size: 14px; }
    .btn-icon:hover { transform: scale(1.05); }
    .btn-icon-view { color: var(--info); } .btn-icon-view:hover { background: #dbeafe; border-color: #93c5fd; }
    .btn-icon-dispatch { color: var(--success); } .btn-icon-dispatch:hover { background: #d1fae5; border-color: #6ee7b7; }

    .btn-toolbar { padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; border: 1px solid var(--gray-200); background: #ffffff; color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .toolbar-left { display: flex; align-items: center; gap: 8px; }

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
    }
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .stat-card { padding: 14px 16px; }
        .toolbar { flex-direction: column; align-items: stretch; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
        .btn-icon { width: 28px; height: 28px; font-size: 13px; }
    }
</style>

<div class="me-trp-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon"><i class="bi bi-pie-chart"></i></div>
        <div class="module-info">
            <h4>
                Accounts Receivable
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
<!-- AGING STAT CARDS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Receivables</div>
            <div class="stat-value" style="font-size:18px;">&#8369;<?=number_format($total_ar, 2);?></div>
            <div class="stat-sub"><?=count($receivables);?> open invoices</div>
        </div>
        <div class="stat-right"><i class="bi bi-cash-stack"></i></div>
    </div>
    <div class="stat-card" data-filter="CURRENT" onclick="filterTable('CURRENT')">
        <div class="stat-left">
            <div class="stat-label">Current</div>
            <div class="stat-value" style="font-size:16px;">&#8369;<?=number_format($agingTotals['CURRENT'], 2);?></div>
            <div class="stat-sub">Not yet due</div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
    </div>
    <div class="stat-card" data-filter="DAYS_1_30" onclick="filterTable('DAYS_1_30')">
        <div class="stat-left">
            <div class="stat-label">1&ndash;30 Days</div>
            <div class="stat-value" style="font-size:16px;">&#8369;<?=number_format($agingTotals['DAYS_1_30'], 2);?></div>
            <div class="stat-sub">Past due</div>
        </div>
        <div class="stat-right"><i class="bi bi-clock"></i></div>
    </div>
    <div class="stat-card aging-warn" data-filter="DAYS_31_60" onclick="filterTable('DAYS_31_60')">
        <div class="stat-left">
            <div class="stat-label">31&ndash;60 Days</div>
            <div class="stat-value" style="font-size:16px;">&#8369;<?=number_format($agingTotals['DAYS_31_60'], 2);?></div>
            <div class="stat-sub">Past due</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-circle"></i></div>
    </div>
    <div class="stat-card aging-warn" data-filter="DAYS_61_90" onclick="filterTable('DAYS_61_90')">
        <div class="stat-left">
            <div class="stat-label">61&ndash;90 Days</div>
            <div class="stat-value" style="font-size:16px;">&#8369;<?=number_format($agingTotals['DAYS_61_90'], 2);?></div>
            <div class="stat-sub">Past due</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-circle"></i></div>
    </div>
    <div class="stat-card aging-danger" data-filter="DAYS_91_120" onclick="filterTable('DAYS_91_120')">
        <div class="stat-left">
            <div class="stat-label">91&ndash;120 Days</div>
            <div class="stat-value" style="font-size:16px;">&#8369;<?=number_format($agingTotals['DAYS_91_120'], 2);?></div>
            <div class="stat-sub">Seriously overdue</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
    <div class="stat-card aging-danger" data-filter="OVER_120" onclick="filterTable('OVER_120')">
        <div class="stat-left">
            <div class="stat-label">Over 120 Days</div>
            <div class="stat-value" style="font-size:16px;">&#8369;<?=number_format($agingTotals['OVER_120'], 2);?></div>
            <div class="stat-sub">Critical</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Open Receivables</h6>
                <span class="badge badge-secondary" id="recordCount"><?=count($receivables);?> records</span>
            </div>
            <div class="card-body">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button class="btn-toolbar" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="arTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th width="110">Aging</th>
                                <th width="110" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $agingLabels = [
                                'CURRENT' => ['Current', 'badge-success'],
                                'DAYS_1_30' => ['1-30 Days', 'badge-info'],
                                'DAYS_31_60' => ['31-60 Days', 'badge-warning'],
                                'DAYS_61_90' => ['61-90 Days', 'badge-warning'],
                                'DAYS_91_120' => ['91-120 Days', 'badge-danger'],
                                'OVER_120' => ['Over 120', 'badge-danger'],
                            ];
                            ?>
                                <?php foreach($receivables as $row): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['invoice_code'];?></span></td>
                                    <td><?=$row['customer_name'] ?? '—';?></td>
                                    <td><?=date('M d, Y', strtotime($row['invoice_date']));?></td>
                                    <td><?=$row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '—';?></td>
                                    <td class="text-end">&#8369;<?=number_format($row['total_amount'], 2);?></td>
                                    <td class="text-end">&#8369;<?=number_format($row['amount_paid'], 2);?></td>
                                    <td class="text-end"><strong>&#8369;<?=number_format($row['outstanding_balance'], 2);?></strong></td>
                                    <td>
                                        <?php list($label, $cls) = $agingLabels[$row['aging_bucket']]; ?>
                                        <span class="badge <?=$cls;?>"><?=$label;?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view"
                                                    onclick="location.href='<?=site_url('invoice');?>'"
                                                    title="View in Invoices">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-dispatch"
                                                    onclick="location.href='<?=site_url('payment');?>'"
                                                    title="Record Payment">
                                                <i class="bi bi-credit-card"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
var arTable;

$(document).ready(function () {
    arTable = $('#arTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[3, 'asc']],
        language: { search: "Search:", emptyTable: "No open receivables — everything is settled" },
        columnDefs: [{ orderable: false, targets: [8] }]
    });
});

function filterTable(bucket) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + bucket + '"]').addClass('active');

    var columnIndex = 7;

    if (bucket === 'all') {
        arTable.column(columnIndex).search('', true, false).draw();
    } else {
        var labelMap = {
            'CURRENT'      : '^Current$',
            'DAYS_1_30'    : '^1-30 Days$',
            'DAYS_31_60'   : '^31-60 Days$',
            'DAYS_61_90'   : '^61-90 Days$',
            'DAYS_91_120'  : '^91-120 Days$',
            'OVER_120'     : '^Over 120$'
        };
        arTable.column(columnIndex).search(labelMap[bucket] || bucket, true, false).draw();
    }
}
</script>

<?php
echo view('templates/myfooter.php');
?>
