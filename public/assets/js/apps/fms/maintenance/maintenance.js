var __MT_PREVIEW_PARTS = [];
var __MT = new __MT();

function __MT() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Maintenance initialized, URL: ' + mesiteurl);

    // ==============================
    // TRUCK HELPERS
    // ==============================
    this.__onTruckChange = function(el) {
        var plate = $(el).find(':selected').data('plate') || '';
        var odo = $(el).find(':selected').data('odo') || 0;
        $('#sched_truck_plate').val(plate);
        $('#sched_current_odo').val(odo);
        __MT.__calcNextService();
    };

    this.__onRecordTruckChange = function(el) {
        var plate = $(el).find(':selected').data('plate') || '';
        var odo = $(el).find(':selected').data('odo') || 0;
        $('#rec_truck_plate').val(plate);
        if (!$('#rec_odometer').val()) {
            $('#rec_odometer').val(odo);
        }
    };

    this.__calcNextService = function() {
        var cur = parseFloat($('#sched_current_odo').val()) || 0;
        var interval = parseFloat($('#sched_interval').val()) || 0;
        if (cur && interval) {
            $('#sched_next_odo').val(cur + interval);
        }
    };

    // ==============================
    // SCHEDULE MODAL
    // ==============================
    this.__openScheduleModal = function(schedule_id) {
        if (schedule_id) {
            var mparam = { schedule_id: schedule_id, meaction: 'GET_SCHEDULE' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-maintenance',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data && data.schedule_id) {
                        $('#sched_id').val(data.schedule_id);
                        $('#sched_truck_id').val(data.truck_id);
                        $('#sched_truck_plate').val(data.truck_plate);
                        $('#sched_maintenance_type').val(data.maintenance_type);
                        $('#sched_service_type').val(data.service_type);
                        $('#sched_date').val(data.scheduled_date);
                        $('#sched_current_odo').val(data.current_odometer);
                        $('#sched_interval').val(data.service_interval);
                        $('#sched_next_odo').val(data.next_service_odometer);
                        $('#sched_technician').val(data.technician);
                        $('#sched_priority').val(data.priority);
                        $('#sched_status').val(data.status);
                        $('#sched_remarks').val(data.remarks);

                        $('#scheduleModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Maintenance Schedule');
                        $('#schedBtnText').text('Update Schedule');
                        $('#schedSubmitBtn').attr('onclick', '__MT.__updateSchedule()');
                    }
                }
            });
        } else {
            $('#sched_id').val('');
            $('#sched_truck_id').val('');
            $('#sched_truck_plate').val('');
            $('#sched_maintenance_type').val('PREVENTIVE');
            $('#sched_service_type').val('');
            var today = new Date().toISOString().split('T')[0];
            $('#sched_date').val(today);
            $('#sched_current_odo').val('');
            $('#sched_interval').val('');
            $('#sched_next_odo').val('');
            $('#sched_technician').val('');
            $('#sched_priority').val('NORMAL');
            $('#sched_status').val('SCHEDULED');
            $('#sched_remarks').val('');

            $('#scheduleModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Maintenance Schedule');
            $('#schedBtnText').text('Save Schedule');
            $('#schedSubmitBtn').attr('onclick', '__MT.__saveSchedule()');
        }

        var modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        modal.show();
    };

    this.__saveSchedule = function() {
        var truck_id = $('#sched_truck_id').val();
        var scheduled_date = $('#sched_date').val();

        if (!truck_id) {
            toastr.warning('Please select a truck', 'Missing field');
            $('#sched_truck_id').focus();
            return;
        }
        if (!scheduled_date) {
            toastr.warning('Please select scheduled date', 'Missing field');
            $('#sched_date').focus();
            return;
        }

        var mparam = {
            truck_id: truck_id,
            truck_plate: $('#sched_truck_plate').val(),
            maintenance_type: $('#sched_maintenance_type').val(),
            service_type: $('#sched_service_type').val(),
            scheduled_date: scheduled_date,
            current_odometer: $('#sched_current_odo').val() || 0,
            service_interval: $('#sched_interval').val() || 0,
            next_service_odometer: $('#sched_next_odo').val() || 0,
            technician: $('#sched_technician').val(),
            priority: $('#sched_priority').val(),
            status: $('#sched_status').val(),
            remarks: $('#sched_remarks').val(),
            meaction: 'SAVE_SCHEDULE'
        };

        var btn = $('#schedSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('scheduleModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Schedule Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Schedule');
            }
        });
    };

    this.__updateSchedule = function() {
        var schedule_id = $('#sched_id').val();
        var truck_id = $('#sched_truck_id').val();
        var scheduled_date = $('#sched_date').val();

        if (!truck_id) { toastr.warning('Please select a truck'); return; }
        if (!scheduled_date) { toastr.warning('Please select scheduled date'); return; }

        var mparam = {
            schedule_id: schedule_id,
            truck_id: truck_id,
            truck_plate: $('#sched_truck_plate').val(),
            maintenance_type: $('#sched_maintenance_type').val(),
            service_type: $('#sched_service_type').val(),
            scheduled_date: scheduled_date,
            current_odometer: $('#sched_current_odo').val() || 0,
            service_interval: $('#sched_interval').val() || 0,
            next_service_odometer: $('#sched_next_odo').val() || 0,
            technician: $('#sched_technician').val(),
            priority: $('#sched_priority').val(),
            status: $('#sched_status').val(),
            remarks: $('#sched_remarks').val(),
            meaction: 'UPDATE_SCHEDULE'
        };

        var btn = $('#schedSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('scheduleModal'));
                    if (modal) modal.hide();
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update Schedule Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update Schedule');
            }
        });
    };

    this.__deleteSchedule = function(schedule_id) {
        if (confirm('Are you sure you want to delete this schedule?')) {
            var mparam = { schedule_id: schedule_id, meaction: 'DELETE_SCHEDULE' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-maintenance',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    };

    // ==============================
    // CREATE RECORD FROM SCHEDULE
    // ==============================
    this.__createRecordFromSchedule = function(schedule_id) {
        var mparam = { schedule_id: schedule_id, meaction: 'GET_SCHEDULE' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data && data.schedule_id) {
                    __MT.__openRecordModal();
                    setTimeout(function() {
                        $('#rec_schedule_id').val(data.schedule_id);
                        $('#rec_truck_id').val(data.truck_id);
                        $('#rec_truck_plate').val(data.truck_plate);
                        $('#rec_odometer').val(data.current_odometer);
                        $('#rec_maintenance_type').val(data.maintenance_type);
                        $('#rec_service_category').val(data.service_type);
                        $('#rec_technician').val(data.technician);
                    }, 300);
                }
            }
        });
    };

    // ==============================
    // RECORD MODAL
    // ==============================
    this.__openRecordModal = function(record_id) {
        __MT_PREVIEW_PARTS = [];

        if (record_id) {
            var mparam = { record_id: record_id, meaction: 'GET_RECORD' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-maintenance',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data && data.record_id) {
                        $('#rec_id').val(data.record_id);
                        $('#rec_schedule_id').val(data.schedule_id || '');
                        $('#rec_truck_id').val(data.truck_id);
                        $('#rec_truck_plate').val(data.truck_plate);
                        $('#rec_date').val(data.maintenance_date);
                        $('#rec_odometer').val(data.odometer);
                        $('#rec_maintenance_type').val(data.maintenance_type);
                        $('#rec_service_category').val(data.service_category);
                        $('#rec_technician').val(data.technician);
                        $('#rec_problem_reason').val(data.problem_reason);
                        $('#rec_work_performed').val(data.work_performed);
                        $('#rec_labor_cost').val(data.labor_cost);
                        $('#rec_parts_cost').val(data.parts_cost);
                        $('#rec_other_cost').val(data.other_cost);
                        $('#rec_total_cost').val(data.total_cost);
                        $('#rec_downtime').val(data.downtime_hours);
                        $('#rec_status').val(data.status);
                        $('#rec_remarks').val(data.remarks);

                        $('#recordModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Maintenance Record');
                        $('#recBtnText').text('Update Record');
                        $('#recSubmitBtn').attr('onclick', '__MT.__updateRecord()');

                        __MT.__loadParts(data.record_id);
                    }
                }
            });
        } else {
            $('#rec_id').val('');
            $('#rec_schedule_id').val('');
            $('#rec_truck_id').val('');
            $('#rec_truck_plate').val('');
            var today = new Date().toISOString().split('T')[0];
            $('#rec_date').val(today);
            $('#rec_odometer').val('');
            $('#rec_maintenance_type').val('PREVENTIVE');
            $('#rec_service_category').val('');
            $('#rec_technician').val('');
            $('#rec_problem_reason').val('');
            $('#rec_work_performed').val('');
            $('#rec_labor_cost').val('0');
            $('#rec_parts_cost').val('0');
            $('#rec_other_cost').val('0');
            $('#rec_total_cost').val('0');
            $('#rec_downtime').val('');
            $('#rec_status').val('COMPLETED');
            $('#rec_remarks').val('');

            $('#recordModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Maintenance Record');
            $('#recBtnText').text('Save Record');
            $('#recSubmitBtn').attr('onclick', '__MT.__saveRecord()');

            __MT.__renderPreviewParts();
        }

        var modal = new bootstrap.Modal(document.getElementById('recordModal'));
        modal.show();
    };

    // ==============================
    // COST SUMMARY
    // ==============================
    this.__updateCostSummary = function() {
        var labor = parseFloat($('#rec_labor_cost').val()) || 0;
        var parts = parseFloat($('#rec_parts_cost').val()) || 0;
        var other = parseFloat($('#rec_other_cost').val()) || 0;
        $('#rec_total_cost').val((labor + parts + other).toFixed(2));
    };

    // ==============================
    // SAVE RECORD
    // ==============================
    this.__saveRecord = function() {
        var truck_id = $('#rec_truck_id').val();
        var date = $('#rec_date').val();

        if (!truck_id) { toastr.warning('Please select a truck'); return; }
        if (!date) { toastr.warning('Please select maintenance date'); return; }

        var mparam = {
            schedule_id: $('#rec_schedule_id').val() || null,
            truck_id: truck_id,
            truck_plate: $('#rec_truck_plate').val(),
            maintenance_date: date,
            odometer: $('#rec_odometer').val() || 0,
            maintenance_type: $('#rec_maintenance_type').val(),
            service_category: $('#rec_service_category').val(),
            problem_reason: $('#rec_problem_reason').val(),
            work_performed: $('#rec_work_performed').val(),
            technician: $('#rec_technician').val(),
            labor_cost: $('#rec_labor_cost').val() || 0,
            other_cost: $('#rec_other_cost').val() || 0,
            downtime_hours: $('#rec_downtime').val() || 0,
            status: $('#rec_status').val(),
            remarks: $('#rec_remarks').val(),
            meaction: 'SAVE_RECORD'
        };

        if (__MT_PREVIEW_PARTS.length > 0) {
            $.each(__MT_PREVIEW_PARTS, function(i, item) {
                mparam['parts[' + i + '][part_name]'] = item.part_name;
                mparam['parts[' + i + '][quantity]']  = item.quantity;
                mparam['parts[' + i + '][unit_cost]'] = item.unit_cost;
                mparam['parts[' + i + '][supplier]']  = item.supplier;
                mparam['parts[' + i + '][warranty]']  = item.warranty;
            });
        }

        var btn = $('#recSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    __MT_PREVIEW_PARTS = [];

                    var modal = bootstrap.Modal.getInstance(document.getElementById('recordModal'));
                    if (modal) modal.hide();

                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Record Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Record');
            }
        });
    };

    this.__updateRecord = function() {
        var record_id = $('#rec_id').val();
        var mparam = {
            record_id: record_id,
            truck_id: $('#rec_truck_id').val(),
            truck_plate: $('#rec_truck_plate').val(),
            maintenance_date: $('#rec_date').val(),
            odometer: $('#rec_odometer').val() || 0,
            maintenance_type: $('#rec_maintenance_type').val(),
            service_category: $('#rec_service_category').val(),
            problem_reason: $('#rec_problem_reason').val(),
            work_performed: $('#rec_work_performed').val(),
            technician: $('#rec_technician').val(),
            labor_cost: $('#rec_labor_cost').val() || 0,
            other_cost: $('#rec_other_cost').val() || 0,
            downtime_hours: $('#rec_downtime').val() || 0,
            status: $('#rec_status').val(),
            remarks: $('#rec_remarks').val(),
            meaction: 'UPDATE_RECORD'
        };

        var btn = $('#recSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update Record Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Update Record');
            }
        });
    };

    this.__deleteRecord = function(record_id) {
        if (confirm('Are you sure you want to delete this maintenance record? This will also delete all associated parts.')) {
            var mparam = { record_id: record_id, meaction: 'DELETE_RECORD' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-maintenance',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    };

    // ==============================
    // PARTS — LOAD
    // ==============================
    this.__loadParts = function(record_id) {
        if (!record_id || record_id == 0) {
            __MT.__renderPreviewParts();
            return;
        }

        var mparam = { record_id: record_id, meaction: 'GET_PARTS' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data && data.length > 0) {
                    $.each(data, function(index, row) {
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td><strong>' + row.part_name + '</strong></td>';
                        html += '<td>' + parseFloat(row.quantity).toFixed(2) + '</td>';
                        html += '<td>₱' + parseFloat(row.unit_cost).toFixed(2) + '</td>';
                        html += '<td>₱' + parseFloat(row.total_cost).toFixed(2) + '</td>';
                        html += '<td>' + (row.supplier || '—') + '</td>';
                        html += '<td>' + (row.warranty || '—') + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button type="button" class="btn-icon btn-icon-edit" onclick="__MT.__editPart(' + row.part_id + ')" title="Edit"><i class="bi bi-pencil"></i></button>';
                        html += '<button type="button" class="btn-icon btn-icon-delete" onclick="__MT.__deletePart(' + row.part_id + ')" title="Delete"><i class="bi bi-trash"></i></button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="8" class="text-center text-muted">No parts recorded</td></tr>';
                }
                $('#partsBody').html(html);
            }
        });
    };

    // ==============================
    // PARTS — SAVE
    // ==============================
    this.__savePart = function() {
        var record_id = $('#rec_id').val();
        var part_name = $('#part_name').val().trim();

        if (!part_name) {
            toastr.warning('Please enter part name');
            $('#part_name').focus();
            return;
        }

        if (record_id && record_id != 0) {
            var mparam = {
                record_id: record_id,
                part_name: part_name,
                quantity: $('#part_qty').val() || 1,
                unit_cost: $('#part_unit_cost').val() || 0,
                supplier: $('#part_supplier').val(),
                warranty: $('#part_warranty').val(),
                meaction: 'SAVE_PART'
            };

            var btn = $('#partActionBtn');
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>');

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-maintenance',
                data: mparam,
                dataType: 'json',
                timeout: 30000,
                success: function(data) {
                    if (data && data.status == 'success') {
                        toastr.success(data.message);
                        __MT.__resetPartForm();
                        __MT.__loadParts(record_id);
                        __MT.__refreshRecordCost(record_id);
                    } else {
                        toastr.error(data && data.message ? data.message : 'Unknown error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save Part Error:', status, error, xhr.responseText);
                    toastr.error("Error: " + error);
                },
                complete: function() {
                    btn.prop('disabled', false);
                    btn.html('<i class="bi bi-plus"></i> Add');
                }
            });
            return;
        }

        __MT_PREVIEW_PARTS.push({
            part_name: part_name,
            quantity: parseFloat($('#part_qty').val()) || 1,
            unit_cost: parseFloat($('#part_unit_cost').val()) || 0,
            supplier: $('#part_supplier').val(),
            warranty: $('#part_warranty').val()
        });

        __MT.__resetPartForm();
        __MT.__renderPreviewParts();
        __MT.__updatePreviewPartsCost();
        toastr.success('Part added to list');
    };

    // ==============================
    // PARTS — EDIT
    // ==============================
    this.__editPart = function(part_id) {
        var mparam = { part_id: part_id, meaction: 'GET_PART' };
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $('#part_editing_id').val(data.part_id);
                    $('#part_name').val(data.part_name);
                    $('#part_qty').val(data.quantity);
                    $('#part_unit_cost').val(data.unit_cost);
                    $('#part_supplier').val(data.supplier);
                    $('#part_warranty').val(data.warranty);

                    var btn = $('#partActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__MT.__updatePart()');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            }
        });
    };

    this.__updatePart = function() {
        var part_id = $('#part_editing_id').val();
        var record_id = $('#rec_id').val();
        var part_name = $('#part_name').val().trim();

        if (!part_name) { toastr.warning('Please enter part name'); return; }

        var mparam = {
            part_id: part_id,
            part_name: part_name,
            quantity: $('#part_qty').val() || 1,
            unit_cost: $('#part_unit_cost').val() || 0,
            supplier: $('#part_supplier').val(),
            warranty: $('#part_warranty').val(),
            meaction: 'UPDATE_PART'
        };

        var btn = $('#partActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.status == 'success') {
                    toastr.success(data.message);
                    __MT.__resetPartForm();
                    __MT.__loadParts(record_id);
                    __MT.__refreshRecordCost(record_id);
                } else {
                    toastr.error(data && data.message ? data.message : 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update Part Error:', status, error, xhr.responseText);
                toastr.error("Error: " + error);
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-plus"></i> Add');
            }
        });
    };

    this.__deletePart = function(part_id) {
        if (confirm('Delete this part?')) {
            var record_id = $('#rec_id').val();
            var mparam = { part_id: part_id, meaction: 'DELETE_PART' };
            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-maintenance',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message);
                        __MT.__loadParts(record_id);
                        __MT.__refreshRecordCost(record_id);
                    }
                }
            });
        }
    };

    // ==============================
    // PARTS — PREVIEW
    // ==============================
    this.__resetPartForm = function() {
        $('#part_name').val('');
        $('#part_qty').val('1');
        $('#part_unit_cost').val('');
        $('#part_supplier').val('');
        $('#part_warranty').val('');
        $('#part_editing_id').val('');

        var btn = $('#partActionBtn');
        btn.html('<i class="bi bi-plus"></i> Add');
        btn.attr('onclick', '__MT.__savePart()');
        btn.removeClass('btn-warning').addClass('btn-primary');
    };

    this.__renderPreviewParts = function() {
        var html = '';

        if (__MT_PREVIEW_PARTS.length > 0) {
            $.each(__MT_PREVIEW_PARTS, function(i, row) {
                var total = parseFloat(row.quantity) * parseFloat(row.unit_cost);
                html += '<tr>';
                html += '<td>' + (i + 1) + '</td>';
                html += '<td><strong>' + row.part_name + '</strong></td>';
                html += '<td>' + parseFloat(row.quantity).toFixed(2) + '</td>';
                html += '<td>₱' + parseFloat(row.unit_cost).toFixed(2) + '</td>';
                html += '<td>₱' + total.toFixed(2) + '</td>';
                html += '<td>' + (row.supplier || '—') + '</td>';
                html += '<td>' + (row.warranty || '—') + '</td>';
                html += '<td class="text-center">';
                html += '<div class="action-group">';
                html += '<button type="button" class="btn-icon btn-icon-edit" onclick="__MT.__editPreviewPart(' + i + ')" title="Edit"><i class="bi bi-pencil"></i></button>';
                html += '<button type="button" class="btn-icon btn-icon-delete" onclick="__MT.__deletePreviewPart(' + i + ')" title="Remove"><i class="bi bi-trash"></i></button>';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="8" class="text-center text-muted">No parts added yet</td></tr>';
        }

        $('#partsBody').html(html);
    };

    this.__editPreviewPart = function(index) {
        var row = __MT_PREVIEW_PARTS[index];
        if (!row) return;

        $('#part_name').val(row.part_name);
        $('#part_qty').val(row.quantity);
        $('#part_unit_cost').val(row.unit_cost);
        $('#part_supplier').val(row.supplier);
        $('#part_warranty').val(row.warranty);

        __MT_PREVIEW_PARTS.splice(index, 1);
        __MT.__renderPreviewParts();
        __MT.__updatePreviewPartsCost();

        toastr.info('Item removed — edit and click Add to re-add.');
    };

    this.__deletePreviewPart = function(index) {
        if (confirm('Remove this part?')) {
            __MT_PREVIEW_PARTS.splice(index, 1);
            __MT.__renderPreviewParts();
            __MT.__updatePreviewPartsCost();
        }
    };

    this.__updatePreviewPartsCost = function() {
        var parts_total = 0;
        $.each(__MT_PREVIEW_PARTS, function(i, row) {
            parts_total += parseFloat(row.quantity) * parseFloat(row.unit_cost);
        });

        $('#rec_parts_cost').val(parts_total.toFixed(2));
        __MT.__updateCostSummary();
    };

    // ==============================
    // REFRESH RECORD COSTS
    // ==============================
    this.__refreshRecordCost = function(record_id) {
        var mparam = { record_id: record_id, meaction: 'GET_RECORD' };
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-maintenance',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $('#rec_parts_cost').val(data.parts_cost);
                    $('#rec_total_cost').val(data.total_cost);
                }
            }
        });
    };
}

