-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 20, 2013 at 06:27 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forward_chaining`
--

-- --------------------------------------------------------

--
-- Table structure for table `kb_table_1`
--

CREATE TABLE IF NOT EXISTS `kb_table_1` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `fact_index` varchar(5) NOT NULL,
  `fact` varchar(150) NOT NULL,
  `is_derived` set('0','1') DEFAULT NULL,
  `level` int(3) DEFAULT NULL,
  `rules_in_lhs` varchar(300) DEFAULT NULL,
  `rules_in_rhs` varchar(300) DEFAULT NULL,
  `derived_from` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `kb_table_1`
--

INSERT INTO `kb_table_1` (`id`, `fact_index`, `fact`, `is_derived`, `level`, `rules_in_lhs`, `rules_in_rhs`, `derived_from`) VALUES
(1, 'F1', 'animal has hair', '0', NULL, ' R1', NULL, NULL),
(2, 'F14', 'animal is a mammal', '1', NULL, ' R5 R6', ' R1 R2', 'F2+F1'),
(3, 'F2', 'animal gives milk', '0', NULL, ' R2', NULL, NULL),
(4, 'F3', 'animal has feathers', '0', NULL, ' R3', NULL, NULL),
(5, 'F15', 'animal is a bird', '1', NULL, ' R11 R12', ' R3 R4', 'F4.F5+F3'),
(6, 'F4', 'animal flies', '0', NULL, ' R4 R11', NULL, NULL),
(7, 'F5', 'animal lays eggs', '0', NULL, ' R4', NULL, NULL),
(8, 'F6', 'animal eats meat', '0', NULL, ' R5', NULL, NULL),
(9, 'F16', 'animal is a carnivore', '1', NULL, ' R7 R8', ' R5', 'F14.F6'),
(10, 'F7', 'animal chews grass', '0', NULL, ' R6', NULL, NULL),
(11, 'F17', 'animal is a herbivore', '1', NULL, ' R9 R10', ' R6', 'F14.F7'),
(12, 'F8', 'animal has tawny color', '0', NULL, ' R7 R8 R9 R10', NULL, NULL),
(13, 'F9', 'animal has dark spots', '0', NULL, ' R7 R9', NULL, NULL),
(14, 'F18', 'animal is a cheetah', '1', NULL, '', ' R7', 'F16.F8.F9'),
(15, 'F10', 'animal has black stripes', '0', NULL, ' R8 R10', NULL, NULL),
(16, 'F19', 'animal is a tiger', '1', NULL, '', ' R8', 'F16.F8.F10'),
(17, 'F20', 'animal is a giraffe', '1', NULL, '', ' R9', 'F17.F8.F9'),
(18, 'F21', 'animal is a zebra', '1', NULL, '', ' R10', 'F17.F8.F10'),
(19, 'F11', 'animal has long legs', '0', NULL, ' R11', NULL, NULL),
(20, 'F12', 'animal has long neck', '0', NULL, ' R11', NULL, NULL),
(21, 'F22', 'animal is a ostrich', '1', NULL, '', ' R11', 'F15.!F4.F11.F12'),
(22, 'F13', 'animal flies very well', '0', NULL, ' R12', NULL, NULL),
(23, 'F23', 'animal is a albatross', '1', NULL, '', ' R12', 'F15.F13');

-- --------------------------------------------------------

--
-- Table structure for table `kb_table_2`
--

CREATE TABLE IF NOT EXISTS `kb_table_2` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `rule_index` varchar(5) NOT NULL,
  `dependent_facts` varchar(300) DEFAULT NULL,
  `comes_from_previous_rule` varchar(300) DEFAULT NULL,
  `derived_fact` varchar(5) DEFAULT NULL,
  `leads_to_next_rule` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `kb_table_2`
--

INSERT INTO `kb_table_2` (`id`, `rule_index`, `dependent_facts`, `comes_from_previous_rule`, `derived_fact`, `leads_to_next_rule`) VALUES
(1, 'R1', 'F1', '', 'F14', ' R5 R6'),
(2, 'R5', 'F14.F6', 'R2 R1 ', 'F16', ' R7 R8'),
(3, 'R6', 'F14.F7', 'R2 R1 ', 'F17', ' R9 R10'),
(4, 'R2', 'F2', '', 'F14', ' R5 R6'),
(5, 'R3', 'F3', '', 'F15', ' R11 R12'),
(6, 'R11', 'F15.!F4.F11.F12', 'R4 R3 ', 'F22', ''),
(7, 'R12', 'F15.F13', 'R4 R3 ', 'F23', ''),
(8, 'R4', 'F4.F5', '', 'F15', ' R11 R12'),
(9, 'R7', 'F16.F8.F9', 'R5 ', 'F18', ''),
(10, 'R8', 'F16.F8.F10', 'R5 ', 'F19', ''),
(11, 'R9', 'F17.F8.F9', 'R6 ', 'F20', ''),
(12, 'R10', 'F17.F8.F10', 'R6 ', 'F21', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_facts`
--

