var __Tire = new __Tire();

function __Tire() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Tire Management initialized, URL: ' + mesiteurl);

    // ==========================================
    // TIRE MODAL (Create / Edit)
    // ==========================================
    this.__openTireModal = function(tire_id) {
        if (tire_id) {
            var mparam = { tire_id: tire_id, meaction: 'GET_TIRE' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tire',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data && data.tire_id) {
                        $('#tire_id').val(data.tire_id);
                        $('#tire_serial').val(data.serial_number);
                        $('#tire_brand').val(data.brand);
                        $('#tire_model').val(data.model);
                        $('#tire_size').val(data.size);
                        $('#tire_tread').val(data.tread_depth_mm);
                        $('#tire_life').val(data.life_expectancy_km);
                        $('#tire_price').val(data.price);
                        $('#tire_mfg_date').val(data.date_of_manufacture);
                        $('#tire_supplier').val(data.supplier);
                        $('#tire_status').val(data.tire_status);
                        $('#tire_remarks').val(data.remarks);

                        $('#tireModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Tire');
                        $('#tireBtnText').text('Update Tire');
                        $('#tireSubmitBtn').attr('onclick', '__Tire.__updateTire()');
                    }
                }
            });
        } else {
            $('#tire_id').val('');
            $('#tire_serial').val('');
            $('#tire_brand').val('');
            $('#tire_model').val('');
            $('#tire_size').val('');
            $('#tire_tread').val('');
            $('#tire_life').val('');
            $('#tire_price').val('');
            $('#tire_mfg_date').val('');
            $('#tire_supplier').val('');
            $('#tire_status').val('IN_STOCK');
            $('#tire_remarks').val('');

            $('#tireModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Tire');
            $('#tireBtnText').text('Save Tire');
            $('#tireSubmitBtn').attr('onclick', '__Tire.__saveTire()');
        }

        var modal = new bootstrap.Modal(document.getElementById('tireModal'));
        modal.show();
    };

    this.__saveTire = function() {
        var serial = $('#tire_serial').val().trim();
        if (!serial) {
            toastr.warning('Please enter serial number', 'Missing field');
            $('#tire_serial').focus();
            return;
        }

        var mparam = {
            serial_number: serial,
            brand: $('#tire_brand').val(),
            model: $('#tire_model').val(),
            size: $('#tire_size').val(),
            tread_depth_mm: $('#tire_tread').val() || 0,
            life_expectancy_km: $('#tire_life').val() || 0,
            price: $('#tire_price').val() || 0,
            date_of_manufacture: $('#tire_mfg_date').val(),
            supplier: $('#tire_supplier').val(),
            tire_status: $('#tire_status').val(),
            remarks: $('#tire_remarks').val(),
            meaction: 'SAVE_TIRE'
        };

        var btn = $('#tireSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('tireModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Tire Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Tire');
            }
        });
    };

    this.__updateTire = function() {
        var tire_id = $('#tire_id').val();
        var serial = $('#tire_serial').val().trim();

        if (!serial) {
            toastr.warning('Please enter serial number', 'Missing field');
            $('#tire_serial').focus();
            return;
        }

        var mparam = {
            tire_id: tire_id,
            serial_number: serial,
            brand: $('#tire_brand').val(),
            model: $('#tire_model').val(),
            size: $('#tire_size').val(),
            tread_depth_mm: $('#tire_tread').val() || 0,
            life_expectancy_km: $('#tire_life').val() || 0,
            price: $('#tire_price').val() || 0,
            date_of_manufacture: $('#tire_mfg_date').val(),
            supplier: $('#tire_supplier').val(),
            tire_status: $('#tire_status').val(),
            remarks: $('#tire_remarks').val(),
            meaction: 'UPDATE_TIRE'
        };

        var btn = $('#tireSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('tireModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update Tire Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update Tire');
            }
        });
    };

    this.__deleteTire = function(tire_id) {
        if (confirm('Delete this tire? All related transactions, installations, and disposals will remain but reference a deleted tire.')) {
            var mparam = { tire_id: tire_id, meaction: 'DELETE_TIRE' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tire',
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
    // TRANSACTION MODAL (IN / OUT)
    // ==========================================
    this.__openTransactionModal = function(type) {
        $('#txn_type').val(type);

        // Reset
        $('#txn_date').val(new Date().toISOString().split('T')[0]);
        $('#txn_tire_id').val('');
        $('#txn_tire_code').val('');
        $('#txn_quantity').val('1');
        $('#txn_price').val('');
        $('#txn_ref').val('');
        $('#txn_vendor').val('');
        $('#txn_purpose').val('');
        $('#txn_truck_id').val('');
        $('#txn_truck_plate').val('');
        $('#txn_released_by').val('');
        $('#txn_remarks').val('');

        if (type === 'IN') {
            $('#transactionModalTitle').html('<i class="bi bi-arrow-down-circle me-2"></i>Inventory In');
            $('#txnBtnText').text('Save Inventory In');
            $('#txn_info_text').html('<strong>Record tires received / purchased</strong><br>These tires will be added to your inventory.');
            $('.txn-in-field').show();
            $('.txn-out-field').hide();
        } else {
            $('#transactionModalTitle').html('<i class="bi bi-arrow-up-circle me-2"></i>Inventory Out');
            $('#txnBtnText').text('Save Inventory Out');
            $('#txn_info_text').html('<strong>Record tires released from inventory</strong><br>Use this when a tire is installed, replaced, or released.');
            $('.txn-in-field').hide();
            $('.txn-out-field').show();
        }

        var modal = new bootstrap.Modal(document.getElementById('transactionModal'));
        modal.show();
    };

    this.__onTxnTireChange = function(el) {
        var code = $(el).find(':selected').data('code') || '';
        var price = $(el).find(':selected').data('price') || '';
        $('#txn_tire_code').val(code);
        if (price && !$('#txn_price').val()) {
            $('#txn_price').val(price);
        }
    };

    this.__onTxnTruckChange = function(el) {
        var plate = $(el).find(':selected').data('plate') || '';
        $('#txn_truck_plate').val(plate);
    };

    this.__saveTransaction = function() {
        var type = $('#txn_type').val();
        var tire_id = $('#txn_tire_id').val();

        if (!tire_id) {
            toastr.warning('Please select a tire', 'Missing field');
            $('#txn_tire_id').focus();
            return;
        }

        var mparam = {
            transaction_type: type,
            transaction_date: $('#txn_date').val(),
            tire_id: tire_id,
            tire_code: $('#txn_tire_code').val(),
            quantity: $('#txn_quantity').val() || 1,
            price: $('#txn_price').val() || 0,
            reference_number: $('#txn_ref').val(),
            supplier_vendor: $('#txn_vendor').val(),
            purpose: $('#txn_purpose').val(),
            truck_id: $('#txn_truck_id').val() || null,
            truck_plate: $('#txn_truck_plate').val(),
            released_by: $('#txn_released_by').val(),
            remarks: $('#txn_remarks').val(),
            meaction: 'SAVE_TRANSACTION'
        };

        var btn = $('#txnSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
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
        if (confirm('Delete this transaction?')) {
            var mparam = { transaction_id: transaction_id, meaction: 'DELETE_TRANSACTION' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tire',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1200);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    };

    // ==========================================
    // INSTALL TIRE
    // ==========================================
    this.__openInstallModal = function(tire_id) {
        // Reset
        $('#install_tire_id').val('');
        $('#install_tire_code').val('');
        $('#install_truck_id').val('');
        $('#install_truck_plate').val('');
        $('#install_position').val('');
        $('#install_date').val(new Date().toISOString().split('T')[0]);
        $('#install_odometer').val('');
        $('#install_by').val('');
        $('#install_remarks').val('');

        var modal = new bootstrap.Modal(document.getElementById('installModal'));
        modal.show();

        if (tire_id) {
            setTimeout(function() {
                $('#install_tire_id').val(tire_id);
                __Tire.__onInstallTireChange($('#install_tire_id')[0]);
            }, 300);
        }
    };

    this.__onInstallTireChange = function(el) {
        var code = $(el).find(':selected').data('code') || '';
        $('#install_tire_code').val(code);
    };

    this.__onInstallTruckChange = function(el) {
        var plate = $(el).find(':selected').data('plate') || '';
        var odo = $(el).find(':selected').data('odo') || '';
        $('#install_truck_plate').val(plate);
        if (odo && !$('#install_odometer').val()) {
            $('#install_odometer').val(odo);
        }
    };

    this.__saveInstallation = function() {
        var tire_id = $('#install_tire_id').val();
        var truck_id = $('#install_truck_id').val();
        var odo = $('#install_odometer').val();
        var date = $('#install_date').val();

        if (!tire_id) {
            toastr.warning('Please select a tire', 'Missing field');
            return;
        }
        if (!truck_id) {
            toastr.warning('Please select a truck', 'Missing field');
            return;
        }
        if (!date) {
            toastr.warning('Please select installation date', 'Missing field');
            return;
        }
        if (!odo) {
            toastr.warning('Please enter installation odometer', 'Missing field');
            return;
        }

        var mparam = {
            tire_id: tire_id,
            tire_code: $('#install_tire_code').val(),
            truck_id: truck_id,
            truck_plate: $('#install_truck_plate').val(),
            position: $('#install_position').val(),
            installation_date: date,
            installation_odometer: odo,
            installed_by: $('#install_by').val(),
            installation_remarks: $('#install_remarks').val(),
            meaction: 'SAVE_INSTALLATION'
        };

        var btn = $('#installSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('installModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Install Tire Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-check-circle"></i> Install Tire');
            }
        });
    };

    this.__deleteInstallation = function(installation_id) {
        if (confirm('Delete this installation record?')) {
            var mparam = { installation_id: installation_id, meaction: 'DELETE_INSTALLATION' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tire',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1200);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    };

    // ==========================================
    // REMOVE TIRE
    // ==========================================
    this.__openRemoveModal = function(installation_id, install_odometer) {
        $('#remove_installation_id').val(installation_id);
        $('#remove_install_odometer').val(install_odometer);
        $('#remove_date').val(new Date().toISOString().split('T')[0]);
        $('#remove_odometer').val('');
        $('#remove_distance').val('');
        $('#remove_reason').val('');
        $('#remove_condition').val('');
        $('#remove_by').val('');
        $('#remove_remarks').val('');

        var modal = new bootstrap.Modal(document.getElementById('removeModal'));
        modal.show();
    };

    this.__calcDistance = function() {
        var install_odo = parseFloat($('#remove_install_odometer').val()) || 0;
        var remove_odo = parseFloat($('#remove_odometer').val()) || 0;
        if (remove_odo > install_odo) {
            $('#remove_distance').val((remove_odo - install_odo).toFixed(2));
        } else {
            $('#remove_distance').val('0');
        }
    };

    this.__saveRemoval = function() {
        var installation_id = $('#remove_installation_id').val();
        var date = $('#remove_date').val();
        var odo = $('#remove_odometer').val();

        if (!date) {
            toastr.warning('Please select removal date', 'Missing field');
            return;
        }
        if (!odo) {
            toastr.warning('Please enter removal odometer', 'Missing field');
            return;
        }

        var mparam = {
            installation_id: installation_id,
            removed_date: date,
            removal_odometer: odo,
            reason_for_removal: $('#remove_reason').val(),
            tire_condition_on_removal: $('#remove_condition').val(),
            removed_by: $('#remove_by').val(),
            removal_remarks: $('#remove_remarks').val(),
            meaction: 'REMOVE_INSTALLATION'
        };

        var btn = $('#removeSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('removeModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Remove Tire Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-check-circle"></i> Confirm Removal');
            }
        });
    };

    this.__viewRemoval = function(installation_id) {
        var mparam = { installation_id: installation_id, meaction: 'GET_INSTALLATION' };
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data) {
                    var html = '';
                    html += '<div class="row">';
                    html += '<div class="col-md-6 mb-2"><small class="text-muted">Removed Date</small><div><strong>' + (data.removed_date || '—') + '</strong></div></div>';
                    html += '<div class="col-md-6 mb-2"><small class="text-muted">Removal Odometer</small><div><strong>' + (data.removal_odometer || 0) + ' km</strong></div></div>';
                    html += '<div class="col-md-6 mb-2"><small class="text-muted">Distance Used</small><div><strong>' + (data.distance_used_km || 0) + ' km</strong></div></div>';
                    html += '<div class="col-md-6 mb-2"><small class="text-muted">Condition</small><div><strong>' + (data.tire_condition_on_removal || '—') + '</strong></div></div>';
                    html += '<div class="col-md-12 mb-2"><small class="text-muted">Reason</small><div><strong>' + (data.reason_for_removal || '—') + '</strong></div></div>';
                    html += '<div class="col-md-6 mb-2"><small class="text-muted">Removed By</small><div><strong>' + (data.removed_by || '—') + '</strong></div></div>';
                    html += '<div class="col-md-12 mb-2"><small class="text-muted">Remarks</small><div><strong>' + (data.removal_remarks || '—') + '</strong></div></div>';
                    html += '</div>';
                    alert('Removal Details:\n\n' + 
                        'Date: ' + (data.removed_date || '—') + '\n' +
                        'Odometer: ' + (data.removal_odometer || 0) + ' km\n' +
                        'Distance Used: ' + (data.distance_used_km || 0) + ' km\n' +
                        'Reason: ' + (data.reason_for_removal || '—') + '\n' +
                        'Condition: ' + (data.tire_condition_on_removal || '—') + '\n' +
                        'Removed By: ' + (data.removed_by || '—') + '\n' +
                        'Remarks: ' + (data.removal_remarks || '—')
                    );
                }
            }
        });
    };

    // ==========================================
    // DISPOSE TIRE
    // ==========================================
    this.__openDisposeModal = function(tire_id) {
        $('#dispose_tire_id').val('');
        $('#dispose_tire_code').val('');
        $('#dispose_date').val(new Date().toISOString().split('T')[0]);
        $('#dispose_reason').val('');
        $('#dispose_final_km').val('');
        $('#dispose_final_status').val('DISPOSED');
        $('#dispose_details').val('');
        $('#dispose_remarks').val('');

        var modal = new bootstrap.Modal(document.getElementById('disposeModal'));
        modal.show();

        if (tire_id) {
            setTimeout(function() {
                $('#dispose_tire_id').val(tire_id);
                __Tire.__onDisposeTireChange($('#dispose_tire_id')[0]);
            }, 300);
        }
    };

    this.__onDisposeTireChange = function(el) {
        var code = $(el).find(':selected').data('code') || '';
        var km = $(el).find(':selected').data('km') || '';
        $('#dispose_tire_code').val(code);
        if (km) {
            $('#dispose_final_km').val(km);
        }
    };

    this.__saveDisposal = function() {
        var tire_id = $('#dispose_tire_id').val();
        var date = $('#dispose_date').val();

        if (!tire_id) {
            toastr.warning('Please select a tire', 'Missing field');
            return;
        }
        if (!date) {
            toastr.warning('Please select disposal date', 'Missing field');
            return;
        }

        var mparam = {
            tire_id: tire_id,
            tire_code: $('#dispose_tire_code').val(),
            disposal_date: date,
            disposal_reason: $('#dispose_reason').val(),
            final_mileage: $('#dispose_final_km').val() || 0,
            final_status: $('#dispose_final_status').val(),
            disposal_details: $('#dispose_details').val(),
            remarks: $('#dispose_remarks').val(),
            meaction: 'SAVE_DISPOSAL'
        };

        var btn = $('#disposeSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('disposeModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Dispose Tire Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-trash"></i> Dispose Tire');
            }
        });
    };

    this.__deleteDisposal = function(disposal_id) {
        if (confirm('Delete this disposal record? The tire status will remain as-is.')) {
            var mparam = { disposal_id: disposal_id, meaction: 'DELETE_DISPOSAL' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tire',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1200);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    };

    // ==========================================
    // TIRE JOURNEY TIMELINE
    // ==========================================
    this.__openJourneyModal = function(tire_id, tire_code) {
        $('#journey_tire_id').val(tire_id);
        $('#journey_tire_code').text(tire_code);
        $('#journeyContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');

        var modal = new bootstrap.Modal(document.getElementById('journeyModal'));
        modal.show();

        var mparam = { tire_id: tire_id, meaction: 'GET_TIRE_JOURNEY' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tire',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                var html = '';

                if (data && data.length > 0) {
                    html += '<div class="journey-timeline">';
                    $.each(data, function(i, item) {
                        var cls = 'j-purchase';
                        var type = (item.type || '').toUpperCase();
                        if (type.indexOf('INVENTORY IN') !== -1) cls = 'j-inventory';
                        else if (type.indexOf('INVENTORY OUT') !== -1) cls = 'j-inventory';
                        else if (type == 'INSTALLED') cls = 'j-install';
                        else if (type == 'REMOVED') cls = 'j-remove';
                        else if (type == 'DISPOSED') cls = 'j-dispose';

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
                           '<p>This tire has no recorded events.</p></div>';
                }

                $('#journeyContent').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Tire Journey Error:', status, error, xhr.responseText);
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
    console.log('Tire Module ready');
});