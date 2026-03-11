-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Mar 11, 2026 at 07:56 PM
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
  `supermarket_id` int DEFAULT NULL,
  `aisle_key` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `edge`
--

INSERT INTO `edge` (`id`, `length`, `phase`, `start_id`, `end_id`, `supermarket_id`, `aisle_key`) VALUES
(1, 170, 1, 2, 3, 1, NULL),
(2, 147, 1, 3, 4, 1, NULL),
(3, 227, 1, 4, 5, 1, NULL),
(4, 272, 1, 5, 13, 1, NULL),
(5, 255, 1, 13, 12, 1, NULL),
(6, 244, 1, 12, 11, 1, NULL),
(7, 149, 1, 11, 10, 1, NULL),
(8, 80, 1, 10, 14, 1, NULL),
(9, 146, 1, 14, 18, 1, NULL),
(10, 158, 1, 18, 17, 1, NULL),
(11, 156, 1, 17, 16, 1, NULL),
(12, 155, 1, 18, 15, 1, NULL),
(13, 147, 1, 14, 9, 1, NULL),
(14, 227, 1, 11, 8, 1, NULL),
(15, 227, 1, 12, 7, 1, NULL),
(16, 227, 1, 13, 6, 1, NULL),
(17, 272, 1, 4, 6, 1, NULL),
(18, 255, 1, 6, 7, 1, NULL),
(19, 244, 1, 7, 8, 1, NULL),
(20, 149, 1, 8, 9, 1, NULL),
(21, 146, 1, 9, 15, 1, NULL),
(22, 158, 1, 15, 16, 1, NULL),
(23, 287, 1, 16, 19, 1, NULL),
(24, 308, 1, 19, 21, 1, NULL),
(25, 287, 1, 15, 20, 1, NULL),
(26, 308, 1, 20, 22, 1, NULL),
(27, 158, 1, 19, 20, 1, NULL),
(28, 158, 1, 21, 22, 1, NULL),
(29, 322, 1, 21, 24, 1, NULL),
(30, 158, 1, 24, 23, 1, NULL),
(31, 322, 1, 22, 23, 1, NULL),
(32, 321, 2, 24, 25, 1, NULL),
(33, 324, 2, 25, 27, 1, NULL),
(34, 327, 2, 27, 30, 1, NULL),
(35, 321, 2, 23, 26, 1, NULL),
(36, 158, 2, 26, 25, 1, NULL),
(37, 324, 2, 26, 28, 1, NULL),
(38, 158, 2, 28, 27, 1, NULL),
(39, 327, 2, 28, 29, 1, NULL),
(40, 158, 2, 29, 30, 1, NULL),
(41, 170, 2, 30, 31, 1, NULL),
(42, 158, 2, 31, 32, 1, NULL),
(43, 170, 2, 32, 29, 1, NULL),
(44, 146, 3, 29, 39, 1, NULL),
(45, 139, 3, 39, 40, 1, NULL),
(46, 280, 3, 40, 69, 1, NULL),
(47, 244, 3, 69, 70, 1, NULL),
(48, 266, 3, 70, 84, 1, NULL),
(49, 223, 3, 84, 83, 1, NULL),
(50, 142, 4, 83, 72, 1, NULL),
(51, 234, 4, 72, 73, 1, NULL),
(52, 324, 4, 73, 74, 1, NULL),
(53, 321, 4, 74, 75, 1, NULL),
(54, 124, 3, 75, 65, 1, NULL),
(55, 322, 3, 65, 64, 1, 5),
(56, 198, 3, 64, 63, 1, 5),
(57, 250, 3, 63, 62, 1, 5),
(58, 124, 3, 62, 78, 1, NULL),
(59, 272, 3, 78, 3, 1, NULL),
(60, 131, 3, 62, 61, 1, NULL),
(61, 113, 3, 61, 48, 1, NULL),
(62, 131, 3, 48, 47, 1, NULL),
(63, 149, 3, 47, 33, 1, NULL),
(64, 250, 3, 33, 34, 1, 1),
(65, 250, 3, 47, 46, 1, 2),
(66, 250, 3, 48, 49, 1, 3),
(67, 250, 3, 61, 60, 1, 4),
(68, 198, 3, 60, 59, 1, 4),
(69, 198, 3, 49, 50, 1, 3),
(70, 198, 3, 46, 45, 1, 2),
(71, 198, 3, 34, 35, 1, 1),
(72, 322, 3, 35, 36, 1, 1),
(73, 322, 3, 45, 44, 1, 2),
(74, 322, 3, 50, 51, 1, 3),
(75, 322, 3, 59, 58, 1, 4),
(76, 131, 3, 65, 58, 1, NULL),
(77, 113, 3, 58, 51, 1, NULL),
(78, 131, 3, 51, 44, 1, NULL),
(79, 149, 3, 44, 36, 1, NULL),
(80, 146, 3, 23, 36, 1, NULL),
(81, 321, 3, 36, 37, 1, 1),
(82, 321, 3, 44, 43, 1, 2),
(83, 324, 3, 37, 38, 1, 1),
(84, 327, 3, 38, 39, 1, 1),
(85, 149, 3, 39, 41, 1, NULL),
(86, 327, 3, 41, 42, 1, 2),
(87, 324, 3, 42, 43, 1, 2),
(88, 131, 3, 41, 54, 1, NULL),
(89, 327, 3, 54, 53, 1, 3),
(90, 324, 3, 53, 52, 1, 3),
(91, 321, 3, 52, 51, 1, 3),
(92, 321, 3, 58, 57, 1, 4),
(93, 324, 3, 57, 56, 1, 4),
(94, 327, 3, 56, 55, 1, 4),
(95, 113, 3, 55, 54, 1, NULL),
(96, 131, 3, 55, 68, 1, NULL),
(97, 124, 3, 68, 71, 1, NULL),
(98, 93, 3, 71, 72, 1, NULL),
(99, 327, 3, 68, 67, 1, 5),
(100, 324, 3, 67, 66, 1, 5),
(101, 321, 3, 66, 65, 1, 5),
(102, 234, 4, 83, 82, 1, NULL),
(103, 324, 4, 82, 81, 1, NULL),
(104, 321, 4, 81, 79, 1, NULL),
(105, 142, 4, 79, 75, 1, NULL),
(106, 520, 4, 79, 80, 1, NULL),
(107, 322, 4, 75, 76, 1, NULL),
(108, 198, 4, 76, 77, 1, NULL),
(109, 250, 4, 78, 77, 1, NULL),
(110, 142, 4, 77, 80, 1, NULL),
(341, 46, 1, 403, 359, 7, NULL),
(342, 141, 1, 359, 360, 7, NULL),
(343, 71, 1, 360, 361, 7, NULL),
(344, 142, 1, 361, 362, 7, NULL),
(345, 71, 1, 359, 362, 7, NULL),
(346, 71, 1, 362, 363, 7, NULL),
(347, 114, 1, 363, 364, 7, NULL),
(348, 99, 1, 364, 371, 7, NULL),
(349, 85, 1, 371, 372, 7, NULL),
(350, 85, 1, 372, 375, 7, NULL),
(351, 71, 1, 375, 376, 7, NULL),
(352, 71, 1, 376, 379, 7, NULL),
(353, 100, 1, 379, 365, 7, NULL),
(354, 114, 1, 365, 404, 7, NULL),
(355, 100, 1, 404, 378, 7, NULL),
(356, 114, 1, 378, 379, 7, NULL),
(357, 71, 1, 378, 377, 7, NULL),
(358, 114, 1, 377, 376, 7, NULL),
(359, 71, 1, 377, 374, 7, NULL),
(360, 114, 1, 374, 375, 7, NULL),
(361, 85, 1, 374, 373, 7, NULL),
(362, 114, 1, 373, 372, 7, NULL),
(363, 85, 1, 373, 370, 7, NULL),
(364, 114, 1, 370, 371, 7, NULL),
(365, 99, 1, 363, 370, 7, NULL),
(366, 71, 3, 404, 380, 7, NULL),
(367, 71, 3, 380, 383, 7, NULL),
(368, 71, 3, 383, 384, 7, NULL),
(369, 71, 3, 384, 387, 7, NULL),
(370, 71, 3, 387, 388, 7, NULL),
(371, 70, 3, 388, 366, 7, NULL),
(372, 71, 3, 366, 367, 7, NULL),
(373, 29, 3, 367, 391, 7, NULL),
(374, 85, 3, 391, 392, 7, NULL),
(375, 71, 3, 392, 395, 7, NULL),
(376, 71, 3, 395, 396, 7, NULL),
(377, 28, 3, 396, 400, 7, NULL),
(378, 71, 3, 400, 401, 7, NULL),
(379, 142, 3, 401, 402, 7, NULL),
(380, 71, 3, 402, 368, 7, NULL),
(381, 99, 3, 368, 399, 7, NULL),
(382, 43, 3, 399, 400, 7, NULL),
(383, 440, 3, 368, 369, 7, NULL),
(384, 99, 3, 369, 398, 7, NULL),
(385, 71, 3, 398, 397, 7, NULL),
(386, 71, 3, 397, 394, 7, NULL),
(387, 71, 3, 394, 393, 7, NULL),
(388, 85, 3, 393, 390, 7, NULL),
(389, 99, 3, 390, 389, 7, NULL),
(390, 71, 3, 389, 386, 7, NULL),
(391, 71, 3, 386, 385, 7, NULL),
(392, 71, 3, 385, 382, 7, NULL),
(393, 71, 3, 382, 381, 7, NULL),
(394, 71, 3, 381, 363, 7, NULL),
(395, 511, 3, 380, 381, 7, NULL),
(396, 511, 3, 383, 382, 7, NULL),
(397, 511, 3, 385, 384, 7, NULL),
(398, 511, 3, 387, 386, 7, NULL),
(399, 511, 3, 389, 388, 7, NULL),
(400, 440, 3, 391, 390, 7, NULL),
(401, 440, 3, 393, 392, 7, NULL),
(402, 440, 3, 395, 394, 7, NULL),
(403, 440, 3, 397, 396, 7, NULL),
(404, 440, 3, 399, 398, 7, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `food_item`
--

CREATE TABLE `food_item` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `side` smallint DEFAULT NULL,
  `edge_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_item`
--

INSERT INTO `food_item` (`id`, `name`, `side`, `edge_id`, `user_id`) VALUES
(1, 'Milk', NULL, NULL, NULL),
(2, 'Sausages', NULL, NULL, NULL),
(3, 'Chopped tomatoes', NULL, NULL, NULL),
(4, 'Ziplock bags', NULL, NULL, NULL),
(5, 'Maple syrup', NULL, NULL, NULL),
(6, 'Rice', NULL, NULL, NULL),
(7, 'Cling wrap', NULL, NULL, NULL),
(8, 'Bananas', NULL, NULL, NULL),
(9, 'Coke', NULL, NULL, NULL),
(10, 'Rice Crackers', NULL, NULL, NULL),
(11, 'Mince', NULL, NULL, NULL),
(12, 'Cookies', NULL, NULL, NULL),
(13, 'Porridge', NULL, NULL, NULL),
(14, 'Yoghurt', NULL, NULL, NULL),
(15, 'Bread', NULL, NULL, NULL),
(16, 'Chicken', NULL, NULL, NULL),
(17, 'Corn thins', NULL, NULL, NULL),
(18, 'Peppermint Tea', NULL, NULL, NULL),
(19, 'Pork shoulder', NULL, NULL, NULL),
(20, 'Oranges', NULL, NULL, NULL),
(21, 'Lettuce', NULL, NULL, NULL),
(22, 'Black beans', NULL, NULL, NULL),
(23, 'Red onion', NULL, NULL, NULL),
(24, 'Onions', NULL, NULL, NULL),
(25, 'Tomatoes', NULL, NULL, NULL),
(26, 'Tortilla wraps', NULL, NULL, NULL),
(27, 'Avocado', NULL, NULL, NULL),
(28, 'Cucumber', NULL, NULL, NULL),
(35, 'Blueberries', NULL, NULL, NULL),
(36, 'Couscous', NULL, NULL, NULL),
(37, 'Lamb mince', NULL, NULL, NULL),
(38, 'Feta', NULL, NULL, NULL),
(39, 'Apples', NULL, NULL, NULL),
(40, 'Chocolate', NULL, NULL, NULL),
(41, 'Chicken drumsticks', NULL, NULL, NULL),
(42, 'Courgette', NULL, NULL, NULL),
(43, 'Kumera', NULL, NULL, NULL),
(44, 'Sour cream', NULL, NULL, NULL),
(45, 'Corn chips', NULL, NULL, NULL),
(46, 'Meusli', NULL, NULL, NULL),
(47, 'Cat food', NULL, NULL, NULL),
(48, 'Sunblock', NULL, NULL, NULL),
(49, 'Muesli', NULL, NULL, NULL),
(51, 'Sardines', NULL, NULL, NULL),
(52, 'Tuna - canned', NULL, NULL, NULL),
(53, 'Fish', NULL, NULL, NULL),
(54, 'Bread rolls', NULL, NULL, NULL),
(55, 'Nappies', NULL, NULL, NULL),
(56, 'Sparkling water', NULL, NULL, NULL),
(57, 'Bliss balls', NULL, NULL, NULL),
(58, 'Japanese curry', NULL, NULL, NULL),
(59, 'Red lentils', NULL, NULL, NULL),
(60, 'Cheese', NULL, NULL, NULL),
(61, 'Capsicum', NULL, NULL, NULL),
(62, 'Thai pancake', NULL, NULL, NULL),
(63, 'Burger patties', NULL, NULL, NULL),
(64, 'Burger buns', NULL, NULL, NULL),
(66, 'Eggs', NULL, NULL, NULL),
(67, 'Pasta sauce', NULL, NULL, NULL),
(68, 'Carrots', NULL, NULL, NULL),
(69, 'Mushrooms', NULL, NULL, NULL),
(70, 'Garlic', NULL, NULL, NULL),
(71, 'Chicken chasseur mix', NULL, NULL, NULL),
(72, 'Bacon', NULL, NULL, NULL),
(73, 'Blood Orange Tea', NULL, NULL, NULL),
(74, 'Chicken Salt', NULL, NULL, NULL),
(75, 'Salt', NULL, NULL, NULL),
(77, 'Tomatoe sauce', NULL, NULL, NULL),
(86, 'Pepper', NULL, NULL, NULL),
(87, 'Potatoes', NULL, NULL, NULL),
(88, 'Salami', NULL, NULL, NULL),
(89, 'Leek', NULL, NULL, NULL),
(90, 'Cream', NULL, NULL, NULL),
(91, 'Pork', NULL, NULL, NULL),
(92, 'Peanut butter', NULL, NULL, NULL),
(93, 'Yoghurt', NULL, NULL, NULL),
(95, 'Halloumi', NULL, NULL, NULL),
(97, 'newit', NULL, NULL, NULL),
(98, 'newst', NULL, NULL, NULL),
(99, 'Dried thyme', NULL, NULL, NULL),
(100, 'Black adder tea', NULL, NULL, NULL),
(101, 'Napp', NULL, NULL, NULL),
(102, 'Toothpaste', NULL, NULL, NULL),
(103, 'Pasta', NULL, NULL, NULL),
(104, 'Decaf tea', NULL, NULL, NULL),
(105, 'Pizzas', NULL, NULL, NULL),
(106, 'Cottage cheese', NULL, NULL, NULL),
(107, 'Taco seasoning', NULL, NULL, NULL),
(108, 'Parmesan', NULL, NULL, NULL),
(109, 'Coconut milk', NULL, NULL, NULL),
(110, 'Ginger', NULL, NULL, NULL),
(111, 'Cauliflower', NULL, NULL, NULL),
(112, 'White beans', NULL, NULL, NULL),
(113, 'Lemon', NULL, NULL, NULL),
(114, 'Pita breads', NULL, NULL, NULL),
(115, 'Cabbage', NULL, NULL, NULL),
(116, 'Baking soda', NULL, NULL, NULL),
(117, 'Chips', NULL, NULL, NULL),
(118, 'Steak', NULL, NULL, NULL),
(119, 'Cherry tomatoes', NULL, NULL, NULL),
(120, 'Broccoli', NULL, NULL, NULL),
(121, 'Cream Cheese', NULL, NULL, NULL),
(122, 'Butter', NULL, NULL, NULL),
(123, 'Gravy', NULL, NULL, NULL),
(124, 'Pineapple - canned', NULL, NULL, NULL),
(125, 'Brown sugar', NULL, NULL, NULL),
(126, 'Icing sugar', NULL, NULL, NULL),
(127, 'Corn - frozen', NULL, NULL, NULL),
(128, 'Cellotape', NULL, NULL, NULL),
(129, 'Coffee', NULL, NULL, NULL),
(130, 'Pork mince', NULL, NULL, NULL),
(131, 'Cherr', NULL, NULL, NULL),
(132, 'Beer', NULL, NULL, NULL),
(133, 'Bin Liners', NULL, NULL, NULL),
(134, 'sdfs', NULL, NULL, NULL),
(135, 'sfsfd', NULL, NULL, NULL),
(136, 'asfsa', NULL, NULL, NULL),
(137, 'sdfssdf', NULL, NULL, NULL);

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
  `food_item_id` int NOT NULL,
  `session_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `list_item`
--

INSERT INTO `list_item` (`id`, `quantity`, `notes`, `picked_at`, `shopping_list_id`, `food_item_id`, `session_id`) VALUES
(127, NULL, NULL, '2026-02-07 01:42:41', 3, 27, NULL),
(143, NULL, NULL, '2026-02-07 01:41:22', 3, 24, NULL),
(146, NULL, NULL, '2026-02-07 02:20:29', 3, 35, NULL),
(148, NULL, NULL, '2026-02-08 04:20:22', 3, 57, NULL),
(166, NULL, NULL, '2026-02-08 04:21:24', 3, 15, NULL),
(168, NULL, NULL, '2026-02-08 04:11:12', 3, 61, NULL),
(169, NULL, NULL, '2026-02-08 04:15:47', 3, 60, NULL),
(170, NULL, NULL, '2026-02-08 04:36:28', 3, 17, NULL),
(171, NULL, NULL, '2026-02-08 04:14:25', 3, 14, NULL),
(172, NULL, 'Mild', '2026-02-08 04:17:46', 3, 58, NULL),
(173, 2, NULL, '2026-02-08 04:11:12', 3, 24, NULL),
(175, NULL, NULL, '2026-02-08 04:12:47', 3, 11, NULL),
(176, 2, NULL, '2026-02-08 04:12:46', 3, 16, NULL),
(177, NULL, NULL, '2026-02-08 04:16:42', 3, 1, NULL),
(205, NULL, NULL, '2026-02-13 02:21:19', 3, 68, NULL),
(206, NULL, NULL, '2026-02-13 02:21:27', 3, 71, NULL),
(208, NULL, NULL, '2026-02-13 02:21:22', 3, 72, NULL),
(209, NULL, NULL, '2026-02-13 02:21:15', 3, 24, NULL),
(210, 4, NULL, '2026-02-13 02:19:13', 3, 56, NULL),
(211, NULL, NULL, '2026-02-13 02:21:22', 3, 16, NULL),
(212, NULL, NULL, '2026-02-13 02:21:21', 3, 69, NULL),
(213, NULL, NULL, '2026-02-13 02:21:28', 3, 67, NULL),
(214, NULL, NULL, '2026-02-13 02:19:39', 3, 42, NULL),
(215, NULL, NULL, '2026-02-13 02:21:14', 3, 42, NULL),
(216, NULL, NULL, '2026-02-13 02:21:23', 3, 63, NULL),
(217, NULL, NULL, '2026-02-13 02:21:16', 3, 70, NULL),
(218, NULL, NULL, '2026-02-13 02:21:24', 3, 1, NULL),
(219, NULL, NULL, '2026-02-13 02:21:13', 3, 8, NULL),
(220, NULL, NULL, '2026-02-13 02:21:26', 3, 66, NULL),
(221, NULL, NULL, '2026-02-13 02:21:25', 3, 64, NULL),
(222, 4, NULL, '2026-02-13 02:21:27', 3, 56, NULL),
(223, NULL, NULL, '2026-02-13 02:21:24', 3, 14, NULL),
(228, NULL, NULL, '2026-02-15 02:35:52', 3, 61, NULL),
(232, NULL, NULL, '2026-02-15 02:35:58', 3, 8, NULL),
(233, NULL, NULL, '2026-02-15 02:46:08', 3, 38, NULL),
(234, NULL, NULL, NULL, 6, 16, NULL),
(235, NULL, NULL, '2026-02-15 02:38:30', 3, 41, NULL),
(236, NULL, NULL, '2026-02-15 02:49:40', 3, 17, NULL),
(237, NULL, NULL, '2026-02-15 02:42:49', 3, 28, NULL),
(238, NULL, 'Refill', '2026-02-15 02:51:22', 3, 77, NULL),
(239, NULL, NULL, '2026-02-15 02:42:23', 3, 87, NULL),
(240, NULL, NULL, '2026-02-15 02:46:23', 3, 90, NULL),
(241, NULL, NULL, '2026-02-15 02:43:14', 3, 89, NULL),
(242, NULL, NULL, NULL, 6, 39, NULL),
(245, 1, 'Molenburg', NULL, 6, 15, NULL),
(247, 1, NULL, NULL, 6, 93, NULL),
(248, 2, NULL, NULL, 6, 1, NULL),
(264, 1, NULL, NULL, 6, 8, NULL),
(265, 1, 'Wet', '2026-02-20 01:35:12', 3, 47, NULL),
(271, 1, NULL, '2026-02-20 01:18:59', 3, 8, NULL),
(272, 1, NULL, '2026-02-20 01:35:11', 3, 99, NULL),
(278, 1, NULL, '2026-02-20 01:34:50', 3, 100, NULL),
(282, 1, NULL, '2026-02-20 01:19:00', 3, 20, NULL),
(283, 1, 'Molenburg', '2026-02-20 01:34:47', 3, 15, NULL),
(284, 2, NULL, '2026-02-20 01:34:36', 3, 1, NULL),
(286, 1, NULL, '2026-02-20 01:35:11', 3, 102, NULL),
(287, 1, 'Pams finest - smooth', '2026-02-20 01:34:48', 3, 92, NULL),
(288, 1, NULL, '2026-02-20 01:18:56', 3, 21, NULL),
(291, NULL, NULL, '2026-02-20 01:20:32', 3, 16, NULL),
(293, 1, 'Remi rascals size 6', '2026-02-20 01:35:12', 3, 55, NULL),
(295, 1, NULL, '2026-02-20 01:35:10', 3, 3, NULL),
(296, 1, NULL, '2026-02-20 01:20:36', 3, 11, NULL),
(299, 1, NULL, '2026-02-22 04:08:38', 3, 109, NULL),
(300, 1, 'Only if mild', '2026-02-22 04:18:14', 3, 107, NULL),
(301, 1, NULL, '2026-02-22 04:12:15', 3, 12, NULL),
(304, 1, 'The cheapest', '2026-02-22 04:01:28', 3, 106, NULL),
(305, 1, 'Pork', '2026-02-22 03:59:51', 3, 2, NULL),
(306, 1, 'Any (white) beans will be fine', '2026-02-22 04:14:00', 3, 112, NULL),
(307, 1, NULL, '2026-02-22 03:53:52', 3, 113, NULL),
(309, 1, NULL, '2026-02-22 03:56:57', 3, 23, NULL),
(311, NULL, NULL, '2026-02-22 03:51:56', 3, 39, NULL),
(312, 1, 'Half', '2026-02-22 03:56:04', 3, 111, NULL),
(313, 1, NULL, '2026-02-22 04:19:11', 3, 105, NULL),
(314, 1, NULL, '2026-02-22 04:09:28', 3, 114, NULL),
(315, 1, NULL, '2026-02-22 03:53:51', 3, 70, NULL),
(316, 3, NULL, '2026-02-22 03:50:47', 3, 24, NULL),
(318, 1, 'English breakfast', '2026-02-22 04:12:16', 3, 104, NULL),
(319, 2, NULL, '2026-02-22 04:03:29', 3, 1, NULL),
(320, 1, 'Half', '2026-02-22 03:48:16', 3, 115, NULL),
(321, 1, NULL, '2026-02-22 03:59:17', 3, 11, NULL),
(322, 1, NULL, '2026-02-22 03:53:53', 3, 110, NULL),
(324, 2, NULL, '2026-02-22 04:12:14', 3, 3, NULL),
(326, 1, 'Molenburg', '2026-02-22 04:10:14', 3, 15, NULL),
(327, 1, 'Red', '2026-02-22 03:49:39', 3, 61, NULL),
(328, 1, NULL, '2026-02-22 03:58:35', 3, 16, NULL),
(329, 1, NULL, '2026-02-22 04:02:40', 3, 60, NULL),
(330, 2, NULL, '2026-02-22 03:56:46', 3, 43, NULL),
(331, 1, NULL, '2026-02-22 04:01:28', 3, 93, NULL),
(332, 1, 'Cheese', '2026-02-22 04:05:48', 3, 17, NULL),
(335, 1, NULL, '2026-03-03 03:06:18', 3, 116, 7),
(336, 1, NULL, '2026-02-24 08:08:05', 3, 39, 5),
(366, 1, 'Isha', '2026-02-27 03:07:22', 3, 55, 6),
(367, 1, NULL, '2026-02-27 02:36:32', 3, 93, 6),
(368, 1, NULL, '2026-02-27 02:28:47', 3, 8, 6),
(370, 1, NULL, '2026-02-27 02:29:26', 3, 20, 6),
(372, 1, 'Molenburg', '2026-02-27 02:36:35', 3, 15, 6),
(373, 1, NULL, '2026-02-27 02:37:21', 3, 118, 6),
(374, 1, NULL, '2026-03-03 03:06:20', 3, 126, 7),
(375, 2, NULL, '2026-02-27 02:36:31', 3, 1, 6),
(376, NULL, NULL, '2026-02-27 02:36:34', 3, 16, 6),
(377, 1, NULL, '2026-02-27 02:36:31', 3, 11, 6),
(378, 1, NULL, '2026-02-27 02:27:56', 3, 28, 6),
(379, 1, NULL, '2026-02-27 02:37:22', 3, 119, 6),
(380, 1, NULL, '2026-03-07 21:43:17', 3, 64, 10),
(381, 1, NULL, '2026-03-06 02:10:17', 3, 8, 9),
(382, 1, NULL, '2026-03-06 02:10:17', 3, 28, 9),
(383, 1, NULL, '2026-03-06 02:13:12', 3, 120, 9),
(385, 1, NULL, '2026-03-03 03:08:21', 3, 122, 7),
(386, 1, NULL, '2026-03-05 23:57:14', 3, 128, 8),
(389, 1, NULL, '2026-03-06 02:23:04', 3, 56, 9),
(390, 1, 'Roast chicken', '2026-03-06 02:28:40', 3, 123, 9),
(391, 1, NULL, '2026-03-06 02:14:27', 3, 130, 9),
(392, 1, NULL, '2026-03-06 02:29:51', 3, 5, 9),
(393, 1, NULL, '2026-03-07 21:42:00', 3, 63, 10),
(395, 2, NULL, '2026-03-06 02:21:36', 3, 1, 9),
(396, 1, NULL, '2026-03-06 02:29:52', 3, 125, 9),
(398, 1, NULL, '2026-03-06 02:26:44', 3, 18, 9),
(400, 1, NULL, '2026-03-06 02:13:47', 3, 16, 9),
(404, 1, NULL, '2026-03-06 02:27:51', 3, 129, 9),
(405, 1, 'Plain', '2026-03-07 21:42:32', 3, 14, 10),
(406, 1, NULL, '2026-03-06 02:24:04', 3, 15, 9),
(407, 1, NULL, '2026-03-06 02:26:43', 3, 46, 9),
(409, 1, NULL, '2026-03-06 02:13:14', 3, 43, 9),
(410, 1, NULL, '2026-03-06 02:21:05', 3, 106, 9),
(411, 1, NULL, '2026-03-06 02:11:05', 3, 24, 9),
(412, 1, NULL, '2026-03-07 21:41:24', 3, 72, 10),
(415, 1, NULL, '2026-03-07 21:50:08', 3, 40, 10),
(416, 1, NULL, '2026-03-07 21:40:09', 3, 21, 10),
(417, 1, NULL, '2026-03-07 21:44:58', 3, 66, 10),
(418, 1, 'Whole', '2026-03-07 21:40:58', 3, 16, 10),
(419, 1, NULL, '2026-03-07 21:45:01', 3, 1, 10),
(420, 1, NULL, '2026-03-07 21:48:26', 3, 22, 10),
(421, 1, NULL, '2026-03-07 21:40:08', 3, 61, 10),
(429, 1, NULL, '2026-03-10 23:57:52', 3, 133, 12),
(434, 1, 'Both - none left', NULL, 3, 46, NULL),
(435, 1, NULL, NULL, 3, 122, NULL),
(436, 1, 'Isha', '2026-03-10 23:57:52', 3, 55, 12),
(439, 1, NULL, '2026-03-10 23:57:53', 3, 13, 12),
(440, 1, NULL, '2026-03-10 23:57:51', 3, 47, 12);

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
(84, 1165, 2350, 1),
(359, 30, 171, 7),
(360, 30, 30, 7),
(361, 101, 30, 7),
(362, 101, 172, 7),
(363, 172, 172, 7),
(364, 172, 58, 7),
(365, 683, 58, 7),
(366, 683, 597, 7),
(367, 612, 597, 7),
(368, 612, 1023, 7),
(369, 172, 1023, 7),
(370, 271, 172, 7),
(371, 271, 58, 7),
(372, 356, 58, 7),
(373, 356, 172, 7),
(374, 441, 172, 7),
(375, 441, 58, 7),
(376, 512, 58, 7),
(377, 512, 172, 7),
(378, 583, 172, 7),
(379, 583, 58, 7),
(380, 683, 243, 7),
(381, 172, 243, 7),
(382, 172, 314, 7),
(383, 683, 314, 7),
(384, 683, 385, 7),
(385, 172, 385, 7),
(386, 172, 456, 7),
(387, 683, 456, 7),
(388, 683, 527, 7),
(389, 172, 527, 7),
(390, 172, 626, 7),
(391, 612, 626, 7),
(392, 612, 711, 7),
(393, 172, 711, 7),
(394, 172, 782, 7),
(395, 612, 782, 7),
(396, 612, 853, 7),
(397, 172, 853, 7),
(398, 172, 924, 7),
(399, 612, 924, 7),
(400, 612, 881, 7),
(401, 683, 881, 7),
(402, 683, 1023, 7),
(403, 30, 217, 7),
(404, 683, 172, 7);

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
(1, 1, 'system', 35, 1, 52, NULL, 1),
(3, 1, 'system', 47, 1, 100, NULL, 1),
(4, -1, 'system', 40, 1, 108, NULL, 1),
(5, -1, 'system', 8, 1, 15, NULL, 1),
(7, -1, 'system', 20, 1, 15, NULL, NULL),
(13, 1, 'user', 16, 1, 25, NULL, 1),
(14, -1, 'system', 39, 1, 5, NULL, 1),
(16, -1, 'system', 49, 1, 71, NULL, 1),
(17, 1, 'user', 14, 1, 35, NULL, 1),
(18, -1, 'system', 1, 1, 35, NULL, NULL),
(19, -1, 'system', 56, 1, 83, NULL, 1),
(20, -1, 'user', 1, 1, 41, NULL, 1),
(21, -1, 'user', 55, 1, 55, NULL, 1),
(22, -1, 'system', 27, 1, 15, NULL, NULL),
(23, -1, 'system', 42, 1, 15, NULL, NULL),
(24, -1, 'system', 11, 1, 25, NULL, NULL),
(25, -1, 'system', 24, 1, 15, NULL, NULL),
(26, 1, 'system', 17, 1, 65, NULL, 1),
(27, -1, 'system', 25, 1, 15, NULL, NULL),
(28, -1, 'system', 25, 1, 19, NULL, 1),
(29, -1, 'system', 15, 1, 71, NULL, 1),
(30, -1, 'system', 60, 1, 39, NULL, 1),
(31, 1, 'system', 57, 1, 82, NULL, 1),
(32, -1, 'system', 58, 1, 90, NULL, 1),
(34, 1, 'system', 61, 1, 18, NULL, 1),
(35, -1, 'system', 46, 1, 71, NULL, NULL),
(36, -1, 'system', 62, 1, 52, NULL, 1),
(37, -1, 'system', 38, 1, 35, NULL, NULL),
(38, -1, 'system', 38, 1, 37, NULL, 1),
(39, -1, 'system', 41, 1, 25, NULL, NULL),
(40, -1, 'system', 13, 1, 71, NULL, NULL),
(41, 1, 'system', 41, 1, 26, NULL, 1),
(42, -1, 'system', 63, 1, 24, NULL, 1),
(43, -1, 'system', 64, 1, 37, NULL, 1),
(44, 1, 'user', 72, 1, 26, NULL, 1),
(45, 1, 'user', 68, 1, 20, NULL, 1),
(46, -1, 'user', 71, 1, 74, NULL, 1),
(47, 1, 'user', 66, 1, 34, NULL, 1),
(48, 1, 'user', 70, 1, 6, NULL, 1),
(49, 1, 'user', 69, 1, 19, NULL, 1),
(50, 1, 'user', 67, 1, 90, NULL, 1),
(51, -1, 'user', 18, 1, 73, NULL, 1),
(52, -1, 'user', 73, 1, 73, NULL, 1),
(53, 1, 'user', 87, 1, 6, NULL, 1),
(54, 1, 'user', 28, 1, 18, NULL, 1),
(55, 1, 'user', 89, 1, 18, NULL, 1),
(56, 1, 'user', 88, 1, 26, NULL, 1),
(57, -1, 'user', 90, 1, 34, NULL, 1),
(58, 1, 'user', 77, 1, 91, NULL, 1),
(59, -1, 'user', 92, 1, 64, NULL, 1),
(60, 1, 'user', 21, 1, 18, NULL, 1),
(61, -1, 'user', 100, 1, 70, NULL, 1),
(62, 1, 'user', 95, 1, 37, NULL, 1),
(63, -1, 'user', 102, 1, 57, NULL, 1),
(64, -1, 'user', 99, 1, 69, NULL, 1),
(65, -1, 'user', 105, 1, 53, NULL, 1),
(66, -1, 'user', 104, 1, 73, NULL, 1),
(67, 1, 'user', 111, 1, 20, NULL, 1),
(68, 1, 'user', 109, 1, 43, NULL, 1),
(69, 1, 'user', 106, 1, 37, NULL, 1),
(70, 1, 'user', 3, 1, 74, NULL, 1),
(71, 1, 'user', 110, 1, 6, NULL, 1),
(72, 1, 'user', 43, 1, 13, NULL, 1),
(73, -1, 'user', 113, 1, 5, NULL, 1),
(74, -1, 'user', 2, 1, 29, NULL, 1),
(75, 1, 'user', 112, 1, 74, NULL, 1),
(76, 1, 'user', 107, 1, 87, NULL, 1),
(77, 1, 'user', 115, 1, 20, NULL, 1),
(78, -1, 'user', 114, 1, 72, NULL, 1),
(79, 1, 'user', 23, 1, 20, NULL, 1),
(80, 1, 'user', 93, 1, 35, NULL, 1),
(81, 1, 'user', 12, 1, 73, NULL, 1),
(82, -1, 'user', 11, 1, 23, NULL, 1),
(83, -1, 'user', 9, 1, 84, NULL, 1),
(84, -1, 'user', 117, 1, 86, NULL, 1),
(85, 1, 'user', 46, 1, 71, NULL, 1),
(86, 1, 'user', 116, 1, 69, NULL, 1),
(87, 1, 'user', 120, 1, 19, NULL, 1),
(88, -1, 'user', 122, 1, 33, NULL, 1),
(89, 1, 'user', 127, 1, 52, NULL, 1),
(90, -1, 'user', 126, 1, 69, NULL, 1),
(91, -1, 'user', 125, 1, 66, NULL, 1),
(92, 1, 'user', 121, 1, 37, NULL, 1),
(93, 1, 'group', 22, 1, 74, NULL, 1),
(94, -1, 'user', 133, 1, 63, NULL, 1);

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
  `supermarket_id` int NOT NULL,
  `full_select` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shelf`
--

INSERT INTO `shelf` (`id`, `width`, `height`, `x`, `y`, `supermarket_id`, `full_select`) VALUES
(2, 30, 2352, 1, 100, 1, 1),
(3, 1257, 30, 31, 2424, 1, 1),
(4, 78, 90, 111, 2254, 1, 1),
(5, 78, 247, 111, 1927, 1, 1),
(6, 78, 244, 111, 1603, 1, 1),
(7, 78, 241, 111, 1282, 1, 1),
(8, 78, 242, 111, 960, 1, 1),
(9, 78, 228, 111, 652, 1, 1),
(10, 78, 207, 111, 365, 1, 1),
(11, 312, 30, 31, 100, 1, 1),
(12, 1023, 30, 345, 28, 1, 1),
(13, 30, 578, 1335, 58, 1, 1),
(14, 69, 147, 415, 138, 1, 1),
(15, 164, 147, 564, 138, 1, 1),
(16, 175, 147, 808, 138, 1, 1),
(17, 192, 147, 1063, 138, 1, 1),
(18, 652, 67, 335, 365, 1, 0),
(19, 66, 837, 269, 365, 1, 0),
(20, 66, 892, 269, 1282, 1, 0),
(21, 66, 170, 269, 2254, 1, 0),
(22, 710, 56, 415, 2254, 1, 0),
(23, 62, 149, 1063, 2161, 1, 0),
(24, 69, 892, 415, 1282, 1, 0),
(25, 69, 690, 415, 512, 1, 0),
(26, 51, 690, 564, 512, 1, 0),
(27, 52, 690, 684, 512, 1, 0),
(28, 51, 690, 808, 512, 1, 0),
(29, 50, 690, 939, 512, 1, 0),
(30, 51, 892, 564, 1282, 1, 0),
(31, 55, 892, 683, 1282, 1, 0),
(32, 51, 892, 808, 1282, 1, 0),
(33, 51, 892, 939, 1282, 1, 0),
(34, 62, 647, 1063, 1373, 1, 0),
(36, 39, 73, 306, 26, 1, 1),
(37, 48, 259, 1237, 2165, 1, 1),
(109, 531, 25, 189, 13, 7, 1),
(110, 196, 20, 13, -7, 7, 1),
(111, 20, 223, -7, -7, 7, 1),
(113, 36, 107, 47, 47, 7, 0),
(114, 36, 137, 118, 13, 7, 0),
(115, 64, 79, 189, 75, 7, 0),
(116, 50, 79, 288, 75, 7, 0),
(117, 50, 79, 373, 75, 7, 0),
(118, 36, 79, 458, 75, 7, 0),
(119, 36, 79, 529, 75, 7, 0),
(120, 65, 79, 600, 75, 7, 0),
(121, 20, 576, 700, 38, 7, 1),
(122, 476, 36, 189, 189, 7, 0),
(123, 476, 36, 189, 260, 7, 0),
(124, 476, 36, 189, 331, 7, 0),
(125, 476, 36, 189, 402, 7, 0),
(126, 476, 36, 189, 473, 7, 0),
(127, 404, 64, 189, 544, 7, 0),
(128, 20, 209, 629, 634, 7, 1),
(129, 92, 20, 629, 614, 7, 1),
(130, 91, 20, 629, 843, 7, 1),
(131, 405, 50, 189, 643, 7, 0),
(132, 184, 36, 189, 728, 7, 0),
(133, 405, 36, 189, 799, 7, 0),
(134, 405, 36, 189, 870, 7, 0),
(135, 405, 64, 189, 941, 7, 0),
(136, 36, 107, 629, 898, 7, 0),
(137, 20, 196, 700, 863, 7, 1),
(138, 564, 20, 136, 1040, 7, 1),
(139, 20, 99, 134, 941, 7, 1),
(140, 168, 34, 425, 729, 7, 0);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_list`
--

CREATE TABLE `shopping_list` (
  `id` int NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `quick_add_list` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shopping_list`
--

INSERT INTO `shopping_list` (`id`, `date_created`, `user_id`, `quick_add_list`) VALUES
(3, '2026-01-27 07:56:49', 1, 0),
(6, '2026-02-15 21:13:11', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_session`
--

CREATE TABLE `shopping_session` (
  `id` int NOT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `shopping_list_id` int NOT NULL,
  `current_node_id` int DEFAULT NULL,
  `supermarket_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shopping_session`
--

INSERT INTO `shopping_session` (`id`, `started_at`, `completed_at`, `shopping_list_id`, `current_node_id`, `supermarket_id`) VALUES
(1, '2026-02-23 08:52:51', '2026-02-23 19:36:09', 3, NULL, 1),
(5, '2026-02-23 23:54:27', '2026-02-24 08:08:05', 3, 13, 1),
(6, '2026-02-27 02:27:56', '2026-02-27 03:07:22', 3, 65, 1),
(7, '2026-03-03 03:06:18', '2026-03-03 03:08:21', 3, 2, 1),
(8, '2026-03-05 23:57:14', '2026-03-05 23:57:14', 3, 2, 1),
(9, '2026-03-06 02:10:17', '2026-03-06 02:29:52', 3, 7, 1),
(10, '2026-03-07 21:40:08', '2026-03-07 21:50:08', 3, 77, 1),
(11, '2026-03-08 19:16:43', '2026-03-08 20:51:54', 3, 2, 1),
(12, '2026-03-10 23:57:51', '2026-03-10 23:57:53', 3, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `supermarket`
--

CREATE TABLE `supermarket` (
  `id` int NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` int DEFAULT NULL,
  `width` int DEFAULT NULL,
  `entrance_node_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suburb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `walking_path_id` int DEFAULT NULL,
  `active` tinyint NOT NULL,
  `scaled_path_data` json DEFAULT NULL,
  `landscape` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supermarket`
--

INSERT INTO `supermarket` (`id`, `image_path`, `height`, `width`, `entrance_node_id`, `date_created`, `type`, `suburb`, `walking_path_id`, `active`, `scaled_path_data`, `landscape`) VALUES
(1, '69785513d829a.jpg', 2456, 1388, 2, NULL, 'Pak’nSave', 'QT', NULL, 1, NULL, 0),
(7, NULL, 1053, 713, 403, '2026-03-11 04:14:28', 'New World', 'Frankton', 5, 1, '[{\"x\": 30, \"y\": 186}, {\"x\": 30, \"y\": 30}, {\"x\": 101, \"y\": 30}, {\"x\": 101, \"y\": 172}, {\"x\": 172, \"y\": 172}, {\"x\": 172, \"y\": 58}, {\"x\": 683, \"y\": 58}, {\"x\": 683, \"y\": 597}, {\"x\": 612, \"y\": 597}, {\"x\": 612, \"y\": 1023}, {\"x\": 172, \"y\": 1023}, {\"x\": 172, \"y\": 172}, {\"x\": 271, \"y\": 172}, {\"x\": 271, \"y\": 58}, {\"x\": 356, \"y\": 58}, {\"x\": 356, \"y\": 172}, {\"x\": 441, \"y\": 172}, {\"x\": 441, \"y\": 58}, {\"x\": 512, \"y\": 58}, {\"x\": 512, \"y\": 172}, {\"x\": 583, \"y\": 172}, {\"x\": 583, \"y\": 58}, {\"x\": 683, \"y\": 58}, {\"x\": 683, \"y\": 243}, {\"x\": 172, \"y\": 243}, {\"x\": 172, \"y\": 314}, {\"x\": 683, \"y\": 314}, {\"x\": 683, \"y\": 385}, {\"x\": 172, \"y\": 385}, {\"x\": 172, \"y\": 456}, {\"x\": 683, \"y\": 456}, {\"x\": 683, \"y\": 527}, {\"x\": 172, \"y\": 527}, {\"x\": 172, \"y\": 626}, {\"x\": 612, \"y\": 626}, {\"x\": 612, \"y\": 711}, {\"x\": 172, \"y\": 711}, {\"x\": 172, \"y\": 782}, {\"x\": 612, \"y\": 782}, {\"x\": 612, \"y\": 853}, {\"x\": 172, \"y\": 853}, {\"x\": 172, \"y\": 924}, {\"x\": 612, \"y\": 924}, {\"x\": 612, \"y\": 881}, {\"x\": 683, \"y\": 881}, {\"x\": 683, \"y\": 1023}, {\"x\": 612, \"y\": 1023}]', 0);

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
(1, 'dev@local.test', '[\"ROLE_USER\"]', '$2y$13$somehashedpassword', 7);

-- --------------------------------------------------------

--
-- Table structure for table `walking_path`
--

CREATE TABLE `walking_path` (
  `id` int NOT NULL,
  `raw_data` json NOT NULL,
  `suburb` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `converted` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `walking_path`
--

INSERT INTO `walking_path` (`id`, `raw_data`, `suburb`, `date_created`, `type`, `user_id`, `converted`) VALUES
(1, '{\"path\": [{\"x\": 0, \"y\": 0, \"steps\": 0}, {\"x\": 2, \"y\": -1.2246467991473532e-16, \"steps\": 2}, {\"x\": 2, \"y\": -21, \"steps\": 21}, {\"x\": -2, \"y\": -21, \"steps\": 4}, {\"x\": -8, \"y\": -21, \"steps\": 6}, {\"x\": -13, \"y\": -21, \"steps\": 5}, {\"x\": -18, \"y\": -21, \"steps\": 5}, {\"x\": -23, \"y\": -21, \"steps\": 5}, {\"x\": -28, \"y\": -21, \"steps\": 5}, {\"x\": -33, \"y\": -21, \"steps\": 5}, {\"x\": -39, \"y\": -21, \"steps\": 6}, {\"x\": -39, \"y\": -17, \"steps\": 4}, {\"x\": -49, \"y\": -16.999999999999996, \"steps\": 10}, {\"x\": -49, \"y\": -11.999999999999996, \"steps\": 5}, {\"x\": -49, \"y\": -3.999999999999997, \"steps\": 8}, {\"x\": -49, \"y\": 6.0000000000000036, \"steps\": 10}, {\"x\": -49, \"y\": 16.000000000000004, \"steps\": 10}, {\"x\": -49, \"y\": 30.000000000000004, \"steps\": 14}, {\"x\": -49, \"y\": 41, \"steps\": 11}, {\"x\": -49, \"y\": 48, \"steps\": 7}, {\"x\": -49, \"y\": 55, \"steps\": 7}, {\"x\": -49, \"y\": 63, \"steps\": 8}, {\"x\": -49, \"y\": 72, \"steps\": 9}, {\"x\": -43, \"y\": 72, \"steps\": 6}, {\"x\": -43, \"y\": 66, \"steps\": 6}, {\"x\": -39, \"y\": 66, \"steps\": 4}, {\"x\": -39, \"y\": 29, \"steps\": 37}, {\"x\": -39, \"y\": -5, \"steps\": 34}, {\"x\": -34, \"y\": -5, \"steps\": 5}, {\"x\": -29, \"y\": -5, \"steps\": 5}, {\"x\": -24, \"y\": -5, \"steps\": 5}, {\"x\": -19, \"y\": -5, \"steps\": 5}, {\"x\": -14, \"y\": -5, \"steps\": 5}, {\"x\": -9, \"y\": -5, \"steps\": 5}, {\"x\": -3, \"y\": -5, \"steps\": 6}, {\"x\": -2.999999999999999, \"y\": 3, \"steps\": 8}, {\"x\": -14, \"y\": 3.000000000000002, \"steps\": 11}, {\"x\": -13.999999999999996, \"y\": 30.000000000000004, \"steps\": 27}, {\"x\": -13.999999999999991, \"y\": 60, \"steps\": 30}, {\"x\": -19.999999999999993, \"y\": 60, \"steps\": 6}, {\"x\": -19.999999999999993, \"y\": 66, \"steps\": 6}, {\"x\": -36.99999999999999, \"y\": 66, \"steps\": 17}, {\"x\": -36.99999999999999, \"y\": 69, \"steps\": 3}, {\"x\": -36.99999999999999, \"y\": 72, \"steps\": 3}, {\"x\": -26.999999999999993, \"y\": 72, \"steps\": 10}, {\"x\": -15.999999999999991, \"y\": 72, \"steps\": 11}, {\"x\": -12.999999999999991, \"y\": 72, \"steps\": 3}, {\"x\": -12.999999999999991, \"y\": 62, \"steps\": 10}, {\"x\": -14.999999999999991, \"y\": 62, \"steps\": 2}], \"headings\": [0, 1.5707963267948966, 0, 4.71238898038469, 4.71238898038469, 4.71238898038469, 4.71238898038469, 4.71238898038469, 4.71238898038469, 4.71238898038469, 4.71238898038469, 3.141592653589793, 4.71238898038469, 3.141592653589793, 3.141592653589793, 3.141592653589793, 3.141592653589793, 3.141592653589793, 3.141592653589793, 3.141592653589793, 3.141592653589793, 3.141592653589793, 3.141592653589793, 1.5707963267948966, 0, 1.5707963267948966, 0, 0, 1.5707963267948966, 1.5707963267948966, 1.5707963267948966, 1.5707963267948966, 1.5707963267948966, 1.5707963267948966, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 3.141592653589793, 3.141592653589793, 4.71238898038469, 3.141592653589793, 4.71238898038469, 3.141592653589793, 3.141592653589793, 1.5707963267948966, 1.5707963267948966, 1.5707963267948966, 0, 4.71238898038469], \"visualScale\": 6.886944169949397, \"currentHeading\": 4.71238898038469}', '5 Mile', '2026-03-07 04:58:55', 'Pak’nSave', 1, 1),
(2, '{\"path\": [{\"x\": 0, \"y\": 0, \"steps\": 0}, {\"x\": 60, \"y\": -0.00000000000000367394039744206, \"steps\": 60}, {\"x\": 60.000000000000014, \"y\": 100, \"steps\": 100}, {\"x\": 10.000000000000014, \"y\": 100.0, \"steps\": 50}, {\"x\": 10.000000000000014, \"y\": 94.0, \"steps\": 6}, {\"x\": 50.000000000000014, \"y\": 94.0, \"steps\": 40}, {\"x\": 50.000000000000014, \"y\": 88.00000000000001, \"steps\": 6}, {\"x\": 10.000000000000014, \"y\": 88.00000000000003, \"steps\": 40}, {\"x\": 10.000000000000014, \"y\": 82.00000000000003, \"steps\": 6}, {\"x\": 50.000000000000014, \"y\": 82.00000000000003, \"steps\": 40}, {\"x\": 50.000000000000014, \"y\": 76.00000000000003, \"steps\": 6}, {\"x\": 10.000000000000014, \"y\": 76.00000000000004, \"steps\": 40}, {\"x\": 10.000000000000014, \"y\": 16.000000000000043, \"steps\": 60}, {\"x\": 0.000000000000014210854715202004, \"y\": 16.000000000000046, \"steps\": 10}], \"headings\": [0, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 0, 1.5707963267948966, 0, 4.71238898038469, 0, 1.5707963267948966, 0, 4.71238898038469, 0, 4.71238898038469], \"visualScale\": 7.745966692414835, \"currentHeading\": 4.71238898038469}', 'landscape', '2026-03-09 02:46:20', 'Raeward Fresh', 1, 1),
(5, '{\"path\": [{\"x\": 0, \"y\": 0, \"phase\": 1, \"steps\": 0}, {\"x\": 0, \"y\": -11, \"phase\": 1, \"steps\": 11}, {\"x\": 5, \"y\": -11, \"phase\": 1, \"steps\": 5}, {\"x\": 5.000000000000001, \"y\": -1, \"phase\": 1, \"steps\": 10}, {\"x\": 10, \"y\": -1.0000000000000002, \"phase\": 1, \"steps\": 5}, {\"x\": 10, \"y\": -9, \"phase\": 1, \"steps\": 8}, {\"x\": 46, \"y\": -9.000000000000002, \"phase\": 1, \"steps\": 36}, {\"x\": 46.00000000000001, \"y\": 29, \"phase\": 1, \"steps\": 38}, {\"x\": 41.00000000000001, \"y\": 29, \"phase\": 1, \"steps\": 5}, {\"x\": 41.000000000000014, \"y\": 59, \"phase\": 1, \"steps\": 30}, {\"x\": 10.000000000000014, \"y\": 59.00000000000001, \"phase\": 1, \"steps\": 31}, {\"x\": 10.000000000000014, \"y\": -0.9999999999999928, \"phase\": 1, \"steps\": 60}, {\"x\": 17.000000000000014, \"y\": -0.9999999999999932, \"phase\": 1, \"steps\": 7}, {\"x\": 17.000000000000014, \"y\": -8.999999999999993, \"phase\": 1, \"steps\": 8}, {\"x\": 23.000000000000018, \"y\": -8.999999999999993, \"phase\": 1, \"steps\": 6}, {\"x\": 23.000000000000018, \"y\": -0.9999999999999928, \"phase\": 1, \"steps\": 8}, {\"x\": 29.000000000000018, \"y\": -0.9999999999999932, \"phase\": 1, \"steps\": 6}, {\"x\": 29.000000000000018, \"y\": -8.999999999999993, \"phase\": 1, \"steps\": 8}, {\"x\": 34.000000000000014, \"y\": -8.999999999999993, \"phase\": 1, \"steps\": 5}, {\"x\": 34.000000000000014, \"y\": -0.9999999999999928, \"phase\": 1, \"steps\": 8}, {\"x\": 39.000000000000014, \"y\": -0.9999999999999932, \"phase\": 1, \"steps\": 5}, {\"x\": 39.000000000000014, \"y\": -8.999999999999993, \"phase\": 1, \"steps\": 8}, {\"x\": 46.000000000000014, \"y\": -8.999999999999993, \"phase\": 1, \"steps\": 7}, {\"x\": 46.000000000000014, \"y\": 4.000000000000007, \"phase\": 1, \"steps\": 13}, {\"x\": 10.000000000000014, \"y\": 4.000000000000013, \"phase\": 1, \"steps\": 36}, {\"x\": 10.000000000000014, \"y\": 9.000000000000014, \"phase\": 1, \"steps\": 5}, {\"x\": 46.000000000000014, \"y\": 9.000000000000012, \"phase\": 1, \"steps\": 36}, {\"x\": 46.000000000000014, \"y\": 14.000000000000012, \"phase\": 1, \"steps\": 5}, {\"x\": 10.000000000000014, \"y\": 14.00000000000002, \"phase\": 1, \"steps\": 36}, {\"x\": 10.000000000000014, \"y\": 19.00000000000002, \"phase\": 1, \"steps\": 5}, {\"x\": 46.000000000000014, \"y\": 19.000000000000018, \"phase\": 1, \"steps\": 36}, {\"x\": 46.000000000000014, \"y\": 24.000000000000018, \"phase\": 1, \"steps\": 5}, {\"x\": 10.000000000000014, \"y\": 24.000000000000025, \"phase\": 1, \"steps\": 36}, {\"x\": 10.000000000000014, \"y\": 31.000000000000025, \"phase\": 1, \"steps\": 7}, {\"x\": 41.000000000000014, \"y\": 31.00000000000002, \"phase\": 1, \"steps\": 31}, {\"x\": 41.000000000000014, \"y\": 37.00000000000002, \"phase\": 1, \"steps\": 6}, {\"x\": 10.000000000000014, \"y\": 37.00000000000003, \"phase\": 1, \"steps\": 31}, {\"x\": 10.000000000000014, \"y\": 42.00000000000003, \"phase\": 1, \"steps\": 5}, {\"x\": 41.000000000000014, \"y\": 42.00000000000003, \"phase\": 1, \"steps\": 31}, {\"x\": 41.000000000000014, \"y\": 47.00000000000003, \"phase\": 1, \"steps\": 5}, {\"x\": 10.000000000000014, \"y\": 47.000000000000036, \"phase\": 1, \"steps\": 31}, {\"x\": 10.000000000000014, \"y\": 52.000000000000036, \"phase\": 1, \"steps\": 5}, {\"x\": 41.000000000000014, \"y\": 52.000000000000036, \"phase\": 1, \"steps\": 31}, {\"x\": 41.000000000000014, \"y\": 49.000000000000036, \"phase\": 1, \"steps\": 3}, {\"x\": 46.000000000000014, \"y\": 49.000000000000036, \"phase\": 1, \"steps\": 5}, {\"x\": 46.000000000000014, \"y\": 59.000000000000036, \"phase\": 1, \"steps\": 10}, {\"x\": 41.000000000000014, \"y\": 59.000000000000036, \"phase\": 1, \"steps\": 5}], \"phase\": 1, \"headings\": [0, 0, 1.5707963267948966, 3.141592653589793, 1.5707963267948966, 0, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 3.141592653589793, 4.71238898038469, 0, 1.5707963267948966, 0, 1.5707963267948966, 3.141592653589793, 1.5707963267948966, 0, 1.5707963267948966, 3.141592653589793, 1.5707963267948966, 0, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 3.141592653589793, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 3.141592653589793, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 3.141592653589793, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 3.141592653589793, 1.5707963267948966, 3.141592653589793, 4.71238898038469, 3.141592653589793, 1.5707963267948966, 0, 1.5707963267948966, 3.141592653589793, 4.71238898038469], \"visualScale\": 5.674504383644445, \"currentHeading\": 4.71238898038469}', 'Frankton', '2026-03-10 23:50:25', 'New World', 1, 1);

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
-- Indexes for table `food_item`
--
ALTER TABLE `food_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AA3C8DCF696D413E` (`edge_id`),
  ADD KEY `IDX_AA3C8DCFA76ED395` (`user_id`);

--
-- Indexes for table `list_item`
--
ALTER TABLE `list_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5AD5FAF723245BF9` (`shopping_list_id`),
  ADD KEY `IDX_5AD5FAF75DF08E66` (`food_item_id`),
  ADD KEY `IDX_5AD5FAF7613FECDF` (`session_id`);

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
-- Indexes for table `shopping_session`
--
ALTER TABLE `shopping_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CECE98A623245BF9` (`shopping_list_id`),
  ADD KEY `IDX_CECE98A6EC001A52` (`current_node_id`),
  ADD KEY `IDX_CECE98A6933DE57C` (`supermarket_id`);

--
-- Indexes for table `supermarket`
--
ALTER TABLE `supermarket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D51643A2E393F4C1` (`entrance_node_id`),
  ADD KEY `IDX_D51643A282660BC1` (`walking_path_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD KEY `IDX_8D93D6498A51642B` (`last_used_supermarket_id`);

--
-- Indexes for table `walking_path`
--
ALTER TABLE `walking_path`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3E3FA3A8A76ED395` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `edge`
--
ALTER TABLE `edge`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=405;

--
-- AUTO_INCREMENT for table `food_item`
--
ALTER TABLE `food_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `list_item`
--
ALTER TABLE `list_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=441;

--
-- AUTO_INCREMENT for table `node`
--
ALTER TABLE `node`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=405;

--
-- AUTO_INCREMENT for table `owner_type`
--
ALTER TABLE `owner_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_placement`
--
ALTER TABLE `product_placement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `shelf`
--
ALTER TABLE `shelf`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `shopping_list`
--
ALTER TABLE `shopping_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shopping_session`
--
ALTER TABLE `shopping_session`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `supermarket`
--
ALTER TABLE `supermarket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `walking_path`
--
ALTER TABLE `walking_path`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `edge`
--
ALTER TABLE `edge`
  ADD CONSTRAINT `FK_7506D366623DF99B` FOREIGN KEY (`start_id`) REFERENCES `node` (`id`),
  ADD CONSTRAINT `FK_7506D366933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_7506D366E2BD8A10` FOREIGN KEY (`end_id`) REFERENCES `node` (`id`);

--
-- Constraints for table `food_item`
--
ALTER TABLE `food_item`
  ADD CONSTRAINT `FK_AA3C8DCF696D413E` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`),
  ADD CONSTRAINT `FK_AA3C8DCFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `list_item`
--
ALTER TABLE `list_item`
  ADD CONSTRAINT `FK_5AD5FAF723245BF9` FOREIGN KEY (`shopping_list_id`) REFERENCES `shopping_list` (`id`),
  ADD CONSTRAINT `FK_5AD5FAF75DF08E66` FOREIGN KEY (`food_item_id`) REFERENCES `food_item` (`id`),
  ADD CONSTRAINT `FK_5AD5FAF7613FECDF` FOREIGN KEY (`session_id`) REFERENCES `shopping_session` (`id`);

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `FK_857FE845933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_placement`
--
ALTER TABLE `product_placement`
  ADD CONSTRAINT `FK_267BC38339626D86` FOREIGN KEY (`superseded_by_id`) REFERENCES `product_placement` (`id`),
  ADD CONSTRAINT `FK_267BC3835DF08E66` FOREIGN KEY (`food_item_id`) REFERENCES `food_item` (`id`),
  ADD CONSTRAINT `FK_267BC38366290AB1` FOREIGN KEY (`suggested_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_267BC383696D413E` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`),
  ADD CONSTRAINT `FK_267BC383933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shelf`
--
ALTER TABLE `shelf`
  ADD CONSTRAINT `FK_A5475BE3933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_list`
--
ALTER TABLE `shopping_list`
  ADD CONSTRAINT `FK_3DC1A459A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `shopping_session`
--
ALTER TABLE `shopping_session`
  ADD CONSTRAINT `FK_CECE98A623245BF9` FOREIGN KEY (`shopping_list_id`) REFERENCES `shopping_list` (`id`),
  ADD CONSTRAINT `FK_CECE98A6933DE57C` FOREIGN KEY (`supermarket_id`) REFERENCES `supermarket` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CECE98A6EC001A52` FOREIGN KEY (`current_node_id`) REFERENCES `node` (`id`);

--
-- Constraints for table `supermarket`
--
ALTER TABLE `supermarket`
  ADD CONSTRAINT `FK_D51643A282660BC1` FOREIGN KEY (`walking_path_id`) REFERENCES `walking_path` (`id`),
  ADD CONSTRAINT `FK_D51643A2E393F4C1` FOREIGN KEY (`entrance_node_id`) REFERENCES `node` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D6498A51642B` FOREIGN KEY (`last_used_supermarket_id`) REFERENCES `supermarket` (`id`);

--
-- Constraints for table `walking_path`
--
ALTER TABLE `walking_path`
  ADD CONSTRAINT `FK_3E3FA3A8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
