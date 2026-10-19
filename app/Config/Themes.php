<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Color palettes a role can be assigned (User Management → Roles → Theme action).
 * The role's palette recolors the shared sidebar/topbar and every module view —
 * see app/Helpers/theme_helper.php. Printed documents (PDFs) keep the brand blue.
 *
 * To add a palette, add an entry here — no migration needed
 * (tbl_roles.theme_palette stores the key, unknown keys fall back to $default).
 */
class Themes extends BaseConfig
{
    public string $default = 'LARGA_BLUE';
    public string $defaultSidebar = 'LIGHT';

    // How the sidebar takes the palette (tbl_roles.sidebar_style)
    public array $sidebarStyles = [
        'LIGHT'  => ['label' => 'Light',  'description' => 'White sidebar, colored icons'],
        'TINTED' => ['label' => 'Tinted', 'description' => 'Pale shade of the palette'],
        'SOLID'  => ['label' => 'Solid',  'description' => 'Dark palette color, white text'],
    ];

    // Shape of the shared chrome (tbl_roles.sidebar_layout / header_layout) — applied as
    // body classes lrg-sb-<key> / lrg-hd-<key>, styled in templates/myheader.php
    public string $defaultSidebarLayout = 'CLASSIC';
    public string $defaultHeaderLayout = 'CLASSIC';

    public array $sidebarLayouts = [
        'CLASSIC'  => ['label' => 'Classic',  'description' => 'Full menu, rounded items'],
        'COMPACT'  => ['label' => 'Compact',  'description' => 'Icon rail, expands on hover'],
        'FLOATING' => ['label' => 'Floating', 'description' => 'Detached rounded panel'],
        'LINE'     => ['label' => 'Line',     'description' => 'Flat items, accent bar'],
    ];

    public array $headerLayouts = [
        'CLASSIC'  => ['label' => 'Classic',  'description' => 'White bar with shadow'],
        'BRANDED'  => ['label' => 'Branded',  'description' => 'Palette color, name & role'],
        'FLOATING' => ['label' => 'Floating', 'description' => 'Detached card, name & role'],
        'MINIMAL'  => ['label' => 'Minimal',  'description' => 'Blends into the page'],
    ];

