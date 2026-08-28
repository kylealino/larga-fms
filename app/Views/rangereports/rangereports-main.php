<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// GET CURRENT DATA
// ==============================
$current_year = date('Y');
$current_month = date('m');
$current_date = date('Y-m-d');

// Total Transactions
$total_transactions = $this->db->query("SELECT COUNT(*) as total FROM tbl_transactions")->getRow()->total;
$total_revenue = $this->db->query("SELECT SUM(total_amount) as total FROM tbl_transactions WHERE status != 'CANCELLED'")->getRow()->total;
$total_shooters = $this->db->query("SELECT COUNT(DISTINCT shooter_name) as total FROM tbl_transactions")->getRow()->total;
$total_active = $this->db->query("SELECT COUNT(*) as total FROM tbl_transactions WHERE status = 'ACTIVE'")->getRow()->total;

// Get shooter types count
$pnp_count = $this->db->query("SELECT COUNT(*) as total FROM tbl_transactions WHERE shooter_type = 'PNP'")->getRow()->total;
$civilian_count = $this->db->query("SELECT COUNT(*) as total FROM tbl_transactions WHERE shooter_type = 'CIVILIAN'")->getRow()->total;

// Get stage usage
$stage_usage = $this->db->query("
    SELECT stage_id, COUNT(*) as total 
    FROM tbl_transactions 
    GROUP BY stage_id 
    ORDER BY stage_id
")->getResultArray();

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
        --qcpd-blue: #2b6cb0;
        --qcpd-blue-dark: #1a365d;
        --qcpd-blue-light: #ebf4ff;
        --range-red: #dc2626;
        --range-red-light: #fee2e2;
        --range-dark: #1a202c;
        --range-gray: #6c757d;
        --range-border: #e5e7eb;
        --range-gray-dark: #6b7280;
        --range-blue: #2b6cb0;
        --range-blue-light: #ebf4ff;
    }

    body { background: #f8f9fa; }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .stats-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid var(--range-border);
        transition: all 0.2s;
    }

    .stats-card:hover {
        border-color: var(--qcpd-blue);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .stats-card .stats-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--range-gray);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .stats-card .stats-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--range-dark);
        font-family: 'Inter', monospace;
    }

    .stats-card .stats-sub {
        font-size: 10px;
        color: var(--range-gray);
        margin-top: 2px;
    }

    .stats-card .stats-icon {
        font-size: 20px;
        opacity: 0.15;
        float: right;
        color: var(--qcpd-blue);
    }

    /* Report Section */
    .report-section {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 20px;
        border: 1px solid var(--range-border);
    }
    
    .report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--range-border);
    }

    .report-header h6 {
        font-size: 13px;
        font-weight: 600;
        color: var(--range-dark);
        margin: 0;
    }

    .report-header h6 i {
        color: var(--qcpd-blue);
    }

    .report-filters {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
        margin-bottom: 14px;
    }

    .filter-group {
        flex: 1;
        min-width: 140px;
    }

    .filter-group label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        color: var(--range-gray);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-group label i {
        color: var(--qcpd-blue);
    }

    .filter-group input, .filter-group select {
        width: 100%;
        padding: 7px 10px;
        border: 1.5px solid var(--range-border);
        border-radius: 8px;
        font-size: 12px;
        color: var(--range-dark);
        background: #ffffff;
        transition: all 0.2s;
        height: 36px;
    }

    .filter-group input:focus, .filter-group select:focus {
        outline: none;
        border-color: var(--qcpd-blue);
        box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.08);
    }

    /* Report Cards - Single Row */
    .report-cards {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-top: 4px;
    }

    .report-card {
        background: #ffffff;
        border: 1.5px solid var(--range-border);
        border-radius: 10px;
        padding: 14px 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px -8px rgba(0,0,0,0.12);
        border-color: var(--qcpd-blue);
    }

    .report-card .report-icon {
        font-size: 24px;
        color: var(--qcpd-blue);
        opacity: 0.6;
        display: block;
        margin-bottom: 4px;
    }

    .report-card .report-title {
        font-size: 11px;
        font-weight: 600;
        color: var(--range-dark);
        margin-bottom: 2px;
    }

    .report-card .report-desc {
        font-size: 9px;
        color: var(--range-gray);
        margin-bottom: 0;
    }

    .report-card .report-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        font-size: 7px;
        font-weight: 700;
        text-transform: uppercase;
        background: var(--qcpd-blue);
        color: #ffffff;
        padding: 1px 8px;
        border-radius: 10px;
        opacity: 0.8;
    }

    .report-card.primary {
        border-color: var(--qcpd-blue);
        background: var(--qcpd-blue-light);
    }

    .report-card.primary .report-icon {
        opacity: 1;
        color: var(--qcpd-blue-dark);
    }

    .report-card.primary .report-badge {
        background: var(--qcpd-blue-dark);
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: var(--range-gray);
        font-size: 12px;
        transition: all 0.2s;
    }

    .breadcrumb-item a:hover {
        color: var(--qcpd-blue);
    }

    .breadcrumb-item.active {
        color: var(--qcpd-blue);
        font-weight: 600;
    }

    /* Modal */
    .modal-content {
        border-radius: 12px;
        border: 1px solid var(--range-border);
    }

    .modal-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--range-border);
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }

    .modal-header .modal-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--range-dark);
    }

    .modal-header .modal-title i {
        color: var(--qcpd-blue);
    }

    .modal-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--range-border);
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }

    .btn-secondary {
        background: #ffffff;
        border: 1.5px solid var(--range-border);
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        color: var(--range-gray);
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        border-color: var(--qcpd-blue);
        color: var(--qcpd-blue);
        background: #ffffff;
    }

    .btn-danger {
        background: var(--qcpd-blue);
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
    }

    .btn-danger:hover {
        background: var(--qcpd-blue-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(43, 108, 176, 0.3);
    }

    .badge.bg-light {
        background: #f3f4f6 !important;
        color: var(--range-gray) !important;
        border: 1px solid var(--range-border);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .qcpd-module-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 20px;
            gap: 12px;
        }
        
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .report-cards {
            grid-template-columns: repeat(5, 1fr);
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
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .report-filters {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
            min-width: unset;
        }
        
        .report-cards {
            grid-template-columns: repeat(2, 1fr);
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
        
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
        
        .stats-card .stats-value {
            font-size: 18px;
        }
        
        .report-cards {
            grid-template-columns: 1fr 1fr;
        }
        
        .report-card {
            padding: 10px 8px;
        }
        
        .report-card .report-icon {
            font-size: 20px;
        }
        
        .report-card .report-title {
            font-size: 10px;
        }
        
        .qcpd-module-header .header-left .module-icon {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }
    }
</style>

<div class="me-rr-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- QCPD UNIFORM HEADER -->
<!-- ============================================ -->
<div class="qcpd-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="ti ti-file-report"></i>
        </div>
        <div class="module-info">
            <h4>
                Range Reports
                <span class="module-badge">Reports & Analytics</span>
            </h4>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- QUICK STATS - 6 Columns -->
<!-- ============================================ -->
<div class="stats-grid">
    <div class="stats-card">
        <i class="ti ti-receipt stats-icon"></i>
        <div class="stats-label">Total Transactions</div>
        <div class="stats-value"><?=$total_transactions;?></div>
        <div class="stats-sub">All time records</div>
    </div>
    <div class="stats-card">
        <i class="ti ti-currency-peso stats-icon"></i>
        <div class="stats-label">Total Revenue</div>
        <div class="stats-value">₱<?=number_format($total_revenue, 2);?></div>
        <div class="stats-sub">Collected amount</div>
    </div>
    <div class="stats-card">
        <i class="ti ti-users stats-icon"></i>
        <div class="stats-label">Total Shooters</div>
        <div class="stats-value"><?=$total_shooters;?></div>
        <div class="stats-sub">Unique shooters</div>
    </div>
    <div class="stats-card">
        <i class="ti ti-user-check stats-icon"></i>
        <div class="stats-label">Active Sessions</div>
        <div class="stats-value"><?=$total_active;?></div>
        <div class="stats-sub">On range now</div>
    </div>
    <div class="stats-card">
        <i class="ti ti-shield stats-icon"></i>
        <div class="stats-label">PNP Shooters</div>
        <div class="stats-value"><?=$pnp_count;?></div>
        <div class="stats-sub">Police personnel</div>
    </div>
    <div class="stats-card">
        <i class="ti ti-user stats-icon"></i>
        <div class="stats-label">Civilian Shooters</div>
        <div class="stats-value"><?=$civilian_count;?></div>
        <div class="stats-sub">Regular civilians</div>
    </div>
</div>

<!-- ============================================ -->
<!-- REPORT FILTER SECTION -->
<!-- ============================================ -->
<div class="row">
    <div class="col-12">
        <div class="report-section">
            <div class="report-header">
                <h6><i class="ti ti-file-report me-2"></i>Generate Reports</h6>
                <span class="badge bg-light text-dark border">5 Reports</span>
            </div>
            
            <!-- Filters -->
            <div class="report-filters">
                <div class="filter-group">
                    <label><i class="ti ti-calendar me-1"></i> From Date</label>
                    <input type="date" id="report_from_date" value="<?=date('Y-m-01');?>">
                </div>
                <div class="filter-group">
                    <label><i class="ti ti-calendar me-1"></i> To Date</label>
                    <input type="date" id="report_to_date" value="<?=date('Y-m-d');?>">
                </div>
                <div class="filter-group">
                    <label><i class="ti ti-user me-1"></i> Shooter</label>
                    <select id="shooter_filter">
                        <option value="">-- All Shooters --</option>
                        <?php
                        $shooters = $this->db->query("SELECT DISTINCT shooter_name FROM tbl_transactions ORDER BY shooter_name")->getResultArray();
                        foreach($shooters as $s):
                        ?>
                        <option value="<?=$s['shooter_name'];?>"><?=$s['shooter_name'];?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="ti ti-layout-grid me-1"></i> Stage</label>
                    <select id="stage_filter">
                        <option value="">-- All Stages --</option>
                        <?php for($i = 1; $i <= 10; $i++): ?>
                        <option value="<?=$i;?>">Stage <?=$i;?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- REPORT CARDS -->
<!-- ============================================ -->
<div class="row">
    <div class="col-sm-12">
        <div class="report-cards">
            <!-- Daily Summary -->
            <div class="report-card p-5" onclick="showDailySummary()">
                <span class="report-badge">PDF</span>
                <i class="ti ti-calendar-stats report-icon"></i>
                <div class="report-title">Daily Summary</div>
                <div class="report-desc">Daily transactions</div>
            </div>
            
            <!-- Shooter Log -->
            <div class="report-card p-5" onclick="showShooterLog()">
                <span class="report-badge">PDF</span>
                <i class="ti ti-list report-icon"></i>
                <div class="report-title">Shooter Log</div>
                <div class="report-desc">All shooter records</div>
            </div>
            
            <!-- Revenue Summary -->
            <div class="report-card p-5" onclick="showRevenueSummary()">
                <span class="report-badge">PDF</span>
                <i class="ti ti-chart-pie report-icon"></i>
                <div class="report-title">Revenue Summary</div>
                <div class="report-desc">Income by type</div>
            </div>
            
            <!-- Stage Usage -->
            <div class="report-card p-5" onclick="showStageUsage()">
                <span class="report-badge">PDF</span>
                <i class="ti ti-layout-grid report-icon"></i>
                <div class="report-title">Stage Usage</div>
                <div class="report-desc">Bay utilization</div>
            </div>
            
            <!-- Shooter History - Primary -->
            <div class="report-card p-5 primary" onclick="showShooterHistory()">
                <span class="report-badge">PDF</span>
                <i class="ti ti-user-search report-icon"></i>
                <div class="report-title">Shooter History</div>
                <div class="report-desc">Per shooter records</div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- REPORT MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-file-report me-2"></i>Report Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="reportFrame" src="" style="width: 100%; height: 85vh;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function showReport(pdfUrl) {
    var reportFrame = document.getElementById("reportFrame");
    var reportModal = new bootstrap.Modal(document.getElementById("reportModal"));
    reportFrame.src = pdfUrl;
    reportModal.show();
}

function printReport() {
    var reportFrame = document.getElementById("reportFrame");
    reportFrame.contentWindow.print();
}

function showDailySummary() {
    var from_date = document.getElementById('report_from_date').value;
    var to_date = document.getElementById('report_to_date').value;
    if(from_date && to_date) {
        showReport('<?=site_url();?>rangereports?meaction=PRINT-DAILY-SUMMARY&from_date=' + from_date + '&to_date=' + to_date);
    } else {
        toastr.error('Please select FROM and TO dates');
    }
}

function showShooterLog() {
    var from_date = document.getElementById('report_from_date').value;
    var to_date = document.getElementById('report_to_date').value;
    var stage = document.getElementById('stage_filter').value;
    if(from_date && to_date) {
        var url = '<?=site_url();?>rangereports?meaction=PRINT-SHOOTER-LOG&from_date=' + from_date + '&to_date=' + to_date;
        if(stage) url += '&stage=' + stage;
        showReport(url);
    } else {
        toastr.error('Please select FROM and TO dates');
    }
}

function showRevenueSummary() {
    var from_date = document.getElementById('report_from_date').value;
    var to_date = document.getElementById('report_to_date').value;
    if(from_date && to_date) {
        showReport('<?=site_url();?>rangereports?meaction=PRINT-REVENUE-SUMMARY&from_date=' + from_date + '&to_date=' + to_date);
    } else {
        toastr.error('Please select FROM and TO dates');
    }
}

function showStageUsage() {
    var from_date = document.getElementById('report_from_date').value;
    var to_date = document.getElementById('report_to_date').value;
    if(from_date && to_date) {
        showReport('<?=site_url();?>rangereports?meaction=PRINT-STAGE-USAGE&from_date=' + from_date + '&to_date=' + to_date);
    } else {
        toastr.error('Please select FROM and TO dates');
    }
}

function showShooterHistory() {
    var shooter = document.getElementById('shooter_filter').value;
    if(shooter) {
        showReport('<?=site_url();?>rangereports?meaction=PRINT-SHOOTER-HISTORY&shooter=' + encodeURIComponent(shooter));
    } else {
        toastr.error('Please select a shooter');
    }
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

<?php
echo view('templates/myfooter.php');
?>