var __SOA = new __SOA();

function __SOA() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    var lastParams = null;

    $(document).ready(function() {
        var today = new Date();
        var firstOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        $('#soa_date_from').val(firstOfMonth.toISOString().split('T')[0]);
        $('#soa_date_to').val(today.toISOString().split('T')[0]);

        __SOA.__loadCustomers();
    });

    // ==============================
    // LOAD CUSTOMERS
    // ==============================
    this.__loadCustomers = function() {
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'statementofaccount',
            data: { meaction: 'GET_CUSTOMERS' },
            dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select Customer —</option>';
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        opts += '<option value="' + row.customer_id + '">' + row.customer_name + '</option>';
                    });
                }
                $('#soa_customer_id').html(opts);
                $('#adj_customer_id').html(opts);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading customers: " + error);
            }
        });
    };

    // ==============================
    // GENERATE STATEMENT
    // ==============================
    this.__generate = function() {
        var customer_id = $('#soa_customer_id').val();
        var date_from = $('#soa_date_from').val();
        var date_to = $('#soa_date_to').val();

        if (!customer_id) {
            toastr.warning('Please select a customer', 'Missing field');
            return;
        }

        var mparam = { customer_id: customer_id, date_from: date_from, date_to: date_to, meaction: 'GET_SOA' };
        lastParams = mparam;

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'statementofaccount',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status !== 'success') {
                    toastr.error(data.message);
                    return;
                }

                $('#soaEmptyState').hide();
                $('#soaResultsCard').show();
                $('#adjustmentsCard').show();

                $('#soa_customer_name_display').text(data.customer.customer_name);
                $('#soa_beginning').text('₱' + parseFloat(data.beginning_balance).toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#soa_total_debit').text('₱' + parseFloat(data.total_debit).toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#soa_total_credit').text('₱' + parseFloat(data.total_credit).toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#soa_ending').text('₱' + parseFloat(data.ending_balance).toLocaleString('en-US', { minimumFractionDigits: 2 }));

                var html = '';
                html += '<tr class="table-light"><td colspan="5"><strong>Beginning Balance</strong></td><td class="text-end"><strong>₱' +
                        parseFloat(data.beginning_balance).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</strong></td></tr>';

                if (data.transactions && data.transactions.length > 0) {
                    $.each(data.transactions, function(i, t) {
                        html += '<tr>';
                        html += '<td>' + new Date(t.transaction_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) + '</td>';
                        html += '<td><span class="badge badge-secondary">' + (t.reference_number || '—') + '</span></td>';
                        html += '<td>' + t.description + '</td>';
                        html += '<td class="text-end">' + (parseFloat(t.debit) > 0 ? '₱' + parseFloat(t.debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '—') + '</td>';
                        html += '<td class="text-end">' + (parseFloat(t.credit) > 0 ? '₱' + parseFloat(t.credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '—') + '</td>';
                        html += '<td class="text-end">₱' + parseFloat(t.running_balance).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html += '<tr><td colspan="6" class="text-center text-muted">No transactions in this date range</td></tr>';
                }
                $('#soaTransactionsBody').html(html);

                __SOA.__loadAdjustments(customer_id);
            },
            error: function(xhr, status, error) {
                toastr.error("Error generating statement: " + error);
            }
        });
    };

    // ==============================
    // ADJUSTMENTS
    // ==============================
    this.__loadAdjustments = function(customer_id) {
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'statementofaccount',
            data: { customer_id: customer_id, meaction: 'GET_ADJUSTMENTS' },
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        html += '<tr>';
                        html += '<td><span class="badge badge-primary">' + row.adjustment_code + '</span></td>';
                        html += '<td>' + new Date(row.adjustment_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) + '</td>';
                        html += '<td>' + (row.adjustment_type === 'DEBIT' ? '<span class="badge badge-danger">Debit</span>' : '<span class="badge badge-success">Credit</span>') + '</td>';
                        html += '<td>' + (row.reason || '—') + '</td>';
                        html += '<td class="text-end">₱' + parseFloat(row.amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</td>';
                        html += '<td class="text-center"><button class="btn-icon btn-icon-delete" onclick="__SOA.__deleteAdjustment(' + row.adjustment_id + ')" title="Delete"><i class="bi bi-trash"></i></button></td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No adjustments</td></tr>';
                }
                $('#adjustmentsBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading adjustments: " + error);
            }
        });
    };

    this.__openAdjustmentModal = function() {
        $('#adj_date').val(new Date().toISOString().split('T')[0]);
        $('#adj_type').val('DEBIT');
        $('#adj_amount').val('');
        $('#adj_reference').val('');
        $('#adj_reason').val('');
        $('#adj_remarks').val('');

        var currentCustomer = $('#soa_customer_id').val();
        if (currentCustomer) $('#adj_customer_id').val(currentCustomer);

        var modal = new bootstrap.Modal(document.getElementById('adjustmentModal'));
        modal.show();
    };

    this.__saveAdjustment = function() {
        var customer_id = $('#adj_customer_id').val();
        var amount = $('#adj_amount').val();

        if (!customer_id) {
            toastr.warning('Please select a customer', 'Missing field');
            return;
        }
        if (!amount || parseFloat(amount) <= 0) {
            toastr.warning('Please enter a valid amount', 'Missing field');
            $('#adj_amount').focus();
            return;
        }

        var mparam = {
            customer_id: customer_id,
            adjustment_date: $('#adj_date').val(),
            adjustment_type: $('#adj_type').val(),
            amount: amount,
            reference_number: $('#adj_reference').val(),
            reason: $('#adj_reason').val(),
            remarks: $('#adj_remarks').val(),
            meaction: 'SAVE_ADJUSTMENT'
        };

        var btn = $('#adjustmentSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'statementofaccount',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Adjustment');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('adjustmentModal'));
                    modal.hide();

                    if ($('#soa_customer_id').val() === customer_id) {
                        __SOA.__generate();
                    }
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Adjustment');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__deleteAdjustment = function(adjustment_id) {
        if (!confirm('Delete this adjustment? This cannot be undone.')) return;

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'statementofaccount',
            data: { adjustment_id: adjustment_id, meaction: 'DELETE_ADJUSTMENT' },
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    __SOA.__generate();
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // PRINT
    // ==============================
    this.__printSOA = function() {
        if (!lastParams) return;
        var url = mesiteurl + 'statementofaccount?meaction=PRINT-SOA&customer_id=' + lastParams.customer_id +
                   '&date_from=' + lastParams.date_from + '&date_to=' + lastParams.date_to;
        $('#pdfFrame').attr('src', url);
        var modal = new bootstrap.Modal(document.getElementById('pdfModal'));
        modal.show();
    };
}
