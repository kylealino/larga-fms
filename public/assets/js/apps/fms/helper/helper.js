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
}

var deleteId = null;
var deleteName = '';

$(document).ready(function() {
    console.log('Document ready');
});