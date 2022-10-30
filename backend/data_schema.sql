-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: database
-- Generation Time: Oct 28, 2022 at 01:51 AM
-- Server version: 10.9.3-MariaDB-1:10.9.3+maria~ubu2204
-- PHP Version: 8.0.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `flatvote_document`
--

CREATE TABLE `flatvote_document` (
  `id` int(11) NOT NULL,
  `txt_name` varchar(200) COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'document description',
  `id_user` int(11) NOT NULL COMMENT 'moderator / owner',
  `ts_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'original date',
  `ts_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'last modification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `flatvote_document`
--

INSERT INTO `flatvote_document` (`id`, `txt_name`, `id_user`, `ts_date`, `ts_modified`) VALUES
(1, 'original document', 0, '2020-11-25 21:13:10', '2020-11-25 21:13:10');

-- --------------------------------------------------------

--
-- Table structure for table `flatvote_doc_entries`
--

CREATE TABLE `flatvote_doc_entries` (
  `id` int(11) NOT NULL,
  `id_document` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `ts_date` datetime NOT NULL DEFAULT current_timestamp(),
  `txt_element` text COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'actual text contained in this entry',
  `enm_condition` enum('removed','modified','accepted','disputed','considered') COLLATE utf8mb3_unicode_ci NOT NULL COMMENT '''removed'',''modified'',''accepted'',''disputed'',''considered''',
  `id_question` int(11) NOT NULL COMMENT 'the question that resolves this entry'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `flatvote_doc_entries`
--

INSERT INTO `flatvote_doc_entries` (`id`, `id_document`, `id_user`, `ts_date`, `txt_element`, `enm_condition`, `id_question`) VALUES
(1, 1, 0, '2020-11-25 21:10:11', 'this is a test of the system and not of you', 'considered', 4),
(2, 1, 0, '2020-11-25 21:23:13', 'when things go boom at night', 'accepted', 5),
(3, 1, 1, '2020-11-25 21:49:02', 'sometimes things are one way, sometimes are the other way', 'disputed', 0),
(4, 1, 1, '2020-11-25 21:49:02', 'cheese is super tasty', 'modified', 1),
(5, 1, 2, '2020-11-25 21:49:35', 'i cri whin ingils disirvi ti dii', 'removed', 2);

-- --------------------------------------------------------

--
-- Table structure for table `flatvote_options`
--

CREATE TABLE `flatvote_options` (
  `id` int(11) NOT NULL,
  `id_question` int(11) NOT NULL,
  `txt_description` varchar(200) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ts_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='choices that can be made on each question';

--
-- Dumping data for table `flatvote_options`
--

INSERT INTO `flatvote_options` (`id`, `id_question`, `txt_description`, `ts_date`) VALUES
(1, 0, 'choose cake', '2020-11-24 18:46:27'),
(2, 0, 'choose death', '2020-11-24 18:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `flatvote_questions`
--

CREATE TABLE `flatvote_questions` (
  `id` int(11) NOT NULL,
  `txt_question` varchar(200) COLLATE utf8mb3_unicode_ci NOT NULL,
  `id_options` int(11) NOT NULL,
  `enm_status` enum('canceled','proposal period','notify period','vote period','vote over') COLLATE utf8mb3_unicode_ci NOT NULL COMMENT '''canceled'',''proposal period'',''notify period'',''vote period'',''vote over''',
  `ts_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='question text to get a vote on';

--
-- Dumping data for table `flatvote_questions`
--

INSERT INTO `flatvote_questions` (`id`, `txt_question`, `id_options`, `enm_status`, `ts_date`) VALUES
(1, 'what do you want', 0, 'vote period', '2020-11-24 18:46:14'),
(2, 'is this all there is', 1, 'proposal period', '2020-11-25 16:38:37'),
(3, 'things should be nice', 3, 'notify period', '2020-11-25 17:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `flatvote_user`
--

CREATE TABLE `flatvote_user` (
  `id` int(11) NOT NULL,
  `ts_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `txt_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `pwd_password` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ts_last_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `txt_status` varchar(200) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'status'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `flatvote_user`
--

INSERT INTO `flatvote_user` (`id`, `ts_creation`, `txt_name`, `pwd_password`, `ts_last_login`, `txt_status`) VALUES
(1, '2022-10-28 01:50:57', 'qwe', '$2y$10$YHXy6/No182PRiSEeVyG/eIDCxf6klZZZNiefGjUdzNMn4d5xt/cS', '2022-10-28 01:51:10', 'status');

-- --------------------------------------------------------

--
-- Table structure for table `flatvote_votes`
--

CREATE TABLE `flatvote_votes` (
  `id` int(11) UNSIGNED NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  `id_question` int(11) NOT NULL,
  `id_ballot` int(11) NOT NULL COMMENT 'the actual vote option id',
  `txt_comment` varchar(200) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='all votes for all questions';

--
-- Dumping data for table `flatvote_votes`
--

INSERT INTO `flatvote_votes` (`id`, `timestamp`, `id_user`, `id_question`, `id_ballot`, `txt_comment`) VALUES
(4, '2020-11-24 18:28:59', 0, 0, 0, 'first'),
(6, '2020-11-24 18:29:20', 0, 1, 1, 'sec'),
(7, '2020-11-24 19:06:18', 0, 3, 1, 'cake'),
(10, '2020-11-25 07:55:48', 1, 0, 2, 'hello'),
(20, '2020-11-25 11:45:46', 2, 0, 2, 'hello'),
(21, '2020-11-25 11:47:18', 3, 0, 2, 'hello'),
(23, '2020-11-25 11:51:27', 4, 0, 2, 'hello'),
(24, '2020-11-25 11:51:37', 6, 0, 2, 'hello'),
(25, '2020-11-25 11:52:26', 7, 0, 2, 'hello'),
(26, '2020-11-25 11:53:18', 8, 0, 2, 'hello'),
(30, '2020-11-25 16:17:37', 1, 1, 1, 'qwe'),
(32, '2020-11-25 17:18:19', 6, 1, 1, 'yeah'),
(33, '2020-11-25 17:35:00', 4, 1, 2, 'death'),
(35, '2021-02-21 03:36:52', 2, 1, 3, 'NULL');

-- --------------------------------------------------------

--
-- Table structure for table `test_table`
--

CREATE TABLE `test_table` (
  `id` int(11) NOT NULL,
  `test` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `test2` int(11) NOT NULL,
  `test3` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`) VALUES
('ruan'),
('frank'),
('james');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flatvote_document`
--
ALTER TABLE `flatvote_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `txt_name` (`txt_name`),
  ADD KEY `owner` (`id_user`);

--
-- Indexes for table `flatvote_doc_entries`
--
ALTER TABLE `flatvote_doc_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question` (`id_question`),
  ADD KEY `id_document` (`id_document`),
  ADD KEY `user who proposed ammendment` (`id_user`);

--
-- Indexes for table `flatvote_options`
--
ALTER TABLE `flatvote_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_question` (`id_question`) USING BTREE;

--
-- Indexes for table `flatvote_questions`
--
ALTER TABLE `flatvote_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_options` (`id_options`) USING BTREE;
ALTER TABLE `flatvote_questions` ADD FULLTEXT KEY `txt_question` (`txt_question`);

--
-- Indexes for table `flatvote_user`
--
ALTER TABLE `flatvote_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flatvote_votes`
--
ALTER TABLE `flatvote_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_vote` (`id_user`,`id_question`) USING BTREE,
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `test_table`
--
ALTER TABLE `test_table`
  ADD UNIQUE KEY `indexid` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `flatvote_document`
--
ALTER TABLE `flatvote_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `flatvote_doc_entries`
--
ALTER TABLE `flatvote_doc_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `flatvote_options`
--
ALTER TABLE `flatvote_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `flatvote_questions`
--
ALTER TABLE `flatvote_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `flatvote_user`
--
ALTER TABLE `flatvote_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `flatvote_votes`
--
ALTER TABLE `flatvote_votes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `test_table`
--
ALTER TABLE `test_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
