-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2012 年 07 月 14 日 16:16
-- 服务器版本: 5.5.16
-- PHP 版本: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `annoit`
--
CREATE DATABASE `annoit` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `annoit`;

-- --------------------------------------------------------

--
-- 表的结构 `ke1`
--

CREATE TABLE IF NOT EXISTS `ke1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `node_id` bigint(20) NOT NULL,
  `task_id` int(11) NOT NULL,
  `attr1` varchar(200) NOT NULL,
  `attr2` varchar(200) NOT NULL,
  `attr3` varchar(200) NOT NULL,
  `attr4` varchar(200) NOT NULL,
  `attr5` varchar(200) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='知识元属性表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ke2`
--

CREATE TABLE IF NOT EXISTS `ke2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `attr1` varchar(200) NOT NULL,
  `attr2` varchar(200) NOT NULL,
  `attr3` varchar(200) NOT NULL,
  `attr4` varchar(200) NOT NULL,
  `attr5` varchar(200) NOT NULL,
  `attr6` varchar(200) NOT NULL,
  `attr7` varchar(200) NOT NULL,
  `attr8` varchar(200) NOT NULL,
  `attr9` varchar(200) NOT NULL,
  `attr10` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `inner_id` int(11) NOT NULL,
  `offset_start` int(11) NOT NULL,
  `offset_end` int(11) NOT NULL,
  `real_start` int(11) NOT NULL,
  `real_end` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节点表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `node_logic`
--

CREATE TABLE IF NOT EXISTS `node_logic` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `inner_id` int(11) NOT NULL,
  `offset_start` int(11) NOT NULL,
  `offset_end` int(11) NOT NULL,
  `real_start` int(11) NOT NULL,
  `real_end` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='逻辑节点表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `relationship`
--

CREATE TABLE IF NOT EXISTS `relationship` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `node1_id` bigint(20) NOT NULL,
  `node2_id` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='同类节点关系表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `content` mediumtext,
  `length` int(11) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `info` varchar(120) DEFAULT NULL,
  `upload_time` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资源表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `child_id` varchar(200) DEFAULT NULL,
  `list_id` varchar(200) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '1',
  `allocate_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `submit_time` datetime DEFAULT NULL,
  `pass_time` datetime DEFAULT NULL,
  `pass_user_id` int(11) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`),
  KEY `unit_id` (`unit_id`),
  KEY `user_id` (`user_id`),
  KEY `pass_user_id` (`pass_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `unit`
--

CREATE TABLE IF NOT EXISTS `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `logic_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `begin_offset` int(11) NOT NULL,
  `end_offset` int(11) NOT NULL,
  `edit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_assign` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_unit_resource` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='逻辑单元表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email_address` varchar(50) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '0',
  `is_logged_in` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `username`, `password`, `email_address`, `role`, `is_logged_in`) VALUES
(1, 'annoit', 'annoit', 'admin', 'admin', 'admin@admin.com', 1, '0');

--
-- 限制导出的表
--

--
-- 限制表 `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `node_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`);

--
-- 限制表 `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_ibfk_3` FOREIGN KEY (`pass_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `unit`
--
ALTER TABLE `unit`
  ADD CONSTRAINT `fk_unit_resource` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