CREATE TABLE IF NOT EXISTS `tbl_facts` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `fact_index` varchar(5) NOT NULL,
  `fact` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `tbl_facts`
--

INSERT INTO `tbl_facts` (`id`, `fact_index`, `fact`) VALUES
(20, 'F1', 'animal has hair'),
(21, 'F2', 'animal gives milk'),
(22, 'F3', 'animal has feathers'),
(23, 'F4', 'animal flies'),
(24, 'F5', 'animal lays eggs'),
(25, 'F6', 'animal eats meat'),
(26, 'F7', 'animal chews grass'),
(27, 'F8', 'animal has tawny color'),
(28, 'F9', 'animal has dark spots'),
(29, 'F10', 'animal has black stripes'),
(30, 'F11', 'animal has long legs'),
(31, 'F12', 'animal has long neck'),
(32, 'F13', 'animal flies very well'),
(33, 'F14', 'animal is a mammal'),
(34, 'F15', 'animal is a bird'),
(35, 'F16', 'animal is a carnivore'),
(36, 'F17', 'animal is a herbivore'),
(37, 'F18', 'animal is a cheetah'),
(38, 'F19', 'animal is a tiger'),
(39, 'F20', 'animal is a giraffe'),
(40, 'F21', 'animal is a zebra'),
(41, 'F22', 'animal is a ostrich'),
(42, 'F23', 'animal is a albatross'),
(43, 'F24', 'animal dies');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rules`
--

CREATE TABLE IF NOT EXISTS `tbl_rules` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `rule_index` varchar(5) NOT NULL,
  `rule` varchar(300) CHARACTER SET utf8 NOT NULL,
  `rule_short` varchar(300) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `tbl_rules`
--

INSERT INTO `tbl_rules` (`id`, `rule_index`, `rule`, `rule_short`) VALUES
(6, 'R1', 'IF animal has hair THEN animal is a mammal', 'IF+F1+THEN+F14'),
(7, 'R2', 'IF animal gives milk THEN animal is a mammal', 'IF+F2+THEN+F14'),
(9, 'R3', 'IF animal has feathers THEN animal is a bird', 'IF+F3+THEN+F15'),
(10, 'R4', 'IF animal flies AND animal lays eggs THEN animal is a bird', 'IF+F4+AND+F5+THEN+F15'),
(11, 'R5', 'IF animal is a mammal AND animal eats meat THEN animal is a carnivore', 'IF+F14+AND+F6+THEN+F16'),
(12, 'R6', 'IF animal is a mammal AND animal chews grass THEN animal is a herbivore', 'IF+F14+AND+F7+THEN+F17'),
(13, 'R7', 'IF animal is a carnivore AND animal has tawny color AND animal has dark spots THEN animal is a cheetah', 'IF+F16+AND+F8+AND+F9+THEN+F18'),
(14, 'R8', 'IF animal is a carnivore AND animal has tawny color AND animal has black stripes THEN animal is a tiger', 'IF+F16+AND+F8+AND+F10+THEN+F19'),
(15, 'R9', 'IF animal is a herbivore AND animal has tawny color AND animal has dark spots THEN animal is a giraffe', 'IF+F17+AND+F8+AND+F9+THEN+F20'),
(16, 'R10', 'IF animal is a herbivore AND animal has tawny color AND animal has black stripes THEN animal is a zebra', 'IF+F17+AND+F8+AND+F10+THEN+F21'),
(17, 'R11', 'IF animal is a bird AND ( NOT animal flies ) AND animal has long legs AND animal has long neck THEN animal is a ostrich', 'IF+F15+AND+(+NOT+F4+)+AND+F11+AND+F12+THEN+F22'),
(18, 'R12', 'IF animal is a bird AND animal flies very well THEN animal is a albatross', 'IF+F15+AND+F13+THEN+F23');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
