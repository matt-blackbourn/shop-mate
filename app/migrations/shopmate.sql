-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Feb 11, 2026 at 07:26 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopmate`
--

-- --------------------------------------------------------

--
-- Table structure for table `edge`
--

CREATE TABLE `edge` (
  `id` int NOT NULL,
  `length` int NOT NULL,
  `phase` int NOT NULL,
  `start_id` int NOT NULL,
  `end_id` int NOT NULL,
  `supermarket_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `edge`
--

INSERT INTO `edge` (`id`, `length`, `phase`, `start_id`, `end_id`, `supermarket_id`) VALUES
(1, 170, 1, 2, 3, 1),
(2, 147, 1, 3, 4, 1),
(3, 227, 1, 4, 5, 1),
(4, 272, 1, 5, 13, 1),
(5, 255, 1, 13, 12, 1),
(6, 244, 1, 12, 11, 1),
(7, 149, 1, 11, 10, 1),
(8, 80, 1, 10, 14, 1),
(9, 146, 1, 14, 18, 1),
(10, 158, 1, 18, 17, 1),
(11, 156, 1, 17, 16, 1),
(12, 155, 1, 18, 15, 1),
(13, 147, 1, 14, 9, 1),
(14, 227, 1, 11, 8, 1),
(15, 227, 1, 12, 7, 1),
(16, 227, 1, 13, 6, 1),
(17, 272, 1, 4, 6, 1),
(18, 255, 1, 6, 7, 1),
(19, 244, 1, 7, 8, 1),
(20, 149, 1, 8, 9, 1),
(21, 146, 1, 9, 15, 1),
(22, 158, 1, 15, 16, 1),
(23, 287, 1, 16, 19, 1),
(24, 308, 1, 19, 21, 1),
(25, 287, 1, 15, 20, 1),
(26, 308, 1, 20, 22, 1),
(27, 158, 1, 19, 20, 1),
(28, 158, 1, 21, 22, 1),
(29, 322, 1, 21, 24, 1),
(30, 158, 1, 24, 23, 1),
(31, 322, 1, 22, 23, 1),
(32, 321, 2, 24, 25, 1),
(33, 324, 2, 25, 27, 1),
(34, 327, 2, 27, 30, 1),
(35, 321, 2, 23, 26, 1),
(36, 158, 2, 26, 25, 1),
(37, 324, 2, 26, 28, 1),
(38, 158, 2, 28, 27, 1),
(39, 327, 2, 28, 29, 1),
(40, 158, 2, 29, 30, 1),
(41, 170, 2, 30, 31, 1),
(42, 158, 2, 31, 32, 1),
(43, 170, 2, 32, 29, 1),
(44, 146, 3, 29, 39, 1),
(45, 139, 3, 39, 40, 1),
(46, 280, 3, 40, 69, 1),
(47, 244, 3, 69, 70, 1),
(48, 266, 3, 70, 84, 1),
(49, 223, 3, 84, 83, 1),
(50, 142, 3, 83, 72, 1),
(51, 234, 3, 72, 73, 1),
(52, 324, 3, 73, 74, 1),
(53, 321, 3, 74, 75, 1),
(54, 124, 3, 75, 65, 1),
(55, 322, 3, 65, 64, 1),
(56, 198, 3, 64, 63, 1),
(57, 250, 3, 63, 62, 1),
(58, 124, 3, 62, 78, 1),
(59, 272, 3, 78, 3, 1),
(60, 131, 3, 62, 61, 1),
(61, 113, 3, 61, 48, 1),
(62, 131, 3, 48, 47, 1),
(63, 149, 3, 47, 33, 1),
(64, 250, 3, 33, 34, 1),
(65, 250, 3, 47, 46, 1),
(66, 250, 3, 48, 49, 1),
(67, 250, 3, 61, 60, 1),
(68, 198, 3, 60, 59, 1),
(69, 198, 3, 49, 50, 1),
(70, 198, 3, 46, 45, 1),
(71, 198, 3, 34, 35, 1),
(72, 322, 3, 35, 36, 1),
(73, 322, 3, 45, 44, 1),
(74, 322, 3, 50, 51, 1),
(75, 322, 3, 59, 58, 1),
(76, 131, 3, 65, 58, 1),
(77, 113, 3, 58, 51, 1),
(78, 131, 3, 51, 44, 1),
(79, 149, 3, 44, 36, 1),
(80, 146, 3, 23, 36, 1),
(81, 321, 3, 36, 37, 1),
(82, 321, 3, 44, 43, 1),
(83, 324, 3, 37, 38, 1),
(84, 327, 3, 38, 39, 1),
(85, 149, 3, 39, 41, 1),
(86, 327, 3, 41, 42, 1),
(87, 324, 3, 42, 43, 1),
(88, 131, 3, 41, 54, 1),
(89, 327, 3, 54, 53, 1),
(90, 324, 3, 53, 52, 1),
(91, 321, 3, 52, 51, 1),
(92, 321, 3, 58, 57, 1),
(93, 324, 3, 57, 56, 1),
(94, 327, 3, 56, 55, 1),
(95, 113, 3, 55, 54, 1),
(96, 131, 3, 55, 68, 1),
(97, 124, 3, 68, 71, 1),
(98, 93, 3, 71, 72, 1),
(99, 327, 3, 68, 67, 1),
(100, 324, 3, 67, 66, 1),
(101, 321, 3, 66, 65, 1),
(102, 234, 3, 83, 82, 1),
(103, 324, 3, 82, 81, 1),
(104, 321, 3, 81, 79, 1),
(105, 142, 3, 79, 75, 1),
(106, 520, 3, 79, 80, 1),
(107, 322, 3, 75, 76, 1),
(108, 198, 3, 76, 77, 1),
(109, 250, 3, 78, 77, 1),
(110, 142, 3, 77, 80, 1);

