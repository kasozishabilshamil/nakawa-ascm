-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2026 at 01:26 AM
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
-- Database: `nakawa_ascm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `logistics`
--

CREATE TABLE `logistics` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transporter_id` int(11) NOT NULL,
  `pickup_location` varchar(100) NOT NULL,
  `delivery_location` varchar(100) NOT NULL,
  `status` enum('assigned','picked_up','in_transit','delivered') DEFAULT 'assigned',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logistics`
--

INSERT INTO `logistics` (`id`, `order_id`, `transporter_id`, `pickup_location`, `delivery_location`, `status`, `updated_at`) VALUES
(1, 1, 5, 'Nakawa Market', 'Kampala Center', 'delivered', '2026-11-22 19:20:44');

-- --------------------------------------------------------

--
-- Table structure for table `market_prices`
--

CREATE TABLE `market_prices` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `market_prices`
--

INSERT INTO `market_prices` (`id`, `product_name`, `current_price`, `date_updated`) VALUES
(1, 'Tomatoes', 4500.00, '2026-11-22 23:23:13'),
(2, '1kg of Meat', 21000.00, '2026-11-22 23:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `produce_id` int(11) NOT NULL,
  `quantity_ordered` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_status` enum('pending','accepted','in_transit','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `vendor_id`, `produce_id`, `quantity_ordered`, `total_price`, `order_status`, `created_at`) VALUES
(1, 4, 3, 1.00, 599.00, 'delivered', '2026-11-22 19:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `produce`
--

CREATE TABLE `produce` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `status` enum('available','sold_out') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produce`
--

INSERT INTO `produce` (`id`, `farmer_id`, `product_name`, `category`, `quantity`, `unit`, `price_per_unit`, `status`, `created_at`) VALUES
(1, 2, 'Fresh Tomatoes', 'Vegetables', 100.00, 'Kg', 3000.00, 'available', '2026-11-22 18:15:20'),
(2, 2, 'Matooke', 'Fruits', 50.00, 'Bunch', 15000.00, 'available', '2026-11-22 18:15:20'),
(3, 3, 'maize', 'Grains', 55.00, 'Kg', 599.00, 'available', '2026-11-22 19:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('farmer','vendor','transporter','admin') NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `location`, `created_at`) VALUES
(1, 'Admin User', 'admin@nakawa.com', '0700000000', 'password123', 'admin', 'Nakawa Market', '2026-11-22 18:15:20'),
(2, 'David Kurama', 'david@farmer.com', '0711111111', 'password123', 'farmer', 'Nakawa Division', '2026-11-22 18:15:20'),
(3, 'shabil shamil', 'nabilkalz15@gmail.com', '0756126040', '$2y$10$QiAhhkg0fa9xD8hTQ58U3eYUTvoOtFqaM6oSbzSsa1JtvV0QCmLPi', 'farmer', 'Nakawa Division', '2026-11-22 18:26:31'),
(4, 'Nakawa Vendor', 'vendor@test.com', '0722222222', '$2y$10$f5pA.PnCUA/qb6WpxrLWwOKoxuOg3ZCUdgoGgXf8GN1OHuWvFDDr.', 'vendor', 'Nakawa Division', '2026-11-22 19:27:52'),
(5, 'Fast Transporter', 'transport@test.com', '0722222222', '$2y$10$eVKNI5sLOgsiMfYn0g.LPOF9Uq0buRnhZxSlrjFFLALVEpmUa3g0m', 'transporter', 'Nakawa Division', '2026-11-22 19:19:32'),
(6, 'Admin Test', 'admin@test.com', '0700111222', '$2y$10$nOJnSWCY91nBUlZ13IyUDO3baMjcneNclMGvsVldTEvkfKh2xHETW', 'admin', 'Nakawa Market', '2026-11-22 22:09:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logistics`
--
ALTER TABLE `logistics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `transporter_id` (`transporter_id`);

--
-- Indexes for table `market_prices`
--
ALTER TABLE `market_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `produce_id` (`produce_id`);

--
-- Indexes for table `produce`
--
ALTER TABLE `produce`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logistics`
--
ALTER TABLE `logistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `market_prices`
--
ALTER TABLE `market_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `produce`
--
ALTER TABLE `produce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logistics`
--
ALTER TABLE `logistics`
  ADD CONSTRAINT `logistics_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `logistics_ibfk_2` FOREIGN KEY (`transporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`produce_id`) REFERENCES `produce` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produce`
--
ALTER TABLE `produce`
  ADD CONSTRAINT `produce_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
