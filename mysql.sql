-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 09, 2018 at 02:57 PM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `steemauto`
--

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `id` int(11) NOT NULL,
  `user` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `commentupvote`
--

CREATE TABLE `commentupvote` (
  `id` int(11) NOT NULL,
  `user` text,
  `commenter` text,
  `weight` int(11) NOT NULL DEFAULT '10000',
  `aftermin` int(11) DEFAULT '0',
  `enable` int(11) NOT NULL DEFAULT '1',
  `todayvote` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `month` int(11) DEFAULT NULL,
  `amount` float NOT NULL DEFAULT '0',
  `type` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fanbase`
--

CREATE TABLE `fanbase` (
  `id` int(11) NOT NULL,
  `fan` text NOT NULL,
  `follower` text NOT NULL,
  `weight` int(5) NOT NULL,
  `aftermin` int(2) NOT NULL DEFAULT '0',
  `dailylimit` int(2) NOT NULL DEFAULT '5',
  `limitleft` int(2) NOT NULL DEFAULT '5',
  `enable` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fans`
--

CREATE TABLE `fans` (
  `id` int(11) NOT NULL,
  `fan` text NOT NULL,
  `followers` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `trailer` text NOT NULL,
  `follower` text NOT NULL,
  `weight` int(11) NOT NULL,
  `votingway` int(11) NOT NULL DEFAULT '1',
  `fcurator` int(11) NOT NULL DEFAULT '1',
  `aftermin` int(11) NOT NULL DEFAULT '0',
  `enable` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user` mediumtext CHARACTER SET utf8,
  `title` text,
  `content` mediumtext,
  `date` bigint(20) DEFAULT NULL,
  `maintag` text CHARACTER SET utf8,
  `json` text CHARACTER SET utf8,
  `permlink` mediumtext CHARACTER SET utf8,
  `status` int(11) NOT NULL DEFAULT '0',
  `upvote` int(11) NOT NULL DEFAULT '0',
  `rewards` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `trailers`
--

CREATE TABLE `trailers` (
  `id` int(11) NOT NULL,
  `user` text NOT NULL,
  `description` text,
  `followers` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `upvotedcomments`
--

CREATE TABLE `upvotedcomments` (
  `id` int(11) NOT NULL,
  `user` text,
  `permlink` text,
  `time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `upvotelater`
--

CREATE TABLE `upvotelater` (
  `id` int(11) NOT NULL,
  `voter` text,
  `author` text,
  `permlink` text,
  `weight` int(11) DEFAULT NULL,
  `time` bigint(20) DEFAULT NULL,
  `trail_fan` int(11) NOT NULL DEFAULT '0',
  `trailer` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `user` text NOT NULL,
  `email` text,
  `pw` text,
  `memo` text,
  `enable` int(11) NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL DEFAULT '0',
  `claimreward` int(11) NOT NULL DEFAULT '0',
  `current_power` float NOT NULL DEFAULT '0',
  `limit_power` float NOT NULL DEFAULT '70',
  `sp` float NOT NULL DEFAULT '1000',
  `paused` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commentupvote`
--
ALTER TABLE `commentupvote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enable` (`enable`),
  ADD KEY `todayvote` (`todayvote`);
ALTER TABLE `commentupvote` ADD FULLTEXT KEY `commenter` (`commenter`);
ALTER TABLE `commentupvote` ADD FULLTEXT KEY `user` (`user`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fanbase`
--
ALTER TABLE `fanbase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enable` (`enable`),
  ADD KEY `limitleft` (`limitleft`);
ALTER TABLE `fanbase` ADD FULLTEXT KEY `fan` (`fan`);
ALTER TABLE `fanbase` ADD FULLTEXT KEY `follower` (`follower`);

--
-- Indexes for table `fans`
--
ALTER TABLE `fans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followers` (`followers`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enable` (`enable`);
ALTER TABLE `followers` ADD FULLTEXT KEY `trailer` (`trailer`);
ALTER TABLE `followers` ADD FULLTEXT KEY `follower` (`follower`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`),
  ADD KEY `status` (`status`);
ALTER TABLE `posts` ADD FULLTEXT KEY `user` (`user`);
ALTER TABLE `posts` ADD FULLTEXT KEY `permlink` (`permlink`);

--
-- Indexes for table `trailers`
--
ALTER TABLE `trailers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followers` (`followers`);
ALTER TABLE `trailers` ADD FULLTEXT KEY `user` (`user`);

--
-- Indexes for table `upvotedcomments`
--
ALTER TABLE `upvotedcomments`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `upvotedcomments` ADD FULLTEXT KEY `user` (`user`);
ALTER TABLE `upvotedcomments` ADD FULLTEXT KEY `permlink` (`permlink`);

--
-- Indexes for table `upvotelater`
--
ALTER TABLE `upvotelater`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time` (`time`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `limit_power` (`limit_power`),
  ADD KEY `id` (`id`),
  ADD KEY `current_power` (`current_power`),
  ADD KEY `paused` (`paused`),
  ADD KEY `uid` (`uid`);
ALTER TABLE `users` ADD FULLTEXT KEY `user` (`user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commentupvote`
--
ALTER TABLE `commentupvote`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fanbase`
--
ALTER TABLE `fanbase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fans`
--
ALTER TABLE `fans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trailers`
--
ALTER TABLE `trailers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `upvotedcomments`
--
ALTER TABLE `upvotedcomments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `upvotelater`
--
ALTER TABLE `upvotelater`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
