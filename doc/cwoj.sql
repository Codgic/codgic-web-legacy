-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE cwoj CHARACTER SET utf8mb4;
use cwoj;

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
-- Table structure for table `attend`
--

CREATE TABLE `attend` (
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contest_id` int(11) NOT NULL DEFAULT '0',
  `accepts` int(11) DEFAULT '0',
  `penalty` int(11) DEFAULT '0',
  `A_time` int(11) DEFAULT '0',
  `A_WrongSubmits` int(11) DEFAULT '0',
  `B_time` int(11) DEFAULT '0',
  `B_WrongSubmits` int(11) DEFAULT '0',
  `C_time` int(11) DEFAULT '0',
  `C_WrongSubmits` int(11) DEFAULT '0',
  `D_time` int(11) DEFAULT '0',
  `D_WrongSubmits` int(11) DEFAULT '0',
  `E_time` int(11) DEFAULT '0',
  `E_WrongSubmits` int(11) DEFAULT '0',
  `F_time` int(11) DEFAULT '0',
  `F_WrongSubmits` int(11) DEFAULT '0',
  `G_time` int(11) DEFAULT '0',
  `G_WrongSubmits` int(11) DEFAULT '0',
  `H_time` int(11) DEFAULT '0',
  `H_WrongSubmits` int(11) DEFAULT '0',
  `I_time` int(11) DEFAULT '0',
  `I_WrongSubmits` int(11) DEFAULT '0',
  `J_time` int(11) DEFAULT '0',
  `J_WrongSubmits` int(11) DEFAULT '0',
  `K_time` int(11) DEFAULT '0',
  `K_WrongSubmits` int(11) DEFAULT '0',
  `L_time` int(11) DEFAULT '0',
  `L_WrongSubmits` int(11) DEFAULT '0',
  `M_time` int(11) DEFAULT '0',
  `M_WrongSubmits` int(11) DEFAULT '0',
  `N_time` int(11) DEFAULT '0',
  `N_WrongSubmits` int(11) DEFAULT '0',
  `O_time` int(11) DEFAULT '0',
  `O_WrongSubmits` int(11) DEFAULT '0',
  `nick` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `defunct` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `num` int(11) NOT NULL DEFAULT '0',
  `problems` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `in_date` datetime DEFAULT NULL,
  `source` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_tex` tinyint(4) NOT NULL DEFAULT '0',
  `judge_way` int(11) NOT NULL DEFAULT '0',
  `enroll_user` int(11) NOT NULL DEFAULT '0',
  `last_rank_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contest_status`
--

CREATE TABLE `contest_status` (
  `contest_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `scores` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `results` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `times` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tot_scores` int(11) NOT NULL DEFAULT '0',
  `tot_times` int(32) NOT NULL DEFAULT '0',
  `rank` int(11) NOT NULL DEFAULT '0'
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
(-9999, '张柯'),
(10, '蒟蒻'),
(40, '学民'),
(500, '神犇'),
(200, '题霸'),
(100, '优秀'),
(700, 'AC自动机'),
(320, '大牛'),
(50, '单身三十年');

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
(0, 1),
(1, 5),
(2, 10),
(3, 20),
(4, 40),
(5, 70),
(6, 110),
(7, 160);

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
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `time` datetime DEFAULT NULL,
  `importance` tinyint(4) NOT NULL DEFAULT '0',
  `privilege` tinyint(4) NOT NULL DEFAULT '0',
  `defunct` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`news_id`, `user_id`, `title`, `content`, `time`, `importance`, `privilege`, `defunct`) VALUES
(0, '', NULL, '<div class="text-center"><p><b><font size=6>Welcome to CWOJ</font></b></p><font size=4>Built for you to code your future</font></div>', '2016-07-24 17:11:52', 0, 0, 'N'),
(1, '', 'Welcome to CWOJ', 'Built for you to code your future...', '2016-07-24 17:11:45', 1, 0, 'N');

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE `preferences` (
  `id` int(11) NOT NULL,
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
  `defunct` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `contest_id` int(11) DEFAULT NULL,
  `accepted` int(11) DEFAULT '0',
  `submit` int(11) DEFAULT '0',
  `ratio` tinyint(4) NOT NULL DEFAULT '0',
  `compare_way` int(11) NOT NULL DEFAULT '0',
  `has_tex` tinyint(4) NOT NULL DEFAULT '0',
  `submit_user` int(11) DEFAULT '0',
  `solved` int(11) DEFAULT '0',
  `case_time_limit` int(11) NOT NULL DEFAULT '0',
  `rejudged` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `problem`
--

INSERT INTO `problem` (`problem_id`, `title`, `description`, `input`, `output`, `sample_input`, `sample_output`, `hint`, `source`, `in_date`, `time_limit`, `memory_limit`, `case_score`, `defunct`, `contest_id`, `accepted`, `submit`, `ratio`, `compare_way`, `has_tex`, `submit_user`, `solved`, `case_time_limit`, `rejudged`) VALUES
(1000, 'A+B 问题', '计算 a+b', '两个整数 a,b (保证a+b在int范围内)', '输出 a+b', '1 2', '3', '请使用标准输入输出~\r\n', '基础语法', '2016-07-24 17:13:09', 0, 65536, 10, 'N', NULL, 0, 0, 0, 0, 0, 0, 0, 1000, 'N');

-- --------------------------------------------------------

--
-- Table structure for table `saved_contest`
--

CREATE TABLE `saved_contest` (
  `id` int(11) NOT NULL,
  `contest_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `savetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_problem`
--

CREATE TABLE `saved_problem` (
  `id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL DEFAULT '0',
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
  `info` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` tinyint(4) NOT NULL DEFAULT '0',
  `contest_id` int(11) DEFAULT NULL,
  `valid` tinyint(4) NOT NULL DEFAULT '1',
  `num` tinyint(4) NOT NULL DEFAULT '-1',
  `code_length` int(11) NOT NULL DEFAULT '0',
  `public_code` tinyint(1) NOT NULL DEFAULT '0'
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
  `nick` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privilege` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `submit`, `solved`, `score`, `experience`, `defunct`, `ip`, `accesstime`, `volume`, `language`, `password`, `reg_time`, `nick`, `school`, `motto`, `privilege`) VALUES
('root', 'webmaster@localhost', 0, 0, 0, 0, 'N', '127.0.0.1', '2016-07-24 17:16:24', 1, 0, 'CWOJUser125', '2015-11-25 11:25:25', 'admin', 'CFLS', NULL, 15);

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

--
-- Dumping data for table `user_notes`
--

INSERT INTO `user_notes` (`id`, `problem_id`, `user_id`, `tags`, `content`, `edit_time`) VALUES
(0, 0, 'root', '', '<div class="panel panel-success">\n    <div class="panel-heading">\n      <h4 class="panel-title">\n        <a data-toggle="collapse" data-parent="#accordion" \n          href="#algorithm">\n          按算法分类\n        </a>\n      </h4>\n    </div>\n    <div id="algorithm" class="panel-collapse collapse in">\n      <div class="panel-body">\n        <p><a href="search.php?q=基础语法">基础语法</a>  \n<a href="search.php?q=数组">数组</a>  \n<a href="search.php?q=字符串">字符串</a>  \n<a href="search.php?q=数论">数论</a>  \n<a href="search.php?q=高精度">高精度</a>  \n<a href="search.php?q=模拟">模拟</a>  \n<a href="search.php?q=动态规划">动态规划</a>  \n<a href="search.php?q=贪心">贪心</a>  \n<a href="search.php?q=搜索">搜索</a>  \n<a href="search.php?q=二分">二分查找</a></p>\n<p><a href="search.php?q=数据结构">数据结构</a>  \n<a href="search.php?q=树">树结构</a>  \n<a href="search.php?q=图">图结构</a></p>\n      </div>\n    </div>\n  </div>\n  <div class="panel panel-warning">\n    <div class="panel-heading">\n      <h4 class="panel-title">\n        <a data-toggle="collapse" data-parent="#accordion" \n          href="#difficulty">\n          按难度分类\n        </a>\n      </h4>\n    </div>\n    <div id="difficulty" class="panel-collapse collapse">\n      <div class="panel-body">\n        <a href="level.php?level=1">普及</a>  \n<a href="level.php?level=2">普及+</a>  \n<a href="level.php?level=3">提高</a>  \n<a href="level.php?level=4">提高+</a>  \n<a href="level.php?level=5">省选-</a>  \n<a href="level.php?level=6">省选</a>  \n<a href="level.php?level=7">省选+</a>  \n      </div>\n    </div>\n  </div>\n  <div class="panel panel-primary">\n    <div class="panel-heading">\n      <h4 class="panel-title">\n        <a data-toggle="collapse" data-parent="#accordion" \n          href="#source">\n          按来源分类\n        </a>\n      </h4>\n    </div>\n    <div id="source" class="panel-collapse collapse">\n      <div class="panel-body">\n        <a href="search.php?q=普及组">NOIP普及组</a>  \n<a href="search.php?q=提高组">NOIP提高组</a>  \n<a href="search.php?q=省选">省选</a>  \n<a href="search.php?q=NOI2">NOI</a>  <!--注释: 防止NOI与NOIP混淆-->		 \n<a href="search.php?q=IOI">IOI</a>  \n<a href="search.php?q=UESTC">UESTC</a>  \n<a href="search.php?q=USACO">USACO</a>  \n<a href="search.php?q=原创">原创</a>  \n      </div>\n    </div>\n  </div>', '2016-07-10 14:14:51');

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
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`id`),
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_p` (`user_id`,`contest_id`);

--
-- Indexes for table `saved_problem`
--
ALTER TABLE `saved_problem`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_p` (`user_id`,`problem_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `preferences`
--
ALTER TABLE `preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `saved_contest`
--
ALTER TABLE `saved_contest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `saved_problem`
--
ALTER TABLE `saved_problem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_notes`
--
ALTER TABLE `user_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
