/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `auction_blocklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auction_blocklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `blocked_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auction_blocklists_auction_id_user_id_unique` (`auction_id`,`user_id`),
  KEY `auction_blocklists_user_id_foreign` (`user_id`),
  KEY `auction_blocklists_blocked_by_foreign` (`blocked_by`),
  CONSTRAINT `auction_blocklists_auction_id_foreign` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auction_blocklists_blocked_by_foreign` FOREIGN KEY (`blocked_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auction_blocklists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auction_deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auction_deposits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `wallet_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL COMMENT 'مبلغ الضمان المدفوع',
  `status` enum('held','released','forfeited') NOT NULL DEFAULT 'held' COMMENT 'حالة مبلغ الضمان',
  `released_at` timestamp NULL DEFAULT NULL COMMENT 'وقت إعادة الضمان',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auction_deposits_auction_id_user_id_unique` (`auction_id`,`user_id`),
  KEY `auction_deposits_user_id_foreign` (`user_id`),
  KEY `auction_deposits_wallet_transaction_id_foreign` (`wallet_transaction_id`),
  KEY `auction_deposits_status_index` (`status`),
  CONSTRAINT `auction_deposits_auction_id_foreign` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auction_deposits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auction_deposits_wallet_transaction_id_foreign` FOREIGN KEY (`wallet_transaction_id`) REFERENCES `wallet_transactions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auction_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auction_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auction_images_auction_id_foreign` (`auction_id`),
  CONSTRAINT `auction_images_auction_id_foreign` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auction_watchlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auction_watchlist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auction_watchlist_auction_id_user_id_unique` (`auction_id`,`user_id`),
  KEY `auction_watchlist_user_id_foreign` (`user_id`),
  CONSTRAINT `auction_watchlist_auction_id_foreign` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auction_watchlist_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auctions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `title_ar` varchar(255) NOT NULL COMMENT 'عنوان المزاد بالعربي',
  `title_en` varchar(255) NOT NULL COMMENT 'عنوان المزاد بالإنجليزي',
  `description_ar` text DEFAULT NULL COMMENT 'وصف بالعربي',
  `description_en` text DEFAULT NULL COMMENT 'وصف بالإنجليزي',
  `location_ar` varchar(255) DEFAULT NULL,
  `location_en` varchar(255) DEFAULT NULL,
  `start_price` decimal(15,2) NOT NULL COMMENT 'سعر البداية',
  `reserve_price` decimal(15,2) DEFAULT NULL COMMENT 'السعر الاحتياطي (أدنى سعر مقبول للبيع)',
  `min_bid_increment` decimal(15,2) NOT NULL DEFAULT 500.00 COMMENT 'أقل زيادة مسموح بها في العرض',
  `buy_now_price` decimal(15,2) DEFAULT NULL COMMENT 'سعر الشراء الفوري (اختياري)',
  `deposit_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'مبلغ الضمان المطلوب للمشاركة',
  `deposit_required` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'هل الضمان إلزامي',
  `start_time` datetime NOT NULL COMMENT 'وقت بدء المزاد',
  `end_time` datetime NOT NULL COMMENT 'وقت انتهاء المزاد',
  `auto_extend_minutes` smallint(5) unsigned NOT NULL DEFAULT 5 COMMENT 'تمديد تلقائي بالدقائق عند تقديم عرض في آخر لحظة',
  `status` enum('draft','scheduled','live','ended','cancelled','sold') NOT NULL DEFAULT 'draft' COMMENT 'حالة المزاد',
  `is_paused` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'هل المزاد موقوف مؤقتا',
  `winner_id` bigint(20) unsigned DEFAULT NULL,
  `winning_bid_amount` decimal(15,2) DEFAULT NULL COMMENT 'قيمة عرض الفائز',
  `sold_at` timestamp NULL DEFAULT NULL COMMENT 'وقت إتمام البيع',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 5.00 COMMENT 'نسبة عمولة المنصة %',
  `commission_amount` decimal(15,2) DEFAULT NULL COMMENT 'مبلغ العمولة المحسوب',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'مزاد مميز',
  `views_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'عدد المشاهدات',
  `bids_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'عدد العروض المقدمة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auctions_vehicle_id_foreign` (`vehicle_id`),
  KEY `auctions_created_by_foreign` (`created_by`),
  KEY `auctions_winner_id_foreign` (`winner_id`),
  KEY `auctions_status_index` (`status`),
  KEY `auctions_start_time_index` (`start_time`),
  KEY `auctions_end_time_index` (`end_time`),
  KEY `auctions_status_start_time_index` (`status`,`start_time`),
  CONSTRAINT `auctions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `auctions_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  CONSTRAINT `auctions_winner_id_foreign` FOREIGN KEY (`winner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) NOT NULL,
  `iban` varchar(255) NOT NULL,
  `beneficiary_name` varchar(255) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL COMMENT 'قيمة العرض',
  `is_auto_bid` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'هل هو عرض تلقائي (Auto Bid)',
  `max_auto_bid` decimal(15,2) DEFAULT NULL COMMENT 'الحد الأقصى للعرض التلقائي',
  `status` enum('active','outbid','won','cancelled') NOT NULL DEFAULT 'active' COMMENT 'حالة العرض',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'عنوان IP',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'معلومات الجهاز/المتصفح',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bids_auction_id_index` (`auction_id`),
  KEY `bids_user_id_index` (`user_id`),
  KEY `bids_status_index` (`status`),
  KEY `bids_auction_id_amount_index` (`auction_id`,`amount`),
  KEY `bids_auction_id_status_index` (`auction_id`,`status`),
  CONSTRAINT `bids_auction_id_foreign` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bids_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `deposit_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deposit_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `wallet_id` bigint(20) unsigned NOT NULL,
  `bank_account_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `action_by` bigint(20) unsigned DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deposit_requests_user_id_foreign` (`user_id`),
  KEY `deposit_requests_wallet_id_foreign` (`wallet_id`),
  KEY `deposit_requests_bank_account_id_foreign` (`bank_account_id`),
  KEY `deposit_requests_action_by_foreign` (`action_by`),
  CONSTRAINT `deposit_requests_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `deposit_requests_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `deposit_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deposit_requests_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faqs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_ar` varchar(255) NOT NULL,
  `question_en` varchar(255) NOT NULL,
  `answer_ar` text NOT NULL,
  `answer_en` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `features` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `description_en` text NOT NULL,
  `description_ar` text NOT NULL,
  `icon` text NOT NULL,
  `color_class` varchar(255) NOT NULL DEFAULT 'blue',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kyc_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kyc_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `id_number` varchar(255) NOT NULL,
  `id_image` varchar(255) NOT NULL,
  `selfie_image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kyc_requests_user_id_foreign` (`user_id`),
  KEY `kyc_requests_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `kyc_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kyc_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `bid_amount` decimal(15,2) NOT NULL,
  `deposit_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `delivery_type` enum('pickup','delivery') NOT NULL DEFAULT 'pickup',
  `delivery_address` text DEFAULT NULL,
  `delivery_phone` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_auction_id_foreign` (`auction_id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `orders_auction_id_foreign` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `content_ar` text NOT NULL,
  `content_en` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_footer` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `platform_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platform_commissions` (
  `amount` decimal(15,2) NOT NULL COMMENT 'مبلغ العمولة',
  `type` enum('manual','dynamic','fixed') NOT NULL DEFAULT 'dynamic' COMMENT 'نوع العمولة',
  `payment_status` enum('pending','paid','canceled') NOT NULL DEFAULT 'pending' COMMENT 'حالة سداد العمولة للمنصة',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ اكتمال أو تحصيل العمولة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `role_ar` varchar(255) NOT NULL,
  `role_en` varchar(255) NOT NULL,
  `text_ar` text NOT NULL,
  `text_en` text NOT NULL,
  `avatar_init` varchar(255) DEFAULT NULL,
  `avatar_init_en` varchar(2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_messages_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_messages_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','in_progress','closed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_user_id_foreign` (`user_id`),
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `kyc_level` int(11) NOT NULL DEFAULT 0,
  `auto_bid_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `identity_verified_at` timestamp NULL DEFAULT NULL,
  `id_number` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `bic_code` varchar(255) DEFAULT NULL,
  `beneficiary_name` varchar(255) DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `bank_city` varchar(255) DEFAULT NULL,
  `bank_country` varchar(255) DEFAULT NULL,
  `check_bank` tinyint(1) DEFAULT 0,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL COMMENT 'مسار الصورة',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'هل هي الصورة الرئيسية',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'ترتيب العرض',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_images_vehicle_id_index` (`vehicle_id`),
  CONSTRAINT `vehicle_images_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_makes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_makes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_models` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `make_id` bigint(20) unsigned NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_models_make_id_foreign` (`make_id`),
  CONSTRAINT `vehicle_models_make_id_foreign` FOREIGN KEY (`make_id`) REFERENCES `vehicle_makes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submitted_by` bigint(20) unsigned DEFAULT NULL,
  `make_ar` varchar(255) DEFAULT NULL,
  `make_en` varchar(255) DEFAULT NULL,
  `model_ar` varchar(255) DEFAULT NULL,
  `model_en` varchar(255) DEFAULT NULL,
  `year` year(4) NOT NULL COMMENT 'سنة الصنع',
  `color_ar` varchar(255) DEFAULT NULL,
  `color_en` varchar(255) DEFAULT NULL,
  `vin_number` varchar(17) DEFAULT NULL COMMENT 'رقم الهيكل VIN',
  `mileage` bigint(20) unsigned DEFAULT NULL COMMENT 'عداد الكيلومترات',
  `plate_number` varchar(255) DEFAULT NULL COMMENT 'رقم اللوحة',
  `country_of_origin` varchar(255) DEFAULT NULL COMMENT 'بلد المنشأ',
  `fuel_type` enum('petrol','diesel','electric','hybrid','other') NOT NULL DEFAULT 'petrol' COMMENT 'نوع الوقود',
  `transmission` enum('automatic','manual','cvt') NOT NULL DEFAULT 'automatic' COMMENT 'ناقل الحركة',
  `engine_capacity` varchar(255) DEFAULT NULL COMMENT 'سعة المحرك - مثل: 2.0L, 3.5L',
  `cylinders` smallint(5) unsigned DEFAULT NULL COMMENT 'عدد الأسطوانات',
  `condition` enum('new','excellent','good','fair','damaged') NOT NULL DEFAULT 'good' COMMENT 'حالة المركبة',
  `description_ar` text DEFAULT NULL COMMENT 'وصف المركبة بالعربي',
  `description_en` text DEFAULT NULL COMMENT 'وصف المركبة بالإنجليزي',
  `features` text DEFAULT NULL COMMENT 'مميزات إضافية (JSON array)',
  `issues_ar` text DEFAULT NULL,
  `issues_en` text DEFAULT NULL,
  `damage_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`damage_points`)),
  `status` enum('pending','approved','rejected','sold') NOT NULL DEFAULT 'pending' COMMENT 'حالة المركبة في النظام',
  `rejection_reason` text DEFAULT NULL COMMENT 'سبب الرفض إن وجد',
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_vin_number_unique` (`vin_number`),
  KEY `vehicles_submitted_by_foreign` (`submitted_by`),
  KEY `vehicles_reviewed_by_foreign` (`reviewed_by`),
  KEY `vehicles_status_index` (`status`),
  KEY `vehicles_year_index` (`year`),
  CONSTRAINT `vehicles_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicles_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallet_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallet_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_id` bigint(20) unsigned NOT NULL,
  `type` enum('credit','debit') NOT NULL COMMENT 'نوع المعاملة: credit (إيداع) أو debit (سحب)',
  `amount` decimal(15,2) NOT NULL COMMENT 'مبلغ العملية',
  `description` text DEFAULT NULL COMMENT 'الوصف أو الملاحظات',
  `attachment_path` varchar(255) DEFAULT NULL COMMENT 'مسار الملف المرفق (صورة أو PDF)',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `maturity_time` datetime DEFAULT NULL COMMENT 'تاريخ ووقت الاستحقاق',
  `payment_method` varchar(255) DEFAULT NULL COMMENT 'وسيلة الدفع',
  `due_date` date DEFAULT NULL COMMENT 'تاريخ الاستحقاق',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallet_transactions_wallet_id_foreign` (`wallet_id`),
  KEY `wallet_transactions_created_by_foreign` (`created_by`),
  CONSTRAINT `wallet_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_deposits` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_withdrawals` decimal(15,2) NOT NULL DEFAULT 0.00,
  `debt_ceiling` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'سقف الدين المسموح به',
  `debt_usage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'نسبة استهلاك الدين',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallets_user_id_foreign` (`user_id`),
  CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `withdrawal_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `withdrawal_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `wallet_id` bigint(20) unsigned NOT NULL,
  `requested_amount` decimal(15,2) NOT NULL COMMENT 'المبلغ المطلوب سحبه',
  `approved_amount` decimal(15,2) DEFAULT NULL COMMENT 'المبلغ المعتمد من الإدارة',
  `status` enum('pending','processing','approved','rejected','completed') NOT NULL DEFAULT 'pending' COMMENT 'حالة طلب السحب',
  `payment_method` varchar(255) DEFAULT NULL COMMENT 'طريقة الدفع/التحويل',
  `action_by` bigint(20) unsigned DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ ووقت المعالجة',
  `admin_notes` text DEFAULT NULL COMMENT 'ملاحظات الإدارة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdrawal_requests_user_id_foreign` (`user_id`),
  KEY `withdrawal_requests_wallet_id_foreign` (`wallet_id`),
  KEY `withdrawal_requests_action_by_foreign` (`action_by`),
  CONSTRAINT `withdrawal_requests_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `withdrawal_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `withdrawal_requests_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_04_24_235649_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_04_25_004256_add_extra_fields_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_04_28_011541_add_identity_verification_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_04_29_211057_create_pages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_05_02_002542_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_05_03_225646_add_id_photo_and_status_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_05_03_233735_create_kyc_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_05_03_233743_update_users_for_kyc_system',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_05_06_004141_create_bank_accounts_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_05_07_005705_add_bank_details_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_05_07_011836_add_bank_name_and_account_number_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_05_08_003651_create_faqs_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_04_28_141441_create_posts_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_05_11_225936_create_wallets_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_05_11_231234_create_wallet_transactions_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_05_11_231405_create_withdrawal_requests_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_05_11_231659_create_platform_commissions_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_05_12_200600_add_maturity_time_and_payment_method_to_wallet_transactions_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_05_15_234432_create_deposit_requests_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_06_04_000001_create_vehicles_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_06_04_000002_create_vehicle_images_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_06_04_000003_create_auctions_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_06_04_000004_create_bids_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_06_04_000005_create_auction_deposits_and_watchlist_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_06_04_115149_create_personal_access_tokens_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_05_19_020403_create_personal_access_tokens_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_06_09_134911_add_image_to_auctions_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_06_12_013857_create_orders_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_06_12_224744_add_is_paused_to_auctions_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_06_12_230919_create_auction_blocklists_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_06_12_234036_add_damage_points_to_vehicles_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_06_13_204118_add_bilingual_fields_to_vehicles_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_06_13_204924_add_bilingual_location_to_auctions_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_06_22_161830_create_notifications_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_06_22_161831_add_fcm_token_to_users_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_06_23_024500_add_is_deleted_to_users_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_06_23_024655_add_is_deleted_to_users_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_06_23_050000_create_auction_images_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_06_29_100000_add_auto_bid_enabled_to_users_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_06_29_063813_create_tickets_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_06_29_063813_create_vehicle_makes_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_06_29_063813_create_vehicle_models_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_06_29_063822_create_ticket_messages_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_07_07_014500_add_sort_order_to_faqs_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_07_23_202413_create_testimonials_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_07_23_235745_add_avatar_init_en_to_testimonials',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_07_25_200000_create_features_table',11);
