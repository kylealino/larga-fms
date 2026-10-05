-- =============================================
-- PHASE 9: UNIQUE BUSINESS CODES
-- COUNT(*)+1 code generators (now MAX-based) produced duplicate codes after deletes.
-- Renumber the newer row of each duplicate pair, then enforce uniqueness.
-- =============================================
UPDATE `tbl_dispatch` SET `dispatch_code` = 'DSP-2026-000010' WHERE `dispatch_id` = 33; -- was DSP-2026-000008 (dup of dispatch 24)
UPDATE `tbl_trips` SET `trip_code` = 'TRP-2026-000010' WHERE `trip_id` = 33; -- was TRP-2026-000008 (dup of trip 31)
UPDATE `tbl_helpers` SET `helper_code` = 'HLP-2026-0011' WHERE `helper_id` = 12; -- was HLP-2026-0001 (dup of helper 1)
UPDATE `tbl_trucks` SET `truck_code` = 'TRC-2026-0002' WHERE `truck_id` = 3; -- was TRC-2026-0001 (dup of truck 1)
UPDATE `tbl_trucks` SET `truck_code` = 'TRK-2026-0031' WHERE `truck_id` = 6; -- was TRK-2026-0001 (dup of truck 5)

ALTER TABLE `tbl_trucks` DROP INDEX IF EXISTS `idx_truck_code`, ADD UNIQUE KEY `idx_truck_code` (`truck_code`);
ALTER TABLE `tbl_trips` DROP INDEX IF EXISTS `idx_trip_code`, ADD UNIQUE KEY `idx_trip_code` (`trip_code`);
ALTER TABLE `tbl_dispatch` DROP INDEX IF EXISTS `idx_dispatch_code`, ADD UNIQUE KEY `idx_dispatch_code` (`dispatch_code`);
ALTER TABLE `tbl_delivery_receipts` DROP INDEX IF EXISTS `idx_dr_code`, ADD UNIQUE KEY `idx_dr_code` (`dr_code`);
ALTER TABLE `tbl_drivers` DROP INDEX IF EXISTS `idx_driver_code`, ADD UNIQUE KEY `idx_driver_code` (`driver_code`);
ALTER TABLE `tbl_helpers` DROP INDEX IF EXISTS `idx_helper_code`, ADD UNIQUE KEY `idx_helper_code` (`helper_code`);
ALTER TABLE `tbl_vendors` DROP INDEX IF EXISTS `idx_vendor_code`, ADD UNIQUE KEY `idx_vendor_code` (`vendor_code`);
ALTER TABLE `tbl_maintenance_schedules` DROP INDEX IF EXISTS `idx_schedule_code`, ADD UNIQUE KEY `idx_schedule_code` (`schedule_code`);
ALTER TABLE `tbl_maintenance_records` DROP INDEX IF EXISTS `idx_record_code`, ADD UNIQUE KEY `idx_record_code` (`record_code`);
ALTER TABLE `tbl_tire_installations` DROP INDEX IF EXISTS `idx_installation_code`, ADD UNIQUE KEY `idx_installation_code` (`installation_code`);
ALTER TABLE `tbl_tire_disposals` DROP INDEX IF EXISTS `idx_disposal_code`, ADD UNIQUE KEY `idx_disposal_code` (`disposal_code`);
