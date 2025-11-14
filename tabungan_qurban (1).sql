-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 10:02 AM
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
-- Database: `tabungan_qurban`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `name`, `created_at`) VALUES
(1, 'admin', '$2y$10$YYlyHmN2O8D85DvfHEZGJuPcJg9jjCp.yCC78u9bx7/k5NGc8Ji0e', 'Administrator', '2025-09-08 03:20:23');

-- --------------------------------------------------------

--
-- Table structure for table `animals`
--

CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `nama_hewan` varchar(50) NOT NULL,
  `harga` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animals`
--

INSERT INTO `animals` (`id`, `nama_hewan`, `harga`) VALUES
(1, 'Kambing', 3000000),
(2, 'Domba', 3500000),
(3, 'Sapi', 20000000);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `saver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `saver_id`, `message`, `status`, `created_at`, `is_read`) VALUES
(11, 25, 'Halo notif, ini pengingat dari Admin Tabungan Qurban 🙏\r\nTanggal pelunasan tabungan Anda adalah *18 Nov 2025*.\r\nYuk segera lunasi tabungan qurban Anda sebelum tenggat waktunya 🐄.', 'unread', '2025-11-11 09:27:04', 1),
(12, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:03:36', 0),
(13, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:05:49', 0),
(14, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:06:39', 0),
(15, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:14:41', 0),
(16, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:15:10', 0),
(17, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:15:16', 0),
(18, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:16:25', 0),
(19, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:21:53', 0),
(20, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:23:49', 0),
(21, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:24:01', 0),
(22, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:24:45', 0),
(23, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 02:50:52', 0),
(24, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 08:56:07', 0),
(25, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 08:57:57', 0),
(26, 25, 'Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.', 'unread', '2025-11-14 09:00:11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `savers`
--

CREATE TABLE `savers` (
  `id` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telp` varchar(50) DEFAULT NULL,
  `target_qurban` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `target_nominal` int(11) DEFAULT 0,
  `tanggal_lunas` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savers`
--

INSERT INTO `savers` (`id`, `nama`, `username`, `password`, `animal_id`, `nik`, `alamat`, `telp`, `target_qurban`, `created_at`, `target_nominal`, `tanggal_lunas`) VALUES
(12, 'test', NULL, NULL, NULL, NULL, 'jl.test', '1234567890', 0.00, '2025-09-26 07:36:09', 5000000, NULL),
(15, 'coba saja', 'coba', '$2y$10$FaSsrGbVrLjN78W25rtZbOmT7nSfJVlGzjN3yDWC2.l/AlJYlT31e', NULL, NULL, 'jl.coba', '1234567890987654321', 0.00, '2025-10-13 02:52:13', 3000000, NULL),
(16, 'fraeal dwi f', 'lahir', '$2y$10$wF1gigUwTnZe4PuNG7oN8OyU.XBi5b/t5qtT0ocm4QizhensjqGsW', NULL, NULL, 'jl.fr', '09090908767676', 0.00, '2025-10-31 02:44:16', 5000000, NULL),
(21, 'oke', 'oke', '$2y$10$bI4wsCpRaGjlvBtG3M7ZvuTHo4rPUPcKW/1veUqcDjIDUBjwrF6Aa', NULL, NULL, 'oke', '098989', 0.00, '2025-11-06 07:00:37', 3000000, NULL),
(22, 'haf', 'mon', '$2y$10$IV2i8Op8Rmh6gS/deq4.IeLeRPMlMlJWWN.gCL0AfdHUvwLECYDw6', NULL, NULL, 'haf', '123243', 0.00, '2025-11-07 02:38:05', 3000000, NULL),
(23, 'nyar', 'anyar', '$2y$10$Mdy57KHbH5Txev7ExWc7uOsvDxA1mMfzE50MIIs7DCfxOX1fbIM8.', NULL, NULL, 'jl.nyar nyar', '98274324', 0.00, '2025-11-07 02:39:10', 5000000, NULL),
(24, 'target lunas', 'lunas', '$2y$10$aOoK2SBK33gIN7rzObK6dudNgM0oidj9t3bZSOWkh7YmHNG4OIVPG', NULL, NULL, 'lunas tepat waktu', '089789098765', 0.00, '2025-11-11 06:33:28', 3000000, '2025-11-12'),
(25, 'notif', 'notif', '$2y$10$F1lkGwb./rdYTaYTUbqJNemREqeeeEDnJGVn87rM/Fof337Dlmwfu', NULL, NULL, 'notif', '09876545432121', 0.00, '2025-11-11 07:04:38', 5000000, '2025-11-18'),
(26, 'Farrel Dwi Febriyanto', 'farrel', '$2y$10$IWPPkJIVXhaHLhpiCbZtYOqZB7aDpWRgJ2Ce5IRnBI/fInfOnPYEu', NULL, NULL, 'Jl.H Nurrois ', '089682346140', 0.00, '2025-11-14 01:41:23', 5000000, '2025-11-14'),
(27, 'maul', 'maulanaalif', '$2y$10$wxz2OllI6IK1DskmIFeF9Oq7fldxmnNykqlbCar2EUdh2wZXqiQSi', NULL, NULL, 'maulana alif ', '09878909876', 0.00, '2025-11-14 08:59:06', 20000000, '2025-11-21');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `saver_id` int(11) NOT NULL,
  `jenis` enum('setor','tarik') NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status` enum('unpaid','pending','confirmed','rejected') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `saver_id`, `jenis`, `payment_method`, `amount`, `note`, `receipt`, `created_at`, `bukti_transfer`, `status`) VALUES
(41, 16, 'setor', NULL, 100000.00, 'coba saja', '1761878707_690422b3849f2.png', '2025-10-31 02:45:07', NULL, 'unpaid'),
(42, 15, 'setor', NULL, 100000.00, 'wesh wesh', '1761901160_69047a683bd52.png', '2025-10-31 08:59:20', NULL, 'unpaid'),
(43, 15, 'setor', NULL, 1000000.00, 'oke', '1761901453_69047b8daa45a.jpeg', '2025-10-31 09:04:13', NULL, 'unpaid'),
(44, 15, 'setor', NULL, 1000000.00, 'transaksi', '1762412203_690c46ab1c69d.jpg', '2025-11-06 06:56:43', NULL, 'unpaid'),
(45, 21, 'setor', NULL, 10000.00, 'tes BCA', '1762413271_690c4ad7e840e.png', '2025-11-06 07:14:31', NULL, 'unpaid'),
(46, 21, 'setor', NULL, 10000.00, 'test ovo', '1762413300_690c4af455650.png', '2025-11-06 07:15:00', NULL, 'unpaid'),
(47, 21, 'setor', NULL, 100000.00, 'test linkaja', '1762413343_690c4b1fd58f2.png', '2025-11-06 07:15:43', NULL, 'unpaid'),
(48, 15, 'setor', NULL, 500000.00, 'pembayaran lanjut', '1762479797_690d4eb5a7197.png', '2025-11-07 01:43:17', NULL, 'unpaid'),
(49, 15, 'setor', NULL, 50000.00, 'pelunasan', '1762480498_690d517274591.jpeg', '2025-11-07 01:54:58', NULL, 'unpaid'),
(50, 15, 'setor', NULL, 300000.00, 'kurang 50rb', '1762480526_690d518e99f9a.png', '2025-11-07 01:55:26', NULL, 'unpaid'),
(51, 15, 'setor', NULL, 100000.00, 'lunasss', '1762480547_690d51a3bb81a.png', '2025-11-07 01:55:47', NULL, 'unpaid'),
(52, 15, 'setor', NULL, 50000.00, 'coba', '1762481180_690d541c9e895.png', '2025-11-07 02:06:20', NULL, 'unpaid'),
(53, 15, 'setor', NULL, 10000.00, 'coba', '1762481205_690d5435a5fde.png', '2025-11-07 02:06:45', NULL, 'unpaid'),
(54, 22, 'setor', NULL, 1000000.00, 'jutaaa', '1762483122_690d5bb2f1b4c.png', '2025-11-07 02:38:42', NULL, 'unpaid'),
(55, 23, 'setor', NULL, 500000.00, 'test', '1762483179_690d5bebd3628.png', '2025-11-07 02:39:39', NULL, 'unpaid'),
(56, 25, 'setor', NULL, 50000.00, 'pembayaran pertama', '1762850352_6912f630b189b.jpeg', '2025-11-11 08:39:12', NULL, 'unpaid'),
(57, 25, 'setor', NULL, 4950000.00, 'pelunasan', '1762850513_6912f6d1119a9.png', '2025-11-11 08:41:53', NULL, 'unpaid'),
(58, 24, 'setor', NULL, 1000000.00, '', NULL, '2025-11-13 08:42:58', NULL, 'unpaid'),
(59, 24, 'setor', NULL, 50000.00, '', '1763024091_69159cdb10779.png', '2025-11-13 08:54:51', NULL, 'unpaid'),
(60, 24, 'setor', NULL, 50000.00, '', NULL, '2025-11-13 09:02:22', NULL, 'unpaid'),
(61, 24, 'setor', NULL, 50000.00, '', NULL, '2025-11-13 09:04:37', NULL, 'unpaid'),
(62, 24, 'setor', NULL, 50000.00, '', NULL, '2025-11-13 09:08:17', NULL, 'unpaid'),
(63, 24, 'setor', NULL, 50000.00, '', NULL, '2025-11-13 09:12:20', NULL, 'unpaid'),
(64, 24, 'setor', NULL, 50000.00, '', NULL, '2025-11-13 09:17:39', NULL, 'unpaid'),
(65, 24, 'setor', NULL, 50000.00, 'percobaan', '1763025982_6915a43e89683.png', '2025-11-13 09:26:22', NULL, 'unpaid'),
(66, 24, 'setor', NULL, 200000.00, 'oke nice', '1763026027_6915a46b2caab.png', '2025-11-13 09:27:07', NULL, 'unpaid'),
(67, 26, 'setor', NULL, 15000.00, '', NULL, '2025-11-14 01:42:35', NULL, 'unpaid'),
(68, 26, 'setor', NULL, 50000.00, '', NULL, '2025-11-14 01:42:53', NULL, 'unpaid'),
(69, 26, 'setor', NULL, 500000.00, 'pembayaran ke 3', '1763085931_69168e6b09b2e.jpg', '2025-11-14 02:05:31', NULL, 'unpaid'),
(70, 27, 'setor', NULL, 1000000.00, 'percobaan data backup', '1763110796_6916ef8cd5165.jpeg', '2025-11-14 08:59:56', NULL, 'unpaid');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saver_id` (`saver_id`);

--
-- Indexes for table `savers`
--
ALTER TABLE `savers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saver_id` (`saver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `savers`
--
ALTER TABLE `savers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`saver_id`) REFERENCES `savers` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`saver_id`) REFERENCES `savers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
