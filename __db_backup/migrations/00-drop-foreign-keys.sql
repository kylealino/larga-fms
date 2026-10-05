-- =============================================
-- DROP FOREIGN KEY CONSTRAINTS
-- Reference columns stay as plain int(11) + idx_<column> index.
-- Relationships are handled in PHP, not by the DB.
-- Safe to re-run (IF EXISTS / IF NOT EXISTS guards).
-- =============================================

-- CUSTOMER LOCATIONS
ALTER TABLE `tbl_customer_locations` DROP FOREIGN KEY IF EXISTS `tbl_customer_locations_ibfk_1`;
ALTER TABLE `tbl_customer_locations` DROP INDEX IF EXISTS `customer_id`, ADD KEY IF NOT EXISTS `idx_customer_id` (`customer_id`);

-- DRIVER SKILLSETS
ALTER TABLE `tbl_driver_skillsets` DROP FOREIGN KEY IF EXISTS `tbl_driver_skillsets_ibfk_1`;
ALTER TABLE `tbl_driver_skillsets` DROP INDEX IF EXISTS `driver_id`, ADD KEY IF NOT EXISTS `idx_driver_id` (`driver_id`);

-- TRANSACTION DOCUMENTS
ALTER TABLE `tbl_transaction_documents` DROP FOREIGN KEY IF EXISTS `fk_transaction_documents`;
ALTER TABLE `tbl_transaction_documents` DROP INDEX IF EXISTS `transaction_id`, ADD KEY IF NOT EXISTS `idx_transaction_id` (`transaction_id`);

-- TRUCK DOCUMENTS
ALTER TABLE `tbl_truck_documents` DROP FOREIGN KEY IF EXISTS `tbl_truck_documents_ibfk_1`;
ALTER TABLE `tbl_truck_documents` DROP INDEX IF EXISTS `truck_id`, ADD KEY IF NOT EXISTS `idx_truck_id` (`truck_id`);

-- VENDOR SERVICES
ALTER TABLE `tbl_vendor_services` DROP FOREIGN KEY IF EXISTS `tbl_vendor_services_ibfk_1`;
ALTER TABLE `tbl_vendor_services` DROP INDEX IF EXISTS `tbl_vendor_services_ibfk_1`, ADD KEY IF NOT EXISTS `idx_vendor_id` (`vendor_id`);

-- ROLE PERMISSIONS (indexes already named idx_*)
ALTER TABLE `tbl_role_permissions` DROP FOREIGN KEY IF EXISTS `tbl_role_permissions_ibfk_1`;
ALTER TABLE `tbl_role_permissions` DROP FOREIGN KEY IF EXISTS `tbl_role_permissions_ibfk_2`;

-- ROLE DASHBOARD WIDGETS (indexes already named idx_*)
ALTER TABLE `tbl_role_dashboard_widgets` DROP FOREIGN KEY IF EXISTS `tbl_role_dashboard_widgets_ibfk_1`;
ALTER TABLE `tbl_role_dashboard_widgets` DROP FOREIGN KEY IF EXISTS `tbl_role_dashboard_widgets_ibfk_2`;
