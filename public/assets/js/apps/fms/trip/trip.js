var __Trips = new __Trips();

function __Trips() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Trips initialized, URL: ' + mesiteurl);

    // ==============================
    // TOGGLE VEHICLE FIELDS
    // ==============================
    this.__toggleVehicleFields = function() {
        var type = $('#assignment_vehicle_type').val();
        
        $('#assignment_truck_field').hide();
        $('#assignment_tractor_field').hide();
        $('#assignment_chassis_field').hide();
        $('#assignment_chassis_type_field').hide();
        $('#assignment_vendor_field').hide();
        $('#assignment_rental_fields').hide();
        $('#assignment_rented_section').hide();
        
        $('#assignment_driver_field').show();
        $('#assignment_helper_field').show();
        
        if(type == 'RIGID') {
            $('#assignment_truck_field').show();
        } else if(type == 'TRACTOR_CHASSIS') {
            $('#assignment_tractor_field').show();
            $('#assignment_chassis_field').show();
            $('#assignment_chassis_type_field').show();
        } else if(type == 'TRACTOR_RENTED_CHASSIS') {
            $('#assignment_tractor_field').show();
            $('#assignment_chassis_field').show();
            $('#assignment_chassis_type_field').show();
            $('#assignment_chassis_type').val('RENTED');
            $('#assignment_vendor_field').show();
            $('#assignment_rental_fields').show();
        } else if(type == 'RENTED_ALL') {
            $('#assignment_vendor_field').show();
            $('#assignment_rental_fields').show();
            $('#assignment_rented_section').show();
            $('#assignment_truck_plate').prop('required', false);
            $('#assignment_tractor_plate').prop('required', false);
            $('#assignment_chassis_plate').prop('required', false);
        }
    };

    // ==============================
    // TOGGLE RENTAL FIELDS
    // ==============================
    this.__toggleRentalFields = function() {
        var chassisType = $('#assignment_chassis_type').val();
        var vehicleType = $('#assignment_vehicle_type').val();
        
        if(vehicleType == 'TRACTOR_RENTED_CHASSIS' || chassisType == 'RENTED') {
            $('#assignment_vendor_field').show();
            $('#assignment_rental_fields').show();
        } else {
            $('#assignment_vendor_field').hide();
            $('#assignment_rental_fields').hide();
        }
    };

    // ==============================
    // RESET ASSIGNMENT FORM
    // ==============================
    this.__resetAssignmentForm = function() {
        $('#assignment_vehicle_type').val('');
        $('#assignment_truck_plate').val('');
        $('#assignment_tractor_plate').val('');
        $('#assignment_chassis_plate').val('');
        $('#assignment_chassis_type').val('OWNED');
        $('#assignment_vendor_name').val('');
        $('#assignment_rental_rate').val('');
        $('#assignment_rental_start_date').val('');
        $('#assignment_rental_end_date').val('');
        $('#assignment_rental_agreement_no').val('');
        $('#assignment_vendor_contact_person').val('');
        $('#assignment_vendor_contact_number').val('');
        $('#assignment_driver_name').val('');
        $('#assignment_helper_name').val('');
        var today = new Date().toISOString().split('T')[0];
        $('#assignment_assignment_date').val(today);
        $('#assignment_dispatch_time').val('');
        $('#assignment_dispatch_location').val('');
        $('#assignment_odometer_before_trip').val('');
        $('#assignment_fuel_level').val('');
        $('#assignment_remarks').val('');
        $('#assignment_status').val('ASSIGNED');
        
        $('#assignment_truck_field').hide();
        $('#assignment_tractor_field').hide();
        $('#assignment_chassis_field').hide();
        $('#assignment_chassis_type_field').hide();
        $('#assignment_vendor_field').hide();
        $('#assignment_rental_fields').hide();
        $('#assignment_rented_section').hide();
        $('#assignment_driver_field').show();
        $('#assignment_helper_field').show();
        
        toastr.info('Form has been reset');
    };

    // ==============================
    // SAVE TRIP
    // ==============================
    this.__saveTrip = function() {
        var customer_id = $('#form_customer_id').val();
        var scheduled_date = $('#form_scheduled_date').val();

        if(!customer_id) {
            toastr.warning('Please select a customer', 'Missing field');
            $('#form_customer_id').focus();
            return false;
        }

        if(!scheduled_date) {
            toastr.warning('Please select scheduled date', 'Missing field');
            $('#form_scheduled_date').focus();
            return false;
        }

        var mparam = {
            customer_id: customer_id,
            booking_reference: $('#form_booking_reference').val(),
            service_type: $('#form_service_type').val(),
            trip_type: $('#form_trip_type').val(),
            priority: $('#form_priority').val(),
            scheduled_date: scheduled_date,
            pickup_date: $('#form_pickup_date').val(),
            expected_delivery_date: $('#form_expected_delivery_date').val(),
            origin: $('#form_origin').val(),
            destination: $('#form_destination').val(),
            cargo_description: $('#form_cargo_description').val(),
            quantity: $('#form_quantity').val(),
            unit: $('#form_unit').val(),
            estimated_weight: $('#form_estimated_weight').val(),
            special_instructions: $('#form_special_instructions').val(),
            trip_status: $('#form_trip_status').val(),
            remarks: $('#form_remarks').val(),
            meaction: 'SAVE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('tripModal'));
                    if(modal) modal.hide();
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
    };

    // ==============================
    // UPDATE TRIP
    // ==============================
    this.__updateTrip = function() {
        var trip_id = $('#form_trip_id').val();
        var customer_id = $('#form_customer_id').val();
        var scheduled_date = $('#form_scheduled_date').val();

        if(!customer_id) {
            toastr.warning('Please select a customer', 'Missing field');
            $('#form_customer_id').focus();
            return false;
        }

        if(!scheduled_date) {
            toastr.warning('Please select scheduled date', 'Missing field');
            $('#form_scheduled_date').focus();
            return false;
        }

        var mparam = {
            trip_id: trip_id,
            customer_id: customer_id,
            booking_reference: $('#form_booking_reference').val(),
            service_type: $('#form_service_type').val(),
            trip_type: $('#form_trip_type').val(),
            priority: $('#form_priority').val(),
            scheduled_date: scheduled_date,
            pickup_date: $('#form_pickup_date').val(),
            expected_delivery_date: $('#form_expected_delivery_date').val(),
            origin: $('#form_origin').val(),
            destination: $('#form_destination').val(),
            cargo_description: $('#form_cargo_description').val(),
            quantity: $('#form_quantity').val(),
            unit: $('#form_unit').val(),
            estimated_weight: $('#form_estimated_weight').val(),
            special_instructions: $('#form_special_instructions').val(),
            trip_status: $('#form_trip_status').val(),
            remarks: $('#form_remarks').val(),
            meaction: 'EDIT'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('tripModal'));
                    if(modal) modal.hide();
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
    };

    // ==============================
    // DELETE TRIP
    // ==============================
    this.__deleteTrip = function() {
        if(deleteId) {
            var mparam = {
                trip_id: deleteId,
                meaction: 'DELETE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-trips',
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
    // VIEW TRIP
    // ==============================
    this.__viewTrip = function(trip_id) {
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_TRIP'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    var statusBadge = getStatusBadge(data.trip_status);
                    var priorityBadge = getPriorityBadge(data.priority);
                    var hasAssignment = data.has_assignment ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';

                    var html = `
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Trip Number</small>
                                <div><strong>${data.trip_code}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Customer</small>
                                <div><strong>${data.customer_name}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Booking Reference</small>
                                <div><strong>${data.booking_reference || '—'}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Service Type</small>
                                <div><strong>${data.service_type || '—'}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Trip Type</small>
                                <div><strong>${data.trip_type || '—'}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Priority</small>
                                <div>${priorityBadge}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Scheduled Date</small>
                                <div><strong>${data.scheduled_date}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Status</small>
                                <div>${statusBadge}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Has Assignment</small>
                                <div>${hasAssignment}</div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <small class="text-muted">Origin</small>
                                <div><strong>${data.origin || '—'}</strong></div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <small class="text-muted">Destination</small>
                                <div><strong>${data.destination || '—'}</strong></div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <small class="text-muted">Cargo Description</small>
                                <div><strong>${data.cargo_description || '—'}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Quantity</small>
                                <div><strong>${data.quantity || 0} ${data.unit || ''}</strong></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Estimated Weight</small>
                                <div><strong>${data.estimated_weight || 0} kg</strong></div>
                            </div>
                            ${data.special_instructions ? `
                            <div class="col-md-12 mt-2">
                                <hr>
                                <small class="text-muted">Special Instructions</small>
                                <div><strong>${data.special_instructions}</strong></div>
                            </div>` : ''}
                            ${data.remarks ? `
                            <div class="col-md-12 mt-2">
                                <hr>
                                <small class="text-muted">Remarks</small>
                                <div><strong>${data.remarks}</strong></div>
                            </div>` : ''}
                        </div>
                    `;
                    $('#viewTripContent').html(html);
                    var modal = new bootstrap.Modal(document.getElementById('viewModal'));
                    modal.show();
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading trip: " + error);
            }
        });
    };

    // ==============================
    // OPEN ASSIGNMENT MODAL
    // ==============================
    this.__openAssignmentModal = function(trip_id) {
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_TRIP'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#assignment_trip_id').val(trip_id);
                    $('#assignment_trip_code').text(data.trip_code);
                    
                    $('#assignment_id').val('');
                    $('#assignment_vehicle_type').val('');
                    $('#assignment_truck_plate').val('');
                    $('#assignment_tractor_plate').val('');
                    $('#assignment_chassis_plate').val('');
                    $('#assignment_chassis_type').val('OWNED');
                    $('#assignment_vendor_name').val('');
                    $('#assignment_rental_rate').val('');
                    $('#assignment_rental_start_date').val('');
                    $('#assignment_rental_end_date').val('');
                    $('#assignment_rental_agreement_no').val('');
                    $('#assignment_vendor_contact_person').val('');
                    $('#assignment_vendor_contact_number').val('');
                    $('#assignment_driver_name').val('');
                    $('#assignment_helper_name').val('');
                    var today = new Date().toISOString().split('T')[0];
                    $('#assignment_assignment_date').val(today);
                    $('#assignment_dispatch_time').val('');
                    $('#assignment_dispatch_location').val('');
                    $('#assignment_odometer_before_trip').val('');
                    $('#assignment_fuel_level').val('');
                    $('#assignment_remarks').val('');
                    $('#assignment_status').val('ASSIGNED');
                    
                    $('#assignment_driver_field').show();
                    $('#assignment_helper_field').show();
                    $('#assignment_truck_field').hide();
                    $('#assignment_tractor_field').hide();
                    $('#assignment_chassis_field').hide();
                    $('#assignment_chassis_type_field').hide();
                    $('#assignment_vendor_field').hide();
                    $('#assignment_rental_fields').hide();
                    $('#assignment_rented_section').hide();
                    
                    __Trips.__loadResources();
                    __Trips.__loadAssignment(trip_id);
                    
                    var modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
                    modal.show();
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading trip: " + error);
            }
        });
    };

    // ==============================
    // LOAD ASSIGNMENT
    // ==============================
    this.__loadAssignment = function(trip_id) {
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_ASSIGNMENT'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                console.log('Assignment data:', data);
                
                if(data && data.assignment_id) {
                    $('#assignment_id').val(data.assignment_id);
                    $('#assignment_vehicle_type').val(data.vehicle_type);
                    $('#assignment_truck_plate').val(data.truck_plate || '');
                    $('#assignment_tractor_plate').val(data.tractor_plate || '');
                    $('#assignment_chassis_plate').val(data.chassis_plate || '');
                    $('#assignment_chassis_type').val(data.chassis_type || 'OWNED');
                    $('#assignment_vendor_name').val(data.vendor_name || '');
                    $('#assignment_rental_rate').val(data.rental_rate || 0);
                    $('#assignment_rental_start_date').val(data.rental_start_date || '');
                    $('#assignment_rental_end_date').val(data.rental_end_date || '');
                    $('#assignment_rental_agreement_no').val(data.rental_agreement_no || '');
                    $('#assignment_vendor_contact_person').val(data.vendor_contact_person || '');
                    $('#assignment_vendor_contact_number').val(data.vendor_contact_number || '');
                    $('#assignment_driver_name').val(data.driver_name || '');
                    $('#assignment_helper_name').val(data.helper_name || '');
                    $('#assignment_assignment_date').val(data.assignment_date || '');
                    $('#assignment_dispatch_time').val(data.dispatch_time || '');
                    $('#assignment_dispatch_location').val(data.dispatch_location || '');
                    $('#assignment_odometer_before_trip').val(data.odometer_before_trip || 0);
                    $('#assignment_fuel_level').val(data.fuel_level || 0);
                    $('#assignment_status').val(data.assignment_status || 'ASSIGNED');
                    $('#assignment_remarks').val(data.remarks || '');
                    
                    __Trips.__toggleVehicleFields();
                    __Trips.__toggleRentalFields();
                    
                    $('#assignmentActionBtn').text('Update Assignment');
                    $('#assignmentActionBtn').attr('onclick', '__Trips.__updateAssignment()');
                    $('#assignment_status_badge').html('<span class="badge badge-success">Assigned</span>');
                    $('#assignment_remove_btn').show();
                } else {
                    $('#assignmentActionBtn').text('Save Assignment');
                    $('#assignmentActionBtn').attr('onclick', '__Trips.__saveAssignment()');
                    $('#assignment_status_badge').html('<span class="badge badge-secondary">No Assignment</span>');
                    $('#assignment_remove_btn').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading assignment:', error);
                toastr.error("Error loading assignment: " + error);
            }
        });
    };

    // ==============================
    // SAVE ASSIGNMENT
    // ==============================
    this.__saveAssignment = function() {
        var trip_id = $('#assignment_trip_id').val();
        var vehicle_type = $('#assignment_vehicle_type').val();
        var driver_name = $('#assignment_driver_name').val();
        var assignment_date = $('#assignment_assignment_date').val();
        
        if(!vehicle_type) {
            toastr.warning('Please select vehicle type', 'Missing field');
            $('#assignment_vehicle_type').focus();
            return;
        }
        
        if(vehicle_type == 'RIGID') {
            if(!$('#assignment_truck_plate').val()) {
                toastr.warning('Please select a truck', 'Missing field');
                $('#assignment_truck_plate').focus();
                return;
            }
        } else if(vehicle_type == 'TRACTOR_CHASSIS') {
            if(!$('#assignment_tractor_plate').val() || !$('#assignment_chassis_plate').val()) {
                toastr.warning('Please select both tractor and chassis', 'Missing field');
                return;
            }
        } else if(vehicle_type == 'TRACTOR_RENTED_CHASSIS') {
            if(!$('#assignment_tractor_plate').val() || !$('#assignment_chassis_plate').val()) {
                toastr.warning('Please select both tractor and chassis', 'Missing field');
                return;
            }
            if(!$('#assignment_vendor_name').val()) {
                toastr.warning('Please select a vendor', 'Missing field');
                $('#assignment_vendor_name').focus();
                return;
            }
        } else if(vehicle_type == 'RENTED_ALL') {
            if(!$('#assignment_vendor_name').val()) {
                toastr.warning('Please select a vendor', 'Missing field');
                $('#assignment_vendor_name').focus();
                return;
            }
            if(!$('#assignment_truck_plate').val() && !$('#assignment_tractor_plate').val() && !$('#assignment_chassis_plate').val()) {
                toastr.warning('Please provide at least one vehicle (Truck, Tractor, or Chassis)', 'Missing field');
                return;
            }
        }
        
        if(!driver_name) {
            toastr.warning('Please select a driver', 'Missing field');
            $('#assignment_driver_name').focus();
            return;
        }
        
        if(!assignment_date) {
            toastr.warning('Please select assignment date', 'Missing field');
            $('#assignment_assignment_date').focus();
            return;
        }

        var chassis_type = $('#assignment_chassis_type').val();
        if(vehicle_type == 'RENTED_ALL') {
            chassis_type = 'RENTED';
        }
        
        var mparam = {
            trip_id: trip_id,
            vehicle_type: vehicle_type,
            truck_plate: $('#assignment_truck_plate').val() || '',
            tractor_plate: $('#assignment_tractor_plate').val() || '',
            chassis_plate: $('#assignment_chassis_plate').val() || '',
            chassis_type: chassis_type,
            vendor_name: $('#assignment_vendor_name').val() || '',
            rental_rate: $('#assignment_rental_rate').val() || 0,
            rental_start_date: $('#assignment_rental_start_date').val() || '',
            rental_end_date: $('#assignment_rental_end_date').val() || '',
            rental_agreement_no: $('#assignment_rental_agreement_no').val() || '',
            vendor_contact_person: $('#assignment_vendor_contact_person').val() || '',
            vendor_contact_number: $('#assignment_vendor_contact_number').val() || '',
            driver_name: driver_name,
            helper_name: $('#assignment_helper_name').val() || '',
            assignment_date: assignment_date,
            dispatch_time: $('#assignment_dispatch_time').val() || '',
            dispatch_location: $('#assignment_dispatch_location').val() || '',
            odometer_before_trip: $('#assignment_odometer_before_trip').val() || 0,
            fuel_level: $('#assignment_fuel_level').val() || 0,
            assignment_status: $('#assignment_status').val() || 'ASSIGNED',
            remarks: $('#assignment_remarks').val() || '',
            meaction: 'SAVE_ASSIGNMENT'
        };

        var btn = $('#assignmentActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                    btn.html('Save Assignment');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('Save Assignment');
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // UPDATE ASSIGNMENT
    // ==============================
    this.__updateAssignment = function() {
        var assignment_id = $('#assignment_id').val();
        var trip_id = $('#assignment_trip_id').val();
        var vehicle_type = $('#assignment_vehicle_type').val();
        var driver_name = $('#assignment_driver_name').val();
        var assignment_date = $('#assignment_assignment_date').val();
        
        if(!vehicle_type) {
            toastr.warning('Please select vehicle type', 'Missing field');
            $('#assignment_vehicle_type').focus();
            return;
        }
        
        if(vehicle_type == 'RIGID') {
            if(!$('#assignment_truck_plate').val()) {
                toastr.warning('Please select a truck', 'Missing field');
                $('#assignment_truck_plate').focus();
                return;
            }
        } else if(vehicle_type == 'TRACTOR_CHASSIS') {
            if(!$('#assignment_tractor_plate').val() || !$('#assignment_chassis_plate').val()) {
                toastr.warning('Please select both tractor and chassis', 'Missing field');
                return;
            }
        } else if(vehicle_type == 'TRACTOR_RENTED_CHASSIS') {
            if(!$('#assignment_tractor_plate').val() || !$('#assignment_chassis_plate').val()) {
                toastr.warning('Please select both tractor and chassis', 'Missing field');
                return;
            }
            if(!$('#assignment_vendor_name').val()) {
                toastr.warning('Please select a vendor', 'Missing field');
                $('#assignment_vendor_name').focus();
                return;
            }
        } else if(vehicle_type == 'RENTED_ALL') {
            if(!$('#assignment_vendor_name').val()) {
                toastr.warning('Please select a vendor', 'Missing field');
                $('#assignment_vendor_name').focus();
                return;
            }
            if(!$('#assignment_truck_plate').val() && !$('#assignment_tractor_plate').val() && !$('#assignment_chassis_plate').val()) {
                toastr.warning('Please provide at least one vehicle (Truck, Tractor, or Chassis)', 'Missing field');
                return;
            }
        }
        
        if(!driver_name) {
            toastr.warning('Please select a driver', 'Missing field');
            $('#assignment_driver_name').focus();
            return;
        }
        
        if(!assignment_date) {
            toastr.warning('Please select assignment date', 'Missing field');
            $('#assignment_assignment_date').focus();
            return;
        }

        var chassis_type = $('#assignment_chassis_type').val();
        if(vehicle_type == 'RENTED_ALL') {
            chassis_type = 'RENTED';
        }
        
        var mparam = {
            assignment_id: assignment_id,
            trip_id: trip_id,
            vehicle_type: vehicle_type,
            truck_plate: $('#assignment_truck_plate').val() || '',
            tractor_plate: $('#assignment_tractor_plate').val() || '',
            chassis_plate: $('#assignment_chassis_plate').val() || '',
            chassis_type: chassis_type,
            vendor_name: $('#assignment_vendor_name').val() || '',
            rental_rate: $('#assignment_rental_rate').val() || 0,
            rental_start_date: $('#assignment_rental_start_date').val() || '',
            rental_end_date: $('#assignment_rental_end_date').val() || '',
            rental_agreement_no: $('#assignment_rental_agreement_no').val() || '',
            vendor_contact_person: $('#assignment_vendor_contact_person').val() || '',
            vendor_contact_number: $('#assignment_vendor_contact_number').val() || '',
            driver_name: driver_name,
            helper_name: $('#assignment_helper_name').val() || '',
            assignment_date: assignment_date,
            dispatch_time: $('#assignment_dispatch_time').val() || '',
            dispatch_location: $('#assignment_dispatch_location').val() || '',
            odometer_before_trip: $('#assignment_odometer_before_trip').val() || 0,
            fuel_level: $('#assignment_fuel_level').val() || 0,
            assignment_status: $('#assignment_status').val() || 'ASSIGNED',
            remarks: $('#assignment_remarks').val() || '',
            meaction: 'UPDATE_ASSIGNMENT'
        };

        var btn = $('#assignmentActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                    btn.html('Update Assignment');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('Update Assignment');
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // REMOVE ASSIGNMENT
    // ==============================
    this.__removeAssignment = function() {
        var assignment_id = $('#assignment_id').val();
        var trip_id = $('#assignment_trip_id').val();
        
        if(!assignment_id) {
            toastr.warning('No assignment to remove');
            return;
        }
        
        if(confirm('Are you sure you want to remove this assignment?')) {
            var mparam = {
                assignment_id: assignment_id,
                trip_id: trip_id,
                meaction: 'DELETE_ASSIGNMENT'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-trips',
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
    // LOAD RESOURCES
    // ==============================
    this.__loadResources = function() {
        this.__loadTrucks();
        this.__loadTractors();
        this.__loadChassis();
        this.__loadDrivers();
        this.__loadHelpers();
        this.__loadVendors();
    };

    this.__loadTrucks = function() {
        var mparam = { meaction: 'GET_AVAILABLE_TRUCKS' };
        jQuery.ajax({
            type: "POST", url: mesiteurl + 'fms-trips', data: mparam, dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select —</option>';
                if(data && data.length > 0) {
                    $.each(data, function(i, item) {
                        opts += '<option value="' + item.plate_number + '">' + item.plate_number + '</option>';
                    });
                }
                $('#assignment_truck_plate').html(opts);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading trucks: " + error);
            }
        });
    };

    this.__loadTractors = function() {
        var mparam = { meaction: 'GET_AVAILABLE_TRACTORS' };
        jQuery.ajax({
            type: "POST", url: mesiteurl + 'fms-trips', data: mparam, dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select —</option>';
                if(data && data.length > 0) {
                    $.each(data, function(i, item) {
                        opts += '<option value="' + item.plate_number + '">' + item.plate_number + '</option>';
                    });
                }
                $('#assignment_tractor_plate').html(opts);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading tractors: " + error);
            }
        });
    };

    this.__loadChassis = function() {
        var mparam = { meaction: 'GET_AVAILABLE_CHASSIS' };
        jQuery.ajax({
            type: "POST", url: mesiteurl + 'fms-trips', data: mparam, dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select —</option>';
                if(data && data.length > 0) {
                    $.each(data, function(i, item) {
                        opts += '<option value="' + item.plate_number + '">' + item.plate_number + '</option>';
                    });
                }
                $('#assignment_chassis_plate').html(opts);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading chassis: " + error);
            }
        });
    };

    this.__loadDrivers = function() {
        var mparam = { meaction: 'GET_AVAILABLE_DRIVERS' };
        jQuery.ajax({
            type: "POST", url: mesiteurl + 'fms-trips', data: mparam, dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select —</option>';
                if(data && data.length > 0) {
                    $.each(data, function(i, item) {
                        opts += '<option value="' + item.driver_name + '">' + item.driver_name + '</option>';
                    });
                }
                $('#assignment_driver_name').html(opts);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading drivers: " + error);
            }
        });
    };

    this.__loadHelpers = function() {
        var mparam = { meaction: 'GET_AVAILABLE_HELPERS' };
        jQuery.ajax({
            type: "POST", url: mesiteurl + 'fms-trips', data: mparam, dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select —</option>';
                if(data && data.length > 0) {
                    $.each(data, function(i, item) {
                        opts += '<option value="' + item.helper_name + '">' + item.helper_name + '</option>';
                    });
                }
                $('#assignment_helper_name').html(opts);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading helpers: " + error);
            }
        });
    };

    this.__loadVendors = function() {
        var mparam = { meaction: 'GET_ACTIVE_VENDORS' };
        jQuery.ajax({
            type: "POST", url: mesiteurl + 'fms-trips', data: mparam, dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select —</option>';
                if(data && data.length > 0) {
                    $.each(data, function(i, item) {
                        opts += '<option value="' + item.vendor_name + '">' + item.vendor_name + '</option>';
                    });
                }
                $('#assignment_vendor_name').html(opts);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading vendors: " + error);
            }
        });
    };

    // ==============================
    // OPEN ADD TRIP
    // ==============================
    this.__openAddTrip = function() {
        $('#tripModalTitle').html('<i class="bi bi-plus-circle me-2"></i>New Trip');
        $('#formBtnText').text('Save Trip');
        $('#form_trip_id').val('');
        $('#form_trip_code').val('');
        $('#form_trip_code_display').val('Auto-generated');
        $('#form_customer_id').val('');
        $('#form_booking_reference').val('');
        $('#form_service_type').val('');
        $('#form_trip_type').val('ONE_WAY');
        $('#form_priority').val('NORMAL');
        $('#form_scheduled_date').val('');
        $('#form_pickup_date').val('');
        $('#form_expected_delivery_date').val('');
        $('#form_origin').val('');
        $('#form_destination').val('');
        $('#form_cargo_description').val('');
        $('#form_quantity').val('0');
        $('#form_unit').val('');
        $('#form_estimated_weight').val('0');
        $('#form_special_instructions').val('');
        $('#form_trip_status').val('SCHEDULED');
        $('#form_remarks').val('');
        
        // Hide cargo items card in New Trip mode
        $('#cargoItemsCard').hide();
        
        $('#tripForm').removeClass('was-validated');
        var modal = new bootstrap.Modal(document.getElementById('tripModal'));
        modal.show();
    };

    // ==============================
    // EDIT TRIP
    // ==============================
    this.__editTrip = function(trip_id) {
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_TRIP'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#tripModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Trip');
                    $('#formBtnText').text('Update Trip');
                    $('#form_trip_id').val(data.trip_id);
                    $('#form_trip_code').val(data.trip_code);
                    $('#form_trip_code_display').val(data.trip_code);
                    $('#form_customer_id').val(data.customer_id);
                    $('#form_booking_reference').val(data.booking_reference);
                    $('#form_service_type').val(data.service_type);
                    $('#form_trip_type').val(data.trip_type);
                    $('#form_priority').val(data.priority);
                    $('#form_scheduled_date').val(data.scheduled_date);
                    $('#form_pickup_date').val(data.pickup_date);
                    $('#form_expected_delivery_date').val(data.expected_delivery_date);
                    $('#form_origin').val(data.origin);
                    $('#form_destination').val(data.destination);
                    $('#form_cargo_description').val(data.cargo_description);
                    $('#form_quantity').val(data.quantity);
                    $('#form_unit').val(data.unit);
                    $('#form_estimated_weight').val(data.estimated_weight);
                    $('#form_special_instructions').val(data.special_instructions);
                    $('#form_trip_status').val(data.trip_status);
                    $('#form_remarks').val(data.remarks);
                    
                    // Show cargo items section
                    $('#cargoItemsCard').show();
                    __Trips.__loadCargoItems(data.trip_id);
                    
                    $('#tripForm').removeClass('was-validated');
                    var modal = new bootstrap.Modal(document.getElementById('tripModal'));
                    modal.show();
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading trip: " + error);
            }
        });
    };

    // ==============================
    // CARGO ITEMS — LOAD
    // ==============================
    this.__loadCargoItems = function(trip_id) {
        if(!trip_id || trip_id == 0) {
            $('#cargoItemsBody').html('<tr><td colspan="7" class="text-center text-muted">Save trip first to add cargo items</td></tr>');
            return;
        }

        var mparam = { trip_id: trip_id, meaction: 'GET_CARGO_ITEMS' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data && data.length > 0) {
                    $.each(data, function(index, row) {
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td><strong>' + row.item_description + '</strong></td>';
                        html += '<td>' + parseFloat(row.quantity).toFixed(2) + '</td>';
                        html += '<td>' + (row.unit || '—') + '</td>';
                        html += '<td>' + parseFloat(row.weight).toFixed(2) + '</td>';
                        html += '<td>' + (row.remarks || '—') + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button type="button" class="btn-icon btn-icon-edit" onclick="__Trips.__editCargoItem(' + row.item_id + ')" title="Edit"><i class="bi bi-pencil"></i></button>';
                        html += '<button type="button" class="btn-icon btn-icon-delete" onclick="__Trips.__deleteCargoItem(' + row.item_id + ')" title="Delete"><i class="bi bi-trash"></i></button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center text-muted">No cargo items</td></tr>';
                }
                $('#cargoItemsBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading cargo items: " + error);
            }
        });
    };

    // ==============================
    // CARGO ITEMS — SAVE
    // ==============================
    this.__saveCargoItem = function() {
        var trip_id = $('#form_trip_id').val();
        var item_description = $('#cargo_item_description').val().trim();

        if(!trip_id) {
            toastr.warning('Please save the trip first');
            return;
        }
        if(!item_description) {
            toastr.warning('Please enter item description', 'Missing field');
            $('#cargo_item_description').focus();
            return;
        }

        var mparam = {
            trip_id: trip_id,
            item_description: item_description,
            quantity: $('#cargo_quantity').val() || 0,
            unit: $('#cargo_unit').val(),
            weight: $('#cargo_weight').val() || 0,
            remarks: $('#cargo_remarks').val(),
            meaction: 'SAVE_CARGO_ITEM'
        };

        var btn = $('#cargoActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    $('#cargo_item_description').val('');
                    $('#cargo_quantity').val('');
                    $('#cargo_unit').val('');
                    $('#cargo_weight').val('');
                    $('#cargo_remarks').val('');
                    $('#cargo_editing_id').val('');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Trips.__saveCargoItem()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    __Trips.__loadCargoItems(trip_id);
                } else {
                    toastr.error(data.message);
                    btn.html('<i class="bi bi-plus"></i> Add');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-plus"></i> Add');
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // CARGO ITEMS — EDIT
    // ==============================
    this.__editCargoItem = function(item_id) {
        var mparam = { item_id: item_id, meaction: 'GET_CARGO_ITEM' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#cargo_editing_id').val(data.item_id);
                    $('#cargo_item_description').val(data.item_description);
                    $('#cargo_quantity').val(data.quantity);
                    $('#cargo_unit').val(data.unit);
                    $('#cargo_weight').val(data.weight);
                    $('#cargo_remarks').val(data.remarks);

                    var btn = $('#cargoActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__Trips.__updateCargoItem()');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            }
        });
    };

    // ==============================
    // CARGO ITEMS — UPDATE
    // ==============================
    this.__updateCargoItem = function() {
        var item_id = $('#cargo_editing_id').val();
        var trip_id = $('#form_trip_id').val();
        var item_description = $('#cargo_item_description').val().trim();

        if(!item_description) {
            toastr.warning('Please enter item description');
            return;
        }

        var mparam = {
            item_id: item_id,
            item_description: item_description,
            quantity: $('#cargo_quantity').val() || 0,
            unit: $('#cargo_unit').val(),
            weight: $('#cargo_weight').val() || 0,
            remarks: $('#cargo_remarks').val(),
            meaction: 'UPDATE_CARGO_ITEM'
        };

        var btn = $('#cargoActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    $('#cargo_item_description').val('');
                    $('#cargo_quantity').val('');
                    $('#cargo_unit').val('');
                    $('#cargo_weight').val('');
                    $('#cargo_remarks').val('');
                    $('#cargo_editing_id').val('');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Trips.__saveCargoItem()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    __Trips.__loadCargoItems(trip_id);
                } else {
                    toastr.error(data.message);
                    btn.html('Update');
                }
            }
        });
    };

    // ==============================
    // CARGO ITEMS — DELETE
    // ==============================
    this.__deleteCargoItem = function(item_id) {
        if(confirm('Are you sure you want to delete this cargo item?')) {
            var trip_id = $('#form_trip_id').val();
            var mparam = { item_id: item_id, meaction: 'DELETE_CARGO_ITEM' };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-trips',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Trips.__loadCargoItems(trip_id);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    };

    // ==============================
    // VIEW ROUTE
    // ==============================
    this.__viewRoute = function(trip_id, trip_code) {
        $('#route_trip_id').val(trip_id);
        $('#route_trip_code').text(trip_code);
        
        $('#wp_sequence').val('1');
        $('#wp_type').val('GARAGE');
        $('#wp_name').val('');
        $('#wp_address').val('');
        $('#wp_expected_arrival').val('');
        $('#wp_expected_departure').val('');
        $('#wp_remarks').val('');
        $('#route_editing_id').val('');
        
        var btn = $('#wpActionBtn');
        btn.html('<i class="bi bi-plus"></i> Add');
        btn.attr('onclick', '__Trips.__saveWaypoint()');
        btn.removeClass('btn-warning').addClass('btn-primary');
        
        this.__loadWaypoints(trip_id);
        
        var modal = new bootstrap.Modal(document.getElementById('routeModal'));
        modal.show();
    };

    this.__loadWaypoints = function(trip_id) {
        var mparam = {
            trip_id: trip_id,
            meaction: 'GET_WAYPOINTS'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data && data.length > 0) {
                    $.each(data, function(index, row) {
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
                        
                        html += '<tr>';
                        html += '<td>' + row.sequence + '</td>';
                        html += '<td><strong>' + (row.waypoint_name || '—') + '</strong></td>';
                        html += '<td>' + typeDisplay + '</td>';
                        html += '<td>' + (row.address || '—') + '</td>';
                        html += '<td>' + (row.expected_arrival || '—') + '</td>';
                        html += '<td>' + (row.expected_departure || '—') + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__Trips.__editWaypoint(' + row.waypoint_id + ')" title="Edit">';
                        html += '<i class="bi bi-pencil"></i>';
                        html += '</button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__Trips.__deleteWaypoint(' + row.waypoint_id + ')" title="Delete">';
                        html += '<i class="bi bi-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center text-muted">No waypoints found</td></tr>';
                }
                $('#waypointsBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading waypoints: " + error);
            }
        });
    };

    this.__saveWaypoint = function() {
        var trip_id = $('#route_trip_id').val();
        var waypoint_name = $('#wp_name').val().trim();

        if(!waypoint_name) {
            toastr.warning('Please enter waypoint name', 'Missing field');
            $('#wp_name').focus();
            return;
        }

        if(!trip_id) {
            toastr.warning('No trip selected', 'Error');
            return;
        }

        var sequence = parseInt($('#wp_sequence').val()) || 1;
        var waypoint_type = $('#wp_type').val();
        
        if(!waypoint_type || waypoint_type === '') {
            toastr.warning('Please select a waypoint type', 'Missing field');
            $('#wp_type').focus();
            return;
        }
        
        var address = $('#wp_address').val().trim();
        var expected_arrival = $('#wp_expected_arrival').val();
        var expected_departure = $('#wp_expected_departure').val();
        var remarks = $('#wp_remarks').val().trim();
        
        var editing_id = $('#route_editing_id').val();
        var meaction = editing_id ? 'EDIT_WAYPOINT' : 'SAVE_WAYPOINT';
        
        var mparam = {
            trip_id: trip_id,
            sequence: sequence,
            waypoint_type: waypoint_type,
            waypoint_name: waypoint_name,
            address: address || '',
            expected_arrival: expected_arrival || null,
            expected_departure: expected_departure || null,
            remarks: remarks || '',
            meaction: meaction
        };
        
        if(editing_id) {
            mparam.waypoint_id = editing_id;
        }

        var btn = $('#wpActionBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if(data.status == 'success'){
                    toastr.success(data.message);
                    $('#wp_sequence').val('1');
                    $('#wp_type').val('GARAGE');
                    $('#wp_name').val('');
                    $('#wp_address').val('');
                    $('#wp_expected_arrival').val('');
                    $('#wp_expected_departure').val('');
                    $('#wp_remarks').val('');
                    $('#route_editing_id').val('');
                    
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Trips.__saveWaypoint()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    
                    __Trips.__loadWaypoints(trip_id);
                } else {
                    toastr.error(data.message);
                    btn.html('<i class="bi bi-plus"></i> Add');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-plus"></i> Add');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__editWaypoint = function(waypoint_id) {
        var mparam = {
            waypoint_id: waypoint_id,
            meaction: 'GET_WAYPOINT'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trips',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data && data.waypoint_id) {
                    $('#wp_sequence').val(data.sequence);
                    $('#wp_type').val(data.waypoint_type);
                    $('#wp_name').val(data.waypoint_name);
                    $('#wp_address').val(data.address || '');
                    
                    if(data.expected_arrival) {
                        var arrival = data.expected_arrival.replace(' ', 'T');
                        $('#wp_expected_arrival').val(arrival);
                    } else {
                        $('#wp_expected_arrival').val('');
                    }
                    
                    if(data.expected_departure) {
                        var departure = data.expected_departure.replace(' ', 'T');
                        $('#wp_expected_departure').val(departure);
                    } else {
                        $('#wp_expected_departure').val('');
                    }
                    
                    $('#wp_remarks').val(data.remarks || '');
                    $('#route_editing_id').val(waypoint_id);
                    
                    var btn = $('#wpActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__Trips.__saveWaypoint()');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                } else {
                    toastr.error('Failed to load waypoint data');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading waypoint: " + error);
            }
        });
    };

    this.__deleteWaypoint = function(waypoint_id) {
        if(confirm('Are you sure you want to delete this waypoint?')) {
            var trip_id = $('#route_trip_id').val();
            
            var mparam = {
                waypoint_id: waypoint_id,
                meaction: 'DELETE_WAYPOINT'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-trips',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Trips.__loadWaypoints(trip_id);
                        $('#route_editing_id').val('');
                        $('#wp_type').val('GARAGE');
                        var btn = $('#wpActionBtn');
                        btn.html('<i class="bi bi-plus"></i> Add');
                        btn.attr('onclick', '__Trips.__saveWaypoint()');
                        btn.removeClass('btn-warning').addClass('btn-primary');
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
    // SHOW DELETE MODAL
    // ==============================
    this.__showDeleteModal = function(id, name) {
        deleteId = id;
        deleteName = name;
        document.getElementById('delete_trip_name').innerHTML = name;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };
}

// ==============================
// HELPER FUNCTIONS
// ==============================
function getStatusBadge(status) {
    var map = {
        'DRAFT': '<span class="badge badge-secondary">Draft</span>',
        'SCHEDULED': '<span class="badge badge-info">Scheduled</span>',
        'ASSIGNED': '<span class="badge badge-primary">Assigned</span>',
        'DISPATCHED': '<span class="badge badge-warning">Dispatched</span>',
        'IN_TRANSIT': '<span class="badge badge-info">In Transit</span>',
        'DELIVERED': '<span class="badge badge-success">Delivered</span>',
        'COMPLETED': '<span class="badge badge-success">Completed</span>',
        'CANCELLED': '<span class="badge badge-danger">Cancelled</span>'
    };
    return map[status] || '<span class="badge badge-secondary">' + status + '</span>';
}

function getPriorityBadge(priority) {
    var map = {
        'LOW': '<span class="badge badge-secondary">Low</span>',
        'NORMAL': '<span class="badge badge-info">Normal</span>',
        'HIGH': '<span class="badge badge-warning">High</span>',
        'URGENT': '<span class="badge badge-danger">Urgent</span>'
    };
    return map[priority] || '<span class="badge badge-secondary">' + priority + '</span>';
}

var deleteId = null;
var deleteName = '';

$(document).ready(function() {
    console.log('Trips ready');
});