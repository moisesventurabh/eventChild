-- MySQL dump 10.13  Distrib 8.4.7, for Linux (aarch64)
--
-- Host: localhost    Database: eventshield
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
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
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'outdoor',
  `start_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_uuid_unique` (`uuid`),
  KEY `events_user_id_foreign` (`user_id`),
  KEY `idx_events_start_at` (`start_at`),
  KEY `idx_events_location` (`city`,`country`),
  CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (8,'019f5d39-3e92-723b-ba00-33fd4225c297',1,'Festival de Música Urbana','Evento ao ar livre com palco principal, área de food trucks e estrutura de som de grande porte.','Ribeirão das Neves','MG','BRA',-19.80746300,-44.10227600,'outdoor','2026-08-22 16:00:00','2026-07-13 20:44:20','2026-07-13 20:44:20'),(11,'019f5d63-c8b7-704a-936a-140f601dbc4b',1,'Festival de Rap','Evento ao ar livre com palco principal, área de food trucks e estrutura de som de grande porte.','Santo André','SP','BRA',-23.64690100,-46.53508000,'outdoor','2026-11-30 16:00:00','2026-07-13 21:30:48','2026-07-13 21:30:48'),(12,'019f5d66-6350-7082-a1d1-e86e35476561',1,'Balada','Evento ao ar livre com palco principal, área de food trucks e estrutura de som de grande porte.','Newark','NJ','US',40.74316400,-74.17176500,'outdoor','2026-12-30 16:00:00','2026-07-13 21:33:39','2026-07-13 21:33:39'),(13,'019f5d8b-16a4-70cd-87fe-c29a27ed6338',1,'Teste Festa','Evento ao ar livre com palco principal, área de food trucks e estrutura de som de grande porte.','Nova Iorque','NY','US',40.63103500,-73.95200800,'outdoor','2026-12-31 16:00:00','2026-07-13 22:13:44','2026-07-13 22:13:44'),(14,'019f5ec5-85ca-70c5-a873-3d877ba47568',1,'testando','valeus','Ribeirão das Neves','MG','BRA',-19.80746300,-44.10227600,'indoor','2026-07-30 01:56:00','2026-07-14 03:57:10','2026-07-14 03:57:10'),(15,'019f63e9-d825-730c-9f5b-21c6cf538b9a',2,'Carnaval BH 2027','Festa popular brasileira na cidade de Belo Horizonte.','Belo Horizonte','MG','BRA',-19.92370000,-43.93634400,'outdoor','2027-03-05 15:00:00','2026-07-15 03:54:57','2026-07-15 03:54:57');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_10_150545_create_personal_access_tokens_table',1),(5,'2026_07_10_163623_create_events_table',2),(6,'2026_07_10_163623_create_weather_reports_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('jhon@jhon.com','$2y$12$lqu.topvaNjuom7GkDVuaexODOHbAm2SnTEgyr5fgML9ejLESyc1u','2026-07-15 04:08:58');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'auth_token','8ebcd7362dc2f2f595d20a823ba271b91b5d03eb675e250aa52c25f275be9779','[\"*\"]',NULL,NULL,'2026-07-13 03:11:57','2026-07-13 03:11:57'),(2,'App\\Models\\User',1,'auth_token','d64d1083a134b7e190afbc10bae1c6510afe3053da21e833d57a0bac54cfbe9e','[\"*\"]',NULL,NULL,'2026-07-13 19:45:15','2026-07-13 19:45:15'),(3,'App\\Models\\User',1,'auth_token','26df0e796ceb6483604a06830d9e873bb07378d04d68e6b48cbb3d259914ef07','[\"*\"]','2026-07-13 19:55:14',NULL,'2026-07-13 19:55:14','2026-07-13 19:55:14'),(4,'App\\Models\\User',1,'auth_token','81b9565914aedf30486f7f972a6d9117521ac700765cb392f8c2787b2a1b92d4','[\"*\"]','2026-07-13 20:26:43',NULL,'2026-07-13 20:05:40','2026-07-13 20:26:43'),(5,'App\\Models\\User',1,'auth_token','9c4fcfb93094577edd350c37ec04af643f4538bad0040df2215ef4ceb22ef09f','[\"*\"]','2026-07-13 20:37:55',NULL,'2026-07-13 20:27:06','2026-07-13 20:37:55'),(6,'App\\Models\\User',1,'auth_token','468b4e19c41c3e4858e8cb961027a581d7284a92232abdbd24da421a7729996c','[\"*\"]','2026-07-13 20:44:21',NULL,'2026-07-13 20:38:07','2026-07-13 20:44:21'),(7,'App\\Models\\User',1,'auth_token','97be9981179517ebdd7b8ab961ef10b472fdf38e7fcf67b0e93c6ec8cce7d594','[\"*\"]','2026-07-13 21:15:29',NULL,'2026-07-13 20:45:45','2026-07-13 21:15:29'),(8,'App\\Models\\User',1,'auth_token','f4e2545479ac285a2300c9918d67e5db26ded483a4317944eee76767456e8b20','[\"*\"]','2026-07-13 21:15:38',NULL,'2026-07-13 21:15:38','2026-07-13 21:15:38'),(9,'App\\Models\\User',1,'auth_token','722feecfd7a9c362c4b1d9ec128c5b425c10af3ffe4b9755db4551c3aa8f84c9','[\"*\"]','2026-07-13 22:11:04',NULL,'2026-07-13 21:21:21','2026-07-13 22:11:04'),(10,'App\\Models\\User',1,'auth_token','0a62dc428dccde67af6e6c2e9da0cba71cc2805c9a7a0c6a7246360b995fa8e7','[\"*\"]','2026-07-13 22:13:48',NULL,'2026-07-13 22:11:21','2026-07-13 22:13:48'),(11,'App\\Models\\User',1,'auth_token','a02bea0e1e7e89f7386a4506ad02b87fa66727f102f6dfa02a5950974bf405e0','[\"*\"]','2026-07-14 00:50:54',NULL,'2026-07-14 00:50:54','2026-07-14 00:50:54'),(12,'App\\Models\\User',1,'auth_token','b448be45f0d23b59a3634fa5797120a09867a856b3b535bfdfeef32f0eb869ce','[\"*\"]','2026-07-14 00:57:55',NULL,'2026-07-14 00:57:55','2026-07-14 00:57:55'),(17,'App\\Models\\User',1,'auth_token','35f2106e5d43374d00eae9def33ed827ccbcf72688f11a6eceb8bfc6f728b91a','[\"*\"]',NULL,NULL,'2026-07-14 03:33:42','2026-07-14 03:33:42'),(19,'App\\Models\\User',1,'auth_token','93c19d27604947312d52e16477f16ddb4f070ee0d33a9cd9b344ff2c866c9297','[\"*\"]','2026-07-14 03:57:11',NULL,'2026-07-14 03:41:41','2026-07-14 03:57:11'),(20,'App\\Models\\User',1,'auth_token','d504d06314e0453b609526b7d1aea4314e70bab3caf2b2eed6e18093e444a7d4','[\"*\"]',NULL,NULL,'2026-07-14 04:37:16','2026-07-14 04:37:16'),(23,'App\\Models\\User',1,'auth_token','bc211807565f31f5f7c9b3c876882bd36a85baadb05c2a520e01613bd0404568','[\"*\"]',NULL,NULL,'2026-07-14 05:08:14','2026-07-14 05:08:14'),(26,'App\\Models\\User',1,'auth_token','e5ef3c3a9135379e07ce509146c3556863e82522b34899c4c45f83cb8a4481fa','[\"*\"]','2026-07-14 14:54:45',NULL,'2026-07-14 14:34:47','2026-07-14 14:54:45');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('HThLtKnV3QOUncDFSNsmawvmAfNwaG7nKSYyHeWr',1,'192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJTdFRXWXVpalNrcUlSbWdLZ0lqWWQ3d3NlWm1TeXRacGtOS1dtaGEzIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2FwaVwvdjFcL2V2ZW50cyIsInJvdXRlIjpudWxsfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiNWYyMGVjNjBiNzBiOGI3YjQ4OTE3MDkwNWI3YzU4NTkyNTI5M2E0Y2VlYjU3ODQwMTQwN2RlN2YxNjVlZmNhYSJ9',1784077239),('KJCKdY2Pqn3eFg8olFDoMwtr7vjWUL17bm9Er7sO',NULL,'192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJmdVhVQjhDVmN3R0R3QUxsbmY4dG8wR3IyQXFFdUNoa0Y3VEFvbTRrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL3YxXC91c2VyIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1784087023),('WLvqiKC5pY4No5yTmMDYHIulgDx9vosH47MwK9GD',1,'192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJoTno3NzJ2cEtNTDJnWHJwWWd2dEhDVDJpMWNFYXNvS3ZPODgyamtZIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2FwaVwvdjFcL2V2ZW50cyIsInJvdXRlIjpudWxsfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiZTFhOGQzNjVkNTI1N2M1YzYxMmRlNmE3NWNkOGEwZTM2NjQ3OGE4ZjNlYTA3ZTQyMTg0MjMxZDFlYjU4ZGZlYSJ9',1784088894);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `users` VALUES (1,'Miss Eda','teste@exemplo.com','2026-07-12 03:12:48','$2y$12$wPygAlIzJvrstmg.FeUU7ewgQ.rKl2yV2ALUrzgS7dDCdZLeUGUuK','Tco4yIQ05Qb3Hj3L1lZ12Es8Q8b4gFc9AAVrbKne7ebL3D3vNT2BoUyOD5hq','2026-07-12 03:12:48','2026-07-15 03:29:03'),(2,'jhon','jhon@jhon.com',NULL,'$2y$12$sXmnFhqDc4AhFtkJO6sIAeVrs7FEZQZ/BMjod4Th4mIOzCpwsOiUS',NULL,'2026-07-15 03:44:50','2026-07-15 03:44:50');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weather_reports`
--

DROP TABLE IF EXISTS `weather_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weather_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `temperature` decimal(4,2) NOT NULL,
  `feels_like` decimal(4,2) NOT NULL,
  `humidity` tinyint unsigned NOT NULL,
  `wind_speed` decimal(5,2) NOT NULL,
  `rain_probability` tinyint unsigned NOT NULL DEFAULT '0',
  `uv_index` decimal(3,1) NOT NULL DEFAULT '0.0',
  `risk_score` tinyint unsigned NOT NULL DEFAULT '0',
  `risk_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'low',
  `recommendations` json DEFAULT NULL,
  `cached_until` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weather_reports_event_id_foreign` (`event_id`),
  KEY `idx_reports_cached_until` (`cached_until`),
  CONSTRAINT `weather_reports_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weather_reports`
--

LOCK TABLES `weather_reports` WRITE;
/*!40000 ALTER TABLE `weather_reports` DISABLE KEYS */;
INSERT INTO `weather_reports` VALUES (1,8,19.75,19.35,60,5.40,67,5.0,46,'moderate','[\"Risco moderado de precipitação. Recomenda-se a contratação de tendas ou coberturas estruturais.\"]','2026-07-13 22:44:21','2026-07-13 20:44:21','2026-07-13 20:44:21'),(2,11,12.93,12.37,80,15.01,35,0.0,31,'moderate','[]','2026-07-13 23:30:48','2026-07-13 21:30:48','2026-07-13 21:30:48'),(3,12,29.47,30.15,49,17.71,0,0.6,14,'low','[]','2026-07-13 23:33:39','2026-07-13 21:33:39','2026-07-13 21:33:39'),(4,13,27.56,28.13,52,16.09,0,0.6,14,'low','[]','2026-07-14 00:13:44','2026-07-13 22:13:44','2026-07-13 22:13:44'),(5,14,12.07,11.76,93,4.82,0,0.0,10,'low','[]','2026-07-14 05:57:11','2026-07-14 03:57:11','2026-07-14 03:57:11'),(6,15,11.34,10.46,74,7.92,0,0.0,14,'low','[]','2026-07-15 05:54:58','2026-07-15 03:54:58','2026-07-15 03:54:58');
/*!40000 ALTER TABLE `weather_reports` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-15  4:25:32
