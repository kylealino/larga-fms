-- =============================================
-- PHASE 11: ERP BOOKING SLIP LINK (erplogisticdev → Trip Scheduling)
-- A trip can be created from an ERP booking slip that has a Trucking line.
-- erp_* columns are plain references into the ERP database (no FK, other DB).
-- Booking details are snapshotted on the trip so FMS keeps working if the ERP changes.
-- =============================================

-- Customer ↔ ERP client (mst_orgzn.recid), set when a customer is linked/created from a booking
ALTER TABLE `tbl_customers`
    ADD COLUMN IF NOT EXISTS `erp_orgzn_id` int(11) DEFAULT NULL AFTER `customer_code`,
    ADD UNIQUE KEY IF NOT EXISTS `idx_erp_orgzn_id` (`erp_orgzn_id`);

-- Trip ↔ ERP booking slip (trx_bookslip_hd.id) + snapshot of its reference details
ALTER TABLE `tbl_trips`
    ADD COLUMN IF NOT EXISTS `erp_bookslip_id` int(11) DEFAULT NULL AFTER `booking_reference`,
    ADD COLUMN IF NOT EXISTS `client_reference` varchar(50) DEFAULT NULL AFTER `erp_bookslip_id`,
    ADD COLUMN IF NOT EXISTS `shipper_name` varchar(150) DEFAULT NULL AFTER `client_reference`,
    ADD COLUMN IF NOT EXISTS `mode_of_shipment` varchar(100) DEFAULT NULL AFTER `shipper_name`,
    ADD COLUMN IF NOT EXISTS `incoterms` varchar(150) DEFAULT NULL AFTER `mode_of_shipment`,
    ADD COLUMN IF NOT EXISTS `commodity_type` varchar(50) DEFAULT NULL AFTER `incoterms`,
    ADD KEY IF NOT EXISTS `idx_erp_bookslip_id` (`erp_bookslip_id`);

-- Snapshot of the booking's Trucking quote lines (used for display and to prefill billing)
CREATE TABLE IF NOT EXISTS `tbl_trip_booking_charges` (
  `charge_id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL,
  `erp_bookslip_dt_id` int(11) DEFAULT NULL,
  `description` varchar(150) NOT NULL,
  `charge_basis` varchar(100) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'PHP',
  `quantity` decimal(15,2) NOT NULL DEFAULT 1.00,
  `cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_php` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_to` varchar(150) DEFAULT NULL,
  `rate_validity` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  PRIMARY KEY (`charge_id`),
  KEY `idx_trip_id` (`trip_id`),
  KEY `idx_erp_bookslip_dt_id` (`erp_bookslip_dt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
