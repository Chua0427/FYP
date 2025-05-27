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
(5, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK\'', 'Footwear', 'Adidas', 'Running', 'Men', 'Promotion', 120.00, 100.00, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK‘\r\n', '67f5e7b0c8aa6.jpg', '67f5e7b0c92b6.jpg', '67f5e7b0c9d2f.jpg', '67f5e7b0ca726.jpg', '67fcc26199200.jpg', '2025-04-03 04:48:01'),
(6, 'Nike Quest 6 Mens Road Running Shoes FD6033002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 120.00, NULL, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f5e6725324c.jpg', '67f5e67254561.jpg', '67f5e67254d38.jpg', '67f5e67255331.jpg', '67f5387e89e10.jpg', '2025-04-03 07:35:49'),
(7, 'Asics GT2000 13 LiteShow Men\'s Running Shoes Blue', 'Footwear', 'Asics', 'Running', 'Men', 'New', 180.00, 0.00, 'The GT200 13 running shoe offers great support for a comfortable run that will help you clear your mind. Its the most comfortable version of the shoe yet and the ideal choice if you are looking for extra guidance during your run.\r\n\r\n', '67f5eb24629b0.png', '67f5eb2462e7a.png', '67f5f044d935c.png', '67f5f044d9738.png', '67ee3aed4f0d6.jpg', '2025-04-03 07:38:21'),
(8, 'Nike Quest 6 Men Road Running Shoes FD6033-002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 369.00, NULL, 'Nike Quest 6\r\nMen Road Running Shoes\r\nThe Nike Quest 6 is for runners of all levels. But make no mistake, it’s anything but entry-level. A supercomfortable and supportive midfoot fit band helps keep you stable for your miles. Plus, a supersoft midsole foam helps cushion each step.', '67f5efee89f95.png', '67f5efee8aad9.png', '67f5efee8aee7.png', '67f5efee8b26e.png', '67ee3d5b17ff8.jpg', '2025-04-03 07:48:43'),
(18, 'Nike ACD TOP NOV Men Training Jersey', 'Apparel', 'Nike', 'Jersey', 'Men', 'Promotion', 135.00, 100.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f63019d51ec.png', '67f63019d51f0.png', '67f63019d51f1.png', '67f714adddae5.png', '67f8d72022188.jpg', '2025-04-09 08:30:17'),
(19, 'Asics Gel Cumulus 27 Lite Show Mens Running Shoes Orange', 'Footwear', 'Asics', 'Running', 'Men', 'New', 150.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f631e68b401.png', '67f631e68b405.png', '67f631e68b407.png', '67f631e68b408.png', '67f631e68b409.jpg', '2025-04-09 08:37:58'),
(20, 'Nike Downshifter 13 Mens Road Running Shoes FD6454008', 'Footwear', 'Nike', 'Running', 'Men', 'Normal', 120.00, 0.00, 'Nike Downshifter 13\r\nMens Road Running Shoes\r\nWhether you are starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a revamped upper and cushion and durability, it helps you find that extra gear or take that first stride toward chasing down your goals.', '67f63260c09de.png', '67f63260c09e2.png', '67f63260c09e3.png', '67f63260c09e4.png', '67f63260c09e5.jpg', '2025-04-09 08:40:00'),
(22, 'Umbro Men Jersey Blue', 'Apparel', 'Umbro', 'Jersey', 'Men', 'Normal', 50.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f6334ac43d9.png', '67f6334ac43dd.png', '67f6334ac43de.png', '67f6859bcf9d7.png', '67f6334ac43e0.jpg', '2025-04-09 08:43:54'),
(23, 'ADIDAS ENTRADA 22 MEN FOOTBALL JERSEY RED', 'Apparel', 'Adidas', 'Jersey', 'Men', 'Normal', 80.50, 0.00, 'ADIDAS ENTRADA 22 MENS FOOTBALL JERSEY RED', '67f633af8d002.png', '67f633af8d005.png', '67f633af8d006.png', '67f685b032d2a.png', '67f633af8d008.jpg', '2025-04-09 08:45:35'),
(24, 'Umbro 1924 Women Jersey MAROON', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Normal', 35.00, 0.00, 'Umbro 1924 Women Jersey MAROON', '67f63a639bb60.png', '67f63a639bb68.png', '67f63a639bb69.png', '67f685cf1fc6b.png', '67f63a639bb6b.jpg', '2025-04-09 09:14:11'),
(25, 'PUMA Scend Pro Men Running Shoes Red', 'Footwear', 'Puma', 'Running', 'Men', 'New', 114.50, 0.00, 'PRODUCT STORY\r\nIntroducing the Scend Pro, PUMA new essential runner. The Protread rubber outsole is designed for distance and enhanced with a bold paint line and cushioned tooling. The breathable mesh uppers are ultra lightweight, while the comfort collar locks down for a secure fit during workouts.', '67f8c2fcb865e.png', '67f8c2fcb8662.png', '67f8c2fcb8663.png', '67f8c2fcb8664.png', '67f8c2fcb8665.jpg', '2025-04-11 07:21:32'),
(26, 'Nike Downshifter 13 Men Running Shoes Black', 'Footwear', 'Nike', 'Running', 'Men', 'Normal', 255.00, 0.00, 'Nike Downshifter 13 Men Running Shoes Black', '67f8c66f0e148.png', '67f8c66f0e14c.png', '67f8c66f0e14d.png', '67f8c66f0e14e.png', '67f8c66f0e14f.jpg', '2025-04-11 07:36:15'),
(27, 'PUMA Velocity Nitro 3 Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'New', 419.30, 0.00, 'PRODUCT DESCRIPTIONS\r\nEmbark on your running journey with the VELOCITY NITR 3, PUMA s hero franchise featuring NITROFOA cushioning. Sleek styling, outstanding comfort, and a slightly higher stack height make it the ideal do-it-all trainer. From short distances to long runs, this neutral shoe offers a smooth ride and optimal cushioning for all runners. Experience the best in comfort and performance with the VELOCITY 3’.', '67f8c7497ab71.png', '67f8c7497ab78.png', '67f8c7497ab7a.png', '67f8c7497ab7b.png', '67f8c7497ab7c.jpg', '2025-04-11 07:39:53'),
(28, 'Puma Pounce Lite Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'Normal', 289.00, 0.00, 'The all-new Pounce Lite joins our Running line this year. Featuring an ultra-lightweight foam and a durable PROTREAD outsole, it is the ultimate blend of style and performance.', '67f8c8ff9d98d.png', '67f8c8ff9d990.png', '67f8c8ff9d991.png', '67f8c8ff9d992.png', '67f8c8ff9d993.jpg', '2025-04-11 07:47:11'),
(29, 'New Balance Fresh Foam 510 V6 Men Running Shoes Black', 'Footwear', 'New Balance', 'Running', 'Men', 'Promotion', 359.00, 179.50, 'PRODUCT DESCRIPTIONS\r\nA rugged shoe designed to help you conquer the roughest trails with ease.', '67f8ca88b98cf.png', '67f8ca88b98d2.png', '67f8ca88b98d4.png', '67f8ca88b98d5.png', '67f8ca88b98d6.jpg', '2025-04-11 07:53:44'),
(30, 'Nike Phantom GX 2 Elite LV8 FG Low-Top Football Boots', 'Footwear', 'Nike', 'Boot', 'Men', 'Normal', 1000.00, NULL, 'Nike Phantom GX 2 Elite LV8\r\nFG Low-Top Football Boots\r\nObsessed with perfecting your craft? We made this for you. In the middle of the storm, with chaos swirling all around you, you’ve calmly found the final third of the field, thanks to your uncanny mix of on-ball guile and grace. Go finish the job in the Phantom GX 2 Elite. Revolutionary Nike Gripknit covers the striking area of the cleat while Nike Cyclone 360 traction helps guide your unscripted agility. We design Elite Boots for you and the world’s biggest stars to give you high-level quality, because you demand greatness from yourself and your footwear.', '67f8cb61928fa.png', '67f8cb61928ff.png', '67f8cb6192900.png', '67f8cb6192901.png', '67f8cb6192902.jpg', '2025-04-11 07:57:21'),
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
(42, 'Asics Upcourt 5 Women Court Shoes White', 'Footwear', 'Asics', 'Court', 'Women', 'Promotion', 249.00, 199.20, 'PRODUCT STORY The UPCOURT 5 shoe is designed to offer better flexibility and a more comfortable fit. ? It features a broader section of mesh paneling that helps create a softer and more adaptable fit. Meanwhile, its supportive mid-foot overlays offer better stability during multi-directional movements. Lastly, the toe and heel counter are reinforced with durable panels that help your shoes last longer. DETAILS - Supportive overlays - Improves stability. - Mesh panels - Improves comfort. - Toe and heel counter are designed for durability - The sockliner is produced with the solution dyeing process that reduces water usage by approximately\r\n\r\n', '67f8d88e8b5de.png', '67f8d88e8b5e3.png', '67f8d88e8b5e4.png', '67f8d88e8b5e5.png', '67f8d88e8b5e6.jpg', '2025-04-11 08:53:34'),
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
(57, 'Adidas Predator Training Football Gloves Yellow', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'New', 119.00, NULL, 'PRODUCT DESCRIPTIONS\r\nCOMFORTABLE FOOTBALL GLOVES FOR IMPROVING YOUR KEEPING.\r\nBuild up your skills between the sticks. These adidas Predator Training gloves are for goalkeepers who are always working on their game. A positive cut at the fingers provides maximum ball contact for grasping crosses and throwing long. The Soft Grip latex palm ensures confident play whatever Mother Nature fires at you.\r\n\r\nPRODUCT DETAILS\r\n- Positive cut\r\n- Palm: Soft Grip 100% latex\r\n- Body: 100% polyurethane\r\n- Half-wrap wrist strap\r\n- Colour: Solar Yellow / Black / Solar Red', '680214b9d5a99.png', '680214b9d5a9c.png', '680214b9d5a9d.png', '680214b9d5a9e.png', '680214b9d5a9f.', '2025-04-18 09:00:41'),
(58, 'PUMA Orbita Laliga 1 MS Football Ball White', 'Equipment', 'Puma', 'Football Accessories', 'Men', 'Promotion', 139.00, 125.00, 'PUMA Orbita Laliga 1 MS Football Ball White', '6802153d03a60.png', '6802153d03a64.png', '6802153d03a65.png', '6802153d03a66.png', '6802153d03a67.', '2025-04-18 09:02:53'),
(59, 'JORDAN RISE CAP ADJUSTABLE HAT BROWN', 'Equipment', 'Nike', 'Cap', 'Men', 'New', 79.00, NULL, 'View Product Details\r\nX\r\nJordan Rise Cap\r\nAdjustable Hat\r\nIs your fit missing an understated dad hat to show the word your Jordan pride? We got you. This classic structured fit has a sloped crown and a curved bill for a casual, broken-in look. And the metal Jumpman emblem makes a perfectly subtle statement.\r\n\r\nAdjustable back strap lets you change up the fit.\r\nSweatband provides a cool, comfortable feel.\r\n\r\nMore Details\r\nBody: 100% nylon. Front Panel Lining: 65% polyester/35% cotton.\r\nDo not wash\r\nImported\r\n100% NYLON\r\n\r\nPRODUCT COLOUR : Hemp/ Black/ Black/ Gunmetal\r\n', '680216b4ab82f.png', '680b404a2984a.png', '680b404a29b06.png', '680b404a29d17.png', '680216b4ab836.', '2025-04-18 09:09:08'),
(60, 'ADIDAS BASEBALL CAP WHITE', 'Equipment', 'Adidas', 'Cap', 'Men', 'Normal', 99.00, 0.00, 'BASEBALL CAP\r\nA LIGHTWEIGHT CAP WITH AN ADJUSTABLE FIT.\r\nTop things off nicely with this classic Baseball Cap. Made with sunny days in mind, the soft fabric shields you from the sun rays. An adidas Badge of Sport takes centre stage in front.\r\n\r\nSPECIFICATIONS\r\n- 100% cotton twill\r\n- Soft, lightweight feel\r\n- Classic, adjustable cap\r\n- UV 50 factor\r\n- Padded sweatband\r\n- Medium-curved crown and visor\r\n- Twill\r\n- Product Colour: White / White / Black\r\n', '6802179d95475.png', '6802179d95479.png', '6802179d9547b.png', '6802179d9547c.png', '6802179d9547d.', '2025-04-18 09:13:01'),
(61, 'NIKE EVERYDAY LIGHTWEIGHT TRAINING CREW SOCKS (3 PAIRS) WHITE', 'Equipment', 'Nike', 'Sock', 'Men', 'Normal', 69.00, 0.00, 'NIKE EVERYDAY LIGHTWEIGHT TRAINING CREW SOCKS (3 PAIRS) WHITE', '6802184bcb53d.png', '6802184bcb540.png', '6802184bcb541.png', '6802184bcb542.png', '6802184bcb543.', '2025-04-18 09:15:55'),
(68, 'Under Armour Style Logo Men\'s Round Neck White', 'Apparel', 'Under Amour', 'Jersey', 'Men', 'Normal', 62.10, NULL, 'Under Armour Style Logo Men\'s Round Neck White', '6824b8dde175a.png', '6824b8dde175d.png', '6824b8dde175e.png', '6824b8dde175f.png', '6824b8dde1760.jpg', '2025-05-14 15:38:05'),
(69, 'Under Armour Sportstyle Logo Men\'s Round Neck Black', 'Apparel', 'Under Amour', 'Jersey', 'Men', 'Normal', 62.10, NULL, 'Under Armour Sportstyle Logo Men\'s Round Neck Black', '6824b9698cce2.png', '6824b9698cce4.png', '6824b9698cce5.png', '6824b9698cce6.png', '6824b9698cce7.jpg', '2025-05-14 15:40:25'),
(70, 'Umbro 1924 Women\'s Jersey NAVY', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Normal', 16.00, NULL, 'Umbro 1924 Women\'s Jersey NAVY', '6824ba0d48bb8.png', '6824ba0d48bba.png', '6824ba0d48bbb.png', '6824ba0d48bbc.png', '6824ba0d48bbd.jpg', '2025-05-14 15:43:09'),
(72, 'PUMA BVB Anniversary Men\'s Jc Replica', 'Apparel', 'Puma', 'Jersey', 'Men', 'Normal', 319.00, NULL, 'PRODUCT STORY\r\nDare to stand out with the 24/25 BVB Special Jersey. Inspired by the 94/95 season, which ended with BVB winning their first league title in 32 years, it lights up in neon yellow for a look as loud as the matchday atmosphere at SIGNAL IDUNA PARK. On the 30th anniversary of the historic season, this jersey is all about being fearless, taking risks, and relentlessly chasing your dreams. Designed for the fans, Replica jersey pairs the same match-worn look with a casual silhouette, details, and materials, ideal for both game day and everyday wear.\r\n\r\nThe jersey comes in exclusive special packaging for a premium experience', '6824bd807575c.png', '6824bd807575e.png', '6824bd807575f.png', '6824bd8075760.png', '6824bd8075761.jpg', '2025-05-14 15:57:52'),
(73, 'PUMA ESSENTIALS FULL-ZIP WOMEN\'S HOODIE BLACK', 'Apparel', 'Puma', 'Jacket', 'Women', 'Normal', 119.50, NULL, 'FEATURES & BENEFITS\r\n\r\nContains Recycled Material: Made with recycled fibres. One of PUMA\'s answers to reduce its environmental impact.\r\n\r\nDETAILS\r\n\r\n- Regular fit\r\n- Jersey-lined hood with drawcord for an adjustable fit\r\n- Kangaroo-style side pockets for convenient storage of belongings\r\n- Ribbed cuffs and waistband\r\n- PUMA No. 1 Logo raised rubber print at left chest\r\n- Cotton and recycled polyester\r\n\r\nMaterial Information\r\n\r\nHood Lining: 100% cotton\r\nRib: 98% cotton, 2% elastane\r\nShell: 68% cotton, 32% polyester', '6824be6ae4acf.png', '6824be6ae4ad2.png', '6824be6ae4ad4.png', '6824be6ae4ad7.png', '6824be6ae4ad8.jpg', '2025-05-14 16:01:46'),
(74, 'NIKE SPORTSWEAR CLUB FLEECE WOMEN\'S FULL-ZIP HOODIE BLACK', 'Apparel', 'Nike', 'Jacket', 'Women', 'Promotion', 255.00, 153.00, 'More Details\r\nBody: 80% cotton/20% polyester. Hood Lining: 100% cotton.\r\nEmbroidered Futura logo\r\nFlat drawcord\r\nFront pocket\r\nMachine wash\r\nImported\r\n80% COTTON/ 20% POLYESTER\r\n\r\nPRODUCT COLOUR : Black/ White', '6824bf33d0882.png', '6824bf33d0885.png', '6824bf33d0886.png', '6824bf33d0887.png', '6824bf33d0888.jpg', '2025-05-14 16:05:07'),
(75, 'Asics Versablast 4 Women\'s Running Shoes Grey 1012B775-501', 'Footwear', 'Asics', 'Running', 'Women', 'Normal', 329.00, NULL, 'The VERSABLAST™ 4 shoe is a versatile training partner for varied workouts and running regimens. ​\r\n\r\nInspired by our BLAST series, we wanted to make this shoe softer and lighter. It’s designed to provide good comfort for a range of activities. ​\r\n\r\nBy focusing on the shoe’s energy return, we equipped the midsole foam with a responsive rebound for a fast feel underfoot.\r\n\r\nStyle #: 1012B775.501', '6824c04470bb2.png', '6824c04470bb8.png', '6824c04470bba.png', '6824c04470bbc.png', '6824c04470bbe.jpg', '2025-05-14 16:09:40'),
(76, 'Adidas Duramo SL2 Women\'s Running Shoes IF9397', 'Footwear', 'Adidas', 'Running', 'Women', 'New', 299.00, NULL, 'Lightweight running shoes for training.\r\nStart your running journey with ease, and then keep going. From your first step to your first 10k race, these adidas running shoes help you train in comfort. The engineered mesh upper and padded heel create a soft, supportive feel, and the LIGHTMOTION midsole adds light, stable cushioning to every stride', '6824c1acbecac.png', '6824c1acbecae.png', '6824c1acbecaf.png', '6824c1acbecb0.png', '6824c1acbecb1.jpg', '2025-05-14 16:15:40'),
(78, 'Adidas F50 Club Junior Football Boots Blue IE1308', 'Footwear', 'Adidas', 'Football Shoes', 'Kid', 'New', 179.00, NULL, 'Push your pace to the limit in lightweight adidas F50 footwear engineered for speed. These juniors\' Club football boots keep you comfortable on the move with a supportive Fiberskin upper and perforated tongue. Underneath, a versatile outsole propels you to lightning-fast play on firm ground and artificial grass surfaces.\r\n\r\nThis product features at least 20% recycled materials. By reusing materials that have already been created, we help to reduce waste and our reliance on finite resources and reduce the footprint of the products we make.', '68311d5b5fd57.png', '68311d5b5fd5b.png', '68311d5b5fd5c.png', '68311d5b5fd5d.png', '68311d5b5fd5e.jpeg', '2025-05-24 01:14:03'),
(79, 'NIKE JR. MERCURIAL VAPOR 15 CLUB FG/MG LITTLE/BIG KIDS\' MULTI-GROUND FOOTBALL BOOTS IN BLACK', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'New', 175.00, NULL, 'FAST FOR THE FIELD.\r\nLearn all the skills and drills, and use them in your next game with the Nike Jr. Mercurial Vapor 15 Club Boots. Made for all different surfaces from artificial to real grass fields, they have the traction you need to move and run up the field. The durable design is meant to hold up through every practice and every minute on game day.\r\n\r\nContain Your Speed\r\nThe speed cage inside is made of a thin but strong material that secures the foot to the plate without adding extra weight.\r\nExceptional Touch\r\nSynthetic leather upper features small textured details that provide grip for better ball control when dribbling at quick speeds.\r\nGround Control\r\nPlastic plates with molded studs are designed to work on real grass and artificial grass fields. It\'s versatile for athletes who must switch between surfaces.\r\nSeamless Fit and Feel\r\nA comfortable lining wraps your foot for a natural, close-fitting feel.\r\n', '68311eb933134.png', '68311eb93313b.png', '68311eb93313d.png', '68311eb93313e.png', '68311eb933140.jpeg', '2025-05-24 01:19:53'),
(80, 'Nike Jr. Tiempo Legend 10 Club Little/Big Kids\' Multi-Ground Low-Top Football Boots', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'Promotion', 155.00, 139.50, 'Nike Jr. Tiempo Legend 10 Club\r\nLittle/Big Kids\' Multi-Ground Low-Top Football Boots\r\nEven Legends find ways to evolve. Whether you are starting out or just playing for fun, the latest iteration of these Club Boots gets you on the field without compromising on quality. Synthetic leather conforms to your foot and doesn\'t overstretch, giving you better control. Lighter and sleeker than any other Tiempo to date, the Legend 10 is for any position on the field, whether you\'re sending a pinpoint pass through the defense or tracking back to stop a breakaway.\r\n', '68311fe67d191.png', '68311fe67d195.png', '68311fe67d1ae.png', '68311fe67d1b4.png', '68311fe67d1b5.jpeg', '2025-05-24 01:24:54'),
(81, 'Nike Mercurial Vapor 16 Academy Junior Football Boots Black FQ8392-002', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'Normal', 269.00, NULL, 'Nike Mercurial Vapor 16 Academy Junior Boot Black FQ8392-002 now available at Al-Ikhsan Sports. Push your pace to the limit in lightweight adidas F50 footwear engineered for speed. These juniors\' Club football boots keep you comfortable on the move with a supportive Fiberskin upper and perforated tongue. Underneath, a versatile outsole propels you to lightning-fast play on firm ground and artificial grass surfaces.\r\n\r\nThis product features at least 20% recycled materials. By reusing materials that have already been created, we help to reduce waste and our reliance on finite resources and reduce the footprint of the products we make.', '683120aeb14ab.png', '683120aeb14ae.png', '683120aeb14af.png', '683120aeb14b0.png', '683120aeb14b1.jpeg', '2025-05-24 01:28:14'),
(82, 'Adidas Predator Club Junior Football Boots White ID3810', 'Footwear', 'Adidas', 'Football Shoes', 'Kid', 'Normal', 179.00, NULL, 'Go into every game knowing you\'ll score in juniors\' adidas Predator Club boots crafted for goals. Their synthetic upper is lined with soft textile for a comfortable feel on the pitch. Turn them over and you\'ll find a versatile outsole designed for precision football on firm ground and artificial grass.\r\n\r\nThis product features at least 20% recycled materials. By reusing materials that have already been created, we help to reduce waste and our reliance on finite resources and reduce the footprint of the products we make.', '683121ac82851.png', '683121ac82857.png', '683121ac82858.png', '683121ac82859.png', '683121ac8285a.jpeg', '2025-05-24 01:32:28'),
(83, 'Nike Jr. Phantom GX 2 Club Little/Big Kids\' MG Low-Top Football Boots', 'Footwear', 'Nike', 'Football Shoes', 'Kid', 'Promotion', 195.00, 175.50, 'Description Return Policy Warranty\r\nNike Jr. Phantom GX 2 Club\r\nLittle/Big Kids\' MG Low-Top Football Boots\r\nWhether you’re starting out or just playing for fun, Club shoes get you on the field without compromising on quality. Created with goals in mind, the Jr. Phantom GX 2 Club has a grippy texture covering the striking area of the shoe and reliable traction to help guide your unscripted agility.\r\n', '683122057271d.png', '6831220572722.png', '6831220572723.png', '6831220572724.png', '6831220572725.jpeg', '2025-05-24 01:33:57'),
(84, 'NIKE FLEX RUNNER 2 BABY/TODDLER SHOES BLACK', 'Footwear', 'Nike', 'Kid Shoes', 'Kid', 'Promotion', 145.00, 87.00, 'Nike Flex Runner 2\r\nBaby/Toddler Shoes\r\nA SLIP-ON FOR LITTLE SPEEDSTERS.\r\nWhoÃ¢â‚¬â„¢s ready to play? The Nike Flex Runner 2 is built for the kiddo on the goÃ¢â‚¬â€from the crib to the playground to wherever their day takes them. ItÃ¢â‚¬â„¢s laces-free! Meaning itÃ¢â‚¬â„¢s super quick to slip on and off. The straps and bootie-like design make sure your little oneÃ¢â‚¬â„¢s fit stays snug.\r\n', '6831234788538.png', '683123478853d.png', '683123478853e.png', '683123478853f.png', '6831234788540.jpeg', '2025-05-24 01:39:19'),
(85, 'Nike Pico 5 Baby & Toddler Shoes', 'Footwear', 'Nike', 'Kid Shoes', 'Kid', 'Promotion', 125.00, 75.00, 'No cancellation of order, EXCHANGE, or refund will be entertained, unless the item you have ordered is out of stock or we are unable to fulfill your order for any reason whatsoever.\r\n\r\nIf an item is defective, we will replace the item once the defective item is returned to us, provided that we receive notice of the defect within seven (7) days of receipt of such defective item by you, together with the receipt evidencing the purchase made by you.', '683123bf06964.png', '683123bf06969.png', '683123bf0696a.png', '683123bf0696b.png', '683123bf0696c.jpeg', '2025-05-24 01:41:19'),
(86, 'Adidas Tensaur Hook And Loop Kids\' Shoes Black', 'Footwear', 'Adidas', 'Kid Shoes', 'Kid', 'Promotion', 159.00, 99.00, 'ROBUST TRAINERS THAT FASTEN EASILY, MADE IN PART WITH RECYCLED MATERIALS.\r\nSpinning, striding, leaping or sprinting, kids are constantly testing their shoes. Built with a sturdy, non-marking rubber outsole, these adidas trainers grip the ground while leaving floors exactly how they found them. And the two hook-and-loop straps on each shoe are easy to fasten when they\'re on the go.\r\n\r\nMade with a series of recycled materials, this upper features at least 50% recycled content. This product represents just one of our solutions to help end plastic waste.', '68312438573e1.png', '68312438573e4.png', '68312438573e5.png', '68312438573e6.png', '68312438573e7.jpeg', '2025-05-24 01:43:20'),
(87, 'PUMA Anzarun Lite Kids\' Trainers Pink', 'Footwear', 'Puma', 'Kid Shoes', 'Kid', 'Promotion', 169.00, 101.40, 'Deconstructed and refined, the Anzarun Lite Trainers ensure a clean look that\'s perfect for every occasion. Featuring a breathable mesh upper, a cushy EVA midsole and true heritage PUMA branding throughout, this shoe is comfort and style combined.\r\n', '683124be35649.png', '683124be3564d.png', '683124be3564e.png', '683124be3564f.png', '683124be35650.jpeg', '2025-05-24 01:45:34'),
(88, 'ADIDAS TENSAUR SPORT TRAINING SHOES JUNIOR BLACK', 'Footwear', 'Adidas', 'Kid Shoes', 'Kid', 'Promotion', 159.00, 99.00, 'ROBUST TRAINERS THAT FASTEN EASILY, MADE IN PART WITH RECYCLED MATERIALS.\r\nSpinning, striding, leaping or sprinting, kids are constantly testing their shoes. Built with a sturdy, non-marking rubber outsole, these adidas trainers grip the ground while leaving floors exactly how they found them. And the two hook-and-loop straps on each shoe are easy to fasten when they\'re on the go.\r\n\r\nMade with a series of recycled materials, this upper features at least 5% recycled content. This product represents just one of our solutions to help end plastic waste.', '683125507ed57.png', '683125507ed5c.png', '683125507ed5d.png', '683125507ed5e.png', '683125507ed5f.jpeg', '2025-05-24 01:48:00'),
(89, 'PUMA Flyer Runner V Kids Trainers Grey', 'Footwear', 'Puma', 'Kid Shoes', 'Kid', 'New', 169.00, NULL, 'Comfort is key in PUMA\'s Flyer Runner. With a breathable mesh upper, an extra-soft sockliner and a cushioning midsole, this lightweight trainer adds style to sportswear.\r\n', '6831260b6d224.png', '6831260b6d228.png', '6831260b6d22a.png', '6831260b6d22b.png', '6831260b6d22c.jpeg', '2025-05-24 01:51:07'),
(90, 'PUMA Flyer Runner Junior Trainers Pink', 'Footwear', 'Puma', 'Kid Shoes', 'Kid', 'Normal', 179.00, NULL, 'Comfort is key in PUMA\'s Flyer Runner. With a breathable mesh upper, an extra-soft sockliner and a cushioning midsole, this lightweight trainer adds style to sportswear.', '683126e45e24d.png', '683126e45e251.png', '683126e45e252.png', '683126e45e253.png', '683126e45e254.jpeg', '2025-05-24 01:54:44'),
(91, 'Under Armour Assert 10 AC Junior School Shoes Black', 'Footwear', 'Under Amour', 'School Shoes', 'Kid', 'Normal', 99.00, NULL, 'Under Armour Assert 10 AC Junior School Shoes', '6831279d6eb04.png', '6831279d6eb09.png', '6831279d6eb0a.png', '6831279d6eb0b.png', '6831279d6eb0c.jpeg', '2025-05-24 01:57:49'),
(92, 'Under Armour Phade RN 2 Junior School Shoes Black', 'Footwear', 'Under Amour', 'School Shoes', 'Kid', 'Normal', 199.00, NULL, 'Under Armour Phade RN 2 Junior School Shoes Black', '6831281f3fdaf.png', '6831281f3fdb6.png', '6831281f3fdb9.png', '6831281f3fdbb.png', '6831281f3fdbd.jpeg', '2025-05-24 01:59:59'),
(93, 'Nike Academy Big Kids\' Dri-FIT Football Track Jacket', 'Apparel', 'Nike', 'Jacket', 'Kid', 'Promotion', 165.00, 132.00, 'Nike Academy\r\nBig Kids\' Dri-FIT Football Track Jacket\r\nTake your skills to the next level in this Academy Football jacket. We infused its soft double knit fabric with our sweat-wicking tech to help you stay dry and comfortable as you fine-tune your skills.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nDouble knit fabric is soft and smooth.\r\nZippered side hand pockets help provide secure storage.\r\n', '683128fcb690b.png', '683128fcb6911.png', '683128fcb6912.png', '683128fcb6913.png', '683128fcb6914.jpeg', '2025-05-24 02:03:40'),
(94, 'Nike Sportswear Club Junior Jacket Green FZ5513-037', 'Apparel', 'Nike', 'Jacket', 'Kid', 'New', 129.00, NULL, 'Nike Sportswear Club Junior Jacket Green FZ5513-037 now available at Al-Ikhsan Sports.\r\n\r\nNike Sportswear Club Big Kids\' Full-Zip Knit Jacket Infused with classic Nike style, this jacket has the familiar fit of our Club Fleece favorites with the soft feel of a lightweight jersey knit fabric. Slip it over your favorite tee when you want an extra layer but worry fleece is too warm.  Single knit jersey fabric is soft and lightweight for comfortable wear. Tall collar helps block out cold drafts while ribbed cuffs and hem help hold the jacket in place as you move. With just the right amount of room through the body and hips, this jacket has a classic fit that makes layering easy.', '683129820f25b.png', '683129820f25e.png', '683129820f25f.png', '683129820f260.png', '683129820f261.jpeg', '2025-05-24 02:05:54'),
(95, 'Nike Sportswear Junior Jacket Purple FZ5557-537', 'Apparel', 'Nike', 'Jacket', 'Kid', 'Normal', 195.00, NULL, 'Nike Sportswear\r\nGirls\' Oversized Lightweight Jacket\r\nMeet an all-star styling piece you can wear every day. Made from smooth woven twill, this lightweight jacket features a water-repellent finish that helps you stay dry in wet weather. Plus, dropped shoulders and a spacious fit through the body and sleeves make layering a breeze.\r\n\r\nWater-repellent finish helps keep you dry in wet weather.\r\nWoven twill fabric is lightweight and smooth for comfortable wear.\r\nHigh collar helps block out the wind for a touch of added warmth.', '683129e348ea2.png', '683129e348ea5.png', '683129e348ea6.png', '683129e348ea7.png', '683129e348ea8.jpeg', '2025-05-24 02:07:31'),
(96, 'PUMA ESS+ Mid 90S Junior Boy Hoodie Beige', 'Apparel', 'Puma', 'Jacket', 'Kid', 'Promotion', 199.00, 179.10, 'This comfy hoodie is built for everyday. Show off bold graphics front and back, stay cosy in the lined hood and pockets. The ribbed hem and cuffs keep the chill out.', '68312a5375b53.png', '68312a5375b5b.png', '68312a5375b5d.png', '68312a5375b5f.png', '68312a5375b60.jpeg', '2025-05-24 02:09:23'),
(97, 'PUMA ESS+ Mid 90S Junior Boy Hoodie Black', 'Apparel', 'Puma', 'Jacket', 'Kid', 'Promotion', 199.00, 179.10, 'This comfy hoodie is built for everyday. Show off bold graphics front and back, stay cosy in the lined hood and pockets. The ribbed hem and cuffs keep the chill out', '68312abfad007.png', '68312abfad00b.png', '68312abfad00c.png', '68312abfad00d.png', '68312abfad00e.jpeg', '2025-05-24 02:11:11'),
(98, 'ADIDAS ENTRADA 22 JUNIOR JERSEY RED', 'Apparel', 'Adidas', 'Jersey', 'Kid', 'New', 65.00, NULL, 'Train like a professional. Relax like a champion. This juniors\' soccer jersey shows off a clean, classic design with an adidas Badge of Sport on the chest. Moisture-absorbing AEROREADY will keep you dry and cool whether you\'re training on the pitch or enjoying a night out with friends.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '68312b818159c.png', '68312b81815a1.png', '68312b81815a3.png', '68312b81815a4.png', '68312b81815a5.jpeg', '2025-05-24 02:14:25'),
(99, 'ADIDAS ENTRADA 22 JUNIOR JERSEY BLUE', 'Apparel', 'Adidas', 'Jersey', 'Kid', 'Normal', 65.00, NULL, 'Train like a professional. Relax like a champion. This juniors\' soccer jersey shows off a clean, classic design with an adidas Badge of Sport on the chest. Moisture-absorbing AEROREADY will keep you dry and cool whether you\'re training on the pitch or enjoying a night out with friends.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '68312bf67799d.png', '68312bf6779a0.png', '68312bf6779a1.png', '68312bf6779a2.png', '68312bf6779a3.jpeg', '2025-05-24 02:16:22'),
(100, 'Nike Dri-FIT Academy23 Kids\' Football Top', 'Apparel', 'Nike', 'Jersey', 'Kid', 'Promotion', 59.00, 47.20, 'Nike Dri-FIT Academy23\r\nKids\' Football Top\r\nHere at Academy23, we\'re all about encouraging you to work on those ball skills. Whether you\'re at practice after school or out at recess, this soft, stretchy knit top is a great choice to keep you cool and comfortable. Plus, the classic fit gives you room to move without distractions. Ready, set, let\'s GOAL!\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nBreathable, stretchy knit fabric feels airy and smooth.\r\nMesh side panels ramp up the breathability to keep you cool.\r\n', '68312c65e2014.png', '68312c65e2018.png', '68312c65e2019.png', '68312c65e201a.png', '68312c65e201b.jpeg', '2025-05-24 02:18:13'),
(101, 'ADIDAS ENTRADA 22 JUNIOR JERSEY WHITE', 'Apparel', 'Adidas', 'Jersey', 'Kid', 'Promotion', 65.00, 45.50, 'Train like a professional. Relax like a champion. This juniors\' football jersey shows off a clean, classic design with an adidas Badge of Sport on the chest. Moisture-absorbing AEROREADY will keep you dry and cool whether you\'re training on the pitch or enjoying a night out with friends.\r\n\r\nMade with 100% recycled materials, this product represents just one of our solutions to help end plastic waste.', '68312cc41f030.png', '68312cc41f035.png', '68312cc41f036.png', '68312cc41f037.png', '68312cc41f038.jpeg', '2025-05-24 02:19:48'),
(102, 'Nike Academy Big Kids\' Dri-FIT Football Top Purple', 'Apparel', 'Nike', 'Jersey', 'Kid', 'New', 79.00, NULL, 'Nike Academy\r\nBig Kids\' Dri-FIT Football Top\r\nDesigned to help you stay cool and comfortable while you fine-tune your skills, this lightweight knit top is infused with our sweat-wicking Dri-FIT technology. Mesh at the back and sides adds breathability.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nMesh panels help add breathability.', '68312d2b118d6.png', '68312d2b118d9.png', '68312d2b118da.png', '68312d2b118db.png', '68312d2b118dc.jpeg', '2025-05-24 02:21:31'),
(103, 'PUMA teamLIGA Junior Football Jersey', 'Apparel', 'Puma', 'Jersey', 'Kid', 'Normal', 79.00, NULL, 'For those who love the battle of the competition as much as the win, teamLIGA steps up its game to bring you a collection with a redesigned fit, scoring big on performance and wearability. Whether you\'re playing your greatest match or cheering your team from the sidelines, you\'ll stay cool and comfortable in this clean-lined jersey featuring PUMA\'s moisture-wicking dryCELL technology and mesh detailing.', '68312d9f2908f.png', '68312d9f29092.png', '68312d9f29093.png', '68312d9f29094.png', '68312d9f29095.jpeg', '2025-05-24 02:23:27'),
(104, 'PUMA teamGOAL Junior Football Jersey Red', 'Apparel', 'Puma', 'Jersey', 'Kid', 'New', 69.00, NULL, 'Help your team keep up with the demands of the game at every level with this football jersey. Combining high-performance materials and a modern design, jersey is here to take your team to the next level. The jersey is treated to wick away moisture.\r\n\r\n', '68312df0a1db3.png', '68312df0a1db7.png', '68312df0a1db8.png', '68312df0a1db9.png', '68312df0a1dba.jpeg', '2025-05-24 02:24:48'),
(105, 'Nike Dri-FIT Academy23 Big Kids\' Short-Sleeve Football Shirt Pink', 'Apparel', 'Nike', 'Jersey', 'Kid', 'Promotion', 89.00, 62.30, 'Nike Dri-FIT Academy23\r\nBig Kids\' Short-Sleeve Football Shirt\r\nDribble down the field, pass to your friend and score as many good times as you can. Whether you\'re at practice or just out at recess, this breathable and easy-fitting Shirt helps you stay cool and comfortable. The fabric pulls sweat away from your skin while mesh panels on the back and under the sleeves allow for airflowâ€”perfect for when play heats up.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nKnit fabric is lightweight and smooth.\r\nMesh on the back and sleeves adds breathability to help you stay cool.\r\n', '68312e5ee368a.png', '68312e5ee368e.png', '68312e5ee368f.png', '68312e5ee3690.png', '68312e5ee3691.jpeg', '2025-05-24 02:26:38'),
(106, 'Nike Sportswear Big Kids\' Track Pants Blue', 'Apparel', 'Nike', 'Pant', 'Kid', 'New', 90.00, NULL, 'PRODUCT COLOUR : Aquarius Blue/ Court Blue/ White', '68312ed372bbe.png', '68312ed372bc3.png', '68312ed372bc6.png', '68312ed372bc9.png', '68312ed372bca.jpeg', '2025-05-24 02:28:35');
INSERT INTO `product` (`product_id`, `product_name`, `product_type`, `brand`, `product_categories`, `gender`, `status`, `price`, `discount_price`, `description`, `product_img1`, `product_img2`, `product_img3`, `product_img4`, `size_chart`, `create_at`) VALUES
(107, 'Nike Trophy23 Big Kids\' Dri-FIT Training Shorts Grey', 'Apparel', 'Nike', 'Pant', 'Kid', 'Promotion', 79.00, 55.30, 'Nike Trophy23\r\nBig Kids\' Dri-FIT Training Shorts\r\nWhether you\'re dreaming of playing at the next level, or a beginner learning the sport, the Nike Trophy23 shorts get you geared up for the game. Super breathable fabric that kicks sweat away helps you stay cool and confident while honing your skills.\r\n\r\nNike Dri-FIT technology moves sweat away from your skin for quicker evaporation, helping you stay dry and comfortable.\r\nSmooth knit fabric is lightweight and breathable.\r\nMesh side panels provide extra breathability.\r\nElastic waistband and drawcord provide a snug fit.\r\n', '68312f38c5d26.png', '68312f38c5d2e.png', '68312f38c5d30.png', '68312f38c5d32.png', '68312f38c5d33.jpeg', '2025-05-24 02:30:16'),
(108, 'Puma No.1 Logo Junior Boy\'s T-Bottom Black', 'Apparel', 'Puma', 'Pant', 'Kid', 'Normal', 129.00, NULL, 'Puma No.1 Logo Junior Boy\'s T-Bottom Black', '68312fa414f65.png', '68312fa414f69.png', '68312fa414f6a.png', '68312fa414f6b.png', '68312fa414f6c.jpeg', '2025-05-24 02:32:04'),
(109, 'Nike Multiessntl Junior\'s T-Bottom', 'Apparel', 'Nike', 'Pant', 'Kid', 'Normal', 145.00, NULL, 'NIKE A T/BOTTOM JR B MULTI ESSNTL 0125', '6831300c3e5f1.png', '6831300c3e5f8.png', '6831300c3e5fa.png', '6831300c3e5fc.png', '6831300c3e5fe.jpeg', '2025-05-24 02:33:48'),
(110, 'Nike Academy 23 Junior Boy Short Black', 'Apparel', 'Nike', 'Pant', 'Kid', 'New', 99.00, NULL, 'Nike Academy 23 Junior Boy Short Black', '6831308fbae79.png', '6831308fbae7e.png', '6831308fbae97.png', '6831308fbae9b.png', '6831308fbae9c.jpeg', '2025-05-24 02:35:59'),
(111, 'NIKE CLUB KIDS\' ADJUSTABLE UNSTRUCTURED BOXY CAP BLUE', 'Equipment', 'Nike', 'Cap', 'Kid', 'Promotion', 69.00, 48.30, 'Nike Club\r\nKids\' Adjustable Unstructured Boxy Cap\r\nOur beloved mascot is moving up in the world! Boxy started on tees and now joins in on headwear. Seen here on this mid-depth, curved-bill Club Cap, Boxy is sure to make all your friends and classmates feel welcome with a nice, big ol\' wave.\r\n\r\nTwill fabric is lightweight and smooth.\r\nClub caps all have any-day style with a mid depth.\r\nBranded metal tri-glide lets you adjust your fit with ease.', '683131dfc9017.png', '683131dfc901c.png', '683131dfc901d.png', '683131dfc901f.png', '683131dfc9020.jpeg', '2025-05-24 02:41:35'),
(112, 'NIKE APEX KIDS\' FUTURA BUCKET HAT PURPLE', 'Equipment', 'Nike', 'Cap', 'Kid', 'Promotion', 69.00, 48.30, 'X\r\nNike Apex\r\nKids\' Futura Bucket Hat\r\nEnjoy the sun and add some fun to your look with the Nike Apex Bucket Hat. As easy to throw on as it is to pack away when you\'re not wearing it, this lightweight hat keeps you comfortable whenever the sun\'s out and adventure is on the menu!\r\n\r\nApex bucket hats all have a mid depth and 360-degree coverage.\r\nEasy-to-store unstructured design packs away flat.\r\n', '68313241a2432.png', '68313241a2436.png', '68313241a2437.png', '68313241a2438.png', '68313241a2439.jpeg', '2025-05-24 02:43:13'),
(113, 'Nike Club Unstructured Futura Wash Junior Boy Caps', 'Equipment', 'Nike', 'Cap', 'Kid', 'New', 49.00, NULL, 'Fly under the radar in this unstructured, mid-depth Nike Club Cap. Washed for a well-worn, well-loved look, it\'s a lightweight, easy-to-wear curved-brim cap with a simple design that you can add to practically any look.', '683132a5ca665.png', '683132a5ca66a.png', '683132a5ca66c.png', '683132a5ca66d.png', '683132a5ca670.jpeg', '2025-05-24 02:44:53'),
(114, 'NIKE CLUB KIDS\' UNSTRUCTURED FUTURA WASH CAP WHITE', 'Equipment', 'Nike', 'Cap', 'Kid', 'New', 49.00, NULL, 'Nike Club\r\nKids\' Unstructured Futura Wash Cap\r\nFly under the radar in this unstructured, mid-depth Nike Club Cap. Washed for a well-worn, well-loved look, it\'s a lightweight, easy-to-wear curved-brim cap with a simple design that you can add to practically any look.\r\n\r\nOrganic cotton twill fabric is lightweight and smooth.\r\nMid-depth, 6-panel design makes for easy styling.\r\nSwoosh branded metal tri-glide lets you adjust your fit with ease.', '683132e6a6236.png', '683132e6a6239.png', '683132e6a623a.png', '683132e6a623b.png', '683132e6a623c.jpeg', '2025-05-24 02:45:58'),
(115, 'Nike Club 1024 Junior\'s Caps', 'Equipment', 'Nike', 'Cap', 'Kid', 'Normal', 49.00, NULL, 'NIKE E CAPS JR B CLUB 1024-0225', '6831332bbbcb0.png', '6831332bbbcb5.png', '6831332bbbcb6.png', '6831332bbbcb7.png', '6831332bbbcb8.jpeg', '2025-05-24 02:47:07'),
(116, 'Umbro Basic School Socks Black', 'Equipment', 'Umbro', 'Sock', 'Kid', 'Normal', 9.90, NULL, 'Umbro Basic School Socks Black', '6831379fbcad4.png', '6831379fbcad9.png', '6831379fbcada.png', '6831379fbcadb.png', '6831379fbcadc.jpeg', '2025-05-24 03:06:07'),
(117, 'PUMA SNEAKER SOCKS JUNIOR 3P BLACK', 'Equipment', 'Puma', 'Sock', 'Kid', 'Normal', 39.00, NULL, 'PUMA SNEAKER SOCKS JUNIOR 3P BLACK', '68313afa30452.png', '68313afa30456.png', '68313afa30458.png', '68313afa30459.png', '68313afa3045a.jpeg', '2025-05-24 03:20:26'),
(118, 'Umbro Basic 2in1 School Socks Black', 'Equipment', 'Umbro', 'Sock', 'Kid', 'Normal', 16.90, NULL, 'Umbro Basic 2in1 School Socks Black', '68313b78d917e.png', '68313b78d9182.png', '68313b78d9183.png', '68313b78d9184.png', '68313b78d9185.jpeg', '2025-05-24 03:22:32'),
(119, 'Nike Everyday Kids\' Cushioned Ankle Socks White (6 Pairs)', 'Equipment', 'Nike', 'Sock', 'Kid', 'New', 69.00, NULL, 'Nike Everyday Kids\' Cushioned Ankle Socks White (6 Pairs)', '68313bc7ccfce.png', '68313bc7ccfd2.png', '68313bc7ccfd3.png', '68313bc7ccfd4.png', '68313bc7ccfd5.jpeg', '2025-05-24 03:23:51'),
(120, 'NIKE ELEMENTAL KIDS\' BACKPACK (20L) BLACK DR6084-010', 'Equipment', 'Nike', 'Bag', 'Kid', 'New', 139.00, NULL, 'Nike Elemental\r\nKids\' Backpack (20L)\r\nWhether it\'s rushing to class or hanging out after school with friends, this backpack has the space to tackle it all. This bag features a large side pocket for water bottles as well as a front pocket to store quick-grab items. Adjustable padded straps make wearing this bag comfortable and easy.\r\n\r\nConnected by a secure clip, the bag also comes with a large pencil case.\r\nDouble zippers on main compartment allow for easy opening.\r\nHaul loop gives you an alternate carrying option.\r\n', '6833b9334bdb6.png', '6833b9334bdba.png', '6833b9334bdbb.png', '6833b9334bdbc.png', '6833b9334bdbd.', '2025-05-26 00:43:31'),
(121, 'Nike Elemental Junior Boy', 'Equipment', 'Nike', 'Bag', 'Kid', 'Normal', 139.00, NULL, 'Nike Elemental Junior Boy Bagpack', '6833b99792281.png', '6833b9979228b.png', '6833b9979228d.png', '6833b9979228f.png', '6833b99792290.', '2025-05-26 00:45:11'),
(122, 'Puma Phase AOP Small Kid\'s Backpack Blue', 'Equipment', 'Puma', 'Bag', 'Kid', 'Normal', 75.00, NULL, 'Puma Phase AOP Small Kid\'s Backpack Blue', '6833ba0e9c1e8.png', '6833ba0e9c1ec.png', '6833ba0e9c1ed.png', '6833ba0e9c1ee.png', '6833ba0e9c1ef.jpeg', '2025-05-26 00:47:10'),
(123, 'NIKE ELEMENTAL KIDS\' BACKPACK (20L) NAVY', 'Equipment', 'Nike', 'Bag', 'Kid', 'Promotion', 139.00, 97.30, 'Nike Elemental\r\nKids\' Backpack (20L)\r\nLet your backpack stand out just as much as your kicks. Designed with a padded back panel and adjustable straps, this bag is all about a comfy all-day carry. The removable case has plenty of room for all your pens, pencils, erasers or chargers, ensuring your small stuff stays organized and easy to reach.\r\n\r\nHaul loop gives you an alternate carrying option.\r\nPadded shoulder straps are adjustable for a comfortable fit.\r\nSide pockets hold a water bottle or other small essentials.\r\nDetachable case clips onto the bag or fits inside to help keep small items organized.\r\nPadded back panel helps provide a comfortable carry.', '6833ba741e456.png', '6833ba741e459.png', '6833ba741e45a.png', '6833ba741e45b.png', '6833ba741e45c.', '2025-05-26 00:48:52'),
(124, 'Puma Phase Small Kid\'s Backpack Black', 'Equipment', 'Puma', 'Bag', 'Kid', 'New', 75.00, NULL, 'Step up your game with this vibrant backpack. Featuring a spacious main compartment. front zip pocket, and padded shoulder straps, it\'s designed for your dynamic lifestyle', '6833bad9dbf86.png', '6833bad9dbf8a.png', '6833bad9dbf8b.png', '6833bad9dbf8c.png', '6833bad9dbf8d.jpeg', '2025-05-26 00:50:33'),
(125, 'Adidas Uefa Champions League Mini Ball White', 'Equipment', 'Adidas', 'Football Accessories', 'Kid', 'Promotion', 55.00, 38.50, 'The pride of London. The bold design on this adidas UCL Mini ball borrows from the lion-inspired look of the official match ball. Made for showing your skills on the move, this durable ball features a TPU cover for pinpoint control and a foam core that means you never have to pump it up. You\'ll feel like football royalty every time you see that iconic UEFA Champions League logo.', '6833bb4874d0f.png', '6833bb4874d13.png', '6833bb4874d15.png', '6833bb4874d16.png', '6833bb4874d17.', '2025-05-26 00:52:24'),
(126, 'Puma Orbita Fam Mini Ball White', 'Equipment', 'Puma', 'Football Accessories', 'Kid', 'Promotion', 59.00, 53.10, 'Mini ball of the FAM league.', '6833bb9eb4f03.png', '6833bb9eb4f08.png', '6833bb9eb4f09.png', '6833bb9eb4f0a.png', '6833bb9eb4f0b.', '2025-05-26 00:53:50'),
(127, 'Puma Orbita Mfl Mini Ball White', 'Equipment', 'Puma', 'Football Accessories', 'Kid', 'Normal', 59.00, NULL, 'Mini ball of The Malaysia Football League', '6833bbe2b8e4a.png', '6833bbe2b8e50.png', '6833bbe2b8e51.png', '6833bbe2b8e52.png', '6833bbe2b8e53.', '2025-05-26 00:54:58'),
(128, 'Adidas Mini Ball Euro 2024', 'Equipment', 'Adidas', 'Football Accessories', 'Kid', 'Promotion', 55.00, 38.50, 'Adidas Mini Ball Euro 2024', '6833bc3576f97.png', '6833bc3576f9b.png', '6833bc3576f9c.png', '6833bc3576f9d.png', '6833bc3576f9e.', '2025-05-26 00:56:21'),
(129, 'NIKE SKILLS MINI KIDS\' BASKETBALL MULTI', 'Equipment', 'Nike', 'Football Accessories', 'Kid', 'Promotion', 89.00, 62.30, 'Ideal for young athletes under the age of 5, this Nike mini basketball features a moulded, deep-channel design that allows for precise ball control.\r\n', '6833bc79460ef.png', '6833bc79460f3.png', '6833bc79460f4.png', '6833bc79460f5.png', '6833bc79460f6.', '2025-05-26 00:57:29'),
(130, 'Nike Grind Dumbbell', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 73.00, NULL, 'The first of its kind, the Nike Grind Dumbbell is crafted from Nike Grind rubber, a byproduct of Nike\'s footwear manufacturing process. Measured by total product volume, each dumbbell contains at least 20% Nike Grind rubber. With bold Swoosh branding, anti-roll geometry, sculpted edges, and a knurled handle, it delivers durability, comfort, and control for every workout.', '6833bd3d98497.png', '6833bd3d9849a.png', '6833bd3d9849b.png', '6833bd3d9849c.png', '6833bd3d9849d.', '2025-05-26 01:00:45'),
(131, 'Nike Dumbbell', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 92.00, NULL, 'The Nike Dumbbell is your ultimate training companion. With prominent swoosh branding and numbering, robust rubber, and sculpted edges, this dumbbell offers both durability and comfort for all your training sessions. A medium knurled handle ensures a secure and comfortable grip, delivering the ideal balance of friction and control. The dumbbell is arguably the most versatile piece of gym equipment one can own — enabling thousands of movements to the athlete. Pick yours up and get moving! JUST DO IT.', '6833be56c856e.png', '6833be56c8573.png', '6833be56c8574.png', '6833be56c8575.png', '6833be56c8577.', '2025-05-26 01:05:26'),
(132, 'Nike Strength Pro Urethane Dumbbell', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'New', 232.00, NULL, 'Introducing the Nike Strength Pro Urethane Dumbbell, the next evolution in commercial strength equipment. Built with championship athletes in mind, this premium dumbbell blends durability, design, and performance to handle the toughest workouts. High-quality urethane molded around precision-machined steel ensures unmatched durability, making it ideal for commercial use. Its octagonal design prevents rolling, while the flat bottom ensures stability during training. The knurled grip provides secure handling, giving athletes total control with each rep. Combining bold Nike aesthetics and elite performance, Nike Strength Pro sets a new standard in strength training equipment.', '6833beafa7bd2.png', '6833beafa7bd7.png', '6833beafa7bd9.png', '6833beafa7bda.png', '6833beafa7bdb.', '2025-05-26 01:06:55'),
(133, 'Nike Dumbbell Rack Sets', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 7570.00, NULL, 'The 5-50 lb Nike Dumbbell Set features the most popular dumbbells from 5-50 lb and a storage rack that allows you to fully stock your home gym for almost any dumbbell workout. With prominent swoosh branding and numbering, robust rubber, and sculpted edges, this dumbbell offers both durability and comfort for all your training sessions. ', '6833bf5b88f7a.png', '6833bf5b88f7e.png', '6833bf5b88f7f.png', '6833bf5b88f80.png', '6833bf5b88f81.', '2025-05-26 01:09:47'),
(134, 'Nike Dumbbell Tree Sets', 'Equipment', 'Nike', 'Gym Accessories', 'Men', 'Normal', 4004.00, NULL, 'Maximize your training space with the 10-50 lb Nike Grind Dumbbell Tree Set, including 5 pairs of the Nike Grind Dumbbells weighing 10, 20, 30, 40, and 50 lb. The sleek, five-tiered steel tree offers optimal storage, keeping your workout area organized and clutter-free. Paired with the Nike Dumbbell Tree, this bundle offers an optimal storage solution for your essential dumbbells.', '6833bfafbead6.png', '6833bfafbeae5.png', '6833bfafbeae7.png', '6833bfafbeae8.png', '6833bfafbeae9.', '2025-05-26 01:11:11');

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
