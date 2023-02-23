-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 07, 2022 at 07:09 AM
-- Server version: 5.7.24
-- PHP Version: 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ejire_dps`
--

-- --------------------------------------------------------

--
-- Table structure for table `drug_type_list`
--

CREATE TABLE `drug_type_list` (
  `id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `drug_type_list`
--

INSERT INTO `drug_type_list` (`id`, `name`) VALUES
(2, 'Drops'),
(3, 'Tablet'),
(5, 'Capsule'),
(7, 'Inhalers');

-- --------------------------------------------------------

--
-- Table structure for table `location_rack_msbs`
--

CREATE TABLE `location_rack_msbs` (
  `location_rack_id` int(11) NOT NULL,
  `location_rack_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `location_rack_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `location_rack_msbs`
--

INSERT INTO `location_rack_msbs` (`location_rack_id`, `location_rack_name`, `location_rack_datetime`) VALUES
(1, 'Rack 01', '2022-04-18 03:44:23'),
(5, 'Rack 02', '2022-05-03 13:12:05'),
(6, 'Rack 03', '2022-05-03 13:12:08'),
(7, 'Rack 04', '2022-05-03 13:12:13');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_msbs`
--

CREATE TABLE `medicine_msbs` (
  `medicine_id` int(11) NOT NULL,
  `medicine_name` text COLLATE utf8_unicode_ci NOT NULL,
  `drug_type` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `pk_size` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `brand` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `medicine_available_quantity` int(11) NOT NULL,
  `medicine_location_rack` int(11) NOT NULL,
  `medicine_add_datetime` datetime NOT NULL,
  `medicine_update_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `medicine_msbs`
--

INSERT INTO `medicine_msbs` (`medicine_id`, `medicine_name`, `drug_type`, `pk_size`, `brand`, `medicine_available_quantity`, `medicine_location_rack`, `medicine_add_datetime`, `medicine_update_datetime`) VALUES
(12, 'ALBENDZOLE 400MG', '3', '20s', 'SAM PHARM', 57, 1, '2022-04-29 22:32:51', '2022-04-29 22:32:51'),
(13, 'Gentamicin ', '5', 'bott', 'SK Medicine', 54, 5, '2022-05-03 17:08:46', '2022-05-03 17:08:46');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_purchase_msbs`
--

CREATE TABLE `medicine_purchase_msbs` (
  `medicine_purchase_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `medicine_batch_no` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `medicine_purchase_qty` int(11) NOT NULL,
  `available_quantity` int(11) NOT NULL,
  `medicine_purchase_price_per_unit` decimal(12,2) NOT NULL,
  `medicine_purchase_total_cost` decimal(12,2) NOT NULL,
  `date_manufactured` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `expiry_date` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `medicine_sale_price_per_unit` decimal(12,2) NOT NULL,
  `medicine_purchase_enter_by` int(11) NOT NULL,
  `medicine_purchase_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `medicine_purchase_msbs`
--

INSERT INTO `medicine_purchase_msbs` (`medicine_purchase_id`, `medicine_id`, `supplier_id`, `medicine_batch_no`, `medicine_purchase_qty`, `available_quantity`, `medicine_purchase_price_per_unit`, `medicine_purchase_total_cost`, `date_manufactured`, `expiry_date`, `medicine_sale_price_per_unit`, `medicine_purchase_enter_by`, `medicine_purchase_datetime`) VALUES
(17, 12, 3, '456', 20, 20, '200.00', '4000.00', '2021-05-09', '2022-06-06', '230.00', 1, '2022-05-05 20:27:08');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_msbs`
--

CREATE TABLE `order_item_msbs` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `medicine_purchase_id` int(11) NOT NULL,
  `medicine_quantity` int(11) NOT NULL,
  `medicine_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_item_msbs`
--

INSERT INTO `order_item_msbs` (`order_item_id`, `order_id`, `medicine_id`, `medicine_purchase_id`, `medicine_quantity`, `medicine_price`) VALUES
(12, 9, 4, 6, 6, '200.00'),
(13, 10, 12, 11, 2, '210.00'),
(14, 11, 12, 12, 3, '210.00'),
(15, 12, 12, 12, 5, '210.00'),
(16, 13, 13, 13, 20, '210.00'),
(17, 14, 12, 17, 1, '230.00'),
(18, 15, 12, 17, 2, '230.00');

-- --------------------------------------------------------

--
-- Table structure for table `order_msbs`
--

CREATE TABLE `order_msbs` (
  `order_id` int(11) NOT NULL,
  `patient_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_total_amount` decimal(12,2) NOT NULL,
  `order_created_by` int(11) NOT NULL,
  `order_added_on` datetime NOT NULL,
  `order_updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_msbs`
--

INSERT INTO `order_msbs` (`order_id`, `patient_name`, `category`, `order_total_amount`, `order_created_by`, `order_added_on`, `order_updated_on`) VALUES
(11, 'Abighe-simon Pius', 'Patient', '630.00', 1, '2022-05-03 15:23:14', '2022-05-05 11:58:16'),
(13, 'Johnson Timothy', 'Patient', '4200.00', 1, '2022-05-03 17:09:59', '2022-05-05 12:13:38'),
(14, 'david', 'Patient', '230.00', 3, '2022-05-06 04:33:55', '2022-05-06 04:34:14'),
(15, 'Johnson Timi', 'Guest', '460.00', 4, '2022-05-11 12:03:07', '2022-05-11 12:03:07');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(6) UNSIGNED NOT NULL,
  `patient_name` varchar(250) NOT NULL,
  `sex` varchar(250) NOT NULL,
  `phoneno` varchar(250) NOT NULL,
  `age` varchar(250) NOT NULL,
  `date_of_admission` varchar(250) NOT NULL,
  `ward_number` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_name`, `sex`, `phoneno`, `age`, `date_of_admission`, `ward_number`) VALUES
(5, 'Johnson Tomithoy', 'Male', '08081315287', '29', '2022-05-06', 'EM108');

-- --------------------------------------------------------

--
-- Table structure for table `store_msbs`
--

CREATE TABLE `store_msbs` (
  `store_id` int(11) NOT NULL,
  `store_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `store_address` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `store_contact_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `store_email_address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `store_timezone` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `store_currency` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `store_added_on` datetime NOT NULL,
  `store_updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `store_msbs`
--

INSERT INTO `store_msbs` (`store_id`, `store_name`, `store_address`, `store_contact_no`, `store_email_address`, `store_timezone`, `store_currency`, `store_added_on`, `store_updated_on`) VALUES
(1, 'Ejire Primary health  center', 'No 11 raji road', '0807675647', 'admin@gmail.com', 'Africa/Monrovia', 'NGN', '2022-04-18 02:43:23', '2022-04-18 02:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_msbs`
--

CREATE TABLE `supplier_msbs` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_address` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `supplier_contact_no` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `supplier_msbs`
--

INSERT INTO `supplier_msbs` (`supplier_id`, `supplier_name`, `supplier_address`, `supplier_contact_no`, `supplier_email`, `supplier_datetime`) VALUES
(3, 'Supplier  One', 'No 11 raji road', '08079549494', 'admin@gmail.com', '2022-05-03 10:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_msbs`
--

CREATE TABLE `user_msbs` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_type` enum('Master','User') COLLATE utf8_unicode_ci NOT NULL,
  `user_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `user_created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_msbs`
--

INSERT INTO `user_msbs` (`user_id`, `user_name`, `user_email`, `user_password`, `user_type`, `user_status`, `user_created_on`) VALUES
(1, 'OSHO BOLUWATIFE', 'admin@gmail.com', 'admin123', 'Master', 'Enable', '2022-04-18 02:42:50'),
(4, 'Temitope', 'user1@gmail.com', 'aaaaaa', 'User', 'Enable', '2022-05-11 11:45:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drug_type_list`
--
ALTER TABLE `drug_type_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location_rack_msbs`
--
ALTER TABLE `location_rack_msbs`
  ADD PRIMARY KEY (`location_rack_id`);

--
-- Indexes for table `medicine_msbs`
--
ALTER TABLE `medicine_msbs`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `medicine_purchase_msbs`
--
ALTER TABLE `medicine_purchase_msbs`
  ADD PRIMARY KEY (`medicine_purchase_id`);

--
-- Indexes for table `order_item_msbs`
--
ALTER TABLE `order_item_msbs`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `order_msbs`
--
ALTER TABLE `order_msbs`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_msbs`
--
ALTER TABLE `store_msbs`
  ADD PRIMARY KEY (`store_id`);

--
-- Indexes for table `supplier_msbs`
--
ALTER TABLE `supplier_msbs`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `user_msbs`
--
ALTER TABLE `user_msbs`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drug_type_list`
--
ALTER TABLE `drug_type_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `location_rack_msbs`
--
ALTER TABLE `location_rack_msbs`
  MODIFY `location_rack_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `medicine_msbs`
--
ALTER TABLE `medicine_msbs`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `medicine_purchase_msbs`
--
ALTER TABLE `medicine_purchase_msbs`
  MODIFY `medicine_purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_item_msbs`
--
ALTER TABLE `order_item_msbs`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_msbs`
--
ALTER TABLE `order_msbs`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `store_msbs`
--
ALTER TABLE `store_msbs`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_msbs`
--
ALTER TABLE `supplier_msbs`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_msbs`
--
ALTER TABLE `user_msbs`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
