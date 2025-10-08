-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 12:36 PM
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
  `fname` varchar(100) NOT NULL COMMENT 'ชื่อ',
  `lname` varchar(100) NOT NULL COMMENT 'นามสกุล',
  `Email` varchar(100) NOT NULL COMMENT 'อีเมล',
  `Tel` varchar(20) NOT NULL COMMENT 'เบอร์โทร',
  `Created At` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'เวลาสร้าง',
  `Age` varchar(2) NOT NULL COMMENT 'อายุ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_664230051`
--

INSERT INTO `tb_664230051` (`No`, `ID`, `fname`, `lname`, `Email`, `Tel`, `Created At`, `Age`) VALUES
(1, '664230051', 'นันศิริ', 'พุกภูษา', 'new@gmai.com', '06234567890', '2025-10-08 09:40:53', '20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_664230051`
--
ALTER TABLE `tb_664230051`
  ADD PRIMARY KEY (`No`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_664230051`
--
ALTER TABLE `tb_664230051`
  MODIFY `No` int(5) NOT NULL AUTO_INCREMENT COMMENT 'ลำดับ', AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
