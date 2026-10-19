-- =============================================
-- PHASE 13: ROLE-BASED COLOR PALETTE
-- Each role picks a color palette (User Management → Roles → Edit Role) that
-- recolors the shared sidebar/topbar and every module view for its users.
-- The value is a key of app/Config/Themes.php $palettes (UPPER_SNAKE, e.g.
-- OCEAN_TEAL). varchar, not enum, so a palette can be added in the config
-- without a migration; unknown keys fall back to LARGA_BLUE in theme_helper.php.
-- =============================================

ALTER TABLE `tbl_roles`
    ADD COLUMN IF NOT EXISTS `theme_palette` varchar(30) NOT NULL DEFAULT 'LARGA_BLUE' AFTER `description`;
