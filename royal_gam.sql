-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 11:48 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `royal_gam`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_members`
--

CREATE TABLE `family_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `education` varchar(255) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `member_type` enum('father','mother','son','daughter','spouse','other') NOT NULL,
  `marital_status` enum('single','married','divorced','widowed') DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_members`
--

INSERT INTO `family_members` (`id`, `user_id`, `name`, `father_name`, `surname`, `age`, `education`, `blood_group`, `occupation`, `member_type`, `marital_status`, `profile_image`, `created_at`, `updated_at`) VALUES
(10, 15, 'John Doe new', 'Michael Doe', 'Doe', 35, 'Bachelor\'s Degree', 'A+', 'Software Engineer', 'father', 'married', '1747733616.jpg', '2025-05-20 04:03:36', '2025-05-20 04:03:36'),
(11, 15, 'Alice', NULL, 'Smith', 30, NULL, NULL, NULL, 'mother', 'married', '1747733637_682c4c855a191.jpg', '2025-05-20 04:03:57', '2025-05-20 04:03:57'),
(12, 15, 'Bob', NULL, 'Smith', 35, NULL, NULL, NULL, 'father', 'married', '1747733637_682c4c8560972.jpg', '2025-05-20 04:03:57', '2025-05-20 04:03:57'),
(13, 15, 'Alice', NULL, 'Smith', 30, 'Bachelor\'s Degree', 'A+', 'Teacher', 'mother', 'married', '1747733785_682c4d19c8866.jpg', '2025-05-20 04:06:25', '2025-05-20 04:06:25'),
(14, 15, 'Bob', NULL, 'Smith', 35, 'Master\'s Degree', 'O-', 'Engineer', 'father', 'married', '1747733785_682c4d19ccada.jpg', '2025-05-20 04:06:25', '2025-05-20 04:06:25'),
(15, 15, 'Alice', NULL, 'Smith', 30, 'Bachelor\'s Degree', 'A+', 'Teacher', 'mother', 'married', NULL, '2025-05-20 04:08:20', '2025-05-20 04:08:20'),
(16, 15, 'Bob', NULL, 'Smith', 35, 'Master\'s Degree', 'O-', 'Engineer', 'father', 'married', NULL, '2025-05-20 04:08:20', '2025-05-20 04:08:20'),
(17, 15, 'Charlie', NULL, 'Smith', 5, NULL, NULL, 'Student', 'son', 'single', NULL, '2025-05-20 04:08:20', '2025-05-20 04:08:20'),
(18, 15, 'John Doe new', 'Michael Doe', 'Doe', 35, 'Bachelor\'s Degree', 'A+', 'Software Engineer', 'father', 'married', NULL, '2025-05-20 04:18:16', '2025-05-20 04:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_05_17_110153_create_otp_verifications_table', 2),
(6, '2025_05_17_112817_add_whatsapp_verified_at_to_users_table', 3),
(7, '2025_05_17_113855_make_password_nullable_in_users_table', 4),
(8, '2025_05_19_102350_add_profile_fields_to_users_table', 5),
(9, '2025_05_19_123220_add_profile_image_to_users_table', 6),
(10, '2025_05_20_062133_create_family_members_table', 7),
(11, '2025_05_20_085806_add_profile_image_to_family_members_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `whatsapp_number` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `whatsapp_number`, `otp`, `expires_at`, `verified`, `created_at`, `updated_at`) VALUES
(40, '+919316014020', '123456', '2025-05-19 03:24:36', 0, '2025-05-19 03:19:36', '2025-05-19 03:19:36'),
(58, '+918569352480', '123456', '2025-05-19 04:35:33', 0, '2025-05-19 04:30:33', '2025-05-19 04:30:33'),
(59, '+918536985741', '123456', '2025-05-19 04:37:30', 0, '2025-05-19 04:32:30', '2025-05-19 04:32:30'),
(65, '+911236547890', '123456', '2025-05-19 05:50:18', 0, '2025-05-19 05:45:18', '2025-05-19 05:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 11, 'royal-gam-token', 'f31752c79c1d158a140a1518c05e5560f7d9d18b6f90228d39f2503b70ead4fe', '[\"*\"]', NULL, NULL, '2025-05-17 06:48:06', '2025-05-17 06:48:06'),
(2, 'App\\Models\\User', 12, 'royal-gam-token', '1d684b3a5f7b57fc156f7ee249572fce1ba7dac3e3aa9596aa44ece55ca86141', '[\"*\"]', NULL, NULL, '2025-05-17 07:13:23', '2025-05-17 07:13:23'),
(3, 'App\\Models\\User', 13, 'royal-gam-token', '9dfbd19e71bfefd7785f34a6df04160feb5edbef1d55c16458ed4cb42d725249', '[\"*\"]', NULL, NULL, '2025-05-17 07:16:09', '2025-05-17 07:16:09'),
(4, 'App\\Models\\User', 15, 'royal-gam-token', '05b11598ef08c191ea62bbd7bd0b610181538e4335ad8cbc1127415f8c919980', '[\"*\"]', NULL, NULL, '2025-05-17 07:51:00', '2025-05-17 07:51:00'),
(5, 'App\\Models\\User', 15, 'royal-gam-login-token', '091cfa03835c014b8d2e6c7ad12afbe6d0c2ed5821c02f535585fba9406c7f8f', '[\"*\"]', NULL, NULL, '2025-05-17 08:03:06', '2025-05-17 08:03:06'),
(6, 'App\\Models\\User', 58, 'royal-gam-token', '29587306dcd86c10cef73047b72b8d07b88837a2fcc2bfa7f0e4112ea4f0bcb8', '[\"*\"]', '2025-05-19 05:40:37', NULL, '2025-05-19 04:17:06', '2025-05-19 05:40:37'),
(7, 'App\\Models\\User', 62, 'royal-gam-token', '94eb319ce691ad8f4fa39a84940ec6e1376aeb9cb8cc8f2962844e76e6e52e03', '[\"*\"]', NULL, NULL, '2025-05-19 04:21:24', '2025-05-19 04:21:24'),
(8, 'App\\Models\\User', 15, 'royal-gam-login-token', 'ada54256c33a011bdbed53c8286ed8bc5bbb1d4005ce13e577794cd359970993', '[\"*\"]', '2025-05-20 04:18:16', NULL, '2025-05-19 05:10:42', '2025-05-20 04:18:16'),
(9, 'App\\Models\\User', 58, 'royal-gam-login-token', '792e3c668e987284c0d8c8dbac1ac74f521c503e9a37204d3c01566882c9eb10', '[\"*\"]', NULL, NULL, '2025-05-19 05:52:29', '2025-05-19 05:52:29'),
(10, 'App\\Models\\User', 58, 'royal-gam-login-token', '545fcf3909d448a41e1f8aa735ab22fb6976dff42e0b79518cbd991938486f0e', '[\"*\"]', NULL, NULL, '2025-05-19 05:59:23', '2025-05-19 05:59:23'),
(11, 'App\\Models\\User', 58, 'royal-gam-login-token', '421eaa6a8fe1ce99fd0385591073b2dbae1c0835c4942dff2a9a744260ddf605', '[\"*\"]', NULL, NULL, '2025-05-19 06:02:02', '2025-05-19 06:02:02'),
(12, 'App\\Models\\User', 58, 'royal-gam-login-token', '2641b129c1fcc3e8f856a6bba0596b9f1f43cfe9a62c5e23bbc7999c29b6b71c', '[\"*\"]', NULL, NULL, '2025-05-19 06:03:03', '2025-05-19 06:03:03'),
(13, 'App\\Models\\User', 58, 'royal-gam-login-token', 'bdb59a70d89472edeaa663dfc95294cee8a57524d5ea280737b5e7e65cd506e8', '[\"*\"]', NULL, NULL, '2025-05-19 06:09:11', '2025-05-19 06:09:11'),
(14, 'App\\Models\\User', 58, 'royal-gam-login-token', '9c01ddf916e1a4f201a3e9adc5538071e77be39cacfc38cb9602417baa30b109', '[\"*\"]', NULL, NULL, '2025-05-19 06:11:30', '2025-05-19 06:11:30'),
(15, 'App\\Models\\User', 58, 'royal-gam-login-token', '19916922fc089e1d6480467301b033a699d7a0887da66bc992cedb49cc2ec30b', '[\"*\"]', NULL, NULL, '2025-05-19 06:14:15', '2025-05-19 06:14:15'),
(16, 'App\\Models\\User', 58, 'royal-gam-login-token', '6bfad1a7a0f9af61b26e4f509789fe7b782bed5b84ca58234efc1674a06398f2', '[\"*\"]', NULL, NULL, '2025-05-19 06:15:09', '2025-05-19 06:15:09'),
(17, 'App\\Models\\User', 58, 'royal-gam-login-token', '7def5c0dbcc302a127c550934e6d3f802255051b1fe7f6930d7e122fbd7d2e31', '[\"*\"]', NULL, NULL, '2025-05-19 06:16:35', '2025-05-19 06:16:35'),
(18, 'App\\Models\\User', 58, 'royal-gam-login-token', 'fa09099cca7f243d3ea2a3aac09ca37be10f9dadba639cb9bf446669a316c356', '[\"*\"]', NULL, NULL, '2025-05-19 06:17:38', '2025-05-19 06:17:38'),
(19, 'App\\Models\\User', 58, 'royal-gam-login-token', 'de9e271a7f179c261c66c3627a01c04440eef956a5babf7c44128268d67a2aba', '[\"*\"]', NULL, NULL, '2025-05-19 06:18:55', '2025-05-19 06:18:55'),
(20, 'App\\Models\\User', 58, 'royal-gam-login-token', '9abdb0cf2380b2ad574675aa3df38ebb645017612fca0ce4ac104d04fefb4aab', '[\"*\"]', NULL, NULL, '2025-05-19 06:19:59', '2025-05-19 06:19:59'),
(21, 'App\\Models\\User', 58, 'royal-gam-login-token', 'a8e6c0613a06b65566e413e82375a365f2edd0ea85c066ab5f32151deb898ad4', '[\"*\"]', NULL, NULL, '2025-05-19 06:21:54', '2025-05-19 06:21:54'),
(22, 'App\\Models\\User', 58, 'royal-gam-login-token', 'cd677b66f2a1888e7d9d5b7ef0375c5f04b24627c3a6e3b85e2e634ce59b1708', '[\"*\"]', NULL, NULL, '2025-05-19 06:23:01', '2025-05-19 06:23:01'),
(23, 'App\\Models\\User', 58, 'royal-gam-login-token', '8cfb61f5144549fb8666aa829333275ae74503b6c91c7dec2d4d48b851a414a0', '[\"*\"]', NULL, NULL, '2025-05-19 06:25:03', '2025-05-19 06:25:03'),
(24, 'App\\Models\\User', 58, 'royal-gam-login-token', '0e515fb5a4d60905b900e3a4dba34e67ba256b14d0f6101f80f4638a78c70467', '[\"*\"]', '2025-05-19 07:28:09', NULL, '2025-05-19 06:30:30', '2025-05-19 07:28:09'),
(25, 'App\\Models\\User', 58, 'royal-gam-login-token', '5fc248e5f038b815c357decaa92547aadd83474bbc4b35c3402371cbb84d9e25', '[\"*\"]', NULL, NULL, '2025-05-20 00:06:29', '2025-05-20 00:06:29'),
(26, 'App\\Models\\User', 58, 'royal-gam-login-token', '79a748f04b34715ce70b9e37a70a24f0f0be48c15e18205c46b5d7e182423303', '[\"*\"]', NULL, NULL, '2025-05-20 00:11:28', '2025-05-20 00:11:28'),
(27, 'App\\Models\\User', 58, 'royal-gam-login-token', '028c7498af6572cd8b9f2dd29882ccae5b0bf12eabfc2d90d0aa7349918cbdc0', '[\"*\"]', NULL, NULL, '2025-05-20 00:34:32', '2025-05-20 00:34:32'),
(28, 'App\\Models\\User', 58, 'royal-gam-login-token', 'f928176dedcbcd642d8406a4c8035559990a651bc8115ea095468cd889645a65', '[\"*\"]', '2025-05-20 01:46:21', NULL, '2025-05-20 00:35:04', '2025-05-20 01:46:21'),
(29, 'App\\Models\\User', 67, 'royal-gam-token', 'c4ebacb7a0424e28e500b3be99e53d5eb0206ed64a0c93d1c2ce11242ecb1134', '[\"*\"]', NULL, NULL, '2025-05-20 00:42:31', '2025-05-20 00:42:31'),
(30, 'App\\Models\\User', 58, 'royal-gam-login-token', '12a2e0015ad3dc7d2b3cd6de01ee891f6d9429a88d1aef30c44364b5ee1a918c', '[\"*\"]', '2025-05-20 03:24:30', NULL, '2025-05-20 03:22:05', '2025-05-20 03:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `whatsapp_verified_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `surname`, `father_name`, `email`, `whatsapp_number`, `address`, `blood_group`, `education`, `occupation`, `age`, `marital_status`, `profile_image`, `whatsapp_verified_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(15, 'NewUsername', 'NewName sd sdfsdf', 'NewSurname', 'NewFatherName', 'newemail@example.com', '919876543210', 'New Address, City, State', 'A+', 'Master\'s Degree', 'Software Engineer', 30, 'single', '1747664905.jpg', '2025-05-17 07:51:00', NULL, NULL, NULL, '2025-05-17 07:50:42', '2025-05-19 08:58:25'),
(42, 'testuser11', 'Test', 'User', NULL, NULL, '+919316014020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-19 03:19:36', '2025-05-19 03:19:36'),
(58, 'Mansi', 'Nileshbhai', 'Bhadani', NULL, NULL, '+919016727236', 'Surat', 'O+', 'બેચલર', 'Business', 20, 'single', '1747725382.jpg', '2025-05-19 04:17:05', NULL, NULL, NULL, '2025-05-19 04:12:32', '2025-05-20 03:24:30'),
(62, 'Riya', 'Nileshbhai', 'Patel', NULL, NULL, '+917984109682', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-19 04:21:24', NULL, NULL, NULL, '2025-05-19 04:21:06', '2025-05-19 04:21:24'),
(63, 'Rajvi', 'Dineshbhai', 'Dhameliya', NULL, NULL, '+918569352480', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-19 04:30:33', '2025-05-19 04:30:33'),
(64, 'Sneha', 'Rameshbhai', 'Patel', NULL, NULL, '+918536985741', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-19 04:32:30', '2025-05-19 04:32:30'),
(66, 'testuser', 'Tesst', 'User', NULL, NULL, '+911236547890', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-19 05:45:18', '2025-05-19 05:45:18'),
(67, 'Sanjaykumar', 'Kanjibhai', 'Dhameliya', NULL, NULL, '+919714027278', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-20 00:42:31', NULL, NULL, NULL, '2025-05-20 00:42:23', '2025-05-20 00:42:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_members_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_whatsapp_number_unique` (`whatsapp_number`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_members`
--
ALTER TABLE `family_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `family_members`
--
ALTER TABLE `family_members`
  ADD CONSTRAINT `family_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
