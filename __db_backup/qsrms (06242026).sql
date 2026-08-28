-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2026 at 11:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qsrms`
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
(1, 1, 'AVAILABLE', NULL, '2026-06-24 16:20:59'),
(2, 2, 'AVAILABLE', NULL, '2026-06-24 16:46:16'),
(3, 3, 'AVAILABLE', NULL, '2026-06-24 16:20:52'),
(4, 4, 'OCCUPIED', 13, '2026-06-24 17:02:24'),
(5, 5, 'AVAILABLE', NULL, '2026-06-24 16:20:40'),
(6, 6, 'OCCUPIED', 14, '2026-06-24 17:04:26'),
(7, 7, 'AVAILABLE', NULL, NULL),
(8, 8, 'OCCUPIED', 15, '2026-06-24 17:13:01'),
(9, 9, 'RESERVED', NULL, '2026-06-24 13:42:53'),
(10, 10, 'MAINTENANCE', NULL, '2026-06-24 11:20:19');

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
(1, 'PO1 Mark Reyes', '#2345', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 14:57:00'),
(2, 'PO2 Sarah Cruz', '#6789', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(3, 'PO3 John Santos', '#3456', 'Senior Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(4, 'PO1 Mike Tan', '#7890', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(5, 'PO2 Anna Garcia', '#4567', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(6, 'PO3 David Lim', '#8901', 'Range Officer', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 13:04:26'),
(7, 'PO1 HadjiIIII', '#5678', 'Range Assistant', 'ACTIVE', '2026-06-24 10:17:58', '', '2026-06-24 16:25:27');

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
(13, '2026-06-24', '17:02:00', '00:00:00', 4, 'PNP', 'ROMMEL QUISORA', 3, 300.00, 250.00, 700.00, 1250.00, 'ACTIVE', 'TEST', 'admin', '2026-06-24 17:02:24', NULL),
(14, '2026-06-24', '17:03:00', '00:00:00', 6, 'CIVILIAN', 'CARLO DAGDAG', 5, 300.00, 300.00, 1000.00, 1600.00, 'ACTIVE', 'TEST', 'admin', '2026-06-24 17:04:26', NULL),
(15, '2026-06-24', '17:12:00', '00:00:00', 8, 'CIVILIAN', 'LEE ALINO', 4, 300.00, 200.00, 1500.00, 2000.00, 'ACTIVE', 'TEST', 'admin', '2026-06-24 17:13:01', NULL);

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
(25, 11, 'SHOOTER_PHOTO', '11_SHOOTER_PHOTO_1782289166.jpg', 'uploads/documents/11_SHOOTER_PHOTO_1782289166.jpg', '131.71 KB', 'image/jpeg', 'admin', '2026-06-24 16:19:26');

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
-- Indexes for table `tbl_range_assistants`
--
ALTER TABLE `tbl_range_assistants`
  ADD PRIMARY KEY (`assistant_id`);

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
  MODIFY `bay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_range_assistants`
--
ALTER TABLE `tbl_range_assistants`
  MODIFY `assistant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_transaction_documents`
--
ALTER TABLE `tbl_transaction_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_transaction_documents`
--
ALTER TABLE `tbl_transaction_documents`
  ADD CONSTRAINT `fk_transaction_documents` FOREIGN KEY (`transaction_id`) REFERENCES `tbl_transactions` (`transaction_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
