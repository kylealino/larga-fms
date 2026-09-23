var __Payment = new __Payment();

function __Payment() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    // ==============================
    // RESET FORM
    // ==============================
    this.__resetForm = function() {
        $('#payment_id').val('');
        $('#payment_date').val(new Date().toISOString().split('T')[0]);
        $('#amount_paid').val('');
        $('#payment_method').val('CASH');
        $('#payment_bank').val('');
        $('#reference_number').val('');
        $('#payment_status').val('CLEARED');
        $('#payment_remarks').val('');
        $('#receipt_file').val('');
        $('#receiptLinkWrap').hide();
        $('#invoiceBalanceBox').hide();
        $('#payment_invoice_id').prop('disabled', false);
    };

    // ==============================
    // NEW PAYMENT
    // ==============================
    this.__openAddPayment = function() {
        this.__resetForm();
        $('#paymentModalTitle').html('<i class="bi bi-credit-card me-2"></i>New Payment');
        $('#paymentSubmitBtn').text('Save Payment').attr('onclick', '__Payment.__savePayment()');

        this.__loadPayableInvoices(null);

        var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    };

    this.__loadPayableInvoices = function(selectedId) {
        var mparam = { meaction: 'GET_PAYABLE_INVOICES' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'payment',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select Invoice —</option>';
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        opts += '<option value="' + row.invoice_id + '" data-total="' + row.total_amount + '" data-paid="' + row.amount_paid + '" data-balance="' + row.outstanding_balance + '">' +
                                row.invoice_code + ' — ' + (row.customer_name || '—') + ' (Balance: ₱' + parseFloat(row.outstanding_balance).toLocaleString('en-US', { minimumFractionDigits: 2 }) + ')' +
                                '</option>';
                    });
                }
                $('#payment_invoice_id').html(opts);
                if (selectedId) $('#payment_invoice_id').val(selectedId);
                __Payment.__updateInvoiceBalanceBox();
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading invoices: " + error);
            }
        });
    };

    this.__updateInvoiceBalanceBox = function() {
        var opt = $('#payment_invoice_id option:selected');
        if (opt.val()) {
            $('#pi_total').text('₱' + parseFloat(opt.data('total') || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#pi_paid').text('₱' + parseFloat(opt.data('paid') || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#pi_balance').html('<strong>₱' + parseFloat(opt.data('balance') || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</strong>');
            $('#invoiceBalanceBox').show();
        } else {
            $('#invoiceBalanceBox').hide();
        }
    };

    $(document).on('change', '#payment_invoice_id', function() {
        __Payment.__updateInvoiceBalanceBox();
    });

    // ==============================
    // VIEW / EDIT PAYMENT
    // ==============================
    this.__openViewPayment = function(payment_id) {
        var mparam = { payment_id: payment_id, meaction: 'GET_PAYMENT' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'payment',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (!data) {
                    toastr.error('Payment not found.');
                    return;
                }

                __Payment.__resetForm();

                $('#payment_id').val(data.payment_id);
                $('#payment_invoice_id').html('<option value="' + data.invoice_id + '">' + data.invoice_code + ' — ' + (data.customer_name || '—') + '</option>');
                $('#payment_invoice_id').prop('disabled', true);
                $('#pi_total').text('₱' + parseFloat(data.invoice_total || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#pi_paid').text('₱' + parseFloat(data.amount_paid || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#pi_balance').html('<strong>₱' + parseFloat(data.invoice_balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</strong>');
                $('#invoiceBalanceBox').show();

                $('#payment_date').val(data.payment_date);
                $('#amount_paid').val(data.amount_paid);
                $('#payment_method').val(data.payment_method);
                $('#payment_bank').val(data.bank);
                $('#reference_number').val(data.reference_number);
                $('#payment_status').val(data.payment_status);
                $('#payment_remarks').val(data.remarks);

                if (data.receipt_attachment) {
                    $('#receiptLink').attr('href', mesiteurl + data.receipt_attachment);
                    $('#receiptLinkWrap').show();
                }

                $('#paymentModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Payment');
                $('#paymentSubmitBtn').text('Update Payment').attr('onclick', '__Payment.__updatePayment()');

                var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading payment: " + error);
            }
        });
    };

    // ==============================
    // SAVE / UPDATE PAYMENT
    // ==============================
    this.__validateForm = function() {
        var invoice_id = $('#payment_invoice_id').val();
        var amount = $('#amount_paid').val();

        if (!invoice_id) {
            toastr.warning('Please select an invoice', 'Missing field');
            return false;
        }
        if (!amount || parseFloat(amount) <= 0) {
            toastr.warning('Please enter a valid amount', 'Missing field');
            $('#amount_paid').focus();
            return false;
        }
        return true;
    };

    this.__uploadReceiptIfAny = function(payment_id, callback) {
        var fileInput = document.getElementById('receipt_file');
        if (!fileInput.files || !fileInput.files[0]) {
            callback();
            return;
        }

        var formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('payment_id', payment_id);
        formData.append('meaction', 'UPLOAD_RECEIPT');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'payment',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function() { callback(); },
            error: function() { callback(); }
        });
    };

    this.__savePayment = function() {
        if (!this.__validateForm()) return;

        var mparam = {
            invoice_id: $('#payment_invoice_id').val(),
            payment_date: $('#payment_date').val(),
            amount_paid: $('#amount_paid').val(),
            payment_method: $('#payment_method').val(),
            bank: $('#payment_bank').val(),
            reference_number: $('#reference_number').val(),
            payment_status: $('#payment_status').val(),
            remarks: $('#payment_remarks').val(),
            meaction: 'SAVE_PAYMENT'
        };

        var btn = $('#paymentSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'payment',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    __Payment.__uploadReceiptIfAny(data.payment_id, function() {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1200);
                    });
                } else {
                    btn.prop('disabled', false);
                    btn.html('<i class="bi bi-save"></i> Save Payment');
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Payment');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__updatePayment = function() {
        if (!this.__validateForm()) return;

        var payment_id = $('#payment_id').val();

        var mparam = {
            payment_id: payment_id,
            payment_date: $('#payment_date').val(),
            amount_paid: $('#amount_paid').val(),
            payment_method: $('#payment_method').val(),
            bank: $('#payment_bank').val(),
            reference_number: $('#reference_number').val(),
            payment_status: $('#payment_status').val(),
            remarks: $('#payment_remarks').val(),
            meaction: 'UPDATE_PAYMENT'
        };

        var btn = $('#paymentSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'payment',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    __Payment.__uploadReceiptIfAny(payment_id, function() {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1200);
                    });
                } else {
                    btn.prop('disabled', false);
                    btn.html('<i class="bi bi-save"></i> Update Payment');
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update Payment');
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // DELETE PAYMENT
    // ==============================
    this.__deletePayment = function(payment_id, receipt_number) {
        $('#delete_payment_name').text(receipt_number);
        $('#confirmDeleteBtn').attr('onclick', '__Payment.__confirmDelete(' + payment_id + ')');
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };

    this.__confirmDelete = function(payment_id) {
        var mparam = { payment_id: payment_id, meaction: 'DELETE_PAYMENT' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'payment',
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
}
