var __OperationsReports = new __OperationsReports();

function __OperationsReports() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    var reportTable;
    var currentType = 'trip_report';

    if ($.fn.dataTable) {
        $.fn.dataTable.ext.errMode = function(settings, techNote, message) {
            console.warn('DataTables warning:', message);
        };
    }

    // ==============================
    // CURRENT FILTER VALUES
    // ==============================
    function getFilters() {
        return {
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val(),
            status: $('#filterStatus').val()
        };
    }

    // ==============================
    // LOAD A REPORT
    // ==============================
    this.load = function(el) {
        if (typeof el === 'string') {
            currentType = el;
        } else {
            currentType = $(el).data('report');
            $('.report-tab').removeClass('active');
            $(el).addClass('active');
        }

        var mparam = $.extend({ meaction: 'GET_REPORT', report_type: currentType }, getFilters());

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'operationsreports',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status !== 'success') {
                    toastr.error(data.message || 'Error loading report.');
                    return;
                }

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
                $('#recordCount').text($('#reportTable tbody tr').length + ' records');

                renderStats(data.stats);
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // RENDER STAT CARDS
    // ==============================
    function renderStats(stats) {
        var html = '';
        if (stats && stats.length > 0) {
            $.each(stats, function(i, stat) {
                html += '<div class="stat-card">';
                html += '<div class="stat-left">';
                html += '<div class="stat-label">' + stat.label + '</div>';
                html += '<div class="stat-value">' + stat.value + '</div>';
                html += '</div>';
                html += '<div class="stat-right"><i class="bi ' + (stat.icon || 'bi-bar-chart') + '"></i></div>';
                html += '</div>';
            });
        }
        $('#statGrid').html(html);
    }

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
        var mparam = $.extend({ meaction: 'PRINT_REPORT', report_type: currentType }, getFilters());
        var url = mesiteurl + 'operationsreports?' + $.param(mparam);
        window.open(url, '_blank');
    };

    // ==============================
    // INIT
    // ==============================
    $(document).ready(function() {
        __OperationsReports.load('trip_report');
    });
}
