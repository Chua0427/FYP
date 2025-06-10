-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 03:41 PM
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
-- Database: `verosports`
--
CREATE DATABASE verosports;
USE verosports;
-- --------------------------------------------------------

--
-- Table structure for table `billboard`
--

CREATE TABLE `billboard` (
  `billboard_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billboard`
--

INSERT INTO `billboard` (`billboard_id`, `image`, `created_at`) VALUES
(8, '1743578374.png', '2025-04-02 07:19:34'),
(9, '1743578665.png', '2025-04-02 07:24:25'),
(25, '1749027472.jpg', '2025-06-04 08:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_size` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contactUs_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `contact_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`contactUs_id`, `user_id`, `email`, `message`, `contact_at`) VALUES
(16, 51, '', 'Hi May i ask some question?', '2025-06-05 01:38:32'),
(17, 51, '', 'Hi May i ask some question about the shoes size?', '2025-06-05 01:39:04'),
(18, 51, '', 'How can i tracking my order?', '2025-06-05 01:39:32');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `login_attempts_id` int(11) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_successful` tinyint(1) DEFAULT 0,
  `lockout_until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`login_attempts_id`, `admin_email`, `ip_address`, `user_agent`, `attempt_time`, `is_successful`, `lockout_until`) VALUES
(1, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-03 13:53:50', 1, NULL),
(2, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-03 15:31:10', 1, NULL),
(3, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-03 15:31:21', 1, NULL),
(4, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-03 15:33:49', 1, NULL),
(5, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-03 15:34:09', 1, NULL),
(6, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 03:19:46', 1, NULL),
(7, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 07:36:53', 1, NULL),
(8, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:33:54', 1, NULL),
(9, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:42:55', 1, NULL),
(10, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:43:58', 1, NULL),
(11, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:55:53', 0, NULL),
(12, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:56:17', 1, NULL),
(13, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:56:43', 1, NULL),
(14, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:58:33', 1, NULL),
(15, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:58:53', 1, NULL),
(16, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 08:59:35', 1, NULL),
(17, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:00:33', 1, NULL),
(18, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:00:53', 1, NULL),
(19, 'cianchua8@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:01:45', 0, NULL),
(20, 'caichian8@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:01:59', 0, NULL),
(21, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:02:08', 1, NULL),
(22, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:10:44', 1, NULL),
(23, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:11:25', 1, NULL),
(24, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:17:21', 1, NULL),
(25, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:19:03', 1, NULL),
(26, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-04 09:20:03', 1, NULL),
(27, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-05 01:12:58', 1, NULL),
(28, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-05 01:33:25', 0, NULL),
(29, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-05 01:33:35', 1, NULL),
(30, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-06-05 19:10:24', 1, NULL),
(31, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-06-05 20:15:21', 1, NULL),
(32, 'chiannchua05@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-06-06 07:32:11', 1, NULL),
(33, 'superadmin@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-06-06 08:25:02', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `delivery_status` enum('prepare','packing','assign','shipping','delivered') DEFAULT 'prepare',
  `order_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `shipping_address`, `delivery_status`, `order_at`) VALUES
(150, 54, 542.30, 'No. 36, Jalan Maya, Taman Miya, Segamat, 85000, Johor', 'delivered', '2025-05-31 05:24:36'),
(151, 54, 546.00, 'No. 36, Jalan Maya, Taman Miya, Segamat, 85000, Johor', 'shipping', '2025-05-31 15:35:25'),
(152, 54, 960.30, 'No. 36, Jalan Maya, Taman Miya, Segamat, 85000, Johor', 'shipping', '2025-06-01 00:56:44'),
(153, 66, 448.30, 'N0.6, Jalan Jaya, Taman Miri\r\n', 'packing', '2025-06-02 04:10:39'),
(154, 64, 711.00, 'No,21 Jalan Eco Cascadia 5/8, Taman Setia Eco Cascadia, Segamat, 85000, Johor', 'assign', '2025-06-01 18:58:33'),
(155, 64, 626.00, 'No,21 Jalan Eco Cascadia 5/8, Taman Setia Eco Cascadia, Segamat, 85000, Johor', 'prepare', '2025-06-02 09:39:19'),
(156, 66, 656.30, 'No.6, Jalan Jaya, 85647, Sarawak', 'prepare', '2025-06-03 05:04:11'),
(157, 67, 465.30, 'N0.22,Taman Aurora Pelangi, Jalan D3 3,, Johor Bahru, 81100, Johor', 'prepare', '2025-06-03 10:47:26'),
(158, 67, 316.10, 'N0.22,Taman Aurora Pelangi, Jalan D3 3,, Johor Bahru, 81100, Johor', 'prepare', '2025-06-03 19:31:04'),
(159, 68, 394.00, 'No,13 Jalan Titiwangsa 2, Taman Tampoi Indah, Segamat, 85000, Johor', 'prepare', '2025-06-04 13:23:35'),
(160, 68, 394.00, 'No,13 Jalan Titiwangsa 2, Taman Tampoi Indah, Segamat, 85000, Johor', 'prepare', '2025-06-05 00:55:02'),
(161, 51, 552.00, 'No.26,Jalan Oren 3, Taman Sri Pelangi', 'shipping', '2025-06-05 01:11:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_size` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `product_size`, `quantity`, `price`) VALUES
(203, 150, 110, 'XXS', 1, 99.00),
(204, 150, 102, 'XXS', 1, 79.00),
(205, 150, 183, 'One Size', 1, 129.00),
(206, 150, 84, 'UK13.5C', 1, 87.00),
(207, 150, 112, 'One Size', 1, 48.30),
(208, 150, 5, 'UK6', 1, 100.00),
(209, 151, 89, 'UK13.5C', 1, 169.00),
(210, 151, 183, 'One Size', 1, 129.00),
(211, 151, 78, 'UK13.5C', 1, 179.00),
(212, 151, 104, 'XXS', 1, 69.00),
(213, 152, 7, 'UK1.5Y', 1, 180.00),
(214, 152, 50, 'M', 1, 59.00),
(215, 152, 132, 'One Size', 1, 232.00),
(216, 152, 167, 'UK13.5C', 1, 489.30),
(217, 153, 7, 'UK1.5Y', 1, 180.00),
(218, 153, 175, 'XXS', 1, 209.30),
(219, 153, 188, 'One Size', 1, 59.00),
(220, 154, 164, 'UK13.5C', 1, 255.00),
(221, 154, 57, 'One Size', 1, 119.00),
(222, 154, 183, 'One Size', 1, 129.00),
(223, 154, 156, 'One Size', 1, 139.00),
(224, 154, 119, 'One Size', 1, 69.00),
(225, 155, 94, 'XXS', 1, 129.00),
(226, 155, 181, 'One Size', 1, 149.00),
(227, 155, 57, 'One Size', 1, 119.00),
(228, 155, 166, 'UK13.5C', 1, 229.00),
(229, 156, 27, 'UK13.5C', 1, 419.30),
(230, 156, 57, 'One Size', 1, 119.00),
(231, 156, 55, 'One Size', 1, 69.00),
(232, 156, 113, 'One Size', 1, 49.00),
(233, 157, 6, 'UK10', 1, 120.00),
(234, 157, 55, 'One Size', 1, 69.00),
(235, 157, 188, 'One Size', 1, 59.00),
(236, 157, 135, 'UK13.5C', 1, 155.00),
(237, 157, 105, 'XXS', 1, 62.30),
(238, 158, 154, 'One Size', 1, 97.30),
(239, 158, 107, 'XXS', 1, 55.30),
(240, 158, 87, 'UK13.5C', 1, 101.40),
(241, 158, 68, 'S', 1, 62.10),
(242, 159, 19, 'UK10.5', 1, 150.00),
(243, 159, 78, 'UK13.5C', 1, 179.00),
(244, 159, 98, 'XXS', 1, 65.00),
(245, 160, 41, 'One Size', 1, 45.00),
(246, 160, 43, 'UK3.5Y', 1, 200.00),
(247, 160, 48, 'M', 1, 149.00),
(248, 161, 18, 'XXS', 1, 100.00),
(249, 161, 20, 'UK5', 1, 120.00),
(250, 161, 39, 'One Size', 1, 145.00),
(251, 161, 119, 'One Size', 2, 69.00),
(252, 161, 114, 'One Size', 1, 49.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `expiry_time` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `otp`, `ip`, `expiry_time`, `used`, `created_at`) VALUES
(6, 'kaiwen211105@gmail.com', '850443', '::1', '2025-05-27 06:55:12', 1, '2025-05-27 04:40:12'),
(8, 'sohxj0802@gmail.com', '301613', '::1', '2025-05-29 08:21:18', 1, '2025-05-29 06:06:18'),
(9, 'caichian8@gmail.com', '696344', '::1', '2025-06-03 11:09:57', 1, '2025-06-03 08:54:57'),
(10, 'caichian8@gmail.com', '157818', '::1', '2025-06-04 10:26:55', 1, '2025-06-04 08:11:55'),
(11, 'sohxj0802@gmail.com', '199294', '::1', '2025-06-08 12:16:23', 1, '2025-06-08 10:01:23'),
(12, 'sohxj0802@gmail.com', '577951', '::1', '2025-06-08 12:23:20', 1, '2025-06-08 10:08:20'),
(13, 'sohxj0802@gmail.com', '498646', '::1', '2025-06-09 13:00:52', 0, '2025-06-09 10:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` varchar(36) NOT NULL,
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL CHECK (`total_amount` > 0),
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'MYR',
  `payment_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stripe_created` int(11) DEFAULT NULL COMMENT 'Stripe时间戳',
  `last_error` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `total_amount`, `payment_status`, `payment_method`, `stripe_id`, `currency`, `payment_at`, `stripe_created`, `last_error`) VALUES
('pay_68404b6cc969b6.6387780481653aeca', 150, 542.30, 'completed', 'stripe_card', 'ch_3RWHSgQZPLk7FzRY5XHgDvgu', 'MYR', '2025-05-31 05:24:36', NULL, NULL),
('pay_68404b9d9174c0.6039974106e7c0cd9', 151, 546.00, 'completed', 'stripe_card', 'ch_3RWHTTQZPLk7FzRY27ObTqls', 'MYR', '2025-05-31 15:36:25', NULL, NULL),
('pay_6840509c5d3970.811708948572f3493', 152, 960.30, 'completed', 'stripe_card', 'ch_3RWHo6QZPLk7FzRY1NPvbvyo', 'MYR', '2025-06-01 00:57:44', NULL, NULL),
('pay_684053dfcf9934.82293447e3f4461e2', 153, 448.30, 'completed', 'stripe_card', 'ch_3RWI1ZQZPLk7FzRY06UgMCUm', 'MYR', '2025-06-02 04:11:39', NULL, NULL),
('pay_684097596799f3.7936024076b621f4f', 154, 711.00, 'completed', 'stripe_card', 'ch_3RWMW4QZPLk7FzRY3eJh3ADj', 'MYR', '2025-06-01 18:59:33', NULL, NULL),
('pay_684097876a5715.461385027acf6fc74', 155, 626.00, 'completed', 'stripe_card', 'ch_3RWMWvQZPLk7FzRY4QhDXLrH', 'MYR', '2025-06-02 09:39:30', NULL, NULL),
('pay_684098ab4045d9.89554213af0151c9d', 156, 656.30, 'completed', 'stripe_card', 'ch_3RWMbdQZPLk7FzRY3rQFlubr', 'MYR', '2025-06-03 05:05:11', NULL, NULL),
('pay_6840996edda899.17805678ef64d6cf6', 157, 465.30, 'completed', 'stripe_card', 'ch_3RWMenQZPLk7FzRY1iT23KiZ', 'MYR', '2025-06-03 10:47:55', NULL, NULL),
('pay_68409ef8990079.033947330ab290cd4', 158, 316.10, 'completed', 'stripe_card', 'ch_3RWN1eQZPLk7FzRY1MbN4AF4', 'MYR', '2025-06-03 19:32:04', NULL, NULL),
('pay_6840a43f092877.23282661b571549ac', 159, 394.00, 'completed', 'stripe_card', 'ch_3RWNNRQZPLk7FzRY5s9XDgKU', 'MYR', '2025-06-04 13:25:35', NULL, NULL),
('pay_6840a45ae1b800.9168347221506a6ad', 160, 394.00, 'completed', 'stripe_card', 'ch_3RWNNtQZPLk7FzRY3NzftQkn', 'MYR', '2025-06-04 19:54:02', NULL, NULL),
('pay_6840eece0d4439.96826153f7c455bee', 161, 552.00, 'completed', 'stripe_card', 'ch_3RWSLKQZPLk7FzRY2vJau2SL', 'MYR', '2025-06-05 01:12:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_log`
--

CREATE TABLE `payment_log` (
  `log_id` int(11) NOT NULL,
  `payment_id` varchar(36) NOT NULL,
  `log_level` enum('info','warning','error') NOT NULL DEFAULT 'info',
  `log_message` text NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_log`
--

INSERT INTO `payment_log` (`log_id`, `payment_id`, `log_level`, `log_message`, `log_time`) VALUES
(217, 'pay_68404b6cc969b6.6387780481653aeca', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWHSgQZPLk7FzRY5XHgDvgu', '2025-06-04 13:34:36'),
(218, 'pay_68404b6cc969b6.6387780481653aeca', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 13:34:37'),
(219, 'pay_68404b6cc969b6.6387780481653aeca', 'info', 'Stock updated successfully after payment verification', '2025-06-04 13:34:37'),
(220, 'pay_68404b9d9174c0.6039974106e7c0cd9', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWHTTQZPLk7FzRY27ObTqls', '2025-06-04 13:35:25'),
(221, 'pay_68404b9d9174c0.6039974106e7c0cd9', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 13:35:25'),
(222, 'pay_68404b9d9174c0.6039974106e7c0cd9', 'info', 'Stock updated successfully after payment verification', '2025-06-04 13:35:25'),
(223, 'pay_6840509c5d3970.811708948572f3493', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWHo6QZPLk7FzRY1NPvbvyo', '2025-06-04 13:56:44'),
(224, 'pay_6840509c5d3970.811708948572f3493', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 13:56:44'),
(225, 'pay_6840509c5d3970.811708948572f3493', 'info', 'Stock updated successfully after payment verification', '2025-06-04 13:56:44'),
(226, 'pay_684053dfcf9934.82293447e3f4461e2', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWI1ZQZPLk7FzRY06UgMCUm', '2025-06-04 14:10:39'),
(227, 'pay_684053dfcf9934.82293447e3f4461e2', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 14:10:40'),
(228, 'pay_684053dfcf9934.82293447e3f4461e2', 'info', 'Stock updated successfully after payment verification', '2025-06-04 14:10:40'),
(229, 'pay_684097596799f3.7936024076b621f4f', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWMW4QZPLk7FzRY3eJh3ADj', '2025-06-04 18:58:33'),
(230, 'pay_684097596799f3.7936024076b621f4f', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 18:58:33'),
(231, 'pay_684097596799f3.7936024076b621f4f', 'info', 'Stock updated successfully after payment verification', '2025-06-04 18:58:33'),
(232, 'pay_684097876a5715.461385027acf6fc74', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWMWvQZPLk7FzRY4QhDXLrH', '2025-06-04 18:59:19'),
(233, 'pay_684097876a5715.461385027acf6fc74', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 18:59:19'),
(234, 'pay_684097876a5715.461385027acf6fc74', 'info', 'Stock updated successfully after payment verification', '2025-06-04 18:59:19'),
(235, 'pay_684098ab4045d9.89554213af0151c9d', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWMbdQZPLk7FzRY3rQFlubr', '2025-06-04 19:04:11'),
(236, 'pay_684098ab4045d9.89554213af0151c9d', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 19:04:11'),
(237, 'pay_684098ab4045d9.89554213af0151c9d', 'info', 'Stock updated successfully after payment verification', '2025-06-04 19:04:11'),
(238, 'pay_6840996edda899.17805678ef64d6cf6', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWMenQZPLk7FzRY1iT23KiZ', '2025-06-04 19:07:26'),
(239, 'pay_6840996edda899.17805678ef64d6cf6', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 19:07:27'),
(240, 'pay_6840996edda899.17805678ef64d6cf6', 'info', 'Stock updated successfully after payment verification', '2025-06-04 19:07:27'),
(241, 'pay_68409ef8990079.033947330ab290cd4', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWN1eQZPLk7FzRY1MbN4AF4', '2025-06-04 19:31:04'),
(242, 'pay_68409ef8990079.033947330ab290cd4', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 19:31:04'),
(243, 'pay_68409ef8990079.033947330ab290cd4', 'info', 'Stock updated successfully after payment verification', '2025-06-04 19:31:04'),
(244, 'pay_6840a43f092877.23282661b571549ac', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWNNRQZPLk7FzRY5s9XDgKU', '2025-06-04 19:53:35'),
(245, 'pay_6840a43f092877.23282661b571549ac', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 19:53:35'),
(246, 'pay_6840a43f092877.23282661b571549ac', 'info', 'Stock updated successfully after payment verification', '2025-06-04 19:53:35'),
(247, 'pay_6840a45ae1b800.9168347221506a6ad', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWNNtQZPLk7FzRY3NzftQkn', '2025-06-04 19:54:02'),
(248, 'pay_6840a45ae1b800.9168347221506a6ad', 'info', 'Stripe verification: Confirmed successful', '2025-06-04 19:54:03'),
(249, 'pay_6840a45ae1b800.9168347221506a6ad', 'info', 'Stock updated successfully after payment verification', '2025-06-04 19:54:03'),
(250, 'pay_6840eece0d4439.96826153f7c455bee', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RWSLKQZPLk7FzRY2vJau2SL', '2025-06-05 01:11:42'),
(251, 'pay_6840eece0d4439.96826153f7c455bee', 'info', 'Stripe verification: Confirmed successful', '2025-06-05 01:11:42'),
(252, 'pay_6840eece0d4439.96826153f7c455bee', 'info', 'Stock updated successfully after payment verification', '2025-06-05 01:11:42'),
(253, 'pay_6840eece0d4439.96826153f7c455bee', 'info', 'Invoice email sent successfully to caichian8@gmail.com', '2025-06-05 01:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `product_categories` varchar(50) NOT NULL,
  `gender` enum('Men','Women','Kid') NOT NULL,
  `status` enum('Normal','New','Promotion') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `description` text NOT NULL,
  `product_img1` varchar(255) NOT NULL,
  `product_img2` varchar(255) NOT NULL,
  `product_img3` varchar(255) NOT NULL,
  `product_img4` varchar(255) NOT NULL,
  `size_chart` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_type`, `brand`, `product_categories`, `gender`, `status`, `price`, `discount_price`, `description`, `product_img1`, `product_img2`, `product_img3`, `product_img4`, `size_chart`, `deleted`, `create_at`) VALUES
(5, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK\'', 'Footwear', 'Adidas', 'Running', 'Men', 'Promotion', 120.00, 100.00, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK‘\r\n', '67f5e7b0c8aa6.jpg', '67f5e7b0c92b6.jpg', '67f5e7b0c9d2f.jpg', '67f5e7b0ca726.jpg', '6837c644c9a28.jpg', 0, '2025-04-03 04:48:01'),
(6, 'Nike Quest 6 Mens Road Running Shoes FD6033002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 120.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f5e6725324c.jpg', '67f5e67254561.jpg', '67f5e67254d38.jpg', '67f5e67255331.jpg', '67f5387e89e10.jpg', 0, '2025-04-03 07:35:49'),
(7, 'Asics GT2000 13 LiteShow Men\'s Running Shoes Blue', 'Footwear', 'Asics', 'Running', 'Men', 'New', 180.00, 0.00, 'The GT200 13 running shoe offers great support for a comfortable run that will help you clear your mind. Its the most comfortable version of the shoe yet and the ideal choice if you are looking for extra guidance during your run.\r\n\r\n', '67f5eb24629b0.png', '67f5eb2462e7a.png', '67f5f044d935c.png', '67f5f044d9738.png', '67ee3aed4f0d6.jpg', 0, '2025-04-03 07:38:21'),
(8, 'Nike Quest 6 Men Road Running Shoes FD6033-002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 369.00, NULL, 'Nike Quest 6\r\nMen Road Running Shoes\r\nThe Nike Quest 6 is for runners of all levels. But make no mistake, it’s anything but entry-level. A supercomfortable and supportive midfoot fit band helps keep you stable for your miles. Plus, a supersoft midsole foam helps cushion each step.', '67f5efee89f95.png', '67f5efee8aad9.png', '67f5efee8aee7.png', '67f5efee8b26e.png', '67ee3d5b17ff8.jpg', 0, '2025-04-03 07:48:43'),
(18, 'Nike ACD TOP NOV Men Training Jersey', 'Apparel', 'Nike', 'Jersey', 'Men', 'Promotion', 135.00, 100.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f63019d51ec.png', '67f63019d51f0.png', '67f63019d51f1.png', '67f714adddae5.png', '67f8d72022188.jpg', 0, '2025-04-09 08:30:17'),
(19, 'Asics Gel Cumulus 27 Lite Show Mens Running Shoes Orange', 'Footwear', 'Asics', 'Running', 'Men', 'New', 150.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f631e68b401.png', '67f631e68b405.png', '67f631e68b407.png', '67f631e68b408.png', '67f631e68b409.jpg', 0, '2025-04-09 08:37:58'),
(20, 'Nike Downshifter 13 Mens Road Running Shoes FD6454008', 'Footwear', 'Nike', 'Running', 'Men', 'Normal', 120.00, 0.00, 'Nike Downshifter 13\r\nMens Road Running Shoes\r\nWhether you are starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a revamped upper and cushion and durability, it helps you find that extra gear or take that first stride toward chasing down your goals.', '67f63260c09de.png', '67f63260c09e2.png', '67f63260c09e3.png', '67f63260c09e4.png', '67f63260c09e5.jpg', 0, '2025-04-09 08:40:00'),
(22, 'Umbro Men Jersey Blue', 'Apparel', 'Umbro', 'Jersey', 'Men', 'Normal', 50.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f6334ac43d9.png', '67f6334ac43dd.png', '67f6334ac43de.png', '67f6859bcf9d7.png', '67f6334ac43e0.jpg', 0, '2025-04-09 08:43:54'),
(23, 'ADIDAS ENTRADA 22 MEN FOOTBALL JERSEY RED', 'Apparel', 'Adidas', 'Jersey', 'Men', 'Normal', 80.50, 0.00, 'ADIDAS ENTRADA 22 MENS FOOTBALL JERSEY RED', '67f633af8d002.png', '67f633af8d005.png', '67f633af8d006.png', '67f685b032d2a.png', '67f633af8d008.jpg', 0, '2025-04-09 08:45:35'),
(24, 'Umbro 1924 Women Jersey MAROON', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Normal', 35.00, 0.00, 'Umbro 1924 Women Jersey MAROON', '67f63a639bb60.png', '67f63a639bb68.png', '67f63a639bb69.png', '67f685cf1fc6b.png', '67f63a639bb6b.jpg', 0, '2025-04-09 09:14:11'),
(25, 'PUMA Scend Pro Men Running Shoes Red', 'Footwear', 'Puma', 'Running', 'Men', 'New', 114.50, 0.00, 'PRODUCT STORY\r\nIntroducing the Scend Pro, PUMA new essential runner. The Protread rubber outsole is designed for distance and enhanced with a bold paint line and cushioned tooling. The breathable mesh uppers are ultra lightweight, while the comfort collar locks down for a secure fit during workouts.', '67f8c2fcb865e.png', '67f8c2fcb8662.png', '67f8c2fcb8663.png', '67f8c2fcb8664.png', '67f8c2fcb8665.jpg', 0, '2025-04-11 07:21:32'),
(26, 'Nike Downshifter 13 Men Running Shoes Black', 'Footwear', 'Nike', 'Running', 'Men', 'Normal', 255.00, 0.00, 'Nike Downshifter 13 Men Running Shoes Black', '67f8c66f0e148.png', '67f8c66f0e14c.png', '67f8c66f0e14d.png', '67f8c66f0e14e.png', '67f8c66f0e14f.jpg', 0, '2025-04-11 07:36:15'),
(27, 'PUMA Velocity Nitro 3 Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'New', 419.30, 0.00, 'PRODUCT DESCRIPTIONS\r\nEmbark on your running journey with the VELOCITY NITR 3, PUMA s hero franchise featuring NITROFOA cushioning. Sleek styling, outstanding comfort, and a slightly higher stack height make it the ideal do-it-all trainer. From short distances to long runs, this neutral shoe offers a smooth ride and optimal cushioning for all runners. Experience the best in comfort and performance with the VELOCITY 3’.', '67f8c7497ab71.png', '67f8c7497ab78.png', '67f8c7497ab7a.png', '67f8c7497ab7b.png', '67f8c7497ab7c.jpg', 0, '2025-04-11 07:39:53'),
(28, 'Puma Pounce Lite Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'Normal', 289.00, 0.00, 'The all-new Pounce Lite joins our Running line this year. Featuring an ultra-lightweight foam and a durable PROTREAD outsole, it is the ultimate blend of style and performance.', '67f8c8ff9d98d.png', '67f8c8ff9d990.png', '67f8c8ff9d991.png', '67f8c8ff9d992.png', '67f8c8ff9d993.jpg', 0, '2025-04-11 07:47:11'),
(29, 'New Balance Fresh Foam 510 V6 Men Running Shoes Black', 'Footwear', 'New Balance', 'Running', 'Men', 'Promotion', 359.00, 179.50, 'PRODUCT DESCRIPTIONS\r\nA rugged shoe designed to help you conquer the roughest trails with ease.', '67f8ca88b98cf.png', '67f8ca88b98d2.png', '67f8ca88b98d4.png', '67f8ca88b98d5.png', '67f8ca88b98d6.jpg', 0, '2025-04-11 07:53:44'),
(30, 'Nike Phantom GX 2 Elite LV8 FG Low-Top Football Boots', 'Footwear', 'Nike', 'Boot', 'Men', 'Normal', 1000.00, NULL, 'Nike Phantom GX 2 Elite LV8\r\nFG Low-Top Football Boots\r\nObsessed with perfecting your craft? We made this for you. In the middle of the storm, with chaos swirling all around you, you’ve calmly found the final third of the field, thanks to your uncanny mix of on-ball guile and grace. Go finish the job in the Phantom GX 2 Elite. Revolutionary Nike Gripknit covers the striking area of the cleat while Nike Cyclone 360 traction helps guide your unscripted agility. We design Elite Boots for you and the world’s biggest stars to give you high-level quality, because you demand greatness from yourself and your footwear.', '67f8cb61928fa.png', '67f8cb61928ff.png', '67f8cb6192900.png', '67f8cb6192901.png', '67f8cb6192902.jpg', 0, '2025-04-11 07:57:21'),
(31, 'Adidas F50 Club Firm/Multi-Ground Men Football Boots', 'Footwear', 'Adidas', 'Boot', 'Men', 'Normal', 219.00, 0.00, 'STAY ONE STEP AHEAD IN F50 BOOTS DESIGNED FOR MULTIPLE SURFACES.\r\n\r\nPRODUCT STORY\r\n\r\nPush your pace to the limit in lightweight adidas F50 footwear engineered for speed. These Club football boots keep you comfortable on the move with a supportive Fiberskin upper and perforated tongue. Underneath, a versatile outsole propels you to lightning-fast play on firm ground and artificial grass surfaces.\r\nDETAILS\r\n\r\nRegular fit\r\nLace closure\r\nSynthetic Fiberskin upper\r\nPerforated tongue\r\nTextile lining\r\nFirm/multi-ground outsole', '67f8cc45b1590.png', '67f8cc45b1593.png', '67f8cc45b1594.png', '67f8cc45b1595.png', '67f8cc45b1596.jpg', 0, '2025-04-11 08:01:09'),
(32, 'Adidas Deportivo III Flexible Ground Men Football Boots', 'Footwear', 'Adidas', 'Boot', 'Men', 'Promotion', 159.00, 127.20, 'Adidas Deportivo III Flexible Ground Men Football Boots\r\nComfortable synthetic boots with a versatile outsole.\r\n\r\nTake the beautiful game seriously in these adidas football boots. Designed for play on a variety of surfaces, they have a perforated tongue and soft lining to keep your feet fresh through long matches. Stitching on the synthetic forefoot improves ball control, allowing for pinpoint passes and powerful strikes. An anatomically shaped heel keeps you strapped in for fast-moving play.\r\n\r\nMore Details\r\n- Regular fit\r\n- Lace closure\r\n- Synthetic upper\r\n- Textile lining\r\n- Breathable perforated tongue\r\n- Control stitching on forefoot\r\n- Flexible ground outsole\r\nColor : White,Solar Red,Lucid Blue', '67f8cce2c043e.png', '67f8cce2c0441.png', '67f8cce2c0442.png', '67f8cce2c0443.png', '67f8cce2c0444.jpg', 0, '2025-04-11 08:03:46'),
(33, 'PUMA Men Boot Future Play FG/AG', 'Footwear', 'Puma', 'Boot', 'Men', 'Normal', 219.00, 0.00, 'PUMA Men Boot Future Play FG/AG', '67f8cd5089cef.png', '67f8cd5089cf3.png', '67f8cd5089cf5.png', '67f8cd5089cf6.png', '67f8cd5089cf8.jpg', 0, '2025-04-11 08:05:36'),
(34, 'Asics Upcourt 5 Men Court Shoes White', 'Footwear', 'Asics', 'Court', 'Men', 'Promotion', 249.00, 199.20, 'PRODUCT STORY\r\n\r\n\r\nThe UPCOURT 5 shoe is a lightweight court offering that is designed to offer better flexibility and a more comfortable fit.\r\n\r\nIt features a broader section of mesh paneling that helps create a softer and more adaptable fit. Meanwhile, its supportive midfoot overlays offer better stability during multi-directional movements. ?\r\n\r\nLastly, the toe and heel counter are reinforced with durable panels that help your shoes last longer.', '67f8cdee40e3e.png', '67f8cdee40e43.png', '67f8cdee40e44.png', '67f8cdee40e45.png', '67f8cdee40e46.jpg', 0, '2025-04-11 08:08:14'),
(35, 'LOTTO MAXIMO MEN COURT SHOES BLACK', 'Footwear', 'Lotto', 'Court', 'Men', 'Promotion', 119.00, 83.30, 'LOTTO MAXIMO MEN COURT SHOES BLACK', '67f8ce734b277.png', '67f8ce734b27b.png', '67f8ce734b27c.png', '67f8ce734b27d.png', '67f8ce734b27e.jpg', 0, '2025-04-11 08:10:27'),
(37, 'PUMA ESSENTIALS SMALL LOGO FULL-ZIP MEN HOODIE NAVY', 'Apparel', 'Puma', 'Jacket', 'Men', 'Promotion', 239.00, 119.50, 'PUMA ESSENTIALS SMALL LOGO FULL-ZIP MEN HOODIE NAVY', '67f8d2db7c9c5.png', '67f8d2db7c9cd.png', '67f8d2db7c9cf.png', '67f8d2db7c9d0.png', '67f8d6a512aae.jpg', 0, '2025-04-11 08:29:15'),
(38, 'PUMA WOMEN 3/4 PANTS', 'Apparel', 'Puma', 'Pant', 'Women', 'New', 99.00, 0.00, 'PUMA MEN 3/4 PANTS', '67f8d34f0bb18.png', '67f8d34f0bb1b.png', '67f8d34f0bb1c.png', '67f8d34f0bb1d.png', '67f8d6ebab640.jpg', 0, '2025-04-11 08:31:11'),
(39, 'Nike Academy Team Football Duffel Bag (Medium, 60L) Black', 'Equipment', 'Nike', 'Bag', 'Men', 'Normal', 145.00, 0.00, 'Nike Academy Team\r\nFootball Duffel Bag (Medium, 60L)\r\nCONVENIENT STORAGE TO AND FROM THE FIELD.\r\nThe Nike Academy Team Duffel Bag is a durable design built to keep you organized. Designated compartments provide space for your ball, Boots and clothes while multiple straps let you comfortably carry your gear when you are on the go.', '67f8d443de86c.png', '67f8d443de870.png', '67f8d443de871.png', '67f8d443de872.png', '67f8d443de873.jpg', 0, '2025-04-11 08:35:15'),
(40, 'ADIDAS 4ATHLTS DUFFEL BAG LARGE', 'Equipment', 'Adidas', 'Bag', 'Men', 'New', 259.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nA LARGE DUFFEL BAG FOR TRAINING, MADE IN PART WITH RECYCLED CONTENT.\r\nYou ve got a lot of gear to haul. This adidas large duffel bag has the space. The durable bag is sized just right for out of town tournaments or training camps. A zip compartment keeps wet clothes or shoes separate.', '67f8d4c88e90e.png', '67f8d4c88e911.png', '67f8d4c88e912.png', '67f8d4c88e913.png', '67f8d4c88e914.', 0, '2025-04-11 08:37:28'),
(41, 'PUMA ESSENTIALS III CAP BLACK', 'Equipment', 'Puma', 'Cap', 'Men', 'New', 45.00, 0.00, 'PRODUCT STORY\r\nFirst seen as far back as 1860, and rising to prominence in the 1980s, fewer modern streetwear accessories have the same claim to fame as the baseball cap. This hat from our Essentials range is wonderfully simple, with a classic six-panel shape, pure cotton fabrication, a hair-safe hook-and-loop adjuster and a woven badge with understated PUMA branding on the front.\r\n\r\nDETAILS\r\n- Six-panel shape with curved visor\r\n- Structured buckram\r\n- Hair-safe hook-and-loop adjuster\r\n- Moisture-wicking mesh sweatband\r\n- Woven badge with PUMA No. 1 Logo and merrowed edge on front\r\n\r\nMaterial Information\r\nSHELL : 100% cotton', '67f8d7853fa9b.png', '67f8d7853faa2.png', '67f8d7853faa5.png', '67f8d7853faa7.png', '67f8d7853faa9.', 0, '2025-04-11 08:49:09'),
(42, 'Asics Upcourt 5 Women Court Shoes White', 'Footwear', 'Asics', 'Court', 'Women', 'Promotion', 249.00, 199.20, 'PRODUCT STORY The UPCOURT 5 shoe is designed to offer better flexibility and a more comfortable fit. ? It features a broader section of mesh paneling that helps create a softer and more adaptable fit. Meanwhile, its supportive mid-foot overlays offer better stability during multi-directional movements. Lastly, the toe and heel counter are reinforced with durable panels that help your shoes last longer. DETAILS - Supportive overlays - Improves stability. - Mesh panels - Improves comfort. - Toe and heel counter are designed for durability - The sockliner is produced with the solution dyeing process that reduces water usage by approximately\r\n\r\n', '67f8d88e8b5de.png', '67f8d88e8b5e3.png', '67f8d88e8b5e4.png', '67f8d88e8b5e5.png', '67f8d88e8b5e6.jpg', 0, '2025-04-11 08:53:34'),
(43, 'Asics GT-2000 13 Lite-Show Women Running Shoes Pink', 'Footwear', 'Asics', 'Running', 'Women', 'New', 200.00, 0.00, 'The GT-2000™ 13 running shoe offers great support for a comfortable run that will help you clear your mind. It is the most comfortable version of the shoe yet and the ideal choice if you are looking for extra guidance during your run.', '6802088d2df3e.png', '6802088d2df43.png', '6802088d2df44.png', '6802088d2df45.png', '6802088d2df46.jpg', 0, '2025-04-18 08:08:45'),
(44, 'Adidas Goletto IX FG/MG Men Football Boots Gold JP5268', 'Footwear', 'Adidas', 'Boot', 'Men', 'Promotion', 200.00, 159.00, 'Adidas Goletto IX FG/MG Men Football Boots Gold JP5268', '6802097817dc1.png', '6802097817dc5.png', '6802097817dc6.png', '6802097817dc7.png', '6802097817dc8.jpg', 0, '2025-04-18 08:12:40'),
(45, 'ADIDAS GOLETTO VIII MEN FUTSAL SHOES BLACK GY5785', 'Footwear', 'Adidas', 'Futsal', 'Men', 'Normal', 95.40, 0.00, 'Indoor court boots made in part with recycled content.\r\nWhether you are scoring them or stopping them, the beautiful game is all about goals. These adidas Goletto VIII football boots show off a stylish new synthetic upper, complete with control-boosting forefoot stitching and comfortable heel padding. Their non-marking rubber outsole grips flat courts to help you shine.\r\n\r\nMade in part with recycled content generated from production waste, e.g. cutting scraps, and post-consumer household waste to avoid the larger environmental impact of producing virgin content.\r\n\r\nSPECIFICATIONS\r\n- Regular fit\r\n- Lace closure\r\n- Soft synthetic upper\r\n- Non-marking rubber outsole\r\n- 25% of the components used to make the upper are made with a minimum of 50% recycled content\r\n- Colour : Core Black/ White/ Red', '68020a3c4f429.png', '68020a3c4f42c.png', '68020a3c4f42d.png', '68020a3c4f42e.png', '68020a3c4f42f.jpg', 0, '2025-04-18 08:15:56'),
(46, 'UMBRO VELOCITA ELIXIR CLUB IC MEN FUTSAL SHOES BLACK', 'Footwear', 'Umbro', 'Futsal', 'Men', 'Normal', 64.00, 159.00, 'UMBRO VELOCITA ELIXIR CLUB IC MEN FUTSAL SHOES BLACK', '68020b43e0cc5.png', '68020b43e0cc8.png', '68020b43e0cc9.png', '68020b43e0cca.png', '68020b43e0ccb.jpg', 0, '2025-04-18 08:20:19'),
(47, 'Nike Tiempo Legend 10 Club Indoor Court Low-Top Men Football Shoes', 'Footwear', 'Nike', 'Futsal', 'Men', 'Promotion', 225.00, 205.50, 'Nike Tiempo Legend 10 Club Indoor Court Low-Top Men Football Shoes', '68020c9324488.png', '68020c932448a.png', '68020c932448b.png', '68020c932448c.png', '68020c932448d.jpg', 0, '2025-04-18 08:25:55'),
(48, 'ADIDAS ENTRADA 22 MEN TRAINING PANTS BLACK', 'Apparel', 'Adidas', 'Pant', 'Men', 'New', 149.00, 0.00, 'ENTRADA 22 TRAINING PANTS\r\nFOOTBALL TRAINING PANTS MADE WITH RECYCLED MATERIALS.\r\nTrain like the professionals, relax in style. These adidas football track pants are made with moisture-absorbing AEROREADY so you feel dry and ready for anything on or off the pitch. The drawcord-adjustable waist ensures a stay-put fit, and ankle zips mean easy on and off wherever your day takes you.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.\r\n\r\nSPECIFICATIONS\r\n- Regular fit with mid rise\r\n- Elastic waist with drawcord\r\n- 100% recycled polyester doubleknit\r\n- Moisture-absorbing AEROREADY\r\n- Side seam pockets\r\n- Ankle zips\r\n- Doubleknit\r\n- Product colour: Black', '68020ebe08859.png', '68020ebe08868.png', '68020ebe0886c.png', '68020ebe0886f.png', '68020ebe08873.jpg', 0, '2025-04-18 08:35:10'),
(49, 'Nike Dri-Fit Challenger Men 7', 'Apparel', 'Nike', 'Pant', 'Men', 'Promotion', 135.00, 94.50, 'Nike Dri-FIT Challenger\r\nMen 7\" Brief-Lined Versatile Shorts\r\nDesigned for running, training and yoga, our sweat-wicking Challenger Shorts keep it light and breathable with a relaxed fit that helps you get the most out of your movement. We geared them for more than running, with a comfortable pocket that would not irritate when you move from the track to the gym.\r\n\r\nKeep Dry, Stay Cool\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.Brief lining offers lightweight support.\r\nRace Ready Comfort\r\nMade with lightweight and stretchy woven fabric, these shorts also have mesh at the lower side panels to help you stay cool. Side vents keep you moving freely through your stride.\r\nSafe Storage\r\nHand pockets help keep small essentials close, like your keys or card. A back pocket with a hook-and-loop closure is big enough to hold most phones.\r\nDesigned for Running, Yoga and Training\r\nLength: 7\"\r\nLiner: Brief\r\nFabric: Lightweight woven\r\nStorage: Side pockets and hook-and-loop back pocket\r\nWaistband: Narrow with drawcord\r\n\r\nMore Details\r\nBody: 100% Polyester. Lining: 92% Polyester/8% Spandex.\r\nMachine wash\r\nImported\r\nNot intended for use as Personal Protective Equipment (PPE)\r\nPRODUCT COLOUR : Black/ Black/ Black/ Reflective Silver', '6802101026fc5.png', '6802101026fc8.png', '6802101026fc9.png', '6802101026fca.png', '6802101026fcb.jpg', 1, '2025-04-18 08:40:48'),
(50, 'PUMA TEAMRISE MEN SHORTS NAVY', 'Apparel', 'Puma', 'Pant', 'Men', 'New', 59.00, 0.00, 'PRODUCT STORY\r\nThe Rise collection combines high-performance materials with modern design to keep your team prepared for the demands of the game at every level.\r\n\r\nFEATURES & BENEFITS\r\nDryCELL technology wicks away moisture, leaving you feeling comfortable.\r\n\r\nDETAILS\r\n- Elasticated waistband with drawcord\r\n- Heat transfer PUMA Cat Logo on the left leg\r\n- Regular Fit.\r\n\r\nMaterial Information\r\n100% Polyester\r\n', '680210cad82fc.png', '680210cad8300.png', '680210cad8301.png', '680210cad8302.png', '680210cad8303.jpg', 0, '2025-04-18 08:43:54'),
(52, 'Adidas EPP Club Ball WHITE HT2458', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'Promotion', 69.00, 28.00, 'PRODUCT DESCRIPTIONS\r\nAN EVERYDAY FOOTBALL FOR ANY-DAY BALLERS.\r\nWhen your teammates come calling, always be prepared. This adidas EPP Club Ball was built for training sessions, kickabouts in the park and for showing friends your skills. Its durable, machine-stitched surface means it is ready to stand up to razor-sharp passes and high-intensity scrimmages. The butyl bladder ensures optimal air-retention, so you can worry less about the ball and focus more on your footwork.\r\n\r\nPRODUCT DETAILS\r\n- Size 5\r\n- Machine stitched\r\n- Butyl Bladder\r\n- Requires inflation\r\n- 100% TPU film\r\n- Product Colour: White / Bold Aqua\r\n', '680212760801a.png', '680212760801f.png', '6802127608020.png', '6802127608021.png', '6802127608022.', 0, '2025-04-18 08:51:02'),
(53, 'Adidas Tiro Club Shin Guards White', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'Normal', 49.00, 0.00, '- 100% polypropylene (25% recycled)\r\n- Foam backing\r\n- Perforated flexible hard shield\r\n\r\n\r\nView Product Details\r\nX\r\n- 100% polypropylene (25% recycled)\r\n- Foam backing\r\n- Perforated flexible hard shield', '680212e6cc63f.png', '680212e6cc642.png', '680212e6cc643.png', '680212e6cc644.png', '680212e6cc645.', 0, '2025-04-18 08:52:54'),
(54, 'NIKE MEN J SHINGUARD SHINGUARD BLACK', 'Equipment', 'Nike', 'Football Accessories', 'Men', 'Normal', 39.00, 0.00, 'Designed to take the impacts of the game, the Nike J Shinguards are made from a tough composite shell and feature perforations for ventilated comfort.', '68021332451da.png', '68021332451dc.png', '68021332451dd.png', '68021332451de.png', '68021332451df.', 0, '2025-04-18 08:54:10'),
(55, 'Adidas Starlancer Club Football BLACK', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'New', 69.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nA DURABLE BALL FOR SHOWING OFF YOUR SKILLS.\r\nLet your skills shine every time you head to the park with this Starlancer Club Ball. Made for recreational play, it has a machine-stitched TPU cover for durability and a butyl bladder to ensure it stays pumped up for longer. That blurred graphic takes its inspiration from a classic adidas football look.\r\n\r\nPRODUCT DESCRIPTIONS\r\n- 100 percent TPU cover\r\n- Machine-stitched construction\r\n- Butyl bladder\r\n- Product Colour : Black/ Silver Metallic', '680213b0e52fb.png', '680213b0e52fe.png', '680213b0e52ff.png', '680213b0e5300.png', '680213b0e5301.', 0, '2025-04-18 08:56:16'),
(56, 'Adidas Copa Glove Pro Beige', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'New', 339.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nGRIPPY FOOTBALL GOALIE GLOVES WITH A CLASSIC FIT AND FEEL.\r\nKeep goal in total comfort. These adidas Copa Pro goalkeeper gloves come with a classic latex mesh backhand and stretchy wrist strap for a perfect fit. On the palm, URG 2.0 provides superior grip and an abrasion zone adds extra resilience. The negative cut ensures a snug fit around the fingers so your handling is always on point.\r\n\r\nPRODUCT DETAILS\r\n- Negative cut\r\n- Palm: URG 2.0 100% Latex\r\n- Body: 45% Recycled Polyester, 30% Cotton, 20% Polyurethane, 5% Elastane\r\n- Elastic wrist strap\r\n- Product Colour : Ivory/ Solar Red/ Black', '6802143456aea.png', '6802143456aed.png', '6802143456aee.png', '6802143456aef.png', '6802143456af0.', 0, '2025-04-18 08:58:28'),
(57, 'Adidas Predator Training Football Gloves Yellow', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'New', 119.00, NULL, 'PRODUCT DESCRIPTIONS\r\nCOMFORTABLE FOOTBALL GLOVES FOR IMPROVING YOUR KEEPING.\r\nBuild up your skills between the sticks. These adidas Predator Training gloves are for goalkeepers who are always working on their game. A positive cut at the fingers provides maximum ball contact for grasping crosses and throwing long. The Soft Grip latex palm ensures confident play whatever Mother Nature fires at you.\r\n\r\nPRODUCT DETAILS\r\n- Positive cut\r\n- Palm: Soft Grip 100% latex\r\n- Body: 100% polyurethane\r\n- Half-wrap wrist strap\r\n- Colour: Solar Yellow / Black / Solar Red', '680214b9d5a99.png', '680214b9d5a9c.png', '680214b9d5a9d.png', '680214b9d5a9e.png', '680214b9d5a9f.', 0, '2025-04-18 09:00:41'),
(58, 'PUMA Orbita Laliga 1 MS Football Ball White', 'Equipment', 'Puma', 'Football Accessories', 'Men', 'Promotion', 139.00, 125.00, 'PUMA Orbita Laliga 1 MS Football Ball White', '6802153d03a60.png', '6802153d03a64.png', '6802153d03a65.png', '6802153d03a66.png', '6802153d03a67.', 0, '2025-04-18 09:02:53'),
(59, 'JORDAN RISE CAP ADJUSTABLE HAT BROWN', 'Equipment', 'Nike', 'Cap', 'Men', 'New', 79.00, NULL, 'View Product Details\r\nX\r\nJordan Rise Cap\r\nAdjustable Hat\r\nIs your fit missing an understated dad hat to show the word your Jordan pride? We got you. This classic structured fit has a sloped crown and a curved bill for a casual, broken-in look. And the metal Jumpman emblem makes a perfectly subtle statement.\r\n\r\nAdjustable back strap lets you change up the fit.\r\nSweatband provides a cool, comfortable feel.\r\n\r\nMore Details\r\nBody: 100% nylon. Front Panel Lining: 65% polyester/35% cotton.\r\nDo not wash\r\nImported\r\n100% NYLON\r\n\r\nPRODUCT COLOUR : Hemp/ Black/ Black/ Gunmetal\r\n', '680216b4ab82f.png', '680b404a2984a.png', '680b404a29b06.png', '680b404a29d17.png', '680216b4ab836.', 0, '2025-04-18 09:09:08'),
(60, 'ADIDAS BASEBALL CAP WHITE', 'Equipment', 'Adidas', 'Cap', 'Men', 'Normal', 99.00, 0.00, 'BASEBALL CAP\r\nA LIGHTWEIGHT CAP WITH AN ADJUSTABLE FIT.\r\nTop things off nicely with this classic Baseball Cap. Made with sunny days in mind, the soft fabric shields you from the sun rays. An adidas Badge of Sport takes centre stage in front.\r\n\r\nSPECIFICATIONS\r\n- 100% cotton twill\r\n- Soft, lightweight feel\r\n- Classic, adjustable cap\r\n- UV 50 factor\r\n- Padded sweatband\r\n- Medium-curved crown and visor\r\n- Twill\r\n- Product Colour: White / White / Black\r\n', '6802179d95475.png', '6802179d95479.png', '6802179d9547b.png', '6802179d9547c.png', '6802179d9547d.', 0, '2025-04-18 09:13:01'),
(61, 'NIKE EVERYDAY LIGHTWEIGHT TRAINING CREW SOCKS (3 PAIRS) WHITE', 'Equipment', 'Nike', 'Sock', 'Men', 'Normal', 69.00, 0.00, 'NIKE EVERYDAY LIGHTWEIGHT TRAINING CREW SOCKS (3 PAIRS) WHITE', '6802184bcb53d.png', '6802184bcb540.png', '6802184bcb541.png', '6802184bcb542.png', '6802184bcb543.', 0, '2025-04-18 09:15:55'),
(68, 'Under Armour Style Logo Men\'s Round Neck White', 'Apparel', 'Under Amour', 'Jersey', 'Men', 'Normal', 62.10, NULL, 'Under Armour Style Logo Men\'s Round Neck White', '6824b8dde175a.png', '6824b8dde175d.png', '6824b8dde175e.png', '6824b8dde175f.png', '6824b8dde1760.jpg', 0, '2025-05-14 15:38:05'),
(69, 'Under Armour Sportstyle Logo Men\'s Round Neck Black', 'Apparel', 'Under Amour', 'Jersey', 'Men', 'Normal', 62.10, NULL, 'Under Armour Sportstyle Logo Men\'s Round Neck Black', '6824b9698cce2.png', '6824b9698cce4.png', '6824b9698cce5.png', '6824b9698cce6.png', '6824b9698cce7.jpg', 0, '2025-05-14 15:40:25'),
(70, 'Umbro 1924 Women\'s Jersey NAVY', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Normal', 16.00, NULL, 'Umbro 1924 Women\'s Jersey NAVY', '6824ba0d48bb8.png', '6824ba0d48bba.png', '6824ba0d48bbb.png', '6824ba0d48bbc.png', '6824ba0d48bbd.jpg', 0, '2025-05-14 15:43:09'),
(72, 'PUMA BVB Anniversary Men\'s Jc Replica', 'Apparel', 'Puma', 'Jersey', 'Men', 'Normal', 319.00, NULL, 'PRODUCT STORY\r\nDare to stand out with the 24/25 BVB Special Jersey. Inspired by the 94/95 season, which ended with BVB winning their first league title in 32 years, it lights up in neon yellow for a look as loud as the matchday atmosphere at SIGNAL IDUNA PARK. On the 30th anniversary of the historic season, this jersey is all about being fearless, taking risks, and relentlessly chasing your dreams. Designed for the fans, Replica jersey pairs the same match-worn look with a casual silhouette, details, and materials, ideal for both game day and everyday wear.\r\n\r\nThe jersey comes in exclusive special packaging for a premium experience', '6824bd807575c.png', '6824bd807575e.png', '6824bd807575f.png', '6824bd8075760.png', '6824bd8075761.jpg', 0, '2025-05-14 15:57:52'),
(73, 'PUMA ESSENTIALS FULL-ZIP WOMEN\'S HOODIE BLACK', 'Apparel', 'Puma', 'Jacket', 'Women', 'Normal', 119.50, NULL, 'FEATURES & BENEFITS\r\n\r\nContains Recycled Material: Made with recycled fibres. One of PUMA\'s answers to reduce its environmental impact.\r\n\r\nDETAILS\r\n\r\n- Regular fit\r\n- Jersey-lined hood with drawcord for an adjustable fit\r\n- Kangaroo-style side pockets for convenient storage of belongings\r\n- Ribbed cuffs and waistband\r\n- PUMA No. 1 Logo raised rubber print at left chest\r\n- Cotton and recycled polyester\r\n\r\nMaterial Information\r\n\r\nHood Lining: 100% cotton\r\nRib: 98% cotton, 2% elastane\r\nShell: 68% cotton, 32% polyester', '6824be6ae4acf.png', '6824be6ae4ad2.png', '6824be6ae4ad4.png', '6824be6ae4ad7.png', '6824be6ae4ad8.jpg', 0, '2025-05-14 16:01:46'),
(74, 'NIKE SPORTSWEAR CLUB FLEECE WOMEN\'S FULL-ZIP HOODIE BLACK', 'Apparel', 'Nike', 'Jacket', 'Women', 'Promotion', 255.00, 153.00, 'More Details\r\nBody: 80% cotton/20% polyester. Hood Lining: 100% cotton.\r\nEmbroidered Futura logo\r\nFlat drawcord\r\nFront pocket\r\nMachine wash\r\nImported\r\n80% COTTON/ 20% POLYESTER\r\n\r\nPRODUCT COLOUR : Black/ White', '6824bf33d0882.png', '6824bf33d0885.png', '6824bf33d0886.png', '6824bf33d0887.png', '6824bf33d0888.jpg', 0, '2025-05-14 16:05:07'),
(75, 'Asics Versablast 4 Women\'s Running Shoes Grey 1012B775-501', 'Footwear', 'Asics', 'Running', 'Women', 'Normal', 329.00, NULL, 'The VERSABLAST™ 4 shoe is a versatile training partner for varied workouts and running regimens. ​\r\n\r\nInspired by our BLAST series, we wanted to make this shoe softer and lighter. It’s designed to provide good comfort for a range of activities. ​\r\n\r\nBy focusing on the shoe’s energy return, we equipped the midsole foam with a responsive rebound for a fast feel underfoot.\r\n\r\nStyle #: 1012B775.501', '6824c04470bb2.png', '6824c04470bb8.png', '6824c04470bba.png', '6824c04470bbc.png', '6824c04470bbe.jpg', 0, '2025-05-14 16:09:40'),
(76, 'Adidas Duramo SL2 Women\'s Running Shoes IF9397', 'Footwear', 'Adidas', 'Running', 'Women', 'New', 299.00, NULL, 'Lightweight running shoes for training.\r\nStart your running journey with ease, and then keep going. From your first step to your first 10k race, these adidas running shoes help you train in comfort. The engineered mesh upper and padded heel create a soft, supportive feel, and the LIGHTMOTION midsole adds light, stable cushioning to every stride', '6824c1acbecac.png', '6824c1acbecae.png', '6824c1acbecaf.png', '6824c1acbecb0.png', '6824c1acbecb1.jpg', 1, '2025-05-14 16:15:40'),
(78, 'Adidas F50 Club Junior Football Boots Blue IE1308', 'Footwear', 'Adidas', 'Football Shoes', 'Kid', 'New', 179.00, NULL, 'Push your pace to the limit in lightweight adidas F50 footwear engineered for speed. These juniors\' Club football boots keep you comfortable on the move with a supportive Fiberskin upper and perforated tongue. Underneath, a versatile outsole propels you to lightning-fast play on firm ground and artificial grass surfaces.\r\n\r\nThis product features at least 20% recycled materials. By reusing materials that have already been created, we help to reduce waste and our reliance on finite resources and reduce the footprint of the products we make.', '68311d5b5fd57.png', '68311d5b5fd5b.png', '68311d5b5fd5c.png', '68311d5b5fd5d.png', '68311d5b5fd5e.jpeg', 0, '2025-05-24 01:14:03'),
(79, 'NIKE JR. MERCURIAL VAPOR 15 CLUB FG/MG LITTLE/BIG KIDS\' MULTI-GROUND FOOTBALL BOOTS IN BLACK', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'New', 175.00, NULL, 'FAST FOR THE FIELD.\r\nLearn all the skills and drills, and use them in your next game with the Nike Jr. Mercurial Vapor 15 Club Boots. Made for all different surfaces from artificial to real grass fields, they have the traction you need to move and run up the field. The durable design is meant to hold up through every practice and every minute on game day.\r\n\r\nContain Your Speed\r\nThe speed cage inside is made of a thin but strong material that secures the foot to the plate without adding extra weight.\r\nExceptional Touch\r\nSynthetic leather upper features small textured details that provide grip for better ball control when dribbling at quick speeds.\r\nGround Control\r\nPlastic plates with molded studs are designed to work on real grass and artificial grass fields. It\'s versatile for athletes who must switch between surfaces.\r\nSeamless Fit and Feel\r\nA comfortable lining wraps your foot for a natural, close-fitting feel.\r\n', '68311eb933134.png', '68311eb93313b.png', '68311eb93313d.png', '68311eb93313e.png', '68311eb933140.jpeg', 0, '2025-05-24 01:19:53'),
(80, 'Nike Jr. Tiempo Legend 10 Club Little/Big Kids\' Multi-Ground Low-Top Football Boots', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'Promotion', 155.00, 139.50, 'Nike Jr. Tiempo Legend 10 Club\r\nLittle/Big Kids\' Multi-Ground Low-Top Football Boots\r\nEven Legends find ways to evolve. Whether you are starting out or just playing for fun, the latest iteration of these Club Boots gets you on the field without compromising on quality. Synthetic leather conforms to your foot and doesn\'t overstretch, giving you better control. Lighter and sleeker than any other Tiempo to date, the Legend 10 is for any position on the field, whether you\'re sending a pinpoint pass through the defense or tracking back to stop a breakaway.\r\n', '68311fe67d191.png', '68311fe67d195.png', '68311fe67d1ae.png', '68311fe67d1b4.png', '68311fe67d1b5.jpeg', 0, '2025-05-24 01:24:54'),
(81, 'Nike Mercurial Vapor 16 Academy Junior Football Boots Black FQ8392-002', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'Normal', 269.00, NULL, 'Nike Mercurial Vapor 16 Academy Junior Boot Black FQ8392-002 now available at Al-Ikhsan Sports. Push your pace to the limit in lightweight adidas F50 footwear engineered for speed. These juniors\' Club football boots keep you comfortable on the move with a supportive Fiberskin upper and perforated tongue. Underneath, a versatile outsole propels you to lightning-fast play on firm ground and artificial grass surfaces.\r\n\r\nThis product features at least 20% recycled materials. By reusing materials that have already been created, we help to reduce waste and our reliance on finite resources and reduce the footprint of the products we make.', '683120aeb14ab.png', '683120aeb14ae.png', '683120aeb14af.png', '683120aeb14b0.png', '683120aeb14b1.jpeg', 0, '2025-05-24 01:28:14'),
(82, 'Adidas Predator Club Junior Football Boots White ID3810', 'Footwear', 'Adidas', 'Football Shoes', 'Kid', 'Normal', 179.00, NULL, 'Go into every game knowing you\'ll score in juniors\' adidas Predator Club boots crafted for goals. Their synthetic upper is lined with soft textile for a comfortable feel on the pitch. Turn them over and you\'ll find a versatile outsole designed for precision football on firm ground and artificial grass.\r\n\r\nThis product features at least 20% recycled materials. By reusing materials that have already been created, we help to reduce waste and our reliance on finite resources and reduce the footprint of the products we make.', '683121ac82851.png', '683121ac82857.png', '683121ac82858.png', '683121ac82859.png', '683121ac8285a.jpeg', 0, '2025-05-24 01:32:28'),
(83, 'Nike Jr. Phantom GX 2 Club Little/Big Kids\' MG Low-Top Football Boots', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'Promotion', 195.00, 175.50, 'Description Return Policy Warranty\r\nNike Jr. Phantom GX 2 Club\r\nLittle/Big Kids\' MG Low-Top Football Boots\r\nWhether you’re starting out or just playing for fun, Club shoes get you on the field without compromising on quality. Created with goals in mind, the Jr. Phantom GX 2 Club has a grippy texture covering the striking area of the shoe and reliable traction to help guide your unscripted agility.\r\n', '683122057271d.png', '6831220572722.png', '6831220572723.png', '6831220572724.png', '6831220572725.jpeg', 0, '2025-05-24 01:33:57'),
(84, 'NIKE FLEX RUNNER 2 BABY/TODDLER SHOES BLACK', 'Footwear', 'Nike', 'Kid Shoes', 'Kid', 'Promotion', 145.00, 87.00, 'Nike Flex Runner 2\r\nBaby/Toddler Shoes\r\nA SLIP-ON FOR LITTLE SPEEDSTERS.\r\nWhoÃ¢â‚¬â„¢s ready to play? The Nike Flex Runner 2 is built for the kiddo on the goÃ¢â‚¬â€from the crib to the playground to wherever their day takes them. ItÃ¢â‚¬â„¢s laces-free! Meaning itÃ¢â‚¬â„¢s super quick to slip on and off. The straps and bootie-like design make sure your little oneÃ¢â‚¬â„¢s fit stays snug.\r\n', '6831234788538.png', '683123478853d.png', '683123478853e.png', '683123478853f.png', '6831234788540.jpeg', 0, '2025-05-24 01:39:19'),
(85, 'Nike Pico 5 Baby & Toddler Shoes', 'Footwear', 'Nike', 'Kid Shoes', 'Kid', 'Promotion', 125.00, 75.00, 'No cancellation of order, EXCHANGE, or refund will be entertained, unless the item you have ordered is out of stock or we are unable to fulfill your order for any reason whatsoever.\r\n\r\nIf an item is defective, we will replace the item once the defective item is returned to us, provided that we receive notice of the defect within seven (7) days of receipt of such defective item by you, together with the receipt evidencing the purchase made by you.', '683123bf06964.png', '683123bf06969.png', '683123bf0696a.png', '683123bf0696b.png', '683123bf0696c.jpeg', 0, '2025-05-24 01:41:19'),
(86, 'Adidas Tensaur Hook And Loop Kids\' Shoes Black', 'Footwear', 'Adidas', 'Kid Shoes', 'Kid', 'Promotion', 159.00, 99.00, 'ROBUST TRAINERS THAT FASTEN EASILY, MADE IN PART WITH RECYCLED MATERIALS.\r\nSpinning, striding, leaping or sprinting, kids are constantly testing their shoes. Built with a sturdy, non-marking rubber outsole, these adidas trainers grip the ground while leaving floors exactly how they found them. And the two hook-and-loop straps on each shoe are easy to fasten when they\'re on the go.\r\n\r\nMade with a series of recycled materials, this upper features at least 50% recycled content. This product represents just one of our solutions to help end plastic waste.', '68312438573e1.png', '68312438573e4.png', '68312438573e5.png', '68312438573e6.png', '68312438573e7.jpeg', 0, '2025-05-24 01:43:20'),
(87, 'PUMA Anzarun Lite Kids\' Trainers Pink', 'Footwear', 'Puma', 'Kid Shoes', 'Kid', 'Promotion', 169.00, 101.40, 'Deconstructed and refined, the Anzarun Lite Trainers ensure a clean look that\'s perfect for every occasion. Featuring a breathable mesh upper, a cushy EVA midsole and true heritage PUMA branding throughout, this shoe is comfort and style combined.\r\n', '683124be35649.png', '683124be3564d.png', '683124be3564e.png', '683124be3564f.png', '683124be35650.jpeg', 0, '2025-05-24 01:45:34'),
(88, 'ADIDAS TENSAUR SPORT TRAINING SHOES JUNIOR BLACK', 'Footwear', 'Adidas', 'Kid Shoes', 'Kid', 'Promotion', 159.00, 99.00, 'ROBUST TRAINERS THAT FASTEN EASILY, MADE IN PART WITH RECYCLED MATERIALS.\r\nSpinning, striding, leaping or sprinting, kids are constantly testing their shoes. Built with a sturdy, non-marking rubber outsole, these adidas trainers grip the ground while leaving floors exactly how they found them. And the two hook-and-loop straps on each shoe are easy to fasten when they\'re on the go.\r\n\r\nMade with a series of recycled materials, this upper features at least 5% recycled content. This product represents just one of our solutions to help end plastic waste.', '683125507ed57.png', '683125507ed5c.png', '683125507ed5d.png', '683125507ed5e.png', '683125507ed5f.jpeg', 0, '2025-05-24 01:48:00'),
(89, 'PUMA Flyer Runner V Kids Trainers Grey', 'Footwear', 'Puma', 'Kid Shoes', 'Kid', 'New', 169.00, 0.00, 'Comfort is key in PUMA\'s Flyer Runner. With a breathable mesh upper, an extra-soft sockliner and a cushioning midsole, this lightweight trainer adds style to sportswear.\r\n', '6831260b6d224.png', '6831260b6d228.png', '6831260b6d22a.png', '6831260b6d22b.png', '6831260b6d22c.jpeg', 0, '2025-05-24 01:51:07'),
(90, 'PUMA Flyer Runner Junior Trainers Pink', 'Footwear', 'Puma', 'Kid Shoes', 'Kid', 'Normal', 179.00, NULL, 'Comfort is key in PUMA\'s Flyer Runner. With a breathable mesh upper, an extra-soft sockliner and a cushioning midsole, this lightweight trainer adds style to sportswear.', '683126e45e24d.png', '683126e45e251.png', '683126e45e252.png', '683126e45e253.png', '683126e45e254.jpeg', 0, '2025-05-24 01:54:44'),
(91, 'Under Armour Assert 10 AC Junior School Shoes Black', 'Footwear', 'Under Amour', 'School Shoes', 'Kid', 'Normal', 99.00, NULL, 'Under Armour Assert 10 AC Junior School Shoes', '6831279d6eb04.png', '6831279d6eb09.png', '6831279d6eb0a.png', '6831279d6eb0b.png', '6831279d6eb0c.jpeg', 0, '2025-05-24 01:57:49'),
(92, 'Under Armour Phade RN 2 Junior School Shoes Black', 'Footwear', 'Under Amour', 'School Shoes', 'Kid', 'Normal', 199.00, NULL, 'Under Armour Phade RN 2 Junior School Shoes Black', '6831281f3fdaf.png', '6831281f3fdb6.png', '6831281f3fdb9.png', '6831281f3fdbb.png', '6831281f3fdbd.jpeg', 0, '2025-05-24 01:59:59'),
(93, 'Nike Academy Big Kids\' Dri-FIT Football Track Jacket', 'Apparel', 'Nike', 'Jacket', 'Kid', 'Promotion', 165.00, 132.00, 'Nike Academy\r\nBig Kids\' Dri-FIT Football Track Jacket\r\nTake your skills to the next level in this Academy Football jacket. We infused its soft double knit fabric with our sweat-wicking tech to help you stay dry and comfortable as you fine-tune your skills.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nDouble knit fabric is soft and smooth.\r\nZippered side hand pockets help provide secure storage.\r\n', '683128fcb690b.png', '683128fcb6911.png', '683128fcb6912.png', '683128fcb6913.png', '683128fcb6914.jpeg', 0, '2025-05-24 02:03:40'),
(94, 'Nike Sportswear Club Junior Jacket Green FZ5513-037', 'Apparel', 'Nike', 'Jacket', 'Kid', 'New', 129.00, NULL, 'Nike Sportswear Club Junior Jacket Green FZ5513-037 now available at Al-Ikhsan Sports.\r\n\r\nNike Sportswear Club Big Kids\' Full-Zip Knit Jacket Infused with classic Nike style, this jacket has the familiar fit of our Club Fleece favorites with the soft feel of a lightweight jersey knit fabric. Slip it over your favorite tee when you want an extra layer but worry fleece is too warm.  Single knit jersey fabric is soft and lightweight for comfortable wear. Tall collar helps block out cold drafts while ribbed cuffs and hem help hold the jacket in place as you move. With just the right amount of room through the body and hips, this jacket has a classic fit that makes layering easy.', '683129820f25b.png', '683129820f25e.png', '683129820f25f.png', '683129820f260.png', '683129820f261.jpeg', 0, '2025-05-24 02:05:54'),
(95, 'Nike Sportswear Junior Jacket Purple FZ5557-537', 'Apparel', 'Nike', 'Jacket', 'Kid', 'Normal', 195.00, NULL, 'Nike Sportswear\r\nGirls\' Oversized Lightweight Jacket\r\nMeet an all-star styling piece you can wear every day. Made from smooth woven twill, this lightweight jacket features a water-repellent finish that helps you stay dry in wet weather. Plus, dropped shoulders and a spacious fit through the body and sleeves make layering a breeze.\r\n\r\nWater-repellent finish helps keep you dry in wet weather.\r\nWoven twill fabric is lightweight and smooth for comfortable wear.\r\nHigh collar helps block out the wind for a touch of added warmth.', '683129e348ea2.png', '683129e348ea5.png', '683129e348ea6.png', '683129e348ea7.png', '683129e348ea8.jpeg', 0, '2025-05-24 02:07:31'),
(96, 'PUMA ESS+ Mid 90S Junior Boy Hoodie Beige', 'Apparel', 'Puma', 'Jacket', 'Kid', 'Promotion', 199.00, 179.10, 'This comfy hoodie is built for everyday. Show off bold graphics front and back, stay cosy in the lined hood and pockets. The ribbed hem and cuffs keep the chill out.', '68312a5375b53.png', '68312a5375b5b.png', '68312a5375b5d.png', '68312a5375b5f.png', '68312a5375b60.jpeg', 0, '2025-05-24 02:09:23'),
(97, 'PUMA ESS+ Mid 90S Junior Boy Hoodie Black', 'Apparel', 'Puma', 'Jacket', 'Kid', 'Promotion', 199.00, 179.10, 'This comfy hoodie is built for everyday. Show off bold graphics front and back, stay cosy in the lined hood and pockets. The ribbed hem and cuffs keep the chill out', '68312abfad007.png', '68312abfad00b.png', '68312abfad00c.png', '68312abfad00d.png', '68312abfad00e.jpeg', 0, '2025-05-24 02:11:11'),
(98, 'ADIDAS ENTRADA 22 JUNIOR JERSEY RED', 'Apparel', 'Adidas', 'Jersey', 'Kid', 'New', 65.00, NULL, 'Train like a professional. Relax like a champion. This juniors\' soccer jersey shows off a clean, classic design with an adidas Badge of Sport on the chest. Moisture-absorbing AEROREADY will keep you dry and cool whether you\'re training on the pitch or enjoying a night out with friends.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '68312b818159c.png', '68312b81815a1.png', '68312b81815a3.png', '68312b81815a4.png', '68312b81815a5.jpeg', 0, '2025-05-24 02:14:25'),
(99, 'ADIDAS ENTRADA 22 JUNIOR JERSEY BLUE', 'Apparel', 'Adidas', 'Jersey', 'Kid', 'Normal', 65.00, NULL, 'Train like a professional. Relax like a champion. This juniors\' soccer jersey shows off a clean, classic design with an adidas Badge of Sport on the chest. Moisture-absorbing AEROREADY will keep you dry and cool whether you\'re training on the pitch or enjoying a night out with friends.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '68312bf67799d.png', '68312bf6779a0.png', '68312bf6779a1.png', '68312bf6779a2.png', '68312bf6779a3.jpeg', 0, '2025-05-24 02:16:22'),
(100, 'Nike Dri-FIT Academy23 Kids\' Football Top', 'Apparel', 'Nike', 'Jersey', 'Kid', 'Promotion', 59.00, 47.20, 'Nike Dri-FIT Academy23\r\nKids\' Football Top\r\nHere at Academy23, we\'re all about encouraging you to work on those ball skills. Whether you\'re at practice after school or out at recess, this soft, stretchy knit top is a great choice to keep you cool and comfortable. Plus, the classic fit gives you room to move without distractions. Ready, set, let\'s GOAL!\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nBreathable, stretchy knit fabric feels airy and smooth.\r\nMesh side panels ramp up the breathability to keep you cool.\r\n', '68312c65e2014.png', '68312c65e2018.png', '68312c65e2019.png', '68312c65e201a.png', '68312c65e201b.jpeg', 0, '2025-05-24 02:18:13'),
(101, 'ADIDAS ENTRADA 22 JUNIOR JERSEY WHITE', 'Apparel', 'Adidas', 'Jersey', 'Kid', 'Promotion', 65.00, 45.50, 'Train like a professional. Relax like a champion. This juniors\' football jersey shows off a clean, classic design with an adidas Badge of Sport on the chest. Moisture-absorbing AEROREADY will keep you dry and cool whether you\'re training on the pitch or enjoying a night out with friends.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '68312cc41f030.png', '68312cc41f035.png', '68312cc41f036.png', '68312cc41f037.png', '68312cc41f038.jpeg', 0, '2025-05-24 02:19:48'),
(102, 'Nike Academy Big Kids\' Dri-FIT Football Top Purple', 'Apparel', 'Nike', 'Jersey', 'Kid', 'New', 79.00, NULL, 'Nike Academy\r\nBig Kids\' Dri-FIT Football Top\r\nDesigned to help you stay cool and comfortable while you fine-tune your skills, this lightweight knit top is infused with our sweat-wicking Dri-FIT technology. Mesh at the back and sides adds breathability.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nMesh panels help add breathability.', '68312d2b118d6.png', '68312d2b118d9.png', '68312d2b118da.png', '68312d2b118db.png', '68312d2b118dc.jpeg', 0, '2025-05-24 02:21:31'),
(103, 'PUMA teamLIGA Junior Football Jersey', 'Apparel', 'Puma', 'Jersey', 'Kid', 'Normal', 79.00, NULL, 'For those who love the battle of the competition as much as the win, teamLIGA steps up its game to bring you a collection with a redesigned fit, scoring big on performance and wearability. Whether you\'re playing your greatest match or cheering your team from the sidelines, you\'ll stay cool and comfortable in this clean-lined jersey featuring PUMA\'s moisture-wicking dryCELL technology and mesh detailing.', '68312d9f2908f.png', '68312d9f29092.png', '68312d9f29093.png', '68312d9f29094.png', '68312d9f29095.jpeg', 0, '2025-05-24 02:23:27'),
(104, 'PUMA teamGOAL Junior Football Jersey Red', 'Apparel', 'Puma', 'Jersey', 'Kid', 'New', 69.00, NULL, 'Help your team keep up with the demands of the game at every level with this football jersey. Combining high-performance materials and a modern design, jersey is here to take your team to the next level. The jersey is treated to wick away moisture.\r\n\r\n', '68312df0a1db3.png', '68312df0a1db7.png', '68312df0a1db8.png', '68312df0a1db9.png', '68312df0a1dba.jpeg', 0, '2025-05-24 02:24:48'),
(105, 'Nike Dri-FIT Academy23 Big Kids\' Short-Sleeve Football Shirt Pink', 'Apparel', 'Nike', 'Jersey', 'Kid', 'Promotion', 89.00, 62.30, 'Nike Dri-FIT Academy23\r\nBig Kids\' Short-Sleeve Football Shirt\r\nDribble down the field, pass to your friend and score as many good times as you can. Whether you\'re at practice or just out at recess, this breathable and easy-fitting Shirt helps you stay cool and comfortable. The fabric pulls sweat away from your skin while mesh panels on the back and under the sleeves allow for airflowâ€”perfect for when play heats up.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nKnit fabric is lightweight and smooth.\r\nMesh on the back and sleeves adds breathability to help you stay cool.\r\n', '68312e5ee368a.png', '68312e5ee368e.png', '68312e5ee368f.png', '68312e5ee3690.png', '68312e5ee3691.jpeg', 0, '2025-05-24 02:26:38');
INSERT INTO `product` (`product_id`, `product_name`, `product_type`, `brand`, `product_categories`, `gender`, `status`, `price`, `discount_price`, `description`, `product_img1`, `product_img2`, `product_img3`, `product_img4`, `size_chart`, `deleted`, `create_at`) VALUES
(106, 'Nike Sportswear Big Kids\' Track Pants Blue', 'Apparel', 'Nike', 'Pant', 'Kid', 'New', 90.00, NULL, 'PRODUCT COLOUR : Aquarius Blue/ Court Blue/ White', '68312ed372bbe.png', '68312ed372bc3.png', '68312ed372bc6.png', '68312ed372bc9.png', '68312ed372bca.jpeg', 0, '2025-05-24 02:28:35'),
(107, 'Nike Trophy23 Big Kids\' Dri-FIT Training Shorts Grey', 'Apparel', 'Nike', 'Pant', 'Kid', 'Promotion', 79.00, 55.30, 'Nike Trophy23\r\nBig Kids\' Dri-FIT Training Shorts\r\nWhether you\'re dreaming of playing at the next level, or a beginner learning the sport, the Nike Trophy23 shorts get you geared up for the game. Super breathable fabric that kicks sweat away helps you stay cool and confident while honing your skills.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nSmooth knit fabric is lightweight and breathable.\r\nMesh side panels provide extra breathability.\r\nElastic waistband and drawcord provide a snug fit.\r\n', '68312f38c5d26.png', '68312f38c5d2e.png', '68312f38c5d30.png', '68312f38c5d32.png', '68312f38c5d33.jpeg', 0, '2025-05-24 02:30:16'),
(108, 'Puma No.1 Logo Junior Boy\'s T-Bottom Black', 'Apparel', 'Puma', 'Pant', 'Kid', 'Normal', 129.00, NULL, 'Puma No.1 Logo Junior Boy\'s T-Bottom Black', '68312fa414f65.png', '68312fa414f69.png', '68312fa414f6a.png', '68312fa414f6b.png', '68312fa414f6c.jpeg', 0, '2025-05-24 02:32:04'),
(109, 'Nike Multiessntl Junior\'s T-Bottom', 'Apparel', 'Nike', 'Pant', 'Kid', 'Normal', 145.00, NULL, 'NIKE A T/BOTTOM JR B MULTI ESSNTL 0125', '6831300c3e5f1.png', '6831300c3e5f8.png', '6831300c3e5fa.png', '6831300c3e5fc.png', '6831300c3e5fe.jpeg', 0, '2025-05-24 02:33:48'),
(110, 'Nike Academy 23 Junior Boy Short Black', 'Apparel', 'Nike', 'Pant', 'Kid', 'New', 99.00, NULL, 'Nike Academy 23 Junior Boy Short Black', '6831308fbae79.png', '6831308fbae7e.png', '6831308fbae97.png', '6831308fbae9b.png', '6831308fbae9c.jpeg', 0, '2025-05-24 02:35:59'),
(111, 'NIKE CLUB KIDS\' ADJUSTABLE UNSTRUCTURED BOXY CAP BLUE', 'Equipment', 'Nike', 'Cap', 'Kid', 'Promotion', 69.00, 48.30, 'Nike Club\r\nKids\' Adjustable Unstructured Boxy Cap\r\nOur beloved mascot is moving up in the world! Boxy started on tees and now joins in on headwear. Seen here on this mid-depth, curved-bill Club Cap, Boxy is sure to make all your friends and classmates feel welcome with a nice, big ol\' wave.\r\n\r\nTwill fabric is lightweight and smooth.\r\nClub caps all have any-day style with a mid depth.\r\nBranded metal tri-glide lets you adjust your fit with ease.', '683131dfc9017.png', '683131dfc901c.png', '683131dfc901d.png', '683131dfc901f.png', '683131dfc9020.jpeg', 0, '2025-05-24 02:41:35'),
(112, 'NIKE APEX KIDS\' FUTURA BUCKET HAT PURPLE', 'Equipment', 'Nike', 'Cap', 'Kid', 'Promotion', 69.00, 48.30, 'X\r\nNike Apex\r\nKids\' Futura Bucket Hat\r\nEnjoy the sun and add some fun to your look with the Nike Apex Bucket Hat. As easy to throw on as it is to pack away when you\'re not wearing it, this lightweight hat keeps you comfortable whenever the sun\'s out and adventure is on the menu!\r\n\r\nApex bucket hats all have a mid depth and 360-degree coverage.\r\nEasy-to-store unstructured design packs away flat.\r\n', '68313241a2432.png', '68313241a2436.png', '68313241a2437.png', '68313241a2438.png', '68313241a2439.jpeg', 0, '2025-05-24 02:43:13'),
(113, 'Nike Club Unstructured Futura Wash Junior Boy Caps', 'Equipment', 'Nike', 'Cap', 'Kid', 'New', 49.00, NULL, 'Fly under the radar in this unstructured, mid-depth Nike Club Cap. Washed for a well-worn, well-loved look, it\'s a lightweight, easy-to-wear curved-brim cap with a simple design that you can add to practically any look.', '683132a5ca665.png', '683132a5ca66a.png', '683132a5ca66c.png', '683132a5ca66d.png', '683132a5ca670.jpeg', 0, '2025-05-24 02:44:53'),
(114, 'NIKE CLUB KIDS\' UNSTRUCTURED FUTURA WASH CAP WHITE', 'Equipment', 'Nike', 'Cap', 'Kid', 'New', 49.00, NULL, 'Nike Club\r\nKids\' Unstructured Futura Wash Cap\r\nFly under the radar in this unstructured, mid-depth Nike Club Cap. Washed for a well-worn, well-loved look, it\'s a lightweight, easy-to-wear curved-brim cap with a simple design that you can add to practically any look.\r\n\r\nOrganic cotton twill fabric is lightweight and smooth.\r\nMid-depth, 6-panel design makes for easy styling.\r\nSwoosh branded metal tri-glide lets you adjust your fit with ease.', '683132e6a6236.png', '683132e6a6239.png', '683132e6a623a.png', '683132e6a623b.png', '683132e6a623c.jpeg', 0, '2025-05-24 02:45:58'),
(115, 'Nike Club 1024 Junior\'s Caps', 'Equipment', 'Nike', 'Cap', 'Kid', 'Normal', 49.00, NULL, 'NIKE E CAPS JR B CLUB 1024-0225', '6831332bbbcb0.png', '6831332bbbcb5.png', '6831332bbbcb6.png', '6831332bbbcb7.png', '6831332bbbcb8.jpeg', 0, '2025-05-24 02:47:07'),
(116, 'Umbro Basic School Socks Black', 'Equipment', 'Umbro', 'Sock', 'Kid', 'Normal', 9.90, NULL, 'Umbro Basic School Socks Black', '6831379fbcad4.png', '6831379fbcad9.png', '6831379fbcada.png', '6831379fbcadb.png', '6831379fbcadc.jpeg', 0, '2025-05-24 03:06:07'),
(117, 'PUMA SNEAKER SOCKS JUNIOR 3P BLACK', 'Equipment', 'Puma', 'Sock', 'Kid', 'Normal', 39.00, NULL, 'PUMA SNEAKER SOCKS JUNIOR 3P BLACK', '68313afa30452.png', '68313afa30456.png', '68313afa30458.png', '68313afa30459.png', '68313afa3045a.jpeg', 0, '2025-05-24 03:20:26'),
(118, 'Umbro Basic 2in1 School Socks Black', 'Equipment', 'Umbro', 'Sock', 'Kid', 'Normal', 16.90, NULL, 'Umbro Basic 2in1 School Socks Black', '68313b78d917e.png', '68313b78d9182.png', '68313b78d9183.png', '68313b78d9184.png', '68313b78d9185.jpeg', 0, '2025-05-24 03:22:32'),
(119, 'Nike Everyday Kids\' Cushioned Ankle Socks White (6 Pairs)', 'Equipment', 'Nike', 'Sock', 'Kid', 'New', 69.00, NULL, 'Nike Everyday Kids\' Cushioned Ankle Socks White (6 Pairs)', '68313bc7ccfce.png', '68313bc7ccfd2.png', '68313bc7ccfd3.png', '68313bc7ccfd4.png', '68313bc7ccfd5.jpeg', 0, '2025-05-24 03:23:51'),
(120, 'NIKE ELEMENTAL KIDS\' BACKPACK (20L) BLACK DR6084-010', 'Equipment', 'Nike', 'Bag', 'Kid', 'New', 139.00, NULL, 'Nike Elemental\r\nKids\' Backpack (20L)\r\nWhether it\'s rushing to class or hanging out after school with friends, this backpack has the space to tackle it all. This bag features a large side pocket for water bottles as well as a front pocket to store quick-grab items. Adjustable padded straps make wearing this bag comfortable and easy.\r\n\r\nConnected by a secure clip, the bag also comes with a large pencil case.\r\nDouble zippers on main compartment allow for easy opening.\r\nHaul loop gives you an alternate carrying option.\r\n', '6833b9334bdb6.png', '6833b9334bdba.png', '6833b9334bdbb.png', '6833b9334bdbc.png', '6833b9334bdbd.', 0, '2025-05-26 00:43:31'),
(121, 'Nike Elemental Junior Boy', 'Equipment', 'Nike', 'Bag', 'Kid', 'Normal', 139.00, NULL, 'Nike Elemental Junior Boy Bagpack', '6833b99792281.png', '6833b9979228b.png', '6833b9979228d.png', '6833b9979228f.png', '6833b99792290.', 0, '2025-05-26 00:45:11'),
(122, 'Puma Phase AOP Small Kid\'s Backpack Blue', 'Equipment', 'Puma', 'Bag', 'Kid', 'Normal', 75.00, NULL, 'Puma Phase AOP Small Kid\'s Backpack Blue', '6833ba0e9c1e8.png', '6833ba0e9c1ec.png', '6833ba0e9c1ed.png', '6833ba0e9c1ee.png', '6833ba0e9c1ef.jpeg', 0, '2025-05-26 00:47:10'),
(123, 'NIKE ELEMENTAL KIDS\' BACKPACK (20L) NAVY', 'Equipment', 'Nike', 'Bag', 'Kid', 'Promotion', 139.00, 97.30, 'Nike Elemental\r\nKids\' Backpack (20L)\r\nLet your backpack stand out just as much as your kicks. Designed with a padded back panel and adjustable straps, this bag is all about a comfy all-day carry. The removable case has plenty of room for all your pens, pencils, erasers or chargers, ensuring your small stuff stays organized and easy to reach.\r\n\r\nHaul loop gives you an alternate carrying option.\r\nPadded shoulder straps are adjustable for a comfortable fit.\r\nSide pockets hold a water bottle or other small essentials.\r\nDetachable case clips onto the bag or fits inside to help keep small items organized.\r\nPadded back panel helps provide a comfortable carry.', '6833ba741e456.png', '6833ba741e459.png', '6833ba741e45a.png', '6833ba741e45b.png', '6833ba741e45c.', 0, '2025-05-26 00:48:52'),
(124, 'Puma Phase Small Kid\'s Backpack Black', 'Equipment', 'Puma', 'Bag', 'Kid', 'New', 75.00, NULL, 'Step up your game with this vibrant backpack. Featuring a spacious main compartment. front zip pocket, and padded shoulder straps, it\'s designed for your dynamic lifestyle', '6833bad9dbf86.png', '6833bad9dbf8a.png', '6833bad9dbf8b.png', '6833bad9dbf8c.png', '6833bad9dbf8d.jpeg', 1, '2025-05-26 00:50:33'),
(125, 'Adidas Uefa Champions League Mini Ball White', 'Equipment', 'Adidas', 'Football Accessories', 'Kid', 'Promotion', 55.00, 38.50, 'The pride of London. The bold design on this adidas UCL Mini ball borrows from the lion-inspired look of the official match ball. Made for showing your skills on the move, this durable ball features a TPU cover for pinpoint control and a foam core that means you never have to pump it up. You\'ll feel like football royalty every time you see that iconic UEFA Champions League logo.', '6833bb4874d0f.png', '6833bb4874d13.png', '6833bb4874d15.png', '6833bb4874d16.png', '6833bb4874d17.', 0, '2025-05-26 00:52:24'),
(126, 'Puma Orbita Fam Mini Ball White', 'Equipment', 'Puma', 'Football Accessories', 'Kid', 'Promotion', 59.00, 53.10, 'Mini ball of the FAM league.', '6833bb9eb4f03.png', '6833bb9eb4f08.png', '6833bb9eb4f09.png', '6833bb9eb4f0a.png', '6833bb9eb4f0b.', 0, '2025-05-26 00:53:50'),
(127, 'Puma Orbita Mfl Mini Ball White', 'Equipment', 'Puma', 'Football Accessories', 'Kid', 'Normal', 59.00, NULL, 'Mini ball of The Malaysia Football League', '6833bbe2b8e4a.png', '6833bbe2b8e50.png', '6833bbe2b8e51.png', '6833bbe2b8e52.png', '6833bbe2b8e53.', 0, '2025-05-26 00:54:58'),
(128, 'Adidas Mini Ball Euro 2024', 'Equipment', 'Adidas', 'Football Accessories', 'Kid', 'Promotion', 55.00, 38.50, 'Adidas Mini Ball Euro 2024', '6833bc3576f97.png', '6833bc3576f9b.png', '6833bc3576f9c.png', '6833bc3576f9d.png', '6833bc3576f9e.', 0, '2025-05-26 00:56:21'),
(129, 'NIKE SKILLS MINI KIDS\' BASKETBALL MULTI', 'Equipment', 'Nike', 'Football Accessories', 'Kid', 'Promotion', 89.00, 62.30, 'Ideal for young athletes under the age of 5, this Nike mini basketball features a moulded, deep-channel design that allows for precise ball control.\r\n', '6833bc79460ef.png', '6833bc79460f3.png', '6833bc79460f4.png', '6833bc79460f5.png', '6833bc79460f6.', 0, '2025-05-26 00:57:29'),
(130, 'Nike Grind Dumbbell', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 73.00, NULL, 'The first of its kind, the Nike Grind Dumbbell is crafted from Nike Grind rubber, a byproduct of Nike\'s footwear manufacturing process. Measured by total product volume, each dumbbell contains at least 20% Nike Grind rubber. With bold Swoosh branding, anti-roll geometry, sculpted edges, and a knurled handle, it delivers durability, comfort, and control for every workout.', '6833bd3d98497.png', '6833bd3d9849a.png', '6833bd3d9849b.png', '6833bd3d9849c.png', '6833bd3d9849d.', 0, '2025-05-26 01:00:45'),
(131, 'Nike Dumbbell', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 92.00, NULL, 'The Nike Dumbbell is your ultimate training companion. With prominent swoosh branding and numbering, robust rubber, and sculpted edges, this dumbbell offers both durability and comfort for all your training sessions. A medium knurled handle ensures a secure and comfortable grip, delivering the ideal balance of friction and control. The dumbbell is arguably the most versatile piece of gym equipment one can own — enabling thousands of movements to the athlete. Pick yours up and get moving! JUST DO IT.', '6833be56c856e.png', '6833be56c8573.png', '6833be56c8574.png', '6833be56c8575.png', '6833be56c8577.', 0, '2025-05-26 01:05:26'),
(132, 'Nike Strength Pro Urethane Dumbbell', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'New', 232.00, NULL, 'Introducing the Nike Strength Pro Urethane Dumbbell, the next evolution in commercial strength equipment. Built with championship athletes in mind, this premium dumbbell blends durability, design, and performance to handle the toughest workouts. High-quality urethane molded around precision-machined steel ensures unmatched durability, making it ideal for commercial use. Its octagonal design prevents rolling, while the flat bottom ensures stability during training. The knurled grip provides secure handling, giving athletes total control with each rep. Combining bold Nike aesthetics and elite performance, Nike Strength Pro sets a new standard in strength training equipment.', '6833beafa7bd2.png', '6833beafa7bd7.png', '6833beafa7bd9.png', '6833beafa7bda.png', '6833beafa7bdb.', 0, '2025-05-26 01:06:55'),
(133, 'Nike Dumbbell Rack Sets', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 7570.00, NULL, 'The 5-50 lb Nike Dumbbell Set features the most popular dumbbells from 5-50 lb and a storage rack that allows you to fully stock your home gym for almost any dumbbell workout. With prominent swoosh branding and numbering, robust rubber, and sculpted edges, this dumbbell offers both durability and comfort for all your training sessions. ', '6833bf5b88f7a.png', '6833bf5b88f7e.png', '6833bf5b88f7f.png', '6833bf5b88f80.png', '6833bf5b88f81.', 0, '2025-05-26 01:09:47'),
(134, 'Nike Dumbbell Tree Sets', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 4004.00, NULL, 'Maximize your training space with the 10-50 lb Nike Grind Dumbbell Tree Set, including 5 pairs of the Nike Grind Dumbbells weighing 10, 20, 30, 40, and 50 lb. The sleek, five-tiered steel tree offers optimal storage, keeping your workout area organized and clutter-free. Paired with the Nike Dumbbell Tree, this bundle offers an optimal storage solution for your essential dumbbells.', '6833bfafbead6.png', '6833bfafbeae5.png', '6833bfafbeae7.png', '6833bfafbeae8.png', '6833bfafbeae9.', 0, '2025-05-26 01:11:11'),
(135, 'Nike Court Borough Low Recraft Baby Toddler Shoes', 'Footwear', 'Nike', 'Kid Shoes', 'Kid', 'New', 155.00, 0.00, 'Nike Court Borough Low Recraft\r\nBaby/Toddler Shoes\r\nStart your little ones off on the right foot with the Nike Court Borough. Made for the long haul, this recrafted legend uses a combination of durable materials on the upper and outsole to achieve a classic look with an easy on and off strap for entry. Plus, a redesigned toe box and midfoot give their feet a little extra room so they can charge through the day in comfort.', '683790ca940c1.png', '683790ca940c4.png', '683790ca940c5.png', '683790ca940c6.png', '683790ca940c7.jpeg', 0, '2025-05-28 22:40:10'),
(136, 'ADIDAS TENSAUR RUN JUNIOR SHOES WHITE', 'Footwear', 'Adidas', 'Kid Shoes', 'Kid', 'Promotion', 169.00, 101.40, 'Kids\' comfy running shoes made in part with recycled materials.\r\nLet your kid do up the hook-and-loop straps on these adidas shoes and get playing. The durable EVA unitsole gives them all they need to perfect their tree climbing and races across the lawn. These shoes are the perfect companion for their everyday adventures.\r\n\r\nMade with a series of recycled materials, and at least 50% recycled content, this product represents just one of our solutions to help end plastic waste.\r\n', '68379149bba06.png', '68379149bba0d.png', '68379149bba0e.png', '68379149bba0f.png', '68379149bba11.jpeg', 0, '2025-05-28 22:42:17'),
(137, 'Nike Court Borough Low Recraft Baby Toddler Shoes White Brown', 'Footwear', 'Nike', 'Kid Shoes', 'Kid', 'Normal', 155.00, 0.00, 'Nike Court Borough Low Recraft Baby/Toddler Shoes\r\n\r\nStart your little ones off on the right foot with the Nike Court Borough. Made for the long haul, this recrafted legend uses a combination of durable materials on the upper and outsole to achieve a classic look with an easy on and off strap for entry. Plus, a redesigned toe box and midfoot give their feet a little extra room so they can charge through the day in comfort.', '683791cebdb6a.png', '683791cebdb6f.png', '683791cebdb70.png', '683791cebdb71.png', '683791cebdb72.jpeg', 0, '2025-05-28 22:44:30'),
(138, 'Nike Revolution 7 Little Kids\' Shoes Blue', 'Footwear', 'Nike', 'Kid Shoes', 'Kid', 'Normal', 185.00, 0.00, 'Nike Revolution 7\r\nLittle Kids\' Shoes\r\nLet your kiddo flash, dash and blast into the day with the help of shoes made especially for fun. No, they\'re not loaded with sugar or toys, but they do come in awesome colors and designs that\'ll have your little one reaching for them every day of the year.', '6837924bcb9a2.png', '6837924bcb9a6.png', '6837924bcb9a7.png', '6837924bcb9a8.png', '6837924bcb9a9.jpeg', 0, '2025-05-28 22:46:35'),
(139, 'Adidas Breaknet 2.0 Kids\' Shoes Black', 'Footwear', 'Adidas', 'Kid Shoes', 'Kid', 'Promotion', 159.00, 143.10, 'INFANTS\' SNEAKERS MADE IN PART WITH RECYCLED MATERIALS.\r\nNo matter how busy the day, your little one can relax in comfort with these infants\' Breaknet 2.0 shoes from adidas. The hook-and-loop straps make them a breeze to slip on and off, while the ultra-plush one-piece EVA combined midsole and outsole keeps each step soft and steady. Whether playing make-believe on the playground or practising on the swings, these sneakers will keep little feet comfy.\r\n\r\nThis product features at least 20% recycled materials. By reusing materials that have already been created, we help to reduce waste and our reliance on finite resources and reduce the footprint of the products we make.\r\n', '683792cf126cc.png', '683792cf126d4.png', '683792cf126d6.png', '683792cf126d7.png', '683792cf126d9.jpeg', 0, '2025-05-28 22:48:47'),
(140, 'Under Armour Assert 10 Junior School Shoes Black', 'Footwear', 'Under Amour', 'School Shoes', 'Kid', 'Normal', 99.00, 0.00, 'Under Armour Assert 10 Junior School Shoes Black', '683793692f282.png', '683793692f288.png', '683793692f289.png', '683793692f28a.png', '683793692f28b.jpeg', 0, '2025-05-28 22:51:21'),
(141, 'Nike Sportswear Junior Jacket Blue FD3067-006A', 'Apparel', 'Nike', 'Jacket', 'Kid', 'Normal', 85.00, 0.00, 'Nike Sportswear\r\nBig Kids\' Tracksuit\r\nGet up and get going in a flash with the help of this matching outfit. Smooth on the outside and brushed on the inside for added comfort, the cozy knit fabric delivers a classic tracksuit look and an extra soft feel. A subtle nod to Nike heritage, the v-shaped chevron design lines on the chest set you up with a classic Windrunner look.\r\n\r\nStore your snacks and treasures in the pockets on the jacket.\r\nElastic cuffs on the pants and jacket provide a cozy, snug fit.\r\nLarge printed Futura logo on the back of the jacket helps you show off your love for the brand.', '68379400448c1.png', '68379400448c6.png', '68379400448c7.png', '68379400448c8.png', '68379400448c9.jpeg', 0, '2025-05-28 22:53:52'),
(142, 'Nike Sportswear Club Big Kids\' Full-Zip Knit Jacket Black', 'Apparel', 'Nike', 'Jacket', 'Kid', 'Normal', 129.00, 0.00, 'Nike Sportswear Club\r\nBig Kids\' Full-Zip Knit Jacket\r\nInfused with classic Nike style, this jacket has the familiar fit of our Club Fleece favorites with the soft feel of a lightweight jersey knit fabric. Slip it over your favorite tee when you want an extra layer but worry fleece is too warm.\r\n\r\nSingle knit jersey fabric is soft and lightweight for comfortable wear.\r\nTall collar helps block out cold drafts while ribbed cuffs and hem help hold the jacket in place as you move.\r\nWith just the right amount of room through the body and hips, this jacket has a classic fit that makes layering easy.', '683794565c27f.png', '683794565c286.png', '683794565c288.png', '683794565c289.png', '683794565c28b.jpeg', 0, '2025-05-28 22:55:18'),
(143, 'Nike Culture Of Basketball Big Kids\' Pullover Fleece Hoodie Red', 'Apparel', 'Nike', 'Jacket', 'Kid', 'Promotion', 205.00, 123.00, 'Nike Culture of Basketball\r\nBig Kids\' Pullover Fleece Hoodie\r\nAn all-star on and off the court, this seriously soft hoodie delivers championship-level comfort. Smooth on the outside and brushed soft on the inside, this lightweight fleece is an easy layer when you want a little extra warmth. Throw it on when staying cozy is your number 1 priorityâ€”the baggy fit makes layering a slam dunk.', '683794d3c467b.png', '683794d3c467f.png', '683794d3c4680.png', '683794d3c4681.png', '683794d3c4682.jpeg', 0, '2025-05-28 22:57:23'),
(144, 'Nike Dri-FIT Academy23 Kids\' Football Shirt Black', 'Apparel', 'Nike', 'Jersey', 'Kid', 'Normal', 59.00, 0.00, 'Nike Dri-FIT Academy23\r\nKids\' Football Shirt\r\nHere at Academy23, we\'re all about encouraging you to work on those ball skills. Whether you\'re at practice after school or out at recess, this soft, stretchy knit Shirt is a great choice to keep you cool and comfortable. Plus, the classic fit gives you room to move without distractions. Ready, set, let\'s GOAL!\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nBreathable, stretchy knit fabric feels airy and smooth.\r\nMesh side panels ramp up the breathability to keep you cool.\r\n', '683795516ff21.png', '683795516ff24.png', '683795516ff25.png', '683795516ff26.png', '683795516ff27.jpeg', 0, '2025-05-28 22:59:29'),
(145, 'Nike TRPHY23 Junior\'s Football Jersey', 'Apparel', 'Nike', 'Jersey', 'Kid', 'Normal', 79.00, 0.00, 'NIKE A JC JR B TRPHY23 0125', '683795b2810bf.png', '683795b2810c7.png', '683795b2810c9.png', '683795b2810ca.png', '683795b2810cc.jpeg', 0, '2025-05-28 23:01:06'),
(146, 'Nike ACD25 Top Junior\'s Football Jersey', 'Apparel', 'Nike', 'Jersey', 'Kid', 'Normal', 79.00, 0.00, 'NIKE A JC JR B ACD25 TOP 0125', '6837960589ca0.png', '6837960589ca4.png', '6837960589ca5.png', '6837960589ca6.png', '6837960589caf.jpeg', 0, '2025-05-28 23:02:29'),
(147, 'ADIDAS ENTRADA 22 JUNIOR JERSEY BLACK', 'Apparel', 'Adidas', 'Jersey', 'Kid', 'Normal', 65.00, 0.00, 'A MOISTURE-ABSORBING SOCCER JERSEY MADE WITH RECYCLED MATERIALS.\r\nTrain like a professional. Relax like a champion. This juniors\' soccer jersey shows off a clean, classic design with an adidas Badge of Sport on the chest. Moisture-absorbing AEROREADY will keep you dry and cool whether you\'re training on the pitch or enjoying a night out with friends.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '68379687444a7.png', '68379687444af.png', '68379687444b1.png', '68379687444b4.png', '68379687444b6.jpeg', 0, '2025-05-28 23:04:39'),
(148, 'Nike Dri-Fit Academy23 Kids\' Football Shorts Black', 'Apparel', 'Nike', 'Pant', 'Kid', 'Promotion', 79.00, 55.30, 'Nike Dri-FIT Academy23\r\nKids\' Football Shorts\r\nAre you ready to hit the field? Smooth and soft knit fabric wicks sweat to keep you cool and comfortable while you bring your A-game to every recess, practice and play date. A classic fit gives you room to move without distractions. Ready, set, let\'s GOAL!\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nTighten up the waist on the go with the quick-tie drawcord.\r\nMesh side panels ramp up the breathability to keep you cool.', '68379719790f9.png', '68379719790fd.png', '68379719790fe.png', '68379719790ff.png', '6837971979100.jpeg', 0, '2025-05-28 23:07:05'),
(149, 'NIKE JERSEY BIG KIDS\' (BOYS\') SHORTS GREY', 'Apparel', 'Nike', 'Pant', 'Kid', 'Promotion', 99.00, 69.30, 'Big Kids\' (Boys\') Shorts\r\nEVERYDAY CLASSIC.\r\nClassic = awesome. The Nike Sportswear Shorts are made with soft jersey fabric to keep you going in comfort.\r\n\r\nJersey fabric feels soft and lightweight.\r\nAn elastic waistband and drawcord provide a snug fit.', '68379785d0a1e.png', '68379785d0a22.png', '68379785d0a23.png', '68379785d0a24.png', '68379785d0a25.jpeg', 0, '2025-05-28 23:08:53'),
(150, 'Cr7 Big Kids\' Dri-Fit Academy 23 Football Shorts Black', 'Apparel', 'Nike', 'Pant', 'Kid', 'Promotion', 125.00, 87.50, 'Cristiano Ronaldo CR7\r\nBig Kids\' Dri-FIT Academy 23 Football Shorts\r\nReady to hit the field and fine-tune your skills? Made from a smooth and soft knit fabric, these shorts wick sweat away from your skin to help keep you cool and comfortable while you bring your A-game to every recess, practice and play date. A classic fit gives you room to move without distractions. Ready, set, let\'s GOAL!\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nElastic waistband with internal drawcord helps you find your perfect fit.\r\nMesh side panels ramp up the breathability to help keep you cool.\r\n', '683797dbadfb1.png', '683797dbadfba.png', '683797dbadfbc.png', '683797dbadfbd.png', '683797dbadfbe.jpeg', 0, '2025-05-28 23:10:19'),
(151, 'CR7 Big Kids\' Club Fleece Football Joggers Black', 'Apparel', 'Nike', 'Pant', 'Kid', 'Promotion', 165.00, 115.50, 'Cristiano Ronaldo CR7\r\nBig Kids\' Club Fleece Football Joggers\r\nKeep warm on the sidelines of your next game or while practicing your skills at home with these cozy joggers. Smooth on the outside and brushed soft on the inside, this lightweight fleece is an easy layer when you want a little extra warmth.\r\n\r\nElastic waistband with internal drawcord helps you find your perfect fit.\r\nRibbed cuffs let you show off your favorite sneakers.\r\n', '68379845477e1.png', '68379845477e5.png', '68379845477e6.png', '68379845477e7.png', '68379845477e8.jpeg', 0, '2025-05-28 23:12:05'),
(152, 'Nike Challenger JR B T-Bottom Black', 'Apparel', 'Nike', 'Pant', 'Kid', 'Normal', 99.00, 0.00, 'An essential for running laps, chasing down tennis balls, playing football or just good old-fashioned playing during break time, these cool, comfortable shorts help you do it all and have fun, no sweat. What makes these extra special? You can keep your phone or media player in the back drop-in pocket! Now you\'re ready to rock.\r\n\r\n', '683798a56d082.png', '683798a56d087.png', '683798a56d088.png', '683798a56d089.png', '683798a56d08a.jpeg', 0, '2025-05-28 23:13:41'),
(153, 'Nike Elemental Junior Girl Bagpack', 'Equipment', 'Nike', 'Bag', 'Kid', 'Normal', 139.00, 0.00, 'Whether it\'s rushing to class or hanging out after school with friends, this backpack has the space to tackle it all. This bag features a large side pocket for water bottles as well as a front pocket to store quick-grab items. Adjustable padded straps make wearing this bag comfortable and easy.', '6837991f022bb.png', '6837991f022bf.png', '6837991f022c0.png', '6837991f022c1.png', '6837991f022c2.', 0, '2025-05-28 23:15:43'),
(154, 'Nike Elemental Kids\' Backpack (20L) Yellow', 'Equipment', 'Nike', 'Bag', 'Kid', 'Promotion', 139.00, 97.30, 'Nike Elemental\r\nKids\' Backpack (20L)\r\nHead to class with all the gear you need to make the day great. There\'s plenty of room for your books in the main compartment while pockets on the front and sides give you a place to tuck smaller items. A detachable pencil case keeps all your writing utensils close at hand. Plus, we made the shoulder straps padded and adjustable to help make this bag comfortable to carry.\r\n\r\nZippered front pocket helps provide easy-access storage for small items.\r\nSide pockets hold a water bottle or other small essentials.\r\nDetachable pencil case gives you a space to store pens, pencils, erasers and more. Throw it inside your bag or clip it to the outside.\r\nPadded shoulder straps are adjustable for a comfortable fit.', '683799747cb4a.png', '683799747cb51.png', '683799747cb53.png', '683799747cb55.png', '683799747cb56.', 0, '2025-05-28 23:17:08'),
(155, 'Nike Classic Junior Boy Bagpack Multi', 'Equipment', 'Nike', 'Bag', 'Kid', 'New', 129.00, 0.00, 'Nike Classic Junior Boy Bagpack Multi', '683799bd905b2.png', '683799bd905b6.png', '683799bd905b7.png', '683799bd905b8.png', '683799bd905b9.', 0, '2025-05-28 23:18:21'),
(156, 'Nike Elemental Kids\' Backpack (20L)', 'Equipment', 'Nike', 'Bag', 'Kid', 'New', 139.00, 0.00, 'Nike Elemental\r\nKids\' Backpack (20L)\r\nWhether it\'s rushing to class or hanging out after school with friends, this backpack has the space to tackle it all. This bag features a large side pocket for water bottles as well as a front pocket to store quick-grab items. Adjustable padded straps make wearing this bag comfortable and easy.\r\n\r\nConnected by a secure clip, the bag also comes with a large pencil case.\r\nDouble zippers on main compartment allow for easy opening.\r\nHaul loop gives you an alternate carrying option.', '683799fe4139f.png', '683799fe413a7.png', '683799fe413a9.png', '683799fe413ab.png', '683799fe413ad.', 0, '2025-05-28 23:19:26'),
(157, 'Nike Club Big Kids\' Cap', 'Equipment', 'Nike', 'Cap', 'Kid', 'Normal', 69.00, 0.00, 'Nike Club\r\nBig Kids\' Cap\r\nWhile the sun may make playing outside more fun, it can be distracting when you\'re up to bat, playing on the monkey bars or skating with friends. Shield your eyes with the curved bill on this Nike Club cap. A googly-eyed Swoosh logo adds a touch of fun while an adjustable strap and mid-depth design make this hat ideal for everyday wear.', '68379a4c22d1e.png', '68379a4c22d28.png', '68379a4c22d2a.png', '68379a4c22d2b.png', '68379a4c22d2d.', 0, '2025-05-28 23:20:44'),
(158, 'Nike Apex Kids\' Futura Bucket Hat Black', 'Equipment', 'Nike', 'Cap', 'Kid', 'Normal', 69.00, 0.00, 'Nike Apex\r\nKids\' Futura Bucket Hat\r\nEnjoy the sun and add some fun to your look with the Nike Apex Bucket Hat. As easy to throw on as it is to pack away when you\'re not wearing it, this lightweight hat keeps you comfortable whenever the sun\'s out and adventure is on the menu!\r\n\r\nApex bucket hats all have a mid depth and 360-degree coverage.\r\nEasy-to-store unstructured design packs away flat.\r\n', '68379a9741a95.png', '68379a9741a9d.png', '68379a9741a9f.png', '68379a9741aa0.png', '68379a9741aa2.', 0, '2025-05-28 23:21:59'),
(159, 'Nike Everyday Kids\' Cushioned Ankle Socks Black (6 Pairs)', 'Equipment', 'Nike', 'Sock', 'Kid', 'Normal', 69.00, 0.00, 'Nike Everyday\r\nKids\' Cushioned Ankle Socks (6 Pairs)\r\nMADE FOR EVERYDAY PLAY.\r\nFrom playtime to game day, the Nike Everyday Socks keep you going. They\'re powered by Dri-FIT technology and mesh at the top and cushioning underfoot to keep your feet cool and comfy with every move.\r\n\r\nDri-FIT technology helps you stay dry and comfortable.\r\nCushioning underfoot means targeted comfort.\r\nBreathable mesh at the top helps feet stay cool.', '68379b0396ab9.png', '68379b0396abe.png', '68379b0396abf.png', '68379b0396ac0.png', '68379b0396ac1.', 0, '2025-05-28 23:23:47'),
(160, 'Nike Downshifter 13 Women\'s Road Running Shoes Black', 'Footwear', 'Nike', 'Running', 'Women', 'Promotion', 255.00, 229.50, 'Nike Downshifter 13\r\nWomen\'s Road Running Shoes\r\nWhether you re excitedly starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a new, soft, upper and uncompromised cushion and durability, it helps you find that extra gear or simply take that expectant first stride toward meeting your mileage needs.', '68379bbb40aec.png', '68379bbb40af0.png', '68379bbb40af1.png', '68379bbb40af2.png', '68379bbb40af3.jpeg', 0, '2025-05-28 23:26:51'),
(161, 'ADIDAS DURAMO RC WOMEN\'S RUNNING SHOES BLUE', 'Footwear', 'Adidas', 'Running', 'Women', 'Normal', 249.00, 0.00, 'LIGHTWEIGHT RUNNING SHOES MADE IN PART WITH RECYCLED MATERIALS.\r\nRun a little faster. Run a little further. With the light, soft and supportive feel of these adidas running shoes, you\'re set to take the next step in your running journey. EVA cushioning brings comfort to training runs and race day, while an Adiwear outsole grips to the track or pavement. Made with a series of recycled materials, this upper features at least 50% recycled content. This product represents just one of our solutions to help end plastic waste.\r\n', '68379c2911887.png', '68379c291188e.png', '68379c2911890.png', '68379c2911891.png', '68379c2911893.jpeg', 0, '2025-05-28 23:28:41'),
(162, 'ADIDAS DURAMO RC WOMEN\'S RUNNING SHOES BLACK', 'Footwear', 'Adidas', 'Running', 'Women', 'Normal', 249.00, 0.00, 'LIGHTWEIGHT RUNNING SHOES MADE IN PART WITH RECYCLED MATERIALS.\r\nRun a little faster. Run a little further. With the light, soft and supportive feel of these adidas running shoes, you\'re set to take the next step in your running journey. EVA cushioning brings comfort to training runs and race day, while an Adiwear outsole grips to the track or pavement. Made with a series of recycled materials, this upper features at least 50% recycled content. This product represents just one of our solutions to help end plastic waste.', '68379c8cab9ea.png', '68379c8cab9f0.png', '68379c8cab9f1.png', '68379c8cab9f2.png', '68379c8cab9f3.jpeg', 0, '2025-05-28 23:30:20'),
(163, 'Asics Versablast 3 Women\'s Running Shoes Beige', 'Footwear', 'Asics', 'Running', 'Women', 'Promotion', 299.00, 269.10, 'Asics Versablast 3 Women\'s Running Shoes Beige', '68379cfd54903.png', '68379cfd54907.png', '68379cfd54908.png', '68379cfd54909.png', '68379cfd5490a.jpeg', 0, '2025-05-28 23:32:13'),
(164, 'Nike Downshifter 13 Women\'s Road Running Shoes', 'Footwear', 'Nike', 'Running', 'Women', 'New', 255.00, 0.00, 'Nike Downshifter 13\r\nWomen\'s Road Running Shoes\r\nWhether you’re starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a revamped upper and cushion and durability, it helps you find that extra gear or take that first stride toward chasing down your goals.\r\n', '68379d4ee7fe7.png', '68379d4ee7fef.png', '68379d4ee7ff1.png', '68379d4ee7ff2.png', '68379d4ee7ff4.jpeg', 0, '2025-05-28 23:33:34'),
(165, 'Nike Downshifer 13 Women\'s Running Shoes', 'Footwear', 'Nike', 'Running', 'Women', 'Normal', 255.00, 0.00, 'Whether you’re starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a revamped upper and cushion and durability, it helps you find that extra gear or take that first stride toward chasing down your goals.\r\n\r\nShown: Phantom/Summit White/Sail/Elemental Pink\r\nStyle: FD6476-004', '68379da946213.png', '68379da946217.png', '68379da946218.png', '68379da946219.png', '68379da94621a.jpeg', 0, '2025-05-28 23:35:05'),
(166, 'PUMA Scend Pro Women\'s Running Purple', 'Footwear', 'Puma', 'Running', 'Women', 'New', 229.00, 0.00, 'Step into a new world of support and guidance with the ForeverRun Nitro™. Perfect for all runners and especially beginners, this shoe packs the tried-and-true RUNGUIDE system, designed to keep your feet running in the right position. We\'ve combined a secure engineered mesh upper with an asymmetrical heel counter, to give you softness, comfort and stability. Lace up and start your running adventure with confidence.', '68379dfd045a5.png', '68379dfd045ad.png', '68379dfd045af.png', '68379dfd045b1.png', '68379dfd045b4.jpeg', 0, '2025-05-28 23:36:29'),
(167, 'Asics Gel-Nimbus 26 Women\'s Running Shoes Grey SKU: 1012B601-020-6', 'Footwear', 'Asics', 'Running', 'Women', 'Promotion', 699.00, 489.30, 'The GEL-NIMBUS 26 shoe\'s soft cushioning properties help you feel like you\'re landing on clouds. This design is revamped to help create a softer and smoother running experience.\r\n\r\nA soft engineered knit upper comfortably wraps your foot while supplying advanced ventilation. Additionally, the knit tongue and collar help provide a soft and supportive feel.\r\n\r\nBy using FF BLAST PLUS ECO cushioning, this trainer creates a lighter and softer cushioning experience with less of an impact on the environment.\r\n\r\nLastly, ASICSGRIP outsole rubber helps create better traction, improved softness, and advanced durability.', '68379e6374a61.png', '68379e6374a65.png', '68379e6374a66.png', '68379e6374a67.png', '68379e6374a68.jpeg', 0, '2025-05-28 23:38:11'),
(169, 'PUMA Velocity Nitro 3 Women\'s Running Shoes Green', 'Footwear', 'Puma', 'Running', 'Women', 'Promotion', 599.00, 299.50, 'The PUMA Velocity NITRO 3 with this shoe, we\'re taking industry-best and bringing it to you. These kicks are all about speed and comfort thanks to advanced NITRO technology, offering superior responsiveness and cushioning in a lightweight package that feels like you\'re running on clouds. Plus, with the heel spoiler, you not only get a sleek look but you\'ll also benefit from enhanced stability for confident strides. Get ready to up your running game with the Velocity NITRO 3. It\'s not just a shoe; it\'s a statement.\r\n\r\nNITRO Advanced technology providing superior responsiveness and cushioning in a lightweight package. PUMAGRIP: Lightweight rubber outsole provides grip for your most powerful toe-off.', '68379f85c49df.png', '68379f85c5912.png', '68379f85c6371.png', '68379f85c72cd.png', '68379f85c84b8.jpeg', 0, '2025-05-28 23:41:16'),
(170, 'PUMA Team Oversized Crew Women\'s Jacket Brown', 'Apparel', 'Puma', 'Jacket', 'Women', 'Normal', 329.00, 0.00, 'PUMA has been defining sports since 1948, and this PUMA Team oversized crew is a retro-inspired gem that proudly shows that heritage. This oversized women\'s crew features embroidered branding on the left side with a graphic embroidery outline and satin infill on the back for that heritage look.', '6837a0589eeac.png', '6837a0589eeb0.png', '6837a0589eeb1.png', '6837a0589eeb2.png', '6837a0589eeb3.jpeg', 0, '2025-05-28 23:46:32'),
(171, 'PUMA Team Oversized Crew Women\'s Jacket Black', 'Apparel', 'Puma', 'Jacket', 'Women', 'Promotion', 309.00, 247.20, 'PUMA has been defining sports since 1948, and this PUMA Team oversized crew is a retro-inspired gem that proudly shows that heritage. This oversized women\'s crew features embroidered branding on the left side with a graphic embroidery outline and satin infill on the back for that heritage look.', '6837a0a3f3f0f.png', '6837a0a3f3f15.png', '6837a0a3f3f17.png', '6837a0a3f3f19.png', '6837a0a3f3f1b.jpeg', 0, '2025-05-28 23:47:47'),
(172, 'PUMA HER Women\'s Full-Zip Hoodie', 'Apparel', 'Puma', 'Jacket', 'Women', 'Normal', 359.00, 0.00, 'We\'ve taken our Classics and updated it for HER. This relaxed-fitting hoodie is cut for the woman on the go and is built with a soft, comfortable fabric to handle the season.', '6837a0fa79f7e.png', '6837a0fa79f82.png', '6837a0fa79f83.png', '6837a0fa79f84.png', '6837a0fa79f85.jpeg', 0, '2025-05-28 23:49:14'),
(173, 'PUMA Power Hoody Women\'s Jacket', 'Apparel', 'Puma', 'Jacket', 'Women', 'Promotion', 329.00, 263.20, 'We\'ve added a dose of edge to our traditional athletic silhouettes in our PUMA POWER collection. Unleash your full power in this hoodie, which has our standard, athletic fit, French terry material, and some cool colorblocked details with a PUMA graphic. Rep that casual and sporty look.', '6837a1506c64d.png', '6837a1506c653.png', '6837a1506c654.png', '6837a1506c655.png', '6837a1506c657.jpeg', 0, '2025-05-28 23:50:40'),
(174, 'PUMA ESS Full-Zip Women\'s Hoodie', 'Apparel', 'Puma', 'Jacket', 'Women', 'Normal', 239.00, 0.00, 'Play and move freely. This full-zip hoodie delivers warmth and comfort for everyday pursuits. A kangaroo pocket provides extra warmth while an adjustable hood allows for customisable coverage.', '6837a1a6c63e7.png', '6837a1a6c63ef.png', '6837a1a6c63f1.png', '6837a1a6c63f3.png', '6837a1a6c63f5.jpeg', 0, '2025-05-28 23:52:06'),
(175, 'PUMA Train All Day Big Cat Women\'s Jacket Black', 'Apparel', 'Puma', 'Jacket', 'Women', 'New', 209.30, 0.00, 'Packed with moisture control tech and emblazoned with the iconic Big Cat graphics, our latest zip-up training jacket is a must-have when you\'re up against a cold weather warm up or cool down.', '6837a1f63d7fc.png', '6837a1f63d801.png', '6837a1f63d802.png', '6837a1f63d803.png', '6837a1f63d804.jpeg', 0, '2025-05-28 23:53:26'),
(176, 'NIKE GYM TOTE (24L) BLACK', 'Equipment', 'Nike', 'Bag', 'Women', 'Normal', 175.00, 0.00, 'Nike\r\nGym Tote (24L)\r\nThis tote bag is as multidimensional as you are. No matter what your fitness journey is, the Nike Gym Tote is made to handle it. Multiple deep pockets line the inside of this bag, both open and zippered for extra security. The front and back features pockets for the small things you want easy access to. And, the size of this tote is deceptiveÃ¢â‚¬â€the sides feature expanding fabric panels for extra space whenever you need it.\r\n\r\nPockets on the outside have snap on 1 side and hook-and-loop closure on the other.\r\nProvides 2 handle optionsÃ¢â‚¬â€long shoulder tote and short grab-and-go.', '6837a2f11d8e2.png', '6837a2f11d8ec.png', '6837a2f11d8ee.png', '6837a2f11d8ef.png', '6837a2f11d8f0.', 0, '2025-05-28 23:57:37'),
(177, 'Nike Sportswear Women\'s Futura 365 Crossbody Bag (3L) Green', 'Equipment', 'Nike', 'Bag', 'Women', 'Normal', 99.00, 0.00, 'Women\'s Futura 365 Crossbody Bag (3L)\r\nA clean design that fits your style and your essentials, the Nike Sportswear Crossbody Bag features a zipper back pocket and magnetic flap close. It\'s made from fabric that contains a blend of at least 65% recycled fibers.\r\n\r\nAn internal stash pocket and zipper back pocket keep your keys, cards and phone organized and stored securely.\r\nMessenger bag-style magnetic flap closure provides quick access to your essentials.\r\nPremium shoulder strap provides styling and fit options.\r\n', '6837a341a219d.png', '6837a341a21a2.png', '6837a341a21a4.png', '6837a341a21a5.png', '6837a341a21a6.', 0, '2025-05-28 23:58:57'),
(178, 'PUMA Phase Backpack Purple', 'Equipment', 'Puma', 'Bag', 'Women', 'Normal', 85.00, 0.00, 'The PUMA Phase backpack\'s classic silhouette and statement branding make for a long-standing fave. This easy-to-wear look stands the test of time.', '6837a393b8aeb.png', '6837a393b8af1.png', '6837a393b8af2.png', '6837a393b8af3.png', '6837a393b8af5.', 0, '2025-05-29 00:00:19'),
(179, 'NIKE GYM TOTE (28L) PEACH', 'Equipment', 'Nike', 'Bag', 'Women', 'Promotion', 175.00, 122.50, 'Gym Tote (28L)\r\nThis tote bag is as multidimensional as you are. No matter what your fitness journey is, the Nike Gym Tote is made to handle it. Multiple deep pockets line the inside of this bag, both open and zippered for extra security. The front and back features pockets for the small things you want easy access to. And, the size of this tote is deceptive - the sides feature expanding fabric panels for extra space whenever you need it.\r\n\r\nPockets on the outside have snap on 1 side and hook-and-loop closure on the other.\r\nProvides 2 handle options - long shoulder tote and short grab-and-go.', '6837a3e012d29.png', '6837a3e012d2e.png', '6837a3e012d2f.png', '6837a3e012d30.png', '6837a3e012d31.', 0, '2025-05-29 00:01:36'),
(180, 'Adidas Gym High-Intensity Pouch GREY', 'Equipment', 'Adidas', 'Bag', 'Women', 'New', 149.00, 0.00, 'Functionality keeps its cool in this compact bag from adidas. Designed to store essentials easily on the go, it sports a hidden front zip pocket, a top zip closure and multiple ways to organise inside. Wear it over one shoulder or across your body with the detachable, adjustable shoulder strap. Or use the handles for grab-and-go mobility that will never slow you down.\r\n\r\nMade with a series of recycled materials, and at least 40 percent recycled content, this product represents just one of our solutions to help end plastic waste.', '6837a42aa94fd.png', '6837a42aa9502.png', '6837a42aa9504.png', '6837a42aa9505.png', '6837a42aa9506.', 0, '2025-05-29 00:02:50'),
(181, 'Nike Mini Shoebox Crossbody Bag (3L) FN3059-010', 'Equipment', 'Nike', 'Bag', 'Women', 'New', 149.00, 0.00, 'Nike\r\nMini Shoebox Crossbody Bag (3L)\r\nLove our orange shoeboxes? Us too. While we don\'t recommend you use them to tote your stuff around for the day, you can capture their iconic style with this mini crossbody bag. Its zippered compartment features plenty of space for your phone, wallet, keys and more while 2 interior pockets provide added organization for small essentials. A haul loop and adjustable shoulder strap give you multiple styling and carrying options.', '6837a47a9f9f5.png', '6837a47a9f9f9.png', '6837a47a9f9fa.png', '6837a47a9f9fb.png', '6837a47a9f9fc.', 0, '2025-05-29 00:04:10'),
(182, 'PUMA Phase Color Block Women\'s Bagpack Blue', 'Equipment', 'Puma', 'Bag', 'Women', 'Normal', 89.00, 0.00, 'PUMA Phase Color Block Women\'s Bagpack Blue', '6837a4fe01943.png', '6837a4fe02458.png', '6837a4fe032d8.png', '6837a4fe044d1.png', '6837a4bde72f2.', 0, '2025-05-29 00:05:17'),
(183, 'ADIDAS Women\'s Bagpack Classic 3S FG', 'Equipment', 'Adidas', 'Bag', 'Women', 'New', 129.00, 0.00, 'ADIDAS Women\'s Bagpack Classic 3S FG', '6837a53e77bf8.png', '6837a53e77bfc.png', '6837a53e77bfd.png', '6837a53e77bfe.png', '6837a53e77bff.', 0, '2025-05-29 00:07:26'),
(184, 'PUMA Phase Packable Shopper Women\'s Bag Pink', 'Equipment', 'Puma', 'Bag', 'Women', 'Normal', 69.00, 0.00, 'They say good things come in small packages, and this shopper is no exception. It folds away, so you can carry a reusable bag with you at all times with no extra fuss.\r\n\r\nColour: Peach Smoothie', '6837a6019e271.png', '6837a6019e276.png', '6837a6019e277.png', '6837a6019e278.png', '6837a6019e279.', 0, '2025-05-29 00:10:41'),
(185, 'Under Armour Hustle Bagpack Blue', 'Equipment', 'Under Amour', 'Bag', 'Women', 'Normal', 119.00, 0.00, 'Under Armour Hustle Bagpack Blue', '6837a65a03626.png', '6837a65a0362b.png', '6837a65a0362c.png', '6837a65a0362d.png', '6837a65a0362e.', 0, '2025-05-29 00:12:10'),
(186, 'ADIDAS THIN LINEAR BALLERINA SOCKS 2 PAIRS WHITE', 'Equipment', 'Adidas', 'Sock', 'Women', 'Normal', 20.30, 0.00, 'These adidas ballerina socks can\'t be seen inside your low-top sneakers or loafers. Available in packs of two, these no-show socks are made from a blend of cotton and polyester for a soft, feather-light feel. They keep feet airy and comfortable even in balmy weather.\r\n', '6837a706c2724.png', '6837a706c272a.png', '6837a706c272c.png', '6837a706c272d.png', '6837a706c272f.', 0, '2025-05-29 00:15:02'),
(187, 'Adidas Performance Light Low Socks 3 Pairs WHITE', 'Equipment', 'Adidas', 'Sock', 'Women', 'New', 59.00, 0.00, 'Keep your feet in the game as you push your limits. These adidas low-cut training socks feature AEROREADY to manage moisture, so you stay dry in the gym, on the court or on the trail. Soft, stretchy and thin, they offer a natural feel and fit. Three pairs per pack.\r\n\r\nA minimum of 50% of this product is a blend of recycled and renewable materials.', '6837a76372c53.png', '6837a76372c57.png', '6837a76372c58.png', '6837a76372c59.png', '6837a76372c5a.', 0, '2025-05-29 00:16:35'),
(188, 'Asics Sports Sock Unisex 3 Pack Black 3033B959-001', 'Equipment', 'Asics', 'Sock', 'Women', 'New', 59.00, 0.00, 'Asics Sports Sock Unisex 3 Pack Black 3033B959-001', '6837a7a9503d6.png', '6837a7a9503da.png', '6837a7a9503db.png', '6837a7a9503dc.png', '6837a7a9503dd.', 0, '2025-05-29 00:17:45'),
(189, 'Asics Sports Sock Unisex 3 Pack White 3033B959-100', 'Equipment', 'Asics', 'Sock', 'Women', 'Normal', 59.00, 0.00, 'Asics Sports Sock Unisex 3 Pack White 3033B959-100', '6837a7f6ebca6.png', '6837a7f6ebcaa.png', '6837a7f6ebcab.png', '6837a7f6ebcac.png', '6837a7f6ebcad.', 0, '2025-05-29 00:19:02'),
(191, 'Al Men Football Jersey Black', 'Footwear', 'Nike', 'Boot', 'Men', 'Normal', 100.00, 0.00, '1', '6839382aa3e2e.png', '6839382aa3e31.png', '6839382aa3e32.png', '6839382aa3e33.png', '6839382aa3e34.jpg', 1, '2025-05-30 04:46:34'),
(192, 'Nike', 'Equipment', 'Nike', 'Bag', 'Men', 'Promotion', 120.00, 100.00, '1', '683e571fcac36.png', '683e571fcac39.png', '683e571fcac3b.png', '683e571fcac3c.png', '683e571fcac3d.', 1, '2025-06-03 01:59:59'),
(193, 'adidas', 'Apparel', 'Adidas', 'Jersey', 'Men', 'Promotion', 100.00, 10.00, '2', '6840030ed485d.png', '6840030ed50db.png', '6840030ed588e.png', '6840030ed5ed4.png', '684002ceb7efb.jpg', 1, '2025-06-04 08:24:46'),
(194, 'Asics Gel-Sonoma7 Women\'s Court Shoes Black', 'Footwear', 'Asics', 'Court', 'Women', 'Normal', 164.00, 0.00, 'Asics Gel-SONOMA7 Women\'s Outdoor Shoes Black', '6841ecd26621e.png', '6841ecd266223.png', '6841ecd266225.png', '6841ecd266227.png', '6841ecd26622c.jpeg', 0, '2025-06-05 19:15:30'),
(195, 'Asics Trial Scout Women\'s Court Shoes', 'Footwear', 'Asics', 'Court', 'Women', 'Normal', 249.00, 0.00, 'Asics Trial Scout Women\'s Court Shoes', '6841ed34d5d1c.png', '6841ed34d5d21.png', '6841ed34d5d22.png', '6841ed34d5d23.png', '6841ed34d5d25.jpeg', 0, '2025-06-05 19:17:08'),
(196, 'LOTTO MAXIMO MEN\'S COURT SHOES BLUE', 'Footwear', 'Lotto', 'Court', 'Men', 'Normal', 149.00, 0.00, 'LOTTO MAXIMO MEN\'S COURT SHOES BLUE', '6841edf99d602.png', '6841edf99d609.png', '6841edf99d60b.png', '6841edf99d60d.png', '6841edf99d610.jpeg', 0, '2025-06-05 19:20:25'),
(197, 'LOTTO MAXIMO WOMEN\'S COURT SHOES BLUE', 'Footwear', 'Lotto', 'Court', 'Women', 'Normal', 149.00, 0.00, 'LOTTO MAXIMO WOMEN\'S COURT SHOES BLUE', '6841ee28bf6d7.png', '6841ee28bf6de.png', '6841ee28bf6e0.png', '6841ee28bf6e1.png', '6841ee28bf6e4.jpeg', 0, '2025-06-05 19:21:12'),
(198, 'Asics Gel-Rocket 11 Men\'s Court Shoes White', 'Footwear', 'Asics', 'Court', 'Men', 'Normal', 299.00, 0.00, 'PRODUCT STORY The GEL-ROCKET 11 style is a multi-purpose indoor court shoe that offers good stability and easy movement for a range of court athletes. ? This shoe is designed with a flexible upper construction that\'s breathable and more comfortable.? A TPU TRUSSTIC application in the midsole is designed to help resist over-twisting and improve stability during quick transitions. ? Its wrap-up outsole and outrigger are functional support components that are helpful during side-to-side movements. Meanwhile, the outsole\'s flexion grooves provide better flexibility to help you move more freely on the court', '6841eebbdc0e3.png', '6841eebbdc0eb.png', '6841eebbdc0ee.png', '6841eebbdc0ef.png', '6841eebbdc0f0.jpeg', 0, '2025-06-05 19:23:39');
INSERT INTO `product` (`product_id`, `product_name`, `product_type`, `brand`, `product_categories`, `gender`, `status`, `price`, `discount_price`, `description`, `product_img1`, `product_img2`, `product_img3`, `product_img4`, `size_chart`, `deleted`, `create_at`) VALUES
(199, 'Asics Gel-Rocket 11 Women\'s Court Shoes White', 'Footwear', 'Asics', 'Court', 'Women', 'Normal', 299.00, 0.00, 'PRODUCT STORY The GEL-ROCKET 11 style is a multi-purpose indoor court shoe that offers good stability and easy movement for a range of court athletes. ? This shoe is designed with a flexible upper construction that\'s breathable and more comfortable.? A TPU TRUSSTIC application in the midsole is designed to help resist over-twisting and improve stability during quick transitions. ? Its wrap-up outsole and outrigger are functional support components that are helpful during side-to-side movements. Meanwhile, the outsole\'s flexion grooves provide better flexibility to help you move more freely on the court', '6841eef12f7b2.png', '6841eef12f7b8.png', '6841eef12f7b9.png', '6841eef12f7ba.png', '6841eef12f7bc.jpeg', 0, '2025-06-05 19:24:33'),
(200, 'LOTTO MAXIMO MEN\'S COURT SHOES BLACK', 'Footwear', 'Lotto', 'Court', 'Men', 'Normal', 119.00, 0.00, 'LOTTO MAXIMO MEN\'S COURT SHOES BLACK', '6841ef8219913.png', '6841ef8219919.png', '6841ef821991b.png', '6841ef821991d.png', '6841ef821991f.jpeg', 0, '2025-06-05 19:26:58'),
(201, 'LOTTO MAXIMO WOMEN\'S COURT SHOES BLACK', 'Footwear', 'Lotto', 'Court', 'Women', 'Normal', 119.00, 0.00, 'LOTTO MAXIMO WOMEN\'S COURT SHOES BLACK', '6841efaa313c2.png', '6841efaa313ca.png', '6841efaa313cc.png', '6841efaa313ce.png', '6841efaa313d1.jpeg', 0, '2025-06-05 19:27:38'),
(202, 'Asics Gel-Rocket 11 Men\'s Court Shoes White 1071A091-105', 'Footwear', 'Asics', 'Court', 'Men', 'Normal', 299.00, 0.00, 'The GEL-ROCKET™ 11 style is a multi-purpose indoor court shoe that offers good stability and easy movement for a range of court athletes. ​\r\n\r\nThis shoe is designed with a flexible upper construction that\'s breathable and more comfortable.​\r\n\r\nA TPU TRUSSTIC™ application in the midsole is designed to help resist over-twisting and improve stability during quick transitions. ​\r\n\r\nIts wrap-up outsole and outrigger are functional support components that are helpful during side-to-side movements. Meanwhile, the outsole\'s flexion grooves provide better flexibility to help you move more freely on the court.', '6841f032a8804.png', '6841f032a8809.png', '6841f032a880a.png', '6841f032a880b.png', '6841f032a880c.jpeg', 0, '2025-06-05 19:29:54'),
(203, 'Asics Gel-Rocket 11 Women\'s Court Shoes White 1071A091-105', 'Footwear', 'Asics', 'Court', 'Women', 'Normal', 299.00, 0.00, 'The GEL-ROCKET™ 11 style is a multi-purpose indoor court shoe that offers good stability and easy movement for a range of court athletes. ​\r\n\r\nThis shoe is designed with a flexible upper construction that\'s breathable and more comfortable.​\r\n\r\nA TPU TRUSSTIC™ application in the midsole is designed to help resist over-twisting and improve stability during quick transitions. ​\r\n\r\nIts wrap-up outsole and outrigger are functional support components that are helpful during side-to-side movements. Meanwhile, the outsole\'s flexion grooves provide better flexibility to help you move more freely on the court.', '6841f060c3056.png', '6841f060c305d.png', '6841f060c3060.png', '6841f060c3062.png', '6841f060c3066.jpeg', 0, '2025-06-05 19:30:40'),
(204, 'LOTTO SVOLTA MEN\'S COURT SHOES PFCS22002-GRY', 'Footwear', 'Lotto', 'Court', 'Men', 'Promotion', 149.00, 104.30, 'LOTTO SVOLTA MEN\'S COURT SHOES PFCS22002-GRY', '6841f0e2959e3.png', '6841f0e2959e7.png', '6841f0e2959e8.png', '6841f0e2959e9.png', '6841f0e2959ea.jpeg', 0, '2025-06-05 19:32:50'),
(205, 'LOTTO SVOLTA WOMEN\'S COURT SHOES PFCS22002-GRY', 'Footwear', 'Lotto', 'Court', 'Women', 'Promotion', 149.00, 104.30, 'LOTTO SVOLTA WOMEN\'S COURT SHOES PFCS22002-GRY', '6841f114a9560.png', '6841f114a9568.png', '6841f114a9569.png', '6841f114a956a.png', '6841f12017191.jpeg', 0, '2025-06-05 19:33:40'),
(206, 'Asics Blade Ff Men\'s Court Shoes White', 'Footwear', 'Asics', 'Court', 'Men', 'New', 449.00, 0.00, 'Asics Blade Ff Men\'s Court Shoes White', '6841f1b040222.png', '6841f1b040226.png', '6841f1b040227.png', '6841f1b040228.png', '6841f1b040229.jpeg', 0, '2025-06-05 19:36:16'),
(207, 'Asics Blade Ff Women\'s Court Shoes White', 'Footwear', 'Asics', 'Court', 'Women', 'New', 449.00, 0.00, 'Asics Blade Ff Women\'s Court Shoes White', '6841f1d96eca9.png', '6841f1d96ecaf.png', '6841f1d96ecb1.png', '6841f1d96ecb2.png', '6841f1d96ecb4.jpeg', 0, '2025-06-05 19:36:57'),
(208, 'Asics Upcourt 6 Men\'s Court Shoes Navy', 'Footwear', 'Asics', 'Court', 'Men', 'New', 249.00, 0.00, 'Asics Upcourt 6 Men\'s Court Shoes Navy', '6841f26ce2171.png', '6841f26ce217b.png', '6841f26ce217d.png', '6841f26ce217f.png', '6841f26ce2182.jpeg', 0, '2025-06-05 19:39:24'),
(209, 'Asics Upcourt 6 Women\'s Court Shoes Navy', 'Footwear', 'Asics', 'Court', 'Women', 'New', 249.00, 0.00, 'Asics Upcourt 6 Women\'s Court Shoes Navy', '6841f29b2358c.png', '6841f29b23592.png', '6841f29b23594.png', '6841f29b23596.png', '6841f29b23597.jpeg', 0, '2025-06-05 19:40:11'),
(210, 'Umbro Linear Print Men\'s Jersey Green', 'Apparel', 'Umbro', 'Jersey', 'Men', 'New', 119.00, 0.00, 'Umbro Linear Print Men\'s Jersey Green', '6841f37d0b727.png', '6841f37d0b732.png', '6841f37d0b735.png', '6841f37d0b737.png', '6841f37d0b73a.jpeg', 0, '2025-06-05 19:43:57'),
(211, 'Umbro Linear Print Women\'s Jersey Green', 'Apparel', 'Umbro', 'Jersey', 'Women', 'New', 119.00, 0.00, 'Umbro Linear Print Women\'s Jersey Green', '6841f3a962da8.png', '6841f3a962daf.png', '6841f3a962db1.png', '6841f3a962db2.png', '6841f3a962db4.jpeg', 0, '2025-06-05 19:44:41'),
(212, 'Umbro Linear Print Men\'s Jersey Blue', 'Apparel', 'Umbro', 'Jersey', 'Men', 'Normal', 119.00, 0.00, 'Umbro Linear Print Men\'s Jersey Blue', '6841f44e20ed2.png', '6841f44e20ed7.png', '6841f44e20ed8.png', '6841f44e20ed9.png', '6841f44e20eda.jpeg', 0, '2025-06-05 19:47:26'),
(213, 'Umbro Linear Print Women\'s Jersey Blue', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Normal', 119.00, 0.00, 'Umbro Linear Print Women\'s Jersey Blue', '6841f46d00ecb.png', '6841f46d00ecf.png', '6841f46d00ed0.png', '6841f46d00ed1.png', '6841f46d00ed2.jpeg', 0, '2025-06-05 19:47:57'),
(214, 'PUMA TEAMRISE MEN\'S FOOTBALL JERSEY RED 704932 01', 'Apparel', 'Puma', 'Jersey', 'Men', 'Normal', 75.00, 0.00, '- Puma teamRISE crewneck jersey, made of fabric with high performance materials. The teamRISE collection combines cutting-edge functionality and contemporary design.\r\n- Breathable dryingCELL for a dry feeling and comfort at all times, while the multi-tone contrast and details on the sleeves.\r\n- PUMA Cat logo on the chest in the center with heat transfer.\r\n- Regular fit\r\n- 100% polyester', '6841f5139a261.png', '6841f5139a26a.png', '6841f5139a26c.png', '6841f5139a26d.png', '6841f5139a26e.jpeg', 0, '2025-06-05 19:50:43'),
(215, 'PUMA TEAMRISE WOMEN\'S FOOTBALL JERSEY RED 704932 01', 'Apparel', 'Puma', 'Jersey', 'Women', 'Normal', 75.00, 0.00, '- Puma teamRISE crewneck jersey, made of fabric with high performance materials. The teamRISE collection combines cutting-edge functionality and contemporary design.\r\n- Breathable dryingCELL for a dry feeling and comfort at all times, while the multi-tone contrast and details on the sleeves.\r\n- PUMA Cat logo on the chest in the center with heat transfer.\r\n- Regular fit\r\n- 100% polyester', '6841f53fc9b8b.png', '6841f53fc9b93.png', '6841f53fc9b95.png', '6841f53fc9b96.png', '6841f53fc9b97.jpeg', 0, '2025-06-05 19:51:27'),
(216, 'Umbro Men\'s Jersey Orange', 'Apparel', 'Umbro', 'Jersey', 'Men', 'Promotion', 139.00, 111.20, 'Umbro Men\'s Jersey Orange', '6841f5b4b384f.png', '6841f5b4b3854.png', '6841f5b4b3855.png', '6841f5b4b3856.png', '6841f5b4b3857.jpeg', 0, '2025-06-05 19:53:24'),
(217, 'Umbro Women\'s Jersey Orange', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Promotion', 139.00, 111.20, 'Umbro Women\'s Jersey Orange', '6841f5e57e3a7.png', '6841f5e57e3ac.png', '6841f5e57e3ae.png', '6841f5e57e3af.png', '6841f5e57e3b0.jpeg', 0, '2025-06-05 19:54:13'),
(218, 'PUMA WOMEN ICONIC T7 MID-RISE WOMEN\'S LEGGINGS LEGGING BLACK', 'Apparel', 'Puma', 'Legging', 'Women', 'Normal', 109.00, 0.00, 'Give your curves the ultimate hug in the sleek and sporty Iconic T7 Leggings, which feature track-inspired side seam striping and a tight but comfortable fit.', '6841f67458e3d.png', '6841f67458e42.png', '6841f67458e43.png', '6841f67458e44.png', '6841f67458e46.jpeg', 0, '2025-06-05 19:56:36'),
(219, 'PUMA Essentials+ Metallic Women\'s Legging Black', 'Apparel', 'Puma', 'Legging', 'Women', 'New', 89.00, 0.00, 'Three reasons leggings are the best garment ever created: 1. They always fit. 2. You feel completely free. 3. They go with virtually anything. These cotton-blend Essentials+ Metallic Women\'s Leggings are no exception: Available in a tight fit, they accentuate your body in all the right places and add a dash of athleisurewear style to your outfit thanks to the metallic PUMA No. 1 Logo wrapped around the left ankle.', '6841f6dd4a51c.png', '6841f6dd4a523.png', '6841f6dd4a525.png', '6841f6dd4a526.png', '6841f6dd4a528.jpeg', 0, '2025-06-05 19:58:21'),
(220, 'Nike Clsc Gx Hr Women\'s Lifestyle Legging Navy', 'Apparel', 'Nike', 'Legging', 'Women', 'New', 159.00, 0.00, 'Nike Clsc Gx Hr Women\'s Lifestyle Legging Navy', '6841f758c0fb4.png', '6841f758c0fbf.png', '6841f758c0fc1.png', '6841f758c0fc2.png', '6841f758c0fc4.jpeg', 0, '2025-06-05 20:00:24'),
(221, 'Adidas Train Essentials 3-Stripes High-Waisted Women\'s 7/8 Leggings Black', 'Apparel', 'Adidas', 'Legging', 'Women', 'Normal', 149.00, 0.00, 'Dance class or day hike. HIIT class or heavy bag. These adidas training tights support all the ways that you love to move. They feel extra smooth, with minimal seams and sleek, smooth fabric. No matter how fierce you get, AEROREADY absorbs moisture to keep you dry and a high-rise waistband stays in place. Tuck your house key in the waist pocket before you head out.\r\n\r\nMade with a series of recycled materials, and at least 70% recycled content, this product represents just one of our solutions to help end plastic waste.', '6841f7da7be0c.png', '6841f7da7be18.png', '6841f7da7be1b.png', '6841f7da7be1d.png', '6841f7da7be1f.jpeg', 0, '2025-06-05 20:02:34'),
(222, 'PUMA Performance Yoga Women\'s Training Pants', 'Apparel', 'Nike', 'Pant', 'Women', 'Normal', 129.00, 0.00, 'Spread the word – the wide-leg is back! These sophisticated yoga pants have sleek, sweeping lines and incorporate high-tech performance features. Made with sweat-wicking dryCELL technology, and low-friction flatlock stitching to protect from chafing, these on-trend pants are designed for the girl on the move. Ideal for those yoga flows, and as an addition to your everyday street-ready wardrobe.', '6841faff6f0ea.png', '6841faff6f0f1.png', '6841faff6f0f3.png', '6841faff6f0f4.png', '6841faff6f0f7.jpeg', 0, '2025-06-05 20:15:59'),
(223, 'Adidas AEROREADY Train Essentials 3-Stripes Women\'s Joggers GREY', 'Apparel', 'Adidas', 'Pant', 'Women', 'Promotion', 199.00, 80.00, 'Soft knit pants with AEROREADY moisture-absorbing tech, made with recycled materials.\r\nReady to move. Ready to perform. Whether you\'re stretching, lifting or perfecting your yoga poses, test your limits in comfort in these adidas pants. The loose fit and smart design make freedom of movement a given, even with shorts underneath. Signature 3-Stripes add a hint of sporty heritage style.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '6841fbde5d634.png', '6841fbb37fadf.png', '6841fbb37fae1.png', '6841fbde5e102.png', '6841fbb37fae5.jpeg', 0, '2025-06-05 20:18:59'),
(224, 'ADIDAS ESSENTIALS 3-STRIPES WOMEN\'S WOVEN 7/8 PANTS BLACK', 'Apparel', 'Adidas', 'Pant', 'Women', 'New', 199.00, 0.00, 'VERSATILE, SPORTY PANTS MADE WITH RECYCLED MATERIALS.\r\nAthleisure has been around for as long as sports have. The vibe is always the same Ã¢â‚¬â€ casual and comfortable. The same holds true for these adidas pants. Signature 3-Stripes flash down the sides, so you can always throw some sport DNA into the mix. They pair well with anything from bodysuits to oversized button-downs.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '6841fc48a46e0.png', '6841fc48a46e8.png', '6841fc48a46ea.png', '6841fc48a46ec.png', '6841fc48a46f0.jpeg', 0, '2025-06-05 20:21:28'),
(225, 'Nike Zenvy Women\'s Dri-FIT High-Waisted Training Yoga Pants Black ', 'Apparel', 'Nike', 'Pant', 'Women', 'Normal', 349.00, 0.00, 'Nike Zenvy Women\'s Dri-FIT High-Waisted Training Yoga Pants Black HJ5361-010 now available at Al-Ikhsan Sports. Balance your day in comfort in these Zenvy wide-leg trousers. Their lightweight InfinaSoft fabric gives you softness that you can feel with every bend, stretch and shift.', '6841fd02abbda.png', '6841fd02abbe0.png', '6841fd02abbe1.png', '6841fd02abbe3.png', '6841fd02abbe6.jpeg', 0, '2025-06-05 20:24:34'),
(226, 'Adidas Essentials 3 - Stripes Women T-Bottom', 'Apparel', 'Adidas', 'Pant', 'Women', 'Normal', 199.00, 0.00, 'Chill-worthy joggers that are primed for relaxation mode.\r\nSlip into these adidas pants and get ready to relax. Their soft cotton build and slim fit have a laid-back vibe that\'s perfect for weekend lounging or casual coffee runs. The signature 3-Stripes down the sides add a sporty touch.', '6841fdb40226c.png', '6841fdb402271.png', '6841fdb402272.png', '6841fdb402273.png', '6841fdb402274.jpeg', 0, '2025-06-05 20:27:32'),
(227, 'Adidas Essentials 3- Stripes Woven Women\'s 7/8 Pant Blue', 'Apparel', 'Adidas', 'Pant', 'Women', 'Promotion', 199.00, 139.30, 'Adidas Essentials 3- Stripes Woven Women\'s 7/8 Pant Blue', '6841fe23335dc.png', '6841fe23335e0.png', '6841fe23335e1.png', '6841fe23335e2.png', '6841fe23335e3.jpeg', 0, '2025-06-05 20:29:23'),
(228, 'PUMA Metal Cat Cap Black', 'Equipment', 'Puma', 'Cap', 'Men', 'Normal', 69.00, 0.00, 'PUMA Metal Cat Cap Black', '6841fee0d5d22.png', '6841fee0d5d26.png', '6841fee0d5d27.png', '6841fee0d5d28.png', '6841fee0d5d29.', 0, '2025-06-05 20:32:32'),
(229, 'PUMA Metal Cat Cap Black', 'Equipment', 'Puma', 'Cap', 'Women', 'Normal', 69.00, 0.00, 'PUMA Metal Cat Cap Black', '6841fefabb65c.png', '6841fefabb661.png', '6841fefabb662.png', '6841fefabb663.png', '6841fefabb664.', 0, '2025-06-05 20:32:58'),
(230, 'ADIDAS MEN BASEBALL CAPS BLACK', 'Equipment', 'Adidas', 'Cap', 'Men', 'Promotion', 99.00, 40.00, 'ADIDAS MEN BASEBALL CAPS BLACK', '6841ff42dc246.png', '6841ff42dc24b.png', '6841ff42dc24c.png', '6841ff42dc24d.png', '6841ff42dc24e.', 0, '2025-06-05 20:34:10'),
(231, 'ADIDAS MEN BASEBALL CAPS BLACK', 'Equipment', 'Adidas', 'Cap', 'Women', 'Promotion', 99.00, 40.00, 'ADIDAS MEN BASEBALL CAPS BLACK', '6841ff6530ed7.png', '6841ff6530edb.png', '6841ff6530edc.png', '6841ff6530edd.png', '6841ff6530ede.', 0, '2025-06-05 20:34:45'),
(232, 'Nike Dri-Fit Club Structured Metal Logo Cap Black', 'Equipment', 'Nike', 'Cap', 'Men', 'Normal', 89.00, 0.00, 'Nike Dri-FIT Club\r\nStructured Metal Logo Cap\r\nAdd some elevated shine to your headwear game with our Nike Club cap. Sweat-wicking fabric helps you stay cool and dry. The signature mid-depth design pairs with a structured crown and precurved bill for easy styling. Wear it with your favorite Nike sneakers for a clean look, head-to-toe.', '6841ffbc3ef5f.png', '6841ffbc3ef64.png', '6841ffbc3ef65.png', '6841ffbc3ef66.png', '6841ffbc3ef67.', 0, '2025-06-05 20:36:12'),
(233, 'Nike Dri-Fit Club Structured Metal Logo Cap Black', 'Equipment', 'Nike', 'Cap', 'Women', 'Normal', 89.00, 0.00, 'Nike Dri-FIT Club\r\nStructured Metal Logo Cap\r\nAdd some elevated shine to your headwear game with our Nike Club cap. Sweat-wicking fabric helps you stay cool and dry. The signature mid-depth design pairs with a structured crown and precurved bill for easy styling. Wear it with your favorite Nike sneakers for a clean look, head-to-toe.', '6841ffe2934dd.png', '6841ffe2934e1.png', '6841ffe2934e3.png', '6841ffe2934e4.png', '6841ffe2934e5.', 0, '2025-06-05 20:36:50'),
(234, 'PUMA Metal Cat Caps', 'Equipment', 'Puma', 'Cap', 'Men', 'Normal', 69.00, 0.00, 'PUMA Metal Cat Caps', '6842005933d63.png', '6842005933d6a.png', '6842005933d6c.png', '6842005933d6d.png', '6842005933d6f.', 0, '2025-06-05 20:38:49'),
(235, 'PUMA Metal Cat Caps', 'Equipment', 'Puma', 'Cap', 'Women', 'Normal', 69.00, 0.00, 'PUMA Metal Cat Caps', '68420071b7214.png', '68420071b7217.png', '68420071b7218.png', '68420071b7219.png', '68420071b721a.', 0, '2025-06-05 20:39:13'),
(236, 'NIKE APEX SWOOSH BUCKET HAT BROWN', 'Equipment', 'Nike', 'Cap', 'Men', 'Normal', 99.00, 0.00, 'NIKE APEX SWOOSH BUCKET HAT BROWN', '684200de50cea.png', '684200de50cf2.png', '684200de50cf4.png', '684200de50cf5.png', '684200de50cf7.', 0, '2025-06-05 20:41:02'),
(237, 'NIKE APEX SWOOSH BUCKET HAT BROWN', 'Equipment', 'Nike', 'Cap', 'Women', 'Normal', 99.00, 0.00, 'NIKE APEX SWOOSH BUCKET HAT BROWN', '684200f29ae97.png', '684200f29ae9c.png', '684200f29ae9d.png', '684200f29ae9e.png', '684200f29ae9f.', 0, '2025-06-05 20:41:22'),
(238, 'Skipping Rope', 'Equipment', 'Nike', 'Gym Accessories', 'Women', 'Promotion', 49.00, 15.00, 'Never skip skipping day with this rope from Everlast. Up your sports game with this must-have, you can take it anywhere. Jump around!', '684203af1d43c.png', '684203af1d43f.png', '684203af1d440.png', '684203af1d441.png', '684203af1d442.', 0, '2025-06-05 20:53:03'),
(239, 'Kettlebell', 'Equipment', 'Nike', 'Gym Accessories', 'Women', 'Normal', 129.00, 0.00, 'Whether you\'re building a home gym or beginning a new workout routine, the Everlast Kettlebell is a smart, space-efficient tool for full-body training. Ideal for strength, cardio, or mobility workouts, it’s perfect for early morning circuits, post-work sessions, or weekend fitness routines.\r\n\r\nWide Grip Comfort Handle: Provides stability and control for safer, more effective movements.\r\n\r\nHigh-Quality Cast Iron PVC Coating: Protects floors and extends product life for long-term use.\r\n\r\nVersatile Weight Options 6kg, 8kg, 10kg, 12kg: Offers a range of weights to match your evolving fitness goals.', '684203f88c8b3.png', '684203f88c8b8.png', '684203f88c8ba.png', '684203f88c8bb.png', '684203f88c8bc.', 0, '2025-06-05 20:54:16'),
(240, 'Yoga Mat 8 mm', 'Equipment', 'Nike', 'Gym Accessories', 'Women', 'Normal', 85.00, 0.00, 'Yoga Mat 8 mm', '684204574ab63.png', '684204574ab67.png', '684204574ab68.png', '684204574ab69.png', '684204574ab6a.', 0, '2025-06-05 20:55:51'),
(241, 'Yoga Positions Mat', 'Equipment', 'Nike', 'Gym Accessories', 'Women', 'Normal', 59.00, 0.00, 'Yoga Positions Mat', '684204ab7e334.png', '684204ab7e338.png', '684204ab7e339.png', '684204ab7e33a.png', '684204ab7e33b.', 0, '2025-06-05 20:57:15');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `review_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_size` varchar(20) NOT NULL,
  `product_sku` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL,
  `last_update_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`stock_id`, `product_id`, `product_size`, `product_sku`, `stock`, `last_update_at`) VALUES
(20, 5, 'UK7.5', '111111', 10, '2025-05-12 19:00:55'),
(21, 7, 'UK13.5C', '111111', 9, '2025-05-13 06:07:20'),
(33, 7, 'UK1.5Y', '111111', 8, '2025-06-04 14:10:40'),
(54, 5, 'UK6', 'SKU1122', 49, '2025-06-04 13:34:37'),
(55, 5, 'UK8.5', 'SKU1123', 60, '2025-04-08 06:33:55'),
(56, 5, 'UK9', 'SKU1124', 8, '2025-04-18 01:41:06'),
(58, 6, 'UK8.5', 'SKU1112', 92, '2025-06-03 09:02:43'),
(59, 6, 'UK9', 'SKU1113', 99, '2025-05-29 06:16:44'),
(60, 6, 'UK9.5', 'SKU1114', 100, '2025-04-08 06:54:33'),
(61, 6, 'UK10', 'SKU1115', 98, '2025-06-04 19:07:27'),
(62, 6, 'UK10.5', 'SKU1116', 100, '2025-04-08 06:54:56'),
(64, 8, 'UK13.5C', 'SKU1122', 0, '2025-04-18 01:46:03'),
(65, 19, 'UK6', 'SKU5525', 7, '2025-04-28 04:37:24'),
(66, 19, 'UK6.5', 'SKU5521', 10, '2025-04-09 17:06:25'),
(67, 19, 'UK10.5', 'SKU5520', 9, '2025-06-04 19:53:35'),
(69, 20, 'UK5', 'SKU3356', 89, '2025-06-05 01:11:42'),
(70, 18, 'XXS', '111111', 5, '2025-06-05 01:11:42'),
(71, 22, 'S', '111112', 2, '2025-04-18 02:01:17'),
(72, 22, 'M', '111113', 10, '2025-04-09 17:08:08'),
(73, 22, 'L', '111114', 10, '2025-04-09 17:08:15'),
(74, 24, 'XXS', '111111', 10, '2025-04-29 02:34:50'),
(75, 23, 'XXS', 'SKU1234', 98, '2025-05-14 18:05:52'),
(76, 25, 'UK13.5C', '111111', 18, '2025-05-14 18:05:52'),
(77, 26, 'UK7', '111111', 98, '2025-04-18 01:41:06'),
(78, 29, 'UK6', '111111', 9, '2025-05-14 18:05:52'),
(79, 32, 'UK5.5', '111111', 7, '2025-05-14 18:05:52'),
(80, 38, 'XXS', '111111', 7, '2025-04-28 04:28:22'),
(81, 37, 'S', '111111', 10, '2025-04-22 16:27:56'),
(82, 42, 'UK6', '111111', 10, '2025-05-31 04:54:42'),
(83, 41, 'One Size', '111111', 7, '2025-06-04 19:54:03'),
(84, 40, 'One Size', '111111', 0, '2025-04-18 01:41:06'),
(85, 39, 'One Size', '111111', 0, '2025-06-05 01:11:42'),
(86, 35, 'UK13.5C', '111111', 0, '2025-06-01 02:28:04'),
(87, 27, 'UK13.5C', '111111', 6, '2025-06-04 19:04:11'),
(88, 33, 'UK6.5Y', 'SKU1121', 98, '2025-04-18 02:39:27'),
(89, 31, 'UK13.5C', '111111', 1, '2025-04-16 01:13:30'),
(90, 43, 'UK3Y', '111111', 0, '2025-04-23 16:34:42'),
(91, 43, 'UK3.5Y', '111111', 96, '2025-06-04 19:54:03'),
(92, 43, 'UK4Y', '111111', 100, '2025-04-18 00:09:20'),
(93, 43, 'UK4.5Y', '111111', 100, '2025-04-18 00:09:25'),
(95, 60, 'One Size', 'FK0890-OSFM', 10, '2025-04-18 01:17:19'),
(96, 59, 'One Size', 'FD5186-200-M/L', 8, '2025-05-14 18:05:52'),
(97, 58, 'One Size', '084288 01-5', 98, '2025-05-14 18:05:52'),
(98, 57, 'One Size', 'IQ4026-9', 6, '2025-06-04 19:04:11'),
(99, 56, 'One Size', 'IQ4013-10', 7, '2025-05-14 22:00:31'),
(100, 55, 'One Size', 'IA0976-5', 4, '2025-06-04 19:07:27'),
(101, 54, 'One Size', 'SP0040009-L', 99, '2025-04-18 01:43:45'),
(102, 53, 'One Size', 'IP3995-L', 95, '2025-04-18 02:01:17'),
(103, 52, 'One Size', 'HT2458-5', 7, '2025-04-28 04:37:24'),
(108, 50, 'M', '704942 06-M', 17, '2025-06-04 13:56:44'),
(109, 50, 'XL', '704942 06-XL', 20, '2025-04-18 01:24:53'),
(110, 49, 'S', 'DV9360-010-S', 10, '2025-04-18 01:25:46'),
(111, 49, 'M', 'DV9360-010-M', 9, '2025-05-29 02:38:32'),
(112, 49, 'L', 'DV9360-010-L', 10, '2025-04-18 01:26:01'),
(113, 49, 'XL', 'DV9360-010-XL', 10, '2025-04-18 01:26:08'),
(114, 49, '2XL', 'DV9360-010-2XL', 10, '2025-04-18 01:26:23'),
(116, 48, 'M', 'HC0332-M', 0, '2025-06-04 19:54:03'),
(117, 48, 'L', 'HC0332-L', 10, '2025-04-18 01:27:18'),
(121, 30, 'UK6', '111111', 10, '2025-04-18 05:53:55'),
(122, 47, 'UK13.5C', '111111', 0, '2025-04-23 16:34:42'),
(123, 44, 'UK6', '111111', 1, '2025-04-25 05:41:52'),
(124, 45, 'UK6.5', '704942 06-5', 10, '2025-04-18 05:46:14'),
(125, 45, 'UK6Y', '704942 06', 10, '2025-04-18 05:46:25'),
(127, 46, 'UK5Y', '111111', 1, '2025-04-18 05:46:56'),
(128, 28, 'UK6Y', 'SKU1123', 18, '2025-05-14 18:05:52'),
(129, 34, 'UK6.5Y', '111111', 8, '2025-05-14 18:05:52'),
(133, 61, 'One Size', 'SKU1120', 1, '2025-05-09 20:19:39'),
(134, 68, 'S', '1382911-408-XL', 8, '2025-06-04 19:31:04'),
(135, 69, 'XL', '1382911-001-XL', 10, '2025-05-14 07:50:49'),
(136, 70, 'S', 'UBWA03018-20-S', 10, '2025-05-14 07:51:10'),
(138, 72, 'L', ' 775350 04-L', 9, '2025-05-14 18:05:52'),
(139, 73, 'L', '586813 51-L', 10, '2025-05-14 08:02:06'),
(140, 74, 'XL', 'DQ5472-010-XL', 10, '2025-05-14 08:05:52'),
(141, 75, 'UK6Y', '1012B775-501-9', 10, '2025-05-14 08:10:48'),
(143, 76, 'UK6', 'IF9397-6', 10, '2025-05-14 08:17:04'),
(144, 78, 'UK13.5C', 'IE1308-3H', 97, '2025-06-04 19:53:35'),
(145, 79, 'UK13.5C', 'DJ5958-001-1Y', 19, '2025-06-03 13:49:15'),
(146, 80, 'UK13.5C', 'DV4352-002-6Y', 20, '2025-05-23 17:25:19'),
(147, 81, 'UK13.5C', 'FQ8392-002-4Y', 20, '2025-05-23 17:28:43'),
(149, 82, 'UK13.5C', 'ID3810-1', 10, '2025-05-23 17:34:55'),
(150, 83, 'UK13.5C', 'FJ2600-002-6Y', 10, '2025-05-23 17:35:11'),
(151, 84, 'UK13.5C', 'DJ6039-002-3C', 0, '2025-06-04 13:34:37'),
(152, 85, 'UK13.5C', 'AR4162-001-6C', 11, '2025-05-23 17:41:38'),
(153, 86, 'UK13.5C', 'GW6439-4H', 111, '2025-05-23 17:43:36'),
(154, 87, 'UK13.5C', '372009 32-10', 10, '2025-06-04 19:31:04'),
(155, 88, 'UK13.5C', 'GW6440-2', 111, '2025-05-23 17:48:20'),
(156, 89, 'UK13.5C', '192929 45-2', 109, '2025-06-04 13:35:25'),
(157, 90, 'UK13.5C', '192928 46-6', 111, '2025-05-23 17:55:02'),
(158, 91, 'UK13.5C', '3027100-001-3', 111, '2025-05-23 17:58:14'),
(159, 92, 'UK13.5C', '3024880-002-9.5', 11, '2025-05-23 18:00:14'),
(160, 93, 'XXS', 'FZ5309-010-S', 111, '2025-05-23 18:04:06'),
(161, 94, 'XXS', 'FZ5513-037-S', 110, '2025-06-04 18:59:19'),
(162, 95, 'XXS', 'FZ5557-537-L', 111, '2025-05-23 18:07:46'),
(163, 97, 'XXS', '682769 01-140', 111, '2025-05-23 18:11:30'),
(164, 96, 'XXS', '682769 87-140', 111, '2025-05-23 18:11:44'),
(165, 98, 'XXS', 'H57496-176', 110, '2025-06-04 19:53:35'),
(166, 99, 'XXS', 'H57564-152', 111, '2025-05-23 18:16:37'),
(168, 101, 'XXS', 'HC5054-152', 111, '2025-05-23 18:20:00'),
(169, 102, 'XXS', 'HJ3716-539-L', 110, '2025-06-04 13:34:37'),
(170, 103, 'XXS', '704925 56-176', 111, '2025-05-23 18:23:42'),
(171, 104, 'XXS', '658637 01-176', 109, '2025-06-04 13:35:25'),
(172, 105, 'XXS', 'FN8278-675-L', 110, '2025-06-04 19:07:27'),
(173, 106, 'XXS', 'FD3067-476B-XL', 211, '2025-05-23 18:28:47'),
(174, 107, 'XXS', 'DX5416-084-M', 9, '2025-06-04 19:31:04'),
(175, 108, 'XXS', '684914 01-140', 11, '2025-05-23 18:32:16'),
(176, 109, 'XXS', 'FN8371-410-M', 222, '2025-05-23 18:34:02'),
(177, 110, 'XXS', 'FZ5311-010-M', 9, '2025-06-04 13:34:37'),
(178, 111, 'One Size', 'FB5362-480-NS', 111, '2025-05-23 18:41:47'),
(179, 112, 'One Size', 'FB5648-581-NS', 110, '2025-06-04 13:34:37'),
(180, 115, 'One Size', 'FB5063-010-NS', 111, '2025-05-23 18:47:19'),
(181, 116, 'One Size', 'UBJE28004-01-NS-L', 111, '2025-05-23 19:06:23'),
(182, 113, 'One Size', 'FB5063-073-NS', 110, '2025-06-04 19:04:11'),
(183, 114, 'One Size', 'FB5063-100-NS', 108, '2025-06-05 01:11:42'),
(184, 117, 'One Size', '701221137002-027', 111, '2025-05-23 19:21:04'),
(185, 118, 'One Size', 'UBJE28003-01-NS-L', 11, '2025-05-23 19:22:48'),
(186, 119, 'One Size', 'SX6912-100-M', 106, '2025-06-05 01:11:42'),
(187, 120, 'One Size', 'DR6084-010-NS', 110, '2025-06-04 07:33:45'),
(188, 121, 'One Size', 'DR6089-247-NS', 1111, '2025-05-25 16:45:26'),
(189, 122, 'One Size', '091324 03-UA', 111, '2025-05-25 16:47:26'),
(190, 123, 'One Size', 'FB3051-411-NS', 111, '2025-05-25 16:49:10'),
(191, 124, 'One Size', '091323 01-UA', 109, '2025-06-04 08:16:01'),
(192, 125, 'One Size', 'IN9337-1', 111, '2025-05-25 16:52:40'),
(193, 126, 'One Size', '084464 01-MINI', 111, '2025-05-25 16:54:04'),
(194, 127, 'One Size', '084461 01-MINI', 111, '2025-05-25 16:55:11'),
(195, 129, 'One Size', 'N000128543703-3', 111, '2025-05-25 16:57:44'),
(196, 128, 'One Size', 'IX4048-1', 111, '2025-05-25 16:57:57'),
(197, 130, 'One Size', '001', 111, '2025-05-25 17:01:27'),
(198, 131, 'One Size', '002', 1111, '2025-05-25 17:07:39'),
(199, 132, 'One Size', '003', 110, '2025-06-04 13:56:44'),
(200, 134, 'One Size', '005', 111, '2025-05-25 17:11:27'),
(201, 133, 'One Size', '004', 111, '2025-05-25 17:11:36'),
(202, 135, 'UK13.5C', 'DV5458-105-6C', 1106, '2025-06-04 19:07:27'),
(203, 136, 'UK13.5C', 'H06380-2', 110, '2025-06-01 02:28:04'),
(204, 137, 'UK13.5C', 'DV5458-112-6C', 111, '2025-05-28 22:44:46'),
(205, 138, 'UK13.5C', 'FB7690-401-10C', 111, '2025-05-28 22:46:51'),
(206, 140, 'UK13.5C', '3027099-001-6', 111, '2025-05-28 22:51:27'),
(207, 141, 'XXS', ' FD3067-006A-XL', 110, '2025-06-04 03:51:21'),
(208, 142, 'XXS', 'FZ5513-010-L', 111, '2025-05-28 22:55:33'),
(209, 143, 'XXS', 'FN8355-657-L', 109, '2025-06-03 09:02:43'),
(210, 144, 'XXS', 'DX5482-010-S', 110, '2025-05-29 02:38:32'),
(211, 145, 'XXS', 'HF8079-657-XS', 110, '2025-06-01 02:28:04'),
(212, 146, 'XXS', 'HJ3716-492-XS', 111, '2025-05-28 23:02:39'),
(213, 147, 'XXS', 'H57497-152', 111, '2025-05-28 23:04:59'),
(214, 148, 'XXS', 'DX5476-017-M', 111, '2025-05-28 23:07:19'),
(215, 149, 'XXS', 'DA0806-091-M', 211, '2025-05-28 23:09:10'),
(216, 150, 'XXS', 'FN8436-010-L', 111, '2025-05-28 23:10:30'),
(217, 151, 'XXS', 'FN8426-010-L', 111, '2025-05-28 23:12:16'),
(218, 152, 'XXS', 'FD0238-010-L', 111, '2025-05-28 23:14:01'),
(219, 153, 'One Size', ' DR6084-646-NS', 111, '2025-05-28 23:15:55'),
(220, 154, 'One Size', ' FN0961-700-NS', 110, '2025-06-04 19:31:04'),
(221, 155, 'One Size', 'FZ7254-020-NS', 111, '2025-05-28 23:18:31'),
(222, 156, 'One Size', 'DR6084-480-NS', 109, '2025-06-04 18:58:33'),
(223, 157, 'One Size', ' FZ0831-010-NS', 111, '2025-05-28 23:21:01'),
(224, 158, 'One Size', 'FB5648-010-1SIZE', 111, '2025-05-28 23:22:10'),
(225, 160, 'UK13.5C', ' FD6476-001-9', 111, '2025-05-28 23:27:05'),
(226, 161, 'UK13.5C', 'ID2706-4', 111, '2025-05-28 23:28:56'),
(227, 162, 'UK13.5C', 'ID2709-4', 111, '2025-05-28 23:30:32'),
(228, 163, 'UK13.5C', '1012B511-250-8H', 111, '2025-05-28 23:32:20'),
(229, 164, 'UK13.5C', 'FD6476-010-8', 109, '2025-06-04 18:58:33'),
(230, 165, 'UK13.5C', 'FD6476-004-7H', 111, '2025-05-28 23:35:17'),
(231, 166, 'UK13.5C', '378776 27-5', 109, '2025-06-04 18:59:19'),
(232, 167, 'UK13.5C', '1012B601-020-6', 110, '2025-06-04 13:56:44'),
(233, 168, 'UK13.5C', '1012B775-501-8H', 111, '2025-05-28 23:39:55'),
(234, 169, 'UK13.5C', '380081 01-4', 1111, '2025-05-28 23:41:25'),
(235, 170, 'XXS', '624318 83-L', 2111, '2025-05-28 23:46:43'),
(236, 171, 'XXS', '624318 01-L', 110, '2025-05-31 03:48:30'),
(237, 172, 'XXS', '677882 83-L', 111, '2025-05-28 23:49:24'),
(238, 173, 'XXS', '677893 60-L', 111, '2025-05-28 23:50:55'),
(239, 174, 'XXS', '680878 01-L', 111, '2025-05-28 23:52:17'),
(240, 175, 'XXS', '523804 51-L', 110, '2025-06-04 14:10:40'),
(241, 176, 'One Size', 'DR7217-010-NS', 111, '2025-05-28 23:57:52'),
(242, 177, 'One Size', 'CW9300-338-NS', 111, '2025-05-28 23:59:13'),
(243, 178, 'One Size', '079943 38-UA', 111, '2025-05-29 00:00:29'),
(244, 179, 'One Size', 'DR7217-838-NS', 111, '2025-05-29 00:01:47'),
(245, 180, 'One Size', 'HZ5950-NS', 109, '2025-06-03 14:54:58'),
(246, 181, 'One Size', 'FN3059-010-NS', 109, '2025-06-04 18:59:19'),
(247, 182, 'One Size', '091175 03-UA', 111, '2025-05-29 00:05:30'),
(248, 183, 'One Size', 'IZ1897-NS', 108, '2025-06-04 18:58:33'),
(249, 184, 'One Size', '079953 32-UA', 111, '2025-05-29 00:10:54'),
(250, 185, 'One Size', '1364180-432-OSFA', 111, '2025-05-29 00:12:25'),
(251, 186, 'One Size', 'HT3448-L', 111, '2025-05-29 00:15:21'),
(252, 187, 'One Size', 'HT3440-L', 111, '2025-05-29 00:16:47'),
(253, 188, 'One Size', '3033B959-001-L', 108, '2025-06-04 19:07:27'),
(254, 189, 'One Size', '3033B959-100-M', 111, '2025-05-29 00:19:23'),
(255, 190, 'UK13.5C', '111', 1, '2025-05-29 06:25:36'),
(256, 100, 'XXS', 'DX5482-475-L', 111, '2025-06-01 05:46:18'),
(257, 194, 'UK13.5C', '012B413-004-9', 111, '2025-06-05 19:15:51'),
(258, 195, 'UK13.5C', '1012B516-002-7H', 111, '2025-06-05 19:17:24'),
(259, 196, 'UK13.5C', 'PFCS22005-BUOR-41', 111, '2025-06-05 19:21:31'),
(260, 197, 'UK13.5C', 'PFCS22005-BUOR-41', 111, '2025-06-05 19:21:43'),
(261, 198, 'UK13.5C', '1071A091-102-10', 111, '2025-06-05 19:24:50'),
(262, 199, 'UK13.5C', '1071A091-102-10', 111, '2025-06-05 19:24:59'),
(263, 200, 'UK13.5C', 'PFCS22005-BLGN-43', 111, '2025-06-05 19:27:52'),
(264, 201, 'UK13.5C', 'PFCS22005-BLGN-43', 111, '2025-06-05 19:27:59'),
(265, 202, 'UK13.5C', '1071A091-105-8', 111, '2025-06-05 19:30:52'),
(266, 203, 'UK13.5C', '1071A091-105-8', 111, '2025-06-05 19:31:04'),
(267, 204, 'UK13.5C', 'PFCS22002-GRY-40', 111, '2025-06-05 19:34:07'),
(268, 205, 'UK13.5C', 'PFCS22002-GRY-40', 111, '2025-06-05 19:34:14'),
(269, 206, 'UK13.5C', '1071A093-101-9', 111, '2025-06-05 19:37:08'),
(270, 207, 'UK13.5C', '1071A093-101-9', 111, '2025-06-05 19:37:19'),
(271, 208, 'UK13.5C', '1071A104-400-11', 111, '2025-06-05 19:40:25'),
(272, 209, 'UK13.5C', '1071A104-400-11', 111, '2025-06-05 19:40:34'),
(273, 210, 'XXS', '66397U-MAS-L', 111, '2025-06-05 19:44:56'),
(274, 211, 'XXS', '66397U-MAS-L', 111, '2025-06-05 19:45:07'),
(275, 212, 'XXS', '66397U-MAT-M', 111, '2025-06-05 19:48:19'),
(276, 213, 'XXS', '66397U-MAT-M', 111, '2025-06-05 19:48:25'),
(277, 214, 'XXS', '704932 01-L', 111, '2025-06-05 19:51:40'),
(278, 215, 'XXS', '704932 01-L', 111, '2025-06-05 19:51:49'),
(279, 216, 'XXS', '66403U-MAL-XL', 111, '2025-06-05 19:54:27'),
(280, 217, 'XXS', '66403U-MAL-XL', 111, '2025-06-05 19:54:34'),
(281, 218, 'XXS', '530080 01-S', 111, '2025-06-05 19:56:48'),
(282, 219, 'XXS', '848307 01-L', 111, '2025-06-05 19:58:35'),
(283, 220, 'XXS', 'DV7792-478-XL', 111, '2025-06-05 20:00:36'),
(284, 221, 'XXS', 'HT5438-A/2XS', 111, '2025-06-05 20:02:40'),
(285, 222, 'XXS', '521771 01-XL', 111, '2025-06-05 20:16:22'),
(286, 223, 'XXS', 'HZ5647-A/2XS', 111, '2025-06-05 20:19:30'),
(287, 224, 'XXS', 'HT3398-L', 111, '2025-06-05 20:21:47'),
(288, 225, 'XXS', 'HJ5361-010-L', 111, '2025-06-05 20:25:50'),
(289, 226, 'XXS', 'JD0896-A/SS', 111, '2025-06-05 20:27:49'),
(290, 227, 'XXS', 'IC0558-A/2XS', 111, '2025-06-05 20:29:40'),
(291, 228, 'One Size', '021269 01-ADULT', 111, '2025-06-05 20:33:09'),
(292, 229, 'One Size', '021269 01-ADULT', 111, '2025-06-05 20:33:16'),
(293, 230, 'One Size', 'FK0891-OSFM', 111, '2025-06-05 20:34:58'),
(294, 231, 'One Size', 'FK0891-OSFM', 111, '2025-06-05 20:35:03'),
(295, 232, 'One Size', 'FB5371-010-S/M', 111, '2025-06-05 20:37:03'),
(296, 233, 'One Size', 'FB5371-010-S/M', 111, '2025-06-05 20:37:10'),
(297, 234, 'One Size', '021269 07-ADULT', 111, '2025-06-05 20:39:23'),
(298, 235, 'One Size', '021269 07-ADULT', 111, '2025-06-05 20:39:33'),
(299, 236, 'One Size', 'NIKE APEX SWOOSH BUCKET HAT BROWN', 111, '2025-06-05 20:41:38'),
(300, 237, 'One Size', 'NIKE APEX SWOOSH BUCKET HAT BROWN', 111, '2025-06-05 20:41:48'),
(301, 238, 'One Size', '761845', 111, '2025-06-05 20:53:18'),
(302, 239, 'One Size', '761372', 111, '2025-06-05 20:54:28'),
(303, 240, 'One Size', '761706', 1111, '2025-06-05 20:56:05'),
(304, 241, 'One Size', '737536', 111, '2025-06-05 20:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `state` varchar(50) NOT NULL,
  `city` varchar(255) NOT NULL,
  `birthday_date` date NOT NULL,
  `gender` varchar(10) NOT NULL CHECK (`gender` in ('Male','Female')),
  `profile_image` varchar(255) DEFAULT NULL,
  `user_type` enum('1','2','3') NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `mobile_number`, `password`, `address`, `postcode`, `state`, `city`, `birthday_date`, `gender`, `profile_image`, `user_type`, `create_at`) VALUES
(41, 'Chua', 'Chi Ann', 'chiannchua05@gmail.com', '0189468221', '$2y$10$NIIHO0xw1uGfWfMOKKojtOru2NzQYPY2yNcDw8eJbV/603C22LaO6', 'No.21,Jalan Biru 2\r\n', '85000', 'Melaka', 'Ayer Keroh', '2007-04-06', 'Male', '1748485914.jpg', '2', '2025-04-07 06:47:57'),
(51, 'Chua', 'Chi Ann', 'caichian8@gmail.com', '0189468221', '$2y$10$0zosYrvIpeUm7o7Y7MHSQ.7NefyDyRxd72fTxXDmAsZhzM3S0S0l.', 'No.26,Jalan Oren 3, Taman Sri Pelangi', '85000', 'Johor', 'segamat', '2025-05-29', 'male', 'user_51_1748485583.jpeg', '1', '2025-05-29 02:21:28'),
(52, 'Chua', 'Chi Ann', 'superadmin@gmail.com', '0189468221', '$2y$10$FN7BQ/vwISb3uQwJ.0GyA.dhIOo7/mRpvHyIso.1CZTjHv2JNZ4HC', '1', '85000', 'Johor', 'Segamat', '2007-05-28', 'Male', '1748485806.jpg', '3', '2025-05-29 02:30:06'),
(54, 'Elvis', 'Xi Jie.....', 'sohxj0802@gmail.com', '01139681141', '$2y$10$JdBDxUTnFavEOCFvXNGbY.OWw9oZNywXYmTQH14nBoQkWExX.K2/a', 'No. 36,', '85000', 'Kedah', 'Sungai Petani', '2007-06-07', 'Male', 'user_54_1748498879.jpg', '1', '2025-05-29 06:04:10'),
(64, 'John', 'Doe', 'c8653116@gmail.com', '01325792646', '$2y$10$oZYWRVeaPVz307eVB.Ez8u3AdKw939R2JhVEuGCJ8B95OojfqBhs.', 'No,21 Jalan Eco Cascadia 5/8, Taman Setia Eco Cascadia', '85000', 'Johor', 'Segamat', '2000-07-20', 'male', NULL, '1', '2025-06-04 13:56:15'),
(66, '22', '22', 'c16042267@gmail.com', '0141414134', '$2y$10$Khv2Whl6qnbSiIgB/xKYSeQ1d6wTzPZUILDFdoFdLzyVO4GIMG.oW', 'No.6, Jalan Biru,Taman Jaya', '85647', 'Sarawak', 'Miri', '2025-06-17', 'male', NULL, '1', '2025-06-04 14:09:07'),
(67, 'Alex', 'Tan', 'deadhunter0802@gmail.com', '01139681141', '$2y$10$GqDQ7bahOa3MT0OIePqhV.axQFLTpMZkH1DNFKKKikam6cCZkphvi', 'N0.22,Taman Aurora Pelangi, Jalan D3 3,', '81100', 'Johor', 'Johor Bahru', '2025-06-04', 'female', NULL, '1', '2025-06-04 19:06:02'),
(68, 'Amily', 'Sue', 'verosports11@gmail.com', '0214351532', '$2y$10$IQaTbVe.rGk1m1tfspBoQ.ARlaoJfxIsPE9y2J0wwZCzlhIW4xbvG', 'No,13 Jalan Titiwangsa 2, Taman Tampoi Indah', '85000', 'Johor', 'Segamat', '2025-06-05', 'female', NULL, '1', '2025-06-04 19:48:42'),
(69, 'Soh', 'Xi Jie', 'sohxj08@gmail.com', '01139681141', '$2y$10$Vit58lw26tBQ9T6CvYO6Yueek4jSXyLZmrxQg.z4jT6E6oj.LuTZi', 'B 13-05, Jalan D1', '75450', 'Melaka', 'Ayer Keroh', '2007-06-01', 'Male', '1749087369.jpeg', '2', '2025-06-05 01:36:09'),
(70, 's', 'xj', 'deaddiehunter@gmail.com', '01139681141', '$2y$10$cWkkQ7vtV7M/0ZcdcVByaer2zHpgTVkdFwHrTa1MkZLj9OHfYbRqO', 'No,21 Jalan Eco Cascadia 5/8, Taman Setia Eco Cascadia, 81100 Johor Bahru.', '81100', 'Kuala Lumpur', 'Kuala Lumpur', '2007-06-01', 'male', NULL, '1', '2025-06-07 12:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `token_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `is_revoked` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tokens`
--

INSERT INTO `user_tokens` (`token_id`, `user_id`, `token`, `expires_at`, `created_at`, `last_used_at`, `is_revoked`, `user_agent`, `ip_address`) VALUES
(26, 44, '75eac37e556abd7a839aef26b0e06ea282e2ecd1cd1d7c07c404bcc6a3d99dd5', '2025-06-26 06:44:03', '2025-05-27 12:44:03', NULL, 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '::1'),
(28, 51, '9add98658b954e03c3c78c0f13eab3983106ac44d05b60ac9a95bdc1c1b90f6c', '2025-06-04 10:58:23', '2025-06-03 16:58:23', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '::1'),
(29, 51, '986ae7a000e46f5937400ddfe6932852b38dd6d5f47b96aec29687de30014294', '2025-06-04 10:59:57', '2025-06-03 16:59:57', NULL, 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '::1'),
(30, 51, '37ad689b2cf3aebca0ae4e29d12c773012b8078d7da5a4ff56095003576814eb', '2025-06-04 15:46:39', '2025-06-03 21:46:39', '2025-06-03 21:53:31', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1'),
(34, 51, '279117421abf95172aef63a6184fe5a987613d07b06dcfdb4897eb590dcca5d5', '2025-06-05 05:50:20', '2025-06-04 11:50:20', '2025-06-04 11:56:47', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1'),
(35, 51, '4056594a703741edc7398e19f94084092d28de0de5b0467827de0ce5bae08703', '2025-06-05 05:56:47', '2025-06-04 11:56:47', '2025-06-04 12:29:32', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1'),
(40, 51, '89d03d2cdecb1750eaec227e50f183c256cad000a080d68bd8282784e48d0eed', '2025-06-05 11:03:22', '2025-06-04 17:03:22', '2025-06-04 21:01:12', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1'),
(41, 51, '6dd3e737403ce442f21c9f863ce31e09e46a15bc896d318d503a81abeb999b16', '2025-06-05 15:01:12', '2025-06-04 21:01:12', '2025-06-04 21:01:12', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1'),
(43, 66, '16509fdd063199c18055a002b62ade4d6c73920705b550074b4e0c3842f4b26d', '9999-12-31 23:59:59', '2025-06-04 22:10:10', '2025-06-04 22:10:25', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1'),
(46, 67, '5b0159fc9e31249cb2038d8bc8ff1c3a0d036a0a7754c1430cbff5eab80f7743', '2025-06-05 21:06:42', '2025-06-05 03:06:42', '2025-06-05 03:46:45', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '::1'),
(48, 68, 'bebdc52ad1727d007149431b73b331a07026d0fcf40a4281eff1fd0418d528ef', '2025-06-05 21:53:06', '2025-06-05 03:53:06', '2025-06-05 03:54:06', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '::1'),
(49, 51, '83bcfb9d0d0dc589c05f058138fd209645b586de046388aa44d5d4c8118beec9', '2025-06-06 03:09:39', '2025-06-05 09:09:39', '2025-06-05 09:38:34', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1'),
(50, 51, 'cd35d2ace9c20727ebf2b407c6794d45dd4c3de83eaa14ca749c1424cfd834ec', '2025-06-06 03:38:34', '2025-06-05 09:38:34', '2025-06-05 09:40:34', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billboard`
--
ALTER TABLE `billboard`
  ADD PRIMARY KEY (`billboard_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`,`product_size`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_product_size` (`user_id`,`product_id`,`product_size`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contactUs_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`login_attempts_id`),
  ADD KEY `admin_email` (`admin_email`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `unq_stripe` (`stripe_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Indexes for table `payment_log`
--
ALTER TABLE `payment_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_payment` (`payment_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_review_order` (`order_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token_validation` (`token`,`expires_at`,`is_revoked`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billboard`
--
ALTER TABLE `billboard`
  MODIFY `billboard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=471;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contactUs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `login_attempts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payment_log`
--
ALTER TABLE `payment_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD CONSTRAINT `contact_us_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
