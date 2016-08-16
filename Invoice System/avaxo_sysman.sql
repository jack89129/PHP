-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 23, 2013 at 08:59 AM
-- Server version: 5.5.31
-- PHP Version: 5.3.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `avaxo_sysman`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkdate`
--

CREATE TABLE IF NOT EXISTS `checkdate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `inc_count` int(11) NOT NULL,
  `eq_count` int(11) NOT NULL,
  `dec_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `checkdate`
--

INSERT INTO `checkdate` (`id`, `check_date`, `end_date`, `inc_count`, `eq_count`, `dec_count`) VALUES
(1, '2013-09-24 09:00:00', '2013-09-24 09:10:00', 0, 0, 0),
(4, '2013-11-21 20:42:39', '2013-11-21 20:43:10', 17, 19, 0),
(5, '2013-11-21 23:04:30', '2013-11-21 23:04:53', 0, 19, 17);

-- --------------------------------------------------------

--
-- Table structure for table `ip`
--

CREATE TABLE IF NOT EXISTS `ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `ip`
--

INSERT INTO `ip` (`id`, `ip`) VALUES
(1, '84.244.156.217'),
(2, '84.244.156.218'),
(3, '84.244.156.219'),
(4, '84.244.156.220'),
(5, '84.244.156.221'),
(6, '84.244.156.222'),
(7, '84.244.156.223'),
(8, '84.244.156.224'),
(9, '84.244.156.225'),
(10, '84.244.156.226'),
(11, '84.244.156.227'),
(12, '84.244.156.228'),
(13, '84.244.156.229'),
(14, '84.244.156.230'),
(15, '84.244.156.231'),
(16, '84.244.156.232'),
(17, '84.244.156.233'),
(18, '84.244.156.234'),
(19, '84.244.156.235'),
(20, '84.244.156.236'),
(21, '84.244.156.237'),
(22, '84.244.156.238'),
(23, '84.244.156.239'),
(24, '84.244.156.240'),
(25, '84.244.156.241'),
(26, '84.244.156.241'),
(27, '84.244.156.242'),
(28, '84.244.156.243'),
(29, '84.244.156.244'),
(30, '84.244.156.245'),
(31, '84.244.156.246'),
(32, '84.244.156.247'),
(33, '84.244.156.248'),
(34, '84.244.156.249'),
(35, '84.244.156.250'),
(36, '84.244.156.251');

-- --------------------------------------------------------

--
-- Table structure for table `score`
--

CREATE TABLE IF NOT EXISTS `score` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `ipid` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `oldscore` int(11) NOT NULL,
  `dateid` int(11) NOT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=145 ;

--
-- Dumping data for table `score`
--

