var __Invoice = new __Invoice();

function __Invoice() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    var isCreatingInvoice = false;

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

    // Selecting a billing does NOT create the invoice right away — it opens the
    // Invoice Details modal pre-filled for review (invoice date, payment terms,
    // remarks are all editable). The invoice is only actually generated, with its
    // success toast, when "Generate Invoice" is clicked inside that modal.
    this.__pickBilling = function(billing_id) {
        var pickModal = bootstrap.Modal.getInstance(document.getElementById('pickBillingModal'));
        if (pickModal) pickModal.hide();

        var mparam = { billing_id: billing_id, meaction: 'GET_BILLING' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (!data || !data.billing_id) {
                    toastr.error('Billing not found.');
                    return;
                }
                __Invoice.__openCreateInvoice(data);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading billing: " + error);
            }
        });
    };

    this.__openCreateInvoice = function(billing) {
        isCreatingInvoice = true;

        $('#invoice_id').val('');
        $('#invoice_pending_billing_id').val(billing.billing_id);
        $('#invoiceModalTitle').html('<i class="bi bi-plus-circle me-2"></i>Generate Invoice');
        $('#invoiceSubmitBtnText').text('Generate Invoice');

        $('#invoice_code_display').text('Auto-generated');
        $('#invoice_customer_display').text(billing.customer_name || '—');
        $('#invoice_billing_display').text(billing.billing_code || '—');

        var now = new Date();
        var today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
        $('#invoice_date').val(today);
        $('#invoice_payment_terms').val(billing.customer_payment_terms || '30 Days');
        __Invoice.__previewDueDate();

        // Discount/Status aren't accepted as overrides when generating from a billing
        // (discount is fixed from the billing itself, status always starts DRAFT) —
        // disable them here so the form doesn't imply they can be changed at this step.
        $('#invoice_discount').val(parseFloat(billing.discount || 0).toFixed(2)).prop('disabled', true);
        $('#invoice_status').val('DRAFT').prop('disabled', true);
        $('#invoice_amount_paid').val('₱0.00');
        $('#invoice_remarks').val('');

        __Invoice.__updateCalcDisplay({
            taxable_amount: (parseFloat(billing.subtotal) || 0) - (parseFloat(billing.discount) || 0),
            vat: billing.vat,
            total_amount: billing.total,
            outstanding_balance: billing.total
        });

        var modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
        modal.show();
    };

    // Live due-date preview while generating — mirrors the server's own
    // computeDueDate() (first number found in Payment Terms, or 0 for COD, else 30).
    this.__previewDueDate = function() {
        if (!isCreatingInvoice) return;

        var invoice_date = $('#invoice_date').val();
        if (!invoice_date) return;

        var payment_terms = $('#invoice_payment_terms').val() || '';
        var days = 30;
        var match = payment_terms.match(/(\d+)/);
        if (match) {
            days = parseInt(match[1], 10);
        } else if (payment_terms.toUpperCase() === 'COD') {
            days = 0;
        }

        // Build/format using local Y-M-D components (not toISOString(), which
        // converts through UTC and can roll the date back a day in +UTC zones).
        var parts = invoice_date.split('-');
        var d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        d.setDate(d.getDate() + days);

        var y = d.getFullYear();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        $('#invoice_due_date').val(y + '-' + m + '-' + day);
    };

    this.__createInvoice = function() {
        var billing_id = $('#invoice_pending_billing_id').val();
        var invoice_date = $('#invoice_date').val();

        if (!invoice_date) {
            toastr.warning('Please select invoice date', 'Missing field');
            $('#invoice_date').focus();
            return;
        }

        var mparam = {
            billing_id: billing_id,
            invoice_date: invoice_date,
            payment_terms: $('#invoice_payment_terms').val(),
            remarks: $('#invoice_remarks').val(),
            meaction: 'CREATE_INVOICE_FROM_BILLING'
        };

        var btn = $('#invoiceSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Generating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'invoice',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> <span id="invoiceSubmitBtnText">Generate Invoice</span>');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('invoiceModal'));
                    if (modal) modal.hide();
                    // Navigate to a clean URL (not location.reload()) — arriving here via
                    // Billing's "Create Invoice" action leaves ?from_billing=X in the address
                    // bar, and reloading that same URL would re-trigger the auto-open-modal
                    // logic below for the billing that's now already invoiced.
                    setTimeout(function() { window.location.href = mesiteurl + 'invoice'; }, 1200);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> <span id="invoiceSubmitBtnText">Generate Invoice</span>');
                toastr.error("Error: " + error);
            }
        });
    };

    // Dispatches to invoice creation (from a billing, not yet saved) or updating
    // an existing invoice, depending on which the modal is currently showing.
    this.__saveInvoiceModal = function() {
        if (!$('#invoice_id').val()) {
            __Invoice.__createInvoice();
        } else {
            __Invoice.__updateInvoice();
        }
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
                isCreatingInvoice = false;
                $('#invoice_pending_billing_id').val('');
                $('#invoiceModalTitle').html('<i class="bi bi-file-earmark-text me-2"></i>Invoice Details');
                $('#invoiceSubmitBtnText').text('Save Invoice');
                $('#invoice_discount').prop('disabled', false);
                $('#invoice_status').prop('disabled', false);

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
