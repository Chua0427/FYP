-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 09:13 AM
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
(18, '1743944378.png', '2025-04-06 12:59:38');

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

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `product_size`, `quantity`, `added_at`) VALUES
(1, 25, 5, 'UK6', 21, '2025-04-16 11:19:21'),
(221, 44, 18, 'XXS', 5, '2025-05-26 07:12:24');

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
(1, 25, '', '', '2025-04-14 08:06:51'),
(2, 44, '', '', '2025-04-18 10:30:06'),
(3, 44, '', '00193164', '2025-05-24 04:22:25'),
(4, 44, '', 'jerry', '2025-05-24 04:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `delivery_status` enum('prepare','packing','assign','shipped','delivered') DEFAULT 'prepare',
  `order_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `shipping_address`, `delivery_status`, `order_at`) VALUES
(75, 44, 720.00, 'we, we, 21121, Johor', 'delivered', '2025-04-24 03:52:26'),
(76, 44, 480.00, 'we, we, 21121, Johor', 'delivered', '2025-04-24 03:52:47'),
(77, 44, 120.00, 'we, we, 21121, Johor', 'delivered', '2025-04-24 04:47:16'),
(78, 44, 100.00, 'we, we, 21121, Johor', 'delivered', '2025-04-24 05:09:34'),
(79, 44, 120.00, 'we, we, 21121, Johor', 'packing', '2025-04-24 05:19:16'),
(80, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-04-24 06:08:53'),
(81, 44, 100.00, 'we, we, 21121, Johor', 'packing', '2025-04-24 06:14:40'),
(82, 44, 50.00, 'we, we, 21121, Johor', 'packing', '2025-04-24 06:27:55'),
(83, 44, 50.00, 'we, we, 21121, Johor', 'prepare', '2025-04-24 06:36:04'),
(84, 44, 136.50, 'we, we, 21121, Johor', 'prepare', '2025-04-24 06:36:25'),
(85, 44, 136.50, 'we, we, 21121, Johor', 'prepare', '2025-04-24 06:36:50'),
(86, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-04-24 06:41:25'),
(87, 44, 114.50, 'we, we, 21121, Johor', 'prepare', '2025-04-24 06:58:22'),
(88, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-04-24 07:01:35'),
(89, 44, 419.30, 'we, we, 21121, Johor', 'delivered', '2025-04-25 15:01:36'),
(90, 44, 2582.00, 'we, we, 21121, Johor', 'packing', '2025-04-25 15:47:37'),
(91, 44, 13432.00, 'we, we, 21121, Johor', 'prepare', '2025-04-25 16:01:17'),
(92, 44, 3342.20, 'we, we, 21121, Johor', 'delivered', '2025-04-25 17:09:59'),
(93, 44, 35.00, 'we, we, 21121, Johor', 'delivered', '2025-04-25 17:21:17'),
(94, 44, 120.00, 'we, we, 21121, Johor', 'delivered', '2025-05-15 02:48:53'),
(95, 44, 2437.00, 'we, we, 21121, Johor', 'delivered', '2025-05-21 03:41:43'),
(96, 44, 597.60, 'we, we, 21121, Johor', 'prepare', '2025-05-24 04:11:04'),
(97, 44, 597.60, 'we, we, 21121, Johor', 'prepare', '2025-05-24 04:14:59'),
(98, 44, 816.60, 'we, we, 21121, Johor', 'prepare', '2025-05-24 04:20:03'),
(99, 44, 816.60, 'we, we, 21121, Johor', 'prepare', '2025-05-24 04:20:33'),
(100, 44, 816.60, 'we, we, 21121, Johor', 'prepare', '2025-05-24 04:29:28'),
(101, 44, 300.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 05:46:20'),
(102, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 05:54:33'),
(103, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:00:37'),
(104, 44, 150.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:06:04'),
(105, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:15:01'),
(106, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:20:04'),
(107, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:28:11'),
(108, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:32:08'),
(109, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:48:20'),
(110, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:53:10'),
(111, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 06:58:59'),
(112, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-05-24 08:49:58'),
(113, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-05-24 08:50:19'),
(114, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-05-24 09:26:59'),
(115, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-05-24 09:27:36'),
(116, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 09:35:24'),
(117, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 09:44:46'),
(118, 44, 320.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 10:20:27'),
(119, 44, 398.40, 'we, we, 21121, Johor', 'prepare', '2025-05-24 10:27:45'),
(120, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 10:34:02'),
(121, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 10:34:47'),
(122, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 11:24:35'),
(123, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 11:27:34'),
(124, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 13:46:03'),
(125, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 13:47:04'),
(126, 44, 200.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 15:54:21'),
(127, 44, 100.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 15:55:22'),
(128, 44, 100.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 16:07:42'),
(129, 44, 100.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 16:22:40'),
(130, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 16:42:08'),
(131, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 16:50:21'),
(132, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 17:02:17'),
(133, 44, 120.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 17:10:21'),
(134, 44, 199.20, 'we, we, 21121, Johor', 'prepare', '2025-05-24 17:17:06'),
(135, 44, 35.00, 'we, we, 21121, Johor', 'prepare', '2025-05-24 17:23:29'),
(136, 44, 508.00, 'we, we, 21121, Johor', 'prepare', '2025-05-25 15:35:45');

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
(82, 75, 5, 'UK7.5', 6, 120.00),
(83, 76, 6, 'UK8.5', 4, 120.00),
(84, 77, 6, 'UK8.5', 1, 120.00),
(85, 78, 18, 'XXS', 1, 100.00),
(86, 79, 6, 'UK8.5', 1, 120.00),
(87, 80, 5, 'UK7.5', 1, 120.00),
(88, 81, 18, 'XXS', 1, 100.00),
(89, 82, 22, 'M', 1, 50.00),
(90, 83, 22, 'M', 1, 50.00),
(91, 84, 23, 'XXS', 1, 80.50),
(92, 84, 52, 'One Size', 2, 28.00),
(93, 85, 23, 'XXS', 1, 80.50),
(94, 85, 52, 'One Size', 2, 28.00),
(95, 86, 6, 'UK8.5', 1, 120.00),
(96, 87, 25, 'UK13.5C', 1, 114.50),
(97, 88, 42, 'UK6', 1, 199.20),
(98, 89, 27, 'UK13.5C', 1, 419.30),
(99, 90, 6, 'UK8.5', 5, 120.00),
(100, 90, 22, 'S', 1, 50.00),
(101, 90, 23, 'XXS', 24, 80.50),
(102, 91, 6, 'UK8.5', 87, 120.00),
(103, 91, 38, 'XXS', 8, 99.00),
(104, 91, 43, 'UK3.5Y', 11, 200.00),
(105, 92, 42, 'UK6', 1, 199.20),
(106, 92, 19, 'UK10.5', 5, 150.00),
(107, 92, 56, 'One Size', 6, 339.00),
(108, 92, 29, 'UK6', 2, 179.50),
(109, 93, 24, 'XXS', 1, 35.00),
(110, 94, 6, 'UK10', 1, 120.00),
(111, 95, 6, 'UK9', 14, 120.00),
(112, 95, 21, 'One Size', 2, 100.00),
(113, 95, 25, 'UK13.5C', 4, 114.50),
(114, 95, 60, 'One Size', 1, 99.00),
(115, 96, 42, 'UK6', 3, 199.20),
(116, 97, 42, 'UK6', 3, 199.20),
(117, 98, 19, 'UK10.5', 1, 150.00),
(118, 98, 42, 'UK6', 3, 199.20),
(119, 98, 55, 'One Size', 1, 69.00),
(120, 99, 19, 'UK10.5', 1, 150.00),
(121, 99, 42, 'UK6', 3, 199.20),
(122, 99, 55, 'One Size', 1, 69.00),
(123, 100, 19, 'UK10.5', 1, 150.00),
(124, 100, 42, 'UK6', 3, 199.20),
(125, 100, 55, 'One Size', 1, 69.00),
(126, 101, 21, 'One Size', 3, 100.00),
(127, 102, 20, 'UK5', 1, 120.00),
(128, 103, 6, 'UK9', 1, 120.00),
(129, 104, 19, 'UK10.5', 1, 150.00),
(130, 105, 42, 'UK6', 1, 199.20),
(131, 106, 43, 'UK3.5Y', 1, 200.00),
(132, 107, 6, 'UK9', 1, 120.00),
(133, 108, 42, 'UK6', 1, 199.20),
(134, 109, 43, 'UK3.5Y', 1, 200.00),
(135, 110, 43, 'UK3.5Y', 1, 200.00),
(136, 111, 43, 'UK3.5Y', 1, 200.00),
(137, 112, 42, 'UK6', 1, 199.20),
(138, 113, 42, 'UK6', 1, 199.20),
(139, 114, 42, 'UK6', 1, 199.20),
(140, 115, 42, 'UK6', 1, 199.20),
(141, 116, 6, 'UK9', 1, 120.00),
(142, 117, 43, 'UK3.5Y', 1, 200.00),
(143, 118, 6, 'UK9', 1, 120.00),
(144, 118, 43, 'UK3.5Y', 1, 200.00),
(145, 119, 42, 'UK6', 2, 199.20),
(146, 120, 43, 'UK3.5Y', 1, 200.00),
(147, 121, 43, 'UK3.5Y', 1, 200.00),
(148, 122, 43, 'UK3.5Y', 1, 200.00),
(149, 123, 43, 'UK3.5Y', 1, 200.00),
(150, 124, 43, 'UK3.5Y', 1, 200.00),
(151, 125, 43, 'UK3.5Y', 1, 200.00),
(152, 126, 43, 'UK3.5Y', 1, 200.00),
(153, 127, 18, 'XXS', 1, 100.00),
(154, 128, 18, 'XXS', 1, 100.00),
(155, 129, 18, 'XXS', 1, 100.00),
(156, 130, 6, 'UK9', 1, 120.00),
(157, 131, 6, 'UK9', 1, 120.00),
(158, 132, 6, 'UK9', 1, 120.00),
(159, 133, 6, 'UK9', 1, 120.00),
(160, 134, 42, 'UK6', 1, 199.20),
(161, 135, 24, 'XXS', 1, 35.00),
(162, 136, 22, 'M', 1, 50.00),
(163, 136, 25, 'UK13.5C', 4, 114.50);

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
('pay_680130a75a73f1.08739427f1f002844', 14, 220.00, 'pending', 'stripe_checkout', 'cs_test_b1GBYfS3ayD7lZy9mvsiA13efyTEMkWBNAuYvnqrVOOoVH1GpVJSEQfUrp', 'MYR', '2025-04-17 16:47:35', NULL, NULL),
('pay_680130b1493105.559011928f97441b0', 14, 220.00, 'pending', 'stripe_checkout', 'cs_test_b1RwSVk9nNffJTsUEkUuPsDe11Y3LqoxfGTT0hnjeyFV8ol38lXI0I49DH', 'MYR', '2025-04-17 16:47:45', NULL, NULL),
('pay_680130d6632ab5.43754763d8198062f', 15, 120.00, 'pending', 'stripe_checkout', 'cs_test_a1fKRL83d2nzmnerhRNeP8XBEREO7YxctOvgR5wTBWTDziza8RsbNtZaKw', 'MYR', '2025-04-17 16:48:22', NULL, NULL),
('pay_680135711045c8.935201784d9d27779', 15, 120.00, 'pending', 'stripe_checkout', 'cs_test_a1h29nR6GiU3E7Bwgx2dq0bednliqhgk5xWPjETfvENBkxGrvba4cc5pAR', 'MYR', '2025-04-17 17:08:01', NULL, NULL),
('pay_6802157a7542a5.72784802c5ee9dce8', 22, 120.00, 'completed', 'stripe_card', 'ch_3RFAq1QZPLk7FzRY1ae48wO1', 'MYR', '2025-04-18 09:03:54', NULL, NULL),
('pay_680283fd7ece22.88145433e8992ca27', 42, 100.00, 'completed', 'stripe_card', 'ch_3RFICLQZPLk7FzRY5eaYJ2ug', 'MYR', '2025-04-18 16:55:25', NULL, NULL),
('pay_6802887305fb20.6485519629c399388', 45, 100.00, 'completed', 'stripe_card', 'ch_3RFIUkQZPLk7FzRY1PiIassd', 'MYR', '2025-04-18 17:14:27', NULL, NULL),
('pay_68064d72139975.859046540e5a033d3', 64, 80.00, 'completed', 'stripe_card', 'ch_3RGKlBQZPLk7FzRY4NEasPjk', 'MYR', '2025-04-21 13:51:46', NULL, NULL),
('pay_68064ed2f1ef50.62990797139cd3e58', 65, 720.00, 'completed', 'stripe_card', 'ch_3RGKqsQZPLk7FzRY4UErhYRU', 'MYR', '2025-04-21 13:57:38', NULL, NULL),
('pay_6806505942c225.52764427e5c3b9bf2', 66, 720.00, 'completed', 'stripe_card', 'ch_3RGKxAQZPLk7FzRY2TolkfW8', 'MYR', '2025-04-21 14:04:09', NULL, NULL),
('pay_680662461618b5.16208218bab0fefa8', 57, 80.00, 'completed', 'stripe_card', 'ch_3RGM9BQZPLk7FzRY4BsUJvIa', 'MYR', '2025-04-21 15:20:38', NULL, NULL),
('pay_68066273c8e539.19548579d3e7bb2b0', 68, 720.00, 'completed', 'stripe_card', 'ch_3RGM9uQZPLk7FzRY5iK4qoHA', 'MYR', '2025-04-21 15:21:23', NULL, NULL),
('pay_6806645e5913a1.82369492dd22fd053', 70, 720.00, 'completed', 'stripe_card', 'ch_3RGMHpQZPLk7FzRY5LPB9apD', 'MYR', '2025-04-21 15:29:34', NULL, NULL),
('pay_680667b12cb623.25780585de1aeb77e', 72, 80.00, 'completed', 'stripe_card', 'ch_3RGMVYQZPLk7FzRY2r4QFevE', 'MYR', '2025-04-21 15:43:45', 1745250224, NULL),
('pay_68066a2194e729.03470571b9e694650', 73, 600.00, 'completed', 'stripe_card', 'ch_3RGMfcQZPLk7FzRY4i8bFkM8', 'MYR', '2025-04-21 15:54:09', 1745250848, NULL),
('pay_68066aa8cd5786.17353337c0bc7dd79', 74, 9900.00, 'completed', 'stripe_card', 'ch_3RGMhnQZPLk7FzRY53XruMF0', 'MYR', '2025-04-21 15:56:24', 1745250983, NULL),
('pay_6809b5a2c6fe76.698119968f0722a5c', 76, 480.00, 'completed', 'stripe_card', 'ch_3RHGqVQZPLk7FzRY2ICNCdH2', 'MYR', '2025-04-24 03:53:06', NULL, NULL),
('pay_6809d8b82457b3.784286151709a4f77', 81, 100.00, 'completed', 'stripe_card', 'ch_3RHJBNQZPLk7FzRY5ru9Vf0T', 'MYR', '2025-04-24 06:22:48', NULL, NULL),
('pay_6809d97c739ec5.09380345788f08b66', 79, 120.00, 'completed', 'stripe_card', 'ch_3RHJEXQZPLk7FzRY5R32YjT6', 'MYR', '2025-04-24 06:26:04', NULL, NULL),
('pay_6809dcf2c62784.24670195d7c2a11f3', 82, 50.00, 'completed', 'stripe_card', 'ch_3RHJSpQZPLk7FzRY3u9ZOlI4', 'MYR', '2025-04-24 06:40:50', NULL, NULL),
('pay_680ba3e4549d93.08830899bf36ceaa7', 89, 419.30, 'completed', 'stripe_card', 'ch_3RHnlHQZPLk7FzRY0YD5GMU2', 'MYR', '2025-04-25 15:01:56', NULL, NULL),
('pay_680baeab056c03.05289107d5ed801ab', 90, 2582.00, 'completed', 'stripe_card', 'ch_3RHoTmQZPLk7FzRY3fB7GyBu', 'MYR', '2025-04-25 15:47:55', NULL, NULL),
('pay_680bb1e49c9ab3.6701615729597ff05', 91, 13432.00, 'completed', 'stripe_card', 'ch_3RHoh5QZPLk7FzRY2MgSoQed', 'MYR', '2025-04-25 16:01:40', NULL, NULL),
('pay_680bc1fc150078.33028383cd0dfb04f', 92, 3342.20, 'completed', 'stripe_card', 'ch_3RHplXQZPLk7FzRY4I3jua9k', 'MYR', '2025-04-25 17:10:20', NULL, NULL),
('pay_682d4b8ccdd2e8.8515589618fb103cc', 95, 2437.00, 'completed', 'stripe_card', 'ch_3RR3XcQZPLk7FzRY40xBilVT', 'MYR', '2025-05-21 03:42:04', NULL, NULL),
('pay_68315e9d915a96.290532364924a4bdd', 101, 300.00, 'completed', 'stripe_card', 'ch_3RSB0UQZPLk7FzRY3yh7XHSX', 'MYR', '2025-05-24 05:52:29', NULL, NULL),
('pay_68315f2e4510a5.72649841be5ef30fc', 102, 120.00, 'completed', 'stripe_card', 'ch_3RSB2oQZPLk7FzRY3uWyy4DU', 'MYR', '2025-05-24 05:54:54', NULL, NULL),
('pay_68316164e72bd7.060775456ca48e337', 103, 120.00, 'completed', 'stripe_card', 'ch_3RSBBxQZPLk7FzRY5EUjoXCq', 'MYR', '2025-05-24 06:04:20', NULL, NULL),
('pay_6831634c3550b7.4897688343505833a', 104, 150.00, 'completed', 'stripe_card', 'ch_3RSBJoQZPLk7FzRY5XZzVYgK', 'MYR', '2025-05-24 06:12:28', NULL, NULL),
('pay_683163f66cf244.184906871c446e710', 105, 199.20, 'completed', 'stripe_card', 'ch_3RSBMYQZPLk7FzRY4WUbC7wY', 'MYR', '2025-05-24 06:15:18', NULL, NULL),
('pay_683165285f2db3.39596352069998996', 106, 200.00, 'completed', 'stripe_card', 'ch_3RSBRUQZPLk7FzRY0VJXU3Yb', 'MYR', '2025-05-24 06:20:24', NULL, NULL),
('pay_68316711877429.3284210847b2ae021', 107, 120.00, 'completed', 'stripe_card', 'ch_3RSBZOQZPLk7FzRY4IFhVp2j', 'MYR', '2025-05-24 06:28:33', NULL, NULL),
('pay_683167fc76a4f3.9813749979e0ddfd1', 108, 199.20, 'completed', 'stripe_card', 'ch_3RSBdBQZPLk7FzRY4CqkF6zF', 'MYR', '2025-05-24 06:32:28', NULL, NULL),
('pay_68316bd1c59603.793032614bfadccd9', 109, 200.00, 'completed', 'stripe_card', 'ch_3RSBt0QZPLk7FzRY5cSaH3bk', 'MYR', '2025-05-24 06:48:49', NULL, NULL),
('pay_68316cf3d9ec91.523732843ee6aa41e', 110, 200.00, 'completed', 'stripe_card', 'ch_3RSBxgQZPLk7FzRY2D1TWfxl', 'MYR', '2025-05-24 06:53:39', NULL, NULL),
('pay_68316e5151a974.86305275f705a427f', 111, 200.00, 'completed', 'stripe_card', 'ch_3RSC3JQZPLk7FzRY51Qtwp09', 'MYR', '2025-05-24 06:59:29', NULL, NULL),
('pay_6831ebad697194.92211992b9bbda51a', 126, 200.00, 'completed', 'stripe_card', 'ch_3RSKOwQZPLk7FzRY0wCT7xZQ', 'MYR', '2025-05-24 15:54:21', NULL, NULL),
('pay_6831ebea1b6104.56999963f6754ea24', 127, 100.00, 'completed', 'stripe_card', 'ch_3RSKPvQZPLk7FzRY3WmX5jC5', 'MYR', '2025-05-24 15:55:22', NULL, NULL),
('pay_6831eecea8a043.68697143840854741', 128, 100.00, 'completed', 'stripe_card', 'ch_3RSKbrQZPLk7FzRY5OPPLKWo', 'MYR', '2025-05-24 16:07:42', NULL, NULL),
('pay_6831f250c40766.5463438725ab6a319', 129, 100.00, 'completed', 'stripe_card', 'ch_3RSKqKQZPLk7FzRY0kXPlgks', 'MYR', '2025-05-24 16:22:40', NULL, NULL),
('pay_6831f6e0ca3e22.7859032350d111445', 130, 120.00, 'completed', 'stripe_card', 'ch_3RSL9AQZPLk7FzRY4xBFoyKb', 'MYR', '2025-05-24 16:42:08', NULL, NULL),
('pay_6831f8cd575a15.868988443134bb590', 131, 120.00, 'completed', 'stripe_card', 'ch_3RSLH6QZPLk7FzRY2jBb76TJ', 'MYR', '2025-05-24 16:50:21', NULL, NULL),
('pay_6831fb99b99d73.61539453f946ea6d9', 132, 120.00, 'completed', 'stripe_card', 'ch_3RSLSfQZPLk7FzRY424ezJJp', 'MYR', '2025-05-24 17:02:17', NULL, NULL),
('pay_6831fd7d7e4008.392106452e8634437', 133, 120.00, 'completed', 'stripe_card', 'ch_3RSLaSQZPLk7FzRY0ya7keqn', 'MYR', '2025-05-24 17:10:21', NULL, NULL),
('pay_6831ff12e90684.445665748b61d346e', 134, 199.20, 'completed', 'stripe_card', 'ch_3RSLh0QZPLk7FzRY05ayUNdd', 'MYR', '2025-05-24 17:17:06', NULL, NULL),
('pay_68320091d173a8.90581603257e22302', 135, 35.00, 'completed', 'stripe_card', 'ch_3RSLnBQZPLk7FzRY5Dv0Y7AB', 'MYR', '2025-05-24 17:23:29', NULL, NULL),
('pay_683338d11ae8a6.167692841e409d476', 136, 508.00, 'completed', 'stripe_card', 'ch_3RSgaTQZPLk7FzRY5Hnp8BdU', 'MYR', '2025-05-25 15:35:45', NULL, NULL),
('pay_fail_68027e32806d83.22837620', 31, 155.00, 'failed', 'card', '', 'MYR', '2025-04-18 16:30:42', NULL, 'Must provide source or customer.');

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
(1, 'pay_680130a75a73f1.08739427f1f002844', 'info', 'Checkout session created: cs_test_b1GBYfS3ayD7lZy9mvsiA13efyTEMkWBNAuYvnqrVOOoVH1GpVJSEQfUrp', '2025-04-17 16:47:35'),
(2, 'pay_680130b1493105.559011928f97441b0', 'info', 'Checkout session created: cs_test_b1RwSVk9nNffJTsUEkUuPsDe11Y3LqoxfGTT0hnjeyFV8ol38lXI0I49DH', '2025-04-17 16:47:45'),
(3, 'pay_680130d6632ab5.43754763d8198062f', 'info', 'Checkout session created: cs_test_a1fKRL83d2nzmnerhRNeP8XBEREO7YxctOvgR5wTBWTDziza8RsbNtZaKw', '2025-04-17 16:48:22'),
(4, 'pay_680135711045c8.935201784d9d27779', 'info', 'Checkout session created: cs_test_a1h29nR6GiU3E7Bwgx2dq0bednliqhgk5xWPjETfvENBkxGrvba4cc5pAR', '2025-04-17 17:08:01'),
(5, 'pay_6802157a7542a5.72784802c5ee9dce8', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RFAq1QZPLk7FzRY1ae48wO1', '2025-04-18 09:03:54'),
(6, 'pay_680283fd7ece22.88145433e8992ca27', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RFICLQZPLk7FzRY5eaYJ2ug', '2025-04-18 16:55:25'),
(9, 'pay_6802887305fb20.6485519629c399388', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RFIUkQZPLk7FzRY1PiIassd', '2025-04-18 17:14:27'),
(26, 'pay_68064d72139975.859046540e5a033d3', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGKlBQZPLk7FzRY4NEasPjk', '2025-04-21 13:51:46'),
(27, 'pay_68064d72139975.859046540e5a033d3', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 13:51:46'),
(28, 'pay_68064d72139975.859046540e5a033d3', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 13:51:46'),
(29, 'pay_68064ed2f1ef50.62990797139cd3e58', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGKqsQZPLk7FzRY4UErhYRU', '2025-04-21 13:57:38'),
(30, 'pay_68064ed2f1ef50.62990797139cd3e58', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 13:57:38'),
(31, 'pay_68064ed2f1ef50.62990797139cd3e58', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 13:57:38'),
(32, 'pay_6806505942c225.52764427e5c3b9bf2', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGKxAQZPLk7FzRY2TolkfW8', '2025-04-21 14:04:09'),
(33, 'pay_6806505942c225.52764427e5c3b9bf2', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 14:04:09'),
(34, 'pay_6806505942c225.52764427e5c3b9bf2', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 14:04:09'),
(35, 'pay_680662461618b5.16208218bab0fefa8', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGM9BQZPLk7FzRY4BsUJvIa', '2025-04-21 15:20:38'),
(36, 'pay_680662461618b5.16208218bab0fefa8', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:20:38'),
(37, 'pay_680662461618b5.16208218bab0fefa8', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:20:38'),
(38, 'pay_68066273c8e539.19548579d3e7bb2b0', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGM9uQZPLk7FzRY5iK4qoHA', '2025-04-21 15:21:23'),
(39, 'pay_68066273c8e539.19548579d3e7bb2b0', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:21:23'),
(40, 'pay_68066273c8e539.19548579d3e7bb2b0', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:21:23'),
(41, 'pay_6806645e5913a1.82369492dd22fd053', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGMHpQZPLk7FzRY5LPB9apD', '2025-04-21 15:29:34'),
(42, 'pay_6806645e5913a1.82369492dd22fd053', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:29:34'),
(43, 'pay_6806645e5913a1.82369492dd22fd053', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:29:34'),
(44, 'pay_680667b12cb623.25780585de1aeb77e', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGMVYQZPLk7FzRY2r4QFevE', '2025-04-21 15:43:45'),
(45, 'pay_680667b12cb623.25780585de1aeb77e', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:43:45'),
(46, 'pay_680667b12cb623.25780585de1aeb77e', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:43:45'),
(47, 'pay_680667b12cb623.25780585de1aeb77e', 'info', 'Payment completed via webhook (charge.succeeded) | Stripe ID: ch_3RGMVYQZPLk7FzRY2r4QFevE', '2025-04-21 15:43:45'),
(48, 'pay_680667b12cb623.25780585de1aeb77e', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:43:45'),
(49, 'pay_680667b12cb623.25780585de1aeb77e', 'error', 'Stock update/verification failed (charge.succeeded): Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:43:45'),
(50, 'pay_68066a2194e729.03470571b9e694650', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGMfcQZPLk7FzRY4i8bFkM8', '2025-04-21 15:54:09'),
(51, 'pay_68066a2194e729.03470571b9e694650', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:54:09'),
(52, 'pay_68066a2194e729.03470571b9e694650', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:54:09'),
(53, 'pay_68066a2194e729.03470571b9e694650', 'info', 'Payment completed via webhook (charge.succeeded) | Stripe ID: ch_3RGMfcQZPLk7FzRY4i8bFkM8', '2025-04-21 15:54:10'),
(54, 'pay_68066a2194e729.03470571b9e694650', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:54:10'),
(55, 'pay_68066a2194e729.03470571b9e694650', 'error', 'Stock update/verification failed (charge.succeeded): Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:54:10'),
(56, 'pay_68066aa8cd5786.17353337c0bc7dd79', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RGMhnQZPLk7FzRY53XruMF0', '2025-04-21 15:56:24'),
(57, 'pay_68066aa8cd5786.17353337c0bc7dd79', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:56:24'),
(58, 'pay_68066aa8cd5786.17353337c0bc7dd79', 'error', 'Payment verification/stock update error: Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:56:24'),
(59, 'pay_68066aa8cd5786.17353337c0bc7dd79', 'info', 'Payment completed via webhook (charge.succeeded) | Stripe ID: ch_3RGMhnQZPLk7FzRY53XruMF0', '2025-04-21 15:56:25'),
(60, 'pay_68066aa8cd5786.17353337c0bc7dd79', 'error', 'Stripe verification error: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:56:25'),
(61, 'pay_68066aa8cd5786.17353337c0bc7dd79', 'error', 'Stock update/verification failed (charge.succeeded): Failed to verify payment with Stripe: No API key provided.  (HINT: set your API key using \"Stripe::setApiKey(<API-KEY>)\".  You can generate API keys from the Stripe web interface.  See https://stripe.com/api for details, or email support@stripe.com if you have any questions.', '2025-04-21 15:56:25'),
(62, 'pay_6809b5a2c6fe76.698119968f0722a5c', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHGqVQZPLk7FzRY2ICNCdH2', '2025-04-24 03:53:06'),
(63, 'pay_6809b5a2c6fe76.698119968f0722a5c', 'info', 'Stripe verification: Confirmed successful', '2025-04-24 03:53:07'),
(64, 'pay_6809b5a2c6fe76.698119968f0722a5c', 'info', 'Stock updated successfully after payment verification', '2025-04-24 03:53:07'),
(65, 'pay_6809d8b82457b3.784286151709a4f77', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHJBNQZPLk7FzRY5ru9Vf0T', '2025-04-24 06:22:48'),
(66, 'pay_6809d8b82457b3.784286151709a4f77', 'info', 'Stripe verification: Confirmed successful', '2025-04-24 06:22:48'),
(67, 'pay_6809d8b82457b3.784286151709a4f77', 'info', 'Stock updated successfully after payment verification', '2025-04-24 06:22:48'),
(68, 'pay_6809d97c739ec5.09380345788f08b66', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHJEXQZPLk7FzRY5R32YjT6', '2025-04-24 06:26:04'),
(69, 'pay_6809d97c739ec5.09380345788f08b66', 'info', 'Stripe verification: Confirmed successful', '2025-04-24 06:26:04'),
(70, 'pay_6809d97c739ec5.09380345788f08b66', 'info', 'Stock updated successfully after payment verification', '2025-04-24 06:26:04'),
(71, 'pay_6809dcf2c62784.24670195d7c2a11f3', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHJSpQZPLk7FzRY3u9ZOlI4', '2025-04-24 06:40:50'),
(72, 'pay_6809dcf2c62784.24670195d7c2a11f3', 'info', 'Stripe verification: Confirmed successful', '2025-04-24 06:40:51'),
(73, 'pay_6809dcf2c62784.24670195d7c2a11f3', 'info', 'Stock updated successfully after payment verification', '2025-04-24 06:40:51'),
(74, 'pay_680ba3e4549d93.08830899bf36ceaa7', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHnlHQZPLk7FzRY0YD5GMU2', '2025-04-25 15:01:56'),
(75, 'pay_680ba3e4549d93.08830899bf36ceaa7', 'info', 'Stripe verification: Confirmed successful', '2025-04-25 15:01:56'),
(76, 'pay_680ba3e4549d93.08830899bf36ceaa7', 'info', 'Stock updated successfully after payment verification', '2025-04-25 15:01:56'),
(77, 'pay_680baeab056c03.05289107d5ed801ab', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHoTmQZPLk7FzRY3fB7GyBu', '2025-04-25 15:47:55'),
(78, 'pay_680baeab056c03.05289107d5ed801ab', 'info', 'Stripe verification: Confirmed successful', '2025-04-25 15:47:55'),
(79, 'pay_680baeab056c03.05289107d5ed801ab', 'info', 'Stock updated successfully after payment verification', '2025-04-25 15:47:55'),
(80, 'pay_680bb1e49c9ab3.6701615729597ff05', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHoh5QZPLk7FzRY2MgSoQed', '2025-04-25 16:01:40'),
(81, 'pay_680bb1e49c9ab3.6701615729597ff05', 'info', 'Stripe verification: Confirmed successful', '2025-04-25 16:01:40'),
(82, 'pay_680bb1e49c9ab3.6701615729597ff05', 'info', 'Stock updated successfully after payment verification', '2025-04-25 16:01:40'),
(83, 'pay_680bc1fc150078.33028383cd0dfb04f', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RHplXQZPLk7FzRY4I3jua9k', '2025-04-25 17:10:20'),
(84, 'pay_680bc1fc150078.33028383cd0dfb04f', 'info', 'Stripe verification: Confirmed successful', '2025-04-25 17:10:20'),
(85, 'pay_680bc1fc150078.33028383cd0dfb04f', 'info', 'Stock updated successfully after payment verification', '2025-04-25 17:10:20'),
(86, 'pay_682d4b8ccdd2e8.8515589618fb103cc', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RR3XcQZPLk7FzRY40xBilVT', '2025-05-21 03:42:04'),
(87, 'pay_682d4b8ccdd2e8.8515589618fb103cc', 'info', 'Stripe verification: Confirmed successful', '2025-05-21 03:42:05'),
(88, 'pay_682d4b8ccdd2e8.8515589618fb103cc', 'info', 'Stock updated successfully after payment verification', '2025-05-21 03:42:05'),
(89, 'pay_68315e9d915a96.290532364924a4bdd', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSB0UQZPLk7FzRY3yh7XHSX', '2025-05-24 05:52:29'),
(90, 'pay_68315e9d915a96.290532364924a4bdd', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 05:52:29'),
(91, 'pay_68315e9d915a96.290532364924a4bdd', 'info', 'Stock updated successfully after payment verification', '2025-05-24 05:52:29'),
(92, 'pay_68315f2e4510a5.72649841be5ef30fc', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSB2oQZPLk7FzRY3uWyy4DU', '2025-05-24 05:54:54'),
(93, 'pay_68315f2e4510a5.72649841be5ef30fc', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 05:54:54'),
(94, 'pay_68315f2e4510a5.72649841be5ef30fc', 'info', 'Stock updated successfully after payment verification', '2025-05-24 05:54:54'),
(95, 'pay_68316164e72bd7.060775456ca48e337', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBBxQZPLk7FzRY5EUjoXCq', '2025-05-24 06:04:20'),
(96, 'pay_68316164e72bd7.060775456ca48e337', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:04:21'),
(97, 'pay_68316164e72bd7.060775456ca48e337', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:04:21'),
(98, 'pay_6831634c3550b7.4897688343505833a', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBJoQZPLk7FzRY5XZzVYgK', '2025-05-24 06:12:28'),
(99, 'pay_6831634c3550b7.4897688343505833a', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:12:28'),
(100, 'pay_6831634c3550b7.4897688343505833a', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:12:28'),
(101, 'pay_683163f66cf244.184906871c446e710', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBMYQZPLk7FzRY4WUbC7wY', '2025-05-24 06:15:18'),
(102, 'pay_683163f66cf244.184906871c446e710', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:15:18'),
(103, 'pay_683163f66cf244.184906871c446e710', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:15:18'),
(104, 'pay_683165285f2db3.39596352069998996', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBRUQZPLk7FzRY0VJXU3Yb', '2025-05-24 06:20:24'),
(105, 'pay_683165285f2db3.39596352069998996', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:20:24'),
(106, 'pay_683165285f2db3.39596352069998996', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:20:24'),
(107, 'pay_68316711877429.3284210847b2ae021', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBZOQZPLk7FzRY4IFhVp2j', '2025-05-24 06:28:33'),
(108, 'pay_68316711877429.3284210847b2ae021', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:28:33'),
(109, 'pay_68316711877429.3284210847b2ae021', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:28:33'),
(110, 'pay_683167fc76a4f3.9813749979e0ddfd1', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBdBQZPLk7FzRY4CqkF6zF', '2025-05-24 06:32:28'),
(111, 'pay_683167fc76a4f3.9813749979e0ddfd1', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:32:28'),
(112, 'pay_683167fc76a4f3.9813749979e0ddfd1', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:32:28'),
(113, 'pay_68316bd1c59603.793032614bfadccd9', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBt0QZPLk7FzRY5cSaH3bk', '2025-05-24 06:48:49'),
(114, 'pay_68316bd1c59603.793032614bfadccd9', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:48:50'),
(115, 'pay_68316bd1c59603.793032614bfadccd9', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:48:50'),
(116, 'pay_68316cf3d9ec91.523732843ee6aa41e', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSBxgQZPLk7FzRY2D1TWfxl', '2025-05-24 06:53:39'),
(117, 'pay_68316cf3d9ec91.523732843ee6aa41e', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:53:40'),
(118, 'pay_68316cf3d9ec91.523732843ee6aa41e', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:53:40'),
(119, 'pay_68316e5151a974.86305275f705a427f', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSC3JQZPLk7FzRY51Qtwp09', '2025-05-24 06:59:29'),
(120, 'pay_68316e5151a974.86305275f705a427f', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 06:59:29'),
(121, 'pay_68316e5151a974.86305275f705a427f', 'info', 'Stock updated successfully after payment verification', '2025-05-24 06:59:29'),
(122, 'pay_68316e5151a974.86305275f705a427f', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 06:59:39'),
(123, 'pay_6831ebad697194.92211992b9bbda51a', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSKOwQZPLk7FzRY0wCT7xZQ', '2025-05-24 15:54:21'),
(124, 'pay_6831ebad697194.92211992b9bbda51a', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 15:54:21'),
(125, 'pay_6831ebad697194.92211992b9bbda51a', 'info', 'Stock updated successfully after payment verification', '2025-05-24 15:54:21'),
(126, 'pay_6831ebea1b6104.56999963f6754ea24', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSKPvQZPLk7FzRY3WmX5jC5', '2025-05-24 15:55:22'),
(127, 'pay_6831ebea1b6104.56999963f6754ea24', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 15:55:22'),
(128, 'pay_6831ebea1b6104.56999963f6754ea24', 'info', 'Stock updated successfully after payment verification', '2025-05-24 15:55:22'),
(129, 'pay_6831eecea8a043.68697143840854741', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSKbrQZPLk7FzRY5OPPLKWo', '2025-05-24 16:07:42'),
(130, 'pay_6831eecea8a043.68697143840854741', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 16:07:43'),
(131, 'pay_6831eecea8a043.68697143840854741', 'info', 'Stock updated successfully after payment verification', '2025-05-24 16:07:43'),
(132, 'pay_6831f250c40766.5463438725ab6a319', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSKqKQZPLk7FzRY0kXPlgks', '2025-05-24 16:22:40'),
(133, 'pay_6831f250c40766.5463438725ab6a319', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 16:22:41'),
(134, 'pay_6831f250c40766.5463438725ab6a319', 'info', 'Stock updated successfully after payment verification', '2025-05-24 16:22:41'),
(135, 'pay_6831f6e0ca3e22.7859032350d111445', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSL9AQZPLk7FzRY4xBFoyKb', '2025-05-24 16:42:08'),
(136, 'pay_6831f6e0ca3e22.7859032350d111445', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 16:42:09'),
(137, 'pay_6831f6e0ca3e22.7859032350d111445', 'info', 'Stock updated successfully after payment verification', '2025-05-24 16:42:09'),
(138, 'pay_6831f6e0ca3e22.7859032350d111445', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 16:42:15'),
(139, 'pay_6831f8cd575a15.868988443134bb590', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSLH6QZPLk7FzRY2jBb76TJ', '2025-05-24 16:50:21'),
(140, 'pay_6831f8cd575a15.868988443134bb590', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 16:50:21'),
(141, 'pay_6831f8cd575a15.868988443134bb590', 'info', 'Stock updated successfully after payment verification', '2025-05-24 16:50:21'),
(142, 'pay_6831f8cd575a15.868988443134bb590', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 16:50:27'),
(143, 'pay_6831fb99b99d73.61539453f946ea6d9', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSLSfQZPLk7FzRY424ezJJp', '2025-05-24 17:02:17'),
(144, 'pay_6831fb99b99d73.61539453f946ea6d9', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 17:02:18'),
(145, 'pay_6831fb99b99d73.61539453f946ea6d9', 'info', 'Stock updated successfully after payment verification', '2025-05-24 17:02:18'),
(146, 'pay_6831fb99b99d73.61539453f946ea6d9', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 17:02:23'),
(147, 'pay_6831fd7d7e4008.392106452e8634437', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSLaSQZPLk7FzRY0ya7keqn', '2025-05-24 17:10:21'),
(148, 'pay_6831fd7d7e4008.392106452e8634437', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 17:10:21'),
(149, 'pay_6831fd7d7e4008.392106452e8634437', 'info', 'Stock updated successfully after payment verification', '2025-05-24 17:10:21'),
(150, 'pay_6831fd7d7e4008.392106452e8634437', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 17:10:27'),
(151, 'pay_6831ff12e90684.445665748b61d346e', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSLh0QZPLk7FzRY05ayUNdd', '2025-05-24 17:17:06'),
(152, 'pay_6831ff12e90684.445665748b61d346e', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 17:17:07'),
(153, 'pay_6831ff12e90684.445665748b61d346e', 'info', 'Stock updated successfully after payment verification', '2025-05-24 17:17:07'),
(154, 'pay_6831ff12e90684.445665748b61d346e', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 17:17:13'),
(155, 'pay_68320091d173a8.90581603257e22302', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSLnBQZPLk7FzRY5Dv0Y7AB', '2025-05-24 17:23:29'),
(156, 'pay_68320091d173a8.90581603257e22302', 'info', 'Stripe verification: Confirmed successful', '2025-05-24 17:23:30'),
(157, 'pay_68320091d173a8.90581603257e22302', 'info', 'Stock updated successfully after payment verification', '2025-05-24 17:23:30'),
(158, 'pay_68320091d173a8.90581603257e22302', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 17:23:36'),
(159, 'pay_68320091d173a8.90581603257e22302', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-24 17:23:46'),
(160, 'pay_683338d11ae8a6.167692841e409d476', 'info', 'Payment completed successfully via card payment. Stripe ID: ch_3RSgaTQZPLk7FzRY5Hnp8BdU', '2025-05-25 15:35:45'),
(161, 'pay_683338d11ae8a6.167692841e409d476', 'info', 'Stripe verification: Confirmed successful', '2025-05-25 15:35:45'),
(162, 'pay_683338d11ae8a6.167692841e409d476', 'info', 'Stock updated successfully after payment verification', '2025-05-25 15:35:45'),
(163, 'pay_683338d11ae8a6.167692841e409d476', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-25 15:35:51'),
(164, 'pay_683338d11ae8a6.167692841e409d476', 'info', 'Invoice email sent successfully to kaiwen211105@gmail.com', '2025-05-25 15:36:00');

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
  `product_img2` varchar(255) DEFAULT NULL,
  `product_img3` varchar(255) DEFAULT NULL,
  `product_img4` varchar(255) DEFAULT NULL,
  `size_chart` varchar(255) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_type`, `brand`, `product_categories`, `gender`, `status`, `price`, `discount_price`, `description`, `product_img1`, `product_img2`, `product_img3`, `product_img4`, `size_chart`, `create_at`) VALUES
