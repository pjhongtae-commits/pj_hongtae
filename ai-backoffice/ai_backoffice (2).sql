-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 03:58 PM
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
(7, 'รองเท้า / เสื้อผ้าจีน'),
(8, 'บะหมี่'),
(9, 'เครื่องใช้ไฟฟ้า'),
(10, 'อุปกรณ์แพ็คกิ้ง'),
(11, 'ขนม'),
(12, 'กาแฟ/เครื่องดื่ม');

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
(23, '50.00', '2026-03-29 11:43:07', NULL, NULL, NULL, NULL, 'paid', '2026-03-29 11:43:07'),
(24, '67.00', '2026-03-31 08:11:20', NULL, NULL, NULL, NULL, 'paid', '2026-03-31 08:11:20'),
(25, '47.00', '2026-04-02 19:27:34', NULL, NULL, NULL, NULL, 'paid', '2026-04-02 19:27:34'),
(26, '50.00', '2026-04-03 19:45:05', NULL, NULL, NULL, NULL, 'paid', '2026-04-03 19:45:05'),
(27, '60.00', '2026-04-12 19:01:20', NULL, NULL, NULL, NULL, 'paid', '2026-04-12 19:01:20'),
(28, '1020.00', '2026-04-13 22:24:22', NULL, NULL, NULL, NULL, 'paid', '2026-04-13 22:24:22'),
(29, NULL, '2026-04-14 08:42:42', NULL, NULL, NULL, NULL, 'paid', '2026-04-14 08:42:42'),
(30, NULL, '2026-04-14 11:47:31', NULL, NULL, NULL, NULL, 'paid', '2026-04-14 11:47:31'),
(41, '380.00', '2026-04-17 11:05:35', NULL, NULL, NULL, NULL, 'paid', '2026-04-17 11:05:35'),
(42, '66.00', '2026-04-17 11:13:25', NULL, NULL, NULL, NULL, 'paid', '2026-04-13 11:13:00');

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
(56, 23, 13, 1, '50.00', NULL, NULL),
(57, 24, 7, 1, '20.00', NULL, NULL),
(58, 24, 8, 1, '10.00', NULL, NULL),
(59, 24, 11, 1, '37.00', NULL, NULL),
(60, 25, 8, 1, '10.00', NULL, NULL),
(61, 25, 11, 1, '37.00', NULL, NULL),
(62, 26, 13, 1, '50.00', NULL, NULL),
(63, 27, 22, 1, '60.00', NULL, NULL),
(69, 41, 37, 1, '380.00', NULL, NULL),
(70, 42, 22, 1, '66.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `price_logs`
--

CREATE TABLE `price_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `diff` decimal(10,2) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `price_logs`
--

INSERT INTO `price_logs` (`id`, `product_id`, `old_price`, `new_price`, `diff`, `source`, `created_at`) VALUES
(1, 14, '105.00', '62.00', '-43.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(2, 20, '65.00', '103.00', '38.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(3, 21, '140.00', '63.00', '-77.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(4, 22, '56.00', '88.00', '32.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(5, 23, '56.00', '53.00', '-3.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(6, 24, '579.00', '93.00', '-486.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(7, 25, '70.00', '119.00', '49.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(8, 26, '18.00', '88.00', '70.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(9, 27, '17.00', '110.00', '93.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(10, 28, '17.00', '117.00', '100.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(11, 29, '17.00', '52.00', '35.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(12, 30, '37.00', '116.00', '79.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(13, 31, '299.00', '43.00', '-256.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(14, 32, '699.00', '72.00', '-627.00', 'AUTO_SYNC', '2026-04-14 15:56:05'),
(15, 41, '0.00', '55.00', '55.00', 'makro_new', '2026-04-22 21:04:29'),
(16, 42, '0.00', '60.00', '60.00', 'makro_new', '2026-04-22 21:04:29'),
(17, 44, '0.00', '65.00', '65.00', 'makro_new', '2026-04-22 21:04:29'),
(18, 43, '0.00', '60.00', '60.00', 'makro_new', '2026-04-22 21:04:29');

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
  `category_id` int(11) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `packaging_cost` decimal(10,2) DEFAULT 0.00,
  `shopee_fee_percent` decimal(5,2) DEFAULT 10.00,
  `profit_percent` decimal(5,2) DEFAULT 20.00,
  `sales_count` int(11) DEFAULT 0,
  `use_ai` tinyint(4) DEFAULT 1,
  `alert_sent` tinyint(4) DEFAULT 0,
  `last_alert_time` datetime DEFAULT NULL,
  `last_price` decimal(10,2) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `profit_last` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `barcode`, `name`, `price`, `cost`, `stock`, `min_stock`, `supplier_id`, `created_at`, `image`, `category_id`, `shipping_cost`, `packaging_cost`, `shopee_fee_percent`, `profit_percent`, `sales_count`, `use_ai`, `alert_sent`, `last_alert_time`, `last_price`, `updated_at`, `profit_last`) VALUES
(14, NULL, '8856543896544', 'ส้มโอขาวแตงกวา กกละ', '35.00', '25.00', 4, 5, NULL, '2026-03-24 07:06:29', '1774335989images (3).jpeg', 1, '0.00', '0.00', '10.00', '5.00', 0, 0, 1, '2026-04-17 10:10:46', '62.00', '2026-04-14 15:56:05', '0.00'),
(20, NULL, '8856573896544', 'มาม่า ต้มยำกุ้ง 55 กรัม แพ็ค 10', '139.00', '65.00', 10, 5, NULL, '2026-04-02 12:31:05', '17751330651775055981570.jpg', 8, '29.00', '10.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '103.00', '2026-04-14 15:56:05', '0.00'),
(21, NULL, '', 'กางเกงสแล็คผ้ายืด ขายาว ทรงกระบอกเล็ก แบบสุภาพ สีดำ', '187.00', '140.00', 30, 5, NULL, '2026-04-03 12:49:46', '17752205861775213772013.jpg', 7, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 08:42:43', '63.00', '2026-04-14 15:56:05', '0.00'),
(22, NULL, '895490', 'มาม่า รสคาโบนาร่าเบคอน 85 ก. 4 ซอง', '75.00', '56.00', 29, 5, NULL, '2026-04-12 11:51:57', '17759947171775430399516.jpg', 8, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '88.00', '2026-04-14 15:56:05', '0.00'),
(23, NULL, '916769', 'มาม่า ออเรียลทัล หม่าล่าเนื้อ 85 ก. 4 ซอง', '75.00', '56.00', 10, 5, NULL, '2026-04-12 11:55:25', '17759949251775430136256.jpg', 8, '0.00', '0.00', '10.00', '15.00', 0, 1, 0, NULL, '53.00', '2026-04-14 15:56:05', '0.00'),
(24, NULL, '116500', 'ออตโต้ ฝาอบ รุ่น CO-706/708/703A 12 ล.', '772.00', '579.00', 10, 5, NULL, '2026-04-12 11:57:45', '17759950651775428767359.jpg', 0, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '93.00', '2026-04-14 15:56:05', '0.00'),
(25, NULL, '', 'กล่องไปรษณีย์', '84.00', '70.00', 20, 5, NULL, '2026-04-12 12:05:55', '1775995594th-11134207-7ra0q-mcvpvmu5vn1iaf.jpg', NULL, '0.00', '0.00', '10.00', '25.00', 0, 0, 1, '2026-04-14 15:56:05', '119.00', '2026-04-14 15:56:05', '0.00'),
(26, NULL, '', 'บับเบิ้ลกันกระแทก', '28.00', '18.00', 30, 5, NULL, '2026-04-12 12:09:22', '', NULL, '0.00', '0.00', '10.00', '5.00', 0, 0, 1, '2026-04-14 15:56:05', '88.00', '2026-04-14 15:56:05', '0.00'),
(27, NULL, '922930', 'ลอตเต้ สโนวี่ อัลมอนด์ บิสกิตแท่ง 32 ก.', '23.00', '17.00', 1, 5, NULL, '2026-04-12 12:12:14', '17759959341775430964976.jpg', 11, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '110.00', '2026-04-14 15:56:05', '0.00'),
(28, NULL, '927243', 'เปปเปอโร บิสกิตแท่ง รสสตรอเบอร์รี่ 32 ก.', '23.00', '17.00', 7, 5, NULL, '2026-04-12 12:13:39', '17759960191775431314779.jpg', 11, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '117.00', '2026-04-14 15:56:05', '0.00'),
(29, NULL, '922929', 'เปปเปอโร ไวท์ คุ้กกี้ 32ก.X1', '23.00', '17.00', 6, 5, NULL, '2026-04-12 12:21:19', '17759965361775431560580.jpg', 11, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '52.00', '2026-04-14 15:56:05', '0.00'),
(30, NULL, '160939', 'ครีมโอ พลัส คุกกี้ราดคาราเมลและช็อกโกแลต 13 ก. x 24', '50.00', '37.00', 10, 5, NULL, '2026-04-12 12:27:38', '17759968581775432672833.jpg', 11, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '116.00', '2026-04-14 15:56:05', '0.00'),
(31, NULL, '', 'แท้ Seagull แก้วน้ำสุญญากาศ สมูทตี้ มิกซ์ 1.2 ลิตร มีให้เลือก 4 สี', '399.00', '299.00', 2, 5, NULL, '2026-04-12 12:37:46', '17759974661775637780861.jpg', NULL, '0.00', '0.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '43.00', '2026-04-14 15:56:05', '0.00'),
(32, NULL, '', 'ทีฟาล์ว ชุดเครื่องครัว 3 ชิ้น', '975.00', '699.00', 30, 5, NULL, '2026-04-12 12:55:10', '17759985101775257660344.jpg', 1, '29.00', '3.00', '10.00', '15.00', 0, 1, 1, '2026-04-14 15:56:05', '72.00', '2026-04-14 15:56:05', '0.00'),
(37, NULL, NULL, 'มาม่า บะหมี่กึ่งสําเร็จรูป รสต้มยำกุ้งน้ำข้น 55 ก. x 40', '274.80', '229.00', 6, 5, NULL, '2026-04-17 02:27:00', '17763960826761217949891.jpg', 8, NULL, NULL, '10.00', NULL, 0, 0, 0, NULL, NULL, NULL, '0.00'),
(38, NULL, '', 'เนสกาแฟ เบลนด์ แอนด์ บรู เอสเปรสโซ 3อิน1 15.1 ก. 60 ซอง', '318.00', '235.00', 15, 5, NULL, '2026-04-17 04:51:01', '1776401461173921.jpg', NULL, '0.00', '3.00', '10.00', '15.00', 0, 1, 0, NULL, NULL, NULL, '0.00'),
(39, NULL, '', 'ทิวลี่ ทวิน เวเฟอร์สอดไส้ครีม กลิ่นวนิลาเคลือบช็อกโกแลต 10 ก. x 24', '55.00', '38.00', 10, 5, NULL, '2026-04-19 00:38:43', '', NULL, '0.00', '3.00', '10.00', '15.00', 0, 1, 0, NULL, NULL, NULL, '0.00'),
(41, '158236', NULL, 'แตงโมสกายลัค กก.ละ', '74.00', '55.00', 0, 5, NULL, '2026-04-22 14:04:29', NULL, NULL, '0.00', '0.00', '10.00', '15.00', 0, 1, 0, NULL, NULL, NULL, '0.00'),
(42, '917589', NULL, 'พริกชี้ฟ้าแดง กก.ละ', '80.00', '60.00', 0, 5, NULL, '2026-04-22 14:04:29', NULL, NULL, '0.00', '0.00', '10.00', '15.00', 0, 1, 0, NULL, NULL, NULL, '0.00'),
(43, '917589', NULL, 'พริกชี้ฟ้าแดง กก.ละ', '80.00', '60.00', 0, 5, NULL, '2026-04-22 14:04:29', NULL, NULL, '0.00', '0.00', '10.00', '15.00', 0, 1, 0, NULL, NULL, NULL, '0.00'),
(44, '56862', NULL, 'องุ่นแดงนอกAUS - กก.ละ', '87.00', '65.00', 30, 5, NULL, '2026-04-22 14:04:29', NULL, NULL, '0.00', '0.00', '10.00', '15.00', 0, 1, 0, NULL, NULL, NULL, '0.00');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `stock_before` int(11) DEFAULT NULL,
  `stock_after` int(11) DEFAULT NULL,
  `ref_type` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock_logs`
--

INSERT INTO `stock_logs` (`id`, `product_id`, `type`, `qty`, `note`, `created_at`, `user_id`, `order_id`, `stock_before`, `stock_after`, `ref_type`, `price`, `cost`, `source`, `ref_id`, `reason`) VALUES
(12, 5, 'adjust', 18, 'ปรับสต๊อก', '2026-03-24 14:53:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 20, 'add', 3, 'เพิ่มสินค้า', '2026-04-02 19:31:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 21, 'add', 2, 'เพิ่มสินค้า', '2026-04-03 19:49:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 20, 'adjust', 7, 'ปรับสต๊อก', '2026-04-12 18:47:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 14, 'adjust', -1175, 'ปรับสต๊อก', '2026-04-12 18:48:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 22, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 18:51:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 23, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 18:55:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 24, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 18:57:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 25, 'add', 20, 'เพิ่มสินค้า', '2026-04-12 19:05:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 26, 'add', 2, 'เพิ่มสินค้า', '2026-04-12 19:09:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 27, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 19:12:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 28, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 19:13:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 29, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 19:21:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 30, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 19:27:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 31, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 19:37:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 32, 'add', 10, 'เพิ่มสินค้า', '2026-04-12 19:55:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 32, 'in', 20, 'AI สั่งเพิ่ม', '2026-04-13 21:54:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 21, 'out', 2, NULL, '2026-04-14 08:42:42', NULL, 29, 2, 0, 'sale', NULL, NULL, NULL, NULL, NULL),
(37, 21, 'in', 30, 'AI สั่งเพิ่ม', '2026-04-14 08:43:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 14, 'in', 1100, 'คืนสินค้า', '2026-04-14 08:49:59', NULL, NULL, -14, 1086, 'return', NULL, NULL, NULL, NULL, NULL),
(39, 31, 'adjust', -8, 'ปรับสต๊อก', '2026-04-14 10:01:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 14, 'in', 3, 'คืนสินค้า', '2026-04-14 10:43:33', NULL, NULL, 1086, 1089, 'return', NULL, NULL, 'MANUAL', NULL, NULL),
(41, 14, 'adjust', -1084, 'เสีย', '2026-04-14 10:43:58', NULL, NULL, 1089, 5, 'adjust', NULL, NULL, 'MANUAL', NULL, NULL),
(42, 27, 'adjust', -9, 'เสีย', '2026-04-14 11:34:55', NULL, NULL, 10, 1, 'adjust', NULL, NULL, 'MANUAL', NULL, NULL),
(43, 22, 'out', 1, NULL, '2026-04-14 11:47:31', NULL, 30, 9, 8, 'sale', NULL, NULL, NULL, NULL, NULL),
(44, 22, 'in', 22, 'AI สั่งเพิ่ม', '2026-04-14 11:55:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 32, 'in', 28, 'AI สั่งเพิ่ม', '2026-04-14 11:56:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 32, 'adjust', -28, 'หาไม่เจอ', '2026-04-14 12:04:08', NULL, NULL, 30, 2, 'adjust', NULL, NULL, 'MANUAL', NULL, NULL),
(47, 32, 'in', 28, 'AI สั่งเพิ่ม', '2026-04-14 17:35:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 26, 'in', 28, 'AI สั่งเพิ่ม', '2026-04-16 09:31:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 37, 'out', 1, NULL, '2026-04-17 11:05:35', NULL, 41, 7, 6, 'sale', NULL, NULL, NULL, NULL, NULL),
(61, 44, 'in', 30, 'AI สั่งเพิ่ม', '2026-04-22 21:08:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
-- Indexes for table `price_logs`
--
ALTER TABLE `price_logs`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `price_logs`
--
ALTER TABLE `price_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

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
