var __Tool = new __Tool();

function __Tool() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Tools initialized, URL: ' + mesiteurl);

    // ==========================================
    // TOOL MODAL (Create / Edit)
    // ==========================================
    this.__openToolModal = function(tool_id) {
        if (tool_id) {
            var mparam = { tool_id: tool_id, meaction: 'GET_TOOL' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tool',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data && data.tool_id) {
                        $('#tool_id').val(data.tool_id);
                        $('#tool_name').val(data.tool_name);
                        $('#tool_category').val(data.category);
                        $('#tool_brand').val(data.brand);
                        $('#tool_model').val(data.model);
                        $('#tool_serial').val(data.serial_number);
                        $('#tool_purchase_date').val(data.purchase_date);
                        $('#tool_purchase_cost').val(data.purchase_cost);
                        $('#tool_quantity').val(data.quantity || 1);
                        $('#tool_quantity_display').val(data.quantity_on_hand || 0);
                        $('#tool_location').val(data.current_location);
                        $('#tool_condition').val(data.tool_condition);
                        $('#tool_availability').val(data.availability);
                        $('#tool_assigned_to').val(data.assigned_to);
                        $('#tool_truck_id').val(data.truck_id);
                        $('#tool_truck_plate').val(data.truck_plate);
                        $('#tool_remarks').val(data.remarks);

                        $('#toolModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Tool');
                        $('#toolBtnText').text('Update Tool');
                        $('#toolSubmitBtn').attr('onclick', '__Tool.__updateTool()');
                    }
                }
            });
        } else {
            $('#tool_id').val('');
            $('#tool_name').val('');
            $('#tool_category').val('');
            $('#tool_brand').val('');
            $('#tool_model').val('');
            $('#tool_serial').val('');
            $('#tool_purchase_date').val('');
            $('#tool_purchase_cost').val('');
            $('#tool_quantity').val('1');
            $('#tool_quantity_display').val('1');
            $('#tool_location').val('');
            $('#tool_condition').val('GOOD');
            $('#tool_availability').val('AVAILABLE');
            $('#tool_assigned_to').val('');
            $('#tool_truck_id').val('');
            $('#tool_truck_plate').val('');
            $('#tool_remarks').val('');

            $('#toolModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Tool');
            $('#toolBtnText').text('Save Tool');
            $('#toolSubmitBtn').attr('onclick', '__Tool.__saveTool()');
        }

        var modal = new bootstrap.Modal(document.getElementById('toolModal'));
        modal.show();
    };

    this.__onToolTruckChange = function(el) {
        var plate = $(el).find(':selected').data('plate') || '';
        $('#tool_truck_plate').val(plate);
    };

    this.__saveTool = function() {
        var name = $('#tool_name').val().trim();
        if (!name) {
            toastr.warning('Please enter tool name', 'Missing field');
            $('#tool_name').focus();
            return;
        }

        var mparam = {
            tool_name: name,
            category: $('#tool_category').val(),
            brand: $('#tool_brand').val(),
            model: $('#tool_model').val(),
            serial_number: $('#tool_serial').val(),
            purchase_date: $('#tool_purchase_date').val(),
            purchase_cost: $('#tool_purchase_cost').val() || 0,
            quantity: $('#tool_quantity').val() || 1,
            current_location: $('#tool_location').val(),
            tool_condition: $('#tool_condition').val(),
            availability: $('#tool_availability').val(),
            assigned_to: $('#tool_assigned_to').val(),
            truck_id: $('#tool_truck_id').val() || null,
            truck_plate: $('#tool_truck_plate').val(),
            remarks: $('#tool_remarks').val(),
            meaction: 'SAVE_TOOL'
        };

        var btn = $('#toolSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tool',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('toolModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Tool Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Tool');
            }
        });
    };

    this.__updateTool = function() {
        var tool_id = $('#tool_id').val();
        var name = $('#tool_name').val().trim();
        if (!name) { toastr.warning('Please enter tool name'); return; }

        var mparam = {
            tool_id: tool_id,
            tool_name: name,
            category: $('#tool_category').val(),
            brand: $('#tool_brand').val(),
            model: $('#tool_model').val(),
            serial_number: $('#tool_serial').val(),
            purchase_date: $('#tool_purchase_date').val(),
            purchase_cost: $('#tool_purchase_cost').val() || 0,
            quantity: $('#tool_quantity').val() || 1,
            current_location: $('#tool_location').val(),
            tool_condition: $('#tool_condition').val(),
            availability: $('#tool_availability').val(),
            assigned_to: $('#tool_assigned_to').val(),
            truck_id: $('#tool_truck_id').val() || null,
            truck_plate: $('#tool_truck_plate').val(),
            remarks: $('#tool_remarks').val(),
            meaction: 'UPDATE_TOOL'
        };

        var btn = $('#toolSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tool',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('toolModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update Tool Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update Tool');
            }
        });
    };

    this.__deleteTool = function(tool_id) {
        if (confirm('Delete this tool? All related issuance records will remain but reference a deleted tool.')) {
            var mparam = { tool_id: tool_id, meaction: 'DELETE_TOOL' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tool',
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
    // ISSUE TOOL MODAL
    // ==========================================
    this.__openIssueModal = function(tool_id) {
        $('#issue_tool_id').val('');
        $('#issue_tool_code').val('');
        $('#issue_tool_name').val('');
        $('#issue_max_qty').val('1');
        $('#issue_issued_to').val('');
        $('#issue_quantity').val('1');
        $('#issue_issued_to_type').val('');
        var today = new Date().toISOString().split('T')[0];
        $('#issue_date').val(today);
        $('#issue_time').val(new Date().toTimeString().slice(0,5));
        $('#issue_expected_return').val('');
        $('#issue_condition_before').val('GOOD');
        $('#issue_reference').val('');
        $('#issue_purpose').val('');
        $('#issue_truck_id').val('');
        $('#issue_truck_plate').val('');
        $('#issue_remarks').val('');
        $('#issue_stock_display').hide();

        var modal = new bootstrap.Modal(document.getElementById('issueModal'));
        modal.show();

        if (tool_id) {
            setTimeout(function() {
                $('#issue_tool_id').val(tool_id);
                __Tool.__onIssueToolChange($('#issue_tool_id')[0]);
            }, 300);
        }
    };

    this.__onIssueToolChange = function(el) {
        var code = $(el).find(':selected').data('code') || '';
        var name = $(el).find(':selected').data('name') || '';
        var condition = $(el).find(':selected').data('condition') || 'GOOD';
        var onhand = parseInt($(el).find(':selected').data('onhand')) || 0;

        $('#issue_tool_code').val(code);
        $('#issue_tool_name').val(name);
        $('#issue_max_qty').val(onhand);

        if (condition) {
            $('#issue_condition_before').val(condition);
        }

        if (onhand > 0) {
            $('#issue_stock_display').show();
            $('#issue_onhand_text').text(onhand + ' unit(s)');
            $('#issue_quantity').attr('max', onhand);
            if (parseInt($('#issue_quantity').val()) > onhand) {
                $('#issue_quantity').val(onhand);
            }
        } else {
            $('#issue_stock_display').hide();
        }
    };

    this.__onIssueTruckChange = function(el) {
        var plate = $(el).find(':selected').data('plate') || '';
        $('#issue_truck_plate').val(plate);
    };

    this.__saveIssuance = function() {
        var tool_id = $('#issue_tool_id').val();
        var issued_to = $('#issue_issued_to').val().trim();
        var qty = parseInt($('#issue_quantity').val()) || 1;
        var maxQty = parseInt($('#issue_max_qty').val()) || 0;

        if (!tool_id) {
            toastr.warning('Please select a tool', 'Missing field');
            return;
        }
        if (!issued_to) {
            toastr.warning('Please enter the person to issue to', 'Missing field');
            $('#issue_issued_to').focus();
            return;
        }
        if (qty < 1) {
            toastr.warning('Quantity must be at least 1');
            $('#issue_quantity').focus();
            return;
        }
        if (maxQty > 0 && qty > maxQty) {
            toastr.warning('Only ' + maxQty + ' available on hand');
            $('#issue_quantity').focus();
            return;
        }

        var mparam = {
            tool_id: tool_id,
            tool_code: $('#issue_tool_code').val(),
            tool_name: $('#issue_tool_name').val(),
            quantity_issued: qty,
            issued_to: issued_to,
            issued_to_type: $('#issue_issued_to_type').val(),
            issue_date: $('#issue_date').val(),
            issue_time: $('#issue_time').val(),
            expected_return: $('#issue_expected_return').val(),
            condition_before: $('#issue_condition_before').val(),
            purpose: $('#issue_purpose').val(),
            reference_number: $('#issue_reference').val(),
            truck_id: $('#issue_truck_id').val() || null,
            truck_plate: $('#issue_truck_plate').val(),
            remarks: $('#issue_remarks').val(),
            meaction: 'SAVE_ISSUANCE'
        };

        var btn = $('#issueSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tool',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('issueModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Issue Tool Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-box-arrow-right"></i> Issue Tool');
            }
        });
    };

    // ==========================================
    // RETURN TOOL MODAL
    // ==========================================
    this.__openReturnModal = function(issuance_id) {
        $('#return_issuance_id').val(issuance_id);
        var today = new Date().toISOString().split('T')[0];
        $('#return_date').val(today);
        $('#return_time').val(new Date().toTimeString().slice(0,5));
        $('#return_condition').val('GOOD');
        $('#return_returned_by').val('');
        $('#return_received_by').val('');
        $('#return_remarks').val('');
        $('#return_quantity').val('1');

        var mparam = { issuance_id: issuance_id, meaction: 'GET_ISSUANCE' };
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tool',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data) {
                    var pending = parseInt(data.quantity_pending) || 0;
                    var issued = parseInt(data.quantity_issued) || 0;
                    var returned = parseInt(data.quantity_returned) || 0;

                    var html = '<strong>' + data.tool_name + '</strong> (' + data.tool_code + ')<br>' +
                               'Issued to: <strong>' + data.issued_to + '</strong> on ' + data.issue_date + '<br>' +
                               'Progress: <strong>' + returned + ' of ' + issued + '</strong> returned · ' +
                               '<strong style="color:#f59e0b;">' + pending + ' still out</strong><br>' +
                               'Expected return: ' + (data.expected_return || '—');
                    $('#return_info_text').html(html);
                    $('#return_returned_by').val(data.issued_to);
                    $('#return_quantity').val(pending);
                    $('#return_quantity').attr('max', pending);
                }
            }
        });

        var modal = new bootstrap.Modal(document.getElementById('returnModal'));
        modal.show();
    };

    this.__saveReturn = function() {
        var issuance_id = $('#return_issuance_id').val();
        var condition = $('#return_condition').val();
        var qty = parseInt($('#return_quantity').val()) || 1;
        var maxQty = parseInt($('#return_quantity').attr('max')) || 1;

        if (!condition) {
            toastr.warning('Please select condition after return', 'Missing field');
            return;
        }
        if (qty < 1) {
            toastr.warning('Quantity must be at least 1');
            $('#return_quantity').focus();
            return;
        }
        if (qty > maxQty) {
            toastr.warning('Cannot return more than ' + maxQty + ' pending');
            $('#return_quantity').focus();
            return;
        }

        var mparam = {
            issuance_id: issuance_id,
            quantity_return: qty,
            actual_return: $('#return_date').val(),
            return_time: $('#return_time').val(),
            condition_after: condition,
            returned_by: $('#return_returned_by').val(),
            received_by: $('#return_received_by').val(),
            return_remarks: $('#return_remarks').val(),
            meaction: 'RETURN_TOOL'
        };

        var btn = $('#returnSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tool',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('returnModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Return Tool Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-check-circle"></i> Confirm Return');
            }
        });
    };

    this.__deleteIssuance = function(issuance_id) {
        if (confirm('Delete this issuance record? Any pending quantity will be restored to the tool.')) {
            var mparam = { issuance_id: issuance_id, meaction: 'DELETE_ISSUANCE' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-tool',
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
    // TOOL JOURNEY TIMELINE
    // ==========================================
    this.__openJourneyModal = function(tool_id, tool_name) {
        $('#journey_tool_id').val(tool_id);
        $('#journey_tool_name').text(tool_name);
        $('#journeyContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');

        var modal = new bootstrap.Modal(document.getElementById('journeyModal'));
        modal.show();

        var mparam = { tool_id: tool_id, meaction: 'GET_TOOL_JOURNEY' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-tool',
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
                        if (type.indexOf('ISSUED') !== -1) cls = 'j-issued';
                        else if (type.indexOf('RETURNED') !== -1) cls = 'j-returned';
                        else if (type.indexOf('OVERDUE') !== -1) cls = 'j-overdue';

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
                           '<p>This tool has no recorded events.</p></div>';
                }

                $('#journeyContent').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Tool Journey Error:', status, error, xhr.responseText);
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
    console.log('Tools module ready');
});