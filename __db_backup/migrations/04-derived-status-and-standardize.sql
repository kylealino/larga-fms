-- =============================================
-- PHASE 5: DERIVED STATUS + STANDARDIZATION
-- =============================================

-- =============================================
-- DERIVED STATUS: computed in SELECTs from license_number / expiration_date
-- (see FMS_Driver_Model::getDriver, FMS_Helper_Model::getHelper,
--  FMS_Truck_Model document reads, driver/helper list views)
-- =============================================
ALTER TABLE `tbl_drivers` DROP COLUMN IF EXISTS `license_status`, DROP COLUMN IF EXISTS `has_license`;
ALTER TABLE `tbl_helpers` DROP COLUMN IF EXISTS `license_status`, DROP COLUMN IF EXISTS `has_license`;
ALTER TABLE `tbl_truck_documents` DROP COLUMN IF EXISTS `document_status`;

-- =============================================
-- AUDIT COLUMNS: created_at / created_by / updated_at NOT NULL
-- =============================================
UPDATE `tbl_dispatch` SET `created_by` = 'SYSTEM' WHERE `created_by` IS NULL;
UPDATE `tbl_dispatch_checklist` SET `created_by` = 'SYSTEM' WHERE `created_by` IS NULL;
UPDATE `tbl_dispatch_expenses` SET `created_by` = 'SYSTEM' WHERE `created_by` IS NULL;
UPDATE `tbl_trip_assignments` SET `created_by` = 'SYSTEM' WHERE `created_by` IS NULL;
UPDATE `tbl_trip_waypoints` SET `created_by` = 'SYSTEM' WHERE `created_by` IS NULL;

ALTER TABLE `tbl_dispatch`
    MODIFY `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    MODIFY `created_by` varchar(50) NOT NULL,
    MODIFY `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
ALTER TABLE `tbl_dispatch_checklist`
    MODIFY `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    MODIFY `created_by` varchar(50) NOT NULL,
    MODIFY `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
ALTER TABLE `tbl_dispatch_expenses`
    MODIFY `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    MODIFY `created_by` varchar(50) NOT NULL,
    MODIFY `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
ALTER TABLE `tbl_trips`
    MODIFY `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    MODIFY `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
ALTER TABLE `tbl_trip_assignments`
    MODIFY `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    MODIFY `created_by` varchar(50) NOT NULL,
    MODIFY `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
ALTER TABLE `tbl_trip_waypoints`
    MODIFY `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    MODIFY `created_by` varchar(50) NOT NULL,
    MODIFY `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();

-- =============================================
-- MONEY: decimal(15,2) NOT NULL DEFAULT 0.00
-- =============================================
UPDATE `tbl_customers` SET `credit_limit` = 0 WHERE `credit_limit` IS NULL;
ALTER TABLE `tbl_customers` MODIFY `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_dispatch_expenses` SET `amount` = 0 WHERE `amount` IS NULL;
ALTER TABLE `tbl_dispatch_expenses` MODIFY `amount` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_maintenance_parts` SET `unit_cost` = IFNULL(`unit_cost`, 0), `total_cost` = IFNULL(`total_cost`, 0);
ALTER TABLE `tbl_maintenance_parts`
    MODIFY `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
    MODIFY `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_maintenance_records` SET `labor_cost` = IFNULL(`labor_cost`, 0), `parts_cost` = IFNULL(`parts_cost`, 0),
    `other_cost` = IFNULL(`other_cost`, 0), `total_cost` = IFNULL(`total_cost`, 0);
ALTER TABLE `tbl_maintenance_records`
    MODIFY `labor_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
    MODIFY `parts_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
    MODIFY `other_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
    MODIFY `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_supplies` SET `unit_cost` = 0 WHERE `unit_cost` IS NULL;
ALTER TABLE `tbl_supplies` MODIFY `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_supply_transactions` SET `unit_cost` = IFNULL(`unit_cost`, 0), `total_cost` = IFNULL(`total_cost`, 0);
ALTER TABLE `tbl_supply_transactions`
    MODIFY `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
    MODIFY `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_tires` SET `price` = 0 WHERE `price` IS NULL;
ALTER TABLE `tbl_tires` MODIFY `price` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_tire_transactions` SET `price` = 0 WHERE `price` IS NULL;
ALTER TABLE `tbl_tire_transactions` MODIFY `price` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_tools` SET `purchase_cost` = 0 WHERE `purchase_cost` IS NULL;
ALTER TABLE `tbl_tools` MODIFY `purchase_cost` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_trip_assignments` SET `rental_rate` = 0 WHERE `rental_rate` IS NULL;
ALTER TABLE `tbl_trip_assignments` MODIFY `rental_rate` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_truck_documents` SET `premium` = IFNULL(`premium`, 0), `coverage_amount` = IFNULL(`coverage_amount`, 0), `rate` = IFNULL(`rate`, 0);
ALTER TABLE `tbl_truck_documents`
    MODIFY `premium` decimal(15,2) NOT NULL DEFAULT 0.00,
    MODIFY `coverage_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
    MODIFY `rate` decimal(15,2) NOT NULL DEFAULT 0.00;

UPDATE `tbl_vendor_services` SET `rate_amount` = 0 WHERE `rate_amount` IS NULL;
ALTER TABLE `tbl_vendor_services` MODIFY `rate_amount` decimal(15,2) NOT NULL DEFAULT 0.00;

-- =============================================
-- PRECISION: truck measures widened to (15,2) like every other table
-- =============================================
ALTER TABLE `tbl_trucks`
    MODIFY `current_odometer` decimal(15,2) DEFAULT 0.00,
    MODIFY `fuel_tank_capacity` decimal(15,2) DEFAULT 0.00,
    MODIFY `load_capacity` decimal(15,2) DEFAULT 0.00;
