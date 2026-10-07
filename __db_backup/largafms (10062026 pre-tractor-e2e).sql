-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: largafms
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `myua_user`
--

DROP TABLE IF EXISTS `myua_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myua_user` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `hash_password` varchar(200) NOT NULL,
  `hash_value` varchar(150) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `division` varchar(50) NOT NULL,
  `section` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `cert_tag` int(11) DEFAULT 0,
  `is_ppmp_signatory` int(11) NOT NULL DEFAULT 0,
  `added_at` datetime NOT NULL DEFAULT current_timestamp(),
  `added_by` varchar(50) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`recid`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `myua_user`
--

LOCK TABLES `myua_user` WRITE;
/*!40000 ALTER TABLE `myua_user` DISABLE KEYS */;
INSERT INTO `myua_user` VALUES (6,'ADMIN','ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413','123456','KYLE ALINO','IT','MIS','PROGRAMMER V',1,0,0,'2025-05-16 08:55:10','admin',1,'2026-09-24 16:58:18');
/*!40000 ALTER TABLE `myua_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_billing`
--

DROP TABLE IF EXISTS `tbl_billing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_billing` (
  `billing_id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_code` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `dr_id` int(11) NOT NULL,
  `billing_date` date NOT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `billing_basis` enum('PER_TRIP','PER_KILOMETER','PER_HOUR','PER_DAY','FIXED_RATE') NOT NULL DEFAULT 'PER_TRIP',
  `rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(15,2) NOT NULL DEFAULT 1.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `billing_status` enum('DRAFT','FOR_INVOICE','INVOICED','CANCELLED') NOT NULL DEFAULT 'DRAFT',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`billing_id`),
  UNIQUE KEY `billing_code` (`billing_code`),
  UNIQUE KEY `dr_id` (`dr_id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_trip_id` (`trip_id`),
  KEY `idx_billing_status` (`billing_status`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_billing`
--

LOCK TABLES `tbl_billing` WRITE;
/*!40000 ALTER TABLE `tbl_billing` DISABLE KEYS */;
INSERT INTO `tbl_billing` VALUES (1,'BILL-2026-000001',1,16,14,'2026-08-15','Trucking Service','PER_TRIP',15000.00,1.00,15000.00,0.00,1800.00,500.00,17300.00,'INVOICED',NULL,'2026-09-23 09:58:56','ADMIN','2026-09-24 11:32:06'),(2,'BILL-2026-000002',4,17,15,'2026-09-23','','PER_TRIP',50000.00,1.00,50000.00,0.00,6000.00,7000.00,63000.00,'INVOICED','','2026-09-23 10:01:27','admin','2026-09-23 10:03:18'),(8,'BILL-2026-000003',5,23,21,'2026-09-21','Trucking','PER_TRIP',15000.00,1.00,15000.00,0.00,1800.00,0.00,16800.00,'INVOICED','E2E billing scenario 1','2026-09-24 08:05:23','ADMIN','2026-09-24 08:05:23'),(9,'BILL-2026-000004',1,24,22,'2026-09-22','Trucking','PER_TRIP',22000.00,1.00,22000.00,0.00,2640.00,500.00,25140.00,'INVOICED','E2E billing scenario 2','2026-09-24 08:05:27','ADMIN','2026-09-24 08:05:28'),(10,'BILL-2026-000005',9,25,23,'2026-09-20','Trucking','PER_TRIP',18000.00,1.00,18000.00,0.00,2160.00,0.00,20160.00,'INVOICED','E2E billing scenario 3','2026-09-24 08:05:32','ADMIN','2026-09-24 08:05:33'),(11,'BILL-2026-000006',8,26,24,'2026-09-17','Trucking','PER_TRIP',35000.00,1.00,35000.00,0.00,4200.00,0.00,39200.00,'INVOICED','E2E billing scenario 4','2026-09-24 08:05:38','ADMIN','2026-09-24 08:05:38'),(18,'BILL-2026-000008',1,31,26,'2026-09-24','Trucking Service','PER_TRIP',50000.00,1.00,50000.00,0.00,6000.00,0.00,56000.00,'INVOICED','','2026-09-24 11:54:14','admin','2026-09-24 12:02:22'),(20,'BILL-2026-000009',1,33,27,'2026-09-25','Trucking Service','PER_TRIP',50000.00,1.00,50000.00,0.00,6000.00,1500.00,57500.00,'INVOICED','','2026-09-25 11:42:35','admin','2026-09-25 11:43:20'),(21,'BILL-2026-000010',1,34,28,'2026-09-25','Trucking Service','PER_TRIP',50000.00,1.00,50000.00,0.00,6000.00,1500.00,57500.00,'INVOICED','','2026-09-25 15:06:13','admin','2026-09-25 15:07:32'),(22,'BILL-2026-000011',1,41,29,'2026-10-05','Trucking Service','PER_TRIP',18500.00,1.00,18500.00,0.00,2220.00,1490.00,22210.00,'INVOICED','Laguna → Manila, 10-wheeler','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(23,'BILL-2026-000012',2,44,31,'2026-10-05','Trucking Service','PER_TRIP',9800.00,1.00,9800.00,0.00,1176.00,320.00,11296.00,'INVOICED',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_billing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_billing_charges`
--

DROP TABLE IF EXISTS `tbl_billing_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_billing_charges` (
  `charge_id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_id` int(11) NOT NULL,
  `charge_type` enum('TOLL_FEES','WAITING_TIME','DETENTION','EXTRA_STOP','HANDLING','ADDITIONAL_KILOMETER','OTHER') NOT NULL DEFAULT 'OTHER',
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  PRIMARY KEY (`charge_id`),
  KEY `idx_billing_id` (`billing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_billing_charges`
--

LOCK TABLES `tbl_billing_charges` WRITE;
/*!40000 ALTER TABLE `tbl_billing_charges` DISABLE KEYS */;
INSERT INTO `tbl_billing_charges` VALUES (1,1,'HANDLING','Loading/unloading',500.00,NULL,'2026-09-23 09:59:01','ADMIN'),(2,2,'TOLL_FEES','test',5000.00,NULL,'2026-09-23 10:01:38','admin'),(3,2,'WAITING_TIME','asdas',2000.00,NULL,'2026-09-23 10:01:43','admin'),(5,9,'TOLL_FEES','NLEX Toll',500.00,NULL,'2026-09-24 08:05:28','ADMIN'),(11,20,'TOLL_FEES','NLEX',1500.00,NULL,'2026-09-25 11:42:55','admin'),(12,21,'TOLL_FEES','NLEX',1500.00,NULL,'2026-09-25 15:06:29','admin'),(13,22,'TOLL_FEES','SLEX + Skyway toll (round trip)',690.00,NULL,'2026-10-05 11:43:18','ADMIN'),(14,22,'HANDLING','Pallet handling at Dock 3',800.00,NULL,'2026-10-05 11:43:18','ADMIN'),(15,23,'TOLL_FEES','C5 / NLEX toll',320.00,NULL,'2026-10-05 16:41:11','ADMIN');
/*!40000 ALTER TABLE `tbl_billing_charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_customer_adjustments`
--

DROP TABLE IF EXISTS `tbl_customer_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_customer_adjustments` (
  `adjustment_id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_code` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `adjustment_date` date NOT NULL,
  `adjustment_type` enum('DEBIT','CREDIT') NOT NULL DEFAULT 'DEBIT',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reason` varchar(255) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`adjustment_id`),
  UNIQUE KEY `adjustment_code` (`adjustment_code`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_adjustment_date` (`adjustment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_customer_adjustments`
--

LOCK TABLES `tbl_customer_adjustments` WRITE;
/*!40000 ALTER TABLE `tbl_customer_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_customer_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_customer_locations`
--

DROP TABLE IF EXISTS `tbl_customer_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_customer_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `location_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`location_id`),
  KEY `idx_customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_customer_locations`
--

LOCK TABLES `tbl_customer_locations` WRITE;
/*!40000 ALTER TABLE `tbl_customer_locations` DISABLE KEYS */;
INSERT INTO `tbl_customer_locations` VALUES (1,1,'Main Plant','123 Industrial Ave.','Laguna','Laguna','Maria Santos','0917-123-4567','Main manufacturing facility','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(2,1,'Warehouse 1','456 Storage Rd.','Laguna','Laguna','Jose Reyes','0917-765-4321','Finished goods storage','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(3,1,'Distribution Center','789 Logistics Ave.','Manila','Metro Manila','Ana Cruz','0917-987-6543','Main distribution hub','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(4,2,'Head Office','456 Commercial St.','Manila','Metro Manila','Juan Dela Cruz','0918-234-5678','Main office','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(5,2,'Warehouse 2','789 Cargo St.','Pasig','Metro Manila','Pedro Santos','0918-876-5432','Main warehouse','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(6,3,'Davao Farm','789 Davao City','Davao City','Davao','Pedro Reyes','0919-345-6789','Main farm location','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(7,3,'Davao Packing Plant','101 Packing Rd.','Davao City','Davao','Luz Reyes','0919-987-6543','Packing and processing','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(8,3,'GenSan Distribution','202 GenSan Blvd.','General Santos','South Cotabato','Ramon Cruz','0919-654-3210','Distribution to Mindanao','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(9,4,'Cebu Main Store','101 Cebu City','Cebu City','Cebu','Anna Lopez','0920-456-7890','Main retail store','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(10,4,'Cebu Warehouse','303 Storage St.','Mandaue City','Cebu','Carlos Lopez','0920-987-6543','Inventory storage','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(11,5,'Pasig Operations','202 Pasig City','Pasig City','Metro Manila','Ramon Cruz','0921-567-8901','Main operations center','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(12,5,'Manila Port Office','404 Port Area','Manila','Metro Manila','Elena Rivera','0921-654-3210','Port operations','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(13,6,'Pampanga Plant','303 Pampanga','San Fernando','Pampanga','Elena Rivera','0922-678-9012','Food processing plant','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(14,6,'Pampanga Warehouse','505 Storage Rd.','Mabalacat','Pampanga','Dante Cruz','0922-987-6543','Raw materials storage','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(15,7,'Iloilo Terminal','404 Iloilo City','Iloilo City','Iloilo','Mark Tan','0923-789-0123','Main shipping terminal','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(16,7,'Cebu Port Office','606 Port Area','Cebu City','Cebu','Grace Tan','0923-654-3210','Cebu operations','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(17,8,'Surigao Mine','505 Surigao','Surigao City','Surigao del Norte','Grace Santos','0924-890-1234','Main mining site','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(18,8,'Davao Processing Plant','707 Davao City','Davao City','Davao','Ramon Santos','0924-987-6543','Mineral processing','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(19,9,'Laguna Plant','606 Laguna','Santa Rosa','Laguna','Francisco Garcia','0925-901-2345','Auto parts manufacturing','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(20,9,'Manila Showroom','808 Manila Blvd.','Manila','Metro Manila','Teresita Garcia','0925-654-3210','Showroom and retail','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(21,10,'Quezon City Office','707 Quezon City','Quezon City','Metro Manila','Leticia Mendez','0926-012-3456','Main office','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(22,10,'Bulacan Yard','909 Bulacan','Malolos','Bulacan','Antonio Mendez','0926-987-6543','Equipment and materials yard','ACTIVE','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53');
/*!40000 ALTER TABLE `tbl_customer_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_customers`
--

DROP TABLE IF EXISTS `tbl_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `customer_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_code` (`customer_code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_customers`
--

LOCK TABLES `tbl_customers` WRITE;
/*!40000 ALTER TABLE `tbl_customers` DISABLE KEYS */;
INSERT INTO `tbl_customers` VALUES (1,'CUST-2026-0001','ABC Manufacturing Corp','Corporate','123-456-789-000','Maria Santos','Procurement Manager','0917-123-4567','maria@abcmfg.com','123 Industrial Ave., Laguna','123 Industrial Ave., Laguna','30 Days',500000.00,'ACTIVE','Major manufacturer of consumer goods','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(2,'CUST-2026-0002','XYZ Trading Inc.','Corporate','234-567-890-001','Juan Dela Cruz','Operations Director','0918-234-5678','juan@xyztrading.com','456 Commercial St., Manila','456 Commercial St., Manila','15 Days',300000.00,'ACTIVE','Trading company for construction materials','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(3,'CUST-2026-0003','Davao Agricultural Corp','Corporate','345-678-901-002','Pedro Reyes','Farm Manager','0919-345-6789','pedro@davaoagri.com','789 Davao City, Davao','789 Davao City, Davao','45 Days',200000.00,'ACTIVE','Agricultural products supplier','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(4,'CUST-2026-0004','Cebu Retail Group','Regular','456-789-012-003','Anna Lopez','Store Manager','0920-456-7890','anna@ceburetail.com','101 Cebu City, Cebu','101 Cebu City, Cebu','COD',50000.00,'ACTIVE','Retail chain for consumer goods','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(5,'CUST-2026-0005','Manila Logistics Hub','Corporate','567-890-123-004','Ramon Cruz','Logistics Manager','0921-567-8901','ramon@manilalogistics.com','202 Pasig City, Metro Manila','202 Pasig City, Metro Manila','30 Days',400000.00,'ACTIVE','Third-party logistics provider','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(6,'CUST-2026-0006','Pampanga Food Products','Corporate','678-901-234-005','Elena Rivera','Production Manager','0922-678-9012','elena@pampangafoods.com','303 Pampanga, Central Luzon','303 Pampanga, Central Luzon','60 Days',250000.00,'ACTIVE','Food processing and distribution','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(7,'CUST-2026-0007','Visayas Shipping Lines','Corporate','789-012-345-006','Mark Tan','Fleet Manager','0923-789-0123','mark@visayasshipping.com','404 Iloilo City, Iloilo','404 Iloilo City, Iloilo','30 Days',600000.00,'ACTIVE','Shipping and cargo forwarding','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(8,'CUST-2026-0008','Mindanao Mining Corp','Corporate','890-123-456-007','Grace Santos','Supply Chain Head','0924-890-1234','grace@mindanaomining.com','505 Surigao, Caraga Region','505 Surigao, Caraga Region','45 Days',800000.00,'ACTIVE','Mining and mineral processing','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(9,'CUST-2026-0009','Laguna Auto Parts Inc.','Regular','901-234-567-008','Francisco Garcia','Purchasing Manager','0925-901-2345','francisco@lagunaautoparts.com','606 Laguna, Calabarzon','606 Laguna, Calabarzon','15 Days',150000.00,'ACTIVE','Automotive parts supplier','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53'),(10,'CUST-2026-0010','NCR Construction Corp','Corporate','012-345-678-009','Leticia Mendez','Project Manager','0926-012-3456','leticia@ncrconstruction.com','707 Quezon City, Metro Manila','707 Quezon City, Metro Manila','30 Days',450000.00,'ACTIVE','Construction and infrastructure development','2026-08-29 22:00:53','ADMIN','2026-08-29 22:00:53');
/*!40000 ALTER TABLE `tbl_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_dashboard_widgets`
--

DROP TABLE IF EXISTS `tbl_dashboard_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_dashboard_widgets` (
  `widget_id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_key` varchar(50) NOT NULL,
  `widget_name` varchar(100) NOT NULL,
  `widget_group` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `default_visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `widget_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`widget_id`),
  UNIQUE KEY `idx_widget_key` (`widget_key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_dashboard_widgets`
--

LOCK TABLES `tbl_dashboard_widgets` WRITE;
/*!40000 ALTER TABLE `tbl_dashboard_widgets` DISABLE KEYS */;
INSERT INTO `tbl_dashboard_widgets` VALUES (1,'fleet_stats','Fleet Stats','KPI Cards','Total / available / in-transit / under-maintenance truck counts',1,10,'ACTIVE','2026-09-24 17:23:10'),(2,'personnel_stats','Personnel Stats','KPI Cards','Driver and helper totals and availability',1,20,'ACTIVE','2026-09-24 17:23:10'),(3,'trip_stats','Trip Stats','KPI Cards','Trips today, pending dispatch, active and completed trips',1,30,'ACTIVE','2026-09-24 17:23:10'),(4,'billing_stats','Billing & Deliveries Stats','KPI Cards','Pending deliveries/DRs/billing and outstanding receivables',1,40,'ACTIVE','2026-09-24 17:23:10'),(5,'fuel_chart','Monthly Fuel Expense','Analytics','Fuel expense per month for the selected year',1,50,'ACTIVE','2026-09-24 17:23:10'),(6,'fuel_efficiency','Fuel Efficiency','Analytics','Cost per km, consumption rate, and fuel usage by truck',1,60,'ACTIVE','2026-09-24 17:23:10'),(7,'route_analysis','Most Frequent Routes','Analytics','Top origin-destination pairs by trip count',1,70,'ACTIVE','2026-09-24 17:23:10'),(8,'service_analysis','Service Type Analysis','Analytics','Trips and revenue grouped by trip service type',1,80,'ACTIVE','2026-09-24 17:23:10'),(9,'customer_activity','Customer Activity & Performance','Analytics','Top customers by billed amount, with collections and balances',1,90,'ACTIVE','2026-09-24 17:23:10'),(10,'dashboard_insights','Dashboard Insights','Analytics','Highlight callouts summarizing the widgets above',1,100,'ACTIVE','2026-09-24 17:23:10');
/*!40000 ALTER TABLE `tbl_dashboard_widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_delivery_receipt_items`
--

DROP TABLE IF EXISTS `tbl_delivery_receipt_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_delivery_receipt_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`item_id`),
  KEY `idx_dr_id` (`dr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_delivery_receipt_items`
--

LOCK TABLES `tbl_delivery_receipt_items` WRITE;
/*!40000 ALTER TABLE `tbl_delivery_receipt_items` DISABLE KEYS */;
INSERT INTO `tbl_delivery_receipt_items` VALUES (16,14,'cargo test item',50.00,50.00,0.00,0.00,'cartoons',500.00,'GOOD','','2026-09-23 08:04:29','admin','2026-09-23 08:04:29'),(17,15,'test',50.00,50.00,0.00,0.00,'cartons',50.00,'GOOD','test','2026-09-23 08:56:31','admin','2026-09-23 08:56:31'),(23,21,'General Merchandise',10.00,10.00,0.00,0.00,'pallets',5000.00,'GOOD',NULL,'2026-09-24 08:05:22','ADMIN','2026-09-24 08:05:22'),(24,22,'Raw Materials',20.00,20.00,0.00,0.00,'bags',8000.00,'GOOD',NULL,'2026-09-24 08:05:26','ADMIN','2026-09-24 08:05:26'),(25,23,'Auto Parts',50.00,50.00,0.00,0.00,'boxes',3000.00,'GOOD',NULL,'2026-09-24 08:05:31','ADMIN','2026-09-24 08:05:31'),(26,24,'Mining Equipment Parts',100.00,95.00,5.00,0.00,'units',12000.00,'SHORT','Shortage noted on arrival','2026-09-24 08:05:36','ADMIN','2026-09-24 08:05:37'),(28,26,'LED TV 55\"',50.00,50.00,0.00,0.00,'Pcs',1000.00,'GOOD','','2026-09-24 11:07:38','admin','2026-09-24 11:07:38'),(29,26,'LED TV 43\"',40.00,40.00,0.00,0.00,'Pcs',600.00,'GOOD','','2026-09-24 11:07:38','admin','2026-09-24 11:07:38'),(30,26,'Sound Bar',60.00,60.00,0.00,0.00,'Pcs',899.97,'GOOD','','2026-09-24 11:07:38','admin','2026-09-24 11:07:38'),(31,27,'DUMBELL',25.00,25.00,0.00,0.00,'PCS',1500.00,'GOOD','','2026-09-25 11:39:35','admin','2026-09-25 11:39:35'),(32,27,'CHEST RACK',25.00,25.00,0.00,0.00,'PCS',1000.00,'GOOD','','2026-09-25 11:39:35','admin','2026-09-25 11:39:35'),(33,28,'DUMBELL',25.00,25.00,0.00,0.00,'PCS',1500.00,'GOOD','','2026-09-25 14:37:47','admin','2026-09-25 14:37:47'),(34,28,'CHEST RACK',25.00,25.00,0.00,0.00,'PCS',1000.00,'GOOD','','2026-09-25 14:37:47','admin','2026-09-25 14:37:47'),(35,29,'Bottled Water 500ml (24s) — pallets',16.00,16.00,0.00,0.00,'Pallets',6400.00,'GOOD',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(36,29,'Canned Goods Assorted — pallets',8.00,8.00,0.00,0.00,'Pallets',3200.00,'GOOD',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(37,31,'Bond paper (boxes) — pallets',8.00,8.00,0.00,0.00,'Pallets',2800.00,'GOOD',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(38,31,'Water dispensers — pallets',4.00,4.00,0.00,0.00,'Pallets',1400.00,'GOOD',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_delivery_receipt_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_delivery_receipt_pod`
--

DROP TABLE IF EXISTS `tbl_delivery_receipt_pod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_delivery_receipt_pod` (
  `pod_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`pod_id`),
  KEY `idx_dr_id` (`dr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_delivery_receipt_pod`
--

LOCK TABLES `tbl_delivery_receipt_pod` WRITE;
/*!40000 ALTER TABLE `tbl_delivery_receipt_pod` DISABLE KEYS */;
INSERT INTO `tbl_delivery_receipt_pod` VALUES (11,14,'JAMIE','WAREHOUSE SUPERVISOR','2026-09-23','08:05:00','50','GOOD','uploads/delivery_receipts/DR_14_SIGNATURE_1790121921.png','uploads/delivery_receipts/DR_14_DELIVERY_PHOTO_1790121929.jpg','uploads/delivery_receipts/DR_14_SIGNED_DR_1790121936.pdf',NULL,'','2026-09-23 08:05:21','admin','2026-09-23 08:05:44'),(12,15,'JAMIE','WAREHOUSE SUPERVISOR','2026-09-23','08:57:00','50','GOOD','uploads/delivery_receipts/DR_15_SIGNATURE_1790125072.png','uploads/delivery_receipts/DR_15_DELIVERY_PHOTO_1790125075.jpg','uploads/delivery_receipts/DR_15_SIGNED_DR_1790125079.pdf','uploads/delivery_receipts/DR_15_SUPPORTING_DOCUMENTS_1790125085.pdf','','2026-09-23 08:57:52','admin','2026-09-23 08:58:07'),(13,26,'JAMIE','WAREHOUSE SUPERVISOR','2026-09-24','11:12:00','150','GOOD','uploads/delivery_receipts/DR_26_SIGNATURE_1790219563.png','uploads/delivery_receipts/DR_26_DELIVERY_PHOTO_1790219570.jpg','uploads/delivery_receipts/DR_26_SIGNED_DR_1790219575.pdf',NULL,'TEST','2026-09-24 11:12:43','admin','2026-09-24 11:12:59'),(14,27,'SIR OLI','WAREHOUSE SUPERVISOR','2026-09-25','04:40:00','50','GOOD','uploads/delivery_receipts/DR_27_SIGNATURE_1790316810.png','uploads/delivery_receipts/DR_27_DELIVERY_PHOTO_1790307688.jpg','uploads/delivery_receipts/DR_27_SIGNED_DR_1790307700.png',NULL,'','2026-09-25 11:41:15','admin','2026-09-25 14:13:30'),(15,28,'JAMIE','WAREHOUSE SUPERVISOR','2026-09-25','18:00:00','50','GOOD','uploads/delivery_receipts/DR_28_SIGNATURE_1790319682.png','uploads/delivery_receipts/DR_28_DELIVERY_PHOTO_1790319702.png','uploads/delivery_receipts/DR_28_SIGNED_DR_1790319709.jpg',NULL,'','2026-09-25 15:01:22','admin','2026-09-25 15:01:54'),(16,29,'Liza Mendoza','Receiving Supervisor','2026-10-04','12:40:00','24 pallets','GOOD',NULL,NULL,NULL,NULL,'Received complete, seals intact','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(17,31,'R. Garcia','Admin Officer','2026-10-05','09:40:00','12 pallets','GOOD','uploads/delivery_receipts/DR_31_SIGNATURE_1791189671.png','uploads/delivery_receipts/DR_31_DELIVERY_PHOTO_1791189671.png',NULL,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_delivery_receipt_pod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_delivery_receipts`
--

DROP TABLE IF EXISTS `tbl_delivery_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_delivery_receipts` (
  `dr_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dr_id`),
  UNIQUE KEY `idx_dr_code` (`dr_code`),
  KEY `idx_trip_id` (`trip_id`),
  KEY `idx_dispatch_id` (`dispatch_id`),
  KEY `idx_customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_delivery_receipts`
--

LOCK TABLES `tbl_delivery_receipts` WRITE;
/*!40000 ALTER TABLE `tbl_delivery_receipts` DISABLE KEYS */;
INSERT INTO `tbl_delivery_receipts` VALUES (14,'DR-2026-000001',16,12,1,'2026-09-23','08:04:00','ABC-1234','Anna Lopez','Andres Bonifacio','MANILA','CAVITE',0,NULL,NULL,NULL,'DELIVERED','','2026-09-23 08:04:29','admin','2026-09-23 08:05:52'),(15,'DR-2026-000002',17,13,4,'2026-09-23','08:56:00','TRC-001 + CHS-001','Anna Lopez','Andres Bonifacio','CAVITE','TAGUIG',0,NULL,NULL,NULL,'DELIVERED','test','2026-09-23 08:56:31','admin','2026-09-23 08:57:37'),(21,'DR-2026-000003',23,19,5,'2026-09-21','14:00:00','XYZ-5678','Juan Dela Cruz','KYLE HELPER','Manila','Batangas',0,'',NULL,NULL,'DELIVERED','Delivered','2026-09-24 08:05:22','ADMIN','2026-09-24 08:05:22'),(22,'DR-2026-000004',24,20,1,'2026-09-22','14:00:00','GHI-3456','Maria Santos','Andres Bonifacio','Manila','Pampanga',0,'',NULL,NULL,'DELIVERED','Delivered','2026-09-24 08:05:26','ADMIN','2026-09-24 08:05:27'),(23,'DR-2026-000005',25,21,9,'2026-09-20','14:00:00','ABC-1234','Pedro Reyes','Emilio Aguinaldo','Manila','Laguna',0,'',NULL,NULL,'DELIVERED','Delivered','2026-09-24 08:05:31','ADMIN','2026-09-24 08:05:32'),(24,'DR-2026-000006',26,22,8,'2026-09-17','14:00:00','TRC-003 + CHS-003','Anna Lopez','Jose Rizal','Davao Port','Mining Site',1,'CONT-2026-0004',NULL,NULL,'PARTIALLY_DELIVERED','Delivered','2026-09-24 08:05:36','ADMIN','2026-09-24 08:05:37'),(26,'DR-2026-000008',31,24,1,'2026-09-24','11:07:00','TRC-001 + CHS-001','Anna Lopez','Andres Bonifacio','MANILA','TAGUIG',0,NULL,NULL,NULL,'DELIVERED','','2026-09-24 11:07:38','admin','2026-09-24 11:13:06'),(27,'DR-2026-000009',33,26,1,'2026-09-25','04:39:00','TRC-001 + CHS-001','Anna Lopez','Andres Bonifacio','MANILA','CAVITE',0,NULL,NULL,NULL,'DELIVERED','','2026-09-25 11:39:35','admin','2026-09-25 11:42:19'),(28,'DR-2026-000010',34,33,1,'2026-09-25','17:36:00','TRC-002 + CHS-002','Elena Rivera','Antonio Luna','MANILA','CAVITE',0,NULL,NULL,NULL,'DELIVERED','','2026-09-25 14:37:47','admin','2026-09-25 15:04:17'),(29,'DR-2026-000011',41,36,1,'2026-10-04','12:45:00','DEF-9012','Maria Santos','Jose Rizal','ABC Manufacturing Main Plant, Calamba, Laguna','ABC Distribution Center, Tondo, Manila',0,NULL,NULL,NULL,'DELIVERED','Delivered complete and in good condition','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(31,'DR-2026-000012',44,38,2,'2026-10-05','09:45:00','XYZ-5678','Pedro Reyes','Emilio Aguinaldo','XYZ Trading Warehouse 2, Pasig City','XYZ Trading Head Office, Manila',0,NULL,NULL,NULL,'DELIVERED',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_delivery_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_dispatch`
--

DROP TABLE IF EXISTS `tbl_dispatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_dispatch` (
  `dispatch_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `actual_delivery_date` date DEFAULT NULL,
  `actual_delivery_time` time DEFAULT NULL,
  `odometer_in` decimal(15,2) DEFAULT 0.00,
  `fuel_level_in` decimal(5,2) DEFAULT 0.00,
  `total_distance` decimal(15,2) DEFAULT 0.00,
  `fuel_consumed` decimal(15,2) DEFAULT 0.00,
  `delay_reason` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dispatch_id`),
  UNIQUE KEY `idx_dispatch_code` (`dispatch_code`),
  KEY `idx_trip_id` (`trip_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_dispatch`
--

LOCK TABLES `tbl_dispatch` WRITE;
/*!40000 ALTER TABLE `tbl_dispatch` DISABLE KEYS */;
INSERT INTO `tbl_dispatch` VALUES (19,'DSP-2026-000003',23,'2026-09-20','08:00:00','XYZ-5678','Juan Dela Cruz','KYLE HELPER','Manila','Batangas',10000.00,100.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0.00,'NOT_APPLICABLE',NULL,'ADMIN','2026-09-21','14:00:00',0.00,0.00,0.00,0.00,NULL,NULL,'2026-09-24 08:05:21','ADMIN','2026-09-24 08:05:22'),(20,'DSP-2026-000004',24,'2026-09-21','08:00:00','GHI-3456','Maria Santos','Andres Bonifacio','Manila','Pampanga',10000.00,100.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0.00,'NOT_APPLICABLE',NULL,'ADMIN','2026-09-22','14:00:00',0.00,0.00,0.00,0.00,NULL,NULL,'2026-09-24 08:05:26','ADMIN','2026-09-24 08:05:27'),(21,'DSP-2026-000005',25,'2026-09-19','08:00:00','ABC-1234','Pedro Reyes','Emilio Aguinaldo','Manila','Laguna',10000.00,100.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0.00,'NOT_APPLICABLE',NULL,'ADMIN','2026-09-20','14:00:00',0.00,0.00,0.00,0.00,NULL,NULL,'2026-09-24 08:05:31','ADMIN','2026-09-24 08:05:32'),(22,'DSP-2026-000006',26,'2026-09-15','08:00:00','TRC-003 + CHS-003','Anna Lopez','Jose Rizal','Davao Port','Mining Site',10000.00,100.00,1,'CONT-2026-0004','20FT',NULL,NULL,'REF-4',NULL,NULL,NULL,1,'2026-09-18','10:00:00',NULL,0.00,'RETURNED',NULL,'ADMIN','2026-09-18','10:00:00',0.00,0.00,0.00,0.00,NULL,NULL,'2026-09-24 08:05:36','ADMIN','2026-09-24 08:05:39'),(24,'DSP-2026-000008',31,'2026-09-24','06:00:00','TRC-001 + CHS-001','Anna Lopez','Andres Bonifacio','MANILA','TAGUIG',80000.00,50.00,1,'ABC1231','40ft dry','test','ABC','CONT-S1231','Manila Port','2026-09-24','11:06:00',1,'2026-09-24','11:11:00','Manila Port',50000.00,'RETURNED','asdasasd','','2026-09-24','13:05:00',0.00,0.00,0.00,0.00,'','','2026-09-24 11:06:49','admin','2026-09-24 13:05:59'),(26,'DSP-2026-000009',33,'2026-09-25','06:00:00','TRC-001 + CHS-001','Anna Lopez','Andres Bonifacio','MANILA','CAVITE',50000.00,50.00,1,'ACNADA','40FT DRY','TEST','123','CONT-1231231','MANILA PORT','2026-09-25','02:34:00',1,'2026-09-25','11:47:00','MANILA PORT',55000.00,'RETURNED','','','2026-09-25','11:47:00',60000.00,30.00,10000.00,20.00,'','','2026-09-25 11:35:12','admin','2026-09-25 11:47:15'),(33,'DSP-2026-000010',34,'2026-09-25','06:00:00','TRC-002 + CHS-002','Elena Rivera','Antonio Luna','MANILA','CAVITE',50000.00,50.00,1,'ASDASD','40FT DRY','TEST','ASD','ASD','MANILA PORT','2026-09-25','16:34:00',1,'2026-09-25','15:05:00','MANILA PORT',0.00,'RETURNED','','KYLE ALINO','2026-09-25','15:05:00',0.00,0.00,0.00,0.00,'','','2026-09-25 14:35:01','admin','2026-10-05 11:20:54'),(36,'DSP-2026-000011',41,'2026-10-04','06:00:00','DEF-9012','Maria Santos','Jose Rizal','ABC Manufacturing Main Plant, Calamba, Laguna','ABC Distribution Center, Tondo, Manila',12500.00,95.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0.00,'NOT_APPLICABLE',NULL,'ADMIN','2026-10-04','12:45:00',12738.00,55.00,238.00,60.00,NULL,'Completed. 238 km round trip.','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(38,'DSP-2026-000012',44,'2026-10-05','06:00:00','XYZ-5678','Pedro Reyes','Emilio Aguinaldo','XYZ Trading Warehouse 2, Pasig City','XYZ Trading Head Office, Manila',78450.00,90.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0.00,'NOT_APPLICABLE',NULL,'ADMIN','2026-10-05','09:45:00',78512.00,70.00,62.00,14.00,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_dispatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_dispatch_checklist`
--

DROP TABLE IF EXISTS `tbl_dispatch_checklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_dispatch_checklist` (
  `checklist_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`checklist_id`),
  UNIQUE KEY `checklist_code` (`checklist_code`),
  KEY `idx_dispatch_id` (`dispatch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_dispatch_checklist`
--

LOCK TABLES `tbl_dispatch_checklist` WRITE;
/*!40000 ALTER TABLE `tbl_dispatch_checklist` DISABLE KEYS */;
INSERT INTO `tbl_dispatch_checklist` VALUES (4,'CHK-2026-000001',36,41,'Early Warning Device',1,1,'GOOD',1,'Maria Santos','COMPLETE',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(5,'CHK-2026-000002',36,41,'Fire Extinguisher',1,1,'GOOD',1,'Maria Santos','COMPLETE',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(6,'CHK-2026-000003',36,41,'Wheel Chocks',2,2,'GOOD',1,'Maria Santos','COMPLETE',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(7,'CHK-2026-000004',36,41,'Tarpaulin',2,2,'GOOD',1,'Maria Santos','COMPLETE',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(8,'CHK-2026-000005',38,44,'Early Warning Device',1,1,'GOOD',1,'Pedro Reyes','COMPLETE',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(9,'CHK-2026-000006',38,44,'Fire Extinguisher',1,1,'GOOD',1,'Pedro Reyes','COMPLETE',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(10,'CHK-2026-000007',38,44,'Wheel Chocks',1,1,'GOOD',1,'Pedro Reyes','COMPLETE',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_dispatch_checklist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_dispatch_expenses`
--

DROP TABLE IF EXISTS `tbl_dispatch_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_dispatch_expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_code` varchar(50) NOT NULL,
  `dispatch_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `expense_type` enum('TOLL','PARKING','FUEL','MEALS','LOADING_UNLOADING','CONTAINER_RENTAL','EMPTY_RETURN_FEE','CONTAINER_RETURN_FEE','OTHER') DEFAULT 'OTHER',
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_by` varchar(100) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `receipt_attachment` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`expense_id`),
  UNIQUE KEY `expense_code` (`expense_code`),
  KEY `idx_dispatch_id` (`dispatch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_dispatch_expenses`
--

LOCK TABLES `tbl_dispatch_expenses` WRITE;
/*!40000 ALTER TABLE `tbl_dispatch_expenses` DISABLE KEYS */;
INSERT INTO `tbl_dispatch_expenses` VALUES (3,'EXP-2026-000001',8,7,'2026-09-04','FUEL','test',5000.00,'driver','10231',NULL,'','2026-09-04 15:15:57','ADMIN','2026-09-04 15:15:57'),(4,'EXP-2026-000002',24,31,'2026-09-24','PARKING','test',1000.00,'driver','',NULL,'','2026-09-24 11:15:30','admin','2026-09-24 11:15:30'),(5,'EXP-2026-000003',36,41,'2026-10-04','TOLL','SLEX + Skyway toll (round trip)',690.00,'Maria Santos','RFID-77812',NULL,NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(6,'EXP-2026-000004',36,41,'2026-10-04','FUEL','Diesel 60L @ ₱62.50',3750.00,'Maria Santos','OR-558120',NULL,NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(7,'EXP-2026-000005',36,41,'2026-10-04','MEALS','Crew meals',400.00,'Maria Santos','',NULL,NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(8,'EXP-2026-000006',38,44,'2026-10-05','TOLL','C5 / NLEX toll',320.00,'Pedro Reyes',NULL,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(9,'EXP-2026-000007',38,44,'2026-10-05','FUEL','Diesel 40L',2500.00,'Pedro Reyes',NULL,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_dispatch_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_dispatch_inspections`
--

DROP TABLE IF EXISTS `tbl_dispatch_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_dispatch_inspections` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_dispatch_inspections`
--

LOCK TABLES `tbl_dispatch_inspections` WRITE;
/*!40000 ALTER TABLE `tbl_dispatch_inspections` DISABLE KEYS */;
INSERT INTO `tbl_dispatch_inspections` VALUES (2,'INS-2026-000001',38,44,'2026-10-05','05:40:00','XYZ-5678','A. Moquia','SAFE','Right side upper bumper damage; scratch on right side front bumper (cosmetic)','uploads/dispatch_inspections/INSP_38_FORM_1791189671.png',14,'Pedro Reyes','uploads/dispatch_inspections/INSP_38_DRIVER_SIGNATURE_1791189671.png','2026-10-05 16:41:11','uploads/dispatch_inspections/INSP_38_INSPECTOR_SIGNATURE_1791189671.png','2026-10-05 16:41:11','Cleared for operation','2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_dispatch_inspections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_driver_skillsets`
--

DROP TABLE IF EXISTS `tbl_driver_skillsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_driver_skillsets` (
  `skillset_id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `rating_date` date DEFAULT NULL,
  `evaluated_by` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`skillset_id`),
  KEY `idx_driver_id` (`driver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_driver_skillsets`
--

LOCK TABLES `tbl_driver_skillsets` WRITE;
/*!40000 ALTER TABLE `tbl_driver_skillsets` DISABLE KEYS */;
INSERT INTO `tbl_driver_skillsets` VALUES (3,21,'City / Urban Driving',5,'2026-08-29','JOAN','TEST','2026-08-29 22:50:06','ADMIN','2026-08-29 22:50:06'),(4,19,'10-Wheeler Operation',3,'2026-09-23','kyle','test','2026-09-23 10:48:43','admin','2026-09-23 10:48:43'),(5,15,'Heavy Truck Driving',5,'2026-09-25','','','2026-09-25 14:57:21','admin','2026-09-25 14:57:21'),(6,15,'Long-Distance Driving',4,'2026-09-25','KYLE','','2026-09-25 14:57:37','admin','2026-09-25 14:57:37');
/*!40000 ALTER TABLE `tbl_driver_skillsets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_drivers`
--

DROP TABLE IF EXISTS `tbl_drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_drivers` (
  `driver_id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_code` varchar(50) NOT NULL,
  `driver_name` varchar(200) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `driver_status` enum('AVAILABLE','ASSIGNED','ON_TRIP','RETURNING','ON_LEAVE','SUSPENDED','INACTIVE') NOT NULL DEFAULT 'AVAILABLE',
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `years_experience` int(11) DEFAULT 0,
  `heavy_vehicle_experience` int(11) DEFAULT 0,
  `tractor_head_experience` int(11) DEFAULT 0,
  `ten_wheeler_experience` int(11) DEFAULT 0,
  `long_distance_experience` tinyint(1) NOT NULL DEFAULT 0,
  `city_urban_experience` tinyint(1) NOT NULL DEFAULT 0,
  `highway_experience` tinyint(1) NOT NULL DEFAULT 0,
  `route_experience` varchar(255) DEFAULT NULL,
  `cargo_handling_experience` varchar(100) DEFAULT NULL,
  `defensive_driving_training` tinyint(1) NOT NULL DEFAULT 0,
  `safety_training` tinyint(1) NOT NULL DEFAULT 0,
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
  `license_attachment` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`driver_id`),
  UNIQUE KEY `idx_driver_code` (`driver_code`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_drivers`
--

LOCK TABLES `tbl_drivers` WRITE;
/*!40000 ALTER TABLE `tbl_drivers` DISABLE KEYS */;
INSERT INTO `tbl_drivers` VALUES (12,'DRV-2026-0001','Juan Dela Cruz','0917-123-4567','123 Mabini St., Manila','null','2020-01-15','AVAILABLE','Maria Dela Cruz','0917-765-4321','uploads/drivers/DRV_12_1788012288.jpg',15,10,5,8,1,1,1,'Manila-Laguna, Cavite','General Cargo',1,1,'ISO 9001 Certified','2025-12-31','Experienced driver with excellent record',4.8,'2025-01-15','Operations Manager','Highly recommended',4.5,'L-12345678','Professional','B,C,D,E','2020-01-15','2027-01-15',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30'),(13,'DRV-2026-0002','Maria Santos','0917-234-5678','456 Rizal Ave., Cebu','REGULAR','2021-03-20','AVAILABLE','Jose Santos','0917-876-5432',NULL,12,8,3,6,1,1,1,'Cebu-Davao, Bacolod','Heavy Equipment',1,1,'Forklift Certified','2026-06-30','Professional driver with heavy equipment experience',4.5,'2025-01-20','Fleet Manager','Good performance',4.2,'L-23456789','Professional','B,C,D','2021-03-20','2028-03-20',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 11:43:18'),(14,'DRV-2026-0003','Pedro Reyes','0917-345-6789','789 Bonifacio St., Davao','CONTRACTUAL','2022-06-01','AVAILABLE','Luz Reyes','0917-987-6543',NULL,8,5,2,4,0,1,1,'Davao-GenSan, Cotabato','Agricultural Products',1,1,NULL,'2025-09-30','Reliable driver with good local knowledge',4.2,'2025-01-25','Operations Supervisor','Satisfactory',4.0,'L-34567890','Professional','B,C','2022-06-01','2029-06-01',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 16:41:11'),(15,'DRV-2026-0004','Anna Lopez','0917-456-7890','101 Luna St., Pampanga','null','2019-11-01','AVAILABLE','Carlos Lopez','0917-098-7654',NULL,18,12,8,10,1,1,1,'Pampanga-Manila, Bataan, Zambales','Construction Materials',1,1,'Hazardous Materials Certified','2024-12-31','Highly experienced with excellent safety record',4.5,'2025-01-10','Safety Officer','Top performer',4.7,'L-45678901','Professional','B,C,D,E','2019-11-01','2026-11-01',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30'),(16,'DRV-2026-0005','Ramon Cruz','0917-567-8901','202 Magsaysay Blvd., Cebu','REGULAR','2020-08-15','AVAILABLE','Elena Cruz','0917-109-8765',NULL,10,7,4,5,1,1,1,'Cebu-Leyte, Samar, Bohol','General Merchandise',1,1,'First Aid Certified','2025-10-31','Dependable driver with good customer service',4.4,'2025-02-01','Customer Service Manager','Good feedback from clients',4.1,'L-56789012','Professional','B,C,D','2020-08-15','2027-08-15',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30'),(17,'DRV-2026-0006','Elena Rivera','0917-678-9012','303 Aguinaldo St., Manila','null','2023-01-10','AVAILABLE','Roberto Rivera','0917-210-9876',NULL,6,4,1,3,0,1,1,'Manila-Quezon, Rizal','Retail Goods',1,1,'','2026-03-31','New but competent driver',4.0,'2025-02-05','Fleet Supervisor','Good potential',3.8,'L-67890123','Professional','B,C','2023-01-10','2030-01-10',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30'),(18,'DRV-2026-0007','Mark Tan','0917-789-0123','404 Gomez St., Davao','REGULAR','2021-04-01','AVAILABLE','Grace Tan','0917-321-0987',NULL,14,9,6,7,1,1,1,'Davao-CDO, Butuan, Surigao','Agricultural Products',1,1,'Rigging Certified','2025-08-31','Experienced in Mindanao routes',4.6,'2025-01-30','Regional Manager','Excellent performance',4.3,'L-78901234','Professional','B,C,D,E','2021-04-01','2028-04-01',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30'),(19,'DRV-2026-0008','Grace Santos','0917-890-1234','505 Quezon Ave., Cebu','REGULAR','2020-02-20','AVAILABLE','Daniel Santos','0917-432-1098',NULL,16,11,5,9,1,1,1,'Cebu-Manila, Cebu-Iloilo','Heavy Machinery',1,1,'Crane Operator Certified','2025-11-30','Very reliable with heavy equipment',3.0,'2025-02-10','Operations Director','Highly skilled',4.4,'L-89012345','Professional','B,C,D','2020-02-20','2027-02-20',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30'),(20,'DRV-2026-0009','Francisco Garcia','0917-901-2345','606 Mabuhay St., Manila','null','2022-09-01','AVAILABLE','Teresita Garcia','0917-543-2109',NULL,7,4,2,3,0,1,1,'Manila-Batangas, Laguna','Food Products',1,1,'','2025-07-31','Good driver with positive attitude',4.1,'2025-01-28','Operations Supervisor','Good team player',3.9,'L-90123456','Professional','B,C','2022-09-01','2029-09-01',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30'),(21,'DRV-2026-0010','Leticia Mendez','0917-012-3456','707 Rizal St., Pampanga','Regular','2019-07-01','AVAILABLE','Antonio Mendez','0917-654-3210',NULL,20,15,10,12,1,1,1,'Pampanga-Manila, Tarlac, Nueva Ecija','All Types',1,1,'Master Driver Certification','2024-12-31','Most experienced driver with 20 years',5.0,'2025-01-05','Fleet Manager','Outstanding driver',4.8,'L-01234567','Professional','B,C,D,E','2019-07-01','2026-07-01',NULL,'2026-08-29 21:22:30','ADMIN','2026-10-05 10:58:30');
/*!40000 ALTER TABLE `tbl_drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_helpers`
--

DROP TABLE IF EXISTS `tbl_helpers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_helpers` (
  `helper_id` int(11) NOT NULL AUTO_INCREMENT,
  `helper_code` varchar(50) NOT NULL,
  `helper_name` varchar(200) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `helper_status` enum('AVAILABLE','ASSIGNED','ON_TRIP','ON_LEAVE','INACTIVE') NOT NULL DEFAULT 'AVAILABLE',
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `license_type` varchar(50) DEFAULT NULL,
  `restriction_code` varchar(50) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `license_attachment` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`helper_id`),
  UNIQUE KEY `idx_helper_code` (`helper_code`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_helpers`
--

LOCK TABLES `tbl_helpers` WRITE;
/*!40000 ALTER TABLE `tbl_helpers` DISABLE KEYS */;
INSERT INTO `tbl_helpers` VALUES (1,'HLP-2026-0001','KYLE HELPER','0123120310','sdfsd','Probationary','2026-08-28','AVAILABLE','yrdy','','uploads/helpers/HLP_1_1787901447.jpg','test','Professional','1,2,3,8','2031-06-25','uploads/helper_licenses/HLP_LIC_1_1787901447.jpg','2026-08-28 15:17:27','ADMIN','2026-09-24 13:13:10'),(12,'HLP-2026-0011','Andres Bonifacio','0918-123-4567','123 Tondo St., Manila','null','2020-01-20','AVAILABLE','Gregoria Bonifacio','0918-765-4321',NULL,'','','','0000-00-00',NULL,'2026-08-29 21:22:53','ADMIN','2026-10-05 11:20:54'),(13,'HLP-2026-0002','Emilio Aguinaldo','0918-234-5678','456 Kawit St., Cavite','REGULAR','2021-04-15','AVAILABLE','Hilaria Aguinaldo','0918-876-5432',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-29 21:22:53','ADMIN','2026-10-05 16:41:11'),(14,'HLP-2026-0003','Jose Rizal','0918-345-6789','789 Calamba St., Laguna','null','2020-06-01','AVAILABLE','Teodora Rizal','0918-987-6543',NULL,'','','','0000-00-00',NULL,'2026-08-29 21:22:53','ADMIN','2026-10-05 11:43:18'),(15,'HLP-2026-0004','Gabriela Silang','0918-456-7890','101 Vigan St., Ilocos','null','2022-08-10','AVAILABLE','Diego Silang','0918-098-7654',NULL,'','','','0000-00-00',NULL,'2026-08-29 21:22:53','ADMIN','2026-09-24 08:05:44'),(16,'HLP-2026-0005','Diego Silang','0918-567-8901','202 Abra St., Ilocos','null','2020-11-01','AVAILABLE','Gabriela Silang','0918-109-8765',NULL,'','','','0000-00-00',NULL,'2026-08-29 21:22:53','ADMIN','2026-09-22 15:37:30'),(17,'HLP-2026-0006','Antonio Luna','0918-678-9012','303 Badoc St., Ilocos','null','2023-02-15','AVAILABLE','Jose Luna','0918-210-9876',NULL,'','','','0000-00-00',NULL,'2026-08-29 21:22:53','ADMIN','2026-09-25 15:05:40'),(18,'HLP-2026-0007','Gregorio Del Pilar','0918-789-0123','404 Bulacan St., Bulacan','null','2021-07-01','AVAILABLE','Marcela Del Pilar','0918-321-0987',NULL,'','','','0000-00-00',NULL,'2026-08-29 21:22:53','ADMIN','2026-09-22 15:37:22'),(19,'HLP-2026-0008','Juan Luna','0918-890-1234','505 Badoc St., Ilocos','REGULAR','2020-03-01','AVAILABLE','Antonio Luna','0918-432-1098',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-29 21:22:53','ADMIN','2026-08-30 21:26:08'),(20,'HLP-2026-0009','Marcela Agoncillo','0918-901-2345','606 Taal St., Batangas','CONTRACTUAL','2022-10-01','AVAILABLE','Felipe Agoncillo','0918-543-2109',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-29 21:22:53','ADMIN','2026-08-30 21:26:08'),(21,'HLP-2026-0010','Melchora Aquino','0918-012-3456','707 Caloocan St., Caloocan','null','2019-12-01','AVAILABLE','Juan Aquino','0918-654-3210','uploads/helpers/HLP_21_1788505289.jpg','','','','0000-00-00',NULL,'2026-08-29 21:22:53','ADMIN','2026-09-04 15:01:29');
/*!40000 ALTER TABLE `tbl_helpers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_invoices`
--

DROP TABLE IF EXISTS `tbl_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_code` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `payment_terms` varchar(50) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `taxable_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `outstanding_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `invoice_status` enum('DRAFT','ISSUED','PARTIALLY_PAID','PAID','OVERDUE','CANCELLED','VOID') NOT NULL DEFAULT 'DRAFT',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `invoice_code` (`invoice_code`),
  UNIQUE KEY `billing_id` (`billing_id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_invoice_status` (`invoice_status`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_invoices`
--

LOCK TABLES `tbl_invoices` WRITE;
/*!40000 ALTER TABLE `tbl_invoices` DISABLE KEYS */;
INSERT INTO `tbl_invoices` VALUES (1,'INV-2026-000001',1,1,'2026-08-15','2026-09-14','30 Days',15000.00,0.00,15000.00,1800.00,500.00,17300.00,17300.00,0.00,'PAID',NULL,'2026-09-23 09:59:13','ADMIN','2026-09-23 10:27:36'),(2,'INV-2026-000002',4,2,'2026-09-23','2026-09-23','COD',50000.00,0.00,50000.00,6000.00,7000.00,63000.00,63000.00,0.00,'PAID','','2026-09-23 10:03:18','admin','2026-09-23 10:28:14'),(9,'INV-2026-000003',5,8,'2026-09-21','2026-10-21','30 Days',15000.00,0.00,15000.00,1800.00,0.00,16800.00,16800.00,0.00,'PAID',NULL,'2026-09-24 08:05:23','ADMIN','2026-09-24 08:05:23'),(10,'INV-2026-000004',1,9,'2026-09-22','2026-10-22','30 Days',22000.00,0.00,22000.00,2640.00,500.00,25140.00,25140.00,0.00,'PAID',NULL,'2026-09-24 08:05:28','ADMIN','2026-09-24 08:05:28'),(11,'INV-2026-000005',9,10,'2026-09-20','2026-10-05','15 Days',18000.00,0.00,18000.00,2160.00,0.00,20160.00,10000.00,10160.00,'PARTIALLY_PAID',NULL,'2026-09-24 08:05:33','ADMIN','2026-09-24 08:06:41'),(12,'INV-2026-000006',8,11,'2026-09-17','2026-11-01','45 Days',35000.00,0.00,35000.00,4200.00,0.00,39200.00,39200.00,0.00,'PAID',NULL,'2026-09-24 08:05:38','ADMIN','2026-09-24 08:05:38'),(21,'INV-2026-000008',1,18,'2026-09-24','2026-10-24','30 Days',50000.00,0.00,50000.00,6000.00,0.00,56000.00,56000.00,0.00,'PAID','','2026-09-24 12:02:22','admin','2026-09-24 13:03:44'),(22,'INV-2026-000009',1,20,'2026-09-25','2026-10-25','30 Days',50000.00,0.00,50000.00,6000.00,1500.00,57500.00,57500.00,0.00,'PAID','','2026-09-25 11:43:20','admin','2026-09-25 11:45:00'),(23,'INV-2026-000010',1,21,'2026-09-25','2026-10-25','30 Days',50000.00,0.00,50000.00,6000.00,1500.00,57500.00,57500.00,0.00,'PAID','','2026-09-25 15:07:32','admin','2026-09-25 15:11:03'),(24,'INV-2026-000011',1,22,'2026-10-05','2026-11-04','30 Days',18500.00,0.00,18500.00,2220.00,1490.00,22210.00,10000.00,12210.00,'PARTIALLY_PAID','Sent to ABC Manufacturing AP','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(25,'INV-2026-000012',2,23,'2026-10-05','2026-10-20','15 Days',9800.00,0.00,9800.00,1176.00,320.00,11296.00,11296.00,0.00,'PAID',NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_maintenance_parts`
--

DROP TABLE IF EXISTS `tbl_maintenance_parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_maintenance_parts` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `quantity` decimal(15,2) DEFAULT 1.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `supplier` varchar(200) DEFAULT NULL,
  `warranty` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`part_id`),
  KEY `idx_record_id` (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_maintenance_parts`
--

LOCK TABLES `tbl_maintenance_parts` WRITE;
/*!40000 ALTER TABLE `tbl_maintenance_parts` DISABLE KEYS */;
INSERT INTO `tbl_maintenance_parts` VALUES (26,10,'Cabin Filter',1.00,1300.00,1300.00,'FilterPro','6 Months','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(27,11,'Radiator',1.00,15000.00,15000.00,'CoolTech Supply','12 Months','OEM replacement','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(28,11,'Coolant',5.00,420.00,2100.00,'CoolTech Supply','N/A','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(29,11,'Coolant Hoses',2.00,450.00,900.00,'CoolTech Supply','6 Months','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(30,11,'Thermostat',1.00,500.00,500.00,'CoolTech Supply','6 Months','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(31,13,'Transmission Fluid',6.00,850.00,5100.00,'TransParts Co.','N/A','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(32,13,'Transmission Filter',1.00,1400.00,1400.00,'TransParts Co.','12 Months','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(33,14,'Engine Oil (15W-40)',12.00,350.00,4200.00,'XYZ Auto Parts','N/A','Tractor head','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(34,14,'Oil Filter',2.00,550.00,1100.00,'XYZ Auto Parts','6 Months','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(35,14,'Fuel Filter',1.00,750.00,750.00,'XYZ Auto Parts','6 Months','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(36,14,'Grease',4.00,400.00,1600.00,'XYZ Auto Parts','N/A','Multi-purpose grease','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(37,15,'Brake Shoe Set',2.00,650.00,1300.00,'TrailerPro','12 Months','Trailer brakes','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(38,15,'Grease',2.00,250.00,500.00,'TrailerPro','N/A','','2026-09-17 14:47:32','ADMIN','2026-09-17 14:47:32'),(39,17,'OIL',1.00,500.00,500.00,'TEST','','','2026-09-17 15:02:30','admin','2026-09-17 15:02:30'),(40,17,'GREASE',1.00,1000.00,1000.00,'TEST','',NULL,'2026-09-17 15:02:34','admin','2026-09-17 15:02:34'),(41,18,'OIL',5.00,1000.00,5000.00,'test','','','2026-09-17 15:35:03','admin','2026-09-17 15:35:03'),(42,19,'OIL',6.00,1500.00,9000.00,'','','','2026-09-17 15:45:13','admin','2026-09-17 15:45:13'),(43,20,'OIL',6.00,1500.00,9000.00,'','','','2026-09-17 15:47:18','admin','2026-09-17 15:47:18'),(44,20,'GREASE',3.00,500.00,1500.00,'','','','2026-09-17 15:47:18','admin','2026-09-17 15:47:18'),(45,20,'BRAKE PADS',6.00,2000.00,12000.00,'','','','2026-09-17 15:47:18','admin','2026-09-17 15:47:18'),(47,22,'BREAK PADS',6.00,600.00,3600.00,'','','','2026-09-23 11:14:33','admin','2026-09-23 11:14:33'),(48,22,'OIL',6.00,800.00,4800.00,'','','','2026-09-23 11:14:33','admin','2026-09-23 11:14:33');
/*!40000 ALTER TABLE `tbl_maintenance_parts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_maintenance_records`
--

DROP TABLE IF EXISTS `tbl_maintenance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_maintenance_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `labor_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `parts_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `downtime_hours` decimal(10,2) DEFAULT 0.00,
  `record_status` enum('ONGOING','COMPLETED','CANCELLED') NOT NULL DEFAULT 'COMPLETED',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `idx_record_code` (`record_code`),
  KEY `idx_truck_id` (`truck_id`),
  KEY `idx_schedule_id` (`schedule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_maintenance_records`
--

LOCK TABLES `tbl_maintenance_records` WRITE;
/*!40000 ALTER TABLE `tbl_maintenance_records` DISABLE KEYS */;
INSERT INTO `tbl_maintenance_records` VALUES (17,'MTR-2026-000001',16,6,'ABC-1234','2026-09-17',55500.00,'PREVENTIVE','PMS','FOR PMS','PERFORM PMS','kyle',5000.00,1500.00,0.00,6500.00,10.00,'COMPLETED','','2026-09-17 15:02:30','admin','2026-09-23 11:09:55'),(18,'MTR-2026-000002',17,1,'ABC-12345','2026-09-17',40000.00,'PREVENTIVE','PMS','SUBJECT FOR PMS','PMS','kyle',5000.00,5000.00,0.00,10000.00,5.00,'COMPLETED','TEST','2026-09-17 15:35:03','admin','2026-09-17 15:35:03'),(19,'MTR-2026-000003',18,26,'CHS-001','2026-09-17',23400.00,'PREVENTIVE','OIL CHANGE','SUBJECT TO OIL CHANGE','CHANGE OIL','TEST',1500.00,9000.00,0.00,10500.00,2.00,'COMPLETED','TEST','2026-09-17 15:45:13','admin','2026-09-17 15:45:13'),(20,'MTR-2026-000004',19,27,'CHS-002','2026-09-17',45600.00,'PREVENTIVE','PMS','PMS','PMS','tsad',3000.00,22500.00,0.00,25500.00,5.00,'COMPLETED','TEST','2026-09-17 15:47:17','admin','2026-09-17 15:47:17'),(22,'MTR-2026-000005',20,5,'CMPL-TASDA','2026-09-23',110000.00,'PREVENTIVE','PMS','FOR PMS','PMS','kyle',5000.00,8400.00,0.00,13400.00,5.00,'COMPLETED','','2026-09-23 11:14:33','admin','2026-09-23 11:14:33');
/*!40000 ALTER TABLE `tbl_maintenance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_maintenance_schedules`
--

DROP TABLE IF EXISTS `tbl_maintenance_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_maintenance_schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `schedule_status` enum('SCHEDULED','IN_PROGRESS','COMPLETED','CANCELLED','OVERDUE') NOT NULL DEFAULT 'SCHEDULED',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`schedule_id`),
  UNIQUE KEY `idx_schedule_code` (`schedule_code`),
  KEY `idx_truck_id` (`truck_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_maintenance_schedules`
--

LOCK TABLES `tbl_maintenance_schedules` WRITE;
/*!40000 ALTER TABLE `tbl_maintenance_schedules` DISABLE KEYS */;
INSERT INTO `tbl_maintenance_schedules` VALUES (16,'MTS-2026-000001',6,'ABC-1234','PREVENTIVE','PMS','2026-09-17',45230.50,10000.00,55230.50,'kyle','NORMAL','COMPLETED','test','2026-09-17 15:00:55','admin','2026-09-17 15:02:30'),(17,'MTS-2026-000002',1,'ABC-12345','PREVENTIVE','PM','2026-09-17',30000.00,10000.00,40000.00,'kyle','NORMAL','COMPLETED','TEST','2026-09-17 15:34:10','admin','2026-09-17 15:35:03'),(18,'MTS-2026-000003',26,'CHS-001','PREVENTIVE','OIL CHANGE','2026-09-17',23400.00,10000.00,33400.00,'TEST','NORMAL','COMPLETED','TEST','2026-09-17 15:44:40','admin','2026-09-17 15:45:13'),(19,'MTS-2026-000004',27,'CHS-002','PREVENTIVE','PMS','2026-09-17',45600.00,10000.00,55600.00,'tsad','NORMAL','COMPLETED','asda','2026-09-17 15:46:16','admin','2026-09-17 15:47:18'),(20,'MTS-2026-000005',5,'CMPL-TASDA','PREVENTIVE','PMS','2026-09-23',100000.00,10000.00,110000.00,'kyle','NORMAL','COMPLETED','TEST','2026-09-23 11:13:18','admin','2026-09-23 11:14:33');
/*!40000 ALTER TABLE `tbl_maintenance_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_modules`
--

DROP TABLE IF EXISTS `tbl_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_key` varchar(50) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `module_group` varchar(50) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `module_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `idx_module_key` (`module_key`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_modules`
--

LOCK TABLES `tbl_modules` WRITE;
/*!40000 ALTER TABLE `tbl_modules` DISABLE KEYS */;
INSERT INTO `tbl_modules` VALUES (1,'fms-customers','Customer Management','Operations',10,'ACTIVE','2026-09-24 16:42:40'),(2,'fms-vendors','Vendor Management','Operations',20,'ACTIVE','2026-09-24 16:42:40'),(3,'fms-trucks','Truck Management','Operations',30,'ACTIVE','2026-09-24 16:42:40'),(4,'fms-drivers','Driver Management','Operations',40,'ACTIVE','2026-09-24 16:42:40'),(5,'fms-helpers','Helper Management','Operations',50,'ACTIVE','2026-09-24 16:42:40'),(6,'fms-trips','Trip Scheduling','Trip & Dispatch',60,'ACTIVE','2026-09-24 16:42:40'),(7,'fms-dispatch','Dispatch Monitoring','Trip & Dispatch',70,'ACTIVE','2026-09-24 16:42:40'),(8,'fms-delivery-receipt','Delivery Receipt','Trip & Dispatch',80,'ACTIVE','2026-09-24 16:42:40'),(9,'billing','Billing Generation','Billing & AR',90,'ACTIVE','2026-09-24 16:42:40'),(10,'invoice','Invoice','Billing & AR',100,'ACTIVE','2026-09-24 16:42:40'),(11,'payment','Payment Recording','Billing & AR',110,'ACTIVE','2026-09-24 16:42:40'),(12,'accountsreceivable','Accounts Receivable','Billing & AR',120,'ACTIVE','2026-09-24 16:42:40'),(13,'statementofaccount','Statement of Account','Billing & AR',130,'ACTIVE','2026-09-24 16:42:40'),(14,'fms-maintenance','Preventive Maintenance','Maintenance',140,'ACTIVE','2026-09-24 16:42:40'),(15,'fms-tire','Tire Management','Maintenance',150,'ACTIVE','2026-09-24 16:42:40'),(16,'fms-supply','Supplies Management','Maintenance',160,'ACTIVE','2026-09-24 16:42:40'),(17,'fms-tool','Tools Management','Maintenance',170,'ACTIVE','2026-09-24 16:42:40'),(18,'operationsreports','Operations Reports','Reports',180,'ACTIVE','2026-09-24 16:42:40'),(19,'deliveryreports','Delivery Reports','Reports',190,'ACTIVE','2026-09-24 16:42:40'),(20,'billingreports','Billing Reports','Reports',200,'ACTIVE','2026-09-24 16:42:40'),(21,'maintenancereports','Maintenance Reports','Reports',210,'ACTIVE','2026-09-24 16:42:40'),(22,'usermanagement','User Management','Administration',220,'ACTIVE','2026-09-24 16:42:40');
/*!40000 ALTER TABLE `tbl_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_payments`
--

DROP TABLE IF EXISTS `tbl_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('CASH','BANK_TRANSFER','CHECK','ONLINE_TRANSFER','OTHER') NOT NULL DEFAULT 'CASH',
  `bank` varchar(100) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `payment_status` enum('PENDING','CLEARED','BOUNCED','CANCELLED') NOT NULL DEFAULT 'CLEARED',
  `receipt_attachment` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_payments`
--

LOCK TABLES `tbl_payments` WRITE;
/*!40000 ALTER TABLE `tbl_payments` DISABLE KEYS */;
INSERT INTO `tbl_payments` VALUES (1,'PAY-2026-000001',1,1,'2026-08-20',10000.00,'BANK_TRANSFER','BDO','REF-001','CLEARED',NULL,NULL,'2026-09-23 10:26:08','ADMIN','2026-09-23 10:26:08'),(3,'PAY-2026-000002',1,1,'2026-09-23',7300.00,'CASH','BDO','ASDASDA','CLEARED','uploads/payments/PAY_3_RECEIPT_1790130456.jpg','','2026-09-23 10:27:36','admin','2026-09-23 10:27:36'),(4,'PAY-2026-000003',4,2,'2026-09-23',63000.00,'BANK_TRANSFER','BDO','1231231','CLEARED','uploads/payments/PAY_4_RECEIPT_1790130494.pdf','','2026-09-23 10:28:14','admin','2026-09-23 10:28:14'),(10,'PAY-2026-000004',5,9,'2026-09-21',16800.00,'CASH',NULL,NULL,'CLEARED',NULL,'E2E payment scenario 1','2026-09-24 08:05:23','ADMIN','2026-09-24 08:05:23'),(11,'PAY-2026-000005',1,10,'2026-09-22',25140.00,'BANK_TRANSFER',NULL,NULL,'CLEARED',NULL,'E2E payment scenario 2','2026-09-24 08:05:28','ADMIN','2026-09-24 08:05:28'),(12,'PAY-2026-000006',9,11,'2026-09-20',10000.00,'CASH',NULL,NULL,'CLEARED',NULL,NULL,'2026-09-24 08:05:33','ADMIN','2026-09-24 08:06:41'),(13,'PAY-2026-000007',8,12,'2026-09-17',39200.00,'CHECK',NULL,NULL,'CLEARED',NULL,'E2E payment scenario 4','2026-09-24 08:05:38','ADMIN','2026-09-24 08:05:38'),(14,'PAY-2026-000008',1,21,'2026-09-24',56000.00,'BANK_TRANSFER','BDO','1231ASDA123','CLEARED','uploads/payments/PAY_14_RECEIPT_1790226224.pdf','','2026-09-24 13:03:44','admin','2026-09-24 13:03:44'),(15,'PAY-2026-000009',1,22,'2026-09-25',57000.00,'BANK_TRANSFER','BDO','AASDASD131','CLEARED','uploads/payments/PAY_15_RECEIPT_1790307858.png','','2026-09-25 11:44:18','admin','2026-09-25 11:44:18'),(16,'PAY-2026-000010',1,22,'2026-09-25',500.00,'BANK_TRANSFER','BDO','1231231','CLEARED',NULL,'','2026-09-25 11:45:00','admin','2026-09-25 11:45:00'),(17,'PAY-2026-000011',1,23,'2026-09-25',50000.00,'BANK_TRANSFER','BDO','ASDLASLD','CLEARED','uploads/payments/PAY_17_RECEIPT_1790320147.pdf','','2026-09-25 15:09:07','admin','2026-09-25 15:09:07'),(18,'PAY-2026-000012',1,23,'2026-09-25',7500.00,'BANK_TRANSFER','BDO','ASDASD','CLEARED','uploads/payments/PAY_18_RECEIPT_1790320263.jpg','','2026-09-25 15:11:03','admin','2026-09-25 15:11:03'),(19,'PAY-2026-000013',1,24,'2026-10-05',10000.00,'BANK_TRANSFER','BDO','BDO-TRF-88231045','CLEARED',NULL,'Partial payment','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(20,'PAY-2026-000014',2,25,'2026-10-05',5000.00,'CASH',NULL,NULL,'CLEARED',NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(21,'PAY-2026-000015',2,25,'2026-10-05',6296.00,'BANK_TRANSFER','BPI','BPI-E2E-1005','CLEARED',NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_role_dashboard_widgets`
--

DROP TABLE IF EXISTS `tbl_role_dashboard_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_role_dashboard_widgets` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `idx_role_widget` (`role_id`,`widget_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_widget_id` (`widget_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_role_dashboard_widgets`
--

LOCK TABLES `tbl_role_dashboard_widgets` WRITE;
/*!40000 ALTER TABLE `tbl_role_dashboard_widgets` DISABLE KEYS */;
INSERT INTO `tbl_role_dashboard_widgets` VALUES (131,1,1,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(132,1,2,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(133,1,3,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(134,1,4,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(135,1,5,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(136,1,6,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(137,1,7,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(138,1,8,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(139,1,9,1,'2026-09-25 14:07:10','2026-09-25 14:07:10'),(140,1,10,1,'2026-09-25 14:07:10','2026-09-25 14:07:10');
/*!40000 ALTER TABLE `tbl_role_dashboard_widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_role_permissions`
--

DROP TABLE IF EXISTS `tbl_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_role_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_add` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `idx_role_module` (`role_id`,`module_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_module_id` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_role_permissions`
--

LOCK TABLES `tbl_role_permissions` WRITE;
/*!40000 ALTER TABLE `tbl_role_permissions` DISABLE KEYS */;
INSERT INTO `tbl_role_permissions` VALUES (253,1,1,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(254,1,2,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(255,1,3,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(256,1,4,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(257,1,5,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(258,1,6,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(259,1,7,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(260,1,8,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(261,1,9,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(262,1,10,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(263,1,11,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(264,1,12,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(265,1,13,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(266,1,14,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(267,1,15,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(268,1,16,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(269,1,17,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(270,1,18,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(271,1,19,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(272,1,20,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(273,1,21,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09'),(274,1,22,1,1,1,1,'2026-09-25 14:03:09','2026-09-25 14:03:09');
/*!40000 ALTER TABLE `tbl_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_roles`
--

DROP TABLE IF EXISTS `tbl_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_code` varchar(50) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `role_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `idx_role_code` (`role_code`),
  UNIQUE KEY `idx_role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_roles`
--

LOCK TABLES `tbl_roles` WRITE;
/*!40000 ALTER TABLE `tbl_roles` DISABLE KEYS */;
INSERT INTO `tbl_roles` VALUES (1,'ROLE-2026-000001','Administrator','Full system access to all modules.','ACTIVE','2026-09-24 16:42:40','SYSTEM','2026-09-24 16:42:40');
/*!40000 ALTER TABLE `tbl_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_supplies`
--

DROP TABLE IF EXISTS `tbl_supplies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_supplies` (
  `supply_id` int(11) NOT NULL AUTO_INCREMENT,
  `supply_code` varchar(50) NOT NULL,
  `supply_name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `current_stock` decimal(15,2) DEFAULT 0.00,
  `minimum_stock` decimal(15,2) DEFAULT 0.00,
  `reorder_level` decimal(15,2) DEFAULT 0.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `supplier` varchar(200) DEFAULT NULL,
  `storage_location` varchar(200) DEFAULT NULL,
  `supply_status` enum('IN_STOCK','LOW_STOCK','OUT_OF_STOCK','DISCONTINUED') NOT NULL DEFAULT 'IN_STOCK',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`supply_id`),
  UNIQUE KEY `supply_code` (`supply_code`),
  KEY `idx_category` (`category`),
  KEY `idx_supply_status` (`supply_status`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_supplies`
--

LOCK TABLES `tbl_supplies` WRITE;
/*!40000 ALTER TABLE `tbl_supplies` DISABLE KEYS */;
INSERT INTO `tbl_supplies` VALUES (1,'SUP-2026-000001','Engine Oil 15W-40','Oil','Liters',94.00,30.00,50.00,350.00,'XYZ Auto Parts','Warehouse A - Shelf 1','IN_STOCK','Heavy duty diesel engine oil','2026-09-17 16:32:45','ADMIN','2026-10-05 11:11:01'),(2,'SUP-2026-000002','Engine Oil 10W-30','Oil','Liters',45.00,30.00,50.00,380.00,'XYZ Auto Parts','Warehouse A - Shelf 1','IN_STOCK','For light duty engines','2026-09-17 16:32:45','ADMIN','2026-09-17 16:32:45'),(3,'SUP-2026-000003','Oil Filter (Heavy Duty)','Filters','Pcs',25.00,10.00,15.00,850.00,'ABC Motors','Warehouse A - Shelf 2','IN_STOCK','Genuine Isuzu filter','2026-09-17 16:32:45','ADMIN','2026-09-17 16:32:45'),(4,'SUP-2026-000004','Oil Filter (Light Duty)','Filters','Pcs',7.00,10.00,15.00,400.00,'ABC Motors','Warehouse A - Shelf 2','LOW_STOCK','For light vehicles','2026-09-17 16:32:45','ADMIN','2026-10-05 11:11:01'),(5,'SUP-2026-000005','Air Filter','Filters','Pcs',12.00,5.00,10.00,650.00,'XYZ Auto Parts','Warehouse A - Shelf 3','IN_STOCK','Standard air filter','2026-09-17 16:32:45','ADMIN','2026-10-05 11:11:01'),(6,'SUP-2026-000006','Fuel Filter','Filters','Pcs',10.00,5.00,10.00,750.00,'XYZ Auto Parts','Warehouse A - Shelf 3','IN_STOCK','Diesel fuel filter','2026-09-17 16:32:45','ADMIN','2026-10-05 11:11:01'),(7,'SUP-2026-000007','Grease (Multi-Purpose)','Lubricants','Kg',50.00,10.00,20.00,250.00,'HeavyParts Inc.','Warehouse B - Shelf 1','IN_STOCK','General purpose grease','2026-09-17 16:32:45','ADMIN','2026-10-05 11:11:01'),(8,'SUP-2026-000008','Brake Fluid DOT4','Fluids','Liters',8.00,10.00,15.00,750.00,'BrakePro Inc.','Warehouse B - Shelf 2','LOW_STOCK','Hydraulic brake fluid','2026-09-17 16:32:45','ADMIN','2026-09-17 16:32:45'),(9,'SUP-2026-000009','Coolant (Long-Life)','Fluids','Liters',25.00,20.00,30.00,420.00,'CoolTech Supply','Warehouse B - Shelf 2','IN_STOCK','For radiator system','2026-09-17 16:32:45','ADMIN','2026-10-05 11:11:01'),(10,'SUP-2026-000010','Transmission Fluid ATF','Fluids','Liters',22.00,10.00,15.00,850.00,'TransParts Co.','Warehouse B - Shelf 3','IN_STOCK','Automatic transmission fluid','2026-09-17 16:32:45','ADMIN','2026-09-17 16:32:45'),(11,'SUP-2026-000011','BASAHAN','Equipment','Pcs',101.00,5.00,5.00,20.00,'n/a','Warehouse A','IN_STOCK','test','2026-09-17 16:37:20','admin','2026-09-17 16:37:44');
/*!40000 ALTER TABLE `tbl_supplies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_supply_transactions`
--

DROP TABLE IF EXISTS `tbl_supply_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_supply_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(50) NOT NULL,
  `transaction_type` enum('STOCK_IN','STOCK_OUT','RETURN','ADJUSTMENT','DAMAGED','DISPOSAL') NOT NULL,
  `transaction_date` date NOT NULL,
  `supply_id` int(11) NOT NULL,
  `supply_code` varchar(50) DEFAULT NULL,
  `supply_name` varchar(200) DEFAULT NULL,
  `quantity` decimal(15,2) DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
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
  `created_by` varchar(50) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `transaction_code` (`transaction_code`),
  KEY `idx_supply_id` (`supply_id`),
  KEY `idx_type` (`transaction_type`),
  KEY `idx_date` (`transaction_date`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_supply_transactions`
--

LOCK TABLES `tbl_supply_transactions` WRITE;
/*!40000 ALTER TABLE `tbl_supply_transactions` DISABLE KEYS */;
INSERT INTO `tbl_supply_transactions` VALUES (1,'STX-2026-000001','STOCK_IN','2026-08-01',1,'SUP-2026-000001','Engine Oil 15W-40',150.00,'Liters',350.00,52500.00,0.00,150.00,'INV-2026-0812','Purchase','XYZ Auto Parts',NULL,NULL,NULL,'Initial stock purchase','','2026-09-17 16:32:45','ADMIN'),(2,'STX-2026-000002','STOCK_OUT','2026-08-15',1,'SUP-2026-000001','Engine Oil 15W-40',20.00,'Liters',350.00,7000.00,150.00,130.00,'MNT-2026-000001','Maintenance','','Pedro Santos',6,'ABC-1234','PMS — oil change','Used on ABC-1234','2026-09-17 16:32:45','ADMIN'),(3,'STX-2026-000003','STOCK_OUT','2026-08-20',1,'SUP-2026-000001','Engine Oil 15W-40',15.00,'Liters',350.00,5250.00,130.00,115.00,'MNT-2026-000002','Maintenance','','Mark Tan',7,'XYZ-5678','PMS — oil change','Used on XYZ-5678','2026-09-17 16:32:45','ADMIN'),(4,'STX-2026-000004','RETURN','2026-08-25',1,'SUP-2026-000001','Engine Oil 15W-40',5.00,'Liters',350.00,1750.00,115.00,120.00,'RTN-2026-0001','Return','','Pedro Santos',NULL,NULL,'Unused returned','','2026-09-17 16:32:45','ADMIN'),(5,'STX-2026-000005','STOCK_IN','2026-08-02',2,'SUP-2026-000002','Engine Oil 10W-30',80.00,'Liters',380.00,30400.00,0.00,80.00,'INV-2026-0813','Purchase','XYZ Auto Parts',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(6,'STX-2026-000006','STOCK_OUT','2026-08-18',2,'SUP-2026-000002','Engine Oil 10W-30',35.00,'Liters',380.00,13300.00,80.00,45.00,'MNT-2026-000003','Maintenance','','Pedro Reyes',8,'DEF-9012','PMS — oil change','','2026-09-17 16:32:45','ADMIN'),(7,'STX-2026-000007','STOCK_IN','2026-08-05',3,'SUP-2026-000003','Oil Filter (Heavy Duty)',30.00,'Pcs',850.00,25500.00,0.00,30.00,'INV-2026-0821','Purchase','ABC Motors',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(8,'STX-2026-000008','STOCK_OUT','2026-08-15',3,'SUP-2026-000003','Oil Filter (Heavy Duty)',3.00,'Pcs',850.00,2550.00,30.00,27.00,'MNT-2026-000001','Maintenance','','Pedro Santos',6,'ABC-1234','PMS — filter replacement','','2026-09-17 16:32:45','ADMIN'),(9,'STX-2026-000009','STOCK_OUT','2026-08-20',3,'SUP-2026-000003','Oil Filter (Heavy Duty)',2.00,'Pcs',850.00,1700.00,27.00,25.00,'MNT-2026-000002','Maintenance','','Mark Tan',7,'XYZ-5678','PMS — filter replacement','','2026-09-17 16:32:45','ADMIN'),(10,'STX-2026-000010','STOCK_IN','2026-08-06',4,'SUP-2026-000004','Oil Filter (Light Duty)',15.00,'Pcs',400.00,6000.00,0.00,15.00,'INV-2026-0822','Purchase','ABC Motors',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(11,'STX-2026-000011','STOCK_OUT','2026-08-22',4,'SUP-2026-000004','Oil Filter (Light Duty)',5.00,'Pcs',400.00,2000.00,15.00,10.00,'MNT-2026-000004','Maintenance','','Diego Silang',10,'JKL-7890','Routine filter swap','','2026-09-17 16:32:45','ADMIN'),(12,'STX-2026-000012','DAMAGED','2026-09-01',4,'SUP-2026-000004','Oil Filter (Light Duty)',2.00,'Pcs',400.00,800.00,10.00,8.00,'DMG-2026-0001','Damage','',NULL,NULL,NULL,'Water damaged in storage','','2026-09-17 16:32:45','ADMIN'),(13,'STX-2026-000013','STOCK_IN','2026-08-08',5,'SUP-2026-000005','Air Filter',20.00,'Pcs',650.00,13000.00,0.00,20.00,'INV-2026-0825','Purchase','XYZ Auto Parts',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(14,'STX-2026-000014','STOCK_OUT','2026-08-24',5,'SUP-2026-000005','Air Filter',5.00,'Pcs',650.00,3250.00,20.00,15.00,'MNT-2026-000005','Maintenance','','Antonio Luna',9,'GHI-3456','Air filter replacement','','2026-09-17 16:32:45','ADMIN'),(15,'STX-2026-000015','STOCK_IN','2026-08-10',6,'SUP-2026-000006','Fuel Filter',15.00,'Pcs',750.00,11250.00,0.00,15.00,'INV-2026-0828','Purchase','XYZ Auto Parts',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(16,'STX-2026-000016','STOCK_OUT','2026-08-25',6,'SUP-2026-000006','Fuel Filter',3.00,'Pcs',750.00,2250.00,15.00,12.00,'MNT-2026-000006','Maintenance','','Pedro Reyes',8,'DEF-9012','Fuel filter replacement','','2026-09-17 16:32:45','ADMIN'),(17,'STX-2026-000017','STOCK_IN','2026-08-12',7,'SUP-2026-000007','Grease (Multi-Purpose)',40.00,'Kg',250.00,10000.00,0.00,40.00,'INV-2026-0831','Purchase','HeavyParts Inc.',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(18,'STX-2026-000018','STOCK_OUT','2026-08-26',7,'SUP-2026-000007','Grease (Multi-Purpose)',5.00,'Kg',250.00,1250.00,40.00,35.00,'MNT-2026-000007','Maintenance','','Ramon Cruz',5,'CMPL-TASDA','Chassis greasing','','2026-09-17 16:32:45','ADMIN'),(19,'STX-2026-000019','STOCK_IN','2026-08-14',8,'SUP-2026-000008','Brake Fluid DOT4',20.00,'Liters',750.00,15000.00,0.00,20.00,'INV-2026-0835','Purchase','BrakePro Inc.',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(20,'STX-2026-000020','STOCK_OUT','2026-08-28',8,'SUP-2026-000008','Brake Fluid DOT4',12.00,'Liters',750.00,9000.00,20.00,8.00,'MNT-2026-000008','Maintenance','','Mark Tan',7,'XYZ-5678','Brake system service','','2026-09-17 16:32:45','ADMIN'),(21,'STX-2026-000021','STOCK_IN','2026-08-03',9,'SUP-2026-000009','Coolant (Long-Life)',30.00,'Liters',420.00,12600.00,0.00,30.00,'INV-2026-0815','Purchase','CoolTech Supply',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(22,'STX-2026-000022','STOCK_OUT','2026-08-16',9,'SUP-2026-000009','Coolant (Long-Life)',15.00,'Liters',420.00,6300.00,30.00,15.00,'MNT-2026-000009','Maintenance','','Maria Santos',11,'MNO-2345','Radiator service','','2026-09-17 16:32:45','ADMIN'),(23,'STX-2026-000023','STOCK_OUT','2026-08-30',9,'SUP-2026-000009','Coolant (Long-Life)',15.00,'Liters',420.00,6300.00,15.00,0.00,'MNT-2026-000010','Maintenance','','Maria Santos',11,'MNO-2345','Coolant flush','','2026-09-17 16:32:45','ADMIN'),(24,'STX-2026-000024','STOCK_IN','2026-08-04',10,'SUP-2026-000010','Transmission Fluid ATF',30.00,'Liters',850.00,25500.00,0.00,30.00,'INV-2026-0818','Purchase','TransParts Co.',NULL,NULL,NULL,'Initial stock','','2026-09-17 16:32:45','ADMIN'),(25,'STX-2026-000025','STOCK_OUT','2026-08-19',10,'SUP-2026-000010','Transmission Fluid ATF',8.00,'Liters',850.00,6800.00,30.00,22.00,'MNT-2026-000011','Maintenance','','Juan Dela Cruz',6,'ABC-1234','Transmission fluid change','','2026-09-17 16:32:45','ADMIN'),(26,'STX-2026-000026','ADJUSTMENT','2026-09-01',1,'SUP-2026-000001','Engine Oil 15W-40',120.00,'Liters',350.00,42000.00,120.00,120.00,'ADJ-2026-0001','Adjustment','',NULL,NULL,NULL,'Physical count reconciliation','Counted on Sep 1','2026-09-17 16:32:45','ADMIN'),(27,'STX-2026-000027','DISPOSAL','2026-09-02',4,'SUP-2026-000004','Oil Filter (Light Duty)',1.00,'Pcs',400.00,400.00,8.00,7.00,'DSP-2026-0001','Disposal','',NULL,NULL,NULL,'Expired filter disposal','Beyond shelf life','2026-09-17 16:32:45','ADMIN'),(28,'STX-2026-000028','STOCK_OUT','2026-09-03',5,'SUP-2026-000005','Air Filter',3.00,'Pcs',650.00,1950.00,15.00,12.00,'MNT-2026-000012','Maintenance','','Antonio Luna',9,'GHI-3456','Air filter replacement','','2026-09-17 16:32:45','ADMIN'),(29,'STX-2026-000029','STOCK_IN','2026-09-04',7,'SUP-2026-000007','Grease (Multi-Purpose)',15.00,'Kg',250.00,3750.00,35.00,50.00,'INV-2026-0901','Purchase','HeavyParts Inc.',NULL,NULL,NULL,'Restock low inventory','','2026-09-17 16:32:45','ADMIN'),(30,'STX-2026-000030','STOCK_OUT','2026-09-05',1,'SUP-2026-000001','Engine Oil 15W-40',25.00,'Liters',350.00,8750.00,120.00,95.00,'MNT-2026-000013','Maintenance','','Pedro Santos',14,'VWX-3456','PMS — oil change','','2026-09-17 16:32:45','ADMIN'),(31,'STX-2026-000031','STOCK_OUT','2026-09-06',6,'SUP-2026-000006','Fuel Filter',2.00,'Pcs',750.00,1500.00,12.00,10.00,'MNT-2026-000014','Maintenance','','Ramon Cruz',14,'VWX-3456','Fuel filter replacement','','2026-09-17 16:32:45','ADMIN'),(32,'STX-2026-000032','STOCK_IN','2026-09-07',9,'SUP-2026-000009','Coolant (Long-Life)',25.00,'Liters',420.00,10500.00,0.00,25.00,'INV-2026-0903','Purchase','CoolTech Supply',NULL,NULL,NULL,'Restock after stockout','','2026-09-17 16:32:45','ADMIN'),(33,'STX-2026-000033','STOCK_OUT','2026-09-17',1,'SUP-2026-000001','Engine Oil 15W-40',1.00,'Liters',350.00,350.00,95.00,94.00,'MNT12031-23',NULL,'','TRUCK-120312',1,'ABC-12345','MAINTENANCE','','2026-09-17 16:33:57','admin'),(38,'STX-2026-000035','ADJUSTMENT','2026-09-17',11,'SUP-2026-000011','BASAHAN',100.00,'Pcs',20.00,2000.00,0.00,100.00,NULL,NULL,NULL,NULL,NULL,NULL,'Opening stock',NULL,'2026-09-17 16:37:20','SYSTEM'),(39,'STX-2026-000034','STOCK_IN','2026-09-17',11,'SUP-2026-000011','BASAHAN',1.00,'Pcs',20.00,20.00,100.00,101.00,'PO-1231',NULL,'TEST','',NULL,'','TEST','TEST','2026-09-17 16:37:44','admin');
/*!40000 ALTER TABLE `tbl_supply_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tire_disposals`
--

DROP TABLE IF EXISTS `tbl_tire_disposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tire_disposals` (
  `disposal_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_by` varchar(50) NOT NULL,
  PRIMARY KEY (`disposal_id`),
  UNIQUE KEY `idx_disposal_code` (`disposal_code`),
  KEY `idx_tire_id` (`tire_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tire_disposals`
--

LOCK TABLES `tbl_tire_disposals` WRITE;
/*!40000 ALTER TABLE `tbl_tire_disposals` DISABLE KEYS */;
INSERT INTO `tbl_tire_disposals` VALUES (1,'TDS-2026-000001',7,'TIR-2026-000007','2026-09-17','Explode',22800.00,'test','DISPOSED','test','2026-09-17 16:07:50','admin');
/*!40000 ALTER TABLE `tbl_tire_disposals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tire_installations`
--

DROP TABLE IF EXISTS `tbl_tire_installations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tire_installations` (
  `installation_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `installation_status` enum('ACTIVE','REMOVED') NOT NULL DEFAULT 'ACTIVE',
  `removed_date` date DEFAULT NULL,
  `removal_odometer` decimal(15,2) DEFAULT 0.00,
  `distance_used_km` decimal(15,2) DEFAULT 0.00,
  `reason_for_removal` varchar(200) DEFAULT NULL,
  `tire_condition_on_removal` varchar(100) DEFAULT NULL,
  `removed_by` varchar(100) DEFAULT NULL,
  `removal_remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`installation_id`),
  UNIQUE KEY `idx_installation_code` (`installation_code`),
  KEY `idx_tire_id` (`tire_id`),
  KEY `idx_truck_id` (`truck_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tire_installations`
--

LOCK TABLES `tbl_tire_installations` WRITE;
/*!40000 ALTER TABLE `tbl_tire_installations` DISABLE KEYS */;
INSERT INTO `tbl_tire_installations` VALUES (1,'TIN-2026-000001',1,'TIR-2026-000001',6,'ABC-1234','Front Left','2026-07-20',70000.00,'Pedro Santos','New install','ACTIVE',NULL,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(2,'TIN-2026-000002',2,'TIR-2026-000002',6,'ABC-1234','Front Right','2026-07-20',70000.00,'Pedro Santos','New install','ACTIVE',NULL,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(3,'TIN-2026-000003',3,'TIR-2026-000003',7,'XYZ-5678','Rear Left','2026-08-15',55000.00,'Pedro Reyes','New install','ACTIVE',NULL,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(4,'TIN-2026-000004',4,'TIR-2026-000004',7,'XYZ-5678','Rear Right','2026-08-15',55000.00,'Pedro Reyes','New install','ACTIVE',NULL,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(5,'TIN-2026-000005',5,'TIR-2026-000005',8,'DEF-9012','Front Left','2026-08-20',80000.00,'Mark Tan','New install','ACTIVE',NULL,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(6,'TIN-2026-000006',6,'TIR-2026-000006',8,'DEF-9012','Front Right','2026-08-20',80000.00,'Mark Tan','New install','ACTIVE',NULL,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(7,'TIN-2026-000007',7,'TIR-2026-000007',9,'GHI-3456','Rear Left','2026-06-15',39700.00,'Antonio Luna','Old tire','REMOVED','2026-09-15',62500.00,22800.00,'Worn out','Poor','Antonio Luna','Thread depth below 2mm','2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(8,'TIN-2026-000008',9,'TIR-2026-000009',10,'JKL-7890','Rear Right','2026-06-10',90200.00,'Diego Silang','New install','REMOVED','2026-08-15',135400.00,45200.00,'Punctured, beyond repair','Damaged','Diego Silang','Sidewall damaged','2026-09-17 15:59:41','ADMIN','2026-09-17 15:59:41'),(9,'TIN-2026-000009',8,'TIR-2026-000008',9,'GHI-3456','FRONT LEFT','2026-09-17',124500.00,'KYLE','','ACTIVE',NULL,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 16:07:14','admin','2026-09-17 16:07:14');
/*!40000 ALTER TABLE `tbl_tire_installations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tire_transactions`
--

DROP TABLE IF EXISTS `tbl_tire_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tire_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(50) NOT NULL,
  `transaction_type` enum('IN','OUT') NOT NULL,
  `transaction_date` date NOT NULL,
  `tire_id` int(11) NOT NULL,
  `tire_code` varchar(50) DEFAULT NULL,
  `quantity` decimal(15,2) DEFAULT 1.00,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `purpose` varchar(200) DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `supplier_vendor` varchar(200) DEFAULT NULL,
  `released_by` varchar(100) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `transaction_code` (`transaction_code`),
  KEY `idx_tire_id` (`tire_id`),
  KEY `idx_type` (`transaction_type`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tire_transactions`
--

LOCK TABLES `tbl_tire_transactions` WRITE;
/*!40000 ALTER TABLE `tbl_tire_transactions` DISABLE KEYS */;
INSERT INTO `tbl_tire_transactions` VALUES (1,'TTX-2026-000001','IN','2026-06-15',1,'TIR-2026-000001',1.00,12500.00,'Purchase',NULL,NULL,'XYZ Auto Parts',NULL,'INV-2026-0451','New stock','2026-09-17 15:59:24','ADMIN'),(2,'TTX-2026-000002','IN','2026-06-15',2,'TIR-2026-000002',1.00,12500.00,'Purchase',NULL,NULL,'XYZ Auto Parts',NULL,'INV-2026-0451','New stock','2026-09-17 15:59:24','ADMIN'),(3,'TTX-2026-000003','IN','2026-07-01',3,'TIR-2026-000003',1.00,11800.00,'Purchase',NULL,NULL,'XYZ Auto Parts',NULL,'INV-2026-0473','New stock','2026-09-17 15:59:24','ADMIN'),(4,'TTX-2026-000004','IN','2026-07-01',4,'TIR-2026-000004',1.00,11800.00,'Purchase',NULL,NULL,'XYZ Auto Parts',NULL,'INV-2026-0473','New stock','2026-09-17 15:59:24','ADMIN'),(5,'TTX-2026-000005','IN','2026-08-10',5,'TIR-2026-000005',1.00,18500.00,'Purchase',NULL,NULL,'ABC Motors',NULL,'INV-2026-0512','New stock','2026-09-17 15:59:24','ADMIN'),(6,'TTX-2026-000006','IN','2026-08-10',6,'TIR-2026-000006',1.00,18500.00,'Purchase',NULL,NULL,'ABC Motors',NULL,'INV-2026-0512','New stock','2026-09-17 15:59:24','ADMIN'),(7,'TTX-2026-000007','IN','2026-09-05',7,'TIR-2026-000007',1.00,13200.00,'Purchase',NULL,NULL,'HeavyParts Inc.',NULL,'INV-2026-0589','New stock','2026-09-17 15:59:24','ADMIN'),(8,'TTX-2026-000008','IN','2026-09-05',8,'TIR-2026-000008',1.00,13200.00,'Purchase',NULL,NULL,'HeavyParts Inc.',NULL,'INV-2026-0589','Spare tire','2026-09-17 15:59:24','ADMIN'),(9,'TTX-2026-000009','IN','2026-05-20',9,'TIR-2026-000009',1.00,15800.00,'Purchase',NULL,NULL,'ABC Motors',NULL,'INV-2026-0398','New stock','2026-09-17 15:59:24','ADMIN'),(10,'TTX-2026-000010','IN','2026-05-20',10,'TIR-2026-000010',1.00,15800.00,'Purchase',NULL,NULL,'ABC Motors',NULL,'INV-2026-0398','Reserved','2026-09-17 15:59:24','ADMIN'),(11,'TTX-2026-000011','OUT','2026-07-20',1,'TIR-2026-000001',1.00,0.00,'Installation',6,'ABC-1234',NULL,'Pedro Santos','WO-2026-001','Installed on ABC-1234','2026-09-17 15:59:33','ADMIN'),(12,'TTX-2026-000012','OUT','2026-07-20',2,'TIR-2026-000002',1.00,0.00,'Installation',6,'ABC-1234',NULL,'Pedro Santos','WO-2026-001','Installed on ABC-1234','2026-09-17 15:59:33','ADMIN'),(13,'TTX-2026-000013','OUT','2026-08-15',3,'TIR-2026-000003',1.00,0.00,'Installation',7,'XYZ-5678',NULL,'Pedro Reyes','WO-2026-014','Installed on XYZ-5678','2026-09-17 15:59:33','ADMIN'),(14,'TTX-2026-000014','OUT','2026-08-15',4,'TIR-2026-000004',1.00,0.00,'Installation',7,'XYZ-5678',NULL,'Pedro Reyes','WO-2026-014','Installed on XYZ-5678','2026-09-17 15:59:33','ADMIN'),(15,'TTX-2026-000015','OUT','2026-08-20',5,'TIR-2026-000005',1.00,0.00,'Installation',8,'DEF-9012',NULL,'Mark Tan','WO-2026-021','Installed on DEF-9012','2026-09-17 15:59:33','ADMIN'),(16,'TTX-2026-000016','OUT','2026-08-20',6,'TIR-2026-000006',1.00,0.00,'Installation',8,'DEF-9012',NULL,'Mark Tan','WO-2026-021','Installed on DEF-9012','2026-09-17 15:59:33','ADMIN'),(17,'TTX-2026-000017','OUT','2026-09-15',7,'TIR-2026-000007',1.00,0.00,'Installation',9,'GHI-3456',NULL,'Antonio Luna','WO-2026-032','Installed on GHI-3456','2026-09-17 15:59:33','ADMIN'),(18,'TTX-2026-000018','OUT','2026-06-10',9,'TIR-2026-000009',1.00,0.00,'Installation',10,'JKL-7890',NULL,'Diego Silang','WO-2026-008','Installed on JKL-7890','2026-09-17 15:59:33','ADMIN');
/*!40000 ALTER TABLE `tbl_tire_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tires`
--

DROP TABLE IF EXISTS `tbl_tires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tires` (
  `tire_id` int(11) NOT NULL AUTO_INCREMENT,
  `tire_code` varchar(50) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `life_expectancy_km` decimal(15,2) DEFAULT 0.00,
  `tread_depth_mm` decimal(5,2) DEFAULT 0.00,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `date_of_manufacture` date DEFAULT NULL,
  `supplier` varchar(200) DEFAULT NULL,
  `tire_status` enum('IN_STOCK','INSTALLED','REMOVED','FOR_DISPOSAL','DISPOSED') NOT NULL DEFAULT 'IN_STOCK',
  `current_odometer` decimal(15,2) DEFAULT 0.00,
  `total_distance_km` decimal(15,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`tire_id`),
  UNIQUE KEY `tire_code` (`tire_code`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `idx_status` (`tire_status`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tires`
--

LOCK TABLES `tbl_tires` WRITE;
/*!40000 ALTER TABLE `tbl_tires` DISABLE KEYS */;
INSERT INTO `tbl_tires` VALUES (1,'TIR-2026-000001','SN-2026-ABC-0001','HDR2','Bridgestone','11R22.5',80000.00,18.50,12500.00,'2025-06-15','XYZ Auto Parts','INSTALLED',82500.00,12500.00,'Mounted on ABC-1234','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16'),(2,'TIR-2026-000002','SN-2026-ABC-0002','HDR2','Bridgestone','11R22.5',80000.00,18.20,12500.00,'2025-06-15','XYZ Auto Parts','INSTALLED',82500.00,11800.00,'Mounted on ABC-1234','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16'),(3,'TIR-2026-000003','SN-2026-XYZ-0003','R150','Bridgestone','11R22.5',75000.00,17.80,11800.00,'2025-07-01','XYZ Auto Parts','INSTALLED',65200.00,9800.00,'Mounted on XYZ-5678','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16'),(4,'TIR-2026-000004','SN-2026-XYZ-0004','R150','Bridgestone','11R22.5',75000.00,18.00,11800.00,'2025-07-01','XYZ Auto Parts','INSTALLED',65200.00,10200.00,'Mounted on XYZ-5678','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16'),(5,'TIR-2026-000005','SN-2026-DEF-0005','M840','Michelin','295/80R22.5',90000.00,20.10,18500.00,'2025-08-10','ABC Motors','INSTALLED',95200.00,14500.00,'Mounted on DEF-9012','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16'),(6,'TIR-2026-000006','SN-2026-DEF-0006','M840','Michelin','295/80R22.5',90000.00,19.80,18500.00,'2025-08-10','ABC Motors','INSTALLED',95200.00,14200.00,'Mounted on DEF-9012','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16'),(7,'TIR-2026-000007','SN-2026-GHI-0007','G580','Goodyear','11R22.5',70000.00,16.50,13200.00,'2025-09-05','HeavyParts Inc.','DISPOSED',62500.00,22800.00,'Worn out, awaiting disposal','2026-09-17 15:59:16','ADMIN','2026-09-17 16:07:50'),(8,'TIR-2026-000008','SN-2026-GHI-0008','G580','Goodyear','11R22.5',70000.00,17.20,13200.00,'2025-09-05','HeavyParts Inc.','INSTALLED',124500.00,0.00,'Spare tire in warehouse','2026-09-17 15:59:16','ADMIN','2026-09-17 16:07:14'),(9,'TIR-2026-000009','SN-2026-JKL-0009','R268','Continental','11R22.5',85000.00,19.50,15800.00,'2025-05-20','ABC Motors','DISPOSED',135400.00,45200.00,'Beyond repair','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16'),(10,'TIR-2026-000010','SN-2026-JKL-0010','R268','Continental','11R22.5',85000.00,18.90,15800.00,'2025-05-20','ABC Motors','IN_STOCK',0.00,0.00,'Reserved for JKL-7890','2026-09-17 15:59:16','ADMIN','2026-09-17 15:59:16');
/*!40000 ALTER TABLE `tbl_tires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tool_issuances`
--

DROP TABLE IF EXISTS `tbl_tool_issuances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tool_issuances` (
  `issuance_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`issuance_id`),
  UNIQUE KEY `issuance_code` (`issuance_code`),
  KEY `idx_tool_id` (`tool_id`),
  KEY `idx_status` (`issuance_status`),
  KEY `idx_issue_date` (`issue_date`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tool_issuances`
--

LOCK TABLES `tbl_tool_issuances` WRITE;
/*!40000 ALTER TABLE `tbl_tool_issuances` DISABLE KEYS */;
INSERT INTO `tbl_tool_issuances` VALUES (1,'TIS-2026-000001',1,'TL-2026-000001','Hydraulic Jack 10-Ton',1,1,0,'Pedro Santos','STAFF','2026-07-10','08:30:00','2026-07-12','2026-07-12','16:45:00','GOOD','GOOD','Tire replacement','WO-2026-001',6,'ABC-1234','RETURNED','Pedro Santos','Pedro Reyes','Returned in good condition','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(2,'TIS-2026-000002',1,'TL-2026-000001','Hydraulic Jack 10-Ton',1,1,0,'Antonio Luna','STAFF','2026-08-15','09:00:00','2026-08-17','2026-08-17','17:30:00','GOOD','GOOD','Suspension repair','WO-2026-014',9,'GHI-3456','RETURNED','Antonio Luna','Pedro Santos','No issues','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(3,'TIS-2026-000003',2,'TL-2026-000002','Hydraulic Jack 5-Ton',1,1,0,'Diego Silang','STAFF','2026-08-20','07:45:00','2026-08-22','2026-08-22','14:20:00','GOOD','GOOD','Brake job on JKL-7890','WO-2026-018',10,'JKL-7890','RETURNED','Diego Silang','Mark Tan','Completed on time','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(4,'TIS-2026-000004',3,'TL-2026-000003','Impact Wrench 1-inch',1,1,0,'Juan Dela Cruz','MECHANIC','2026-07-25','08:00:00','2026-07-27','2026-07-27','16:00:00','GOOD','GOOD','Wheel rotation','WO-2026-005',6,'ABC-1234','RETURNED','Juan Dela Cruz','Pedro Santos','Good condition','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(5,'TIS-2026-000005',3,'TL-2026-000003','Impact Wrench 1-inch',1,1,0,'Pedro Reyes','MECHANIC','2026-08-10','07:30:00','2026-08-12','2026-08-12','15:30:00','GOOD','GOOD','Tire change','WO-2026-010',7,'XYZ-5678','RETURNED','Pedro Reyes','Mark Tan','Standard service','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(6,'TIS-2026-000006',4,'TL-2026-000004','Torque Wrench 600mm',1,1,0,'Mark Tan','MECHANIC','2026-08-01','06:00:00','2026-08-03','2026-08-03','17:00:00','GOOD','GOOD','Wheel torquing','WO-2026-007',8,'DEF-9012','RETURNED','Mark Tan','Pedro Santos','Returned properly','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(7,'TIS-2026-000007',4,'TL-2026-000004','Torque Wrench 600mm',1,0,1,'Pedro Santos','STAFF','2026-09-10','08:00:00','2026-09-18',NULL,NULL,'GOOD',NULL,'Full fleet wheel check','WO-2026-020',NULL,NULL,'ISSUED',NULL,NULL,'Currently in use',NULL,'2026-09-17 16:48:19','ADMIN','2026-09-17 16:48:19'),(8,'TIS-2026-000008',5,'TL-2026-000005','Tool Box (Complete Set)',1,1,0,'Pedro Reyes','MECHANIC','2026-07-15','07:00:00','2026-07-22','2026-07-22','16:30:00','GOOD','GOOD','PMS work on XYZ-5678','WO-2026-008',7,'XYZ-5678','RETURNED','Pedro Reyes','Antonio Luna','All tools complete','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(9,'TIS-2026-000009',5,'TL-2026-000005','Tool Box (Complete Set)',1,1,0,'Antonio Luna','MECHANIC','2026-08-05','06:30:00','2026-08-10','2026-08-10','17:00:00','GOOD','GOOD','Suspension overhaul','WO-2026-011',9,'GHI-3456','RETURNED','Antonio Luna','Pedro Santos','Complete set returned','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(10,'TIS-2026-000010',6,'TL-2026-000006','Digital Tire Pressure Gauge',1,1,0,'Diego Silang','STAFF','2026-08-12','08:15:00','2026-08-14','2026-08-14','16:00:00','GOOD','GOOD','Pressure checks','WO-2026-013',10,'JKL-7890','RETURNED','Diego Silang','Mark Tan','Calibrated','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(11,'TIS-2026-000011',6,'TL-2026-000006','Digital Tire Pressure Gauge',1,0,1,'Mark Tan','MECHANIC','2026-09-12','07:00:00','2026-09-19',NULL,NULL,'GOOD',NULL,'Fleet inspection','WO-2026-022',NULL,NULL,'ISSUED',NULL,NULL,'Active',NULL,'2026-09-17 16:48:19','ADMIN','2026-09-17 16:48:19'),(12,'TIS-2026-000012',7,'TL-2026-000007','Wheel Alignment Gauge',1,1,0,'Juan Dela Cruz','MECHANIC','2026-08-18','06:45:00','2026-08-19','2026-08-19','15:00:00','GOOD','FAIR','Alignment check','WO-2026-016',6,'ABC-1234','RETURNED','Juan Dela Cruz','Pedro Santos','Gauge reading drift','Needs recalibration','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(13,'TIS-2026-000013',9,'TL-2026-000009','Grease Gun (Pneumatic)',1,1,0,'Ramon Cruz','MECHANIC','2026-08-22','07:30:00','2026-08-24','2026-08-24','16:30:00','GOOD','GOOD','Chassis greasing','WO-2026-017',5,'CMPL-TASDA','RETURNED','Ramon Cruz','Mark Tan','All good','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(14,'TIS-2026-000014',9,'TL-2026-000009','Grease Gun (Pneumatic)',1,0,1,'Antonio Luna','MECHANIC','2026-09-08','08:00:00','2026-09-15',NULL,NULL,'GOOD',NULL,'Grease all fleet trucks','WO-2026-019',NULL,NULL,'ISSUED',NULL,NULL,'Active issuance',NULL,'2026-09-17 16:48:19','ADMIN','2026-09-17 16:48:19'),(15,'TIS-2026-000015',3,'TL-2026-000003','Impact Wrench 1-inch',1,0,1,'Pedro Reyes','MECHANIC','2026-08-28','07:00:00','2026-09-01',NULL,NULL,'GOOD',NULL,'Tire service','WO-2026-025',8,'DEF-9012','ISSUED',NULL,NULL,'Not yet returned — overdue','','2026-09-17 16:48:19','ADMIN','2026-09-17 16:48:19'),(16,'TIS-2026-000016',1,'TL-2026-000001','Hydraulic Jack 10-Ton',1,0,1,'Diego Silang','STAFF','2026-08-30','06:00:00','2026-09-02',NULL,NULL,'GOOD',NULL,'Emergency jack on DEF-9012','WO-2026-026',8,'DEF-9012','ISSUED',NULL,NULL,'Overdue — follow up','','2026-09-17 16:48:19','ADMIN','2026-09-17 16:48:19'),(17,'TIS-2026-000017',5,'TL-2026-000005','Tool Box (Complete Set)',1,1,0,'Pedro Santos','STAFF','2026-09-01','06:30:00','2026-09-05','2026-09-05','16:45:00','GOOD','GOOD','Full inspection','WO-2026-023',6,'ABC-1234','RETURNED','Pedro Santos','Antonio Luna','Complete','','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(18,'TIS-2026-000018',2,'TL-2026-000002','Hydraulic Jack 5-Ton',1,1,0,'Mark Tan','MECHANIC','2026-09-02','07:00:00','2026-09-06','2026-09-06','17:00:00','GOOD','FAIR','Tire job on GHI-3456','WO-2026-024',9,'GHI-3456','RETURNED','Mark Tan','Pedro Santos','Slightly worn','Monitor condition','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(19,'TIS-2026-000019',7,'TL-2026-000007','Wheel Alignment Gauge',1,1,0,'Antonio Luna','MECHANIC','2026-09-05','06:45:00','2026-09-08','2026-09-08','15:30:00','FAIR','FAIR','Alignment check','WO-2026-027',8,'DEF-9012','RETURNED','Antonio Luna','Pedro Santos','Still needs recalibr','Sent to calibration','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(20,'TIS-2026-000020',3,'TL-2026-000003','Impact Wrench 1-inch',1,1,0,'Juan Dela Cruz','MECHANIC','2026-09-06','08:00:00','2026-09-10','2026-09-10','16:00:00','GOOD','DAMAGED','Heavy bolt removal','WO-2026-028',14,'VWX-3456','RETURNED','Juan Dela Cruz','Pedro Santos','Motor damaged','Sent for repair','2026-09-17 16:48:19','ADMIN','2026-10-05 11:11:01'),(24,'TIS-2026-000024',12,'TL-2026-000011','SCREW DRIVER',1,0,1,'KYLE','DRIVER','2026-09-17','17:09:00',NULL,NULL,NULL,'GOOD',NULL,'TEST','ASDA',NULL,'','ISSUED',NULL,NULL,'TEST',NULL,'2026-09-17 17:09:27','admin','2026-09-17 17:09:27'),(25,'TIS-2026-000025',12,'TL-2026-000011','SCREW DRIVER',1,0,1,'JAMIE','DRIVER','2026-09-17','17:09:00','2026-09-18',NULL,NULL,'GOOD',NULL,'TEST','',NULL,'','ISSUED',NULL,NULL,'',NULL,'2026-09-17 17:09:48','admin','2026-09-17 17:09:48');
/*!40000 ALTER TABLE `tbl_tool_issuances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tools`
--

DROP TABLE IF EXISTS `tbl_tools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tools` (
  `tool_id` int(11) NOT NULL AUTO_INCREMENT,
  `tool_code` varchar(50) NOT NULL,
  `tool_name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `quantity_on_hand` int(11) NOT NULL DEFAULT 1,
  `current_location` varchar(200) DEFAULT NULL,
  `tool_condition` enum('NEW','GOOD','FAIR','POOR','DAMAGED') NOT NULL DEFAULT 'GOOD',
  `tool_status` enum('AVAILABLE','ASSIGNED','UNDER_REPAIR','DAMAGED','LOST','RETIRED') NOT NULL DEFAULT 'AVAILABLE',
  `assigned_to` varchar(100) DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`tool_id`),
  UNIQUE KEY `tool_code` (`tool_code`),
  KEY `idx_category` (`category`),
  KEY `idx_tool_status` (`tool_status`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tools`
--

LOCK TABLES `tbl_tools` WRITE;
/*!40000 ALTER TABLE `tbl_tools` DISABLE KEYS */;
INSERT INTO `tbl_tools` VALUES (1,'TL-2026-000001','Hydraulic Jack 10-Ton','Jacks','Enerpac',NULL,'EJ-2024-001',NULL,0.00,1,0,NULL,'GOOD','ASSIGNED',NULL,NULL,NULL,NULL,NULL,'2026-09-17 16:48:13','ADMIN','2026-10-05 11:11:01'),(2,'TL-2026-000002','Hydraulic Jack 5-Ton','Jacks','Enerpac','P391','EJ-2024-002','2024-01-15',12500.00,1,1,'Warehouse A - Bay 1','GOOD','AVAILABLE',NULL,NULL,NULL,NULL,'Medium-duty hydraulic jack','2026-09-17 16:48:13','ADMIN','2026-09-17 16:48:13'),(3,'TL-2026-000003','Impact Wrench 1-inch','Power Tools','Ingersoll Rand','IR-285B','IR-2023-045','2023-06-20',22000.00,1,0,'Warehouse A - Shelf 5','GOOD','ASSIGNED',NULL,NULL,NULL,NULL,'Pneumatic impact wrench','2026-09-17 16:48:13','ADMIN','2026-10-05 11:11:01'),(4,'TL-2026-000004','Torque Wrench 600mm','Hand Tools','Stanley','ST-600','SW-2023-078','2023-06-20',4500.00,1,0,'Warehouse A - Shelf 5','GOOD','ASSIGNED','Pedro Santos',NULL,NULL,NULL,'For wheel nuts torquing','2026-09-17 16:48:13','ADMIN','2026-10-05 11:11:01'),(5,'TL-2026-000005','Tool Box (Complete Set)','Tool Sets','Snap-on','SNAP-PRO-24','SN-2024-012','2024-03-10',45000.00,1,1,'Warehouse A - Cabinet 1','GOOD','AVAILABLE',NULL,NULL,NULL,NULL,'Complete mechanics tool set','2026-09-17 16:48:13','ADMIN','2026-09-17 16:48:13'),(6,'TL-2026-000006','Digital Tire Pressure Gauge','Measuring','Kwik-Way','KW-DIG-200','KW-2024-021','2024-02-05',2800.00,1,0,'Warehouse A - Shelf 3','GOOD','ASSIGNED','Mark Tan',NULL,NULL,NULL,'Calibrated monthly','2026-09-17 16:48:13','ADMIN','2026-10-05 11:11:01'),(7,'TL-2026-000007','Wheel Alignment Gauge','Measuring','Kwik-Way','KW-ALIGN-500','KA-2022-008','2022-09-15',65000.00,1,1,'Warehouse A - Bay 3','FAIR','AVAILABLE',NULL,NULL,NULL,NULL,'Needs recalibration','2026-09-17 16:48:13','ADMIN','2026-09-17 16:48:13'),(8,'TL-2026-000008','Portable Air Compressor','Power Tools','Ingersoll Rand','IR-P185','IA-2023-055','2023-11-01',85000.00,1,1,'Warehouse B - Bay 2','GOOD','UNDER_REPAIR',NULL,NULL,NULL,NULL,'Motor needs servicing','2026-09-17 16:48:13','ADMIN','2026-09-17 16:48:13'),(9,'TL-2026-000009','Grease Gun (Pneumatic)','Hand Tools','Lincoln','LIN-PG-300','LN-2024-033','2024-04-12',5200.00,1,0,'Warehouse A - Shelf 4','GOOD','ASSIGNED','Antonio Luna',NULL,NULL,NULL,'Currently issued to mechanic','2026-09-17 16:48:13','ADMIN','2026-10-05 11:11:01'),(10,'TL-2026-000010','Engine Crane 2-Ton','Lifting Equipment','OTC','OTC-2T-4500','OT-2021-010','2021-05-20',42000.00,1,1,'Warehouse B - Bay 1','DAMAGED','DAMAGED',NULL,NULL,NULL,NULL,'Hydraulic seal leaking','2026-09-17 16:48:13','ADMIN','2026-09-17 16:48:13'),(12,'TL-2026-000011','SCREW DRIVER','Power tool','INGKO','N/A','012321','2026-09-17',3000.00,3,1,'Warehouse 1','GOOD','ASSIGNED','JAMIE',NULL,'',NULL,'','2026-09-17 17:09:11','admin','2026-09-17 17:09:48');
/*!40000 ALTER TABLE `tbl_tools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_trip_assignments`
--

DROP TABLE IF EXISTS `tbl_trip_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_trip_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL,
  `vehicle_type` enum('RIGID','TRACTOR_CHASSIS','TRACTOR_RENTED_CHASSIS','RENTED_ALL') DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `truck_plate` varchar(50) DEFAULT NULL,
  `tractor_id` int(11) DEFAULT NULL,
  `tractor_plate` varchar(50) DEFAULT NULL,
  `chassis_id` int(11) DEFAULT NULL,
  `chassis_plate` varchar(50) DEFAULT NULL,
  `chassis_type` enum('OWNED','RENTED') DEFAULT 'OWNED',
  `vendor_id` int(11) DEFAULT NULL,
  `vendor_name` varchar(100) DEFAULT NULL,
  `rental_rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rental_start_date` date DEFAULT NULL,
  `rental_end_date` date DEFAULT NULL,
  `rental_agreement_no` varchar(100) DEFAULT NULL,
  `vendor_contact_person` varchar(100) DEFAULT NULL,
  `vendor_contact_number` varchar(50) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `helper_id` int(11) DEFAULT NULL,
  `helper_name` varchar(100) DEFAULT NULL,
  `assignment_date` date DEFAULT NULL,
  `dispatch_time` time DEFAULT NULL,
  `dispatch_location` varchar(255) DEFAULT NULL,
  `odometer_before_trip` decimal(15,2) DEFAULT 0.00,
  `fuel_level` decimal(5,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`assignment_id`),
  KEY `idx_trip_id` (`trip_id`),
  KEY `idx_truck_id` (`truck_id`),
  KEY `idx_tractor_id` (`tractor_id`),
  KEY `idx_chassis_id` (`chassis_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_driver_id` (`driver_id`),
  KEY `idx_helper_id` (`helper_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_trip_assignments`
--

LOCK TABLES `tbl_trip_assignments` WRITE;
/*!40000 ALTER TABLE `tbl_trip_assignments` DISABLE KEYS */;
INSERT INTO `tbl_trip_assignments` VALUES (15,16,'RIGID',6,'ABC-1234',NULL,NULL,NULL,NULL,'OWNED',NULL,NULL,0.00,'0000-00-00','0000-00-00','','','',15,'Anna Lopez',12,'Andres Bonifacio','2026-09-22','15:50:00','MAIN YARD',120000.00,10.00,'','2026-09-22 15:51:04','admin','2026-10-05 10:29:14'),(16,17,'TRACTOR_CHASSIS',NULL,NULL,16,'TRC-001',26,'CHS-001','OWNED',NULL,NULL,0.00,'0000-00-00','0000-00-00','','','',15,'Anna Lopez',12,'Andres Bonifacio','2026-09-23','08:43:00','MAIN YARD',50000.00,100.00,'','2026-09-23 08:44:06','admin','2026-10-05 10:29:14'),(22,23,'RIGID',7,'XYZ-5678',NULL,NULL,NULL,NULL,'OWNED',NULL,NULL,0.00,NULL,NULL,NULL,NULL,NULL,12,'Juan Dela Cruz',1,'KYLE HELPER','2026-09-20',NULL,NULL,10000.00,100.00,NULL,'2026-09-24 08:05:20','ADMIN','2026-10-05 10:29:14'),(23,24,'RIGID',9,'GHI-3456',NULL,NULL,NULL,NULL,'OWNED',NULL,NULL,0.00,NULL,NULL,NULL,NULL,NULL,13,'Maria Santos',12,'Andres Bonifacio','2026-09-21',NULL,NULL,10000.00,100.00,NULL,'2026-09-24 08:05:24','ADMIN','2026-10-05 10:29:14'),(24,25,'RIGID',6,'ABC-1234',NULL,NULL,NULL,NULL,'OWNED',NULL,NULL,0.00,NULL,NULL,NULL,NULL,NULL,14,'Pedro Reyes',13,'Emilio Aguinaldo','2026-09-19',NULL,NULL,10000.00,100.00,NULL,'2026-09-24 08:05:29','ADMIN','2026-10-05 10:29:14'),(25,26,'TRACTOR_CHASSIS',NULL,NULL,18,'TRC-003',28,'CHS-003','OWNED',NULL,NULL,0.00,NULL,NULL,NULL,NULL,NULL,15,'Anna Lopez',14,'Jose Rizal','2026-09-15',NULL,NULL,10000.00,100.00,NULL,'2026-09-24 08:05:34','ADMIN','2026-10-05 10:29:14'),(27,31,'TRACTOR_CHASSIS',NULL,NULL,16,'TRC-001',26,'CHS-001','OWNED',NULL,NULL,0.00,'0000-00-00','0000-00-00','','','',15,'Anna Lopez',12,'Andres Bonifacio','2026-09-24','11:00:00','MAIN YARD',80000.00,50.00,'','2026-09-24 11:00:31','admin','2026-10-05 10:29:14'),(29,33,'TRACTOR_CHASSIS',NULL,NULL,16,'TRC-001',26,'CHS-001','OWNED',NULL,NULL,0.00,'0000-00-00','0000-00-00','','','',15,'Anna Lopez',12,'Andres Bonifacio','2026-09-25','11:31:00','MAIN YARD',50000.00,50.00,'','2026-09-25 11:31:34','admin','2026-10-05 10:29:14'),(30,34,'TRACTOR_CHASSIS',NULL,NULL,17,'TRC-002',27,'CHS-002','OWNED',NULL,NULL,0.00,'0000-00-00','0000-00-00','','','',17,'Elena Rivera',17,'Antonio Luna','2026-09-25','14:25:00','MAIN YARD',50000.00,50.00,'','2026-09-25 14:26:12','admin','2026-10-05 10:29:14'),(37,41,'RIGID',8,'DEF-9012',NULL,NULL,NULL,NULL,'OWNED',NULL,NULL,0.00,NULL,NULL,NULL,NULL,NULL,13,'Maria Santos',14,'Jose Rizal','2026-10-03','06:00:00','LARGA Truck Yard, Valenzuela',12500.00,95.00,'Pre-trip inspection done 10/03','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(40,44,'RIGID',7,'XYZ-5678',NULL,NULL,NULL,NULL,'OWNED',NULL,NULL,0.00,NULL,NULL,NULL,NULL,NULL,14,'Pedro Reyes',13,'Emilio Aguinaldo','2026-10-05','06:00:00','LARGA Truck Yard',78450.00,90.00,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_trip_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_trip_cargo_items`
--

DROP TABLE IF EXISTS `tbl_trip_cargo_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_trip_cargo_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `quantity` decimal(15,2) DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `weight` decimal(15,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`item_id`),
  KEY `idx_trip_id` (`trip_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_trip_cargo_items`
--

LOCK TABLES `tbl_trip_cargo_items` WRITE;
/*!40000 ALTER TABLE `tbl_trip_cargo_items` DISABLE KEYS */;
INSERT INTO `tbl_trip_cargo_items` VALUES (10,16,'cargo test item',50.00,'cartoons',500.00,'','2026-09-23 08:03:50','admin','2026-09-23 08:03:50'),(11,17,'test',50.00,'cartons',50.00,'test','2026-09-23 08:43:32','admin','2026-09-23 08:43:32'),(17,23,'General Merchandise',10.00,'pallets',5000.00,NULL,'2026-09-24 08:05:19','ADMIN','2026-09-24 08:05:19'),(18,24,'Raw Materials',20.00,'bags',8000.00,NULL,'2026-09-24 08:05:24','ADMIN','2026-09-24 08:05:24'),(19,25,'Auto Parts',50.00,'boxes',3000.00,NULL,'2026-09-24 08:05:29','ADMIN','2026-09-24 08:05:29'),(20,26,'Mining Equipment Parts',100.00,'units',12000.00,NULL,'2026-09-24 08:05:34','ADMIN','2026-09-24 08:05:34'),(26,31,'LED TV 55\"',50.00,'Pcs',1000.00,'','2026-09-24 10:59:28','admin','2026-09-24 10:59:28'),(27,31,'LED TV 43\"',40.00,'Pcs',600.00,'','2026-09-24 10:59:28','admin','2026-09-24 10:59:28'),(28,31,'Sound Bar',60.00,'Pcs',899.97,'','2026-09-24 10:59:28','admin','2026-09-24 10:59:28'),(30,33,'DUMBELL',25.00,'PCS',1500.00,'','2026-09-25 11:31:02','admin','2026-09-25 11:31:02'),(31,33,'CHEST RACK',25.00,'PCS',1000.00,'','2026-09-25 11:31:02','admin','2026-09-25 11:31:02'),(32,34,'DUMBELL',25.00,'PCS',1500.00,'','2026-09-25 14:25:25','admin','2026-09-25 14:25:25'),(33,34,'CHEST RACK',25.00,'PCS',1000.00,'','2026-09-25 14:25:25','admin','2026-09-25 14:25:25'),(34,41,'Bottled Water 500ml (24s) — pallets',16.00,'Pallets',6400.00,'','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(35,41,'Canned Goods Assorted — pallets',8.00,'Pallets',3200.00,'Fragile labels','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(36,44,'Bond paper (boxes) — pallets',8.00,'Pallets',2800.00,'','2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(37,44,'Water dispensers — pallets',4.00,'Pallets',1400.00,'Upright only','2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_trip_cargo_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_trip_waypoints`
--

DROP TABLE IF EXISTS `tbl_trip_waypoints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_trip_waypoints` (
  `waypoint_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`waypoint_id`),
  KEY `idx_trip_id` (`trip_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_trip_waypoints`
--

LOCK TABLES `tbl_trip_waypoints` WRITE;
/*!40000 ALTER TABLE `tbl_trip_waypoints` DISABLE KEYS */;
INSERT INTO `tbl_trip_waypoints` VALUES (42,16,1,'GARAGE','Garage','Naic',NULL,NULL,'2026-09-22 15:58:00','2026-09-22 15:58:00','2026-09-23 07:51:00','2026-09-23 07:52:00','DEPARTED','','','','2026-09-22 15:58:15','admin','2026-09-23 07:52:04'),(43,16,2,'PICKUP_LOCATION','Manila Warehouse','Manila',NULL,NULL,'2026-09-22 16:58:00','2026-09-22 17:58:00','2026-09-23 07:52:00','2026-09-23 07:52:00','DEPARTED','','','','2026-09-22 15:58:32','admin','2026-09-23 07:52:11'),(44,16,3,'DELIVERY_DESTINATION','Cavite warehouse','Bacoor',NULL,NULL,'2026-09-22 18:58:00','2026-09-22 19:58:00','2026-09-23 08:06:00','2026-09-23 08:06:00','DEPARTED','','','test','2026-09-22 15:58:53','admin','2026-09-23 08:06:45'),(45,16,4,'GARAGE','Garage','Naic',NULL,NULL,'2026-09-22 20:59:00','2026-09-22 20:59:00','2026-09-23 08:06:00',NULL,'COMPLETED','',NULL,'','2026-09-22 15:59:18','admin','2026-09-23 08:06:48'),(46,17,1,'GARAGE','Garage','Naic',NULL,NULL,'2026-09-23 08:50:00','2026-09-23 08:55:00','2026-09-23 08:51:00','2026-09-23 08:51:00','DEPARTED','','','','2026-09-23 08:50:21','admin','2026-09-23 08:51:36'),(47,17,2,'PICKUP_LOCATION','Cavite','Bacoor',NULL,NULL,'2026-09-23 09:50:00','2026-09-23 09:53:00','2026-09-23 08:56:00','2026-09-23 08:56:00','DEPARTED','','','','2026-09-23 08:50:41','admin','2026-09-23 08:56:04'),(48,17,3,'DELIVERY_DESTINATION','Taguig','Taguig',NULL,NULL,'2026-09-23 10:50:00','2026-09-23 10:50:00','2026-09-23 08:56:00','2026-09-23 08:56:00','DEPARTED','','','','2026-09-23 08:51:02','admin','2026-09-23 08:56:10'),(49,17,4,'GARAGE','Garage','Naic',NULL,NULL,'2026-09-23 00:51:00','2026-09-23 00:51:00','2026-09-23 09:19:00',NULL,'COMPLETED','',NULL,'','2026-09-23 08:51:15','admin','2026-09-23 09:19:32'),(50,23,1,'PICKUP_LOCATION','Manila',NULL,NULL,NULL,'2026-09-20 08:00:00',NULL,'2026-09-20 08:00:00',NULL,'ARRIVED','Arrived at pickup',NULL,NULL,'2026-09-24 08:05:20','ADMIN','2026-09-24 08:05:21'),(51,23,2,'DELIVERY_DESTINATION','Batangas',NULL,NULL,NULL,'2026-09-21 14:00:00',NULL,'2026-09-21 14:00:00',NULL,'COMPLETED','Arrived at delivery destination',NULL,NULL,'2026-09-24 08:05:20','ADMIN','2026-09-24 08:05:22'),(52,24,1,'PICKUP_LOCATION','Manila',NULL,NULL,NULL,'2026-09-21 08:00:00',NULL,'2026-09-21 08:00:00',NULL,'ARRIVED','Arrived at pickup',NULL,NULL,'2026-09-24 08:05:25','ADMIN','2026-09-24 08:05:26'),(53,24,2,'DELIVERY_DESTINATION','Pampanga',NULL,NULL,NULL,'2026-09-22 14:00:00',NULL,'2026-09-22 14:00:00',NULL,'COMPLETED','Arrived at delivery destination',NULL,NULL,'2026-09-24 08:05:25','ADMIN','2026-09-24 08:05:27'),(54,25,1,'PICKUP_LOCATION','Manila',NULL,NULL,NULL,'2026-09-19 08:00:00',NULL,'2026-09-19 08:00:00',NULL,'ARRIVED','Arrived at pickup',NULL,NULL,'2026-09-24 08:05:30','ADMIN','2026-09-24 08:05:31'),(55,25,2,'DELIVERY_DESTINATION','Laguna',NULL,NULL,NULL,'2026-09-20 14:00:00',NULL,'2026-09-20 14:00:00',NULL,'COMPLETED','Arrived at delivery destination',NULL,NULL,'2026-09-24 08:05:30','ADMIN','2026-09-24 08:05:32'),(56,26,1,'PICKUP_LOCATION','Davao Port',NULL,NULL,NULL,'2026-09-15 08:00:00',NULL,'2026-09-15 08:00:00',NULL,'ARRIVED','Arrived at pickup',NULL,NULL,'2026-09-24 08:05:34','ADMIN','2026-09-24 08:05:36'),(57,26,2,'DELIVERY_DESTINATION','Mining Site',NULL,NULL,NULL,'2026-09-17 14:00:00',NULL,'2026-09-17 14:00:00',NULL,'ARRIVED','Arrived at delivery destination',NULL,NULL,'2026-09-24 08:05:34','ADMIN','2026-09-24 08:05:37'),(58,26,3,'RETURN_POINT','Container Return Yard',NULL,NULL,NULL,'2026-09-18 10:00:00',NULL,'2026-09-18 10:00:00',NULL,'COMPLETED','Empty container returned',NULL,NULL,'2026-09-24 08:05:35','ADMIN','2026-09-24 08:05:39'),(62,31,1,'PORT_TERMINAL','MANILA PORT','MANILA',NULL,NULL,'2026-09-24 11:08:00','2026-09-24 11:08:00','2026-09-24 11:11:00','2026-09-24 11:11:00','DEPARTED','','','','2026-09-24 11:08:38','admin','2026-09-24 11:11:25'),(63,31,2,'PICKUP_LOCATION','MANILA WAREHOUSE','MANILA',NULL,NULL,'2026-09-24 00:08:00','2026-09-24 01:08:00','2026-09-24 11:11:00','2026-09-24 11:11:00','DEPARTED','','','','2026-09-24 11:09:00','admin','2026-09-24 11:11:30'),(64,31,3,'DELIVERY_DESTINATION','TAGUIG WAREHOUSE','TAGUIG',NULL,NULL,'2026-09-24 01:09:00','2026-09-24 02:09:00','2026-09-24 11:11:00','2026-09-24 11:11:00','DEPARTED','','','','2026-09-24 11:09:33','admin','2026-09-24 11:11:33'),(65,31,4,'PORT_TERMINAL','MANILA PORT','TES',NULL,NULL,'2026-09-24 04:09:00','2026-09-24 05:09:00','2026-09-24 11:11:00','2026-09-24 11:11:00','DEPARTED','','','','2026-09-24 11:09:53','admin','2026-09-24 11:11:41'),(66,31,5,'GARAGE','GARAGE','BACOOR',NULL,NULL,'2026-09-24 06:10:00','2026-09-24 07:10:00','2026-09-24 13:05:00',NULL,'COMPLETED','',NULL,'','2026-09-24 11:10:09','admin','2026-09-24 13:05:59'),(68,33,1,'EMPTY_CONTAINER_PICKUP','MANILA PORT','MANILA',NULL,NULL,'2026-09-25 00:32:00','2026-09-25 01:32:00','2026-09-25 11:38:00','2026-09-25 11:38:00','DEPARTED','','','','2026-09-25 11:32:30','admin','2026-09-25 11:38:46'),(69,33,2,'PICKUP_LOCATION','MANILA WAREHOUSE','MANILA',NULL,NULL,'2026-09-25 01:32:00','2026-09-25 02:32:00','2026-09-25 11:39:00','2026-09-25 11:40:00','DEPARTED','','','','2026-09-25 11:32:56','admin','2026-09-25 11:40:03'),(70,33,3,'DELIVERY_DESTINATION','CAVITE WAREHOUSE','BACOOR',NULL,NULL,'2026-09-25 02:33:00','2026-09-25 03:33:00','2026-09-25 11:40:00','2026-09-25 11:46:00','DEPARTED','','','','2026-09-25 11:33:27','admin','2026-09-25 11:46:17'),(71,33,4,'PORT_TERMINAL','MANILA PORT','MANILA',NULL,NULL,'2026-09-25 04:33:00','2026-09-25 05:34:00','2026-09-25 11:47:00',NULL,'COMPLETED','',NULL,'','2026-09-25 11:34:03','admin','2026-09-25 11:47:15'),(72,34,1,'EMPTY_CONTAINER_PICKUP','MANILA PORT','MANILA',NULL,NULL,'2026-09-25 15:26:00','2026-09-25 16:26:00','2026-09-25 14:42:00','2026-09-25 14:45:00','DEPARTED','','','','2026-09-25 14:26:58','admin','2026-09-25 14:45:38'),(73,34,2,'PICKUP_LOCATION','MANILA WAREHOUSE','MANILA',NULL,NULL,'2026-09-25 16:30:00','2026-09-25 17:27:00','2026-09-25 14:45:00','2026-09-25 14:45:00','DEPARTED','','','','2026-09-25 14:27:27','admin','2026-09-25 14:45:52'),(74,34,3,'DELIVERY_DESTINATION','CAVITE WAREHOUSE','BACOOR',NULL,NULL,'2026-09-25 18:27:00','2026-09-25 19:27:00','2026-09-25 14:45:00','2026-09-25 15:04:00','DEPARTED','','','','2026-09-25 14:27:50','admin','2026-09-25 15:04:42'),(75,34,4,'PORT_TERMINAL','MANILA PORTT','MANILA',NULL,NULL,'2026-09-25 20:28:00','2026-09-25 20:34:00','2026-09-25 15:05:00',NULL,'COMPLETED','',NULL,'','2026-09-25 14:28:16','admin','2026-09-25 15:05:40'),(76,41,1,'GARAGE','LARGA Truck Yard',NULL,'Valenzuela City','Metro Manila','2026-10-04 05:30:00','2026-10-04 06:00:00',NULL,'2026-10-04 06:05:00','DEPARTED',NULL,'Left on time',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(77,41,2,'PICKUP_LOCATION','ABC Manufacturing Main Plant',NULL,'Calamba','Laguna','2026-10-04 07:30:00','2026-10-04 09:00:00','2026-10-04 07:40:00','2026-10-04 09:10:00','DEPARTED','Light traffic on SLEX','24 pallets loaded and sealed',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(78,41,3,'DELIVERY_DESTINATION','ABC Distribution Center',NULL,'Tondo, Manila','Metro Manila','2026-10-04 11:30:00','2026-10-04 13:00:00','2026-10-04 11:50:00','2026-10-04 13:05:00','DEPARTED','Unloading at Dock 3','Empty, returning',NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(79,41,4,'RETURN_POINT','LARGA Truck Yard',NULL,'Valenzuela City','Metro Manila','2026-10-04 14:30:00',NULL,'2026-10-04 14:40:00',NULL,'COMPLETED','Truck back at yard',NULL,NULL,'2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(82,44,1,'GARAGE','LARGA Truck Yard',NULL,'Valenzuela City',NULL,NULL,NULL,NULL,'2026-10-05 06:05:00','DEPARTED',NULL,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(83,44,2,'PICKUP_LOCATION','XYZ Warehouse 2',NULL,'Pasig City',NULL,NULL,NULL,'2026-10-05 06:50:00','2026-10-05 08:00:00','DEPARTED',NULL,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(84,44,3,'DELIVERY_DESTINATION','XYZ Head Office',NULL,'Manila',NULL,NULL,NULL,'2026-10-05 09:15:00','2026-10-05 10:00:00','DEPARTED',NULL,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11'),(85,44,4,'RETURN_POINT','LARGA Truck Yard',NULL,'Valenzuela City',NULL,NULL,NULL,'2026-10-05 11:10:00',NULL,'COMPLETED',NULL,NULL,NULL,'2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_trip_waypoints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_trips`
--

DROP TABLE IF EXISTS `tbl_trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_trips` (
  `trip_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `trip_status` enum('DRAFT','SCHEDULED','ASSIGNED','DISPATCHED','IN_TRANSIT','DELIVERED','COMPLETED','CANCELLED') NOT NULL DEFAULT 'DRAFT',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`trip_id`),
  UNIQUE KEY `idx_trip_code` (`trip_code`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_trips`
--

LOCK TABLES `tbl_trips` WRITE;
/*!40000 ALTER TABLE `tbl_trips` DISABLE KEYS */;
INSERT INTO `tbl_trips` VALUES (16,'TRP-2026-000001',1,'BK-TEST-0922026','Trucking Service','ROUND_TRIP','NORMAL','2026-09-22','2026-09-22','2026-09-22',NULL,'MANILA','CAVITE','test',50,'Cartoon',500.00,'test','COMPLETED','test','2026-09-22 15:50:30','admin','2026-09-23 08:05:52'),(17,'TRP-2026-000002',4,'BK-TEST2-09222026','','ROUND_TRIP','NORMAL','2026-09-22','2026-09-22','2026-09-22',NULL,'CAVITE','TAGUIG','test',100,'KG',500.00,'TEST','COMPLETED','','2026-09-22 15:56:46','admin','2026-09-23 09:35:47'),(23,'TRP-2026-000003',5,NULL,'Trucking','ONE_WAY','NORMAL','2026-09-20','2026-09-20','2026-09-21',NULL,'Manila','Batangas','General Merchandise',10,'pallets',5000.00,NULL,'COMPLETED','E2E test scenario 1','2026-09-24 08:05:19','ADMIN','2026-09-24 08:05:22'),(24,'TRP-2026-000004',1,NULL,'Trucking','ONE_WAY','NORMAL','2026-09-21','2026-09-21','2026-09-22',NULL,'Manila','Pampanga','Raw Materials',20,'bags',8000.00,NULL,'COMPLETED','E2E test scenario 2','2026-09-24 08:05:24','ADMIN','2026-09-24 08:05:27'),(25,'TRP-2026-000005',9,NULL,'Trucking','ONE_WAY','NORMAL','2026-09-19','2026-09-19','2026-09-20',NULL,'Manila','Laguna','Auto Parts',50,'boxes',3000.00,NULL,'COMPLETED','E2E test scenario 3','2026-09-24 08:05:29','ADMIN','2026-09-24 08:05:32'),(26,'TRP-2026-000006',8,NULL,'Trucking','ONE_WAY','NORMAL','2026-09-15','2026-09-15','2026-09-17',NULL,'Davao Port','Mining Site','Mining Equipment Parts',100,'units',12000.00,NULL,'COMPLETED','E2E test scenario 4','2026-09-24 08:05:33','ADMIN','2026-09-24 08:05:39'),(31,'TRP-2026-000008',1,'BK-TEST-09232026-V1','Trucking Service','ONE_WAY','NORMAL','2026-09-24','2026-09-24','2026-09-24',NULL,'MANILA','TAGUIG','Consumer Electronics',150,'Cartons',2500.00,'Handle with care','COMPLETED','','2026-09-24 10:59:28','admin','2026-09-24 13:05:59'),(33,'TRP-2026-000010',1,'BK-TEST-09252026V1','Trucking Service','ONE_WAY','HIGH','2026-09-25','2026-09-25','2026-09-25',NULL,'MANILA','CAVITE','GYM EQUIPMENT',50,'PCS',2500.00,'','COMPLETED','','2026-09-25 11:31:02','admin','2026-10-05 11:20:54'),(34,'TRP-2026-000009',1,'BK-09252026V2','Trucking Service','ROUND_TRIP','NORMAL','2026-09-25','2026-09-25','2026-09-25',NULL,'MANILA','CAVITE','GYM EQUIPMENT',50,'PCS',2500.00,'NA','COMPLETED','','2026-09-25 14:25:25','admin','2026-09-25 15:05:40'),(41,'TRP-2026-000011',1,'ABC-PO-2026-1004','Trucking Service','ONE_WAY','HIGH','2026-10-04','2026-10-04','2026-10-04',NULL,'ABC Manufacturing Main Plant, Calamba, Laguna','ABC Distribution Center, Tondo, Manila','Packaged consumer goods on pallets',24,'Pallets',9600.00,'Keep pallets dry. Unload at Dock 3. Call receiving 30 min before arrival.','COMPLETED','SAMPLE end-to-end record for review','2026-10-05 11:43:18','ADMIN','2026-10-05 11:43:18'),(44,'TRP-2026-000012',2,'XYZ-PO-E2E-1005','Trucking Service','ONE_WAY','NORMAL','2026-10-05','2026-10-05','2026-10-05',NULL,'XYZ Trading Warehouse 2, Pasig City','XYZ Trading Head Office, Manila','Office supplies and appliances',12,'Pallets',4200.00,'Handle appliances upright.','COMPLETED','E2E TEST — full transaction','2026-10-05 16:41:11','ADMIN','2026-10-05 16:41:11');
/*!40000 ALTER TABLE `tbl_trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_truck_documents`
--

DROP TABLE IF EXISTS `tbl_truck_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_truck_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `truck_id` int(11) NOT NULL,
  `document_type` enum('REGISTRATION','INSURANCE','SOLIDARITY_STICKER','EAGLE_STICKER','GENERAL_STICKER','FRANCHISE','ACCREDITATION','OTHER') NOT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `provider_name` varchar(100) DEFAULT NULL,
  `policy_number` varchar(100) DEFAULT NULL,
  `coverage_type` varchar(100) DEFAULT NULL,
  `premium` decimal(15,2) NOT NULL DEFAULT 0.00,
  `coverage_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `document_attachment` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`document_id`),
  KEY `idx_truck_id` (`truck_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_truck_documents`
--

LOCK TABLES `tbl_truck_documents` WRITE;
/*!40000 ALTER TABLE `tbl_truck_documents` DISABLE KEYS */;
INSERT INTO `tbl_truck_documents` VALUES (1,1,'REGISTRATION','TEST','2026-08-28','2030-10-28','TEST','TEST','Comprehensive',1.00,1.00,1.00,'uploads/truck_documents/DOC_1_1787910359.pdf','TEST','2026-08-28 17:45:44','ADMIN','2026-08-28 17:45:59'),(2,1,'INSURANCE','TEST','2026-08-28','2030-03-28','test','asd','Comprehensive',1.00,11.00,1.00,'uploads/truck_documents/DOC_1_1787983999.pdf','TEST','2026-08-28 17:57:15','ADMIN','2026-08-29 14:13:19'),(3,35,'INSURANCE','TEST','0000-00-00','0000-00-00','ABC INSURANCE','PO-1231','Comprehensive',250000.00,250000.00,1231.00,'uploads/truck_documents/DOC_35_1788014877.pdf','TEST','2026-08-29 22:47:57','ADMIN','2026-08-29 22:47:57');
/*!40000 ALTER TABLE `tbl_truck_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_trucks`
--

DROP TABLE IF EXISTS `tbl_trucks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_trucks` (
  `truck_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `fuel_tank_capacity` decimal(15,2) DEFAULT 0.00,
  `load_capacity` decimal(15,2) DEFAULT 0.00,
  `current_odometer` decimal(15,2) DEFAULT 0.00,
  `acquisition_date` date DEFAULT NULL,
  `acquired_from` varchar(100) DEFAULT NULL,
  `acquired_from_branch` varchar(100) DEFAULT NULL,
  `account_manager` varchar(100) DEFAULT NULL,
  `ownership` enum('COMPANY_OWNED','LEASED','RENTED') NOT NULL DEFAULT 'COMPANY_OWNED',
  `truck_status` enum('AVAILABLE','ASSIGNED','DISPATCHED','IN_TRANSIT','RETURNING','UNDER_MAINTENANCE','OUT_OF_SERVICE','RETIRED') NOT NULL DEFAULT 'AVAILABLE',
  `truck_image` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`truck_id`),
  UNIQUE KEY `idx_truck_code` (`truck_code`),
  UNIQUE KEY `plate_number` (`plate_number`),
  UNIQUE KEY `chassis_number` (`chassis_number`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_trucks`
--

LOCK TABLES `tbl_trucks` WRITE;
/*!40000 ALTER TABLE `tbl_trucks` DISABLE KEYS */;
INSERT INTO `tbl_trucks` VALUES (1,'TRC-2026-0001','TRAILER','ABC-12345','MV-012931','','CLOSED VAN','ISUZU','F-SERIES',2022,'CASC','ACAC','Diesel',1.00,2.00,2.00,'2026-08-28','ABC MOTOR','','TEST','COMPANY_OWNED','AVAILABLE','uploads/trucks/TRK_1_1787910312.png','TEST','2026-08-28 17:45:12','ADMIN','2026-10-05 10:24:44'),(3,'TRC-2026-0002','TRACTOR','truckhead','','','','','',0000,'','','',0.00,0.00,0.00,'0000-00-00','','','','COMPANY_OWNED','AVAILABLE',NULL,'','2026-08-29 18:57:47','ADMIN','2026-10-05 11:20:54'),(5,'TRK-2026-0001','RIGID','CMPL-TASDA','ASDASD','ASD','ASDA','SDASD','AASDA',0000,'412SADASD','','',0.00,0.00,0.00,'0000-00-00','','','','COMPANY_OWNED','AVAILABLE',NULL,'','2026-08-29 19:21:40','ADMIN','2026-10-05 10:24:44'),(6,'TRK-2026-0031','RIGID','ABC-1234','MV-2026-0001','10-Wheeler','Closed Van','Isuzu','F-Series',2023,'CHS-2026-0001','ENG-2026-0001','Diesel',300.00,15000.00,45230.50,'2023-01-15','Isuzu Motors','Manila Branch','Juan Dela Cruz','COMPANY_OWNED','AVAILABLE',NULL,'New unit, fully serviced','2026-08-29 21:21:40','ADMIN','2026-10-05 11:20:54'),(7,'TRK-2026-0002','RIGID','XYZ-5678','MV-2026-0002','6-Wheeler','Open Truck','Hino','300 Series',2022,'CHS-2026-0002','ENG-2026-0002','Diesel',200.00,8000.00,78450.20,'2022-06-20','Hino Motors','Cebu Branch','Maria Santos','COMPANY_OWNED','AVAILABLE',NULL,'Regular maintenance done','2026-08-29 21:21:40','ADMIN','2026-10-05 16:41:11'),(8,'TRK-2026-0003','RIGID','DEF-9012','MV-2026-0003','10-Wheeler','Refrigerated Van','Mitsubishi','Fuso',2024,'CHS-2026-0003','ENG-2026-0003','Diesel',350.00,12000.00,12500.00,'2024-02-10','Mitsubishi Motors','Davao Branch','Pedro Reyes','COMPANY_OWNED','AVAILABLE',NULL,'Brand new refrigerated unit','2026-08-29 21:21:40','ADMIN','2026-10-05 11:43:18'),(9,'TRK-2026-0004','RIGID','GHI-3456','MV-2026-0004','8-Wheeler','Flatbed','Isuzu','G-Series',2021,'CHS-2026-0004','ENG-2026-0004','Diesel',250.00,10000.00,124500.00,'2021-11-05','Isuzu Motors','Manila Branch','Anna Lopez','COMPANY_OWNED','AVAILABLE',NULL,'For heavy equipment transport','2026-08-29 21:21:40','ADMIN','2026-10-05 10:24:44'),(10,'TRK-2026-0005','RIGID','JKL-7890','MV-2026-0005','6-Wheeler','Dump Truck','Hino','500 Series',2022,'CHS-2026-0005','ENG-2026-0005','Diesel',180.00,9000.00,87600.00,'2022-03-15','Hino Motors','Pampanga Branch','Ramon Cruz','COMPANY_OWNED','AVAILABLE',NULL,'For construction materials','2026-08-29 21:21:40','ADMIN','2026-10-05 10:24:44'),(11,'TRK-2026-0006','RIGID','MNO-2345','MV-2026-0006','10-Wheeler','Wing Van','Fuso','Canter',2023,'CHS-2026-0006','ENG-2026-0006','Diesel',280.00,14000.00,34200.00,'2023-08-01','Mitsubishi Motors','Cebu Branch','Elena Rivera','COMPANY_OWNED','AVAILABLE',NULL,'For express delivery','2026-08-29 21:21:40','ADMIN','2026-10-05 10:24:44'),(12,'TRK-2026-0007','RIGID','PQR-6789','MV-2026-0007','8-Wheeler','Tank Trailer','Isuzu','F-Series',2020,'CHS-2026-0007','ENG-2026-0007','Diesel',400.00,25000.00,156700.00,'2020-09-10','Isuzu Motors','Manila Branch','Mark Tan','COMPANY_OWNED','AVAILABLE',NULL,'For fuel transport','2026-08-29 21:21:40','ADMIN','2026-10-05 10:24:44'),(13,'TRK-2026-0008','RIGID','STU-9012','MV-2026-0008','6-Wheeler','Box Truck','Hino','300 Series',2024,'CHS-2026-0008','ENG-2026-0008','Diesel',220.00,7000.00,8200.00,'2024-01-20','Hino Motors','Davao Branch','Grace Santos','COMPANY_OWNED','AVAILABLE',NULL,'Brand new box truck','2026-08-29 21:21:40','ADMIN','2026-10-05 10:24:44'),(14,'TRK-2026-0009','RIGID','VWX-3456','MV-2026-0009','10-Wheeler','Curtain Side','Mitsubishi','Fuso',2022,'CHS-2026-0009','ENG-2026-0009','Diesel',300.00,16000.00,65400.00,'2022-05-25','Mitsubishi Motors','Cebu Branch','Francisco Garcia','COMPANY_OWNED','AVAILABLE',NULL,'For general cargo','2026-08-29 21:21:40','ADMIN','2026-10-05 10:24:44'),(15,'TRK-2026-0010','RIGID','YZA-7890','MV-2026-0010','8-Wheeler','Drop Side','Isuzu','G-Series',2021,'CHS-2026-0010','ENG-2026-0010','Diesel',260.00,12000.00,98500.00,'2021-12-01','Isuzu Motors','Manila Branch','Leticia Mendez','COMPANY_OWNED','AVAILABLE',NULL,'For agricultural products','2026-08-29 21:21:40','ADMIN','2026-10-05 10:24:44'),(16,'TRK-2026-0011','TRACTOR','TRC-001','MV-2026-0011','Tractor Head','Heavy Duty','Isuzu','Giga',2023,'CHS-2026-0011','ENG-2026-0011','Diesel',400.00,30000.00,45600.00,'2023-03-10','Isuzu Motors','Manila Branch','Ramon Santos','COMPANY_OWNED','AVAILABLE',NULL,'Heavy duty tractor for container hauling','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(17,'TRK-2026-0012','TRACTOR','TRC-002','MV-2026-0012','Tractor Head','Medium Duty','Hino','700 Series',2022,'CHS-2026-0012','ENG-2026-0012','Diesel',350.00,25000.00,78200.00,'2022-07-15','Hino Motors','Cebu Branch','Maria Fernandez','COMPANY_OWNED','AVAILABLE',NULL,'Medium duty tractor','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(18,'TRK-2026-0013','TRACTOR','TRC-003','MV-2026-0013','Tractor Head','Heavy Duty','Mitsubishi','Super Great',2024,'CHS-2026-0013','ENG-2026-0013','Diesel',450.00,35000.00,8900.00,'2024-01-05','Mitsubishi Motors','Davao Branch','Jose Rizal','COMPANY_OWNED','AVAILABLE',NULL,'Brand new tractor for long haul','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(19,'TRK-2026-0014','TRACTOR','TRC-004','MV-2026-0014','Tractor Head','Heavy Duty','Isuzu','Giga',2021,'CHS-2026-0014','ENG-2026-0014','Diesel',400.00,30000.00,125400.00,'2021-10-20','Isuzu Motors','Manila Branch','Antonio Luna','COMPANY_OWNED','AVAILABLE',NULL,'For inter-island hauling','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(20,'TRK-2026-0015','TRACTOR','TRC-005','MV-2026-0015','Tractor Head','Medium Duty','Hino','500 Series',2022,'CHS-2026-0015','ENG-2026-0015','Diesel',320.00,22000.00,98700.00,'2022-04-12','Hino Motors','Pampanga Branch','Gregorio Del Pilar','COMPANY_OWNED','AVAILABLE',NULL,'Medium duty for regional routes','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(21,'TRK-2026-0016','TRACTOR','TRC-006','MV-2026-0016','Tractor Head','Heavy Duty','Fuso','Super Great',2023,'CHS-2026-0016','ENG-2026-0016','Diesel',420.00,32000.00,34500.00,'2023-06-01','Mitsubishi Motors','Cebu Branch','Diego Silang','COMPANY_OWNED','AVAILABLE',NULL,'For heavy cargo transport','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(22,'TRK-2026-0017','TRACTOR','TRC-007','MV-2026-0017','Tractor Head','Heavy Duty','Isuzu','Giga',2020,'CHS-2026-0017','ENG-2026-0017','Diesel',380.00,28000.00,167800.00,'2020-08-15','Isuzu Motors','Manila Branch','Gabriela Silang','COMPANY_OWNED','AVAILABLE',NULL,'For container yard operations','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(23,'TRK-2026-0018','TRACTOR','TRC-008','MV-2026-0018','Tractor Head','Medium Duty','Hino','700 Series',2024,'CHS-2026-0018','ENG-2026-0018','Diesel',340.00,24000.00,5600.00,'2024-02-14','Hino Motors','Davao Branch','Andres Bonifacio','COMPANY_OWNED','AVAILABLE',NULL,'Brand new medium duty','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(24,'TRK-2026-0019','TRACTOR','TRC-009','MV-2026-0019','Tractor Head','Heavy Duty','Mitsubishi','Super Great',2022,'CHS-2026-0019','ENG-2026-0019','Diesel',430.00,33000.00,65400.00,'2022-09-25','Mitsubishi Motors','Cebu Branch','Emilio Aguinaldo','COMPANY_OWNED','AVAILABLE',NULL,'For long distance hauling','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(25,'TRK-2026-0020','TRACTOR','TRC-010','MV-2026-0020','Tractor Head','Heavy Duty','Isuzu','Giga',2021,'CHS-2026-0020','ENG-2026-0020','Diesel',390.00,29000.00,112300.00,'2021-11-30','Isuzu Motors','Manila Branch','Apolinario Mabini','COMPANY_OWNED','AVAILABLE',NULL,'For bulk cargo transport','2026-08-29 21:21:51','ADMIN','2026-10-05 10:24:44'),(26,'TRK-2026-0021','TRAILER','CHS-001','MV-2026-0021','Trailer','Container Chassis','Utility','40ft Container',2023,'CHS-2026-0021',NULL,NULL,NULL,35000.00,23400.00,'2023-03-20','Container Services Inc.','Manila Branch','Carlos Aquino','COMPANY_OWNED','AVAILABLE',NULL,'40ft container chassis for heavy loads','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(27,'TRK-2026-0022','TRAILER','CHS-002','MV-2026-0022','Trailer','Flatbed Trailer','Utility','20ft Container',2022,'CHS-2026-0022',NULL,NULL,NULL,25000.00,45600.00,'2022-08-10','Trailer Solutions','Cebu Branch','Rosa Sevilla','COMPANY_OWNED','AVAILABLE',NULL,'20ft container chassis for regional delivery','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(28,'TRK-2026-0023','TRAILER','CHS-003','MV-2026-0023','Trailer','Refrigerated Trailer','Thermo King','40ft Reefer',2024,'CHS-2026-0023',NULL,NULL,NULL,28000.00,5600.00,'2024-01-15','Cold Chain Logistics','Davao Branch','Rafael Palma','COMPANY_OWNED','AVAILABLE',NULL,'40ft refrigerated trailer for cold chain','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(29,'TRK-2026-0024','TRAILER','CHS-004','MV-2026-0024','Trailer','Drop Deck Trailer','Utility','Lowboy',2021,'CHS-2026-0024',NULL,NULL,NULL,45000.00,87600.00,'2021-12-01','Heavy Equipment Logistics','Manila Branch','Cecilio Apostol','COMPANY_OWNED','AVAILABLE',NULL,'For heavy equipment transport','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(30,'TRK-2026-0025','TRAILER','CHS-005','MV-2026-0025','Trailer','Tanker Trailer','Utility','Fuel Tanker',2022,'CHS-2026-0025',NULL,NULL,NULL,40000.00,54300.00,'2022-05-20','Fuel Logistics Corp','Pampanga Branch','Jose Garcia Villa','COMPANY_OWNED','AVAILABLE',NULL,'For fuel and liquid transport','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(31,'TRK-2026-0026','TRAILER','CHS-006','MV-2026-0026','Trailer','Dry Van Trailer','Utility','53ft Dry Van',2023,'CHS-2026-0026',NULL,NULL,NULL,32000.00,23400.00,'2023-07-01','Van Logistics Inc.','Cebu Branch','Fernando Amorsolo','COMPANY_OWNED','AVAILABLE',NULL,'53ft dry van for general cargo','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(32,'TRK-2026-0027','TRAILER','CHS-007','MV-2026-0027','Trailer','Open Deck Trailer','Utility','Open Flatbed',2020,'CHS-2026-0027',NULL,NULL,NULL,30000.00,123400.00,'2020-09-15','Deck Logistics','Manila Branch','Juan Nakpil','COMPANY_OWNED','AVAILABLE',NULL,'For open cargo transport','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(33,'TRK-2026-0028','TRAILER','CHS-008','MV-2026-0028','Trailer','Side Curtain Trailer','Utility','Curtain Sider',2024,'CHS-2026-0028',NULL,NULL,NULL,30000.00,7800.00,'2024-02-28','Curtain Logistics','Davao Branch','Lorenzo Ruiz','COMPANY_OWNED','AVAILABLE',NULL,'Curtain side trailer for easy loading','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(34,'TRK-2026-0029','TRAILER','CHS-009','MV-2026-0029','Trailer','Car Carrier','Utility','Auto Transport',2022,'CHS-2026-0029',NULL,NULL,NULL,25000.00,65400.00,'2022-10-10','Auto Logistics Inc.','Cebu Branch','Narcisa De Leon','COMPANY_OWNED','AVAILABLE',NULL,'For vehicle transport','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44'),(35,'TRK-2026-0030','TRAILER','CHS-010','MV-2026-0031','Trailer','Container Chassis','Utility','45ft Container',2023,'CHS-2026-0030','','',0.00,38000.00,34500.00,'2023-09-05','Container Services Inc.','Manila Branch','Guillermo Tolentino','COMPANY_OWNED','AVAILABLE',NULL,'45ft container chassis for heavy loads','2026-08-29 21:21:59','ADMIN','2026-10-05 10:24:44');
/*!40000 ALTER TABLE `tbl_trucks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_vendor_services`
--

DROP TABLE IF EXISTS `tbl_vendor_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_vendor_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `service_type` enum('TRUCK_RENTAL','TRACTOR_RENTAL','CHASSIS_RENTAL','DRIVER_SERVICE','HELPER_SERVICE','TRUCK_DRIVER','TRUCK_DRIVER_HELPER','TRACTOR_CHASSIS','TRACTOR_CHASSIS_DRIVER','TRACTOR_CHASSIS_DRIVER_HELPER','OTHER_TRANSPORTATION_SERVICE') NOT NULL,
  `rate_type` enum('PER_TRIP','PER_DAY','PER_KILOMETER','PER_HOUR','MONTHLY','FIXED_RATE') NOT NULL,
  `rate_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `effective_date` date DEFAULT NULL,
  `service_status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`service_id`),
  KEY `idx_vendor_id` (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_vendor_services`
--

LOCK TABLES `tbl_vendor_services` WRITE;
/*!40000 ALTER TABLE `tbl_vendor_services` DISABLE KEYS */;
INSERT INTO `tbl_vendor_services` VALUES (1,1,'TRUCK_DRIVER_HELPER','PER_TRIP',15000.00,'2026-01-01','ACTIVE','Full package with driver and helper','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(2,1,'TRUCK_RENTAL','PER_DAY',8000.00,'2026-01-01','ACTIVE','Rigid truck rental without driver','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(3,1,'TRACTOR_CHASSIS_DRIVER','PER_TRIP',25000.00,'2026-01-01','ACTIVE','Tractor with chassis and driver','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(4,2,'TRUCK_DRIVER','PER_DAY',12000.00,'2026-01-15','ACTIVE','Refrigerated truck with driver','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(5,2,'TRACTOR_CHASSIS','PER_TRIP',20000.00,'2026-01-15','ACTIVE','Tractor and chassis combo','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(6,2,'DRIVER_SERVICE','PER_DAY',2500.00,'2026-01-15','ACTIVE','Driver service only','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(7,3,'TRACTOR_RENTAL','PER_DAY',10000.00,'2026-02-01','ACTIVE','Tractor head rental','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(8,3,'CHASSIS_RENTAL','PER_DAY',5000.00,'2026-02-01','ACTIVE','Chassis/trailer rental','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(9,3,'TRACTOR_CHASSIS_DRIVER_HELPER','PER_TRIP',35000.00,'2026-02-01','ACTIVE','Complete package with crew','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(10,4,'TRUCK_RENTAL','PER_TRIP',12000.00,'2026-03-01','ACTIVE','10-wheeler truck rental','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(11,4,'TRUCK_DRIVER','PER_KILOMETER',150.00,'2026-03-01','ACTIVE','Per kilometer rate','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(12,4,'HELPER_SERVICE','PER_DAY',1500.00,'2026-03-01','ACTIVE','Helper service only','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(13,5,'TRACTOR_RENTAL','MONTHLY',150000.00,'2026-04-01','ACTIVE','Monthly tractor leasing','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(14,5,'CHASSIS_RENTAL','MONTHLY',80000.00,'2026-04-01','ACTIVE','Monthly chassis leasing','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(15,5,'TRACTOR_CHASSIS_DRIVER','PER_TRIP',28000.00,'2026-04-01','ACTIVE','Complete with driver','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(16,6,'TRUCK_DRIVER_HELPER','PER_DAY',18000.00,'2026-05-01','ACTIVE','Inter-island transport package','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(17,6,'TRACTOR_CHASSIS','PER_TRIP',22000.00,'2026-05-01','ACTIVE','Tractor and chassis for container hauling','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(18,6,'OTHER_TRANSPORTATION_SERVICE','FIXED_RATE',30000.00,'2026-05-01','ACTIVE','Specialized cargo handling','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(19,7,'TRUCK_RENTAL','PER_DAY',9000.00,'2026-06-01','ACTIVE','Nationwide truck rental','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(20,7,'TRACTOR_CHASSIS_DRIVER_HELPER','PER_TRIP',38000.00,'2026-06-01','ACTIVE','Complete team for long haul','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(21,7,'DRIVER_SERVICE','PER_TRIP',3000.00,'2026-06-01','ACTIVE','Per trip driver service','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(22,8,'TRUCK_DRIVER','FIXED_RATE',10000.00,'2026-07-01','ACTIVE','Fixed rate for local deliveries','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(23,8,'TRACTOR_CHASSIS','PER_KILOMETER',180.00,'2026-07-01','ACTIVE','Per kilometer rate for tractor+chassis','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(24,8,'HELPER_SERVICE','PER_HOUR',150.00,'2026-07-01','ACTIVE','Helper on hourly basis','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(25,9,'TRUCK_DRIVER_HELPER','PER_KILOMETER',200.00,'2026-08-01','ACTIVE','Full team per kilometer','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(26,9,'CHASSIS_RENTAL','PER_TRIP',6000.00,'2026-08-01','ACTIVE','Chassis rental for cargo','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(27,9,'TRACTOR_CHASSIS_DRIVER','PER_DAY',20000.00,'2026-08-01','ACTIVE','Tractor chassis with driver daily','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(28,10,'TRUCK_RENTAL','PER_TRIP',14000.00,'2026-09-01','ACTIVE','Mindanao truck rental','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(29,10,'TRACTOR_CHASSIS_DRIVER_HELPER','PER_DAY',22000.00,'2026-09-01','ACTIVE','Complete package daily rate','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44'),(30,10,'DRIVER_SERVICE','PER_KILOMETER',100.00,'2026-09-01','ACTIVE','Driver on per kilometer rate','2026-08-29 21:58:53','ADMIN','2026-10-05 10:24:44');
/*!40000 ALTER TABLE `tbl_vendor_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_vendors`
--

DROP TABLE IF EXISTS `tbl_vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_vendors` (
  `vendor_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`vendor_id`),
  UNIQUE KEY `idx_vendor_code` (`vendor_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_vendors`
--

LOCK TABLES `tbl_vendors` WRITE;
/*!40000 ALTER TABLE `tbl_vendors` DISABLE KEYS */;
INSERT INTO `tbl_vendors` VALUES (1,'VND-2026-0001','ABC Trucking Rentals','FLEET PROVIDER','Maria Santos','0917-123-4567','maria@abctrucking.com','123 Logistics Ave., Manila','30 Days','ACTIVE','Full-service trucking and logistics provider','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(2,'VND-2026-0002','XYZ Transport Services','LOGISTICS','Juan Dela Cruz','0918-234-5678','juan@xyztransport.com','456 Cargo St., Cebu','15 Days','ACTIVE','Specializes in refrigerated transport','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(3,'VND-2026-0003','Manila Heavy Equipment Rental','EQUIPMENT RENTAL','Pedro Reyes','0919-345-6789','pedro@manilahire.com','789 Makati Ave., Manila','COD','ACTIVE','Heavy equipment and construction rental','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(4,'VND-2026-0004','Davao Logistics Solutions','3PL PROVIDER','Anna Lopez','0920-456-7890','anna@davaologistics.com','101 Davao St., Davao','45 Days','ACTIVE','Third-party logistics provider','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(5,'VND-2026-0005','Isuzu Fleet Services','FLEET PROVIDER','Ramon Cruz','0921-567-8901','ramon@isuzufleet.com','202 Pasig Blvd., Pasig','30 Days','ACTIVE','Fleet leasing and maintenance services','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(6,'VND-2026-0006','Cebu Transport Co.','LOGISTICS','Elena Rivera','0922-678-9012','elena@cebutransport.com','303 Cebu City, Cebu','60 Days','ACTIVE','Inter-island transport and cargo forwarding','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(7,'VND-2026-0007','Nationwide Trucking','FLEET PROVIDER','Mark Tan','0923-789-0123','mark@nationwidetrucking.com','404 EDSA, Quezon City','15 Days','ACTIVE','Nationwide trucking services','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(8,'VND-2026-0008','Pampanga Logistics Hub','3PL PROVIDER','Grace Santos','0924-890-1234','grace@pampangalogistics.com','505 Clark Freeport, Pampanga','30 Days','ACTIVE','Full logistics and warehousing services','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(9,'VND-2026-0009','Visayas Cargo Express','LOGISTICS','Francisco Garcia','0925-901-2345','francisco@visayascargo.com','606 Iloilo City, Iloilo','45 Days','ACTIVE','Visayas region cargo transport','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53'),(10,'VND-2026-0010','Mindanao Freight Services','FLEET PROVIDER','Leticia Mendez','0926-012-3456','leticia@mindanaofreight.com','707 General Santos City, South Cotabato','30 Days','ACTIVE','Mindanao freight and logistics','2026-08-29 21:58:53','ADMIN','2026-08-29 21:58:53');
/*!40000 ALTER TABLE `tbl_vendors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'largafms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-06  8:01:11