-- --------------------------------------------------------

--
-- Table structure for table `food_category`
--

CREATE TABLE `food_category` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_category`
--

INSERT INTO `food_category` (`id`, `name`, `order_by`) VALUES
(1, 'Fruit & Vegetables', 1),
(2, 'Meat', 3),
(3, 'Fish & Seafood', 2),
(4, 'Dairy & Eggs', 6),
(5, 'Deli', 4),
(6, 'Bakery', 5),
(7, 'Snacks & Crackers', 10),
(8, 'Breakfast & Cereal', 9),
(9, 'Pasta, Rice & Grains', 100),
(10, 'Canned Goods & Soups', 100),
(11, 'Baking & Cooking', 100),
(12, 'Condiments & Spices', 100),
(13, 'Snacks & Crackers', 100),
(14, 'Frozen Foods', 100),
(15, 'Household & Cleaning', 100),
(16, 'Personal Care & Health', 100),
(17, 'Baby', 100),
(18, 'Pet Food', 100),
(19, 'Soft Drinks/Juices', 7),
(20, 'Bread', 8),
(21, 'Biscuits/Cookies', 11),
(22, 'Kitchen & Food Storage', 100),
(23, 'Confectionary', 100);

-- --------------------------------------------------------

--
-- Table structure for table `food_item`
--

CREATE TABLE `food_item` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `side` smallint DEFAULT NULL,
  `edge_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_item`
--

INSERT INTO `food_item` (`id`, `name`, `side`, `edge_id`, `category_id`, `user_id`) VALUES
(1, 'Milk', NULL, NULL, 4, NULL),
(2, 'Sausages', NULL, NULL, 2, NULL),
(3, 'Chopped tomatoes', NULL, NULL, 10, NULL),
(4, 'Ziplock bags', NULL, NULL, 22, NULL),
(5, 'Maple syrup', NULL, NULL, NULL, NULL),
(6, 'Rice', NULL, NULL, 9, NULL),
(7, 'Cling wrap', NULL, NULL, 22, NULL),
(8, 'Bananas', NULL, NULL, 1, NULL),
(9, 'Coke', NULL, NULL, 19, NULL),
(10, 'Rice Crackers', NULL, NULL, 7, NULL),
(11, 'Mince', NULL, NULL, 2, NULL),
(12, 'Cookies', NULL, NULL, 21, NULL),
(13, 'Porridge', NULL, NULL, 8, NULL),
(14, 'Yoghurt', NULL, NULL, 4, NULL),
(15, 'Bread', NULL, NULL, 20, NULL),
(16, 'Chicken', NULL, NULL, 2, NULL),
(17, 'Corn thins multigrain', NULL, NULL, 7, NULL),
(18, 'Peppermint Tea', NULL, NULL, NULL, NULL),
(19, 'Pork shoulder', NULL, NULL, 2, NULL),
(20, 'Oranges', NULL, NULL, 1, NULL),
(21, 'Lettuce', NULL, NULL, 1, NULL),
(22, 'Black beans', NULL, NULL, 10, NULL),
(23, 'Red onion', NULL, NULL, 1, NULL),
(24, 'Onions', NULL, NULL, 1, NULL),
(25, 'Tomatoes', NULL, NULL, 1, NULL),
(26, 'Tortilla wraps', NULL, NULL, 9, NULL),
(27, 'Avocado', NULL, NULL, 1, NULL),
(28, 'Cucumber', NULL, NULL, 1, NULL),
(35, 'Blueberries', NULL, NULL, 14, NULL),
(36, 'Couscous', NULL, NULL, 9, NULL),
(37, 'Lamb mince', NULL, NULL, 2, NULL),
(38, 'Feta', NULL, NULL, 4, NULL),
(39, 'Apples', NULL, NULL, 1, NULL),
(40, 'Chocolate', NULL, NULL, 23, NULL),
(41, 'Chicken drumsticks', NULL, NULL, 2, NULL),
(42, 'Courgette', NULL, NULL, 1, NULL),
(43, 'Kumera', NULL, NULL, 1, NULL),
(44, 'Sour cream', NULL, NULL, 4, NULL),
(45, 'Corn chips', NULL, NULL, NULL, NULL),
(46, 'Meusli', NULL, NULL, 8, NULL),
(47, 'Cat food - wet', NULL, NULL, 18, NULL),
(48, 'Sunblock', NULL, NULL, NULL, NULL),
(49, 'Muesli', NULL, NULL, 8, NULL),
(50, 'new', NULL, NULL, NULL, NULL),
(51, 'Sardines', NULL, NULL, NULL, NULL),
(52, 'Tuna - canned', NULL, NULL, NULL, NULL),
(53, 'Fish', NULL, NULL, NULL, NULL),
(54, 'Bread rolls', NULL, NULL, NULL, NULL),
(55, 'Nappies', NULL, NULL, NULL, NULL),
(56, 'Sparkling water', NULL, NULL, NULL, NULL),
(57, 'Bliss balls', NULL, NULL, NULL, NULL),
(58, 'Japanese curry', NULL, NULL, NULL, NULL),
(59, 'Red lentils', NULL, NULL, NULL, NULL),
(60, 'Cheese', NULL, NULL, NULL, NULL),
(61, 'Capsicum', NULL, NULL, NULL, NULL),
(62, 'Thai pancake', NULL, NULL, NULL, NULL),
(63, 'Burger patties - beef', NULL, NULL, NULL, NULL),
(64, 'Burger buns', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `list_item`
--

CREATE TABLE `list_item` (
  `id` int NOT NULL,
  `quantity` int DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picked_at` datetime DEFAULT NULL,
  `shopping_list_id` int NOT NULL,
  `food_item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `list_item`
--

INSERT INTO `list_item` (`id`, `quantity`, `notes`, `picked_at`, `shopping_list_id`, `food_item_id`) VALUES
(127, NULL, NULL, '2026-02-07 01:42:41', 3, 27),
(143, NULL, NULL, '2026-02-07 01:41:22', 3, 24),
(146, NULL, NULL, '2026-02-07 02:20:29', 3, 35),
(148, NULL, NULL, '2026-02-08 04:20:22', 3, 57),
(166, NULL, NULL, '2026-02-08 04:21:24', 3, 15),
(168, NULL, NULL, '2026-02-08 04:11:12', 3, 61),
(169, NULL, NULL, '2026-02-08 04:15:47', 3, 60),
(170, NULL, NULL, '2026-02-08 04:36:28', 3, 17),
(171, NULL, NULL, '2026-02-08 04:14:25', 3, 14),
(172, NULL, 'Mild', '2026-02-08 04:17:46', 3, 58),
(173, 2, NULL, '2026-02-08 04:11:12', 3, 24),
(175, NULL, NULL, '2026-02-08 04:12:47', 3, 11),
(176, 2, NULL, '2026-02-08 04:12:46', 3, 16),
(177, NULL, NULL, '2026-02-08 04:16:42', 3, 1),
(203, NULL, NULL, NULL, 3, 64),
(205, 4, NULL, NULL, 3, 56),
(206, NULL, NULL, NULL, 3, 63);

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE `node` (
  `id` int NOT NULL,
  `x_value` int DEFAULT NULL,
  `y_value` int DEFAULT NULL,
  `supermarket_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `x_value`, `y_value`, `supermarket_id`) VALUES
