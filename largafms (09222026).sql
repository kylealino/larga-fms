-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2026 at 08:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `largafms`
--

-- --------------------------------------------------------

--
-- Table structure for table `myua_user`
--

CREATE TABLE `myua_user` (
  `recid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `hash_password` varchar(200) NOT NULL,
  `hash_value` varchar(150) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `division` varchar(50) NOT NULL,
  `section` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `cert_tag` int(11) DEFAULT 0,
  `is_ppmp_signatory` int(11) NOT NULL DEFAULT 0,
  `added_at` datetime NOT NULL DEFAULT current_timestamp(),
  `added_by` varchar(50) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `myua_user`
--

INSERT INTO `myua_user` (`recid`, `username`, `hash_password`, `hash_value`, `full_name`, `division`, `section`, `position`, `cert_tag`, `is_ppmp_signatory`, `added_at`, `added_by`, `is_active`) VALUES
(6, 'ADMIN', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'Sir Eric', '', '', 'CHIEF, TRAINING SECTION', 0, 0, '2025-05-16 08:55:10', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bay_status`
--

CREATE TABLE `tbl_bay_status` (
  `bay_id` int(11) NOT NULL,
  `stage_id` int(11) NOT NULL COMMENT '1-10',
  `status` enum('AVAILABLE','OCCUPIED','RESERVED','MAINTENANCE') NOT NULL DEFAULT 'AVAILABLE',
  `current_transaction_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_bay_status`
--

INSERT INTO `tbl_bay_status` (`bay_id`, `stage_id`, `status`, `current_transaction_id`, `updated_at`) VALUES
(1, 1, 'AVAILABLE', NULL, '2026-07-29 09:38:49'),
(2, 2, 'AVAILABLE', NULL, '2026-06-26 15:14:10'),
(3, 3, 'AVAILABLE', NULL, '2026-06-24 16:20:52'),
(4, 4, 'AVAILABLE', NULL, '2026-06-26 09:15:34'),
(5, 5, 'AVAILABLE', NULL, '2026-06-24 16:20:40'),
(6, 6, 'RESERVED', NULL, '2026-06-27 14:48:50'),
(7, 7, 'AVAILABLE', NULL, NULL),
(8, 8, 'AVAILABLE', NULL, '2026-06-27 14:02:33'),
(9, 9, 'MAINTENANCE', NULL, '2026-06-27 14:03:51'),
(10, 10, 'MAINTENANCE', NULL, '2026-06-24 11:20:19'),
(15, 11, 'RESERVED', NULL, '2026-06-27 14:48:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customers`
--

CREATE TABLE `tbl_customers` (
  `customer_id` int(11) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_position` varchar(100) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `payment_terms` varchar(50) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT 0.00,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customers`
--

INSERT INTO `tbl_customers` (`customer_id`, `customer_code`, `customer_name`, `customer_type`, `tin`, `contact_person`, `contact_position`, `contact_number`, `email_address`, `business_address`, `billing_address`, `payment_terms`, `credit_limit`, `status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'CUST-2026-0001', 'ABC Manufacturing Corp', 'Corporate', '123-456-789-000', 'Maria Santos', 'Procurement Manager', '0917-123-4567', 'maria@abcmfg.com', '123 Industrial Ave., Laguna', '123 Industrial Ave., Laguna', '30 Days', 500000.00, 'ACTIVE', 'Major manufacturer of consumer goods', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(2, 'CUST-2026-0002', 'XYZ Trading Inc.', 'Corporate', '234-567-890-001', 'Juan Dela Cruz', 'Operations Director', '0918-234-5678', 'juan@xyztrading.com', '456 Commercial St., Manila', '456 Commercial St., Manila', '15 Days', 300000.00, 'ACTIVE', 'Trading company for construction materials', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(3, 'CUST-2026-0003', 'Davao Agricultural Corp', 'Corporate', '345-678-901-002', 'Pedro Reyes', 'Farm Manager', '0919-345-6789', 'pedro@davaoagri.com', '789 Davao City, Davao', '789 Davao City, Davao', '45 Days', 200000.00, 'ACTIVE', 'Agricultural products supplier', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(4, 'CUST-2026-0004', 'Cebu Retail Group', 'Regular', '456-789-012-003', 'Anna Lopez', 'Store Manager', '0920-456-7890', 'anna@ceburetail.com', '101 Cebu City, Cebu', '101 Cebu City, Cebu', 'COD', 50000.00, 'ACTIVE', 'Retail chain for consumer goods', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(5, 'CUST-2026-0005', 'Manila Logistics Hub', 'Corporate', '567-890-123-004', 'Ramon Cruz', 'Logistics Manager', '0921-567-8901', 'ramon@manilalogistics.com', '202 Pasig City, Metro Manila', '202 Pasig City, Metro Manila', '30 Days', 400000.00, 'ACTIVE', 'Third-party logistics provider', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(6, 'CUST-2026-0006', 'Pampanga Food Products', 'Corporate', '678-901-234-005', 'Elena Rivera', 'Production Manager', '0922-678-9012', 'elena@pampangafoods.com', '303 Pampanga, Central Luzon', '303 Pampanga, Central Luzon', '60 Days', 250000.00, 'ACTIVE', 'Food processing and distribution', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(7, 'CUST-2026-0007', 'Visayas Shipping Lines', 'Corporate', '789-012-345-006', 'Mark Tan', 'Fleet Manager', '0923-789-0123', 'mark@visayasshipping.com', '404 Iloilo City, Iloilo', '404 Iloilo City, Iloilo', '30 Days', 600000.00, 'ACTIVE', 'Shipping and cargo forwarding', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(8, 'CUST-2026-0008', 'Mindanao Mining Corp', 'Corporate', '890-123-456-007', 'Grace Santos', 'Supply Chain Head', '0924-890-1234', 'grace@mindanaomining.com', '505 Surigao, Caraga Region', '505 Surigao, Caraga Region', '45 Days', 800000.00, 'ACTIVE', 'Mining and mineral processing', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(9, 'CUST-2026-0009', 'Laguna Auto Parts Inc.', 'Regular', '901-234-567-008', 'Francisco Garcia', 'Purchasing Manager', '0925-901-2345', 'francisco@lagunaautoparts.com', '606 Laguna, Calabarzon', '606 Laguna, Calabarzon', '15 Days', 150000.00, 'ACTIVE', 'Automotive parts supplier', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(10, 'CUST-2026-0010', 'NCR Construction Corp', 'Corporate', '012-345-678-009', 'Leticia Mendez', 'Project Manager', '0926-012-3456', 'leticia@ncrconstruction.com', '707 Quezon City, Metro Manila', '707 Quezon City, Metro Manila', '30 Days', 450000.00, 'ACTIVE', 'Construction and infrastructure development', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_locations`
--

CREATE TABLE `tbl_customer_locations` (
  `location_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_locations`
--

INSERT INTO `tbl_customer_locations` (`location_id`, `customer_id`, `location_name`, `address`, `city`, `province`, `contact_person`, `contact_number`, `special_instructions`, `status`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 1, 'Main Plant', '123 Industrial Ave.', 'Laguna', 'Laguna', 'Maria Santos', '0917-123-4567', 'Main manufacturing facility', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(2, 1, 'Warehouse 1', '456 Storage Rd.', 'Laguna', 'Laguna', 'Jose Reyes', '0917-765-4321', 'Finished goods storage', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(3, 1, 'Distribution Center', '789 Logistics Ave.', 'Manila', 'Metro Manila', 'Ana Cruz', '0917-987-6543', 'Main distribution hub', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(4, 2, 'Head Office', '456 Commercial St.', 'Manila', 'Metro Manila', 'Juan Dela Cruz', '0918-234-5678', 'Main office', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(5, 2, 'Warehouse 2', '789 Cargo St.', 'Pasig', 'Metro Manila', 'Pedro Santos', '0918-876-5432', 'Main warehouse', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(6, 3, 'Davao Farm', '789 Davao City', 'Davao City', 'Davao', 'Pedro Reyes', '0919-345-6789', 'Main farm location', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(7, 3, 'Davao Packing Plant', '101 Packing Rd.', 'Davao City', 'Davao', 'Luz Reyes', '0919-987-6543', 'Packing and processing', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(8, 3, 'GenSan Distribution', '202 GenSan Blvd.', 'General Santos', 'South Cotabato', 'Ramon Cruz', '0919-654-3210', 'Distribution to Mindanao', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(9, 4, 'Cebu Main Store', '101 Cebu City', 'Cebu City', 'Cebu', 'Anna Lopez', '0920-456-7890', 'Main retail store', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(10, 4, 'Cebu Warehouse', '303 Storage St.', 'Mandaue City', 'Cebu', 'Carlos Lopez', '0920-987-6543', 'Inventory storage', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(11, 5, 'Pasig Operations', '202 Pasig City', 'Pasig City', 'Metro Manila', 'Ramon Cruz', '0921-567-8901', 'Main operations center', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(12, 5, 'Manila Port Office', '404 Port Area', 'Manila', 'Metro Manila', 'Elena Rivera', '0921-654-3210', 'Port operations', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(13, 6, 'Pampanga Plant', '303 Pampanga', 'San Fernando', 'Pampanga', 'Elena Rivera', '0922-678-9012', 'Food processing plant', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(14, 6, 'Pampanga Warehouse', '505 Storage Rd.', 'Mabalacat', 'Pampanga', 'Dante Cruz', '0922-987-6543', 'Raw materials storage', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(15, 7, 'Iloilo Terminal', '404 Iloilo City', 'Iloilo City', 'Iloilo', 'Mark Tan', '0923-789-0123', 'Main shipping terminal', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(16, 7, 'Cebu Port Office', '606 Port Area', 'Cebu City', 'Cebu', 'Grace Tan', '0923-654-3210', 'Cebu operations', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(17, 8, 'Surigao Mine', '505 Surigao', 'Surigao City', 'Surigao del Norte', 'Grace Santos', '0924-890-1234', 'Main mining site', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(18, 8, 'Davao Processing Plant', '707 Davao City', 'Davao City', 'Davao', 'Ramon Santos', '0924-987-6543', 'Mineral processing', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(19, 9, 'Laguna Plant', '606 Laguna', 'Santa Rosa', 'Laguna', 'Francisco Garcia', '0925-901-2345', 'Auto parts manufacturing', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(20, 9, 'Manila Showroom', '808 Manila Blvd.', 'Manila', 'Metro Manila', 'Teresita Garcia', '0925-654-3210', 'Showroom and retail', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(21, 10, 'Quezon City Office', '707 Quezon City', 'Quezon City', 'Metro Manila', 'Leticia Mendez', '0926-012-3456', 'Main office', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53'),
(22, 10, 'Bulacan Yard', '909 Bulacan', 'Malolos', 'Bulacan', 'Antonio Mendez', '0926-987-6543', 'Equipment and materials yard', 'ACTIVE', '2026-08-29 22:00:53', 'ADMIN', '2026-08-29 22:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_delivery_receipts`
--

CREATE TABLE `tbl_delivery_receipts` (
  `dr_id` int(11) NOT NULL,
  `dr_code` varchar(50) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `dispatch_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `dr_date` date NOT NULL,
  `dr_time` time DEFAULT NULL,
  `truck` varchar(50) DEFAULT NULL,
  `driver` varchar(100) DEFAULT NULL,
  `helper` varchar(100) DEFAULT NULL,
  `origin` text DEFAULT NULL,
  `destination` text DEFAULT NULL,
  `container_required` tinyint(1) DEFAULT 0,
  `container_number` varchar(50) DEFAULT NULL,
  `container_type` varchar(100) DEFAULT NULL,
  `container_reference` varchar(100) DEFAULT NULL,
  `dr_status` enum('PENDING','IN_TRANSIT','ARRIVED','DELIVERED','PARTIALLY_DELIVERED','CONTAINER_FOR_RETURN','CONTAINER_RETURNED','FAILED_DELIVERY','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_delivery_receipts`
--

INSERT INTO `tbl_delivery_receipts` (`dr_id`, `dr_code`, `trip_id`, `dispatch_id`, `customer_id`, `dr_date`, `dr_time`, `truck`, `driver`, `helper`, `origin`, `destination`, `container_required`, `container_number`, `container_type`, `container_reference`, `dr_status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(11, 'DR-2026-000001', 10, 9, 5, '2026-09-17', '09:56:00', 'DEF-9012', 'Pedro Reyes', 'Marcela Agoncillo', 'CAVITE', 'TAGUIG', 0, NULL, NULL, NULL, 'DELIVERED', 'test', '2026-09-17 09:56:32', 'admin', '2026-09-17 09:58:02'),
(12, 'DR-2026-000002', 12, 10, 3, '2026-09-17', '14:17:00', 'TRC-007 + CHS-007', 'Mark Tan', 'Juan Luna', 'MANILA', 'TAGUIG', 0, NULL, NULL, NULL, 'DELIVERED', 'TEST', '2026-09-17 14:17:39', 'admin', '2026-09-17 14:17:39');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_delivery_receipt_container_return`
--

CREATE TABLE `tbl_delivery_receipt_container_return` (
  `return_id` int(11) NOT NULL,
  `dr_id` int(11) NOT NULL,
  `container_return_required` tinyint(1) DEFAULT 0,
  `container_return_port` varchar(255) DEFAULT NULL,
  `container_return_date` date DEFAULT NULL,
  `container_return_time` time DEFAULT NULL,
  `container_return_status` enum('NOT_APPLICABLE','FOR_RETURN','RETURNED','OVERDUE','DAMAGED','LOST') DEFAULT 'NOT_APPLICABLE',
  `container_return_odometer` decimal(15,2) DEFAULT 0.00,
  `container_return_distance` decimal(15,2) DEFAULT 0.00,
  `container_return_proof` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_delivery_receipt_items`
--

CREATE TABLE `tbl_delivery_receipt_items` (
  `item_id` int(11) NOT NULL,
  `dr_id` int(11) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `quantity_dispatched` decimal(15,2) DEFAULT 0.00,
  `quantity_delivered` decimal(15,2) DEFAULT 0.00,
  `quantity_shortage` decimal(15,2) DEFAULT 0.00,
  `quantity_damaged` decimal(15,2) DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `weight` decimal(15,2) DEFAULT 0.00,
  `condition_on_arrival` enum('GOOD','FAIR','POOR','DAMAGED','SHORT','REJECTED') DEFAULT 'GOOD',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_delivery_receipt_items`
--

INSERT INTO `tbl_delivery_receipt_items` (`item_id`, `dr_id`, `item_description`, `quantity_dispatched`, `quantity_delivered`, `quantity_shortage`, `quantity_damaged`, `unit`, `weight`, `condition_on_arrival`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(11, 11, 'TEST', 1.00, 1.00, 0.00, 0.00, 'KG', 1.00, 'GOOD', 'TEST', '2026-09-17 09:56:32', 'admin', '2026-09-17 09:56:32'),
(12, 11, 'TEST 2', 1.00, 1.00, 0.00, 0.00, 'KG', 2.00, 'GOOD', 'TEST', '2026-09-17 09:56:32', 'admin', '2026-09-17 09:56:32'),
(13, 11, 'TEST 3', 1.00, 1.00, 0.00, 0.00, 'KG', 2.00, 'GOOD', 'TEST', '2026-09-17 09:56:32', 'admin', '2026-09-17 09:56:32'),
(14, 12, 'DUMBELLS', 5.00, 5.00, 0.00, 0.00, 'KG', 250.00, 'GOOD', 'TEST', '2026-09-17 14:17:39', 'admin', '2026-09-17 14:17:39');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_delivery_receipt_pod`
--

CREATE TABLE `tbl_delivery_receipt_pod` (
  `pod_id` int(11) NOT NULL,
  `dr_id` int(11) NOT NULL,
  `received_by` varchar(100) DEFAULT NULL,
  `received_by_position` varchar(100) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `time_received` time DEFAULT NULL,
  `quantity_received` varchar(100) DEFAULT NULL,
  `delivery_condition` enum('GOOD','PARTIAL','DAMAGED','REJECTED') DEFAULT 'GOOD',
  `customer_signature` varchar(255) DEFAULT NULL,
  `delivery_photo` varchar(255) DEFAULT NULL,
  `signed_dr` varchar(255) DEFAULT NULL,
  `supporting_documents` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_delivery_receipt_pod`
--

INSERT INTO `tbl_delivery_receipt_pod` (`pod_id`, `dr_id`, `received_by`, `received_by_position`, `date_received`, `time_received`, `quantity_received`, `delivery_condition`, `customer_signature`, `delivery_photo`, `signed_dr`, `supporting_documents`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(8, 11, 'JAMIE', 'test', '2026-09-17', '09:57:00', '1', 'GOOD', 'uploads/delivery_receipts/DR_11_SIGNATURE_1789610251.png', 'uploads/delivery_receipts/DR_11_DELIVERY_PHOTO_1789610264.jpg', 'uploads/delivery_receipts/DR_11_SIGNED_DR_1789610260.pdf', 'uploads/delivery_receipts/DR_11_SUPPORTING_DOCUMENTS_1789610271.pdf', 'test', '2026-09-17 09:57:31', 'admin', '2026-09-17 09:57:53'),
(9, 12, 'JAMIE', 'WAREHOUSE SUPERVISOR', '2026-09-17', '14:18:00', '5', 'GOOD', 'uploads/delivery_receipts/DR_12_SIGNATURE_1789625906.png', 'uploads/delivery_receipts/DR_12_DELIVERY_PHOTO_1789625911.jpg', 'uploads/delivery_receipts/DR_12_SIGNED_DR_1789625917.jpg', NULL, 'TEST', '2026-09-17 14:18:26', 'admin', '2026-09-17 14:18:40');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_dispatch`
--

CREATE TABLE `tbl_dispatch` (
  `dispatch_id` int(11) NOT NULL,
  `dispatch_code` varchar(50) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `dispatch_date` date NOT NULL,
  `dispatch_time` time DEFAULT NULL,
  `truck` varchar(50) DEFAULT NULL,
  `driver` varchar(100) DEFAULT NULL,
  `helper` varchar(100) DEFAULT NULL,
  `origin` text DEFAULT NULL,
  `destination` text DEFAULT NULL,
  `odometer_out` decimal(15,2) DEFAULT 0.00,
  `fuel_level_out` decimal(5,2) DEFAULT 0.00,
  `container_required` tinyint(1) DEFAULT 0,
  `container_number` varchar(50) DEFAULT NULL,
  `container_type` varchar(100) DEFAULT NULL,
  `container_description` text DEFAULT NULL,
  `container_markings` varchar(100) DEFAULT NULL,
  `container_reference` varchar(100) DEFAULT NULL,
  `container_release_port` varchar(255) DEFAULT NULL,
  `container_release_date` date DEFAULT NULL,
  `container_release_time` time DEFAULT NULL,
  `container_return_required` tinyint(1) DEFAULT 0,
  `container_return_date` date DEFAULT NULL,
  `container_return_time` time DEFAULT NULL,
  `container_return_port` varchar(255) DEFAULT NULL,
  `container_return_odometer` decimal(15,2) DEFAULT 0.00,
  `container_return_status` enum('NOT_APPLICABLE','FOR_RETURN','RETURNED','OVERDUE','DAMAGED','LOST') DEFAULT 'NOT_APPLICABLE',
  `container_return_proof` varchar(255) DEFAULT NULL,
  `dispatcher_name` varchar(100) DEFAULT NULL,
  `dispatch_status` enum('PENDING','DISPATCHED','IN_TRANSIT','DELIVERED','COMPLETED','CANCELLED') DEFAULT 'PENDING',
  `actual_delivery_date` date DEFAULT NULL,
  `actual_delivery_time` time DEFAULT NULL,
  `odometer_in` decimal(15,2) DEFAULT 0.00,
  `fuel_level_in` decimal(5,2) DEFAULT 0.00,
  `total_distance` decimal(15,2) DEFAULT 0.00,
  `fuel_consumed` decimal(15,2) DEFAULT 0.00,
  `delay_reason` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_dispatch`
--

INSERT INTO `tbl_dispatch` (`dispatch_id`, `dispatch_code`, `trip_id`, `dispatch_date`, `dispatch_time`, `truck`, `driver`, `helper`, `origin`, `destination`, `odometer_out`, `fuel_level_out`, `container_required`, `container_number`, `container_type`, `container_description`, `container_markings`, `container_reference`, `container_release_port`, `container_release_date`, `container_release_time`, `container_return_required`, `container_return_date`, `container_return_time`, `container_return_port`, `container_return_odometer`, `container_return_status`, `container_return_proof`, `dispatcher_name`, `dispatch_status`, `actual_delivery_date`, `actual_delivery_time`, `odometer_in`, `fuel_level_in`, `total_distance`, `fuel_consumed`, `delay_reason`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(3, 'DSP-2026-000001', 2, '2026-09-04', '06:00:00', 'DEF-9012', 'Grace Santos', 'Emilio Aguinaldo', 'MANILA', 'LAGUNA', 0.00, 100.00, 0, '', '', '', '', '', '', '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', '', 0.00, 'NOT_APPLICABLE', '', 'test', 'IN_TRANSIT', '0000-00-00', '00:00:00', 0.00, 0.00, 0.00, 100.00, '', '', '2026-09-04 08:14:01', 'ADMIN', '2026-09-16 10:16:56'),
(4, 'DSP-2026-000002', 3, '2026-09-04', '06:00:00', 'TRC-002 + CHS-002', 'Grace Santos', 'Emilio Aguinaldo', 'MANILA', 'CAVITE', 0.00, 0.00, 1, 'test', '40ft dry', 'test', '1231', 'CONT-S1231', '', '2026-09-04', '08:16:00', 0, '0000-00-00', '00:00:00', '', 0.00, 'NOT_APPLICABLE', '', 'test', 'IN_TRANSIT', '0000-00-00', '00:00:00', 0.00, 0.00, 0.00, 0.00, '', '', '2026-09-04 08:16:58', 'ADMIN', '2026-09-16 10:16:49'),
(5, 'DSP-2026-000003', 4, '2026-09-04', '06:00:00', 'TRC-007 + CHS-007', 'Ramon Cruz', 'Melchora Aquino', 'manila', 'cebu', 0.00, 0.00, 1, 'ACNADA', '40FT DRY', 'TEST', '123', 'CONT-1231231', 'MANILA PORT', '2026-09-04', '09:18:00', 0, '0000-00-00', '00:00:00', '', 0.00, 'NOT_APPLICABLE', '', 'eliesa', 'IN_TRANSIT', '0000-00-00', '00:00:00', 0.00, 0.00, 0.00, 0.00, '', '', '2026-09-04 09:18:40', 'ADMIN', '2026-09-04 09:34:35'),
(6, 'DSP-2026-000004', 5, '2026-09-04', '06:00:00', 'STU-9012', 'Mark Tan', 'KYLE HELPER', 'MANILA', 'VALENZUELA', 51200.00, 50.00, 0, '', '', '', '', '', '', '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', '', 0.00, 'NOT_APPLICABLE', '', 'SIR OLI', 'IN_TRANSIT', '0000-00-00', '00:00:00', 0.00, 0.00, 0.00, 50.00, '', '', '2026-09-04 10:12:47', 'ADMIN', '2026-09-04 10:13:37'),
(7, 'DSP-2026-000005', 6, '2026-09-04', '06:00:00', 'TRC-010 + CHS-010 (Rented)', 'Maria Santos', 'Marcela Agoncillo', 'MANILA', 'DAVA', 2340.00, 50.00, 0, '', '', '', '', '', '', '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', '', 0.00, 'NOT_APPLICABLE', '', 'KYLE', 'IN_TRANSIT', '0000-00-00', '00:00:00', 0.00, 0.00, 0.00, 50.00, '', '', '2026-09-04 14:11:19', 'ADMIN', '2026-09-04 14:13:48'),
(8, 'DSP-2026-000006', 7, '2026-09-04', '06:00:00', 'XYZ-5678', 'Pedro Reyes', 'Marcela Agoncillo', 'MANILA', 'LAGUNA', 120000.00, 50.00, 0, '', '', '', '', '', '', '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', '', 0.00, 'NOT_APPLICABLE', '', 'kyle', 'IN_TRANSIT', '0000-00-00', '00:00:00', 0.00, 0.00, 0.00, 50.00, '', '', '2026-09-04 15:15:26', 'ADMIN', '2026-09-16 10:16:38'),
(9, 'DSP-2026-000007', 10, '2026-09-17', '06:00:00', 'DEF-9012', 'Pedro Reyes', 'Marcela Agoncillo', 'CAVITE', 'TAGUIG', 1231231.00, 0.00, 0, '', '', '', '', '', '', '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', '', 0.00, 'NOT_APPLICABLE', '', 'KYLE', 'COMPLETED', '0000-00-00', '00:00:00', 0.00, 0.00, 0.00, 0.00, '', '', '2026-09-17 09:38:34', 'admin', '2026-09-17 09:58:41'),
(10, 'DSP-2026-000008', 12, '2026-09-17', '06:00:00', 'TRC-007 + CHS-007', 'Mark Tan', 'Juan Luna', 'MANILA', 'TAGUIG', 0.00, 0.00, 1, 'ABC1231', '40ft dry', 'test', 'ABC', 'CONT-S1231', 'Manila Port', '2026-09-17', '14:08:00', 1, '2026-09-18', '14:09:00', 'Manila Port', 26563.00, 'FOR_RETURN', 'asdasasd', '', 'COMPLETED', '2026-09-17', '00:00:00', 263.00, 999.99, 263.00, 0.00, '', '', '2026-09-17 14:09:27', 'admin', '2026-09-17 14:34:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_dispatch_checklist`
--

CREATE TABLE `tbl_dispatch_checklist` (
  `checklist_id` int(11) NOT NULL,
  `checklist_code` varchar(50) NOT NULL,
  `dispatch_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `available_quantity` int(11) DEFAULT 0,
  `required_quantity` int(11) DEFAULT 0,
  `condition_before` enum('GOOD','FAIR','POOR','DAMAGED','MISSING') DEFAULT 'GOOD',
  `checked` tinyint(1) DEFAULT 1,
  `accountable_person` varchar(100) DEFAULT NULL,
  `checklist_status` enum('COMPLETE','INCOMPLETE','MISSING','DAMAGED','NOT_APPLICABLE') DEFAULT 'COMPLETE',
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_dispatch_checklist`
--

INSERT INTO `tbl_dispatch_checklist` (`checklist_id`, `checklist_code`, `dispatch_id`, `trip_id`, `item_name`, `available_quantity`, `required_quantity`, `condition_before`, `checked`, `accountable_person`, `checklist_status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(2, 'CHK-2026-000001', 6, 5, 'Jack', 1, 1, 'GOOD', 1, 'Driver', 'COMPLETE', '', '2026-09-04 10:13:07', 'ADMIN', '2026-09-04 10:13:07'),
(3, 'CHK-2026-000002', 8, 7, 'Jack', 1, 1, 'GOOD', 1, 'driver', 'COMPLETE', '', '2026-09-04 15:16:11', 'ADMIN', '2026-09-04 15:16:11');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_dispatch_expenses`
--

CREATE TABLE `tbl_dispatch_expenses` (
  `expense_id` int(11) NOT NULL,
  `expense_code` varchar(50) NOT NULL,
  `dispatch_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `expense_type` enum('TOLL','PARKING','FUEL','MEALS','LOADING_UNLOADING','CONTAINER_RENTAL','EMPTY_RETURN_FEE','CONTAINER_RETURN_FEE','OTHER') DEFAULT 'OTHER',
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT 0.00,
  `paid_by` varchar(100) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `receipt_attachment` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_dispatch_expenses`
--

INSERT INTO `tbl_dispatch_expenses` (`expense_id`, `expense_code`, `dispatch_id`, `trip_id`, `expense_date`, `expense_type`, `description`, `amount`, `paid_by`, `reference_no`, `receipt_attachment`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(3, 'EXP-2026-000001', 8, 7, '2026-09-04', 'FUEL', 'test', 5000.00, 'driver', '10231', NULL, '', '2026-09-04 15:15:57', 'ADMIN', '2026-09-04 15:15:57');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_drivers`
--

CREATE TABLE `tbl_drivers` (
  `driver_id` int(11) NOT NULL,
  `driver_code` varchar(50) NOT NULL,
  `driver_name` varchar(200) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `driver_status` enum('AVAILABLE','ASSIGNED','ON TRIP','RETURNING','ON LEAVE','SUSPENDED','INACTIVE') NOT NULL DEFAULT 'AVAILABLE',
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `years_experience` int(3) DEFAULT 0,
  `heavy_vehicle_experience` int(3) DEFAULT 0,
  `tractor_head_experience` int(3) DEFAULT 0,
  `ten_wheeler_experience` int(3) DEFAULT 0,
  `long_distance_experience` enum('YES','NO') DEFAULT 'NO',
  `city_urban_experience` enum('YES','NO') DEFAULT 'NO',
  `highway_experience` enum('YES','NO') DEFAULT 'NO',
  `route_experience` varchar(255) DEFAULT NULL,
  `cargo_handling_experience` varchar(100) DEFAULT NULL,
  `defensive_driving_training` enum('YES','NO') DEFAULT 'NO',
  `safety_training` enum('YES','NO') DEFAULT 'NO',
  `other_certifications` text DEFAULT NULL,
  `training_expiration_date` date DEFAULT NULL,
  `qualification_remarks` text DEFAULT NULL,
  `overall_rating` decimal(3,1) DEFAULT 0.0,
  `rating_date` date DEFAULT NULL,
  `evaluated_by` varchar(100) DEFAULT NULL,
  `evaluation_remarks` text DEFAULT NULL,
  `previous_rating` decimal(3,1) DEFAULT 0.0,
  `license_number` varchar(50) DEFAULT NULL,
  `license_type` varchar(50) DEFAULT NULL,
  `restriction_code` varchar(50) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `license_status` enum('VALID','EXPIRING','EXPIRED','NONE') NOT NULL DEFAULT 'NONE',
  `license_attachment` varchar(255) DEFAULT NULL,
  `has_license` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_drivers`
--

INSERT INTO `tbl_drivers` (`driver_id`, `driver_code`, `driver_name`, `contact_number`, `address`, `employment_type`, `date_hired`, `driver_status`, `emergency_contact`, `emergency_contact_number`, `profile_picture`, `years_experience`, `heavy_vehicle_experience`, `tractor_head_experience`, `ten_wheeler_experience`, `long_distance_experience`, `city_urban_experience`, `highway_experience`, `route_experience`, `cargo_handling_experience`, `defensive_driving_training`, `safety_training`, `other_certifications`, `training_expiration_date`, `qualification_remarks`, `overall_rating`, `rating_date`, `evaluated_by`, `evaluation_remarks`, `previous_rating`, `license_number`, `license_type`, `restriction_code`, `issue_date`, `expiration_date`, `license_status`, `license_attachment`, `has_license`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'DRV-2026-0001', 'kyle', 'test', 'test', 'Regular', '2026-08-28', 'ASSIGNED', 'test', 'test', 'uploads/drivers/DRV_1_1787903611.jpg', 1, 1, 1, 1, 'NO', 'NO', 'NO', 'test', 'test', 'NO', 'NO', '', '0000-00-00', 'test', 4.5, '2026-08-28', 'test', 'test', 0.0, 'test', 'Professional', '1,2,3,8', '2026-08-28', '2032-07-07', 'VALID', 'uploads/driver_licenses/DRV_LIC_1_1787903611.png', 'YES', '2026-08-28 15:53:31', 'ADMIN', '2026-09-03 22:04:55'),
(12, 'DRV-2026-0001', 'Juan Dela Cruz', '0917-123-4567', '123 Mabini St., Manila', 'null', '2020-01-15', 'ASSIGNED', 'Maria Dela Cruz', '0917-765-4321', 'uploads/drivers/DRV_12_1788012288.jpg', 15, 10, 5, 8, 'YES', 'YES', 'YES', 'Manila-Laguna, Cavite', 'General Cargo', 'YES', 'YES', 'ISO 9001 Certified', '2025-12-31', 'Experienced driver with excellent record', 4.8, '2025-01-15', 'Operations Manager', 'Highly recommended', 4.5, 'L-12345678', 'Professional', 'B,C,D,E', '2020-01-15', '2027-01-15', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-09-02 08:48:29'),
(13, 'DRV-2026-0002', 'Maria Santos', '0917-234-5678', '456 Rizal Ave., Cebu', 'REGULAR', '2021-03-20', 'AVAILABLE', 'Jose Santos', '0917-876-5432', NULL, 12, 8, 3, 6, 'YES', 'YES', 'YES', 'Cebu-Davao, Bacolod', 'Heavy Equipment', 'YES', 'YES', 'Forklift Certified', '2026-06-30', 'Professional driver with heavy equipment experience', 4.5, '2025-01-20', 'Fleet Manager', 'Good performance', 4.2, 'L-23456789', 'Professional', 'B,C,D', '2021-03-20', '2028-03-20', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-08-30 21:25:50'),
(14, 'DRV-2026-0003', 'Pedro Reyes', '0917-345-6789', '789 Bonifacio St., Davao', 'CONTRACTUAL', '2022-06-01', 'AVAILABLE', 'Luz Reyes', '0917-987-6543', NULL, 8, 5, 2, 4, 'NO', 'YES', 'YES', 'Davao-GenSan, Cotabato', 'Agricultural Products', 'YES', 'YES', NULL, '2025-09-30', 'Reliable driver with good local knowledge', 4.2, '2025-01-25', 'Operations Supervisor', 'Satisfactory', 4.0, 'L-34567890', 'Professional', 'B,C', '2022-06-01', '2029-06-01', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-08-30 21:25:50'),
(15, 'DRV-2026-0004', 'Anna Lopez', '0917-456-7890', '101 Luna St., Pampanga', 'REGULAR', '2019-11-01', 'ASSIGNED', 'Carlos Lopez', '0917-098-7654', NULL, 18, 12, 8, 10, 'YES', 'YES', 'YES', 'Pampanga-Manila, Bataan, Zambales', 'Construction Materials', 'YES', 'YES', 'Hazardous Materials Certified', '2024-12-31', 'Highly experienced with excellent safety record', 4.9, '2025-01-10', 'Safety Officer', 'Top performer', 4.7, 'L-45678901', 'Professional', 'B,C,D,E', '2019-11-01', '2026-11-01', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-08-30 21:32:03'),
(16, 'DRV-2026-0005', 'Ramon Cruz', '0917-567-8901', '202 Magsaysay Blvd., Cebu', 'REGULAR', '2020-08-15', 'AVAILABLE', 'Elena Cruz', '0917-109-8765', NULL, 10, 7, 4, 5, 'YES', 'YES', 'YES', 'Cebu-Leyte, Samar, Bohol', 'General Merchandise', 'YES', 'YES', 'First Aid Certified', '2025-10-31', 'Dependable driver with good customer service', 4.4, '2025-02-01', 'Customer Service Manager', 'Good feedback from clients', 4.1, 'L-56789012', 'Professional', 'B,C,D', '2020-08-15', '2027-08-15', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-08-29 21:22:30'),
(17, 'DRV-2026-0006', 'Elena Rivera', '0917-678-9012', '303 Aguinaldo St., Manila', 'CONTRACTUAL', '2023-01-10', 'ASSIGNED', 'Roberto Rivera', '0917-210-9876', NULL, 6, 4, 1, 3, 'NO', 'YES', 'YES', 'Manila-Quezon, Rizal', 'Retail Goods', 'YES', 'YES', NULL, '2026-03-31', 'New but competent driver', 4.0, '2025-02-05', 'Fleet Supervisor', 'Good potential', 3.8, 'L-67890123', 'Professional', 'B,C', '2023-01-10', '2030-01-10', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-08-30 21:50:33'),
(18, 'DRV-2026-0007', 'Mark Tan', '0917-789-0123', '404 Gomez St., Davao', 'REGULAR', '2021-04-01', 'AVAILABLE', 'Grace Tan', '0917-321-0987', NULL, 14, 9, 6, 7, 'YES', 'YES', 'YES', 'Davao-CDO, Butuan, Surigao', 'Agricultural Products', 'YES', 'YES', 'Rigging Certified', '2025-08-31', 'Experienced in Mindanao routes', 4.6, '2025-01-30', 'Regional Manager', 'Excellent performance', 4.3, 'L-78901234', 'Professional', 'B,C,D,E', '2021-04-01', '2028-04-01', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-08-30 21:25:50'),
(19, 'DRV-2026-0008', 'Grace Santos', '0917-890-1234', '505 Quezon Ave., Cebu', 'REGULAR', '2020-02-20', 'AVAILABLE', 'Daniel Santos', '0917-432-1098', NULL, 16, 11, 5, 9, 'YES', 'YES', 'YES', 'Cebu-Manila, Cebu-Iloilo', 'Heavy Machinery', 'YES', 'YES', 'Crane Operator Certified', '2025-11-30', 'Very reliable with heavy equipment', 4.7, '2025-02-10', 'Operations Director', 'Highly skilled', 4.4, 'L-89012345', 'Professional', 'B,C,D', '2020-02-20', '2027-02-20', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-09-03 22:14:22'),
(20, 'DRV-2026-0009', 'Francisco Garcia', '0917-901-2345', '606 Mabuhay St., Manila', 'CONTRACTUAL', '2022-09-01', 'ASSIGNED', 'Teresita Garcia', '0917-543-2109', NULL, 7, 4, 2, 3, 'NO', 'YES', 'YES', 'Manila-Batangas, Laguna', 'Food Products', 'YES', 'YES', NULL, '2025-07-31', 'Good driver with positive attitude', 4.1, '2025-01-28', 'Operations Supervisor', 'Good team player', 3.9, 'L-90123456', 'Professional', 'B,C', '2022-09-01', '2029-09-01', 'VALID', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-08-30 22:02:55'),
(21, 'DRV-2026-0010', 'Leticia Mendez', '0917-012-3456', '707 Rizal St., Pampanga', 'Regular', '2019-07-01', 'ASSIGNED', 'Antonio Mendez', '0917-654-3210', NULL, 20, 15, 10, 12, 'YES', 'YES', 'YES', 'Pampanga-Manila, Tarlac, Nueva Ecija', 'All Types', 'YES', 'YES', 'Master Driver Certification', '2024-12-31', 'Most experienced driver with 20 years', 5.0, '2025-01-05', 'Fleet Manager', 'Outstanding driver', 4.8, 'L-01234567', 'Professional', 'B,C,D,E', '2019-07-01', '2026-07-01', 'EXPIRED', NULL, 'YES', '2026-08-29 21:22:30', 'ADMIN', '2026-09-03 22:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_driver_skillsets`
--

CREATE TABLE `tbl_driver_skillsets` (
  `skillset_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `rating` int(1) NOT NULL DEFAULT 0,
  `rating_date` date DEFAULT NULL,
  `evaluated_by` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_driver_skillsets`
--

INSERT INTO `tbl_driver_skillsets` (`skillset_id`, `driver_id`, `skill_name`, `rating`, `rating_date`, `evaluated_by`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 1, 'Heavy Truck Driving', 4, '2026-08-28', 'rarara', 'test', '2026-08-28 16:01:36', 'ADMIN', '2026-08-29 13:49:00'),
(2, 1, 'Maneuvering / Reverse Parking', 5, '2026-08-28', 'test', 'test', '2026-08-28 16:01:42', 'ADMIN', '2026-08-28 16:01:42'),
(3, 21, 'City / Urban Driving', 5, '2026-08-29', 'JOAN', 'TEST', '2026-08-29 22:50:06', 'ADMIN', '2026-08-29 22:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_helpers`
--

CREATE TABLE `tbl_helpers` (
  `helper_id` int(11) NOT NULL,
  `helper_code` varchar(50) NOT NULL,
  `helper_name` varchar(200) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `helper_status` enum('AVAILABLE','ASSIGNED','ON TRIP','ON LEAVE','INACTIVE') NOT NULL DEFAULT 'AVAILABLE',
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `license_type` varchar(50) DEFAULT NULL,
  `restriction_code` varchar(50) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `license_status` enum('VALID','EXPIRING','EXPIRED','NONE') NOT NULL DEFAULT 'NONE',
  `license_attachment` varchar(255) DEFAULT NULL,
  `has_license` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_helpers`
--

INSERT INTO `tbl_helpers` (`helper_id`, `helper_code`, `helper_name`, `contact_number`, `address`, `employment_type`, `date_hired`, `helper_status`, `emergency_contact`, `emergency_contact_number`, `profile_picture`, `license_number`, `license_type`, `restriction_code`, `expiration_date`, `license_status`, `license_attachment`, `has_license`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'HLP-2026-0001', 'KYLE HELPER', '0123120310', 'sdfsd', 'Probationary', '2026-08-28', 'AVAILABLE', 'yrdy', '', 'uploads/helpers/HLP_1_1787901447.jpg', 'test', 'Professional', '1,2,3,8', '2031-06-25', 'VALID', 'uploads/helper_licenses/HLP_LIC_1_1787901447.jpg', 'YES', '2026-08-28 15:17:27', 'ADMIN', '2026-08-30 21:26:08'),
(12, 'HLP-2026-0001', 'Andres Bonifacio', '0918-123-4567', '123 Tondo St., Manila', 'REGULAR', '2020-01-20', 'ASSIGNED', 'Gregoria Bonifacio', '0918-765-4321', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-08-30 21:32:03'),
(13, 'HLP-2026-0002', 'Emilio Aguinaldo', '0918-234-5678', '456 Kawit St., Cavite', 'REGULAR', '2021-04-15', 'AVAILABLE', 'Hilaria Aguinaldo', '0918-876-5432', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-09-03 22:14:22'),
(14, 'HLP-2026-0003', 'Jose Rizal', '0918-345-6789', '789 Calamba St., Laguna', 'REGULAR', '2020-06-01', 'ASSIGNED', 'Teodora Rizal', '0918-987-6543', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-09-03 22:14:22'),
(15, 'HLP-2026-0004', 'Gabriela Silang', '0918-456-7890', '101 Vigan St., Ilocos', 'CONTRACTUAL', '2022-08-10', 'ASSIGNED', 'Diego Silang', '0918-098-7654', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-09-02 08:48:29'),
(16, 'HLP-2026-0005', 'Diego Silang', '0918-567-8901', '202 Abra St., Ilocos', 'REGULAR', '2020-11-01', 'ASSIGNED', 'Gabriela Silang', '0918-109-8765', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-08-30 22:02:55'),
(17, 'HLP-2026-0006', 'Antonio Luna', '0918-678-9012', '303 Badoc St., Ilocos', 'CONTRACTUAL', '2023-02-15', 'ASSIGNED', 'Jose Luna', '0918-210-9876', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-08-30 21:50:33'),
(18, 'HLP-2026-0007', 'Gregorio Del Pilar', '0918-789-0123', '404 Bulacan St., Bulacan', 'REGULAR', '2021-07-01', 'ASSIGNED', 'Marcela Del Pilar', '0918-321-0987', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-09-03 22:04:55'),
(19, 'HLP-2026-0008', 'Juan Luna', '0918-890-1234', '505 Badoc St., Ilocos', 'REGULAR', '2020-03-01', 'AVAILABLE', 'Antonio Luna', '0918-432-1098', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-08-30 21:26:08'),
(20, 'HLP-2026-0009', 'Marcela Agoncillo', '0918-901-2345', '606 Taal St., Batangas', 'CONTRACTUAL', '2022-10-01', 'AVAILABLE', 'Felipe Agoncillo', '0918-543-2109', NULL, NULL, NULL, NULL, NULL, 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-08-30 21:26:08'),
(21, 'HLP-2026-0010', 'Melchora Aquino', '0918-012-3456', '707 Caloocan St., Caloocan', 'null', '2019-12-01', 'AVAILABLE', 'Juan Aquino', '0918-654-3210', 'uploads/helpers/HLP_21_1788505289.jpg', '', '', '', '0000-00-00', 'NONE', NULL, 'NO', '2026-08-29 21:22:53', 'ADMIN', '2026-09-04 15:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_maintenance_parts`
--

CREATE TABLE `tbl_maintenance_parts` (
  `part_id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `quantity` decimal(15,2) DEFAULT 1.00,
  `unit_cost` decimal(15,2) DEFAULT 0.00,
  `total_cost` decimal(15,2) DEFAULT 0.00,
  `supplier` varchar(200) DEFAULT NULL,
  `warranty` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_maintenance_parts`
--

INSERT INTO `tbl_maintenance_parts` (`part_id`, `record_id`, `part_name`, `quantity`, `unit_cost`, `total_cost`, `supplier`, `warranty`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(26, 10, 'Cabin Filter', 1.00, 1300.00, 1300.00, 'FilterPro', '6 Months', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(27, 11, 'Radiator', 1.00, 15000.00, 15000.00, 'CoolTech Supply', '12 Months', 'OEM replacement', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(28, 11, 'Coolant', 5.00, 420.00, 2100.00, 'CoolTech Supply', 'N/A', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(29, 11, 'Coolant Hoses', 2.00, 450.00, 900.00, 'CoolTech Supply', '6 Months', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(30, 11, 'Thermostat', 1.00, 500.00, 500.00, 'CoolTech Supply', '6 Months', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(31, 13, 'Transmission Fluid', 6.00, 850.00, 5100.00, 'TransParts Co.', 'N/A', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(32, 13, 'Transmission Filter', 1.00, 1400.00, 1400.00, 'TransParts Co.', '12 Months', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(33, 14, 'Engine Oil (15W-40)', 12.00, 350.00, 4200.00, 'XYZ Auto Parts', 'N/A', 'Tractor head', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(34, 14, 'Oil Filter', 2.00, 550.00, 1100.00, 'XYZ Auto Parts', '6 Months', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(35, 14, 'Fuel Filter', 1.00, 750.00, 750.00, 'XYZ Auto Parts', '6 Months', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(36, 14, 'Grease', 4.00, 400.00, 1600.00, 'XYZ Auto Parts', 'N/A', 'Multi-purpose grease', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(37, 15, 'Brake Shoe Set', 2.00, 650.00, 1300.00, 'TrailerPro', '12 Months', 'Trailer brakes', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(38, 15, 'Grease', 2.00, 250.00, 500.00, 'TrailerPro', 'N/A', '', '2026-09-17 14:47:32', 'ADMIN', '2026-09-17 14:47:32'),
(39, 17, 'OIL', 1.00, 500.00, 500.00, 'TEST', '', '', '2026-09-17 15:02:30', 'admin', '2026-09-17 15:02:30'),
(40, 17, 'GREASE', 1.00, 1000.00, 1000.00, 'TEST', '', NULL, '2026-09-17 15:02:34', 'admin', '2026-09-17 15:02:34'),
(41, 18, 'OIL', 5.00, 1000.00, 5000.00, 'test', '', '', '2026-09-17 15:35:03', 'admin', '2026-09-17 15:35:03'),
(42, 19, 'OIL', 6.00, 1500.00, 9000.00, '', '', '', '2026-09-17 15:45:13', 'admin', '2026-09-17 15:45:13'),
(43, 20, 'OIL', 6.00, 1500.00, 9000.00, '', '', '', '2026-09-17 15:47:18', 'admin', '2026-09-17 15:47:18'),
(44, 20, 'GREASE', 3.00, 500.00, 1500.00, '', '', '', '2026-09-17 15:47:18', 'admin', '2026-09-17 15:47:18'),
(45, 20, 'BRAKE PADS', 6.00, 2000.00, 12000.00, '', '', '', '2026-09-17 15:47:18', 'admin', '2026-09-17 15:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_maintenance_records`
--

CREATE TABLE `tbl_maintenance_records` (
  `record_id` int(11) NOT NULL,
  `record_code` varchar(50) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `truck_id` int(11) NOT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `maintenance_date` date NOT NULL,
  `odometer` decimal(15,2) DEFAULT 0.00,
  `maintenance_type` enum('PREVENTIVE','CORRECTIVE','EMERGENCY') NOT NULL DEFAULT 'PREVENTIVE',
  `service_category` varchar(100) DEFAULT NULL,
  `problem_reason` text DEFAULT NULL,
  `work_performed` text DEFAULT NULL,
  `technician` varchar(100) DEFAULT NULL,
  `labor_cost` decimal(15,2) DEFAULT 0.00,
  `parts_cost` decimal(15,2) DEFAULT 0.00,
  `other_cost` decimal(15,2) DEFAULT 0.00,
  `total_cost` decimal(15,2) DEFAULT 0.00,
  `downtime_hours` decimal(10,2) DEFAULT 0.00,
  `status` enum('ONGOING','COMPLETED','CANCELLED') NOT NULL DEFAULT 'COMPLETED',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_maintenance_records`
--

INSERT INTO `tbl_maintenance_records` (`record_id`, `record_code`, `schedule_id`, `truck_id`, `truck_plate`, `maintenance_date`, `odometer`, `maintenance_type`, `service_category`, `problem_reason`, `work_performed`, `technician`, `labor_cost`, `parts_cost`, `other_cost`, `total_cost`, `downtime_hours`, `status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(17, 'MTR-2026-000001', 16, 6, 'ABC-1234', '2026-09-17', 55500.00, 'PREVENTIVE', 'PMS', 'FOR PMS', 'PERFORM PMS', 'kyle', 5000.00, 1500.00, 0.00, 6500.00, 10.00, 'COMPLETED', '', '2026-09-17 15:02:30', 'admin', '2026-09-17 15:02:34'),
(18, 'MTR-2026-000002', 17, 1, 'ABC-12345', '2026-09-17', 40000.00, 'PREVENTIVE', 'PMS', 'SUBJECT FOR PMS', 'PMS', 'kyle', 5000.00, 5000.00, 0.00, 10000.00, 5.00, 'COMPLETED', 'TEST', '2026-09-17 15:35:03', 'admin', '2026-09-17 15:35:03'),
(19, 'MTR-2026-000003', 18, 26, 'CHS-001', '2026-09-17', 23400.00, 'PREVENTIVE', 'OIL CHANGE', 'SUBJECT TO OIL CHANGE', 'CHANGE OIL', 'TEST', 1500.00, 9000.00, 0.00, 10500.00, 2.00, 'COMPLETED', 'TEST', '2026-09-17 15:45:13', 'admin', '2026-09-17 15:45:13'),
(20, 'MTR-2026-000004', 19, 27, 'CHS-002', '2026-09-17', 45600.00, 'PREVENTIVE', 'PMS', 'PMS', 'PMS', 'tsad', 3000.00, 22500.00, 0.00, 25500.00, 5.00, 'COMPLETED', 'TEST', '2026-09-17 15:47:17', 'admin', '2026-09-17 15:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_maintenance_schedules`
--

CREATE TABLE `tbl_maintenance_schedules` (
  `schedule_id` int(11) NOT NULL,
  `schedule_code` varchar(50) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `maintenance_type` enum('PREVENTIVE','CORRECTIVE','EMERGENCY') NOT NULL DEFAULT 'PREVENTIVE',
  `service_type` varchar(100) DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `current_odometer` decimal(15,2) DEFAULT 0.00,
  `service_interval` decimal(15,2) DEFAULT 0.00,
  `next_service_odometer` decimal(15,2) DEFAULT 0.00,
  `technician` varchar(100) DEFAULT NULL,
  `priority` enum('LOW','NORMAL','HIGH','URGENT') DEFAULT 'NORMAL',
  `status` enum('SCHEDULED','IN_PROGRESS','COMPLETED','CANCELLED','OVERDUE') NOT NULL DEFAULT 'SCHEDULED',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_maintenance_schedules`
--

INSERT INTO `tbl_maintenance_schedules` (`schedule_id`, `schedule_code`, `truck_id`, `truck_plate`, `maintenance_type`, `service_type`, `scheduled_date`, `current_odometer`, `service_interval`, `next_service_odometer`, `technician`, `priority`, `status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(16, 'MTS-2026-000001', 6, 'ABC-1234', 'PREVENTIVE', 'PMS', '2026-09-17', 45230.50, 10000.00, 55230.50, 'kyle', 'NORMAL', 'COMPLETED', 'test', '2026-09-17 15:00:55', 'admin', '2026-09-17 15:02:30'),
(17, 'MTS-2026-000002', 1, 'ABC-12345', 'PREVENTIVE', 'PM', '2026-09-17', 30000.00, 10000.00, 40000.00, 'kyle', 'NORMAL', 'COMPLETED', 'TEST', '2026-09-17 15:34:10', 'admin', '2026-09-17 15:35:03'),
(18, 'MTS-2026-000003', 26, 'CHS-001', 'PREVENTIVE', 'OIL CHANGE', '2026-09-17', 23400.00, 10000.00, 33400.00, 'TEST', 'NORMAL', 'COMPLETED', 'TEST', '2026-09-17 15:44:40', 'admin', '2026-09-17 15:45:13'),
(19, 'MTS-2026-000004', 27, 'CHS-002', 'PREVENTIVE', 'PMS', '2026-09-17', 45600.00, 10000.00, 55600.00, 'tsad', 'NORMAL', 'COMPLETED', 'asda', '2026-09-17 15:46:16', 'admin', '2026-09-17 15:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_range_assistants`
--

CREATE TABLE `tbl_range_assistants` (
  `assistant_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `badge_number` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_range_assistants`
--

INSERT INTO `tbl_range_assistants` (`assistant_id`, `full_name`, `badge_number`, `position`, `status`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'PO1 Mark Reyes', '#2345', 'Range Assistantsss', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-08-28 08:13:49'),
(2, 'PO2 Sarah Cruz', '#6789', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(3, 'PO3 John Santos', '#3456', 'Senior Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(4, 'PO1 Mike Tan', '#7890', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(5, 'PO2 Anna Garcia', '#4567', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(6, 'PO3 David Lim', '#8901', 'Range Officer', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(7, 'PO1 elek baraykay', '#5678121', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-08-28 08:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_supplies`
--

CREATE TABLE `tbl_supplies` (
  `supply_id` int(11) NOT NULL,
  `supply_code` varchar(50) NOT NULL,
  `supply_name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `current_stock` decimal(15,2) DEFAULT 0.00,
  `minimum_stock` decimal(15,2) DEFAULT 0.00,
  `reorder_level` decimal(15,2) DEFAULT 0.00,
  `unit_cost` decimal(15,2) DEFAULT 0.00,
  `supplier` varchar(200) DEFAULT NULL,
  `storage_location` varchar(200) DEFAULT NULL,
  `status` enum('IN_STOCK','LOW_STOCK','OUT_OF_STOCK','DISCONTINUED') NOT NULL DEFAULT 'IN_STOCK',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_supplies`
--

INSERT INTO `tbl_supplies` (`supply_id`, `supply_code`, `supply_name`, `category`, `unit`, `current_stock`, `minimum_stock`, `reorder_level`, `unit_cost`, `supplier`, `storage_location`, `status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'SUP-2026-000001', 'Engine Oil 15W-40', 'Oil', 'Liters', 119.00, 30.00, 50.00, 350.00, 'XYZ Auto Parts', 'Warehouse A - Shelf 1', 'IN_STOCK', 'Heavy duty diesel engine oil', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:33:57'),
(2, 'SUP-2026-000002', 'Engine Oil 10W-30', 'Oil', 'Liters', 45.00, 30.00, 50.00, 380.00, 'XYZ Auto Parts', 'Warehouse A - Shelf 1', 'IN_STOCK', 'For light duty engines', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(3, 'SUP-2026-000003', 'Oil Filter (Heavy Duty)', 'Filters', 'Pcs', 25.00, 10.00, 15.00, 850.00, 'ABC Motors', 'Warehouse A - Shelf 2', 'IN_STOCK', 'Genuine Isuzu filter', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(4, 'SUP-2026-000004', 'Oil Filter (Light Duty)', 'Filters', 'Pcs', 8.00, 10.00, 15.00, 400.00, 'ABC Motors', 'Warehouse A - Shelf 2', 'LOW_STOCK', 'For light vehicles', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(5, 'SUP-2026-000005', 'Air Filter', 'Filters', 'Pcs', 15.00, 5.00, 10.00, 650.00, 'XYZ Auto Parts', 'Warehouse A - Shelf 3', 'IN_STOCK', 'Standard air filter', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(6, 'SUP-2026-000006', 'Fuel Filter', 'Filters', 'Pcs', 12.00, 5.00, 10.00, 750.00, 'XYZ Auto Parts', 'Warehouse A - Shelf 3', 'IN_STOCK', 'Diesel fuel filter', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(7, 'SUP-2026-000007', 'Grease (Multi-Purpose)', 'Lubricants', 'Kg', 35.00, 10.00, 20.00, 250.00, 'HeavyParts Inc.', 'Warehouse B - Shelf 1', 'IN_STOCK', 'General purpose grease', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(8, 'SUP-2026-000008', 'Brake Fluid DOT4', 'Fluids', 'Liters', 8.00, 10.00, 15.00, 750.00, 'BrakePro Inc.', 'Warehouse B - Shelf 2', 'LOW_STOCK', 'Hydraulic brake fluid', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(9, 'SUP-2026-000009', 'Coolant (Long-Life)', 'Fluids', 'Liters', 0.00, 20.00, 30.00, 420.00, 'CoolTech Supply', 'Warehouse B - Shelf 2', 'OUT_OF_STOCK', 'For radiator system', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(10, 'SUP-2026-000010', 'Transmission Fluid ATF', 'Fluids', 'Liters', 22.00, 10.00, 15.00, 850.00, 'TransParts Co.', 'Warehouse B - Shelf 3', 'IN_STOCK', 'Automatic transmission fluid', '2026-09-17 16:32:45', 'ADMIN', '2026-09-17 16:32:45'),
(11, 'SUP-2026-000011', 'BASAHAN', 'Equipment', 'Pcs', 101.00, 5.00, 5.00, 20.00, 'n/a', 'Warehouse A', 'IN_STOCK', 'test', '2026-09-17 16:37:20', 'admin', '2026-09-17 16:37:44');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_supply_transactions`
--

CREATE TABLE `tbl_supply_transactions` (
  `transaction_id` int(11) NOT NULL,
  `transaction_code` varchar(50) NOT NULL,
  `transaction_type` enum('STOCK_IN','STOCK_OUT','RETURN','ADJUSTMENT','DAMAGED','DISPOSAL') NOT NULL,
  `transaction_date` date NOT NULL,
  `supply_id` int(11) NOT NULL,
  `supply_code` varchar(50) DEFAULT NULL,
  `supply_name` varchar(200) DEFAULT NULL,
  `quantity` decimal(15,2) DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `unit_cost` decimal(15,2) DEFAULT 0.00,
  `total_cost` decimal(15,2) DEFAULT 0.00,
  `previous_stock` decimal(15,2) DEFAULT 0.00,
  `new_stock` decimal(15,2) DEFAULT 0.00,
  `reference_number` varchar(100) DEFAULT NULL,
  `reference_module` varchar(100) DEFAULT NULL,
  `supplier_vendor` varchar(200) DEFAULT NULL,
  `issued_to` varchar(100) DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `purpose` varchar(200) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_supply_transactions`
--

INSERT INTO `tbl_supply_transactions` (`transaction_id`, `transaction_code`, `transaction_type`, `transaction_date`, `supply_id`, `supply_code`, `supply_name`, `quantity`, `unit`, `unit_cost`, `total_cost`, `previous_stock`, `new_stock`, `reference_number`, `reference_module`, `supplier_vendor`, `issued_to`, `truck_id`, `truck_plate`, `purpose`, `remarks`, `created_at`, `created_by`) VALUES
(1, 'STX-2026-000001', 'STOCK_IN', '2026-08-01', 1, 'SUP-2026-000001', 'Engine Oil 15W-40', 150.00, 'Liters', 350.00, 52500.00, 0.00, 150.00, 'INV-2026-0812', 'Purchase', 'XYZ Auto Parts', NULL, NULL, NULL, 'Initial stock purchase', '', '2026-09-17 16:32:45', 'ADMIN'),
(2, 'STX-2026-000002', 'STOCK_OUT', '2026-08-15', 1, 'SUP-2026-000001', 'Engine Oil 15W-40', 20.00, 'Liters', 350.00, 7000.00, 150.00, 130.00, 'MNT-2026-000001', 'Maintenance', '', 'Pedro Santos', 6, 'ABC-1234', 'PMS — oil change', 'Used on ABC-1234', '2026-09-17 16:32:45', 'ADMIN'),
(3, 'STX-2026-000003', 'STOCK_OUT', '2026-08-20', 1, 'SUP-2026-000001', 'Engine Oil 15W-40', 15.00, 'Liters', 350.00, 5250.00, 130.00, 115.00, 'MNT-2026-000002', 'Maintenance', '', 'Mark Tan', 7, 'XYZ-5678', 'PMS — oil change', 'Used on XYZ-5678', '2026-09-17 16:32:45', 'ADMIN'),
(4, 'STX-2026-000004', 'RETURN', '2026-08-25', 1, 'SUP-2026-000001', 'Engine Oil 15W-40', 5.00, 'Liters', 350.00, 1750.00, 115.00, 120.00, 'RTN-2026-0001', 'Return', '', 'Pedro Santos', NULL, NULL, 'Unused returned', '', '2026-09-17 16:32:45', 'ADMIN'),
(5, 'STX-2026-000005', 'STOCK_IN', '2026-08-02', 2, 'SUP-2026-000002', 'Engine Oil 10W-30', 80.00, 'Liters', 380.00, 30400.00, 0.00, 80.00, 'INV-2026-0813', 'Purchase', 'XYZ Auto Parts', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(6, 'STX-2026-000006', 'STOCK_OUT', '2026-08-18', 2, 'SUP-2026-000002', 'Engine Oil 10W-30', 35.00, 'Liters', 380.00, 13300.00, 80.00, 45.00, 'MNT-2026-000003', 'Maintenance', '', 'Pedro Reyes', 8, 'DEF-9012', 'PMS — oil change', '', '2026-09-17 16:32:45', 'ADMIN'),
(7, 'STX-2026-000007', 'STOCK_IN', '2026-08-05', 3, 'SUP-2026-000003', 'Oil Filter (Heavy Duty)', 30.00, 'Pcs', 850.00, 25500.00, 0.00, 30.00, 'INV-2026-0821', 'Purchase', 'ABC Motors', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(8, 'STX-2026-000008', 'STOCK_OUT', '2026-08-15', 3, 'SUP-2026-000003', 'Oil Filter (Heavy Duty)', 3.00, 'Pcs', 850.00, 2550.00, 30.00, 27.00, 'MNT-2026-000001', 'Maintenance', '', 'Pedro Santos', 6, 'ABC-1234', 'PMS — filter replacement', '', '2026-09-17 16:32:45', 'ADMIN'),
(9, 'STX-2026-000009', 'STOCK_OUT', '2026-08-20', 3, 'SUP-2026-000003', 'Oil Filter (Heavy Duty)', 2.00, 'Pcs', 850.00, 1700.00, 27.00, 25.00, 'MNT-2026-000002', 'Maintenance', '', 'Mark Tan', 7, 'XYZ-5678', 'PMS — filter replacement', '', '2026-09-17 16:32:45', 'ADMIN'),
(10, 'STX-2026-000010', 'STOCK_IN', '2026-08-06', 4, 'SUP-2026-000004', 'Oil Filter (Light Duty)', 15.00, 'Pcs', 400.00, 6000.00, 0.00, 15.00, 'INV-2026-0822', 'Purchase', 'ABC Motors', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(11, 'STX-2026-000011', 'STOCK_OUT', '2026-08-22', 4, 'SUP-2026-000004', 'Oil Filter (Light Duty)', 5.00, 'Pcs', 400.00, 2000.00, 15.00, 10.00, 'MNT-2026-000004', 'Maintenance', '', 'Diego Silang', 10, 'JKL-7890', 'Routine filter swap', '', '2026-09-17 16:32:45', 'ADMIN'),
(12, 'STX-2026-000012', 'DAMAGED', '2026-09-01', 4, 'SUP-2026-000004', 'Oil Filter (Light Duty)', 2.00, 'Pcs', 400.00, 800.00, 10.00, 8.00, 'DMG-2026-0001', 'Damage', '', NULL, NULL, NULL, 'Water damaged in storage', '', '2026-09-17 16:32:45', 'ADMIN'),
(13, 'STX-2026-000013', 'STOCK_IN', '2026-08-08', 5, 'SUP-2026-000005', 'Air Filter', 20.00, 'Pcs', 650.00, 13000.00, 0.00, 20.00, 'INV-2026-0825', 'Purchase', 'XYZ Auto Parts', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(14, 'STX-2026-000014', 'STOCK_OUT', '2026-08-24', 5, 'SUP-2026-000005', 'Air Filter', 5.00, 'Pcs', 650.00, 3250.00, 20.00, 15.00, 'MNT-2026-000005', 'Maintenance', '', 'Antonio Luna', 9, 'GHI-3456', 'Air filter replacement', '', '2026-09-17 16:32:45', 'ADMIN'),
(15, 'STX-2026-000015', 'STOCK_IN', '2026-08-10', 6, 'SUP-2026-000006', 'Fuel Filter', 15.00, 'Pcs', 750.00, 11250.00, 0.00, 15.00, 'INV-2026-0828', 'Purchase', 'XYZ Auto Parts', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(16, 'STX-2026-000016', 'STOCK_OUT', '2026-08-25', 6, 'SUP-2026-000006', 'Fuel Filter', 3.00, 'Pcs', 750.00, 2250.00, 15.00, 12.00, 'MNT-2026-000006', 'Maintenance', '', 'Pedro Reyes', 8, 'DEF-9012', 'Fuel filter replacement', '', '2026-09-17 16:32:45', 'ADMIN'),
(17, 'STX-2026-000017', 'STOCK_IN', '2026-08-12', 7, 'SUP-2026-000007', 'Grease (Multi-Purpose)', 40.00, 'Kg', 250.00, 10000.00, 0.00, 40.00, 'INV-2026-0831', 'Purchase', 'HeavyParts Inc.', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(18, 'STX-2026-000018', 'STOCK_OUT', '2026-08-26', 7, 'SUP-2026-000007', 'Grease (Multi-Purpose)', 5.00, 'Kg', 250.00, 1250.00, 40.00, 35.00, 'MNT-2026-000007', 'Maintenance', '', 'Ramon Cruz', 5, 'CMPL-TASDA', 'Chassis greasing', '', '2026-09-17 16:32:45', 'ADMIN'),
(19, 'STX-2026-000019', 'STOCK_IN', '2026-08-14', 8, 'SUP-2026-000008', 'Brake Fluid DOT4', 20.00, 'Liters', 750.00, 15000.00, 0.00, 20.00, 'INV-2026-0835', 'Purchase', 'BrakePro Inc.', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(20, 'STX-2026-000020', 'STOCK_OUT', '2026-08-28', 8, 'SUP-2026-000008', 'Brake Fluid DOT4', 12.00, 'Liters', 750.00, 9000.00, 20.00, 8.00, 'MNT-2026-000008', 'Maintenance', '', 'Mark Tan', 7, 'XYZ-5678', 'Brake system service', '', '2026-09-17 16:32:45', 'ADMIN'),
(21, 'STX-2026-000021', 'STOCK_IN', '2026-08-03', 9, 'SUP-2026-000009', 'Coolant (Long-Life)', 30.00, 'Liters', 420.00, 12600.00, 0.00, 30.00, 'INV-2026-0815', 'Purchase', 'CoolTech Supply', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(22, 'STX-2026-000022', 'STOCK_OUT', '2026-08-16', 9, 'SUP-2026-000009', 'Coolant (Long-Life)', 15.00, 'Liters', 420.00, 6300.00, 30.00, 15.00, 'MNT-2026-000009', 'Maintenance', '', 'Maria Santos', 11, 'MNO-2345', 'Radiator service', '', '2026-09-17 16:32:45', 'ADMIN'),
(23, 'STX-2026-000023', 'STOCK_OUT', '2026-08-30', 9, 'SUP-2026-000009', 'Coolant (Long-Life)', 15.00, 'Liters', 420.00, 6300.00, 15.00, 0.00, 'MNT-2026-000010', 'Maintenance', '', 'Maria Santos', 11, 'MNO-2345', 'Coolant flush', '', '2026-09-17 16:32:45', 'ADMIN'),
(24, 'STX-2026-000024', 'STOCK_IN', '2026-08-04', 10, 'SUP-2026-000010', 'Transmission Fluid ATF', 30.00, 'Liters', 850.00, 25500.00, 0.00, 30.00, 'INV-2026-0818', 'Purchase', 'TransParts Co.', NULL, NULL, NULL, 'Initial stock', '', '2026-09-17 16:32:45', 'ADMIN'),
(25, 'STX-2026-000025', 'STOCK_OUT', '2026-08-19', 10, 'SUP-2026-000010', 'Transmission Fluid ATF', 8.00, 'Liters', 850.00, 6800.00, 30.00, 22.00, 'MNT-2026-000011', 'Maintenance', '', 'Juan Dela Cruz', 6, 'ABC-1234', 'Transmission fluid change', '', '2026-09-17 16:32:45', 'ADMIN'),
(26, 'STX-2026-000026', 'ADJUSTMENT', '2026-09-01', 1, 'SUP-2026-000001', 'Engine Oil 15W-40', 120.00, 'Liters', 350.00, 42000.00, 120.00, 120.00, 'ADJ-2026-0001', 'Adjustment', '', NULL, NULL, NULL, 'Physical count reconciliation', 'Counted on Sep 1', '2026-09-17 16:32:45', 'ADMIN'),
(27, 'STX-2026-000027', 'DISPOSAL', '2026-09-02', 4, 'SUP-2026-000004', 'Oil Filter (Light Duty)', 1.00, 'Pcs', 400.00, 400.00, 8.00, 7.00, 'DSP-2026-0001', 'Disposal', '', NULL, NULL, NULL, 'Expired filter disposal', 'Beyond shelf life', '2026-09-17 16:32:45', 'ADMIN'),
(28, 'STX-2026-000028', 'STOCK_OUT', '2026-09-03', 5, 'SUP-2026-000005', 'Air Filter', 3.00, 'Pcs', 650.00, 1950.00, 15.00, 12.00, 'MNT-2026-000012', 'Maintenance', '', 'Antonio Luna', 9, 'GHI-3456', 'Air filter replacement', '', '2026-09-17 16:32:45', 'ADMIN'),
(29, 'STX-2026-000029', 'STOCK_IN', '2026-09-04', 7, 'SUP-2026-000007', 'Grease (Multi-Purpose)', 15.00, 'Kg', 250.00, 3750.00, 35.00, 50.00, 'INV-2026-0901', 'Purchase', 'HeavyParts Inc.', NULL, NULL, NULL, 'Restock low inventory', '', '2026-09-17 16:32:45', 'ADMIN'),
(30, 'STX-2026-000030', 'STOCK_OUT', '2026-09-05', 1, 'SUP-2026-000001', 'Engine Oil 15W-40', 25.00, 'Liters', 350.00, 8750.00, 120.00, 95.00, 'MNT-2026-000013', 'Maintenance', '', 'Pedro Santos', 14, 'VWX-3456', 'PMS — oil change', '', '2026-09-17 16:32:45', 'ADMIN'),
(31, 'STX-2026-000031', 'STOCK_OUT', '2026-09-06', 6, 'SUP-2026-000006', 'Fuel Filter', 2.00, 'Pcs', 750.00, 1500.00, 12.00, 10.00, 'MNT-2026-000014', 'Maintenance', '', 'Ramon Cruz', 14, 'VWX-3456', 'Fuel filter replacement', '', '2026-09-17 16:32:45', 'ADMIN'),
(32, 'STX-2026-000032', 'STOCK_IN', '2026-09-07', 9, 'SUP-2026-000009', 'Coolant (Long-Life)', 25.00, 'Liters', 420.00, 10500.00, 0.00, 25.00, 'INV-2026-0903', 'Purchase', 'CoolTech Supply', NULL, NULL, NULL, 'Restock after stockout', '', '2026-09-17 16:32:45', 'ADMIN'),
(33, 'STX-2026-000033', 'STOCK_OUT', '2026-09-17', 1, 'SUP-2026-000001', 'Engine Oil 15W-40', 1.00, 'Liters', 350.00, 350.00, 120.00, 119.00, 'MNT12031-23', NULL, '', 'TRUCK-120312', 1, 'ABC-12345', 'MAINTENANCE', '', '2026-09-17 16:33:57', 'admin'),
(34, 'STX-2026-000034', 'STOCK_IN', '2026-09-17', 11, 'SUP-2026-000011', 'BASAHAN', 1.00, 'Pcs', 20.00, 20.00, 100.00, 101.00, 'PO-1231', NULL, 'TEST', '', NULL, '', 'TEST', 'TEST', '2026-09-17 16:37:44', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tires`
--

CREATE TABLE `tbl_tires` (
  `tire_id` int(11) NOT NULL,
  `tire_code` varchar(50) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `life_expectancy_km` decimal(15,2) DEFAULT 0.00,
  `tread_depth_mm` decimal(5,2) DEFAULT 0.00,
  `price` decimal(15,2) DEFAULT 0.00,
  `date_of_manufacture` date DEFAULT NULL,
  `supplier` varchar(200) DEFAULT NULL,
  `tire_status` enum('IN_STOCK','INSTALLED','REMOVED','FOR_DISPOSAL','DISPOSED') NOT NULL DEFAULT 'IN_STOCK',
  `current_odometer` decimal(15,2) DEFAULT 0.00,
  `total_distance_km` decimal(15,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tires`
--

INSERT INTO `tbl_tires` (`tire_id`, `tire_code`, `serial_number`, `model`, `brand`, `size`, `life_expectancy_km`, `tread_depth_mm`, `price`, `date_of_manufacture`, `supplier`, `tire_status`, `current_odometer`, `total_distance_km`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'TIR-2026-000001', 'SN-2026-ABC-0001', 'HDR2', 'Bridgestone', '11R22.5', 80000.00, 18.50, 12500.00, '2025-06-15', 'XYZ Auto Parts', 'INSTALLED', 82500.00, 12500.00, 'Mounted on ABC-1234', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16'),
(2, 'TIR-2026-000002', 'SN-2026-ABC-0002', 'HDR2', 'Bridgestone', '11R22.5', 80000.00, 18.20, 12500.00, '2025-06-15', 'XYZ Auto Parts', 'INSTALLED', 82500.00, 11800.00, 'Mounted on ABC-1234', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16'),
(3, 'TIR-2026-000003', 'SN-2026-XYZ-0003', 'R150', 'Bridgestone', '11R22.5', 75000.00, 17.80, 11800.00, '2025-07-01', 'XYZ Auto Parts', 'INSTALLED', 65200.00, 9800.00, 'Mounted on XYZ-5678', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16'),
(4, 'TIR-2026-000004', 'SN-2026-XYZ-0004', 'R150', 'Bridgestone', '11R22.5', 75000.00, 18.00, 11800.00, '2025-07-01', 'XYZ Auto Parts', 'INSTALLED', 65200.00, 10200.00, 'Mounted on XYZ-5678', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16'),
(5, 'TIR-2026-000005', 'SN-2026-DEF-0005', 'M840', 'Michelin', '295/80R22.5', 90000.00, 20.10, 18500.00, '2025-08-10', 'ABC Motors', 'INSTALLED', 95200.00, 14500.00, 'Mounted on DEF-9012', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16'),
(6, 'TIR-2026-000006', 'SN-2026-DEF-0006', 'M840', 'Michelin', '295/80R22.5', 90000.00, 19.80, 18500.00, '2025-08-10', 'ABC Motors', 'INSTALLED', 95200.00, 14200.00, 'Mounted on DEF-9012', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16'),
(7, 'TIR-2026-000007', 'SN-2026-GHI-0007', 'G580', 'Goodyear', '11R22.5', 70000.00, 16.50, 13200.00, '2025-09-05', 'HeavyParts Inc.', 'DISPOSED', 62500.00, 22800.00, 'Worn out, awaiting disposal', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 16:07:50'),
(8, 'TIR-2026-000008', 'SN-2026-GHI-0008', 'G580', 'Goodyear', '11R22.5', 70000.00, 17.20, 13200.00, '2025-09-05', 'HeavyParts Inc.', 'INSTALLED', 124500.00, 0.00, 'Spare tire in warehouse', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 16:07:14'),
(9, 'TIR-2026-000009', 'SN-2026-JKL-0009', 'R268', 'Continental', '11R22.5', 85000.00, 19.50, 15800.00, '2025-05-20', 'ABC Motors', 'DISPOSED', 135400.00, 45200.00, 'Beyond repair', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16'),
(10, 'TIR-2026-000010', 'SN-2026-JKL-0010', 'R268', 'Continental', '11R22.5', 85000.00, 18.90, 15800.00, '2025-05-20', 'ABC Motors', 'IN_STOCK', 0.00, 0.00, 'Reserved for JKL-7890', '2026-09-17 15:59:16', 'ADMIN', '2026-09-17 15:59:16');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tire_disposals`
--

CREATE TABLE `tbl_tire_disposals` (
  `disposal_id` int(11) NOT NULL,
  `disposal_code` varchar(50) NOT NULL,
  `tire_id` int(11) NOT NULL,
  `tire_code` varchar(50) DEFAULT NULL,
  `disposal_date` date NOT NULL,
  `disposal_reason` varchar(200) DEFAULT NULL,
  `final_mileage` decimal(15,2) DEFAULT 0.00,
  `disposal_details` text DEFAULT NULL,
  `final_status` enum('DISPOSED','SOLD_SCRAP','RETURNED_VENDOR','WRITTEN_OFF') DEFAULT 'DISPOSED',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tire_disposals`
--

INSERT INTO `tbl_tire_disposals` (`disposal_id`, `disposal_code`, `tire_id`, `tire_code`, `disposal_date`, `disposal_reason`, `final_mileage`, `disposal_details`, `final_status`, `remarks`, `created_at`, `created_by`) VALUES
(1, 'TDS-2026-000001', 7, 'TIR-2026-000007', '2026-09-17', 'Explode', 22800.00, 'test', 'DISPOSED', 'test', '2026-09-17 16:07:50', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tire_installations`
--

CREATE TABLE `tbl_tire_installations` (
  `installation_id` int(11) NOT NULL,
  `installation_code` varchar(50) NOT NULL,
  `tire_id` int(11) NOT NULL,
  `tire_code` varchar(50) DEFAULT NULL,
  `truck_id` int(11) NOT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `installation_date` date NOT NULL,
  `installation_odometer` decimal(15,2) DEFAULT 0.00,
  `installed_by` varchar(100) DEFAULT NULL,
  `installation_remarks` text DEFAULT NULL,
  `status` enum('ACTIVE','REMOVED') NOT NULL DEFAULT 'ACTIVE',
  `removed_date` date DEFAULT NULL,
  `removal_odometer` decimal(15,2) DEFAULT 0.00,
  `distance_used_km` decimal(15,2) DEFAULT 0.00,
  `reason_for_removal` varchar(200) DEFAULT NULL,
  `tire_condition_on_removal` varchar(100) DEFAULT NULL,
  `removed_by` varchar(100) DEFAULT NULL,
  `removal_remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tire_installations`
--

INSERT INTO `tbl_tire_installations` (`installation_id`, `installation_code`, `tire_id`, `tire_code`, `truck_id`, `truck_plate`, `position`, `installation_date`, `installation_odometer`, `installed_by`, `installation_remarks`, `status`, `removed_date`, `removal_odometer`, `distance_used_km`, `reason_for_removal`, `tire_condition_on_removal`, `removed_by`, `removal_remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'TIN-2026-000001', 1, 'TIR-2026-000001', 6, 'ABC-1234', 'Front Left', '2026-07-20', 70000.00, 'Pedro Santos', 'New install', 'ACTIVE', NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(2, 'TIN-2026-000002', 2, 'TIR-2026-000002', 6, 'ABC-1234', 'Front Right', '2026-07-20', 70000.00, 'Pedro Santos', 'New install', 'ACTIVE', NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(3, 'TIN-2026-000003', 3, 'TIR-2026-000003', 7, 'XYZ-5678', 'Rear Left', '2026-08-15', 55000.00, 'Pedro Reyes', 'New install', 'ACTIVE', NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(4, 'TIN-2026-000004', 4, 'TIR-2026-000004', 7, 'XYZ-5678', 'Rear Right', '2026-08-15', 55000.00, 'Pedro Reyes', 'New install', 'ACTIVE', NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(5, 'TIN-2026-000005', 5, 'TIR-2026-000005', 8, 'DEF-9012', 'Front Left', '2026-08-20', 80000.00, 'Mark Tan', 'New install', 'ACTIVE', NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(6, 'TIN-2026-000006', 6, 'TIR-2026-000006', 8, 'DEF-9012', 'Front Right', '2026-08-20', 80000.00, 'Mark Tan', 'New install', 'ACTIVE', NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(7, 'TIN-2026-000007', 7, 'TIR-2026-000007', 9, 'GHI-3456', 'Rear Left', '2026-06-15', 39700.00, 'Antonio Luna', 'Old tire', 'REMOVED', '2026-09-15', 62500.00, 22800.00, 'Worn out', 'Poor', 'Antonio Luna', 'Thread depth below 2mm', '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(8, 'TIN-2026-000008', 9, 'TIR-2026-000009', 10, 'JKL-7890', 'Rear Right', '2026-06-10', 90200.00, 'Diego Silang', 'New install', 'REMOVED', '2026-08-15', 135400.00, 45200.00, 'Punctured, beyond repair', 'Damaged', 'Diego Silang', 'Sidewall damaged', '2026-09-17 15:59:41', 'ADMIN', '2026-09-17 15:59:41'),
(9, 'TIN-2026-000009', 8, 'TIR-2026-000008', 9, 'GHI-3456', 'FRONT LEFT', '2026-09-17', 124500.00, 'KYLE', '', 'ACTIVE', NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-09-17 16:07:14', 'admin', '2026-09-17 16:07:14');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tire_transactions`
--

CREATE TABLE `tbl_tire_transactions` (
  `transaction_id` int(11) NOT NULL,
  `transaction_code` varchar(50) NOT NULL,
  `transaction_type` enum('IN','OUT') NOT NULL,
  `transaction_date` date NOT NULL,
  `tire_id` int(11) NOT NULL,
  `tire_code` varchar(50) DEFAULT NULL,
  `quantity` decimal(15,2) DEFAULT 1.00,
  `price` decimal(15,2) DEFAULT 0.00,
  `purpose` varchar(200) DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `supplier_vendor` varchar(200) DEFAULT NULL,
  `released_by` varchar(100) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tire_transactions`
--

INSERT INTO `tbl_tire_transactions` (`transaction_id`, `transaction_code`, `transaction_type`, `transaction_date`, `tire_id`, `tire_code`, `quantity`, `price`, `purpose`, `truck_id`, `truck_plate`, `supplier_vendor`, `released_by`, `reference_number`, `remarks`, `created_at`, `created_by`) VALUES
(1, 'TTX-2026-000001', 'IN', '2026-06-15', 1, 'TIR-2026-000001', 1.00, 12500.00, 'Purchase', NULL, NULL, 'XYZ Auto Parts', NULL, 'INV-2026-0451', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(2, 'TTX-2026-000002', 'IN', '2026-06-15', 2, 'TIR-2026-000002', 1.00, 12500.00, 'Purchase', NULL, NULL, 'XYZ Auto Parts', NULL, 'INV-2026-0451', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(3, 'TTX-2026-000003', 'IN', '2026-07-01', 3, 'TIR-2026-000003', 1.00, 11800.00, 'Purchase', NULL, NULL, 'XYZ Auto Parts', NULL, 'INV-2026-0473', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(4, 'TTX-2026-000004', 'IN', '2026-07-01', 4, 'TIR-2026-000004', 1.00, 11800.00, 'Purchase', NULL, NULL, 'XYZ Auto Parts', NULL, 'INV-2026-0473', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(5, 'TTX-2026-000005', 'IN', '2026-08-10', 5, 'TIR-2026-000005', 1.00, 18500.00, 'Purchase', NULL, NULL, 'ABC Motors', NULL, 'INV-2026-0512', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(6, 'TTX-2026-000006', 'IN', '2026-08-10', 6, 'TIR-2026-000006', 1.00, 18500.00, 'Purchase', NULL, NULL, 'ABC Motors', NULL, 'INV-2026-0512', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(7, 'TTX-2026-000007', 'IN', '2026-09-05', 7, 'TIR-2026-000007', 1.00, 13200.00, 'Purchase', NULL, NULL, 'HeavyParts Inc.', NULL, 'INV-2026-0589', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(8, 'TTX-2026-000008', 'IN', '2026-09-05', 8, 'TIR-2026-000008', 1.00, 13200.00, 'Purchase', NULL, NULL, 'HeavyParts Inc.', NULL, 'INV-2026-0589', 'Spare tire', '2026-09-17 15:59:24', 'ADMIN'),
(9, 'TTX-2026-000009', 'IN', '2026-05-20', 9, 'TIR-2026-000009', 1.00, 15800.00, 'Purchase', NULL, NULL, 'ABC Motors', NULL, 'INV-2026-0398', 'New stock', '2026-09-17 15:59:24', 'ADMIN'),
(10, 'TTX-2026-000010', 'IN', '2026-05-20', 10, 'TIR-2026-000010', 1.00, 15800.00, 'Purchase', NULL, NULL, 'ABC Motors', NULL, 'INV-2026-0398', 'Reserved', '2026-09-17 15:59:24', 'ADMIN'),
(11, 'TTX-2026-000011', 'OUT', '2026-07-20', 1, 'TIR-2026-000001', 1.00, 0.00, 'Installation', 6, 'ABC-1234', NULL, 'Pedro Santos', 'WO-2026-001', 'Installed on ABC-1234', '2026-09-17 15:59:33', 'ADMIN'),
(12, 'TTX-2026-000012', 'OUT', '2026-07-20', 2, 'TIR-2026-000002', 1.00, 0.00, 'Installation', 6, 'ABC-1234', NULL, 'Pedro Santos', 'WO-2026-001', 'Installed on ABC-1234', '2026-09-17 15:59:33', 'ADMIN'),
(13, 'TTX-2026-000013', 'OUT', '2026-08-15', 3, 'TIR-2026-000003', 1.00, 0.00, 'Installation', 7, 'XYZ-5678', NULL, 'Pedro Reyes', 'WO-2026-014', 'Installed on XYZ-5678', '2026-09-17 15:59:33', 'ADMIN'),
(14, 'TTX-2026-000014', 'OUT', '2026-08-15', 4, 'TIR-2026-000004', 1.00, 0.00, 'Installation', 7, 'XYZ-5678', NULL, 'Pedro Reyes', 'WO-2026-014', 'Installed on XYZ-5678', '2026-09-17 15:59:33', 'ADMIN'),
(15, 'TTX-2026-000015', 'OUT', '2026-08-20', 5, 'TIR-2026-000005', 1.00, 0.00, 'Installation', 8, 'DEF-9012', NULL, 'Mark Tan', 'WO-2026-021', 'Installed on DEF-9012', '2026-09-17 15:59:33', 'ADMIN'),
(16, 'TTX-2026-000016', 'OUT', '2026-08-20', 6, 'TIR-2026-000006', 1.00, 0.00, 'Installation', 8, 'DEF-9012', NULL, 'Mark Tan', 'WO-2026-021', 'Installed on DEF-9012', '2026-09-17 15:59:33', 'ADMIN'),
(17, 'TTX-2026-000017', 'OUT', '2026-09-15', 7, 'TIR-2026-000007', 1.00, 0.00, 'Installation', 9, 'GHI-3456', NULL, 'Antonio Luna', 'WO-2026-032', 'Installed on GHI-3456', '2026-09-17 15:59:33', 'ADMIN'),
(18, 'TTX-2026-000018', 'OUT', '2026-06-10', 9, 'TIR-2026-000009', 1.00, 0.00, 'Installation', 10, 'JKL-7890', NULL, 'Diego Silang', 'WO-2026-008', 'Installed on JKL-7890', '2026-09-17 15:59:33', 'ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tools`
--

CREATE TABLE `tbl_tools` (
  `tool_id` int(11) NOT NULL,
  `tool_code` varchar(50) NOT NULL,
  `tool_name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(15,2) DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `quantity_on_hand` int(11) NOT NULL DEFAULT 1,
  `current_location` varchar(200) DEFAULT NULL,
  `tool_condition` enum('NEW','GOOD','FAIR','POOR','DAMAGED') NOT NULL DEFAULT 'GOOD',
  `availability` enum('AVAILABLE','ASSIGNED','UNDER_REPAIR','DAMAGED','LOST','RETIRED') NOT NULL DEFAULT 'AVAILABLE',
  `assigned_to` varchar(100) DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tools`
--

INSERT INTO `tbl_tools` (`tool_id`, `tool_code`, `tool_name`, `category`, `brand`, `model`, `serial_number`, `purchase_date`, `purchase_cost`, `quantity`, `quantity_on_hand`, `current_location`, `tool_condition`, `availability`, `assigned_to`, `truck_id`, `truck_plate`, `image_path`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'TL-2026-000001', 'Hydraulic Jack 10-Ton', 'Jacks', 'Enerpac', 'P392', 'EJ-2024-001', '2024-01-15', 18500.00, 1, 1, 'Warehouse A - Bay 1', 'GOOD', 'AVAILABLE', NULL, NULL, NULL, NULL, 'Heavy-duty hydraulic jack', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(2, 'TL-2026-000002', 'Hydraulic Jack 5-Ton', 'Jacks', 'Enerpac', 'P391', 'EJ-2024-002', '2024-01-15', 12500.00, 1, 1, 'Warehouse A - Bay 1', 'GOOD', 'AVAILABLE', NULL, NULL, NULL, NULL, 'Medium-duty hydraulic jack', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(3, 'TL-2026-000003', 'Impact Wrench 1-inch', 'Power Tools', 'Ingersoll Rand', 'IR-285B', 'IR-2023-045', '2023-06-20', 22000.00, 1, 1, 'Warehouse A - Shelf 5', 'GOOD', 'AVAILABLE', NULL, NULL, NULL, NULL, 'Pneumatic impact wrench', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(4, 'TL-2026-000004', 'Torque Wrench 600mm', 'Hand Tools', 'Stanley', 'ST-600', 'SW-2023-078', '2023-06-20', 4500.00, 1, 1, 'Warehouse A - Shelf 5', 'GOOD', 'ASSIGNED', 'Pedro Santos', NULL, NULL, NULL, 'For wheel nuts torquing', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(5, 'TL-2026-000005', 'Tool Box (Complete Set)', 'Tool Sets', 'Snap-on', 'SNAP-PRO-24', 'SN-2024-012', '2024-03-10', 45000.00, 1, 1, 'Warehouse A - Cabinet 1', 'GOOD', 'AVAILABLE', NULL, NULL, NULL, NULL, 'Complete mechanics tool set', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(6, 'TL-2026-000006', 'Digital Tire Pressure Gauge', 'Measuring', 'Kwik-Way', 'KW-DIG-200', 'KW-2024-021', '2024-02-05', 2800.00, 1, 1, 'Warehouse A - Shelf 3', 'GOOD', 'ASSIGNED', 'Mark Tan', NULL, NULL, NULL, 'Calibrated monthly', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(7, 'TL-2026-000007', 'Wheel Alignment Gauge', 'Measuring', 'Kwik-Way', 'KW-ALIGN-500', 'KA-2022-008', '2022-09-15', 65000.00, 1, 1, 'Warehouse A - Bay 3', 'FAIR', 'AVAILABLE', NULL, NULL, NULL, NULL, 'Needs recalibration', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(8, 'TL-2026-000008', 'Portable Air Compressor', 'Power Tools', 'Ingersoll Rand', 'IR-P185', 'IA-2023-055', '2023-11-01', 85000.00, 1, 1, 'Warehouse B - Bay 2', 'GOOD', 'UNDER_REPAIR', NULL, NULL, NULL, NULL, 'Motor needs servicing', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(9, 'TL-2026-000009', 'Grease Gun (Pneumatic)', 'Hand Tools', 'Lincoln', 'LIN-PG-300', 'LN-2024-033', '2024-04-12', 5200.00, 1, 1, 'Warehouse A - Shelf 4', 'GOOD', 'ASSIGNED', 'Antonio Luna', NULL, NULL, NULL, 'Currently issued to mechanic', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(10, 'TL-2026-000010', 'Engine Crane 2-Ton', 'Lifting Equipment', 'OTC', 'OTC-2T-4500', 'OT-2021-010', '2021-05-20', 42000.00, 1, 1, 'Warehouse B - Bay 1', 'DAMAGED', 'DAMAGED', NULL, NULL, NULL, NULL, 'Hydraulic seal leaking', '2026-09-17 16:48:13', 'ADMIN', '2026-09-17 16:48:13'),
(12, 'TL-2026-000011', 'SCREW DRIVER', 'Power tool', 'INGKO', 'N/A', '012321', '2026-09-17', 3000.00, 3, 1, 'Warehouse 1', 'GOOD', 'ASSIGNED', 'JAMIE', NULL, '', NULL, '', '2026-09-17 17:09:11', 'admin', '2026-09-17 17:09:48');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tool_issuances`
--

CREATE TABLE `tbl_tool_issuances` (
  `issuance_id` int(11) NOT NULL,
  `issuance_code` varchar(50) NOT NULL,
  `tool_id` int(11) NOT NULL,
  `tool_code` varchar(50) DEFAULT NULL,
  `tool_name` varchar(200) DEFAULT NULL,
  `quantity_issued` int(11) NOT NULL DEFAULT 1,
  `quantity_returned` int(11) NOT NULL DEFAULT 0,
  `quantity_pending` int(11) NOT NULL DEFAULT 1,
  `issued_to` varchar(100) NOT NULL,
  `issued_to_type` varchar(50) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `issue_time` time DEFAULT NULL,
  `expected_return` date DEFAULT NULL,
  `actual_return` date DEFAULT NULL,
  `return_time` time DEFAULT NULL,
  `condition_before` enum('NEW','GOOD','FAIR','POOR','DAMAGED') DEFAULT 'GOOD',
  `condition_after` enum('NEW','GOOD','FAIR','POOR','DAMAGED') DEFAULT NULL,
  `purpose` varchar(200) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `issuance_status` enum('ISSUED','RETURNED','OVERDUE','LOST','DAMAGED') NOT NULL DEFAULT 'ISSUED',
  `returned_by` varchar(100) DEFAULT NULL,
  `received_by` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `return_remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tool_issuances`
--

INSERT INTO `tbl_tool_issuances` (`issuance_id`, `issuance_code`, `tool_id`, `tool_code`, `tool_name`, `quantity_issued`, `quantity_returned`, `quantity_pending`, `issued_to`, `issued_to_type`, `issue_date`, `issue_time`, `expected_return`, `actual_return`, `return_time`, `condition_before`, `condition_after`, `purpose`, `reference_number`, `truck_id`, `truck_plate`, `issuance_status`, `returned_by`, `received_by`, `remarks`, `return_remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'TIS-2026-000001', 1, 'TL-2026-000001', 'Hydraulic Jack 10-Ton', 1, 0, 1, 'Pedro Santos', 'STAFF', '2026-07-10', '08:30:00', '2026-07-12', '2026-07-12', '16:45:00', 'GOOD', 'GOOD', 'Tire replacement', 'WO-2026-001', 6, 'ABC-1234', 'RETURNED', 'Pedro Santos', 'Pedro Reyes', 'Returned in good condition', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(2, 'TIS-2026-000002', 1, 'TL-2026-000001', 'Hydraulic Jack 10-Ton', 1, 0, 1, 'Antonio Luna', 'STAFF', '2026-08-15', '09:00:00', '2026-08-17', '2026-08-17', '17:30:00', 'GOOD', 'GOOD', 'Suspension repair', 'WO-2026-014', 9, 'GHI-3456', 'RETURNED', 'Antonio Luna', 'Pedro Santos', 'No issues', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(3, 'TIS-2026-000003', 2, 'TL-2026-000002', 'Hydraulic Jack 5-Ton', 1, 0, 1, 'Diego Silang', 'STAFF', '2026-08-20', '07:45:00', '2026-08-22', '2026-08-22', '14:20:00', 'GOOD', 'GOOD', 'Brake job on JKL-7890', 'WO-2026-018', 10, 'JKL-7890', 'RETURNED', 'Diego Silang', 'Mark Tan', 'Completed on time', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(4, 'TIS-2026-000004', 3, 'TL-2026-000003', 'Impact Wrench 1-inch', 1, 0, 1, 'Juan Dela Cruz', 'MECHANIC', '2026-07-25', '08:00:00', '2026-07-27', '2026-07-27', '16:00:00', 'GOOD', 'GOOD', 'Wheel rotation', 'WO-2026-005', 6, 'ABC-1234', 'RETURNED', 'Juan Dela Cruz', 'Pedro Santos', 'Good condition', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(5, 'TIS-2026-000005', 3, 'TL-2026-000003', 'Impact Wrench 1-inch', 1, 0, 1, 'Pedro Reyes', 'MECHANIC', '2026-08-10', '07:30:00', '2026-08-12', '2026-08-12', '15:30:00', 'GOOD', 'GOOD', 'Tire change', 'WO-2026-010', 7, 'XYZ-5678', 'RETURNED', 'Pedro Reyes', 'Mark Tan', 'Standard service', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(6, 'TIS-2026-000006', 4, 'TL-2026-000004', 'Torque Wrench 600mm', 1, 0, 1, 'Mark Tan', 'MECHANIC', '2026-08-01', '06:00:00', '2026-08-03', '2026-08-03', '17:00:00', 'GOOD', 'GOOD', 'Wheel torquing', 'WO-2026-007', 8, 'DEF-9012', 'RETURNED', 'Mark Tan', 'Pedro Santos', 'Returned properly', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(7, 'TIS-2026-000007', 4, 'TL-2026-000004', 'Torque Wrench 600mm', 1, 0, 1, 'Pedro Santos', 'STAFF', '2026-09-10', '08:00:00', '2026-09-18', NULL, NULL, 'GOOD', NULL, 'Full fleet wheel check', 'WO-2026-020', NULL, NULL, 'ISSUED', NULL, NULL, 'Currently in use', NULL, '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(8, 'TIS-2026-000008', 5, 'TL-2026-000005', 'Tool Box (Complete Set)', 1, 0, 1, 'Pedro Reyes', 'MECHANIC', '2026-07-15', '07:00:00', '2026-07-22', '2026-07-22', '16:30:00', 'GOOD', 'GOOD', 'PMS work on XYZ-5678', 'WO-2026-008', 7, 'XYZ-5678', 'RETURNED', 'Pedro Reyes', 'Antonio Luna', 'All tools complete', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(9, 'TIS-2026-000009', 5, 'TL-2026-000005', 'Tool Box (Complete Set)', 1, 0, 1, 'Antonio Luna', 'MECHANIC', '2026-08-05', '06:30:00', '2026-08-10', '2026-08-10', '17:00:00', 'GOOD', 'GOOD', 'Suspension overhaul', 'WO-2026-011', 9, 'GHI-3456', 'RETURNED', 'Antonio Luna', 'Pedro Santos', 'Complete set returned', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(10, 'TIS-2026-000010', 6, 'TL-2026-000006', 'Digital Tire Pressure Gauge', 1, 0, 1, 'Diego Silang', 'STAFF', '2026-08-12', '08:15:00', '2026-08-14', '2026-08-14', '16:00:00', 'GOOD', 'GOOD', 'Pressure checks', 'WO-2026-013', 10, 'JKL-7890', 'RETURNED', 'Diego Silang', 'Mark Tan', 'Calibrated', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(11, 'TIS-2026-000011', 6, 'TL-2026-000006', 'Digital Tire Pressure Gauge', 1, 0, 1, 'Mark Tan', 'MECHANIC', '2026-09-12', '07:00:00', '2026-09-19', NULL, NULL, 'GOOD', NULL, 'Fleet inspection', 'WO-2026-022', NULL, NULL, 'ISSUED', NULL, NULL, 'Active', NULL, '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(12, 'TIS-2026-000012', 7, 'TL-2026-000007', 'Wheel Alignment Gauge', 1, 0, 1, 'Juan Dela Cruz', 'MECHANIC', '2026-08-18', '06:45:00', '2026-08-19', '2026-08-19', '15:00:00', 'GOOD', 'FAIR', 'Alignment check', 'WO-2026-016', 6, 'ABC-1234', 'RETURNED', 'Juan Dela Cruz', 'Pedro Santos', 'Gauge reading drift', 'Needs recalibration', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(13, 'TIS-2026-000013', 9, 'TL-2026-000009', 'Grease Gun (Pneumatic)', 1, 0, 1, 'Ramon Cruz', 'MECHANIC', '2026-08-22', '07:30:00', '2026-08-24', '2026-08-24', '16:30:00', 'GOOD', 'GOOD', 'Chassis greasing', 'WO-2026-017', 5, 'CMPL-TASDA', 'RETURNED', 'Ramon Cruz', 'Mark Tan', 'All good', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(14, 'TIS-2026-000014', 9, 'TL-2026-000009', 'Grease Gun (Pneumatic)', 1, 0, 1, 'Antonio Luna', 'MECHANIC', '2026-09-08', '08:00:00', '2026-09-15', NULL, NULL, 'GOOD', NULL, 'Grease all fleet trucks', 'WO-2026-019', NULL, NULL, 'ISSUED', NULL, NULL, 'Active issuance', NULL, '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(15, 'TIS-2026-000015', 3, 'TL-2026-000003', 'Impact Wrench 1-inch', 1, 0, 1, 'Pedro Reyes', 'MECHANIC', '2026-08-28', '07:00:00', '2026-09-01', NULL, NULL, 'GOOD', NULL, 'Tire service', 'WO-2026-025', 8, 'DEF-9012', 'ISSUED', NULL, NULL, 'Not yet returned — overdue', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(16, 'TIS-2026-000016', 1, 'TL-2026-000001', 'Hydraulic Jack 10-Ton', 1, 0, 1, 'Diego Silang', 'STAFF', '2026-08-30', '06:00:00', '2026-09-02', NULL, NULL, 'GOOD', NULL, 'Emergency jack on DEF-9012', 'WO-2026-026', 8, 'DEF-9012', 'ISSUED', NULL, NULL, 'Overdue — follow up', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(17, 'TIS-2026-000017', 5, 'TL-2026-000005', 'Tool Box (Complete Set)', 1, 0, 1, 'Pedro Santos', 'STAFF', '2026-09-01', '06:30:00', '2026-09-05', '2026-09-05', '16:45:00', 'GOOD', 'GOOD', 'Full inspection', 'WO-2026-023', 6, 'ABC-1234', 'RETURNED', 'Pedro Santos', 'Antonio Luna', 'Complete', '', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(18, 'TIS-2026-000018', 2, 'TL-2026-000002', 'Hydraulic Jack 5-Ton', 1, 0, 1, 'Mark Tan', 'MECHANIC', '2026-09-02', '07:00:00', '2026-09-06', '2026-09-06', '17:00:00', 'GOOD', 'FAIR', 'Tire job on GHI-3456', 'WO-2026-024', 9, 'GHI-3456', 'RETURNED', 'Mark Tan', 'Pedro Santos', 'Slightly worn', 'Monitor condition', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(19, 'TIS-2026-000019', 7, 'TL-2026-000007', 'Wheel Alignment Gauge', 1, 0, 1, 'Antonio Luna', 'MECHANIC', '2026-09-05', '06:45:00', '2026-09-08', '2026-09-08', '15:30:00', 'FAIR', 'FAIR', 'Alignment check', 'WO-2026-027', 8, 'DEF-9012', 'RETURNED', 'Antonio Luna', 'Pedro Santos', 'Still needs recalibr', 'Sent to calibration', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(20, 'TIS-2026-000020', 3, 'TL-2026-000003', 'Impact Wrench 1-inch', 1, 0, 1, 'Juan Dela Cruz', 'MECHANIC', '2026-09-06', '08:00:00', '2026-09-10', '2026-09-10', '16:00:00', 'GOOD', 'DAMAGED', 'Heavy bolt removal', 'WO-2026-028', 14, 'VWX-3456', 'RETURNED', 'Juan Dela Cruz', 'Pedro Santos', 'Motor damaged', 'Sent for repair', '2026-09-17 16:48:19', 'ADMIN', '2026-09-17 16:48:19'),
(21, 'TIS-2026-000021', 11, 'TL-2026-000011', 'SCREW DRIVER', 1, 0, 1, 'KYLE', 'DRIVER', '2026-09-17', '16:53:00', '2026-09-18', NULL, NULL, 'GOOD', NULL, '', '', NULL, '', 'ISSUED', NULL, NULL, '', NULL, '2026-09-17 16:53:16', 'admin', '2026-09-17 16:53:16'),
(22, 'TIS-2026-000022', 11, 'TL-2026-000011', 'SCREW DRIVER', 1, 0, 1, 'KYLE', 'DRIVER', '2026-09-17', '16:59:00', '2026-09-18', NULL, NULL, 'GOOD', NULL, 'ASDASD', 'ASDA', NULL, '', 'ISSUED', NULL, NULL, 'DASDA', NULL, '2026-09-17 16:59:34', 'admin', '2026-09-17 16:59:34'),
(23, 'TIS-2026-000023', 11, 'TL-2026-000011', 'SCREW DRIVER', 1, 0, 1, 'BRYAN', 'MECHANIC', '2026-09-17', '17:03:00', '2026-09-18', NULL, NULL, 'GOOD', NULL, 'dasd', 'ASDA', NULL, '', 'ISSUED', NULL, NULL, 'asdasd', NULL, '2026-09-17 17:03:33', 'admin', '2026-09-17 17:03:33'),
(24, 'TIS-2026-000024', 12, 'TL-2026-000011', 'SCREW DRIVER', 1, 0, 1, 'KYLE', 'DRIVER', '2026-09-17', '17:09:00', NULL, NULL, NULL, 'GOOD', NULL, 'TEST', 'ASDA', NULL, '', 'ISSUED', NULL, NULL, 'TEST', NULL, '2026-09-17 17:09:27', 'admin', '2026-09-17 17:09:27'),
(25, 'TIS-2026-000025', 12, 'TL-2026-000011', 'SCREW DRIVER', 1, 0, 1, 'JAMIE', 'DRIVER', '2026-09-17', '17:09:00', '2026-09-18', NULL, NULL, 'GOOD', NULL, 'TEST', '', NULL, '', 'ISSUED', NULL, NULL, '', NULL, '2026-09-17 17:09:48', 'admin', '2026-09-17 17:09:48');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transactions`
--

CREATE TABLE `tbl_transactions` (
  `transaction_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `checkin_time` time NOT NULL,
  `checkout_time` time DEFAULT NULL,
  `stage_id` int(11) NOT NULL COMMENT '1-10',
  `shooter_type` enum('PNP','CIVILIAN') NOT NULL,
  `shooter_name` varchar(100) NOT NULL,
  `range_assistant_id` int(11) DEFAULT NULL,
  `rangefee_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `targetboard_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ammunition_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('ACTIVE','COMPLETED','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
  `notes` text DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transactions`
--

INSERT INTO `tbl_transactions` (`transaction_id`, `transaction_date`, `checkin_time`, `checkout_time`, `stage_id`, `shooter_type`, `shooter_name`, `range_assistant_id`, `rangefee_amount`, `targetboard_amount`, `ammunition_amount`, `total_amount`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '2026-06-24', '10:21:00', '10:28:00', 1, 'PNP', 'KUKAY BARAYKAY', 1, 300.00, 15.00, 0.00, 315.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 10:22:22', '2026-06-24 10:28:55'),
(2, '2026-06-24', '10:38:00', '10:52:00', 4, 'CIVILIAN', 'EDUARDO CARPIO', 5, 300.00, 90.00, 500.00, 890.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 10:38:48', '2026-06-24 10:52:57'),
(3, '2026-06-24', '11:08:00', '11:24:00', 8, 'CIVILIAN', 'BRYAN', 4, 300.00, 200.00, 0.00, 500.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 11:09:30', '2026-06-24 11:24:24'),
(4, '2026-06-24', '11:09:00', '12:56:00', 3, 'CIVILIAN', 'ELEK', 2, 300.00, 150.00, 0.00, 450.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 11:09:54', '2026-06-24 12:56:02'),
(5, '2026-06-24', '11:09:00', '12:55:00', 1, 'PNP', 'ANGEL', 6, 300.00, 75.00, 555.00, 930.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 11:10:26', '2026-06-24 12:55:58'),
(6, '2026-06-24', '13:04:00', '13:42:00', 1, 'CIVILIAN', 'KENGKOY', 7, 300.00, 75.00, 123.00, 498.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 13:05:42', '2026-06-24 15:54:18'),
(7, '2026-06-24', '15:07:00', '16:20:00', 1, 'PNP', 'CESS ALINO', 1, 300.00, 300.00, 0.00, 600.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 15:08:14', '2026-06-24 16:20:59'),
(8, '2026-06-24', '15:16:00', '16:20:00', 2, 'CIVILIAN', 'ALEX HABIG', 5, 300.00, 150.00, 0.00, 450.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 15:17:07', '2026-06-24 16:20:56'),
(9, '2026-06-24', '15:52:00', '16:20:00', 3, 'PNP', 'CESS ALINO', 4, 333.00, 222.00, 0.00, 555.00, 'COMPLETED', 'test', 'admin', '2026-06-24 15:52:52', '2026-06-24 16:20:52'),
(10, '2026-06-24', '16:07:00', '16:20:00', 4, 'PNP', 'AEROL TOMARSE', 7, 300.00, 130.00, 0.00, 430.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 16:08:19', '2026-06-24 16:20:49'),
(11, '2026-06-24', '16:13:00', '16:20:00', 5, 'PNP', 'ALEX PRADO', 4, 300.00, 230.00, 250.00, 780.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 16:14:19', '2026-06-24 16:20:40'),
(12, '2026-06-24', '16:45:00', '16:46:00', 2, 'CIVILIAN', 'ALEX PRADO', 2, 300.00, 250.00, 500.00, 1050.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 16:46:08', '2026-06-24 16:46:16'),
(13, '2026-06-24', '17:02:00', '09:15:00', 4, 'PNP', 'ROMMEL QUISORA', 3, 300.00, 250.00, 700.00, 1250.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 17:02:24', '2026-06-26 09:15:34'),
(14, '2026-06-24', '17:03:00', '09:15:00', 6, 'CIVILIAN', 'CARLO DAGDAG', 5, 300.00, 300.00, 1000.00, 1600.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 17:04:26', '2026-06-26 09:15:28'),
(15, '2026-06-24', '17:12:00', '09:15:00', 8, 'CIVILIAN', 'LEE ALINO', 4, 300.00, 200.00, 1500.00, 2000.00, 'COMPLETED', 'TEST', 'admin', '2026-06-24 17:13:01', '2026-06-26 09:15:24'),
(16, '2026-06-26', '09:15:00', '15:14:00', 1, 'PNP', 'KYLE', 7, 300.00, 50.00, 0.00, 350.00, 'COMPLETED', 'TEST', 'ADMIN', '2026-06-26 09:15:52', '2026-06-26 15:14:51'),
(17, '2026-06-26', '15:12:00', '15:14:00', 2, 'PNP', 'ALFRED', 3, 300.00, 180.00, 1500.00, 1980.00, 'COMPLETED', 'TO FOLLOW AMMUNITION PAYMENT', 'ADMIN', '2026-06-26 15:12:37', '2026-06-26 15:14:10'),
(18, '2026-06-26', '15:42:00', '14:02:00', 8, 'CIVILIAN', 'BRYAN', 6, 300.00, 200.00, 0.00, 500.00, 'COMPLETED', 'TO FOLLOW AMMUNITION PAYMENT', 'ADMIN', '2026-06-26 15:43:36', '2026-06-27 14:02:33'),
(19, '2026-06-27', '14:03:00', '14:07:00', 1, 'CIVILIAN', 'ELEK ELEK', 4, 300.00, 500.00, 5000.00, 5800.00, 'COMPLETED', 'TO FOLLOW AMMUNITION PAYMENT', 'ADMIN', '2026-06-27 14:04:42', '2026-06-27 14:07:11'),
(20, '2026-06-27', '14:25:00', '14:31:00', 1, 'CIVILIAN', 'ELEK POGI', 7, 300.00, 25.00, 3000.00, 3325.00, 'COMPLETED', 'TO FOLLOW AMMUNITION PAYMENT', 'ADMIN', '2026-06-27 14:26:18', '2026-06-27 14:31:49'),
(21, '2026-06-27', '14:33:00', '09:38:00', 1, 'PNP', 'GENERAL TORRE', 4, 600.00, 25.00, 0.00, 625.00, 'COMPLETED', '', 'ADMIN', '2026-06-27 14:38:09', '2026-07-29 09:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaction_documents`
--

CREATE TABLE `tbl_transaction_documents` (
  `document_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `document_type` enum('VALID_ID','LTOPF','FIREARM_REG','PTCFOR','SHOOTER_PHOTO') NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_path` varchar(500) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_by` varchar(50) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transaction_documents`
--

INSERT INTO `tbl_transaction_documents` (`document_id`, `transaction_id`, `document_type`, `document_name`, `document_path`, `file_size`, `file_type`, `uploaded_by`, `uploaded_at`) VALUES
(1, 1, 'VALID_ID', '1_VALID_ID_1782268084.jpg', 'uploads/documents/1_VALID_ID_1782268084.jpg', '137.17 KB', 'image/jpeg', 'admin', '2026-06-24 10:28:04'),
(2, 1, 'LTOPF', '1_LTOPF_1782268094.jpg', 'uploads/documents/1_LTOPF_1782268094.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 10:28:14'),
(3, 1, 'FIREARM_REG', '1_FIREARM_REG_1782268102.jpg', 'uploads/documents/1_FIREARM_REG_1782268102.jpg', '137.64 KB', 'image/jpeg', 'admin', '2026-06-24 10:28:22'),
(4, 1, 'PTCFOR', '1_PTCFOR_1782268106.jpg', 'uploads/documents/1_PTCFOR_1782268106.jpg', '137.69 KB', 'image/jpeg', 'admin', '2026-06-24 10:28:26'),
(6, 2, 'VALID_ID', '2_VALID_ID_1782269402.jpg', 'uploads/documents/2_VALID_ID_1782269402.jpg', '137.64 KB', 'image/jpeg', 'admin', '2026-06-24 10:50:02'),
(7, 2, 'LTOPF', '2_LTOPF_1782269418.jpg', 'uploads/documents/2_LTOPF_1782269418.jpg', '137.64 KB', 'image/jpeg', 'admin', '2026-06-24 10:50:18'),
(8, 2, 'FIREARM_REG', '2_FIREARM_REG_1782269424.jpg', 'uploads/documents/2_FIREARM_REG_1782269424.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 10:50:24'),
(9, 2, 'PTCFOR', '2_PTCFOR_1782269435.jpg', 'uploads/documents/2_PTCFOR_1782269435.jpg', '137.17 KB', 'image/jpeg', 'admin', '2026-06-24 10:50:35'),
(10, 2, 'SHOOTER_PHOTO', '2_SHOOTER_PHOTO_1782269444.jpg', 'uploads/documents/2_SHOOTER_PHOTO_1782269444.jpg', '137.17 KB', 'image/jpeg', 'admin', '2026-06-24 10:50:44'),
(11, 5, 'VALID_ID', '5_VALID_ID_1782271747.jpg', 'uploads/documents/5_VALID_ID_1782271747.jpg', '137.69 KB', 'image/jpeg', 'admin', '2026-06-24 11:29:07'),
(13, 5, 'FIREARM_REG', '5_FIREARM_REG_1782272251.jpg', 'uploads/documents/5_FIREARM_REG_1782272251.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 11:37:31'),
(14, 5, 'PTCFOR', '5_PTCFOR_1782276778.jpg', 'uploads/documents/5_PTCFOR_1782276778.jpg', '137.69 KB', 'image/jpeg', 'admin', '2026-06-24 12:52:58'),
(15, 5, 'SHOOTER_PHOTO', '5_SHOOTER_PHOTO_1782276785.jpg', 'uploads/documents/5_SHOOTER_PHOTO_1782276785.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 12:53:05'),
(16, 7, 'VALID_ID', '7_VALID_ID_1782285298.jpg', 'uploads/documents/7_VALID_ID_1782285298.jpg', '137.69 KB', 'image/jpeg', 'admin', '2026-06-24 15:14:58'),
(17, 7, 'LTOPF', '7_LTOPF_1782285305.jpg', 'uploads/documents/7_LTOPF_1782285305.jpg', '137.64 KB', 'image/jpeg', 'admin', '2026-06-24 15:15:05'),
(18, 7, 'FIREARM_REG', '7_FIREARM_REG_1782285309.jpg', 'uploads/documents/7_FIREARM_REG_1782285309.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 15:15:09'),
(19, 7, 'PTCFOR', '7_PTCFOR_1782285315.jpg', 'uploads/documents/7_PTCFOR_1782285315.jpg', '137.17 KB', 'image/jpeg', 'admin', '2026-06-24 15:15:15'),
(20, 7, 'SHOOTER_PHOTO', '7_SHOOTER_PHOTO_1782285321.jpg', 'uploads/documents/7_SHOOTER_PHOTO_1782285321.jpg', '137.64 KB', 'image/jpeg', 'admin', '2026-06-24 15:15:21'),
(21, 11, 'VALID_ID', '11_VALID_ID_1782289145.jpg', 'uploads/documents/11_VALID_ID_1782289145.jpg', '137.69 KB', 'image/jpeg', 'admin', '2026-06-24 16:19:05'),
(22, 11, 'LTOPF', '11_LTOPF_1782289151.jpg', 'uploads/documents/11_LTOPF_1782289151.jpg', '137.64 KB', 'image/jpeg', 'admin', '2026-06-24 16:19:11'),
(23, 11, 'FIREARM_REG', '11_FIREARM_REG_1782289155.jpg', 'uploads/documents/11_FIREARM_REG_1782289155.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 16:19:15'),
(24, 11, 'PTCFOR', '11_PTCFOR_1782289161.jpg', 'uploads/documents/11_PTCFOR_1782289161.jpg', '137.17 KB', 'image/jpeg', 'admin', '2026-06-24 16:19:21'),
(25, 11, 'SHOOTER_PHOTO', '11_SHOOTER_PHOTO_1782289166.jpg', 'uploads/documents/11_SHOOTER_PHOTO_1782289166.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 16:19:26'),
(26, 17, 'VALID_ID', '17_VALID_ID_1782457983.jpg', 'uploads/documents/17_VALID_ID_1782457983.jpg', '5.21 KB', 'image/jpeg', 'ADMIN', '2026-06-26 15:13:03'),
(27, 17, 'LTOPF', '17_LTOPF_1782457988.jpg', 'uploads/documents/17_LTOPF_1782457988.jpg', '9.8 KB', 'image/jpeg', 'ADMIN', '2026-06-26 15:13:08'),
(28, 17, 'FIREARM_REG', '17_FIREARM_REG_1782457993.jpg', 'uploads/documents/17_FIREARM_REG_1782457993.jpg', '61.04 KB', 'image/jpeg', 'ADMIN', '2026-06-26 15:13:13'),
(29, 17, 'PTCFOR', '17_PTCFOR_1782457999.jpg', 'uploads/documents/17_PTCFOR_1782457999.jpg', '59.36 KB', 'image/jpeg', 'ADMIN', '2026-06-26 15:13:19'),
(30, 17, 'SHOOTER_PHOTO', '17_SHOOTER_PHOTO_1782458005.jpg', 'uploads/documents/17_SHOOTER_PHOTO_1782458005.jpg', '37.82 KB', 'image/jpeg', 'ADMIN', '2026-06-26 15:13:25'),
(31, 19, 'VALID_ID', '19_VALID_ID_1782540363.jpg', 'uploads/documents/19_VALID_ID_1782540363.jpg', '53.81 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:06:03'),
(32, 19, 'LTOPF', '19_LTOPF_1782540371.jpg', 'uploads/documents/19_LTOPF_1782540371.jpg', '60.81 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:06:11'),
(33, 19, 'FIREARM_REG', '19_FIREARM_REG_1782540380.jpg', 'uploads/documents/19_FIREARM_REG_1782540380.jpg', '59.36 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:06:20'),
(34, 19, 'PTCFOR', '19_PTCFOR_1782540387.jpg', 'uploads/documents/19_PTCFOR_1782540387.jpg', '9.8 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:06:27'),
(35, 19, 'SHOOTER_PHOTO', '19_SHOOTER_PHOTO_1782540395.jpg', 'uploads/documents/19_SHOOTER_PHOTO_1782540395.jpg', '5.21 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:06:35'),
(36, 20, 'VALID_ID', '20_VALID_ID_1782541708.jpg', 'uploads/documents/20_VALID_ID_1782541708.jpg', '5.21 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:28:28'),
(37, 20, 'LTOPF', '20_LTOPF_1782541715.jpg', 'uploads/documents/20_LTOPF_1782541715.jpg', '9.8 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:28:35'),
(38, 20, 'FIREARM_REG', '20_FIREARM_REG_1782541724.jpg', 'uploads/documents/20_FIREARM_REG_1782541724.jpg', '61.04 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:28:44'),
(39, 20, 'PTCFOR', '20_PTCFOR_1782541733.jpg', 'uploads/documents/20_PTCFOR_1782541733.jpg', '59.36 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:28:53'),
(41, 20, 'SHOOTER_PHOTO', '20_SHOOTER_PHOTO_1782542609.jpg', 'uploads/documents/20_SHOOTER_PHOTO_1782542609.jpg', '60.81 KB', 'image/jpeg', 'ADMIN', '2026-06-27 14:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trips`
--

CREATE TABLE `tbl_trips` (
  `trip_id` int(11) NOT NULL,
  `trip_code` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `booking_reference` varchar(50) DEFAULT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `trip_type` enum('ONE_WAY','ROUND_TRIP','MULTI_DROP') DEFAULT 'ONE_WAY',
  `priority` enum('LOW','NORMAL','HIGH','URGENT') DEFAULT 'NORMAL',
  `scheduled_date` date NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `actual_delivery_date` date DEFAULT NULL,
  `origin` text DEFAULT NULL,
  `destination` text DEFAULT NULL,
  `cargo_description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `estimated_weight` decimal(15,2) DEFAULT 0.00,
  `special_instructions` text DEFAULT NULL,
  `trip_status` enum('DRAFT','SCHEDULED','ASSIGNED','DISPATCHED','IN_TRANSIT','DELIVERED','COMPLETED','CANCELLED') DEFAULT 'DRAFT',
  `remarks` text DEFAULT NULL,
  `has_assignment` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trips`
--

INSERT INTO `tbl_trips` (`trip_id`, `trip_code`, `customer_id`, `booking_reference`, `service_type`, `trip_type`, `priority`, `scheduled_date`, `pickup_date`, `expected_delivery_date`, `actual_delivery_date`, `origin`, `destination`, `cargo_description`, `quantity`, `unit`, `estimated_weight`, `special_instructions`, `trip_status`, `remarks`, `has_assignment`, `created_at`, `created_by`, `updated_at`) VALUES
(2, 'TRP-2026-000001', 1, 'BK-12301', 'Trucking Service', 'ROUND_TRIP', 'NORMAL', '2026-09-03', '2026-09-04', '2026-09-07', NULL, 'MANILA', 'LAGUNA', 'TEST', 10, 'KG', 1000.00, 'TEST', 'IN_TRANSIT', 'TEST', 1, '2026-09-03 22:57:20', 'ADMIN', '2026-09-16 10:16:56'),
(3, 'TRP-2026-000002', 4, 'BK-21301', 'Trucking Service', 'ROUND_TRIP', 'NORMAL', '2026-09-04', '2026-09-04', '2026-09-04', NULL, 'MANILA', 'CAVITE', 'TEST', 1, 'KG', 100.00, 'TEST', 'IN_TRANSIT', '', 1, '2026-09-04 08:14:50', 'ADMIN', '2026-09-16 10:16:49'),
(4, 'TRP-2026-000003', 3, 'BK-120301123', 'Trucking Service', 'ROUND_TRIP', 'NORMAL', '2026-09-04', '2026-09-04', '2026-09-04', NULL, 'manila', 'cebu', 'test', 1, 'kg', 100.00, 'handle with card', 'IN_TRANSIT', 'test', 1, '2026-09-04 09:11:42', 'ADMIN', '2026-09-04 09:34:35'),
(5, 'TRP-2026-000004', 8, '', 'Trucking Service', 'ROUND_TRIP', 'HIGH', '2026-09-04', '2026-09-04', '2026-09-04', NULL, 'MANILA', 'VALENZUELA', 'GOODS', 5, 'PCS', 50.00, 'Handle with care', 'IN_TRANSIT', '', 1, '2026-09-04 10:10:13', 'ADMIN', '2026-09-04 10:13:37'),
(6, 'TRP-2026-000005', 2, 'BK0123910', 'Trucking Service', 'MULTI_DROP', 'URGENT', '2026-09-04', '2026-09-04', '2026-09-07', NULL, 'MANILA', 'DAVA', 'TEST', 1, 'KG', 500.00, 'TEST', 'IN_TRANSIT', '', 1, '2026-09-04 14:01:36', 'ADMIN', '2026-09-04 14:13:48'),
(7, 'TRP-2026-000006', 6, 'BK-022222', 'Trucking Service', 'ONE_WAY', 'NORMAL', '2026-09-04', '2026-09-04', '2026-09-04', NULL, 'MANILA', 'LAGUNA', 'SAMPLE CARGO ENTRY', 1, 'KG', 50.00, 'HANDLE WITH CARE', 'IN_TRANSIT', '', 1, '2026-09-04 15:08:32', 'ADMIN', '2026-09-16 10:16:38'),
(8, 'TRP-2026-000007', 10, '', 'Trucking Service', 'ROUND_TRIP', 'NORMAL', '2026-09-04', '2026-09-04', '2026-09-04', NULL, 'manila', 'cavite', 'test', 1, 'kg', 150.00, 'handle with care', 'ASSIGNED', '', 1, '2026-09-04 15:19:53', 'ADMIN', '2026-09-04 15:23:56'),
(9, 'TRP-2026-000008', 10, 'BK-10231-', 'Trucking Service', 'ONE_WAY', 'NORMAL', '2026-09-16', '2026-09-16', '2026-09-24', NULL, 'MANILA', 'CAGAYAN', 'TEST', 1, 'KG', 55.00, 'TEST', 'ASSIGNED', '', 1, '2026-09-16 09:57:22', 'admin', '2026-09-16 09:58:03'),
(10, 'TRP-2026-000009', 5, 'BK-10231', 'Trucking Service', 'ROUND_TRIP', 'NORMAL', '2026-09-17', '2026-09-17', '2026-09-17', NULL, 'CAVITE', 'TAGUIG', 'TEST', 1, 'KG', 1.00, 'TEST', 'COMPLETED', '', 1, '2026-09-17 09:36:45', 'admin', '2026-09-17 09:58:41'),
(11, 'TRP-2026-000010', 2, 'BK-2026-0917', 'Trucking Service', 'ROUND_TRIP', 'HIGH', '2026-09-17', '2026-09-17', '2026-09-18', NULL, 'MANILA', 'ALBAY', 'TEST', 50, 'Box', 200.00, 'Handle with card', 'ASSIGNED', 'test', 1, '2026-09-17 13:53:33', 'admin', '2026-09-17 14:03:32'),
(12, 'TRP-2026-000011', 3, 'BK-2026-0917V2', 'Trucking Service', 'ONE_WAY', 'NORMAL', '2026-09-17', '2026-09-17', '2026-09-18', NULL, 'MANILA', 'TAGUIG', 'GYM EQUIPMENTS', 5, 'KG', 250.00, 'Handle with care', 'COMPLETED', '', 1, '2026-09-17 14:05:58', 'admin', '2026-09-17 14:34:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trip_assignments`
--

CREATE TABLE `tbl_trip_assignments` (
  `assignment_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `vehicle_type` enum('RIGID','TRACTOR_CHASSIS','TRACTOR_RENTED_CHASSIS','RENTED_ALL') DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `tractor_plate` varchar(50) DEFAULT NULL,
  `chassis_plate` varchar(50) DEFAULT NULL,
  `chassis_type` enum('OWNED','RENTED') DEFAULT 'OWNED',
  `vendor_name` varchar(100) DEFAULT NULL,
  `rental_rate` decimal(15,2) DEFAULT 0.00,
  `rental_start_date` date DEFAULT NULL,
  `rental_end_date` date DEFAULT NULL,
  `rental_agreement_no` varchar(100) DEFAULT NULL,
  `vendor_contact_person` varchar(100) DEFAULT NULL,
  `vendor_contact_number` varchar(50) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `helper_name` varchar(100) DEFAULT NULL,
  `assignment_date` date DEFAULT NULL,
  `dispatch_time` time DEFAULT NULL,
  `dispatch_location` varchar(255) DEFAULT NULL,
  `odometer_before_trip` decimal(15,2) DEFAULT 0.00,
  `fuel_level` decimal(5,2) DEFAULT 0.00,
  `assignment_status` enum('ASSIGNED','DISPATCHED','IN_TRANSIT','COMPLETED','CANCELLED') DEFAULT 'ASSIGNED',
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trip_assignments`
--

INSERT INTO `tbl_trip_assignments` (`assignment_id`, `trip_id`, `vehicle_type`, `truck_plate`, `tractor_plate`, `chassis_plate`, `chassis_type`, `vendor_name`, `rental_rate`, `rental_start_date`, `rental_end_date`, `rental_agreement_no`, `vendor_contact_person`, `vendor_contact_number`, `driver_name`, `helper_name`, `assignment_date`, `dispatch_time`, `dispatch_location`, `odometer_before_trip`, `fuel_level`, `assignment_status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(2, 2, 'RIGID', 'DEF-9012', '', '', 'RENTED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Grace Santos', 'Emilio Aguinaldo', '2026-09-03', '22:58:00', 'MAIN YARD', 123.00, 10.00, 'ASSIGNED', 'TEST', '2026-09-03 22:58:29', 'ADMIN', '2026-09-03 22:58:29'),
(3, 3, 'TRACTOR_RENTED_CHASSIS', '', 'TRC-002', 'CHS-002', 'RENTED', 'ABC Trucking Rentals', 5000.00, '2026-09-04', '2026-09-11', 'RA-12301', 'TEST', '01293100', 'Grace Santos', 'Emilio Aguinaldo', '2026-09-04', '08:15:00', 'MAIN YARD', 120310.00, 10.00, 'ASSIGNED', 'TEST', '2026-09-04 08:15:37', 'ADMIN', '2026-09-04 08:15:37'),
(4, 4, 'TRACTOR_CHASSIS', '', 'TRC-007', 'CHS-007', 'OWNED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Ramon Cruz', 'Melchora Aquino', '2026-09-04', '09:12:00', 'MAIN YARD', 50500.00, 50.00, 'ASSIGNED', 'test', '2026-09-04 09:12:46', 'ADMIN', '2026-09-04 09:12:46'),
(5, 5, 'RIGID', 'STU-9012', '', '', 'OWNED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Mark Tan', 'KYLE HELPER', '2026-09-04', '10:10:00', 'MAIN YARD', 15015.00, 10.00, 'ASSIGNED', '', '2026-09-04 10:10:39', 'ADMIN', '2026-09-04 10:10:39'),
(6, 6, 'TRACTOR_RENTED_CHASSIS', '', 'TRC-010', 'CHS-010', 'RENTED', 'XYZ Transport Services', 10000.00, '2026-09-04', '2026-09-04', 'RA-1023', 'SADASD', '00912301', 'Maria Santos', 'Marcela Agoncillo', '2026-09-04', '14:03:00', 'MAIN YARD', 123.00, 0.00, 'ASSIGNED', '', '2026-09-04 14:04:03', 'ADMIN', '2026-09-04 14:04:03'),
(7, 7, 'RIGID', 'XYZ-5678', '', '', 'OWNED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Pedro Reyes', 'Marcela Agoncillo', '2026-09-04', '15:10:00', 'garahe', 120000.00, 50.00, 'ASSIGNED', '', '2026-09-04 15:10:31', 'ADMIN', '2026-09-04 15:10:31'),
(8, 8, 'TRACTOR_RENTED_CHASSIS', '', 'TRC-007', 'CHS-003', 'RENTED', 'Isuzu Fleet Services', 150000.00, '2026-09-04', '2026-09-07', '', '', '', 'Maria Santos', 'Marcela Agoncillo', '2026-09-04', '15:22:00', 'MAIN YARD', 150000.00, 50.00, 'ASSIGNED', 'sample tractor and chassis owned', '2026-09-04 15:23:56', 'ADMIN', '2026-09-04 15:25:12'),
(9, 9, 'TRACTOR_CHASSIS', '', 'TRC-003', 'CHS-002', 'OWNED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Grace Santos', 'Emilio Aguinaldo', '2026-09-16', '09:57:00', 'MAIN YARD', 156233.00, 999.99, 'COMPLETED', 'TEST', '2026-09-16 09:58:03', 'admin', '2026-09-17 14:31:51'),
(10, 10, 'RIGID', 'DEF-9012', '', '', 'OWNED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Pedro Reyes', 'Marcela Agoncillo', '2026-09-17', '21:37:00', 'MAIN YARD', 1231123.00, 0.00, 'ASSIGNED', 'TEST', '2026-09-17 09:37:50', 'admin', '2026-09-17 09:37:50'),
(11, 11, 'TRACTOR_CHASSIS', '', 'TRC-009', 'CHS-009', 'OWNED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Ramon Cruz', 'Melchora Aquino', '2026-09-17', '13:54:00', 'MAIN YARD', 26563.00, 100.00, 'ASSIGNED', 'test', '2026-09-17 13:54:10', 'admin', '2026-09-17 14:04:46'),
(12, 12, 'TRACTOR_CHASSIS', '', 'TRC-007', 'CHS-007', 'OWNED', '', 0.00, '0000-00-00', '0000-00-00', '', '', '', 'Mark Tan', 'Juan Luna', '2026-09-17', '14:06:00', 'MAIN YARD', 26326.00, 100.00, 'COMPLETED', 'test', '2026-09-17 14:06:28', 'admin', '2026-09-17 14:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trip_cargo_items`
--

CREATE TABLE `tbl_trip_cargo_items` (
  `item_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `quantity` decimal(15,2) DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `weight` decimal(15,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trip_cargo_items`
--

INSERT INTO `tbl_trip_cargo_items` (`item_id`, `trip_id`, `item_description`, `quantity`, `unit`, `weight`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 9, 'test', 1.00, 'test', 5.00, 'test', '2026-09-17 09:25:29', 'admin', '2026-09-17 09:25:29'),
(2, 10, 'TEST', 1.00, 'KG', 1.00, 'TEST', '2026-09-17 09:37:02', 'admin', '2026-09-17 09:37:02'),
(3, 10, 'TEST 2', 1.00, 'KG', 2.00, 'TEST', '2026-09-17 09:37:12', 'admin', '2026-09-17 09:37:12'),
(4, 10, 'TEST 3', 1.00, 'KG', 2.00, 'TEST', '2026-09-17 09:37:27', 'admin', '2026-09-17 09:37:27'),
(5, 11, 'Automotive Parts', 25.00, 'Box', 120.00, '', '2026-09-17 14:00:43', 'admin', '2026-09-17 14:00:43'),
(6, 11, 'Mechanic Tools', 25.00, 'Box', 80.00, '', '2026-09-17 14:01:33', 'admin', '2026-09-17 14:01:33'),
(7, 12, 'DUMBELLS', 5.00, 'KG', 250.00, 'TEST', '2026-09-17 14:17:07', 'admin', '2026-09-17 14:17:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trip_waypoints`
--

CREATE TABLE `tbl_trip_waypoints` (
  `waypoint_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `waypoint_type` enum('GARAGE','EMPTY_CONTAINER_PICKUP','CLIENT_WAREHOUSE','DELIVERY_DESTINATION','PORT_TERMINAL','RETURN_POINT','PICKUP_LOCATION','OTHER') DEFAULT 'OTHER',
  `waypoint_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `expected_arrival` datetime DEFAULT NULL,
  `expected_departure` datetime DEFAULT NULL,
  `actual_arrival` datetime DEFAULT NULL,
  `actual_departure` datetime DEFAULT NULL,
  `waypoint_status` enum('PENDING','ARRIVED','DEPARTED','COMPLETED') DEFAULT 'PENDING',
  `arrival_remarks` text DEFAULT NULL,
  `departure_remarks` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trip_waypoints`
--

INSERT INTO `tbl_trip_waypoints` (`waypoint_id`, `trip_id`, `sequence`, `waypoint_type`, `waypoint_name`, `address`, `city`, `province`, `expected_arrival`, `expected_departure`, `actual_arrival`, `actual_departure`, `waypoint_status`, `arrival_remarks`, `departure_remarks`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(3, 2, 1, 'GARAGE', 'MAIN YARD', 'NAIC', NULL, NULL, '2026-09-03 22:58:00', '2026-09-03 22:59:00', NULL, NULL, 'PENDING', NULL, NULL, 'TEST', '2026-09-03 22:59:10', 'ADMIN', '2026-09-03 22:59:10'),
(4, 2, 2, 'CLIENT_WAREHOUSE', 'LAGUNA WAREHOUSE', 'LAGUNA', NULL, NULL, '2026-09-03 22:59:00', '2026-09-03 13:02:00', NULL, NULL, 'PENDING', NULL, NULL, 'DONE', '2026-09-03 22:59:49', 'ADMIN', '2026-09-03 22:59:49'),
(5, 2, 3, 'GARAGE', 'MAIN YARD', 'NAIC', NULL, NULL, '2026-09-03 23:00:00', '2026-09-03 23:00:00', NULL, NULL, 'PENDING', NULL, NULL, 'TEST', '2026-09-03 23:00:11', 'ADMIN', '2026-09-03 23:00:11'),
(6, 3, 1, 'GARAGE', 'main yard', 'tes', NULL, NULL, '2026-09-04 08:15:00', '2026-09-04 08:15:00', NULL, NULL, 'PENDING', NULL, NULL, 'test', '2026-09-04 08:15:59', 'ADMIN', '2026-09-04 08:15:59'),
(7, 4, 1, 'GARAGE', 'MAIN YARD', 'NAIC', NULL, NULL, '2026-09-04 09:13:00', '2026-09-04 12:13:00', '2026-09-04 09:33:00', '2026-09-04 09:34:00', 'DEPARTED', 'TEST', '', '', '2026-09-04 09:13:19', 'ADMIN', '2026-09-04 09:34:11'),
(8, 4, 2, 'EMPTY_CONTAINER_PICKUP', 'MANILA PORTT', 'TEST', NULL, NULL, '2026-09-04 13:13:00', '2026-09-04 14:13:00', '2026-09-04 09:34:00', '2026-09-04 09:34:00', 'DEPARTED', '', '', '', '2026-09-04 09:13:47', 'ADMIN', '2026-09-04 09:34:19'),
(9, 4, 3, 'CLIENT_WAREHOUSE', 'CEBU', 'CEBU', NULL, NULL, '2026-09-04 15:14:00', '2026-09-04 17:14:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-04 09:14:21', 'ADMIN', '2026-09-04 09:14:21'),
(10, 5, 1, 'GARAGE', 'main yard', 'naic', NULL, NULL, '2026-09-04 10:10:00', '2026-09-04 12:10:00', '2026-09-04 10:13:00', '2026-09-04 11:13:00', 'DEPARTED', '', '', '', '2026-09-04 10:11:00', 'ADMIN', '2026-09-04 10:13:56'),
(11, 5, 2, 'CLIENT_WAREHOUSE', 'Client warehourse', 'test', NULL, NULL, '2026-09-04 01:11:00', '2026-09-04 02:15:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-04 10:11:36', 'ADMIN', '2026-09-04 10:11:36'),
(12, 5, 3, 'GARAGE', 'main yard', 'naic', NULL, NULL, '2026-09-04 03:11:00', '2026-09-04 03:11:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-04 10:11:58', 'ADMIN', '2026-09-04 10:11:58'),
(13, 6, 1, 'GARAGE', 'MAIN YARD', 'NAIC', NULL, NULL, '2026-09-04 14:04:00', '2026-09-04 15:04:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-04 14:04:30', 'ADMIN', '2026-09-04 14:04:30'),
(14, 6, 2, 'CLIENT_WAREHOUSE', 'LAGUNA WAREHOUSE', 'LAGUNA', NULL, NULL, '2026-09-04 15:04:00', '2026-09-04 14:06:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-04 14:05:07', 'ADMIN', '2026-09-04 14:05:07'),
(15, 7, 1, 'GARAGE', 'MAIN YARD', 'NAIC', NULL, NULL, '2026-09-04 15:11:00', '2026-09-04 16:11:00', '2026-09-04 15:17:00', '2026-09-04 17:18:00', 'DEPARTED', '', '', '', '2026-09-04 15:11:20', 'ADMIN', '2026-09-04 15:18:06'),
(16, 7, 2, 'CLIENT_WAREHOUSE', 'MANILA', 'MANILA', NULL, NULL, '2026-09-04 17:11:00', '2026-09-04 19:11:00', '2026-09-04 15:18:00', '2026-09-04 15:18:00', 'DEPARTED', '', '', '', '2026-09-04 15:11:46', 'ADMIN', '2026-09-04 15:18:36'),
(17, 7, 3, 'DELIVERY_DESTINATION', 'LAGUNA WAREHOUSE', 'LAGUNA', NULL, NULL, '2026-09-04 20:12:00', '2026-09-04 21:12:00', '2026-09-04 15:18:00', '2026-09-04 15:18:00', 'DEPARTED', '', '', '', '2026-09-04 15:12:25', 'ADMIN', '2026-09-04 15:18:40'),
(18, 7, 4, 'GARAGE', 'GARAHE', 'NAIC', NULL, NULL, '2026-09-04 23:13:00', '2026-09-04 12:13:00', '2026-09-04 15:18:00', '2026-09-04 15:18:00', 'DEPARTED', '', '', '', '2026-09-04 15:13:39', 'ADMIN', '2026-09-04 15:18:44'),
(19, 9, 1, 'GARAGE', 'GARAHE', 'NAIC', NULL, NULL, '2026-09-16 09:58:00', '2026-09-16 09:58:00', NULL, NULL, 'PENDING', NULL, NULL, 'TEST', '2026-09-16 09:58:19', 'admin', '2026-09-16 09:58:19'),
(20, 9, 2, 'CLIENT_WAREHOUSE', 'NAIC', 'TEST', NULL, NULL, '2026-09-16 09:58:00', '2026-09-16 09:58:00', NULL, NULL, 'PENDING', NULL, NULL, 'TEST', '2026-09-16 09:58:32', 'admin', '2026-09-16 09:58:32'),
(21, 9, 3, 'GARAGE', 'GARAHE', 'TEST', NULL, NULL, '2026-09-16 09:58:00', '2026-09-16 09:58:00', NULL, NULL, 'PENDING', NULL, NULL, 'TEST', '2026-09-16 09:58:44', 'admin', '2026-09-16 09:58:44'),
(22, 10, 1, 'GARAGE', 'GARAHE', 'NAIC', NULL, NULL, NULL, NULL, '2026-09-17 09:40:00', '2026-09-17 09:40:00', 'DEPARTED', '', '', '', '2026-09-17 09:39:29', 'admin', '2026-09-17 09:40:37'),
(23, 10, 2, 'CLIENT_WAREHOUSE', 'CAVITE', 'TEST', NULL, NULL, NULL, NULL, '2026-09-17 09:40:00', '2026-09-17 09:40:00', 'DEPARTED', '', '', '', '2026-09-17 09:39:38', 'admin', '2026-09-17 09:40:41'),
(24, 10, 3, 'DELIVERY_DESTINATION', 'TAGUIG', 'TEST', NULL, NULL, NULL, NULL, '2026-09-17 09:40:00', '2026-09-17 09:40:00', 'DEPARTED', '', '', '', '2026-09-17 09:40:10', 'admin', '2026-09-17 09:40:46'),
(25, 11, 1, 'GARAGE', 'Garage', 'Naic', NULL, NULL, '2026-09-17 13:54:00', '2026-09-17 14:54:00', NULL, NULL, 'PENDING', NULL, NULL, 'test', '2026-09-17 13:54:29', 'admin', '2026-09-17 13:54:29'),
(26, 11, 2, 'EMPTY_CONTAINER_PICKUP', 'Manila Port', 'test', NULL, NULL, '2026-09-17 15:54:00', '2026-09-17 15:54:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-17 13:54:59', 'admin', '2026-09-17 13:54:59'),
(27, 11, 3, 'CLIENT_WAREHOUSE', 'Manila Warehouse', '', NULL, NULL, '2026-09-17 16:55:00', '2026-09-17 16:55:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-17 13:55:31', 'admin', '2026-09-17 13:55:31'),
(28, 11, 4, 'DELIVERY_DESTINATION', 'Albay warehouse', 'teest', NULL, NULL, '2026-09-17 23:55:00', '2026-09-18 02:55:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-17 13:56:05', 'admin', '2026-09-17 13:56:05'),
(29, 11, 5, 'PORT_TERMINAL', 'Manila Port', 'test', NULL, NULL, '2026-09-18 15:56:00', '2026-09-18 16:56:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-17 13:56:52', 'admin', '2026-09-17 13:56:52'),
(30, 11, 6, 'GARAGE', 'Garage', 'Naic', NULL, NULL, '2026-09-18 18:57:00', '2026-09-17 18:57:00', NULL, NULL, 'PENDING', NULL, NULL, '', '2026-09-17 13:57:14', 'admin', '2026-09-17 13:57:14'),
(31, 12, 1, 'GARAGE', 'Garage', 'naic', NULL, NULL, '2026-09-17 14:07:00', '2026-09-17 15:07:00', '2026-09-17 14:09:00', '2026-09-17 15:09:00', 'DEPARTED', '', '', '', '2026-09-17 14:07:06', 'admin', '2026-09-17 14:09:51'),
(32, 12, 2, 'CLIENT_WAREHOUSE', 'Manila Warehouse', 'Manila', NULL, NULL, '2026-09-17 15:07:00', '2026-09-17 16:07:00', '2026-09-17 14:11:00', '2026-09-17 14:11:00', 'DEPARTED', '', '', 'test', '2026-09-17 14:07:41', 'admin', '2026-09-17 14:11:32'),
(33, 12, 3, 'DELIVERY_DESTINATION', 'Taguig Warehouse', 'test', NULL, NULL, '2026-09-17 16:07:00', '2026-09-17 17:07:00', '2026-09-17 14:22:00', '2026-09-17 14:22:00', 'DEPARTED', '', '', '', '2026-09-17 14:08:00', 'admin', '2026-09-17 14:22:20');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trucks`
--

CREATE TABLE `tbl_trucks` (
  `truck_id` int(11) NOT NULL,
  `truck_code` varchar(50) NOT NULL,
  `vehicle_config` enum('RIGID','TRACTOR','TRAILER') NOT NULL DEFAULT 'RIGID',
  `plate_number` varchar(50) DEFAULT NULL,
  `mv_file_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `body_type` varchar(50) DEFAULT NULL,
  `make` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `model_year` year(4) DEFAULT NULL,
  `chassis_number` varchar(50) DEFAULT NULL,
  `engine_number` varchar(50) DEFAULT NULL,
  `fuel_type` varchar(50) DEFAULT NULL,
  `fuel_tank_capacity` decimal(10,2) DEFAULT 0.00,
  `load_capacity` decimal(10,2) DEFAULT 0.00,
  `current_odometer` decimal(10,2) DEFAULT 0.00,
  `acquisition_date` date DEFAULT NULL,
  `acquired_from` varchar(100) DEFAULT NULL,
  `acquired_from_branch` varchar(100) DEFAULT NULL,
  `account_manager` varchar(100) DEFAULT NULL,
  `ownership` enum('COMPANY-OWNED','LEASED','RENTED') DEFAULT 'COMPANY-OWNED',
  `truck_status` enum('AVAILABLE','ASSIGNED','DISPATCHED','IN TRANSIT','RETURNING','UNDER MAINTENANCE','OUT OF SERVICE','RETIRED') NOT NULL DEFAULT 'AVAILABLE',
  `truck_image` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trucks`
--

INSERT INTO `tbl_trucks` (`truck_id`, `truck_code`, `vehicle_config`, `plate_number`, `mv_file_number`, `vehicle_type`, `body_type`, `make`, `model`, `model_year`, `chassis_number`, `engine_number`, `fuel_type`, `fuel_tank_capacity`, `load_capacity`, `current_odometer`, `acquisition_date`, `acquired_from`, `acquired_from_branch`, `account_manager`, `ownership`, `truck_status`, `truck_image`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'TRC-2026-0001', 'TRAILER', 'ABC-12345', 'MV-012931', '', 'CLOSED VAN', 'ISUZU', 'F-SERIES', '2022', 'CASC', 'ACAC', 'Diesel', 1.00, 2.00, 2.00, '2026-08-28', 'ABC MOTOR', '', 'TEST', 'COMPANY-OWNED', 'ASSIGNED', 'uploads/trucks/TRK_1_1787910312.png', 'TEST', '2026-08-28 17:45:12', 'ADMIN', '2026-08-30 21:50:33'),
(3, 'TRC-2026-0001', 'TRACTOR', 'truckhead', '', '', '', '', '', '0000', '', '', '', 0.00, 0.00, 0.00, '0000-00-00', '', '', '', 'COMPANY-OWNED', 'AVAILABLE', NULL, '', '2026-08-29 18:57:47', 'ADMIN', '2026-08-29 18:57:47'),
(5, 'TRK-2026-0001', 'RIGID', 'CMPL-TASDA', 'ASDASD', 'ASD', 'ASDA', 'SDASD', 'AASDA', '0000', '412SADASD', '', '', 0.00, 0.00, 0.00, '0000-00-00', '', '', '', 'COMPANY-OWNED', 'ASSIGNED', NULL, '', '2026-08-29 19:21:40', 'ADMIN', '2026-08-30 22:02:55'),
(6, 'TRK-2026-0001', 'RIGID', 'ABC-1234', 'MV-2026-0001', '10-Wheeler', 'Closed Van', 'Isuzu', 'F-Series', '2023', 'CHS-2026-0001', 'ENG-2026-0001', 'Diesel', 300.00, 15000.00, 45230.50, '2023-01-15', 'Isuzu Motors', 'Manila Branch', 'Juan Dela Cruz', 'COMPANY-OWNED', 'ASSIGNED', NULL, 'New unit, fully serviced', '2026-08-29 21:21:40', 'ADMIN', '2026-08-30 21:32:03'),
(7, 'TRK-2026-0002', 'RIGID', 'XYZ-5678', 'MV-2026-0002', '6-Wheeler', 'Open Truck', 'Hino', '300 Series', '2022', 'CHS-2026-0002', 'ENG-2026-0002', 'Diesel', 200.00, 8000.00, 78450.20, '2022-06-20', 'Hino Motors', 'Cebu Branch', 'Maria Santos', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Regular maintenance done', '2026-08-29 21:21:40', 'ADMIN', '2026-08-29 21:21:40'),
(8, 'TRK-2026-0003', 'RIGID', 'DEF-9012', 'MV-2026-0003', '10-Wheeler', 'Refrigerated Van', 'Mitsubishi', 'Fuso', '2024', 'CHS-2026-0003', 'ENG-2026-0003', 'Diesel', 350.00, 12000.00, 12500.00, '2024-02-10', 'Mitsubishi Motors', 'Davao Branch', 'Pedro Reyes', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Brand new refrigerated unit', '2026-08-29 21:21:40', 'ADMIN', '2026-09-03 22:14:22'),
(9, 'TRK-2026-0004', 'RIGID', 'GHI-3456', 'MV-2026-0004', '8-Wheeler', 'Flatbed', 'Isuzu', 'G-Series', '2021', 'CHS-2026-0004', 'ENG-2026-0004', 'Diesel', 250.00, 10000.00, 124500.00, '2021-11-05', 'Isuzu Motors', 'Manila Branch', 'Anna Lopez', 'COMPANY-OWNED', 'ASSIGNED', NULL, 'For heavy equipment transport', '2026-08-29 21:21:40', 'ADMIN', '2026-09-02 08:48:29'),
(10, 'TRK-2026-0005', 'RIGID', 'JKL-7890', 'MV-2026-0005', '6-Wheeler', 'Dump Truck', 'Hino', '500 Series', '2022', 'CHS-2026-0005', 'ENG-2026-0005', 'Diesel', 180.00, 9000.00, 87600.00, '2022-03-15', 'Hino Motors', 'Pampanga Branch', 'Ramon Cruz', 'COMPANY-OWNED', 'ASSIGNED', NULL, 'For construction materials', '2026-08-29 21:21:40', 'ADMIN', '2026-09-03 22:04:55'),
(11, 'TRK-2026-0006', 'RIGID', 'MNO-2345', 'MV-2026-0006', '10-Wheeler', 'Wing Van', 'Fuso', 'Canter', '2023', 'CHS-2026-0006', 'ENG-2026-0006', 'Diesel', 280.00, 14000.00, 34200.00, '2023-08-01', 'Mitsubishi Motors', 'Cebu Branch', 'Elena Rivera', 'COMPANY-OWNED', 'ASSIGNED', NULL, 'For express delivery', '2026-08-29 21:21:40', 'ADMIN', '2026-09-03 22:14:22'),
(12, 'TRK-2026-0007', 'RIGID', 'PQR-6789', 'MV-2026-0007', '8-Wheeler', 'Tank Trailer', 'Isuzu', 'F-Series', '2020', 'CHS-2026-0007', 'ENG-2026-0007', 'Diesel', 400.00, 25000.00, 156700.00, '2020-09-10', 'Isuzu Motors', 'Manila Branch', 'Mark Tan', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For fuel transport', '2026-08-29 21:21:40', 'ADMIN', '2026-08-30 21:26:29'),
(13, 'TRK-2026-0008', 'RIGID', 'STU-9012', 'MV-2026-0008', '6-Wheeler', 'Box Truck', 'Hino', '300 Series', '2024', 'CHS-2026-0008', 'ENG-2026-0008', 'Diesel', 220.00, 7000.00, 8200.00, '2024-01-20', 'Hino Motors', 'Davao Branch', 'Grace Santos', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Brand new box truck', '2026-08-29 21:21:40', 'ADMIN', '2026-08-30 21:26:29'),
(14, 'TRK-2026-0009', 'RIGID', 'VWX-3456', 'MV-2026-0009', '10-Wheeler', 'Curtain Side', 'Mitsubishi', 'Fuso', '2022', 'CHS-2026-0009', 'ENG-2026-0009', 'Diesel', 300.00, 16000.00, 65400.00, '2022-05-25', 'Mitsubishi Motors', 'Cebu Branch', 'Francisco Garcia', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For general cargo', '2026-08-29 21:21:40', 'ADMIN', '2026-08-30 21:26:29'),
(15, 'TRK-2026-0010', 'RIGID', 'YZA-7890', 'MV-2026-0010', '8-Wheeler', 'Drop Side', 'Isuzu', 'G-Series', '2021', 'CHS-2026-0010', 'ENG-2026-0010', 'Diesel', 260.00, 12000.00, 98500.00, '2021-12-01', 'Isuzu Motors', 'Manila Branch', 'Leticia Mendez', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For agricultural products', '2026-08-29 21:21:40', 'ADMIN', '2026-08-29 21:21:40'),
(16, 'TRK-2026-0011', 'TRACTOR', 'TRC-001', 'MV-2026-0011', 'Tractor Head', 'Heavy Duty', 'Isuzu', 'Giga', '2023', 'CHS-2026-0011', 'ENG-2026-0011', 'Diesel', 400.00, 30000.00, 45600.00, '2023-03-10', 'Isuzu Motors', 'Manila Branch', 'Ramon Santos', 'COMPANY-OWNED', 'ASSIGNED', NULL, 'Heavy duty tractor for container hauling', '2026-08-29 21:21:51', 'ADMIN', '2026-08-30 21:50:33'),
(17, 'TRK-2026-0012', 'TRACTOR', 'TRC-002', 'MV-2026-0012', 'Tractor Head', 'Medium Duty', 'Hino', '700 Series', '2022', 'CHS-2026-0012', 'ENG-2026-0012', 'Diesel', 350.00, 25000.00, 78200.00, '2022-07-15', 'Hino Motors', 'Cebu Branch', 'Maria Fernandez', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Medium duty tractor', '2026-08-29 21:21:51', 'ADMIN', '2026-08-30 20:22:36'),
(18, 'TRK-2026-0013', 'TRACTOR', 'TRC-003', 'MV-2026-0013', 'Tractor Head', 'Heavy Duty', 'Mitsubishi', 'Super Great', '2024', 'CHS-2026-0013', 'ENG-2026-0013', 'Diesel', 450.00, 35000.00, 8900.00, '2024-01-05', 'Mitsubishi Motors', 'Davao Branch', 'Jose Rizal', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Brand new tractor for long haul', '2026-08-29 21:21:51', 'ADMIN', '2026-08-30 21:26:29'),
(19, 'TRK-2026-0014', 'TRACTOR', 'TRC-004', 'MV-2026-0014', 'Tractor Head', 'Heavy Duty', 'Isuzu', 'Giga', '2021', 'CHS-2026-0014', 'ENG-2026-0014', 'Diesel', 400.00, 30000.00, 125400.00, '2021-10-20', 'Isuzu Motors', 'Manila Branch', 'Antonio Luna', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For inter-island hauling', '2026-08-29 21:21:51', 'ADMIN', '2026-08-29 21:21:51'),
(20, 'TRK-2026-0015', 'TRACTOR', 'TRC-005', 'MV-2026-0015', 'Tractor Head', 'Medium Duty', 'Hino', '500 Series', '2022', 'CHS-2026-0015', 'ENG-2026-0015', 'Diesel', 320.00, 22000.00, 98700.00, '2022-04-12', 'Hino Motors', 'Pampanga Branch', 'Gregorio Del Pilar', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Medium duty for regional routes', '2026-08-29 21:21:51', 'ADMIN', '2026-08-29 21:21:51'),
(21, 'TRK-2026-0016', 'TRACTOR', 'TRC-006', 'MV-2026-0016', 'Tractor Head', 'Heavy Duty', 'Fuso', 'Super Great', '2023', 'CHS-2026-0016', 'ENG-2026-0016', 'Diesel', 420.00, 32000.00, 34500.00, '2023-06-01', 'Mitsubishi Motors', 'Cebu Branch', 'Diego Silang', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For heavy cargo transport', '2026-08-29 21:21:51', 'ADMIN', '2026-08-29 21:21:51'),
(22, 'TRK-2026-0017', 'TRACTOR', 'TRC-007', 'MV-2026-0017', 'Tractor Head', 'Heavy Duty', 'Isuzu', 'Giga', '2020', 'CHS-2026-0017', 'ENG-2026-0017', 'Diesel', 380.00, 28000.00, 167800.00, '2020-08-15', 'Isuzu Motors', 'Manila Branch', 'Gabriela Silang', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For container yard operations', '2026-08-29 21:21:51', 'ADMIN', '2026-08-29 21:21:51'),
(23, 'TRK-2026-0018', 'TRACTOR', 'TRC-008', 'MV-2026-0018', 'Tractor Head', 'Medium Duty', 'Hino', '700 Series', '2024', 'CHS-2026-0018', 'ENG-2026-0018', 'Diesel', 340.00, 24000.00, 5600.00, '2024-02-14', 'Hino Motors', 'Davao Branch', 'Andres Bonifacio', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Brand new medium duty', '2026-08-29 21:21:51', 'ADMIN', '2026-08-29 21:21:51'),
(24, 'TRK-2026-0019', 'TRACTOR', 'TRC-009', 'MV-2026-0019', 'Tractor Head', 'Heavy Duty', 'Mitsubishi', 'Super Great', '2022', 'CHS-2026-0019', 'ENG-2026-0019', 'Diesel', 430.00, 33000.00, 65400.00, '2022-09-25', 'Mitsubishi Motors', 'Cebu Branch', 'Emilio Aguinaldo', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For long distance hauling', '2026-08-29 21:21:51', 'ADMIN', '2026-08-29 21:21:51'),
(25, 'TRK-2026-0020', 'TRACTOR', 'TRC-010', 'MV-2026-0020', 'Tractor Head', 'Heavy Duty', 'Isuzu', 'Giga', '2021', 'CHS-2026-0020', 'ENG-2026-0020', 'Diesel', 390.00, 29000.00, 112300.00, '2021-11-30', 'Isuzu Motors', 'Manila Branch', 'Apolinario Mabini', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For bulk cargo transport', '2026-08-29 21:21:51', 'ADMIN', '2026-08-29 21:21:51'),
(26, 'TRK-2026-0021', 'TRAILER', 'CHS-001', 'MV-2026-0021', 'Trailer', 'Container Chassis', 'Utility', '40ft Container', '2023', 'CHS-2026-0021', NULL, NULL, NULL, 35000.00, 23400.00, '2023-03-20', 'Container Services Inc.', 'Manila Branch', 'Carlos Aquino', 'COMPANY-OWNED', 'AVAILABLE', NULL, '40ft container chassis for heavy loads', '2026-08-29 21:21:59', 'ADMIN', '2026-08-30 20:07:41'),
(27, 'TRK-2026-0022', 'TRAILER', 'CHS-002', 'MV-2026-0022', 'Trailer', 'Flatbed Trailer', 'Utility', '20ft Container', '2022', 'CHS-2026-0022', NULL, NULL, NULL, 25000.00, 45600.00, '2022-08-10', 'Trailer Solutions', 'Cebu Branch', 'Rosa Sevilla', 'COMPANY-OWNED', 'AVAILABLE', NULL, '20ft container chassis for regional delivery', '2026-08-29 21:21:59', 'ADMIN', '2026-08-30 20:22:36'),
(28, 'TRK-2026-0023', 'TRAILER', 'CHS-003', 'MV-2026-0023', 'Trailer', 'Refrigerated Trailer', 'Thermo King', '40ft Reefer', '2024', 'CHS-2026-0023', NULL, NULL, NULL, 28000.00, 5600.00, '2024-01-15', 'Cold Chain Logistics', 'Davao Branch', 'Rafael Palma', 'COMPANY-OWNED', 'AVAILABLE', NULL, '40ft refrigerated trailer for cold chain', '2026-08-29 21:21:59', 'ADMIN', '2026-08-30 21:26:29'),
(29, 'TRK-2026-0024', 'TRAILER', 'CHS-004', 'MV-2026-0024', 'Trailer', 'Drop Deck Trailer', 'Utility', 'Lowboy', '2021', 'CHS-2026-0024', NULL, NULL, NULL, 45000.00, 87600.00, '2021-12-01', 'Heavy Equipment Logistics', 'Manila Branch', 'Cecilio Apostol', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For heavy equipment transport', '2026-08-29 21:21:59', 'ADMIN', '2026-08-29 21:21:59'),
(30, 'TRK-2026-0025', 'TRAILER', 'CHS-005', 'MV-2026-0025', 'Trailer', 'Tanker Trailer', 'Utility', 'Fuel Tanker', '2022', 'CHS-2026-0025', NULL, NULL, NULL, 40000.00, 54300.00, '2022-05-20', 'Fuel Logistics Corp', 'Pampanga Branch', 'Jose Garcia Villa', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For fuel and liquid transport', '2026-08-29 21:21:59', 'ADMIN', '2026-08-29 21:21:59'),
(31, 'TRK-2026-0026', 'TRAILER', 'CHS-006', 'MV-2026-0026', 'Trailer', 'Dry Van Trailer', 'Utility', '53ft Dry Van', '2023', 'CHS-2026-0026', NULL, NULL, NULL, 32000.00, 23400.00, '2023-07-01', 'Van Logistics Inc.', 'Cebu Branch', 'Fernando Amorsolo', 'COMPANY-OWNED', 'AVAILABLE', NULL, '53ft dry van for general cargo', '2026-08-29 21:21:59', 'ADMIN', '2026-08-29 21:21:59'),
(32, 'TRK-2026-0027', 'TRAILER', 'CHS-007', 'MV-2026-0027', 'Trailer', 'Open Deck Trailer', 'Utility', 'Open Flatbed', '2020', 'CHS-2026-0027', NULL, NULL, NULL, 30000.00, 123400.00, '2020-09-15', 'Deck Logistics', 'Manila Branch', 'Juan Nakpil', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For open cargo transport', '2026-08-29 21:21:59', 'ADMIN', '2026-08-29 21:21:59'),
(33, 'TRK-2026-0028', 'TRAILER', 'CHS-008', 'MV-2026-0028', 'Trailer', 'Side Curtain Trailer', 'Utility', 'Curtain Sider', '2024', 'CHS-2026-0028', NULL, NULL, NULL, 30000.00, 7800.00, '2024-02-28', 'Curtain Logistics', 'Davao Branch', 'Lorenzo Ruiz', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'Curtain side trailer for easy loading', '2026-08-29 21:21:59', 'ADMIN', '2026-08-29 21:21:59'),
(34, 'TRK-2026-0029', 'TRAILER', 'CHS-009', 'MV-2026-0029', 'Trailer', 'Car Carrier', 'Utility', 'Auto Transport', '2022', 'CHS-2026-0029', NULL, NULL, NULL, 25000.00, 65400.00, '2022-10-10', 'Auto Logistics Inc.', 'Cebu Branch', 'Narcisa De Leon', 'COMPANY-OWNED', 'AVAILABLE', NULL, 'For vehicle transport', '2026-08-29 21:21:59', 'ADMIN', '2026-08-29 21:21:59'),
(35, 'TRK-2026-0030', 'TRAILER', 'CHS-010', 'MV-2026-0031', 'Trailer', 'Container Chassis', 'Utility', '45ft Container', '2023', 'CHS-2026-0030', '', '', 0.00, 38000.00, 34500.00, '2023-09-05', 'Container Services Inc.', 'Manila Branch', 'Guillermo Tolentino', 'COMPANY-OWNED', 'AVAILABLE', NULL, '45ft container chassis for heavy loads', '2026-08-29 21:21:59', 'ADMIN', '2026-08-29 22:59:56');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_truck_documents`
--

CREATE TABLE `tbl_truck_documents` (
  `document_id` int(11) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `document_type` enum('REGISTRATION','INSURANCE','SOLIDARITY STICKER','EAGLE STICKER','GENERAL STICKER','FRANCHISE','ACCREDITATION','OTHER') NOT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `document_status` enum('VALID','EXPIRING','EXPIRED') NOT NULL DEFAULT 'VALID',
  `provider_name` varchar(100) DEFAULT NULL,
  `policy_number` varchar(100) DEFAULT NULL,
  `coverage_type` varchar(100) DEFAULT NULL,
  `premium` decimal(15,2) DEFAULT 0.00,
  `coverage_amount` decimal(15,2) DEFAULT 0.00,
  `rate` decimal(15,2) DEFAULT 0.00,
  `document_attachment` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_truck_documents`
--

INSERT INTO `tbl_truck_documents` (`document_id`, `truck_id`, `document_type`, `document_number`, `issue_date`, `expiration_date`, `document_status`, `provider_name`, `policy_number`, `coverage_type`, `premium`, `coverage_amount`, `rate`, `document_attachment`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 1, 'REGISTRATION', 'TEST', '2026-08-28', '2030-10-28', 'VALID', 'TEST', 'TEST', 'Comprehensive', 1.00, 1.00, 1.00, 'uploads/truck_documents/DOC_1_1787910359.pdf', 'TEST', '2026-08-28 17:45:44', 'ADMIN', '2026-08-28 17:45:59'),
(2, 1, 'INSURANCE', 'TEST', '2026-08-28', '2030-03-28', 'VALID', 'test', 'asd', 'Comprehensive', 1.00, 11.00, 1.00, 'uploads/truck_documents/DOC_1_1787983999.pdf', 'TEST', '2026-08-28 17:57:15', 'ADMIN', '2026-08-29 14:13:19'),
(3, 35, 'INSURANCE', 'TEST', '0000-00-00', '0000-00-00', 'VALID', 'ABC INSURANCE', 'PO-1231', 'Comprehensive', 250000.00, 250000.00, 1231.00, 'uploads/truck_documents/DOC_35_1788014877.pdf', 'TEST', '2026-08-29 22:47:57', 'ADMIN', '2026-08-29 22:47:57');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vendors`
--

CREATE TABLE `tbl_vendors` (
  `vendor_id` int(11) NOT NULL,
  `vendor_code` varchar(50) NOT NULL,
  `vendor_name` varchar(200) NOT NULL,
  `vendor_type` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `payment_terms` varchar(50) DEFAULT NULL,
  `vendor_status` enum('ACTIVE','INACTIVE','SUSPENDED','TERMINATED') NOT NULL DEFAULT 'ACTIVE',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_vendors`
--

INSERT INTO `tbl_vendors` (`vendor_id`, `vendor_code`, `vendor_name`, `vendor_type`, `contact_person`, `contact_number`, `email_address`, `address`, `payment_terms`, `vendor_status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'VND-2026-0001', 'ABC Trucking Rentals', 'FLEET PROVIDER', 'Maria Santos', '0917-123-4567', 'maria@abctrucking.com', '123 Logistics Ave., Manila', '30 Days', 'ACTIVE', 'Full-service trucking and logistics provider', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(2, 'VND-2026-0002', 'XYZ Transport Services', 'LOGISTICS', 'Juan Dela Cruz', '0918-234-5678', 'juan@xyztransport.com', '456 Cargo St., Cebu', '15 Days', 'ACTIVE', 'Specializes in refrigerated transport', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(3, 'VND-2026-0003', 'Manila Heavy Equipment Rental', 'EQUIPMENT RENTAL', 'Pedro Reyes', '0919-345-6789', 'pedro@manilahire.com', '789 Makati Ave., Manila', 'COD', 'ACTIVE', 'Heavy equipment and construction rental', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(4, 'VND-2026-0004', 'Davao Logistics Solutions', '3PL PROVIDER', 'Anna Lopez', '0920-456-7890', 'anna@davaologistics.com', '101 Davao St., Davao', '45 Days', 'ACTIVE', 'Third-party logistics provider', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(5, 'VND-2026-0005', 'Isuzu Fleet Services', 'FLEET PROVIDER', 'Ramon Cruz', '0921-567-8901', 'ramon@isuzufleet.com', '202 Pasig Blvd., Pasig', '30 Days', 'ACTIVE', 'Fleet leasing and maintenance services', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(6, 'VND-2026-0006', 'Cebu Transport Co.', 'LOGISTICS', 'Elena Rivera', '0922-678-9012', 'elena@cebutransport.com', '303 Cebu City, Cebu', '60 Days', 'ACTIVE', 'Inter-island transport and cargo forwarding', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(7, 'VND-2026-0007', 'Nationwide Trucking', 'FLEET PROVIDER', 'Mark Tan', '0923-789-0123', 'mark@nationwidetrucking.com', '404 EDSA, Quezon City', '15 Days', 'ACTIVE', 'Nationwide trucking services', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(8, 'VND-2026-0008', 'Pampanga Logistics Hub', '3PL PROVIDER', 'Grace Santos', '0924-890-1234', 'grace@pampangalogistics.com', '505 Clark Freeport, Pampanga', '30 Days', 'ACTIVE', 'Full logistics and warehousing services', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(9, 'VND-2026-0009', 'Visayas Cargo Express', 'LOGISTICS', 'Francisco Garcia', '0925-901-2345', 'francisco@visayascargo.com', '606 Iloilo City, Iloilo', '45 Days', 'ACTIVE', 'Visayas region cargo transport', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(10, 'VND-2026-0010', 'Mindanao Freight Services', 'FLEET PROVIDER', 'Leticia Mendez', '0926-012-3456', 'leticia@mindanaofreight.com', '707 General Santos City, South Cotabato', '30 Days', 'ACTIVE', 'Mindanao freight and logistics', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vendor_services`
--

CREATE TABLE `tbl_vendor_services` (
  `service_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `service_type` enum('TRUCK RENTAL','TRACTOR RENTAL','CHASSIS RENTAL','DRIVER SERVICE','HELPER SERVICE','TRUCK + DRIVER','TRUCK + DRIVER + HELPER','TRACTOR + CHASSIS','TRACTOR + CHASSIS + DRIVER','TRACTOR + CHASSIS + DRIVER + HELPER','OTHER TRANSPORTATION SERVICE') NOT NULL,
  `rate_type` enum('PER TRIP','PER DAY','PER KILOMETER','PER HOUR','MONTHLY','FIXED RATE') NOT NULL,
  `rate_amount` decimal(15,2) DEFAULT 0.00,
  `effective_date` date DEFAULT NULL,
  `service_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_vendor_services`
--

INSERT INTO `tbl_vendor_services` (`service_id`, `vendor_id`, `service_type`, `rate_type`, `rate_amount`, `effective_date`, `service_status`, `remarks`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 1, 'TRUCK + DRIVER + HELPER', 'PER TRIP', 15000.00, '2026-01-01', 'ACTIVE', 'Full package with driver and helper', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(2, 1, 'TRUCK RENTAL', 'PER DAY', 8000.00, '2026-01-01', 'ACTIVE', 'Rigid truck rental without driver', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(3, 1, 'TRACTOR + CHASSIS + DRIVER', 'PER TRIP', 25000.00, '2026-01-01', 'ACTIVE', 'Tractor with chassis and driver', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(4, 2, 'TRUCK + DRIVER', 'PER DAY', 12000.00, '2026-01-15', 'ACTIVE', 'Refrigerated truck with driver', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(5, 2, 'TRACTOR + CHASSIS', 'PER TRIP', 20000.00, '2026-01-15', 'ACTIVE', 'Tractor and chassis combo', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(6, 2, 'DRIVER SERVICE', 'PER DAY', 2500.00, '2026-01-15', 'ACTIVE', 'Driver service only', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(7, 3, 'TRACTOR RENTAL', 'PER DAY', 10000.00, '2026-02-01', 'ACTIVE', 'Tractor head rental', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(8, 3, 'CHASSIS RENTAL', 'PER DAY', 5000.00, '2026-02-01', 'ACTIVE', 'Chassis/trailer rental', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(9, 3, 'TRACTOR + CHASSIS + DRIVER + HELPER', 'PER TRIP', 35000.00, '2026-02-01', 'ACTIVE', 'Complete package with crew', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(10, 4, 'TRUCK RENTAL', 'PER TRIP', 12000.00, '2026-03-01', 'ACTIVE', '10-wheeler truck rental', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(11, 4, 'TRUCK + DRIVER', 'PER KILOMETER', 150.00, '2026-03-01', 'ACTIVE', 'Per kilometer rate', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(12, 4, 'HELPER SERVICE', 'PER DAY', 1500.00, '2026-03-01', 'ACTIVE', 'Helper service only', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(13, 5, 'TRACTOR RENTAL', 'MONTHLY', 150000.00, '2026-04-01', 'ACTIVE', 'Monthly tractor leasing', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(14, 5, 'CHASSIS RENTAL', 'MONTHLY', 80000.00, '2026-04-01', 'ACTIVE', 'Monthly chassis leasing', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(15, 5, 'TRACTOR + CHASSIS + DRIVER', 'PER TRIP', 28000.00, '2026-04-01', 'ACTIVE', 'Complete with driver', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(16, 6, 'TRUCK + DRIVER + HELPER', 'PER DAY', 18000.00, '2026-05-01', 'ACTIVE', 'Inter-island transport package', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(17, 6, 'TRACTOR + CHASSIS', 'PER TRIP', 22000.00, '2026-05-01', 'ACTIVE', 'Tractor and chassis for container hauling', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(18, 6, 'OTHER TRANSPORTATION SERVICE', 'FIXED RATE', 30000.00, '2026-05-01', 'ACTIVE', 'Specialized cargo handling', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(19, 7, 'TRUCK RENTAL', 'PER DAY', 9000.00, '2026-06-01', 'ACTIVE', 'Nationwide truck rental', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(20, 7, 'TRACTOR + CHASSIS + DRIVER + HELPER', 'PER TRIP', 38000.00, '2026-06-01', 'ACTIVE', 'Complete team for long haul', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(21, 7, 'DRIVER SERVICE', 'PER TRIP', 3000.00, '2026-06-01', 'ACTIVE', 'Per trip driver service', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(22, 8, 'TRUCK + DRIVER', 'FIXED RATE', 10000.00, '2026-07-01', 'ACTIVE', 'Fixed rate for local deliveries', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(23, 8, 'TRACTOR + CHASSIS', 'PER KILOMETER', 180.00, '2026-07-01', 'ACTIVE', 'Per kilometer rate for tractor+chassis', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(24, 8, 'HELPER SERVICE', 'PER HOUR', 150.00, '2026-07-01', 'ACTIVE', 'Helper on hourly basis', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(25, 9, 'TRUCK + DRIVER + HELPER', 'PER KILOMETER', 200.00, '2026-08-01', 'ACTIVE', 'Full team per kilometer', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(26, 9, 'CHASSIS RENTAL', 'PER TRIP', 6000.00, '2026-08-01', 'ACTIVE', 'Chassis rental for cargo', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(27, 9, 'TRACTOR + CHASSIS + DRIVER', 'PER DAY', 20000.00, '2026-08-01', 'ACTIVE', 'Tractor chassis with driver daily', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(28, 10, 'TRUCK RENTAL', 'PER TRIP', 14000.00, '2026-09-01', 'ACTIVE', 'Mindanao truck rental', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(29, 10, 'TRACTOR + CHASSIS + DRIVER + HELPER', 'PER DAY', 22000.00, '2026-09-01', 'ACTIVE', 'Complete package daily rate', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53'),
(30, 10, 'DRIVER SERVICE', 'PER KILOMETER', 100.00, '2026-09-01', 'ACTIVE', 'Driver on per kilometer rate', '2026-08-29 21:58:53', 'ADMIN', '2026-08-29 21:58:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `myua_user`
--
ALTER TABLE `myua_user`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `tbl_bay_status`
--
ALTER TABLE `tbl_bay_status`
  ADD PRIMARY KEY (`bay_id`),
  ADD UNIQUE KEY `stage_id` (`stage_id`);

--
-- Indexes for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`);

--
-- Indexes for table `tbl_customer_locations`
--
ALTER TABLE `tbl_customer_locations`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `tbl_delivery_receipts`
--
ALTER TABLE `tbl_delivery_receipts`
  ADD PRIMARY KEY (`dr_id`),
  ADD KEY `idx_dr_code` (`dr_code`),
  ADD KEY `idx_trip_id` (`trip_id`),
  ADD KEY `idx_dispatch_id` (`dispatch_id`),
  ADD KEY `idx_customer_id` (`customer_id`);

--
-- Indexes for table `tbl_delivery_receipt_container_return`
--
ALTER TABLE `tbl_delivery_receipt_container_return`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `idx_dr_id` (`dr_id`);

--
-- Indexes for table `tbl_delivery_receipt_items`
--
ALTER TABLE `tbl_delivery_receipt_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_dr_id` (`dr_id`);

--
-- Indexes for table `tbl_delivery_receipt_pod`
--
ALTER TABLE `tbl_delivery_receipt_pod`
  ADD PRIMARY KEY (`pod_id`),
  ADD KEY `idx_dr_id` (`dr_id`);

--
-- Indexes for table `tbl_dispatch`
--
ALTER TABLE `tbl_dispatch`
  ADD PRIMARY KEY (`dispatch_id`),
  ADD UNIQUE KEY `dispatch_code` (`dispatch_code`),
  ADD KEY `idx_trip_id` (`trip_id`);

--
-- Indexes for table `tbl_dispatch_checklist`
--
ALTER TABLE `tbl_dispatch_checklist`
  ADD PRIMARY KEY (`checklist_id`),
  ADD UNIQUE KEY `checklist_code` (`checklist_code`),
  ADD KEY `idx_dispatch_id` (`dispatch_id`);

--
-- Indexes for table `tbl_dispatch_expenses`
--
ALTER TABLE `tbl_dispatch_expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD UNIQUE KEY `expense_code` (`expense_code`),
  ADD KEY `idx_dispatch_id` (`dispatch_id`);

--
-- Indexes for table `tbl_drivers`
--
ALTER TABLE `tbl_drivers`
  ADD PRIMARY KEY (`driver_id`);

--
-- Indexes for table `tbl_driver_skillsets`
--
ALTER TABLE `tbl_driver_skillsets`
  ADD PRIMARY KEY (`skillset_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `tbl_helpers`
--
ALTER TABLE `tbl_helpers`
  ADD PRIMARY KEY (`helper_id`);

--
-- Indexes for table `tbl_maintenance_parts`
--
ALTER TABLE `tbl_maintenance_parts`
  ADD PRIMARY KEY (`part_id`),
  ADD KEY `idx_record_id` (`record_id`);

--
-- Indexes for table `tbl_maintenance_records`
--
ALTER TABLE `tbl_maintenance_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `idx_record_code` (`record_code`),
  ADD KEY `idx_truck_id` (`truck_id`),
  ADD KEY `idx_schedule_id` (`schedule_id`);

--
-- Indexes for table `tbl_maintenance_schedules`
--
ALTER TABLE `tbl_maintenance_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `idx_schedule_code` (`schedule_code`),
  ADD KEY `idx_truck_id` (`truck_id`);

--
-- Indexes for table `tbl_range_assistants`
--
ALTER TABLE `tbl_range_assistants`
  ADD PRIMARY KEY (`assistant_id`);

--
-- Indexes for table `tbl_supplies`
--
ALTER TABLE `tbl_supplies`
  ADD PRIMARY KEY (`supply_id`),
  ADD UNIQUE KEY `supply_code` (`supply_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `tbl_supply_transactions`
--
ALTER TABLE `tbl_supply_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `idx_supply_id` (`supply_id`),
  ADD KEY `idx_type` (`transaction_type`),
  ADD KEY `idx_date` (`transaction_date`);

--
-- Indexes for table `tbl_tires`
--
ALTER TABLE `tbl_tires`
  ADD PRIMARY KEY (`tire_id`),
  ADD UNIQUE KEY `tire_code` (`tire_code`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `idx_status` (`tire_status`);

--
-- Indexes for table `tbl_tire_disposals`
--
ALTER TABLE `tbl_tire_disposals`
  ADD PRIMARY KEY (`disposal_id`),
  ADD KEY `idx_tire_id` (`tire_id`);

--
-- Indexes for table `tbl_tire_installations`
--
ALTER TABLE `tbl_tire_installations`
  ADD PRIMARY KEY (`installation_id`),
  ADD KEY `idx_tire_id` (`tire_id`),
  ADD KEY `idx_truck_id` (`truck_id`);

--
-- Indexes for table `tbl_tire_transactions`
--
ALTER TABLE `tbl_tire_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `idx_tire_id` (`tire_id`),
  ADD KEY `idx_type` (`transaction_type`);

--
-- Indexes for table `tbl_tools`
--
ALTER TABLE `tbl_tools`
  ADD PRIMARY KEY (`tool_id`),
  ADD UNIQUE KEY `tool_code` (`tool_code`),
  ADD KEY `idx_availability` (`availability`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `tbl_tool_issuances`
--
ALTER TABLE `tbl_tool_issuances`
  ADD PRIMARY KEY (`issuance_id`),
  ADD UNIQUE KEY `issuance_code` (`issuance_code`),
  ADD KEY `idx_tool_id` (`tool_id`),
  ADD KEY `idx_status` (`issuance_status`),
  ADD KEY `idx_issue_date` (`issue_date`);

--
-- Indexes for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `stage_id` (`stage_id`),
  ADD KEY `range_assistant_id` (`range_assistant_id`);

--
-- Indexes for table `tbl_transaction_documents`
--
ALTER TABLE `tbl_transaction_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `tbl_trips`
--
ALTER TABLE `tbl_trips`
  ADD PRIMARY KEY (`trip_id`);

--
-- Indexes for table `tbl_trip_assignments`
--
ALTER TABLE `tbl_trip_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `idx_trip_id` (`trip_id`);

--
-- Indexes for table `tbl_trip_cargo_items`
--
ALTER TABLE `tbl_trip_cargo_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_trip_id` (`trip_id`);

--
-- Indexes for table `tbl_trip_waypoints`
--
ALTER TABLE `tbl_trip_waypoints`
  ADD PRIMARY KEY (`waypoint_id`),
  ADD KEY `idx_trip_id` (`trip_id`);

--
-- Indexes for table `tbl_trucks`
--
ALTER TABLE `tbl_trucks`
  ADD PRIMARY KEY (`truck_id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD UNIQUE KEY `chassis_number` (`chassis_number`);

--
-- Indexes for table `tbl_truck_documents`
--
ALTER TABLE `tbl_truck_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `truck_id` (`truck_id`);

--
-- Indexes for table `tbl_vendors`
--
ALTER TABLE `tbl_vendors`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `tbl_vendor_services`
--
ALTER TABLE `tbl_vendor_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `tbl_vendor_services_ibfk_1` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `myua_user`
--
ALTER TABLE `myua_user`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tbl_bay_status`
--
ALTER TABLE `tbl_bay_status`
  MODIFY `bay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_customer_locations`
--
ALTER TABLE `tbl_customer_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tbl_delivery_receipts`
--
ALTER TABLE `tbl_delivery_receipts`
  MODIFY `dr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_delivery_receipt_container_return`
--
ALTER TABLE `tbl_delivery_receipt_container_return`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_delivery_receipt_items`
--
ALTER TABLE `tbl_delivery_receipt_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_delivery_receipt_pod`
--
ALTER TABLE `tbl_delivery_receipt_pod`
  MODIFY `pod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_dispatch`
--
ALTER TABLE `tbl_dispatch`
  MODIFY `dispatch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_dispatch_checklist`
--
ALTER TABLE `tbl_dispatch_checklist`
  MODIFY `checklist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_dispatch_expenses`
--
ALTER TABLE `tbl_dispatch_expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_drivers`
--
ALTER TABLE `tbl_drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_driver_skillsets`
--
ALTER TABLE `tbl_driver_skillsets`
  MODIFY `skillset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_helpers`
--
ALTER TABLE `tbl_helpers`
  MODIFY `helper_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_maintenance_parts`
--
ALTER TABLE `tbl_maintenance_parts`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tbl_maintenance_records`
--
ALTER TABLE `tbl_maintenance_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_maintenance_schedules`
--
ALTER TABLE `tbl_maintenance_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_range_assistants`
--
ALTER TABLE `tbl_range_assistants`
  MODIFY `assistant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_supplies`
--
ALTER TABLE `tbl_supplies`
  MODIFY `supply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_supply_transactions`
--
ALTER TABLE `tbl_supply_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tbl_tires`
--
ALTER TABLE `tbl_tires`
  MODIFY `tire_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_tire_disposals`
--
ALTER TABLE `tbl_tire_disposals`
  MODIFY `disposal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_tire_installations`
--
ALTER TABLE `tbl_tire_installations`
  MODIFY `installation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_tire_transactions`
--
ALTER TABLE `tbl_tire_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbl_tools`
--
ALTER TABLE `tbl_tools`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_tool_issuances`
--
ALTER TABLE `tbl_tool_issuances`
  MODIFY `issuance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_transaction_documents`
--
ALTER TABLE `tbl_transaction_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tbl_trips`
--
ALTER TABLE `tbl_trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_trip_assignments`
--
ALTER TABLE `tbl_trip_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_trip_cargo_items`
--
ALTER TABLE `tbl_trip_cargo_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_trip_waypoints`
--
ALTER TABLE `tbl_trip_waypoints`
  MODIFY `waypoint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tbl_trucks`
--
ALTER TABLE `tbl_trucks`
  MODIFY `truck_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tbl_truck_documents`
--
ALTER TABLE `tbl_truck_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_vendors`
--
ALTER TABLE `tbl_vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_vendor_services`
--
ALTER TABLE `tbl_vendor_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_customer_locations`
--
ALTER TABLE `tbl_customer_locations`
  ADD CONSTRAINT `tbl_customer_locations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `tbl_customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_driver_skillsets`
--
ALTER TABLE `tbl_driver_skillsets`
  ADD CONSTRAINT `tbl_driver_skillsets_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `tbl_drivers` (`driver_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_transaction_documents`
--
ALTER TABLE `tbl_transaction_documents`
  ADD CONSTRAINT `fk_transaction_documents` FOREIGN KEY (`transaction_id`) REFERENCES `tbl_transactions` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_truck_documents`
--
ALTER TABLE `tbl_truck_documents`
  ADD CONSTRAINT `tbl_truck_documents_ibfk_1` FOREIGN KEY (`truck_id`) REFERENCES `tbl_trucks` (`truck_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_vendor_services`
--
ALTER TABLE `tbl_vendor_services`
  ADD CONSTRAINT `tbl_vendor_services_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `tbl_vendors` (`vendor_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
