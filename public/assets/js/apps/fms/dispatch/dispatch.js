var __Dispatch = new __Dispatch();

function __Dispatch() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Dispatch initialized, URL: ' + mesiteurl);

        // ==============================
    // SHOW PDF IN MODAL
    // ==============================
    this.__showPdfInModal = function(pdfUrl) {
        var pdfFrame = document.getElementById("pdfFrame");
        var pdfModal = new bootstrap.Modal(document.getElementById("pdfModal"));

        pdfFrame.src = pdfUrl;
        pdfModal.show();
    };
    // ==============================
    // OPEN DISPATCH MODAL
    // ==============================
    this.__openDispatchModal = function(trip_id) {
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_DISPATCH_BY_TRIP'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                console.log('Dispatch data:', data);
                
                $('#dispatch_trip_id').val(trip_id);
                
                var tripParam = {
                    trip_id: trip_id,
                    meaction: 'GET_TRIP'
                };
                
                jQuery.ajax({
                    type: "POST",
                    url: mesiteurl + 'fms-trips',
                    data: tripParam,
                    dataType: 'json',
                    success: function(tripData) {
                        if(tripData) {
                            $('#dispatch_trip_code').text(tripData.trip_code);
                            
                            var assignParam = {
                                trip_id: trip_id,
                                meaction: 'GET_ASSIGNMENT'
                            };
                            jQuery.ajax({
                                type: "POST",
                                url: mesiteurl + 'fms-trips',
                                data: assignParam,
                                dataType: 'json',
                                success: function(assignData) {
                                    console.log('Assignment data:', assignData);
                                    
                                    if(assignData && assignData.assignment_id) {
                                        var vehicleDisplay = '';
                                        var vehicleTypeLabel = '';
                                        var truckValue = '';
                                        var driverValue = assignData.driver_name || '';
                                        var helperValue = assignData.helper_name || '';
                                        var originValue = tripData.origin || '';
                                        var destinationValue = tripData.destination || '';
                                        var vendorName = assignData.vendor_name || '';
                                        
                                        switch(assignData.vehicle_type) {
                                            case 'RIGID':
                                                vehicleTypeLabel = 'Rigid Truck';
                                                vehicleDisplay = assignData.truck_plate || '—';
                                                truckValue = assignData.truck_plate || '';
                                                break;
                                            case 'TRACTOR_CHASSIS':
                                                vehicleTypeLabel = 'Tractor + Owned Chassis';
                                                if(assignData.tractor_plate && assignData.chassis_plate) {
                                                    vehicleDisplay = assignData.tractor_plate + ' + ' + assignData.chassis_plate;
                                                    truckValue = assignData.tractor_plate + ' + ' + assignData.chassis_plate;
                                                } else if(assignData.tractor_plate) {
                                                    vehicleDisplay = assignData.tractor_plate + ' + (Chassis)';
                                                    truckValue = assignData.tractor_plate + ' + (Chassis)';
                                                } else if(assignData.chassis_plate) {
                                                    vehicleDisplay = '(Tractor) + ' + assignData.chassis_plate;
                                                    truckValue = '(Tractor) + ' + assignData.chassis_plate;
                                                } else {
                                                    vehicleDisplay = 'Tractor + Chassis';
                                                    truckValue = 'Tractor + Chassis';
                                                }
                                                break;
                                            case 'TRACTOR_RENTED_CHASSIS':
                                                vehicleTypeLabel = 'Tractor + Rented Chassis';
                                                if(assignData.tractor_plate && assignData.chassis_plate) {
                                                    vehicleDisplay = assignData.tractor_plate + ' + ' + assignData.chassis_plate + ' (Rented)';
                                                    truckValue = assignData.tractor_plate + ' + ' + assignData.chassis_plate + ' (Rented)';
                                                } else if(assignData.tractor_plate) {
                                                    vehicleDisplay = assignData.tractor_plate + ' + (Rented Chassis)';
                                                    truckValue = assignData.tractor_plate + ' + (Rented Chassis)';
                                                } else if(assignData.chassis_plate) {
                                                    vehicleDisplay = '(Tractor) + ' + assignData.chassis_plate + ' (Rented)';
                                                    truckValue = '(Tractor) + ' + assignData.chassis_plate + ' (Rented)';
                                                } else {
                                                    vehicleDisplay = 'Tractor + Rented Chassis';
                                                    truckValue = 'Tractor + Rented Chassis';
                                                }
                                                break;
                                            case 'CHASSIS_ONLY':
                                                vehicleTypeLabel = 'Chassis Only';
                                                vehicleDisplay = assignData.chassis_plate || 'Chassis Only';
                                                truckValue = assignData.chassis_plate || 'Chassis Only';
                                                break;
                                            case 'RENTED_ALL':
                                                vehicleTypeLabel = 'Rented All (Package)';
                                                var vendorUnits = [assignData.tractor_plate, assignData.chassis_plate].filter(Boolean).join(' + ');
                                                vehicleDisplay = (vendorUnits ? vendorUnits + ' — ' : '') + 'Package: ' + (vendorName || 'Vendor');
                                                truckValue = (vendorUnits || 'Rented Package') + ' (Vendor: ' + (vendorName || '—') + ')';
                                                break;
                                            default:
                                                vehicleTypeLabel = '—';
                                                vehicleDisplay = '—';
                                                truckValue = '';
                                        }
                                        
                                        // UPDATE THE DISPLAY CARD
                                        $('#dispatch_vehicle_type_display').text(vehicleTypeLabel);
                                        $('#dispatch_truck_display').text(vehicleDisplay);
                                        $('#dispatch_driver_display').text(driverValue);
                                        $('#dispatch_helper_display').text(helperValue);
                                        
                                        // Also update origin and destination display
                                        $('#dispatch_origin_display').text(originValue);
                                        $('#dispatch_destination_display').text(destinationValue);
                                        
                                        // Populate form fields
                                        if(!data || !data.dispatch_id) {
                                            $('#dispatch_truck').val(truckValue);
                                            $('#dispatch_driver').val(driverValue);
                                            $('#dispatch_helper').val(helperValue);
                                            $('#dispatch_origin').val(originValue);
                                            $('#dispatch_destination').val(destinationValue);
                                            $('#odometer_out').val(assignData.odometer_before_trip || 0);
                                            $('#fuel_level_out').val(assignData.fuel_level || 100);
                                        }
                                    } else {
                                        $('#dispatch_vehicle_type_display').text('No assignment');
                                        $('#dispatch_truck_display').text('No assignment');
                                        $('#dispatch_driver_display').text('No assignment');
                                        $('#dispatch_helper_display').text('No assignment');
                                        $('#dispatch_origin_display').text('—');
                                        $('#dispatch_destination_display').text('—');
                                    }
                                }
                            });
                        }
                    }
                });
                
                if(data && data.dispatch_id) {
                    $('#dispatch_id').val(data.dispatch_id);
                    $('#dispatch_code_display').text(data.dispatch_code);
                    $('#dispatch_date').val(data.dispatch_date);
                    $('#dispatch_time').val(data.dispatch_time);
                    if(data.truck) $('#dispatch_truck').val(data.truck);
                    if(data.driver) $('#dispatch_driver').val(data.driver);
                    if(data.helper) $('#dispatch_helper').val(data.helper);
                    if(data.origin) $('#dispatch_origin').val(data.origin);
                    if(data.destination) $('#dispatch_destination').val(data.destination);
                    $('#odometer_out').val(data.odometer_out);
                    $('#fuel_level_out').val(data.fuel_level_out);
                    $('#container_required').prop('checked', data.container_required == 1);
                    $('#container_number').val(data.container_number);
                    $('#container_type').val(data.container_type);
                    $('#container_description').val(data.container_description);
                    $('#container_markings').val(data.container_markings);
                    $('#container_reference').val(data.container_reference);
                    $('#container_release_port').val(data.container_release_port);
                    $('#container_release_date').val(data.container_release_date);
                    $('#container_release_time').val(data.container_release_time);
                    $('#container_return_required').prop('checked', data.container_return_required == 1);
                    $('#container_return_date').val(data.container_return_date);
                    $('#container_return_time').val(data.container_return_time);
                    $('#container_return_port').val(data.container_return_port);
                    $('#container_return_odometer').val(data.container_return_odometer);
                    $('#container_return_status').val(data.container_return_status);
                    $('#container_return_proof').val(data.container_return_proof);
                    $('#dispatcher_name').val(data.dispatcher_name);
                    $('#trip_status').val(data.trip_status);
                    $('#actual_delivery_date').val(data.actual_delivery_date);
                    $('#actual_delivery_time').val(data.actual_delivery_time);
                    $('#odometer_in').val(data.odometer_in);
                    $('#fuel_level_in').val(data.fuel_level_in);
                    $('#total_distance').val(data.total_distance);
                    $('#fuel_consumed').val(data.fuel_consumed);
                    $('#delay_reason').val(data.delay_reason);
                    $('#dispatch_remarks').val(data.remarks);
                    
                    __Dispatch.__toggleContainerFields();
                    __Dispatch.__toggleReturnFields();
                    
                    $('#dispatchModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Dispatch');
                    $('#dispatchBtnText').text('Update Dispatch');
                    $('#dispatchSubmitBtn').attr('onclick', '__Dispatch.__updateDispatch()');
                    
                    __Dispatch.__loadChecklist(data.dispatch_id);
                    __Dispatch.__loadExpenses(data.dispatch_id);
                    __Dispatch.__loadInspection(data.dispatch_id);
                    
                } else {
                    $('#dispatch_id').val('');
                    $('#dispatch_code_display').text('Auto-generated');
                    var today = new Date().toISOString().split('T')[0];
                    $('#dispatch_date').val(today);
                    $('#dispatch_time').val('06:00');
                    $('#odometer_out').val('');
                    $('#fuel_level_out').val('');
                    $('#container_required').prop('checked', false);
                    $('#container_number').val('');
                    $('#container_type').val('');
                    $('#container_description').val('');
                    $('#container_markings').val('');
                    $('#container_reference').val('');
                    $('#container_release_port').val('');
                    $('#container_release_date').val('');
                    $('#container_release_time').val('');
                    $('#container_return_required').prop('checked', false);
                    $('#container_return_date').val('');
                    $('#container_return_time').val('');
                    $('#container_return_port').val('');
                    $('#container_return_odometer').val('');
                    $('#container_return_status').val('NOT_APPLICABLE');
                    $('#container_return_proof').val('');
                    $('#dispatcher_name').val('');
                    $('#trip_status').val('DISPATCHED');
                    $('#actual_delivery_date').val('');
                    $('#actual_delivery_time').val('');
                    $('#odometer_in').val('');
                    $('#fuel_level_in').val('');
                    $('#total_distance').val('');
                    $('#fuel_consumed').val('');
                    $('#delay_reason').val('');
                    $('#dispatch_remarks').val('');
                    
                    $('#container_fields').hide();
                    $('#container_return_fields').hide();
                    
                    $('#dispatchModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Dispatch');
                    $('#dispatchBtnText').text('Save Dispatch');
                    $('#dispatchSubmitBtn').attr('onclick', '__Dispatch.__saveDispatch()');
                    
                    $('#checklistBody').html('<tr><td colspan="9" class="text-center text-muted">No checklist items</td></tr>');
                    $('#expensesBody').html('<tr><td colspan="8" class="text-center text-muted">No expenses recorded</td></tr>');
                    $('#total_expenses').text('₱0.00');
                    __Dispatch.__resetInspection();
                }
                
                var modal = new bootstrap.Modal(document.getElementById('dispatchModal'));
                modal.show();
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading dispatch: " + error);
                console.error('Error:', error);
            }
        });
    };

    // ==============================
    // TOGGLE CONTAINER FIELDS
    // ==============================
    this.__toggleContainerFields = function() {
        if($('#container_required').is(':checked')) {
            $('#container_fields').show();
        } else {
            $('#container_fields').hide();
            $('#container_return_fields').hide();
        }
    };

    // ==============================
    // TOGGLE RETURN FIELDS
    // ==============================
    this.__toggleReturnFields = function() {
        if($('#container_return_required').is(':checked') && $('#container_required').is(':checked')) {
            $('#container_return_fields').show();
        } else {
            $('#container_return_fields').hide();
        }
    };

    // ==============================
    // PRE-TRIP INSPECTION — RESET (new dispatch)
    // ==============================
    this.__resetInspection = function() {
        $('#insp_code_display').text('Not started').attr('class', 'badge badge-secondary');
        $('#insp_banner').attr('class', 'insp-banner pending');
        $('#insp_banner_text').text('Save the dispatch first, then complete the inspection. The trip can\'t go In Transit until it\'s cleared.');
        $('#insp_date').val(new Date().toISOString().split('T')[0]);
        $('#insp_time').val('');
        $('#insp_inspector_name').val('');
        $('#insp_final_status').val('SAFE');
        $('#insp_defects').val('');
        $('#insp_remarks').val('');
        $('#insp_driver_name').text($('#dispatch_driver_display').text() || '—');
        $('#insp_inspector_display').text('— save the inspector name first');
        $('#insp_form_file, #insp_driver_file, #insp_inspector_file').val('');
        $('#insp_form_upload, #insp_driver_pad, #insp_inspector_pad').show();
        $('#insp_form_preview, #insp_driver_signed, #insp_inspector_signed').hide();
        __Dispatch.__clearSigPad('driver');
        __Dispatch.__clearSigPad('inspector');
    };

    // ==============================
    // PRE-TRIP INSPECTION — LOAD
    // ==============================
    this.__loadInspection = function(dispatch_id) {
        if(!dispatch_id) { __Dispatch.__resetInspection(); return; }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: { dispatch_id: dispatch_id, meaction: 'GET_INSPECTION' },
            dataType: 'json',
            success: function(data) {
                __Dispatch.__resetInspection();
                if(!data) return;
                __Dispatch.__renderInspection(data);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading inspection: " + error);
            }
        });
    };

    // ==============================
    // PRE-TRIP INSPECTION — RENDER
    // ==============================
    this.__renderInspection = function(data) {
        var ins = data.inspection;
        $('#insp_driver_name').text(data.assigned_driver_name || 'No driver assigned');

        if(data.cleared) {
            $('#insp_banner').attr('class', 'insp-banner ok');
            $('#insp_banner_text').text('Inspection cleared — the truck may go In Transit.');
            $('#insp_code_display').attr('class', 'badge badge-success');
        } else if(ins && ins.final_status === 'REQUIRES_REPAIR') {
            $('#insp_banner').attr('class', 'insp-banner blocked');
            $('#insp_banner_text').text('Vehicle requires repair — it cannot go In Transit until it is re-inspected as Safe and signed again.');
            $('#insp_code_display').attr('class', 'badge badge-danger');
        } else {
            $('#insp_banner').attr('class', 'insp-banner pending');
            $('#insp_banner_text').text('Still needed before In Transit: ' + data.missing.join(', ') + '.');
            $('#insp_code_display').attr('class', 'badge badge-warning');
        }
        if(!ins) return;

        $('#insp_code_display').text(ins.inspection_code);
        $('#insp_date').val(ins.inspection_date);
        $('#insp_time').val(ins.inspection_time ? ins.inspection_time.substring(0, 5) : '');
        $('#insp_inspector_name').val(ins.inspector_name || '');
        $('#insp_final_status').val(ins.final_status);
        $('#insp_defects').val(ins.defects_found || '');
        $('#insp_remarks').val(ins.remarks || '');
        $('#insp_inspector_display').text(ins.inspector_name || '— save the inspector name first');

        if(ins.inspection_form) {
            var url = mesiteurl + ins.inspection_form;
            $('#insp_form_link').attr('href', url);
            if(/\.pdf$/i.test(ins.inspection_form)) {
                $('#insp_form_img').hide(); $('#insp_form_pdf').show();
            } else {
                $('#insp_form_img').attr('src', url).show(); $('#insp_form_pdf').hide();
            }
            $('#insp_form_upload').hide(); $('#insp_form_preview').show();
        }

        $.each(['driver', 'inspector'], function(i, who) {
            var path = ins[who + '_signature'];
            if(!path) return;
            var name = who === 'driver' ? ins.driver_name : ins.inspector_name;
            $('#insp_' + who + '_img').attr('src', mesiteurl + path);
            $('#insp_' + who + '_meta').text('Signed by ' + (name || '—') + ' · ' + (ins[who + '_signed_at'] || ''));
            $('#insp_' + who + '_pad').hide(); $('#insp_' + who + '_signed').show();
        });
    };

    // ==============================
    // PRE-TRIP INSPECTION — SAVE DETAILS
    // ==============================
    this.__saveInspection = function() {
        var dispatch_id = $('#dispatch_id').val();
        if(!dispatch_id) {
            toastr.warning('Please save the dispatch first before the inspection');
            return;
        }
        if(!$('#insp_inspector_name').val().trim()) {
            toastr.warning('Please enter the inspector name', 'Missing field');
            $('#insp_inspector_name').focus();
            return;
        }

        var mparam = {
            dispatch_id: dispatch_id,
            inspection_date: $('#insp_date').val(),
            inspection_time: $('#insp_time').val(),
            inspector_name: $('#insp_inspector_name').val().trim(),
            final_status: $('#insp_final_status').val(),
            defects_found: $('#insp_defects').val(),
            remarks: $('#insp_remarks').val(),
            meaction: 'SAVE_INSPECTION'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Dispatch.__loadInspection(dispatch_id);
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
    // PRE-TRIP INSPECTION — UPLOAD FORM
    // ==============================
    this.__uploadInspectionForm = function() {
        var dispatch_id = $('#dispatch_id').val();
        var file = $('#insp_form_file')[0].files[0];
        if(!file) return;
        if(!dispatch_id) {
            toastr.warning('Please save the dispatch first before the inspection');
            $('#insp_form_file').val('');
            return;
        }

        var formData = new FormData();
        formData.append('dispatch_id', dispatch_id);
        formData.append('file', file);
        formData.append('meaction', 'UPLOAD_INSPECTION_FORM');

        toastr.info('Uploading inspection form...');
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                $('#insp_form_file').val('');
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Dispatch.__loadInspection(dispatch_id);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                $('#insp_form_file').val('');
                toastr.error("Upload error: " + error);
            }
        });
    };

    // ==============================
    // PRE-TRIP INSPECTION — SIGNATURE PADS (mouse + touch)
    // ==============================
    this.__initSigPad = function(who) {
        var canvas = document.getElementById('insp_' + who + '_canvas');
        if(!canvas) return;
        __Dispatch.__fitSigPad(canvas);
        if(canvas.dataset.initialized === '1') return;
        canvas.dataset.initialized = '1';

        var ctx = canvas.getContext('2d');
        var drawing = false, lastX = 0, lastY = 0;
        var wrapper = $(canvas).closest('.insp-sig-wrapper');

        function getPos(e) {
            var rect = canvas.getBoundingClientRect();
            var t = (e.touches && e.touches[0]) || (e.changedTouches && e.changedTouches[0]) || e;
            return { x: t.clientX - rect.left, y: t.clientY - rect.top };
        }
        function start(e) {
            if(e.cancelable) e.preventDefault();
            drawing = true;
            wrapper.addClass('active');
            var p = getPos(e);
            lastX = p.x; lastY = p.y;
            ctx.beginPath();
            ctx.arc(p.x, p.y, 1.1, 0, Math.PI * 2);
            ctx.fillStyle = '#1a1a1a';
            ctx.fill();
        }
        function move(e) {
            if(!drawing) return;
            if(e.cancelable) e.preventDefault();
            var p = getPos(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            lastX = p.x; lastY = p.y;
        }
        function stop(e) {
            if(!drawing) return;
            if(e.cancelable) e.preventDefault();
            drawing = false;
            wrapper.removeClass('active');
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

    this.__fitSigPad = function(canvas) {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        var w = canvas.offsetWidth, h = canvas.offsetHeight;
        if(w < 10 || h < 10) return;
        canvas.width = w * ratio;
        canvas.height = h * ratio;
        var ctx = canvas.getContext('2d');
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(ratio, ratio);
        ctx.strokeStyle = '#1a1a1a';
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    };

    this.__clearSigPad = function(who) {
        var canvas = document.getElementById('insp_' + who + '_canvas');
        if(!canvas) return;
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    };

    this.__isSigPadEmpty = function(who) {
        var canvas = document.getElementById('insp_' + who + '_canvas');
        if(!canvas) return true;
        var blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        return canvas.toDataURL() === blank.toDataURL();
    };

    // ==============================
    // PRE-TRIP INSPECTION — SIGN (drawn or uploaded image)
    // ==============================
    this.__signInspection = function(who, fromFile) {
        var dispatch_id = $('#dispatch_id').val();
        if(!dispatch_id) {
            toastr.warning('Please save the dispatch first before the inspection');
            return;
        }

        var formData = new FormData();
        formData.append('dispatch_id', dispatch_id);
        formData.append('signer', who);
        formData.append('meaction', 'SIGN_INSPECTION');

        if(fromFile) {
            var file = $('#insp_' + who + '_file')[0].files[0];
            if(!file) return;
            formData.append('file', file);
        } else {
            if(__Dispatch.__isSigPadEmpty(who)) {
                toastr.warning('Please sign in the box before saving.');
                return;
            }
            formData.append('signature_data', document.getElementById('insp_' + who + '_canvas').toDataURL('image/png'));
        }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                $('#insp_' + who + '_file').val('');
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Dispatch.__loadInspection(dispatch_id);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                $('#insp_' + who + '_file').val('');
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // PRE-TRIP INSPECTION — REMOVE FILE / SIGNATURE (to replace or re-sign)
    // ==============================
    this.__removeInspectionFile = function(field) {
        var dispatch_id = $('#dispatch_id').val();
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: { dispatch_id: dispatch_id, field: field, meaction: 'REMOVE_INSPECTION_FILE' },
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    __Dispatch.__loadInspection(dispatch_id);
                    setTimeout(function() {
                        __Dispatch.__initSigPad('driver');
                        __Dispatch.__initSigPad('inspector');
                    }, 300);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    };

    // Signature pads need the modal visible to measure their size
    // (Bootstrap 5 fires native events — a jQuery-delegated handler doesn't see them)
    document.addEventListener('shown.bs.modal', function(e) {
        if(e.target && e.target.id === 'dispatchModal') {
            __Dispatch.__initSigPad('driver');
            __Dispatch.__initSigPad('inspector');
        }
    });

    // ==============================
    // SAVE DISPATCH
    // ==============================
    this.__saveDispatch = function() {
        var trip_id = $('#dispatch_trip_id').val();
        var dispatch_date = $('#dispatch_date').val();
        var trip_status = $('#trip_status').val();

        if(!dispatch_date) {
            toastr.warning('Please select dispatch date', 'Missing field');
            $('#dispatch_date').focus();
            return;
        }

        var mparam = {
            trip_id: trip_id,
            dispatch_date: dispatch_date,
            dispatch_time: $('#dispatch_time').val(),
            truck: $('#dispatch_truck').val(),
            driver: $('#dispatch_driver').val(),
            helper: $('#dispatch_helper').val(),
            origin: $('#dispatch_origin').val(),
            destination: $('#dispatch_destination').val(),
            odometer_out: $('#odometer_out').val() || 0,
            fuel_level_out: $('#fuel_level_out').val() || 0,
            container_required: $('#container_required').is(':checked') ? 1 : 0,
            container_number: $('#container_number').val(),
            container_type: $('#container_type').val(),
            container_description: $('#container_description').val(),
            container_markings: $('#container_markings').val(),
            container_reference: $('#container_reference').val(),
            container_release_port: $('#container_release_port').val(),
            container_release_date: $('#container_release_date').val(),
            container_release_time: $('#container_release_time').val(),
            container_return_required: $('#container_return_required').is(':checked') ? 1 : 0,
            container_return_date: $('#container_return_date').val(),
            container_return_time: $('#container_return_time').val(),
            container_return_port: $('#container_return_port').val(),
            container_return_odometer: $('#container_return_odometer').val() || 0,
            container_return_status: $('#container_return_status').val(),
            container_return_proof: $('#container_return_proof').val(),
            dispatcher_name: $('#dispatcher_name').val(),
            trip_status: trip_status,
            actual_delivery_date: $('#actual_delivery_date').val(),
            actual_delivery_time: $('#actual_delivery_time').val(),
            odometer_in: $('#odometer_in').val() || 0,
            fuel_level_in: $('#fuel_level_in').val() || 0,
            total_distance: $('#total_distance').val() || 0,
            fuel_consumed: $('#fuel_consumed').val() || 0,
            delay_reason: $('#delay_reason').val(),
            remarks: $('#dispatch_remarks').val(),
            meaction: 'SAVE_DISPATCH'
        };

        var btn = $('#dispatchSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('dispatchModal'));
                    if(modal) modal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                    btn.html('Save Dispatch');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('Save Dispatch');
                toastr.error("Error: " + error);
                console.error('Error:', error);
            }
        });
    };

    // ==============================
    // UPDATE DISPATCH
    // ==============================
    this.__updateDispatch = function() {
        var dispatch_id = $('#dispatch_id').val();
        var trip_id = $('#dispatch_trip_id').val();
        var dispatch_date = $('#dispatch_date').val();
        var trip_status = $('#trip_status').val();

        if(!dispatch_id) {
            toastr.error('No dispatch ID found. Please save the dispatch first.');
            return;
        }

        if(!dispatch_date) {
            toastr.warning('Please select dispatch date', 'Missing field');
            $('#dispatch_date').focus();
            return;
        }

        var mparam = {
            dispatch_id: dispatch_id,
            trip_id: trip_id,
            dispatch_date: dispatch_date,
            dispatch_time: $('#dispatch_time').val(),
            truck: $('#dispatch_truck').val(),
            driver: $('#dispatch_driver').val(),
            helper: $('#dispatch_helper').val(),
            origin: $('#dispatch_origin').val(),
            destination: $('#dispatch_destination').val(),
            odometer_out: $('#odometer_out').val() || 0,
            fuel_level_out: $('#fuel_level_out').val() || 0,
            container_required: $('#container_required').is(':checked') ? 1 : 0,
            container_number: $('#container_number').val(),
            container_type: $('#container_type').val(),
            container_description: $('#container_description').val(),
            container_markings: $('#container_markings').val(),
            container_reference: $('#container_reference').val(),
            container_release_port: $('#container_release_port').val(),
            container_release_date: $('#container_release_date').val(),
            container_release_time: $('#container_release_time').val(),
            container_return_required: $('#container_return_required').is(':checked') ? 1 : 0,
            container_return_date: $('#container_return_date').val(),
            container_return_time: $('#container_return_time').val(),
            container_return_port: $('#container_return_port').val(),
            container_return_odometer: $('#container_return_odometer').val() || 0,
            container_return_status: $('#container_return_status').val(),
            container_return_proof: $('#container_return_proof').val(),
            dispatcher_name: $('#dispatcher_name').val(),
            trip_status: trip_status,
            actual_delivery_date: $('#actual_delivery_date').val(),
            actual_delivery_time: $('#actual_delivery_time').val(),
            odometer_in: $('#odometer_in').val() || 0,
            fuel_level_in: $('#fuel_level_in').val() || 0,
            total_distance: $('#total_distance').val() || 0,
            fuel_consumed: $('#fuel_consumed').val() || 0,
            delay_reason: $('#delay_reason').val(),
            remarks: $('#dispatch_remarks').val(),
            meaction: 'UPDATE_DISPATCH'
        };

        var btn = $('#dispatchSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('dispatchModal'));
                    if(modal) modal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                    btn.html('Update Dispatch');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('Update Dispatch');
                toastr.error("Error: " + error);
                console.error('Error:', error);
            }
        });
    };

    // ==============================
    // DELETE DISPATCH
    // ==============================
    this.__deleteDispatch = function(dispatch_id, trip_id) {
        if(!dispatch_id || dispatch_id == 0) {
            toastr.error('Invalid dispatch ID');
            return;
        }
        
        if(confirm('Are you sure you want to delete this dispatch?')) {
            var mparam = {
                dispatch_id: dispatch_id,
                trip_id: trip_id,
                meaction: 'DELETE_DISPATCH'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-dispatch',
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
                    console.error('Error:', error);
                }
            });
        }
    };

    // ==============================
    // CHECKLIST METHODS
    // ==============================
    this.__loadChecklist = function(dispatch_id) {
        if(!dispatch_id || dispatch_id == 0) {
            $('#checklistBody').html('<tr><td colspan="9" class="text-center text-muted">Save dispatch first</td></tr>');
            return;
        }
        
        var mparam = {
            dispatch_id: dispatch_id,
            meaction: 'GET_CHECKLIST'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data && data.length > 0) {
                    $.each(data, function(index, row) {
                        var statusBadge = getChecklistStatusBadge(row.checklist_status);
                        var conditionBadge = getConditionBadge(row.condition_before);
                        var checked = row.checked ? 'Yes' : 'No';
                        
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td><strong>' + row.item_name + '</strong></td>';
                        html += '<td>' + row.available_quantity + '</td>';
                        html += '<td>' + row.required_quantity + '</td>';
                        html += '<td>' + conditionBadge + '</td>';
                        html += '<td>' + checked + '</td>';
                        html += '<td>' + (row.accountable_person || '—') + '</td>';
                        html += '<td>' + statusBadge + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__Dispatch.__editChecklistItem(' + row.checklist_id + ')" title="Edit">';
                        html += '<i class="bi bi-pencil"></i>';
                        html += '</button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__Dispatch.__deleteChecklistItem(' + row.checklist_id + ')" title="Delete">';
                        html += '<i class="bi bi-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="9" class="text-center text-muted">No checklist items</td></tr>';
                }
                $('#checklistBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading checklist: " + error);
                console.error('Error loading checklist:', error);
            }
        });
    };

    this.__saveChecklistItem = function() {
        var dispatch_id = $('#dispatch_id').val();
        var trip_id = $('#dispatch_trip_id').val();
        var item_name = $('#cl_item_name').val().trim();
        
        if(!dispatch_id || dispatch_id == 0) {
            toastr.warning('Please save the dispatch first before adding checklist items');
            return;
        }
        
        if(!item_name) {
            toastr.warning('Please enter item name', 'Missing field');
            $('#cl_item_name').focus();
            return;
        }

        var mparam = {
            dispatch_id: dispatch_id,
            trip_id: trip_id,
            item_name: item_name,
            available_quantity: $('#cl_available_qty').val() || 0,
            required_quantity: $('#cl_required_qty').val() || 0,
            condition_before: $('#cl_condition').val(),
            checked: $('#cl_checked').is(':checked') ? 1 : 0,
            accountable_person: $('#cl_accountable').val(),
            checklist_status: $('#cl_status').val(),
            remarks: $('#cl_remarks').val(),
            meaction: 'SAVE_CHECKLIST'
        };

        var btn = $('#clActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    $('#cl_item_name').val('');
                    $('#cl_available_qty').val('1');
                    $('#cl_required_qty').val('1');
                    $('#cl_condition').val('GOOD');
                    $('#cl_checked').prop('checked', true);
                    $('#cl_accountable').val('');
                    $('#cl_status').val('COMPLETE');
                    $('#cl_remarks').val('');
                    $('#cl_editing_id').val('');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Dispatch.__saveChecklistItem()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    __Dispatch.__loadChecklist(dispatch_id);
                } else {
                    toastr.error(data.message);
                    btn.html('<i class="bi bi-plus"></i> Add');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-plus"></i> Add');
                toastr.error("Error: " + error);
                console.error('Error saving checklist:', error);
            }
        });
    };

    this.__editChecklistItem = function(checklist_id) {
        var mparam = {
            checklist_id: checklist_id,
            meaction: 'GET_CHECKLIST_ITEM'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#cl_editing_id').val(data.checklist_id);
                    $('#cl_item_name').val(data.item_name);
                    $('#cl_available_qty').val(data.available_quantity);
                    $('#cl_required_qty').val(data.required_quantity);
                    $('#cl_condition').val(data.condition_before);
                    $('#cl_checked').prop('checked', data.checked == 1);
                    $('#cl_accountable').val(data.accountable_person);
                    $('#cl_status').val(data.checklist_status);
                    $('#cl_remarks').val(data.remarks);
                    
                    var btn = $('#clActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__Dispatch.__updateChecklistItem()');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading checklist item: " + error);
                console.error('Error loading checklist item:', error);
            }
        });
    };

    this.__updateChecklistItem = function() {
        var checklist_id = $('#cl_editing_id').val();
        var dispatch_id = $('#dispatch_id').val();
        var item_name = $('#cl_item_name').val().trim();
        
        if(!item_name) {
            toastr.warning('Please enter item name', 'Missing field');
            $('#cl_item_name').focus();
            return;
        }

        var mparam = {
            checklist_id: checklist_id,
            item_name: item_name,
            available_quantity: $('#cl_available_qty').val() || 0,
            required_quantity: $('#cl_required_qty').val() || 0,
            condition_before: $('#cl_condition').val(),
            checked: $('#cl_checked').is(':checked') ? 1 : 0,
            accountable_person: $('#cl_accountable').val(),
            checklist_status: $('#cl_status').val(),
            remarks: $('#cl_remarks').val(),
            meaction: 'UPDATE_CHECKLIST'
        };

        var btn = $('#clActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    $('#cl_item_name').val('');
                    $('#cl_available_qty').val('1');
                    $('#cl_required_qty').val('1');
                    $('#cl_condition').val('GOOD');
                    $('#cl_checked').prop('checked', true);
                    $('#cl_accountable').val('');
                    $('#cl_status').val('COMPLETE');
                    $('#cl_remarks').val('');
                    $('#cl_editing_id').val('');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Dispatch.__saveChecklistItem()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    __Dispatch.__loadChecklist(dispatch_id);
                } else {
                    toastr.error(data.message);
                    btn.html('Update');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('Update');
                toastr.error("Error: " + error);
                console.error('Error updating checklist:', error);
            }
        });
    };

    this.__deleteChecklistItem = function(checklist_id) {
        if(confirm('Are you sure you want to delete this checklist item?')) {
            var dispatch_id = $('#dispatch_id').val();
            
            var mparam = {
                checklist_id: checklist_id,
                meaction: 'DELETE_CHECKLIST'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-dispatch',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Dispatch.__loadChecklist(dispatch_id);
                        $('#cl_editing_id').val('');
                        var btn = $('#clActionBtn');
                        btn.html('<i class="bi bi-plus"></i> Add');
                        btn.attr('onclick', '__Dispatch.__saveChecklistItem()');
                        btn.removeClass('btn-warning').addClass('btn-primary');
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Error: " + error);
                    console.error('Error deleting checklist:', error);
                }
            });
        }
    };

    // ==============================
    // EXPENSES METHODS
    // ==============================
    this.__loadExpenses = function(dispatch_id) {
        if(!dispatch_id || dispatch_id == 0) {
            $('#expensesBody').html('<tr><td colspan="8" class="text-center text-muted">Save dispatch first</td></tr>');
            $('#total_expenses').text('₱0.00');
            return;
        }
        
        var mparam = {
            dispatch_id: dispatch_id,
            meaction: 'GET_EXPENSES'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                var total = 0;
                if(data && data.length > 0) {
                    $.each(data, function(index, row) {
                        total += parseFloat(row.amount);
                        var expenseTypeLabels = {
                            'TOLL': 'Toll',
                            'PARKING': 'Parking',
                            'FUEL': 'Fuel',
                            'MEALS': 'Meals',
                            'LOADING_UNLOADING': 'Loading/Unloading',
                            'CONTAINER_RENTAL': 'Container Rental',
                            'EMPTY_RETURN_FEE': 'Empty Return Fee',
                            'CONTAINER_RETURN_FEE': 'Container Return Fee',
                            'OTHER': 'Other'
                        };
                        
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td>' + (row.expense_date || '—') + '</td>';
                        html += '<td><strong>' + (expenseTypeLabels[row.expense_type] || row.expense_type) + '</strong></td>';
                        html += '<td>' + (row.description || '—') + '</td>';
                        html += '<td class="text-end">₱' + parseFloat(row.amount).toFixed(2) + '</td>';
                        html += '<td>' + (row.paid_by || '—') + '</td>';
                        html += '<td>' + (row.reference_no || '—') + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__Dispatch.__editExpenseItem(' + row.expense_id + ')" title="Edit">';
                        html += '<i class="bi bi-pencil"></i>';
                        html += '</button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__Dispatch.__deleteExpenseItem(' + row.expense_id + ')" title="Delete">';
                        html += '<i class="bi bi-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    html += '<tr class="table-active">';
                    html += '<td colspan="4" class="text-end"><strong>Total:</strong></td>';
                    html += '<td class="text-end"><strong>₱' + total.toFixed(2) + '</strong></td>';
                    html += '<td colspan="3"></td>';
                    html += '</tr>';
                } else {
                    html = '<tr><td colspan="8" class="text-center text-muted">No expenses recorded</td></tr>';
                }
                $('#expensesBody').html(html);
                $('#total_expenses').text('₱' + total.toFixed(2));
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading expenses: " + error);
                console.error('Error loading expenses:', error);
            }
        });
    };

    this.__saveExpenseItem = function() {
        var dispatch_id = $('#dispatch_id').val();
        var trip_id = $('#dispatch_trip_id').val();
        var expense_type = $('#exp_type').val();
        var amount = $('#exp_amount').val();
        
        if(!dispatch_id || dispatch_id == 0) {
            toastr.warning('Please save the dispatch first before adding expenses');
            return;
        }
        
        if(!expense_type) {
            toastr.warning('Please select expense type', 'Missing field');
            $('#exp_type').focus();
            return;
        }
        
        if(!amount || amount <= 0) {
            toastr.warning('Please enter valid amount', 'Missing field');
            $('#exp_amount').focus();
            return;
        }

        var mparam = {
            dispatch_id: dispatch_id,
            trip_id: trip_id,
            expense_date: $('#exp_date').val() || new Date().toISOString().split('T')[0],
            expense_type: expense_type,
            description: $('#exp_description').val(),
            amount: amount,
            paid_by: $('#exp_paid_by').val(),
            reference_no: $('#exp_reference').val(),
            remarks: $('#exp_remarks').val(),
            meaction: 'SAVE_EXPENSE'
        };

        var btn = $('#expActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    $('#exp_type').val('');
                    $('#exp_description').val('');
                    $('#exp_amount').val('');
                    $('#exp_paid_by').val('');
                    $('#exp_reference').val('');
                    $('#exp_remarks').val('');
                    $('#exp_editing_id').val('');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Dispatch.__saveExpenseItem()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    __Dispatch.__loadExpenses(dispatch_id);
                } else {
                    toastr.error(data.message);
                    btn.html('<i class="bi bi-plus"></i> Add');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-plus"></i> Add');
                toastr.error("Error: " + error);
                console.error('Error saving expense:', error);
            }
        });
    };

    this.__editExpenseItem = function(expense_id) {
        var mparam = {
            expense_id: expense_id,
            meaction: 'GET_EXPENSE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#exp_editing_id').val(data.expense_id);
                    $('#exp_date').val(data.expense_date);
                    $('#exp_type').val(data.expense_type);
                    $('#exp_description').val(data.description);
                    $('#exp_amount').val(data.amount);
                    $('#exp_paid_by').val(data.paid_by);
                    $('#exp_reference').val(data.reference_no);
                    $('#exp_remarks').val(data.remarks);
                    
                    var btn = $('#expActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__Dispatch.__updateExpenseItem()');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading expense: " + error);
                console.error('Error loading expense:', error);
            }
        });
    };

    this.__updateExpenseItem = function() {
        var expense_id = $('#exp_editing_id').val();
        var dispatch_id = $('#dispatch_id').val();
        var expense_type = $('#exp_type').val();
        var amount = $('#exp_amount').val();
        
        if(!expense_type) {
            toastr.warning('Please select expense type', 'Missing field');
            $('#exp_type').focus();
            return;
        }
        
        if(!amount || amount <= 0) {
            toastr.warning('Please enter valid amount', 'Missing field');
            $('#exp_amount').focus();
            return;
        }

        var mparam = {
            expense_id: expense_id,
            expense_date: $('#exp_date').val(),
            expense_type: expense_type,
            description: $('#exp_description').val(),
            amount: amount,
            paid_by: $('#exp_paid_by').val(),
            reference_no: $('#exp_reference').val(),
            remarks: $('#exp_remarks').val(),
            meaction: 'UPDATE_EXPENSE'
        };

        var btn = $('#expActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    $('#exp_type').val('');
                    $('#exp_description').val('');
                    $('#exp_amount').val('');
                    $('#exp_paid_by').val('');
                    $('#exp_reference').val('');
                    $('#exp_remarks').val('');
                    $('#exp_editing_id').val('');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Dispatch.__saveExpenseItem()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    __Dispatch.__loadExpenses(dispatch_id);
                } else {
                    toastr.error(data.message);
                    btn.html('Update');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('Update');
                toastr.error("Error: " + error);
                console.error('Error updating expense:', error);
            }
        });
    };

    this.__deleteExpenseItem = function(expense_id) {
        if(confirm('Are you sure you want to delete this expense?')) {
            var dispatch_id = $('#dispatch_id').val();
            
            var mparam = {
                expense_id: expense_id,
                meaction: 'DELETE_EXPENSE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-dispatch',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Dispatch.__loadExpenses(dispatch_id);
                        $('#exp_editing_id').val('');
                        var btn = $('#expActionBtn');
                        btn.html('<i class="bi bi-plus"></i> Add');
                        btn.attr('onclick', '__Dispatch.__saveExpenseItem()');
                        btn.removeClass('btn-warning').addClass('btn-primary');
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Error: " + error);
                    console.error('Error deleting expense:', error);
                }
            });
        }
    };

    // ==============================
    // WAYPOINT TRACKING
    // ==============================
    this.__openWaypointTracking = function(trip_id, trip_code) {
        $('#tracking_trip_id').val(trip_id);
        $('#tracking_trip_code').text(trip_code);
        
        __Dispatch.__loadWaypointsTracking(trip_id);
        
        var modal = new bootstrap.Modal(document.getElementById('waypointTrackingModal'));
        modal.show();
    };

    this.__loadWaypointsTracking = function(trip_id) {
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_WAYPOINTS_TRACKING'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                console.log('Waypoints tracking data:', data);
                var html = '';
                if(data && data.length > 0) {
                    html += '<div class="journey-timeline">';
                    $.each(data, function(index, row) {
                        var statusBadge = getWaypointStatusBadge(row.waypoint_status);
                        var expectedArrival = row.expected_arrival ? formatDateTime(row.expected_arrival) : '—';
                        var expectedDeparture = row.expected_departure ? formatDateTime(row.expected_departure) : '—';
                        var actualArrival = row.actual_arrival ? formatDateTime(row.actual_arrival) : '—';
                        var actualDeparture = row.actual_departure ? formatDateTime(row.actual_departure) : '—';

                        var typeLabels = {
                            'GARAGE': 'Garage',
                            'EMPTY_CONTAINER_PICKUP': 'Empty Container Pickup',
                            'CLIENT_WAREHOUSE': 'Client Warehouse',
                            'DELIVERY_DESTINATION': 'Delivery Destination',
                            'PORT_TERMINAL': 'Port / Terminal',
                            'RETURN_POINT': 'Return Point',
                            'PICKUP_LOCATION': 'Pickup Location',
                            'OTHER': 'Other'
                        };
                        var typeDisplay = typeLabels[row.waypoint_type] || row.waypoint_type || '—';

                        // Escape single quotes in waypoint name to prevent breaking inline JS
                        var safeName = (row.waypoint_name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');

                        var itemCls = '';
                        var arrivalBtn = '';
                        var departureBtn = '';

                        if(row.waypoint_status == 'PENDING' || row.waypoint_status == '') {
                            arrivalBtn = `<button class="btn btn-sm btn-success" onclick="__Dispatch.__openTrackingUpdate(${row.waypoint_id}, 'arrival', '${safeName}')" title="Record Arrival">
                                <i class="bi bi-arrow-down-circle"></i> Arrive
                            </button>`;
                        } else if(row.waypoint_status == 'ARRIVED') {
                            itemCls = 'wp-arrived';
                            departureBtn = `<button class="btn btn-sm btn-warning" onclick="__Dispatch.__openTrackingUpdate(${row.waypoint_id}, 'departure', '${safeName}')" title="Record Departure">
                                <i class="bi bi-arrow-up-circle"></i> Depart
                            </button>`;
                        } else if(row.waypoint_status == 'DEPARTED') {
                            itemCls = 'wp-departed';
                        } else if(row.waypoint_status == 'COMPLETED') {
                            itemCls = 'wp-completed';
                        }

                        html += '<div class="journey-item ' + itemCls + '">';
                        html += '<div class="wp-dot">' + row.sequence + '</div>';
                        html += '<div class="j-header">';
                        html += '<span class="j-type">' + typeDisplay + '</span>';
                        html += statusBadge;
                        html += '</div>';
                        html += '<div class="j-description">' + (row.waypoint_name || '—') + '</div>';
                        html += '<div class="j-details">';
                        html += 'Expected Arrival: ' + expectedArrival + ' &nbsp;|&nbsp; Expected Departure: ' + expectedDeparture + '<br>';
                        html += 'Actual Arrival: ' + actualArrival + ' &nbsp;|&nbsp; Actual Departure: ' + actualDeparture;
                        html += '</div>';
                        if(arrivalBtn || departureBtn) {
                            html += '<div class="wp-actions">' + arrivalBtn + departureBtn + '</div>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-4 text-muted">' +
                           '<i class="bi bi-inbox" style="font-size:48px;opacity:0.3;"></i>' +
                           '<h5 class="mt-3">No waypoints found</h5>' +
                           '<p>This trip has no route waypoints defined.</p></div>';
                }
                $('#trackingWaypointsBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading waypoints: " + error);
                console.error('Error loading waypoints:', error);
                $('#trackingWaypointsBody').html(
                    '<div class="text-center py-4 text-danger">' +
                    '<i class="bi bi-exclamation-triangle" style="font-size:48px;opacity:0.5;"></i>' +
                    '<h5 class="mt-3">Failed to load waypoints</h5></div>'
                );
            }
        });
    };

    this.__openTrackingUpdate = function(waypoint_id, type, waypoint_name) {
        $('#tracking_update_waypoint_id').val(waypoint_id);
        $('#tracking_update_type').val(type);
        $('#tracking_update_waypoint_name').text(waypoint_name);
        $('#tracking_update_datetime').val('');
        $('#tracking_update_remarks').val('');
        
        var title = type == 'arrival' ? 'Record Arrival' : 'Record Departure';
        var icon = type == 'arrival' ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle';
        $('#trackingUpdateTitle').html('<i class="bi ' + icon + ' me-2"></i>' + title);
        $('#trackingUpdateBtn').text('Save ' + title);
        
        var now = new Date();
        var formatted = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + 'T' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0');
        $('#tracking_update_datetime').val(formatted);
        
        var modal = new bootstrap.Modal(document.getElementById('trackingUpdateModal'));
        modal.show();
    };

    this.__saveWaypointTracking = function() {
        var waypoint_id = $('#tracking_update_waypoint_id').val();
        var type = $('#tracking_update_type').val();
        var datetime = $('#tracking_update_datetime').val();
        var remarks = $('#tracking_update_remarks').val();
        
        if(!datetime) {
            toastr.warning('Please select date/time', 'Missing field');
            $('#tracking_update_datetime').focus();
            return;
        }
        
        var meaction = type == 'arrival' ? 'UPDATE_WAYPOINT_ARRIVAL' : 'UPDATE_WAYPOINT_DEPARTURE';
        
        var mparam = {
            waypoint_id: waypoint_id,
            actual_arrival: type == 'arrival' ? datetime : '',
            actual_departure: type == 'departure' ? datetime : '',
            arrival_remarks: type == 'arrival' ? remarks : '',
            departure_remarks: type == 'departure' ? remarks : '',
            meaction: meaction
        };
        
        var btn = $('#trackingUpdateBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');
        
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-dispatch',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('Save');
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('trackingUpdateModal'));
                    modal.hide();

                    if (type === 'arrival') {
                        // Recording an arrival can cascade to dispatch/trip status, container
                        // return, and (on the last waypoint) freeing the truck/driver/helper —
                        // none of which live-update elsewhere on the page (stat cards, the main
                        // table row), so reload to reflect it everywhere instead of just here.
                        setTimeout(function() { location.reload(); }, 1200);
                    } else {
                        var trip_id = $('#tracking_trip_id').val();
                        __Dispatch.__loadWaypointsTracking(trip_id);
                    }
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('Save');
                toastr.error("Error: " + error);
                console.error('Error saving tracking:', error);
            }
        });
    };
}

