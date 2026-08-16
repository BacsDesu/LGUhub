-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2026 at 05:19 AM
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
-- Database: `lgu_requests_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `attachment_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deleted_requests`
--

CREATE TABLE `deleted_requests` (
  `delete_id` int(11) NOT NULL,
  `original_request_id` int(11) DEFAULT NULL,
  `request_number` varchar(50) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `request_type` varchar(50) DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `from_dept` int(11) DEFAULT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `status_at_deletion` varchar(20) DEFAULT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deleted_requests`
--

INSERT INTO `deleted_requests` (`delete_id`, `original_request_id`, `request_number`, `title`, `description`, `request_type`, `priority`, `from_dept`, `requested_by`, `deleted_by`, `status_at_deletion`, `deleted_at`) VALUES
(32, 44, 'REQ-202607-0013', 'Request #REQ-202607-0013', '', 'Supply/Equipment', 'high', 22, 9, 1, 'completed', '2026-08-08 14:27:11'),
(33, 43, 'REQ-202607-0012', 'Request #REQ-202607-0012', '', 'Financial', 'medium', 22, 9, 1, 'completed', '2026-08-08 14:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `dept_id` int(11) NOT NULL,
  `dept_code` varchar(20) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_id`, `dept_code`, `dept_name`, `description`, `created_at`) VALUES
(1, 'MAYOR', 'Office of the Mayor', 'Executive Department', '2026-07-07 09:21:36'),
(2, 'VICE', 'Office of the Vice Mayor', 'Legislative Department', '2026-07-07 09:21:36'),
(3, 'SB', 'Sangguniang Bayan', 'Legislative Body', '2026-07-07 09:21:36'),
(4, 'HRMO', 'Human Resource Management Office', 'Personnel Management', '2026-07-07 09:21:36'),
(5, 'BUDGET', 'Budget Office', 'Financial Planning', '2026-07-07 09:21:36'),
(6, 'MTO', 'Municipal Treasury Office', 'Revenue Collection', '2026-07-07 09:21:36'),
(7, 'ASSESSOR', 'Assessors Office', 'Property Assessment', '2026-07-07 09:21:36'),
(8, 'ACCOUNTING', 'Accounting Office', 'Financial Reporting', '2026-07-07 09:21:36'),
(9, 'MENRO', 'Municipal Environment and Natural Resources Office', 'Environmental Protection', '2026-07-07 09:21:36'),
(10, 'ENGINEERING', 'Engineering Office', 'Infrastructure', '2026-07-07 09:21:36'),
(11, 'GSO', 'General Services Office', 'Procurement and Logistics', '2026-07-07 09:21:36'),
(12, 'AGRICULTURE', 'Agriculture Office', 'Agricultural Services', '2026-07-07 09:21:36'),
(13, 'HEALTH', 'Health Office', 'Public Health', '2026-07-07 09:21:36'),
(14, 'PESO', 'Public Employment Service Office', 'Job Placement', '2026-07-07 09:21:36'),
(16, 'DRRM', 'Disaster Risk Reduction and Management', 'Disaster Management', '2026-07-07 09:21:36'),
(21, 'MPDO', 'Municipal Planning and Development Office', 'Strategic Planning', '2026-07-17 19:44:10'),
(22, 'MCR', 'Municipal Civil Registry', 'Civil Records', '2026-07-17 19:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_system` tinyint(1) DEFAULT 0,
  `sender_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `user_id`, `request_id`, `message`, `is_read`, `is_system`, `sender_id`, `created_at`) VALUES
(2, 1, NULL, '🆕 New user registration pending approval: Pia Marie (piamariebacaling)', 1, 1, NULL, '2026-07-10 09:50:10'),
(3, 5, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 1, 1, NULL, '2026-07-10 10:07:30'),
(4, 1, NULL, '🆕 New user registration pending approval: Arlie G. Dumali (arliedumali)', 1, 1, NULL, '2026-07-17 12:27:02'),
(5, 1, NULL, '🆕 New user registration pending approval: Jacques Geomel  (jacquesgeomel)', 1, 1, NULL, '2026-07-17 12:41:35'),
(6, 1, NULL, '🆕 New user registration pending approval: Angelo A. Gallardo (angelogallardo)', 1, 1, NULL, '2026-07-17 12:51:51'),
(7, 8, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 1, 1, NULL, '2026-07-17 12:53:48'),
(8, 7, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 1, 1, NULL, '2026-07-17 12:53:53'),
(9, 6, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 0, 1, NULL, '2026-07-17 12:53:57'),
(19, 1, NULL, '🆕 New user registration pending approval: Collen V. Dela Paz (mcrlguestancia)', 1, 1, NULL, '2026-07-17 19:54:43'),
(20, 1, NULL, '🆕 New user registration pending approval: Rosalinda M. Cruz (assesslguestancia)', 1, 1, NULL, '2026-07-17 19:57:02'),
(21, 1, NULL, '🆕 New user registration pending approval: Bernadette S. Cruz (pesolguestancia)', 1, 1, NULL, '2026-07-17 19:58:42'),
(22, 1, NULL, '🆕 New user registration pending approval: Dennis A. Fernandez (drrmlguestancia)', 1, 1, NULL, '2026-07-17 20:02:10'),
(23, 1, NULL, '🆕 New user registration pending approval: Rommel A. Fernandez (menrolguestancia)', 1, 1, NULL, '2026-07-17 20:03:59'),
(24, 13, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 0, 1, NULL, '2026-07-18 16:06:15'),
(25, 9, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 1, 1, NULL, '2026-07-18 16:06:20'),
(26, 12, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 0, 1, NULL, '2026-07-18 16:06:24'),
(27, 10, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 1, 1, NULL, '2026-07-18 16:06:28'),
(28, 1, NULL, '🆕 New user registration pending approval: Ployd Punsalan (mpdolguestancia)', 1, 1, NULL, '2026-07-18 17:40:39'),
(29, 14, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 0, 1, NULL, '2026-07-18 19:16:08'),
(45, 11, NULL, '✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.', 0, 1, NULL, '2026-07-23 13:54:23'),
(50, 9, 19, '📋 NEW REQUEST: Permit/License | Priority: 🟡 Medium (Request #: REQ-202607-0011) - 1 attachment(s)', 1, 0, 8, '2026-07-23 15:01:12'),
(51, 5, 19, '📋 NEW REQUEST: Permit/License | Priority: 🟡 Medium (Request #: REQ-202607-0011) - 1 attachment(s)', 1, 0, 8, '2026-07-23 15:01:12'),
(52, 1, 19, '📋 NEW MULTI-DEPARTMENT REQUEST: Permit/License | Priority: 🟡 Medium (Request #: REQ-202607-0011) - 1 attachment(s)', 1, 0, 8, '2026-07-23 15:01:12'),
(53, 8, 19, '📢 Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY updated status to \'rejected\'', 1, 0, 9, '2026-07-23 20:35:12'),
(54, 9, 19, '✅ You updated status to \'rejected\' for Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY', 1, 0, 9, '2026-07-23 20:35:12'),
(55, 8, 19, '✅ All departments have seen your request #REQ-202607-0011', 1, 0, 5, '2026-07-23 20:44:30'),
(56, 8, 19, '📢 Request #REQ-202607-0011 - Department MUNICIPAL TREASURY OFFICE updated status to \'in_progress\'', 1, 0, 5, '2026-07-23 20:44:53'),
(57, 5, 19, '✅ You updated status to \'in_progress\' for Request #REQ-202607-0011 - Department MUNICIPAL TREASURY OFFICE', 1, 0, 5, '2026-07-23 20:44:53'),
(159, 2, 27, '📋 NEW REQUEST: Supply/Equipment | Priority: 🟢 Low (Request #: REQ-202607-0014) - 1 attachment(s)', 1, 0, 9, '2026-07-24 17:17:08'),
(160, 1, 27, '📋 NEW MULTI-DEPARTMENT REQUEST: Supply/Equipment | Priority: 🟢 Low (Request #: REQ-202607-0014) - 1 attachment(s)', 0, 0, 9, '2026-07-24 17:17:08'),
(161, 9, 27, '📤 You sent a new request: Supply/Equipment (Request #: REQ-202607-0014) to 1 department(s) with 1 attachment(s)', 0, 0, 9, '2026-07-24 17:17:08'),
(162, 9, 27, '✅ All departments have seen your request #REQ-202607-0014', 0, 0, 2, '2026-07-24 17:18:00'),
(163, 9, 27, '📢 Request #REQ-202607-0014 - Department General Services Office updated status to \'approved\'', 0, 0, 2, '2026-07-24 17:18:09'),
(164, 1, 27, '📢 Request #REQ-202607-0014 - Department General Services Office updated status to \'approved\'', 0, 0, 2, '2026-07-24 17:18:09'),
(165, 2, 27, '✅ You updated status to \'approved\' for Request #REQ-202607-0014 - Department General Services Office', 0, 0, 2, '2026-07-24 17:18:09'),
(166, 9, 27, '📢 Request #REQ-202607-0014 - Department General Services Office updated status to \'in_progress\'', 0, 0, 2, '2026-07-24 17:18:20'),
(167, 1, 27, '📢 Request #REQ-202607-0014 - Department General Services Office updated status to \'in_progress\'', 0, 0, 2, '2026-07-24 17:18:20'),
(168, 2, 27, '✅ You updated status to \'in_progress\' for Request #REQ-202607-0014 - Department General Services Office', 0, 0, 2, '2026-07-24 17:18:20'),
(169, 9, 27, '📢 Request #REQ-202607-0014 - Department General Services Office updated status to \'completed\'', 0, 0, 2, '2026-07-24 17:18:28'),
(170, 1, 27, '📢 Request #REQ-202607-0014 - Department General Services Office updated status to \'completed\'', 0, 0, 2, '2026-07-24 17:18:28'),
(171, 2, 27, '✅ You updated status to \'completed\' for Request #REQ-202607-0014 - Department General Services Office', 0, 0, 2, '2026-07-24 17:18:28'),
(172, 9, 27, '🎉 Request #REQ-202607-0014 has been COMPLETED by General Services Office', 0, 0, 2, '2026-07-24 17:18:28'),
(173, 1, 27, '🎉 Request #REQ-202607-0014 has been COMPLETED by General Services Office', 0, 0, 2, '2026-07-24 17:18:28'),
(201, 2, 29, '📋 NEW REQUEST: Financial | Priority: 🟡 Medium (Request #: REQ-202607-0015) - 2 attachment(s)', 1, 0, 1, '2026-07-24 19:38:52'),
(202, 9, 29, '📋 NEW REQUEST: Financial | Priority: 🟡 Medium (Request #: REQ-202607-0015) - 2 attachment(s)', 0, 0, 1, '2026-07-24 19:38:52'),
(203, 1, 29, '📤 You sent a new request: Financial (Request #: REQ-202607-0015) to 2 department(s) with 2 attachment(s)', 0, 0, 1, '2026-07-24 19:38:52'),
(204, 1, 29, '✅ All departments have seen your request #REQ-202607-0015', 0, 0, 2, '2026-07-24 19:39:59'),
(205, 1, 29, '✅ All departments have seen your request #REQ-202607-0015', 0, 0, 9, '2026-07-24 19:40:50'),
(206, 1, 29, '📢 Request #REQ-202607-0015 - Department MUNICIPAL CIVIL REGISTRY updated status to \'approved\'', 0, 0, 9, '2026-07-24 19:42:25'),
(207, 9, 29, '✅ You updated status to \'approved\' for Request #REQ-202607-0015 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 9, '2026-07-24 19:42:25'),
(208, 1, 29, '📢 Request #REQ-202607-0015 - Department General Services Office updated status to \'approved\'', 0, 0, 2, '2026-07-24 19:44:23'),
(209, 2, 29, '✅ You updated status to \'approved\' for Request #REQ-202607-0015 - Department General Services Office', 0, 0, 2, '2026-07-24 19:44:23'),
(213, 9, 31, '📋 NEW REQUEST: Supply/Equipment | Priority: 🟡 Medium (Request #: REQ-202607-0017) - 2 attachment(s)', 1, 0, 2, '2026-07-24 20:14:07'),
(214, 5, 31, '📋 NEW REQUEST: Supply/Equipment | Priority: 🟡 Medium (Request #: REQ-202607-0017) - 2 attachment(s)', 0, 0, 2, '2026-07-24 20:14:07'),
(215, 1, 31, '📋 NEW MULTI-DEPARTMENT REQUEST: Supply/Equipment | Priority: 🟡 Medium (Request #: REQ-202607-0017) - 2 attachment(s)', 0, 0, 2, '2026-07-24 20:14:07'),
(216, 2, 31, '📤 You sent a new request: Supply/Equipment (Request #: REQ-202607-0017) to 2 department(s) with 2 attachment(s)', 0, 0, 2, '2026-07-24 20:14:08'),
(217, 2, 31, '✅ All departments have seen your request #REQ-202607-0017', 0, 0, 9, '2026-07-24 20:15:50'),
(218, 2, 31, '✅ All departments have seen your request #REQ-202607-0017', 0, 0, 5, '2026-07-24 20:17:33'),
(219, 2, 31, '📢 Request #REQ-202607-0017 - Department MUNICIPAL CIVIL REGISTRY updated status to \'approved\'', 0, 0, 9, '2026-07-24 20:20:28'),
(220, 1, 31, '📢 Request #REQ-202607-0017 - Department MUNICIPAL CIVIL REGISTRY updated status to \'approved\'', 0, 0, 9, '2026-07-24 20:20:28'),
(221, 9, 31, '✅ You updated status to \'approved\' for Request #REQ-202607-0017 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 9, '2026-07-24 20:20:28'),
(222, 2, 31, '📢 Request #REQ-202607-0017 - Department MUNICIPAL TREASURY OFFICE updated status to \'approved\'', 0, 0, 1, '2026-08-08 10:29:21'),
(223, 1, 31, '✅ You updated status to \'approved\' for Request #REQ-202607-0017 - Department MUNICIPAL TREASURY OFFICE', 0, 0, 1, '2026-08-08 10:29:21'),
(224, 2, 31, '📢 Request #REQ-202607-0017 - Department MUNICIPAL CIVIL REGISTRY updated status to \'in_progress\'', 0, 0, 1, '2026-08-08 10:29:29'),
(225, 1, 31, '✅ You updated status to \'in_progress\' for Request #REQ-202607-0017 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 1, '2026-08-08 10:29:29'),
(226, 2, 31, '📢 Request #REQ-202607-0017 - Department MUNICIPAL TREASURY OFFICE updated status to \'in_progress\'', 0, 0, 1, '2026-08-08 10:29:44'),
(227, 1, 31, '✅ You updated status to \'in_progress\' for Request #REQ-202607-0017 - Department MUNICIPAL TREASURY OFFICE', 0, 0, 1, '2026-08-08 10:29:44'),
(229, 1, 29, '📢 Request #REQ-202607-0015 - Department General Services Office updated status to \'in_progress\'', 0, 0, 1, '2026-08-08 11:02:29'),
(230, 1, 29, '✅ You updated status to \'in_progress\' for Request #REQ-202607-0015 - Department General Services Office', 0, 0, 1, '2026-08-08 11:02:29'),
(231, 2, 31, '📢 Request #REQ-202607-0017 - Department MUNICIPAL CIVIL REGISTRY updated status to \'completed\'', 0, 0, 1, '2026-08-08 11:03:26'),
(232, 1, 31, '✅ You updated status to \'completed\' for Request #REQ-202607-0017 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 1, '2026-08-08 11:03:26'),
(233, 2, 31, '🎉 Request #REQ-202607-0017 has been COMPLETED by MUNICIPAL CIVIL REGISTRY', 0, 0, 1, '2026-08-08 11:03:26'),
(234, 8, 19, '📢 Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY updated status to \'seen\'', 0, 0, 1, '2026-08-08 11:34:00'),
(235, 1, 19, '✅ You updated status to \'seen\' for Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 1, '2026-08-08 11:34:00'),
(236, 8, 19, '📢 Request #REQ-202607-0011 - Department MUNICIPAL TREASURY OFFICE updated status to \'seen\'', 0, 0, 1, '2026-08-08 11:34:07'),
(237, 1, 19, '✅ You updated status to \'seen\' for Request #REQ-202607-0011 - Department MUNICIPAL TREASURY OFFICE', 0, 0, 1, '2026-08-08 11:34:07'),
(238, 8, 19, '📢 Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY updated status to \'approved\'', 0, 0, 1, '2026-08-08 11:34:15'),
(239, 1, 19, '✅ You updated status to \'approved\' for Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 1, '2026-08-08 11:34:15'),
(240, 1, 29, '📢 Request #REQ-202607-0015 - Department MUNICIPAL CIVIL REGISTRY updated status to \'in_progress\'', 0, 0, 1, '2026-08-08 11:53:56'),
(241, 1, 29, '✅ You updated status to \'in_progress\' for Request #REQ-202607-0015 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 1, '2026-08-08 11:53:56'),
(242, 8, 19, '📢 Request #REQ-202607-0011 - Department MUNICIPAL TREASURY OFFICE updated status to \'approved\'', 0, 0, 1, '2026-08-08 12:42:01'),
(243, 1, 19, '✅ You updated status to \'approved\' for Request #REQ-202607-0011 - Department MUNICIPAL TREASURY OFFICE', 0, 0, 1, '2026-08-08 12:42:01'),
(244, 8, 19, '📢 Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY updated status to \'in_progress\'', 0, 0, 1, '2026-08-08 12:42:09'),
(245, 1, 19, '✅ You updated status to \'in_progress\' for Request #REQ-202607-0011 - Department MUNICIPAL CIVIL REGISTRY', 0, 0, 1, '2026-08-08 12:42:09'),
(246, 7, 45, '📋 NEW REQUEST: Training/Seminar | Priority: 🔴 High (Request #: REQ-202608-0001) - 2 attachment(s)', 1, 0, 2, '2026-08-08 13:09:34'),
(247, 6, 45, '📋 NEW REQUEST: Training/Seminar | Priority: 🔴 High (Request #: REQ-202608-0001) - 2 attachment(s)', 1, 0, 2, '2026-08-08 13:09:34'),
(248, 1, 45, '📋 NEW MULTI-DEPARTMENT REQUEST: Training/Seminar | Priority: 🔴 High (Request #: REQ-202608-0001) - 2 attachment(s)', 1, 0, 2, '2026-08-08 13:09:34'),
(249, 2, 45, '📤 You sent a new request: Training/Seminar (Request #: REQ-202608-0001) to 2 department(s) with 2 attachment(s)', 0, 0, 2, '2026-08-08 13:09:34'),
(250, 2, 45, '✅ All departments have seen your request #REQ-202608-0001', 0, 0, 7, '2026-08-08 13:11:54'),
(251, 2, 45, '✅ All departments have seen your request #REQ-202608-0001', 0, 0, 1, '2026-08-08 13:32:10'),
(252, 2, 45, '📢 Request #REQ-202608-0001 - Department Budget Office updated status to \'approved\'', 0, 0, 1, '2026-08-08 13:32:22'),
(253, 1, 45, '✅ You updated status to \'approved\' for Request #REQ-202608-0001 - Department Budget Office', 0, 0, 1, '2026-08-08 13:32:22'),
(254, 2, 45, '📢 Request #REQ-202608-0001 - Department Engineering Office updated status to \'approved\'', 0, 0, 6, '2026-08-08 13:33:28'),
(255, 1, 45, '📢 Request #REQ-202608-0001 - Department Engineering Office updated status to \'approved\'', 0, 0, 6, '2026-08-08 13:33:28'),
(256, 6, 45, '✅ You updated status to \'approved\' for Request #REQ-202608-0001 - Department Engineering Office', 0, 0, 6, '2026-08-08 13:33:28'),
(257, 2, 45, '📢 Request #REQ-202608-0001 - Department Engineering Office updated status to \'in_progress\'', 0, 0, 6, '2026-08-08 13:33:35'),
(258, 1, 45, '📢 Request #REQ-202608-0001 - Department Engineering Office updated status to \'in_progress\'', 0, 0, 6, '2026-08-08 13:33:35'),
(259, 6, 45, '✅ You updated status to \'in_progress\' for Request #REQ-202608-0001 - Department Engineering Office', 0, 0, 6, '2026-08-08 13:33:35'),
(260, 11, 50, '📋 NEW REQUEST: Supply/Equipment | Priority: 🔴 High (Request #: REQ-202608-0002) - 2 attachment(s)', 1, 0, 6, '2026-08-08 14:22:02'),
(261, 1, 50, '📋 NEW MULTI-DEPARTMENT REQUEST: Supply/Equipment | Priority: 🔴 High (Request #: REQ-202608-0002) - 2 attachment(s)', 0, 0, 6, '2026-08-08 14:22:02'),
(262, 6, 50, '📤 You sent a new request: Supply/Equipment (Request #: REQ-202608-0002) to 2 department(s) with 2 attachment(s)', 0, 0, 6, '2026-08-08 14:22:02'),
(263, 2, 31, '📢 Request #REQ-202607-0017 - Department Municipal Treasury Office updated status to \'completed\'', 0, 0, 1, '2026-08-08 14:32:42'),
(264, 1, 31, '✅ You updated status to \'completed\' for Request #REQ-202607-0017 - Department Municipal Treasury Office', 0, 0, 1, '2026-08-08 14:32:42'),
(265, 2, 31, '🎉 Request #REQ-202607-0017 has been COMPLETED by Municipal Treasury Office', 0, 0, 1, '2026-08-08 14:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `request_number` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `request_type` varchar(50) NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `from_dept` int(11) NOT NULL,
  `requested_by` int(11) NOT NULL,
  `status` enum('pending','seen','approved','rejected','in_progress','completed') DEFAULT 'pending',
  `attachment_path` varchar(500) DEFAULT NULL,
  `attachment_name` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deadline` date DEFAULT NULL,
  `additional_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `request_number`, `title`, `description`, `request_type`, `priority`, `from_dept`, `requested_by`, `status`, `attachment_path`, `attachment_name`, `created_at`, `updated_at`, `deadline`, `additional_notes`) VALUES
(19, 'REQ-202607-0011', 'Request #REQ-202607-0011', NULL, 'Permit/License', 'medium', 8, 8, 'in_progress', NULL, NULL, '2026-07-23 15:01:12', '2026-08-08 12:42:09', '2026-07-24', ''),
(27, 'REQ-202607-0014', 'Request #REQ-202607-0014', NULL, 'Supply/Equipment', 'low', 22, 9, 'completed', NULL, NULL, '2026-07-24 17:17:08', '2026-07-24 17:18:28', '2026-07-31', ''),
(29, 'REQ-202607-0015', 'Request #REQ-202607-0015', NULL, 'Financial', 'medium', 1, 1, 'in_progress', NULL, NULL, '2026-07-24 19:38:51', '2026-08-08 11:53:56', '2026-07-27', ''),
(31, 'REQ-202607-0017', 'Request #REQ-202607-0017', NULL, 'Supply/Equipment', 'medium', 11, 2, 'completed', NULL, NULL, '2026-07-24 20:14:07', '2026-08-08 14:32:42', '2026-07-30', ''),
(45, 'REQ-202608-0001', 'Request #REQ-202608-0001', NULL, 'Training/Seminar', 'high', 11, 2, 'approved', NULL, NULL, '2026-08-08 13:09:34', '2026-08-08 13:33:28', '2026-08-29', ''),
(50, 'REQ-202608-0002', 'Request #REQ-202608-0002', NULL, 'Supply/Equipment', 'high', 10, 6, 'pending', NULL, NULL, '2026-08-08 14:22:02', NULL, '2026-08-29', '');

-- --------------------------------------------------------

--
-- Table structure for table `request_attachments`
--

CREATE TABLE `request_attachments` (
  `attachment_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_attachments`
--

INSERT INTO `request_attachments` (`attachment_id`, `request_id`, `file_path`, `file_name`, `file_size`, `file_type`, `uploaded_at`, `uploaded_by`) VALUES
(6, 19, 'uploads/req_19_1784790072_6a61bc38609a2.pdf', 'CHAPTER-1.pdf', 12148366, 'application/pdf', '2026-07-23 15:01:12', 8),
(16, 27, 'uploads/req_27_1784884628_6a632d94eb7f3.png', 'login_bg.png', 100053, 'image/png', '2026-07-24 17:17:08', 9),
(18, 29, 'uploads/req_29_1784893131_6a634ecbefdd4.png', 'EST.png', 100053, 'image/png', '2026-07-24 19:38:51', 1),
(19, 29, 'uploads/req_29_1784893131_6a634ecbf13c0.jpg', '72f3d135-89e3-4c7e-8e42-4f4e32d4aae8.jpg', 133781, 'image/jpeg', '2026-07-24 19:38:51', 1),
(21, 31, 'uploads/req_31_1784895247_6a63570fe7d02.png', 'EST.png', 100053, 'image/png', '2026-07-24 20:14:07', 2),
(22, 31, 'uploads/req_31_1784895247_6a63570fe975d.jpg', 'b2780760-b188-4946-89cc-7e4a88e0da6f.jpg', 175849, 'image/jpeg', '2026-07-24 20:14:07', 2),
(23, 45, 'uploads/req_45_1786165774_6a76ba0e1c0cd.docx', 'BABYLONIAN-CIVILIZATIONEM (1).docx', 21948, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-08-08 13:09:34', 2),
(24, 45, 'uploads/req_45_1786165774_6a76ba0e1d5fe.docx', 'BABYLONIAN-CIVILIZATIONEM.docx', 21948, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-08-08 13:09:34', 2),
(25, 50, 'uploads/req_50_1786170122_6a76cb0ad5e11.docx', 'BABYLONIAN-CIVILIZATIONEM (1).docx', 21948, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-08-08 14:22:02', 6),
(26, 50, 'uploads/req_50_1786170122_6a76cb0ad74fd.docx', 'Updated-IM_Lesson-1_2023_24.docx', 784193, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-08-08 14:22:02', 6);

-- --------------------------------------------------------

--
-- Table structure for table `request_details`
--

CREATE TABLE `request_details` (
  `detail_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `action_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_details`
--

INSERT INTO `request_details` (`detail_id`, `request_id`, `action`, `action_by`, `notes`, `created_at`) VALUES
(17, 19, 'Request Created', 8, 'Sent to 2 departments with 1 attachment(s)', '2026-07-23 15:01:12'),
(18, 19, 'Recipient Status Updated', 9, 'Status: rejected. Notes: ', '2026-07-23 20:35:12'),
(19, 19, 'Recipient Status Updated', 5, 'Status: in_progress. Notes: ', '2026-07-23 20:44:53'),
(60, 27, 'Request Created', 9, 'Sent to 1 departments with 1 attachment(s)', '2026-07-24 17:17:08'),
(61, 27, 'Recipient Status Updated', 2, 'Status: approved. Notes: ', '2026-07-24 17:18:09'),
(62, 27, 'Recipient Status Updated', 2, 'Status: in_progress. Notes: ', '2026-07-24 17:18:20'),
(63, 27, 'Recipient Status Updated', 2, 'Status: completed. Notes: ', '2026-07-24 17:18:28'),
(72, 29, 'Request Created', 1, 'Sent to 2 departments with 2 attachment(s)', '2026-07-24 19:38:52'),
(73, 29, 'Recipient Status Updated', 9, 'Status: approved. Notes: ', '2026-07-24 19:42:25'),
(74, 29, 'Recipient Status Updated', 2, 'Status: approved. Notes: ', '2026-07-24 19:44:23'),
(76, 31, 'Request Created', 2, 'Sent to 2 departments with 2 attachment(s)', '2026-07-24 20:14:07'),
(77, 31, 'Recipient Status Updated', 9, 'Status: approved. Notes: ', '2026-07-24 20:20:28'),
(78, 31, 'Recipient Status Updated', 1, 'Status: approved. Notes: ', '2026-08-08 10:29:21'),
(79, 31, 'Recipient Status Updated', 1, 'Status: in_progress. Notes: ', '2026-08-08 10:29:29'),
(80, 31, 'Recipient Status Updated', 1, 'Status: in_progress. Notes: ', '2026-08-08 10:29:44'),
(81, 29, 'Recipient Status Updated', 1, 'Status: in_progress. Notes: ', '2026-08-08 11:02:29'),
(82, 31, 'Recipient Status Updated', 1, 'Status: completed. Notes: ', '2026-08-08 11:03:26'),
(83, 19, 'Recipient Status Updated', 1, 'Status: seen. Notes: ', '2026-08-08 11:34:00'),
(84, 19, 'Recipient Status Updated', 1, 'Status: seen. Notes: ', '2026-08-08 11:34:07'),
(85, 19, 'Recipient Status Updated', 1, 'Status: approved. Notes: ', '2026-08-08 11:34:15'),
(86, 29, 'Recipient Status Updated', 1, 'Status: in_progress. Notes: ', '2026-08-08 11:53:56'),
(87, 19, 'Recipient Status Updated', 1, 'Status: approved. Notes: ', '2026-08-08 12:42:01'),
(88, 19, 'Recipient Status Updated', 1, 'Status: in_progress. Notes: ', '2026-08-08 12:42:09'),
(89, 45, 'Request Created', 2, 'Sent to 2 departments with 2 attachment(s)', '2026-08-08 13:09:34'),
(90, 45, 'Recipient Status Updated', 1, 'Status: approved. Notes: ', '2026-08-08 13:32:22'),
(91, 45, 'Recipient Status Updated', 6, 'Status: approved. Notes: ', '2026-08-08 13:33:28'),
(92, 45, 'Recipient Status Updated', 6, 'Status: in_progress. Notes: ', '2026-08-08 13:33:35'),
(93, 50, 'Request Created', 6, 'Sent to 2 departments with 2 attachment(s)', '2026-08-08 14:22:02'),
(94, 31, 'Recipient Status Updated', 1, 'Status: completed. Notes: ', '2026-08-08 14:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `request_items`
--

CREATE TABLE `request_items` (
  `item_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `date_borrowed` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_recipients`
--

CREATE TABLE `request_recipients` (
  `recipient_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `status` enum('pending','seen','approved','rejected','completed','in_progress') DEFAULT 'pending',
  `seen_at` datetime DEFAULT NULL,
  `responded_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_recipients`
--

INSERT INTO `request_recipients` (`recipient_id`, `request_id`, `dept_id`, `status`, `seen_at`, `responded_at`, `notes`) VALUES
(30, 19, 22, 'in_progress', '2026-07-23 20:34:49', '2026-08-08 12:42:09', NULL),
(31, 19, 6, 'approved', '2026-07-23 20:44:30', '2026-08-08 12:42:01', NULL),
(42, 27, 11, 'completed', '2026-07-24 17:18:00', '2026-07-24 17:18:28', NULL),
(45, 29, 11, 'in_progress', '2026-07-24 19:39:59', '2026-08-08 11:02:29', NULL),
(46, 29, 22, 'in_progress', '2026-07-24 19:40:50', '2026-08-08 11:53:56', NULL),
(48, 31, 22, 'completed', '2026-07-24 20:15:50', '2026-08-08 11:03:26', NULL),
(49, 31, 6, 'completed', '2026-07-24 20:17:33', '2026-08-08 14:32:42', NULL),
(50, 45, 5, 'approved', '2026-08-08 13:11:54', '2026-08-08 13:32:22', NULL),
(51, 45, 10, 'in_progress', '2026-08-08 13:32:10', '2026-08-08 13:33:35', NULL),
(52, 50, 14, 'seen', '2026-08-08 14:25:31', NULL, NULL),
(53, 50, 3, 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `request_status_history`
--

CREATE TABLE `request_status_history` (
  `history_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_timeline`
--

CREATE TABLE `request_timeline` (
  `timeline_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `action_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_timeline`
--

INSERT INTO `request_timeline` (`timeline_id`, `request_id`, `action`, `action_by`, `notes`, `created_at`) VALUES
(32, 27, '🎉 Request Completed by Department', 2, 'Completed by General Services Office - ', '2026-07-24 17:18:28'),
(34, 31, '🎉 Request Completed by Department', 1, 'Completed by MUNICIPAL CIVIL REGISTRY - ', '2026-08-08 11:03:26'),
(46, 31, '🎉 Request Completed by Department', 1, 'Completed by Municipal Treasury Office - ', '2026-08-08 14:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `request_tracking`
--

CREATE TABLE `request_tracking` (
  `track_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `action_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `dept_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_tracking`
--

INSERT INTO `request_tracking` (`track_id`, `request_id`, `action`, `action_by`, `notes`, `created_at`, `dept_id`) VALUES
(59, 19, '📤 Request Sent', 8, 'Sent to MUNICIPAL CIVIL REGISTRY', '2026-07-23 15:01:12', 22),
(60, 19, '📤 Request Sent', 8, 'Sent to MUNICIPAL TREASURY OFFICE', '2026-07-23 15:01:12', 6),
(61, 19, '📝 Request Created', 8, 'Request created by Angelo A. Gallardo with 1 attachment(s)', '2026-07-23 15:01:12', 8),
(62, 19, '👀 Request Seen', 9, 'Viewed by: Collen V. Dela Paz - MUNICIPAL CIVIL REGISTRY', '2026-07-23 20:34:49', 22),
(63, 19, '❌ Status: rejected', 9, 'Updated by: Collen V. Dela Paz - ', '2026-07-23 20:35:12', 22),
(64, 19, '👀 Request Seen', 5, 'Viewed by: John Gerryll Grecia - MUNICIPAL TREASURY OFFICE', '2026-07-23 20:44:30', 6),
(65, 19, '👀 Seen by All Departments', 5, 'All departments have viewed this request', '2026-07-23 20:44:30', 6),
(66, 19, '📌 Status: in_progress', 5, 'Updated by: John Gerryll Grecia - ', '2026-07-23 20:44:53', 6),
(151, 27, '📤 Request Sent', 9, 'Sent to General Services Office', '2026-07-24 17:17:08', 11),
(152, 27, '📝 Request Created', 9, 'Request created by Collen V. Dela Paz with 1 attachment(s)', '2026-07-24 17:17:08', 22),
(153, 27, '👀 Request Seen', 2, 'Viewed by: Josh Andrie - General Services Office', '2026-07-24 17:18:00', 11),
(154, 27, '📊 Overall Status: seen', 2, 'System updated overall status', '2026-07-24 17:18:00', 11),
(155, 27, '✅ Status: approved', 2, 'Updated by: Josh Andrie - ', '2026-07-24 17:18:09', 11),
(156, 27, '📊 Overall Status: approved', 2, 'System updated overall status', '2026-07-24 17:18:09', 11),
(157, 27, '🔄 Status: in_progress', 2, 'Updated by: Josh Andrie - ', '2026-07-24 17:18:20', 11),
(158, 27, '📊 Overall Status: pending', 2, 'System updated overall status', '2026-07-24 17:18:20', 11),
(159, 27, '🎉 Status: completed', 2, 'Updated by: Josh Andrie - ', '2026-07-24 17:18:28', 11),
(160, 27, '📊 Overall Status: completed', 2, 'System updated overall status', '2026-07-24 17:18:28', 11),
(182, 29, '📤 Request Sent', 1, 'Sent to General Services Office', '2026-07-24 19:38:52', 11),
(183, 29, '📤 Request Sent', 1, 'Sent to MUNICIPAL CIVIL REGISTRY', '2026-07-24 19:38:52', 22),
(184, 29, '📝 Request Created', 1, 'Request created by System Administrator with 2 attachment(s)', '2026-07-24 19:38:52', 1),
(185, 29, '👀 Request Seen', 2, 'Viewed by: Josh Andrie - General Services Office', '2026-07-24 19:39:59', 11),
(186, 29, '📊 Overall Status: seen', 2, 'System updated overall status', '2026-07-24 19:39:59', 11),
(187, 29, '👀 Request Seen', 9, 'Viewed by: Collen V. Dela Paz - MUNICIPAL CIVIL REGISTRY', '2026-07-24 19:40:50', 22),
(188, 29, '📊 Overall Status: seen', 9, 'System updated overall status', '2026-07-24 19:40:50', 22),
(189, 29, '✅ Status: approved', 9, 'Updated by: Collen V. Dela Paz - ', '2026-07-24 19:42:25', 22),
(190, 29, '📊 Overall Status: seen', 9, 'System updated overall status', '2026-07-24 19:42:25', 22),
(191, 29, '✅ Status: approved', 2, 'Updated by: Josh Andrie - ', '2026-07-24 19:44:23', 11),
(192, 29, '📊 Overall Status: approved', 2, 'System updated overall status', '2026-07-24 19:44:23', 11),
(195, 31, '📤 Request Sent', 2, 'Sent to MUNICIPAL CIVIL REGISTRY', '2026-07-24 20:14:07', 22),
(196, 31, '📤 Request Sent', 2, 'Sent to MUNICIPAL TREASURY OFFICE', '2026-07-24 20:14:07', 6),
(197, 31, '📝 Request Created', 2, 'Request created by Josh Andrie with 2 attachment(s)', '2026-07-24 20:14:07', 11),
(198, 31, '👀 Request Seen', 9, 'Viewed by: Collen V. Dela Paz - MUNICIPAL CIVIL REGISTRY', '2026-07-24 20:15:50', 22),
(199, 31, '📊 Overall Status: seen', 9, 'System updated overall status', '2026-07-24 20:15:50', 22),
(200, 31, '👀 Request Seen', 5, 'Viewed by: John Gerryll Grecia - MUNICIPAL TREASURY OFFICE', '2026-07-24 20:17:33', 6),
(201, 31, '📊 Overall Status: seen', 5, 'System updated overall status', '2026-07-24 20:17:33', 6),
(202, 31, '✅ Status: approved', 9, 'Updated by: Collen V. Dela Paz - ', '2026-07-24 20:20:28', 22),
(203, 31, '📊 Overall Status: seen', 9, 'System updated overall status', '2026-07-24 20:20:28', 22),
(204, 31, '✅ Status: approved', 1, 'Updated by: System Administrator - ', '2026-08-08 10:29:21', 1),
(205, 31, '📊 Overall Status: approved', 1, 'System updated overall status', '2026-08-08 10:29:21', 1),
(206, 31, '🔄 Status: in_progress', 1, 'Updated by: System Administrator - ', '2026-08-08 10:29:29', 1),
(207, 31, '📊 Overall Status: in_progress', 1, 'System updated overall status', '2026-08-08 10:29:29', 1),
(208, 31, '🔄 Status: in_progress', 1, 'Updated by: System Administrator - ', '2026-08-08 10:29:44', 1),
(209, 31, '📊 Overall Status: in_progress', 1, 'System updated overall status', '2026-08-08 10:29:44', 1),
(212, 29, '🔄 Status: in_progress', 1, 'Updated by: System Administrator - ', '2026-08-08 11:02:29', 1),
(213, 29, '📊 Overall Status: pending', 1, 'System updated overall status', '2026-08-08 11:02:29', 1),
(214, 31, '🎉 Status: completed', 1, 'Updated by: System Administrator - ', '2026-08-08 11:03:26', 1),
(215, 31, '📊 Overall Status: in_progress', 1, 'System updated overall status', '2026-08-08 11:03:26', 1),
(216, 19, '👀 Status: seen', 1, 'Updated by: System Administrator - ', '2026-08-08 11:34:00', 1),
(217, 19, '📊 Overall Status: pending', 1, 'System updated overall status', '2026-08-08 11:34:00', 1),
(218, 19, '👀 Status: seen', 1, 'Updated by: System Administrator - ', '2026-08-08 11:34:07', 1),
(219, 19, '📊 Overall Status: seen', 1, 'System updated overall status', '2026-08-08 11:34:07', 1),
(220, 19, '✅ Status: approved', 1, 'Updated by: System Administrator - ', '2026-08-08 11:34:15', 1),
(221, 19, '📊 Overall Status: approved', 1, 'System updated overall status', '2026-08-08 11:34:15', 1),
(222, 29, '🔄 Status: in_progress', 1, 'Updated by: System Administrator - ', '2026-08-08 11:53:56', 1),
(223, 29, '📊 Overall Status: in_progress', 1, 'System updated overall status', '2026-08-08 11:53:56', 1),
(224, 19, '✅ Status: approved', 1, 'Updated by: System Administrator - ', '2026-08-08 12:42:01', 1),
(225, 19, '📊 Overall Status: approved', 1, 'System updated overall status', '2026-08-08 12:42:01', 1),
(226, 19, '🔄 Status: in_progress', 1, 'Updated by: System Administrator - ', '2026-08-08 12:42:09', 1),
(227, 19, '📊 Overall Status: in_progress', 1, 'System updated overall status', '2026-08-08 12:42:09', 1),
(228, 45, '📤 Request Sent', 2, 'Sent to Budget Office', '2026-08-08 13:09:34', 5),
(229, 45, '📤 Request Sent', 2, 'Sent to Engineering Office', '2026-08-08 13:09:34', 10),
(230, 45, '📝 Request Created', 2, 'Request created by Josh Andrie with 2 attachment(s)', '2026-08-08 13:09:34', 11),
(231, 45, '👀 Request Seen', 7, 'Viewed by: Jacques Geomel  - Budget Office', '2026-08-08 13:11:54', 5),
(232, 45, '📊 Overall Status: seen', 7, 'System updated overall status', '2026-08-08 13:11:54', 5),
(233, 45, '👀 Request Seen', 1, 'Viewed by: System Administrator - Office of the Mayor', '2026-08-08 13:32:10', 1),
(234, 45, '📊 Overall Status: seen', 1, 'System updated overall status', '2026-08-08 13:32:10', 1),
(235, 45, '✅ Status: approved', 1, 'Updated by: System Administrator - ', '2026-08-08 13:32:22', 1),
(236, 45, '📊 Overall Status: seen', 1, 'System updated overall status', '2026-08-08 13:32:22', 1),
(237, 45, '✅ Status: approved', 6, 'Updated by: Arlie G. Dumali - ', '2026-08-08 13:33:28', 10),
(238, 45, '📊 Overall Status: approved', 6, 'System updated overall status', '2026-08-08 13:33:28', 10),
(239, 45, '🔄 Status: in_progress', 6, 'Updated by: Arlie G. Dumali - ', '2026-08-08 13:33:35', 10),
(240, 45, '📊 Overall Status: approved', 6, 'System updated overall status', '2026-08-08 13:33:35', 10),
(241, 50, '📤 Request Sent', 6, 'Sent to PUBLIC EMPLOYMENT SERVICE OFFICE', '2026-08-08 14:22:02', 14),
(242, 50, '📤 Request Sent', 6, 'Sent to Sangguniang Bayan', '2026-08-08 14:22:02', 3),
(243, 50, '📝 Request Created', 6, 'Request created by Arlie G. Dumali with 2 attachment(s)', '2026-08-08 14:22:02', 10),
(244, 50, '👀 Request Seen', 11, 'Viewed by: Bernadette S. Cruz - PUBLIC EMPLOYMENT SERVICE OFFICE', '2026-08-08 14:25:31', 14),
(245, 50, '📊 Overall Status: pending', 11, 'System updated overall status', '2026-08-08 14:25:31', 14),
(246, 31, '🎉 Status: completed', 1, 'Updated by: System Administrator - ', '2026-08-08 14:32:42', 1),
(247, 31, '📊 Overall Status: completed', 1, 'System updated overall status', '2026-08-08 14:32:42', 1);

-- --------------------------------------------------------

--
-- Table structure for table `request_types`
--

CREATE TABLE `request_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_types`
--

INSERT INTO `request_types` (`type_id`, `type_name`, `created_at`) VALUES
(1, 'Supply/Equipment', '2026-07-18 16:56:09'),
(2, 'Document', '2026-07-18 16:56:09'),
(3, 'Repair/Maintenance', '2026-07-18 16:56:09'),
(4, 'Vehicle', '2026-07-18 16:56:09'),
(5, 'Manpower', '2026-07-18 16:56:09'),
(6, 'Financial', '2026-07-18 16:56:09'),
(7, 'IT/Computer', '2026-07-18 16:56:09'),
(8, 'Permit/License', '2026-07-18 16:56:09'),
(9, 'Training/Seminar', '2026-07-18 16:56:09'),
(10, 'Other', '2026-07-18 16:56:09'),
(11, 'travel', '2026-07-23 11:34:53');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'system_title', 'LGU ESTANCIA ', '2026-08-08 11:44:29'),
(2, 'system_logo', 'image/EST.png', '2026-07-24 16:43:59'),
(3, 'system_theme', 'teal', '2026-08-08 11:44:29'),
(4, 'login_bg', 'image/login_bg.jpg', '2026-08-08 11:44:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `role` enum('admin','department_head','staff','viewer') DEFAULT 'staff',
  `status` enum('active','inactive','pending') DEFAULT 'pending',
  `theme_preference` varchar(50) DEFAULT 'dark',
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `dept_id`, `role`, `status`, `theme_preference`, `created_at`, `last_login`) VALUES
(1, 'admin', 'Admin#123', 'System Administrator', 'admin@lgu.gov.ph', 1, 'admin', 'active', 'dark', '2026-07-07 09:21:36', '2026-08-16 10:18:21'),
(2, 'gsolguestancia', 'gso#123', 'Josh Andrie', 'josh@lgu.gov.ph', 11, 'staff', 'active', 'green', '2026-07-07 09:21:36', '2026-08-08 13:54:51'),
(3, 'healthlguestancia', 'health#123', 'Maria Santos', 'maria@lgu.gov.ph', 13, 'department_head', 'active', 'dark', '2026-07-07 09:21:36', NULL),
(4, 'hrmolguestancia', 'hrmo#123', 'Pedro Reyes', 'pedro@lgu.gov.ph', 4, 'staff', 'active', 'purple', '2026-07-07 09:21:36', '2026-07-24 13:57:05'),
(5, 'mtolguestancia', 'mto#123', 'John Gerryll Grecia', 'john@gmail.com', 6, 'staff', 'active', 'dark', '2026-07-10 09:50:10', '2026-07-24 20:17:25'),
(6, 'engrlguestancia', 'eng#123', 'Arlie G. Dumali', 'arlieg@gmail.com', 10, 'staff', 'active', 'pink', '2026-07-17 12:27:02', '2026-08-08 14:20:18'),
(7, 'budjetlguestancia', 'budjet#123', 'Jacques Geomel ', 'jacquesgeomel@gmail.com', 5, 'staff', 'active', 'pink', '2026-07-17 12:41:35', '2026-08-08 13:46:06'),
(8, 'acclguestancia', 'acc#123', 'Angelo A. Gallardo', 'Angelo@gmail.com', 8, 'staff', 'active', 'blue', '2026-07-17 12:51:51', '2026-07-24 10:00:54'),
(9, 'mcrlguestancia', 'mcr#123', 'Collen V. Dela Paz', 'collen@gmail.com', 22, 'staff', 'active', 'green', '2026-07-17 19:54:43', '2026-08-08 14:27:31'),
(10, 'assesslguestancia', 'assess#123', 'Rosalinda M. Cruz', 'rosalinda@gmail.com', 7, 'staff', 'active', 'dark', '2026-07-17 19:57:02', '2026-07-20 08:14:11'),
(11, 'pesolguestancia', 'peso#123', 'Bernadette S. Cruz', 'bernadette@gmail.com', 14, 'staff', 'active', 'orange', '2026-07-17 19:58:42', '2026-08-08 14:24:46'),
(12, 'drrmlguestancia', 'drrm#123', 'Dennis A. Fernandez', 'dennis@gmail.com', 16, 'staff', 'active', 'orange', '2026-07-17 20:02:10', '2026-08-08 10:28:19'),
(13, 'menrolguestancia', 'menro#123', 'Rommel A. Fernandez', 'Romel@gmail.com', 9, 'staff', 'active', 'dark', '2026-07-17 20:03:59', NULL),
(14, 'mpdolguestancia', 'mpdo#123', 'Ployd Punsalan', 'ployd@gmail.com', 21, 'staff', 'active', 'dark', '2026-07-18 17:40:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `pref_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `theme_name` varchar(50) DEFAULT 'blue_pink',
  `notifications_push` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`pref_id`, `user_id`, `theme_name`, `notifications_push`, `created_at`, `updated_at`) VALUES
(1, 1, 'green', 1, '2026-07-10 10:54:49', '2026-07-20 09:12:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `deleted_requests`
--
ALTER TABLE `deleted_requests`
  ADD PRIMARY KEY (`delete_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dept_id`),
  ADD UNIQUE KEY `dept_code` (`dept_code`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `notifications_ibfk_1` (`user_id`),
  ADD KEY `notifications_ibfk_2` (`request_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD UNIQUE KEY `request_number` (`request_number`),
  ADD KEY `from_dept` (`from_dept`),
  ADD KEY `requested_by` (`requested_by`);

--
-- Indexes for table `request_attachments`
--
ALTER TABLE `request_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `request_details`
--
ALTER TABLE `request_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `request_details_ibfk_2` (`action_by`),
  ADD KEY `request_details_ibfk_1` (`request_id`);

--
-- Indexes for table `request_items`
--
ALTER TABLE `request_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `request_recipients`
--
ALTER TABLE `request_recipients`
  ADD PRIMARY KEY (`recipient_id`),
  ADD KEY `request_recipients_ibfk_2` (`dept_id`),
  ADD KEY `request_recipients_ibfk_1` (`request_id`);

--
-- Indexes for table `request_status_history`
--
ALTER TABLE `request_status_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `request_timeline`
--
ALTER TABLE `request_timeline`
  ADD PRIMARY KEY (`timeline_id`),
  ADD KEY `request_timeline_ibfk_2` (`action_by`),
  ADD KEY `request_timeline_ibfk_1` (`request_id`);

--
-- Indexes for table `request_tracking`
--
ALTER TABLE `request_tracking`
  ADD PRIMARY KEY (`track_id`),
  ADD KEY `action_by` (`action_by`),
  ADD KEY `request_tracking_ibfk_2` (`dept_id`),
  ADD KEY `request_tracking_ibfk_1` (`request_id`);

--
-- Indexes for table `request_types`
--
ALTER TABLE `request_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`pref_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deleted_requests`
--
ALTER TABLE `deleted_requests`
  MODIFY `delete_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=266;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `request_attachments`
--
ALTER TABLE `request_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `request_details`
--
ALTER TABLE `request_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `request_items`
--
ALTER TABLE `request_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `request_recipients`
--
ALTER TABLE `request_recipients`
  MODIFY `recipient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `request_status_history`
--
ALTER TABLE `request_status_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `request_timeline`
--
ALTER TABLE `request_timeline`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `request_tracking`
--
ALTER TABLE `request_tracking`
  MODIFY `track_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `request_types`
--
ALTER TABLE `request_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `pref_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`from_dept`) REFERENCES `departments` (`dept_id`),
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `request_attachments`
--
ALTER TABLE `request_attachments`
  ADD CONSTRAINT `request_attachments_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_attachments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `request_details`
--
ALTER TABLE `request_details`
  ADD CONSTRAINT `request_details_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_details_ibfk_2` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `request_items`
--
ALTER TABLE `request_items`
  ADD CONSTRAINT `request_items_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE;

--
-- Constraints for table `request_recipients`
--
ALTER TABLE `request_recipients`
  ADD CONSTRAINT `request_recipients_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_recipients_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) ON DELETE CASCADE;

--
-- Constraints for table `request_status_history`
--
ALTER TABLE `request_status_history`
  ADD CONSTRAINT `request_status_history_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_status_history_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_status_history_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) ON DELETE CASCADE;

--
-- Constraints for table `request_timeline`
--
ALTER TABLE `request_timeline`
  ADD CONSTRAINT `request_timeline_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_timeline_ibfk_2` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `request_tracking`
--
ALTER TABLE `request_tracking`
  ADD CONSTRAINT `request_tracking_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_tracking_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_tracking_ibfk_4` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
