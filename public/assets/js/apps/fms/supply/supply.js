var __Sup = new __Sup();

function __Sup() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Supplies initialized, URL: ' + mesiteurl);

    // ==========================================
    // SUPPLY MODAL (Create / Edit)
    // ==========================================
    this.__openSupplyModal = function(supply_id) {
        if (supply_id) {
            var mparam = { supply_id: supply_id, meaction: 'GET_SUPPLY' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-supply',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data && data.supply_id) {
                        $('#sup_id').val(data.supply_id);
                        $('#sup_name').val(data.supply_name);
                        $('#sup_category').val(data.category);
                        $('#sup_unit').val(data.unit);
                        $('#sup_cost').val(data.unit_cost);
                        $('#sup_stock').val(data.current_stock);
                        $('#sup_min').val(data.minimum_stock);
                        $('#sup_reorder').val(data.reorder_level);
                        $('#sup_supplier').val(data.supplier);
                        $('#sup_location').val(data.storage_location);
                        $('#sup_status_display').val(data.status);
                        $('#sup_remarks').val(data.remarks);

                        $('#supplyModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Supply');
                        $('#supBtnText').text('Update Supply');
                        $('#supSubmitBtn').attr('onclick', '__Sup.__updateSupply()');
                    }
                }
            });
        } else {
            $('#sup_id').val('');
            $('#sup_name').val('');
            $('#sup_category').val('');
            $('#sup_unit').val('');
            $('#sup_cost').val('');
            $('#sup_stock').val('0');
            $('#sup_min').val('0');
            $('#sup_reorder').val('0');
            $('#sup_supplier').val('');
            $('#sup_location').val('');
            $('#sup_status_display').val('Auto-computed');
            $('#sup_remarks').val('');

            $('#supplyModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Supply');
            $('#supBtnText').text('Save Supply');
            $('#supSubmitBtn').attr('onclick', '__Sup.__saveSupply()');
        }

        var modal = new bootstrap.Modal(document.getElementById('supplyModal'));
        modal.show();
    };

    this.__saveSupply = function() {
        var name = $('#sup_name').val().trim();
        var unit = $('#sup_unit').val().trim();

        if (!name) {
            toastr.warning('Please enter supply name', 'Missing field');
            $('#sup_name').focus();
            return;
        }
        if (!unit) {
            toastr.warning('Please enter unit', 'Missing field');
            $('#sup_unit').focus();
            return;
        }

        var mparam = {
            supply_name: name,
            category: $('#sup_category').val(),
            unit: unit,
            unit_cost: $('#sup_cost').val() || 0,
            current_stock: $('#sup_stock').val() || 0,
            minimum_stock: $('#sup_min').val() || 0,
            reorder_level: $('#sup_reorder').val() || 0,
            supplier: $('#sup_supplier').val(),
            storage_location: $('#sup_location').val(),
            remarks: $('#sup_remarks').val(),
            meaction: 'SAVE_SUPPLY'
        };

        var btn = $('#supSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-supply',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('supplyModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Supply Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Supply');
            }
        });
    };

    this.__updateSupply = function() {
        var supply_id = $('#sup_id').val();
        var name = $('#sup_name').val().trim();
        var unit = $('#sup_unit').val().trim();

        if (!name) { toastr.warning('Please enter supply name'); return; }
        if (!unit) { toastr.warning('Please enter unit'); return; }

        var mparam = {
            supply_id: supply_id,
            supply_name: name,
            category: $('#sup_category').val(),
            unit: unit,
            unit_cost: $('#sup_cost').val() || 0,
            current_stock: $('#sup_stock').val() || 0,
            minimum_stock: $('#sup_min').val() || 0,
            reorder_level: $('#sup_reorder').val() || 0,
            supplier: $('#sup_supplier').val(),
            storage_location: $('#sup_location').val(),
            remarks: $('#sup_remarks').val(),
            meaction: 'UPDATE_SUPPLY'
        };

        var btn = $('#supSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-supply',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('supplyModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update Supply Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update Supply');
            }
        });
    };

    this.__deleteSupply = function(supply_id) {
        if (confirm('Delete this supply? All related transactions will remain but will reference a deleted supply.')) {
            var mparam = { supply_id: supply_id, meaction: 'DELETE_SUPPLY' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-supply',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1200);
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Error: " + error);
                }
            });
        }
    };

    // ==========================================
    // TRANSACTION MODAL
    // ==========================================
    this.__openTransactionModal = function(type, supply_id) {
        $('#txn_type').val(type);

        // Reset
        $('#txn_date').val(new Date().toISOString().split('T')[0]);
        $('#txn_supply_id').val('');
        $('#txn_quantity').val('0');
        $('#txn_cost').val('');
        $('#txn_ref').val('');
        $('#txn_vendor').val('');
        $('#txn_issued_to').val('');
        $('#txn_truck_id').val('');
        $('#txn_truck_plate').val('');
        $('#txn_purpose').val('');
        $('#txn_remarks').val('');
        $('#txn_stock_display').hide();

        // Config per type
        var config = {
            'STOCK_IN':   { title: 'Stock In',     icon: 'bi-arrow-down-circle', info: '<strong>Add stock from purchase / delivery</strong><br>Increases the current stock.', btn: 'Save Stock In' },
            'STOCK_OUT':  { title: 'Stock Out',    icon: 'bi-arrow-up-circle',   info: '<strong>Release stock for use</strong><br>Decreases the current stock.', btn: 'Save Stock Out' },
            'RETURN':     { title: 'Return',       icon: 'bi-arrow-return-left', info: '<strong>Return unused stock back to inventory</strong><br>Increases the current stock.', btn: 'Save Return' },
            'ADJUSTMENT': { title: 'Adjustment',   icon: 'bi-sliders',           info: '<strong>Set stock to a specific value</strong><br>Enter the NEW total stock value in the quantity field.', btn: 'Save Adjustment' },
            'DAMAGED':    { title: 'Damaged',      icon: 'bi-exclamation-triangle', info: '<strong>Record damaged stock</strong><br>Decreases the current stock.', btn: 'Save Damaged' },
            'DISPOSAL':   { title: 'Disposal',     icon: 'bi-trash',             info: '<strong>Record disposal of stock</strong><br>Decreases the current stock.', btn: 'Save Disposal' }
        };

        var cfg = config[type] || config['STOCK_IN'];

        $('#transactionModalTitle').html('<i class="bi ' + cfg.icon + ' me-2"></i>' + cfg.title);
        $('#txnBtnText').text(cfg.btn);
        $('#txn_info_text').html(cfg.info);

        if (type == 'STOCK_IN' || type == 'RETURN' || type == 'DAMAGED' || type == 'DISPOSAL') {
            $('.txn-in-field').hide();
            $('.txn-out-field').hide();
        } else if (type == 'STOCK_OUT') {
            $('.txn-in-field').hide();
            $('.txn-out-field').show();
        } else {
            $('.txn-in-field').hide();
            $('.txn-out-field').hide();
        }

        // STOCK_IN shows Vendor
        if (type == 'STOCK_IN') {
            $('.txn-in-field').show();
        }

        // Quantity label
        if (type == 'ADJUSTMENT') {
            $('#txn_qty_label').html('New Total Stock <span class="required">*</span>');
        } else {
            $('#txn_qty_label').html('Quantity <span class="required">*</span>');
        }

        var modal = new bootstrap.Modal(document.getElementById('transactionModal'));
        modal.show();

        if (supply_id) {
            setTimeout(function() {
                $('#txn_supply_id').val(supply_id);
                __Sup.__onTxnSupplyChange($('#txn_supply_id')[0]);
            }, 300);
        }
    };

    this.__onTxnSupplyChange = function(el) {
        var code = $(el).find(':selected').data('code') || '';
        var unit = $(el).find(':selected').data('unit') || '';
        var cost = $(el).find(':selected').data('cost') || '';
        var stock = $(el).find(':selected').data('stock') || 0;

        if (code) {
            $('#txn_stock_display').show();
            $('#txn_stock_current').text(parseFloat(stock).toFixed(2));
            $('#txn_stock_unit').text(unit);
        } else {
            $('#txn_stock_display').hide();
        }

        if (cost && !$('#txn_cost').val()) {
            $('#txn_cost').val(cost);
        }
    };

    this.__onTxnTruckChange = function(el) {
        var plate = $(el).find(':selected').data('plate') || '';
        $('#txn_truck_plate').val(plate);
    };

    this.__saveTransaction = function() {
        var type = $('#txn_type').val();
        var supply_id = $('#txn_supply_id').val();
        var quantity = $('#txn_quantity').val();

        if (!supply_id) {
            toastr.warning('Please select a supply', 'Missing field');
            $('#txn_supply_id').focus();
            return;
        }
        if (!quantity || parseFloat(quantity) <= 0) {
            toastr.warning('Please enter a valid quantity', 'Missing field');
            $('#txn_quantity').focus();
            return;
        }

        var mparam = {
            transaction_type: type,
            transaction_date: $('#txn_date').val(),
            supply_id: supply_id,
            quantity: quantity,
            unit_cost: $('#txn_cost').val() || 0,
            reference_number: $('#txn_ref').val(),
            supplier_vendor: $('#txn_vendor').val(),
            issued_to: $('#txn_issued_to').val(),
            truck_id: $('#txn_truck_id').val() || null,
            truck_plate: $('#txn_truck_plate').val(),
            purpose: $('#txn_purpose').val(),
            remarks: $('#txn_remarks').val(),
            meaction: 'SAVE_TRANSACTION'
        };

        var btn = $('#txnSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-supply',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('transactionModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Transaction Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> ' + $('#txnBtnText').text());
            }
        });
    };

    this.__deleteTransaction = function(transaction_id) {
        if (confirm('Delete this transaction? Stock will be recalculated from remaining transactions.')) {
            var mparam = { transaction_id: transaction_id, meaction: 'DELETE_TRANSACTION' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-supply',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1200);
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Error: " + error);
                }
            });
        }
    };

    // ==========================================
    // SUPPLY JOURNEY TIMELINE
    // ==========================================
    this.__openJourneyModal = function(supply_id, supply_name) {
        $('#journey_supply_id').val(supply_id);
        $('#journey_supply_name').text(supply_name);
        $('#journeyContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');

        var modal = new bootstrap.Modal(document.getElementById('journeyModal'));
        modal.show();

        var mparam = { supply_id: supply_id, meaction: 'GET_SUPPLY_JOURNEY' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-supply',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                var html = '';

                if (data && data.length > 0) {
                    html += '<div class="journey-timeline">';
                    $.each(data, function(i, item) {
                        var cls = 'j-registered';
                        var type = (item.type || '').toUpperCase();
                        if (type.indexOf('STOCK IN') !== -1) cls = 'j-in';
                        else if (type.indexOf('STOCK OUT') !== -1) cls = 'j-out';
                        else if (type.indexOf('RETURN') !== -1) cls = 'j-return';
                        else if (type.indexOf('ADJUSTMENT') !== -1) cls = 'j-adjust';
                        else if (type.indexOf('DAMAGED') !== -1) cls = 'j-damaged';
                        else if (type.indexOf('DISPOSAL') !== -1) cls = 'j-dispose';

                        var dateStr = item.date ? new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : '—';

                        html += '<div class="journey-item ' + cls + '">';
                        html += '<div class="j-header">';
                        html += '<span class="j-type">' + (item.type || '') + '</span>';
                        html += '<span class="j-date">' + dateStr + '</span>';
                        html += '</div>';
                        html += '<div class="j-description">' + (item.description || '') + '</div>';
                        if (item.details) {
                            html += '<div class="j-details">' + item.details + '</div>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-4 text-muted">' +
                           '<i class="bi bi-inbox" style="font-size:48px;opacity:0.3;"></i>' +
                           '<h5 class="mt-3">No journey records yet</h5>' +
                           '<p>This supply has no recorded transactions.</p></div>';
                }

                $('#journeyContent').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Supply Journey Error:', status, error, xhr.responseText);
                $('#journeyContent').html(
                    '<div class="text-center py-4 text-danger">' +
                    '<i class="bi bi-exclamation-triangle" style="font-size:48px;opacity:0.5;"></i>' +
                    '<h5 class="mt-3">Failed to load journey</h5>' +
                    '<p style="font-size:12px;">' + error + '</p></div>'
                );
            }
        });
    };
}

$(document).ready(function() {
    console.log('Supplies module ready');
});