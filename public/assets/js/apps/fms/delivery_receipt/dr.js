var __DR_PREVIEW_ITEMS = [];
var __DR = new __DR();

function __DR() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    const baseurl = mesiteurl.replace(/fms-delivery-receipt\/?$/, '');

    console.log('Delivery Receipt initialized, URL: ' + mesiteurl);

    // ==============================
    // OPEN DR MODAL FROM TRIP (create new DR directly)
    // ==============================
    this.__openDRModalFromTrip = function(trip_id, dispatch_id) {
        // Reset all fields
        $('#dr_id').val('');
        $('#dr_trip_id').val(trip_id);
        $('#dr_customer_id').val('');
        $('#dr_dispatch_id').val(dispatch_id);
        $('#dr_code_display').text('Auto-generated');
        $('#dr_trip_code_display').text('—');
        $('#dr_customer_display').text('—');
        $('#dr_truck_display').text('—');
        $('#dr_driver_display').text('—');
        $('#dr_helper_display').text('—');

        var today = new Date().toISOString().split('T')[0];
        $('#dr_date').val(today);
        $('#dr_time').val('');
        $('#dr_status').val('PENDING');
        $('#dr_truck').val('');
        $('#dr_driver').val('');
        $('#dr_helper').val('');
        $('#dr_origin').val('');
        $('#dr_destination').val('');
        $('#dr_container_required').prop('checked', false);
        $('#dr_container_number').val('');
        $('#dr_container_type').val('');
        $('#dr_container_reference').val('');
        $('#dr_remarks').val('');

        $('#dr_container_fields').hide();

        // Reset preview items + item form
        __DR_PREVIEW_ITEMS = [];
        __DR.__resetItemForm();
        $('#drItemsBody').html('<tr><td colspan="10" class="text-center text-muted">Loading trip cargo items...</td></tr>');

        $('#drModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Delivery Receipt');
        $('#drBtnText').text('Save DR');
        $('#drSubmitBtn').attr('onclick', '__DR.__saveDR()');

        var modal = new bootstrap.Modal(document.getElementById('drModal'));
        modal.show();

        // Fetch trip info to auto-fill
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_TRIP_FOR_DR'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data && data.trip_id) {
                    $('#dr_customer_id').val(data.customer_id || '');
                    $('#dr_trip_code_display').text(data.trip_code || '—');
                    $('#dr_customer_display').text(data.customer_name || '—');
                    $('#dr_truck_display').text(data.truck || '—');
                    $('#dr_driver_display').text(data.driver || '—');
                    $('#dr_helper_display').text(data.helper || '—');

                    $('#dr_truck').val(data.truck || '');
                    $('#dr_driver').val(data.driver || '');
                    $('#dr_helper').val(data.helper || '');
                    $('#dr_origin').val(data.origin || '');
                    $('#dr_destination').val(data.destination || '');
                    $('#dr_container_required').prop('checked', data.container_required == 1);
                    $('#dr_container_number').val(data.container_number || '');
                    $('#dr_container_type').val(data.container_type || '');
                    $('#dr_container_reference').val(data.container_reference || '');

                    __DR.__toggleContainerFields();
                }

                // Load cargo items preview
                __DR.__loadTripCargoPreview(trip_id);
            },
            error: function(xhr, status, error) {
                console.error('Error loading trip info:', error);
                __DR.__loadTripCargoPreview(trip_id);
            }
        });
    };

    // ==============================
    // LOAD TRIP CARGO ITEMS AS PREVIEW
    // ==============================
    this.__loadTripCargoPreview = function(trip_id) {
        if (!trip_id) return;

        var mparam = { trip_id: trip_id, meaction: 'GET_TRIP_CARGO_ITEMS' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                __DR_PREVIEW_ITEMS = [];

                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        __DR_PREVIEW_ITEMS.push({
                            item_description: row.item_description,
                            quantity_dispatched: parseFloat(row.quantity) || 0,
                            quantity_delivered: parseFloat(row.quantity) || 0,
                            quantity_shortage: 0,
                            quantity_damaged: 0,
                            unit: row.unit || '',
                            weight: parseFloat(row.weight) || 0,
                            condition_on_arrival: 'GOOD',
                            remarks: row.remarks || ''
                        });
                    });
                }

                __DR.__renderPreviewItems();
            },
            error: function(xhr, status, error) {
                console.error('Error loading trip cargo preview:', error);
                $('#drItemsBody').html('<tr><td colspan="10" class="text-center text-muted">Failed to load cargo items</td></tr>');
            }
        });
    };

    // ==============================
    // RENDER PREVIEW ITEMS
    // ==============================
    this.__renderPreviewItems = function() {
        var html = '';

        if (__DR_PREVIEW_ITEMS.length > 0) {
            $.each(__DR_PREVIEW_ITEMS, function(i, row) {
                html += '<tr>';
                html += '<td>' + (i + 1) + '</td>';
                html += '<td><strong>' + row.item_description + '</strong></td>';
                html += '<td>' + parseFloat(row.quantity_dispatched).toFixed(2) + '</td>';
                html += '<td>' + parseFloat(row.quantity_delivered).toFixed(2) + '</td>';
                html += '<td>' + parseFloat(row.quantity_shortage).toFixed(2) + '</td>';
                html += '<td>' + parseFloat(row.quantity_damaged).toFixed(2) + '</td>';
                html += '<td>' + (row.unit || '—') + '</td>';
                html += '<td>' + parseFloat(row.weight).toFixed(2) + '</td>';
                html += '<td>' + getConditionBadge(row.condition_on_arrival) + '</td>';
                html += '<td class="text-center">';
                html += '<div class="action-group">';
                html += '<button type="button" class="btn-icon btn-icon-edit" onclick="__DR.__editPreviewItem(' + i + ')" title="Edit"><i class="bi bi-pencil"></i></button>';
                html += '<button type="button" class="btn-icon btn-icon-delete" onclick="__DR.__deletePreviewItem(' + i + ')" title="Remove"><i class="bi bi-trash"></i></button>';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="10" class="text-center text-muted">No cargo items from trip</td></tr>';
        }

        $('#drItemsBody').html(html);
    };

    // ==============================
    // EDIT PREVIEW ITEM
    // ==============================
    this.__editPreviewItem = function(index) {
        var row = __DR_PREVIEW_ITEMS[index];
        if (!row) return;

        $('#item_description').val(row.item_description);
        $('#item_qty_dispatched').val(row.quantity_dispatched);
        $('#item_qty_delivered').val(row.quantity_delivered);
        $('#item_qty_shortage').val(row.quantity_shortage);
        $('#item_qty_damaged').val(row.quantity_damaged);
        $('#item_unit').val(row.unit);
        $('#item_weight').val(row.weight);
        $('#item_condition').val(row.condition_on_arrival);
        $('#item_remarks').val(row.remarks);

        __DR_PREVIEW_ITEMS.splice(index, 1);
        __DR.__renderPreviewItems();

        toastr.info('Item removed from list — edit and click Add to re-add.');
    };

    // ==============================
    // DELETE PREVIEW ITEM
    // ==============================
    this.__deletePreviewItem = function(index) {
        if (confirm('Remove this item from the DR?')) {
            __DR_PREVIEW_ITEMS.splice(index, 1);
            __DR.__renderPreviewItems();
        }
    };

    // ==============================
    // RESET ITEM FORM
    // ==============================
    this.__resetItemForm = function() {
        $('#item_description').val('');
        $('#item_qty_dispatched').val('');
        $('#item_qty_delivered').val('');
        $('#item_qty_shortage').val('');
        $('#item_qty_damaged').val('');
        $('#item_unit').val('');
        $('#item_weight').val('');
        $('#item_condition').val('GOOD');
        $('#item_remarks').val('');
        $('#item_editing_id').val('');

        var btn = $('#itemActionBtn');
        btn.html('<i class="bi bi-plus"></i> Add');
        btn.attr('onclick', '__DR.__saveDRItem()');
        btn.removeClass('btn-warning').addClass('btn-primary');
    };

    // ==============================
    // OPEN DR MODAL (Edit mode)
    // ==============================
    this.__openDRModal = function(dr_id) {
        var mparam = {
            dr_id: dr_id,
            meaction: 'GET_DR'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data && data.dr_id) {
                    __DR_PREVIEW_ITEMS = [];

                    $('#dr_id').val(data.dr_id);
                    $('#dr_trip_id').val(data.trip_id);
                    $('#dr_customer_id').val(data.customer_id);
                    $('#dr_dispatch_id').val(data.dispatch_id);
                    $('#dr_code_display').text(data.dr_code);
                    $('#dr_trip_code_display').text(data.trip_code || '—');
                    $('#dr_customer_display').text(data.customer_name || '—');
                    $('#dr_truck_display').text(data.truck || '—');
                    $('#dr_driver_display').text(data.driver || '—');
                    $('#dr_helper_display').text(data.helper || '—');

                    $('#dr_date').val(data.dr_date);
                    $('#dr_time').val(data.dr_time);
                    $('#dr_status').val(data.dr_status);
                    $('#dr_truck').val(data.truck);
                    $('#dr_driver').val(data.driver);
                    $('#dr_helper').val(data.helper);
                    $('#dr_origin').val(data.origin);
                    $('#dr_destination').val(data.destination);
                    $('#dr_container_required').prop('checked', data.container_required == 1);
                    $('#dr_container_number').val(data.container_number);
                    $('#dr_container_type').val(data.container_type);
                    $('#dr_container_reference').val(data.container_reference);
                    $('#dr_remarks').val(data.remarks);

                    __DR.__toggleContainerFields();

                    $('#drModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Delivery Receipt');
                    $('#drBtnText').text('Update DR');
                    $('#drSubmitBtn').attr('onclick', '__DR.__updateDR()');

                    __DR.__loadDRItems(data.dr_id);
                }

                var modal = new bootstrap.Modal(document.getElementById('drModal'));
                modal.show();
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading DR: " + error);
                console.error('Error:', error);
            }
        });
    };

    // ==============================
    // TOGGLE CONTAINER FIELDS
    // ==============================
    this.__toggleContainerFields = function() {
        if($('#dr_container_required').is(':checked')) {
            $('#dr_container_fields').show();
        } else {
            $('#dr_container_fields').hide();
        }
    };

    // ==============================
    // SAVE DR
    // ==============================
    this.__saveDR = function() {
        var dr_date = $('#dr_date').val();
        if(!dr_date) {
            toastr.warning('Please select delivery date', 'Missing field');
            $('#dr_date').focus();
            return;
        }

        var mparam = {
            trip_id: $('#dr_trip_id').val(),
            dispatch_id: $('#dr_dispatch_id').val(),
            customer_id: $('#dr_customer_id').val(),
            dr_date: dr_date,
            dr_time: $('#dr_time').val(),
            truck: $('#dr_truck').val(),
            driver: $('#dr_driver').val(),
            helper: $('#dr_helper').val(),
            origin: $('#dr_origin').val(),
            destination: $('#dr_destination').val(),
            container_required: $('#dr_container_required').is(':checked') ? 1 : 0,
            container_number: $('#dr_container_number').val(),
            container_type: $('#dr_container_type').val(),
            container_reference: $('#dr_container_reference').val(),
            dr_status: $('#dr_status').val(),
            remarks: $('#dr_remarks').val(),
            meaction: 'SAVE_DR'
        };

        // Attach preview items
        if (__DR_PREVIEW_ITEMS.length > 0) {
            $.each(__DR_PREVIEW_ITEMS, function(i, item) {
                mparam['dr_items[' + i + '][item_description]'] = item.item_description;
                mparam['dr_items[' + i + '][quantity_dispatched]'] = item.quantity_dispatched;
                mparam['dr_items[' + i + '][quantity_delivered]'] = item.quantity_delivered;
                mparam['dr_items[' + i + '][quantity_shortage]'] = item.quantity_shortage;
                mparam['dr_items[' + i + '][quantity_damaged]'] = item.quantity_damaged;
                mparam['dr_items[' + i + '][unit]'] = item.unit;
                mparam['dr_items[' + i + '][weight]'] = item.weight;
                mparam['dr_items[' + i + '][condition_on_arrival]'] = item.condition_on_arrival;
                mparam['dr_items[' + i + '][remarks]'] = item.remarks;
            });
        }

        var btn = $('#drSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('drModal'));
                    if(modal) modal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                    btn.html('<i class="bi bi-save"></i> Save DR');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save DR');
                toastr.error("Error: " + error);
                console.error('Error:', error);
            }
        });
    };

    // ==============================
    // UPDATE DR
    // ==============================
    this.__updateDR = function() {
        var dr_id = $('#dr_id').val();
        var dr_date = $('#dr_date').val();

        if(!dr_id) {
            toastr.error('No DR ID found.');
            return;
        }
        if(!dr_date) {
            toastr.warning('Please select delivery date', 'Missing field');
            $('#dr_date').focus();
            return;
        }

        var mparam = {
            dr_id: dr_id,
            dr_date: dr_date,
            dr_time: $('#dr_time').val(),
            truck: $('#dr_truck').val(),
            driver: $('#dr_driver').val(),
            helper: $('#dr_helper').val(),
            origin: $('#dr_origin').val(),
            destination: $('#dr_destination').val(),
            container_required: $('#dr_container_required').is(':checked') ? 1 : 0,
            container_number: $('#dr_container_number').val(),
            container_type: $('#dr_container_type').val(),
            container_reference: $('#dr_container_reference').val(),
            dr_status: $('#dr_status').val(),
            remarks: $('#dr_remarks').val(),
            meaction: 'UPDATE_DR'
        };

        var btn = $('#drSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('drModal'));
                    if(modal) modal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                    btn.html('<i class="bi bi-save"></i> Update DR');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update DR');
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // DELETE DR
    // ==============================
    this.__deleteDR = function(dr_id) {
        if(!dr_id || dr_id == 0) {
            toastr.error('Invalid DR ID');
            return;
        }
        if(confirm('Are you sure you want to delete this Delivery Receipt?')) {
            var mparam = {
                dr_id: dr_id,
                meaction: 'DELETE_DR'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-delivery-receipt',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
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

    // ==============================
    // DR ITEMS METHODS
    // ==============================
    this.__loadDRItems = function(dr_id) {
        if(!dr_id || dr_id == 0) {
            $('#drItemsBody').html('<tr><td colspan="10" class="text-center text-muted">Save DR first</td></tr>');
            return;
        }

        var mparam = {
            dr_id: dr_id,
            meaction: 'GET_DR_ITEMS'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data && data.length > 0) {
                    $.each(data, function(index, row) {
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td><strong>' + row.item_description + '</strong></td>';
                        html += '<td>' + parseFloat(row.quantity_dispatched).toFixed(2) + '</td>';
                        html += '<td>' + parseFloat(row.quantity_delivered).toFixed(2) + '</td>';
                        html += '<td>' + parseFloat(row.quantity_shortage).toFixed(2) + '</td>';
                        html += '<td>' + parseFloat(row.quantity_damaged).toFixed(2) + '</td>';
                        html += '<td>' + (row.unit || '—') + '</td>';
                        html += '<td>' + parseFloat(row.weight).toFixed(2) + '</td>';
                        html += '<td>' + getConditionBadge(row.condition_on_arrival) + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__DR.__editDRItem(' + row.item_id + ')" title="Edit"><i class="bi bi-pencil"></i></button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__DR.__deleteDRItem(' + row.item_id + ')" title="Delete"><i class="bi bi-trash"></i></button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="10" class="text-center text-muted">No items</td></tr>';
                }
                $('#drItemsBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading DR items: " + error);
            }
        });
    };

    // ==============================
    // SAVE DR ITEM (handles 3 cases)
    // ==============================
    this.__saveDRItem = function() {
        var item_description = $('#item_description').val().trim();

        if(!item_description) {
            toastr.warning('Please enter item description', 'Missing field');
            $('#item_description').focus();
            return;
        }

        var editing_id = $('#item_editing_id').val();

        // === Case 1: Editing an existing saved DR item ===
        if (editing_id !== '' && editing_id !== null && editing_id !== undefined) {
            var dr_id = $('#dr_id').val();
            var mparam = {
                item_id: editing_id,
                item_description: item_description,
                quantity_dispatched: $('#item_qty_dispatched').val() || 0,
                quantity_delivered: $('#item_qty_delivered').val() || 0,
                quantity_shortage: $('#item_qty_shortage').val() || 0,
                quantity_damaged: $('#item_qty_damaged').val() || 0,
                unit: $('#item_unit').val(),
                weight: $('#item_weight').val() || 0,
                condition_on_arrival: $('#item_condition').val(),
                remarks: $('#item_remarks').val(),
                meaction: 'UPDATE_DR_ITEM'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-delivery-receipt',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __DR.__resetItemForm();
                        __DR.__loadDRItems(dr_id);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
            return;
        }

        // === Case 2: DR already saved, adding new item directly to DB ===
        if ($('#dr_id').val()) {
            var dr_id = $('#dr_id').val();
            var mparam = {
                dr_id: dr_id,
                item_description: item_description,
                quantity_dispatched: $('#item_qty_dispatched').val() || 0,
                quantity_delivered: $('#item_qty_delivered').val() || 0,
                quantity_shortage: $('#item_qty_shortage').val() || 0,
                quantity_damaged: $('#item_qty_damaged').val() || 0,
                unit: $('#item_unit').val(),
                weight: $('#item_weight').val() || 0,
                condition_on_arrival: $('#item_condition').val(),
                remarks: $('#item_remarks').val(),
                meaction: 'SAVE_DR_ITEM'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-delivery-receipt',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __DR.__resetItemForm();
                        __DR.__loadDRItems(dr_id);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
            return;
        }

        // === Case 3: DR not saved yet → add to preview in memory ===
        __DR_PREVIEW_ITEMS.push({
            item_description: item_description,
            quantity_dispatched: parseFloat($('#item_qty_dispatched').val()) || 0,
            quantity_delivered: parseFloat($('#item_qty_delivered').val()) || 0,
            quantity_shortage: parseFloat($('#item_qty_shortage').val()) || 0,
            quantity_damaged: parseFloat($('#item_qty_damaged').val()) || 0,
            unit: $('#item_unit').val(),
            weight: parseFloat($('#item_weight').val()) || 0,
            condition_on_arrival: $('#item_condition').val(),
            remarks: $('#item_remarks').val()
        });

        __DR.__resetItemForm();
        __DR.__renderPreviewItems();
        toastr.success('Item added to list');
    };

    this.__editDRItem = function(item_id) {
        var mparam = {
            item_id: item_id,
            meaction: 'GET_DR_ITEM'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#item_editing_id').val(data.item_id);
                    $('#item_description').val(data.item_description);
                    $('#item_qty_dispatched').val(data.quantity_dispatched);
                    $('#item_qty_delivered').val(data.quantity_delivered);
                    $('#item_qty_shortage').val(data.quantity_shortage);
                    $('#item_qty_damaged').val(data.quantity_damaged);
                    $('#item_unit').val(data.unit);
                    $('#item_weight').val(data.weight);
                    $('#item_condition').val(data.condition_on_arrival);
                    $('#item_remarks').val(data.remarks);

                    var btn = $('#itemActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__DR.__saveDRItem()');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            }
        });
    };

    this.__deleteDRItem = function(item_id) {
        if(confirm('Are you sure you want to delete this item?')) {
            var dr_id = $('#dr_id').val();
            var mparam = {
                item_id: item_id,
                meaction: 'DELETE_DR_ITEM'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-delivery-receipt',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __DR.__loadDRItems(dr_id);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    };

    // ==============================
    // POD
    // ==============================
    this.__openPODModal = function(dr_id) {
        $('#pod_dr_id').val(dr_id);

        var mparam = {
            dr_id: dr_id,
            meaction: 'GET_POD'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data && data.pod_id) {
                    $('#pod_id').val(data.pod_id);
                    $('#pod_received_by').val(data.received_by);
                    $('#pod_position').val(data.received_by_position);
                    $('#pod_date').val(data.date_received);
                    $('#pod_time').val(data.time_received);
                    $('#pod_quantity').val(data.quantity_received);
                    $('#pod_condition').val(data.delivery_condition);
                    $('#pod_signature').val(data.customer_signature);
                    $('#pod_photo').val(data.delivery_photo);
                    $('#pod_signed_dr').val(data.signed_dr);
                    $('#pod_supporting').val(data.supporting_documents);
                    $('#pod_remarks').val(data.remarks);

                    __DR.__restorePODPreviews(data);

                    $('#podBtnText').text('Update POD');
                    $('#podSubmitBtn').attr('onclick', '__DR.__updatePOD()');
                } else {
                    $('#pod_id').val('');
                    $('#pod_received_by').val('');
                    $('#pod_position').val('');
                    $('#pod_date').val('');
                    $('#pod_time').val('');
                    $('#pod_quantity').val('');
                    $('#pod_condition').val('GOOD');
                    $('#pod_signature').val('');
                    $('#pod_photo').val('');
                    $('#pod_signed_dr').val('');
                    $('#pod_supporting').val('');
                    $('#pod_remarks').val('');

                    $('#pod_signature_file').val('');
                    $('#pod_photo_file').val('');
                    $('#pod_signed_dr_file').val('');
                    $('#pod_supporting_file').val('');
                    $('#pod_signature_preview').hide();
                    $('#pod_photo_preview').hide();
                    $('#pod_signed_dr_preview').hide();
                    $('#pod_supporting_preview').hide();

                    __DR.__switchSigTab('draw');

                    $('#podBtnText').text('Save POD');
                    $('#podSubmitBtn').attr('onclick', '__DR.__savePOD()');
                }

                var modal = new bootstrap.Modal(document.getElementById('podModal'));
                modal.show();

                setTimeout(function() {
                    __DR.__initSignaturePad();
                    __DR.__clearSignaturePad();
                }, 400);

                $(window).off('resize.dr-sig').on('resize.dr-sig', function() {
                    var canvas = document.getElementById('signaturePad');
                    if (canvas && canvas.dataset.initialized === '1') {
                        __DR.__fitCanvasSize(canvas);
                    }
                });
            }
        });
    };

    this.__savePOD = function() {
        var dr_id = $('#pod_dr_id').val();
        var received_by = $('#pod_received_by').val().trim();

        if(!received_by) {
            toastr.warning('Please enter received by', 'Missing field');
            $('#pod_received_by').focus();
            return;
        }

        var mparam = {
            dr_id: dr_id,
            received_by: received_by,
            received_by_position: $('#pod_position').val(),
            date_received: $('#pod_date').val(),
            time_received: $('#pod_time').val(),
            quantity_received: $('#pod_quantity').val(),
            delivery_condition: $('#pod_condition').val(),
            customer_signature: $('#pod_signature').val(),
            delivery_photo: $('#pod_photo').val(),
            signed_dr: $('#pod_signed_dr').val(),
            supporting_documents: $('#pod_supporting').val(),
            remarks: $('#pod_remarks').val(),
            meaction: 'SAVE_POD'
        };

        var btn = $('#podSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save POD');
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('podModal'));
                    if(modal) modal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save POD');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__updatePOD = function() {
        var pod_id = $('#pod_id').val();
        var received_by = $('#pod_received_by').val().trim();

        if(!received_by) {
            toastr.warning('Please enter received by');
            return;
        }

        var mparam = {
            pod_id: pod_id,
            received_by: received_by,
            received_by_position: $('#pod_position').val(),
            date_received: $('#pod_date').val(),
            time_received: $('#pod_time').val(),
            quantity_received: $('#pod_quantity').val(),
            delivery_condition: $('#pod_condition').val(),
            customer_signature: $('#pod_signature').val(),
            delivery_photo: $('#pod_photo').val(),
            signed_dr: $('#pod_signed_dr').val(),
            supporting_documents: $('#pod_supporting').val(),
            remarks: $('#pod_remarks').val(),
            meaction: 'UPDATE_POD'
        };

        var btn = $('#podSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update POD');
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('podModal'));
                    if(modal) modal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                }
            }
        });
    };

    // ==============================
    // UPLOAD POD FILE
    // ==============================
    this.__uploadPODFile = function(file_type) {
        var dr_id = $('#pod_dr_id').val();

        var inputMap = {
            'customer_signature':   'pod_signature_file',
            'delivery_photo':       'pod_photo_file',
            'signed_dr':            'pod_signed_dr_file',
            'supporting_documents': 'pod_supporting_file'
        };
        var inputId = inputMap[file_type];
        var fileInput = inputId ? document.getElementById(inputId) : null;

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) return;
        if (!dr_id) {
            toastr.warning('Please select a DR first.');
            fileInput.value = '';
            return;
        }

        var file = fileInput.files[0];
        var allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        var ext = file.name.split('.').pop().toLowerCase();

        if (allowedExt.indexOf(ext) === -1) {
            toastr.error('File type not allowed. Only JPG, PNG, GIF, WEBP, PDF.');
            fileInput.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            toastr.error('File too large. Max 5MB.');
            fileInput.value = '';
            return;
        }

        var formData = new FormData();
        formData.append('file', file);
        formData.append('dr_id', dr_id);
        formData.append('file_type', file_type);
        formData.append('meaction', 'UPLOAD_POD_FILE');

        toastr.info('Uploading ' + file.name + '...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    toastr.success(data.message);

                    var map = {
                        'customer_signature': '#pod_signature',
                        'delivery_photo':    '#pod_photo',
                        'signed_dr':         '#pod_signed_dr',
                        'supporting_documents': '#pod_supporting'
                    };
                    $(map[file_type]).val(data.path);

                    var previewMap = {
                        'customer_signature': {wrap: '#pod_signature_preview', img: '#pod_signature_img', name: '#pod_signature_name'},
                        'delivery_photo':     {wrap: '#pod_photo_preview', img: '#pod_photo_img', name: '#pod_photo_name'},
                        'signed_dr':          {wrap: '#pod_signed_dr_preview', img: '#pod_signed_dr_img', name: '#pod_signed_dr_name'},
                        'supporting_documents': {wrap: '#pod_supporting_preview', img: '#pod_supporting_img', name: '#pod_supporting_name'}
                    };
                    var p = previewMap[file_type];
                    $(p.wrap).show();
                    $(p.name).text(file.name);

                    if (ext === 'pdf') {
                        $(p.img).hide();
                    } else {
                        $(p.img).attr('src', baseurl + data.path).show();
                    }
                } else {
                    toastr.error(data.message);
                    fileInput.value = '';
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Upload error: " + error);
                fileInput.value = '';
            }
        });
    };

    // ==============================
    // CLEAR POD FILE
    // ==============================
    this.__clearPODFile = function(file_type) {
        var map = {
            'customer_signature': {hidden: '#pod_signature', input: '#pod_signature_file', wrap: '#pod_signature_preview'},
            'delivery_photo':     {hidden: '#pod_photo', input: '#pod_photo_file', wrap: '#pod_photo_preview'},
            'signed_dr':          {hidden: '#pod_signed_dr', input: '#pod_signed_dr_file', wrap: '#pod_signed_dr_preview'},
            'supporting_documents': {hidden: '#pod_supporting', input: '#pod_supporting_file', wrap: '#pod_supporting_preview'}
        };
        var m = map[file_type];
        if (!m) return;
        $(m.hidden).val('');
        $(m.input).val('');
        $(m.wrap).hide();

        if (file_type === 'customer_signature') {
            __DR.__clearSignaturePad();
        }
    };

    // ==============================
    // RESTORE POD FILE PREVIEWS
    // ==============================
    this.__restorePODPreviews = function(data) {
        var map = {
            'customer_signature': {path: data.customer_signature, wrap: '#pod_signature_preview', img: '#pod_signature_img', name: '#pod_signature_name'},
            'delivery_photo':     {path: data.delivery_photo,    wrap: '#pod_photo_preview',     img: '#pod_photo_img',     name: '#pod_photo_name'},
            'signed_dr':          {path: data.signed_dr,         wrap: '#pod_signed_dr_preview', img: '#pod_signed_dr_img', name: '#pod_signed_dr_name'},
            'supporting_documents': {path: data.supporting_documents, wrap: '#pod_supporting_preview', img: '#pod_supporting_img', name: '#pod_supporting_name'}
        };

        $.each(map, function(key, m) {
            if (m.path) {
                $(m.wrap).show();
                $(m.name).text(m.path.split('/').pop());
                var ext = m.path.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    $(m.img).hide();
                } else {
                    $(m.img).attr('src', baseurl + m.path).show();
                }
            } else {
                $(m.wrap).hide();
            }
        });
    };

    // ==============================
    // SIGNATURE PAD — SWITCH TAB
    // ==============================
    this.__switchSigTab = function(tab) {
        if (tab === 'draw') {
            $('#sigTabDraw').addClass('active');
            $('#sigTabUpload').removeClass('active');
            $('#sigPaneDraw').show();
            $('#sigPaneUpload').hide();
        } else {
            $('#sigTabUpload').addClass('active');
            $('#sigTabDraw').removeClass('active');
            $('#sigPaneUpload').show();
            $('#sigPaneDraw').hide();
        }
    };

    // ==============================
    // SIGNATURE PAD — INIT (mobile-safe)
    // ==============================
    this.__initSignaturePad = function() {
        var canvas = document.getElementById('signaturePad');
        if (!canvas) return;

        canvas.style.touchAction = 'none';
        canvas.style.webkitUserSelect = 'none';
        canvas.style.userSelect = 'none';

        if (canvas.dataset.initialized === '1') {
            __DR.__fitCanvasSize(canvas);
            return;
        }
        canvas.dataset.initialized = '1';

        __DR.__fitCanvasSize(canvas);

        var ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#1a1a1a';
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        var drawing = false;
        var lastX = 0, lastY = 0;

        function getPos(e) {
            var rect = canvas.getBoundingClientRect();
            var clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else if (e.changedTouches && e.changedTouches.length > 0) {
                clientX = e.changedTouches[0].clientX;
                clientY = e.changedTouches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        function start(e) {
            if (e.cancelable) e.preventDefault();
            drawing = true;
            $('#sigPadWrapper').addClass('active');
            var p = getPos(e);
            lastX = p.x; lastY = p.y;

            ctx.beginPath();
            ctx.arc(p.x, p.y, 1.1, 0, Math.PI * 2);
            ctx.fillStyle = '#1a1a1a';
            ctx.fill();
        }

        function move(e) {
            if (!drawing) return;
            if (e.cancelable) e.preventDefault();
            var p = getPos(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            lastX = p.x; lastY = p.y;
        }

        function stop(e) {
            if (!drawing) return;
            if (e.cancelable) e.preventDefault();
            drawing = false;
            $('#sigPadWrapper').removeClass('active');
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        canvas.addEventListener('mouseup', stop);
        canvas.addEventListener('mouseleave', stop);

        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', move, { passive: false });
        canvas.addEventListener('touchend', stop, { passive: false });
        canvas.addEventListener('touchcancel', stop, { passive: false });
    };

    // ==============================
    // SIGNATURE PAD — FIT CANVAS SIZE
    // ==============================
    this.__fitCanvasSize = function(canvas) {
        if (!canvas) return;
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        var displayW = canvas.offsetWidth;
        var displayH = canvas.offsetHeight;

        if (displayW < 10 || displayH < 10) {
            setTimeout(function() { __DR.__fitCanvasSize(canvas); }, 200);
            return;
        }

        var prevImage = null;
        try {
            if (canvas.width > 0 && canvas.height > 0) {
                prevImage = canvas.toDataURL();
            }
        } catch (e) {}

        canvas.width = displayW * ratio;
        canvas.height = displayH * ratio;

        var ctx = canvas.getContext('2d');
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(ratio, ratio);
        ctx.strokeStyle = '#1a1a1a';
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        if (prevImage) {
            var img = new Image();
            img.onload = function() {
                ctx.drawImage(img, 0, 0, displayW, displayH);
            };
            img.src = prevImage;
        }
    };

    // ==============================
    // SIGNATURE PAD — CLEAR
    // ==============================
    this.__clearSignaturePad = function() {
        var canvas = document.getElementById('signaturePad');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    };

    // ==============================
    // SIGNATURE PAD — CHECK IF EMPTY
    // ==============================
    this.__isSignatureEmpty = function() {
        var canvas = document.getElementById('signaturePad');
        if (!canvas) return true;
        var blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        return canvas.toDataURL() === blank.toDataURL();
    };

    // ==============================
    // SIGNATURE PAD — SAVE (upload base64)
    // ==============================
    this.__saveSignature = function() {
        var dr_id = $('#pod_dr_id').val();
        if (!dr_id) {
            toastr.warning('Please select a DR first.');
            return;
        }
        if (__DR.__isSignatureEmpty()) {
            toastr.warning('Please sign before saving.');
            return;
        }

        var dataURL = document.getElementById('signaturePad').toDataURL('image/png');

        var mparam = {
            dr_id: dr_id,
            signature_data: dataURL,
            meaction: 'UPLOAD_SIGNATURE'
        };

        toastr.info('Saving signature...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-delivery-receipt',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    toastr.success(data.message);
                    $('#pod_signature').val(data.path);
                    $('#pod_signature_preview').show();
                    $('#pod_signature_img').attr('src', baseurl + data.path).show();
                    $('#pod_signature_name').text('signature.png (drawn)');
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Upload error: " + error);
            }
        });
    };

    // ==============================
    // SHOW PDF IN MODAL
    // ==============================
    this.__showPdfInModal = function(pdfUrl) {
        var pdfFrame = document.getElementById("pdfFrame");
        var pdfModal = new bootstrap.Modal(document.getElementById("pdfModal"));

        pdfFrame.src = pdfUrl;
        pdfModal.show();
    };
}

// ==============================
// HELPER FUNCTIONS
// ==============================
function getConditionBadge(condition) {
    var map = {
        'GOOD': '<span class="badge badge-success">Good</span>',
        'FAIR': '<span class="badge badge-info">Fair</span>',
        'POOR': '<span class="badge badge-warning">Poor</span>',
        'DAMAGED': '<span class="badge badge-danger">Damaged</span>',
        'SHORT': '<span class="badge badge-warning">Short</span>',
        'REJECTED': '<span class="badge badge-danger">Rejected</span>'
    };
    return map[condition] || '<span class="badge badge-secondary">' + condition + '</span>';
}

$(document).ready(function() {
    console.log('Delivery Receipt ready');

    $('#dr_container_required').on('change', function() {
        __DR.__toggleContainerFields();
    });

    $('#pdfModal').on('hidden.bs.modal', function () {
        document.getElementById('pdfFrame').src = '';
    });

    $('#podModal').on('hidden.bs.modal', function () {
        $(window).off('resize.dr-sig');
    });
});