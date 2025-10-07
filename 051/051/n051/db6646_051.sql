-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 11:22 AM
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
-- Database: `db6646_051`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_664230051`
--

CREATE TABLE `tb_664230051` (
  `No` int(5) NOT NULL COMMENT 'ลำดับ',
  `ID` varchar(9) NOT NULL COMMENT 'รหัสนักศึกษา',
  `Name` varchar(100) NOT NULL COMMENT 'ชื่อ',
  `Last name` varchar(100) NOT NULL COMMENT 'นามสกุล',
  `Email` varchar(100) NOT NULL COMMENT 'อีเมล',
  `Tel` varchar(20) NOT NULL COMMENT 'เบอร์โทร',
  `Created At` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'เวลาสร้าง'
  `Age` varchar(2) NOT NULL COMMENT 'อายุ',
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
