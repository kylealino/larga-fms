var __UserManagement = new __UserManagement();

function __UserManagement() {
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    // ==============================
    // TABS
    // ==============================
    this.__switchTab = function(tabId, btnEl) {
        $('.lrg-tab-pane').removeClass('active');
        $('#' + tabId).addClass('active');
        $('.lrg-tab-btn').removeClass('active');
        $(btnEl).addClass('active');
    };

    // ==============================
    // ROLES DROPDOWN (shared by add/edit user modal)
    // ==============================
    this.__loadRolesDropdown = function(selectedRoleId, done) {
        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: { meaction: 'GET_ROLES_DROPDOWN' },
            dataType: 'json',
            success: function(data) {
                var html = '<option value="">— No Role —</option>';
                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        html += '<option value="' + row.role_id + '">' + row.role_name + '</option>';
                    });
                }
                $('#user_role_id').html(html);
                if (selectedRoleId) { $('#user_role_id').val(selectedRoleId); }
                if (typeof done === 'function') { done(); }
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading roles: " + error);
            }
        });
    };

    // ==============================
    // USERS — ADD / EDIT
    // ==============================
    this.__resetUserForm = function() {
        $('#user_recid').val('');
        $('#user_username').val('').prop('disabled', false);
        $('#user_password').val('');
        $('#user_full_name').val('');
        $('#user_division').val('');
        $('#user_section').val('');
        $('#user_position').val('');
        $('#user_is_active').val('1');
    };

    this.__openAddUser = function() {
        __UserManagement.__resetUserForm();
        $('#userModalTitle').html('<i class="bi bi-person-plus me-2"></i>New User');
        $('#user_password_required').show();
        $('#user_password_hint').hide();
        $('#user_password').attr('placeholder', '');

        __UserManagement.__loadRolesDropdown(null, function() {
            var modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        });
    };

    this.__openEditUser = function(recid) {
        var mparam = { recid: recid, meaction: 'GET_USER' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (!data) {
                    toastr.error('User not found.');
                    return;
                }
                __UserManagement.__resetUserForm();
                $('#userModalTitle').html('<i class="bi bi-pencil-square me-2"></i>Edit User');
                $('#user_password_required').hide();
                $('#user_password_hint').show();
                $('#user_password').attr('placeholder', 'Leave blank to keep current password');

                $('#user_recid').val(data.recid);
                $('#user_username').val(data.username);
                $('#user_full_name').val(data.full_name);
                $('#user_division').val(data.division);
                $('#user_section').val(data.section);
                $('#user_position').val(data.position);
                $('#user_is_active').val(String(data.is_active));

                __UserManagement.__loadRolesDropdown(data.role_id, function() {
                    var modal = new bootstrap.Modal(document.getElementById('userModal'));
                    modal.show();
                });
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading user: " + error);
            }
        });
    };

    this.__saveUser = function() {
        var recid = $('#user_recid').val();
        var username = $('#user_username').val().trim();
        var password = $('#user_password').val();
        var full_name = $('#user_full_name').val().trim();

        if (!username || !full_name) {
            toastr.warning('Username and full name are required', 'Missing field');
            return;
        }
        if (!recid && !password) {
            toastr.warning('Password is required for a new user', 'Missing field');
            $('#user_password').focus();
            return;
        }

        var mparam = {
            recid: recid,
            username: username,
            password: password,
            full_name: full_name,
            division: $('#user_division').val(),
            section: $('#user_section').val(),
            position: $('#user_position').val(),
            role_id: $('#user_role_id').val(),
            is_active: $('#user_is_active').val(),
            meaction: recid ? 'UPDATE_USER' : 'CREATE_USER'
        };

        var btn = $('#userSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save User');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
                    modal.hide();
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save User');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__deleteUser = function(recid, username) {
        $('#delete_title').text('Delete User');
        $('#delete_target_name').text(username);
        $('#confirmDeleteBtn').attr('onclick', '__UserManagement.__confirmDeleteUser(' + recid + ')');
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };

    this.__confirmDeleteUser = function(recid) {
        var mparam = { recid: recid, meaction: 'DELETE_USER' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                    setTimeout(function() { location.reload(); }, 1000);
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
    // ROLES — ADD / EDIT
    // ==============================
    this.__openAddRole = function() {
        $('#role_id').val('');
        $('#role_name').val('');
        $('#role_description').val('');
        $('#role_status').val('ACTIVE');
        $('#roleModalTitle').html('<i class="bi bi-shield-plus me-2"></i>New Role');

        var modal = new bootstrap.Modal(document.getElementById('roleModal'));
        modal.show();
    };

    this.__openEditRole = function(role_id) {
        var mparam = { role_id: role_id, meaction: 'GET_ROLE' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (!data) {
                    toastr.error('Role not found.');
                    return;
                }
                $('#roleModalTitle').html('<i class="bi bi-pencil-square me-2"></i>Edit Role');
                $('#role_id').val(data.role_id);
                $('#role_name').val(data.role_name);
                $('#role_description').val(data.description);
                $('#role_status').val(data.role_status);

                var modal = new bootstrap.Modal(document.getElementById('roleModal'));
                modal.show();
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading role: " + error);
            }
        });
    };

    this.__saveRole = function() {
        var role_id = $('#role_id').val();
        var role_name = $('#role_name').val().trim();

        if (!role_name) {
            toastr.warning('Role name is required', 'Missing field');
            $('#role_name').focus();
            return;
        }

        var mparam = {
            role_id: role_id,
            role_name: role_name,
            description: $('#role_description').val(),
            status: $('#role_status').val(),
            meaction: role_id ? 'UPDATE_ROLE' : 'CREATE_ROLE'
        };

        var btn = $('#roleSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Role');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('roleModal'));
                    modal.hide();
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Role');
                toastr.error("Error: " + error);
            }
        });
    };

    this.__deleteRole = function(role_id, role_name) {
        $('#delete_title').text('Delete Role');
        $('#delete_target_name').text(role_name);
        $('#confirmDeleteBtn').attr('onclick', '__UserManagement.__confirmDeleteRole(' + role_id + ')');
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };

    this.__confirmDeleteRole = function(role_id) {
        var mparam = { role_id: role_id, meaction: 'DELETE_ROLE' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                    setTimeout(function() { location.reload(); }, 1000);
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
    // PERMISSION MATRIX
    // ==============================
    this.__openPermissions = function(role_id, role_name) {
        $('#perm_role_id').val(role_id);
        $('#perm_role_name').text(role_name);
        $('#permMatrixBody').html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');

        var modal = new bootstrap.Modal(document.getElementById('permModal'));
        modal.show();

        var mparam = { role_id: role_id, meaction: 'GET_MODULES_MATRIX' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                var lastGroup = null;

                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        if (row.module_group !== lastGroup) {
                            html += '<tr class="perm-group-row"><td colspan="5">' + row.module_group + '</td></tr>';
                            lastGroup = row.module_group;
                        }
                        html += '<tr data-module-id="' + row.module_id + '">';
                        html += '<td>' + row.module_name + '</td>';
                        html += '<td><input type="checkbox" class="perm-view" ' + (Number(row.can_view) ? 'checked' : '') + '></td>';
                        html += '<td><input type="checkbox" class="perm-add" ' + (Number(row.can_add) ? 'checked' : '') + '></td>';
                        html += '<td><input type="checkbox" class="perm-edit" ' + (Number(row.can_edit) ? 'checked' : '') + '></td>';
                        html += '<td><input type="checkbox" class="perm-delete" ' + (Number(row.can_delete) ? 'checked' : '') + '></td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted">No modules registered</td></tr>';
                }
                $('#permMatrixBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading permissions: " + error);
                $('#permMatrixBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    };

    this.__savePermissions = function() {
        var role_id = $('#perm_role_id').val();
        var permissions = {};

        $('#permMatrixBody tr[data-module-id]').each(function() {
            var moduleId = $(this).data('module-id');
            permissions[moduleId] = {
                view: $(this).find('.perm-view').is(':checked') ? 1 : 0,
                add: $(this).find('.perm-add').is(':checked') ? 1 : 0,
                edit: $(this).find('.perm-edit').is(':checked') ? 1 : 0,
                delete: $(this).find('.perm-delete').is(':checked') ? 1 : 0
            };
        });

        var mparam = {
            role_id: role_id,
            permissions_json: JSON.stringify(permissions),
            meaction: 'SAVE_ROLE_PERMISSIONS'
        };

        var btn = $('#permSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Permissions');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('permModal'));
                    modal.hide();
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Permissions');
                toastr.error("Error: " + error);
            }
        });
    };

    // ==============================
    // DASHBOARD WIDGETS
    // ==============================
    this.__openDashboardWidgets = function(role_id, role_name) {
        $('#widgets_role_id').val(role_id);
        $('#widgets_role_name').text(role_name);
        $('#widgetsListBody').html('<div class="text-center text-muted">Loading...</div>');

        var modal = new bootstrap.Modal(document.getElementById('widgetsModal'));
        modal.show();

        var mparam = { role_id: role_id, meaction: 'GET_DASHBOARD_WIDGETS' };

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                var html = '';
                var lastGroup = null;

                if (data && data.length > 0) {
                    $.each(data, function(i, row) {
                        if (row.widget_group !== lastGroup) {
                            html += '<div class="widget-group-label">' + row.widget_group + '</div>';
                            lastGroup = row.widget_group;
                        }
                        html += '<label class="widget-item" data-widget-id="' + row.widget_id + '">';
                        html += '<input type="checkbox" class="widget-visible" ' + (Number(row.is_visible) ? 'checked' : '') + '>';
                        html += '<span><span class="widget-name d-block">' + row.widget_name + '</span>';
                        if (row.description) { html += '<span class="widget-desc">' + row.description + '</span>'; }
                        html += '</span>';
                        html += '</label>';
                    });
                } else {
                    html = '<div class="text-center text-muted">No widgets registered</div>';
                }
                $('#widgetsListBody').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error("Error loading widgets: " + error);
                $('#widgetsListBody').html('<div class="text-center text-danger">Error loading data</div>');
            }
        });
    };

    this.__saveDashboardWidgets = function() {
        var role_id = $('#widgets_role_id').val();
        var widgets = {};

        $('#widgetsListBody .widget-item').each(function() {
            var widgetId = $(this).data('widget-id');
            widgets[widgetId] = $(this).find('.widget-visible').is(':checked') ? 1 : 0;
        });

        var mparam = {
            role_id: role_id,
            widgets_json: JSON.stringify(widgets),
            meaction: 'SAVE_DASHBOARD_WIDGETS'
        };

        var btn = $('#widgetsSubmitBtn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');

        jQuery.ajax({
            type: "POST",
            url: mesiteurl + 'usermanagement',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Widgets');
                if (data.status == 'success') {
                    toastr.success(data.message);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('widgetsModal'));
                    modal.hide();
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-save"></i> Save Widgets');
                toastr.error("Error: " + error);
            }
        });
    };
}
