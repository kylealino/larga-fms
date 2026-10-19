-- =============================================
-- PHASE 16: ROLE UI LAYOUTS
-- Besides colors, a role picks the SHAPE of the shared chrome:
-- sidebar_layout (CLASSIC / COMPACT / FLOATING / LINE) and
-- header_layout (CLASSIC / BRANDED / FLOATING / MINIMAL).
-- Keys of app/Config/Themes.php $sidebarLayouts / $headerLayouts; unknown keys fall back to CLASSIC.
-- Set from User Management → Roles → Theme action.
-- =============================================

ALTER TABLE `tbl_roles`
    ADD COLUMN IF NOT EXISTS `sidebar_layout` varchar(20) NOT NULL DEFAULT 'CLASSIC' AFTER `sidebar_style`,
    ADD COLUMN IF NOT EXISTS `header_layout` varchar(20) NOT NULL DEFAULT 'CLASSIC' AFTER `sidebar_layout`;