(5, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK', 'Footwear', 'Adidas', 'Running', 'Men', 'Normal', 120.00, 0.00, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK', '67f5e7b0c8aa6.jpg', '67f5e7b0c92b6.jpg', '67f5e7b0c9d2f.jpg', '67f5e7b0ca726.jpg', '67fcc26199200.jpg', '2025-04-03 04:48:01'),
(6, 'Nike Quest 6 Mens Road Running Shoes FD6033002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 120.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f5e6725324c.jpg', '67f5e67254561.jpg', '67f5e67254d38.jpg', '67f5e67255331.jpg', '67f5387e89e10.jpg', '2025-04-03 07:35:49'),
(7, 'Asics GT2000 13 LiteShow Mens Running Shoes Blue', 'Footwear', 'Asics', 'Running', 'Men', 'New', 180.00, 0.00, 'The GT200 13 running shoe offers great support for a comfortable run that will help you clear your mind. Its the most comfortable version of the shoe yet and the ideal choice if you are looking for extra guidance during your run.\r\n\r\n', '67f5eb24629b0.png', '67f5eb2462e7a.png', '67f5f044d935c.png', '67f5f044d9738.png', '67ee3aed4f0d6.jpg', '2025-04-03 07:38:21'),
(8, 'Nike Quest 6 Men Road Running Shoes FD6033-002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 369.00, 0.00, 'Nike Quest 6\r\nMen Road Running Shoes\r\nThe Nike Quest 6 is for runners of all levels. But make no mistake, it’s anything but entry-level. A supercomfortable and supportive midfoot fit band helps keep you stable for your miles. Plus, a supersoft midsole foam helps cushion each step.', '67f5efee89f95.png', '67f5efee8aad9.png', '67f5efee8aee7.png', '67f5efee8b26e.png', '67ee3d5b17ff8.jpg', '2025-04-03 07:48:43'),
(18, 'Nike ACD TOP NOV Men Training Jersey', 'Apparel', 'Nike', 'Jersey', 'Men', 'Promotion', 135.00, 100.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f63019d51ec.png', '67f63019d51f0.png', '67f63019d51f1.png', '67f714adddae5.png', '67f8d72022188.jpg', '2025-04-09 08:30:17'),
(19, 'Asics Gel Cumulus 27 Lite Show Mens Running Shoes Orange', 'Footwear', 'Asics', 'Running', 'Men', 'New', 150.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f631e68b401.png', '67f631e68b405.png', '67f631e68b407.png', '67f631e68b408.png', '67f631e68b409.jpg', '2025-04-09 08:37:58'),
(20, 'Nike Downshifter 13 Mens Road Running Shoes FD6454008', 'Footwear', 'Nike', 'Running', 'Men', 'Normal', 120.00, 0.00, 'Nike Downshifter 13\r\nMens Road Running Shoes\r\nWhether you are starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a revamped upper and cushion and durability, it helps you find that extra gear or take that first stride toward chasing down your goals.', '67f63260c09de.png', '67f63260c09e2.png', '67f63260c09e3.png', '67f63260c09e4.png', '67f63260c09e5.jpg', '2025-04-09 08:40:00'),
(21, 'Adidas EPP Club Ball WHITE HT2459', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'Normal', 100.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nAN EVERYDAY FOOTBALL FOR ANY DAY BALLERS.\r\nWhen your teammates come calling, always be prepared. This adidas EPP Club Ball was built for training sessions, kickabouts in the park and for showing friends your skills. Its durable, machine stitched surface means its ready to stand up to razor-sharp passes and high intensity scrimmages. The butyl bladder ensures optimal air retention, so you can worry less about the ball and focus more on your footwork.', '67f63303d6016.png', '67f63303d6018.png', '67f63303d6019.png', '67f63303d601a.png', '67f63303d601b.', '2025-04-09 08:42:43'),
(22, 'Umbro Men Jersey Blue', 'Apparel', 'Umbro', 'Jersey', 'Men', 'Normal', 50.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f6334ac43d9.png', '67f6334ac43dd.png', '67f6334ac43de.png', '67f6859bcf9d7.png', '67f6334ac43e0.jpg', '2025-04-09 08:43:54'),
(23, 'ADIDAS ENTRADA 22 MEN FOOTBALL JERSEY RED', 'Apparel', 'Adidas', 'Jersey', 'Men', 'Normal', 80.50, 0.00, 'ADIDAS ENTRADA 22 MENS FOOTBALL JERSEY RED', '67f633af8d002.png', '67f633af8d005.png', '67f633af8d006.png', '67f685b032d2a.png', '67f633af8d008.jpg', '2025-04-09 08:45:35'),
(24, 'Umbro 1924 Women Jersey MAROON', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Normal', 35.00, 0.00, 'Umbro 1924 Women Jersey MAROON', '67f63a639bb60.png', '67f63a639bb68.png', '67f63a639bb69.png', '67f685cf1fc6b.png', '67f63a639bb6b.jpg', '2025-04-09 09:14:11'),
(25, 'PUMA Scend Pro Men Running Shoes Red', 'Footwear', 'Puma', 'Running', 'Men', 'New', 114.50, 0.00, 'PRODUCT STORY\r\nIntroducing the Scend Pro, PUMA new essential runner. The Protread rubber outsole is designed for distance and enhanced with a bold paint line and cushioned tooling. The breathable mesh uppers are ultra lightweight, while the comfort collar locks down for a secure fit during workouts.', '67f8c2fcb865e.png', '67f8c2fcb8662.png', '67f8c2fcb8663.png', '67f8c2fcb8664.png', '67f8c2fcb8665.jpg', '2025-04-11 07:21:32'),
(26, 'Nike Downshifter 13 Men Running Shoes Black', 'Footwear', 'Nike', 'Running', 'Men', 'Normal', 255.00, 0.00, 'Nike Downshifter 13 Men Running Shoes Black', '67f8c66f0e148.png', '67f8c66f0e14c.png', '67f8c66f0e14d.png', '67f8c66f0e14e.png', '67f8c66f0e14f.jpg', '2025-04-11 07:36:15'),
(27, 'PUMA Velocity Nitro 3 Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'New', 419.30, 0.00, 'PRODUCT DESCRIPTIONS\r\nEmbark on your running journey with the VELOCITY NITR 3, PUMA s hero franchise featuring NITROFOA cushioning. Sleek styling, outstanding comfort, and a slightly higher stack height make it the ideal do-it-all trainer. From short distances to long runs, this neutral shoe offers a smooth ride and optimal cushioning for all runners. Experience the best in comfort and performance with the VELOCITY 3’.', '67f8c7497ab71.png', '67f8c7497ab78.png', '67f8c7497ab7a.png', '67f8c7497ab7b.png', '67f8c7497ab7c.jpg', '2025-04-11 07:39:53'),
(28, 'Puma Pounce Lite Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'Normal', 289.00, 0.00, 'The all-new Pounce Lite joins our Running line this year. Featuring an ultra-lightweight foam and a durable PROTREAD outsole, it is the ultimate blend of style and performance.', '67f8c8ff9d98d.png', '67f8c8ff9d990.png', '67f8c8ff9d991.png', '67f8c8ff9d992.png', '67f8c8ff9d993.jpg', '2025-04-11 07:47:11'),
(29, 'New Balance Fresh Foam 510 V6 Men Running Shoes Black', 'Footwear', 'New Balance', 'Running', 'Men', 'Promotion', 359.00, 179.50, 'PRODUCT DESCRIPTIONS\r\nA rugged shoe designed to help you conquer the roughest trails with ease.', '67f8ca88b98cf.png', '67f8ca88b98d2.png', '67f8ca88b98d4.png', '67f8ca88b98d5.png', '67f8ca88b98d6.jpg', '2025-04-11 07:53:44'),
(30, 'Nike Phantom GX 2 Elite LV8 FG Low-Top Football Boots', 'Footwear', 'Nike', 'Boot', 'Men', 'Normal', 1229.00, 0.00, 'Nike Phantom GX 2 Elite LV8\r\nFG Low-Top Football Boots\r\nObsessed with perfecting your craft? We made this for you. In the middle of the storm, with chaos swirling all around you, you’ve calmly found the final third of the field, thanks to your uncanny mix of on-ball guile and grace. Go finish the job in the Phantom GX 2 Elite. Revolutionary Nike Gripknit covers the striking area of the cleat while Nike Cyclone 360 traction helps guide your unscripted agility. We design Elite Boots for you and the world’s biggest stars to give you high-level quality, because you demand greatness from yourself and your footwear.', '67f8cb61928fa.png', '67f8cb61928ff.png', '67f8cb6192900.png', '67f8cb6192901.png', '67f8cb6192902.jpg', '2025-04-11 07:57:21'),
(31, 'Adidas F50 Club Firm/Multi-Ground Men Football Boots', 'Footwear', 'Adidas', 'Boot', 'Men', 'Normal', 219.00, 0.00, 'STAY ONE STEP AHEAD IN F50 BOOTS DESIGNED FOR MULTIPLE SURFACES.\r\n\r\nPRODUCT STORY\r\n\r\nPush your pace to the limit in lightweight adidas F50 footwear engineered for speed. These Club football boots keep you comfortable on the move with a supportive Fiberskin upper and perforated tongue. Underneath, a versatile outsole propels you to lightning-fast play on firm ground and artificial grass surfaces.\r\nDETAILS\r\n\r\nRegular fit\r\nLace closure\r\nSynthetic Fiberskin upper\r\nPerforated tongue\r\nTextile lining\r\nFirm/multi-ground outsole', '67f8cc45b1590.png', '67f8cc45b1593.png', '67f8cc45b1594.png', '67f8cc45b1595.png', '67f8cc45b1596.jpg', '2025-04-11 08:01:09'),
(32, 'Adidas Deportivo III Flexible Ground Men Football Boots', 'Footwear', 'Adidas', 'Boot', 'Men', 'Promotion', 159.00, 127.20, 'Adidas Deportivo III Flexible Ground Men Football Boots\r\nComfortable synthetic boots with a versatile outsole.\r\n\r\nTake the beautiful game seriously in these adidas football boots. Designed for play on a variety of surfaces, they have a perforated tongue and soft lining to keep your feet fresh through long matches. Stitching on the synthetic forefoot improves ball control, allowing for pinpoint passes and powerful strikes. An anatomically shaped heel keeps you strapped in for fast-moving play.\r\n\r\nMore Details\r\n- Regular fit\r\n- Lace closure\r\n- Synthetic upper\r\n- Textile lining\r\n- Breathable perforated tongue\r\n- Control stitching on forefoot\r\n- Flexible ground outsole\r\nColor : White,Solar Red,Lucid Blue', '67f8cce2c043e.png', '67f8cce2c0441.png', '67f8cce2c0442.png', '67f8cce2c0443.png', '67f8cce2c0444.jpg', '2025-04-11 08:03:46'),
(33, 'PUMA Men Boot Future Play FG/AG', 'Footwear', 'Puma', 'Boot', 'Men', 'Normal', 219.00, 0.00, 'PUMA Men Boot Future Play FG/AG', '67f8cd5089cef.png', '67f8cd5089cf3.png', '67f8cd5089cf5.png', '67f8cd5089cf6.png', '67f8cd5089cf8.jpg', '2025-04-11 08:05:36'),
(34, 'Asics Upcourt 5 Men Court Shoes White', 'Footwear', 'Asics', 'Court', 'Men', 'Promotion', 249.00, 199.20, 'PRODUCT STORY\r\n\r\n\r\nThe UPCOURT 5 shoe is a lightweight court offering that is designed to offer better flexibility and a more comfortable fit.\r\n\r\nIt features a broader section of mesh paneling that helps create a softer and more adaptable fit. Meanwhile, its supportive midfoot overlays offer better stability during multi-directional movements. ?\r\n\r\nLastly, the toe and heel counter are reinforced with durable panels that help your shoes last longer.', '67f8cdee40e3e.png', '67f8cdee40e43.png', '67f8cdee40e44.png', '67f8cdee40e45.png', '67f8cdee40e46.jpg', '2025-04-11 08:08:14'),
(35, 'LOTTO MAXIMO MEN COURT SHOES BLACK', 'Footwear', 'Lotto', 'Court', 'Men', 'Promotion', 119.00, 83.30, 'LOTTO MAXIMO MEN COURT SHOES BLACK', '67f8ce734b277.png', '67f8ce734b27b.png', '67f8ce734b27c.png', '67f8ce734b27d.png', '67f8ce734b27e.jpg', '2025-04-11 08:10:27'),
(37, 'PUMA ESSENTIALS SMALL LOGO FULL-ZIP MEN HOODIE NAVY', 'Apparel', 'Puma', 'Jacket', 'Men', 'Promotion', 239.00, 119.50, 'PUMA ESSENTIALS SMALL LOGO FULL-ZIP MEN HOODIE NAVY', '67f8d2db7c9c5.png', '67f8d2db7c9cd.png', '67f8d2db7c9cf.png', '67f8d2db7c9d0.png', '67f8d6a512aae.jpg', '2025-04-11 08:29:15'),
(38, 'PUMA MEN 3/4 PANTS', 'Apparel', 'Puma', 'Pant', 'Women', 'New', 99.00, 0.00, 'PUMA MEN 3/4 PANTS', '67f8d34f0bb18.png', '67f8d34f0bb1b.png', '67f8d34f0bb1c.png', '67f8d34f0bb1d.png', '67f8d6ebab640.jpg', '2025-04-11 08:31:11'),
(39, 'Nike Academy Team Football Duffel Bag (Medium, 60L) Black', 'Equipment', 'Nike', 'Bag', 'Men', 'Normal', 145.00, 0.00, 'Nike Academy Team\r\nFootball Duffel Bag (Medium, 60L)\r\nCONVENIENT STORAGE TO AND FROM THE FIELD.\r\nThe Nike Academy Team Duffel Bag is a durable design built to keep you organized. Designated compartments provide space for your ball, Boots and clothes while multiple straps let you comfortably carry your gear when you are on the go.', '67f8d443de86c.png', '67f8d443de870.png', '67f8d443de871.png', '67f8d443de872.png', '67f8d443de873.jpg', '2025-04-11 08:35:15'),
(40, 'ADIDAS 4ATHLTS DUFFEL BAG LARGE', 'Equipment', 'Adidas', 'Bag', 'Men', 'New', 259.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nA LARGE DUFFEL BAG FOR TRAINING, MADE IN PART WITH RECYCLED CONTENT.\r\nYou ve got a lot of gear to haul. This adidas large duffel bag has the space. The durable bag is sized just right for out of town tournaments or training camps. A zip compartment keeps wet clothes or shoes separate.', '67f8d4c88e90e.png', '67f8d4c88e911.png', '67f8d4c88e912.png', '67f8d4c88e913.png', '67f8d4c88e914.', '2025-04-11 08:37:28'),
(41, 'PUMA ESSENTIALS III CAP BLACK', 'Equipment', 'Puma', 'Cap', 'Men', 'New', 45.00, 0.00, 'PRODUCT STORY\r\nFirst seen as far back as 1860, and rising to prominence in the 1980s, fewer modern streetwear accessories have the same claim to fame as the baseball cap. This hat from our Essentials range is wonderfully simple, with a classic six-panel shape, pure cotton fabrication, a hair-safe hook-and-loop adjuster and a woven badge with understated PUMA branding on the front.\r\n\r\nDETAILS\r\n- Six-panel shape with curved visor\r\n- Structured buckram\r\n- Hair-safe hook-and-loop adjuster\r\n- Moisture-wicking mesh sweatband\r\n- Woven badge with PUMA No. 1 Logo and merrowed edge on front\r\n\r\nMaterial Information\r\nSHELL : 100% cotton', '67f8d7853fa9b.png', '67f8d7853faa2.png', '67f8d7853faa5.png', '67f8d7853faa7.png', '67f8d7853faa9.', '2025-04-11 08:49:09'),
(42, 'Asics Upcourt 5 Women Court Shoes White', 'Footwear', 'Asics', 'Court', 'Women', 'Promotion', 249.00, 199.20, 'PRODUCT STORY The UPCOURT 5 shoe is designed to offer better flexibility and a more comfortable fit. ? It features a broader section of mesh paneling that helps create a softer and more adaptable fit. Meanwhile, its supportive mid-foot overlays offer better stability during multi-directional movements. Lastly, the toe and heel counter are reinforced with durable panels that help your shoes last longer. DETAILS - Supportive overlays - Improves stability. - Mesh panels - Improves comfort. - Toe and heel counter are designed for durability - The sockliner is produced with the solution dyeing process that reduces water usage by approximately', '67f8d88e8b5de.png', '67f8d88e8b5e3.png', '67f8d88e8b5e4.png', '67f8d88e8b5e5.png', '67f8d88e8b5e6.jpg', '2025-04-11 08:53:34'),
(43, 'Asics GT-2000 13 Lite-Show Women Running Shoes Pink', 'Footwear', 'Asics', 'Running', 'Women', 'New', 200.00, 0.00, 'The GT-2000™ 13 running shoe offers great support for a comfortable run that will help you clear your mind. It is the most comfortable version of the shoe yet and the ideal choice if you are looking for extra guidance during your run.', '6802088d2df3e.png', '6802088d2df43.png', '6802088d2df44.png', '6802088d2df45.png', '6802088d2df46.jpg', '2025-04-18 08:08:45'),
(44, 'Adidas Goletto IX FG/MG Men Football Boots Gold JP5268', 'Footwear', 'Adidas', 'Boot', 'Men', 'Promotion', 200.00, 159.00, 'Adidas Goletto IX FG/MG Men Football Boots Gold JP5268', '6802097817dc1.png', '6802097817dc5.png', '6802097817dc6.png', '6802097817dc7.png', '6802097817dc8.jpg', '2025-04-18 08:12:40'),
(45, 'ADIDAS GOLETTO VIII MEN FUTSAL SHOES BLACK GY5785', 'Footwear', 'Adidas', 'Futsal', 'Men', 'Normal', 95.40, 0.00, 'Indoor court boots made in part with recycled content.\r\nWhether you are scoring them or stopping them, the beautiful game is all about goals. These adidas Goletto VIII football boots show off a stylish new synthetic upper, complete with control-boosting forefoot stitching and comfortable heel padding. Their non-marking rubber outsole grips flat courts to help you shine.\r\n\r\nMade in part with recycled content generated from production waste, e.g. cutting scraps, and post-consumer household waste to avoid the larger environmental impact of producing virgin content.\r\n\r\nSPECIFICATIONS\r\n- Regular fit\r\n- Lace closure\r\n- Soft synthetic upper\r\n- Non-marking rubber outsole\r\n- 25% of the components used to make the upper are made with a minimum of 50% recycled content\r\n- Colour : Core Black/ White/ Red', '68020a3c4f429.png', '68020a3c4f42c.png', '68020a3c4f42d.png', '68020a3c4f42e.png', '68020a3c4f42f.jpg', '2025-04-18 08:15:56'),
(46, 'UMBRO VELOCITA ELIXIR CLUB IC MEN FUTSAL SHOES BLACK', 'Footwear', 'Umbro', 'Futsal', 'Men', 'Normal', 64.00, 159.00, 'UMBRO VELOCITA ELIXIR CLUB IC MEN FUTSAL SHOES BLACK', '68020b43e0cc5.png', '68020b43e0cc8.png', '68020b43e0cc9.png', '68020b43e0cca.png', '68020b43e0ccb.jpg', '2025-04-18 08:20:19'),
(47, 'Nike Tiempo Legend 10 Club Indoor Court Low-Top Men Football Shoes', 'Footwear', 'Nike', 'Futsal', 'Men', 'Promotion', 225.00, 205.50, 'Nike Tiempo Legend 10 Club Indoor Court Low-Top Men Football Shoes', '68020c9324488.png', '68020c932448a.png', '68020c932448b.png', '68020c932448c.png', '68020c932448d.jpg', '2025-04-18 08:25:55'),
(48, 'ADIDAS ENTRADA 22 MEN TRAINING PANTS BLACK', 'Apparel', 'Adidas', 'Pant', 'Men', 'New', 149.00, 0.00, 'ENTRADA 22 TRAINING PANTS\r\nFOOTBALL TRAINING PANTS MADE WITH RECYCLED MATERIALS.\r\nTrain like the professionals, relax in style. These adidas football track pants are made with moisture-absorbing AEROREADY so you feel dry and ready for anything on or off the pitch. The drawcord-adjustable waist ensures a stay-put fit, and ankle zips mean easy on and off wherever your day takes you.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.\r\n\r\nSPECIFICATIONS\r\n- Regular fit with mid rise\r\n- Elastic waist with drawcord\r\n- 100% recycled polyester doubleknit\r\n- Moisture-absorbing AEROREADY\r\n- Side seam pockets\r\n- Ankle zips\r\n- Doubleknit\r\n- Product colour: Black', '68020ebe08859.png', '68020ebe08868.png', '68020ebe0886c.png', '68020ebe0886f.png', '68020ebe08873.jpg', '2025-04-18 08:35:10'),
(49, 'Nike Dri-Fit Challenger Men 7', 'Apparel', 'Nike', 'Pant', 'Men', 'Promotion', 135.00, 94.50, 'Nike Dri-FIT Challenger\r\nMen 7\" Brief-Lined Versatile Shorts\r\nDesigned for running, training and yoga, our sweat-wicking Challenger Shorts keep it light and breathable with a relaxed fit that helps you get the most out of your movement. We geared them for more than running, with a comfortable pocket that would not irritate when you move from the track to the gym.\r\n\r\nKeep Dry, Stay Cool\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.Brief lining offers lightweight support.\r\nRace Ready Comfort\r\nMade with lightweight and stretchy woven fabric, these shorts also have mesh at the lower side panels to help you stay cool. Side vents keep you moving freely through your stride.\r\nSafe Storage\r\nHand pockets help keep small essentials close, like your keys or card. A back pocket with a hook-and-loop closure is big enough to hold most phones.\r\nDesigned for Running, Yoga and Training\r\nLength: 7\"\r\nLiner: Brief\r\nFabric: Lightweight woven\r\nStorage: Side pockets and hook-and-loop back pocket\r\nWaistband: Narrow with drawcord\r\n\r\nMore Details\r\nBody: 100% Polyester. Lining: 92% Polyester/8% Spandex.\r\nMachine wash\r\nImported\r\nNot intended for use as Personal Protective Equipment (PPE)\r\nPRODUCT COLOUR : Black/ Black/ Black/ Reflective Silver', '6802101026fc5.png', '6802101026fc8.png', '6802101026fc9.png', '6802101026fca.png', '6802101026fcb.jpg', '2025-04-18 08:40:48'),
(50, 'PUMA TEAMRISE MEN SHORTS NAVY', 'Apparel', 'Puma', 'Pant', 'Men', 'New', 59.00, 0.00, 'PRODUCT STORY\r\nThe Rise collection combines high-performance materials with modern design to keep your team prepared for the demands of the game at every level.\r\n\r\nFEATURES & BENEFITS\r\nDryCELL technology wicks away moisture, leaving you feeling comfortable.\r\n\r\nDETAILS\r\n- Elasticated waistband with drawcord\r\n- Heat transfer PUMA Cat Logo on the left leg\r\n- Regular Fit.\r\n\r\nMaterial Information\r\n100% Polyester\r\n', '680210cad82fc.png', '680210cad8300.png', '680210cad8301.png', '680210cad8302.png', '680210cad8303.jpg', '2025-04-18 08:43:54'),
(52, 'Adidas EPP Club Ball WHITE HT2458', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'Promotion', 69.00, 28.00, 'PRODUCT DESCRIPTIONS\r\nAN EVERYDAY FOOTBALL FOR ANY-DAY BALLERS.\r\nWhen your teammates come calling, always be prepared. This adidas EPP Club Ball was built for training sessions, kickabouts in the park and for showing friends your skills. Its durable, machine-stitched surface means it is ready to stand up to razor-sharp passes and high-intensity scrimmages. The butyl bladder ensures optimal air-retention, so you can worry less about the ball and focus more on your footwork.\r\n\r\nPRODUCT DETAILS\r\n- Size 5\r\n- Machine stitched\r\n- Butyl Bladder\r\n- Requires inflation\r\n- 100% TPU film\r\n- Product Colour: White / Bold Aqua\r\n', '680212760801a.png', '680212760801f.png', '6802127608020.png', '6802127608021.png', '6802127608022.', '2025-04-18 08:51:02'),
(53, 'Adidas Tiro Club Shin Guards White', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'Normal', 49.00, 0.00, '- 100% polypropylene (25% recycled)\r\n- Foam backing\r\n- Perforated flexible hard shield\r\n\r\n\r\nView Product Details\r\nX\r\n- 100% polypropylene (25% recycled)\r\n- Foam backing\r\n- Perforated flexible hard shield', '680212e6cc63f.png', '680212e6cc642.png', '680212e6cc643.png', '680212e6cc644.png', '680212e6cc645.', '2025-04-18 08:52:54'),
(54, 'NIKE MEN J SHINGUARD SHINGUARD BLACK', 'Equipment', 'Nike', 'Football Accessories', 'Men', 'Normal', 39.00, 0.00, 'Designed to take the impacts of the game, the Nike J Shinguards are made from a tough composite shell and feature perforations for ventilated comfort.', '68021332451da.png', '68021332451dc.png', '68021332451dd.png', '68021332451de.png', '68021332451df.', '2025-04-18 08:54:10'),
(55, 'Adidas Starlancer Club Football BLACK', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'New', 69.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nA DURABLE BALL FOR SHOWING OFF YOUR SKILLS.\r\nLet your skills shine every time you head to the park with this Starlancer Club Ball. Made for recreational play, it has a machine-stitched TPU cover for durability and a butyl bladder to ensure it stays pumped up for longer. That blurred graphic takes its inspiration from a classic adidas football look.\r\n\r\nPRODUCT DESCRIPTIONS\r\n- 100 percent TPU cover\r\n- Machine-stitched construction\r\n- Butyl bladder\r\n- Product Colour : Black/ Silver Metallic', '680213b0e52fb.png', '680213b0e52fe.png', '680213b0e52ff.png', '680213b0e5300.png', '680213b0e5301.', '2025-04-18 08:56:16'),
(56, 'Adidas Copa Glove Pro Beige', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'New', 339.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nGRIPPY FOOTBALL GOALIE GLOVES WITH A CLASSIC FIT AND FEEL.\r\nKeep goal in total comfort. These adidas Copa Pro goalkeeper gloves come with a classic latex mesh backhand and stretchy wrist strap for a perfect fit. On the palm, URG 2.0 provides superior grip and an abrasion zone adds extra resilience. The negative cut ensures a snug fit around the fingers so your handling is always on point.\r\n\r\nPRODUCT DETAILS\r\n- Negative cut\r\n- Palm: URG 2.0 100% Latex\r\n- Body: 45% Recycled Polyester, 30% Cotton, 20% Polyurethane, 5% Elastane\r\n- Elastic wrist strap\r\n- Product Colour : Ivory/ Solar Red/ Black', '6802143456aea.png', '6802143456aed.png', '6802143456aee.png', '6802143456aef.png', '6802143456af0.', '2025-04-18 08:58:28'),
(57, 'Adidas Predator Training Football Gloves Yellow', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'New', 119.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nCOMFORTABLE FOOTBALL GLOVES FOR IMPROVING YOUR KEEPING.\r\nBuild up your skills between the sticks. These adidas Predator Training gloves are for goalkeepers who are always working on their game. A positive cut at the fingers provides maximum ball contact for grasping crosses and throwing long. The Soft Grip latex palm ensures confident play whatever Mother Nature fires at you.\r\n\r\nPRODUCT DETAILS\r\n- Positive cut\r\n- Palm: Soft Grip 100% latex\r\n- Body: 100% polyurethane\r\n- Half-wrap wrist strap\r\n- Colour: Solar Yellow / Black / Solar Red', '680214b9d5a99.png', '680214b9d5a9c.png', '680214b9d5a9d.png', '680214b9d5a9e.png', '680214b9d5a9f.', '2025-04-18 09:00:41'),
(58, 'PUMA Orbita Laliga 1 MS Football Ball White', 'Equipment', 'Puma', 'Football Accessories', 'Men', 'Promotion', 139.00, 125.00, 'PUMA Orbita Laliga 1 MS Football Ball White', '6802153d03a60.png', '6802153d03a64.png', '6802153d03a65.png', '6802153d03a66.png', '6802153d03a67.', '2025-04-18 09:02:53'),
(59, 'JORDAN RISE CAP ADJUSTABLE HAT BROWN', 'Equipment', 'Nike', 'Cap', 'Men', 'New', 79.00, 0.00, 'View Product Details\r\nX\r\nJordan Rise Cap\r\nAdjustable Hat\r\nIs your fit missing an understated dad hat to show the word your Jordan pride? We got you. This classic structured fit has a sloped crown and a curved bill for a casual, broken-in look. And the metal Jumpman emblem makes a perfectly subtle statement.\r\n\r\nAdjustable back strap lets you change up the fit.\r\nSweatband provides a cool, comfortable feel.\r\n\r\nMore Details\r\nBody: 100% nylon. Front Panel Lining: 65% polyester/35% cotton.\r\nDo not wash\r\nImported\r\n100% NYLON\r\n\r\nPRODUCT COLOUR : Hemp/ Black/ Black/ Gunmetal\r\n', '680216b4ab82f.png', '680216b4ab833.', '680216b4ab834.', '680216b4ab835.', '680216b4ab836.', '2025-04-18 09:09:08'),
(60, 'ADIDAS BASEBALL CAP WHITE', 'Equipment', 'Adidas', 'Cap', 'Men', 'Normal', 99.00, 0.00, 'BASEBALL CAP\r\nA LIGHTWEIGHT CAP WITH AN ADJUSTABLE FIT.\r\nTop things off nicely with this classic Baseball Cap. Made with sunny days in mind, the soft fabric shields you from the sun rays. An adidas Badge of Sport takes centre stage in front.\r\n\r\nSPECIFICATIONS\r\n- 100% cotton twill\r\n- Soft, lightweight feel\r\n- Classic, adjustable cap\r\n- UV 50 factor\r\n- Padded sweatband\r\n- Medium-curved crown and visor\r\n- Twill\r\n- Product Colour: White / White / Black\r\n', '6802179d95475.png', '6802179d95479.png', '6802179d9547b.png', '6802179d9547c.png', '6802179d9547d.', '2025-04-18 09:13:01'),
(61, 'NIKE EVERYDAY LIGHTWEIGHT TRAINING CREW SOCKS (3 PAIRS) WHITE', 'Equipment', 'Nike', 'Sock', 'Men', 'Normal', 69.00, 0.00, 'NIKE EVERYDAY LIGHTWEIGHT TRAINING CREW SOCKS (3 PAIRS) WHITE', '6802184bcb53d.png', '6802184bcb540.png', '6802184bcb541.png', '6802184bcb542.png', '6802184bcb543.', '2025-04-18 09:15:55');

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

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `user_id`, `product_id`, `order_id`, `rating`, `review_text`, `review_at`) VALUES
(7, 44, 6, 0, 3, 'shou gou liao', '2025-05-21 04:25:22'),
(8, 44, 6, 76, 2, 'q', '2025-05-21 04:59:41'),
(9, 44, 6, 95, 2, 'dsf', '2025-05-24 03:59:47');

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
(20, 5, 'UK7.5', '111111', 7, '2025-04-18 09:41:06'),
(21, 7, 'UK13.5C', '111111', 0, '2025-04-18 09:41:06'),
(33, 7, 'UK1.5Y', '111111', 0, '2025-04-18 10:01:17'),
(54, 5, 'UK6', 'SKU1122', 50, '2025-04-08 14:33:47'),
(55, 5, 'UK8.5', 'SKU1123', 60, '2025-04-08 14:33:55'),
(56, 5, 'UK9', 'SKU1124', 8, '2025-04-18 09:41:06'),
(57, 5, 'UK12', 'SKU1125', 100, '2025-04-08 14:33:33'),
(58, 6, 'UK8.5', 'SKU1112', 0, '2025-04-25 16:01:40'),
(59, 6, 'UK9', 'SKU1113', 80, '2025-05-24 17:10:21'),
(60, 6, 'UK9.5', 'SKU1114', 100, '2025-04-08 14:54:33'),
(61, 6, 'UK10', 'SKU1115', 100, '2025-04-08 14:54:42'),
(62, 6, 'UK10.5', 'SKU1116', 100, '2025-04-08 14:54:56'),
(64, 8, 'UK13.5C', 'SKU1122', 0, '2025-04-18 09:46:03'),
(65, 19, 'UK6', 'SKU5525', 8, '2025-04-20 23:45:36'),
(66, 19, 'UK6.5', 'SKU5521', 10, '2025-04-10 01:06:25'),
(67, 19, 'UK10.5', 'SKU5520', 4, '2025-05-24 06:12:28'),
(69, 20, 'UK5', 'SKU3356', 90, '2025-05-24 05:54:54'),
(70, 18, 'XXS', '111111', 5, '2025-05-24 16:22:41'),
(71, 22, 'S', '111112', 1, '2025-04-25 15:47:55'),
(72, 22, 'M', '111113', 8, '2025-05-25 15:35:45'),
(73, 22, 'L', '111114', 10, '2025-04-10 01:08:15'),
(74, 24, 'XXS', '111111', 0, '2025-05-24 17:23:30'),
(75, 23, 'XXS', 'SKU1234', 76, '2025-04-25 15:47:55'),
(76, 25, 'UK13.5C', '111111', 12, '2025-05-25 15:35:45'),
(77, 26, 'UK7', '111111', 98, '2025-04-18 09:41:06'),
(78, 29, 'UK6', '111111', 8, '2025-04-25 17:10:20'),
(79, 32, 'UK5.5', '111111', 0, '2025-04-19 06:30:18'),
(80, 38, 'XXS', '111111', 0, '2025-04-25 16:01:40'),
(81, 37, 'S', '111111', 10, '2025-04-23 00:27:56'),
(82, 42, 'UK6', '111111', 4, '2025-05-24 17:17:07'),
(83, 41, 'One Size', '111111', 9, '2025-04-24 00:34:42'),
(84, 40, 'One Size', '111111', 0, '2025-04-18 09:41:06'),
(85, 39, 'One Size', '111111', 1, '2025-04-14 04:36:17'),
(86, 35, 'UK13.5C', '111111', 1, '2025-04-14 04:36:30'),
(87, 27, 'UK13.5C', '111111', 8, '2025-04-25 15:01:56'),
(88, 33, 'UK6.5Y', 'SKU1121', 98, '2025-04-18 10:39:27'),
(89, 31, 'UK13.5C', '111111', 1, '2025-04-16 09:13:30'),
(90, 43, 'UK3Y', '111111', 0, '2025-04-24 00:34:42'),
(91, 43, 'UK3.5Y', '111111', 83, '2025-05-24 15:54:21'),
(92, 43, 'UK4Y', '111111', 100, '2025-04-18 08:09:20'),
(93, 43, 'UK4.5Y', '111111', 100, '2025-04-18 08:09:25'),
(94, 61, 'One Size', 'SX7676-100-L', 10, '2025-04-19 10:12:56'),
(95, 60, 'One Size', 'FK0890-OSFM', 9, '2025-05-21 03:42:05'),
(96, 59, 'One Size', 'FD5186-200-M/L', 9, '2025-04-18 10:01:17'),
(97, 58, 'One Size', '084288 01-5', 99, '2025-04-18 09:43:45'),
(98, 57, 'One Size', 'IQ4026-9', 9, '2025-04-20 10:47:29'),
(99, 56, 'One Size', 'IQ4013-10', 3, '2025-04-25 17:10:20'),
(100, 55, 'One Size', 'IA0976-5', 6, '2025-04-24 00:34:42'),
(101, 54, 'One Size', 'SP0040009-L', 99, '2025-04-18 09:43:45'),
(102, 53, 'One Size', 'IP3995-L', 95, '2025-04-18 10:01:17'),
(103, 52, 'One Size', 'HT2458-5', 8, '2025-04-18 10:01:17'),
(104, 21, 'One Size', 'HT2459-5', 5, '2025-05-24 05:52:29'),
(108, 50, 'M', '704942 06-M', 18, '2025-04-24 00:34:56'),
(109, 50, 'XL', '704942 06-XL', 20, '2025-04-18 09:24:53'),
(110, 49, 'S', 'DV9360-010-S', 10, '2025-04-18 09:25:46'),
(111, 49, 'M', 'DV9360-010-M', 10, '2025-04-18 09:25:53'),
(112, 49, 'L', 'DV9360-010-L', 10, '2025-04-18 09:26:01'),
(113, 49, 'XL', 'DV9360-010-XL', 10, '2025-04-18 09:26:08'),
(114, 49, '2XL', 'DV9360-010-2XL', 10, '2025-04-18 09:26:23'),
(116, 48, 'M', 'HC0332-M', 4, '2025-04-24 00:34:56'),
(117, 48, 'L', 'HC0332-L', 10, '2025-04-18 09:27:18'),
(121, 30, 'UK6', '111111', 10, '2025-04-18 13:53:55'),
(122, 47, 'UK13.5C', '111111', 0, '2025-04-24 00:34:42'),
(123, 44, 'UK6', '111111', 0, '2025-04-24 00:34:42'),
(124, 45, 'UK6.5', '704942 06-5', 10, '2025-04-18 13:46:14'),
(125, 45, 'UK6Y', '704942 06', 10, '2025-04-18 13:46:25'),
(127, 46, 'UK5Y', '111111', 1, '2025-04-18 13:46:56'),
(128, 28, 'UK6Y', 'SKU1123', 20, '2025-04-18 13:53:07'),
(129, 34, 'UK6.5Y', '111111', 10, '2025-04-18 13:54:26');

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
  `user_type` enum('1','2','3') DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `mobile_number`, `password`, `address`, `postcode`, `state`, `city`, `birthday_date`, `gender`, `profile_image`, `user_type`, `create_at`) VALUES
(25, 'Chua', 'Chi Ann', 'cca@gmail.com', '0189468221', '$2y$10$SpwRL0funqbCXGxTE8xprOy64TOVzOB.2vqwr.7/XlBwepViXDLd.', '1', '85000', 'Johor', 'segamat', '2025-03-26', 'male', '1743133435.jpg', '1', '2025-03-28 03:43:55'),
(26, 'Alex', 'Tan', 'superadmin@verosports.com', '012-3456789', 'ba21767ae494afe5a2165dcb3338c5323e9907050e34542c405d575cc31bf527', 'No. 1, Jalan SuperAdmin, Vero City', '12345', 'Selangor', 'Shah Alam', '1990-01-01', 'Male', 'profile.jpg', '3', '2025-03-28 07:32:55'),
(29, 'Soh', 'Xi Jie', 'Sxj0802@gmail.com', '0123456789', '$2y$10$EUV/88A2JHlQQjUQi9fhLuTqYcpHonlJ7O.0rkXNummdt09EFwrRy', '1', '85000', 'Johor', 'Johor', '2025-03-28', 'male', '1743157583.png', '1', '2025-03-28 10:26:23'),
(33, 'Soh', 'Xi Jie', 'soh@gmail.com', '0189468222', '$2y$10$d4.YhyIsPdi7deOQxXt9seBaiho6gPFFIrEIl0iGlSjdNySYxL4Oq', 'Jalan Oren\r\n', '85000', 'Kedah', 'Sungai Petani', '2007-03-31', 'Male', '1743487755.png', '2', '2025-04-01 06:08:24'),
(41, 'Chua', 'Chi Ann', 'chiannchua05@gmail.com', '0189468221', '$2y$10$FN3uf7HhDlE7uBAJW3NuaujuqmKq1nzzOF6jNtZuCxvfvBtVDCy56', '1', '85000', 'Melaka', 'Ayer Keroh', '2007-04-06', 'Male', '1744008477.png', '2', '2025-04-07 06:47:57'),
(44, 'Evis', 't', 'kaiwen211105@gmail.com', '0125316512', '$2y$10$CKD5Z6khpNEhUPMwbt5qqOy1u24m48gc/Up7ZIm6mudlCIFWiLRCm', 'we', '21121', 'Johor', 'we', '2025-04-23', 'male', NULL, '1', '2025-04-11 15:10:29'),
(48, 'Evis', 'Wen', 'amilylvis@gmail.com', '0125316512', '$2y$10$f4p.5sDp3nXJ3Ld5YshkUOHLEop8UvfP5QkZU9TrV4oMrY3YUNEHq', 'we', '21121', 'Kuala Lumpur', 'Kuala Lumpur', '2007-04-02', 'Male', '1745667447.', '3', '2025-04-26 11:37:27');

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
(1, 44, 'd203609ee610d0ffda8a3b8987c3224c780b3fef420c0df138597716dd32ddb4', '2025-05-16 10:43:06', '2025-04-16 16:43:06', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(2, 44, '2401f78f28d271132b55b2cefa361a605da8cd2c287fd707b73286ce0b9133d7', '2025-05-16 10:43:26', '2025-04-16 16:43:26', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(3, 44, '5d0372a8a3e6be5d71a70ed9c89f953e86cc926f9e2b76aca720d3dc32ad4f40', '2025-05-16 13:30:27', '2025-04-16 19:30:27', '2025-04-17 13:53:30', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(4, 44, '90e352ab9ffe568f8ad3fb345879c133266f0773a8086ffe500fe90df70b205a', '2025-05-17 02:39:30', '2025-04-17 08:39:30', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(5, 44, 'be27cfa60d5a4ce742b684749e92fe84ee25736c546311d3bc1741517fe16de7', '2025-05-17 03:52:14', '2025-04-17 09:52:14', '2025-04-17 11:13:00', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(6, 44, 'd8370232a8efd8cfe6670cadc43ca5a3b89f4253f729f4f9fb32488d156c22d1', '2025-05-17 05:41:11', '2025-04-17 11:41:11', '2025-04-17 11:41:58', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(7, 44, '42d536b77b5539615dd037d0039c8b015d2ddf1dde035c8ad9345ec37a5fcaf7', '2025-05-17 06:11:02', '2025-04-17 12:11:02', '2025-04-17 22:18:14', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(8, 44, '8e5bed1bcc3487103b102921f4680d2fabf47a82500c457cff1b227ade7fee37', '2025-05-17 08:06:07', '2025-04-17 14:06:07', '2025-04-25 23:59:43', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(9, 44, '3d11b8ec93e5d04360de0f84293b52ab3a4deb413465aa9b7d95a2d888d9ee48', '2025-05-18 08:49:50', '2025-04-18 14:49:50', '2025-04-18 14:54:56', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(10, 44, '14ce4ddda40d8d471a9d5fd1e3bc1929c92ab9a2505281a3bce700cf729b10a4', '2025-05-18 11:03:14', '2025-04-18 17:03:14', '2025-04-18 17:25:07', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(11, 44, 'e93aa39cae0e6c128f6a1e8ff742fed2719b397e66074b42887a1a1a7abd3962', '2025-05-18 12:03:13', '2025-04-18 18:03:13', '2025-04-18 18:03:24', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(12, 44, '669a3f1e0d73bb93ba737ff192131b2bffb351c8c269ae3b7ce5794267976876', '2025-05-21 12:55:34', '2025-04-21 18:55:34', '2025-04-21 19:03:38', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(13, 44, '9f462dcc30c9497cefc6614b82b6ca030f458e060ecfefe5ca3bb742217c198d', '2025-05-21 13:55:25', '2025-04-21 19:55:25', '2025-04-21 23:42:53', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(14, 44, '13a073cd8642b8771c03589ef5c28ccc88eb8148dbc7459cc33b507788ec31fd', '2025-05-21 16:03:42', '2025-04-21 22:03:42', '2025-04-21 23:56:04', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(15, 44, '5ba9d96d14bb0afd3d8f0d8d907abc3339d28efacd00eddc493d88314176f239', '2025-05-24 05:52:04', '2025-04-24 11:52:04', '2025-04-25 23:01:34', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(16, 44, '26a33b44b109ae2ebc253f7ab6669d0daf3c341b11417c5ecd02dbf350f236d1', '2025-05-24 08:08:47', '2025-04-24 14:08:47', '2025-04-24 14:14:25', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(17, 44, 'ed817456e36e35c9cb474cd875c1686548a748ff1b504df7b01e6ff865d5ae91', '2025-05-25 17:46:45', '2025-04-25 23:46:45', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(18, 44, 'a053168e7f395e6f529c047e4ad1ed0e12df43a03510595ad9dbe81663bb3046', '2025-05-25 19:00:16', '2025-04-26 01:00:16', '2025-04-26 19:08:01', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(19, 44, 'c3ddc25e03cc7bbdb56900e560c9a2407a1632f84769803478e4d8111a14f6da', '2025-05-25 19:05:41', '2025-04-26 01:05:41', '2025-04-26 20:28:54', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(20, 48, 'd47d87bce9e6c2fa51b2d85b14fefa9d054d2b8c5453c3398ee72598ae53c0ab', '2025-05-26 13:38:06', '2025-04-26 19:38:06', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(21, 44, '3c3c61a55dbeedb96ddb7c7aeb238502cad72d4ef4e2e1c260a26512e57dd48a', '2025-05-26 14:17:03', '2025-04-26 20:17:03', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(22, 44, '6c1f87358adc5a54a98078dface05b588730f83652b9bd49bea780083ecc67f5', '2025-05-26 18:12:15', '2025-04-27 00:12:15', '2025-04-27 00:17:47', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(23, 44, '47e0f85d7a58c562f253491d230b0d58eca1a92fb8fa7b46acdc32b071a7fa94', '2025-06-14 04:42:55', '2025-05-15 10:42:55', '2025-05-15 11:00:54', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '::1'),
(24, 44, '6c97419e561f29e7068e0fa48e71fb8729dbe4b5a94f6f23e1cc9b18588a700e', '2025-06-20 05:40:33', '2025-05-21 11:40:33', NULL, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '::1'),
(25, 44, '2c2133f205c3cc7a3cf30c93107ba4052ac9a88cc4a3150108804d0452651a1b', '2025-06-23 05:13:40', '2025-05-24 11:13:40', '2025-05-24 14:32:05', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '::1');

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
  MODIFY `billboard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contactUs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_log`
--
ALTER TABLE `payment_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`);

--
-- Constraints for table `payment_log`
--
ALTER TABLE `payment_log`
  ADD CONSTRAINT `fk_log_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
