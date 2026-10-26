-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2026 at 09:23 AM
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
-- Database: `ruangkarya`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Administrator Sekolah', 'admin@sekolah.sch.id', '$2y$10$NEpdBJqx6lxbXMxz34exKeWknXXuwb1ePbCujPbNCb7hpivn3isNO', '2026-10-17 16:02:37'),
(3, 'Administrator Sekolah', 'admin@gmail.com', '$2y$10$s4OZD3.8.B5epYmRWATO4eZyuM9U1ou/2k.LzQDCXwOsJeKulwj6', '2026-10-17 16:30:45');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(180) NOT NULL,
  `student_name` varchar(120) NOT NULL,
  `student_class` varchar(80) NOT NULL,
  `category` enum('Website','Game','Desain','Aplikasi','Video','Lainnya') NOT NULL DEFAULT 'Lainnya',
  `description` text NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `demo_url` varchar(500) DEFAULT NULL,
  `project_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `student_name`, `student_class`, `category`, `description`, `cover_image`, `demo_url`, `project_url`, `status`, `created_at`) VALUES
(1, 'websiteecommers', 'salsa1', 'x', 'Website', 'website pembelian barang', 'uploads/ebe6856f25da79ed1b00135b3fa0efe5.png', 'https://ekizr.com/', 'https://github.com/EkiZR/Portofolio_V5.git', 'approved', '2026-10-17 16:08:54'),
(2, 'video anti bullyingggg', 'andikaaa', 'XRPL2', 'Video', 'Video yang mnerngkan anti bully', 'uploads/85fa5f26bcc369521ae62624663ac24d.jpg', 'https://www.youtube.com/watch?v=ACDNRR9XIns&list=RDACDNRR9XIns&start_radio=1', 'https://www.youtube.com/watch?v=ACDNRR9XIns&list=RDACDNRR9XIns&start_radio=1', 'approved', '2026-10-17 16:21:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
