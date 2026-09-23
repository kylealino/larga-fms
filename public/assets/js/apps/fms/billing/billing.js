var __Billing = new __Billing();

function __Billing() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    // ==============================
    // NEW BILLING — PICK A DELIVERED DR
    // ==============================
    this.__openAddBilling = function() {
        $('#pickDRBody').html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');

        var modal = new bootstrap.Modal(document.getElementById('pickDRModal'));
        modal.show();

        var mparam = { meaction: 'GET_BILLABLE_DRS' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        html += '<tr>';
                        html += '<td><span class="badge badge-primary">' + row.dr_code + '</span></td>';
                        html += '<td>' + (row.trip_code || '—') + '</td>';
                        html += '<td>' + (row.customer_name || '—') + '</td>';
                        html += '<td>' + (row.dr_date ? new Date(row.dr_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : '—') + '</td>';
                        html += '<td class="text-center"><button class="btn btn-primary btn-sm" onclick="__Billing.__pickDR(' + row.dr_id + ')"><i class="bi bi-check2"></i> Select</button></td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted">No delivered DRs available for billing</td></tr>';
                }
                $('#pickDRBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading delivered DRs: " + error);
                $('#pickDRBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    };

    this.__pickDR = function(dr_id) {
        var mparam = {
            dr_id: dr_id,
            billing_date: new Date().toISOString().split('T')[0],
            billing_basis: 'PER_TRIP',
            rate: 0,
            quantity: 1,
            discount: 0,
            meaction: 'CREATE_BILLING_FROM_DR'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('pickDRModal'));
                    modal.hide();
                    __Billing.__openViewBilling(data.billing_id);
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
    // VIEW / EDIT BILLING
    // ==============================
    this.__openViewBilling = function(billing_id) {
        var mparam = { billing_id: billing_id, meaction: 'GET_BILLING' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (!data) {
                    toastr.error('Billing not found.');
                    return;
                }
                $('#billing_id').val(data.billing_id);
                $('#billing_dr_id').val(data.dr_id);
                $('#billing_code_display').text(data.billing_code);
                $('#billing_customer_display').text(data.customer_name || '—');
                $('#billing_trip_dr_display').text((data.trip_code || '—') + ' / ' + (data.dr_code || '—'));
                $('#billing_date').val(data.billing_date);
                $('#billing_basis').val(data.billing_basis);
                $('#billing_status').val(data.billing_status);
                $('#billing_rate').val(data.rate);
                $('#billing_quantity').val(data.quantity);
                $('#billing_discount').val(data.discount);
                $('#billing_remarks').val(data.remarks);

                __Billing.__updateCalcDisplay(data);
                __Billing.__loadCharges(billing_id);
                __Billing.__resetChargeForm();

                var modal = new bootstrap.Modal(document.getElementById('billingModal'));
                modal.show();
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading billing: " + error);
            }
        });
    };

    this.__updateCalcDisplay = function(data) {
        $('#calc_subtotal').text('₱' + parseFloat(data.subtotal || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#calc_vat').text('₱' + parseFloat(data.vat || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#calc_other').text('₱' + parseFloat(data.other_charges || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#calc_total').html('<strong>₱' + parseFloat(data.total || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</strong>');
    };

    this.__refreshBillingCalc = function() {
        var billing_id = $('#billing_id').val();
        var mparam = { billing_id: billing_id, meaction: 'GET_BILLING' };
        jQuery.ajax({
            type: "POST", url: mesiteurl + 'billing', data: mparam, dataType: 'json',
            success: function(data) {
                if (data) __Billing.__updateCalcDisplay(data);
            }
        });
    };

    this.__updateBilling = function() {
        var billing_id = $('#billing_id').val();
        var rate = $('#billing_rate').val();

        if (!rate || parseFloat(rate) <= 0) {
            toastr.warning('Please enter a rate', 'Missing field');
            $('#billing_rate').focus();
            return;
        }

        var mparam = {
            billing_id: billing_id,
            billing_date: $('#billing_date').val(),
            billing_basis: $('#billing_basis').val(),
            billing_status: $('#billing_status').val(),
            rate: rate,
            quantity: $('#billing_quantity').val() || 1,
            discount: $('#billing_discount').val() || 0,
            remarks: $('#billing_remarks').val(),
            meaction: 'UPDATE_BILLING'
        };

        var btn = $('#billingSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Billing');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    __Billing.__refreshBillingCalc();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Billing');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__deleteBilling = function(billing_id, billing_code) {
        $('#delete_billing_name').text(billing_code);
        $('#confirmDeleteBtn').attr('onclick', '__Billing.__confirmDelete(' + billing_id + ')');
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };

    this.__confirmDelete = function(billing_id) {
        var mparam = { billing_id: billing_id, meaction: 'DELETE_BILLING' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
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
    // ADDITIONAL CHARGES
    // ==============================
    this.__loadCharges = function(billing_id) {
        var mparam = { billing_id: billing_id, meaction: 'GET_CHARGES' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                var typeLabels = {
                    'TOLL_FEES': 'Toll Fees', 'WAITING_TIME': 'Waiting Time', 'DETENTION': 'Detention',
                    'EXTRA_STOP': 'Extra Stop', 'HANDLING': 'Handling', 'ADDITIONAL_KILOMETER': 'Additional Kilometer', 'OTHER': 'Other'
                };
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + (typeLabels[row.charge_type] || row.charge_type) + '</td>';
                        html += '<td>' + (row.description || '—') + '</td>';
                        html += '<td class="text-end">₱' + parseFloat(row.amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</td>';
                        html += '<td class="text-center"><button class="btn-icon btn-icon-delete" onclick="__Billing.__deleteCharge(' + row.charge_id + ')" title="Remove"><i class="bi bi-trash"></i></button></td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center text-muted">No additional charges</td></tr>';
                }
                $('#chargesBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading charges: " + error);
            }
        });
    };

    this.__resetChargeForm = function() {
        $('#charge_type').val('OTHER');
        $('#charge_description').val('');
        $('#charge_amount').val('');
    };

    this.__saveCharge = function() {
        var billing_id = $('#billing_id').val();
        var amount = $('#charge_amount').val();

        if (!amount || parseFloat(amount) <= 0) {
            toastr.warning('Please enter a charge amount', 'Missing field');
            $('#charge_amount').focus();
            return;
        }

        var mparam = {
            billing_id: billing_id,
            charge_type: $('#charge_type').val(),
            description: $('#charge_description').val(),
            amount: amount,
            meaction: 'SAVE_CHARGE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    __Billing.__resetChargeForm();
                    __Billing.__loadCharges(billing_id);
                    __Billing.__refreshBillingCalc();
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    };

    this.__deleteCharge = function(charge_id) {
        var billing_id = $('#billing_id').val();
        var mparam = { charge_id: charge_id, meaction: 'DELETE_CHARGE' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'billing',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    __Billing.__loadCharges(billing_id);
                    __Billing.__refreshBillingCalc();
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
