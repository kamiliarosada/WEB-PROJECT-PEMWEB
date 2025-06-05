-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 11:54 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pemweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `service_category_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `category_id`, `service_category_id`, `author_id`, `image_url`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 'How to Maintain Your Smartphone', 'Content about smartphone maintenance...', NULL, 1, 1, NULL, 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(2, 'Common Refrigerator Problems', 'Content about refrigerator issues...', NULL, 2, 1, NULL, 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(3, 'Basic Plumbing Tips', 'Content about plumbing maintenance...', NULL, 3, 1, NULL, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(4, 'Electrical Safety at Home', 'Content about electrical safety...', NULL, 4, 1, NULL, 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `technician_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_types`
--

CREATE TABLE `item_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `service_category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_types`
--

INSERT INTO `item_types` (`id`, `name`, `description`, `service_category_id`, `created_at`, `updated_at`) VALUES
(1, 'Smartphone', 'Mobile phone repair', 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(2, 'Laptop', 'Laptop and computer repair', 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(3, 'Refrigerator', 'Refrigerator repair', 2, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(4, 'Washing Machine', 'Washing machine repair', 2, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(5, 'Toilet', 'Toilet repair and installation', 3, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(6, 'Faucet', 'Faucet repair and installation', 3, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(7, 'Wiring', 'Electrical wiring services', 4, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(8, 'Lighting', 'Lighting installation and repair', 4, '2025-05-30 06:53:45', '2025-05-30 06:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `item_type_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `feedback_id` int(11) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `technician_id`, `item_type_id`, `description`, `status`, `total_amount`, `feedback_id`, `is_verified`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 'Screen replacement for iPhone 12', 'completed', '120.00', NULL, 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(2, 2, NULL, 3, 'Refrigerator not cooling', 'pending', NULL, NULL, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(3, 3, 2, 4, 'Washing machine leaking', 'processing', '85.00', NULL, 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(4, 3, NULL, 6, 'Kitchen faucet replacement', 'pending', NULL, NULL, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', 'Repair services for electronic devices', '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(2, 'Appliances', 'Repair services for home appliances', '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(3, 'Plumbing', 'Plumbing services and repairs', '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(4, 'Electrical', 'Electrical system services and repairs', '2025-05-30 06:53:45', '2025-05-30 06:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `technicians`
--

CREATE TABLE `technicians` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `certification` varchar(255) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT 0.00,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `technicians`
--

INSERT INTO `technicians` (`id`, `user_id`, `specialization`, `experience_years`, `certification`, `rate`, `is_verified`, `created_at`, `updated_at`) VALUES
(1, 4, 'Electronics Repair', 5, 'Certified Electronics Technician', '25.00', 1, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(2, 5, 'Appliance Repair', 3, 'Appliance Repair Certification', '20.00', 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('admin','technician','user') DEFAULT 'user',
  `is_verified` tinyint(1) DEFAULT 0,
  `is_suspended` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `phone`, `country`, `gender`, `profile_picture`, `role`, `is_verified`, `is_suspended`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'admin@locals.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, 'admin', 1, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(2, 'John Doe', 'johndoe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456789', 'USA', 'male', NULL, 'user', 1, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(3, 'Jane Smith', 'janesmith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654321', 'UK', 'female', NULL, 'user', 1, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(4, 'Mike Johnson', 'mikej', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555123456', 'Canada', 'male', NULL, 'technician', 1, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(5, 'Sarah Williams', 'sarahw', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555987654', 'Australia', 'female', NULL, 'technician', 0, 0, '2025-05-30 06:53:45', '2025-05-30 06:53:45'),
(6, 'ocha', 'ocha', 'ocha@gmail', '$2y$10$1MnojEFg88nZn5UePRsWyeB8K8QZugWX6V1Jy.z8rHQUPSIBICOLi', '12345567889', 'Indonesia', 'female', 'semangka.jpg', 'user', 0, 0, '2025-06-03 05:27:10', '2025-06-03 05:27:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `service_category_id` (`service_category_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `technician_id` (`technician_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `item_types`
--
ALTER TABLE `item_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_category_id` (`service_category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `technician_id` (`technician_id`),
  ADD KEY `item_type_id` (`item_type_id`),
  ADD KEY `fk_feedback` (`feedback_id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `technicians`
--
ALTER TABLE `technicians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_types`
--
ALTER TABLE `item_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `technicians`
--
ALTER TABLE `technicians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_types`
--
ALTER TABLE `item_types`
  ADD CONSTRAINT `item_types_ibfk_1` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_feedback` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`item_type_id`) REFERENCES `item_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `technicians`
--
ALTER TABLE `technicians`
  ADD CONSTRAINT `technicians_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
