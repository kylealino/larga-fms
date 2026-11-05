-- =============================================
-- PHASE 17: INVOICE BOOKING CHARGES
-- The trip keeps the ERP booking quote in tbl_trip_booking_charges; when an invoice
-- is generated those lines are copied here so the invoice keeps its own breakdown
-- (later relinking / editing the trip's booking no longer changes an issued invoice).
-- Same columns as tbl_trip_booking_charges plus invoice_id and the source row.
-- =============================================

CREATE TABLE IF NOT EXISTS `tbl_invoice_booking_charges` (
  `charge_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `trip_charge_id` int(11) DEFAULT NULL,
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
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_trip_id` (`trip_id`),
  KEY `idx_trip_charge_id` (`trip_charge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Backfill: invoices generated before this change get a copy of their trip's current quote
INSERT INTO `tbl_invoice_booking_charges` (
    `invoice_id`, `trip_id`, `trip_charge_id`, `erp_bookslip_dt_id`, `description`, `charge_basis`, `currency`,
    `quantity`, `cost`, `selling`, `selling_php`, `payment_to`, `rate_validity`, `created_at`, `created_by`
)
SELECT i.invoice_id, c.trip_id, c.charge_id, c.erp_bookslip_dt_id, c.description, c.charge_basis, c.currency,
       c.quantity, c.cost, c.selling, c.selling_php, c.payment_to, c.rate_validity, i.created_at, i.created_by
FROM tbl_invoices i
JOIN tbl_billing b ON b.billing_id = i.billing_id
JOIN tbl_trip_booking_charges c ON c.trip_id = b.trip_id
WHERE NOT EXISTS (SELECT 1 FROM tbl_invoice_booking_charges x WHERE x.invoice_id = i.invoice_id)
ORDER BY i.invoice_id, c.charge_id;
