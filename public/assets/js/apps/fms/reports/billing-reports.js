var __BillingReports = new __BillingReports();

function __BillingReports() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    var reportTable = null;
    var currentType = 'billing_summary';

    if ($.fn.dataTable) {
        $.fn.dataTable.ext.errMode = function(settings, techNote, message) {
            console.warn('DataTables warning:', message);
        };
    }

    const statusOptionsMap = {
        billing_summary: [['', 'All Statuses'], ['DRAFT', 'Draft'], ['FOR_INVOICE', 'For Invoice'], ['INVOICED', 'Invoiced'], ['CANCELLED', 'Cancelled']],
        invoice_report: [['', 'All Statuses'], ['DRAFT', 'Draft'], ['ISSUED', 'Issued'], ['PARTIALLY_PAID', 'Partially Paid'], ['PAID', 'Paid'], ['OVERDUE', 'Overdue'], ['CANCELLED', 'Cancelled'], ['VOID', 'Void']],
        payment_report: [['', 'All Statuses'], ['PENDING', 'Pending'], ['CLEARED', 'Cleared'], ['BOUNCED', 'Bounced'], ['CANCELLED', 'Cancelled']],
        accounts_receivable: [],
        ar_aging: [],
        statement_of_account: []
    };

    // ==============================
    // INIT
    // ==============================
    this.__init = function() {
        this.__loadCustomers();
        this.__bindTabs();
        this.__renderStatusOptions('billing_summary');
        this.load('billing_summary');
    };

    // ==============================
    // LOAD CUSTOMERS (filter dropdown)
    // ==============================
    this.__loadCustomers = function() {
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billingreports',
            data: { meaction: 'GET_CUSTOMERS' },
            dataType: 'json',
            success: function(data) {
                var html = '<option value="">All Customers</option>';
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        html += '<option value="' + row.customer_id + '">' + row.customer_name + '</option>';
                    });
                }
                $('#filterCustomer').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading customers: " + error);
            }
        });
    };

    // ==============================
    // TABS
    // ==============================
    this.__bindTabs = function() {
        $('.report-tab').on('click', function() {
            var type = $(this).data('type');

            if (type === 'statement_of_account') {
                window.open(mesiteurl + 'statementofaccount', '_blank');
                return;
            }

            $('.report-tab').removeClass('active');
            $(this).addClass('active');
            currentType = type;

            __BillingReports.__renderStatusOptions(type);
            __BillingReports.load(type);
        });
    };

    this.__renderStatusOptions = function(type) {
        var opts = statusOptionsMap[type] || [];
        if (opts.length === 0) {
            $('#filterStatusWrap').hide();
            $('#filterStatus').html('<option value="">All Statuses</option>');
            return;
        }
        $('#filterStatusWrap').show();
        var html = '';
        $.each(opts, function(i, o) {
            html += '<option value="' + o[0] + '">' + o[1] + '</option>';
        });
        $('#filterStatus').html(html);
    };

    // ==============================
    // LOAD REPORT (AJAX GET_REPORT, or link-out for SOA)
    // ==============================
    this.load = function(type) {
        currentType = type;

        if (type === 'statement_of_account') {
            window.open(mesiteurl + 'statementofaccount', '_blank');
            return;
        }

        var mparam = {
            meaction: 'GET_REPORT',
            report_type: type,
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val(),
            customer_id: $('#filterCustomer').val(),
            status: $('#filterStatus').val()
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billingreports',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (!data || data.status === 'error') {
                    toastr.error((data && data.message) ? data.message : 'Failed to load report.');
                    return;
                }

                if ($.fn.DataTable.isDataTable('#reportTable')) {
                    $('#reportTable').DataTable().destroy();
                }
                $('#reportTable').html(data.html);

                reportTable = $('#reportTable').DataTable({
                    pageLength: 10,
                    lengthChange: false,
                    language: { search: "Search:", emptyTable: "No records found." }
                });

                __BillingReports.__renderStats(data.stats);
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // STAT CARDS
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
    // FILTER / PRINT ACTIONS
    // ==============================
    this.__applyFilters = function() {
        this.load(currentType);
    };

    this.__printPDF = function() {
        if (currentType === 'statement_of_account') {
            var soaParams = { meaction: 'PRINT-SOA', customer_id: $('#filterCustomer').val() };
            window.open(mesiteurl + 'statementofaccount?' + $.param(soaParams), '_blank');
            return;
        }

        var params = {
            meaction: 'PRINT_REPORT',
            report_type: currentType,
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val(),
            customer_id: $('#filterCustomer').val(),
            status: $('#filterStatus').val()
        };

        window.open(mesiteurl + 'billingreports?' + $.param(params), '_blank');
    };
}

$(document).ready(function() {
    __BillingReports.__init();
});
