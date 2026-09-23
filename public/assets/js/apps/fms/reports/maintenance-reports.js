var __MaintenanceReports = new __MaintenanceReports();

function __MaintenanceReports() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    var reportTable;
    var currentType = 'maintenance_history';

    if ($.fn.dataTable) {
        $.fn.dataTable.ext.errMode = function(settings, techNote, message) {
            console.warn('DataTables warning:', message);
        };
    }

    // ==============================
    // STATUS OPTIONS PER REPORT TYPE
    // ==============================
    var statusOptionsMap = {
        maintenance_history: [
            ['', 'All Statuses'], ['COMPLETED', 'Completed'], ['IN_PROGRESS', 'In Progress'],
            ['PENDING', 'Pending'], ['CANCELLED', 'Cancelled']
        ],
        maintenance_cost: [['', 'All Statuses']],
        upcoming_maintenance: [['', 'All Statuses']],
        parts_replacement: [['', 'All Statuses']],
        registration_expiration: [['', 'All Statuses']],
        insurance_expiration: [['', 'All Statuses']],
        tools_inventory: [
            ['', 'All Statuses'], ['AVAILABLE', 'Available'], ['ASSIGNED', 'Assigned'],
            ['UNDER_REPAIR', 'Under Repair'], ['DAMAGED', 'Damaged'], ['RETIRED', 'Retired'], ['LOST', 'Lost']
        ],
        tool_issuance: [
            ['', 'All Statuses'], ['ISSUED', 'Issued'], ['RETURNED', 'Returned']
        ],
        supplies_inventory: [
            ['', 'All Statuses'], ['IN_STOCK', 'In Stock'], ['LOW_STOCK', 'Low Stock'], ['OUT_OF_STOCK', 'Out of Stock']
        ],
        stock_movement: [
            ['', 'All Types'], ['STOCK_IN', 'Stock In'], ['STOCK_OUT', 'Stock Out'], ['RETURN', 'Return'],
            ['ADJUSTMENT', 'Adjustment'], ['DAMAGED', 'Damaged'], ['DISPOSAL', 'Disposal']
        ],
        low_stock: [['', 'All Statuses']]
    };

    // Reports that are point-in-time snapshots — date filters are ignored server-side
    var snapshotReports = ['tools_inventory', 'supplies_inventory', 'low_stock', 'registration_expiration', 'insurance_expiration'];

    // ==============================
    // BUILD CURRENT FILTERS
    // ==============================
    this.__getFilters = function() {
        return {
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val(),
            status: $('#filterStatus').val()
        };
    };

    // ==============================
    // REFRESH STATUS DROPDOWN FOR TYPE
    // ==============================
    this.__refreshStatusOptions = function(type) {
        var options = statusOptionsMap[type] || [['', 'All Statuses']];
        var html = '';
        $.each(options, function(i, opt) {
            html += '<option value="' + opt[0] + '">' + opt[1] + '</option>';
        });
        $('#filterStatus').html(html);
        $('#filterStatus').prop('disabled', options.length <= 1);

        var isSnapshot = snapshotReports.indexOf(type) !== -1;
        $('#filterDateFrom, #filterDateTo').prop('disabled', isSnapshot);
    };

    // ==============================
    // LOAD A REPORT
    // ==============================
    this.load = function(el) {
        var type = (typeof el === 'string') ? el : $(el).data('report');
        currentType = type;

        $('.report-tab').removeClass('active');
        $('.report-tab[data-report="' + type + '"]').addClass('active');

        this.__refreshStatusOptions(type);

        var filters = this.__getFilters();
        var mparam = $.extend({ meaction: 'GET_REPORT', report_type: type }, filters);

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'maintenancereports',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    if ($.fn.DataTable.isDataTable('#reportTable')) {
                        $('#reportTable').DataTable().destroy();
                    }
                    $('#reportTable').html(data.html);

                    reportTable = $('#reportTable').DataTable({
                        pageLength: 10,
                        lengthChange: false,
                        language: { search: "Search:", emptyTable: "No records found for the selected filters." }
                    });

                    $('#reportTitle').html('<i class="bi bi-table me-2"></i>' + data.title);
                    $('#recordCount').text(reportTable.rows().count() + ' records');

                    __MaintenanceReports.__renderStats(data.stats);
                } else {
                    toastr.error(data.message || 'Unable to load report.');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // RENDER STAT CARDS
    // ==============================
    this.__renderStats = function(stats) {
        var html = '';
        if (stats && stats.length > 0) {
            $.each(stats, function(i, s) {
                html += '<div class="stat-card">';
                html += '<div class="stat-left">';
                html += '<div class="stat-label">' + s.label + '</div>';
                html += '<div class="stat-value">' + s.value + '</div>';
                html += '</div>';
                html += '<div class="stat-right"><i class="bi ' + (s.icon || 'bi-graph-up') + '"></i></div>';
                html += '</div>';
            });
        }
        $('#statGrid').html(html);
    };

    // ==============================
    // APPLY FILTERS
    // ==============================
    this.applyFilters = function() {
        this.load(currentType);
    };

    // ==============================
    // PRINT PDF
    // ==============================
    this.printPdf = function() {
        var filters = this.__getFilters();
        var mparam = $.extend({ meaction: 'PRINT_REPORT', report_type: currentType }, filters);
        var url = mesiteurl + 'maintenancereports?' + $.param(mparam);
        window.open(url, '_blank');
    };

    // ==============================
    // INIT
    // ==============================
    $(document).ready(function() {
        __MaintenanceReports.load('maintenance_history');
    });
}
