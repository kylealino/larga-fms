-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 12:18 PM
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
-- Database: `mjeshter`
--

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

--
-- Dumping data for table `myctr_pos`
--

INSERT INTO `myctr_pos` (`CTR_YEAR`, `CTR_MONTH`, `CTR_DAY`, `CTRL_NO01`, `CTRL_NO02`, `CTRL_NO03`, `CTRL_NO04`, `CTRL_NO05`, `CTRL_NO06`, `CTRL_NO07`, `CTRL_NO08`, `CTRL_NO09`, `CTRL_NO10`, `CTRL_NO11`, `SS_CTR`) VALUES
('2026', '04', '30', '010', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000');

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
(1, 'FAD-ROMANA', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ROMANA L. LLAMAS', 'FAD', 'BUDGET SECTION', 'ADMINISTRATIVE OFFICER V', 2, 0, '2025-04-23 07:21:50', 'admin', 1),
(6, 'ADMIN-KYLE', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'KYLE ANDRAE ALINO', 'FAD', 'CDS', 'PROJECT TECHNICAL SPECIALIST I', 0, 0, '2025-05-16 08:55:10', 'admin', 1),
(7, 'ADMIN-JOVY', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'JOVY S. MEDINA', 'FAD', 'CDS', 'PROJECT ADMINISTRATIVE OFFICER IV', 0, 0, '2025-05-16 09:30:39', 'admin', 1),
(8, 'NFRDD-ROSEMARIE', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ROSEMARIE J. DUMAG, RCh., MSc.', 'NFRDD', 'OFFICE OF THE DIVISION CHIEF', 'Chief SRS', 1, 1, '2025-07-14 13:44:32', 'admin', 1),
(9, 'BS-MILDRED', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MILDRED D. VILLANUEVA', 'FAD', '-', 'ADMINISTRATIVE OFFICER IV', 0, 0, '2025-07-14 13:58:09', 'admin', 1),
(10, 'BS-ROSEFIL', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ROSEFIL J. MALINAO', 'FAD', 'BUDGET SECTION', '-', 0, 0, '2025-07-14 13:58:38', 'admin', 1),
(11, 'FAD-ALEXIS', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ALEXIS M. ORTIZ', 'FAD', '-', 'CHIEF ADMINISTRATIVE OFFICER', 1, 1, '2025-07-14 15:23:50', 'CDS-KYLE', 1),
(12, 'OD-LUCIEDEN', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ATTY. LUCIEDEN G. RAZ', 'Office of the Director', '-', 'Director III & Officer-in-charge', 1, 0, '2025-08-06 09:46:06', 'CDS-KYLE', 1),
(13, 'FAD-ALEXIS', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ALEXIS M. ORTIZ', 'FAD', '-', 'Chief Administrative Officer', 0, 0, '2025-08-06 09:46:49', 'CDS-KYLE', 1),
(14, 'TDSTSD-MILFLOR', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MILFLOR S. GONZALES, Ph.D', 'TDSTSD', '-', 'Chief SRS', 1, 0, '2025-08-06 09:47:48', 'CDS-KYLE', 1),
(15, 'PO-DIVORAH', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'DIVORAH V. AGUILA', '-', '-', 'Planning Officer IV', 1, 1, '2025-08-06 09:49:41', 'CDS-KYLE', 1),
(16, 'SLG-LEAH', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'LEAH C. DAJAY', 'SLG', '-', 'Supervising SRS', 1, 1, '2025-08-06 09:50:14', 'CDS-KYLE', 1),
(17, 'NAMD-LILIBETH', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MA. LILIBETH P. DASCO', 'NAMD', '-', 'Supervising SRS', 1, 0, '2025-08-12 10:17:07', 'CDS-KYLE', 1),
(18, 'PPT-USER', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'GENERIC PROPERTY USER', 'PROPERTY', 'PROPERTY', 'PROPERTY USER', 0, 0, '2025-10-09 10:06:04', 'ADMIN-KYLE', 1),
(19, 'NAMD-MILDRED', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MILDRED O. GUIRINDOLA, Ph. D.', 'NAMD', '-', 'Chief SRS', 1, 1, '2025-10-21 16:18:42', 'ADMIN-KYLE', 1),
(20, 'BS-ANN', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MARY ANN DELA RAMA', 'FAD', 'BUDGET SECTION', 'Project Admin Assistant I', 0, 0, '2025-12-02 10:38:43', 'ADMIN-KYLE', 1),
(21, 'CDS-OJT', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'OJT ACCOUNT - PIA', 'FAD', 'CDS', 'OJT', 0, 0, '2025-12-03 15:15:23', 'ADMIN-KYLE', 1),
(22, 'TDSTSD-SALVADOR', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'SALVADOR R. SERRANO', 'TDSTSD', '-', 'Supervising SRS & Officer-in-charge', 1, 1, '2025-12-11 10:12:16', 'ADMIN-KYLE', 1),
(23, 'FAD-JESTER', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'JESTER C. VIRIÑA', 'FAD', '-', 'SUPERVISING AO, FAD/SAU', 1, 0, '2026-01-28 09:35:05', 'ADMIN-KYLE', 1),
(24, 'NAMD-EVA', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'EVA A. GOYENA', 'NAMD', '-', 'SUPERVISING SRS, NIEPS & SCIENTIST I', 1, 0, '2026-01-28 09:35:38', 'ADMIN-KYLE', 1),
(25, 'NAMD-LILIBETH', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MA. LILIBETH P. DASCO', 'NAMD', '-', 'SUPERVISING SRS, NAS', 1, 0, '2026-01-28 09:36:05', 'ADMIN-KYLE', 1),
(26, 'NAMD-GLEN', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'GLEN MELVIN P. GIRONELLA', 'NAMD', '-', 'SUPERVISING SRS, NSIS', 1, 0, '2026-01-28 09:36:26', 'ADMIN-KYLE', 1),
(27, 'NAMD-MAE', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MAE ANN S.A. JAVIER', 'NAMD', '-', 'SENIOR SRS, NSIS', 1, 0, '2026-01-28 09:37:27', 'ADMIN-KYLE', 1),
(28, 'NAMD-STEPHANI ', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MA. STEPHANI N. PARANI', 'NAMD', '-', 'SENIOR SRS, NAS', 1, 0, '2026-01-28 09:37:48', 'ADMIN-KYLE', 1),
(29, 'NAMD-MAYLENE', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'MAYLENE P. CAJUCOM', 'NAMD', '-', 'SRS II, NAS', 1, 0, '2026-01-28 09:38:08', 'ADMIN-KYLE', 1),
(30, 'NAMD-JEMN', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'JEMN D. SERRANO', 'NAMD', '-', 'SRS II, NAS', 1, 0, '2026-01-28 09:38:36', 'ADMIN-KYLE', 1),
(31, 'NAMD-RICA', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'RICAMAE V. LARRAZABAL', 'NAMD', '-', 'SRS II, NSIS', 1, 0, '2026-01-28 09:38:55', 'ADMIN-KYLE', 1),
(32, 'NAMD-ROWENA', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ROWENA VIAJAR', 'NAMD', '-', 'SRS II, NIEPS', 1, 0, '2026-01-28 09:39:14', 'ADMIN-KYLE', 1),
(33, 'NAMD-EMILY', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'EMILY O. RONGAVILLA', 'NAMD', '-', 'SRS II, NIEPS', 1, 0, '2026-01-28 09:39:36', 'ADMIN-KYLE', 1),
(34, 'NAMD-ROD', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'ROD PAULO B. LORENZO', 'NAMD', '-', 'SRS I, NAS', 1, 0, '2026-01-28 09:40:01', 'ADMIN-KYLE', 1),
(35, 'NAMD-CHEDER', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '123456', 'CHEDER D. SUMANGUE', 'NAMD', '-', 'SRS I, NSIS', 1, 0, '2026-01-28 09:40:18', 'ADMIN-KYLE', 1);

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

--
-- Dumping data for table `tbl_checkin_history`
--

INSERT INTO `tbl_checkin_history` (`checkin_id`, `member_id`, `rfid_uid`, `checkin_time`, `checkout_time`, `duration_minutes`, `checkin_method`, `checked_in_by`, `checked_out_by`, `status`, `device_info`, `ip_address`, `notes`, `created_at`, `walkin_name`, `walkin_contact`, `is_walkin`) VALUES
(1, 1, '0007717262', '2026-04-25 01:18:41', '2026-04-25 01:20:29', 1, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 17:18:41', NULL, NULL, 0),
(2, 1, '0007717262', '2026-04-25 01:24:10', '2026-04-25 01:24:44', 0, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 17:24:10', NULL, NULL, 0),
(3, 1, '0007717262', '2026-04-25 01:26:35', '2026-04-25 01:27:28', 0, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 17:26:35', NULL, NULL, 0),
(4, 1, '0007717262', '2026-04-25 01:30:01', '2026-04-25 01:31:06', 1, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 17:30:01', NULL, NULL, 0),
(5, 1, '0007717262', '2026-04-25 01:31:11', '2026-04-25 01:36:57', 5, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 17:31:11', NULL, NULL, 0),
(6, 1, '0007717262', '2026-04-25 01:37:39', '2026-04-25 02:34:41', 57, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 17:37:39', NULL, NULL, 0),
(7, 7, '0007514936', '2026-04-25 01:55:08', '2026-04-25 02:02:11', 7, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 17:55:08', NULL, NULL, 0),
(8, 7, '0007514936', '2026-04-25 02:03:48', '2026-04-25 02:04:06', 0, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 18:03:48', NULL, NULL, 0),
(9, 7, '0007514936', '2026-04-25 02:04:08', '2026-04-25 02:04:33', 0, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 18:04:08', NULL, NULL, 0),
(10, 7, '0007514936', '2026-04-25 02:19:09', '2026-04-25 02:20:05', 0, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 18:19:09', NULL, NULL, 0),
(11, 7, '0007514936', '2026-04-25 02:20:10', '2026-04-25 02:20:28', 0, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 18:20:10', NULL, NULL, 0),
(12, 7, '0007514936', '2026-04-25 02:28:47', '2026-04-25 02:31:22', 2, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 18:28:47', NULL, NULL, 0),
(13, 7, '0007514936', '2026-04-25 02:31:28', '2026-04-25 12:16:22', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 18:31:28', NULL, NULL, 0),
(14, 1, '0007717262', '2026-04-25 02:38:21', '2026-04-25 02:38:29', 0, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-24 18:38:21', NULL, NULL, 0),
(15, 1, '0007717262', '2026-04-25 02:38:35', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-04-24 18:38:35', NULL, NULL, 0),
(16, 1, '0007717262', '2026-04-25 02:50:37', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-04-24 18:50:37', NULL, NULL, 0),
(17, 8, '0007519496', '2026-04-25 03:07:52', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-04-24 19:07:52', NULL, NULL, 0),
(18, 7, '0007514936', '2026-04-25 12:16:33', '2026-04-25 12:16:59', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:16:33', NULL, NULL, 0),
(19, 7, '0007514936', '2026-04-25 12:19:44', '2026-04-25 12:19:54', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:19:44', NULL, NULL, 0),
(20, 7, '0007514936', '2026-04-25 12:20:41', '2026-04-25 12:20:49', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:20:41', NULL, NULL, 0),
(21, 7, '0007514936', '2026-04-25 12:22:35', '2026-04-25 12:22:42', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:22:35', NULL, NULL, 0),
(22, 7, '0007514936', '2026-04-25 12:25:02', '2026-04-25 12:25:09', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:25:02', NULL, NULL, 0),
(23, 7, '0007514936', '2026-04-25 12:26:19', '2026-04-25 12:26:24', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:26:19', NULL, NULL, 0),
(24, 7, '0007514936', '2026-04-25 12:27:11', '2026-04-25 12:27:33', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:27:11', NULL, NULL, 0),
(25, 7, '0007514936', '2026-04-25 12:28:12', '2026-04-25 12:30:45', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:28:12', NULL, NULL, 0),
(26, 7, '0007514936', '2026-04-25 12:35:29', '2026-04-25 12:35:38', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:35:29', NULL, NULL, 0),
(27, 7, '0007514936', '2026-04-25 12:38:10', '2026-04-25 12:38:17', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:38:10', NULL, NULL, 0),
(28, 7, '0007514936', '2026-04-25 12:41:33', '2026-04-25 12:41:41', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:41:33', NULL, NULL, 0),
(29, 7, '0007514936', '2026-04-25 12:44:53', '2026-04-25 12:46:35', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:44:53', NULL, NULL, 0),
(30, 7, '0007514936', '2026-04-25 12:46:38', '2026-04-25 12:46:39', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:46:38', NULL, NULL, 0),
(31, 7, '0007514936', '2026-04-25 12:47:47', '2026-04-25 12:47:54', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:47:47', NULL, NULL, 0),
(32, 7, '0007514936', '2026-04-25 12:50:19', '2026-04-25 12:50:25', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 04:50:19', NULL, NULL, 0),
(33, 7, '0007514936', '2026-04-25 13:38:22', '2026-04-25 13:39:11', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 05:38:22', NULL, NULL, 0),
(34, 7, '0007514936', '2026-04-25 13:40:19', '2026-04-25 13:40:24', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 05:40:19', NULL, NULL, 0),
(35, 7, '0007514936', '2026-04-25 20:40:34', '2026-04-25 20:40:48', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 12:40:34', NULL, NULL, 0),
(36, 7, '0007514936', '2026-04-25 20:47:04', '2026-04-25 20:47:13', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 12:47:04', NULL, NULL, 0),
(37, 7, '0007514936', '2026-04-25 20:47:43', '2026-04-25 20:47:49', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 12:47:43', NULL, NULL, 0),
(38, 7, '0007514936', '2026-04-25 21:16:28', '2026-04-25 21:16:44', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 13:16:28', NULL, NULL, 0),
(39, 1, '0007717262', '2026-04-25 21:16:40', '2026-04-25 21:16:50', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 13:16:40', NULL, NULL, 0),
(40, 1, '0007717262', '2026-04-25 21:16:56', '2026-04-25 21:38:05', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 13:16:56', NULL, NULL, 0),
(41, 7, '0007514936', '2026-04-25 21:25:36', '2026-04-25 21:37:26', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 13:25:36', NULL, NULL, 0),
(42, 1, '0007717262', '2026-04-25 21:38:19', '2026-04-27 21:22:48', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 13:38:19', NULL, NULL, 0),
(43, 7, '0007514936', '2026-04-25 21:42:39', '2026-04-25 22:04:59', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 13:42:39', NULL, NULL, 0),
(44, 7, '0007514936', '2026-04-25 22:05:05', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-04-25 14:05:05', NULL, NULL, 0),
(45, 1, '0007717262', '2026-04-27 21:22:55', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-04-27 13:22:55', NULL, NULL, 0);

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
  `membership_plan` enum('Basic','Premium','VIP') DEFAULT 'Basic',
  `membership_start_date` date DEFAULT NULL,
  `membership_end_date` date DEFAULT NULL,
  `membership_status` enum('Active','Expired','Suspended','Pending') DEFAULT 'Pending',
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

--
-- Dumping data for table `tbl_members`
--

INSERT INTO `tbl_members` (`member_id`, `member_no`, `rfid_uid`, `first_name`, `last_name`, `middle_name`, `gender`, `date_of_birth`, `contact_number`, `age`, `email`, `mobile_number`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_relationship`, `address`, `city`, `health_conditions`, `allergies`, `fitness_goals`, `experience_level`, `membership_plan`, `membership_start_date`, `membership_end_date`, `membership_status`, `username`, `password`, `hash_password`, `referred_by`, `how_did_you_hear`, `waiver_signed`, `terms_accepted`, `total_checkins`, `created_by`, `created_at`, `updated_at`, `is_loggedin`) VALUES
(1, '01239123', '0007717262', 'KYLE ANDRAE', 'ALINO', 'posadas', 'Male', '1999-10-04', '', 26, 'kylealino@gmail.com', '09158018602', 'eric alino', '09158018602', 'father', 'caloocan', 'caloocan', '', '', '', 'Beginner', 'Basic', '2026-04-24', '2026-05-24', 'Active', 'ADMIN-KYLE', '123456', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 'kyle', 'Friend/Family', 1, 1, 4, 'ADMIN-KYLE', '2026-04-24 15:52:41', '2026-04-27 13:22:55', 1),
(5, 'asdaskdjalsd', '0007534080', 'JAMIE', 'CRUZ', 'SANTIAGO', 'Female', '1999-10-03', '', 26, 'jamie@gmail.com', '0919201230', '', '', '', '', '', '', '', '', 'Beginner', 'Basic', '0000-00-00', '0000-00-00', 'Pending', 'ADMIN-JAMIE', '123456', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '', '', 1, 1, 0, 'ADMIN-KYLE', '2026-04-24 17:02:26', '2026-04-25 13:09:42', 0),
(7, '01293102301', '0007514936', 'BRYAN', 'ALINO', 'POSADAS', 'Male', '1999-10-04', '', 26, 'test@gmail.com', 'test 3', '', '', '', '', '', '', '', '', 'Beginner', 'Basic', '2026-04-25', '2026-05-25', 'Active', 'test-KYLEdasdasd', '123456', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '', '', 1, 1, 16, 'ADMIN-KYLE', '2026-04-24 17:43:48', '2026-04-25 14:05:05', 1),
(8, '0101293120', '0007519496', 'LEE ERICKSON', 'ALINO', 'POSADAS', 'Female', '2001-10-26', '', 24, 'kylealino@msda.com', '2312312', '', '', '', '', '', '', '', '', 'Beginner', 'Basic', '2026-03-28', '2026-04-27', 'Active', 'teasdas-asdasdas', '123456', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '', '', 1, 1, 1, 'ADMIN-KYLE', '2026-04-24 19:00:36', '2026-04-25 13:22:33', 0),
(9, '1092301923', '010211230122', 'RYE', 'RYE', '[ASPDASD', 'Male', '2001-01-01', '', 25, 'asdal@gmail.com', '0916523112', '', '', '', '', '', '', '', '', 'Beginner', 'Basic', '0000-00-00', '0000-00-00', 'Pending', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 'ADMIN-KYLE', '2026-04-29 05:37:41', '2026-04-29 05:37:41', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pos_dt`
--

CREATE TABLE `tbl_pos_dt` (
  `recid` int(25) NOT NULL,
  `postrxno` varchar(100) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_type` varchar(20) NOT NULL,
  `item_qty` int(25) NOT NULL,
  `item_amount` decimal(15,2) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pos_dt`
--

INSERT INTO `tbl_pos_dt` (`recid`, `postrxno`, `item_name`, `item_type`, `item_qty`, `item_amount`, `created_by`, `created_at`) VALUES
(28, 'POS-001', 'Membership - ALINO, KYLE ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-04-30 14:32:46'),
(29, 'POS-002', 'Protein Shake', 'PRODUCT', 1, 120.00, 'ADMIN-KYLE', '2026-04-30 14:33:19'),
(30, 'POS-002', 'Energy Drink', 'PRODUCT', 2, 90.00, 'ADMIN-KYLE', '2026-04-30 14:33:19'),
(31, 'POS-002', 'Gym Towel', 'PRODUCT', 2, 250.00, 'ADMIN-KYLE', '2026-04-30 14:33:19'),
(32, 'POS-002', 'Whey Protein', 'PRODUCT', 1, 2500.00, 'ADMIN-KYLE', '2026-04-30 14:33:19'),
(33, 'POS-003', 'Walk-In - KYLE', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 14:33:56'),
(34, 'POS-003', 'Walk-In - BRYAN', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 14:33:56'),
(35, 'POS-003', 'Walk-In - ELEK', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 14:33:56'),
(36, 'POS-004', 'Protein Shake', 'PRODUCT', 1, 120.00, 'ADMIN-KYLE', '2026-04-30 14:53:03'),
(37, 'POS-004', 'Energy Drink', 'PRODUCT', 2, 90.00, 'ADMIN-KYLE', '2026-04-30 14:53:03'),
(38, 'POS-004', 'Gym Towel', 'PRODUCT', 2, 250.00, 'ADMIN-KYLE', '2026-04-30 14:53:03'),
(39, 'POS-004', 'Whey Protein', 'PRODUCT', 3, 2500.00, 'ADMIN-KYLE', '2026-04-30 14:53:03'),
(40, 'POS-005', 'Membership - ALINO, BRYAN', 'MEMBERSHIP', 1, 12000.00, 'ADMIN-KYLE', '2026-04-30 14:53:21'),
(41, 'POS-006', 'Crossfit - ALEX', 'CROSSFIT', 1, 500.00, 'ADMIN-KYLE', '2026-04-30 14:53:47'),
(42, 'POS-007', 'Protein Shake', 'PRODUCT', 1, 120.00, 'ADMIN-KYLE', '2026-04-30 16:01:58'),
(43, 'POS-007', 'Energy Drink', 'PRODUCT', 3, 90.00, 'ADMIN-KYLE', '2026-04-30 16:01:58'),
(44, 'POS-007', 'Gym Towel', 'PRODUCT', 3, 250.00, 'ADMIN-KYLE', '2026-04-30 16:01:58'),
(45, 'POS-007', 'Whey Protein', 'PRODUCT', 3, 2500.00, 'ADMIN-KYLE', '2026-04-30 16:01:58'),
(46, 'POS-008', 'Membership - ALINO, KYLE ANDRAE', 'MEMBERSHIP', 1, 12000.00, 'ADMIN-KYLE', '2026-04-30 16:03:04'),
(47, 'POS-008', 'Whey Protein', 'PRODUCT', 3, 2500.00, 'ADMIN-KYLE', '2026-04-30 16:03:04'),
(48, 'POS-009', 'Protein Shake', 'PRODUCT', 1, 120.00, 'ADMIN-KYLE', '2026-04-30 16:15:55'),
(49, 'POS-009', 'Energy Drink', 'PRODUCT', 3, 90.00, 'ADMIN-KYLE', '2026-04-30 16:15:55'),
(50, 'POS-009', 'Gym Towel', 'PRODUCT', 3, 250.00, 'ADMIN-KYLE', '2026-04-30 16:15:55'),
(51, 'POS-009', 'Whey Protein', 'PRODUCT', 5, 2500.00, 'ADMIN-KYLE', '2026-04-30 16:15:55'),
(52, 'POS-010', 'Walk-In - kyle', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 16:16:38'),
(53, 'POS-010', 'Walk-In - bryan', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 16:16:38'),
(54, 'POS-010', 'Walk-In - elek', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 16:16:38'),
(55, 'POS-010', 'Walk-In - idol mike', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 16:16:38');

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

--
-- Dumping data for table `tbl_pos_payment`
--

INSERT INTO `tbl_pos_payment` (`recid`, `postrxno`, `payment_method`, `amount_tendered`, `change_amount`, `grand_total`, `created_by`, `created_at`) VALUES
(2, 'POS-001', 'Cash', 1000.00, 0.00, 1000.00, 'ADMIN-KYLE', '2026-04-30 14:32:46'),
(3, 'POS-002', 'Cash', 3300.00, 0.00, 3300.00, 'ADMIN-KYLE', '2026-04-30 14:33:19'),
(4, 'POS-003', 'GCash', 300.00, 0.00, 300.00, 'ADMIN-KYLE', '2026-04-30 14:33:56'),
(5, 'POS-004', 'Maya', 8500.00, 200.00, 8300.00, 'ADMIN-KYLE', '2026-04-30 14:53:03'),
(6, 'POS-005', 'Card', 12000.00, 0.00, 12000.00, 'ADMIN-KYLE', '2026-04-30 14:53:21'),
(7, 'POS-006', 'Cash', 500.00, 0.00, 500.00, 'ADMIN-KYLE', '2026-04-30 14:53:47'),
(8, 'POS-007', 'Cash', 9000.00, 360.00, 8640.00, 'ADMIN-KYLE', '2026-04-30 16:01:58'),
(9, 'POS-008', 'Cash', 20000.00, 500.00, 19500.00, 'ADMIN-KYLE', '2026-04-30 16:03:04'),
(10, 'POS-009', 'Cash', 14000.00, 360.00, 13640.00, 'ADMIN-KYLE', '2026-04-30 16:15:55'),
(11, 'POS-010', 'Cash', 400.00, 0.00, 400.00, 'ADMIN-KYLE', '2026-04-30 16:16:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `myctr_pos`
--
ALTER TABLE `myctr_pos`
  ADD UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`);

--
-- Indexes for table `myua_user`
--
ALTER TABLE `myua_user`
  ADD PRIMARY KEY (`recid`);

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
-- Indexes for table `tbl_members`
--
ALTER TABLE `tbl_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `member_no` (`member_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `rfid_uid` (`rfid_uid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_membership_status` (`membership_status`),
  ADD KEY `idx_rfid` (`rfid_uid`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `myua_user`
--
ALTER TABLE `myua_user`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tbl_checkin_history`
--
ALTER TABLE `tbl_checkin_history`
  MODIFY `checkin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tbl_members`
--
ALTER TABLE `tbl_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_pos_dt`
--
ALTER TABLE `tbl_pos_dt`
  MODIFY `recid` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `tbl_pos_payment`
--
ALTER TABLE `tbl_pos_payment`
  MODIFY `recid` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
