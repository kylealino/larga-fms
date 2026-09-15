var __Customers = new __Customers();

function __Customers() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Customers initialized, URL: ' + mesiteurl);

    // ==============================
    // SAVE CUSTOMER - MODAL VERSION
    // ==============================
    this.__saveCustomerModal = function() {
        var customer_name = $('#form_customer_name').val();
        var customer_type = $('#form_customer_type').val();
        var tin = $('#form_tin').val();
        var contact_person = $('#form_contact_person').val();
        var contact_position = $('#form_contact_position').val();
        var contact_number = $('#form_contact_number').val();
        var email_address = $('#form_email_address').val();
        var business_address = $('#form_business_address').val();
        var billing_address = $('#form_billing_address').val();
        var payment_terms = $('#form_payment_terms').val();
        var credit_limit = $('#form_credit_limit').val();
        var status = $('#form_status').val();
        var remarks = $('#form_remarks').val();

        if(!customer_name) {
            toastr.warning('Please enter customer name', 'Missing field');
            $('#form_customer_name').focus();
            return false;
        }

        var mparam = {
            customer_name: customer_name,
            customer_type: customer_type,
            tin: tin,
            contact_person: contact_person,
            contact_position: contact_position,
            contact_number: contact_number,
            email_address: email_address,
            business_address: business_address,
            billing_address: billing_address,
            payment_terms: payment_terms,
            credit_limit: credit_limit,
            status: status,
            remarks: remarks,
            meaction: 'SAVE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-customers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
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
    // UPDATE CUSTOMER - MODAL VERSION
    // ==============================
    this.__updateCustomerModal = function() {
        var customer_id = $('#form_customer_id').val();
        var customer_name = $('#form_customer_name').val();
        var customer_type = $('#form_customer_type').val();
        var tin = $('#form_tin').val();
        var contact_person = $('#form_contact_person').val();
        var contact_position = $('#form_contact_position').val();
        var contact_number = $('#form_contact_number').val();
        var email_address = $('#form_email_address').val();
        var business_address = $('#form_business_address').val();
        var billing_address = $('#form_billing_address').val();
        var payment_terms = $('#form_payment_terms').val();
        var credit_limit = $('#form_credit_limit').val();
        var status = $('#form_status').val();
        var remarks = $('#form_remarks').val();

        if(!customer_name) {
            toastr.warning('Please enter customer name', 'Missing field');
            $('#form_customer_name').focus();
            return false;
        }

        var mparam = {
            customer_id: customer_id,
            customer_name: customer_name,
            customer_type: customer_type,
            tin: tin,
            contact_person: contact_person,
            contact_position: contact_position,
            contact_number: contact_number,
            email_address: email_address,
            business_address: business_address,
            billing_address: billing_address,
            payment_terms: payment_terms,
            credit_limit: credit_limit,
            status: status,
            remarks: remarks,
            meaction: 'EDIT'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-customers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
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
    // DELETE CUSTOMER
    // ==============================
    this.__deleteCustomer = function() {
        if(deleteId) {
            var mparam = {
                customer_id: deleteId,
                meaction: 'DELETE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-customers',
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
    // VIEW LOCATIONS
    // ==============================
    this.__viewLocations = function(customer_id, customer_name) {
        $('#loc_customer_id').val(customer_id);
        $('#loc_customer_name').text(customer_name);
        
        // Reset location form
        $('#loc_location_name').val('');
        $('#loc_address').val('');
        $('#loc_city').val('');
        $('#loc_province').val('');
        $('#loc_contact_person').val('');
        $('#loc_contact_number').val('');
        $('#loc_special_instructions').val('');
        
        // Reset button to Add mode
        var btn = $('#locActionBtn');
        btn.html('<i class="bi bi-plus"></i> Add');
        btn.attr('onclick', '__Customers.__saveLocation()');
        btn.removeClass('btn-warning').addClass('btn-primary');
        
        // Load locations
        this.__loadLocations(customer_id);
        
        var modal = new bootstrap.Modal(document.getElementById('locationsModal'));
        modal.show();
    };

    // ==============================
    // LOAD LOCATIONS
    // ==============================
    this.__loadLocations = function(customer_id) {
        var mparam = {
            customer_id: customer_id,
            meaction: 'GET_LOCATIONS'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-customers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data.length > 0) {
                    $.each(data, function(index, row) {
                        var statusBadge = row.status == 'ACTIVE' ? 
                            '<span class="badge badge-success">Active</span>' : 
                            '<span class="badge badge-secondary">Inactive</span>';
                        
                        html += '<tr>';
                        html += '<td><strong>' + row.location_name + '</strong></td>';
                        html += '<td>' + (row.address || '—') + '</td>';
                        html += '<td>' + (row.city || '—') + '</td>';
                        html += '<td>' + (row.province || '—') + '</td>';
                        html += '<td>' + statusBadge + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__Customers.__editLocation(' + row.location_id + ')" title="Edit">';
                        html += '<i class="bi bi-pencil"></i>';
                        html += '</button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__Customers.__deleteLocation(' + row.location_id + ')" title="Delete">';
                        html += '<i class="bi bi-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No locations found</td></tr>';
                }
                $('#locationsBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading locations: " + error);
            }
        });
    };

    // ==============================
    // SAVE LOCATION
    // ==============================
    this.__saveLocation = function() {
        var customer_id = $('#loc_customer_id').val();
        var location_name = $('#loc_location_name').val();
        var address = $('#loc_address').val();
        var city = $('#loc_city').val();
        var province = $('#loc_province').val();
        var contact_person = $('#loc_contact_person').val();
        var contact_number = $('#loc_contact_number').val();
        var special_instructions = $('#loc_special_instructions').val();

        if(!location_name) {
            toastr.warning('Please enter location name', 'Missing field');
            $('#loc_location_name').focus();
            return;
        }

        var mparam = {
            customer_id: customer_id,
            location_name: location_name,
            address: address,
            city: city,
            province: province,
            contact_person: contact_person,
            contact_number: contact_number,
            special_instructions: special_instructions,
            meaction: 'SAVE_LOCATION'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-customers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Customers.__loadLocations(customer_id);
                    // Clear form
                    $('#loc_location_name').val('');
                    $('#loc_address').val('');
                    $('#loc_city').val('');
                    $('#loc_province').val('');
                    $('#loc_contact_person').val('');
                    $('#loc_contact_number').val('');
                    $('#loc_special_instructions').val('');
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
    // EDIT LOCATION
    // ==============================
    this.__editLocation = function(location_id) {
        var mparam = {
            location_id: location_id,
            meaction: 'GET_LOCATION'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-customers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#loc_location_name').val(data.location_name);
                    $('#loc_address').val(data.address);
                    $('#loc_city').val(data.city);
                    $('#loc_province').val(data.province);
                    $('#loc_contact_person').val(data.contact_person);
                    $('#loc_contact_number').val(data.contact_number);
                    $('#loc_special_instructions').val(data.special_instructions);
                    
                    // Change button to update
                    var btn = $('#locActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__Customers.__updateLocation(' + location_id + ')');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading location: " + error);
            }
        });
    };

    // ==============================
    // UPDATE LOCATION
    // ==============================
    this.__updateLocation = function(location_id) {
        var customer_id = $('#loc_customer_id').val();
        var location_name = $('#loc_location_name').val();
        var address = $('#loc_address').val();
        var city = $('#loc_city').val();
        var province = $('#loc_province').val();
        var contact_person = $('#loc_contact_person').val();
        var contact_number = $('#loc_contact_number').val();
        var special_instructions = $('#loc_special_instructions').val();

        if(!location_name) {
            toastr.warning('Please enter location name', 'Missing field');
            $('#loc_location_name').focus();
            return;
        }

        var mparam = {
            location_id: location_id,
            location_name: location_name,
            address: address,
            city: city,
            province: province,
            contact_person: contact_person,
            contact_number: contact_number,
            special_instructions: special_instructions,
            meaction: 'EDIT_LOCATION'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-customers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Customers.__loadLocations(customer_id);
                    
                    // Reset button back to Add
                    var btn = $('#locActionBtn');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Customers.__saveLocation()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    
                    // Clear form
                    $('#loc_location_name').val('');
                    $('#loc_address').val('');
                    $('#loc_city').val('');
                    $('#loc_province').val('');
                    $('#loc_contact_person').val('');
                    $('#loc_contact_number').val('');
                    $('#loc_special_instructions').val('');
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
    // DELETE LOCATION
    // ==============================
    this.__deleteLocation = function(location_id) {
        if(confirm('Are you sure you want to delete this location?')) {
            var customer_id = $('#loc_customer_id').val();
            
            var mparam = {
                location_id: location_id,
                meaction: 'DELETE_LOCATION'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-customers',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Customers.__loadLocations(customer_id);
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
    // EDIT CUSTOMER
    // ==============================
    this.__editCustomer = function(id, code, name, type, tin, contact_person, contact_position, contact_number, email, business_addr, billing_addr, payment_terms, credit_limit, status, remarks) {
        $('#customerModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Customer');
        $('#formBtnText').text('Update Customer');
        $('#form_customer_id').val(id);
        $('#form_customer_code').val(code);
        $('#form_customer_code_display').val(code);
        $('#form_customer_name').val(name);
        $('#form_customer_type').val(type);
        $('#form_tin').val(tin);
        $('#form_contact_person').val(contact_person);
        $('#form_contact_position').val(contact_position);
        $('#form_contact_number').val(contact_number);
        $('#form_email_address').val(email);
        $('#form_business_address').val(business_addr);
        $('#form_billing_address').val(billing_addr);
        $('#form_payment_terms').val(payment_terms);
        $('#form_credit_limit').val(credit_limit);
        $('#form_status').val(status);
        $('#form_remarks').val(remarks);
        $('#customerForm').removeClass('was-validated');
        var modal = new bootstrap.Modal(document.getElementById('customerModal'));
        modal.show();
    };

    // ==============================
    // SHOW DELETE MODAL
    // ==============================
    this.__showDeleteModal = function(id, name) {
        deleteId = id;
        deleteName = name;
        document.getElementById('delete_customer_name').innerHTML = name;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };
}

var deleteId = null;
var deleteName = '';

$(document).ready(function() {
    console.log('Document ready');
});