-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 05:02 PM
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

--
-- Dumping data for table `myctr_adjustment`
--

INSERT INTO `myctr_adjustment` (`CTR_YEAR`, `CTR_MONTH`, `CTR_DAY`, `CTRL_NO01`, `CTRL_NO02`, `CTRL_NO03`, `CTRL_NO04`, `CTRL_NO05`, `CTRL_NO06`, `CTRL_NO07`, `CTRL_NO08`, `CTRL_NO09`, `CTRL_NO10`, `CTRL_NO11`, `SS_CTR`) VALUES
('2026', '05', '05', '002', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000'),
('2026', '05', '06', '002', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000'),
('2026', '05', '08', '002', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000');

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
('2026', '04', '30', '019', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000'),
('2026', '05', '03', '019', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000'),
('2026', '05', '05', '019', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000'),
('2026', '05', '06', '019', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000'),
('2026', '05', '08', '019', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000');

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

--
-- Dumping data for table `myctr_stockin`
--

INSERT INTO `myctr_stockin` (`CTR_YEAR`, `CTR_MONTH`, `CTR_DAY`, `CTRL_NO01`, `CTRL_NO02`, `CTRL_NO03`, `CTRL_NO04`, `CTRL_NO05`, `CTRL_NO06`, `CTRL_NO07`, `CTRL_NO08`, `CTRL_NO09`, `CTRL_NO10`, `CTRL_NO11`, `SS_CTR`) VALUES
('2026', '05', '05', '021', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '00000000', '000000');

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
(44, 7, '0007514936', '2026-04-25 22:05:05', '2026-05-08 22:04:12', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-25 14:05:05', NULL, NULL, 0),
(45, 1, '0007717262', '2026-04-27 21:22:55', '2026-05-08 22:04:20', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-04-27 13:22:55', NULL, NULL, 0),
(46, 9, '010211230122', '2026-05-06 13:54:21', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-06 05:54:21', NULL, NULL, 0),
(48, 11, '0007723503', '2026-05-06 21:30:51', '2026-05-06 21:31:15', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-05-06 13:30:51', NULL, NULL, 0),
(49, 11, '0007723503', '2026-05-06 21:39:04', '2026-05-06 21:39:19', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-05-06 13:39:04', NULL, NULL, 0),
(50, 12, '0007553966', '2026-05-06 21:58:07', '2026-05-06 22:12:12', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-05-06 13:58:07', NULL, NULL, 0),
(51, 13, '0007546221', '2026-05-06 22:02:42', '2026-05-06 22:12:08', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-05-06 14:02:42', NULL, NULL, 0),
(52, 14, '01293120312', '2026-05-08 17:40:27', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 09:40:27', NULL, NULL, 0),
(53, 11, '0007723503', '2026-05-08 22:04:47', '2026-05-08 22:05:05', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-05-08 14:04:47', NULL, NULL, 0),
(54, 12, '0007553966', '2026-05-08 22:05:13', '2026-05-08 22:05:19', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-05-08 14:05:13', NULL, NULL, 0),
(55, 13, '0007546221', '2026-05-08 22:05:25', '2026-05-08 22:05:47', NULL, 'RFID', NULL, NULL, 'Completed', NULL, NULL, NULL, '2026-05-08 14:05:25', NULL, NULL, 0),
(56, 15, '0007534080', '2026-05-08 22:19:03', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:19:03', NULL, NULL, 0),
(57, 15, '0007534080', '2026-05-08 22:20:35', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:20:35', NULL, NULL, 0),
(58, 15, '0007534080', '2026-05-08 22:21:51', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:21:51', NULL, NULL, 0),
(59, 15, '0007534080', '2026-05-08 22:24:58', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:24:58', NULL, NULL, 0),
(60, 15, '0007534080', '2026-05-08 22:26:46', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:26:46', NULL, NULL, 0),
(61, 15, '0007534080', '2026-05-08 22:28:06', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:28:06', NULL, NULL, 0),
(62, 15, '0007534080', '2026-05-08 22:29:32', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:29:32', NULL, NULL, 0),
(63, 15, '0007534080', '2026-05-08 22:33:55', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:33:55', NULL, NULL, 0),
(64, 15, '0007534080', '2026-05-08 22:35:03', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:35:03', NULL, NULL, 0),
(65, 15, '0007534080', '2026-05-08 22:37:39', NULL, NULL, 'RFID', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-05-08 14:37:39', NULL, NULL, 0);

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

--
-- Dumping data for table `tbl_crossfit_checkin_history`
--

INSERT INTO `tbl_crossfit_checkin_history` (`checkin_id`, `crossfit_name`, `checkin_time`, `checkout_time`, `created_at`) VALUES
(1, ' ELEK', '2026-05-08 08:14:26', '2026-05-08 12:14:26', '2026-05-08 08:14:26'),
(2, ' ARA', '2026-05-08 17:35:58', '2026-05-08 21:35:58', '2026-05-08 17:35:58');

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

--
-- Dumping data for table `tbl_inventory_movements`
--

INSERT INTO `tbl_inventory_movements` (`movement_id`, `product_id`, `product_name`, `movement_type`, `quantity`, `reference_type`, `reference_no`, `remarks`, `created_by`, `created_at`) VALUES
(3, 3, 'Shirt', 'IN', 20, 'STOCK IN', 'STOCKIN-001', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:20:07'),
(4, 4, 'Bag', 'IN', 15, 'STOCK IN', 'STOCKIN-002', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:20:20'),
(5, 5, 'Cap', 'IN', 10, 'STOCK IN', 'STOCKIN-003', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:20:33'),
(6, 6, 'Hand Grip', 'IN', 10, 'STOCK IN', 'STOCKIN-004', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:20:51'),
(7, 7, 'Tumbler-300', 'IN', 10, 'STOCK IN', 'STOCKIN-005', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:21:27'),
(8, 8, 'Tumbler-350', 'IN', 10, 'STOCK IN', 'STOCKIN-006', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:21:49'),
(9, 9, 'Bottled Water', 'IN', 30, 'STOCK IN', 'STOCKIN-007', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:22:10'),
(10, 10, 'Gatorade', 'IN', 30, 'STOCK IN', 'STOCKIN-008', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:22:22'),
(11, 11, 'Vitamilk', 'IN', 30, 'STOCK IN', 'STOCKIN-009', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:22:37'),
(12, 12, 'Sting', 'IN', 30, 'STOCK IN', 'STOCKIN-010', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:23:12'),
(13, 13, 'Cobra', 'IN', 30, 'STOCK IN', 'STOCKIN-011', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:23:30'),
(14, 14, 'Egg', 'IN', 48, 'STOCK IN', 'STOCKIN-012', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:24:10'),
(15, 15, 'Kaman', 'IN', 30, 'STOCK IN', 'STOCKIN-013', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:24:22'),
(16, 16, 'Amino 2222', 'IN', 100, 'STOCK IN', 'STOCKIN-014', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:24:45'),
(17, 17, 'Amino 8000', 'IN', 100, 'STOCK IN', 'STOCKIN-015', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:24:59'),
(18, 18, 'Creatine Powder', 'IN', 100, 'STOCK IN', 'STOCKIN-016', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:25:49'),
(19, 19, 'Creatine Capsule', 'IN', 50, 'STOCK IN', 'STOCKIN-017', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:26:03'),
(20, 20, 'Pre Workout', 'IN', 40, 'STOCK IN', 'STOCKIN-018', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:26:17'),
(21, 21, 'Ripped', 'IN', 30, 'STOCK IN', 'STOCKIN-019', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:26:36'),
(22, 22, 'Whey Core', 'IN', 30, 'STOCK IN', 'STOCKIN-020', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:26:59'),
(23, 23, 'Isolate', 'IN', 25, 'STOCK IN', 'STOCKIN-021', 'NEW PRODUCT', 'ADMIN-KYLE', '2026-05-05 14:27:12'),
(24, 23, 'Isolate', 'ADJUSTMENT', -5, 'ADJUSTMENT', 'ADJUSTMENT-004', 'Physical Count Correction', 'ADMIN-KYLE', '2026-05-05 16:26:26'),
(25, 22, 'Whey Core', 'ADJUSTMENT', 20, 'ADJUSTMENT', 'ADJUSTMENT-005', 'System Correction', 'ADMIN-KYLE', '2026-05-05 16:31:08'),
(26, 23, 'Isolate', 'ADJUSTMENT', -18, 'ADJUSTMENT', 'ADJUSTMENT-006', 'Expired Item', 'ADMIN-KYLE', '2026-05-05 16:38:56'),
(27, 4, 'Bag', 'OUT', -3, 'POS', 'POS05052026005', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-05 17:07:54'),
(28, 5, 'Cap', 'OUT', -2, 'POS', 'POS05052026005', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-05 17:07:54'),
(29, 3, 'Shirt', 'OUT', -5, 'POS', 'POS05052026005', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-05 17:07:54'),
(30, 13, 'Cobra', 'OUT', -1, 'POS', 'POS05062026006', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-06 11:14:34'),
(31, 10, 'Gatorade', 'OUT', -1, 'POS', 'POS05062026006', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-06 11:14:34'),
(32, 12, 'Sting', 'OUT', -1, 'POS', 'POS05062026006', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-06 11:14:34'),
(33, 5, 'Cap', 'ADJUSTMENT', -4, 'ADJUSTMENT', 'ADJUSTMENT-001', 'Physical Count Correction', 'ADMIN-KYLE', '2026-05-06 22:16:00'),
(34, 5, 'Cap', 'ADJUSTMENT', 6, 'ADJUSTMENT', 'ADJUSTMENT-002', 'System Correction', 'ADMIN-KYLE', '2026-05-06 22:16:24'),
(35, 4, 'Bag', 'OUT', -1, 'POS', 'POS05082026001', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 08:14:26'),
(36, 3, 'Shirt', 'OUT', -1, 'POS', 'POS05082026002', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 13:32:12'),
(37, 7, 'Tumbler-300', 'OUT', -2, 'POS', 'POS05082026002', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 13:32:12'),
(38, 7, 'Tumbler-300', 'ADJUSTMENT', -4, 'ADJUSTMENT', 'ADJUSTMENT-001', 'Physical Count Correction', 'ADMIN-KYLE', '2026-05-08 17:36:58'),
(39, 3, 'Shirt', 'OUT', -4, 'POS', 'POS05082026006', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 17:37:52'),
(40, 3, 'Shirt', 'ADJUSTMENT', 10, 'ADJUSTMENT', 'ADJUSTMENT-002', 'System Correction', 'ADMIN-KYLE', '2026-05-08 17:38:21'),
(41, 9, 'Bottled Water', 'OUT', -1, 'POS', 'POS05082026019', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(42, 11, 'Vitamilk', 'OUT', -1, 'POS', 'POS05082026019', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(43, 13, 'Cobra', 'OUT', -1, 'POS', 'POS05082026019', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(44, 10, 'Gatorade', 'OUT', -1, 'POS', 'POS05082026019', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(45, 12, 'Sting', 'OUT', -1, 'POS', 'POS05082026019', 'POS TRANSACTION', 'ADMIN-KYLE', '2026-05-08 22:42:26');

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
  `membership_plan` varchar(30) DEFAULT NULL,
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
(1, '01239123', '0007717262', 'KYLE ANDRAE', 'ALINO', 'posadas', 'Male', '1999-10-04', '', 26, 'rarao@gmail.com', '09158018602', 'eric alino', '09158018602', 'father', 'caloocan', 'caloocan', '', '', '', 'Beginner', '1 Month', '2026-04-24', '2026-05-24', 'Active', 'ADMIN-KYLE', '123456', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 'kyle', 'Friend/Family', 1, 1, 4, 'ADMIN-KYLE', '2026-04-24 15:52:41', '2026-05-08 14:15:03', 0),
(7, '01293102301', '0007514936', 'BRYAN', 'ALINO', 'POSADAS', 'Male', '1999-10-04', '', 26, 'test@gmail.com', 'test 3', '', '', '', '', '', '', '', '', 'Beginner', '1 Month', '2026-04-25', '2026-05-25', 'Active', 'test-KYLEdasdasd', '123456', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '', '', 1, 1, 16, 'ADMIN-KYLE', '2026-04-24 17:43:48', '2026-05-08 14:04:12', 0),
(9, '1092301923', '010211230122', 'RYE', 'RYE', '[ASPDASD', 'Male', '2001-01-01', '', 25, 'asdal@gmail.com', '0916523112', '', '', '', '', '', '', '', '', 'Beginner', '1 Month', '2026-05-06', '2026-06-06', 'Active', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 'ADMIN-KYLE', '2026-04-29 05:37:41', '2026-05-06 13:38:33', 1),
(11, '012030123', '0007723503', 'SAIRA', 'CRUZ', 'SANTIAGO', 'Female', '2000-10-27', '', 25, 'saira@gmail.com', '09202003829', '', '', '', '', '', '', '', '', 'Beginner', '1 Month', '2026-05-06', '2026-06-06', 'Active', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 'ADMIN-KYLE', '2026-05-06 13:30:22', '2026-05-08 14:05:05', 0),
(12, '0123010', '0007553966', 'AEROL', 'TOMARSE', 'GONGON', 'Male', '2001-09-07', '', 24, 'erol@gmail.com', '0902910231', '', '', '', '', '', '', '', '', 'Beginner', '3 Months', '2026-05-06', '2026-08-06', 'Active', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 'ADMIN-KYLE', '2026-05-06 13:46:53', '2026-05-08 14:05:19', 0),
(13, '01293190132', '0007546221', 'ALEXANDER', 'HABIG', 'JOKOY', 'Male', '2005-08-09', '', 20, 'alex@gmail.com', '09201023131', '', '', '', '', '', '', '', '', 'Beginner', '6 Months', '2026-05-06', '2026-11-06', 'Active', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 'ADMIN-KYLE', '2026-05-06 14:02:00', '2026-05-08 14:05:47', 0),
(14, '10201203', '01293120312', 'OLI', 'SANTA MARIA', 'SAI', 'Male', '1999-10-02', '', 26, 'OLI@GMAIL.COM', '0092102301', '', '', '', '', '', '', '', '', 'Beginner', '12 Months', '2026-05-08', '2027-05-08', 'Active', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 'ADMIN-KYLE', '2026-05-08 09:40:00', '2026-05-08 09:40:27', 1),
(15, '01203102', '0007534080', 'ANDRAE', 'POSADAS', 'ALINO', 'Male', '1999-10-03', '', 26, 'kylealino@gmail.com', '09157789982', '', '', '', '', '', '', '', '', 'Beginner', '12 Months', '2026-05-08', '2027-05-08', 'Active', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 'ADMIN-KYLE', '2026-05-08 14:16:17', '2026-05-08 14:37:39', 1);

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
(55, 'POS-010', 'Walk-In - idol mike', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-04-30 16:16:38'),
(56, 'POS-001', 'Whey Protein', 'PRODUCT', 4, 2500.00, 'ADMIN-KYLE', '2026-05-03 21:07:13'),
(57, 'POS-001', 'Bag', 'Merchandise', 1, 500.00, 'ADMIN-KYLE', '2026-05-05 16:58:59'),
(58, 'POS-001', 'Cap', 'Merchandise', 1, 350.00, 'ADMIN-KYLE', '2026-05-05 16:58:59'),
(59, 'POS-001', 'Shirt', 'Merchandise', 1, 400.00, 'ADMIN-KYLE', '2026-05-05 16:58:59'),
(60, 'POS-002', 'Bag', 'Merchandise', 1, 500.00, 'ADMIN-KYLE', '2026-05-05 16:59:30'),
(61, 'POS-002', 'Cap', 'Merchandise', 1, 350.00, 'ADMIN-KYLE', '2026-05-05 16:59:30'),
(62, 'POS-002', 'Shirt', 'Merchandise', 1, 400.00, 'ADMIN-KYLE', '2026-05-05 16:59:30'),
(63, 'POS-05052026003', 'Hand Grip', 'Accessories', 1, 120.00, 'ADMIN-KYLE', '2026-05-05 17:01:30'),
(64, 'POS-05052026003', 'Tumbler-300', 'Accessories', 1, 300.00, 'ADMIN-KYLE', '2026-05-05 17:01:30'),
(65, 'POS-05052026003', 'Tumbler-350', 'Accessories', 1, 350.00, 'ADMIN-KYLE', '2026-05-05 17:01:30'),
(66, 'POS05052026004', 'Bottled Water', 'Beverages', 1, 20.00, 'ADMIN-KYLE', '2026-05-05 17:02:08'),
(67, 'POS05052026004', 'Cobra', 'Beverages', 1, 28.00, 'ADMIN-KYLE', '2026-05-05 17:02:08'),
(68, 'POS05052026004', 'Gatorade', 'Beverages', 1, 55.00, 'ADMIN-KYLE', '2026-05-05 17:02:08'),
(69, 'POS05052026005', 'Bag', 'Merchandise', 3, 500.00, 'ADMIN-KYLE', '2026-05-05 17:07:54'),
(70, 'POS05052026005', 'Cap', 'Merchandise', 2, 350.00, 'ADMIN-KYLE', '2026-05-05 17:07:54'),
(71, 'POS05052026005', 'Shirt', 'Merchandise', 5, 400.00, 'ADMIN-KYLE', '2026-05-05 17:07:54'),
(72, 'POS05062026001', 'Membership - ALINO, KYLE ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 09:59:45'),
(73, 'POS05062026002', 'Membership - ALINO, KYLE ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 10:04:16'),
(74, 'POS05062026003', 'Membership - ALINO, KYLE ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 10:04:38'),
(75, 'POS05062026004', 'Membership - ALINO, KYLE ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 11:04:24'),
(76, 'POS05062026005', 'Membership - ALINO, KYLE ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 11:04:46'),
(77, 'POS05062026006', 'Cobra', 'Beverages', 1, 28.00, 'ADMIN-KYLE', '2026-05-06 11:14:34'),
(78, 'POS05062026006', 'Gatorade', 'Beverages', 1, 55.00, 'ADMIN-KYLE', '2026-05-06 11:14:34'),
(79, 'POS05062026006', 'Sting', 'Beverages', 1, 28.00, 'ADMIN-KYLE', '2026-05-06 11:14:34'),
(80, 'POS05062026007', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:40:09'),
(81, 'POS05062026008', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:43:43'),
(82, 'POS05062026009', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:45:39'),
(83, 'POS05062026010', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:47:07'),
(84, 'POS05062026011', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:47:28'),
(85, 'POS05062026012', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:48:03'),
(86, 'POS05062026013', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:50:09'),
(87, 'POS05062026014', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 13:53:53'),
(88, 'POS05062026015', 'Membership - RYE, RYE', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-06 13:54:21'),
(89, 'POS05062026016', 'Walk-In - TEST', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:05:13'),
(90, 'POS05062026017', 'Walk-In - TEST', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:05:40'),
(91, 'POS05062026018', 'Walk-In - JESTER', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:10:00'),
(92, 'POS05062026019', 'Walk-In - TETETETE', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:10:58'),
(93, 'POS05062026020', 'Walk-In - asdasdasd', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:11:46'),
(94, 'POS05062026021', 'Walk-In - asdasdas', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:12:12'),
(95, 'POS05062026022', 'Walk-In - asdasdasda', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:12:28'),
(96, 'POS05062026023', 'Walk-In - KYLE', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:13:30'),
(97, 'POS05062026023', 'Walk-In - ELEK', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:13:30'),
(98, 'POS05062026023', 'Walk-In - BRYAN', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:13:30'),
(99, 'POS05062026024', 'Walk-In - KEKENG', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 14:24:15'),
(100, 'POS05062026026', 'Membership - CRUZ, GHEMILYNNE', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-06 21:23:29'),
(101, 'POS05062026027', 'Walk-In - RARARARA', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 21:26:22'),
(102, 'POS05062026028', 'Membership - CRUZ, SAIRA', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-06 21:30:51'),
(103, 'POS05062026029', 'Membership - TOMARSE, AEROL', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-06 21:58:07'),
(104, 'POS05062026030', 'Membership - HABIG, ALEXANDER', 'MEMBERSHIP', 1, 6000.00, 'ADMIN-KYLE', '2026-05-06 22:02:42'),
(105, 'POS05062026031', 'Walk-In - KOLOKOY', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 22:03:11'),
(106, 'POS05062026032', 'Walk-In - MAYKEL', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-06 22:05:05'),
(107, 'POS05082026001', 'Walk-In - KYLE', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 08:14:26'),
(108, 'POS05082026001', 'Zumba - BRYAN', 'ZUMBA', 1, 70.00, 'ADMIN-KYLE', '2026-05-08 08:14:26'),
(109, 'POS05082026001', 'Crossfit - ELEK', 'CROSSFIT', 1, 500.00, 'ADMIN-KYLE', '2026-05-08 08:14:26'),
(110, 'POS05082026001', 'Yoga - ANGEL', 'YOGA', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 08:14:26'),
(111, 'POS05082026001', 'Bag', 'Merchandise', 1, 500.00, 'ADMIN-KYLE', '2026-05-08 08:14:26'),
(112, 'POS05082026002', 'Shirt', 'Merchandise', 1, 400.00, 'ADMIN-KYLE', '2026-05-08 13:32:12'),
(113, 'POS05082026002', 'Tumbler-300', 'Accessories', 2, 300.00, 'ADMIN-KYLE', '2026-05-08 13:32:12'),
(114, 'POS05082026003', 'Walk-In - KYLE', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 13:32:51'),
(115, 'POS05082026003', 'Walk-In - BRYAN', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 13:32:51'),
(116, 'POS05082026003', 'Walk-In - ELEK', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 13:32:51'),
(117, 'POS05082026004', 'Yoga - RARA', 'YOGA', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 14:07:25'),
(118, 'POS05082026004', 'Yoga - RERE', 'YOGA', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 14:07:25'),
(119, 'POS05082026005', 'Walk-In - OLI', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 17:35:58'),
(120, 'POS05082026005', 'Walk-In - JOAN', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 17:35:58'),
(121, 'POS05082026005', 'Crossfit - ARA', 'CROSSFIT', 1, 500.00, 'ADMIN-KYLE', '2026-05-08 17:35:58'),
(122, 'POS05082026006', 'Shirt', 'Merchandise', 4, 400.00, 'ADMIN-KYLE', '2026-05-08 17:37:52'),
(123, 'POS05082026007', 'Membership - SANTA MARIA, OLI', 'MEMBERSHIP', 1, 12000.00, 'ADMIN-KYLE', '2026-05-08 17:40:27'),
(124, 'POS05082026008', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:19:03'),
(125, 'POS05082026009', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:20:35'),
(126, 'POS05082026010', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:21:51'),
(127, 'POS05082026011', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:24:58'),
(128, 'POS05082026012', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 6000.00, 'ADMIN-KYLE', '2026-05-08 22:26:46'),
(129, 'POS05082026013', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-08 22:28:06'),
(130, 'POS05082026014', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:29:32'),
(131, 'POS05082026015', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-08 22:33:55'),
(132, 'POS05082026016', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 1000.00, 'ADMIN-KYLE', '2026-05-08 22:35:03'),
(133, 'POS05082026017', 'Membership - POSADAS, ANDRAE', 'MEMBERSHIP', 1, 12000.00, 'ADMIN-KYLE', '2026-05-08 22:37:39'),
(134, 'POS05082026018', 'Walk-In - ELEK', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 22:41:28'),
(135, 'POS05082026018', 'Walk-In - BRYAN', 'WALK-IN', 1, 100.00, 'ADMIN-KYLE', '2026-05-08 22:41:28'),
(136, 'POS05082026019', 'Bottled Water', 'Beverages', 1, 20.00, 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(137, 'POS05082026019', 'Vitamilk', 'Beverages', 1, 45.00, 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(138, 'POS05082026019', 'Cobra', 'Beverages', 1, 28.00, 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(139, 'POS05082026019', 'Gatorade', 'Beverages', 1, 55.00, 'ADMIN-KYLE', '2026-05-08 22:42:26'),
(140, 'POS05082026019', 'Sting', 'Beverages', 1, 28.00, 'ADMIN-KYLE', '2026-05-08 22:42:26');

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
(11, 'POS-010', 'Cash', 400.00, 0.00, 400.00, 'ADMIN-KYLE', '2026-04-30 16:16:38'),
(12, 'POS-001', 'GCash', 10000.00, 0.00, 10000.00, 'ADMIN-KYLE', '2026-05-03 21:07:13'),
(13, 'POS-001', 'Cash', 1300.00, 50.00, 1250.00, 'ADMIN-KYLE', '2026-05-05 16:58:59'),
(14, 'POS-002', 'Cash', 1300.00, 50.00, 1250.00, 'ADMIN-KYLE', '2026-05-05 16:59:30'),
(15, 'POS-05052026003', 'Cash', 770.00, 0.00, 770.00, 'ADMIN-KYLE', '2026-05-05 17:01:30'),
(16, 'POS05052026004', 'Cash', 103.00, 0.00, 103.00, 'ADMIN-KYLE', '2026-05-05 17:02:08'),
(17, 'POS05052026005', 'Cash', 4200.00, 0.00, 4200.00, 'ADMIN-KYLE', '2026-05-05 17:07:54'),
(18, 'POS05062026003', 'Cash', 1000.00, 0.00, 1000.00, 'ADMIN-KYLE', '2026-05-06 10:04:38'),
(19, 'POS05062026005', 'Cash', 1000.00, 0.00, 1000.00, 'ADMIN-KYLE', '2026-05-06 11:04:46'),
(20, 'POS05062026006', 'GCash', 111.00, 0.00, 111.00, 'ADMIN-KYLE', '2026-05-06 11:14:34'),
(21, 'POS05062026015', 'Cash', 3000.00, 0.00, 3000.00, 'ADMIN-KYLE', '2026-05-06 13:54:21'),
(22, 'POS05062026017', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 14:05:40'),
(23, 'POS05062026018', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 14:10:00'),
(24, 'POS05062026019', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 14:10:58'),
(25, 'POS05062026020', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 14:11:46'),
(26, 'POS05062026021', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 14:12:12'),
(27, 'POS05062026022', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 14:12:28'),
(28, 'POS05062026023', 'Cash', 300.00, 0.00, 300.00, 'ADMIN-KYLE', '2026-05-06 14:13:30'),
(29, 'POS05062026024', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 14:24:15'),
(30, 'POS05062026026', 'Cash', 3000.00, 0.00, 3000.00, 'ADMIN-KYLE', '2026-05-06 21:23:29'),
(31, 'POS05062026027', 'Card', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 21:26:22'),
(32, 'POS05062026028', 'GCash', 1000.00, 0.00, 1000.00, 'ADMIN-KYLE', '2026-05-06 21:30:51'),
(33, 'POS05062026029', 'Cash', 3000.00, 0.00, 3000.00, 'ADMIN-KYLE', '2026-05-06 21:58:07'),
(34, 'POS05062026030', 'Maya', 6000.00, 0.00, 6000.00, 'ADMIN-KYLE', '2026-05-06 22:02:42'),
(35, 'POS05062026031', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 22:03:11'),
(36, 'POS05062026032', 'Cash', 100.00, 0.00, 100.00, 'ADMIN-KYLE', '2026-05-06 22:05:05'),
(37, 'POS05082026001', 'Cash', 1270.00, 0.00, 1270.00, 'ADMIN-KYLE', '2026-05-08 08:14:26'),
(38, 'POS05082026002', 'Cash', 1000.00, 0.00, 1000.00, 'ADMIN-KYLE', '2026-05-08 13:32:12'),
(39, 'POS05082026003', 'Cash', 300.00, 0.00, 300.00, 'ADMIN-KYLE', '2026-05-08 13:32:51'),
(40, 'POS05082026004', 'Cash', 200.00, 0.00, 200.00, 'ADMIN-KYLE', '2026-05-08 14:07:25'),
(41, 'POS05082026005', 'Cash', 700.00, 0.00, 700.00, 'ADMIN-KYLE', '2026-05-08 17:35:58'),
(42, 'POS05082026006', 'Cash', 1600.00, 0.00, 1600.00, 'ADMIN-KYLE', '2026-05-08 17:37:52'),
(43, 'POS05082026007', 'Cash', 12000.00, 0.00, 12000.00, 'ADMIN-KYLE', '2026-05-08 17:40:27'),
(44, 'POS05082026009', 'Cash', 3000.00, 0.00, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:20:39'),
(45, 'POS05082026010', 'Cash', 3000.00, 0.00, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:21:55'),
(46, 'POS05082026011', 'Cash', 3000.00, 0.00, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:25:02'),
(47, 'POS05082026012', 'Cash', 6000.00, 0.00, 6000.00, 'ADMIN-KYLE', '2026-05-08 22:26:51'),
(48, 'POS05082026013', 'Cash', 3000.00, 2000.00, 1000.00, 'ADMIN-KYLE', '2026-05-08 22:28:09'),
(49, 'POS05082026014', 'Cash', 3000.00, 0.00, 3000.00, 'ADMIN-KYLE', '2026-05-08 22:29:35'),
(50, 'POS05082026016', 'Cash', 1000.00, 0.00, 1000.00, 'ADMIN-KYLE', '2026-05-08 22:35:08'),
(51, 'POS05082026017', 'Cash', 12000.00, 0.00, 12000.00, 'ADMIN-KYLE', '2026-05-08 22:37:43'),
(52, 'POS05082026018', 'Cash', 200.00, 0.00, 200.00, 'ADMIN-KYLE', '2026-05-08 22:41:28'),
(53, 'POS05082026019', 'Cash', 178.00, 2.00, 176.00, 'ADMIN-KYLE', '2026-05-08 22:42:26');

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

--
-- Dumping data for table `tbl_products`
--

INSERT INTO `tbl_products` (`product_id`, `product_name`, `category`, `purchase_price`, `selling_price`, `stock_qty`, `status`, `reorder_level`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'Shirt', 'Merchandise', 200.00, 400.00, 20, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:20:07', '2026-05-08 17:38:21'),
(4, 'Bag', 'Merchandise', 250.00, 500.00, 11, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:20:20', '2026-05-08 08:14:26'),
(5, 'Cap', 'Merchandise', 100.00, 350.00, 10, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:20:33', '2026-05-06 22:16:24'),
(6, 'Hand Grip', 'Accessories', 50.00, 120.00, 10, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:20:51', NULL),
(7, 'Tumbler-300', 'Accessories', 150.00, 300.00, 4, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:21:27', '2026-05-08 17:36:58'),
(8, 'Tumbler-350', 'Accessories', 175.00, 350.00, 10, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:21:49', NULL),
(9, 'Bottled Water', 'Beverages', 10.00, 20.00, 29, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:22:10', '2026-05-08 22:42:26'),
(10, 'Gatorade', 'Beverages', 25.00, 55.00, 28, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:22:22', '2026-05-08 22:42:26'),
(11, 'Vitamilk', 'Beverages', 20.00, 45.00, 29, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:22:37', '2026-05-08 22:42:26'),
(12, 'Sting', 'Beverages', 10.00, 28.00, 28, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:23:12', '2026-05-08 22:42:26'),
(13, 'Cobra', 'Beverages', 10.00, 28.00, 28, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:23:30', '2026-05-08 22:42:26'),
(14, 'Egg', 'Food', 5.00, 15.00, 48, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:24:10', NULL),
(15, 'Kaman', 'Food', 5.00, 12.00, 30, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:24:22', NULL),
(16, 'Amino 2222', 'Supplements', 10.00, 25.00, 100, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:24:45', NULL),
(17, 'Amino 8000', 'Supplements', 5.00, 20.00, 100, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:24:59', NULL),
(18, 'Creatine Powder', 'Supplements', 10.00, 25.00, 100, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:25:49', NULL),
(19, 'Creatine Capsule', 'Supplements', 10.00, 30.00, 50, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:26:03', NULL),
(20, 'Pre Workout', 'Supplements', 40.00, 80.00, 40, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:26:17', NULL),
(21, 'Ripped', 'Supplements', 20.00, 45.00, 30, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:26:36', NULL),
(22, 'Whey Core', 'Supplements', 35.00, 75.00, 50, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:26:59', '2026-05-05 16:31:08'),
(23, 'Isolate', 'Supplements', 45.00, 100.00, 2, 'ACTIVE', 5, 'ADMIN-KYLE', '2026-05-05 14:27:12', '2026-05-05 16:38:56');

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

--
-- Dumping data for table `tbl_walkin_checkin_history`
--

INSERT INTO `tbl_walkin_checkin_history` (`checkin_id`, `walkin_name`, `checkin_time`, `checkout_time`, `created_at`) VALUES
(7, ' KYLE', '2026-05-06 14:13:30', '2026-05-06 18:13:30', '2026-05-06 14:13:30'),
(8, ' ELEK', '2026-05-06 14:13:30', '2026-05-06 18:13:30', '2026-05-06 14:13:30'),
(9, ' BRYAN', '2026-05-06 14:13:30', '2026-05-06 18:13:30', '2026-05-06 14:13:30'),
(10, ' KEKENG', '2026-05-06 14:24:15', '2026-05-06 18:24:15', '2026-05-06 14:24:15'),
(11, ' RARARARA', '2026-05-06 21:26:22', '2026-05-07 01:26:22', '2026-05-06 21:26:22'),
(12, ' KOLOKOY', '2026-05-06 22:03:11', '2026-05-07 02:03:11', '2026-05-06 22:03:11'),
(13, ' MAYKEL', '2026-05-06 22:05:05', '2026-05-07 02:05:05', '2026-05-06 22:05:05'),
(14, ' KYLE', '2026-05-08 08:14:26', '2026-05-08 12:14:26', '2026-05-08 08:14:26'),
(15, ' KYLE', '2026-05-08 13:32:51', '2026-05-08 17:32:51', '2026-05-08 13:32:51'),
(16, ' BRYAN', '2026-05-08 13:32:51', '2026-05-08 17:32:51', '2026-05-08 13:32:51'),
(17, ' ELEK', '2026-05-08 13:32:51', '2026-05-08 17:32:51', '2026-05-08 13:32:51'),
(18, ' OLI', '2026-05-08 17:35:58', '2026-05-08 21:35:58', '2026-05-08 17:35:58'),
(19, ' JOAN', '2026-05-08 17:35:58', '2026-05-08 21:35:58', '2026-05-08 17:35:58'),
(20, ' ELEK', '2026-05-08 22:41:28', '2026-05-09 02:41:28', '2026-05-08 22:41:28'),
(21, ' BRYAN', '2026-05-08 22:41:28', '2026-05-09 02:41:28', '2026-05-08 22:41:28');

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

--
-- Dumping data for table `tbl_yoga_checkin_history`
--

INSERT INTO `tbl_yoga_checkin_history` (`checkin_id`, `yoga_name`, `checkin_time`, `checkout_time`, `created_at`) VALUES
(1, ' ANGEL', '2026-05-08 08:14:26', '2026-05-08 12:14:26', '2026-05-08 08:14:26'),
(2, ' RARA', '2026-05-08 14:07:25', '2026-05-08 18:07:25', '2026-05-08 14:07:25'),
(3, ' RERE', '2026-05-08 14:07:25', '2026-05-08 18:07:25', '2026-05-08 14:07:25');

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
-- Dumping data for table `tbl_zumba_checkin_history`
--

INSERT INTO `tbl_zumba_checkin_history` (`checkin_id`, `zumba_name`, `checkin_time`, `checkout_time`, `created_at`) VALUES
(1, ' BRYAN', '2026-05-08 08:14:26', '2026-05-08 12:14:26', '2026-05-08 08:14:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `myctr_adjustment`
--
ALTER TABLE `myctr_adjustment`
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
-- Indexes for table `tbl_inventory_movements`
--
ALTER TABLE `tbl_inventory_movements`
  ADD PRIMARY KEY (`movement_id`);

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
-- Indexes for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`product_id`);

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
-- AUTO_INCREMENT for table `tbl_checkin_history`
--
ALTER TABLE `tbl_checkin_history`
  MODIFY `checkin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `tbl_crossfit_checkin_history`
--
ALTER TABLE `tbl_crossfit_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_inventory_movements`
--
ALTER TABLE `tbl_inventory_movements`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tbl_members`
--
ALTER TABLE `tbl_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_pos_dt`
--
ALTER TABLE `tbl_pos_dt`
  MODIFY `recid` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `tbl_pos_payment`
--
ALTER TABLE `tbl_pos_payment`
  MODIFY `recid` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_yoga_checkin_history`
--
ALTER TABLE `tbl_yoga_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_zumba_checkin_history`
--
ALTER TABLE `tbl_zumba_checkin_history`
  MODIFY `checkin_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
