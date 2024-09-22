-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2024 at 08:37 PM
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
-- Database: `blood_donation_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `appointment_date` date DEFAULT NULL,
  `donor_id` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blood_summary`
--

CREATE TABLE `blood_summary` (
  `id` int(11) NOT NULL,
  `type` enum('A','A+','A-','B','B+','B-','AB','AB+','O','O+') NOT NULL,
  `collection_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `amount` int(11) DEFAULT 0,
  `donor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_summary`
--

INSERT INTO `blood_summary` (`id`, `type`, `collection_date`, `expiration_date`, `amount`, `donor_id`) VALUES
(6, 'A-', '2024-09-12', '2024-12-12', 1, 29),
(7, 'AB+', '2024-09-08', '2024-09-08', 1, 22),
(8, 'AB+', '2024-09-10', '2024-12-10', 1, 23),
(9, 'AB+', '2024-09-13', '2024-12-13', 1, 21),
(10, 'O+', '2024-09-13', '2024-12-13', 1, 30),
(11, 'B+', '2024-09-13', '2024-12-13', 1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `blood_units`
--

CREATE TABLE `blood_units` (
  `id` int(11) NOT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `collection_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `donor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `blood_units`
--
DELIMITER $$
CREATE TRIGGER `after_blood_unit_insert` AFTER INSERT ON `blood_units` FOR EACH ROW BEGIN
    INSERT INTO blood_summary (type, collection_date, expiration_date, amount, donor_id)
    VALUES (NEW.blood_type, NEW.collection_date, NEW.expiration_date, 1, NEW.donor_id)
    ON DUPLICATE KEY UPDATE
        collection_date = VALUES(collection_date),
        expiration_date = VALUES(expiration_date),
        amount = amount + 1;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_blood_unit_update` AFTER UPDATE ON `blood_units` FOR EACH ROW BEGIN
    -- Check if the blood type or donor_id has changed
    IF OLD.blood_type != NEW.blood_type OR OLD.donor_id != NEW.donor_id THEN
        -- Decrease the count for the old blood type and donor_id
        UPDATE blood_summary
        SET amount = amount - 1
        WHERE type = OLD.blood_type AND donor_id = OLD.donor_id;

        -- Insert or update the count for the new blood type and donor_id
        INSERT INTO blood_summary (type, collection_date, expiration_date, amount, donor_id)
        VALUES (NEW.blood_type, NEW.collection_date, NEW.expiration_date, 1, NEW.donor_id)
        ON DUPLICATE KEY UPDATE
            collection_date = VALUES(collection_date),
            expiration_date = VALUES(expiration_date),
            amount = amount + 1;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blood_usage`
--

CREATE TABLE `blood_usage` (
  `id` int(11) NOT NULL,
  `blood_unit_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `usage_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `name`, `comment`, `created_at`, `role`) VALUES
(4, 'nurse', 'ughgkhgkhghghgk', '2024-09-12 08:52:19', 'nurse'),
(30, 'jijiga roman', 'kjhjhljhjhljhljk', '2024-09-12 08:42:25', 'donor'),
(34, 'nurse', 'hkfhghghjgkhjgjhjhg', '2024-09-12 08:38:57', 'nurse'),
(35, 'nurse', 'hghgkjghkhjkg', '2024-09-12 08:39:16', 'nurse'),
(36, 'jijiga roman', 'jhjhljhjhlhj', '2024-09-12 08:49:55', 'donor'),
(37, 'jijiga roman', 'kjhjhjhjhljhl', '2024-09-12 08:49:59', 'donor'),
(38, 'nurse', 'ughgkhgkhghghgk', '2024-09-12 08:55:02', 'nurse'),
(39, 'nurse', 'jhjhkjhjh', '2024-09-12 08:55:05', 'nurse'),
(40, 'nurse', 'iohjhjhljhjhl', '2024-09-12 08:55:09', 'nurse'),
(41, 'nurse', 'kkjkjkjkj', '2024-09-12 08:55:41', 'nurse'),
(42, 'kebede', 'jfkjfkjfkhkhhg', '2024-09-12 09:10:25', 'inventory_manager'),
(43, 'kebede', 'kjgkgkjhjkhkjjhh', '2024-09-12 09:10:29', 'inventory_manager'),
(44, 'kebede', 'iijikjij', '2024-09-12 09:11:41', 'inventory_manager'),
(45, 'debark', 'hghghgglhjlhljhlj', '2024-09-12 09:12:00', 'hospital'),
(46, 'debark', 'jkhjhljhjhljhjhj', '2024-09-12 09:12:05', 'hospital'),
(47, 'debark hospital', 'ghjghggghhggh', '2024-09-12 09:47:15', 'hospital'),
(48, 'kebede', 'jglglglgljg', '2024-09-12 10:10:23', 'inventory_manager'),
(49, 'eyuel ', 'is;lkfjdgklgf', '2024-09-12 11:33:46', 'donor');

-- --------------------------------------------------------

--
-- Table structure for table `discarded_bloods`
--

CREATE TABLE `discarded_bloods` (
  `id` int(11) NOT NULL,
  `blood_type` varchar(50) DEFAULT NULL,
  `donor_id` int(11) DEFAULT NULL,
  `discarded_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distributed_bloods`
--

CREATE TABLE `distributed_bloods` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `blood_type` varchar(10) NOT NULL,
  `volume` int(11) NOT NULL,
  `distributed_at` datetime DEFAULT current_timestamp(),
  `donor_id` int(11) NOT NULL,
  `status` enum('pending','notified') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_requests`
--

CREATE TABLE `donation_requests` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `date_requested` date DEFAULT NULL,
  `status` enum('pending','donated','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_requests`
--

CREATE TABLE `hospital_requests` (
  `id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `blood_type` varchar(10) NOT NULL,
  `volume` int(11) NOT NULL,
  `status` enum('pending','fulfilled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_requests`
--

INSERT INTO `hospital_requests` (`id`, `hospital_id`, `blood_type`, `volume`, `status`, `created_at`) VALUES
(6, 3, 'B+', 1, 'fulfilled', '2024-08-27 19:10:48');

-- --------------------------------------------------------

--
-- Table structure for table `information`
--

CREATE TABLE `information` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `information`
--

INSERT INTO `information` (`id`, `title`, `content`, `posted_at`) VALUES
(5, 'kdjfkasfdjkadsf', 'jfjkjasfdjadsfdf', '2024-09-12 16:37:19'),
(7, 'efdfdfdf', 'sdfsdfdsfdsfdf', '2024-09-13 14:06:35');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `read_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `request_type` varchar(50) DEFAULT NULL,
  `request_details` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','donor','nurse','inventory_manager','hospital') NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `last_donation_date` date DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('active','blocked') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_verified`, `last_donation_date`, `verification_token`, `height`, `weight`, `blood_type`, `sex`, `profile_picture`, `phone`, `address`, `status`) VALUES
(2, 'admin', 'admin@gmail.com', '$2y$10$hd9uflfy4A7sni6tHLRRL.UI2pmJ/c193.QjD2/NkXAt4IMITtXUK', 'admin', 0, NULL, '015e7dfb1d674c91cd82114fa49a50ee', NULL, NULL, NULL, 'Male', 'E8AzAe4XsA0SaGH.jpg', '911887733', 'hghgkhghghg', 'active'),
(3, 'debark hospital', 'debark@gmail.com', '$2y$10$KztOzm2bxi75MkJzf056wuyLEPFhsxZxlJE.70yAS2TFaAlioKd1y', 'hospital', 0, NULL, '9955119abf53eb8254e9346a8a77ebb1', NULL, NULL, NULL, 'Male', 'E8AzAe7WYAExZ0I.jpg', '911887733', 'adiss ababa', 'active'),
(4, 'nurse', 'nurse@gmail.com', '$2y$10$A60SJV6kGhmxfOFsc0WK0.hb3h0QnCw.ibBDpC.FcSd2/os4sgxQ6', 'nurse', 0, NULL, '7696e5b91ac5687fdd499d6984401541', NULL, NULL, NULL, 'Male', 'uploads/profile_pictures/ofi.PNG', '+251911887733', 'adiss ababa', 'active'),
(5, 'kebede', 'invent@gmail.com', '$2y$10$IXa8ZibEdVgB8.wl8oHDguUJ4HeWfBu7f7Witw3OKR/pNZZBcsp6q', 'inventory_manager', 0, NULL, '5a7f187abf01ca587d3ffd62c33bca57', NULL, NULL, NULL, 'Male', 'E8AzAe4XsA0SaGH.jpg', '12233', NULL, 'active'),
(13, 'donor', 'donor@gmail.com', '$2y$10$yu2wxepQLbx4Pt9L8D7Ax.OViGgP60nyEqChrniMfWMUxg0pZTOZa', 'donor', 0, '2024-08-24', '7b4fba01cebfb888ceb207760c2b5f8d', '18', '1', 'u', 'Male', NULL, NULL, NULL, 'active'),
(19, 'tesfaye', 'tesfayetsega17@gmail.com', '$2y$10$RnGAhjaqbpHcKMj99jhdqeIHx9lWOx10Ci2H1dRXiXQn0NvGSEx42', 'donor', 0, '2024-08-24', 'cf69bd5decc8669ba026578c99e317ec', '23', '33', 'a', 'Male', '../../uploads/profile_pictures/66ca20e65ec81.png', NULL, NULL, 'blocked'),
(27, 'Abebe kebede ', 'abe@gmail.com', '$2y$10$stHaavqk5ddUEzX1hTOIp.9YU0wZgNzuSxaAjkcf6vfkqfvmFz6/2', 'donor', 0, NULL, 'b8a1dbc30ee3a3ca88ffb9069a7177ea', NULL, NULL, NULL, 'Male', '../../uploads/profile_pictures/66e17e6eb09ad.png', '+251911887733', 'gondar', 'active'),
(28, 'abush', 'abush@gmail.com', '$2y$10$umexjELmbwaBEAINmkK1guhraHGkzLMxRDul4nnLzs8X1ji5OCvoS', 'donor', 0, NULL, '79d6559fd94f29724656b6133e2cad1e', '12', '12', 'a', 'Male', '../../uploads/profile_pictures/66e17ed6eaf7a.png', '+251911887733', 'adiss ababa', 'active'),
(33, 'admin 2', 'admin2@gmail.com', '$2y$10$wCcLs8RycLPgb8hBROG4G.tbvHhu2Mq8lq/KA48OnmfQNuX2ZLjm.', 'admin', 0, NULL, '5b665eaad8f2aa4861017bddc9f7a2ca', NULL, NULL, NULL, 'Male', '../../uploads/profile_pictures/66e2bd55b8496.png', '+251911887733', 'gondar', 'active'),
(35, 'nurse3', 'nurse3@gmail.com', '$2y$10$Yuc3HZ5ciETOUQWvsgqraOa4f2ZxWpOWr88Nopc4oWpHiIoIEH4E.', 'nurse', 0, NULL, '0075563fbbc7726d1269ad39f38d943c', NULL, NULL, NULL, 'Male', 'uploads/profile_pictures/Pixstory-image-165520146664462.png', '+251911887733', 'dfdfdf', 'active'),
(36, 'abebe', 'invent2@gmail.com', '$2y$10$0qTArEyktWlObuctRlmnlukFF3YCm7PJ1hTIuVsjYTTfy2dy9f1ra', 'inventory_manager', 0, NULL, '202cca9f1dfa074a09de82ab3659ef99', NULL, NULL, NULL, 'Male', 'Pixstory-image-165520146664462.png', '+251911887733', 'dfsdfjjhhhghhghghggfgf', 'active'),
(38, 'nurse4', 'nurse4@gmail.com', '$2y$10$MSgleeQT4OraOKm4fF76COnulaB5MCISVp0XkVZJYT2P91i/bxiYi', 'nurse', 0, NULL, 'e1da78c4a573d14526da764559dfa930', NULL, NULL, NULL, 'Male', 'photo_2024-09-10_05-22-00.jpg', '+251911887733', 'adiss ababaea', 'active'),
(41, 'fksdfjafdskfjsdf', 'erer@gmail.com', '$2y$10$KiYSrfuGblKOF7VO8XmFAO1471qExVJdi3n775HhmcuuwmMHm6dsC', 'donor', 0, NULL, 'c9a8919c0a19cb885e830a0177725a8e', NULL, NULL, NULL, 'Male', NULL, '+251911887733', 'adiss ababa', 'active'),
(42, 'noor hospital', 'noor@gmail.com', '$2y$10$O7SBBqGxP8uPKPBgO1nGUuIQHSQfZQtUKWBlRv2yV6pcDPZwQRN4G', 'hospital', 0, NULL, 'd8370385b6ef8cbcbb70839833f8ed88', NULL, NULL, NULL, 'Male', '../../uploads/profile_pictures/66e4682c7cad3.jpg', '+251911887733', 'adiss ababa', 'active'),
(43, 'Birhanu Nega', 'birhanunega12@gmail.com', '$2y$10$aXfAMWch09SlmEZNKXFgIOx8MVO5y6nh86zcIJ84delPHdUK2Xxau', 'donor', 0, NULL, 'adc2c5799877180651d4ec6b762a3cf7', NULL, NULL, NULL, 'Male', '../../uploads/profile_pictures/66e475b2e381e.jpg', '+251911887733', 'adiss ababa', 'active'),
(45, 'aman', 'amanido41@gmail.com', '$2y$10$mubrn73fBaQjkraLuTUGCuz4kjZWjIhVgRcvC1RfWAyTvZN4DVpLK', 'donor', 0, NULL, '04ebb7a23f1aad38145a1a94e69cbf58', NULL, NULL, NULL, 'Male', NULL, '+251911887733', 'adiss ababa', 'active'),
(47, 'man', 'mannathy5@gmail.com', '$2y$10$ithTCMS4tS1DSEhSh7.qM.kVuF7SQ3P.RADToawRDjgovOQvEjpKi', 'donor', 0, NULL, '3390d6b3cef0e06a3bda7c166a9fe69d', NULL, NULL, NULL, 'Male', NULL, '+251911887733', 'adiss ababa', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `blood_summary`
--
ALTER TABLE `blood_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blood_units`
--
ALTER TABLE `blood_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `blood_usage`
--
ALTER TABLE `blood_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blood_unit_id` (`blood_unit_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discarded_bloods`
--
ALTER TABLE `discarded_bloods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `distributed_bloods`
--
ALTER TABLE `distributed_bloods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `donation_requests`
--
ALTER TABLE `donation_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_requests`
--
ALTER TABLE `hospital_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `information`
--
ALTER TABLE `information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_summary`
--
ALTER TABLE `blood_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `blood_units`
--
ALTER TABLE `blood_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `blood_usage`
--
ALTER TABLE `blood_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `discarded_bloods`
--
ALTER TABLE `discarded_bloods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `distributed_bloods`
--
ALTER TABLE `distributed_bloods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `donation_requests`
--
ALTER TABLE `donation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_requests`
--
ALTER TABLE `hospital_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `information`
--
ALTER TABLE `information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `blood_units`
--
ALTER TABLE `blood_units`
  ADD CONSTRAINT `blood_units_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blood_usage`
--
ALTER TABLE `blood_usage`
  ADD CONSTRAINT `blood_usage_ibfk_1` FOREIGN KEY (`blood_unit_id`) REFERENCES `blood_units` (`id`);

--
-- Constraints for table `distributed_bloods`
--
ALTER TABLE `distributed_bloods`
  ADD CONSTRAINT `distributed_bloods_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `hospital_requests` (`id`);

--
-- Constraints for table `donation_requests`
--
ALTER TABLE `donation_requests`
  ADD CONSTRAINT `donation_requests_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
