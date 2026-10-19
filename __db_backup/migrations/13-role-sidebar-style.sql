-- =============================================
-- PHASE 14: ROLE SIDEBAR STYLE
-- How the sidebar takes the role's color palette: LIGHT (white, colored icons),
-- TINTED (pale palette shade) or SOLID (dark palette color, white text).
-- Key of app/Config/Themes.php $sidebarStyles; unknown keys fall back to LIGHT.
-- Set together with theme_palette from User Management → Roles → Theme action.
-- =============================================

ALTER TABLE `tbl_roles`
    ADD COLUMN IF NOT EXISTS `sidebar_style` varchar(20) NOT NULL DEFAULT 'LIGHT' AFTER `theme_palette`;
