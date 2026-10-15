-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2026 at 08:44 PM
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
-- Database: `nexus_educational_tours`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'yashkarpe29', '$2y$10$JNpl87S3FFiaf6k6ZHgGDuxo6pmnADjkpFFDjbmQcbgrUzqvPieR.', '2026-10-12 14:30:18', '2026-10-12 14:30:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_attempts`
--

CREATE TABLE `admin_login_attempts` (
  `ip_address` varchar(45) NOT NULL,
  `failed_attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `first_attempt_at` datetime NOT NULL,
  `locked_until` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_enquiries`
--

CREATE TABLE `contact_enquiries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','contacted','in_progress','closed') NOT NULL DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_enquiries`
--

INSERT INTO `contact_enquiries` (`id`, `name`, `phone`, `email`, `subject`, `message`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 'Yash Karpe', '+919689855914', 'rishikeshhake2018@gmail.com', 'Educational Tour', 'jhfyhrn vbhgn vnghb bgfbv nhbfhtgftr nhvbfg tryhfng bcgf\nApproximate group size: 54\nPreferred tour date: 2026-10-13\nTour type: Educational Tour', 'contacted', NULL, '2026-10-12 18:00:27', '2026-10-12 18:17:26'),
(2, 'Yash Karpe', '+919689855914', 'yashkarpr1212@gmail.com', 'One Day Trip', 'bus and resrt\n\nOrganization: sangamner\nApproximate group size: 54\nPreferred tour date: 2026-10-13\nTour type: One Day Trip', 'contacted', NULL, '2026-10-17 13:35:25', '2026-10-17 13:35:47');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('tour_images','video_highlights','user_reviews','student_activities') NOT NULL,
  `media_type` enum('image','video') NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `video_url` varchar(1000) DEFAULT NULL,
  `alt_text` varchar(250) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_name` varchar(150) NOT NULL,
  `reviewer_role` varchar(150) DEFAULT NULL,
  `review_text` text NOT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `consent_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `category` varchar(100) NOT NULL,
  `destination` varchar(200) NOT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `price_from` decimal(10,2) DEFAULT NULL,
  `currency` char(3) NOT NULL DEFAULT 'INR',
  `min_group_size` int(10) UNSIGNED DEFAULT NULL,
  `max_group_size` int(10) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `booking_deadline` date DEFAULT NULL,
  `best_season` varchar(150) DEFAULT NULL,
  `meeting_point` varchar(255) DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `itinerary` mediumtext DEFAULT NULL,
  `inclusions` text DEFAULT NULL,
  `exclusions` text DEFAULT NULL,
  `things_to_carry` text DEFAULT NULL,
  `terms_conditions` mediumtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `title`, `slug`, `category`, `destination`, `short_description`, `description`, `duration`, `image_path`, `status`, `sort_order`, `price_from`, `currency`, `min_group_size`, `max_group_size`, `start_date`, `end_date`, `booking_deadline`, `best_season`, `meeting_point`, `highlights`, `itinerary`, `inclusions`, `exclusions`, `things_to_carry`, `terms_conditions`, `created_at`, `updated_at`) VALUES
(1, 'yahs', 'yahs', 'educatiojnal', 'pune', 'pune', 'pune', '1 day', '', 'published', 0, NULL, 'INR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 19:58:01', '2026-10-18 16:39:52'),
(2, 'visit', 'visit', 'educational', 'nashik', 'pune', 'pune', '2 days', '', 'archived', 1, NULL, 'INR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-16 17:27:34', '2026-10-18 17:29:33'),
(3, 'po', 'po', 'educational', 'pune', 'tyhgn cgyujh cgyuj', 'yjhg tyu', '1 day', 'uploads/tours/731634fc5576b7b42f44e050c5dfc4e4.jpg', 'published', 2, NULL, 'INR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 17:52:54', '2026-10-18 17:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `tour_enquiries`
--

CREATE TABLE `tour_enquiries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enquiry_type` enum('tour','custom') NOT NULL DEFAULT 'tour',
  `tour_id` bigint(20) UNSIGNED DEFAULT NULL,
  `institution_name` varchar(200) DEFAULT NULL,
  `contact_person` varchar(150) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `starting_location` varchar(200) DEFAULT NULL,
  `destination` varchar(200) DEFAULT NULL,
  `tour_category` varchar(100) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `students_count` int(10) UNSIGNED DEFAULT NULL,
  `teachers_count` int(10) UNSIGNED DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `status` enum('new','contacted','in_progress','closed') NOT NULL DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_gallery`
--

CREATE TABLE `tour_gallery` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_login_attempts`
--
ALTER TABLE `admin_login_attempts`
  ADD PRIMARY KEY (`ip_address`),
  ADD KEY `idx_locked_until` (`locked_until`);

--
-- Indexes for table `contact_enquiries`
--
ALTER TABLE `contact_enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contact_status` (`status`),
  ADD KEY `idx_contact_created` (`created_at`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gallery_status_category` (`status`,`category`),
  ADD KEY `idx_gallery_created` (`created_at`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviews_status_order` (`status`,`sort_order`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_tours_status_order` (`status`,`sort_order`),
  ADD KEY `idx_tours_category` (`category`);

--
-- Indexes for table `tour_enquiries`
--
ALTER TABLE `tour_enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_enquiry_status` (`status`),
  ADD KEY `idx_tour_enquiry_created` (`created_at`),
  ADD KEY `fk_tour_enquiry_tour` (`tour_id`);

--
-- Indexes for table `tour_gallery`
--
ALTER TABLE `tour_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_gallery_tour_sort` (`tour_id`,`sort_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_enquiries`
--
ALTER TABLE `contact_enquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tour_enquiries`
--
ALTER TABLE `tour_enquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_gallery`
--
ALTER TABLE `tour_gallery`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tour_enquiries`
--
ALTER TABLE `tour_enquiries`
  ADD CONSTRAINT `fk_tour_enquiry_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tour_gallery`
--
ALTER TABLE `tour_gallery`
  ADD CONSTRAINT `fk_tour_gallery_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
