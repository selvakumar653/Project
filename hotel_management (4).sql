-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 03:23 AM
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
-- Database: `hotel_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `type` enum('users','orders','inventory') NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `setting_name`, `setting_value`, `updated_at`) VALUES
(1, 'restaurant_name', 'Chellappa Hotel', '2025-04-14 01:05:44'),
(2, 'contact_email', 'contact@chellappahotel.com', '2025-04-14 01:05:44'),
(3, 'opening_hours', '7:00-22:00', '2025-04-14 01:05:44'),
(4, 'theme_color', '#8B0000', '2025-04-14 01:05:44'),
(5, 'maintenance_mode', 'false', '2025-04-14 01:05:44');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `order_id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `waiting_id` varchar(12) DEFAULT NULL,
  `location_type` enum('table','room','takeaway') NOT NULL,
  `location_number` int(11) NOT NULL,
  `arrival_time` time DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(15) DEFAULT NULL,
  `items` text NOT NULL,
  `item_ids` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`order_id`, `user_email`, `waiting_id`, `location_type`, `location_number`, `arrival_time`, `customer_name`, `customer_phone`, `items`, `item_ids`, `quantity`, `total_amount`, `status`, `order_date`) VALUES
(100218, 'selva@gmail.com', NULL, 'room', 582, NULL, 'selvakumar', NULL, 'Idli Sambar x2, Pongal x1', '4', 3, 200.00, 'completed', '2025-04-27 06:12:20'),
(100219, 'selva@gmail.com', NULL, 'room', 582, NULL, 'selvakumar', NULL, 'Idli Sambar x2', '4', 2, 120.00, 'pending', '2025-04-27 06:12:32'),
(100220, 'selva@gmail.com', NULL, 'room', 55, NULL, 'selvakumar', NULL, 'Ghee Roast Dosa x1, Idli Sambar x1, Pongal x1, Vada x1', '3', 4, 340.00, 'pending', '2025-04-27 06:15:36'),
(100221, 'selva@gmail.com', NULL, 'room', 555, NULL, 'selvakumar', NULL, 'Idli Sambar x1, Pongal x1', '4', 2, 140.00, 'pending', '2025-04-27 06:17:25'),
(100222, 'selva@gmail.com', NULL, 'room', 58, NULL, 'selvakumar', NULL, 'Ghee Roast Dosa x1, Idli Sambar x1', '3', 2, 210.00, 'completed', '2025-04-27 06:20:22'),
(100223, 'selva@gmail.com', NULL, 'room', 895, NULL, 'selvakumar', NULL, 'Ghee Roast Dosa x1, Idli Sambar x1', '3', 2, 210.00, 'pending', '2025-04-27 06:21:36'),
(100224, 'kumar@gmail.com', NULL, 'table', 65, NULL, 'kumar', NULL, 'Kuzhi Paniyaram x2', '123', 2, 12.00, '', '2025-04-30 12:42:21'),
(100225, 'kumar@gmail.com', NULL, 'room', 452, NULL, 'kumar', NULL, 'Idli with Sambar & Chutney x1, Kuzhi Paniyaram x1, Chapati with Vegetable Kurma x1', '122', 3, 18.00, 'completed', '2025-04-30 13:23:49'),
(100226, 'kumar@gmail.com', NULL, 'table', 88, NULL, 'kumar', NULL, 'Masala Dosai x1, Rava Upma x1', '121', 2, 12.00, 'completed', '2025-04-30 13:24:12'),
(100227, 'kumar@gmail.com', '202504301002', 'takeaway', 0, '22:54:00', 'selvakumar', '8940271739', 'Rava Upma x1, Idli with Sambar & Chutney x1', '125', 2, 10.00, 'completed', '2025-04-30 13:24:52');

