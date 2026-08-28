<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("SELECT * FROM tbl_range_assistants ORDER BY assistant_id DESC");
$assistants = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_assistants = count($assistants);
$total_active = $this->db->query("SELECT COUNT(*) as total FROM tbl_range_assistants WHERE status = 'ACTIVE'")->getRow()->total;
$total_inactive = $this->db->query("SELECT COUNT(*) as total FROM tbl_range_assistants WHERE status = 'INACTIVE'")->getRow()->total;

echo view('templates/myheader.php');
?>
<style>
    /* ============================================ */
    /* QCPD HEADER - UNIFIED WITH DASHBOARD */
    /* ============================================ */
    .qcpd-module-header {
        background: #1a365d;
        background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 14px 24px;
        margin: -24px -24px 24px -24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .qcpd-module-header .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        min-width: 200px;
    }

    .qcpd-module-header .header-left .module-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #90cdf4;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .qcpd-module-header .header-left .module-info h4 {
        font-size: 17px;
        font-weight: 600;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .qcpd-module-header .header-left .module-info h4 .module-badge {
        font-size: 9px;
        font-weight: 600;
        padding: 2px 12px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.12);
        color: #bee3f8;
        border: 1px solid rgba(255, 255, 255, 0.08);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .qcpd-module-header .header-left .module-info .module-meta {
        color: #bee3f8;
        font-size: 12px;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .qcpd-module-header .header-left .module-info .module-meta .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #e2e8f0;
    }

    .qcpd-module-header .header-left .module-info .module-meta .meta-item i {
        color: #90cdf4;
        font-size: 11px;
    }

    .qcpd-module-header .header-left .module-info .module-meta .divider {
        color: rgba(255, 255, 255, 0.15);
    }

    .qcpd-module-header .header-left .module-info .module-meta .live-clock {
        color: #ffffff;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.12);
        padding: 2px 10px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .qcpd-module-header .header-left .module-info .module-meta .live-clock i {
        color: #90cdf4;
    }

    :root {
        --primary: #1e3a5f;
        --primary-dark: #0f2b44;
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
    }

    body {
        background: var(--gray-50);
    }

    .attendance-card {
        border-radius: 12px;
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
        background: #ffffff;
        height: 100%;
    }

    .attendance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -12px rgba(0,0,0,0.1);
        border-color: var(--gray-300);
    }

    .attendance-card .card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
    }

    .attendance-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
        color: var(--gray-800);
        font-family: 'Inter', sans-serif;
    }

    .attendance-icon {
        font-size: 36px;
        opacity: 0.1;
        color: var(--primary);
    }

    .attendance-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .attendance-sub {
        font-size: 11px;
        color: var(--gray-400);
        margin-top: 4px;
    }

    .card {
        border-radius: 12px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        background: #ffffff;
        margin-bottom: 20px;
    }

    .card-header {
        background: #ffffff;
        border-bottom: 1px solid var(--gray-200);
        padding: 14px 20px;
        border-radius: 12px 12px 0 0;
    }

    .card-header h6 {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 0;
        color: var(--gray-700);
    }

    .card-body {
        padding: 16px 20px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 4px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .form-control, select.form-control {
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        color: var(--gray-700);
        background: #ffffff;
        transition: all 0.2s;
        width: 100%;
        height: 38px;
    }

    .form-control:focus, select.form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
    }

    .form-control[readonly] {
        background: var(--gray-50);
        cursor: not-allowed;
    }

    .btn-danger {
        background: var(--danger);
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
    }

    .btn-danger:hover {
        background: var(--danger-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-success {
        background: var(--success);
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        color: white;
    }

    .btn-success:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: #ffffff;
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #ffffff;
    }

    .badge {
        font-size: 10px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }

    .bg-success { background: var(--success) !important; color: #ffffff; }
    .bg-danger { background: var(--danger) !important; color: #ffffff; }
    .bg-warning { background: var(--warning) !important; color: #ffffff; }
    .bg-primary { background: var(--primary) !important; color: #ffffff; }
    .bg-info { background: var(--info) !important; color: #ffffff; }
    .bg-secondary { background: var(--gray-500) !important; color: #ffffff; }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: var(--gray-500);
        font-size: 12px;
        transition: all 0.2s;
    }

    .breadcrumb-item a:hover {
        color: var(--primary);
    }

    .breadcrumb-item.active {
        color: var(--primary);
        font-weight: 600;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .table thead th {
        font-size: 10px;
        font-weight: 600;
        color: var(--gray-500);
        background: var(--gray-50);
        border-bottom: 1.5px solid var(--gray-200);
        padding: 10px 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
    }

    .table tbody td {
        font-size: 12px;
        color: var(--gray-700);
        padding: 10px 10px;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
        text-align: center;
    }

    .table-hover tbody tr:hover {
        background: var(--gray-50);
    }

    .dataTables_wrapper {
        font-family: 'Inter', sans-serif;
        overflow-x: visible !important;
    }

    .dataTables_filter {
        float: right;
        margin-bottom: 16px;
    }

    .dataTables_filter label {
        font-size: 12px;
        font-weight: 500;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dataTables_filter input {
        width: 200px;
        padding: 6px 12px;
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        font-size: 12px;
        transition: all 0.2s;
        outline: none;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
    }

    .dataTables_paginate {
        float: right;
        margin-top: 16px;
    }

    .dataTables_paginate .paginate_button {
        padding: 4px 10px !important;
        margin: 0 2px !important;
        border-radius: 6px !important;
        border: 1px solid var(--gray-200) !important;
        background: #ffffff !important;
        color: var(--gray-600) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        transition: all 0.2s;
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--danger) !important;
        border-color: var(--danger) !important;
        color: #ffffff !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: var(--gray-50) !important;
        border-color: var(--gray-300) !important;
        color: var(--primary) !important;
    }

    .dataTables_info {
        float: left;
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 16px;
    }

    .btn-icon {
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: none;
        background: transparent;
        cursor: pointer;
        padding: 0;
    }

    .btn-icon i {
        font-size: 16px;
    }

    .btn-icon:hover {
        transform: scale(1.15);
        background: var(--gray-100);
    }

    .btn-icon.text-primary:hover { background: #dbeafe; }
    .btn-icon.text-danger:hover { background: #fee2e2; }
    .btn-icon.text-success:hover { background: #d1fae5; }

    /* ============================================ */
    /* ACTION BUTTONS */
    /* ============================================ */
    .action-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2px;
        flex-wrap: wrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-action i {
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .btn-action .action-label {
        font-size: 10px;
        font-weight: 500;
    }

    .btn-action:hover {
        transform: translateY(-1px);
    }

    .btn-edit {
        color: var(--warning);
    }

    .btn-edit:hover {
        background: #fef3c7;
        border-color: #fcd34d;
    }

    .btn-delete {
        color: var(--danger);
    }

    .btn-delete:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .qcpd-module-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 20px;
            gap: 12px;
        }
    }

    @media (max-width: 768px) {
        .qcpd-module-header .header-left .module-info h4 {
            font-size: 16px;
        }
        
        .qcpd-module-header .header-left .module-info .module-meta {
            font-size: 11px;
            gap: 6px;
        }
        
        .attendance-value {
            font-size: 22px;
        }
        
        .attendance-icon {
            font-size: 30px;
        }
        
        .card-header {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start !important;
        }
        
        .dataTables_filter input {
            width: 150px;
        }
        
        .btn-action .action-label {
            display: none;
        }
        
        .btn-action {
            padding: 4px 6px;
        }
        
        .qcpd-module-header .header-left .module-icon {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .qcpd-module-header {
            padding: 12px 16px;
        }
        
        .qcpd-module-header .header-left .module-info h4 {
            font-size: 14px;
        }
        
        .qcpd-module-header .header-left .module-info h4 .module-badge {
            font-size: 8px;
            padding: 1px 8px;
        }
        
        .qcpd-module-header .header-left .module-info .module-meta {
            font-size: 10px;
            gap: 4px;
        }
        
        .qcpd-module-header .header-left .module-info .module-meta .divider {
            display: none;
        }
        
        .attendance-card .card-body {
            padding: 12px 16px;
        }
        
        .attendance-value {
            font-size: 18px;
        }
        
        .attendance-icon {
            font-size: 24px;
        }
        
        .btn-action {
            width: 28px;
            height: 28px;
            padding: 0;
            border-radius: 6px;
        }
        
        .btn-action i {
            font-size: 13px;
        }
        
        .qcpd-module-header .header-left .module-icon {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }
    }

    
</style>

<div class="me-ra-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- QCPD UNIFORM HEADER -->
<!-- ============================================ -->
<div class="qcpd-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="ti ti-users"></i>
        </div>
        <div class="module-info">
            <h4>
                Range Assistants
                <span class="module-badge">Personnel Management</span>
            </h4>
        </div>
    </div>
</div>


<!-- ============================================ -->
<!-- TOTALS -->
<!-- ============================================ -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="attendance-label">Total Assistants</div>
                    <div class="attendance-value"><?=$total_assistants;?></div>
                    <div class="attendance-sub">All assistants</div>
                </div>
                <i class="ti ti-users attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="attendance-label">Active</div>
                    <div class="attendance-value"><?=$total_active;?></div>
                    <div class="attendance-sub">Active assistants</div>
                </div>
                <i class="ti ti-user-check attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="attendance-label">Inactive</div>
                    <div class="attendance-value"><?=$total_inactive;?></div>
                    <div class="attendance-sub">Inactive assistants</div>
                </div>
                <i class="ti ti-user-x attendance-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ASSISTANTS TABLE & FORM -->
<!-- ============================================ -->
<div class="row">
    <!-- ASSISTANTS TABLE -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Range Assistants List</h6>
                <span class="badge bg-light text-dark border"><?=count($assistants);?> records</span>
            </div>

            <div class="card-body">
                <table id="raTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Badge #</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th width="250">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($assistants as $row): ?>
                        <tr>
                            <td><strong>#<?=$row['assistant_id'];?></strong></td>
                            <td><?=$row['full_name'];?></td>
                            <td><?=$row['badge_number'];?></td>
                            <td><?=$row['position'];?></td>
                            <td>
                                <?php if($row['status'] == 'ACTIVE'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-wrapper">
                                    <!-- Edit Button -->
                                    <button class="btn-action btn-edit" 
                                            onclick="editAssistant(<?=$row['assistant_id'];?>, '<?=addslashes($row['full_name']);?>', '<?=$row['badge_number'];?>', '<?=addslashes($row['position']);?>', '<?=$row['status'];?>')" 
                                            title="Edit">
                                        <i class="ti ti-edit"></i>
                                        <span class="action-label">Edit</span>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <button class="btn-action btn-delete" 
                                            onclick="showDeleteModal(<?=$row['assistant_id'];?>, '<?=addslashes($row['full_name']);?>')" 
                                            title="Delete">
                                        <i class="ti ti-trash"></i>
                                        <span class="action-label">Delete</span>
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

    <!-- ADD ASSISTANT FORM -->
    <div class="col-md-4">
        <div class="card">
            <form class="ra-reg-form" id="raRegForm">
                <div class="card-header">
                    <h6 class="fw-semibold mb-0">Add New Assistant</h6>
                    <small class="text-muted">Create a new range assistant</small>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" id="full_name" class="form-control" name="full_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Badge Number</label>
                        <input type="text" id="badge_number" class="form-control" name="badge_number" placeholder="e.g., #2345">
                        <small class="text-muted">Unique badge number</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" id="position" class="form-control" name="position" placeholder="e.g., Range Assistant">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="INACTIVE">INACTIVE</option>
                        </select>
                    </div>

                    <div class="col text-end mt-2">
                        <button type="submit" class="btn btn-danger w-100 mt-2">
                            <i class="ti ti-device-floppy me-1"></i>
                            Save Assistant
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Assistant Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Assistant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_assistant_id">
                
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" id="edit_full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Badge Number</label>
                    <input type="text" id="edit_badge_number" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input type="text" id="edit_position" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select id="edit_status" class="form-control">
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="INACTIVE">INACTIVE</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="updateAssistant()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="ti ti-trash me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="ti ti-alert-triangle" style="font-size: 48px; color: var(--danger);"></i>
                    <h4 class="mt-3">Are you sure?</h4>
                    <p class="text-muted">You are about to delete assistant: <br>
                        <strong id="delete_assistant_name" class="text-danger"></strong>
                    </p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="ti ti-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('#raTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']],
        language: {
            search: "Search Assistant:"
        }
    });

    $('#confirmDeleteBtn').on('click', function() {
        deleteAssistant();
        var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    });
});

function editAssistant(id, name, badge, position, status) {
    document.getElementById('edit_assistant_id').value = id;
    document.getElementById('edit_full_name').value = name;
    document.getElementById('edit_badge_number').value = badge;
    document.getElementById('edit_position').value = position;
    document.getElementById('edit_status').value = status;
    
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

var deleteId = null;
var deleteName = '';

function showDeleteModal(id, name) {
    deleteId = id;
    deleteName = name;
    document.getElementById('delete_assistant_name').innerHTML = name;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function deleteAssistant() {
    if(deleteId) {
        var mparam = {
            assistant_id: deleteId,
            meaction: 'DELETE'
        };

        jQuery.ajax({
            type: "POST",
            url: '<?=site_url();?>rangeassistants',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    }
}

function updateAssistant() {
    var assistant_id = document.getElementById('edit_assistant_id').value;
    var full_name = document.getElementById('edit_full_name').value;
    var badge_number = document.getElementById('edit_badge_number').value;
    var position = document.getElementById('edit_position').value;
    var status = document.getElementById('edit_status').value;

    var mparam = {
        assistant_id: assistant_id,
        full_name: full_name,
        badge_number: badge_number,
        position: position,
        status: status,
        meaction: 'EDIT'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>rangeassistants',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data.status == 'success'){
                toastr.success(data.message);
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                toastr.error(data.message);
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error: " + error);
        }
    });
}

// Live Clock Update
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

<script src="<?=base_url('assets/js/rangeassistants/ra.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>