INSERT INTO `score` (`no`, `ipid`, `score`, `oldscore`, `dateid`) VALUES
(1, 1, 0, 0, 1),
(2, 2, 0, 0, 1),
(3, 3, 0, 0, 1),
(4, 4, 0, 0, 1),
(5, 5, 0, 0, 1),
(6, 6, 0, 0, 1),
(7, 7, 0, 0, 1),
(8, 8, 0, 0, 1),
(9, 9, 0, 0, 1),
(10, 10, 0, 0, 1),
(11, 11, 0, 0, 1),
(12, 12, 0, 0, 1),
(13, 13, 0, 0, 1),
(14, 14, 0, 0, 1),
(15, 15, 0, 0, 1),
(16, 16, 0, 0, 1),
(17, 17, 0, 0, 1),
(18, 18, 0, 0, 1),
(19, 19, 0, 0, 1),
(20, 20, 0, 0, 1),
(21, 21, 0, 0, 1),
(22, 22, 0, 0, 1),
(23, 23, 0, 0, 1),
(24, 24, 0, 0, 1),
(25, 25, 0, 0, 1),
(26, 26, 0, 0, 1),
(27, 27, 0, 0, 1),
(28, 28, 0, 0, 1),
(29, 29, 0, 0, 1),
(30, 30, 0, 0, 1),
(31, 31, 0, 0, 1),
(32, 32, 0, 0, 1),
(33, 33, 0, 0, 1),
(34, 34, 0, 0, 1),
(35, 35, 0, 0, 1),
(36, 36, 0, 0, 1),
(73, 1, 99, 0, 4),
(74, 2, 100, 0, 4),
(75, 3, 99, 0, 4),
(76, 4, 56, 0, 4),
(77, 5, 56, 0, 4),
(78, 6, 96, 0, 4),
(79, 7, 98, 0, 4),
(80, 8, 98, 0, 4),
(81, 9, 0, 0, 4),
(82, 10, 56, 0, 4),
(83, 11, 92, 0, 4),
(84, 12, 88, 0, 4),
(85, 13, 100, 0, 4),
(86, 14, 97, 0, 4),
(87, 15, 98, 0, 4),
(88, 16, 0, 0, 4),
(89, 17, 99, 0, 4),
(90, 18, 0, 0, 4),
(91, 19, 0, 0, 4),
(92, 20, 0, 0, 4),
(93, 21, 0, 0, 4),
(94, 22, 0, 0, 4),
(95, 23, 87, 0, 4),
(96, 24, 0, 0, 4),
(97, 25, 0, 0, 4),
(98, 26, 0, 0, 4),
(99, 27, 97, 0, 4),
(100, 28, 0, 0, 4),
(101, 29, 0, 0, 4),
(102, 30, 0, 0, 4),
(103, 31, 0, 0, 4),
(104, 32, 0, 0, 4),
(105, 33, 0, 0, 4),
(106, 34, 0, 0, 4),
(107, 35, 0, 0, 4),
(108, 36, 0, 0, 4),
(109, 1, 0, 99, 5),
(110, 2, 0, 100, 5),
(111, 3, 0, 99, 5),
(112, 4, 0, 56, 5),
(113, 5, 0, 56, 5),
(114, 6, 0, 96, 5),
(115, 7, 0, 98, 5),
(116, 8, 0, 98, 5),
(117, 9, 0, 0, 5),
(118, 10, 0, 56, 5),
(119, 11, 0, 92, 5),
(120, 12, 0, 88, 5),
(121, 13, 0, 100, 5),
(122, 14, 0, 97, 5),
(123, 15, 0, 98, 5),
(124, 16, 0, 0, 5),
(125, 17, 0, 99, 5),
(126, 18, 0, 0, 5),
(127, 19, 0, 0, 5),
(128, 20, 0, 0, 5),
(129, 21, 0, 0, 5),
(130, 22, 0, 0, 5),
(131, 23, 0, 87, 5),
(132, 24, 0, 0, 5),
(133, 25, 0, 0, 5),
(134, 26, 0, 0, 5),
(135, 27, 0, 97, 5),
(136, 28, 0, 0, 5),
(137, 29, 0, 0, 5),
(138, 30, 0, 0, 5),
(139, 31, 0, 0, 5),
(140, 32, 0, 0, 5),
(141, 33, 0, 0, 5),
(142, 34, 0, 0, 5),
(143, 35, 0, 0, 5),
(144, 36, 0, 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `database` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `firstname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `company` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `database`, `name`, `firstname`, `company`, `status`) VALUES
(2, 'demo', '98cf271ff64b71be3d340727aaace187322bd1c6', 'test@gmail.com', 'avaxo_avaxo', 'Test', NULL, '', 1),
(40, 'mkbwebhoster', 'bf7f6126bfe764cf347313723ff91a33652abd58', 'sdhaarlem@gmail.com', 'avaxo_mkbwebhoster', 'mkbwebhoster', 'mkbwebhoster', 'mkbwebhoster', 1),
(38, 'itecron', '98cf271ff64b71be3d340727aaace187322bd1c6', 'niels@itecron.com', 'avaxo_itecron', 'Daniels', 'Niels', 'iTecron bvba', 1),
(39, 'Marc Eerdekens', '1204cb1519852c18c1de9facc186dd23fd55a1ce', 'marceerdekens@hotmail.com', 'avaxo_Marc Eerdekens', 'Eerdekens', 'Marc', 'Maxx-eventsVOF', 2),
(33, 'psYch0', '95133294afce46aa0cd5d2bcc89cf87f28daed77', 'info@jsdev.be', 'avaxo_psYch0', 'Senden', 'Jos', 'J.S.Developments', 1),
(35, 'henri0208', 'cbce351eaa2e4b1867729af5f3a04ac83128f1dc', 'henri@mijdam.nl', 'avaxo_henri0208', 'mijdam', 'henri', 'Kulturhus Haaften', 1),
(36, 'ntritsmans', '823d9d1884ba20cbac945de665112727590fda46', 'niels.tritsmans@gmail.com', 'avaxo_ntritsmans', 'Tritsmans', 'Niels', 'Repetitorcollege', 1),
(37, 'tniels', '823d9d1884ba20cbac945de665112727590fda46', 'niels.tritsmans@me.com', 'avaxo_tniels', 'Tritsmans', 'Niels', 'Repetitorcollege', 1),
(32, 'james', '98cf271ff64b71be3d340727aaace187322bd1c6', 'jamesjiang1985@gmail.com', 'avaxo_james', 'Jos', 'James', 'Zend Expert Team', 1),
(41, 'andrewpcone', 'f23e8dc5dc2958ea6142ac371da5b1847ed0e4d4', 'andrewpcone@gmail.com', 'avaxo_andrewpcone', 'cone', 'andrew', 'acone', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
