-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: buku_tamu
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
-- Table structure for table `blacklists`
--

DROP TABLE IF EXISTS `blacklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blacklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_or_institution` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blacklists`
--

LOCK TABLES `blacklists` WRITE;
/*!40000 ALTER TABLE `blacklists` DISABLE KEYS */;
INSERT INTO `blacklists` VALUES (1,'Politeknik Bingung','Bermasalah','2026-08-30 06:20:42','2026-08-30 06:20:42');
/*!40000 ALTER TABLE `blacklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'Budi Santoso','Divisi Umum','Kepala Divisi',NULL,1,'2026-08-27 21:37:05','2026-08-27 21:37:05'),(2,'Siti Rahma','SDM','Staf HRD',NULL,1,'2026-08-27 21:37:05','2026-08-27 21:37:05'),(3,'Andi Wijaya','IT','System Analyst',NULL,1,'2026-08-27 21:37:05','2026-08-27 21:37:05'),(4,'Dewi Lestari','Keuangan','Finance Officer',NULL,1,'2026-08-27 21:37:05','2026-08-27 21:37:05'),(5,'Rian Fahmi','Operasional','Supervisor Lapangan','43321',1,'2026-08-27 21:37:05','2026-08-30 07:34:05'),(7,'Oza Okta','IT','System Analyst','4332',1,'2026-08-30 07:21:36','2026-08-30 07:21:36'),(9,'Visual Studio Code','APA AJA','CEO','1122',1,'2026-09-06 17:49:04','2026-09-06 17:49:04');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gate_logs`
--

DROP TABLE IF EXISTS `gate_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gate_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `direction` enum('in','out') NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gate_logs_visit_id_foreign` (`visit_id`),
  CONSTRAINT `gate_logs_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gate_logs`
--

LOCK TABLES `gate_logs` WRITE;
/*!40000 ALTER TABLE `gate_logs` DISABLE KEYS */;
INSERT INTO `gate_logs` VALUES (1,29,'in','ea681fA2XlovygyJSh4WcoU6s3w2fe4h','2026-09-06 17:57:40','2026-09-06 17:57:40'),(2,30,'in','v1cgCM2nR7XCItjeCaH1SSIQJBr49o2i','2026-09-06 17:58:22','2026-09-06 17:58:22'),(3,30,'out','ixZ4mnSLRyAqbGoISZl4rg5Qx1Z41xS0','2026-10-20 02:51:19','2026-10-20 02:51:19');
/*!40000 ALTER TABLE `gate_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_28_042143_create_employees_table',1),(5,'2026_08_28_042144_create_blacklists_table',1),(6,'2026_08_28_042144_create_visits_table',1),(7,'2026_08_28_070016_add_visit_details_to_visits_table',2),(8,'2026_08_29_003631_create_services_table',3),(9,'2026_08_29_030000_add_meeting_invitation_to_visits_visit_type',4),(10,'2026_09_03_000001_add_gate_fields_to_visits_table',5),(11,'2026_09_03_000002_create_gate_logs_table',5),(12,'2026_10_08_000001_add_indexes_to_visits_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '✨',
  `description` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Kunjungan Tamu','🤝','Pertemuan dengan pegawai terkait kepentingan resmi.','Tamu wajib melakukan registrasi melalui kiosk sebelum masuk.',1,'2026-08-28 18:01:03','2026-08-28 18:01:03'),(2,'Pengantaran Surat/Dokumen','📄','Pengiriman surat, dokumen, atau paket ke kantor.','Dokumen dapat dititipkan ke resepsionis bila yang dituju tidak berada di kantor.',1,'2026-08-28 18:01:03','2026-08-28 18:01:03'),(3,'Koordinasi Meeting','📊','Reservasi dan koordinasi ruang rapat.','Pengajuan ruang rapat minimal 1 hari sebelumnya.',1,'2026-08-28 18:01:03','2026-08-28 18:01:03'),(4,'Layanan Keamanan','🛡️','Bantuan keamanan dan laporan insiden.','Hubungi pos keamanan untuk keadaan darurat.',1,'2026-08-28 18:01:03','2026-08-28 18:01:03'),(5,'Undangan Rapat / Kegiatan','RPT','Permohonan kedatangan untuk undangan rapat atau kegiatan resmi.','Tamu memilih pegawai atau unit yang menjadi tujuan undangan.',1,'2026-08-28 19:49:29','2026-08-28 19:49:29');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5RcgDnWD7fB7rU8NO8dB28D1qv3DMbb5RWoCJFGU',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNlU0RVUyeFdxQTc3OWFrRzVVQ3FFZjZKVWNHeGZHM3RaN2lrc1ZEYyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO3M6NToicm91dGUiO3M6NToia2lvc2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1790215450);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','receptionist') NOT NULL DEFAULT 'admin',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Pelindo','admin@pelindo.id','2026-08-27 21:37:04','$2y$12$RC9WfSryRo9xsdD2Ww7fLurpS7TExlb7Zn6K5U0U0J.vL.C5QxUfG','admin','xZ3wa7oeYzwWGIEpMlWYXFVTdcG0nO3HkBtc5sQTnWIEF6QnzPpAypQZ5Nl4','2026-08-27 21:37:04','2026-08-30 11:58:58'),(2,'Resepsionis Lobi','receptionist@pelindo.id','2026-08-27 21:37:04','$2y$12$CiQJwDJZiSy5AsmTtYPAFuyphXZkUFPIv44LFQ6M4ezsHvmw6gXvq','receptionist','jky3F052BUzZ2awpLlKKIDkSuZKIgHEUs0AM1HNmTnykK1OX6fi6jTDAKeRF','2026-08-27 21:37:05','2026-08-27 21:37:05');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visitor_name` varchar(255) NOT NULL,
  `visitor_phone` varchar(255) NOT NULL,
  `visitor_institution` varchar(255) NOT NULL,
  `identity_number` varchar(255) DEFAULT NULL,
  `plate_number` varchar(255) DEFAULT NULL,
  `person_to_meet` varchar(255) DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `purpose` text NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `qr_code_token` varchar(255) NOT NULL,
  `checkout_token` varchar(255) DEFAULT NULL,
  `status` enum('pending','waiting','active','completed','rejected') NOT NULL DEFAULT 'pending',
  `source` varchar(10) NOT NULL DEFAULT 'kiosk',
  `visit_type` enum('meet','deliver','meeting_invitation') NOT NULL DEFAULT 'meet',
  `delivery_pref` enum('hand_in','leave') DEFAULT NULL,
  `received_by_name` varchar(255) DEFAULT NULL,
  `status_note` text DEFAULT NULL,
  `check_in_time` datetime DEFAULT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `checkout_used_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visits_qr_code_token_unique` (`qr_code_token`),
  UNIQUE KEY `visits_checkout_token_unique` (`checkout_token`),
  KEY `visits_employee_id_foreign` (`employee_id`),
  KEY `visits_source_index` (`source`),
  KEY `visits_status_index` (`status`),
  KEY `visits_check_in_time_index` (`check_in_time`),
  KEY `visits_created_at_index` (`created_at`),
  KEY `visits_source_check_in_index` (`source`,`check_in_time`),
  KEY `visits_source_status_index` (`source`,`status`),
  CONSTRAINT `visits_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visits`
--

LOCK TABLES `visits` WRITE;
/*!40000 ALTER TABLE `visits` DISABLE KEYS */;
INSERT INTO `visits` VALUES (1,'Oza Okta Gistradaaaa','089505647628','Politeknik Caltex Riau',NULL,NULL,NULL,3,'Kunjungan Ke Kantor','visits/925df0bc-d211-4af5-a8fc-9bb3e9d2494e.jpg','MkUD8luMkKaX0bQ8RwNlRbFArNMyg7yo',NULL,'rejected','kiosk','meet',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 21:44:34','2026-08-27 21:55:55'),(2,'induyyy','0825112121','polbeng',NULL,NULL,NULL,4,'kunjungan','visits/a14db231-4972-4b5e-970d-e22b5896c88c.jpg','w0fDiEEV1Q0mqGtQsR5f1EeWwoJbfcme',NULL,'active','kiosk','meet',NULL,NULL,NULL,'2026-08-28 14:31:59',NULL,NULL,'2026-08-27 23:40:58','2026-08-28 07:31:59'),(3,'Kurir Surat Test','081234567890','PT Test',NULL,NULL,NULL,1,'Mengantar surat dinas',NULL,'eSZz92N7CgnOzjd7y0AXJvjCdqXRpde5',NULL,'waiting','kiosk','deliver','leave',NULL,NULL,NULL,NULL,NULL,'2026-08-28 07:16:45','2026-08-28 07:31:03'),(4,'indah','08251127852','SMK 1 Dumai',NULL,NULL,NULL,2,'Magang','visits/09a2a73a-fe1d-49c4-a293-118b04de9004.jpg','MLX8rjFC5BVr7kAHsZR2HWpzajpTl8Ts',NULL,'completed','kiosk','meet','hand_in',NULL,NULL,'2026-08-28 14:30:57','2026-08-28 14:32:09',NULL,'2026-08-28 07:25:45','2026-08-28 07:32:09'),(5,'ecuyy','08545452','PT.Pelindo Regional 1 Dumai',NULL,NULL,NULL,5,'surat','visits/8d80a1d4-710b-4199-9496-11414524f5a0.jpg','3WlIee0foki5L3souSkQ9WYoJw8OwF89',NULL,'rejected','kiosk','deliver','hand_in',NULL,NULL,NULL,NULL,NULL,'2026-08-28 07:33:50','2026-08-28 17:19:18'),(6,'Foto Kita Blur','08454545125','polbeng',NULL,NULL,NULL,1,'cape tai','visits/30550ed6-07cd-4e20-b8de-5f1e8c010325.jpg','cgk8SxVLnKGaHNEiHh3SfpsO9IUb4nRH',NULL,'completed','kiosk','deliver','hand_in',NULL,NULL,'2026-08-29 00:15:07','2026-08-29 00:15:14',NULL,'2026-08-28 07:34:47','2026-08-28 17:15:14'),(7,'oza','08522112','apa',NULL,NULL,NULL,4,'kunjungan','visits/949f3d4a-aa4a-4a08-bb56-82524e8509dd.jpg','7GzOLjuleQTkmbQc3bqXKhLROj8HCwq0',NULL,'completed','kiosk','meet','leave',NULL,NULL,'2026-08-28 15:36:02','2026-08-28 15:36:07',NULL,'2026-08-28 08:13:35','2026-08-28 08:36:07'),(8,'mumut','08638382982','PCR',NULL,NULL,NULL,NULL,'apa','visits/ecfe8c43-f237-475f-94eb-7979798f482a.jpg','4KvlBHbeOHlB8tqWU0GdV6QyEDm9k4FT',NULL,'pending','kiosk','meeting_invitation',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-28 20:32:08','2026-08-28 20:32:08'),(9,'Tia','08228402739','pcr',NULL,NULL,NULL,NULL,'🤬🤬','visits/a894b3e6-254b-4146-9e52-80fce4bce5d2.jpg','F5ERRTWNxQGJEt889v4OVbHwWcqEqJv2',NULL,'rejected','kiosk','meet',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-28 20:32:48','2026-08-28 20:33:09'),(10,'0jeeet','08111111','SMA IT Mutiara',NULL,NULL,NULL,NULL,'Rapat Penting','visits/ade82db4-ab0c-4089-b098-e15a3f3f6ab3.jpg','Puh0ZjA4lvbCCzXbP0ujyE1Efvcatkan',NULL,'completed','kiosk','meeting_invitation',NULL,NULL,NULL,'2026-08-30 13:53:58','2026-08-30 13:54:03',NULL,'2026-08-30 05:43:34','2026-08-30 06:54:03'),(11,'mumuttt','131','SMA IT Mutiara',NULL,NULL,NULL,7,'apa ya','visits/d61a2ae5-d92b-4a79-9d6e-62f954352c22.jpg','SfLOjyBWyigfldeylo3gLIsNPnmwTMY8',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-08-30 14:31:05','2026-08-30 14:39:37',NULL,'2026-08-30 07:30:03','2026-08-30 07:39:37'),(12,'s','1','q',NULL,NULL,NULL,7,'q','visits/eafad702-c1b0-4243-bf21-c84444fecd47.jpg','Z3kJ5tRuDQxS200YJN4gJBkwKDwzHQqL',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-08-30 14:59:24','2026-08-30 14:59:29',NULL,'2026-08-30 07:41:49','2026-08-30 07:59:29'),(13,'q','21','apa',NULL,NULL,NULL,7,'qq','visits/fe78f0fe-00aa-4cca-b17f-6fa2d0c58b36.jpg','aUGu6i0F8o4kvDWuYg40er4Zz4NKhuuF',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-08-30 14:50:46','2026-08-30 14:59:20',NULL,'2026-08-30 07:48:48','2026-08-30 07:59:20'),(14,'a','1','1',NULL,NULL,NULL,3,'11','visits/0911d247-2b5b-482a-bcae-cb860ceb9220.jpg','Cpih66DrghyPtKVAzXQtDkZLBwOGD0gR',NULL,'rejected','kiosk','deliver','hand_in',NULL,NULL,NULL,NULL,NULL,'2026-08-30 08:00:17','2026-08-30 08:02:33'),(15,'apaa','12331','SMK 1 Dumai',NULL,NULL,NULL,2,'rpt','visits/e2bc392b-8b81-4917-9230-23bfe06aba04.jpg','RBaYGhHr2MsOlnqhfRTdv5mcYQL8upH6',NULL,'completed','kiosk','meeting_invitation',NULL,NULL,NULL,'2026-08-30 15:06:17','2026-08-30 15:39:06',NULL,'2026-08-30 08:00:59','2026-08-30 08:39:06'),(16,'aaa','aaa','aaa',NULL,NULL,NULL,1,'aaa','visits/f10a0dfe-a48b-4161-940f-d6f3e27db730.jpg','wGxBuY8REgfBXuwg7e09Hg8nj7IoZcMF',NULL,'active','kiosk','meet',NULL,NULL,NULL,'2026-08-30 17:51:20',NULL,NULL,'2026-08-30 08:40:46','2026-08-30 10:51:20'),(17,'s','qw','polbeng',NULL,NULL,NULL,5,'q','visits/fc799582-860f-4b6b-a1c1-bfe33c47ce3f.jpg','0Yfbh1zIeONIBRIFVWjehvW1J0VIkGmL',NULL,'pending','kiosk','meet',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-30 11:31:37','2026-08-30 11:31:37'),(18,'qq','qq','qq',NULL,NULL,NULL,3,'qq','visits/38b70152-64c9-42f0-8ceb-b08030ac9a91.jpg','ER4nJ1CgmmpqRB6by2V0juLGkxGcLT8t',NULL,'active','kiosk','meet',NULL,NULL,NULL,'2026-08-30 19:11:21',NULL,NULL,'2026-08-30 11:44:46','2026-08-30 12:11:21'),(19,'andre','123','PT.Pelindo Regional 1 Dumai',NULL,NULL,NULL,5,'andre masam','visits/bdb7b97e-e2e3-4176-be15-5d3b6de4eef8.jpg','OD409WrmmRu1Wvgn6iIYlLzqhfVqM4fo',NULL,'pending','kiosk','meet',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-30 11:49:18','2026-08-30 11:49:18'),(20,'rajip','121','polbeng',NULL,NULL,NULL,2,'numpang berak','visits/10d1dde0-52a0-4cba-8cd0-75cb7891293f.jpg','R9diZaN91bHNz4gGMEFG5bVVvNYuw9KX',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-08-30 18:53:30','2026-08-30 18:53:41',NULL,'2026-08-30 11:52:07','2026-08-30 11:53:41'),(21,'picuyy','1111','Politeknik Caltex Riau',NULL,NULL,NULL,1,'kangen','visits/4b1eaf41-181a-41a7-9f63-84f1345599ca.jpg','d1Rqv3JpN6BX2u3GYmUr1RVIQaRy6YUr',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-08-31 12:47:13','2026-08-31 12:53:46',NULL,'2026-08-31 04:56:11','2026-08-31 05:53:46'),(22,'aa','aa','aa',NULL,NULL,NULL,7,'aa','visits/fa9f8781-c9d7-4bd9-8f79-6bfd04a8380d.jpg','i9BBTxRbaUN26mAzk8ad9gzxZMMqS43X',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-08-31 12:44:23','2026-08-31 12:46:58',NULL,'2026-08-31 05:26:59','2026-08-31 05:46:58'),(23,'w','w','w',NULL,NULL,NULL,7,'w','visits/d9762924-b84b-4b1d-a099-21718a88c62a.jpg','fo5dQwbrxIkRxQaIxFOF4XvReyznp2va',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-08-31 12:53:22','2026-08-31 12:53:35',NULL,'2026-08-31 05:53:03','2026-08-31 05:53:35'),(24,'iputt','08888','SMK 1 Dumai',NULL,NULL,NULL,7,'Numpang berak','visits/cc77af5c-e7a9-4659-a0a6-83fcd927d6c4.jpg','9GcPgqXl5GqktwTAjsQHtztbpnIJEyl3',NULL,'waiting','kiosk','meet',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-01 05:37:54','2026-09-01 05:39:01'),(25,'induttt','123','SMK 1 Dumai',NULL,NULL,NULL,7,'numpang makeup','visits/cd16ff86-1b76-4370-b345-2f2299a1725f.jpg','Lfu3TBr1nTwSNu0rcqbgBqRWvqEhRPzV',NULL,'pending','kiosk','meet',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-01 06:06:06','2026-09-01 06:06:06'),(26,'oza','1222','SMK 1 Dumai',NULL,NULL,NULL,2,'Rapat Penting','visits/a6454ac6-f005-4346-8b7c-b7b6fa802f81.jpg','0nvv5k0s6UxXgjfSRoQ1wEDnFUOQFECF',NULL,'completed','kiosk','meeting_invitation',NULL,NULL,NULL,'2026-09-03 10:29:53','2026-09-03 10:30:02',NULL,'2026-09-03 03:27:21','2026-09-03 03:30:02'),(28,'mima','111122','SMA IT Mutiara',NULL,NULL,NULL,4,'apa','visits/f8b186f8-bf63-421a-86a8-628ef4bdc2c9.jpg','lP1POXodHBtUlQeacNTnfY0j8PhDCGMC',NULL,'pending','kiosk','meet',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-06 17:40:27','2026-09-06 17:40:27'),(29,'induyyy','123','SMK 1 Dumai',NULL,'bm 123 ab','Oza',NULL,'apa','visits/87cfb4f7-6182-47c5-8a68-7ddfcf4fccc1.jpg','ea681fA2XlovygyJSh4WcoU6s3w2fe4h','3yS9ZB4fjbogSiogqyUWk2tiVElLxr8q','active','gate','meet',NULL,NULL,NULL,'2026-09-07 00:57:40',NULL,NULL,'2026-09-06 17:57:40','2026-09-06 17:57:40'),(30,'induyyy','089505647628','SMK 1 Dumai',NULL,'BM 805 DIA','Oza',NULL,'Rapattttt','visits/f7fafbd7-396a-4cf1-8e97-34ade4339b1f.jpg','v1cgCM2nR7XCItjeCaH1SSIQJBr49o2i','ixZ4mnSLRyAqbGoISZl4rg5Qx1Z41xS0','completed','gate','meet',NULL,NULL,NULL,'2026-09-07 00:58:22','2026-10-20 09:51:18','2026-10-20 09:51:18','2026-09-06 17:58:22','2026-10-20 02:51:18'),(31,'gibran','08888888','PCR',NULL,NULL,NULL,1,'rapat night ride','visits/43e446cc-9aa8-4eea-8544-98c559ce96af.jpg','IpU9W8jMQjcjo84zlnB1YaOeVxlL76oi',NULL,'pending','kiosk','meeting_invitation',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-23 14:22:15','2026-09-23 14:22:15'),(32,'Fachri Akmal Rifaat','08555555555','Taper',NULL,NULL,NULL,NULL,'numpang berak','visits/5d0d37c8-89f2-4a9b-b8e0-b84a5e3e0c69.jpg','kOx9RU9TBec9SUyZfrnaYOOLFMpDIIdW',NULL,'completed','kiosk','meet',NULL,NULL,NULL,'2026-10-19 10:39:28','2026-10-19 10:42:35',NULL,'2026-10-19 02:46:58','2026-10-19 03:42:35');
/*!40000 ALTER TABLE `visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'buku_tamu'
--

--
-- Dumping routines for database 'buku_tamu'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-20 13:12:24
