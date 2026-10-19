<?php

// ==============================
// ROLE-BASED COLOR PALETTE
// Palettes + layouts live in app/Config/Themes.php, the chosen keys in tbl_roles.theme_palette / sidebar_style / sidebar_layout / header_layout.
// myheader.php prints theme_css_vars() so every page using the shared template follows the role.
// ==============================

// ==============================
// ALL PALETTES (key => palette array)
// ==============================
function theme_palettes()
{
    return config('Themes')->palettes;
}

// ==============================
// ALL SIDEBAR STYLES (key => ['label', 'description'])
// ==============================
function theme_sidebar_styles()
{
    return config('Themes')->sidebarStyles;
}

// ==============================
// WHITELIST A PALETTE KEY — unknown/empty falls back to the default
// ==============================
function theme_palette_key($key)
{
    $key = strtoupper(trim((string) $key));
    return isset(theme_palettes()[$key]) ? $key : config('Themes')->default;
}

// ==============================
// WHITELIST A SIDEBAR STYLE KEY — unknown/empty falls back to the default
// ==============================
function theme_sidebar_key($key)
{
    $key = strtoupper(trim((string) $key));
    return isset(theme_sidebar_styles()[$key]) ? $key : config('Themes')->defaultSidebar;
}

// ==============================
// UI LAYOUTS (key => ['label', 'description']) + WHITELISTS
// ==============================
function theme_sidebar_layouts()
{
    return config('Themes')->sidebarLayouts;
}

function theme_header_layouts()
{
    return config('Themes')->headerLayouts;
}

function theme_sidebar_layout_key($key)
{
    $key = strtoupper(trim((string) $key));
    return isset(theme_sidebar_layouts()[$key]) ? $key : config('Themes')->defaultSidebarLayout;
}

function theme_header_layout_key($key)
{
    $key = strtoupper(trim((string) $key));
    return isset(theme_header_layouts()[$key]) ? $key : config('Themes')->defaultHeaderLayout;
}

// ==============================
// THEME OF A ROLE — ['palette', 'sidebar', 'sidebar_layout', 'header_layout'] (all whitelisted keys)
// Looked up per request so a change applies on the next page load.
// ==============================
function role_theme($role_id)
{
    $row = [];
    if ($role_id) {
        $db = \Config\Database::connect();
        $row = $db->query("SELECT theme_palette, sidebar_style, sidebar_layout, header_layout FROM tbl_roles WHERE role_id = ?", [$role_id])->getRowArray() ?: [];
    }

    return [
        'palette' => theme_palette_key($row['theme_palette'] ?? ''),
        'sidebar' => theme_sidebar_key($row['sidebar_style'] ?? ''),
        'sidebar_layout' => theme_sidebar_layout_key($row['sidebar_layout'] ?? ''),
        'header_layout' => theme_header_layout_key($row['header_layout'] ?? ''),
    ];
}

// ==============================
// <body> CLASSES FOR THE LAYOUTS — e.g. "lrg-sb-compact lrg-hd-branded"
// ==============================
function theme_body_classes($theme)
{
    return 'lrg-sb-' . strtolower($theme['sidebar_layout']) . ' lrg-hd-' . strtolower($theme['header_layout']);
}

// ==============================
// '#1a6bb0' → '26, 107, 176' (for rgba(var(--x-rgb), alpha))
// ==============================
function theme_hex_rgb($hex)
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    return hexdec(substr($hex, 0, 2)) . ', ' . hexdec(substr($hex, 2, 2)) . ', ' . hexdec(substr($hex, 4, 2));
}

