-- =============================================
-- PHASE 6: <entity>_status COLUMN NAMES
-- Plain `status` / `availability` renamed so every table follows <entity>_status.
-- =============================================
ALTER TABLE `tbl_customers` CHANGE `status` `customer_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE';
ALTER TABLE `tbl_customer_locations` CHANGE `status` `location_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE';
ALTER TABLE `tbl_dashboard_widgets` CHANGE `status` `widget_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE';
ALTER TABLE `tbl_maintenance_records` CHANGE `status` `record_status` enum('ONGOING','COMPLETED','CANCELLED') NOT NULL DEFAULT 'COMPLETED';
ALTER TABLE `tbl_maintenance_schedules` CHANGE `status` `schedule_status` enum('SCHEDULED','IN_PROGRESS','COMPLETED','CANCELLED','OVERDUE') NOT NULL DEFAULT 'SCHEDULED';
ALTER TABLE `tbl_modules` CHANGE `status` `module_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE';
ALTER TABLE `tbl_roles` CHANGE `status` `role_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE';
ALTER TABLE `tbl_supplies` CHANGE `status` `supply_status` enum('IN_STOCK','LOW_STOCK','OUT_OF_STOCK','DISCONTINUED') NOT NULL DEFAULT 'IN_STOCK';
ALTER TABLE `tbl_tire_installations` CHANGE `status` `installation_status` enum('ACTIVE','REMOVED') NOT NULL DEFAULT 'ACTIVE';
ALTER TABLE `tbl_tools` CHANGE `availability` `tool_status` enum('AVAILABLE','ASSIGNED','UNDER_REPAIR','DAMAGED','LOST','RETIRED') NOT NULL DEFAULT 'AVAILABLE';

-- Index names follow the column
ALTER TABLE `tbl_supplies` DROP INDEX IF EXISTS `idx_status`, ADD KEY IF NOT EXISTS `idx_supply_status` (`supply_status`);
ALTER TABLE `tbl_tools` DROP INDEX IF EXISTS `idx_availability`, ADD KEY IF NOT EXISTS `idx_tool_status` (`tool_status`);