--
-- Triggers `bills`
--
DELIMITER $$
CREATE TRIGGER `after_bill_insert_room_service` AFTER INSERT ON `bills` FOR EACH ROW BEGIN
    IF NEW.location_type = 'room' THEN
        INSERT INTO room_service (
            order_id,
            room_number,
            customer_name,
            order_date,
            items,
            total_amount,
            status
        ) VALUES (
            NEW.order_id,
            NEW.location_number,
            NEW.customer_name,
            NEW.order_date,
            NEW.items,
            NEW.total_amount,
            NEW.status
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_bill_insert_table_service` AFTER INSERT ON `bills` FOR EACH ROW BEGIN
    IF NEW.location_type = 'table' THEN
        INSERT INTO table_service (
            order_id,
            table_number,
            customer_name,
            order_date,
            items,
            total_amount,
            status
        ) VALUES (
            NEW.order_id,
            NEW.location_number,
            NEW.customer_name,
            NEW.order_date,
            NEW.items,
            NEW.total_amount,
            NEW.status
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_bill_insert_takeaway_service` AFTER INSERT ON `bills` FOR EACH ROW BEGIN
    IF NEW.location_type = 'takeaway' THEN
        INSERT INTO takeaway_service (
            order_id,
            customer_name,
            customer_phone,
            arrival_time,
            order_date,
            items,
            total_amount,
            status
        ) VALUES (
            NEW.order_id,
            NEW.customer_name,
            NEW.customer_phone,
            NEW.arrival_time,
            NEW.order_date,
            NEW.items,
            NEW.total_amount,
            NEW.status
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chef_tasks`
--

CREATE TABLE `chef_tasks` (
  `task_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `food_items` text NOT NULL,
  `source` enum('room_service','table_service','takeaway_service') NOT NULL,
  `completion_date` datetime DEFAULT current_timestamp(),
  `status` enum('Completed','Rejected','Delayed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chef_tasks`
--

INSERT INTO `chef_tasks` (`task_id`, `order_id`, `food_items`, `source`, `completion_date`, `status`) VALUES
(1, 62, 'Idli Sambar x2, Pongal x1', 'room_service', '2025-04-30 20:09:42', 'Completed'),
(2, 20, 'Masala Dosai x1, Rava Upma x1', 'table_service', '2025-04-30 20:18:50', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `available` int(255) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `alert_threshold` int(11) DEFAULT 5,
  `meal_time` enum('breakfast','lunch','dinner','all') DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `category`, `stock_quantity`, `image_url`, `available`, `created_at`, `alert_threshold`, `meal_time`) VALUES
(1, 'Chettinad Chicken', 'Spicy chicken curry with authentic Chettinad masala', 320.00, 'Main Course', 150, 'https://images.unsplash.com/photo-1631515243349-e0cb75fb8d3a', 1, '2025-04-13 20:16:51', 5, 'lunch'),
(2, 'Meen Kuzhambu', 'Traditional Tamil fish curry with tamarind', 280.00, 'Main Course', 30, 'https://www.foodiaq.com/wp-content/uploads/2024/05/meen-kulambu-1.jpg', 1, '2025-04-13 20:16:51', 5, 'lunch'),
(3, 'Ghee Roast Dosa', 'Crispy fermented rice crepe roasted in pure ghee', 150.00, 'Breakfast', 94, 'https://www.squarecut.net/wp-content/uploads/2024/08/crispy-crepes-made-barnyard-millets-lentils-commonly-known-as-milled-ghee-roast-dosa-plated-conical-shape-rolls-served-238893976.webp', 1, '2025-04-13 20:16:51', 5, 'breakfast'),
(4, 'Idli Sambar', 'Soft steamed rice cakes served with sambar and chutney', 60.00, 'Breakfast', 191, 'https://media.istockphoto.com/id/2159618247/photo/idli-vada-with-sambar.jpg?s=612x612&w=0&k=20&c=0HNP26WxESqfA3i3Xr1uTxxpKKYc69d9NRn9Dai4xok=', 1, '2025-04-13 20:20:14', 5, 'breakfast'),
(5, 'Pongal', 'Traditional rice and lentil dish with pepper and ghee', 80.00, 'Breakfast', 96, 'https://www.spiceindiaonline.com/wp-content/uploads/2014/01/Ven-Pongal-3.jpg', 1, '2025-04-13 20:20:14', 5, 'breakfast'),
(6, 'Vada', 'Crispy lentil donuts served with sambar and chutney', 50.00, 'Breakfast', 149, 'https://vaya.in/recipes/wp-content/uploads/2018/02/dreamstime_xs_44383666.jpg', 1, '2025-04-13 20:20:14', 5, 'breakfast'),
(7, 'Poori Masala', 'Fluffy deep-fried bread with potato masala', 90.00, 'Breakfast', 98, 'https://palakkadbusiness.com/Gangashankaram/wp-content/uploads/sites/79/2023/11/Poori-Masala.png', 1, '2025-04-13 20:20:14', 5, 'breakfast'),
(8, 'Paneer Butter Masala', 'Cottage cheese in rich tomato gravy', 180.00, 'Main Course', 80, 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgIhLcOIgSfPph9kwyJScX0oZOf9W6XT26Chnlc5uXPP4C8_52cTsozMURL_SDruHd-DQtC9GLHqWKFvqHvnWlsqULIkpwga-6KTUiXW1btD7KQI7oNmljdwykZ1WGZB7QZr8fsqGgqoy4/s2048/paneer+butter+masala+15.JPG', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(9, 'Vegetable Biryani', 'Fragrant rice cooked with mixed vegetables and spices', 160.00, 'Main Course', 100, 'https://media.istockphoto.com/id/179085494/photo/indian-biryani.jpg?s=612x612&w=0&k=20&c=VJAUfiuavFYB7PXwisvUhLqWFJ20-9m087-czUJp9Fs=', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(10, 'Dal Tadka', 'Yellow lentils tempered with spices', 140.00, 'Main Course', 90, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSY1l_jZRmr6YriO6mvEXEofhE1yhpb5HES1w&s', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(11, 'Kadai Mushroom', 'Mushrooms cooked with bell peppers in spicy gravy', 170.00, 'Main Course', 60, 'https://static.toiimg.com/photo/62997250.cms', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(12, 'Chicken 65', 'Spicy deep-fried chicken with curry leaves', 220.00, 'Main Course', 80, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQuBWfoWkhdZVPd16GGTM93qjTG7AWwPULmPA&s', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(13, 'Mutton Curry', 'Traditional Tamil-style mutton curry', 280.00, 'Main Course', 50, 'https://atanurrannagharrecipe.com/wp-content/uploads/2023/03/Best-Mutton-Curry-Recipe-Atanur-Rannaghar.jpg', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(14, 'Fish Curry', 'Fresh fish cooked in tangy tamarind sauce', 240.00, 'Main Course', 40, 'https://www.recipetineats.com/tachyon/2020/10/Goan-Fish-Curry_6-SQ.jpg', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(15, 'Prawn Masala', 'Prawns cooked in spicy masala gravy', 260.00, 'Main Course', 30, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_0Z0p1dWKj_Ltu3e_kEqMHAGy7HalMdX8oQ&s', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(16, 'Sambar Rice', 'Rice mixed with traditional lentil stew', 100.00, 'Rice', 150, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1VeM9HJJksvX4STFIUUyFUmMwRe3F_ddKag&s', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(17, 'Curd Rice', 'Yogurt rice tempered with mustard and curry leaves', 90.00, 'Rice', 100, 'https://maharajaroyaldining.com/wp-content/uploads/2024/03/Curd-Rice-1.webp', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(18, 'Lemon Rice', 'Tangy rice with peanuts and curry leaves', 95.00, 'Rice', 100, 'https://www.flavourstreat.com/wp-content/uploads/2020/12/turmeric-lemon-rice-recipe-02.jpg', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(19, 'Coconut Rice', 'Rice flavored with fresh coconut and spices', 100.00, 'Rice', 80, 'https://static.toiimg.com/thumb/52413325.cms?imgsize=190896&width=800&height=800', 1, '2025-04-13 20:20:14', 5, 'lunch'),
(20, 'Parotta', 'Flaky layered flatbread', 40.00, 'Breads', 200, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQl2ExoO9ArN3mJ13eP-4AoLHhmgYrDGXCL4Q&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(21, 'Naan', 'Tandoor-baked flatbread', 45.00, 'Breads', 150, 'https://www.thespruceeats.com/thmb/MReCj8olqrCsPaGvikesPJie02U=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/naan-leavened-indian-flatbread-1957348-final-08-116a2e523f6e4ee693b1a9655784d9b9.jpg', 1, '2025-04-13 20:20:14', 5, 'all'),
(22, 'Chapati', 'Whole wheat flatbread', 35.00, 'Breads', 200, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_freDZrX7LsLnPPwG27dGa443MeYjcsE_mQ&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(23, 'Butter Roti', 'Whole wheat bread with butter', 40.00, 'Breads', 150, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGFVv0p_3-hNCCIR_1gVoJoXv7YHTCbYzQGw&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(24, 'Gulab Jamun', 'Sweet milk dumplings in sugar syrup', 80.00, 'Desserts', 98, 'https://carveyourcraving.com/wp-content/uploads/2020/09/gulab-jamun-mousse-layered-dessert.jpg', 1, '2025-04-13 20:20:14', 5, 'all'),
(25, 'Payasam', 'Traditional South Indian sweet pudding', 90.00, 'Desserts', 77, 'https://www.whiskaffair.com/wp-content/uploads/2020/11/Semiya-Payasam-2-3.jpg', 1, '2025-04-13 20:20:14', 5, 'all'),
(26, 'Rasmalai', 'Soft cottage cheese dumplings in sweet milk', 100.00, 'Desserts', 60, 'https://prashantcorner.com/cdn/shop/files/RasmalaiSR-2.png?v=1720595089&width=1946', 1, '2025-04-13 20:20:14', 5, 'all'),
(27, 'Jalebi', 'Crispy spiral sweets in sugar syrup', 70.00, 'Desserts', 90, 'https://static.toiimg.com/thumb/53099699.cms?imgsize=182393&width=800&height=800', 1, '2025-04-13 20:20:14', 5, 'all'),
(28, 'Masala Chai', 'Indian spiced tea', 30.00, 'Beverages', 200, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR8LMM6j0uSjpwGASdoFVtMLW_iojIyFp6ZfQ&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(29, 'Filter Coffee', 'Traditional South Indian coffee', 35.00, 'Beverages', 200, 'https://www.clubmahindra.com/blog/media/section_images/indianfilt-351110d18aec48f.jpg', 1, '2025-04-13 20:20:14', 5, 'all'),
(30, 'Buttermilk', 'Spiced yogurt drink', 25.00, 'Beverages', 150, 'https://static.toiimg.com/thumb/msid-76625491,imgsize-957295,width-400,resizemode-4/76625491.jpg', 1, '2025-04-13 20:20:14', 5, 'all'),
(31, 'Fresh Lime Soda', 'Refreshing lime-based drink', 40.00, 'Beverages', 100, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQIxacpaTgsCyexsCPzWztI8aIFGqnZ3bAKzA&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(32, 'Onion Pakoda', 'Crispy onion fritters', 80.00, 'Starters', 100, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTIT8uIqE1VMbvmPrCEQr_Pm7_t9JT486YuxQ&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(33, 'Paneer 65', 'Spicy cottage cheese starter', 160.00, 'Starters', 80, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQX-aS8DhH5p_6IFqic0Y4WAfLnbvOjRVkaGA&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(34, 'Gobi Manchurian', 'Indo-Chinese cauliflower fritters', 140.00, 'Starters', 90, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJSTFk6lnhBZh05OqwHyuyjjzhrL6321XVUw&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(35, 'Papad', 'Crispy lentil wafers', 20.00, 'Sides', 300, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQgIjv2Y9pyRYsvpuwQI4DTQ3Qc4YfqGpzXYQ&s', 1, '2025-04-13 20:20:14', 5, 'all'),
(88, 'Pancakes', 'Fluffy pancakes served with syrup and butter', 5.99, 'dessert', 150, 'https://www.savoryexperiments.com/wp-content/uploads/2022/01/Funfetti-Pancakes-5.jpg', 1, '2025-04-29 11:09:00', 5, 'all'),
(89, 'Omelette', 'Three-egg omelette with cheese and vegetables', 6.49, 'breakfast', 150, 'https://www.allrecipes.com/thmb/xb0_9ETJEeeld-xZTfOHGvR446s=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/262697ham-and-cheese-omelettefabeveryday4x3-831275518e14417a9c1f695ce59e24d3.jpg', 1, '2025-04-29 11:09:00', 5, 'breakfast'),
(90, 'Grilled Chicken Sandwich', 'Served with fries and salad', 9.99, 'Main Course', 150, 'https://www.chicken.ca/wp-content/uploads/2020/09/Canadian-BBQ.jpg', 1, '2025-04-29 11:09:00', 5, 'lunch'),
(91, 'Caesar Salad', 'Classic Caesar salad with croutons and parmesan', 7.99, 'salad', 150, 'https://assets.farmison.com/images/recipe-detail-1380/74550-classic-chicken-caesar-salad.jpg', 1, '2025-04-29 11:09:00', 5, 'lunch'),
(92, 'Steak with Mashed Potatoes', 'Grilled steak with creamy mashed potatoes', 14.99, 'Main Course', 150, 'https://i.pinimg.com/736x/34/07/f5/3407f5e6e9714a0ef2d3646cdd903467.jpg', 1, '2025-04-29 11:09:00', 5, 'lunch'),
(93, 'Spaghetti Bolognese', 'Traditional Italian pasta with meat sauce', 11.49, 'Main Course', 150, 'https://www.kitchensanctuary.com/wp-content/uploads/2019/09/Spaghetti-Bolognese-square-FS-0204.jpg', 1, '2025-04-29 11:09:00', 5, 'lunch'),
(94, 'Chocolate Cake', 'Rich chocolate cake with ganache frosting', 4.99, 'dessert', 150, NULL, 1, '2025-04-29 11:09:00', 5, 'all'),
(95, 'Ice Cream Sundae', 'Vanilla ice cream with chocolate syrup and nuts', 3.99, 'dessert', 150, NULL, 1, '2025-04-29 11:09:00', 5, 'all'),
(96, 'Pancakes', 'Fluffy pancakes with maple syrup', 5.99, 'breakfast', 150, 'https://www.savoryexperiments.com/wp-content/uploads/2022/01/Funfetti-Pancakes-5.jpg', 1, '2025-04-29 11:12:25', 5, 'breakfast'),
(97, 'Omelette', 'Three-egg omelette with veggies and cheese', 6.49, 'breakfast', 150, 'https://www.allrecipes.com/thmb/xb0_9ETJEeeld-xZTfOHGvR446s=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/262697ham-and-cheese-omelettefabeveryday4x3-831275518e14417a9c1f695ce59e24d3.jpg', 1, '2025-04-29 11:12:25', 5, 'breakfast'),
(98, 'French Toast', 'Brioche toast with cinnamon and syrup', 5.79, 'breakfast', 150, 'https://somebodyfeedseb.com/wp-content/uploads/2023/02/2022.06.11-Savory-French-Toast-1128.jpg', 1, '2025-04-29 11:12:25', 5, 'breakfast'),
(99, 'Breakfast Burrito', 'Eggs, sausage, and cheese wrapped in a tortilla', 6.99, 'breakfast', 150, 'https://www.makeaheadmealmom.com/wp-content/uploads/2023/08/BreakfastBurritos_Featured_compressed.jpg', 1, '2025-04-29 11:12:25', 5, 'breakfast'),
(100, 'Granola Parfait', 'Granola, yogurt, and fresh berries', 4.99, 'breakfast', 150, 'https://newsite.susanjoyfultable.com/site/assets/files/1339/chia_and_granola_parfait-1.jpg', 1, '2025-04-29 11:12:25', 5, 'breakfast'),
(101, 'Avocado Toast', 'Sourdough topped with smashed avocado and eggs', 7.49, 'breakfast', 150, 'https://californiaavocado.com/wp-content/uploads/2020/07/California-Avocado-Toast-Three-Ways.jpeg', 1, '2025-04-29 11:12:25', 5, 'breakfast'),
(102, 'Grilled Chicken Sandwich', 'Chicken breast with lettuce, tomato, and mayo', 9.99, 'Main Course', 150, 'https://www.chicken.ca/wp-content/uploads/2020/09/Canadian-BBQ.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(103, 'Caesar Salad', 'Crisp romaine with Caesar dressing and croutons', 7.99, 'salad', 150, 'https://assets.farmison.com/images/recipe-detail-1380/74550-classic-chicken-caesar-salad.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(104, 'Club Sandwich', 'Triple-decker sandwich with turkey, bacon, and lettuce', 8.99, 'Main Course', 150, 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/club_sandwich_16496_16x9.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(105, 'Veggie Wrap', 'Grilled vegetables and hummus in a wrap', 7.49, 'Main Course', 150, 'https://s.lightorangebean.com/media/20240914152454/Fresh-Veggie-Hummus-Wrap_-done-830x521.png', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(106, 'Cheeseburger', 'Beef patty with cheese, pickles, and ketchup', 9.49, 'Main Course', 150, 'https://www.awesomecuisine.com/wp-content/uploads/2014/01/Double-Cheeseburger.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(107, 'Tomato Soup', 'Creamy tomato soup served with bread', 4.99, 'soup', 150, 'https://mahatmarice.com/wp-content/uploads/2019/08/Chicken-Tomato-Basil-Rice-Soup.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(108, 'Chicken Quesadilla', 'Cheese-filled tortilla with grilled chicken', 8.49, 'Main Course', 150, 'https://www.foodnetwork.com/content/dam/images/food/fullset/2013/2/5/1/WU0404H_chicken-quesadillas-recipe_s4x3.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(109, 'Steak with Mashed Potatoes', 'Grilled sirloin with sides', 14.99, 'Main Course', 150, 'https://i.pinimg.com/736x/34/07/f5/3407f5e6e9714a0ef2d3646cdd903467.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(110, 'Spaghetti Bolognese', 'Pasta with rich meat sauce', 11.49, 'Main Course', 150, 'https://www.kitchensanctuary.com/wp-content/uploads/2019/09/Spaghetti-Bolognese-square-FS-0204.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(111, 'Grilled Salmon', 'Salmon filet with lemon butter sauce', 13.99, 'Main Course', 150, 'https://lifeloveandgoodfood.com/wp-content/uploads/2015/11/Best-Grilled-Salmon_9331-2.jpg', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(112, 'Chicken Alfredo', 'Fettuccine in creamy Alfredo sauce with chicken', 12.49, 'Main Course', 150, 'https://www.slimmingeats.com/blog/wp-content/uploads/2024/01/cajun-chicken-alfredo-3.jpg', 127, '2025-04-29 11:12:25', 5, 'lunch'),
(113, 'Vegetable Stir Fry', 'Seasonal vegetables in soy-ginger sauce', 10.49, 'vegetarian', 150, 'https://playswellwithbutter.com/wp-content/uploads/2025/02/Vegetable-Stir-Fried-Noodles-17.jpg', 1, '2025-04-29 11:12:25', 5, 'dinner'),
(114, 'Beef Tacos', 'Soft tortillas filled with seasoned beef and toppings', 9.99, 'Main Course', 150, 'https://oliviaadriance.com/wp-content/uploads/2023/07/Final_3_Crispy_Baked_Beef_Tacos_grain-free-dairy-free.jpg.webp', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(115, 'Lamb Curry', 'Spiced lamb curry with basmati rice', 13.49, 'Main Course', 150, 'https://www.ocado.com/cmscontent/recipe_image_large/34731104.jpg?bQAP', 1, '2025-04-29 11:12:25', 5, 'lunch'),
(116, 'Chocolate Cake', 'Moist chocolate cake with ganache', 4.99, 'dessert', 150, NULL, 1, '2025-04-29 11:12:25', 5, 'all'),
(117, 'Ice Cream Sundae', 'Vanilla ice cream with toppings', 3.99, 'dessert', 150, NULL, 1, '2025-04-29 11:12:25', 5, 'all'),
(118, 'Cheesecake', 'Classic New York-style cheesecake', 5.49, 'dessert', 150, NULL, 1, '2025-04-29 11:12:25', 5, 'all'),
(119, 'Fruit Salad', 'Fresh mixed fruits', 3.49, 'dessert', 150, NULL, 1, '2025-04-29 11:12:25', 5, 'all'),
(120, 'Brownie', 'Fudgy brownie with chocolate chips', 3.99, 'dessert', 150, NULL, 1, '2025-04-29 11:12:25', 5, 'all'),
(121, 'Masala Dosai', 'Crispy dosa filled with spiced potato masala', 7.49, 'light meals', 149, 'https://vismaifood.com/storage/app/uploads/public/fc8/6e9/476/thumb__700_0_0_0_auto.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner'),
(122, 'Idli with Sambar & Chutney', 'Steamed rice cakes served with hot sambar and chutneys', 5.99, 'light meals', 148, 'https://storypick.com/wp-content/uploads/2018/03/idli-sambar.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner'),
(123, 'Kuzhi Paniyaram', 'Crispy fermented rice dumplings served with chutney', 6.49, 'light meals', 147, 'https://images.news18.com/webstories/uploads/2024/10/20220210_1952372-2024-10-ae64828ee36d05b56496a715affe9e59.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner'),
(124, 'Chapati with Vegetable Kurma', 'Whole wheat flatbread with coconut-based veg curry', 7.99, 'light meals', 149, 'https://sangskitchen.b-cdn.net/wp-content/uploads/2018/08/Veg-kurma-thumbnail.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner'),
(125, 'Rava Upma', 'Semolina upma with mustard, ginger, and veggies', 5.49, 'light meals', 148, NULL, 1, '2025-04-29 13:57:28', 5, 'dinner'),
(126, 'Parotta with Chicken Salna', 'Flaky parotta served with spicy chicken gravy', 9.99, 'non-veg street', 150, NULL, 1, '2025-04-29 13:57:28', 5, 'dinner'),
(127, 'Kothu Parotta (Chicken)', 'Minced parotta stir-fried with egg, chicken, and masala', 10.49, 'non-veg street', 150, 'https://i.pinimg.com/736x/70/62/6a/70626ace5b289ec60cae9999bc80fcef.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner'),
(128, 'Veg Kothu Parotta', 'Minced parotta stir-fried with vegetables and spices', 9.49, 'veg street', 150, 'https://images.herzindagi.info/image/2024/Apr/Kothu-Parotta.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner'),
(129, 'Egg Masala Curry', 'Boiled eggs in a thick onion-tomato gravy', 8.49, 'egg curry', 150, 'https://eggs.ca/wp-content/uploads/2024/06/Kerala-Coconut-Egg-Curry-1664x834-1.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner'),
(130, 'Nethili Meen Fry', 'Crispy anchovy fish fry with curry leaves and masala', 9.49, 'non-veg fry', 150, 'https://desertfoodfeed.com/wp-content/uploads/2020/08/nethili-fry2-3-800x620.jpg', 1, '2025-04-29 13:57:28', 5, 'dinner');

-- --------------------------------------------------------

--
-- Table structure for table `onwaycust`
--

CREATE TABLE `onwaycust` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `waiting_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `status` enum('pending','ready','collected') DEFAULT 'pending',
  `order_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `user_email` varchar(255) DEFAULT NULL,
  `location_type` enum('table','room','takeaway') DEFAULT NULL,
  `location_number` int(11) DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `status` enum('pending','confirmed','preparing','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_service`
--

CREATE TABLE `room_service` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_service`
--

INSERT INTO `room_service` (`id`, `order_id`, `room_number`, `customer_name`, `order_date`, `items`, `total_amount`, `status`, `created_at`) VALUES
(62, 100218, '582', 'selvakumar', '2025-04-27 11:42:20', 'Idli Sambar x2, Pongal x1', 200.00, 'Completed', '2025-04-27 06:12:20'),
(63, 100219, '582', 'selvakumar', '2025-04-27 11:42:32', 'Idli Sambar x2', 120.00, 'pending', '2025-04-27 06:12:32'),
(64, 100220, '55', 'selvakumar', '2025-04-27 11:45:36', 'Ghee Roast Dosa x1, Idli Sambar x1, Pongal x1, Vada x1', 340.00, 'pending', '2025-04-27 06:15:36'),
(65, 100221, '555', 'selvakumar', '2025-04-27 11:47:25', 'Idli Sambar x1, Pongal x1', 140.00, 'pending', '2025-04-27 06:17:25'),
(66, 100222, '58', 'selvakumar', '2025-04-27 11:50:22', 'Ghee Roast Dosa x1, Idli Sambar x1', 210.00, 'completed', '2025-04-27 06:20:22'),
(67, 100223, '895', 'selvakumar', '2025-04-27 11:51:36', 'Ghee Roast Dosa x1, Idli Sambar x1', 210.00, 'completed', '2025-04-27 06:21:36'),
(68, 100225, '452', 'kumar', '2025-04-30 18:53:49', 'Idli with Sambar & Chutney x1, Kuzhi Paniyaram x1, Chapati with Vegetable Kurma x1', 18.00, 'completed', '2025-04-30 13:23:49');

--
-- Triggers `room_service`
--
DELIMITER $$
CREATE TRIGGER `trg_update_bills_from_room` AFTER UPDATE ON `room_service` FOR EACH ROW BEGIN
    UPDATE bills
    SET status = NEW.status
    WHERE order_id = NEW.order_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `table_service`
--

CREATE TABLE `table_service` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `table_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_service`
--

INSERT INTO `table_service` (`id`, `order_id`, `table_number`, `customer_name`, `order_date`, `items`, `total_amount`, `status`, `created_at`) VALUES
(19, 100224, '65', 'kumar', '2025-04-30 18:12:21', 'Kuzhi Paniyaram x2', 12.00, 'In Progress', '2025-04-30 12:42:21'),
(20, 100226, '88', 'kumar', '2025-04-30 18:54:12', 'Masala Dosai x1, Rava Upma x1', 12.00, 'Completed', '2025-04-30 13:24:12');

--
-- Triggers `table_service`
--
DELIMITER $$
CREATE TRIGGER `trg_update_bills_from_table` AFTER UPDATE ON `table_service` FOR EACH ROW BEGIN
    UPDATE bills
    SET status = NEW.status
    WHERE order_id = NEW.order_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `takeaway_service`
--

CREATE TABLE `takeaway_service` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `takeaway_service`
--

INSERT INTO `takeaway_service` (`id`, `order_id`, `customer_name`, `customer_phone`, `arrival_time`, `order_date`, `items`, `total_amount`, `status`, `created_at`) VALUES
(7, 100227, 'selvakumar', '8940271739', '22:54:00', '2025-04-30 18:54:52', 'Rava Upma x1, Idli with Sambar & Chutney x1', 10.00, 'completed', '2025-04-30 13:24:52');

--
-- Triggers `takeaway_service`
--
DELIMITER $$
CREATE TRIGGER `trg_update_bills_from_takeaway` AFTER UPDATE ON `takeaway_service` FOR EACH ROW BEGIN
    UPDATE bills
    SET status = NEW.status
    WHERE order_id = NEW.order_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'guest'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `room_number`, `password`, `created_at`, `username`, `role`) VALUES
(24, 'selvakumar', 'selva@gmail.com', '06379516896', '100', '$2y$10$oOQ3tDIfHtHy/xYUErcVv.n6l1YtL7uk67pZH2BXOAkP.20YQTzy6', '2025-04-25 19:14:52', NULL, 'guest'),
(27, 'kumar', 'kumar@gmail.com', '8940271739', '54', '$2y$10$7.DbxV4ns1prnsePNUwxf.1ReTdCcnLVF9te3JsDF8q2fXI38BrhS', '2025-04-30 18:09:07', NULL, 'guest');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `chef_tasks`
--
ALTER TABLE `chef_tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onwaycust`
--
ALTER TABLE `onwaycust`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `room_service`
--
ALTER TABLE `room_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `table_service`
--
ALTER TABLE `table_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `takeaway_service`
--
ALTER TABLE `takeaway_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100228;

--
-- AUTO_INCREMENT for table `chef_tasks`
--
ALTER TABLE `chef_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `onwaycust`
--
ALTER TABLE `onwaycust`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_service`
--
ALTER TABLE `room_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `table_service`
--
ALTER TABLE `table_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `takeaway_service`
--
ALTER TABLE `takeaway_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `onwaycust`
--
ALTER TABLE `onwaycust`
  ADD CONSTRAINT `onwaycust_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_service`
--
ALTER TABLE `room_service`
  ADD CONSTRAINT `room_service_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `bills` (`order_id`);

--
-- Constraints for table `table_service`
--
ALTER TABLE `table_service`
  ADD CONSTRAINT `table_service_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `bills` (`order_id`);

--
-- Constraints for table `takeaway_service`
--
ALTER TABLE `takeaway_service`
  ADD CONSTRAINT `takeaway_service_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `bills` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
