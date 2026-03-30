-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2026 at 05:23 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ai_backoffice`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_logs`
--

CREATE TABLE `ai_logs` (
  `id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'ผักและผลไม้'),
(2, 'ระบบ'),
(3, 'ชาจีน / เครื่องดื่มจีน'),
(4, 'ขนมจีนดังๆ'),
(5, 'Gadget จีน'),
(6, 'ของเล่น / กล่องสุ่มจีน (กำลังมาแรง)'),
(7, 'รองเท้า / เสื้อผ้าจีน');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `points`, `created_at`, `email`, `note`, `updated_at`) VALUES
(1, 'ปรีชา จงธรรม', '093926954', 0, '2026-03-24 13:41:39', 'tazaza.ponnon@gmail.com', 'คุณเต้', NULL),
(2, 'เมทิกา ทองรักษ์', '0838978709', 0, '2026-03-28 03:29:32', 'bf.fern1994@gmail.com', 'รายใหญ่', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `customer_id` int(11) DEFAULT NULL,
  `paid` decimal(10,2) DEFAULT NULL,
  `change_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'paid',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `total`, `date`, `customer_id`, `paid`, `change_amount`, `payment_method`, `status`, `created_at`) VALUES
(1, '0.00', '2026-03-24 06:56:09', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 10:23:51'),
(2, '0.00', '2026-03-24 07:03:33', NULL, NULL, '0.00', 'cash', 'paid', '2026-03-24 10:23:51'),
(3, '70.00', '2026-03-24 11:50:51', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 11:50:51'),
(4, '40.00', '2026-03-24 11:51:44', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 11:51:44'),
(5, '50.00', '2026-03-24 12:38:29', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 12:38:29'),
(6, '40.00', '2026-03-24 12:46:26', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 12:46:26'),
(7, '110.00', '2026-03-24 12:52:16', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 12:52:16'),
(8, '110.00', '2026-03-24 13:43:53', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 13:43:53'),
(9, '319.00', '2026-03-24 13:58:25', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 13:58:25'),
(10, '200.00', '2026-03-24 14:08:58', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 14:08:58'),
(11, '232.00', '2026-03-24 14:50:47', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 14:50:47'),
(12, '145.00', '2026-03-24 19:38:07', NULL, NULL, NULL, NULL, 'paid', '2026-03-24 19:38:07'),
(13, '145.00', '2026-03-28 10:26:48', NULL, NULL, NULL, NULL, 'paid', '2026-03-28 10:26:48'),
(14, '65.00', '2026-03-28 10:27:17', NULL, NULL, NULL, NULL, 'paid', '2026-03-28 10:27:17'),
(15, '190.00', '2026-03-29 10:39:35', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 10:39:35'),
(16, '398.00', '2026-03-29 10:40:18', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 10:40:18'),
(17, '369.00', '2026-03-29 11:00:47', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:00:47'),
(18, '0.00', '2026-03-29 11:11:43', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:11:43'),
(19, '0.00', '2026-03-29 11:11:49', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:11:49'),
(20, '0.00', '2026-03-29 11:12:13', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:12:13'),
(21, '0.00', '2026-03-29 11:12:18', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:12:18'),
(22, '90.00', '2026-03-29 11:12:38', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:12:38'),
(23, '50.00', '2026-03-29 11:43:07', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:43:07');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `qty`, `price`, `cost`, `total`) VALUES
(1, 3, 5, 1, '20.00', NULL, NULL),
(2, 3, 8, 5, '10.00', NULL, NULL),
(3, 4, 8, 4, '10.00', NULL, NULL),
(4, 5, 5, 1, '20.00', NULL, NULL),
(5, 5, 7, 1, '20.00', NULL, NULL),
(6, 5, 8, 1, '10.00', NULL, NULL),
(7, 6, 5, 2, '20.00', NULL, NULL),
(8, 7, 5, 2, '20.00', NULL, NULL),
(9, 7, 7, 3, '20.00', NULL, NULL),
(10, 7, 8, 1, '10.00', NULL, NULL),
(11, 8, 5, 1, '20.00', NULL, NULL),
(12, 8, 7, 3, '20.00', NULL, NULL),
(13, 8, 8, 3, '10.00', NULL, NULL),
(14, 9, 5, 1, '20.00', NULL, NULL),
(15, 9, 7, 1, '20.00', NULL, NULL),
(16, 9, 8, 3, '10.00', NULL, NULL),
(17, 9, 9, 1, '40.00', NULL, NULL),
(18, 9, 10, 2, '35.00', NULL, NULL),
(19, 9, 11, 2, '37.00', NULL, NULL),
(20, 9, 12, 1, '65.00', NULL, NULL),
(21, 10, 5, 10, '20.00', NULL, NULL),
(22, 11, 7, 1, '20.00', NULL, NULL),
(23, 11, 9, 1, '40.00', NULL, NULL),
(24, 11, 10, 2, '35.00', NULL, NULL),
(25, 11, 11, 1, '37.00', NULL, NULL),
(26, 11, 12, 1, '65.00', NULL, NULL),
(27, 12, 12, 1, '65.00', NULL, NULL),
(28, 12, 14, 1, '80.00', NULL, NULL),
(29, 13, 7, 1, '20.00', NULL, NULL),
(30, 13, 9, 1, '40.00', NULL, NULL),
(31, 13, 10, 1, '35.00', NULL, NULL),
(32, 13, 13, 1, '50.00', NULL, NULL),
(33, 14, 12, 1, '65.00', NULL, NULL),
(34, 15, 7, 1, '20.00', NULL, NULL),
(35, 15, 9, 1, '40.00', NULL, NULL),
(36, 15, 13, 1, '50.00', NULL, NULL),
(37, 15, 14, 1, '80.00', NULL, NULL),
(38, 16, 17, 1, '199.00', NULL, NULL),
(39, 16, 18, 1, '199.00', NULL, NULL),
(40, 17, 5, 1, '20.00', NULL, NULL),
(41, 17, 8, 1, '10.00', NULL, NULL),
(42, 17, 9, 1, '40.00', NULL, NULL),
(43, 17, 11, 2, '37.00', NULL, NULL),
(44, 17, 12, 1, '65.00', NULL, NULL),
(45, 17, 14, 2, '80.00', NULL, NULL),
(46, 18, 0, NULL, NULL, NULL, NULL),
(47, 18, 0, NULL, NULL, NULL, NULL),
(48, 19, 0, NULL, NULL, NULL, NULL),
(49, 19, 0, NULL, NULL, NULL, NULL),
(50, 20, 0, NULL, NULL, NULL, NULL),
(51, 20, 0, NULL, NULL, NULL, NULL),
(52, 21, 0, NULL, NULL, NULL, NULL),
(53, 21, 0, NULL, NULL, NULL, NULL),
(54, 22, 8, 1, '10.00', NULL, NULL),
(55, 22, 14, 1, '80.00', NULL, NULL),
(56, 23, 13, 1, '50.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `cost` decimal(10,2) DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 5,
  `supplier_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `barcode`, `name`, `price`, `cost`, `stock`, `min_stock`, `supplier_id`, `created_at`, `image`, `category_id`) VALUES
(7, NULL, '0664', 'มะม่วง', '20.00', '0.00', 79, 5, NULL, '2026-03-24 03:50:15', '1774324215ดาวน์โหลด.jpg', 1),
(8, NULL, '170987654321', 'มะนาว', '10.00', '5.00', 31, 5, NULL, '2026-03-24 04:17:44', '1774325864ดาวน์โหลด (1).jpg', 1),
(9, NULL, '885123456', 'แตงโม ชิ้นละ', '40.00', '7.00', 11, 5, NULL, '2026-03-24 06:49:42', '1774334982images.webp', 1),
(10, NULL, '8856543896543', 'ฝรั่งกิมจู', '35.00', '25.00', 39, 5, NULL, '2026-03-24 06:52:15', '1774335135original-1634715957747.jpg', 1),
(11, NULL, '8856543896549', 'ฝรั่งใส่แดง กก.ละ', '37.00', '29.00', 11, 5, NULL, '2026-03-24 06:53:25', '1774335205images.jpeg', 1),
(12, NULL, '', 'เมล่อนเนื้อส้ม ลูกละ', '65.00', '35.00', 15, 5, NULL, '2026-03-24 06:56:16', '1774335376images (1).jpeg', 1),
(13, NULL, '8856253896544', 'เมล่อนเนื้อเขียว กกละ', '50.00', '23.00', 53, 5, NULL, '2026-03-24 06:56:54', '1774335414images (2).jpeg', 1),
(14, NULL, '8856543896544', 'ส้มโอขาวแตงกวา กกละ', '80.00', '25.00', 1195, 5, NULL, '2026-03-24 07:06:29', '1774335989images (3).jpeg', 1),
(15, NULL, '999', 'ระบบหลังบ้าน', '1500.00', '0.00', 999, 5, NULL, '2026-03-24 08:03:12', '1774339573IMG_5212.png', 2),
(16, NULL, '', 'Heytea ชาผลไม้ชีส', '199.00', '99.00', 9, 5, NULL, '2026-03-28 04:46:16', '', 3),
(17, NULL, '', 'CHAGEE ชานมพรีเมียม', '199.00', '99.00', 8, 5, NULL, '2026-03-28 04:47:05', '17747596061774759573590.jpg', 3),
(18, NULL, '', 'Nayuki ชาผลไม้', '199.00', '99.00', 8, 5, NULL, '2026-03-28 04:47:41', '', 0),
(19, NULL, '', 'ชาผู่เอ๋อร์ (สายสุขภาพ)', '199.00', '99.00', 9, 5, NULL, '2026-03-28 04:48:44', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock_logs`
--

INSERT INTO `stock_logs` (`id`, `product_id`, `type`, `qty`, `note`, `created_at`) VALUES
(1, 6, 'add', 0, 'เพิ่มสินค้า', '2026-03-24 10:49:02'),
(2, 7, 'add', 100, 'เพิ่มสินค้า', '2026-03-24 10:50:15'),
(3, 7, 'adjust', -10, 'ปรับสต๊อก', '2026-03-24 10:50:28'),
(4, 8, 'add', 20, 'เพิ่มสินค้า', '2026-03-24 11:17:44'),
(5, 8, 'adjust', 30, 'ปรับสต๊อก', '2026-03-24 11:19:22'),
(6, 9, 'add', 4, 'เพิ่มสินค้า', '2026-03-24 13:49:42'),
(7, 10, 'add', 44, 'เพิ่มสินค้า', '2026-03-24 13:52:15'),
(8, 11, 'add', 16, 'เพิ่มสินค้า', '2026-03-24 13:53:25'),
(9, 12, 'add', 20, 'เพิ่มสินค้า', '2026-03-24 13:56:16'),
(10, 13, 'add', 56, 'เพิ่มสินค้า', '2026-03-24 13:56:54'),
(11, 14, 'add', 1200, 'เพิ่มสินค้า', '2026-03-24 14:06:29'),
(12, 5, 'adjust', 18, 'ปรับสต๊อก', '2026-03-24 14:53:01'),
(13, 15, 'add', 999, 'เพิ่มสินค้า', '2026-03-24 15:03:12'),
(14, 16, 'add', 9, 'เพิ่มสินค้า', '2026-03-28 11:46:16'),
(15, 17, 'add', 9, 'เพิ่มสินค้า', '2026-03-28 11:47:05'),
(16, 18, 'add', 9, 'เพิ่มสินค้า', '2026-03-28 11:47:41'),
(17, 19, 'add', 9, 'เพิ่มสินค้า', '2026-03-28 11:48:44'),
(18, 9, 'adjust', 12, 'ปรับสต๊อก', '2026-03-29 10:41:48');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_logs`
--
ALTER TABLE `ai_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id_2` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barcode` (`barcode`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_logs`
--
ALTER TABLE `ai_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
