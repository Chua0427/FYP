-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2025 at 03:28 AM
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
(1, 25, 5, 'UK6', 21, '2025-04-16 11:19:21');

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
(1, 25, '', '', '2025-04-14 08:06:51');

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
(5, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK', 'Footwear', 'Adidas', 'Running', 'Men', 'New', 120.00, 0.00, 'ADIDAS GALAXY 6 MEN RUNNING SHOES BLACK', '67f5e7b0c8aa6.jpg', '67f5e7b0c92b6.jpg', '67f5e7b0c9d2f.jpg', '67f5e7b0ca726.jpg', '67f52ee52cfd4.jpg', '2025-04-03 04:48:01'),
(6, 'Nike Quest 6 Mens Road Running Shoes FD6033002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 120.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f5e6725324c.jpg', '67f5e67254561.jpg', '67f5e67254d38.jpg', '67f5e67255331.jpg', '67f5387e89e10.jpg', '2025-04-03 07:35:49'),
(7, 'Asics GT2000 13 LiteShow Mens Running Shoes Blue', 'Footwear', 'Asics', 'Running', 'Women', 'New', 120.00, 0.00, 'The GT200 13 running shoe offers great support for a comfortable run that will help you clear your mind. Its the most comfortable version of the shoe yet and the ideal choice if you are looking for extra guidance during your run.\r\n\r\n', '67f5eb24629b0.png', '67f5eb2462e7a.png', '67f5f044d935c.png', '67f5f044d9738.png', '67ee3aed4f0d6.jpg', '2025-04-03 07:38:21'),
(8, 'Nike Quest 6 Men's Road Running Shoes FD6033-002', 'Footwear', 'Nike', 'Running', 'Men', 'New', 369.00, 0.00, 'Nike Quest 6\r\nMen Road Running Shoes\r\nThe Nike Quest 6 is for runners of all levels. But make no mistake, it's anything but entry-level. A supercomfortable and supportive midfoot fit band helps keep you stable for your miles. Plus, a supersoft midsole foam helps cushion each step.', '67f5efee89f95.png', '67f5efee8aad9.png', '67f5efee8aee7.png', '67f5efee8b26e.png', '67ee3d5b17ff8.jpg', '2025-04-03 07:48:43'),
(18, 'Nike ACD TOP NOV Men Training Jersey', 'Apparel', 'Nike', 'Jersey', 'Men', 'Promotion', 135.00, 100.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f63019d51ec.png', '67f63019d51f0.png', '67f63019d51f1.png', '67f714adddae5.png', '67f8d72022188.jpg', '2025-04-09 08:30:17'),
(19, 'Asics Gel Cumulus 27 Lite Show Mens Running Shoes Orange', 'Footwear', 'Asics', 'Running', 'Men', 'New', 150.00, 0.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f631e68b401.png', '67f631e68b405.png', '67f631e68b407.png', '67f631e68b408.png', '67f631e68b409.jpg', '2025-04-09 08:37:58'),
(20, 'Nike Downshifter 13 Mens Road Running Shoes FD6454008', 'Footwear', 'Nike', 'Running', 'Men', 'Promotion', 120.00, 100.00, 'Nike Downshifter 13\r\nMens Road Running Shoes\r\nWhether you are starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a revamped upper and cushion and durability, it helps you find that extra gear or take that first stride toward chasing down your goals.', '67f63260c09de.png', '67f63260c09e2.png', '67f63260c09e3.png', '67f63260c09e4.png', '67f63260c09e5.jpg', '2025-04-09 08:40:00'),
(21, 'Adidas EPP Club Ball WHITE HT2459', 'Equipment', 'Adidas', 'Football Accessories', 'Men', 'Promotion', 100.00, 80.00, 'PRODUCT DESCRIPTIONS\r\nAN EVERYDAY FOOTBALL FOR ANY DAY BALLERS.\r\nWhen your teammates come calling, always be prepared. This adidas EPP Club Ball was built for training sessions, kickabouts in the park and for showing friends your skills. Its durable, machine stitched surface means its ready to stand up to razor-sharp passes and high intensity scrimmages. The butyl bladder ensures optimal air retention, so you can worry less about the ball and focus more on your footwork.', '67f63303d6016.png', '67f63303d6018.png', '67f63303d6019.png', '67f63303d601a.png', '67f63303d601b.', '2025-04-09 08:42:43'),
(22, 'Umbro Men Jersey Blue', 'Apparel', 'Umbro', 'Jersey', 'Men', 'Promotion', 50.00, 10.00, 'The GEL CUMULU 27 running shoe is a great choice if you want a shoe with extra cushioning. With its smooth comfort, you will be able to run further and find your calm along the way.', '67f6334ac43d9.png', '67f6334ac43dd.png', '67f6334ac43de.png', '67f6859bcf9d7.png', '67f6334ac43e0.jpg', '2025-04-09 08:43:54'),
(23, 'ADIDAS ENTRADA 22 MEN FOOTBALL JERSEY RED', 'Apparel', 'Adidas', 'Jersey', 'Men', 'Promotion', 80.50, 60.00, 'ADIDAS ENTRADA 22 MENS FOOTBALL JERSEY RED', '67f633af8d002.png', '67f633af8d005.png', '67f633af8d006.png', '67f685b032d2a.png', '67f633af8d008.jpg', '2025-04-09 08:45:35'),
(24, 'Umbro 1924 Women Jersey MAROON', 'Apparel', 'Umbro', 'Jersey', 'Women', 'Normal', 35.00, 0.00, 'Umbro 1924 Women Jersey MAROON', '67f63a639bb60.png', '67f63a639bb68.png', '67f63a639bb69.png', '67f685cf1fc6b.png', '67f63a639bb6b.jpg', '2025-04-09 09:14:11'),
(25, 'PUMA Scend Pro Men Running Shoes Red', 'Footwear', 'Puma', 'Running', 'Men', 'New', 114.50, 0.00, 'PRODUCT STORY\r\nIntroducing the Scend Pro, PUMA new essential runner. The Protread rubber outsole is designed for distance and enhanced with a bold paint line and cushioned tooling. The breathable mesh uppers are ultra lightweight, while the comfort collar locks down for a secure fit during workouts.', '67f8c2fcb865e.png', '67f8c2fcb8662.png', '67f8c2fcb8663.png', '67f8c2fcb8664.png', '67f8c2fcb8665.jpg', '2025-04-11 07:21:32'),
(26, 'Nike Downshifter 13 Men Running Shoes Black', 'Footwear', 'Nike', 'Running', 'Men', 'Normal', 255.00, 0.00, 'Nike Downshifter 13 Men Running Shoes Black', '67f8c66f0e148.png', '67f8c66f0e14c.png', '67f8c66f0e14d.png', '67f8c66f0e14e.png', '67f8c66f0e14f.jpg', '2025-04-11 07:36:15'),
(27, 'PUMA Velocity Nitro 3 Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'New', 419.30, 0.00, 'PRODUCT DESCRIPTIONS\r\nEmbark on your running journey with the VELOCITY NITR 3, PUMA s hero franchise featuring NITROFOA cushioning. Sleek styling, outstanding comfort, and a slightly higher stack height make it the ideal do-it-all trainer. From short distances to long runs, this neutral shoe offers a smooth ride and optimal cushioning for all runners. Experience the best in comfort and performance with the VELOCITY 3'.'.', '67f8c7497ab71.png', '67f8c7497ab78.png', '67f8c7497ab7a.png', '67f8c7497ab7b.png', '67f8c7497ab7c.jpg', '2025-04-11 07:39:53'),
(28, 'Puma Pounce Lite Men Running Shoes Black', 'Footwear', 'Puma', 'Running', 'Men', 'Normal', 289.00, 0.00, 'The all-new Pounce Lite joins our Running line this year. Featuring an ultra-lightweight foam and a durable PROTREAD outsole, it is the ultimate blend of style and performance.', '67f8c8ff9d98d.png', '67f8c8ff9d990.png', '67f8c8ff9d991.png', '67f8c8ff9d992.png', '67f8c8ff9d993.jpg', '2025-04-11 07:47:11'),
(29, 'New Balance Fresh Foam 510 V6 Men Running Shoes Black', 'Footwear', 'New Balance', 'Running', 'Men', 'Promotion', 359.00, 179.50, 'PRODUCT DESCRIPTIONS\r\nA rugged shoe designed to help you conquer the roughest trails with ease.', '67f8ca88b98cf.png', '67f8ca88b98d2.png', '67f8ca88b98d4.png', '67f8ca88b98d5.png', '67f8ca88b98d6.jpg', '2025-04-11 07:53:44'),
(30, 'Nike Phantom GX 2 Elite LV8 FG Low-Top Football Boots', 'Footwear', 'Nike', 'Boot', 'Men', 'Normal', 1229.00, 0.00, 'Nike Phantom GX 2 Elite LV8\r\nFG Low-Top Football Boots\r\nObsessed with perfecting your craft? We made this for you. In the middle of the storm, with chaos swirling all around you, you've calmly found the final third of the field, thanks to your uncanny mix of on-ball guile and grace. Go finish the job in the Phantom GX 2 Elite. Revolutionary Nike Gripknit covers the striking area of the cleat while Nike Cyclone 360 traction helps guide your unscripted agility. We design Elite Boots for you and the world's biggest stars to give you high-level quality, because you demand greatness from yourself and your footwear.', '67f8cb61928fa.png', '67f8cb61928ff.png', '67f8cb6192900.png', '67f8cb6192901.png', '67f8cb6192902.jpg', '2025-04-11 07:57:21'),
(31, 'Adidas F50 Club Firm/Multi-Ground Men Football Boots', 'Footwear', 'Adidas', 'Boot', 'Men', 'Normal', 219.00, 0.00, 'STAY ONE STEP AHEAD IN F50 BOOTS DESIGNED FOR MULTIPLE SURFACES.\r\n\r\nPRODUCT STORY\r\n\r\nPush your pace to the limit in lightweight adidas F50 footwear engineered for speed. These Club football boots keep you comfortable on the move with a supportive Fiberskin upper and perforated tongue. Underneath, a versatile outsole propels you to lightning-fast play on firm ground and artificial grass surfaces.\r\nDETAILS\r\n\r\nRegular fit\r\nLace closure\r\nSynthetic Fiberskin upper\r\nPerforated tongue\r\nTextile lining\r\nFirm/multi-ground outsole', '67f8cc45b1590.png', '67f8cc45b1593.png', '67f8cc45b1594.png', '67f8cc45b1595.png', '67f8cc45b1596.jpg', '2025-04-11 08:01:09'),
(32, 'Adidas Deportivo III Flexible Ground Men Football Boots', 'Footwear', 'Adidas', 'Boot', 'Men', 'Promotion', 159.00, 127.20, 'Adidas Deportivo III Flexible Ground Men Football Boots\r\nComfortable synthetic boots with a versatile outsole.\r\n\r\nTake the beautiful game seriously in these adidas football boots. Designed for play on a variety of surfaces, they have a perforated tongue and soft lining to keep your feet fresh through long matches. Stitching on the synthetic forefoot improves ball control, allowing for pinpoint passes and powerful strikes. An anatomically shaped heel keeps you strapped in for fast-moving play.\r\n\r\nMore Details\r\n- Regular fit\r\n- Lace closure\r\n- Synthetic upper\r\n- Textile lining\r\n- Breathable perforated tongue\r\n- Control stitching on forefoot\r\n- Flexible ground outsole\r\nColor : White,Solar Red,Lucid Blue', '67f8cce2c043e.png', '67f8cce2c0441.png', '67f8cce2c0442.png', '67f8cce2c0443.png', '67f8cce2c0444.jpg', '2025-04-11 08:03:46'),
(33, 'PUMA Men Boot Future Play FG/AG', 'Footwear', 'Puma', 'Boot', 'Men', 'Normal', 219.00, 0.00, 'PUMA Men Boot Future Play FG/AG', '67f8cd5089cef.png', '67f8cd5089cf3.png', '67f8cd5089cf5.png', '67f8cd5089cf6.png', '67f8cd5089cf8.jpg', '2025-04-11 08:05:36'),
(34, 'Asics Upcourt 5 Men Court Shoes White', 'Footwear', 'Asics', 'Court', 'Men', 'Promotion', 249.00, 199.20, 'PRODUCT STORY\r\n\r\n\r\nThe UPCOURT 5 shoe is a lightweight court offering that is designed to offer better flexibility and a more comfortable fit.\r\n\r\nIt features a broader section of mesh paneling that helps create a softer and more adaptable fit. Meanwhile, its supportive midfoot overlays offer better stability during multi-directional movements. ?\r\n\r\nLastly, the toe and heel counter are reinforced with durable panels that help your shoes last longer.', '67f8cdee40e3e.png', '67f8cdee40e43.png', '67f8cdee40e44.png', '67f8cdee40e45.png', '67f8cdee40e46.jpg', '2025-04-11 08:08:14'),
(35, 'LOTTO MAXIMO MEN COURT SHOES BLACK', 'Footwear', 'Lotto', 'Court', 'Men', 'Promotion', 119.00, 83.30, 'LOTTO MAXIMO MEN COURT SHOES BLACK', '67f8ce734b277.png', '67f8ce734b27b.png', '67f8ce734b27c.png', '67f8ce734b27d.png', '67f8ce734b27e.jpg', '2025-04-11 08:10:27'),
(36, 'UNDER ARMOUR MEN SPORTSTYLE JERSEY LOGO SHORT SLEEVE ROUND NECK BLACK', 'Apparel', 'Under Amour', 'Jersey', 'Men', 'Promotion', 69.00, 39.50, 'UNDER ARMOUR MEN SPORTSTYLE JERSEY LOGO SHORT SLEEVE ', '67f8cf2dd897d.png', '67f8cf2dd8986.png', '67f8cf2dd8987.png', '67f8cf2dd8988.png', '67f8d72f331da.jpg', '2025-04-11 08:13:33'),
(37, 'PUMA ESSENTIALS SMALL LOGO FULL-ZIP MEN HOODIE NAVY', 'Apparel', 'Puma', 'Jacket', 'Men', 'Promotion', 239.00, 119.50, 'PUMA ESSENTIALS SMALL LOGO FULL-ZIP MEN HOODIE NAVY', '67f8d2db7c9c5.png', '67f8d2db7c9cd.png', '67f8d2db7c9cf.png', '67f8d2db7c9d0.png', '67f8d6a512aae.jpg', '2025-04-11 08:29:15'),
(38, 'PUMA MEN 3/4 PANTS', 'Apparel', 'Puma', 'Paint', 'Men', 'New', 99.00, 0.00, 'PUMA MEN 3/4 PANTS', '67f8d34f0bb18.png', '67f8d34f0bb1b.png', '67f8d34f0bb1c.png', '67f8d34f0bb1d.png', '67f8d6ebab640.jpg', '2025-04-11 08:31:11'),
(39, 'Nike Academy Team Football Duffel Bag (Medium, 60L) Black', 'Equipment', 'Nike', 'Bag', 'Men', 'Normal', 145.00, 0.00, 'Nike Academy Team\r\nFootball Duffel Bag (Medium, 60L)\r\nCONVENIENT STORAGE TO AND FROM THE FIELD.\r\nThe Nike Academy Team Duffel Bag is a durable design built to keep you organized. Designated compartments provide space for your ball, Boots and clothes while multiple straps let you comfortably carry your gear when you are on the go.', '67f8d443de86c.png', '67f8d443de870.png', '67f8d443de871.png', '67f8d443de872.png', '67f8d443de873.jpg', '2025-04-11 08:35:15'),
(40, 'ADIDAS 4ATHLTS DUFFEL BAG LARGE', 'Equipment', 'Adidas', 'Bag', 'Men', 'New', 259.00, 0.00, 'PRODUCT DESCRIPTIONS\r\nA LARGE DUFFEL BAG FOR TRAINING, MADE IN PART WITH RECYCLED CONTENT.\r\nYou ve got a lot of gear to haul. This adidas large duffel bag has the space. The durable bag is sized just right for out of town tournaments or training camps. A zip compartment keeps wet clothes or shoes separate.', '67f8d4c88e90e.png', '67f8d4c88e911.png', '67f8d4c88e912.png', '67f8d4c88e913.png', '67f8d4c88e914.', '2025-04-11 08:37:28'),
(41, 'PUMA ESSENTIALS III CAP BLACK', 'Equipment', 'Puma', 'Cap', 'Men', 'New', 45.00, 0.00, 'PRODUCT STORY\r\nFirst seen as far back as 1860, and rising to prominence in the 1980s, fewer modern streetwear accessories have the same claim to fame as the baseball cap. This hat from our Essentials range is wonderfully simple, with a classic six-panel shape, pure cotton fabrication, a hair-safe hook-and-loop adjuster and a woven badge with understated PUMA branding on the front.\r\n\r\nDETAILS\r\n- Six-panel shape with curved visor\r\n- Structured buckram\r\n- Hair-safe hook-and-loop adjuster\r\n- Moisture-wicking mesh sweatband\r\n- Woven badge with PUMA No. 1 Logo and merrowed edge on front\r\n\r\nMaterial Information\r\nSHELL : 100% cotton', '67f8d7853fa9b.png', '67f8d7853faa2.png', '67f8d7853faa5.png', '67f8d7853faa7.png', '67f8d7853faa9.', '2025-04-11 08:49:09'),
(42, 'Asics Upcourt 5 Women Court Shoes White', 'Footwear', 'Asics', 'Court', 'Women', 'Promotion', 249.00, 199.20, 'PRODUCT STORY The UPCOURT 5 shoe is designed to offer better flexibility and a more comfortable fit. ? It features a broader section of mesh paneling that helps create a softer and more adaptable fit. Meanwhile, its supportive mid-foot overlays offer better stability during multi-directional movements. Lastly, the toe and heel counter are reinforced with durable panels that help your shoes last longer. DETAILS - Supportive overlays - Improves stability. - Mesh panels - Improves comfort. - Toe and heel counter are designed for durability - The sockliner is produced with the solution dyeing process that reduces water usage by approximately', '67f8d88e8b5de.png', '67f8d88e8b5e3.png', '67f8d88e8b5e4.png', '67f8d88e8b5e5.png', '67f8d88e8b5e6.jpg', '2025-04-11 08:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
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
(20, 5, 'UK7.5', '111111', 10, '2025-04-08 14:33:39'),
(21, 7, 'UK13.5C', '111111', 1, '2025-04-04 15:42:26'),
(33, 7, 'UK1.5Y', '111111', 1, '2025-04-04 15:14:43'),
(54, 5, 'UK6', 'SKU1122', 50, '2025-04-08 14:33:47'),
(55, 5, 'UK8.5', 'SKU1123', 60, '2025-04-08 14:33:55'),
(56, 5, 'UK9', 'SKU1124', 80, '2025-04-08 14:34:03'),
(57, 5, 'UK12', 'SKU1125', 100, '2025-04-08 14:33:33'),
(58, 6, 'UK8.5', 'SKU1112', 100, '2025-04-08 14:54:13'),
(59, 6, 'UK9', 'SKU1113', 100, '2025-04-08 14:54:22'),
(60, 6, 'UK9.5', 'SKU1114', 100, '2025-04-08 14:54:33'),
(61, 6, 'UK10', 'SKU1115', 100, '2025-04-08 14:54:42'),
(62, 6, 'UK10.5', 'SKU1116', 100, '2025-04-08 14:54:56'),
(63, 21, 'One Size', 'SKU1222', 10, '2025-04-09 09:39:26'),
(64, 8, 'UK13.5C', 'SKU1122', 1, '2025-04-10 00:42:16'),
(65, 19, 'UK6', 'SKU5525', 10, '2025-04-10 01:06:16'),
(66, 19, 'UK6.5', 'SKU5521', 10, '2025-04-10 01:06:25'),
(67, 19, 'UK10.5', 'SKU5520', 10, '2025-04-10 01:06:34'),
(69, 20, 'UK5', 'SKU3356', 100, '2025-04-10 01:07:11'),
(70, 18, 'XXS', '111111', 10, '2025-04-10 01:07:44'),
(71, 22, 'S', '111112', 10, '2025-04-10 01:07:56'),
(72, 22, 'M', '111113', 10, '2025-04-10 01:08:08'),
(73, 22, 'L', '111114', 10, '2025-04-10 01:08:15'),
(74, 24, 'XXS', '111111', 1, '2025-04-10 05:20:20');

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
(44, 'Evis', 't', 'kaiwen211105@gmail.com', '0125316512', '$2y$10$CKD5Z6khpNEhUPMwbt5qqOy1u24m48gc/Up7ZIm6mudlCIFWiLRCm', 'we', '21121', 'Johor', 'we', '2025-04-23', 'male', NULL, '1', '2025-04-11 15:10:29');

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
(3, 44, '5d0372a8a3e6be5d71a70ed9c89f953e86cc926f9e2b76aca720d3dc32ad4f40', '2025-05-16 13:30:27', '2025-04-16 19:30:27', '2025-04-17 08:52:56', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(4, 44, '90e352ab9ffe568f8ad3fb345879c133266f0773a8086ffe500fe90df70b205a', '2025-05-17 02:39:30', '2025-04-17 08:39:30', NULL, 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1');

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
  ADD KEY `product_id` (`product_id`);

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
  ADD KEY `product_id` (`product_id`);

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
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contactUs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_log`
--
ALTER TABLE `payment_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Constraints for table `payment_log`
--
ALTER TABLE `payment_log`
  ADD CONSTRAINT `fk_log_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
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
