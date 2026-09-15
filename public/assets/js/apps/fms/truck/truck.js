var __Trucks = new __Trucks();

function __Trucks() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Trucks initialized, URL: ' + mesiteurl);

    // ==============================
    // SAVE TRUCK
    // ==============================
    this.__saveTruck = function() {
        var plate_number = $('#form_plate_number').val();
        
        if(!plate_number) {
            toastr.warning('Please enter plate number', 'Missing field');
            $('#form_plate_number').focus();
            return false;
        }

        var formData = new FormData();
        formData.append('vehicle_config', $('#form_vehicle_config').val());
        formData.append('plate_number', plate_number);
        formData.append('mv_file_number', $('#form_mv_file_number').val());
        formData.append('vehicle_type', $('#form_vehicle_type').val());
        formData.append('body_type', $('#form_body_type').val());
        formData.append('make', $('#form_make').val());
        formData.append('model', $('#form_model').val());
        formData.append('model_year', $('#form_model_year').val());
        formData.append('chassis_number', $('#form_chassis_number').val());
        formData.append('engine_number', $('#form_engine_number').val());
        formData.append('fuel_type', $('#form_fuel_type').val());
        formData.append('fuel_tank_capacity', $('#form_fuel_tank_capacity').val());
        formData.append('load_capacity', $('#form_load_capacity').val());
        formData.append('current_odometer', $('#form_current_odometer').val());
        formData.append('acquisition_date', $('#form_acquisition_date').val());
        formData.append('acquired_from', $('#form_acquired_from').val());
        formData.append('acquired_from_branch', $('#form_acquired_from_branch').val());
        formData.append('account_manager', $('#form_account_manager').val());
        formData.append('ownership', $('#form_ownership').val());
        formData.append('truck_status', $('#form_truck_status').val());
        formData.append('remarks', $('#form_remarks').val());
        formData.append('meaction', 'SAVE');

        // Add truck image if selected
        var imageFile = document.getElementById('form_truck_image');
        if(imageFile && imageFile.files.length > 0) {
            formData.append('truck_image', imageFile.files[0]);
        }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trucks',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('truckModal'));
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
    // UPDATE TRUCK
    // ==============================
    this.__updateTruck = function() {
        var truck_id = $('#form_truck_id').val();
        var plate_number = $('#form_plate_number').val();

        if(!plate_number) {
            toastr.warning('Please enter plate number', 'Missing field');
            $('#form_plate_number').focus();
            return false;
        }

        var formData = new FormData();
        formData.append('truck_id', truck_id);
        formData.append('vehicle_config', $('#form_vehicle_config').val());
        formData.append('plate_number', plate_number);
        formData.append('mv_file_number', $('#form_mv_file_number').val());
        formData.append('vehicle_type', $('#form_vehicle_type').val());
        formData.append('body_type', $('#form_body_type').val());
        formData.append('make', $('#form_make').val());
        formData.append('model', $('#form_model').val());
        formData.append('model_year', $('#form_model_year').val());
        formData.append('chassis_number', $('#form_chassis_number').val());
        formData.append('engine_number', $('#form_engine_number').val());
        formData.append('fuel_type', $('#form_fuel_type').val());
        formData.append('fuel_tank_capacity', $('#form_fuel_tank_capacity').val());
        formData.append('load_capacity', $('#form_load_capacity').val());
        formData.append('current_odometer', $('#form_current_odometer').val());
        formData.append('acquisition_date', $('#form_acquisition_date').val());
        formData.append('acquired_from', $('#form_acquired_from').val());
        formData.append('acquired_from_branch', $('#form_acquired_from_branch').val());
        formData.append('account_manager', $('#form_account_manager').val());
        formData.append('ownership', $('#form_ownership').val());
        formData.append('truck_status', $('#form_truck_status').val());
        formData.append('remarks', $('#form_remarks').val());
        formData.append('meaction', 'EDIT');

        // Add truck image if selected
        var imageFile = document.getElementById('form_truck_image');
        if(imageFile && imageFile.files.length > 0) {
            formData.append('truck_image', imageFile.files[0]);
        }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trucks',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('truckModal'));
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
    // DELETE TRUCK
    // ==============================
    this.__deleteTruck = function() {
        if(deleteId) {
            var mparam = {
                truck_id: deleteId,
                meaction: 'DELETE'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-trucks',
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
        document.getElementById('delete_truck_name').innerHTML = name;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };

    // ==============================
    // VIEW DOCUMENTS
    // ==============================
    this.__viewDocuments = function(truck_id, plate_number) {
        $('#doc_truck_id').val(truck_id);
        $('#doc_truck_plate').text(plate_number);
        
        // Reset document form
        $('#doc_document_type').val('');
        $('#doc_document_number').val('');
        $('#doc_issue_date').val('');
        $('#doc_expiration_date').val('');
        $('#doc_provider_name').val('');
        $('#doc_policy_number').val('');
        $('#doc_coverage_type').val('');
        $('#doc_premium').val('');
        $('#doc_coverage_amount').val('');
        $('#doc_rate').val('');
        $('#doc_remarks').val('');
        $('#doc_attachment').val('');
        $('#doc_existing_attachment').val('');
        $('#doc_editing_id').val('');
        $('#doc_attachment_preview').hide();
        $('#insuranceFields').hide();
        
        // Reset button to Add mode
        var btn = $('#docActionBtn');
        btn.html('<i class="bi bi-plus"></i> <span id="docBtnText">Add</span>');
        btn.attr('onclick', '__Trucks.__saveDocument()');
        btn.removeClass('btn-warning').addClass('btn-primary');
        
        // Load documents
        this.__loadDocuments(truck_id);
        
        var modal = new bootstrap.Modal(document.getElementById('documentsModal'));
        modal.show();
    };

    // ==============================
    // LOAD DOCUMENTS
    // ==============================
    this.__loadDocuments = function(truck_id) {
        var mparam = {
            truck_id: truck_id,
            meaction: 'GET_DOCUMENTS'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trucks',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                if(data.length > 0) {
                    $.each(data, function(index, row) {
                        var statusBadge = row.document_status == 'VALID' ? 
                            '<span class="badge badge-success">Valid</span>' : 
                            (row.document_status == 'EXPIRING' ? '<span class="badge badge-warning">Expiring</span>' : '<span class="badge badge-danger">Expired</span>');
                        
                        var docType = row.document_type.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                        
                        // Attachment preview
                        var attachmentHtml = '<span class="text-muted">No file</span>';
                        if(row.document_attachment) {
                            var fileUrl = mesiteurl + '/' + row.document_attachment;
                            var fileName = row.document_attachment.split('/').pop();
                            var fileExt = fileName.split('.').pop().toLowerCase();
                            if(['jpg','jpeg','png','gif','webp'].includes(fileExt)) {
                                attachmentHtml = '<a href="javascript:void(0)" onclick="openDocZoom(\'' + fileUrl + '\')"><img src="' + fileUrl + '" style="width:40px;height:40px;object-fit:cover;border-radius:4px;border:1px solid var(--gray-200);"></a>';
                            } else if(fileExt == 'pdf') {
                                attachmentHtml = '<a href="javascript:void(0)" onclick="openDocZoom(\'' + fileUrl + '\')"><i class="bi bi-file-pdf" style="font-size:24px;color:var(--danger);"></i></a>';
                            } else {
                                attachmentHtml = '<a href="javascript:void(0)" onclick="window.open(\'' + fileUrl + '\', \'_blank\')"><i class="bi bi-file-earmark" style="font-size:24px;color:var(--gray-400);"></i></a>';
                            }
                        }
                        
                        html += '<tr>';
                        html += '<td><strong>' + docType + '</strong></td>';
                        html += '<td>' + (row.document_number || '—') + '</td>';
                        html += '<td>' + (row.issue_date || '—') + '</td>';
                        html += '<td>' + (row.expiration_date || '—') + '</td>';
                        html += '<td>' + statusBadge + '</td>';
                        html += '<td>' + attachmentHtml + '</td>';
                        html += '<td class="text-center">';
                        html += '<div class="action-group">';
                        html += '<button class="btn-icon btn-icon-edit" onclick="__Trucks.__editDocument(' + row.document_id + ')" title="Edit">';
                        html += '<i class="bi bi-pencil"></i>';
                        html += '</button>';
                        html += '<button class="btn-icon btn-icon-delete" onclick="__Trucks.__deleteDocument(' + row.document_id + ')" title="Delete">';
                        html += '<i class="bi bi-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center text-muted">No documents found</td></tr>';
                }
                $('#documentsBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading documents: " + error);
            }
        });
    };

    // ==============================
    // SAVE DOCUMENT
    // ==============================
    this.__saveDocument = function() {
        var truck_id = $('#doc_truck_id').val();
        var document_type = $('#doc_document_type').val();
        var document_number = $('#doc_document_number').val();
        var issue_date = $('#doc_issue_date').val();
        var expiration_date = $('#doc_expiration_date').val();
        var provider_name = $('#doc_provider_name').val();
        var policy_number = $('#doc_policy_number').val();
        var coverage_type = $('#doc_coverage_type').val();
        var premium = $('#doc_premium').val();
        var coverage_amount = $('#doc_coverage_amount').val();
        var rate = $('#doc_rate').val();
        var remarks = $('#doc_remarks').val();

        if(!document_type) {
            toastr.warning('Please select document type', 'Missing field');
            $('#doc_document_type').focus();
            return;
        }

        var formData = new FormData();
        formData.append('truck_id', truck_id);
        formData.append('document_type', document_type);
        formData.append('document_number', document_number);
        formData.append('issue_date', issue_date);
        formData.append('expiration_date', expiration_date);
        formData.append('provider_name', provider_name);
        formData.append('policy_number', policy_number);
        formData.append('coverage_type', coverage_type);
        formData.append('premium', premium);
        formData.append('coverage_amount', coverage_amount);
        formData.append('rate', rate);
        formData.append('remarks', remarks);
        formData.append('meaction', 'SAVE_DOCUMENT');

        // Add attachment if selected
        var fileInput = document.getElementById('doc_attachment');
        if(fileInput && fileInput.files.length > 0) {
            formData.append('document_attachment', fileInput.files[0]);
        }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trucks',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Trucks.__loadDocuments(truck_id);
                    // Clear form
                    $('#doc_document_type').val('');
                    $('#doc_document_number').val('');
                    $('#doc_issue_date').val('');
                    $('#doc_expiration_date').val('');
                    $('#doc_provider_name').val('');
                    $('#doc_policy_number').val('');
                    $('#doc_coverage_type').val('');
                    $('#doc_premium').val('');
                    $('#doc_coverage_amount').val('');
                    $('#doc_rate').val('');
                    $('#doc_remarks').val('');
                    $('#doc_attachment').val('');
                    $('#doc_existing_attachment').val('');
                    $('#doc_editing_id').val('');
                    $('#doc_attachment_preview').hide();
                    $('#insuranceFields').hide();
                    
                    // Reset button to Add mode
                    var btn = $('#docActionBtn');
                    btn.html('<i class="bi bi-plus"></i> <span id="docBtnText">Add</span>');
                    btn.attr('onclick', '__Trucks.__saveDocument()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
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
    // EDIT DOCUMENT
    // ==============================
    this.__editDocument = function(document_id) {
        var mparam = {
            document_id: document_id,
            meaction: 'GET_DOCUMENT'
        };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trucks',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $('#doc_document_type').val(data.document_type);
                    $('#doc_document_number').val(data.document_number);
                    $('#doc_issue_date').val(data.issue_date);
                    $('#doc_expiration_date').val(data.expiration_date);
                    $('#doc_provider_name').val(data.provider_name);
                    $('#doc_policy_number').val(data.policy_number);
                    $('#doc_coverage_type').val(data.coverage_type);
                    $('#doc_premium').val(data.premium);
                    $('#doc_coverage_amount').val(data.coverage_amount);
                    $('#doc_rate').val(data.rate);
                    $('#doc_remarks').val(data.remarks);
                    $('#doc_editing_id').val(document_id);
                    
                    // Show insurance fields if document type is insurance
                    if(data.document_type == 'INSURANCE') {
                        $('#insuranceFields').show();
                    } else {
                        $('#insuranceFields').hide();
                    }
                    
                    // Show current attachment if exists
                    if(data.document_attachment) {
                        var fileName = data.document_attachment.split('/').pop();
                        $('#doc_attachment_preview').show();
                        $('#doc_attachment_preview').html('<span class="badge badge-info"><i class="bi bi-file-earmark"></i> Current: ' + fileName + ' <button type="button" class="btn-close btn-close-sm" onclick="$(\'#doc_attachment_preview\').hide(); $(\'#doc_attachment\').val(\'\');" style="font-size:10px;"></button></span>');
                        $('#doc_existing_attachment').val(data.document_attachment);
                    } else {
                        $('#doc_attachment_preview').hide();
                        $('#doc_existing_attachment').val('');
                    }
                    
                    // Change button to update
                    var btn = $('#docActionBtn');
                    btn.html('<i class="bi bi-pencil"></i> <span id="docBtnText">Update</span>');
                    btn.attr('onclick', '__Trucks.__updateDocument(' + document_id + ')');
                    btn.removeClass('btn-primary').addClass('btn-warning');
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading document: " + error);
            }
        });
    };

    // ==============================
    // UPDATE DOCUMENT
    // ==============================
    this.__updateDocument = function(document_id) {
        var truck_id = $('#doc_truck_id').val();
        var document_type = $('#doc_document_type').val();
        var document_number = $('#doc_document_number').val();
        var issue_date = $('#doc_issue_date').val();
        var expiration_date = $('#doc_expiration_date').val();
        var provider_name = $('#doc_provider_name').val();
        var policy_number = $('#doc_policy_number').val();
        var coverage_type = $('#doc_coverage_type').val();
        var premium = $('#doc_premium').val();
        var coverage_amount = $('#doc_coverage_amount').val();
        var rate = $('#doc_rate').val();
        var remarks = $('#doc_remarks').val();

        if(!document_type) {
            toastr.warning('Please select document type', 'Missing field');
            $('#doc_document_type').focus();
            return;
        }

        var formData = new FormData();
        formData.append('document_id', document_id);
        formData.append('truck_id', truck_id);
        formData.append('document_type', document_type);
        formData.append('document_number', document_number);
        formData.append('issue_date', issue_date);
        formData.append('expiration_date', expiration_date);
        formData.append('provider_name', provider_name);
        formData.append('policy_number', policy_number);
        formData.append('coverage_type', coverage_type);
        formData.append('premium', premium);
        formData.append('coverage_amount', coverage_amount);
        formData.append('rate', rate);
        formData.append('remarks', remarks);
        formData.append('meaction', 'EDIT_DOCUMENT');

        // Add attachment if selected (new file)
        var fileInput = document.getElementById('doc_attachment');
        if(fileInput && fileInput.files.length > 0) {
            formData.append('document_attachment', fileInput.files[0]);
        }

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'fms-trucks',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    __Trucks.__loadDocuments(truck_id);
                    
                    // Reset button back to Add
                    var btn = $('#docActionBtn');
                    btn.html('<i class="bi bi-plus"></i> <span id="docBtnText">Add</span>');
                    btn.attr('onclick', '__Trucks.__saveDocument()');
                    btn.removeClass('btn-warning').addClass('btn-primary');
                    
                    // Clear form
                    $('#doc_document_type').val('');
                    $('#doc_document_number').val('');
                    $('#doc_issue_date').val('');
                    $('#doc_expiration_date').val('');
                    $('#doc_provider_name').val('');
                    $('#doc_policy_number').val('');
                    $('#doc_coverage_type').val('');
                    $('#doc_premium').val('');
                    $('#doc_coverage_amount').val('');
                    $('#doc_rate').val('');
                    $('#doc_remarks').val('');
                    $('#doc_attachment').val('');
                    $('#doc_existing_attachment').val('');
                    $('#doc_editing_id').val('');
                    $('#doc_attachment_preview').hide();
                    $('#insuranceFields').hide();
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
    // DELETE DOCUMENT
    // ==============================
    this.__deleteDocument = function(document_id) {
        if(confirm('Are you sure you want to delete this document?')) {
            var truck_id = $('#doc_truck_id').val();
            
            var mparam = {
                document_id: document_id,
                meaction: 'DELETE_DOCUMENT'
            };

            jQuery.ajax({
                type: "POST",
                url: mesiteurl + 'fms-trucks',
                data: mparam,
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'success'){
                        toastr.success(data.message);
                        __Trucks.__loadDocuments(truck_id);
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