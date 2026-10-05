-- =============================================
-- PHASE 2: ENUM VALUES -> UPPER_SNAKE_CASE
-- (no spaces / hyphens / "+"). Widen -> convert -> narrow.
-- =============================================

-- DRIVERS
ALTER TABLE `tbl_drivers` MODIFY `driver_status` enum('AVAILABLE','ASSIGNED','ON TRIP','ON_TRIP','RETURNING','ON LEAVE','ON_LEAVE','SUSPENDED','INACTIVE') NOT NULL DEFAULT 'AVAILABLE';
UPDATE `tbl_drivers` SET `driver_status` = REPLACE(`driver_status`, ' ', '_');
ALTER TABLE `tbl_drivers` MODIFY `driver_status` enum('AVAILABLE','ASSIGNED','ON_TRIP','RETURNING','ON_LEAVE','SUSPENDED','INACTIVE') NOT NULL DEFAULT 'AVAILABLE';

-- HELPERS
ALTER TABLE `tbl_helpers` MODIFY `helper_status` enum('AVAILABLE','ASSIGNED','ON TRIP','ON_TRIP','ON LEAVE','ON_LEAVE','INACTIVE') NOT NULL DEFAULT 'AVAILABLE';
UPDATE `tbl_helpers` SET `helper_status` = REPLACE(`helper_status`, ' ', '_');
ALTER TABLE `tbl_helpers` MODIFY `helper_status` enum('AVAILABLE','ASSIGNED','ON_TRIP','ON_LEAVE','INACTIVE') NOT NULL DEFAULT 'AVAILABLE';

-- TRUCKS
ALTER TABLE `tbl_trucks` MODIFY `truck_status` enum('AVAILABLE','ASSIGNED','DISPATCHED','IN TRANSIT','IN_TRANSIT','RETURNING','UNDER MAINTENANCE','UNDER_MAINTENANCE','OUT OF SERVICE','OUT_OF_SERVICE','RETIRED') NOT NULL DEFAULT 'AVAILABLE';
UPDATE `tbl_trucks` SET `truck_status` = REPLACE(`truck_status`, ' ', '_');
ALTER TABLE `tbl_trucks` MODIFY `truck_status` enum('AVAILABLE','ASSIGNED','DISPATCHED','IN_TRANSIT','RETURNING','UNDER_MAINTENANCE','OUT_OF_SERVICE','RETIRED') NOT NULL DEFAULT 'AVAILABLE';

ALTER TABLE `tbl_trucks` MODIFY `ownership` enum('COMPANY-OWNED','COMPANY_OWNED','LEASED','RENTED') NOT NULL DEFAULT 'COMPANY_OWNED';
UPDATE `tbl_trucks` SET `ownership` = REPLACE(`ownership`, '-', '_');
ALTER TABLE `tbl_trucks` MODIFY `ownership` enum('COMPANY_OWNED','LEASED','RENTED') NOT NULL DEFAULT 'COMPANY_OWNED';

-- TRUCK DOCUMENTS
ALTER TABLE `tbl_truck_documents` MODIFY `document_type` enum('REGISTRATION','INSURANCE','SOLIDARITY STICKER','SOLIDARITY_STICKER','EAGLE STICKER','EAGLE_STICKER','GENERAL STICKER','GENERAL_STICKER','FRANCHISE','ACCREDITATION','OTHER') NOT NULL;
UPDATE `tbl_truck_documents` SET `document_type` = REPLACE(`document_type`, ' ', '_');
ALTER TABLE `tbl_truck_documents` MODIFY `document_type` enum('REGISTRATION','INSURANCE','SOLIDARITY_STICKER','EAGLE_STICKER','GENERAL_STICKER','FRANCHISE','ACCREDITATION','OTHER') NOT NULL;

-- VENDOR SERVICES (switch to varchar, convert, back to enum)
ALTER TABLE `tbl_vendor_services` MODIFY `service_type` varchar(100) NOT NULL, MODIFY `rate_type` varchar(50) NOT NULL;
UPDATE `tbl_vendor_services` SET
    `service_type` = REPLACE(REPLACE(`service_type`, ' + ', '_'), ' ', '_'),
    `rate_type` = REPLACE(`rate_type`, ' ', '_');
ALTER TABLE `tbl_vendor_services`
    MODIFY `service_type` enum('TRUCK_RENTAL','TRACTOR_RENTAL','CHASSIS_RENTAL','DRIVER_SERVICE','HELPER_SERVICE','TRUCK_DRIVER','TRUCK_DRIVER_HELPER','TRACTOR_CHASSIS','TRACTOR_CHASSIS_DRIVER','TRACTOR_CHASSIS_DRIVER_HELPER','OTHER_TRANSPORTATION_SERVICE') NOT NULL,
    MODIFY `rate_type` enum('PER_TRIP','PER_DAY','PER_KILOMETER','PER_HOUR','MONTHLY','FIXED_RATE') NOT NULL;
