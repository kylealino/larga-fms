-- =============================================
-- PHASE 3 + 4: TRIP LIFECYCLE + RESOURCE IDS
-- tbl_trips.trip_status is the single lifecycle:
--   DRAFT > SCHEDULED > ASSIGNED > DISPATCHED > IN_TRANSIT > DELIVERED > COMPLETED / CANCELLED
-- tbl_trip_assignments holds the resource IDs (plain int, no FK);
-- plate/name columns stay as history snapshots.
-- tbl_dispatch / tbl_delivery_receipts reach resources via trip_id.
-- =============================================

-- =============================================
-- ASSIGNMENT RESOURCE IDS
-- =============================================
ALTER TABLE `tbl_trip_assignments`
    ADD COLUMN IF NOT EXISTS `truck_id` int(11) DEFAULT NULL AFTER `vehicle_type`,
    ADD COLUMN IF NOT EXISTS `tractor_id` int(11) DEFAULT NULL AFTER `truck_plate`,
    ADD COLUMN IF NOT EXISTS `chassis_id` int(11) DEFAULT NULL AFTER `tractor_plate`,
    ADD COLUMN IF NOT EXISTS `vendor_id` int(11) DEFAULT NULL AFTER `chassis_type`,
    ADD COLUMN IF NOT EXISTS `driver_id` int(11) DEFAULT NULL AFTER `vendor_contact_number`,
    ADD COLUMN IF NOT EXISTS `helper_id` int(11) DEFAULT NULL AFTER `driver_name`,
    ADD KEY IF NOT EXISTS `idx_trip_id` (`trip_id`),
    ADD KEY IF NOT EXISTS `idx_truck_id` (`truck_id`),
    ADD KEY IF NOT EXISTS `idx_tractor_id` (`tractor_id`),
    ADD KEY IF NOT EXISTS `idx_chassis_id` (`chassis_id`),
    ADD KEY IF NOT EXISTS `idx_vendor_id` (`vendor_id`),
    ADD KEY IF NOT EXISTS `idx_driver_id` (`driver_id`),
    ADD KEY IF NOT EXISTS `idx_helper_id` (`helper_id`);

-- Backfill from snapshot text (plates / names are unique in master tables)
UPDATE `tbl_trip_assignments` a JOIN `tbl_trucks` t ON t.plate_number = a.truck_plate SET a.truck_id = t.truck_id WHERE a.truck_id IS NULL AND a.truck_plate <> '';
UPDATE `tbl_trip_assignments` a JOIN `tbl_trucks` t ON t.plate_number = a.tractor_plate SET a.tractor_id = t.truck_id WHERE a.tractor_id IS NULL AND a.tractor_plate <> '';
UPDATE `tbl_trip_assignments` a JOIN `tbl_trucks` t ON t.plate_number = a.chassis_plate SET a.chassis_id = t.truck_id WHERE a.chassis_id IS NULL AND a.chassis_plate <> '';
UPDATE `tbl_trip_assignments` a JOIN `tbl_vendors` v ON v.vendor_name = a.vendor_name SET a.vendor_id = v.vendor_id WHERE a.vendor_id IS NULL AND a.vendor_name <> '';
UPDATE `tbl_trip_assignments` a JOIN `tbl_drivers` d ON d.driver_name = a.driver_name SET a.driver_id = d.driver_id WHERE a.driver_id IS NULL AND a.driver_name <> '';
UPDATE `tbl_trip_assignments` a JOIN `tbl_helpers` h ON h.helper_name = a.helper_name SET a.helper_id = h.helper_id WHERE a.helper_id IS NULL AND a.helper_name <> '';

-- Normalize empty-string snapshots to NULL
UPDATE `tbl_trip_assignments` SET
    `truck_plate` = NULLIF(`truck_plate`, ''),
    `tractor_plate` = NULLIF(`tractor_plate`, ''),
    `chassis_plate` = NULLIF(`chassis_plate`, ''),
    `vendor_name` = NULLIF(`vendor_name`, '');

-- =============================================
-- SINGLE LIFECYCLE: drop duplicate status/flag columns
-- =============================================
ALTER TABLE `tbl_trip_assignments` DROP COLUMN IF EXISTS `assignment_status`;
ALTER TABLE `tbl_dispatch` DROP COLUMN IF EXISTS `dispatch_status`;
ALTER TABLE `tbl_trips` DROP COLUMN IF EXISTS `has_assignment`;

ALTER TABLE `tbl_trips` MODIFY `trip_status` enum('DRAFT','SCHEDULED','ASSIGNED','DISPATCHED','IN_TRANSIT','DELIVERED','COMPLETED','CANCELLED') NOT NULL DEFAULT 'DRAFT';

-- Dispatch / DR lookups by trip
ALTER TABLE `tbl_dispatch` ADD KEY IF NOT EXISTS `idx_trip_id` (`trip_id`);
ALTER TABLE `tbl_delivery_receipts` ADD KEY IF NOT EXISTS `idx_trip_id` (`trip_id`), ADD KEY IF NOT EXISTS `idx_dispatch_id` (`dispatch_id`);
