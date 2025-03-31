-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 25, 2025 at 07:44 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `university_recruitment`
--

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
CREATE TABLE IF NOT EXISTS `programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `program_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tuition_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `duration` int NOT NULL DEFAULT '12',
  `level_id` int NOT NULL,
  `study_mode_id` int NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `program_name`, `school_id`, `created_at`, `updated_at`, `tuition_fee`, `duration`, `level_id`, `study_mode_id`) VALUES
(1, 'Bachelor of IT', 1, '2025-02-02 01:48:16', '2025-02-04 07:22:59', 4500.00, 4, 2, 3),
(4, 'Bachelor of Law (LLB)', 7, '2025-03-14 17:16:14', '2025-03-16 18:59:57', 9000.00, 4, 3, 3),
(5, 'Postgraduate Diploma in Teaching Methodology', 1, '2025-03-16 18:22:48', '2025-03-16 18:22:48', 9750.00, 1, 4, 3),
(6, 'Master of Education in Education Technology', 1, '2025-03-16 18:24:19', '2025-03-16 18:24:19', 9000.00, 2, 4, 3),
(7, 'Master of Education in Open, Distance and E-Learning', 1, '2025-03-16 18:25:40', '2025-03-24 13:17:12', 9000.00, 2, 4, 3),
(8, 'Master of Education in Educational Management', 1, '2025-03-16 18:26:12', '2025-03-16 18:26:12', 9000.00, 2, 4, 3),
(9, 'Master of Science in Computer Science', 5, '2025-03-16 18:27:23', '2025-03-16 18:27:23', 11000.00, 2, 0, 3),
(10, 'Master of Business Administration General', 8, '2025-03-16 18:30:33', '2025-03-16 18:30:33', 9000.00, 2, 4, 3),
(11, 'Master of Business Administration in Human Resource Management', 8, '2025-03-16 18:31:07', '2025-03-16 18:31:07', 9000.00, 2, 4, 3),
(12, 'Master of Business Administration in in Risk and Security management', 8, '2025-03-16 18:31:43', '2025-03-24 13:15:00', 9000.00, 2, 4, 3),
(13, 'MSc in Business Intelligence and Data Analysis', 8, '2025-03-16 18:33:10', '2025-03-24 13:17:21', 9500.00, 2, 4, 3),
(14, 'Master of Social Work in Community Development', 3, '2025-03-16 18:33:47', '2025-03-16 18:33:47', 9000.00, 2, 4, 3),
(15, 'Master of Science in Project Monitoring and Evaluation', 3, '2025-03-16 18:35:01', '2025-03-24 20:43:53', 9000.00, 2, 4, 3),
(16, 'Master of Social Work in Medical Psychiatric', 3, '2025-03-16 18:36:27', '2025-03-16 18:36:27', 9000.00, 2, 4, 3),
(17, 'Bachelor of Education in Primary Education', 1, '2025-03-16 18:37:35', '2025-03-16 18:37:35', 6000.00, 4, 3, 3),
(18, 'Bachelor of Secondary Education', 1, '2025-03-16 18:38:23', '2025-03-16 18:38:23', 7500.00, 4, 3, 3),
(19, 'Bachelor of Science in Computer Science', 5, '2025-03-16 18:40:24', '2025-03-16 18:40:24', 9000.00, 4, 3, 3),
(20, 'Bachelor of Science in Cyber Security and Digital Forensic', 5, '2025-03-16 18:41:28', '2025-03-24 13:16:58', 9000.00, 4, 3, 3),
(21, 'Bachelor of Science in Artificial Intelligence ,machine learning and data science', 5, '2025-03-16 18:42:07', '2025-03-16 18:42:07', 9000.00, 4, 3, 3),
(22, 'Bachelor of Business Administration /Risk and Security management', 8, '2025-03-16 18:43:23', '2025-03-24 13:12:47', 7500.00, 4, 3, 3),
(23, 'Bachelor of business administration in Logistics and Supply chain management', 8, '2025-03-16 18:44:43', '2025-03-24 13:16:26', 7500.00, 4, 3, 3),
(24, 'Business Administration General', 8, '2025-03-16 18:46:28', '2025-03-16 18:46:28', 7500.00, 4, 3, 3),
(25, 'Bachelors of Public administration', 8, '2025-03-16 18:47:27', '2025-03-16 18:47:27', 9000.00, 4, 3, 3),
(26, 'Bachelor of Science in Accounting and Finance', 8, '2025-03-16 18:48:19', '2025-03-24 13:13:35', 7500.00, 4, 3, 3),
(27, 'Master of Science in Public Health', 6, '2025-03-16 18:51:08', '2025-03-16 18:56:03', 10000.00, 2, 3, 3),
(28, 'Bachelor of Science in Public Health', 6, '2025-03-16 18:52:40', '2025-03-16 18:52:40', 9600.00, 4, 3, 3),
(29, 'Diploma in Clinical Medicine', 6, '2025-03-16 18:58:31', '2025-03-24 10:59:59', 10000.00, 3, 2, 1),
(30, 'Diploma in Registered Nursing', 6, '2025-03-16 18:59:07', '2025-03-24 10:59:38', 10000.00, 3, 2, 1),
(31, 'Bachelor of Science in Criminology and Criminal Justice Science', 7, '2025-03-16 19:01:15', '2025-03-24 13:16:46', 8500.00, 4, 3, 3),
(32, 'Bachelor of Social Work', 3, '2025-03-17 07:14:28', '2025-03-17 07:14:28', 7500.00, 4, 3, 3),
(34, 'Cyber Security and Digital forensic', 5, '2025-03-24 07:04:53', '2025-03-24 13:14:28', 6000.00, 1, 1, 3),
(35, 'Artificial Intelligence', 5, '2025-03-24 07:06:23', '2025-03-24 07:06:23', 6000.00, 0, 1, 3),
(36, 'Bachelor of Science in Computer Science', 5, '2025-03-24 10:34:44', '2025-03-24 10:34:44', 11500.00, 4, 3, 1),
(38, 'Business Administration General', 8, '2025-03-24 19:08:48', '2025-03-24 19:08:48', 9500.00, 4, 3, 1),
(39, 'Bachelor of Science in Cyber Security and Digital Forensic', 8, '2025-03-24 19:10:18', '2025-03-24 19:23:31', 11500.00, 4, 3, 1),
(40, 'Bachelor of Business Administration /Risk and Security management', 8, '2025-03-24 19:11:32', '2025-03-24 19:11:32', 9500.00, 4, 3, 1),
(41, 'Bachelor of Education in Primary Education', 1, '2025-03-24 19:13:00', '2025-03-24 19:13:00', 7500.00, 4, 3, 1),
(42, 'Bachelor of Secondary Education', 1, '2025-03-24 19:13:46', '2025-03-24 19:13:46', 9500.00, 4, 3, 1),
(43, 'Bachelor of business administration in Logistics and Supply chain management', 8, '2025-03-24 19:15:03', '2025-03-24 19:15:03', 9500.00, 4, 3, 1),
(44, 'Bachelor of Law (LLB)', 7, '2025-03-24 19:17:08', '2025-03-24 19:17:08', 9500.00, 4, 3, 1),
(45, 'Bachelor of Science in Accounting and Finance', 8, '2025-03-24 19:18:28', '2025-03-24 19:18:28', 9500.00, 4, 3, 1),
(46, 'Bachelor of Science in Artificial Intelligence ,machine learning and data science', 5, '2025-03-24 19:20:19', '2025-03-24 19:20:19', 11500.00, 4, 3, 1),
(47, 'Bachelor of Science in Criminology and Criminal Justice Science', 7, '2025-03-24 19:22:45', '2025-03-25 07:14:39', 10500.00, 4, 3, 1),
(48, 'Bachelor of Science in Public Health', 6, '2025-03-24 19:24:54', '2025-03-24 19:24:54', 10000.00, 4, 3, 1),
(49, 'Bachelor of Social Work', 3, '2025-03-24 19:27:44', '2025-03-24 19:27:44', 9500.00, 4, 3, 1),
(50, 'Bachelors of Public administration', 8, '2025-03-24 19:40:12', '2025-03-24 19:40:12', 9500.00, 4, 3, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