    // primary      → buttons, links, module header, active states
    // primary_dark → hover / gradient end
    // primary_pale → light fills (stat card accent, active menu bg)
    // accent       → brighter highlight of the primary
    // tint, tint_2 → light text/icons on top of the dark module header
    // bg, bg_dark  → page background of the shared layout
    // border, text_* → shared layout borders and text shades
    // label, hint  → name + typical industry, shown in the Theme picker
    // Solid industry palettes (enterprise software colors). Shades derived from primary/dark by mixing toward white/black.
    // Every primary passes WCAG AA (≥ 4.5:1) with white text; keep that when adding one.
    public array $palettes = [
        'LARGA_BLUE' => [
            'label' => 'Larga Blue', 'hint' => 'Brand default',
            'primary' => '#1a6bb0', 'primary_dark' => '#0f5a99', 'primary_pale' => '#e8f2fa', 'accent' => '#3c9eff',
            'tint' => '#90cdf4', 'tint_2' => '#bee3f8',
            'bg' => '#f0f7fe', 'bg_dark' => '#e3effa', 'border' => '#dde8f2',
            'text_dark' => '#0b2a47', 'text_mid' => '#1e4a6e', 'text_light' => '#3f6b8f', 'text_muted' => '#4b6f92',
        ],
        'NAVY' => [
            'label' => 'Corporate Navy', 'hint' => 'Finance · Corporate',
            'primary' => '#0d3b85', 'primary_dark' => '#082d69', 'primary_pale' => '#e7ebf3', 'accent' => '#2f6fd6',
            'tint' => '#bbc8dd', 'tint_2' => '#dde4ee',
            'bg' => '#f3f5f9', 'bg_dark' => '#e4e9f2', 'border' => '#dbe2ed',
            'text_dark' => '#04142f', 'text_mid' => '#06224f', 'text_light' => '#2b4571', 'text_muted' => '#3e5376',
        ],
        'STEEL_BLUE' => [
            'label' => 'Steel Blue', 'hint' => 'Logistics · Industrial',
            'primary' => '#1f5f8b', 'primary_dark' => '#164a6e', 'primary_pale' => '#e9eff3', 'accent' => '#3b8fcf',
            'tint' => '#c0d2df', 'tint_2' => '#e0e9ef',
            'bg' => '#f4f7f9', 'bg_dark' => '#e6edf2', 'border' => '#dde7ee',
            'text_dark' => '#0a2132', 'text_mid' => '#113853', 'text_light' => '#345874', 'text_muted' => '#456078',
        ],
        'SLATE_INDIGO' => [
            'label' => 'Indigo', 'hint' => 'Technology · SaaS',
            'primary' => '#3949ab', 'primary_dark' => '#283593', 'primary_pale' => '#ebedf7', 'accent' => '#5c6bc0',
            'tint' => '#c8cce7', 'tint_2' => '#e3e6f3',
            'bg' => '#f5f6fb', 'bg_dark' => '#e9ebf6', 'border' => '#e1e4f2',
            'text_dark' => '#121842', 'text_mid' => '#1e286e', 'text_light' => '#3f4a8c', 'text_muted' => '#4d5789',
        ],
        'OCEAN_TEAL' => [
            'label' => 'Ocean Teal', 'hint' => 'Healthcare · Services',
            'primary' => '#00796b', 'primary_dark' => '#00695c', 'primary_pale' => '#e6f2f0', 'accent' => '#26a69a',
            'tint' => '#b8d9d6', 'tint_2' => '#dbecea',
            'bg' => '#f2f8f8', 'bg_dark' => '#e3f0ef', 'border' => '#d9ebe9',
            'text_dark' => '#002f29', 'text_mid' => '#004f45', 'text_light' => '#256c69', 'text_muted' => '#3b6e70',
        ],
        'PETROL' => [
            'label' => 'Petrol', 'hint' => 'Energy · Utilities',
            'primary' => '#00607a', 'primary_dark' => '#004b5f', 'primary_pale' => '#e6eff2', 'accent' => '#0891b2',
            'tint' => '#b8d2da', 'tint_2' => '#dbe9ec',
            'bg' => '#f2f7f8', 'bg_dark' => '#e3eef0', 'border' => '#d9e7eb',
            'text_dark' => '#00222b', 'text_mid' => '#003847', 'text_light' => '#25596b', 'text_muted' => '#3b6071',
        ],
        'PINE_GREEN' => [
            'label' => 'Forest Green', 'hint' => 'Agriculture · Environment',
            'primary' => '#15803d', 'primary_dark' => '#166534', 'primary_pale' => '#e8f2ec', 'accent' => '#22a559',
            'tint' => '#bddbc9', 'tint_2' => '#deede4',
            'bg' => '#f3f9f5', 'bg_dark' => '#e5f1ea', 'border' => '#dcece2',
            'text_dark' => '#0a2d17', 'text_mid' => '#114c27', 'text_light' => '#346a4f', 'text_muted' => '#456c5e',
        ],
        'GRAPHITE' => [
            'label' => 'Graphite', 'hint' => 'Neutral · Enterprise',
            'primary' => '#334155', 'primary_dark' => '#1e293b', 'primary_pale' => '#ebecee', 'accent' => '#64748b',
            'tint' => '#c6cacf', 'tint_2' => '#e2e4e7',
            'bg' => '#f5f6f7', 'bg_dark' => '#e9eaec', 'border' => '#e0e3e6',
            'text_dark' => '#0e121b', 'text_mid' => '#171f2c', 'text_light' => '#394353', 'text_muted' => '#485161',
        ],
    ];
}
