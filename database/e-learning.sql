-- SQL siap import untuk InfinityFree (phpMyAdmin)
-- Pilih database if0_xxx dulu di panel kiri, lalu Import.
-- Urutan tabel: users -> password_reset_tokens -> courses -> lessons

SET FOREIGN_KEY_CHECKS=0;
SET NAMES utf8mb4;
SET TIME_ZONE='+00:00';

-- Bersihkan sisa import sebelumnya
DROP TABLE IF EXISTS `lessons`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Demo', 'admin@elearning.test', NULL, '$2y$12$3HXEZHXTeTrA0SSI6Aq95Oh.Sa6ijl4pgqZ7T6mo5CECI5OMJ4q9u', 'admin', NULL, '2026-11-18 12:04:17', '2026-11-18 12:08:24'),
(2, 'Student Demo', 'student@elearning.test', NULL, '$2y$12$8g8ax4XklWYXBBFg.nDB2eBr25L98vIK69e5kGe1SKaP4Nu4Ewjs2', 'student', 'iyyoyho9W1mWQru7L7lRXBYInydMX8teGZmGn8EVJQ450QRv6C0u4Y341CuJ', '2026-11-18 12:04:18', '2026-11-18 12:08:24');

-- ------------------------------------------------------------
-- password_reset_tokens
-- ------------------------------------------------------------
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- courses
-- ------------------------------------------------------------
CREATE TABLE `courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courses_created_by_foreign` (`created_by`),
  CONSTRAINT `courses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `courses` (`id`, `title`, `description`, `is_published`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 'Dasar UI/UX Design', 'Mempelajari design website responsif dengan figma', 1, 1, '2026-11-18 12:47:46', '2026-11-18 12:56:18'),
(6, 'web development', 'mempelajari dasar web development', 1, 1, '2026-11-18 13:56:42', '2026-11-18 13:58:33');

-- ------------------------------------------------------------
-- lessons
-- ------------------------------------------------------------
CREATE TABLE `lessons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `position` int unsigned NOT NULL DEFAULT '1',
  `video_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_status` enum('none','uploading','ready','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lessons_course_id_position_index` (`course_id`,`position`),
  CONSTRAINT `lessons_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lessons` (`id`, `course_id`, `title`, `description`, `position`, `video_path`, `video_status`, `created_at`, `updated_at`) VALUES
(7, 4, 'Pengenalan Tools', 'Mengenal fungsi setiap tools', 1, 'assets/videos/lessons/7/ujdEu6hNg7vVuGmzeiv6m9gZahA9DybRfoRCeQGA.mp4', 'ready', '2026-11-18 12:57:19', '2026-11-18 12:57:29'),
(9, 6, 'pengenalan web', 'pengertian dan cara kerja web', 1, NULL, 'none', '2026-11-18 13:58:13', '2026-11-18 13:58:13'),
(10, 4, 'Framing', 'Membuat tampilan sederhana untuk frame website dan mobile', 2, 'assets/videos/lessons/10/oOk72EiFf83EygVWkr6QxDnPmCYzWhkrVLzaxZDl.mp4', 'ready', '2026-11-18 13:59:18', '2026-11-18 13:59:35');

SET FOREIGN_KEY_CHECKS=1;