// ==============================
// HELPER FUNCTIONS
// ==============================
function getChecklistStatusBadge(status) {
    var map = {
        'COMPLETE': '<span class="badge badge-success">Complete</span>',
        'INCOMPLETE': '<span class="badge badge-warning">Incomplete</span>',
        'MISSING': '<span class="badge badge-danger">Missing</span>',
        'DAMAGED': '<span class="badge badge-danger">Damaged</span>',
        'NOT_APPLICABLE': '<span class="badge badge-secondary">N/A</span>'
    };
    return map[status] || '<span class="badge badge-secondary">' + status + '</span>';
}

function getConditionBadge(condition) {
    var map = {
        'GOOD': '<span class="badge badge-success">Good</span>',
        'FAIR': '<span class="badge badge-info">Fair</span>',
        'POOR': '<span class="badge badge-warning">Poor</span>',
        'DAMAGED': '<span class="badge badge-danger">Damaged</span>',
        'MISSING': '<span class="badge badge-danger">Missing</span>'
    };
    return map[condition] || '<span class="badge badge-secondary">' + condition + '</span>';
}

function getWaypointStatusBadge(status) {
    var map = {
        'PENDING': '<span class="badge badge-secondary">Pending</span>',
        'ARRIVED': '<span class="badge badge-success">Arrived</span>',
        'DEPARTED': '<span class="badge badge-warning">Departed</span>',
        'COMPLETED': '<span class="badge badge-primary">Completed</span>'
    };
    return map[status] || '<span class="badge badge-secondary">Pending</span>';
}

function formatDateTime(datetime) {
    if(!datetime) return '—';
    var date = new Date(datetime);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

$(document).ready(function() {
    console.log('Dispatch ready');
    
        // Clear iframe when PDF modal closes
    $('#pdfModal').on('hidden.bs.modal', function () {
        document.getElementById('pdfFrame').src = '';
    });
    // Container toggle
    $('#container_required').on('change', function() {
        __Dispatch.__toggleContainerFields();
    });
    
    $('#container_return_required').on('change', function() {
        __Dispatch.__toggleReturnFields();
    });
    
    // Auto-calculate total distance and fuel consumed
    $('#odometer_out, #odometer_in').on('change keyup', function() {
        var out = parseFloat($('#odometer_out').val()) || 0;
        var inn = parseFloat($('#odometer_in').val()) || 0;
        if(inn > out) {
            $('#total_distance').val((inn - out).toFixed(2));
        }
    });

    $('#fuel_level_out, #fuel_level_in').on('change keyup', function() {
        var out = parseFloat($('#fuel_level_out').val()) || 0;
        var inn = parseFloat($('#fuel_level_in').val()) || 0;
        if(inn < out) {
            $('#fuel_consumed').val((out - inn).toFixed(2));
        }
    });
});