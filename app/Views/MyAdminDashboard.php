<?php
// =============================================
// QCPD SHOOTING RANGE - OPERATIONS DASHBOARD
// =============================================

$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');

// Get current user info
$query = $this->db->query("
    SELECT 
        `full_name`, 
        `division`,
        `section`, 
        `position`,
        `username`
    FROM `myua_user` 
    WHERE `username` = '$this->cuser'
");
$data = $query->getRowArray();
$full_name = $data['full_name'] ?? 'User';
$position = $data['position'] ?? '';
$section = $data['section'] ?? '';
$division = $data['division'] ?? '';

// =============================================
// FILTER PARAMETERS
// =============================================
$selectedMonth = $this->request->getGet('filter_month') ?? date('m');
$selectedYear = $this->request->getGet('filter_year') ?? date('Y');
$selectedDate = $this->request->getGet('filter_date') ?? date('Y-m-d');

// =============================================
// RANGE METRICS - DYNAMIC FROM DATABASE
// =============================================

// Get total transactions count
$transCount = $this->db->query("SELECT COUNT(*) as total FROM `tbl_transactions` WHERE `status` = 'COMPLETED'")->getRow();
$totalTransactions = $transCount->total ?? 0;

// Get previous month transactions
$prevMonth = date('m', strtotime('-1 month'));
$prevYear = date('Y', strtotime('-1 month'));
$prevTransCount = $this->db->query("
    SELECT COUNT(*) as total 
    FROM `tbl_transactions` 
    WHERE `status` = 'COMPLETED' 
    AND MONTH(`created_at`) = ? AND YEAR(`created_at`) = ?
", [$prevMonth, $prevYear])->getRow();
$prevTransactions = $prevTransCount->total ?? 0;

// Calculate transactions growth
$transactionsGrowth = $prevTransactions > 0 ? round((($totalTransactions - $prevTransactions) / $prevTransactions) * 100, 1) : 0;

// Get total revenue
$revenueQuery = $this->db->query("
    SELECT COALESCE(SUM(`total_amount`), 0) as total 
    FROM `tbl_transactions` 
    WHERE `status` = 'COMPLETED' 
    AND MONTH(`created_at`) = ? AND YEAR(`created_at`) = ?
", [$selectedMonth, $selectedYear]);
$totalRevenue = $revenueQuery->getRow()->total ?? 0;

// Get previous month revenue
$prevRevenueQuery = $this->db->query("
    SELECT COALESCE(SUM(`total_amount`), 0) as total 
    FROM `tbl_transactions` 
    WHERE `status` = 'COMPLETED' 
    AND MONTH(`created_at`) = ? AND YEAR(`created_at`) = ?
", [date('m', strtotime('-1 month')), date('Y', strtotime('-1 month'))]);
$prevMonthRevenue = $prevRevenueQuery->getRow()->total ?? 0;
$revenueGrowth = $prevMonthRevenue > 0 ? round((($totalRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1) : 0;

// Get bay status from tbl_bay_status
$bayStatusQuery = $this->db->query("
    SELECT 
        `bay_id`, 
        `stage_id`, 
        `status`, 
        `current_transaction_id`, 
        `updated_at` 
    FROM `tbl_bay_status` 
    ORDER BY `bay_id`
");
$bayStatusData = $bayStatusQuery->getResultArray();

$totalBays = count($bayStatusData);
$availableBays = 0;
$occupiedBays = 0;
$reservedBays = 0;
$maintenanceBays = 0;

$bayStatus = [];
$bayShooters = [];
$bayRanks = [];
$bayCheckins = [];
$bayDurations = [];

// Get active transactions for occupied bays
$activeTransactions = [];
if ($totalBays > 0) {
    $activeTransQuery = $this->db->query("
        SELECT 
            t.`transaction_id`,
            t.`stage_id`,
            t.`shooter_name`,
            t.`shooter_type`,
            t.`checkin_time`,
            t.`status`,
            t.`created_at`,
            TIMESTAMPDIFF(HOUR, t.`checkin_time`, CURTIME()) as hours_elapsed
        FROM `tbl_transactions` t
        WHERE t.`status` = 'ACTIVE'
    ");
    $activeTransactions = $activeTransQuery->getResultArray();
    
    // Create lookup for shooters by stage_id
    $shooterLookup = [];
    foreach ($activeTransactions as $trans) {
        $shooterLookup[$trans['stage_id']] = $trans;
    }
}

foreach ($bayStatusData as $bay) {
    $bay_id = $bay['bay_id'];
    $status = strtolower($bay['status']);
    $stage_id = $bay['stage_id'];
    
    // Count statuses
    if ($status == 'available') {
        $availableBays++;
    } elseif ($status == 'reserved') {
        $reservedBays++;
    } elseif ($status == 'maintenance') {
        $maintenanceBays++;
    } else {
        $occupiedBays++;
    }
    
    $bayStatus[$bay_id] = $status;
    
    // Get shooter info if occupied
    if ($status != 'available' && isset($shooterLookup[$stage_id])) {
        $trans = $shooterLookup[$stage_id];
        $bayShooters[$bay_id] = $trans['shooter_name'];
        $bayRanks[$bay_id] = $trans['shooter_type'] == 'PNP' ? 'PNP' : 'Civilian';
        $bayCheckins[$bay_id] = date('h:i A', strtotime($trans['checkin_time']));
        
        // Calculate duration
        $checkinTime = new DateTime($trans['checkin_time']);
        $now = new DateTime();
        $diff = $checkinTime->diff($now);
        $hours = $diff->h + ($diff->days * 24);
        $minutes = $diff->i;
        if ($hours > 0) {
            $bayDurations[$bay_id] = $hours . 'hr' . ($minutes > 0 ? ' ' . $minutes . 'min' : '');
        } else {
            $bayDurations[$bay_id] = $minutes . 'min';
        }
    } elseif ($status == 'reserved') {
        // For reserved bays, try to find next scheduled shooter
        $nextShooter = $this->db->query("
            SELECT `shooter_name`, `shooter_type`, `checkin_time`
            FROM `tbl_transactions`
            WHERE `stage_id` = ? AND `status` = 'SCHEDULED'
            ORDER BY `checkin_time` ASC
            LIMIT 1
        ", [$stage_id])->getRow();
        if ($nextShooter) {
            $bayShooters[$bay_id] = $nextShooter->shooter_name . ' (Scheduled)';
            $bayRanks[$bay_id] = $nextShooter->shooter_type == 'PNP' ? 'PNP' : 'Civilian';
            $bayCheckins[$bay_id] = date('h:i A', strtotime($nextShooter->checkin_time));
        }
    }
}

// Get total shooters today
$todayShooters = $this->db->query("
    SELECT COUNT(DISTINCT `transaction_id`) as total 
    FROM `tbl_transactions` 
    WHERE DATE(`created_at`) = CURDATE()
")->getRow();
$totalShootersToday = $todayShooters->total ?? 0;

// Get currently on range
$currentlyOnRange = $this->db->query("
    SELECT COUNT(*) as total 
    FROM `tbl_transactions` 
    WHERE `status` = 'ACTIVE'
")->getRow();
$currentlyOnRange = $currentlyOnRange->total ?? 0;

// Get total qualified (completed transactions)
$totalQualified = $totalTransactions;

// Get total firearms (count unique transactions)
$totalFirearms = $totalTransactions;

// Range hours from settings or default
$rangeOpen = '08:00';
$rangeClose = '17:00';

// =============================================
// QUEUE/LOBBY - PENDING SHOOTERS
// =============================================
$queueShootersQuery = $this->db->query("
    SELECT 
        `shooter_name`,
        `shooter_type`,
        `checkin_time`,
        `created_at`,
        TIMESTAMPDIFF(MINUTE, `created_at`, NOW()) as waiting_minutes
    FROM `tbl_transactions`
    WHERE `status` = 'PENDING' OR `status` = 'SCHEDULED'
    ORDER BY `created_at` ASC
    LIMIT 10
");
$queueData = $queueShootersQuery->getResultArray();

$queueShooters = [];
foreach ($queueData as $q) {
    $waiting = $q['waiting_minutes'] ?? 0;
    $waitingText = $waiting < 60 ? $waiting . ' min' : floor($waiting/60) . 'hr ' . ($waiting % 60) . 'min';
    $queueShooters[] = [
        'name' => $q['shooter_name'],
        'rank' => $q['shooter_type'],
        'waiting_since' => date('h:i A', strtotime($q['created_at']))
    ];
}

// =============================================
// RECENT ACTIVITY
// =============================================
$recentActivityQuery = $this->db->query("
    SELECT 
        t.`shooter_name`,
        t.`shooter_type`,
        t.`checkin_time`,
        t.`checkout_time`,
        t.`total_amount`,
        t.`status`,
        t.`stage_id`,
        t.`created_at`,
        b.`bay_id`,
        CASE 
            WHEN t.`checkout_time` IS NOT NULL THEN TIMEDIFF(t.`checkout_time`, t.`checkin_time`)
            ELSE TIMEDIFF(NOW(), t.`checkin_time`)
        END as duration
    FROM `tbl_transactions` t
    LEFT JOIN `tbl_bay_status` b ON t.`stage_id` = b.`stage_id`
    WHERE t.`status` IN ('ACTIVE', 'COMPLETED')
    ORDER BY t.`created_at` DESC
    LIMIT 15
");
$recentData = $recentActivityQuery->getResultArray();

$recentActivity = [];
foreach ($recentData as $act) {
    $duration = $act['duration'] ?? '00:00:00';
    $durationParts = explode(':', $duration);
    $hours = intval($durationParts[0] ?? 0);
    $minutes = intval($durationParts[1] ?? 0);
    $durationText = $hours > 0 ? $hours . 'hr' . ($minutes > 0 ? ' ' . $minutes . 'min' : '') : $minutes . 'min';
    
    $statusDisplay = strtolower($act['status']);
    if ($statusDisplay == 'active') $statusDisplay = 'on range';
    
    $recentActivity[] = [
        'shooter' => $act['shooter_name'],
        'badge' => $act['shooter_type'] == 'PNP' ? 'PNP' : 'Civilian',
        'bay' => $act['bay_id'] ? str_pad($act['bay_id'], 2, '0', STR_PAD_LEFT) : '—',
        'checkin' => $act['checkin_time'] ? date('h:i A', strtotime($act['checkin_time'])) : '—',
        'checkout' => $act['checkout_time'] ? date('h:i A', strtotime($act['checkout_time'])) : '—',
        'duration' => $durationText,
        'status' => $statusDisplay,
        'rank' => $act['shooter_type'],
        'amount' => floatval($act['total_amount'] ?? 0)
    ];
}

// =============================================
// REVENUE DATA
// =============================================
$allMonths = [
    '01' => 'January', '02' => 'February', '03' => 'March', 
    '04' => 'April', '05' => 'May', '06' => 'June',
    '07' => 'July', '08' => 'August', '09' => 'September',
    '10' => 'October', '11' => 'November', '12' => 'December'
];

// Get actual revenue data
$revenueTrend = [];
if ($selectedMonth == 'all') {
    foreach ($allMonths as $num => $name) {
        $monthRevenue = $this->db->query("
            SELECT COALESCE(SUM(`total_amount`), 0) as total 
            FROM `tbl_transactions` 
            WHERE `status` = 'COMPLETED' 
            AND MONTH(`created_at`) = ? AND YEAR(`created_at`) = ?
        ", [$num, $selectedYear])->getRow();
        $revenueTrend[] = [
            'month' => substr($name, 0, 3),
            'revenue' => round($monthRevenue->total / 1000, 1)
        ];
    }
} else {
    // Get daily revenue for selected month
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, intval($selectedMonth), intval($selectedYear));
    for ($d = 1; $d <= min($daysInMonth, 15); $d += 2) {
        $date = $selectedYear . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
        $dayRevenue = $this->db->query("
            SELECT COALESCE(SUM(`total_amount`), 0) as total 
            FROM `tbl_transactions` 
            WHERE `status` = 'COMPLETED' 
            AND DATE(`created_at`) = ?
        ", [$date])->getRow();
        $revenueTrend[] = [
            'month' => $d,
            'revenue' => round($dayRevenue->total / 1000, 1)
        ];
    }
}

// If no data, add fallback
if (empty($revenueTrend)) {
    for ($i = 0; $i < 12; $i++) {
        $revenueTrend[] = ['month' => substr($allMonths[str_pad($i+1, 2, '0', STR_PAD_LEFT)], 0, 3), 'revenue' => 0];
    }
}



// =============================================
// RANGE ASSISTANTS DATA - DYNAMIC
// =============================================
$assistantsQuery = $this->db->query("
    SELECT 
        `assistant_id`,
        `full_name`,
        `badge_number`,
        `position`,
        `status`,
        `created_at`,
        `updated_at`
    FROM `tbl_range_assistants`
    ORDER BY `assistant_id`
");
$assistantData = $assistantsQuery->getResultArray();

$rangeAssistants = [];
foreach ($assistantData as $assistant) {
    $status = strtolower($assistant['status']);
    $displayStatus = $status;
    if ($status == 'active') $displayStatus = 'on_duty';
    elseif ($status == 'inactive') $displayStatus = 'off_duty';
    
    // Try to assign location from current assignments
    $location = '—';
    $locQuery = $this->db->query("
        SELECT GROUP_CONCAT(CONCAT('Bay ', b.`bay_id`) SEPARATOR ', ') as locations
        FROM `tbl_transactions` t
        JOIN `tbl_bay_status` b ON t.`stage_id` = b.`stage_id`
        WHERE t.`range_assistant_id` = ? AND t.`status` = 'ACTIVE'
    ", [$assistant['assistant_id']])->getRow();
    if ($locQuery && $locQuery->locations) {
        $location = $locQuery->locations;
    } elseif ($status == 'active') {
        $location = 'Range Area';
    } elseif ($status == 'inactive') {
        $location = '—';
    }
    
    $rangeAssistants[] = [
        'name' => $assistant['full_name'],
        'badge' => $assistant['badge_number'] ?? 'N/A',
        'status' => $displayStatus,
        'location' => $location,
        'position' => $assistant['position']
    ];
}

echo view('templates/myheader.php');
?>

<style>
    /* ============================================ */
    /* QCPD SHOOTING RANGE - PROFESSIONAL DASHBOARD */
    /* ============================================ */
    :root {
        --bg-primary: #f0f4f8;
        --bg-card: #ffffff;
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --text-muted: #718096;
        --border-color: #e2e8f0;
        --accent: #2b6cb0;
        --accent-light: #ebf4ff;
        --success: #38a169;
        --success-light: #f0fff4;
        --warning: #d69e2e;
        --warning-light: #fffbeb;
        --danger: #e53e3e;
        --danger-light: #fff5f5;
        --disabled: #e2e8f0;
        --disabled-text: #a0aec0;
        --mono: 'SF Mono', 'Menlo', 'Monaco', monospace;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.10);
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* ============================================ */
    /* STATUS BADGES */
    /* ============================================ */
    .status-badge {
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 3px 12px;
        border-radius: 4px;
        letter-spacing: 0.3px;
    }

    .status-badge.on-range,
    .status-badge.on-duty {
        background: var(--success-light);
        color: var(--success);
        border: 1px solid #c6f6d5;
    }

    .status-badge.scheduled,
    .status-badge.break {
        background: var(--warning-light);
        color: var(--warning);
        border: 1px solid #fef3c7;
    }

    .status-badge.completed,
    .status-badge.off-duty {
        background: var(--bg-primary);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }

    .status-badge.queue {
        background: var(--accent-light);
        color: var(--accent);
        border: 1px solid #bee3f8;
    }

    /* ============================================ */
    /* HEADER */
    /* ============================================ */
    .qcpd-top-header {
        background: #1a365d;
        background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 16px 24px;
        margin: -24px -24px 24px -24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .qcpd-top-header .brand-section {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        min-width: 200px;
    }

    .qcpd-top-header .brand-section h2 {
        font-size: 17px;
        font-weight: 600;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .qcpd-top-header .brand-section h2 .welcome-icon {
        color: #90cdf4;
        font-size: 18px;
        margin-right: 6px;
    }

    .qcpd-top-header .brand-section h2 .user-name {
        color: #90cdf4;
        font-weight: 600;
    }

    .qcpd-top-header .brand-section .user-meta {
        color: #bee3f8;
        font-size: 12px;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .qcpd-top-header .brand-section .user-meta .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #e2e8f0;
        font-weight: 500;
        font-size: 11px;
    }

    .qcpd-top-header .brand-section .user-meta .role-badge i {
        font-size: 11px;
        color: #90cdf4;
    }

    .qcpd-top-header .brand-section .user-meta .divider {
        color: rgba(255, 255, 255, 0.15);
    }

    .qcpd-top-header .brand-section .user-meta .info-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #e2e8f0;
    }

    .qcpd-top-header .brand-section .user-meta .info-item i {
        color: #90cdf4;
        font-size: 11px;
    }

    .qcpd-top-header .brand-section .user-meta .live-clock {
        color: #ffffff;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.12);
        padding: 2px 10px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .qcpd-top-header .brand-section .user-meta .live-clock i {
        color: #90cdf4;
    }

    /* ============================================ */
    /* FILTER SECTION */
    /* ============================================ */
    .header-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        background: rgba(255, 255, 255, 0.06);
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .header-filter .filter-group {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .header-filter .filter-group label {
        color: #bee3f8;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        white-space: nowrap;
    }

    .header-filter .filter-group select,
    .header-filter .filter-group input[type="date"] {
        padding: 5px 10px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-size: 12px;
        font-weight: 500;
        outline: none;
        transition: all 0.2s;
        min-width: 90px;
        cursor: pointer;
        font-family: inherit;
        height: 32px;
    }

    .header-filter .filter-group select:hover,
    .header-filter .filter-group input[type="date"]:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .header-filter .filter-group select:focus,
    .header-filter .filter-group input[type="date"]:focus {
        border-color: #90cdf4;
        box-shadow: 0 0 0 3px rgba(144, 205, 244, 0.15);
        background: rgba(255, 255, 255, 0.15);
    }

    .header-filter .filter-group select option {
        background: #1a365d;
        color: #ffffff;
        padding: 4px;
    }

    .header-filter .filter-group input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        opacity: 0.7;
        cursor: pointer;
    }

    .header-filter .filter-group input[type="date"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }

    .header-filter .btn-filter-header {
        padding: 5px 18px;
        border-radius: 4px;
        border: none;
        background: #ffffff;
        color: #1a365d;
        font-weight: 600;
        font-size: 11px;
        transition: all 0.2s;
        cursor: pointer;
        white-space: nowrap;
        height: 32px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .header-filter .btn-filter-header:hover {
        background: #90cdf4;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .header-filter .btn-reset-header {
        padding: 5px 14px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: transparent;
        color: #bee3f8;
        font-weight: 500;
        font-size: 11px;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        height: 32px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .header-filter .btn-reset-header:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fa0909;
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* ============================================ */
    /* STAT CARDS */
    /* ============================================ */
    .stat-card {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 20px 24px;
        border: 1px solid var(--border-color);
        height: 100%;
        position: relative;
        transition: all 0.2s;
        box-shadow: var(--shadow-sm);
    }

    .stat-card:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-label i {
        color: var(--accent);
        font-size: 12px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary);
        font-family: var(--mono);
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.5px;
    }

    .stat-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .stat-sub .up { color: var(--success); }
    .stat-sub .down { color: var(--danger); }

    /* ============================================ */
    /* SECTION TITLES */
    /* ============================================ */
    .section-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
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
        font-size: 9px;
        padding: 2px 10px;
        border-radius: 20px;
        margin-left: 4px;
        font-weight: 600;
        border: 1px solid var(--border-color);
    }

    .section-title .badge-count.current-time {
        background: var(--accent-light);
        color: var(--accent);
        border-color: var(--accent);
    }

    .card-container {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 20px 24px;
        border: 1px solid var(--border-color);
        transition: all 0.2s;
        box-shadow: var(--shadow-sm);
    }

    .card-container:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-md);
    }

    /* ============================================ */
    /* BAY CARDS - PROFESSIONAL DESIGN */
    /* ============================================ */
    .bay-card {
        border-radius: 12px;
        padding: 18px 16px;
        height: 100%;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        cursor: default;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        box-shadow: var(--shadow-sm);
    }

    .bay-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        transition: height 0.3s ease;
    }

    .bay-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .bay-card:hover::after {
        height: 4px;
    }

    /* Available Bay */
    .bay-card.bay-available {
        border-top: 1px solid var(--border-color);
    }

    .bay-card.bay-available::after {
        background: var(--success);
    }

    .bay-card.bay-available .bay-status-text {
        color: var(--success);
    }

    .bay-card.bay-available .bay-number {
        color: var(--text-secondary);
    }

    .bay-card.bay-available .bay-status-dot {
        background: var(--success);
        box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.15);
    }

    .bay-card.bay-available:hover {
        border-color: var(--success);
        background: var(--success-light);
    }

    /* Occupied Bay */
    .bay-card.bay-occupied {
        border-top: 1px solid var(--border-color);
    }

    .bay-card.bay-occupied::after {
        background: var(--danger);
    }

    .bay-card.bay-occupied .bay-status-text {
        color: var(--danger);
    }

    .bay-card.bay-occupied .bay-number {
        color: var(--text-secondary);
    }

    .bay-card.bay-occupied .bay-status-dot {
        background: var(--danger);
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.15);
        animation: pulse-dot-danger 2s ease-in-out infinite;
    }

    .bay-card.bay-occupied .bay-shooter {
        color: var(--text-primary);
    }

    .bay-card.bay-occupied:hover {
        border-color: var(--danger);
        background: var(--danger-light);
    }

    /* Reserved Bay */
    .bay-card.bay-reserved {
        border-top: 1px solid var(--border-color);
    }

    .bay-card.bay-reserved::after {
        background: var(--warning);
    }

    .bay-card.bay-reserved .bay-status-text {
        color: var(--warning);
    }

    .bay-card.bay-reserved .bay-number {
        color: var(--text-secondary);
    }

    .bay-card.bay-reserved .bay-status-dot {
        background: var(--warning);
        box-shadow: 0 0 0 3px rgba(214, 158, 46, 0.15);
        animation: pulse-dot-warning 2s ease-in-out infinite;
    }

    .bay-card.bay-reserved:hover {
        border-color: var(--warning);
        background: var(--warning-light);
    }

    /* Maintenance Bay */
    .bay-card.bay-maintenance {
        border-top: 1px solid var(--border-color);
    }

    .bay-card.bay-maintenance::after {
        background: var(--text-muted);
    }

    .bay-card.bay-maintenance .bay-status-text {
        color: var(--text-muted);
    }

    .bay-card.bay-maintenance .bay-number {
        color: var(--text-muted);
    }

    .bay-card.bay-maintenance .bay-status-dot {
        background: var(--text-muted);
    }

    .bay-card.bay-maintenance:hover {
        border-color: var(--text-muted);
        background: var(--bg-primary);
    }

    /* Animations */
    @keyframes pulse-dot-danger {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    @keyframes pulse-dot-warning {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.8; }
    }

    .bay-card .bay-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .bay-card .bay-number {
        font-weight: 600;
        font-size: 14px;
        font-family: var(--mono);
        letter-spacing: 0.3px;
    }

    .bay-card .bay-status-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.9);
        transition: all 0.3s ease;
    }

    .bay-card .bay-status-text {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .bay-card .bay-status-text i {
        font-size: 14px;
    }

    .bay-card .bay-shooter {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        margin-top: 2px;
        line-height: 1.3;
    }

    .bay-card .bay-details {
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 4px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
    }

    .bay-card .bay-details i {
        margin-right: 2px;
        font-size: 10px;
    }

    .bay-card .bay-badge {
        display: inline-block;
        font-size: 8px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border: 1px solid transparent;
    }

    .bay-card .bay-badge.pnp {
        background: var(--accent-light);
        color: var(--accent);
        border-color: rgba(43, 108, 176, 0.15);
    }

    .bay-card .bay-badge.civilian {
        background: var(--bg-primary);
        color: var(--text-muted);
        border-color: var(--border-color);
    }

    .bay-card .bay-time-info {
        display: flex;
        gap: 8px;
        font-size: 9px;
        color: var(--text-muted);
        margin-top: 2px;
        font-family: var(--mono);
    }

    .bay-card .bay-time-info span {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .bay-card .bay-time-info i {
        font-size: 9px;
        color: var(--text-muted);
    }

    /* Queue Card - Professional */
    .queue-card {
        border-radius: 12px;
        padding: 18px 16px;
        border: 2px dashed var(--accent);
        height: 100%;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: var(--accent-light);
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .queue-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--accent);
        background: #dbeafe;
    }

    .queue-card .queue-icon {
        font-size: 28px;
        color: var(--accent);
        margin-bottom: 2px;
    }

    .queue-card .queue-count {
        font-size: 26px;
        font-weight: 700;
        color: var(--accent);
        font-family: var(--mono);
    }

    .queue-card .queue-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--accent);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    .queue-card .queue-names {
        font-size: 9px;
        color: var(--text-secondary);
        margin-top: 6px;
        line-height: 1.8;
        max-height: 55px;
        overflow-y: auto;
        width: 100%;
    }

    .queue-card .queue-names::-webkit-scrollbar {
        width: 3px;
    }

    .queue-card .queue-names::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.03);
        border-radius: 4px;
    }

    .queue-card .queue-names::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 4px;
    }

    .queue-card .queue-names .queue-item {
        display: inline-block;
        background: rgba(255, 255, 255, 0.7);
        padding: 2px 10px;
        border-radius: 12px;
        margin: 2px 4px;
        font-size: 9px;
        font-weight: 500;
        color: var(--text-primary);
        border: 1px solid rgba(43, 108, 176, 0.1);
    }

    .queue-card .queue-names .queue-item small {
        color: var(--text-muted);
        font-weight: 400;
    }

    /* ============================================ */
    /* TABLES */
    /* ============================================ */
    .table td, .table th {
        padding: 10px 8px;
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid var(--border-color);
    }

    .table thead th {
        background: var(--bg-primary);
        color: var(--text-muted);
        font-weight: 600;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
        background: var(--bg-primary);
    }

    .text-muted-light { color: var(--text-muted); }

    /* ============================================ */
    /* LEGEND */
    /* ============================================ */
    .legend {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 11px;
        color: var(--text-muted);
        padding-top: 12px;
        margin-top: 12px;
        border-top: 1px solid var(--border-color);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 4px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* ============================================ */
    /* QUALIFICATION BADGES */
    /* ============================================ */
    .qual-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
    }

    /* ============================================ */
    /* TYPE BADGES */
    /* ============================================ */
    .type-badge {
        display: inline-block;
        font-size: 7px;
        font-weight: 600;
        padding: 1px 6px;
        border-radius: 4px;
        margin-left: 2px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .type-badge.pnp {
        background: var(--accent-light);
        color: var(--accent);
    }

    .type-badge.civilian {
        background: #f7fafc;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }

    /* ============================================ */
    /* RESPONSIVE */
    /* ============================================ */
    @media (max-width: 992px) {
        .qcpd-top-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 20px;
            gap: 12px;
        }
        
        .header-filter {
            justify-content: flex-start;
            flex-wrap: wrap;
            padding: 10px 12px;
            width: 100%;
        }
        
        .header-filter .filter-group select,
        .header-filter .filter-group input[type="date"] {
            min-width: 80px;
            font-size: 11px;
            padding: 4px 8px;
            height: 30px;
        }
    }

    @media (max-width: 768px) {
        .stat-value { font-size: 22px; }
        .qcpd-top-header .brand-section h2 {
            font-size: 16px;
        }
        .qcpd-top-header .brand-section h2 .welcome-icon {
            font-size: 16px;
            margin-right: 4px;
        }
        .qcpd-top-header .brand-section .user-meta {
            font-size: 11px;
            gap: 6px;
        }
        
        .header-filter {
            padding: 8px 10px;
            gap: 6px;
        }
        
        .header-filter .filter-group select,
        .header-filter .filter-group input[type="date"] {
            min-width: 70px;
            font-size: 10px;
            padding: 4px 6px;
            height: 28px;
        }
        
        .header-filter .filter-group label {
            font-size: 9px;
        }
        
        .header-filter .btn-filter-header,
        .header-filter .btn-reset-header {
            font-size: 10px;
            padding: 4px 12px;
            height: 28px;
        }
        
        .bay-card {
            min-height: 120px;
            padding: 14px 12px;
        }
        
        .bay-card .bay-shooter {
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .qcpd-top-header .brand-section h2 {
            font-size: 14px;
        }
        .qcpd-top-header .brand-section h2 .welcome-icon {
            font-size: 14px;
            margin-right: 4px;
        }
        .qcpd-top-header .brand-section .user-meta {
            font-size: 10px;
            gap: 4px;
        }
        .qcpd-top-header .brand-section .user-meta .divider {
            display: none;
        }
        
        .header-filter {
            flex-wrap: wrap;
            gap: 4px;
            padding: 6px 8px;
        }
        
        .header-filter .filter-group {
            flex: 1 1 auto;
            min-width: 60px;
        }
        
        .header-filter .filter-group select,
        .header-filter .filter-group input[type="date"] {
            min-width: 55px;
            font-size: 9px;
            padding: 3px 5px;
            height: 26px;
        }
        
        .header-filter .filter-group label {
            font-size: 8px;
            letter-spacing: 0.3px;
        }
        
        .header-filter .btn-filter-header,
        .header-filter .btn-reset-header {
            font-size: 9px;
            padding: 3px 10px;
            height: 26px;
        }
    }
</style>

<div class="container-fluid px-0">

    <!-- ============================================ -->
    <!-- HEADER -->
    <!-- ============================================ -->
    <div class="qcpd-top-header">
        <div class="brand-section">
            <div>
                <h2>
                    <i class="bi bi-person-circle welcome-icon"></i>
                    <span>Welcome, <span class="user-name"><?= htmlspecialchars($full_name) ?></span></span>
                </h2>
                <div class="user-meta">
                    <span class="role-badge">
                        <i class="bi bi-shield-check"></i> 
                        <?= htmlspecialchars($position) ?>
                    </span>
                    <span class="divider">|</span>
                    <span class="info-item">
                        <i class="bi bi-clock"></i> 
                        <?= $rangeOpen ?> – <?= $rangeClose ?>
                    </span>
                    <span class="divider">|</span>
                    <span class="info-item">
                        <i class="bi bi-calendar3"></i> 
                        <?= date('F d, Y') ?>
                    </span>
                    <span class="divider">|</span>
                    <span class="info-item live-clock">
                        <i class="bi bi-clock-history"></i> 
                        <span id="liveClock"><?= date('h:i A') ?></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- FILTER -->
        <form method="GET" action="" class="header-filter">
            <div class="filter-group">
                <label for="filter_date">Date</label>
                <input type="date" name="filter_date" id="filter_date" value="<?= $selectedDate ?>">
            </div>

            <div class="filter-group">
                <label for="filter_month">Month</label>
                <select name="filter_month" id="filter_month">
                    <option value="all" <?= $selectedMonth == 'all' ? 'selected' : '' ?>>All</option>
                    <?php foreach($allMonths as $num => $name): ?>
                    <option value="<?= $num ?>" <?= $selectedMonth == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter_year">Year</label>
                <select name="filter_year" id="filter_year">
                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="btn-filter-header">
                <i class="bi bi-check2"></i> Apply
            </button>
            <a href="<?= current_url() ?>" class="btn-reset-header">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>

    <!-- ============================================ -->
    <!-- METRICS -->
    <!-- ============================================ -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-label"><i class="bi bi-coin"></i> Revenue</div>
                <div class="stat-value">₱<?= number_format($totalRevenue) ?></div>
                <div class="stat-sub"><span class="<?= $revenueGrowth >= 0 ? 'up' : 'down' ?>"><?= $revenueGrowth >= 0 ? '↑' : '↓' ?> <?= abs($revenueGrowth) ?>%</span> vs last month</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-label"><i class="bi bi-receipt"></i> Transactions</div>
                <div class="stat-value"><?= number_format($totalTransactions) ?></div>
                <div class="stat-sub"><span class="<?= $transactionsGrowth >= 0 ? 'up' : 'down' ?>"><?= $transactionsGrowth >= 0 ? '↑' : '↓' ?> <?= abs($transactionsGrowth) ?>%</span> this month</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-label"><i class="bi bi-layers"></i> Available Bays</div>
                <div class="stat-value"><?= $availableBays ?>/<?= $totalBays ?></div>
                <div class="stat-sub">
                    <span class="text-danger">●</span> <?= $occupiedBays ?> occupied · 
                    <span class="text-warning">●</span> <?= $reservedBays ?> reserved
                    <?php if ($maintenanceBays > 0): ?>
                        · <span class="text-muted">●</span> <?= $maintenanceBays ?> maintenance
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-label"><i class="bi bi-person-badge"></i> On Range</div>
                <div class="stat-value"><?= $currentlyOnRange ?></div>
                <div class="stat-sub"><?= $totalShootersToday ?> total today</div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- BAY DASHBOARD - PROFESSIONAL DESIGN -->
    <!-- ============================================ -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Bay Status 
                    <span class="badge-count"><?= $totalBays ?> Total</span>
                    <span class="badge-count" style="background: var(--success); color: white; border-color: var(--success);">
                        <?= $availableBays ?> Available
                    </span>
                    <span class="badge-count" style="background: var(--danger); color: white; border-color: var(--danger);">
                        <?= $occupiedBays ?> Occupied
                    </span>
                    <span class="badge-count" style="background: var(--warning); color: white; border-color: var(--warning);">
                        <?= $reservedBays ?> Reserved
                    </span>
                    <?php if ($maintenanceBays > 0): ?>
                    <span class="badge-count" style="background: var(--text-muted); color: white; border-color: var(--text-muted);">
                        <?= $maintenanceBays ?> Maintenance
                    </span>
                    <?php endif; ?>
                    <span class="badge-count current-time">
                        <i class="bi bi-clock"></i> <?= date('h:i A') ?>
                    </span>
                </div>
                
                <div class="row g-3">
                    <?php for($bay = 1; $bay <= $totalBays; $bay++): 
                        $status = $bayStatus[$bay] ?? 'available';
                        $shooter = $bayShooters[$bay] ?? null;
                        $rank = $bayRanks[$bay] ?? null;
                        $checkin = $bayCheckins[$bay] ?? null;
                        $duration = $bayDurations[$bay] ?? null;
                        
                        $statusText = 'Available';
                        $statusIcon = 'bi-check-circle';
                        $cardClass = 'bay-available';
                        $statusColor = 'var(--success)';
                        
                        if($status == 'occupied') {
                            $statusText = 'Occupied';
                            $statusIcon = 'bi-x-circle';
                            $cardClass = 'bay-occupied';
                            $statusColor = 'var(--danger)';
                        } elseif($status == 'reserved') {
                            $statusText = 'Reserved';
                            $statusIcon = 'bi-clock';
                            $cardClass = 'bay-reserved';
                            $statusColor = 'var(--warning)';
                        } elseif($status == 'maintenance') {
                            $statusText = 'Maintenance';
                            $statusIcon = 'bi-tools';
                            $cardClass = 'bay-maintenance';
                            $statusColor = 'var(--text-muted)';
                        }
                        
                        $rankClass = ($rank == 'Civilian') ? 'civilian' : 'pnp';
                    ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="bay-card <?= $cardClass ?>">
                            <div class="bay-header">
                                <span class="bay-number">Bay <?= str_pad($bay, 2, '0', STR_PAD_LEFT) ?></span>
                                <span class="bay-status-dot"></span>
                            </div>
                            
                            <?php if($status == 'available'): ?>
                                <div>
                                    <div class="bay-status-text">
                                        <i class="bi <?= $statusIcon ?>"></i> <?= $statusText ?>
                                    </div>
                                    <div class="bay-details">
                                        <i class="bi bi-check-circle"></i> Ready for booking
                                    </div>
                                </div>
                            <?php elseif($status == 'maintenance'): ?>
                                <div>
                                    <div class="bay-status-text">
                                        <i class="bi <?= $statusIcon ?>"></i> <?= $statusText ?>
                                    </div>
                                    <div class="bay-details">
                                        <i class="bi bi-tools"></i> Under maintenance
                                    </div>
                                </div>
                            <?php elseif($status == 'reserved'): ?>
                                <div>
                                    <div class="bay-status-text">
                                        <i class="bi <?= $statusIcon ?>"></i> <?= $statusText ?>
                                    </div>
                                    <?php if ($shooter): ?>
                                        <div class="bay-shooter" style="font-size: 12px;"><?= htmlspecialchars($shooter) ?></div>
                                        <?php if ($rank): ?>
                                        <div class="bay-details">
                                            <span class="bay-badge <?= $rankClass ?>"><?= htmlspecialchars($rank) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="bay-details">
                                            <i class="bi bi-clock"></i> Reserved for next shooter
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div>
                                    <div class="bay-status-text">
                                        <i class="bi <?= $statusIcon ?>"></i> <?= $statusText ?>
                                    </div>
                                    <div class="bay-shooter"><?= htmlspecialchars($shooter) ?></div>
                                    <?php if ($rank): ?>
                                    <div class="bay-details">
                                        <span class="bay-badge <?= $rankClass ?>"><?= htmlspecialchars($rank) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="bay-time-info">
                                        <?php if($checkin): ?>
                                            <span><i class="bi bi-clock"></i> <?= $checkin ?></span>
                                        <?php endif; ?>
                                        <?php if($duration): ?>
                                            <span><i class="bi bi-hourglass"></i> <?= $duration ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                    
                    <!-- Queue/Lobby Card -->
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="queue-card">
                            <div class="queue-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="queue-count"><?= count($queueShooters) ?></div>
                            <div class="queue-label">In Queue / Lobby</div>
                            <div class="queue-names">
                                <?php if (empty($queueShooters)): ?>
                                    <span class="queue-item" style="background: transparent; border: none; color: var(--text-muted);">
                                        No pending shooters
                                    </span>
                                <?php else: ?>
                                    <?php foreach($queueShooters as $q): ?>
                                        <span class="queue-item">
                                            <?= htmlspecialchars($q['name']) ?>
                                            <small>(<?= $q['waiting_since'] ?>)</small>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="legend">
                    <span class="legend-item"><span class="legend-dot" style="background: var(--success);"></span> Available</span>
                    <span class="legend-item"><span class="legend-dot" style="background: var(--danger);"></span> Occupied</span>
                    <span class="legend-item"><span class="legend-dot" style="background: var(--warning);"></span> Reserved</span>
                    <span class="legend-item"><span class="legend-dot" style="background: var(--text-muted);"></span> Maintenance</span>
                    <span class="legend-item"><span class="legend-dot" style="background: var(--accent); border: 2px solid var(--accent);"></span> Queue</span>
                    <span class="legend-item"><i class="bi bi-clock me-1"></i> Real-time occupancy</span>
                    <span class="legend-item"><i class="bi bi-people me-1"></i> <?= count($queueShooters) ?> waiting</span>
                </div>
            </div>
        </div>
    </div>


    <!-- ============================================ -->
    <!-- RECENT TRANSACTIONS & RANGE ASSISTANTS -->
    <!-- ============================================ -->
    <div class="row g-4">
        <div class="col-6">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-activity"></i> Recent Transactions 
                    <span class="badge-count">live</span>
                    <span class="badge-count" style="background: var(--success); color: white; border-color: var(--success);">
                        ₱<?= number_format(array_sum(array_column($recentActivity, 'amount')), 2) ?>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Shooter</th>
                                <th>Type</th>
                                <th>Bay</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentActivity)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No recent transactions</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($recentActivity as $act): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($act['shooter']) ?></strong></td>
                                    <td><span class="type-badge <?= strtolower($act['rank']) == 'civilian' ? 'civilian' : 'pnp' ?>"><?= htmlspecialchars($act['rank']) ?></span></td>
                                    <td><?= $act['bay'] ?></td>
                                    <td><?= $act['checkin'] ?></td>
                                    <td><?= $act['checkout'] ?></td>
                                    <td><?= $act['duration'] ?></td>
                                    <td>
                                        <span style="font-family: var(--mono); font-weight: 600; color: var(--text-primary); font-size: 12px;">
                                            ₱<?= number_format($act['amount'], 2) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= 
                                            $act['status'] == 'on range' ? 'on-range' : 
                                            ($act['status'] == 'queue' ? 'queue' :
                                            ($act['status'] == 'scheduled' ? 'scheduled' : 'completed')) 
                                        ?>"><?= $act['status'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($recentActivity)): ?>
                        <tfoot>
                            <tr style="background: var(--bg-primary); font-weight: 600;">
                                <td colspan="6" style="text-align: right; font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                                    Total Revenue:
                                </td>
                                <td style="font-family: var(--mono); color: var(--success); font-size: 13px;">
                                    ₱<?= number_format(array_sum(array_column($recentActivity, 'amount')), 2) ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-container">
                <div class="section-title">
                    <i class="bi bi-people"></i> Range Assistants 
                    <span class="badge-count"><?= count($rangeAssistants) ?> Total</span>
                    <span class="badge-count" style="background: var(--success); color: white; border-color: var(--success);">
                        <?= count(array_filter($rangeAssistants, function($a) { return $a['status'] == 'on_duty'; })) ?> On Duty
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rangeAssistants)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No range assistants found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($rangeAssistants as $assistant): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($assistant['name']) ?></strong>
                                        <div style="font-size: 9px; color: var(--text-muted);">
                                            <?= htmlspecialchars($assistant['badge']) ?>
                                        </div>
                                    </td>
                                    <td style="font-size: 11px; color: var(--text-secondary);">
                                        <?= htmlspecialchars($assistant['position'] ?? '—') ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $assistant['status'] == 'on_duty' ? 'on-range' : ($assistant['status'] == 'break' ? 'scheduled' : 'completed') ?>">
                                            <?= $assistant['status'] == 'on_duty' ? '● On Duty' : ($assistant['status'] == 'break' ? '⏸ Break' : '◌ Off Duty') ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 11px; color: var(--text-secondary);">
                                        <?= htmlspecialchars($assistant['location']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 small text-muted" style="border-top: 1px solid var(--border-color); padding-top: 8px;">
                    <i class="bi bi-info-circle"></i> <?= count(array_filter($rangeAssistants, function($a) { return $a['status'] == 'on_duty'; })) ?> assistants currently on duty
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Revenue Chart
    const revenueData = <?= json_encode($revenueTrend) ?>;
    
    // Only create chart if there's data and container exists
    if (document.getElementById('revenueChart') && revenueData.length > 0) {
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: revenueData.map(d => d.month),
                datasets: [{
                    label: 'Revenue (₱K)',
                    data: revenueData.map(d => d.revenue),
                    borderColor: '#2b6cb0',
                    backgroundColor: 'rgba(43, 108, 176, 0.04)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#2b6cb0',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.parsed.y + 'K';
                            }
                        }
                    }
                },
                scales: { 
                    y: { 
                        ticks: { callback: (v) => '₱' + v + 'K' },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: { grid: { display: false } }
                }
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

    // Auto-refresh bay status every 30 seconds (optional)
    setTimeout(function() {
        location.reload();
    }, 300000); // Refresh every 5 minutes
</script>

<?php echo view('templates/myfooter.php'); ?>