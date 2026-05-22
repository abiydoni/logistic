-- AppsBeem Logistic Database Backup
-- Generated: 2026-05-21 05:49:20
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS=0;



-- Structure for table `activity_logs`
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------


-- Structure for table `item_transactions`
DROP TABLE IF EXISTS `item_transactions`;
CREATE TABLE `item_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `item_transactions`
INSERT INTO `item_transactions` (`id`, `item_id`, `type`, `quantity`, `notes`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES ('1', '1', 'in', '1', '', '2026-05-20 10:37:27', '2026-05-20 10:37:27', '1', '1');

-- --------------------------------------------------------


-- Structure for table `items`
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `unit` varchar(20) DEFAULT 'pcs',
  `initial_stock` int(11) DEFAULT 0,
  `current_stock` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 0,
  `expired_date` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_code` (`code`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `items`
INSERT INTO `items` (`id`, `warehouse_id`, `code`, `name`, `unit`, `initial_stock`, `current_stock`, `min_stock`, `expired_date`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES ('1', '1', '8997231110030', 'Saus Sambal Bu Lani', 'pcs', '0', '1', '10', '2027-04-19', '1', '1', '2026-05-20 10:26:18', '2026-05-20 11:01:01');

-- --------------------------------------------------------


-- Structure for table `settings`
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------


-- Structure for table `stock_transactions`
DROP TABLE IF EXISTS `stock_transactions`;
CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `stock_transactions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------


-- Structure for table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('Admin','Staff') DEFAULT 'Staff',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES ('1', 'admin', '$2y$10$51WFupyTCven1Gp2ihyrqeHaIAYZ1srPW2LrP.TpXxIPp5hdH.Yje', 'Doni Abiyantoro', 'Admin', NULL, '1', '2026-05-20 06:27:20', '2026-05-21 04:15:17');
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES ('2', 'user', '$2y$10$l6DkFr60g9UBvSNsvfUC6OxVw6vntVoRE.tAohbiKdj9Y9Sp5dLZi', 'Regular User', '', NULL, NULL, '2026-05-20 06:27:20', '2026-05-20 06:27:20');

-- --------------------------------------------------------


-- Structure for table `warehouses`
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_expiration` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `warehouses`
INSERT INTO `warehouses` (`id`, `name`, `description`, `requires_expiration`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES ('1', 'Food & Beverage', 'Gudang penyimpanan bahan makanan segar, kaleng, dan minuman.', '1', '1', '1', '2026-05-20 06:31:58', '2026-05-20 06:31:58');
INSERT INTO `warehouses` (`id`, `name`, `description`, `requires_expiration`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES ('6', 'General', 'Untuk menyimpan semua barang yang buakan Makanan dan Minuman', '0', '1', '1', '2026-05-20 06:56:22', '2026-05-20 17:50:21');

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS=1;