(2, 1295, 642, 1),
(3, 1295, 472, 1),
(4, 1295, 325, 1),
(5, 1295, 98, 1),
(6, 1023, 325, 1),
(7, 768, 325, 1),
(8, 524, 325, 1),
(9, 375, 325, 1),
(10, 375, 98, 1),
(11, 524, 98, 1),
(12, 768, 98, 1),
(13, 1023, 98, 1),
(14, 375, 170, 1),
(15, 229, 325, 1),
(16, 71, 325, 1),
(17, 71, 170, 1),
(18, 229, 170, 1),
(19, 71, 612, 1),
(20, 229, 612, 1),
(21, 71, 920, 1),
(22, 229, 920, 1),
(23, 229, 1242, 1),
(24, 71, 1242, 1),
(25, 71, 1563, 1),
(26, 229, 1563, 1),
(27, 71, 1887, 1),
(28, 229, 1887, 1),
(29, 229, 2214, 1),
(30, 71, 2214, 1),
(31, 71, 2384, 1),
(32, 229, 2384, 1),
(33, 375, 472, 1),
(34, 375, 722, 1),
(35, 375, 920, 1),
(36, 375, 1242, 1),
(37, 375, 1563, 1),
(38, 375, 1887, 1),
(39, 375, 2214, 1),
(40, 375, 2350, 1),
(41, 524, 2214, 1),
(42, 524, 1887, 1),
(43, 524, 1563, 1),
(44, 524, 1242, 1),
(45, 524, 920, 1),
(46, 524, 722, 1),
(47, 524, 472, 1),
(48, 655, 472, 1),
(49, 655, 722, 1),
(50, 655, 920, 1),
(51, 655, 1242, 1),
(52, 655, 1563, 1),
(53, 655, 1887, 1),
(54, 655, 2214, 1),
(55, 768, 2214, 1),
(56, 768, 1887, 1),
(57, 768, 1563, 1),
(58, 768, 1242, 1),
(59, 768, 920, 1),
(60, 768, 722, 1),
(61, 768, 472, 1),
(62, 899, 472, 1),
(63, 899, 722, 1),
(64, 899, 920, 1),
(65, 899, 1242, 1),
(66, 899, 1563, 1),
(67, 899, 1887, 1),
(68, 899, 2214, 1),
(69, 655, 2350, 1),
(70, 899, 2350, 1),
(71, 1023, 2214, 1),
(72, 1023, 2121, 1),
(73, 1023, 1887, 1),
(74, 1023, 1563, 1),
(75, 1023, 1242, 1),
(76, 1023, 920, 1),
(77, 1023, 722, 1),
(78, 1023, 472, 1),
(79, 1165, 1242, 1),
(80, 1165, 722, 1),
(81, 1165, 1563, 1),
(82, 1165, 1887, 1),
(83, 1165, 2121, 1),
(84, 1165, 2350, 1);

