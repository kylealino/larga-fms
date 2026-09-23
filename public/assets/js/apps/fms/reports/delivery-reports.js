var __DeliveryReports = new __DeliveryReports();

function __DeliveryReports() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    var reportTable;
    var currentType = 'delivery_report';

    if ($.fn.dataTable) {
        $.fn.dataTable.ext.errMode = function(settings, techNote, message) {
            console.warn('DataTables warning:', message);
        };
    }

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
    // LOAD A REPORT
    // ==============================
    this.load = function(el) {
        var type = (typeof el === 'string') ? el : $(el).data('report');
        currentType = type;

        $('.report-tab').removeClass('active');
        $('.report-tab[data-report="' + type + '"]').addClass('active');

        var filters = this.__getFilters();
        var mparam = $.extend({ meaction: 'GET_REPORT', report_type: type }, filters);

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'deliveryreports',
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

                    __DeliveryReports.__renderStats(data.stats);
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
        var url = mesiteurl + 'deliveryreports?' + $.param(mparam);
        window.open(url, '_blank');
    };

    // ==============================
    // INIT
    // ==============================
    $(document).ready(function() {
        __DeliveryReports.load('delivery_report');
    });
}
