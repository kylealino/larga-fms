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
    .btn-header-primary { background: #ffffff; border-color: #ffffff; color: var(--primary); }
    .btn-header-primary:hover { background: rgba(255,255,255,0.9); color: var(--primary-dark); }

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
    textarea.form-control { height: auto; min-height: 60px; resize: vertical; }

    .btn-primary { background: var(--primary); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600; transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,107,176,0.3); color: #ffffff; }
    .btn-danger { background: var(--danger); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600; transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; }
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
    .badge-primary { background: var(--primary); color: #ffffff; }
    .badge-secondary { background: var(--gray-500); color: #ffffff; }

    .btn-icon { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px; border: 1px solid transparent; background: transparent; cursor: pointer; transition: all 0.2s; color: var(--gray-500); font-size: 14px; }
    .btn-icon:hover { transform: scale(1.05); }
    .btn-icon-delete { color: var(--danger); } .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }

    .modal-content { border-radius: 16px; border: none; box-shadow: var(--shadow-lg); }
    .modal-header { border-bottom: 1px solid var(--gray-200); padding: 18px 24px; background: var(--gray-50); border-radius: 16px 16px 0 0; }
    .modal-header .modal-title { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .modal-header .modal-title i { color: var(--primary); }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid var(--gray-200); padding: 16px 24px; gap: 10px; background: var(--gray-50); border-radius: 0 0 16px 16px; }

    .soa-summary-box {
        background: var(--gray-50); border-radius: 8px; padding: 16px; margin-bottom: 16px; border-left: 4px solid var(--primary);
    }
    .soa-balance-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--gray-200); font-size: 13px; }
    .soa-balance-row:last-child { border-bottom: none; font-weight: 700; font-size: 15px; color: var(--primary); }

    .empty-state { text-align: center; padding: 40px 20px; }
    .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state h5 { font-size: 16px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
    .empty-state p { font-size: 13px; color: var(--gray-400); }

    @media (max-width: 992px) {
        .lrg-module-header { flex-direction: column; align-items: stretch; padding: 16px 20px; }
    }
</style>

<div class="me-trp-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
        <div class="module-info">
            <h4>
                Statement of Account
                <span class="module-badge">Billing & AR</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__SOA.__openAdjustmentModal()">
            <i class="bi bi-plus-circle"></i> Add Adjustment
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- FILTERS -->
<!-- ============================================ -->
<div class="card">
    <div class="card-header"><h6><i class="bi bi-funnel me-2"></i>Generate Statement</h6></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Customer <span class="required">*</span></label>
                <select class="form-control" id="soa_customer_id"></select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">From</label>
                <input type="date" class="form-control" id="soa_date_from">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">To</label>
                <input type="date" class="form-control" id="soa_date_to">
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-primary w-100" onclick="__SOA.__generate()">
                    <i class="bi bi-search"></i> Generate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- RESULTS -->
<!-- ============================================ -->
<div class="card" id="soaResultsCard" style="display:none;">
    <div class="card-header">
        <h6><i class="bi bi-file-earmark-text me-2"></i>Statement — <span id="soa_customer_name_display"></span></h6>
        <button class="btn btn-secondary btn-sm" onclick="__SOA.__printSOA()">
            <i class="bi bi-printer"></i> Print
        </button>
    </div>
    <div class="card-body">
        <div class="soa-summary-box">
            <div class="soa-balance-row"><span>Beginning Balance</span><span id="soa_beginning">&#8369;0.00</span></div>
            <div class="soa-balance-row"><span>New Charges (Debit)</span><span id="soa_total_debit">&#8369;0.00</span></div>
            <div class="soa-balance-row"><span>Payments &amp; Credits</span><span id="soa_total_credit">&#8369;0.00</span></div>
            <div class="soa-balance-row"><span>Ending Balance</span><span id="soa_ending">&#8369;0.00</span></div>
        </div>

        <div class="table-wrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference #</th>
                        <th>Description</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Running Balance</th>
                    </tr>
                </thead>
                <tbody id="soaTransactionsBody"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="empty-state" id="soaEmptyState">
    <i class="bi bi-file-earmark-bar-graph"></i>
    <h5>No statement generated yet</h5>
    <p>Select a customer and click Generate to view their account statement.</p>
</div>

<!-- ============================================ -->
<!-- ADJUSTMENTS -->
<!-- ============================================ -->
<div class="card" id="adjustmentsCard" style="display:none;">
    <div class="card-header"><h6><i class="bi bi-sliders me-2"></i>Manual Adjustments</h6></div>
    <div class="card-body">
        <div class="table-wrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="130">Code</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th class="text-end">Amount</th>
                        <th width="70" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="adjustmentsBody">
                    <tr><td colspan="6" class="text-center text-muted">No adjustments</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ADD ADJUSTMENT MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="adjustmentModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Add Manual Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Customer <span class="required">*</span></label>
                    <select class="form-control" id="adj_customer_id"></select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="adj_date">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Type</label>
                        <select class="form-control" id="adj_type">
                            <option value="DEBIT">Debit (increases balance)</option>
                            <option value="CREDIT">Credit (decreases balance)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Amount (&#8369;) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="adj_amount" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="adj_reference" placeholder="Optional">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Reason</label>
                        <input type="text" class="form-control" id="adj_reason" placeholder="e.g. Write-off, billing correction">
                    </div>
                    <div class="col-md-12">
                        <textarea class="form-control" id="adj_remarks" placeholder="Remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="adjustmentSubmitBtn" onclick="__SOA.__saveAdjustment()">
                    <i class="bi bi-save"></i> Save Adjustment
                </button>
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
                <h5 class="modal-title"><i class="bi bi-printer me-2"></i>Print Statement</h5>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url('assets/js/apps/fms/soa/soa.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>