// ==============================
// SIDEBAR VARIABLES FOR A STYLE
// Every style sets the full --lrg-side-* set so the Theme modal preview never
// inherits the viewer's own theme. LIGHT = myheader.php's defaults (white sidebar).
// ==============================
function theme_sidebar_vars($p, $style)
{
    $rgb = theme_hex_rgb($p['primary']);
    $dark_rgb = theme_hex_rgb($p['primary_dark']);

    $light = [
        '--lrg-side-bg' => '#ffffff',
        '--lrg-side-text' => $p['text_light'],
        '--lrg-side-text-strong' => $p['text_dark'],
        '--lrg-side-muted' => $p['text_muted'],
        '--lrg-side-icon' => $p['primary'],
        '--lrg-side-accent' => $p['primary'],
        '--lrg-side-hover' => 'rgba(' . $rgb . ', 0.04)',
        '--lrg-side-active' => 'rgba(' . $rgb . ', 0.06)',
        '--lrg-side-border' => 'rgba(' . $dark_rgb . ', 0.06)',
        '--lrg-side-item-border' => 'rgba(' . $dark_rgb . ', 0.04)',
        '--lrg-side-chip' => $p['primary_pale'],
    ];

    switch ($style) {
        case 'TINTED':
            return array_merge($light, [
                '--lrg-side-bg' => $p['primary_pale'],
                '--lrg-side-hover' => 'rgba(' . $rgb . ', 0.07)',
                '--lrg-side-active' => 'rgba(' . $rgb . ', 0.12)',
                '--lrg-side-border' => 'rgba(' . $dark_rgb . ', 0.10)',
                '--lrg-side-item-border' => 'rgba(' . $dark_rgb . ', 0.08)',
                '--lrg-side-chip' => '#ffffff',
            ]);
        case 'SOLID':
            return [
                '--lrg-side-bg' => $p['primary_dark'],
                '--lrg-side-text' => 'rgba(255, 255, 255, 0.78)',
                '--lrg-side-text-strong' => '#ffffff',
                '--lrg-side-muted' => 'rgba(255, 255, 255, 0.5)',
                '--lrg-side-icon' => $p['tint'],
                '--lrg-side-accent' => '#ffffff',
                '--lrg-side-hover' => 'rgba(255, 255, 255, 0.08)',
                '--lrg-side-active' => 'rgba(255, 255, 255, 0.14)',
                '--lrg-side-border' => 'rgba(255, 255, 255, 0.10)',
                '--lrg-side-item-border' => 'rgba(255, 255, 255, 0.08)',
                '--lrg-side-chip' => 'rgba(255, 255, 255, 0.12)',
            ];
        default:
            return $light;
    }
}

// ==============================
// CSS VARIABLES FOR A PALETTE + SIDEBAR STYLE (name => value)
// Sets both variable systems: --lrg-* (shared chrome) and --primary* (module views).
// Also feeds the live preview in User Management → Roles → Theme.
// ==============================
function theme_vars($key, $sidebar = '')
{
    $p = theme_palettes()[theme_palette_key($key)];
    $rgb = theme_hex_rgb($p['primary']);
    $dark_rgb = theme_hex_rgb($p['primary_dark']);

    $vars = [
        // SHARED CHROME (myheader.php)
        '--lrg-blue' => $p['primary'],
        '--lrg-blue-light' => $p['accent'],
        '--lrg-blue-dark' => $p['primary_dark'],
        '--lrg-blue-pale' => $p['primary_pale'],
        '--lrg-blue-rgb' => $rgb,
        '--lrg-bg' => $p['bg'],
        '--lrg-bg-dark' => $p['bg_dark'],
        '--lrg-border' => $p['border'],
        '--lrg-text-dark' => $p['text_dark'],
        '--lrg-text-mid' => $p['text_mid'],
        '--lrg-text-light' => $p['text_light'],
        '--lrg-text-muted' => $p['text_muted'],
        '--lrg-sidebar-border' => 'rgba(' . $dark_rgb . ', 0.06)',
        '--lrg-border-light' => 'rgba(' . $dark_rgb . ', 0.04)',
        '--lrg-hover' => 'rgba(' . $rgb . ', 0.04)',
        '--lrg-active' => 'rgba(' . $rgb . ', 0.06)',
        '--lrg-shadow' => 'rgba(' . $dark_rgb . ', 0.04)',
        '--lrg-shadow-hover' => 'rgba(' . $dark_rgb . ', 0.06)',
        // MODULE VIEWS (fms/*-main.php)
        '--primary' => $p['primary'],
        '--primary-dark' => $p['primary_dark'],
        '--primary-light' => $p['primary_pale'],
        '--primary-rgb' => $rgb,
        '--primary-tint' => $p['tint'],
        '--primary-tint-2' => $p['tint_2'],
        // DASHBOARD (MyAdminDashboard.php)
        '--accent' => $p['primary'],
        '--accent-dark' => $p['primary_dark'],
        '--accent-light' => $p['primary_pale'],
    ];
    // SIDEBAR (myheader.php .left-sidebar)
    return $vars + theme_sidebar_vars($p, theme_sidebar_key($sidebar));
}

// ==============================
// CSS BLOCK FOR myheader.php
// `html:root` outranks the plain `:root` blocks in myheader.php and in each
// module view's own <style>, so the palette wins regardless of load order.
// ==============================
function theme_css_vars($key, $sidebar = '')
{
    $css = "html:root {\n";
    foreach (theme_vars($key, $sidebar) as $name => $value) {
        $css .= "      " . $name . ": " . $value . ";\n";
    }
    return $css . "    }";
}
