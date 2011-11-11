-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 09, 2011 at 08:24 AM
-- Server version: 5.1.56
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bmobley_freecast`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_mgm_coupons`
--

CREATE TABLE IF NOT EXISTS `wp_mgm_coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `use_limit` int(11) unsigned DEFAULT NULL,
  `used_count` int(11) unsigned DEFAULT NULL,
  `expire_dt` datetime DEFAULT NULL,
  `create_dt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='coupons' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `wp_mgm_coupons`
--

INSERT INTO `wp_mgm_coupons` (`id`, `name`, `value`, `description`, `use_limit`, `used_count`, `expire_dt`, `create_dt`) VALUES
(1, 'MARKETING100', '100%', 'Free Marketing Membership', NULL, 47, NULL, '2011-09-20 16:13:56'),
(2, 'testingclickbank', '98%', 'Created to test clickbank', 5, 1, '2011-10-22 12:00:00', '2011-10-21 08:22:46');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
