var __Helpers = new __Helpers();

function __Helpers() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Helpers initialized, URL: ' + mesiteurl);

    // ==============================
    // SAVE HELPER
    // ==============================
    this.__saveHelper = function() {
        var helper_name = $('#form_helper_name').val();
        
        if(!helper_name) {
            toastr.warning('Please enter helper name', 'Missing field');
            $('#form_helper_name').focus();
            return false;
        }

        var formData = new FormData();
        formData.append('helper_name', helper_name);
        formData.append('contact_number', $('#form_contact_number').val());
        formData.append('address', $('#form_address').val());
        formData.append('employment_type', $('#form_employment_type').val());
        formData.append('date_hired', $('#form_date_hired').val());
        formData.append('helper_status', $('#form_helper_status').val());
        formData.append('emergency_contact', $('#form_emergency_contact').val());
        formData.append('emergency_contact_number', $('#form_emergency_contact_number').val());
        formData.append('license_number', $('#form_license_number').val());
        formData.append('license_type', $('#form_license_type').val());
        formData.append('restriction_code', $('#form_restriction_code').val());
        formData.append('expiration_date', $('#form_expiration_date').val());
        formData.append('meaction', 'SAVE');

        // Add profile picture if selected
        var profileFile = document.getElementById('form_profile_picture');
        if(profileFile && profileFile.files.length > 0) {
            formData.append('profile_picture', profileFile.files[0]);
        }

        // Add license attachment if selected
        var licenseFile = document.getElementById('form_license_attachment');
        if(licenseFile && licenseFile.files.length > 0) {
            formData.append('license_attachment', licenseFile.files[0]);
        }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-helpers',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('helperModal'));
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
    // UPDATE HELPER
    // ==============================
    this.__updateHelper = function() {
        var helper_id = $('#form_helper_id').val();
        var helper_name = $('#form_helper_name').val();

        if(!helper_name) {
            toastr.warning('Please enter helper name', 'Missing field');
            $('#form_helper_name').focus();
            return false;
        }

        var formData = new FormData();
        formData.append('helper_id', helper_id);
        formData.append('helper_name', helper_name);
        formData.append('contact_number', $('#form_contact_number').val());
        formData.append('address', $('#form_address').val());
        formData.append('employment_type', $('#form_employment_type').val());
        formData.append('date_hired', $('#form_date_hired').val());
        formData.append('helper_status', $('#form_helper_status').val());
        formData.append('emergency_contact', $('#form_emergency_contact').val());
        formData.append('emergency_contact_number', $('#form_emergency_contact_number').val());
        formData.append('license_number', $('#form_license_number').val());
        formData.append('license_type', $('#form_license_type').val());
        formData.append('restriction_code', $('#form_restriction_code').val());
        formData.append('expiration_date', $('#form_expiration_date').val());
        formData.append('meaction', 'EDIT');

        // Add profile picture if selected
        var profileFile = document.getElementById('form_profile_picture');
        if(profileFile && profileFile.files.length > 0) {
            formData.append('profile_picture', profileFile.files[0]);
        }

        // Add license attachment if selected
        var licenseFile = document.getElementById('form_license_attachment');
        if(licenseFile && licenseFile.files.length > 0) {
            formData.append('license_attachment', licenseFile.files[0]);
        }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-helpers',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('helperModal'));
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
    // DELETE HELPER
    // ==============================
    this.__deleteHelper = function() {
        if(deleteId) {
            var mparam = {
                helper_id: deleteId,
                meaction: 'DELETE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-helpers',
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
        document.getElementById('delete_helper_name').innerHTML = name;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };

    // ==========================================
    // DELIVERY HISTORY
    // ==========================================
    this.__openHistoryModal = function(helper_id, helper_name) {
        $('#history_helper_id').val(helper_id);
        $('#history_helper_name').text(helper_name);
        $('#historyContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');

        var modal = new bootstrap.Modal(document.getElementById('historyModal'));
        modal.show();

        var mparam = { helper_id: helper_id, meaction: 'GET_HELPER_HISTORY' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-helpers',
            data: mparam,
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                var html = '';

                if (data && data.length > 0) {
                    html += '<div class="journey-timeline">';
                    $.each(data, function(i, item) {
                        var cls = '';
                        var status = (item.dr_status || '').toUpperCase();
                        if (status === 'DELIVERED') cls = 'j-delivered';
                        else if (status === 'PARTIALLY_DELIVERED') cls = 'j-partial';
                        else if (status === 'FAILED_DELIVERY' || status === 'CANCELLED') cls = 'j-failed';

                        var dateStr = item.actual_delivery_date ? new Date(item.actual_delivery_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : '—';
                        var vehicle = item.truck_plate || (item.tractor_plate && item.chassis_plate ? item.tractor_plate + ' + ' + item.chassis_plate : (item.tractor_plate || item.chassis_plate)) || '—';

                        html += '<div class="journey-item ' + cls + '">';
                        html += '<div class="j-header">';
                        html += '<span class="j-type">' + (item.trip_code || '') + '</span>';
                        html += '<span class="j-date">' + dateStr + '</span>';
                        html += '</div>';
                        html += '<div class="j-description">' + (item.origin || '—') + ' &rarr; ' + (item.destination || '—') + '</div>';
                        html += '<div class="j-details">Customer: ' + (item.customer_name || '—') +
                                ' | Vehicle: ' + vehicle +
                                ' | Driver: ' + (item.driver_name || '—') +
                                (item.dr_code ? ' | DR: ' + item.dr_code + ' (' + (item.dr_status || '—') + ')' : '') + '</div>';
                        html += '</div>';
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-4 text-muted">' +
                           '<i class="bi bi-inbox" style="font-size:48px;opacity:0.3;"></i>' +
                           '<h5 class="mt-3">No delivery history yet</h5>' +
                           '<p>This helper has no completed trips.</p></div>';
                }

                $('#historyContent').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Helper History Error:', status, error, xhr.responseText);
                $('#historyContent').html(
                    '<div class="text-center py-4 text-danger">' +
                    '<i class="bi bi-exclamation-triangle" style="font-size:48px;opacity:0.5;"></i>' +
                    '<h5 class="mt-3">Failed to load history</h5>' +
                    '<p style="font-size:12px;">' + error + '</p></div>'
                );
            }
        });
    };
}

var deleteId = null;
var deleteName = '';

$(document).ready(function() {
    console.log('Document ready');
});