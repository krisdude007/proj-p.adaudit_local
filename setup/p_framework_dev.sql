-- phpMyAdmin SQL Dump
-- version 4.2.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 10, 2014 at 07:38 AM
-- Server version: 5.1.72
-- PHP Version: 5.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `p_framework_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl`
--

CREATE TABLE IF NOT EXISTS `acl` (
`acl_id` bigint(20) NOT NULL,
  `acl_role_nm` varchar(32) NOT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `crea_usr_id` bigint(20) NOT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` bigint(20) NOT NULL,
  `deleted` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` (`acl_id`, `acl_role_nm`, `crea_dtm`, `crea_usr_id`, `updt_dtm`, `updt_usr_id`, `deleted`) VALUES
(2, 'Add', '2014-06-05 13:23:00', 893, '2014-06-05 13:24:05', 893, 0),
(3, 'Edit', '2014-06-05 13:24:00', 893, '2014-06-05 13:24:44', 893, 0),
(5, 'View Only', '2014-06-05 13:24:00', 893, '2014-06-05 14:02:23', 893, 0),
(7, 'Add_Edit', '2014-06-05 13:24:00', 893, '2014-06-05 14:07:44', 893, 0),
(8, 'Add_Edit_Delete', '2014-06-05 18:14:23', 893, '2014-06-05 19:35:25', 893, 0),
(9, 'Edit_Delete', '2014-06-05 17:21:09', 893, '2014-06-05 19:36:31', 893, 0),
(10, 'Project Lead', '2014-06-09 16:32:18', 893, '2014-06-09 17:30:44', 893, 0);

-- --------------------------------------------------------

--
-- Table structure for table `column_meta`
--

CREATE TABLE IF NOT EXISTS `column_meta` (
`column_meta_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `grid_nm` varchar(256) NOT NULL,
  `column_nm` varchar(256) NOT NULL,
  `column_alias_nm` varchar(256) DEFAULT NULL,
  `column_width` bigint(20) DEFAULT NULL,
  `column_editor_type_nm` varchar(256) DEFAULT NULL,
  `view_role_nm` varchar(256) DEFAULT NULL,
  `edit_role_nm` varchar(256) DEFAULT NULL,
  `visibility` tinyint(1) DEFAULT '1',
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `crea_usr_id` varchar(32) DEFAULT NULL,
  `updt_dtm` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(32) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `excel_mgr_batch`
--

CREATE TABLE IF NOT EXISTS `excel_mgr_batch` (
`excel_mgr_batch_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tmp_name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `tab` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_row_names` tinyint(1) NOT NULL,
  `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `map` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `log_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uptd_usr_id` int(11) NOT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `excel_mgr_log`
--

CREATE TABLE IF NOT EXISTS `excel_mgr_log` (
`excel_mgr_log_id` bigint(20) NOT NULL,
  `excel_mgr_batch_id` bigint(20) NOT NULL,
  `row` bigint(20) NOT NULL,
  `msg` text COLLATE utf8_unicode_ci NOT NULL,
  `row_json` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `grid`
--

CREATE TABLE IF NOT EXISTS `grid` (
`grid_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `A` text COLLATE utf8_unicode_ci,
  `B` text COLLATE utf8_unicode_ci,
  `C` text COLLATE utf8_unicode_ci,
  `D` text COLLATE utf8_unicode_ci,
  `E` text COLLATE utf8_unicode_ci,
  `F` text COLLATE utf8_unicode_ci,
  `G` text COLLATE utf8_unicode_ci,
  `H` text COLLATE utf8_unicode_ci,
  `I` text COLLATE utf8_unicode_ci,
  `J` text COLLATE utf8_unicode_ci,
  `K` text COLLATE utf8_unicode_ci,
  `L` text COLLATE utf8_unicode_ci,
  `M` text COLLATE utf8_unicode_ci,
  `N` text COLLATE utf8_unicode_ci,
  `O` text COLLATE utf8_unicode_ci,
  `P` text COLLATE utf8_unicode_ci,
  `Q` text COLLATE utf8_unicode_ci,
  `R` text COLLATE utf8_unicode_ci,
  `S` text COLLATE utf8_unicode_ci,
  `T` text COLLATE utf8_unicode_ci,
  `U` text COLLATE utf8_unicode_ci,
  `V` text COLLATE utf8_unicode_ci,
  `W` text COLLATE utf8_unicode_ci,
  `X` text COLLATE utf8_unicode_ci,
  `Y` text COLLATE utf8_unicode_ci,
  `Z` text COLLATE utf8_unicode_ci,
  `A_INT` bigint(20) DEFAULT NULL,
  `B_INT` bigint(20) DEFAULT NULL,
  `C_INT` bigint(20) DEFAULT NULL,
  `D_INT` bigint(20) DEFAULT NULL,
  `E_INT` bigint(20) DEFAULT NULL,
  `F_INT` bigint(20) DEFAULT NULL,
  `G_INT` bigint(20) DEFAULT NULL,
  `H_INT` bigint(20) DEFAULT NULL,
  `I_INT` bigint(20) DEFAULT NULL,
  `J_INT` bigint(20) DEFAULT NULL,
  `A_DATE` date DEFAULT NULL,
  `B_DATE` date DEFAULT NULL,
  `C_DATE` date DEFAULT NULL,
  `D_DATE` date DEFAULT NULL,
  `E_DATE` date DEFAULT NULL,
  `A_DECIMAL` decimal(60,4) DEFAULT NULL,
  `B_DECIMAL` decimal(60,4) DEFAULT NULL,
  `C_DECIMAL` decimal(60,4) DEFAULT NULL,
  `D_DECIMAL` decimal(60,4) DEFAULT NULL,
  `E_DECIMAL` decimal(60,4) DEFAULT NULL,
  `A_TEXT` text COLLATE utf8_unicode_ci,
  `B_TEXT` text COLLATE utf8_unicode_ci,
  `C_TEXT` text COLLATE utf8_unicode_ci,
  `D_TEXT` text COLLATE utf8_unicode_ci,
  `E_TEXT` text COLLATE utf8_unicode_ci,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `excel_mgr_batch_id` bigint(20) NOT NULL,
  `deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `grid`
--

INSERT INTO `grid` (`grid_id`, `project_id`, `A`, `B`, `C`, `D`, `E`, `F`, `G`, `H`, `I`, `J`, `K`, `L`, `M`, `N`, `O`, `P`, `Q`, `R`, `S`, `T`, `U`, `V`, `W`, `X`, `Y`, `Z`, `A_INT`, `B_INT`, `C_INT`, `D_INT`, `E_INT`, `F_INT`, `G_INT`, `H_INT`, `I_INT`, `J_INT`, `A_DATE`, `B_DATE`, `C_DATE`, `D_DATE`, `E_DATE`, `A_DECIMAL`, `B_DECIMAL`, `C_DECIMAL`, `D_DECIMAL`, `E_DECIMAL`, `A_TEXT`, `B_TEXT`, `C_TEXT`, `D_TEXT`, `E_TEXT`, `updt_dtm`, `excel_mgr_batch_id`, `deleted`) VALUES
(1, 0, 'test\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-06-04 19:28:02', 0, 0),
(2, 0, NULL, NULL, NULL, 'ljhkhgjkhg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-06-04 19:28:30', 0, 0),
(3, 0, 'kjkljh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-06-04 19:30:35', 0, 0),
(4, 0, NULL, 'hgligulkhu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-06-04 19:30:48', 0, 0),
(5, 0, NULL, NULL, 'hjklkh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-06-04 19:31:01', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
`group_id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `group_nm` varchar(64) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `role` varchar(32) NOT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `crea_usr_id` bigint(20) NOT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` bigint(20) NOT NULL,
  `deleted` smallint(1) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_user`
--

CREATE TABLE IF NOT EXISTS `group_user` (
`group_user_id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `crea_usr_id` bigint(20) NOT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` bigint(20) NOT NULL,
  `deleted` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
`log_id` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `priority` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `priorityName` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_uri` varchar(1024) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `match_def`
--

CREATE TABLE IF NOT EXISTS `match_def` (
`match_def_id` int(11) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `Left_Column` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Right_Colum` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `match_engine` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
`project_id` bigint(20) NOT NULL,
  `project_txt` varchar(64) NOT NULL,
  `project_desc` varchar(256) DEFAULT NULL,
  `customer_id` bigint(20) NOT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `crea_usr_id` varchar(32) DEFAULT NULL,
  `updt_dtm` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(32) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `project_lead` bigint(20) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_user`
--

CREATE TABLE IF NOT EXISTS `project_user` (
`project_usr_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `crea_usr_id` bigint(20) NOT NULL,
  `crea_dtm` datetime NOT NULL,
  `updt_usr_id` bigint(20) NOT NULL,
  `updt_dtm` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=FIXED AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
`role_id` bigint(20) NOT NULL,
  `role_nm` varchar(32) NOT NULL,
  `acl` varchar(32) NOT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `crea_usr_id` bigint(20) NOT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` bigint(20) NOT NULL,
  `deleted` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acl`
--
ALTER TABLE `acl`
 ADD PRIMARY KEY (`acl_id`), ADD UNIQUE KEY `acl_role_nm` (`acl_role_nm`);

--
-- Indexes for table `column_meta`
--
ALTER TABLE `column_meta`
 ADD PRIMARY KEY (`column_meta_id`), ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `excel_mgr_batch`
--
ALTER TABLE `excel_mgr_batch`
 ADD PRIMARY KEY (`excel_mgr_batch_id`);

--
-- Indexes for table `excel_mgr_log`
--
ALTER TABLE `excel_mgr_log`
 ADD PRIMARY KEY (`excel_mgr_log_id`), ADD KEY `excel_mgr_batch_id` (`excel_mgr_batch_id`), ADD KEY `excel_mgr_batch_id_2` (`excel_mgr_batch_id`);

--
-- Indexes for table `grid`
--
ALTER TABLE `grid`
 ADD PRIMARY KEY (`grid_id`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
 ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `group_user`
--
ALTER TABLE `group_user`
 ADD PRIMARY KEY (`group_user_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
 ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `match_def`
--
ALTER TABLE `match_def`
 ADD PRIMARY KEY (`match_def_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
 ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_user`
--
ALTER TABLE `project_user`
 ADD PRIMARY KEY (`project_usr_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
 ADD PRIMARY KEY (`role_id`), ADD KEY `acl` (`acl`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acl`
--
ALTER TABLE `acl`
MODIFY `acl_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `column_meta`
--
ALTER TABLE `column_meta`
MODIFY `column_meta_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `excel_mgr_batch`
--
ALTER TABLE `excel_mgr_batch`
MODIFY `excel_mgr_batch_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `excel_mgr_log`
--
ALTER TABLE `excel_mgr_log`
MODIFY `excel_mgr_log_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `grid`
--
ALTER TABLE `grid`
MODIFY `grid_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
MODIFY `group_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `group_user`
--
ALTER TABLE `group_user`
MODIFY `group_user_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `match_def`
--
ALTER TABLE `match_def`
MODIFY `match_def_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
MODIFY `project_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `project_user`
--
ALTER TABLE `project_user`
MODIFY `project_usr_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
MODIFY `role_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `column_meta`
--
ALTER TABLE `column_meta`
ADD CONSTRAINT `column_meta_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `role`
--
ALTER TABLE `role`
ADD CONSTRAINT `ACL` FOREIGN KEY (`acl`) REFERENCES `acl` (`acl_role_nm`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
