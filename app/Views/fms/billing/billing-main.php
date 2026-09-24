<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("
    SELECT b.*, c.customer_name, t.trip_code, dr.dr_code
    FROM tbl_billing b
    LEFT JOIN tbl_customers c ON b.customer_id = c.customer_id
    LEFT JOIN tbl_trips t ON b.trip_id = t.trip_id
    LEFT JOIN tbl_delivery_receipts dr ON b.dr_id = dr.dr_id
    ORDER BY b.billing_date DESC, b.billing_id DESC
");
$billings = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_billings = count($billings);
$total_draft = $this->db->query("SELECT COUNT(*) as total FROM tbl_billing WHERE billing_status = 'DRAFT'")->getRow()->total;
$total_for_invoice = $this->db->query("SELECT COUNT(*) as total FROM tbl_billing WHERE billing_status = 'FOR_INVOICE'")->getRow()->total;
$total_invoiced = $this->db->query("SELECT COUNT(*) as total FROM tbl_billing WHERE billing_status = 'INVOICED'")->getRow()->total;

echo view('templates/myheader.php');
?>
<style>
    :root {
        --primary: #1a6bb0;
        --primary-dark: #0f5a99;
        --primary-light: #e8f2fa;
        --danger: #dc2626;
        --danger-dark: #b91c1c;
        --success: #10b981;
        --warning: #f59e0b;
        --info: #3b82f6;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --shadow: 0 1px 3px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.08);
    }

    body { background: var(--gray-50); }

    .lrg-module-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 14px 24px;
        margin: -24px -24px 24px -24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .lrg-module-header .header-left { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 200px; }
    .lrg-module-header .header-left .module-icon {
        width: 40px; height: 40px; background: rgba(255,255,255,0.12); border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
        color: #90cdf4; border: 1px solid rgba(255,255,255,0.08);
    }
    .lrg-module-header .header-left .module-info h4 {
        font-size: 17px; font-weight: 600; color: #ffffff; margin: 0;
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .lrg-module-header .header-left .module-info h4 .module-badge {
        font-size: 9px; font-weight: 600; padding: 2px 12px; border-radius: 4px;
        background: rgba(255,255,255,0.12); color: #bee3f8; border: 1px solid rgba(255,255,255,0.08);
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .header-actions { display: flex; align-items: center; gap: 10px; }
    .btn-header {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: #ffffff;
        padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 500; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
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
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light)); opacity: 0; transition: opacity 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--gray-300); }
    .stat-card:hover::before { opacity: 1; }
    .stat-card.active { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(26,107,176,0.15), var(--shadow-md); }
    .stat-card.active::before { opacity: 1; }
    .stat-left .stat-label { font-size: 10px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .stat-left .stat-value { font-size: 24px; font-weight: 700; color: var(--gray-800); line-height: 1.2; }
    .stat-left .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    .stat-right { font-size: 32px; color: var(--primary); opacity: 0.08; line-height: 1; }

    .card { border-radius: 12px; border: 1px solid var(--gray-200); background: #ffffff; box-shadow: var(--shadow); margin-bottom: 20px; }
    .card-header {
        background: #ffffff; border-bottom: 1px solid var(--gray-200); padding: 14px 20px; border-radius: 12px 12px 0 0;
        display: flex; align-items: center; justify-content: space-between;
    }
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

    .btn-primary {
        background: var(--primary); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600;
        transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,107,176,0.3); color: #ffffff; }
    .btn-danger {
        background: var(--danger); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600;
        transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-danger:hover { background: var(--danger-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,0.3); color: #ffffff; }
    .btn-secondary {
        background: #ffffff; border: 1.5px solid var(--gray-200); border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600;
        color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-secondary:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-sm { padding: 5px 14px; font-size: 11px; }

    .table-wrap { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .table thead th {
        font-size: 10px; font-weight: 600; color: var(--gray-500); background: var(--gray-50); border-bottom: 1.5px solid var(--gray-200);
        padding: 10px 12px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; white-space: nowrap;
    }
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
    .btn-icon {
        display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px;
        border: 1px solid transparent; background: transparent; cursor: pointer; transition: all 0.2s; color: var(--gray-500); font-size: 14px;
    }
    .btn-icon:hover { transform: scale(1.05); }
    .btn-icon-view { color: var(--info); } .btn-icon-view:hover { background: #dbeafe; border-color: #93c5fd; }
    .btn-icon-edit { color: var(--warning); } .btn-icon-edit:hover { background: #fef3c7; border-color: #fcd34d; }
    .btn-icon-delete { color: var(--danger); } .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }
    .btn-icon-dispatch { color: var(--success); } .btn-icon-dispatch:hover { background: #d1fae5; border-color: #6ee7b7; }

    .btn-toolbar {
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; border: 1px solid var(--gray-200);
        background: #ffffff; color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
    }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-toolbar-primary { background: var(--primary); border-color: var(--primary); color: #ffffff; }
    .btn-toolbar-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); color: #ffffff; }

    .toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .toolbar-left { display: flex; align-items: center; gap: 8px; }
    .toolbar-right { display: flex; align-items: center; gap: 10px; }

    .dataTables_wrapper { font-family: 'Inter', sans-serif; }
    .dataTables_filter { float: right; margin-bottom: 16px; }
    .dataTables_filter label { font-size: 12px; font-weight: 500; color: var(--gray-500); display: flex; align-items: center; gap: 8px; }
    .dataTables_filter input { width: 200px; padding: 6px 12px; border: 1.5px solid var(--gray-200); border-radius: 8px; font-size: 12px; transition: all 0.2s; outline: none; }
    .dataTables_filter input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,107,176,0.08); }
    .dataTables_paginate { float: right; margin-top: 16px; }
    .dataTables_paginate .paginate_button {
        padding: 4px 10px !important; margin: 0 2px !important; border-radius: 6px !important; border: 1px solid var(--gray-200) !important;
        background: #ffffff !important; color: var(--gray-600) !important; font-size: 11px !important; font-weight: 600 !important; transition: all 0.2s;
    }
    .dataTables_paginate .paginate_button.current { background: var(--primary) !important; border-color: var(--primary) !important; color: #ffffff !important; }
    .dataTables_paginate .paginate_button:hover { background: var(--gray-50) !important; border-color: var(--gray-300) !important; color: var(--primary) !important; }
    .dataTables_info { float: left; font-size: 12px; color: var(--gray-500); margin-top: 16px; }

    .modal-content { border-radius: 16px; border: none; box-shadow: var(--shadow-lg); }
    .modal-header { border-bottom: 1px solid var(--gray-200); padding: 18px 24px; background: var(--gray-50); border-radius: 16px 16px 0 0; }
    .modal-header .modal-title { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .modal-header .modal-title i { color: var(--primary); }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid var(--gray-200); padding: 16px 24px; gap: 10px; background: var(--gray-50); border-radius: 0 0 16px 16px; }

    .billing-info-box { background: var(--gray-50); border-radius: 8px; padding: 16px; margin-bottom: 16px; border-left: 4px solid var(--primary); }

    .empty-state { text-align: center; padding: 40px 20px; }
    .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state h5 { font-size: 16px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
    .empty-state p { font-size: 13px; color: var(--gray-400); }

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
        <div class="module-icon"><i class="bi bi-receipt"></i></div>
        <div class="module-info">
            <h4>
                Billing Generation
                <span class="module-badge">Billing & AR</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__Billing.__openAddBilling()">
            <i class="bi bi-plus-circle"></i> New Billing
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Billings</div>
            <div class="stat-value"><?=$total_billings;?></div>
            <div class="stat-sub">All billing records</div>
        </div>
        <div class="stat-right"><i class="bi bi-receipt"></i></div>
    </div>
    <div class="stat-card" data-filter="DRAFT" onclick="filterTable('DRAFT')">
        <div class="stat-left">
            <div class="stat-label">Draft</div>
            <div class="stat-value"><?=$total_draft;?></div>
            <div class="stat-sub">Being prepared</div>
        </div>
        <div class="stat-right"><i class="bi bi-pencil-square"></i></div>
    </div>
    <div class="stat-card" data-filter="FOR_INVOICE" onclick="filterTable('FOR_INVOICE')">
        <div class="stat-left">
            <div class="stat-label">For Invoice</div>
            <div class="stat-value"><?=$total_for_invoice;?></div>
            <div class="stat-sub">Ready to bill</div>
        </div>
        <div class="stat-right"><i class="bi bi-arrow-right-circle"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Billing Records</h6>
                <span class="badge badge-secondary" id="recordCount"><?=count($billings);?> records</span>
            </div>
            <div class="card-body">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button class="btn-toolbar" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <span class="text-muted" style="font-size:12px;">
                            <i class="bi bi-info-circle"></i> Click <strong>Invoice</strong> to generate an invoice for a billing
                        </span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="billingTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="130">Billing #</th>
                                <th>Customer</th>
                                <th>Trip #</th>
                                <th>DR #</th>
                                <th>Billing Date</th>
                                <th class="text-end">Total</th>
                                <th width="110">Status</th>
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php foreach($billings as $row): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['billing_code'];?></span></td>
                                    <td><?=$row['customer_name'] ?? '—';?></td>
                                    <td><?=$row['trip_code'] ?? '—';?></td>
                                    <td><?=$row['dr_code'] ?? '—';?></td>
                                    <td><?=date('M d, Y', strtotime($row['billing_date']));?></td>
                                    <td class="text-end">&#8369;<?=number_format($row['total'], 2);?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = $row['billing_status'];
                                        if($row['billing_status'] == 'DRAFT') { $statusClass = 'badge-secondary'; $statusLabel = 'Draft'; }
                                        elseif($row['billing_status'] == 'FOR_INVOICE') { $statusClass = 'badge-warning'; $statusLabel = 'For Invoice'; }
                                        elseif($row['billing_status'] == 'INVOICED') { $statusClass = 'badge-success'; $statusLabel = 'Invoiced'; }
                                        elseif($row['billing_status'] == 'CANCELLED') { $statusClass = 'badge-danger'; $statusLabel = 'Cancelled'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view"
                                                    onclick="__Billing.__openViewBilling(<?=$row['billing_id'];?>)"
                                                    title="View / Edit Billing">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <?php if($row['billing_status'] != 'INVOICED' && $row['billing_status'] != 'CANCELLED'): ?>
                                            <button class="btn-icon btn-icon-dispatch"
                                                    onclick="location.href='<?=site_url('invoice');?>?meaction=MAIN&from_billing=<?=$row['billing_id'];?>'"
                                                    title="Generate Invoice">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-delete"
                                                    onclick="__Billing.__deleteBilling(<?=$row['billing_id'];?>, '<?=$row['billing_code'];?>')"
                                                    title="Delete Billing">
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
<!-- NEW BILLING MODAL (pick a delivered DR) -->
<!-- ============================================ -->
<div class="modal fade" id="pickDRModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Billing — Select Delivered DR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-wrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>DR #</th>
                                <th>Trip #</th>
                                <th>Customer</th>
                                <th>DR Date</th>
                                <th width="90" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="pickDRBody">
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
<!-- BILLING MODAL (view / edit + additional charges) -->
<!-- ============================================ -->
<div class="modal fade" id="billingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="billingModalTitle"><i class="bi bi-receipt me-2"></i>Billing Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="billing_id">
                <input type="hidden" id="billing_dr_id">

                <div class="billing-info-box">
                    <div class="row">
                        <div class="col-md-4"><strong>Billing #:</strong> <span id="billing_code_display"></span></div>
                        <div class="col-md-4"><strong>Customer:</strong> <span id="billing_customer_display"></span></div>
                        <div class="col-md-4"><strong>Trip / DR:</strong> <span id="billing_trip_dr_display"></span></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Billing Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="billing_date">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Billing Basis</label>
                        <select class="form-control" id="billing_basis">
                            <option value="PER_TRIP">Per Trip</option>
                            <option value="PER_KILOMETER">Per Kilometer</option>
                            <option value="PER_HOUR">Per Hour</option>
                            <option value="PER_DAY">Per Day</option>
                            <option value="FIXED_RATE">Fixed Rate</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="billing_status">
                            <option value="DRAFT">Draft</option>
                            <option value="FOR_INVOICE">For Invoice</option>
                            <option value="INVOICED">Invoiced</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Rate (&#8369;) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="billing_rate" step="0.01" placeholder="0.00" oninput="__Billing.__recalcLive()">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="billing_quantity" step="0.01" value="1" oninput="__Billing.__recalcLive()">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Discount (&#8369;)</label>
                        <input type="number" class="form-control" id="billing_discount" step="0.01" placeholder="0.00" oninput="__Billing.__recalcLive()">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Additional Charges</label>
                        </div>
                        <div class="item-form" style="background:var(--gray-50);border-radius:8px;padding:12px;margin-bottom:12px;border:1px solid var(--gray-200);">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label" style="font-size:9px;">Type</label>
                                    <select class="form-control" id="charge_type" style="font-size:12px;height:34px;">
                                        <option value="TOLL_FEES">Toll Fees</option>
                                        <option value="WAITING_TIME">Waiting Time</option>
                                        <option value="DETENTION">Detention</option>
                                        <option value="EXTRA_STOP">Extra Stop</option>
                                        <option value="HANDLING">Handling</option>
                                        <option value="ADDITIONAL_KILOMETER">Additional Kilometer</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" style="font-size:9px;">Description</label>
                                    <input type="text" class="form-control" id="charge_description" style="font-size:12px;height:34px;" placeholder="Description">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label" style="font-size:9px;">Amount</label>
                                    <input type="number" class="form-control" id="charge_amount" step="0.01" style="font-size:12px;height:34px;" placeholder="0.00">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label" style="font-size:9px;">&nbsp;</label>
                                    <button class="btn btn-primary w-100" id="chargeActionBtn" onclick="__Billing.__saveCharge()" style="font-size:12px;padding:6px 8px;white-space:nowrap;">
                                        <i class="bi bi-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                        <th width="80" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="chargesBody">
                                    <tr><td colspan="4" class="text-center text-muted">No additional charges</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <textarea class="form-control" id="billing_remarks" placeholder="Remarks"></textarea>
                    </div>
                </div>

                <div class="billing-info-box mt-3">
                    <div class="row text-end">
                        <div class="col-md-3"><small class="text-muted">Subtotal</small><div id="calc_subtotal">&#8369;0.00</div></div>
                        <div class="col-md-3"><small class="text-muted">VAT (12%)</small><div id="calc_vat">&#8369;0.00</div></div>
                        <div class="col-md-3"><small class="text-muted">Other Charges</small><div id="calc_other">&#8369;0.00</div></div>
                        <div class="col-md-3"><small class="text-muted"><strong>Total</strong></small><div id="calc_total"><strong>&#8369;0.00</strong></div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="billingSubmitBtn" onclick="__Billing.__updateBilling()">
                    <i class="bi bi-save"></i> Save Billing
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
                <h5 class="mt-3">Delete Billing</h5>
                <p class="text-muted">You are about to delete:<br><strong id="delete_billing_name" class="text-danger"></strong></p>
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
<!-- SCRIPTS -->
<!-- ============================================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
var billingTable;

$(document).ready(function () {
    billingTable = $('#billingTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[4, 'desc']],
        language: { search: "Search:", emptyTable: "No billing records found" },
        columnDefs: [{ orderable: false, targets: [7] }]
    });
});

function filterTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    var columnIndex = 6;

    if (status === 'all') {
        billingTable.column(columnIndex).search('', true, false).draw();
    } else {
        var labelMap = {
            'DRAFT'       : '^Draft$',
            'FOR_INVOICE' : '^For Invoice$',
            'INVOICED'    : '^Invoiced$'
        };
        billingTable.column(columnIndex).search(labelMap[status] || status, true, false).draw();
    }
}
</script>

<script src="<?=base_url('assets/js/apps/fms/billing/billing.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>
