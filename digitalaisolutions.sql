-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 21, 2026 at 06:54 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digitalaisolutions`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_1` varchar(20) DEFAULT NULL,
  `phone_2` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `trade_license_no` varchar(60) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `contact_person`, `address`, `phone_1`, `phone_2`, `email`, `trade_license_no`, `note`, `is_active`, `created_at`) VALUES
(1, 'Beximco Pharmaceuticals', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(2, 'Square Pharmaceuticals', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(3, 'Acme Laboratories', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(4, 'Renata Limited', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(5, 'Adidas', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(6, 'Yonex', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(7, 'SS Cricket', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(8, 'Gold\'s Gym', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(9, 'Nivia', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(10, 'Xiaomi', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(11, 'Samsung', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(12, 'Walton', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(13, 'Anker', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(14, 'JBL', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(15, 'Shanta Holdings', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(16, 'Navana Real Estate', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(17, 'Rupayan Housing', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(18, 'Bashundhara Group', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(19, 'BTI Properties', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(20, 'City Group', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(21, 'Pran-RFL Group', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(22, 'ACI Limited', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(23, 'Meghna Group', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(24, 'Square Consumer', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(25, 'Ispahani Tea', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(26, 'Fresh Fruit Supplier', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(27, 'Local Farm Supplier', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(28, 'Dates Importer BD', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(29, 'Corsair', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(30, 'Kingston', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(31, 'SanDisk', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31'),
(32, 'Seagate', NULL, 'Bangladesh', NULL, NULL, NULL, NULL, NULL, 1, '2026-11-06 18:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `company_payments`
--

CREATE TABLE `company_payments` (
  `payment_id` int NOT NULL,
  `company_id` int NOT NULL,
  `purchase_id` int DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` enum('cash','bkash','nagad','bank','cheque','other') NOT NULL DEFAULT 'cash',
  `reference_no` varchar(80) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `company_payments`
--

INSERT INTO `company_payments` (`payment_id`, `company_id`, `purchase_id`, `payment_date`, `amount`, `method`, `reference_no`, `note`, `created_at`) VALUES
(1, 1, 1, '2026-11-01', 18547.20, 'bank', 'TXN-1001', 'Payment for INV-2026-001', '2026-11-06 18:16:31'),
(2, 2, 2, '2026-11-01', 98616.00, 'bank', 'TXN-1002', 'Payment for INV-2026-002', '2026-11-06 18:16:31'),
(3, 3, 3, '2026-11-01', 86400.00, 'bank', 'TXN-1003', 'Payment for INV-2026-003', '2026-11-06 18:16:31'),
(4, 4, 4, '2026-11-01', 35280.00, 'bank', 'TXN-1004', 'Payment for INV-2026-004', '2026-11-06 18:16:31'),
(5, 5, 5, '2026-11-01', 49674.24, 'bank', 'TXN-1005', 'Payment for INV-2026-005', '2026-11-06 18:16:31'),
(6, 6, 6, '2026-11-01', 99014.40, 'bank', 'TXN-1006', 'Payment for INV-2026-006', '2026-11-06 18:16:31'),
(7, 7, 7, '2026-11-01', 31933.44, 'bank', 'TXN-1007', 'Payment for INV-2026-007', '2026-11-06 18:16:31'),
(8, 8, 8, '2026-11-01', 26611.20, 'bank', 'TXN-1008', 'Payment for INV-2026-008', '2026-11-06 18:16:31'),
(9, 9, 9, '2026-11-01', 21420.00, 'bank', 'TXN-1009', 'Payment for INV-2026-009', '2026-11-06 18:16:31'),
(10, 10, 10, '2026-11-01', 289062.34, 'bank', 'TXN-1010', 'Payment for INV-2026-010', '2026-11-06 18:16:31'),
(11, 11, 11, '2026-11-01', 321250.37, 'bank', 'TXN-1011', 'Payment for INV-2026-011', '2026-11-06 18:16:31'),
(12, 12, 12, '2026-11-01', 155961.00, 'bank', 'TXN-1012', 'Payment for INV-2026-012', '2026-11-06 18:16:31'),
(13, 13, 13, '2026-11-01', 86688.00, 'bank', 'TXN-1013', 'Payment for INV-2026-013', '2026-11-06 18:16:31'),
(14, 14, 14, '2026-11-01', 73143.00, 'bank', 'TXN-1014', 'Payment for INV-2026-014', '2026-11-06 18:16:31'),
(15, 15, 15, '2026-11-01', 22032000.00, 'bank', 'TXN-1015', 'Payment for INV-2026-015', '2026-11-06 18:16:31'),
(16, 16, 16, '2026-11-01', 13305600.00, 'bank', 'TXN-1016', 'Payment for INV-2026-016', '2026-11-06 18:16:31'),
(17, 17, 17, '2026-11-01', 11793600.00, 'bank', 'TXN-1017', 'Payment for INV-2026-017', '2026-11-06 18:16:31'),
(18, 18, 18, '2026-11-01', 33174000.00, 'bank', 'TXN-1018', 'Payment for INV-2026-018', '2026-11-06 18:16:31'),
(19, 19, 19, '2026-11-01', 14666400.00, 'bank', 'TXN-1019', 'Payment for INV-2026-019', '2026-11-06 18:16:31'),
(20, 20, 20, '2026-11-01', 67132.80, 'bank', 'TXN-1020', 'Payment for INV-2026-020', '2026-11-06 18:16:31'),
(21, 21, 21, '2026-11-01', 76032.00, 'bank', 'TXN-1021', 'Payment for INV-2026-021', '2026-11-06 18:16:31'),
(22, 22, 22, '2026-11-01', 15561.00, 'bank', 'TXN-1022', 'Payment for INV-2026-022', '2026-11-06 18:16:31'),
(23, 23, 23, '2026-11-01', 22916.25, 'bank', 'TXN-1023', 'Payment for INV-2026-023', '2026-11-06 18:16:31'),
(24, 24, 24, '2026-11-01', 12690.00, 'bank', 'TXN-1024', 'Payment for INV-2026-024', '2026-11-06 18:16:31'),
(25, 25, 25, '2026-11-01', 18553.50, 'bank', 'TXN-1025', 'Payment for INV-2026-025', '2026-11-06 18:16:31'),
(26, 26, 26, '2026-11-01', 53343.36, 'bank', 'TXN-1026', 'Payment for INV-2026-026', '2026-11-06 18:16:31'),
(27, 27, 27, '2026-11-01', 11260.80, 'bank', 'TXN-1027', 'Payment for INV-2026-027', '2026-11-06 18:16:31'),
(28, 28, 28, '2026-11-01', 20412.00, 'bank', 'TXN-1028', 'Payment for INV-2026-028', '2026-11-06 18:16:31'),
(29, 29, 29, '2026-11-01', 71037.12, 'bank', 'TXN-1029', 'Payment for INV-2026-029', '2026-11-06 18:16:31'),
(30, 30, 30, '2026-11-01', 275906.40, 'bank', 'TXN-1030', 'Payment for INV-2026-030', '2026-11-06 18:16:31'),
(31, 31, 31, '2026-11-01', 78601.60, 'bank', 'TXN-1031', 'Payment for INV-2026-031', '2026-11-06 18:16:31'),
(32, 32, 32, '2026-11-01', 73673.60, 'bank', 'TXN-1032', 'Payment for INV-2026-032', '2026-11-06 18:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `address`, `phone`, `created_at`) VALUES
(1, 'Karim Hossain', 'Mirpur, Dhaka', '01811111111', '2026-11-06 18:16:31'),
(2, 'Nasrin Akter', 'Uttara, Dhaka', '01822222222', '2026-11-06 18:16:31'),
(3, 'Tanvir Ahmed', 'Dhanmondi, Dhaka', '01833333333', '2026-11-06 18:16:31'),
(4, 'Sumi Begum', 'Mohammadpur, Dhaka', '01844444444', '2026-11-06 18:16:31'),
(5, 'dfghj', NULL, '01759865221', '2026-11-07 03:39:06'),
(6, 'Masud', NULL, '01746973138', '2026-11-07 04:20:45');

-- --------------------------------------------------------

--
-- Table structure for table `customer_ledger`
--

CREATE TABLE `customer_ledger` (
  `ledger_id` int NOT NULL,
  `shop_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `memo_id` int DEFAULT NULL,
  `entry_date` date NOT NULL,
  `entry_type` enum('due','payment') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` enum('cash','bkash','nagad','bank','other') DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer_ledger`
--

INSERT INTO `customer_ledger` (`ledger_id`, `shop_id`, `customer_id`, `memo_id`, `entry_date`, `entry_type`, `amount`, `method`, `note`, `created_at`) VALUES
(1, 1, 3, 5, '2026-11-05', 'due', 457.20, NULL, 'Due from memo M-2026-0005', '2026-11-06 18:16:31'),
(2, 1, 4, 15, '2026-11-02', 'due', 7231.50, NULL, 'Due from memo M-2026-0015', '2026-11-06 18:16:31'),
(3, 1, 2, 30, '2026-11-04', 'due', 446.40, NULL, 'Due from memo M-2026-0030', '2026-11-06 18:16:31'),
(5, 1, 5, NULL, '2026-11-07', 'payment', 2000.00, 'cash', '1000-2', '2026-11-07 03:43:21');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `company_id` int DEFAULT NULL,
  `unit` varchar(30) DEFAULT 'pcs',
  `default_price` decimal(10,2) DEFAULT '0.00',
  `default_discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `reorder_level` int NOT NULL DEFAULT '10',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `category_id`, `name`, `company_id`, `unit`, `default_price`, `default_discount_percent`, `reorder_level`, `created_at`) VALUES
(1, 1, 'Napa 500mg (10 tablets)', 1, 'strip', 12.00, 0.00, 300, '2026-11-06 18:16:31'),
(2, 1, 'Seclo 20mg (10 capsules)', 2, 'strip', 70.00, 5.00, 150, '2026-11-06 18:16:31'),
(3, 1, 'Fexo 120mg (10 tablets)', 2, 'strip', 80.00, 5.00, 100, '2026-11-06 18:16:31'),
(4, 1, 'Monas 10mg (10 tablets)', 3, 'strip', 150.00, 5.00, 80, '2026-11-06 18:16:31'),
(5, 1, 'Orsaline-N Oral Saline (sachet)', 2, 'sachet', 6.00, 0.00, 500, '2026-11-06 18:16:31'),
(6, 1, 'Maxpro 20mg (10 capsules)', 4, 'strip', 70.00, 5.00, 100, '2026-11-06 18:16:31'),
(7, 2, 'Adidas Tiro Football Size 5', 5, 'pcs', 2800.00, 10.00, 5, '2026-11-06 18:16:31'),
(8, 2, 'Yonex Zr 100 Badminton Racket', 6, 'pcs', 1800.00, 12.00, 5, '2026-11-06 18:16:31'),
(9, 2, 'Yonex Mavis 350 Shuttlecock (tube of 6)', 6, 'tube', 1250.00, 8.00, 10, '2026-11-06 18:16:31'),
(10, 2, 'SS Ton Kashmir Willow Cricket Bat', 7, 'pcs', 4800.00, 10.00, 3, '2026-11-06 18:16:31'),
(11, 2, 'Gold\'s Gym Dumbbell Set 10kg', 8, 'set', 2400.00, 10.00, 5, '2026-11-06 18:16:31'),
(12, 2, 'Nivia Skipping Rope', 9, 'pcs', 350.00, 15.00, 15, '2026-11-06 18:16:31'),
(13, 3, 'Xiaomi Redmi 13C (4GB/128GB)', 10, 'pcs', 15500.00, 5.00, 5, '2026-11-06 18:16:31'),
(14, 3, 'Samsung Galaxy A16 (6GB/128GB)', 11, 'pcs', 24500.00, 4.00, 3, '2026-11-06 18:16:31'),
(15, 3, 'Walton 32 inch Smart LED TV', 12, 'pcs', 19500.00, 6.00, 2, '2026-11-06 18:16:31'),
(16, 3, 'Anker PowerCore 10000 Power Bank', 13, 'pcs', 3200.00, 8.00, 8, '2026-11-06 18:16:31'),
(17, 3, 'JBL Tune 510BT Wireless Headphone', 14, 'pcs', 4500.00, 10.00, 5, '2026-11-06 18:16:31'),
(18, 3, 'Xiaomi Smart Band 9', 10, 'pcs', 4200.00, 8.00, 5, '2026-11-06 18:16:31'),
(19, 4, 'Ready Flat 1100 sqft, 3 Bed - Uttara', 15, 'flat', 8500000.00, 3.00, 1, '2026-11-06 18:16:31'),
(20, 4, 'Ready Flat 1200 sqft, 3 Bed - Bashundhara R/A', 16, 'flat', 11000000.00, 3.00, 1, '2026-11-06 18:16:31'),
(21, 4, 'Ready Flat 950 sqft, 2 Bed - Mirpur', 17, 'flat', 6500000.00, 3.00, 1, '2026-11-06 18:16:31'),
(22, 4, 'Residential Plot 3 Katha - Purbachal', 18, 'plot', 9500000.00, 2.00, 1, '2026-11-06 18:16:31'),
(23, 4, 'Commercial Space 400 sqft - Motijheel', 19, 'space', 12000000.00, 2.00, 1, '2026-11-06 18:16:31'),
(24, 5, 'Teer Soyabean Oil 5L', 20, 'bottle', 925.00, 3.00, 20, '2026-11-06 18:16:31'),
(25, 5, 'Miniket Rice Premium 5kg', 21, 'pack', 440.00, 3.00, 30, '2026-11-06 18:16:31'),
(26, 5, 'ACI Pure Atta 2kg', 22, 'pack', 130.00, 4.00, 30, '2026-11-06 18:16:31'),
(27, 5, 'Fresh Sugar 1kg', 23, 'pack', 125.00, 2.00, 40, '2026-11-06 18:16:31'),
(28, 5, 'Radhuni Turmeric Powder 200g', 24, 'pack', 100.00, 5.00, 20, '2026-11-06 18:16:31'),
(29, 5, 'Ispahani Mirzapore Best Leaf Tea 400g', 25, 'pack', 310.00, 4.00, 15, '2026-11-06 18:16:31'),
(30, 6, 'Apple Fuji (Imported) 1kg', 26, 'kg', 320.00, 5.00, 20, '2026-11-06 18:16:31'),
(31, 6, 'Malta Orange 1kg', 26, 'kg', 280.00, 5.00, 20, '2026-11-06 18:16:31'),
(32, 6, 'Banana Sagor (1 dozen)', 27, 'dozen', 85.00, 0.00, 30, '2026-11-06 18:16:31'),
(33, 6, 'Pomegranate (Dalim) 1kg', 26, 'kg', 450.00, 5.00, 15, '2026-11-06 18:16:31'),
(34, 6, 'Mariam Dates 500g', 28, 'pack', 450.00, 6.00, 15, '2026-11-06 18:16:31'),
(35, 7, 'Corsair Vengeance LPX 8GB DDR4 3200MHz RAM', 29, 'pcs', 3100.00, 5.00, 8, '2026-11-06 18:16:31'),
(36, 7, 'Kingston Fury Beast 16GB DDR4 3200MHz RAM', 30, 'pcs', 5500.00, 5.00, 5, '2026-11-06 18:16:31'),
(37, 7, 'Kingston NV2 500GB NVMe SSD', 30, 'pcs', 4350.00, 6.00, 8, '2026-11-06 18:16:31'),
(38, 7, 'Samsung 980 1TB NVMe SSD', 11, 'pcs', 9800.00, 6.00, 4, '2026-11-06 18:16:31'),
(39, 7, 'SanDisk Cruzer Blade 32GB Pen Drive', 31, 'pcs', 650.00, 10.00, 20, '2026-11-06 18:16:31'),
(40, 7, 'SanDisk Ultra Flair 64GB USB 3.0 Pen Drive', 31, 'pcs', 1000.00, 10.00, 15, '2026-11-06 18:16:31'),
(41, 7, 'Seagate Expansion 1TB Portable HDD', 32, 'pcs', 5200.00, 6.00, 5, '2026-11-06 18:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `memos`
--

CREATE TABLE `memos` (
  `memo_id` int NOT NULL,
  `memo_no` varchar(20) NOT NULL,
  `shop_id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_name` varchar(150) DEFAULT NULL,
  `customer_address` varchar(255) DEFAULT NULL,
  `memo_date` date NOT NULL,
  `total_amount` decimal(10,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount_in_words` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `delivery_staff_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `memos`
--

INSERT INTO `memos` (`memo_id`, `memo_no`, `shop_id`, `staff_id`, `customer_id`, `customer_name`, `customer_address`, `memo_date`, `total_amount`, `discount_amount`, `paid_amount`, `due_amount`, `amount_in_words`, `created_at`, `delivery_staff_id`) VALUES
(1, 'M-2026-0001', 1, 1, 3, 'Tanvir Ahmed', 'Dhanmondi, Dhaka', '2026-11-03', 2100.00, 155.00, 1945.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(2, 'M-2026-0002', 1, 1, 1, 'Karim Hossain', 'Mirpur, Dhaka', '2026-11-02', 5400.00, 346.00, 5054.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(3, 'M-2026-0003', 1, 1, 2, 'Nasrin Akter', 'Uttara, Dhaka', '2026-11-06', 1280.00, 64.00, 1216.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(4, 'M-2026-0004', 1, 1, 1, 'Karim Hossain', 'Mirpur, Dhaka', '2026-11-03', 640.00, 32.00, 608.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(5, 'M-2026-0005', 1, 1, 3, 'Tanvir Ahmed', 'Dhanmondi, Dhaka', '2026-11-05', 1586.00, 62.00, 1066.80, 457.20, NULL, '2026-11-06 18:16:31', NULL),
(6, 'M-2026-0006', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-03', 640.00, 32.00, 608.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(7, 'M-2026-0007', 1, 1, 3, 'Tanvir Ahmed', 'Dhanmondi, Dhaka', '2026-11-02', 320.00, 16.00, 304.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(8, 'M-2026-0008', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-03', 10940.00, 555.60, 10384.40, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(9, 'M-2026-0009', 1, 1, 3, 'Tanvir Ahmed', 'Dhanmondi, Dhaka', '2026-11-04', 3450.00, 345.00, 3105.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(10, 'M-2026-0010', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-04', 60050.00, 2737.00, 57313.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(11, 'M-2026-0011', 1, 1, 2, 'Nasrin Akter', 'Uttara, Dhaka', '2026-11-04', 565.00, 15.70, 549.30, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(12, 'M-2026-0012', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-06', 19400.00, 1546.00, 17854.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(13, 'M-2026-0013', 1, 1, 1, 'Karim Hossain', 'Mirpur, Dhaka', '2026-11-04', 8420.00, 439.00, 7981.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(14, 'M-2026-0014', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-05', 4898.00, 362.00, 4536.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(15, 'M-2026-0015', 1, 1, 4, 'Sumi Begum', 'Mohammadpur, Dhaka', '2026-11-02', 25150.00, 1045.00, 16873.50, 7231.50, NULL, '2026-11-06 18:16:31', NULL),
(16, 'M-2026-0016', 1, 1, 4, 'Sumi Begum', 'Mohammadpur, Dhaka', '2026-11-05', 5800.00, 198.40, 5601.60, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(17, 'M-2026-0017', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-04', 13950.00, 1026.50, 12923.50, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(18, 'M-2026-0018', 1, 1, 2, 'Nasrin Akter', 'Uttara, Dhaka', '2026-11-03', 625.00, 12.50, 612.50, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(19, 'M-2026-0019', 1, 1, 2, 'Nasrin Akter', 'Uttara, Dhaka', '2026-11-04', 2362.00, 66.00, 2296.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(20, 'M-2026-0020', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-04', 3500.00, 175.00, 3325.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(21, 'M-2026-0021', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-06', 4900.00, 171.00, 4729.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(22, 'M-2026-0022', 1, 1, 1, 'Karim Hossain', 'Mirpur, Dhaka', '2026-11-03', 2500.00, 200.00, 2300.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(23, 'M-2026-0023', 1, 1, 2, 'Nasrin Akter', 'Uttara, Dhaka', '2026-11-02', 2060.00, 103.00, 1957.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(24, 'M-2026-0024', 1, 1, 1, 'Karim Hossain', 'Mirpur, Dhaka', '2026-11-04', 400.00, 20.00, 380.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(25, 'M-2026-0025', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-05', 955.00, 105.00, 850.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(26, 'M-2026-0026', 1, 1, 4, 'Sumi Begum', 'Mohammadpur, Dhaka', '2026-11-02', 10400.00, 624.00, 9776.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(27, 'M-2026-0027', 1, 1, 4, 'Sumi Begum', 'Mohammadpur, Dhaka', '2026-11-05', 7534.00, 566.00, 6968.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(28, 'M-2026-0028', 1, 1, NULL, 'Walk-in Customer', NULL, '2026-11-02', 200.00, 10.00, 190.00, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(29, 'M-2026-0029', 1, 1, 1, 'Karim Hossain', 'Mirpur, Dhaka', '2026-11-06', 8350.00, 417.50, 7932.50, 0.00, NULL, '2026-11-06 18:16:31', NULL),
(30, 'M-2026-0030', 1, 1, 2, 'Nasrin Akter', 'Uttara, Dhaka', '2026-11-04', 1550.00, 62.00, 1041.60, 446.40, NULL, '2026-11-06 18:16:31', NULL),
(34, '3', 2, NULL, 6, 'Masud', '01746973138', '2026-11-07', 3200.00, 256.00, 2944.00, 0.00, 'Two Thousand Nine Hundred Forty Four Taka Only', '2026-11-07 04:31:56', 2);

-- --------------------------------------------------------

--
-- Table structure for table `memo_items`
--

CREATE TABLE `memo_items` (
  `item_id` int NOT NULL,
  `memo_id` int NOT NULL,
  `medicine_id` int DEFAULT NULL,
  `batch_id` int DEFAULT NULL,
  `medicine_name` varchar(150) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(10,2) GENERATED ALWAYS AS ((`quantity` * `unit_price`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `memo_items`
--

INSERT INTO `memo_items` (`item_id`, `memo_id`, `medicine_id`, `batch_id`, `medicine_name`, `quantity`, `unit_price`, `discount_percent`) VALUES
(1, 1, 33, 33, 'Pomegranate (Dalim) 1kg', 1.00, 450.00, 5.00),
(2, 1, 2, 2, 'Seclo 20mg (10 capsules)', 7.00, 70.00, 5.00),
(3, 1, 3, 3, 'Fexo 120mg (10 tablets)', 2.00, 80.00, 5.00),
(4, 1, 40, 40, 'SanDisk Ultra Flair 64GB USB 3.0 Pen Drive', 1.00, 1000.00, 10.00),
(5, 2, 25, 25, 'Miniket Rice Premium 5kg', 5.00, 440.00, 3.00),
(6, 2, 2, 2, 'Seclo 20mg (10 capsules)', 8.00, 70.00, 5.00),
(7, 2, 11, 11, 'Gold\'s Gym Dumbbell Set 10kg', 1.00, 2400.00, 10.00),
(8, 2, 3, 3, 'Fexo 120mg (10 tablets)', 3.00, 80.00, 5.00),
(9, 3, 30, 30, 'Apple Fuji (Imported) 1kg', 4.00, 320.00, 5.00),
(10, 4, 30, 30, 'Apple Fuji (Imported) 1kg', 2.00, 320.00, 5.00),
(11, 5, 29, 29, 'Ispahani Mirzapore Best Leaf Tea 400g', 5.00, 310.00, 4.00),
(12, 5, 5, 5, 'Orsaline-N Oral Saline (sachet)', 6.00, 6.00, 0.00),
(13, 6, 30, 30, 'Apple Fuji (Imported) 1kg', 2.00, 320.00, 5.00),
(14, 7, 30, 30, 'Apple Fuji (Imported) 1kg', 1.00, 320.00, 5.00),
(15, 8, 35, 35, 'Corsair Vengeance LPX 8GB DDR4 3200MHz RAM', 2.00, 3100.00, 5.00),
(16, 8, 29, 29, 'Ispahani Mirzapore Best Leaf Tea 400g', 4.00, 310.00, 4.00),
(17, 8, 25, 25, 'Miniket Rice Premium 5kg', 5.00, 440.00, 3.00),
(18, 8, 39, 39, 'SanDisk Cruzer Blade 32GB Pen Drive', 2.00, 650.00, 10.00),
(19, 9, 39, 39, 'SanDisk Cruzer Blade 32GB Pen Drive', 1.00, 650.00, 10.00),
(20, 9, 7, 7, 'Adidas Tiro Football Size 5', 1.00, 2800.00, 10.00),
(21, 10, 17, 17, 'JBL Tune 510BT Wireless Headphone', 1.00, 4500.00, 10.00),
(22, 10, 37, 37, 'Kingston NV2 500GB NVMe SSD', 1.00, 4350.00, 6.00),
(23, 10, 25, 25, 'Miniket Rice Premium 5kg', 5.00, 440.00, 3.00),
(24, 10, 14, 14, 'Samsung Galaxy A16 (6GB/128GB)', 2.00, 24500.00, 4.00),
(25, 11, 27, 27, 'Fresh Sugar 1kg', 1.00, 125.00, 2.00),
(26, 11, 25, 25, 'Miniket Rice Premium 5kg', 1.00, 440.00, 3.00),
(27, 12, 17, 17, 'JBL Tune 510BT Wireless Headphone', 2.00, 4500.00, 10.00),
(28, 12, 35, 35, 'Corsair Vengeance LPX 8GB DDR4 3200MHz RAM', 2.00, 3100.00, 5.00),
(29, 12, 18, 18, 'Xiaomi Smart Band 9', 1.00, 4200.00, 8.00),
(30, 13, 35, 35, 'Corsair Vengeance LPX 8GB DDR4 3200MHz RAM', 2.00, 3100.00, 5.00),
(31, 13, 34, 34, 'Mariam Dates 500g', 4.00, 450.00, 6.00),
(32, 13, 2, 2, 'Seclo 20mg (10 capsules)', 6.00, 70.00, 5.00),
(33, 14, 1, 1, 'Napa 500mg (10 tablets)', 4.00, 12.00, 0.00),
(34, 14, 26, 26, 'ACI Pure Atta 2kg', 5.00, 130.00, 4.00),
(35, 14, 18, 18, 'Xiaomi Smart Band 9', 1.00, 4200.00, 8.00),
(36, 15, 39, 39, 'SanDisk Cruzer Blade 32GB Pen Drive', 1.00, 650.00, 10.00),
(37, 15, 14, 14, 'Samsung Galaxy A16 (6GB/128GB)', 1.00, 24500.00, 4.00),
(38, 16, 3, 3, 'Fexo 120mg (10 tablets)', 10.00, 80.00, 5.00),
(39, 16, 6, 6, 'Maxpro 20mg (10 capsules)', 6.00, 70.00, 5.00),
(40, 16, 25, 25, 'Miniket Rice Premium 5kg', 2.00, 440.00, 3.00),
(41, 16, 24, 24, 'Teer Soyabean Oil 5L', 4.00, 925.00, 3.00),
(42, 17, 18, 18, 'Xiaomi Smart Band 9', 1.00, 4200.00, 8.00),
(43, 17, 35, 35, 'Corsair Vengeance LPX 8GB DDR4 3200MHz RAM', 1.00, 3100.00, 5.00),
(44, 17, 24, 24, 'Teer Soyabean Oil 5L', 2.00, 925.00, 3.00),
(45, 17, 10, 10, 'SS Ton Kashmir Willow Cricket Bat', 1.00, 4800.00, 10.00),
(46, 18, 27, 27, 'Fresh Sugar 1kg', 5.00, 125.00, 2.00),
(47, 19, 1, 1, 'Napa 500mg (10 tablets)', 10.00, 12.00, 0.00),
(48, 19, 5, 5, 'Orsaline-N Oral Saline (sachet)', 7.00, 6.00, 0.00),
(49, 19, 25, 25, 'Miniket Rice Premium 5kg', 5.00, 440.00, 3.00),
(50, 20, 35, 35, 'Corsair Vengeance LPX 8GB DDR4 3200MHz RAM', 1.00, 3100.00, 5.00),
(51, 20, 28, 28, 'Radhuni Turmeric Powder 200g', 4.00, 100.00, 5.00),
(52, 21, 24, 24, 'Teer Soyabean Oil 5L', 4.00, 925.00, 3.00),
(53, 21, 4, 4, 'Monas 10mg (10 tablets)', 8.00, 150.00, 5.00),
(54, 22, 9, 9, 'Yonex Mavis 350 Shuttlecock (tube of 6)', 2.00, 1250.00, 8.00),
(55, 23, 31, 31, 'Malta Orange 1kg', 1.00, 280.00, 5.00),
(56, 23, 2, 2, 'Seclo 20mg (10 capsules)', 4.00, 70.00, 5.00),
(57, 23, 4, 4, 'Monas 10mg (10 tablets)', 10.00, 150.00, 5.00),
(58, 24, 3, 3, 'Fexo 120mg (10 tablets)', 5.00, 80.00, 5.00),
(59, 25, 32, 32, 'Banana Sagor (1 dozen)', 3.00, 85.00, 0.00),
(60, 25, 12, 12, 'Nivia Skipping Rope', 2.00, 350.00, 15.00),
(61, 26, 41, 41, 'Seagate Expansion 1TB Portable HDD', 2.00, 5200.00, 6.00),
(62, 27, 16, 16, 'Anker PowerCore 10000 Power Bank', 2.00, 3200.00, 8.00),
(63, 27, 3, 3, 'Fexo 120mg (10 tablets)', 6.00, 80.00, 5.00),
(64, 27, 5, 5, 'Orsaline-N Oral Saline (sachet)', 9.00, 6.00, 0.00),
(65, 27, 4, 4, 'Monas 10mg (10 tablets)', 4.00, 150.00, 5.00),
(66, 28, 28, 28, 'Radhuni Turmeric Powder 200g', 2.00, 100.00, 5.00),
(67, 29, 33, 33, 'Pomegranate (Dalim) 1kg', 3.00, 450.00, 5.00),
(68, 29, 3, 3, 'Fexo 120mg (10 tablets)', 10.00, 80.00, 5.00),
(69, 29, 35, 35, 'Corsair Vengeance LPX 8GB DDR4 3200MHz RAM', 2.00, 3100.00, 5.00),
(70, 30, 29, 29, 'Ispahani Mirzapore Best Leaf Tea 400g', 5.00, 310.00, 4.00),
(74, 34, 16, 16, 'Anker PowerCore 10000 Power Bank', 1.00, 3200.00, 8.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_requests`
--

CREATE TABLE `order_requests` (
  `order_request_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(120) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `items` json NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_staff_id` bigint UNSIGNED DEFAULT NULL,
  `memo_id` bigint UNSIGNED DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_requests`
--

INSERT INTO `order_requests` (`order_request_id`, `customer_name`, `phone`, `items`, `total_amount`, `status`, `shop_id`, `delivery_staff_id`, `memo_id`, `note`, `created_at`) VALUES
(1, 'dfghj', '01759865221', '[{\"qty\": 3, \"name\": \"Anker PowerCore 10000 Power Bank\", \"unit\": \"pcs\", \"price\": 2944, \"line_total\": 8832, \"medicine_id\": 16}]', 8832.00, 'confirmed', 1, 1, 31, NULL, '2026-11-07 03:36:49'),
(2, 'Masud', '01746973138', '[{\"qty\": 1, \"name\": \"Anker PowerCore 10000 Power Bank\", \"unit\": \"pcs\", \"price\": 2944, \"line_total\": 2944, \"medicine_id\": 16}]', 2944.00, 'confirmed', 2, 2, 34, NULL, '2026-11-07 04:18:29');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) DEFAULT NULL,
  `description` text,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Medicine', 'medicine', 'Tablets, capsules, syrups and ORS', 1, '2026-11-06 18:16:31', '2026-11-06 18:16:31'),
(2, 'Sports', 'sports', 'Sports goods and fitness items', 1, '2026-11-06 18:16:31', '2026-11-06 18:16:31'),
(3, 'Electronics', 'electronics', 'Phones, TV, accessories', 1, '2026-11-06 18:16:31', '2026-11-06 18:16:31'),
(4, 'Real Estate', 'real-estate', 'Flats, plots and commercial space', 1, '2026-11-06 18:16:31', '2026-11-06 18:16:31'),
(5, 'Grocery', 'grocery', 'Daily grocery items', 1, '2026-11-06 18:16:31', '2026-11-06 18:16:31'),
(6, 'Fruits', 'fruits', 'Fresh and dried fruits', 1, '2026-11-06 18:16:31', '2026-11-06 18:16:31'),
(7, 'Computers', 'computers', 'RAM, SSD, pen drive, storage', 1, '2026-11-06 18:16:31', '2026-11-06 18:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int NOT NULL,
  `invoice_no` varchar(40) NOT NULL,
  `shop_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(12,2) GENERATED ALWAYS AS (((`total_amount` - `discount_amount`) - `paid_amount`)) STORED,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`purchase_id`, `invoice_no`, `shop_id`, `company_id`, `purchase_date`, `due_date`, `total_amount`, `discount_amount`, `paid_amount`, `note`, `created_at`) VALUES
(1, 'INV-2026-001', 1, 1, '2026-11-01', '2026-12-01', 28800.00, 2304.00, 18547.20, 'Opening stock', '2026-11-06 18:16:31'),
(2, 'INV-2026-002', 1, 2, '2026-11-01', '2026-12-01', 155200.00, 14320.00, 98616.00, 'Opening stock', '2026-11-06 18:16:31'),
(3, 'INV-2026-003', 1, 3, '2026-11-01', '2026-12-01', 96000.00, 9600.00, 86400.00, 'Opening stock', '2026-11-06 18:16:31'),
(4, 'INV-2026-004', 1, 4, '2026-11-01', '2026-12-01', 56000.00, 5600.00, 35280.00, 'Opening stock', '2026-11-06 18:16:31'),
(5, 'INV-2026-005', 1, 5, '2026-11-01', '2026-12-01', 80640.00, 9676.80, 49674.24, 'Opening stock', '2026-11-06 18:16:31'),
(6, 'INV-2026-006', 1, 6, '2026-11-01', '2026-12-01', 110880.00, 11865.60, 99014.40, 'Opening stock', '2026-11-06 18:16:31'),
(7, 'INV-2026-007', 1, 7, '2026-11-01', '2026-12-01', 51840.00, 6220.80, 31933.44, 'Opening stock', '2026-11-06 18:16:31'),
(8, 'INV-2026-008', 1, 8, '2026-11-01', '2026-12-01', 43200.00, 5184.00, 26611.20, 'Opening stock', '2026-11-06 18:16:31'),
(9, 'INV-2026-009', 1, 9, '2026-11-01', '2026-12-01', 25200.00, 3780.00, 21420.00, 'Opening stock', '2026-11-06 18:16:31'),
(10, 'INV-2026-010', 1, 10, '2026-11-01', '2026-12-01', 441610.00, 28663.80, 289062.34, 'Opening stock', '2026-11-06 18:16:31'),
(11, 'INV-2026-011', 1, 11, '2026-11-01', '2026-12-01', 488530.00, 29600.90, 321250.37, 'Opening stock', '2026-11-06 18:16:31'),
(12, 'INV-2026-012', 1, 12, '2026-11-01', '2026-12-01', 167700.00, 11739.00, 155961.00, 'Opening stock', '2026-11-06 18:16:31'),
(13, 'INV-2026-013', 1, 13, '2026-11-01', '2026-12-01', 137600.00, 13760.00, 86688.00, 'Opening stock', '2026-11-06 18:16:31'),
(14, 'INV-2026-014', 1, 14, '2026-11-01', '2026-12-01', 116100.00, 11610.00, 73143.00, 'Opening stock', '2026-11-06 18:16:31'),
(15, 'INV-2026-015', 1, 15, '2026-11-01', '2026-12-01', 22950000.00, 918000.00, 22032000.00, 'Opening stock', '2026-11-06 18:16:31'),
(16, 'INV-2026-016', 1, 16, '2026-11-01', '2026-12-01', 19800000.00, 792000.00, 13305600.00, 'Opening stock', '2026-11-06 18:16:31'),
(17, 'INV-2026-017', 1, 17, '2026-11-01', '2026-12-01', 17550000.00, 702000.00, 11793600.00, 'Opening stock', '2026-11-06 18:16:31'),
(18, 'INV-2026-018', 1, 18, '2026-11-01', '2026-12-01', 34200000.00, 1026000.00, 33174000.00, 'Opening stock', '2026-11-06 18:16:31'),
(19, 'INV-2026-019', 1, 19, '2026-11-01', '2026-12-01', 21600000.00, 648000.00, 14666400.00, 'Opening stock', '2026-11-06 18:16:31'),
(20, 'INV-2026-020', 1, 20, '2026-11-01', '2026-12-01', 99900.00, 3996.00, 67132.80, 'Opening stock', '2026-11-06 18:16:31'),
(21, 'INV-2026-021', 1, 21, '2026-11-01', '2026-12-01', 79200.00, 3168.00, 76032.00, 'Opening stock', '2026-11-06 18:16:31'),
(22, 'INV-2026-022', 1, 22, '2026-11-01', '2026-12-01', 23400.00, 1170.00, 15561.00, 'Opening stock', '2026-11-06 18:16:31'),
(23, 'INV-2026-023', 1, 23, '2026-11-01', '2026-12-01', 33750.00, 1012.50, 22916.25, 'Opening stock', '2026-11-06 18:16:31'),
(24, 'INV-2026-024', 1, 24, '2026-11-01', '2026-12-01', 13500.00, 810.00, 12690.00, 'Opening stock', '2026-11-06 18:16:31'),
(25, 'INV-2026-025', 1, 25, '2026-11-01', '2026-12-01', 27900.00, 1395.00, 18553.50, 'Opening stock', '2026-11-06 18:16:31'),
(26, 'INV-2026-026', 1, 26, '2026-11-01', '2026-12-01', 84672.00, 8467.20, 53343.36, 'Opening stock', '2026-11-06 18:16:31'),
(27, 'INV-2026-027', 1, 27, '2026-11-01', '2026-12-01', 12240.00, 979.20, 11260.80, 'Opening stock', '2026-11-06 18:16:31'),
(28, 'INV-2026-028', 1, 28, '2026-11-01', '2026-12-01', 32400.00, 3240.00, 20412.00, 'Opening stock', '2026-11-06 18:16:31'),
(29, 'INV-2026-029', 1, 29, '2026-11-01', '2026-12-01', 109120.00, 7638.40, 71037.12, 'Opening stock', '2026-11-06 18:16:31'),
(30, 'INV-2026-030', 1, 30, '2026-11-01', '2026-12-01', 298320.00, 22413.60, 275906.40, 'Opening stock', '2026-11-06 18:16:31'),
(31, 'INV-2026-031', 1, 31, '2026-11-01', '2026-12-01', 127600.00, 15312.00, 78601.60, 'Opening stock', '2026-11-06 18:16:31'),
(32, 'INV-2026-032', 1, 32, '2026-11-01', '2026-12-01', 114400.00, 9152.00, 73673.60, 'Opening stock', '2026-11-06 18:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `batch_id` int NOT NULL,
  `purchase_id` int NOT NULL,
  `medicine_id` int NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `mfg_date` date DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `quantity_remaining` decimal(10,2) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(12,2) GENERATED ALWAYS AS (((`quantity` * `purchase_price`) - `discount_amount`)) STORED,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`batch_id`, `purchase_id`, `medicine_id`, `batch_no`, `mfg_date`, `expiry_date`, `quantity`, `quantity_remaining`, `purchase_price`, `mrp`, `discount_percent`, `discount_amount`, `created_at`) VALUES
(1, 1, 1, 'B001-MED', '2026-02-11', '2031-09-17', 3000.00, 2986.00, 9.60, 12.00, 8.00, 2304.00, '2026-11-06 18:16:31'),
(2, 2, 2, 'B002-MED', '2026-02-08', '2031-10-20', 1200.00, 1175.00, 56.00, 70.00, 10.00, 6720.00, '2026-11-06 18:16:31'),
(3, 2, 3, 'B003-MED', '2026-09-14', '2031-10-18', 1000.00, 964.00, 64.00, 80.00, 10.00, 6400.00, '2026-11-06 18:16:31'),
(4, 3, 4, 'B004-MED', '2026-08-25', '2031-10-15', 800.00, 778.00, 120.00, 150.00, 10.00, 9600.00, '2026-11-06 18:16:31'),
(5, 2, 5, 'B005-MED', '2026-04-01', '2031-10-03', 5000.00, 4978.00, 4.80, 6.00, 5.00, 1200.00, '2026-11-06 18:16:31'),
(6, 4, 6, 'B006-MED', '2026-02-10', '2031-02-15', 1000.00, 994.00, 56.00, 70.00, 10.00, 5600.00, '2026-11-06 18:16:31'),
(7, 5, 7, 'B007-SPO', '2026-01-27', '2031-11-16', 40.00, 39.00, 2016.00, 2800.00, 12.00, 9676.80, '2026-11-06 18:16:31'),
(8, 6, 8, 'B008-SPO', '2026-06-07', '2031-07-09', 30.00, 30.00, 1296.00, 1800.00, 12.00, 4665.60, '2026-11-06 18:16:31'),
(9, 6, 9, 'B009-SPO', '2026-06-12', '2031-07-24', 80.00, 78.00, 900.00, 1250.00, 10.00, 7200.00, '2026-11-06 18:16:31'),
(10, 7, 10, 'B010-SPO', '2026-09-21', '2031-02-24', 15.00, 14.00, 3456.00, 4800.00, 12.00, 6220.80, '2026-11-06 18:16:31'),
(11, 8, 11, 'B011-SPO', '2026-06-03', '2031-09-18', 25.00, 24.00, 1728.00, 2400.00, 12.00, 5184.00, '2026-11-06 18:16:31'),
(12, 9, 12, 'B012-SPO', '2026-05-10', '2031-08-05', 100.00, 98.00, 252.00, 350.00, 15.00, 3780.00, '2026-11-06 18:16:31'),
(13, 10, 13, 'B013-ELE', '2026-10-10', '2031-01-28', 25.00, 25.00, 13330.00, 15500.00, 6.00, 19995.00, '2026-11-06 18:16:31'),
(14, 11, 14, 'B014-ELE', '2026-06-12', '2031-08-14', 15.00, 12.00, 21070.00, 24500.00, 5.00, 15802.50, '2026-11-06 18:16:31'),
(15, 12, 15, 'B015-ELE', '2026-02-13', '2031-10-18', 10.00, 10.00, 16770.00, 19500.00, 7.00, 11739.00, '2026-11-06 18:16:31'),
(16, 13, 16, 'B016-ELE', '2026-08-28', '2031-02-14', 50.00, 47.00, 2752.00, 3200.00, 10.00, 13760.00, '2026-11-06 18:16:31'),
(17, 14, 17, 'B017-ELE', '2026-09-26', '2031-10-16', 30.00, 27.00, 3870.00, 4500.00, 10.00, 11610.00, '2026-11-06 18:16:31'),
(18, 10, 18, 'B018-ELE', '2026-07-17', '2031-05-28', 30.00, 27.00, 3612.00, 4200.00, 8.00, 8668.80, '2026-11-06 18:16:31'),
(19, 15, 19, 'B019-REA', '2026-07-19', '2031-08-17', 3.00, 3.00, 7650000.00, 8500000.00, 4.00, 918000.00, '2026-11-06 18:16:31'),
(20, 16, 20, 'B020-REA', '2026-09-26', '2031-01-19', 2.00, 2.00, 9900000.00, 11000000.00, 4.00, 792000.00, '2026-11-06 18:16:31'),
(21, 17, 21, 'B021-REA', '2026-04-25', '2031-03-27', 3.00, 3.00, 5850000.00, 6500000.00, 4.00, 702000.00, '2026-11-06 18:16:31'),
(22, 18, 22, 'B022-REA', '2026-01-23', '2031-09-22', 4.00, 4.00, 8550000.00, 9500000.00, 3.00, 1026000.00, '2026-11-06 18:16:31'),
(23, 19, 23, 'B023-REA', '2026-02-23', '2031-10-14', 2.00, 2.00, 10800000.00, 12000000.00, 3.00, 648000.00, '2026-11-06 18:16:31'),
(24, 20, 24, 'B024-GRO', '2026-08-05', '2026-12-08', 120.00, 110.00, 832.50, 925.00, 4.00, 3996.00, '2026-11-06 18:16:31'),
(25, 21, 25, 'B025-GRO', '2026-02-21', '2026-11-06', 200.00, 177.00, 396.00, 440.00, 4.00, 3168.00, '2026-11-06 18:16:31'),
(26, 22, 26, 'B026-GRO', '2026-08-23', '2031-09-09', 200.00, 195.00, 117.00, 130.00, 5.00, 1170.00, '2026-11-06 18:16:31'),
(27, 23, 27, 'B027-GRO', '2026-09-13', '2031-11-10', 300.00, 294.00, 112.50, 125.00, 3.00, 1012.50, '2026-11-06 18:16:31'),
(28, 24, 28, 'B028-GRO', '2026-09-26', '2031-11-17', 150.00, 144.00, 90.00, 100.00, 6.00, 810.00, '2026-11-06 18:16:31'),
(29, 25, 29, 'B029-GRO', '2026-08-26', '2031-07-14', 100.00, 86.00, 279.00, 310.00, 5.00, 1395.00, '2026-11-06 18:16:31'),
(30, 26, 30, 'B030-FRU', '2026-04-10', '2031-05-01', 150.00, 141.00, 230.40, 320.00, 10.00, 3456.00, '2026-11-06 18:16:31'),
(31, 26, 31, 'B031-FRU', '2026-01-05', '2031-11-20', 120.00, 119.00, 201.60, 280.00, 10.00, 2419.20, '2026-11-06 18:16:31'),
(32, 27, 32, 'B032-FRU', '2026-08-26', '2031-09-16', 200.00, 197.00, 61.20, 85.00, 8.00, 979.20, '2026-11-06 18:16:31'),
(33, 26, 33, 'B033-FRU', '2026-06-07', '2031-05-23', 80.00, 76.00, 324.00, 450.00, 10.00, 2592.00, '2026-11-06 18:16:31'),
(34, 28, 34, 'B034-FRU', '2026-08-15', '2031-09-25', 100.00, 96.00, 324.00, 450.00, 10.00, 3240.00, '2026-11-06 18:16:31'),
(35, 29, 35, 'B035-COM', '2026-08-26', '2031-07-18', 40.00, 30.00, 2728.00, 3100.00, 7.00, 7638.40, '2026-11-06 18:16:31'),
(36, 30, 36, 'B036-COM', '2026-01-27', '2031-05-11', 30.00, 30.00, 4840.00, 5500.00, 7.00, 10164.00, '2026-11-06 18:16:31'),
(37, 30, 37, 'B037-COM', '2026-08-26', '2031-02-15', 40.00, 39.00, 3828.00, 4350.00, 8.00, 12249.60, '2026-11-06 18:16:31'),
(38, 11, 38, 'B038-COM', '2026-09-09', '2031-08-06', 20.00, 20.00, 8624.00, 9800.00, 8.00, 13798.40, '2026-11-06 18:16:31'),
(39, 31, 39, 'B039-COM', '2026-07-27', '2031-12-16', 100.00, 96.00, 572.00, 650.00, 12.00, 6864.00, '2026-11-06 18:16:31'),
(40, 31, 40, 'B040-COM', '2026-02-21', '2031-05-16', 80.00, 79.00, 880.00, 1000.00, 12.00, 8448.00, '2026-11-06 18:16:31'),
(41, 32, 41, 'B041-COM', '2026-06-04', '2031-04-24', 25.00, 23.00, 4576.00, 5200.00, 8.00, 9152.00, '2026-11-06 18:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `shop_id` int NOT NULL,
  `shop_name` varchar(150) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `owner_name` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_1` varchar(20) DEFAULT NULL,
  `phone_2` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`shop_id`, `shop_name`, `tagline`, `owner_name`, `address`, `phone_1`, `phone_2`, `created_at`) VALUES
(1, 'Saju General Store', 'Everything you need, one place', 'Md. Julfikar Ali Saju', 'Dhaka', '01700000000', NULL, '2026-11-06 18:16:31'),
(2, 'JAS Store', 'Health is wealth, Technology is a tool.', 'Md. Julfikar Ali Saju', 'Dinajpur', '01799549505', NULL, '2026-11-07 04:04:40');

-- --------------------------------------------------------

--
-- Table structure for table `shop_instruments`
--

CREATE TABLE `shop_instruments` (
  `instrument_id` int NOT NULL,
  `shop_id` int NOT NULL,
  `instrument_name` varchar(150) NOT NULL,
  `brand_model` varchar(150) DEFAULT NULL,
  `serial_no` varchar(80) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT '0.00',
  `company_id` int DEFAULT NULL,
  `warranty_until` date DEFAULT NULL,
  `condition_status` enum('good','needs_repair','out_of_order') NOT NULL DEFAULT 'good',
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int NOT NULL,
  `shop_id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `nid_no` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT '0.00',
  `join_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `shop_id`, `name`, `designation`, `phone`, `nid_no`, `address`, `salary`, `join_date`, `is_active`, `created_at`) VALUES
(1, 1, 'Rahim Uddin', 'Salesman', '01711111111', NULL, NULL, 15000.00, '2026-01-01', 1, '2026-11-06 18:16:31'),
(2, 2, 'Firoz', 'Saller', '01746983265', '-', 'Dianjpur', 15000.00, '2026-11-07', 1, '2026-11-07 04:20:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Md. Julfikar Ali Saju', 'esaju.cse@gmail.com', NULL, '$2y$12$mlQL6xDwWYpnfe.62JLAi.uFZkIVlhL5eZulOHqPjaREvwcqDhNQC', NULL, '2026-11-06 11:31:13', '2026-11-06 11:31:13');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_company_due`
-- (See below for the actual view)
--
CREATE TABLE `v_company_due` (
`company_id` int
,`company_name` varchar(150)
,`phone_1` varchar(20)
,`total_due` decimal(34,2)
,`total_paid` decimal(34,2)
,`total_purchased` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_customer_due`
-- (See below for the actual view)
--
CREATE TABLE `v_customer_due` (
`address` varchar(255)
,`balance` decimal(34,2)
,`customer_id` int
,`name` varchar(150)
,`phone` varchar(20)
,`total_due` decimal(34,2)
,`total_paid` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_expiry_alert`
-- (See below for the actual view)
--
CREATE TABLE `v_expiry_alert` (
`batch_id` int
,`batch_no` varchar(50)
,`company_name` varchar(150)
,`days_left` int
,`expiry_date` date
,`medicine_name` varchar(150)
,`quantity_remaining` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_top_products`
-- (See below for the actual view)
--
CREATE TABLE `v_top_products` (
`category` varchar(150)
,`medicine_id` int
,`product` varchar(150)
,`total_qty_sold` decimal(32,2)
,`total_revenue` decimal(52,10)
);

-- --------------------------------------------------------

--
-- Structure for view `v_company_due`
--
DROP TABLE IF EXISTS `v_company_due`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_company_due`  AS SELECT `co`.`company_id` AS `company_id`, `co`.`company_name` AS `company_name`, `co`.`phone_1` AS `phone_1`, coalesce(sum((`p`.`total_amount` - `p`.`discount_amount`)),0) AS `total_purchased`, coalesce(sum(`p`.`paid_amount`),0) AS `total_paid`, coalesce(sum(`p`.`due_amount`),0) AS `total_due` FROM (`companies` `co` left join `purchases` `p` on((`p`.`company_id` = `co`.`company_id`))) GROUP BY `co`.`company_id`, `co`.`company_name`, `co`.`phone_1` ;

-- --------------------------------------------------------

--
-- Structure for view `v_customer_due`
--
DROP TABLE IF EXISTS `v_customer_due`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_customer_due`  AS SELECT `c`.`customer_id` AS `customer_id`, `c`.`name` AS `name`, `c`.`phone` AS `phone`, `c`.`address` AS `address`, coalesce(sum((case when (`l`.`entry_type` = 'due') then `l`.`amount` else 0 end)),0) AS `total_due`, coalesce(sum((case when (`l`.`entry_type` = 'payment') then `l`.`amount` else 0 end)),0) AS `total_paid`, coalesce(sum((case when (`l`.`entry_type` = 'due') then `l`.`amount` else -(`l`.`amount`) end)),0) AS `balance` FROM (`customers` `c` left join `customer_ledger` `l` on((`l`.`customer_id` = `c`.`customer_id`))) GROUP BY `c`.`customer_id`, `c`.`name`, `c`.`phone`, `c`.`address` ;

-- --------------------------------------------------------

--
-- Structure for view `v_expiry_alert`
--
DROP TABLE IF EXISTS `v_expiry_alert`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_expiry_alert`  AS SELECT `pi`.`batch_id` AS `batch_id`, `m`.`name` AS `medicine_name`, `co`.`company_name` AS `company_name`, `pi`.`batch_no` AS `batch_no`, `pi`.`expiry_date` AS `expiry_date`, (to_days(`pi`.`expiry_date`) - to_days(curdate())) AS `days_left`, `pi`.`quantity_remaining` AS `quantity_remaining` FROM (((`purchase_items` `pi` join `medicines` `m` on((`m`.`medicine_id` = `pi`.`medicine_id`))) join `purchases` `p` on((`p`.`purchase_id` = `pi`.`purchase_id`))) join `companies` `co` on((`co`.`company_id` = `p`.`company_id`))) WHERE ((`pi`.`quantity_remaining` > 0) AND (`pi`.`expiry_date` <= (curdate() + interval 90 day))) ORDER BY `pi`.`expiry_date` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_top_products`
--
DROP TABLE IF EXISTS `v_top_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_top_products`  AS SELECT `pc`.`name` AS `category`, `m`.`medicine_id` AS `medicine_id`, `m`.`name` AS `product`, sum(`mi`.`quantity`) AS `total_qty_sold`, sum(((`mi`.`quantity` * `mi`.`unit_price`) * (1 - (`mi`.`discount_percent` / 100)))) AS `total_revenue` FROM ((`memo_items` `mi` join `medicines` `m` on((`m`.`medicine_id` = `mi`.`medicine_id`))) left join `product_categories` `pc` on((`pc`.`id` = `m`.`category_id`))) GROUP BY `pc`.`name`, `m`.`medicine_id`, `m`.`name` ORDER BY `total_qty_sold` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `company_payments`
--
ALTER TABLE `company_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_ledger`
--
ALTER TABLE `customer_ledger`
  ADD PRIMARY KEY (`ledger_id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `memo_id` (`memo_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `memos`
--
ALTER TABLE `memos`
  ADD PRIMARY KEY (`memo_id`),
  ADD UNIQUE KEY `memo_no` (`memo_no`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `memo_items`
--
ALTER TABLE `memo_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `memo_id` (`memo_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `order_requests`
--
ALTER TABLE `order_requests`
  ADD PRIMARY KEY (`order_request_id`),
  ADD KEY `idx_or_status` (`status`,`order_request_id`),
  ADD KEY `idx_or_phone` (`phone`),
  ADD KEY `idx_order_requests_memo_id` (`memo_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `expiry_date` (`expiry_date`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`shop_id`);

--
-- Indexes for table `shop_instruments`
--
ALTER TABLE `shop_instruments`
  ADD PRIMARY KEY (`instrument_id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `company_payments`
--
ALTER TABLE `company_payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer_ledger`
--
ALTER TABLE `customer_ledger`
  MODIFY `ledger_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `memos`
--
ALTER TABLE `memos`
  MODIFY `memo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `memo_items`
--
ALTER TABLE `memo_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `order_requests`
--
ALTER TABLE `order_requests`
  MODIFY `order_request_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `batch_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `shop_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shop_instruments`
--
ALTER TABLE `shop_instruments`
  MODIFY `instrument_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_payments`
--
ALTER TABLE `company_payments`
  ADD CONSTRAINT `company_payments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `company_payments_ibfk_2` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_ledger`
--
ALTER TABLE `customer_ledger`
  ADD CONSTRAINT `ledger_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`),
  ADD CONSTRAINT `ledger_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `ledger_ibfk_3` FOREIGN KEY (`memo_id`) REFERENCES `memos` (`memo_id`) ON DELETE SET NULL;

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE SET NULL;

--
-- Constraints for table `memos`
--
ALTER TABLE `memos`
  ADD CONSTRAINT `memos_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`),
  ADD CONSTRAINT `memos_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `memos_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL;

--
-- Constraints for table `memo_items`
--
ALTER TABLE `memo_items`
  ADD CONSTRAINT `memo_items_ibfk_1` FOREIGN KEY (`memo_id`) REFERENCES `memos` (`memo_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memo_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`),
  ADD CONSTRAINT `memo_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `purchase_items` (`batch_id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`);

--
-- Constraints for table `shop_instruments`
--
ALTER TABLE `shop_instruments`
  ADD CONSTRAINT `instruments_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`),
  ADD CONSTRAINT `instruments_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
