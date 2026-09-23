var __Drivers = new __Drivers();

function __Drivers() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Drivers initialized, URL: ' + mesiteurl);

    // ==============================
    // SAVE DRIVER - MODAL VERSION
    // ==============================
    this.__saveDriverModal = function() {
        var driver_name = $('#form_driver_name').val();
        var contact_number = $('#form_contact_number').val();
        var address = $('#form_address').val();
        var employment_type = $('#form_employment_type').val();
        var date_hired = $('#form_date_hired').val();
        var driver_status = $('#form_driver_status').val();
        var emergency_contact = $('#form_emergency_contact').val();
        var emergency_contact_number = $('#form_emergency_contact_number').val();
        
        var years_experience = $('#form_years_experience').val();
        var heavy_vehicle_experience = $('#form_heavy_vehicle_experience').val();
        var tractor_head_experience = $('#form_tractor_head_experience').val();
        var ten_wheeler_experience = $('#form_ten_wheeler_experience').val();
        var long_distance_experience = $('#form_long_distance_experience').val();
        var city_urban_experience = $('#form_city_urban_experience').val();
        var highway_experience = $('#form_highway_experience').val();
        var route_experience = $('#form_route_experience').val();
        var cargo_handling_experience = $('#form_cargo_handling_experience').val();
        var defensive_driving_training = $('#form_defensive_driving_training').val();
        var safety_training = $('#form_safety_training').val();
        var other_certifications = $('#form_other_certifications').val();
        var training_expiration_date = $('#form_training_expiration_date').val();
        var qualification_remarks = $('#form_qualification_remarks').val();
        
        var overall_rating = $('#form_overall_rating').val();
        var rating_date = $('#form_rating_date').val();
        var evaluated_by = $('#form_evaluated_by').val();
        var previous_rating = $('#form_previous_rating').val();
        var evaluation_remarks = $('#form_evaluation_remarks').val();

        // License fields
        var license_number = $('#form_license_number').val();
        var license_type = $('#form_license_type').val();
        var restriction_code = $('#form_restriction_code').val();
        var issue_date = $('#form_issue_date').val();
        var expiration_date = $('#form_expiration_date').val();

        if(!driver_name) {
            toastr.warning('Please enter driver name', 'Missing field');
            $('#form_driver_name').focus();
            return false;
        }

        var formData = new FormData();
        formData.append('driver_name', driver_name);
        formData.append('contact_number', contact_number);
        formData.append('address', address);
        formData.append('employment_type', employment_type);
        formData.append('date_hired', date_hired);
        formData.append('driver_status', driver_status);
        formData.append('emergency_contact', emergency_contact);
        formData.append('emergency_contact_number', emergency_contact_number);
        formData.append('years_experience', years_experience);
        formData.append('heavy_vehicle_experience', heavy_vehicle_experience);
        formData.append('tractor_head_experience', tractor_head_experience);
        formData.append('ten_wheeler_experience', ten_wheeler_experience);
        formData.append('long_distance_experience', long_distance_experience);
        formData.append('city_urban_experience', city_urban_experience);
        formData.append('highway_experience', highway_experience);
        formData.append('route_experience', route_experience);
        formData.append('cargo_handling_experience', cargo_handling_experience);
        formData.append('defensive_driving_training', defensive_driving_training);
        formData.append('safety_training', safety_training);
        formData.append('other_certifications', other_certifications);
        formData.append('training_expiration_date', training_expiration_date);
        formData.append('qualification_remarks', qualification_remarks);
        formData.append('overall_rating', overall_rating);
        formData.append('rating_date', rating_date);
        formData.append('evaluated_by', evaluated_by);
        formData.append('previous_rating', previous_rating);
        formData.append('evaluation_remarks', evaluation_remarks);
        formData.append('license_number', license_number);
        formData.append('license_type', license_type);
        formData.append('restriction_code', restriction_code);
        formData.append('issue_date', issue_date);
        formData.append('expiration_date', expiration_date);
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
            url: mesiteurl + 'fms-drivers',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('driverModal'));
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
    // UPDATE DRIVER - MODAL VERSION
    // ==============================
    this.__updateDriverModal = function() {
        var driver_id = $('#form_driver_id').val();
        var driver_name = $('#form_driver_name').val();
        var contact_number = $('#form_contact_number').val();
        var address = $('#form_address').val();
        var employment_type = $('#form_employment_type').val();
        var date_hired = $('#form_date_hired').val();
        var driver_status = $('#form_driver_status').val();
        var emergency_contact = $('#form_emergency_contact').val();
        var emergency_contact_number = $('#form_emergency_contact_number').val();
        
        var years_experience = $('#form_years_experience').val();
        var heavy_vehicle_experience = $('#form_heavy_vehicle_experience').val();
        var tractor_head_experience = $('#form_tractor_head_experience').val();
        var ten_wheeler_experience = $('#form_ten_wheeler_experience').val();
        var long_distance_experience = $('#form_long_distance_experience').val();
        var city_urban_experience = $('#form_city_urban_experience').val();
        var highway_experience = $('#form_highway_experience').val();
        var route_experience = $('#form_route_experience').val();
        var cargo_handling_experience = $('#form_cargo_handling_experience').val();
        var defensive_driving_training = $('#form_defensive_driving_training').val();
        var safety_training = $('#form_safety_training').val();
        var other_certifications = $('#form_other_certifications').val();
        var training_expiration_date = $('#form_training_expiration_date').val();
        var qualification_remarks = $('#form_qualification_remarks').val();
        
        var overall_rating = $('#form_overall_rating').val();
        var rating_date = $('#form_rating_date').val();
        var evaluated_by = $('#form_evaluated_by').val();
        var previous_rating = $('#form_previous_rating').val();
        var evaluation_remarks = $('#form_evaluation_remarks').val();

        // License fields
        var license_number = $('#form_license_number').val();
        var license_type = $('#form_license_type').val();
        var restriction_code = $('#form_restriction_code').val();
        var issue_date = $('#form_issue_date').val();
        var expiration_date = $('#form_expiration_date').val();

        if(!driver_name) {
            toastr.warning('Please enter driver name', 'Missing field');
            $('#form_driver_name').focus();
            return false;
        }

        var formData = new FormData();
        formData.append('driver_id', driver_id);
        formData.append('driver_name', driver_name);
        formData.append('contact_number', contact_number);
        formData.append('address', address);
        formData.append('employment_type', employment_type);
        formData.append('date_hired', date_hired);
        formData.append('driver_status', driver_status);
        formData.append('emergency_contact', emergency_contact);
        formData.append('emergency_contact_number', emergency_contact_number);
        formData.append('years_experience', years_experience);
        formData.append('heavy_vehicle_experience', heavy_vehicle_experience);
        formData.append('tractor_head_experience', tractor_head_experience);
        formData.append('ten_wheeler_experience', ten_wheeler_experience);
        formData.append('long_distance_experience', long_distance_experience);
        formData.append('city_urban_experience', city_urban_experience);
        formData.append('highway_experience', highway_experience);
        formData.append('route_experience', route_experience);
        formData.append('cargo_handling_experience', cargo_handling_experience);
        formData.append('defensive_driving_training', defensive_driving_training);
        formData.append('safety_training', safety_training);
        formData.append('other_certifications', other_certifications);
        formData.append('training_expiration_date', training_expiration_date);
        formData.append('qualification_remarks', qualification_remarks);
        formData.append('overall_rating', overall_rating);
        formData.append('rating_date', rating_date);
        formData.append('evaluated_by', evaluated_by);
        formData.append('previous_rating', previous_rating);
        formData.append('evaluation_remarks', evaluation_remarks);
        formData.append('license_number', license_number);
        formData.append('license_type', license_type);
        formData.append('restriction_code', restriction_code);
        formData.append('issue_date', issue_date);
        formData.append('expiration_date', expiration_date);
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
            url: mesiteurl + 'fms-drivers',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('driverModal'));
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
    // DELETE DRIVER
    // ==============================
    this.__deleteDriver = function() {
        if(deleteId) {
            var mparam = {
                driver_id: deleteId,
                meaction: 'DELETE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-drivers',
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
    // VIEW SKILLSETS
    // ==============================
    this.__viewSkillsets = function(driver_id, driver_name) {
        $('#sk_driver_id').val(driver_id);
        $('#sk_driver_name').text(driver_name);
        
        $('#sk_skill_name').val('');
        $('#sk_rating').val('3');
        $('#sk_rating_date').val(new Date().toISOString().split('T')[0]);
        $('#sk_evaluated_by').val('');
        $('#sk_remarks').val('');
        
        var btn = $('#skActionBtn');
        btn.html('<i class="bi bi-plus"></i> Add');
        btn.attr('onclick', '__Drivers.__saveSkillset()');
        btn.removeClass('btn-warning').addClass('btn-primary');
        
        this.__loadSkillsets(driver_id);
        
        var modal = new bootstrap.Modal(document.getElementById('skillsetsModal'));
        modal.show();
    };

    // ==============================
    // LOAD SKILLSETS
    // ==============================
    this.__loadSkillsets = function(driver_id) {
        var mparam = {
            driver_id: driver_id,
            meaction: 'GET_SKILLSETS'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-drivers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data.length > 0) {
                    $.each(data, function(index, row) {
                        var ratingStars = '';
                        for(var i = 1; i <= 5; i++) {
                            ratingStars += '<span class="star' + (i <= row.rating ? ' filled' : '') + '">★</span>';
                        }
                        
                        html += '<tr>';
                        html += '<td><strong>' + row.skill_name + '</strong></td>';
                        html += '<td><div class="rating-stars">' + ratingStars + ' ' + row.rating + '/5</div></td>';
                        html += '<td>' + (row.rating_date || '—') + '</td>';
                        html += '<td>' + (row.evaluated_by || '—') + '</td>';
                        html += '<td>' + (row.remarks || '—') + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__Drivers.__editSkillset(' + row.skillset_id + ')" title="Edit">';
                        html += '<i class="bi bi-pencil"></i>';
                        html += '</button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__Drivers.__deleteSkillset(' + row.skillset_id + ')" title="Delete">';
                        html += '<i class="bi bi-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No skills rated yet</td></tr>';
                }
                $('#skillsetBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading skillsets: " + error);
            }
        });
    };

    // ==============================
    // SAVE SKILLSET
    // ==============================
    this.__saveSkillset = function() {
        var driver_id = $('#sk_driver_id').val();
        var skill_name = $('#sk_skill_name').val();
        var rating = $('#sk_rating').val();
        var rating_date = $('#sk_rating_date').val();
        var evaluated_by = $('#sk_evaluated_by').val();
        var remarks = $('#sk_remarks').val();

        if(!skill_name) {
            toastr.warning('Please select a skill', 'Missing field');
            $('#sk_skill_name').focus();
            return;
        }

        var mparam = {
            driver_id: driver_id,
            skill_name: skill_name,
            rating: rating,
            rating_date: rating_date,
            evaluated_by: evaluated_by,
            remarks: remarks,
            meaction: 'SAVE_SKILLSET'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-drivers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Drivers.__loadSkillsets(driver_id);
                    $('#sk_skill_name').val('');
                    $('#sk_rating').val('3');
                    $('#sk_evaluated_by').val('');
                    $('#sk_remarks').val('');
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
    // EDIT SKILLSET
    // ==============================
    this.__editSkillset = function(skillset_id) {
        var mparam = {
            skillset_id: skillset_id,
            meaction: 'GET_SKILLSET'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-drivers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#sk_skill_name').val(data.skill_name);
                    $('#sk_rating').val(data.rating);
                    $('#sk_rating_date').val(data.rating_date);
                    $('#sk_evaluated_by').val(data.evaluated_by);
                    $('#sk_remarks').val(data.remarks);
                    
                    var btn = $('#skActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> Update');
                    btn.attr('onclick', '__Drivers.__updateSkillset(' + skillset_id + ')');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading skillset: " + error);
            }
        });
    };

    // ==============================
    // UPDATE SKILLSET
    // ==============================
    this.__updateSkillset = function(skillset_id) {
        var driver_id = $('#sk_driver_id').val();
        var skill_name = $('#sk_skill_name').val();
        var rating = $('#sk_rating').val();
        var rating_date = $('#sk_rating_date').val();
        var evaluated_by = $('#sk_evaluated_by').val();
        var remarks = $('#sk_remarks').val();

        if(!skill_name) {
            toastr.warning('Please select a skill', 'Missing field');
            $('#sk_skill_name').focus();
            return;
        }

        var mparam = {
            skillset_id: skillset_id,
            skill_name: skill_name,
            rating: rating,
            rating_date: rating_date,
            evaluated_by: evaluated_by,
            remarks: remarks,
            meaction: 'EDIT_SKILLSET'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-drivers',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Drivers.__loadSkillsets(driver_id);
                    
                    var btn = $('#skActionBtn');
                    btn.html('<i class="bi bi-plus"></i> Add');
                    btn.attr('onclick', '__Drivers.__saveSkillset()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    
                    $('#sk_skill_name').val('');
                    $('#sk_rating').val('3');
                    $('#sk_evaluated_by').val('');
                    $('#sk_remarks').val('');
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
    // DELETE SKILLSET
    // ==============================
    this.__deleteSkillset = function(skillset_id) {
        if(confirm('Are you sure you want to delete this skillset rating?')) {
            var driver_id = $('#sk_driver_id').val();
            
            var mparam = {
                skillset_id: skillset_id,
                meaction: 'DELETE_SKILLSET'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-drivers',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Drivers.__loadSkillsets(driver_id);
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
        document.getElementById('delete_driver_name').innerHTML = name;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };

    // ==========================================
    // DELIVERY HISTORY
    // ==========================================
    this.__openHistoryModal = function(driver_id, driver_name) {
        $('#history_driver_id').val(driver_id);
        $('#history_driver_name').text(driver_name);
        $('#historyContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');

        var modal = new bootstrap.Modal(document.getElementById('historyModal'));
        modal.show();

        var mparam = { driver_id: driver_id, meaction: 'GET_DRIVER_HISTORY' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-drivers',
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
                                ' | Helper: ' + (item.helper_name || '—') +
                                (item.dr_code ? ' | DR: ' + item.dr_code + ' (' + (item.dr_status || '—') + ')' : '') + '</div>';
                        html += '</div>';
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-4 text-muted">' +
                           '<i class="bi bi-inbox" style="font-size:48px;opacity:0.3;"></i>' +
                           '<h5 class="mt-3">No delivery history yet</h5>' +
                           '<p>This driver has no completed trips.</p></div>';
                }

                $('#historyContent').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Driver History Error:', status, error, xhr.responseText);
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