// ==============================
// TAB SWITCHING
// ==============================
function switchTab(tab) {
    if (tab === 'schedules') {
        $('#tabSchedules').addClass('active');
        $('#tabRecords').removeClass('active');
        $('#paneSchedules').show();
        $('#paneRecords').hide();
    } else {
        $('#tabRecords').addClass('active');
        $('#tabSchedules').removeClass('active');
        $('#paneRecords').show();
        $('#paneSchedules').hide();
    }
}

// ==============================
// STAT CARD FILTER
// ==============================
function filterMTTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    // Records card → switch to Records tab
    if (status === 'RECORDS') {
        switchTab('records');
        return;
    }

    // All other cards → Schedules tab + filter
    switchTab('schedules');

    var columnIndex = 9;
    if (status === 'all') {
        scheduleTable.column(columnIndex).search('', true, false).draw();
    } else {
        var labelMap = {
            'IN_PROGRESS' : 'IN PROGRESS',
            'OVERDUE'     : 'OVERDUE'
        };
        var searchTerm = labelMap[status] || status;
        scheduleTable.column(columnIndex).search(searchTerm, true, false).draw();
    }
}

$(document).ready(function() {
    console.log('Maintenance ready');

    $(document).on('input', '#sched_current_odo, #sched_interval', function() {
        __MT.__calcNextService();
    });
});