-- --------------------------------------------------------

--
-- Table structure for table `owner_type`
--

CREATE TABLE `owner_type` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_placement`
--

CREATE TABLE `product_placement` (
  `id` int NOT NULL,
  `aisle_side` int DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `food_item_id` int NOT NULL,
  `supermarket_id` int NOT NULL,
  `edge_id` int NOT NULL,
  `superseded_by_id` int DEFAULT NULL,
  `suggested_by_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_placement`
--

INSERT INTO `product_placement` (`id`, `aisle_side`, `type`, `food_item_id`, `supermarket_id`, `edge_id`, `superseded_by_id`, `suggested_by_id`) VALUES
(1, 1, 'user', 35, 1, 52, NULL, 1),
(3, 1, 'user', 47, 1, 100, NULL, 1),
(4, -1, 'user', 40, 1, 108, NULL, 1),
(5, -1, 'user', 8, 1, 15, NULL, 1),
(7, -1, 'category', 20, 1, 15, NULL, NULL),
(13, -1, 'user', 16, 1, 25, NULL, 1),
(14, -1, 'user', 39, 1, 5, NULL, 1),
(16, -1, 'user', 49, 1, 71, NULL, 1),
(17, -1, 'user', 14, 1, 35, NULL, 1),
(18, -1, 'category', 1, 1, 35, NULL, NULL),
(19, -1, 'user', 56, 1, 83, NULL, 1),
(20, 1, 'user', 1, 1, 41, NULL, 1),
(21, -1, 'user', 55, 1, 55, NULL, 1),
(22, -1, 'category', 27, 1, 15, NULL, NULL),
(23, -1, 'category', 42, 1, 15, NULL, NULL),
(24, -1, 'category', 11, 1, 25, NULL, NULL),
(25, -1, 'category', 24, 1, 15, NULL, NULL),
(26, 1, 'user', 17, 1, 65, NULL, 1),
(27, -1, 'category', 25, 1, 15, NULL, NULL),
(28, -1, 'user', 25, 1, 19, NULL, 1),
(29, 1, 'user', 15, 1, 71, NULL, 1),
(30, -1, 'user', 60, 1, 39, NULL, 1),
(31, 1, 'user', 57, 1, 82, NULL, 1),
(32, -1, 'user', 58, 1, 90, NULL, 1),
(34, -1, 'user', 61, 1, 18, NULL, 1),
(35, -1, 'system', 46, 1, 71, NULL, NULL),
(36, -1, 'user', 62, 1, 52, NULL, 1),
(37, -1, 'category', 38, 1, 35, NULL, NULL),
(38, -1, 'user', 38, 1, 37, NULL, 1),
(39, -1, 'category', 41, 1, 25, NULL, NULL),
(40, -1, 'system', 13, 1, 71, NULL, NULL),
(41, 1, 'user', 41, 1, 26, NULL, 1),
(42, -1, 'user', 63, 1, 24, NULL, 1),
(43, -1, 'user', 64, 1, 37, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shelf`
--

