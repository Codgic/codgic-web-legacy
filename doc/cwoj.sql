-- phpMyAdmin SQL Dump
-- version 4.6.4deb1
-- https://www.phpmyadmin.net/
--
-- Generation Time: Nov 27, 2016 at 10:48 PM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cwoj`
--

DELIMITER $$
--
-- Functions
--
CREATE FUNCTION `get_problem_level` (`pid` INT) RETURNS INT(11) READS SQL DATA
BEGIN
RETURN IFNULL((SELECT (has_tex>>3)&7 FROM problem WHERE problem_id = pid),0);
END$$

CREATE FUNCTION `problem_flag_to_level` (`flag` INT) RETURNS INT(11) NO SQL
BEGIN
RETURN (flag>>3)&7;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `compileinfo`
--

CREATE TABLE `compileinfo` (
  `solution_id` int(11) NOT NULL DEFAULT '0',
  `error` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contest`
--

CREATE TABLE `contest` (
  `contest_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `defunct` tinyint(1) NOT NULL DEFAULT '0',
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `source` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `in_date` datetime NOT NULL,
  `has_tex` tinyint(4) NOT NULL DEFAULT '0',
  `judge_way` int(11) NOT NULL DEFAULT '0',
  `enroll_user` int(11) NOT NULL DEFAULT '0',
  `last_upd_time` datetime DEFAULT NULL,
  `need_update` int(11) NOT NULL DEFAULT '0',
  `hide_source_code` bit(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contest_detail`
--

CREATE TABLE `contest_detail` (
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contest_id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `result` smallint(6) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contest_owner`
--

CREATE TABLE `contest_owner` (
  `contest_id` int(11) NOT NULL,
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contest_problem`
--

CREATE TABLE `contest_problem` (
  `contest_id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `place` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contest_status`
--

CREATE TABLE `contest_status` (
  `contest_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tot_score` int(11) NOT NULL DEFAULT '0',
  `tot_time` int(32) NOT NULL DEFAULT '0',
  `rank` int(11) NOT NULL DEFAULT '0',
  `enroll_time` datetime NOT NULL,
  `leave_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experience_titles`
--

CREATE TABLE `experience_titles` (
  `experience` int(11) NOT NULL,
  `title` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experience_titles`
--

INSERT INTO `experience_titles` (`experience`, `title`) VALUES
(-999999, '张柯'),
(1, '小咸鱼'),
(75, '咸鱼'),
(150, '大咸鱼'),
(2048, '银蛤蟆'),
(0, ''),
(19260817, '长者'),
(8192, '金蛤蟆'),
(350, '铜蛤蟆');

-- --------------------------------------------------------

--
-- Table structure for table `level_experience`
--

CREATE TABLE `level_experience` (
  `level` int(11) NOT NULL,
  `experience` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `level_experience`
--

INSERT INTO `level_experience` (`level`, `experience`) VALUES
(0, 100),
(1, 100),
(2, 100),
(3, 100),
(4, 100),
(5, 100),
(6, 100),
(7, 100);

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE `mail` (
  `mail_id` int(11) NOT NULL,
  `to_user` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `from_user` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `new_mail` tinyint(1) NOT NULL DEFAULT '1',
  `flags` tinyint(4) UNSIGNED DEFAULT '0',
  `in_date` datetime DEFAULT NULL,
  `defunct` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL DEFAULT '0',
  `problem_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `depth` int(11) NOT NULL DEFAULT '0',
  `orderNum` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `in_date` datetime DEFAULT NULL,
  `defunct` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL DEFAULT '0',
  `author` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `time` datetime DEFAULT NULL,
  `importance` tinyint(4) NOT NULL DEFAULT '0',
  `privilege` tinyint(4) NOT NULL DEFAULT '0',
  `defunct` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`news_id`, `author`, `title`, `content`, `time`, `importance`, `privilege`, `defunct`) VALUES
(1, 'root', '欢迎来到CWOJ', '<b>CWOJ - 一个开源且毫无特色并且随时收到水王洪水威胁的信息竞赛刷题系统，现已向校内外同学开放~~~ 祝大家在这里玩得愉快！</b><hr>CWOJ Team:\r<br>jimmy19990: CWOJ前/后端\r<br>Void: CWOJ题库管理\r<br>dreamfly:一条出口咸鱼', '2015-12-12 18:45:21', 0, 0, 0),
(0, NULL, NULL, '<div class="text-center"><div style="font-size:28px"><b>Welcome to CWOJ</b></div><div style="font-size:18px">Built for you to code your future.</div></div>', NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE `preferences` (
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `property` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `problem`
--

CREATE TABLE `problem` (
  `problem_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `input` longtext COLLATE utf8mb4_unicode_ci,
  `output` longtext COLLATE utf8mb4_unicode_ci,
  `sample_input` longtext COLLATE utf8mb4_unicode_ci,
  `sample_output` longtext COLLATE utf8mb4_unicode_ci,
  `hint` longtext COLLATE utf8mb4_unicode_ci,
  `source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `in_date` datetime DEFAULT NULL,
  `time_limit` int(11) NOT NULL DEFAULT '0',
  `memory_limit` int(11) NOT NULL DEFAULT '0',
  `case_score` int(11) NOT NULL DEFAULT '0',
  `defunct` tinyint(1) NOT NULL DEFAULT '0',
  `accepted` int(11) DEFAULT '0',
  `submit` int(11) DEFAULT '0',
  `compare_way` int(11) NOT NULL DEFAULT '0',
  `has_tex` tinyint(4) NOT NULL DEFAULT '0',
  `submit_user` int(11) DEFAULT '0',
  `solved` int(11) DEFAULT '0',
  `case_time_limit` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `problem`
--

INSERT INTO `problem` (`problem_id`, `title`, `description`, `input`, `output`, `sample_input`, `sample_output`, `hint`, `source`, `in_date`, `time_limit`, `memory_limit`, `case_score`, `defunct`, `accepted`, `submit`, `ratio`, `compare_way`, `has_tex`, `submit_user`, `solved`, `case_time_limit`) VALUES
(1000, 'A+B 问题', '计算 a+b\r\n<pre>#include &lt;iostream&gt;\r\nusing namespace std;\r\nint main()\r\n{ \r\n    int a, b; \r\n    cin >> a >> b; \r\n    cout << a + b;\r\n}\r\n</pre>', '两个整数 a,b (保证a+b在int范围内)', '输出 a+b', '1 2', '3', '请使用标准输入输出~\r\n\r\n', '基础语法', '2016-11-19 18:10:45', 1000, 128000, 10, 0, 0, 0, 0, 0, 56, 0, 0, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `saved_contest`
--

CREATE TABLE `saved_contest` (
  `contest_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `savetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_problem`
--

CREATE TABLE `saved_problem` (
  `problem_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `savetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_wiki`
--

CREATE TABLE `saved_wiki` (
  `wiki_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `savetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `solution`
--

CREATE TABLE `solution` (
  `solution_id` int(11) NOT NULL DEFAULT '0',
  `problem_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `memory` int(11) NOT NULL DEFAULT '0',
  `in_date` datetime DEFAULT NULL,
  `result` smallint(6) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `language` tinyint(4) NOT NULL DEFAULT '0',
  `contest_id` int(11) DEFAULT NULL,
  `code_length` int(11) NOT NULL DEFAULT '0',
  `public_code` tinyint(1) NOT NULL DEFAULT '0',
  `malicious` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `source_code`
--

CREATE TABLE `source_code` (
  `solution_id` int(11) NOT NULL DEFAULT '0',
  `source` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submit` int(11) DEFAULT '0',
  `solved` int(11) DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `experience` int(11) NOT NULL DEFAULT '0',
  `defunct` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `ip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `accesstime` datetime DEFAULT NULL,
  `volume` int(11) NOT NULL DEFAULT '1',
  `language` int(11) NOT NULL DEFAULT '0',
  `password` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL,
  `nick` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motto` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privilege` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `submit`, `solved`, `score`, `experience`, `defunct`, `ip`, `accesstime`, `volume`, `language`, `password`, `reg_time`, `nick`, `school`, `motto`, `privilege`) VALUES
('root', 'cwoj@hello.world', 0, 0, 0, 0, 'N', '', '1970-01-01 00:00:00', 1, 0, 'CWOJUser125', '1970-01-01 00:00:00', 'admin', '', 'I\'m an evil administrator.', 15);

-- --------------------------------------------------------

--
-- Table structure for table `user_notes`
--

CREATE TABLE `user_notes` (
  `id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tags` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `edit_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wiki`
--

CREATE TABLE `wiki` (
  `id` int(11) NOT NULL,
  `wiki_id` int(11) NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `tags` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revision` int(11) NOT NULL DEFAULT '0',
  `is_max` tinyint(1) NOT NULL DEFAULT '1',
  `in_date` datetime NOT NULL,
  `privilege` tinyint(4) NOT NULL DEFAULT '0',
  `defunct` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `compileinfo`
--
ALTER TABLE `compileinfo`
  ADD PRIMARY KEY (`solution_id`);

--
-- Indexes for table `contest`
--
ALTER TABLE `contest`
  ADD PRIMARY KEY (`contest_id`);

--
-- Indexes for table `contest_detail`
--
ALTER TABLE `contest_detail`
  ADD UNIQUE KEY `cdk` (`user_id`,`contest_id`,`problem_id`),
  ADD KEY `cdi` (`user_id`,`contest_id`,`problem_id`,`result`,`score`,`time`);

--
-- Indexes for table `contest_owner`
--
ALTER TABLE `contest_owner`
  ADD UNIQUE KEY `contest_id` (`contest_id`,`user_id`),

--
-- Indexes for table `contest_problem`
--
ALTER TABLE `contest_problem`
  ADD UNIQUE KEY `problem_id` (`problem_id`,`contest_id`),
  ADD UNIQUE KEY `place` (`place`);

--
-- Indexes for table `contest_status`
--
ALTER TABLE `contest_status`
  ADD UNIQUE KEY `u_p` (`user_id`,`contest_id`);

--
-- Indexes for table `experience_titles`
--
ALTER TABLE `experience_titles`
  ADD PRIMARY KEY (`experience`);

--
-- Indexes for table `level_experience`
--
ALTER TABLE `level_experience`
  ADD PRIMARY KEY (`level`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD PRIMARY KEY (`mail_id`),
  ADD KEY `uid` (`to_user`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD UNIQUE KEY `news_id` (`news_id`);

--
-- Indexes for table `preferences`
--
ALTER TABLE `preferences`
  ADD UNIQUE KEY `u_p` (`user_id`,`property`);

--
-- Indexes for table `problem`
--
ALTER TABLE `problem`
  ADD PRIMARY KEY (`problem_id`);

--
-- Indexes for table `saved_contest`
--
ALTER TABLE `saved_contest`
  ADD UNIQUE KEY `u_p` (`user_id`,`contest_id`);

--
-- Indexes for table `saved_problem`
--
ALTER TABLE `saved_problem`
  ADD UNIQUE KEY `u_p` (`user_id`,`problem_id`);

--
-- Indexes for table `saved_wiki`
--
ALTER TABLE `saved_wiki`
  ADD UNIQUE KEY `u_p` (`user_id`,`wiki_id`);

--
-- Indexes for table `solution`
--
ALTER TABLE `solution`
  ADD PRIMARY KEY (`solution_id`),
  ADD KEY `uid` (`user_id`),
  ADD KEY `pid` (`problem_id`),
  ADD KEY `res` (`result`),
  ADD KEY `cid` (`contest_id`);

--
-- Indexes for table `source_code`
--
ALTER TABLE `source_code`
  ADD PRIMARY KEY (`solution_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `solve_submit` (`solved`,`submit`);

--
-- Indexes for table `user_notes`
--
ALTER TABLE `user_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_p` (`user_id`,`problem_id`);

--
-- Indexes for table `wiki`
--
ALTER TABLE `wiki`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_notes`
--
ALTER TABLE `user_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wiki`
--
ALTER TABLE `wiki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
