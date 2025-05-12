-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 10:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medical_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `disease_medicine_mapping`
--

CREATE TABLE `disease_medicine_mapping` (
  `id` int(11) NOT NULL,
  `disease` varchar(255) NOT NULL,
  `medicine_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disease_medicine_mapping`
--

INSERT INTO `disease_medicine_mapping` (`id`, `disease`, `medicine_id`) VALUES
(16, 'Common Cold', 27),
(17, 'Common Cold', 28),
(18, 'Common Cold', 29),
(19, 'Common Cold', 34),
(20, 'Fever', 25),
(21, 'Fever', 32),
(22, 'Headache', 25),
(23, 'Headache', 26),
(24, 'Body Pain', 26),
(25, 'Body Pain', 33),
(26, 'Body Pain', 32),
(27, 'Strep Throat', 30),
(28, 'Strep Throat', 31);

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `expiry_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `type`, `expiry_date`, `price`, `stock`) VALUES
(1, 'Syndopa Plus Tablet', 'Tablet', '2025-07-26', 35.00, 1),
(2, 'Prax A 75 Capsule', 'Capsule', '2025-10-10', 250.00, 1),
(3, 'Benadryl DR Syrup', 'Syrup', '2025-12-12', 110.00, 3),
(4, 'Omez D -Tablet', 'Tablet', '2025-10-13', 125.00, 1),
(25, 'Paracetamol', 'Tablet', '2026-05-11', 10.00, 98),
(26, 'Ibuprofen', 'Tablet', '2026-05-11', 15.00, 98),
(27, 'Cetirizine', 'Tablet', '2026-05-11', 8.00, 99),
(28, 'Loratadine', 'Tablet', '2026-05-11', 12.00, 99),
(29, 'Cough Syrup (Dextromethorphan)', 'Syrup', '2026-05-11', 50.00, 95),
(30, 'Amoxicillin', 'Capsule', '2026-05-11', 20.00, 98),
(31, 'Throat Lozenges (Benzocaine)', 'Tablet', '2026-05-11', 5.00, 100),
(32, 'Aspirin', 'Tablet', '2026-05-11', 10.00, 98),
(33, 'Naproxen', 'Tablet', '2026-05-11', 18.00, 100),
(34, 'Pseudoephedrine', 'Tablet', '2026-05-11', 15.00, 99);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `discount` decimal(5,2) DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL,
  `sale_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_name`, `customer_phone`, `doctor_name`, `discount`, `total_price`, `sale_date`) VALUES
(1, 'Test User', '12345678971', 'Dr.John', 70.00, 21.00, '2025-05-10 17:37:16'),
(2, 'John Doe', '1234567810', 'Dr.John', 20.00, 408.00, '2025-05-10 18:15:07'),
(3, 'Test User', '12345678971', 'Dr.John', 20.00, 400.00, '2025-05-10 18:44:51'),
(4, 'Test User', '12345678971', 'Dr.John', 0.00, 35.00, '2025-05-10 22:22:01'),
(5, 'Test User', '12345678971', 'Dr.John', 50.00, 125.00, '2025-05-10 22:39:26'),
(6, 'Test User', '12345678971', 'Dr.John', 0.00, 250.00, '2025-05-11 11:48:44'),
(7, 'Test User', '12345678971', 'Dr.John', 0.00, 100.00, '2025-05-11 13:21:57'),
(8, 'Test User', '12345678971', 'Dr.John', 10.00, 13.50, '2025-05-11 13:29:13'),
(9, 'Test User', '12345678971', 'Dr.John', 0.00, 125.00, '2025-05-11 13:32:44'),
(10, 'Test User', '12345678971', 'Dr.John', 0.00, 10.00, '2025-05-11 13:37:37'),
(11, 'Test User', '12345678971', 'Dr.John', 0.00, 40.00, '2025-05-11 13:46:48'),
(12, 'Test User', '12345678971', 'Dr.John', 0.00, 8.00, '2025-05-11 13:53:02'),
(13, 'Test User', '12345678971', 'Dr.John', 0.00, 12.00, '2025-05-11 13:56:45'),
(14, 'Test User', '12345678971', 'Dr.John', 0.00, 10.00, '2025-05-11 13:57:26'),
(15, 'Test User', '12345678971', 'Dr.John', 0.00, 15.00, '2025-05-11 14:02:36'),
(16, 'Test User', '12345678971', 'Dr.John', 0.00, 110.00, '2025-05-11 14:08:40'),
(17, 'Test User', '12345678971', 'Dr.John', 0.00, 125.00, '2025-05-11 14:17:54'),
(18, 'Test User', '12345678971', 'Dr.John', 0.00, 35.00, '2025-05-11 14:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `medicine_id`, `quantity`, `subtotal`) VALUES
(1, 1, 1, 2, 70.00),
(2, 2, 1, 2, 70.00),
(3, 2, 3, 4, 440.00),
(4, 3, 2, 2, 500.00),
(5, 4, 1, 1, 35.00),
(6, 5, 4, 2, 250.00),
(7, 6, 2, 1, 250.00),
(8, 7, 29, 2, 100.00),
(9, 8, 26, 1, 15.00),
(10, 9, 4, 1, 125.00),
(11, 10, 25, 1, 10.00),
(12, 11, 30, 2, 40.00),
(13, 12, 27, 1, 8.00),
(14, 13, 28, 1, 12.00),
(15, 14, 25, 1, 10.00),
(16, 15, 26, 1, 15.00),
(17, 16, 3, 1, 110.00),
(18, 17, 4, 1, 125.00),
(19, 18, 32, 2, 20.00),
(20, 18, 34, 1, 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `symptom_feedback`
--

CREATE TABLE `symptom_feedback` (
  `id` int(11) NOT NULL,
  `symptom` varchar(255) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `feedback_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `disease_medicine_mapping`
--
ALTER TABLE `disease_medicine_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `symptom_feedback`
--
ALTER TABLE `symptom_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `disease_medicine_mapping`
--
ALTER TABLE `disease_medicine_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `symptom_feedback`
--
ALTER TABLE `symptom_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `disease_medicine_mapping`
--
ALTER TABLE `disease_medicine_mapping`
  ADD CONSTRAINT `disease_medicine_mapping_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);

--
-- Constraints for table `symptom_feedback`
--
ALTER TABLE `symptom_feedback`
  ADD CONSTRAINT `symptom_feedback_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