CREATE TABLE `shelf` (
  `id` int NOT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  `x` int NOT NULL,
  `y` int NOT NULL,
  `supermarket_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shelf`
--

INSERT INTO `shelf` (`id`, `width`, `height`, `x`, `y`, `supermarket_id`) VALUES
(2, 30, 2352, 1, 100, 1),
(3, 1257, 30, 31, 2424, 1),
(4, 78, 90, 111, 2254, 1),
(5, 78, 247, 111, 1927, 1),
(6, 78, 244, 111, 1603, 1),
(7, 78, 241, 111, 1282, 1),
(8, 78, 242, 111, 960, 1),
(9, 78, 228, 111, 652, 1),
(10, 78, 207, 111, 365, 1),
(11, 312, 30, 31, 100, 1),
(12, 1023, 30, 345, 28, 1),
(13, 30, 578, 1335, 58, 1),
(14, 69, 147, 415, 138, 1),
(15, 164, 147, 564, 138, 1),
(16, 175, 147, 808, 138, 1),
(17, 192, 147, 1063, 138, 1),
(18, 652, 67, 335, 365, 1),
(19, 66, 837, 269, 365, 1),
(20, 66, 892, 269, 1282, 1),
(21, 66, 170, 269, 2254, 1),
(22, 710, 56, 415, 2254, 1),
(23, 62, 149, 1063, 2161, 1),
(24, 69, 892, 415, 1282, 1),
(25, 69, 690, 415, 512, 1),
(26, 51, 690, 564, 512, 1),
(27, 33, 690, 695, 512, 1),
(28, 51, 690, 808, 512, 1),
(29, 44, 690, 939, 512, 1),
(30, 51, 892, 564, 1282, 1),
(31, 33, 892, 695, 1282, 1),
(32, 51, 892, 808, 1282, 1),
(33, 44, 892, 939, 1282, 1),
(34, 62, 647, 1063, 1373, 1),
(36, 39, 73, 306, 26, 1),
(37, 48, 259, 1237, 2165, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_list`
--

CREATE TABLE `shopping_list` (
  `id` int NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_completed` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shopping_list`
--

INSERT INTO `shopping_list` (`id`, `date_created`, `date_completed`, `date_modified`, `user_id`) VALUES
(3, '2026-01-27 07:56:49', NULL, '2026-02-11 09:05:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `supermarket`
--

CREATE TABLE `supermarket` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` int DEFAULT NULL,
  `width` int DEFAULT NULL,
  `entrance_node_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supermarket`
--

INSERT INTO `supermarket` (`id`, `name`, `image_path`, `height`, `width`, `entrance_node_id`) VALUES
(1, 'PaknSave QT', '69785513d829a.jpg', 2456, 1388, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_used_supermarket_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `last_used_supermarket_id`) VALUES
(1, 'dev@local.test', '[\"ROLE_USER\"]', '$2y$13$somehashedpassword', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `edge`
--
ALTER TABLE `edge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7506D366623DF99B` (`start_id`),
  ADD KEY `IDX_7506D366E2BD8A10` (`end_id`),
  ADD KEY `IDX_7506D366933DE57C` (`supermarket_id`);

--
-- Indexes for table `food_category`
--
ALTER TABLE `food_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_item`
--
ALTER TABLE `food_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AA3C8DCF696D413E` (`edge_id`),
  ADD KEY `IDX_AA3C8DCF12469DE2` (`category_id`),
  ADD KEY `IDX_AA3C8DCFA76ED395` (`user_id`);

--
-- Indexes for table `list_item`
--
ALTER TABLE `list_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5AD5FAF723245BF9` (`shopping_list_id`),
  ADD KEY `IDX_5AD5FAF75DF08E66` (`food_item_id`);

--
-- Indexes for table `node`
--
ALTER TABLE `node`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_857FE845933DE57C` (`supermarket_id`);

--
-- Indexes for table `owner_type`
--
ALTER TABLE `owner_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_placement`
--
ALTER TABLE `product_placement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_267BC3835DF08E66` (`food_item_id`),
  ADD KEY `IDX_267BC383933DE57C` (`supermarket_id`),
  ADD KEY `IDX_267BC383696D413E` (`edge_id`),
  ADD KEY `IDX_267BC38339626D86` (`superseded_by_id`),
  ADD KEY `IDX_267BC38366290AB1` (`suggested_by_id`);

--
-- Indexes for table `shelf`
--
ALTER TABLE `shelf`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A5475BE3933DE57C` (`supermarket_id`);

--
-- Indexes for table `shopping_list`
--
ALTER TABLE `shopping_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3DC1A459A76ED395` (`user_id`);

--
-- Indexes for table `supermarket`
--
ALTER TABLE `supermarket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D51643A2E393F4C1` (`entrance_node_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD KEY `IDX_8D93D6498A51642B` (`last_used_supermarket_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `edge`
--
ALTER TABLE `edge`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `food_category`
--
ALTER TABLE `food_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `food_item`
--
ALTER TABLE `food_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `list_item`
--
ALTER TABLE `list_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `node`
--
ALTER TABLE `node`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `owner_type`
--
ALTER TABLE `owner_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_placement`
--
ALTER TABLE `product_placement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `shelf`
--
ALTER TABLE `shelf`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `shopping_list`
--
ALTER TABLE `shopping_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `supermarket`
--
ALTER TABLE `supermarket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `edge`
--
ALTER TABLE `edge`
  ADD CONSTRAINT `FK_7506D366623DF99B` FOREIGN KEY (`start_id`) REFERENCES `node` (`id`),
  ADD CONSTRAINT `FK_7506D366933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`),
  ADD CONSTRAINT `FK_7506D366E2BD8A10` FOREIGN KEY (`end_id`) REFERENCES `node` (`id`);

--
-- Constraints for table `food_item`
--
ALTER TABLE `food_item`
  ADD CONSTRAINT `FK_AA3C8DCF12469DE2` FOREIGN KEY (`category_id`) REFERENCES `food_category` (`id`),
  ADD CONSTRAINT `FK_AA3C8DCF696D413E` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`),
  ADD CONSTRAINT `FK_AA3C8DCFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `list_item`
--
ALTER TABLE `list_item`
  ADD CONSTRAINT `FK_5AD5FAF723245BF9` FOREIGN KEY (`shopping_list_id`) REFERENCES `shopping_list` (`id`),
  ADD CONSTRAINT `FK_5AD5FAF75DF08E66` FOREIGN KEY (`food_item_id`) REFERENCES `food_item` (`id`);

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `FK_857FE845933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`);

--
-- Constraints for table `product_placement`
--
ALTER TABLE `product_placement`
  ADD CONSTRAINT `FK_267BC38339626D86` FOREIGN KEY (`superseded_by_id`) REFERENCES `product_placement` (`id`),
  ADD CONSTRAINT `FK_267BC3835DF08E66` FOREIGN KEY (`food_item_id`) REFERENCES `food_item` (`id`),
  ADD CONSTRAINT `FK_267BC38366290AB1` FOREIGN KEY (`suggested_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_267BC383696D413E` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`),
  ADD CONSTRAINT `FK_267BC383933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`);

--
-- Constraints for table `shelf`
--
ALTER TABLE `shelf`
  ADD CONSTRAINT `FK_A5475BE3933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`);

--
-- Constraints for table `shopping_list`
--
ALTER TABLE `shopping_list`
  ADD CONSTRAINT `FK_3DC1A459A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `supermarket`
--
ALTER TABLE `supermarket`
  ADD CONSTRAINT `FK_D51643A2E393F4C1` FOREIGN KEY (`entrance_node_id`) REFERENCES `node` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D6498A51642B` FOREIGN KEY (`last_used_supermarket_id`) REFERENCES `supermarket` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
