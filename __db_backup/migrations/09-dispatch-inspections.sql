-- =============================================
-- PHASE 10: PRE-TRIP INSPECTION
-- One inspection per dispatch. The trip can't move past DISPATCHED until the
-- inspection form is uploaded, driver + inspector have signed, and the final
-- status is SAFE. "Cleared" is derived, not stored.
-- =============================================
CREATE TABLE IF NOT EXISTS `tbl_dispatch_inspections` (
  `inspection_id` int(11) NOT NULL AUTO_INCREMENT,
  `inspection_code` varchar(50) NOT NULL,
  `dispatch_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `inspection_date` date NOT NULL,
  `inspection_time` time DEFAULT NULL,
  `truck_plate` varchar(100) DEFAULT NULL,
  `inspector_name` varchar(100) DEFAULT NULL,
  `final_status` enum('SAFE','REQUIRES_REPAIR') NOT NULL DEFAULT 'SAFE',
  `defects_found` text DEFAULT NULL,
  `inspection_form` varchar(255) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_signature` varchar(255) DEFAULT NULL,
  `driver_signed_at` datetime DEFAULT NULL,
  `inspector_signature` varchar(255) DEFAULT NULL,
  `inspector_signed_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`inspection_id`),
  UNIQUE KEY `idx_inspection_code` (`inspection_code`),
  UNIQUE KEY `idx_dispatch_id` (`dispatch_id`),
  KEY `idx_trip_id` (`trip_id`),
  KEY `idx_driver_id` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
