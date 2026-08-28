<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// =====================================================
// SELECTED DATE
// =====================================================

$selected_date = $this->request->getGet('selected_date');

if(empty($selected_date)){
    $selected_date = date('Y-m-d');
}

// =====================================================
// MEMBER ATTENDANCE
// =====================================================

$memberQuery = $this->db->query("
    SELECT 
        ch.checkin_id,
        m.first_name,
        m.last_name,
        COALESCE(ms.membership_plan, 'No Active Plan') as membership_plan,
        ch.rfid_uid,
        ch.checkin_time,
        ch.checkout_time,
        ch.status,
        CASE 
            WHEN ms.membership_end_date < CURDATE() THEN 'Expired'
            ELSE ms.membership_status
        END as membership_status
    FROM tbl_checkin_history ch
    LEFT JOIN tbl_members m
        ON m.member_id = ch.member_id
    LEFT JOIN tbl_memberships ms
        ON m.member_id = ms.member_id
        AND ch.checkin_time BETWEEN ms.membership_start_date AND ms.membership_end_date
    WHERE DATE(ch.checkin_time) = ?
    ORDER BY ch.checkin_time DESC
", [$selected_date]);

$memberAttendance = $memberQuery->getResultArray();

// =====================================================
// WALKIN ATTENDANCE
// =====================================================

$walkinQuery = $this->db->query("
    SELECT *
    FROM tbl_walkin_checkin_history
    WHERE DATE(checkin_time) = ?
    ORDER BY checkin_time DESC
", [$selected_date]);

$walkinAttendance = $walkinQuery->getResultArray();

// =====================================================
// ZUMBA ATTENDANCE
// =====================================================

$zumbaQuery = $this->db->query("
    SELECT *
    FROM tbl_zumba_checkin_history
    WHERE DATE(checkin_time) = ?
    ORDER BY checkin_time DESC
", [$selected_date]);

$zumbaAttendance = $zumbaQuery->getResultArray();

// =====================================================
// CROSSFIT ATTENDANCE
// =====================================================

$crossfitQuery = $this->db->query("
    SELECT *
    FROM tbl_crossfit_checkin_history
    WHERE DATE(checkin_time) = ?
    ORDER BY checkin_time DESC
", [$selected_date]);

$crossfitAttendance = $crossfitQuery->getResultArray();

// =====================================================
// YOGA ATTENDANCE
// =====================================================

$yogaQuery = $this->db->query("
    SELECT *
    FROM tbl_yoga_checkin_history
    WHERE DATE(checkin_time) = ?
    ORDER BY checkin_time DESC
", [$selected_date]);

$yogaAttendance = $yogaQuery->getResultArray();

// =====================================================
// PERSONAL TRAINER (PT) ATTENDANCE
// =====================================================

$ptQuery = $this->db->query("
    SELECT *
    FROM tbl_pt_checkin_history
    WHERE DATE(checkin_time) = ?
    ORDER BY checkin_time DESC
", [$selected_date]);

$ptAttendance = $ptQuery->getResultArray();

// =====================================================
// COUNTS
// =====================================================

$totalMembersToday = count($memberAttendance);
$totalWalkinsToday = count($walkinAttendance);
$totalZumbaToday = count($zumbaAttendance);
$totalCrossfitToday = count($crossfitAttendance);
$totalYogaToday = count($yogaAttendance);
$totalPTToday = count($ptAttendance);

echo view('templates/myheader.php');
?>

<style>
    :root {
        --primary: #1e3a5f;
        --primary-dark: #0f2b44;
        --primary-light: #2c5a8c;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
    }

    body {
        background: var(--gray-50);
    }

    .table thead th {
        font-size: 12px;
        font-weight: 600;
        border-bottom: 1px solid var(--gray-200);
        color: var(--gray-500);
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        background: var(--gray-50);
    }

    .table tbody td {
        font-size: 13px;
        vertical-align: middle;
        color: var(--gray-700);
        padding-top: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--gray-100);
    }

    .table-hover tbody tr:hover {
        background: var(--gray-50);
    }

    .card {
        border: 1px solid var(--gray-200);
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }

    .card-header {
        background: #ffffff !important;
        border-bottom: 1px solid var(--gray-200);
        padding: 16px 24px;
    }

    .card-body {
        padding: 20px 24px;
    }

    .attendance-card {
        border-radius: 20px;
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .attendance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -12px rgba(0, 0, 0, 0.1);
        border-color: var(--gray-300);
    }

    .attendance-value {
        font-size: 32px;
        font-weight: 700;
        line-height: 1.2;
        color: var(--gray-800);
    }

    .attendance-icon {
        font-size: 42px;
        opacity: 0.12;
        color: var(--primary);
    }

    .form-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .form-control, .form-select {
        border: 1.5px solid var(--gray-200);
        border-radius: 12px;
        min-height: 42px;
        font-size: 13px;
        font-weight: 500;
        color: var(--gray-700);
        background: #ffffff;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.08);
        outline: none;
    }

    .btn-dark {
        background: var(--primary);
        border-color: var(--primary);
        border-radius: 12px;
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-dark:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-light {
        background: #ffffff;
        border: 1.5px solid var(--gray-200);
        border-radius: 12px;
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
        transition: all 0.2s;
    }

    .btn-light:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #ffffff;
    }

    .badge {
        font-size: 10px;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 30px;
        letter-spacing: 0.3px;
    }

    .bg-primary {
        background: var(--primary) !important;
    }

    .bg-danger {
        background: #ef4444 !important;
    }

    .bg-success {
        background: #10b981 !important;
    }

    .bg-secondary {
        background: var(--gray-400) !important;
    }

    .breadcrumb-item,
    .breadcrumb-item a {
        font-size: 12px;
        color: var(--gray-500);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: var(--primary);
        font-weight: 600;
    }

    .current-view-box {
        padding: 10px 18px;
        background: #ffffff;
        border: 1.5px solid var(--gray-200);
        border-radius: 14px;
        min-width: 160px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .current-view-box small {
        font-size: 10px;
        font-weight: 600;
        color: var(--gray-400);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .current-view-box strong {
        font-size: 14px;
        color: var(--gray-800);
    }

    .custom-tabs {
        border-bottom: 1px solid var(--gray-200);
        margin-bottom: 24px;
        gap: 4px;
    }

    .custom-tabs .nav-link {
        border: none !important;
        background: none !important;
        color: var(--gray-500);
        font-size: 13px;
        font-weight: 600;
        padding: 12px 20px;
        border-radius: 12px 12px 0 0;
        transition: all 0.2s;
    }

    .custom-tabs .nav-link.active {
        color: var(--primary);
        background: transparent !important;
        border-bottom: 2px solid var(--primary) !important;
    }

    .custom-tabs .nav-link:hover {
        color: var(--primary);
    }

    .dataTables_wrapper {
        font-family: 'Inter', sans-serif;
        overflow-x: visible !important;
    }

    .table-responsive {
        overflow-x: visible !important;
        overflow-y: visible !important;
    }

    .dataTables_filter {
        float: right;
        margin-bottom: 20px;
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
        width: 220px;
        padding: 8px 14px;
        border: 1.5px solid var(--gray-200);
        border-radius: 12px;
        font-size: 12px;
        transition: all 0.2s;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.08);
    }

    .dataTables_paginate {
        float: right;
        margin-top: 20px;
    }

    .dataTables_paginate .paginate_button {
        padding: 6px 12px !important;
        margin: 0 3px !important;
        border-radius: 10px !important;
        border: 1px solid var(--gray-200) !important;
        background: #ffffff !important;
        color: var(--gray-600) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        transition: all 0.2s;
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: var(--gray-50) !important;
        border-color: var(--gray-300) !important;
        color: var(--primary) !important;
    }

    .dataTables_paginate .paginate_button.current:hover {
        background: var(--primary-dark) !important;
        border-color: var(--primary-dark) !important;
        color: #ffffff !important;
    }

    .dataTables_info {
        float: left;
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 20px;
    }

    .dataTables_length {
        margin-bottom: 20px;
    }

    .dataTables_length label {
        font-size: 12px;
        color: var(--gray-500);
    }

    .dataTables_length select {
        border: 1.5px solid var(--gray-200);
        border-radius: 10px;
        padding: 5px 10px;
        font-size: 12px;
        margin: 0 5px;
    }

    @media (max-width: 768px) {
        .dataTables_filter,
        .dataTables_paginate,
        .dataTables_info {
            float: none;
            text-align: center;
        }
        .dataTables_filter {
            margin-bottom: 15px;
        }
        .dataTables_filter label {
            justify-content: center;
        }
        .dataTables_paginate {
            margin-top: 15px;
        }
        .dataTables_info {
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .attendance-value {
            font-size: 24px;
        }
        .attendance-icon {
            font-size: 34px;
        }
        .current-view-box {
            min-width: 130px;
            padding: 6px 12px;
        }
        .current-view-box strong {
            font-size: 12px;
        }
    }
</style>

<div class="row mb-2">
    <div class="col-6">
        <h4 class="fw-semibold my-3">Attendance Log</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a class="text-muted text-decoration-none" href="<?=site_url();?>attendance">
                        <i class="ti ti-home fs-5"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">Membership</li>
                <li class="breadcrumb-item active">Attendance Log</li>
            </ol>
        </nav>
    </div>
    <div class="col-6">
        <div class="d-flex justify-content-end align-items-end gap-2 flex-wrap my-3">
            <div style="min-width:260px;">
                <label class="form-label fw-semibold small mb-1">Attendance Date</label>
                <input type="date" id="attendance_date" class="form-control form-control-sm" value="<?=$selected_date;?>">
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-dark btn-sm px-4" onclick="filterAttendance()">
                    <i class="ti ti-search me-1"></i> Filter
                </button>
                <a href="<?=site_url();?>attendance?meaction=MAIN" class="btn btn-light border btn-sm px-4">
                    <i class="ti ti-refresh me-1"></i> Today
                </a>
            </div>
            <div class="current-view-box">
                <small class="text-muted d-block">CURRENT VIEW</small>
                <strong><?=date('F d, Y', strtotime($selected_date));?></strong>
            </div>
        </div>
    </div>
</div>

<!-- DASHBOARD CARDS -->
<div class="row g-3 mb-4">
    <div class="col">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small mb-1">Membership</div><div class="attendance-value"><?=$totalMembersToday;?></div></div>
                <i class="ti ti-user-check attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small mb-1">Walk-In</div><div class="attendance-value"><?=$totalWalkinsToday;?></div></div>
                <i class="ti ti-users attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small mb-1">Zumba</div><div class="attendance-value"><?=$totalZumbaToday;?></div></div>
                <i class="ti ti-activity-heartbeat attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small mb-1">Crossfit</div><div class="attendance-value"><?=$totalCrossfitToday;?></div></div>
                <i class="ti ti-barbell attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small mb-1">Yoga</div><div class="attendance-value"><?=$totalYogaToday;?></div></div>
                <i class="ti ti-stretching attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small mb-1">Personal Trainer</div><div class="attendance-value"><?=$totalPTToday;?></div></div>
                <i class="ti ti-dumbbell attendance-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- TABS -->
<div class="card shadow-sm">
    <div class="card-body">
        <ul class="nav nav-tabs custom-tabs" id="attendanceTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#membershipTab">Membership (<?=$totalMembersToday;?>)</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#walkinTab">Walk-In (<?=$totalWalkinsToday;?>)</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#zumbaTab">Zumba (<?=$totalZumbaToday;?>)</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#crossfitTab">Crossfit (<?=$totalCrossfitToday;?>)</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#yogaTab">Yoga (<?=$totalYogaToday;?>)</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ptTab">Personal Trainer (<?=$totalPTToday;?>)</button></li>
        </ul>

        <div class="tab-content">
            <!-- MEMBERSHIP TAB -->
            <div class="tab-pane fade show active" id="membershipTab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display" id="memberAttendanceTable" width="100%">
                        <thead>
                            <tr><th>#</th><th>Member</th><th>Plan</th><th>RFID</th><th>Check In</th><th>Check Out</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php $count=1; foreach($memberAttendance as $row): ?>
                            <tr><td><?=$count++;?></td><td><strong><?=$row['last_name'];?>, <?=$row['first_name'];?></strong></td><td><?=$row['membership_plan'];?></td><td><?=$row['rfid_uid'];?></td><td><?=date('h:i A', strtotime($row['checkin_time']));?></td>
                            <td><?php if(!empty($row['checkout_time'])): ?><?=date('h:i A', strtotime($row['checkout_time']));?><?php else: ?><span class="badge bg-success">Currently Inside</span><?php endif; ?></td>
                            <td><?php if($row['status'] == 'Active'): ?><span class="badge bg-success">Active</span><?php else: ?><span class="badge bg-secondary"><?=$row['status'];?></span><?php endif; ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- WALKIN TAB -->
            <div class="tab-pane fade" id="walkinTab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display" id="walkinAttendanceTable" width="100%">
                        <thead><tr><th>#</th><th>Name</th><th>Check In</th><th>Check Out</th></tr></thead>
                        <tbody>
                            <?php $count=1; foreach($walkinAttendance as $row): ?>
                            <tr><td><?=$count++;?></td><td><strong><?=$row['walkin_name'];?></strong></td><td><?=date('h:i A', strtotime($row['checkin_time']));?></td><td><?=date('h:i A', strtotime($row['checkout_time']));?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ZUMBA TAB -->
            <div class="tab-pane fade" id="zumbaTab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display" id="zumbaAttendanceTable" width="100%">
                        <thead><tr><th>#</th><th>Name</th><th>Check In</th><th>Check Out</th></tr></thead>
                        <tbody>
                            <?php $count=1; foreach($zumbaAttendance as $row): ?>
                            <tr><td><?=$count++;?></td><td><strong><?=$row['zumba_name'];?></strong></td><td><?=date('h:i A', strtotime($row['checkin_time']));?></td><td><?=date('h:i A', strtotime($row['checkout_time']));?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CROSSFIT TAB -->
            <div class="tab-pane fade" id="crossfitTab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display" id="crossfitAttendanceTable" width="100%">
                        <thead><tr><th>#</th><th>Name</th><th>Check In</th><th>Check Out</th></tr></thead>
                        <tbody>
                            <?php $count=1; foreach($crossfitAttendance as $row): ?>
                            <tr><td><?=$count++;?></td><td><strong><?=$row['crossfit_name'];?></strong></td><td><?=date('h:i A', strtotime($row['checkin_time']));?></td><td><?=date('h:i A', strtotime($row['checkout_time']));?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- YOGA TAB -->
            <div class="tab-pane fade" id="yogaTab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display" id="yogaAttendanceTable" width="100%">
                        <thead><tr><th>#</th><th>Name</th><th>Check In</th><th>Check Out</th></tr></thead>
                        <tbody>
                            <?php $count=1; foreach($yogaAttendance as $row): ?>
                            <tr><td><?=$count++;?></td><td><strong><?=$row['yoga_name'];?></strong></td><td><?=date('h:i A', strtotime($row['checkin_time']));?></td><td><?=date('h:i A', strtotime($row['checkout_time']));?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PERSONAL TRAINER TAB -->
            <div class="tab-pane fade" id="ptTab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display" id="ptAttendanceTable" width="100%">
                        <thead><tr><th>#</th><th>Name</th><th>Check In</th><th>Check Out</th></tr></thead>
                        <tbody>
                            <?php $count=1; foreach($ptAttendance as $row): ?>
                            <tr><td><?=$count++;?></td><td><strong><?=$row['pt_name'];?></strong></td><td><?=date('h:i A', strtotime($row['checkin_time']));?></td><td><?=date('h:i A', strtotime($row['checkout_time']));?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
function filterAttendance(){
    var selected_date = $('#attendance_date').val();
    if(selected_date == ''){
        toastr.error('Please select attendance date');
        return;
    }
    window.location.href = "<?=site_url();?>attendance?meaction=MAIN&selected_date=" + selected_date;
}

$(document).ready(function(){
    // Initialize DataTables for each table
    $('#memberAttendanceTable').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        language: { emptyTable: "No membership attendance records for this date" }
    });

    $('#walkinAttendanceTable').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        lengthChange: false,
        language: { emptyTable: "No walk-in attendance records for this date" }
    });

    $('#zumbaAttendanceTable').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        lengthChange: false,
        language: { emptyTable: "No zumba attendance records for this date" }
    });

    $('#crossfitAttendanceTable').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        lengthChange: false,
        language: { emptyTable: "No crossfit attendance records for this date" }
    });

    $('#yogaAttendanceTable').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        lengthChange: false,
        language: { emptyTable: "No yoga attendance records for this date" }
    });

    $('#ptAttendanceTable').DataTable({
        pageLength: 10,
        order: [[0, 'asc']],
        lengthChange: false,
        language: { emptyTable: "No personal trainer attendance records for this date" }
    });
});
</script>

<?php
echo view('templates/myfooter.php');
?>