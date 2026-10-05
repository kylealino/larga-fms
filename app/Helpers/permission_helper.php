<?php

// ==============================
// LOAD PERMISSIONS FOR A ROLE INTO SESSION-READY ARRAY
// module_key => ['view'=>0/1, 'add'=>0/1, 'edit'=>0/1, 'delete'=>0/1]
// ==============================
function load_role_permissions($role_id)
{
    $db = \Config\Database::connect();
    $perms = [];

    // LEFT JOIN from tbl_modules (not tbl_role_permissions) so every registered
    // module_key ends up in the map with explicit 0 flags when the role has no
    // permission row for it yet (or there is no role at all, $role_id null) —
    // otherwise an unassigned module would be missing from the map entirely and
    // user_can() would fail OPEN for it instead of denying.
    $rows = $db->query("
        SELECT m.module_key,
               COALESCE(p.can_view, 0) AS can_view,
               COALESCE(p.can_add, 0) AS can_add,
               COALESCE(p.can_edit, 0) AS can_edit,
               COALESCE(p.can_delete, 0) AS can_delete
        FROM tbl_modules m
        LEFT JOIN tbl_role_permissions p ON p.module_id = m.module_id AND p.role_id = ?
        WHERE m.module_status = 'ACTIVE'
    ", [$role_id])->getResultArray();

    foreach ($rows as $row) {
        $perms[$row['module_key']] = [
            'view'   => (bool) $row['can_view'],
            'add'    => (bool) $row['can_add'],
            'edit'   => (bool) $row['can_edit'],
            'delete' => (bool) $row['can_delete'],
        ];
    }

    return $perms;
}

// ==============================
// CHECK IF THE LOGGED-IN USER CAN DO $type ON $module_key
// Looked up live from the DB (role_id only is kept in session), cached in a
// static var so a page with many checks (sidebar, buttons) only queries once
// per request. This means a permission change takes effect on the very next
// page load for every affected user — no re-login required.
// Modules not present in the permissions map are treated as unrestricted
// (utility/legacy routes that predate the RBAC module registry, e.g. the
// dashboard). Every module registered in tbl_modules IS present in the map
// (see load_role_permissions), so this only fails open for those.
// ==============================
function user_can($module_key, $type = 'view')
{
    static $perms = null;

    if ($perms === null) {
        $perms = load_role_permissions(session('__xsys_myuserrole__'));
    }

    if (!array_key_exists($module_key, $perms)) {
        return true;
    }

    return !empty($perms[$module_key][$type]);
}

// ==============================
// LOAD DASHBOARD WIDGET VISIBILITY FOR A ROLE
// widget_key => bool. Unlike module permissions this is a personalization
// setting, not a security boundary: a widget with no explicit per-role row
// falls back to the widget's own default_visible flag rather than denying.
// ==============================
function load_dashboard_widgets($role_id)
{
    $db = \Config\Database::connect();
    $widgets = [];

    $rows = $db->query("
        SELECT w.widget_key, COALESCE(r.is_visible, w.default_visible) AS is_visible
        FROM tbl_dashboard_widgets w
        LEFT JOIN tbl_role_dashboard_widgets r ON r.widget_id = w.widget_id AND r.role_id = ?
        WHERE w.widget_status = 'ACTIVE'
        ORDER BY w.sort_order ASC
    ", [$role_id])->getResultArray();

    foreach ($rows as $row) {
        $widgets[$row['widget_key']] = (bool) $row['is_visible'];
    }

    return $widgets;
}

// ==============================
// CHECK IF A DASHBOARD WIDGET SHOULD RENDER FOR THE LOGGED-IN USER'S ROLE
// Cached per-request like user_can() — a saved customization applies on the
// next page load, no re-login required.
// ==============================
function widget_visible($widget_key)
{
    static $widgets = null;

    if ($widgets === null) {
        $widgets = load_dashboard_widgets(session('__xsys_myuserrole__'));
    }

    return $widgets[$widget_key] ?? true;
}
