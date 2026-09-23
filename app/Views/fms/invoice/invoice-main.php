<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("
    SELECT i.*, c.customer_name, b.billing_code,
           CASE
               WHEN i.invoice_status IN ('PAID','CANCELLED','VOID','DRAFT') THEN i.invoice_status
               WHEN i.due_date IS NOT NULL AND i.due_date < CURDATE() AND i.outstanding_balance > 0 THEN 'OVERDUE'
               ELSE i.invoice_status
           END as display_status
    FROM tbl_invoices i
    LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
    LEFT JOIN tbl_billing b ON i.billing_id = b.billing_id
    ORDER BY i.invoice_date DESC, i.invoice_id DESC
");
$invoices = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_invoices = count($invoices);
$total_draft = 0; $total_issued = 0; $total_partial = 0; $total_paid = 0; $total_overdue = 0;
foreach($invoices as $row) {
    if($row['display_status'] == 'DRAFT') $total_draft++;
    elseif($row['display_status'] == 'ISSUED') $total_issued++;
    elseif($row['display_status'] == 'PARTIALLY_PAID') $total_partial++;
    elseif($row['display_status'] == 'PAID') $total_paid++;
    elseif($row['display_status'] == 'OVERDUE') $total_overdue++;
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
    .btn-header-primary { background: #ffffff; border-color: #ffffff; color: var(--primary); }
    .btn-header-primary:hover { background: rgba(255,255,255,0.9); color: var(--primary-dark); }

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
    .stat-left .stat-value { font-size: 24px; font-weight: 700; color: var(--gray-800); line-height: 1.2; }
    .stat-left .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    .stat-right { font-size: 32px; color: var(--primary); opacity: 0.08; line-height: 1; }

    .card { border-radius: 12px; border: 1px solid var(--gray-200); background: #ffffff; box-shadow: var(--shadow); margin-bottom: 20px; }
    .card-header { background: #ffffff; border-bottom: 1px solid var(--gray-200); padding: 14px 20px; border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: space-between; }
    .card-header h6 { font-size: 13px; font-weight: 600; margin: 0; color: var(--gray-700); }
    .card-body { padding: 20px; }

    .form-label { font-size: 11px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; display: block; text-transform: uppercase; letter-spacing: 0.3px; }
    .form-label .required { color: var(--danger); margin-left: 2px; }
    .form-control, select.form-control {
        border: 1.5px solid var(--gray-200); border-radius: 8px; padding: 9px 12px; font-size: 13px;
        color: var(--gray-700); background: #ffffff; transition: all 0.2s; width: 100%; height: 40px;
    }
    .form-control:focus, select.form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,107,176,0.08); }
    .form-control[readonly] { background: var(--gray-50); cursor: not-allowed; }
    textarea.form-control { height: auto; min-height: 60px; resize: vertical; }

    .btn-primary { background: var(--primary); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600; transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,107,176,0.3); color: #ffffff; }
    .btn-danger { background: var(--danger); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600; transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; }
    .btn-danger:hover { background: var(--danger-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,0.3); color: #ffffff; }
    .btn-secondary { background: #ffffff; border: 1.5px solid var(--gray-200); border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600; color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-sm { padding: 5px 14px; font-size: 11px; }

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
    .btn-icon-delete { color: var(--danger); } .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }
    .btn-icon-print { color: var(--gray-600); } .btn-icon-print:hover { background: var(--gray-100); border-color: var(--gray-300); }

    .btn-toolbar { padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; border: 1px solid var(--gray-200); background: #ffffff; color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .toolbar-left { display: flex; align-items: center; gap: 8px; }
    .toolbar-right { display: flex; align-items: center; gap: 10px; }

    .dataTables_wrapper { font-family: 'Inter', sans-serif; }
    .dataTables_filter { float: right; margin-bottom: 16px; }
    .dataTables_filter label { font-size: 12px; font-weight: 500; color: var(--gray-500); display: flex; align-items: center; gap: 8px; }
    .dataTables_filter input { width: 200px; padding: 6px 12px; border: 1.5px solid var(--gray-200); border-radius: 8px; font-size: 12px; transition: all 0.2s; outline: none; }
    .dataTables_filter input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,107,176,0.08); }
    .dataTables_paginate { float: right; margin-top: 16px; }
    .dataTables_paginate .paginate_button { padding: 4px 10px !important; margin: 0 2px !important; border-radius: 6px !important; border: 1px solid var(--gray-200) !important; background: #ffffff !important; color: var(--gray-600) !important; font-size: 11px !important; font-weight: 600 !important; transition: all 0.2s; }
    .dataTables_paginate .paginate_button.current { background: var(--primary) !important; border-color: var(--primary) !important; color: #ffffff !important; }
    .dataTables_paginate .paginate_button:hover { background: var(--gray-50) !important; border-color: var(--gray-300) !important; color: var(--primary) !important; }
    .dataTables_info { float: left; font-size: 12px; color: var(--gray-500); margin-top: 16px; }

    .modal-content { border-radius: 16px; border: none; box-shadow: var(--shadow-lg); }
    .modal-header { border-bottom: 1px solid var(--gray-200); padding: 18px 24px; background: var(--gray-50); border-radius: 16px 16px 0 0; }
    .modal-header .modal-title { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .modal-header .modal-title i { color: var(--primary); }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid var(--gray-200); padding: 16px 24px; gap: 10px; background: var(--gray-50); border-radius: 0 0 16px 16px; }

    .invoice-info-box { background: var(--gray-50); border-radius: 8px; padding: 16px; margin-bottom: 16px; border-left: 4px solid var(--primary); }

    @media (max-width: 992px) {
        .lrg-module-header { flex-direction: column; align-items: stretch; padding: 16px 20px; }
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    }
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .stat-card { padding: 14px 16px; }
        .stat-left .stat-value { font-size: 20px; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-left, .toolbar-right { flex-wrap: wrap; }
        .dataTables_filter input { width: 150px; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
        .btn-icon { width: 28px; height: 28px; font-size: 13px; }
        .modal-body { padding: 16px; }
    }
</style>

<div class="me-trp-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon"><i class="bi bi-file-earmark-text"></i></div>
        <div class="module-info">
            <h4>
                Invoice Generation
                <span class="module-badge">Billing & AR</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__Invoice.__openAddInvoice()">
            <i class="bi bi-plus-circle"></i> New Invoice
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value"><?=$total_invoices;?></div>
            <div class="stat-sub">All invoices</div>
        </div>
        <div class="stat-right"><i class="bi bi-file-earmark-text"></i></div>
    </div>
    <div class="stat-card" data-filter="DRAFT" onclick="filterTable('DRAFT')">
        <div class="stat-left">
            <div class="stat-label">Draft</div>
            <div class="stat-value"><?=$total_draft;?></div>
            <div class="stat-sub">Not yet issued</div>
        </div>
        <div class="stat-right"><i class="bi bi-pencil-square"></i></div>
    </div>
    <div class="stat-card" data-filter="ISSUED" onclick="filterTable('ISSUED')">
        <div class="stat-left">
            <div class="stat-label">Issued</div>
            <div class="stat-value"><?=$total_issued;?></div>
            <div class="stat-sub">Sent to customer</div>
        </div>
        <div class="stat-right"><i class="bi bi-send"></i></div>
    </div>
    <div class="stat-card" data-filter="PARTIALLY_PAID" onclick="filterTable('PARTIALLY_PAID')">
        <div class="stat-left">
            <div class="stat-label">Partially Paid</div>
            <div class="stat-value"><?=$total_partial;?></div>
            <div class="stat-sub">Balance remaining</div>
        </div>
        <div class="stat-right"><i class="bi bi-pie-chart"></i></div>
    </div>
    <div class="stat-card" data-filter="PAID" onclick="filterTable('PAID')">
        <div class="stat-left">
            <div class="stat-label">Paid</div>
            <div class="stat-value"><?=$total_paid;?></div>
            <div class="stat-sub">Fully settled</div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
    </div>
    <div class="stat-card" data-filter="OVERDUE" onclick="filterTable('OVERDUE')">
        <div class="stat-left">
            <div class="stat-label">Overdue</div>
            <div class="stat-value"><?=$total_overdue;?></div>
            <div class="stat-sub">Past due date</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-circle"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Invoice Records</h6>
                <span class="badge badge-secondary" id="recordCount"><?=count($invoices);?> records</span>
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
                    <table id="invoiceTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="130">Invoice #</th>
                                <th>Customer</th>
                                <th>Billing #</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Balance</th>
                                <th width="110">Status</th>
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php foreach($invoices as $row): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['invoice_code'];?></span></td>
                                    <td><?=$row['customer_name'] ?? '—';?></td>
                                    <td><?=$row['billing_code'] ?? '—';?></td>
                                    <td><?=date('M d, Y', strtotime($row['invoice_date']));?></td>
                                    <td><?=$row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '—';?></td>
                                    <td class="text-end">&#8369;<?=number_format($row['total_amount'], 2);?></td>
                                    <td class="text-end">&#8369;<?=number_format($row['outstanding_balance'], 2);?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = $row['display_status'];
                                        if($row['display_status'] == 'DRAFT') { $statusClass = 'badge-secondary'; $statusLabel = 'Draft'; }
                                        elseif($row['display_status'] == 'ISSUED') { $statusClass = 'badge-info'; $statusLabel = 'Issued'; }
                                        elseif($row['display_status'] == 'PARTIALLY_PAID') { $statusClass = 'badge-warning'; $statusLabel = 'Partially Paid'; }
                                        elseif($row['display_status'] == 'PAID') { $statusClass = 'badge-success'; $statusLabel = 'Paid'; }
                                        elseif($row['display_status'] == 'OVERDUE') { $statusClass = 'badge-danger'; $statusLabel = 'Overdue'; }
                                        elseif($row['display_status'] == 'CANCELLED') { $statusClass = 'badge-danger'; $statusLabel = 'Cancelled'; }
                                        elseif($row['display_status'] == 'VOID') { $statusClass = 'badge-danger'; $statusLabel = 'Void'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view"
                                                    onclick="__Invoice.__openViewInvoice(<?=$row['invoice_id'];?>)"
                                                    title="View / Edit Invoice">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-print"
                                                    onclick="__Invoice.__printInvoice(<?=$row['invoice_id'];?>)"
                                                    title="Print Invoice">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                            <?php if($row['invoice_status'] == 'DRAFT' && $row['amount_paid'] == 0): ?>
                                            <button class="btn-icon btn-icon-delete"
                                                    onclick="__Invoice.__deleteInvoice(<?=$row['invoice_id'];?>, '<?=$row['invoice_code'];?>')"
                                                    title="Delete Invoice">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
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
<!-- NEW INVOICE MODAL (pick a billing) -->
<!-- ============================================ -->
<div class="modal fade" id="pickBillingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Invoice — Select Billing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-wrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Billing #</th>
                                <th>Customer</th>
                                <th>Billing Date</th>
                                <th class="text-end">Total</th>
                                <th width="90" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="pickBillingBody">
                            <tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- INVOICE MODAL (view / edit) -->
<!-- ============================================ -->
<div class="modal fade" id="invoiceModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Invoice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="invoice_id">

                <div class="invoice-info-box">
                    <div class="row">
                        <div class="col-md-4"><strong>Invoice #:</strong> <span id="invoice_code_display"></span></div>
                        <div class="col-md-4"><strong>Customer:</strong> <span id="invoice_customer_display"></span></div>
                        <div class="col-md-4"><strong>Billing #:</strong> <span id="invoice_billing_display"></span></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Invoice Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="invoice_date">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="invoice_due_date">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Payment Terms</label>
                        <input type="text" class="form-control" id="invoice_payment_terms" placeholder="e.g. 30 Days">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Discount (&#8369;)</label>
                        <input type="number" class="form-control" id="invoice_discount" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="invoice_status">
                            <option value="DRAFT">Draft</option>
                            <option value="ISSUED">Issued</option>
                            <option value="PARTIALLY_PAID">Partially Paid</option>
                            <option value="PAID">Paid</option>
                            <option value="OVERDUE">Overdue</option>
                            <option value="CANCELLED">Cancelled</option>
                            <option value="VOID">Void</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Amount Paid (&#8369;)</label>
                        <input type="text" class="form-control" id="invoice_amount_paid" readonly>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <textarea class="form-control" id="invoice_remarks" placeholder="Remarks"></textarea>
                    </div>
                </div>

                <div class="invoice-info-box mt-3">
                    <div class="row text-end">
                        <div class="col-md-3"><small class="text-muted">Taxable Amount</small><div id="calc_taxable">&#8369;0.00</div></div>
                        <div class="col-md-3"><small class="text-muted">VAT</small><div id="calc_vat">&#8369;0.00</div></div>
                        <div class="col-md-3"><small class="text-muted">Total Amount</small><div id="calc_total">&#8369;0.00</div></div>
                        <div class="col-md-3"><small class="text-muted"><strong>Balance</strong></small><div id="calc_balance"><strong>&#8369;0.00</strong></div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="invoiceSubmitBtn" onclick="__Invoice.__updateInvoice()">
                    <i class="bi bi-save"></i> Save Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- DELETE CONFIRMATION MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle" style="font-size:56px;color:var(--danger);opacity:0.6;"></i>
                <h5 class="mt-3">Delete Invoice</h5>
                <p class="text-muted">You are about to delete:<br><strong id="delete_invoice_name" class="text-danger"></strong></p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><i class="bi bi-trash"></i> Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- PDF MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="height:90vh;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-printer me-2"></i>Print Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfFrame" style="width:100%;height:100%;border:none;"></iframe>
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
var invoiceTable;

$(document).ready(function () {
    invoiceTable = $('#invoiceTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[3, 'desc']],
        language: { search: "Search:", emptyTable: "No invoices found" },
        columnDefs: [{ orderable: false, targets: [8] }]
    });

    var params = new URLSearchParams(window.location.search);
    var fromBilling = params.get('from_billing');
    if (fromBilling) {
        __Invoice.__pickBilling(fromBilling);
    }
});

function filterTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    var columnIndex = 7;

    if (status === 'all') {
        invoiceTable.column(columnIndex).search('', true, false).draw();
    } else {
        var labelMap = {
            'DRAFT'          : '^Draft$',
            'ISSUED'         : '^Issued$',
            'PARTIALLY_PAID' : '^Partially Paid$',
            'PAID'           : '^Paid$',
            'OVERDUE'        : '^Overdue$'
        };
        invoiceTable.column(columnIndex).search(labelMap[status] || status, true, false).draw();
    }
}
</script>

<script src="<?=base_url('assets/js/apps/fms/invoice/invoice.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>
