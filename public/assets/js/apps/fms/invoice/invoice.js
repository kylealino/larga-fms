var __Invoice = new __Invoice();

function __Invoice() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    // ==============================
    // NEW INVOICE — PICK A BILLING
    // ==============================
    this.__openAddInvoice = function() {
        $('#pickBillingBody').html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');

        var modal = new bootstrap.Modal(document.getElementById('pickBillingModal'));
        modal.show();

        this.__loadInvoiceableBillings();
    };

    this.__loadInvoiceableBillings = function() {
        var mparam = { meaction: 'GET_INVOICEABLE_BILLINGS' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'invoice',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        html += '<tr>';
                        html += '<td><span class="badge badge-primary">' + row.billing_code + '</span></td>';
                        html += '<td>' + (row.customer_name || '—') + '</td>';
                        html += '<td>' + (row.billing_date ? new Date(row.billing_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : '—') + '</td>';
                        html += '<td class="text-end">₱' + parseFloat(row.total).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</td>';
                        html += '<td class="text-center"><button class="btn btn-primary btn-sm" onclick="__Invoice.__pickBilling(' + row.billing_id + ')"><i class="bi bi-check2"></i> Select</button></td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted">No billings available for invoicing</td></tr>';
                }
                $('#pickBillingBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading billings: " + error);
                $('#pickBillingBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    };

    this.__pickBilling = function(billing_id) {
        var modal = new bootstrap.Modal(document.getElementById('pickBillingModal'));
        modal.show();
        this.__loadInvoiceableBillings();

        var mparam = {
            billing_id: billing_id,
            invoice_date: new Date().toISOString().split('T')[0],
            meaction: 'CREATE_INVOICE_FROM_BILLING'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'invoice',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('pickBillingModal'));
                    if (modal) modal.hide();
                    __Invoice.__openViewInvoice(data.invoice_id);
                } else {
                    toastr.error(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('pickBillingModal'));
                    if (modal) modal.hide();
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // VIEW / EDIT INVOICE
    // ==============================
    this.__openViewInvoice = function(invoice_id) {
        var mparam = { invoice_id: invoice_id, meaction: 'GET_INVOICE' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'invoice',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (!data) {
                    toastr.error('Invoice not found.');
                    return;
                }
                $('#invoice_id').val(data.invoice_id);
                $('#invoice_code_display').text(data.invoice_code);
                $('#invoice_customer_display').text(data.customer_name || '—');
                $('#invoice_billing_display').text(data.billing_code || '—');
                $('#invoice_date').val(data.invoice_date);
                $('#invoice_due_date').val(data.due_date);
                $('#invoice_payment_terms').val(data.payment_terms);
                $('#invoice_discount').val(data.discount);
                $('#invoice_status').val(data.invoice_status);
                $('#invoice_amount_paid').val('₱' + parseFloat(data.amount_paid || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#invoice_remarks').val(data.remarks);

                __Invoice.__updateCalcDisplay(data);

                var modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
                modal.show();
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading invoice: " + error);
            }
        });
    };

    this.__updateCalcDisplay = function(data) {
        $('#calc_taxable').text('₱' + parseFloat(data.taxable_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#calc_vat').text('₱' + parseFloat(data.vat || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#calc_total').text('₱' + parseFloat(data.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#calc_balance').html('<strong>₱' + parseFloat(data.outstanding_balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</strong>');
    };

    this.__updateInvoice = function() {
        var invoice_id = $('#invoice_id').val();
        var invoice_date = $('#invoice_date').val();

        if (!invoice_date) {
            toastr.warning('Please select invoice date', 'Missing field');
            $('#invoice_date').focus();
            return;
        }

        var mparam = {
            invoice_id: invoice_id,
            invoice_date: invoice_date,
            due_date: $('#invoice_due_date').val(),
            payment_terms: $('#invoice_payment_terms').val(),
            discount: $('#invoice_discount').val() || 0,
            invoice_status: $('#invoice_status').val(),
            remarks: $('#invoice_remarks').val(),
            meaction: 'UPDATE_INVOICE'
        };

        var btn = $('#invoiceSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'invoice',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Invoice');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Invoice');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__deleteInvoice = function(invoice_id, invoice_code) {
        $('#delete_invoice_name').text(invoice_code);
        $('#confirmDeleteBtn').attr('onclick', '__Invoice.__confirmDelete(' + invoice_id + ')');
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };

    this.__confirmDelete = function(invoice_id) {
        var mparam = { invoice_id: invoice_id, meaction: 'DELETE_INVOICE' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'invoice',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                    setTimeout(function() { location.reload(); }, 1000);
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
    // PRINT INVOICE
    // ==============================
    this.__printInvoice = function(invoice_id) {
        $('#pdfFrame').attr('src', mesiteurl + 'invoice?meaction=PRINT-INVOICE&invoice_id=' + invoice_id);
        var modal = new bootstrap.Modal(document.getElementById('pdfModal'));
        modal.show();
    };
}
