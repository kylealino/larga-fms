<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("
    SELECT p.*, c.customer_name, i.invoice_code
    FROM tbl_payments p
    LEFT JOIN tbl_customers c ON p.customer_id = c.customer_id
    LEFT JOIN tbl_invoices i ON p.invoice_id = i.invoice_id
    ORDER BY p.payment_date DESC, p.payment_id DESC
");
$payments = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_payments = count($payments);
$total_collected = $this->db->query("SELECT COALESCE(SUM(amount_paid),0) as total FROM tbl_payments WHERE payment_status = 'CLEARED'")->getRow()->total;
$total_pending = $this->db->query("SELECT COUNT(*) as total FROM tbl_payments WHERE payment_status = 'PENDING'")->getRow()->total;

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
    .btn-icon-edit { color: var(--warning); } .btn-icon-edit:hover { background: #fef3c7; border-color: #fcd34d; }
    .btn-icon-delete { color: var(--danger); } .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }

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

    .payment-info-box { background: var(--gray-50); border-radius: 8px; padding: 16px; margin-bottom: 16px; border-left: 4px solid var(--primary); }

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
        <div class="module-icon"><i class="bi bi-credit-card"></i></div>
        <div class="module-info">
            <h4>
                Payment Recording
                <span class="module-badge">Billing & AR</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__Payment.__openAddPayment()">
            <i class="bi bi-plus-circle"></i> New Payment
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Payments</div>
            <div class="stat-value"><?=$total_payments;?></div>
            <div class="stat-sub">All records</div>
        </div>
        <div class="stat-right"><i class="bi bi-credit-card"></i></div>
    </div>
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Collected</div>
            <div class="stat-value" style="font-size:18px;">&#8369;<?=number_format($total_collected, 2);?></div>
            <div class="stat-sub">Cleared payments</div>
        </div>
        <div class="stat-right"><i class="bi bi-cash-stack"></i></div>
    </div>
    <div class="stat-card" data-filter="PENDING" onclick="filterTable('PENDING')">
        <div class="stat-left">
            <div class="stat-label">Pending</div>
            <div class="stat-value"><?=$total_pending;?></div>
            <div class="stat-sub">Awaiting clearance</div>
        </div>
        <div class="stat-right"><i class="bi bi-hourglass-split"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Payment Records</h6>
                <span class="badge badge-secondary" id="recordCount"><?=count($payments);?> records</span>
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
                    <table id="paymentTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="130">Receipt #</th>
                                <th>Customer</th>
                                <th>Invoice #</th>
                                <th>Payment Date</th>
                                <th class="text-end">Amount</th>
                                <th>Method</th>
                                <th width="100">Status</th>
                                <th width="130" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php foreach($payments as $row): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['receipt_number'];?></span></td>
                                    <td><?=$row['customer_name'] ?? '—';?></td>
                                    <td><?=$row['invoice_code'] ?? '—';?></td>
                                    <td><?=date('M d, Y', strtotime($row['payment_date']));?></td>
                                    <td class="text-end">&#8369;<?=number_format($row['amount_paid'], 2);?></td>
                                    <td><?=str_replace('_', ' ', $row['payment_method']);?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = $row['payment_status'];
                                        if($row['payment_status'] == 'PENDING') { $statusClass = 'badge-warning'; $statusLabel = 'Pending'; }
                                        elseif($row['payment_status'] == 'CLEARED') { $statusClass = 'badge-success'; $statusLabel = 'Cleared'; }
                                        elseif($row['payment_status'] == 'BOUNCED') { $statusClass = 'badge-danger'; $statusLabel = 'Bounced'; }
                                        elseif($row['payment_status'] == 'CANCELLED') { $statusClass = 'badge-danger'; $statusLabel = 'Cancelled'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view"
                                                    onclick="__Payment.__openViewPayment(<?=$row['payment_id'];?>)"
                                                    title="View / Edit Payment">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-delete"
                                                    onclick="__Payment.__deletePayment(<?=$row['payment_id'];?>, '<?=$row['receipt_number'];?>')"
                                                    title="Delete Payment">
                                                <i class="bi bi-trash"></i>
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
<!-- PAYMENT MODAL (add / view / edit) -->
<!-- ============================================ -->
<div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalTitle"><i class="bi bi-credit-card me-2"></i>New Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="payment_id">

                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Invoice <span class="required">*</span></label>
                        <select class="form-control" id="payment_invoice_id"></select>
                    </div>
                    <div class="payment-info-box" id="invoiceBalanceBox" style="display:none;margin:0 12px 12px;">
                        <div class="row">
                            <div class="col-md-4"><small class="text-muted">Total</small><div id="pi_total">&#8369;0.00</div></div>
                            <div class="col-md-4"><small class="text-muted">Already Paid</small><div id="pi_paid">&#8369;0.00</div></div>
                            <div class="col-md-4"><small class="text-muted">Balance</small><div id="pi_balance"><strong>&#8369;0.00</strong></div></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Payment Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="payment_date">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Amount Paid (&#8369;) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="amount_paid" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method">
                            <option value="CASH">Cash</option>
                            <option value="BANK_TRANSFER">Bank Transfer</option>
                            <option value="CHECK">Check</option>
                            <option value="ONLINE_TRANSFER">Online Transfer</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Bank</label>
                        <input type="text" class="form-control" id="payment_bank" placeholder="Bank name">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" placeholder="Check no. / Transaction ID">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Payment Status</label>
                        <select class="form-control" id="payment_status">
                            <option value="CLEARED">Cleared</option>
                            <option value="PENDING">Pending</option>
                            <option value="BOUNCED">Bounced</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Upload Receipt</label>
                        <input type="file" class="form-control" id="receipt_file" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" style="padding:7px 12px;">
                    </div>
                    <div class="col-md-6 mb-2" id="receiptLinkWrap" style="display:none;">
                        <label class="form-label">Current Receipt</label>
                        <div><a href="#" id="receiptLink" target="_blank" class="btn btn-secondary btn-sm"><i class="bi bi-file-earmark"></i> View Receipt</a></div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <textarea class="form-control" id="payment_remarks" placeholder="Remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="paymentSubmitBtn" onclick="__Payment.__savePayment()">
                    <i class="bi bi-save"></i> Save Payment
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
                <h5 class="mt-3">Delete Payment</h5>
                <p class="text-muted">You are about to delete:<br><strong id="delete_payment_name" class="text-danger"></strong></p>
                <p class="text-muted small">This will recalculate the linked invoice's balance. This action cannot be undone.</p>
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
var paymentTable;

$(document).ready(function () {
    paymentTable = $('#paymentTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[3, 'desc']],
        language: { search: "Search:", emptyTable: "No payments recorded" },
        columnDefs: [{ orderable: false, targets: [7] }]
    });
});

function filterTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    var columnIndex = 6;

    if (status === 'all') {
        paymentTable.column(columnIndex).search('', true, false).draw();
    } else {
        var labelMap = { 'PENDING': '^Pending$' };
        paymentTable.column(columnIndex).search(labelMap[status] || status, true, false).draw();
    }
}
</script>

<script src="<?=base_url('assets/js/apps/fms/payment/payment.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>
