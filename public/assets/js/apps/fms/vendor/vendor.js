var __Vendors = new __Vendors();

function __Vendors() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Vendors initialized, URL: ' + mesiteurl);

    // ==============================
    // SAVE VENDOR
    // ==============================
    this.__saveVendor = function() {
        var vendor_name = $('#form_vendor_name').val();
        
        if(!vendor_name) {
            toastr.warning('Please enter vendor name', 'Missing field');
            $('#form_vendor_name').focus();
            return false;
        }

        var mparam = {
            vendor_name: vendor_name,
            vendor_type: $('#form_vendor_type').val(),
            contact_person: $('#form_contact_person').val(),
            contact_number: $('#form_contact_number').val(),
            email_address: $('#form_email_address').val(),
            address: $('#form_address').val(),
            payment_terms: $('#form_payment_terms').val(),
            vendor_status: $('#form_vendor_status').val(),
            remarks: $('#form_remarks').val(),
            meaction: 'SAVE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-vendors',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('vendorModal'));
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
    // UPDATE VENDOR
    // ==============================
    this.__updateVendor = function() {
        var vendor_id = $('#form_vendor_id').val();
        var vendor_name = $('#form_vendor_name').val();

        if(!vendor_name) {
            toastr.warning('Please enter vendor name', 'Missing field');
            $('#form_vendor_name').focus();
            return false;
        }

        var mparam = {
            vendor_id: vendor_id,
            vendor_name: vendor_name,
            vendor_type: $('#form_vendor_type').val(),
            contact_person: $('#form_contact_person').val(),
            contact_number: $('#form_contact_number').val(),
            email_address: $('#form_email_address').val(),
            address: $('#form_address').val(),
            payment_terms: $('#form_payment_terms').val(),
            vendor_status: $('#form_vendor_status').val(),
            remarks: $('#form_remarks').val(),
            meaction: 'EDIT'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-vendors',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('vendorModal'));
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
    // DELETE VENDOR
    // ==============================
    this.__deleteVendor = function() {
        if(deleteId) {
            var mparam = {
                vendor_id: deleteId,
                meaction: 'DELETE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-vendors',
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
    // SHOW DELETE MODAL
    // ==============================
    this.__showDeleteModal = function(id, name) {
        deleteId = id;
        deleteName = name;
        document.getElementById('delete_vendor_name').innerHTML = name;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };

    // ==============================
    // VIEW SERVICES
    // ==============================
    this.__viewServices = function(vendor_id, vendor_name) {
        $('#svc_vendor_id').val(vendor_id);
        $('#svc_vendor_name').text(vendor_name);
        
        // Reset service form
        $('#svc_service_type').val('');
        $('#svc_rate_type').val('');
        $('#svc_rate_amount').val('');
        $('#svc_effective_date').val('');
        $('#svc_service_status').val('ACTIVE');
        $('#svc_remarks').val('');
        
        // Reset button to Add mode
        var btn = $('#svcActionBtn');
        btn.html('<i class="bi bi-plus"></i> Add');
        btn.attr('onclick', '__Vendors.__saveService()');
        btn.removeClass('btn-warning').addClass('btn-primary');
        
        // Load services
        this.__loadServices(vendor_id);
        
        var modal = new bootstrap.Modal(document.getElementById('servicesModal'));
        modal.show();
    };

    // ==============================
    // LOAD SERVICES
    // ==============================
    this.__loadServices = function(vendor_id) {
        var mparam = {
            vendor_id: vendor_id,
            meaction: 'GET_SERVICES'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-vendors',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data.length > 0) {
                    $.each(data, function(index, row) {
                        var statusBadge = row.service_status == 'ACTIVE' ? 
                            '<span class="badge badge-success">Active</span>' : 
                            '<span class="badge badge-secondary">Inactive</span>';
                        
                        var rateType = row.rate_type.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                        
                        html += '<tr>';
                        html += '<td><strong>' + row.service_type + '</strong></td>';
                        html += '<td>' + rateType + '</td>';
                        html += '<td>₱' + parseFloat(row.rate_amount).toFixed(2) + '</td>';
                        html += '<td>' + (row.effective_date || '—') + '</td>';
                        html += '<td>' + statusBadge + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__Vendors.__editService(' + row.service_id + ')" title="Edit">';
                        html += '<i class="bi bi-pencil"></i>';
                        html += '</button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__Vendors.__deleteService(' + row.service_id + ')" title="Delete">';
                        html += '<i class="bi bi-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No services found</td></tr>';
                }
                $('#servicesBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading services: " + error);
            }
        });
    };

    // ==============================
    // SAVE SERVICE
    // ==============================
    this.__saveService = function() {
        var vendor_id = $('#svc_vendor_id').val();
        var service_type = $('#svc_service_type').val();
        var rate_type = $('#svc_rate_type').val();
        var rate_amount = $('#svc_rate_amount').val();
        var effective_date = $('#svc_effective_date').val();
        var service_status = $('#svc_service_status').val();
        var remarks = $('#svc_remarks').val();

        if(!service_type) {
            toastr.warning('Please select service type', 'Missing field');
            $('#svc_service_type').focus();
            return;
        }

        var mparam = {
            vendor_id: vendor_id,
            service_type: service_type,
            rate_type: rate_type,
            rate_amount: rate_amount,
            effective_date: effective_date,
            service_status: service_status,
            remarks: remarks,
            meaction: 'SAVE_SERVICE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-vendors',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Vendors.__loadServices(vendor_id);
                    // Clear form
                    $('#svc_service_type').val('');
                    $('#svc_rate_type').val('');
                    $('#svc_rate_amount').val('');
                    $('#svc_effective_date').val('');
                    $('#svc_service_status').val('ACTIVE');
                    $('#svc_remarks').val('');
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
    // EDIT SERVICE
    // ==============================
    this.__editService = function(service_id) {
        var mparam = {
            service_id: service_id,
            meaction: 'GET_SERVICE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-vendors',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#svc_service_type').val(data.service_type);
                    $('#svc_rate_type').val(data.rate_type);
                    $('#svc_rate_amount').val(data.rate_amount);
                    $('#svc_effective_date').val(data.effective_date);
                    $('#svc_service_status').val(data.service_status);
                    $('#svc_remarks').val(data.remarks);
                    
                    // Change button to Update mode
                    var btn = $('#svcActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__Vendors.__updateService(' + service_id + ')');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading service: " + error);
            }
        });
    };

    // ==============================
    // UPDATE SERVICE
    // ==============================
    this.__updateService = function(service_id) {
        var vendor_id = $('#svc_vendor_id').val();
        var service_type = $('#svc_service_type').val();
        var rate_type = $('#svc_rate_type').val();
        var rate_amount = $('#svc_rate_amount').val();
        var effective_date = $('#svc_effective_date').val();
        var service_status = $('#svc_service_status').val();
        var remarks = $('#svc_remarks').val();

        if(!service_type) {
            toastr.warning('Please select service type', 'Missing field');
            $('#svc_service_type').focus();
            return;
        }

        var mparam = {
            service_id: service_id,
            service_type: service_type,
            rate_type: rate_type,
            rate_amount: rate_amount,
            effective_date: effective_date,
            service_status: service_status,
            remarks: remarks,
            meaction: 'EDIT_SERVICE'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-vendors',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Vendors.__loadServices(vendor_id);
                    
                    // Reset button back to Add mode
                    var btn = $('#svcActionBtn');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Vendors.__saveService()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    
                    // Clear form
                    $('#svc_service_type').val('');
                    $('#svc_rate_type').val('');
                    $('#svc_rate_amount').val('');
                    $('#svc_effective_date').val('');
                    $('#svc_service_status').val('ACTIVE');
                    $('#svc_remarks').val('');
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
    // DELETE SERVICE
    // ==============================
    this.__deleteService = function(service_id) {
        if(confirm('Are you sure you want to delete this service?')) {
            var vendor_id = $('#svc_vendor_id').val();
            
            var mparam = {
                service_id: service_id,
                meaction: 'DELETE_SERVICE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-vendors',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Vendors.__loadServices(vendor_id);
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
}

var deleteId = null;
var deleteName = '';

$(document).ready(function() {
    console.log('Document ready');
});