-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2026 at 06:37 AM
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
-- Database: `mjeshter`
--

-- --------------------------------------------------------

--
-- Table structure for table `myctr_adjustment`
--

CREATE TABLE `myctr_adjustment` (
  `CTR_YEAR` varchar(4) DEFAULT '0000',
  `CTR_MONTH` varchar(2) DEFAULT '00',
  `CTR_DAY` varchar(2) DEFAULT '00',
  `CTRL_NO01` varchar(15) DEFAULT '000',
  `CTRL_NO02` varchar(15) DEFAULT '00000000',
  `CTRL_NO03` varchar(15) DEFAULT '00000000',
  `CTRL_NO04` varchar(15) DEFAULT '00000000',
  `CTRL_NO05` varchar(15) DEFAULT '00000000',
  `CTRL_NO06` varchar(15) DEFAULT '00000000',
  `CTRL_NO07` varchar(15) DEFAULT '00000000',
  `CTRL_NO08` varchar(15) DEFAULT '00000000',
  `CTRL_NO09` varchar(15) DEFAULT '00000000',
  `CTRL_NO10` varchar(15) DEFAULT '00000000',
  `CTRL_NO11` varchar(15) DEFAULT '00000000',
  `SS_CTR` varchar(15) DEFAULT '000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `myctr_cashdisbursement`
--

CREATE TABLE `myctr_cashdisbursement` (
  `CTR_YEAR` varchar(4) DEFAULT '0000',
  `CTR_MONTH` varchar(2) DEFAULT '00',
  `CTR_DAY` varchar(2) DEFAULT '00',
  `CTRL_NO01` varchar(15) DEFAULT '000',
  `CTRL_NO02` varchar(15) DEFAULT '00000000',
  `CTRL_NO03` varchar(15) DEFAULT '00000000',
  `CTRL_NO04` varchar(15) DEFAULT '00000000',
  `CTRL_NO05` varchar(15) DEFAULT '00000000',
  `CTRL_NO06` varchar(15) DEFAULT '00000000',
  `CTRL_NO07` varchar(15) DEFAULT '00000000',
  `CTRL_NO08` varchar(15) DEFAULT '00000000',
  `CTRL_NO09` varchar(15) DEFAULT '00000000',
  `CTRL_NO10` varchar(15) DEFAULT '00000000',
  `CTRL_NO11` varchar(15) DEFAULT '00000000',
  `SS_CTR` varchar(15) DEFAULT '000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `myctr_cashreceipt`
--

CREATE TABLE `myctr_cashreceipt` (
  `CTR_YEAR` varchar(4) DEFAULT '0000',
  `CTR_MONTH` varchar(2) DEFAULT '00',
  `CTR_DAY` varchar(2) DEFAULT '00',
  `CTRL_NO01` varchar(15) DEFAULT '000',
  `CTRL_NO02` varchar(15) DEFAULT '00000000',
  `CTRL_NO03` varchar(15) DEFAULT '00000000',
  `CTRL_NO04` varchar(15) DEFAULT '00000000',
  `CTRL_NO05` varchar(15) DEFAULT '00000000',
  `CTRL_NO06` varchar(15) DEFAULT '00000000',
  `CTRL_NO07` varchar(15) DEFAULT '00000000',
  `CTRL_NO08` varchar(15) DEFAULT '00000000',
  `CTRL_NO09` varchar(15) DEFAULT '00000000',
  `CTRL_NO10` varchar(15) DEFAULT '00000000',
  `CTRL_NO11` varchar(15) DEFAULT '00000000',
  `SS_CTR` varchar(15) DEFAULT '000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `myctr_generaljournal`
--

CREATE TABLE `myctr_generaljournal` (
  `CTR_YEAR` varchar(4) DEFAULT '0000',
  `CTR_MONTH` varchar(2) DEFAULT '00',
  `CTR_DAY` varchar(2) DEFAULT '00',
  `CTRL_NO01` varchar(15) DEFAULT '000',
  `CTRL_NO02` varchar(15) DEFAULT '00000000',
  `CTRL_NO03` varchar(15) DEFAULT '00000000',
  `CTRL_NO04` varchar(15) DEFAULT '00000000',
  `CTRL_NO05` varchar(15) DEFAULT '00000000',
  `CTRL_NO06` varchar(15) DEFAULT '00000000',
  `CTRL_NO07` varchar(15) DEFAULT '00000000',
  `CTRL_NO08` varchar(15) DEFAULT '00000000',
  `CTRL_NO09` varchar(15) DEFAULT '00000000',
  `CTRL_NO10` varchar(15) DEFAULT '00000000',
  `CTRL_NO11` varchar(15) DEFAULT '00000000',
  `SS_CTR` varchar(15) DEFAULT '000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `myctr_pos`
--

CREATE TABLE `myctr_pos` (
  `CTR_YEAR` varchar(4) DEFAULT '0000',
  `CTR_MONTH` varchar(2) DEFAULT '00',
  `CTR_DAY` varchar(2) DEFAULT '00',
  `CTRL_NO01` varchar(15) DEFAULT '000',
  `CTRL_NO02` varchar(15) DEFAULT '00000000',
  `CTRL_NO03` varchar(15) DEFAULT '00000000',
  `CTRL_NO04` varchar(15) DEFAULT '00000000',
  `CTRL_NO05` varchar(15) DEFAULT '00000000',
  `CTRL_NO06` varchar(15) DEFAULT '00000000',
  `CTRL_NO07` varchar(15) DEFAULT '00000000',
  `CTRL_NO08` varchar(15) DEFAULT '00000000',
  `CTRL_NO09` varchar(15) DEFAULT '00000000',
  `CTRL_NO10` varchar(15) DEFAULT '00000000',
  `CTRL_NO11` varchar(15) DEFAULT '00000000',
  `SS_CTR` varchar(15) DEFAULT '000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `myctr_stockin`
--

CREATE TABLE `myctr_stockin` (
  `CTR_YEAR` varchar(4) DEFAULT '0000',
  `CTR_MONTH` varchar(2) DEFAULT '00',
  `CTR_DAY` varchar(2) DEFAULT '00',
  `CTRL_NO01` varchar(15) DEFAULT '000',
  `CTRL_NO02` varchar(15) DEFAULT '00000000',
  `CTRL_NO03` varchar(15) DEFAULT '00000000',
  `CTRL_NO04` varchar(15) DEFAULT '00000000',
  `CTRL_NO05` varchar(15) DEFAULT '00000000',
  `CTRL_NO06` varchar(15) DEFAULT '00000000',
  `CTRL_NO07` varchar(15) DEFAULT '00000000',
  `CTRL_NO08` varchar(15) DEFAULT '00000000',
  `CTRL_NO09` varchar(15) DEFAULT '00000000',
  `CTRL_NO10` varchar(15) DEFAULT '00000000',
  `CTRL_NO11` varchar(15) DEFAULT '00000000',
  `SS_CTR` varchar(15) DEFAULT '000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
(6, 'ADMIN', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'Michael & Joy', 'Admin', '', 'Business Owner', 0, 0, '2025-05-16 08:55:10', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cash_disbursement_journal`
--

CREATE TABLE `tbl_cash_disbursement_journal` (
  `journal_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `account_code` varchar(50) NOT NULL,
  `amount` decimal(12,2) DEFAULT 0.00,
  `payee` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cash_receipts_journal`
--

CREATE TABLE `tbl_cash_receipts_journal` (
  `journal_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `account_code` varchar(50) NOT NULL,
  `amount` decimal(12,2) DEFAULT 0.00,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_chart_of_accounts`
--

CREATE TABLE `tbl_chart_of_accounts` (
  `account_id` int(11) NOT NULL,
  `account_code` varchar(50) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_type` enum('ASSET','LIABILITY','EQUITY','REVENUE','EXPENSE') NOT NULL,
  `account_category` varchar(100) DEFAULT NULL,
  `normal_balance` enum('DEBIT','CREDIT') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `parent_account_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_chart_of_accounts`
--

INSERT INTO `tbl_chart_of_accounts` (`account_id`, `account_code`, `account_name`, `account_type`, `account_category`, `normal_balance`, `is_active`, `parent_account_id`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '1010', 'Cash on Hand', 'ASSET', 'Current Assets', 'DEBIT', 1, NULL, 'Physical cash in the business', NULL, '2026-05-16 20:25:40', '2026-05-17 00:09:20'),
(2, '1020', 'Cash in Bank', 'ASSET', 'Current Assets', 'DEBIT', 1, NULL, 'Cash deposited in bank accounts', NULL, '2026-05-16 20:25:40', NULL),
(3, '1030', 'Accounts Receivable', 'ASSET', 'Current Assets', 'DEBIT', 1, NULL, 'Amounts owed by customers', NULL, '2026-05-16 20:25:40', NULL),
(4, '1040', 'Inventory', 'ASSET', 'Current Assets', 'DEBIT', 1, NULL, 'Products available for sale', NULL, '2026-05-16 20:25:40', NULL),
(5, '2010', 'Accounts Payable', 'LIABILITY', 'Current Liabilities', 'CREDIT', 1, NULL, 'Amounts owed to suppliers', NULL, '2026-05-16 20:25:40', NULL),
(6, '2020', 'Unearned Revenue', 'LIABILITY', 'Current Liabilities', 'CREDIT', 1, NULL, 'Prepayments from customers', NULL, '2026-05-16 20:25:40', NULL),
(7, '3010', 'Owner\'s Capital', 'EQUITY', 'Owner\'s Equity', 'CREDIT', 1, NULL, 'Owner investment in business', NULL, '2026-05-16 20:25:40', NULL),
(8, '3020', 'Retained Earnings', 'EQUITY', 'Owner\'s Equity', 'CREDIT', 1, NULL, 'Accumulated net income', NULL, '2026-05-16 20:25:40', NULL),
(9, '4010', 'Membership Fee Income', 'REVENUE', 'Membership Revenue', 'CREDIT', 1, NULL, 'Income from membership fees', NULL, '2026-05-16 20:25:40', NULL),
(10, '4020', 'Retail Sales Income', 'REVENUE', 'Product Revenue', 'CREDIT', 1, NULL, 'Income from product sales', NULL, '2026-05-16 20:25:40', NULL),
(11, '4030', 'Walk-In Income', 'REVENUE', 'Service Revenue', 'CREDIT', 1, NULL, 'Income from walk-in customers', NULL, '2026-05-16 20:25:40', '2026-05-16 22:03:46'),
(12, '4040', 'Crossfit Income', 'REVENUE', 'Service Revenue', 'CREDIT', 1, NULL, 'Income from crossfit sessions', NULL, '2026-05-16 20:25:40', NULL),
(13, '4050', 'Yoga Income', 'REVENUE', 'Service Revenue', 'CREDIT', 1, NULL, 'Income from personal training', NULL, '2026-05-16 20:25:40', '2026-06-06 23:26:55'),
(14, '4060', 'Zumba Income', 'REVENUE', 'Other Revenue', 'CREDIT', 1, NULL, 'Miscellaneous income', NULL, '2026-05-16 20:25:40', '2026-06-06 23:27:34'),
(15, '5010', 'Cost of Goods Sold', 'EXPENSE', 'Cost of Sales', 'DEBIT', 1, NULL, 'Direct cost of products sold', NULL, '2026-05-16 20:25:40', NULL),
(16, '5020', 'Rent Expense', 'EXPENSE', 'Operating Expenses', 'DEBIT', 1, NULL, 'Monthly rent payment', NULL, '2026-05-16 20:25:40', NULL),
(17, '5030', 'Utilities Expense', 'EXPENSE', 'Operating Expenses', 'DEBIT', 1, NULL, 'Electricity, water, internet', NULL, '2026-05-16 20:25:40', NULL),
(18, '5040', 'Salaries Expense', 'EXPENSE', 'Operating Expenses', 'DEBIT', 1, NULL, 'Employee wages and salaries', NULL, '2026-05-16 20:25:40', NULL),
(19, '5050', 'Supplies Expense', 'EXPENSE', 'Operating Expenses', 'DEBIT', 1, NULL, 'Office and gym supplies', NULL, '2026-05-16 20:25:40', NULL),
(20, '5060', 'Maintenance Expense', 'EXPENSE', 'Operating Expenses', 'DEBIT', 1, NULL, 'Equipment maintenance', NULL, '2026-05-16 20:25:40', NULL),
(32, '4070', 'PT Income', 'REVENUE', 'Service Revenue', 'CREDIT', 1, NULL, '', 'ADMIN', '2026-06-06 23:28:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_checkin_history`
--

CREATE TABLE `tbl_checkin_history` (
  `checkin_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `rfid_uid` varchar(50) DEFAULT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `checkin_method` enum('RFID','Manual','QR Code','App','Admin') DEFAULT 'RFID',
  `checked_in_by` varchar(100) DEFAULT NULL,
  `checked_out_by` varchar(100) DEFAULT NULL,
  `status` enum('Active','Completed','Forced') DEFAULT 'Active',
  `device_info` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `walkin_name` varchar(150) DEFAULT NULL,
  `walkin_contact` varchar(50) DEFAULT NULL,
  `is_walkin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_crossfit_checkin_history`
--

CREATE TABLE `tbl_crossfit_checkin_history` (
  `checkin_id` int(25) NOT NULL,
  `crossfit_name` varchar(50) NOT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_general_journal`
--

CREATE TABLE `tbl_general_journal` (
  `journal_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `account_code` varchar(50) NOT NULL,
  `amount` decimal(12,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gym_assets`
--

CREATE TABLE `tbl_gym_assets` (
  `asset_id` int(11) NOT NULL,
  `asset_name` varchar(150) NOT NULL,
  `asset_category` varchar(100) NOT NULL,
  `acquisition_cost` decimal(12,2) DEFAULT 0.00,
  `useful_life_months` int(11) DEFAULT 0,
  `monthly_depreciation` decimal(12,2) DEFAULT 0.00,
  `status` enum('ACTIVE','DISPOSED') DEFAULT 'ACTIVE',
  `notes` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inventory_movements`
--

CREATE TABLE `tbl_inventory_movements` (
  `movement_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `movement_type` enum('IN','OUT','ADJUSTMENT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_members`
--

CREATE TABLE `tbl_members` (
  `member_id` int(11) NOT NULL,
  `member_no` varchar(50) NOT NULL,
  `rfid_uid` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `contact_number` varchar(30) NOT NULL,
  `age` int(3) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `emergency_contact_relationship` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `health_conditions` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `fitness_goals` text DEFAULT NULL,
  `experience_level` enum('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `hash_password` varchar(255) DEFAULT NULL,
  `referred_by` varchar(100) DEFAULT NULL,
  `how_did_you_hear` varchar(100) DEFAULT NULL,
  `waiver_signed` tinyint(1) DEFAULT 0,
  `terms_accepted` tinyint(1) DEFAULT 0,
  `total_checkins` int(11) DEFAULT 0,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_loggedin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_memberships`
--

CREATE TABLE `tbl_memberships` (
  `recid` int(25) NOT NULL,
  `member_id` int(25) NOT NULL,
  `membership_plan` varchar(50) NOT NULL,
  `membership_start_date` date NOT NULL,
  `membership_end_date` date NOT NULL,
  `membership_status` varchar(25) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_member_progress_images`
--

CREATE TABLE `tbl_member_progress_images` (
  `progress_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `quarter` varchar(20) NOT NULL,
  `year` int(4) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `notes` text DEFAULT NULL,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pos_dt`
--

CREATE TABLE `tbl_pos_dt` (
  `recid` int(25) NOT NULL,
  `postrxno` varchar(100) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_type` varchar(20) NOT NULL,
  `item_paytype` varchar(50) NOT NULL,
  `item_qty` int(25) NOT NULL,
  `item_amount` decimal(15,2) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pos_payment`
--

CREATE TABLE `tbl_pos_payment` (
  `recid` int(25) NOT NULL,
  `postrxno` varchar(100) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount_tendered` decimal(25,2) NOT NULL,
  `change_amount` decimal(15,2) NOT NULL,
  `grand_total` decimal(25,2) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_products`
--

CREATE TABLE `tbl_products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL,
  `stock_qty` int(11) DEFAULT 0,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `reorder_level` int(1) NOT NULL DEFAULT 5,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pt_checkin_history`
--

CREATE TABLE `tbl_pt_checkin_history` (
  `checkin_id` int(25) NOT NULL,
  `pt_name` varchar(50) NOT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_stock_adjustments`
--

CREATE TABLE `tbl_stock_adjustments` (
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `adjustment_type` enum('INCREASE','DECREASE') DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_stock_in`
--

CREATE TABLE `tbl_stock_in` (
  `stockin_id` int(11) NOT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(150) DEFAULT NULL,
  `stockin_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_stock_in_items`
--

CREATE TABLE `tbl_stock_in_items` (
  `id` int(11) NOT NULL,
  `stockin_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_walkin_checkin_history`
--

CREATE TABLE `tbl_walkin_checkin_history` (
  `checkin_id` int(25) NOT NULL,
  `walkin_name` varchar(50) NOT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_yoga_checkin_history`
--

CREATE TABLE `tbl_yoga_checkin_history` (
  `checkin_id` int(25) NOT NULL,
  `yoga_name` varchar(50) NOT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_zumba_checkin_history`
--

CREATE TABLE `tbl_zumba_checkin_history` (
  `checkin_id` int(25) NOT NULL,
  `zumba_name` varchar(50) NOT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `myctr_adjustment`
--
ALTER TABLE `myctr_adjustment`
  ADD UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`);

--
-- Indexes for table `myctr_cashdisbursement`
--
ALTER TABLE `myctr_cashdisbursement`
  ADD UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`);

--
-- Indexes for table `myctr_cashreceipt`
--
ALTER TABLE `myctr_cashreceipt`
  ADD UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`);

--
-- Indexes for table `myctr_generaljournal`
--
ALTER TABLE `myctr_generaljournal`
  ADD UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`);

--
-- Indexes for table `myctr_pos`
--
ALTER TABLE `myctr_pos`
  ADD UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`);

--
-- Indexes for table `myctr_stockin`
--
ALTER TABLE `myctr_stockin`
  ADD UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`);

--
-- Indexes for table `myua_user`
--
ALTER TABLE `myua_user`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `tbl_cash_disbursement_journal`
--
ALTER TABLE `tbl_cash_disbursement_journal`
  ADD PRIMARY KEY (`journal_id`);

--
-- Indexes for table `tbl_cash_receipts_journal`
--
ALTER TABLE `tbl_cash_receipts_journal`
  ADD PRIMARY KEY (`journal_id`);

--
-- Indexes for table `tbl_chart_of_accounts`
--
ALTER TABLE `tbl_chart_of_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_code` (`account_code`);

--
-- Indexes for table `tbl_checkin_history`
--
ALTER TABLE `tbl_checkin_history`
  ADD PRIMARY KEY (`checkin_id`),
  ADD KEY `idx_member_id` (`member_id`),
  ADD KEY `idx_checkin_time` (`checkin_time`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rfid_uid` (`rfid_uid`),
  ADD KEY `idx_checkin_date_range` (`checkin_time`,`status`),
  ADD KEY `idx_member_stats` (`member_id`,`checkin_time`,`duration_minutes`);

--
-- Indexes for table `tbl_crossfit_checkin_history`
--
ALTER TABLE `tbl_crossfit_checkin_history`
  ADD PRIMARY KEY (`checkin_id`);

--
-- Indexes for table `tbl_general_journal`
--
ALTER TABLE `tbl_general_journal`
  ADD PRIMARY KEY (`journal_id`);

--
-- Indexes for table `tbl_gym_assets`
--
ALTER TABLE `tbl_gym_assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `tbl_inventory_movements`
--
ALTER TABLE `tbl_inventory_movements`
  ADD PRIMARY KEY (`movement_id`);

--
-- Indexes for table `tbl_members`
--
ALTER TABLE `tbl_members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `tbl_memberships`
--
ALTER TABLE `tbl_memberships`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `tbl_member_progress_images`
--
ALTER TABLE `tbl_member_progress_images`
  ADD PRIMARY KEY (`progress_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `tbl_pos_dt`
--
ALTER TABLE `tbl_pos_dt`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `tbl_pos_payment`
--
ALTER TABLE `tbl_pos_payment`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `tbl_pt_checkin_history`
--
ALTER TABLE `tbl_pt_checkin_history`
  ADD PRIMARY KEY (`checkin_id`);

--
-- Indexes for table `tbl_stock_adjustments`
--
ALTER TABLE `tbl_stock_adjustments`
  ADD PRIMARY KEY (`adjustment_id`);

--
-- Indexes for table `tbl_stock_in`
--
ALTER TABLE `tbl_stock_in`
  ADD PRIMARY KEY (`stockin_id`);

--
-- Indexes for table `tbl_stock_in_items`
--
ALTER TABLE `tbl_stock_in_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_walkin_checkin_history`
--
ALTER TABLE `tbl_walkin_checkin_history`
  ADD PRIMARY KEY (`checkin_id`);

--
-- Indexes for table `tbl_yoga_checkin_history`
--
ALTER TABLE `tbl_yoga_checkin_history`
  ADD PRIMARY KEY (`checkin_id`);

--
-- Indexes for table `tbl_zumba_checkin_history`
--
ALTER TABLE `tbl_zumba_checkin_history`
  ADD PRIMARY KEY (`checkin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `myua_user`
--
ALTER TABLE `myua_user`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tbl_cash_disbursement_journal`
--
ALTER TABLE `tbl_cash_disbursement_journal`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `tbl_cash_receipts_journal`
--
ALTER TABLE `tbl_cash_receipts_journal`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `tbl_chart_of_accounts`
--
ALTER TABLE `tbl_chart_of_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tbl_checkin_history`
--
ALTER TABLE `tbl_checkin_history`
  MODIFY `checkin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `tbl_crossfit_checkin_history`
--
ALTER TABLE `tbl_crossfit_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_general_journal`
--
ALTER TABLE `tbl_general_journal`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_gym_assets`
--
ALTER TABLE `tbl_gym_assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tbl_inventory_movements`
--
ALTER TABLE `tbl_inventory_movements`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `tbl_members`
--
ALTER TABLE `tbl_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tbl_memberships`
--
ALTER TABLE `tbl_memberships`
  MODIFY `recid` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_member_progress_images`
--
ALTER TABLE `tbl_member_progress_images`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_pos_dt`
--
ALTER TABLE `tbl_pos_dt`
  MODIFY `recid` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `tbl_pos_payment`
--
ALTER TABLE `tbl_pos_payment`
  MODIFY `recid` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tbl_pt_checkin_history`
--
ALTER TABLE `tbl_pt_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_stock_adjustments`
--
ALTER TABLE `tbl_stock_adjustments`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_stock_in`
--
ALTER TABLE `tbl_stock_in`
  MODIFY `stockin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_stock_in_items`
--
ALTER TABLE `tbl_stock_in_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_walkin_checkin_history`
--
ALTER TABLE `tbl_walkin_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tbl_yoga_checkin_history`
--
ALTER TABLE `tbl_yoga_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_zumba_checkin_history`
--
ALTER TABLE `tbl_zumba_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_checkin_history`
--
ALTER TABLE `tbl_checkin_history`
  ADD CONSTRAINT `fk_checkin_member` FOREIGN KEY (`member_id`) REFERENCES `tbl_members` (`member_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
