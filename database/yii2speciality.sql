-- phpMyAdmin SQL Dump
-- version 4.0.10.6
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 30 2015 г., 09:13
-- Версия сервера: 5.5.41-log
-- Версия PHP: 5.4.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `yii2speciality`
--
CREATE DATABASE IF NOT EXISTS `yii2speciality` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yii2speciality`;

DELIMITER $$
--
-- Процедуры
--
DROP PROCEDURE IF EXISTS `insertSpecialitySubjects`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertSpecialitySubjects`(IN spec SMALLINT, IN max SMALLINT, IN beg SMALLINT, IN sp TINYINT)
BEGIN
DECLARE i SMALLINT DEFAULT beg;
WHILE i<max DO
INSERT INTO `specSubjects` (`idSp`, `idSub`, `canPassed`) VALUES (spec, i, sp);
SET i = i + 1;
END WHILE;
END$$

DROP PROCEDURE IF EXISTS `insertSubjects`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertSubjects`(IN max SMALLINT, IN beg SMALLINT)
BEGIN
DECLARE i SMALLINT DEFAULT beg;
WHILE i<max DO
INSERT INTO `subjects` (`name`) VALUES (CONCAT('subject', i));
SET i = i + 1;
END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `application`
--

DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idSp` smallint(5) unsigned NOT NULL,
  `idSo` tinyint(3) unsigned DEFAULT NULL,
  `idEv` smallint(5) unsigned DEFAULT NULL,
  `idTech` tinyint(3) unsigned DEFAULT NULL,
  `idUsr` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `city` varchar(254) NOT NULL,
  `info` text,
  `benefits` tinyint(1) unsigned NOT NULL,
  `payment` tinyint(1) unsigned NOT NULL,
  `course` tinyint(3) unsigned NOT NULL,
  `term` float unsigned NOT NULL,
  `cost` mediumint(8) unsigned NOT NULL,
  `dateInsert` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idSp` (`idSp`),
  KEY `idSo` (`idSo`),
  KEY `idEv` (`idEv`),
  KEY `idTech` (`idTech`),
  KEY `idUsr` (`idUsr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Дамп данных таблицы `application`
--

INSERT INTO `application` (`id`, `idSp`, `idSo`, `idEv`, `idTech`, `idUsr`, `name`, `email`, `city`, `info`, `benefits`, `payment`, `course`, `term`, `cost`, `dateInsert`) VALUES
(36, 1, 2, 1, 1, 2, 'Иванов Иван Иванович', 'topor434@mail.ru', 'Брест', 'Вариант заявки с дисциплинами для перезачета', 1, 0, 4, 4, 100000, 1438191430),
(37, 2, 2, 2, 1, 7, 'Иванов Петр Алексеевич', 'example11111@mail.ru', 'Брест', 'Просто дополнительная информация', 0, 1, 3, 3, 102500, 1438192951),
(38, 1, 2, 3, 2, 2, 'Сидоров Сидр Сидорович', 'topor434@mail.ru', 'Пинск', 'Дополнительная информация от пользователя', 1, 1, 3, 3, 117000, 1438225019),
(39, 4, 2, 2, 1, 8, 'Степанов Степан Степанович', 'example2222222@mail.ru', 'Барановичи', '', 1, 0, 2, 2, 52500, 1438225789);

-- --------------------------------------------------------

--
-- Структура таблицы `educationVariant`
--

DROP TABLE IF EXISTS `educationVariant`;
CREATE TABLE IF NOT EXISTS `educationVariant` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(3002) NOT NULL,
  `subjectsYear` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `educationVariant`
--

INSERT INTO `educationVariant` (`id`, `name`, `description`, `subjectsYear`) VALUES
(1, 'Заочное обучение', '<p>Вы предполагаете освоить 1 курс (10 учебных дисциплин) за 1 календарный год</p>', 10),
(2, 'Экстернат', '<p>Вы предполагаете освоить 2 курса (20 учебных дисциплин) за 1 календарный год</p>', 20),
(3, 'Совмещенное обучение (заочное + экстернат)', '<p>Вы предполагаете освоить от 1 до 2 курсов (15 учебных дисциплин) за 1 календарный год</p>', 15);

-- --------------------------------------------------------

--
-- Структура таблицы `speciality`
--

DROP TABLE IF EXISTS `speciality`;
CREATE TABLE IF NOT EXISTS `speciality` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `speciality`
--

INSERT INTO `speciality` (`id`, `name`) VALUES
(1, 'Экономика'),
(2, 'Менеджмент'),
(3, 'Бизнес информатика');

-- --------------------------------------------------------

--
-- Структура таблицы `specSubjects`
--

DROP TABLE IF EXISTS `specSubjects`;
CREATE TABLE IF NOT EXISTS `specSubjects` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idSp` smallint(5) unsigned NOT NULL,
  `idSub` smallint(5) unsigned NOT NULL,
  `canPassed` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idSp` (`idSp`),
  KEY `idSub` (`idSub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=196 ;

--
-- Дамп данных таблицы `specSubjects`
--

INSERT INTO `specSubjects` (`id`, `idSp`, `idSub`, `canPassed`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 0),
(3, 3, 1, 1),
(4, 1, 2, 1),
(5, 1, 3, 1),
(6, 1, 4, 1),
(7, 1, 5, 1),
(8, 1, 6, 1),
(9, 1, 7, 1),
(10, 1, 8, 1),
(11, 1, 9, 1),
(12, 1, 10, 1),
(13, 1, 11, 1),
(14, 1, 12, 1),
(15, 1, 13, 1),
(16, 1, 14, 1),
(17, 1, 15, 1),
(18, 1, 16, 1),
(19, 1, 17, 1),
(20, 1, 18, 1),
(21, 1, 19, 1),
(22, 1, 20, 1),
(23, 1, 21, 1),
(24, 1, 22, 1),
(25, 1, 23, 1),
(26, 1, 24, 1),
(27, 1, 25, 0),
(28, 1, 26, 0),
(29, 1, 27, 0),
(30, 1, 28, 0),
(31, 1, 29, 0),
(32, 1, 30, 0),
(33, 1, 31, 0),
(34, 1, 32, 0),
(35, 1, 33, 0),
(36, 1, 34, 0),
(37, 1, 35, 0),
(38, 1, 36, 0),
(39, 1, 37, 0),
(40, 1, 38, 0),
(41, 1, 39, 0),
(42, 1, 40, 0),
(43, 1, 41, 0),
(44, 1, 42, 0),
(45, 1, 43, 0),
(46, 1, 44, 0),
(47, 1, 45, 0),
(48, 2, 2, 1),
(49, 2, 3, 1),
(50, 2, 4, 1),
(51, 2, 5, 1),
(52, 2, 6, 1),
(53, 2, 7, 1),
(54, 2, 8, 1),
(55, 2, 9, 1),
(56, 2, 10, 1),
(57, 2, 11, 1),
(58, 2, 12, 1),
(59, 2, 13, 1),
(60, 2, 14, 1),
(61, 2, 15, 1),
(62, 2, 17, 0),
(63, 2, 18, 0),
(64, 2, 19, 0),
(65, 2, 20, 0),
(66, 2, 21, 0),
(67, 2, 22, 0),
(68, 2, 23, 0),
(69, 2, 24, 0),
(70, 2, 25, 0),
(71, 2, 26, 0),
(72, 2, 27, 0),
(73, 2, 28, 0),
(74, 2, 29, 0),
(75, 2, 30, 0),
(76, 2, 31, 0),
(77, 2, 32, 0),
(78, 2, 33, 0),
(79, 2, 34, 0),
(80, 2, 35, 0),
(81, 2, 36, 0),
(82, 2, 37, 0),
(83, 2, 38, 0),
(84, 2, 39, 0),
(85, 2, 40, 0),
(86, 2, 41, 0),
(87, 2, 42, 0),
(88, 2, 43, 0),
(89, 2, 44, 0),
(90, 2, 45, 0),
(91, 3, 2, 1),
(92, 3, 3, 1),
(93, 3, 4, 1),
(94, 3, 5, 1),
(95, 3, 6, 1),
(96, 3, 7, 1),
(97, 3, 8, 1),
(98, 3, 9, 1),
(99, 3, 10, 1),
(100, 3, 11, 1),
(101, 3, 12, 1),
(102, 3, 13, 1),
(103, 3, 14, 1),
(104, 3, 15, 1),
(105, 3, 16, 1),
(106, 3, 17, 1),
(107, 3, 18, 1),
(108, 3, 19, 1),
(109, 3, 20, 1),
(110, 3, 21, 1),
(111, 3, 22, 1),
(112, 3, 23, 1),
(113, 3, 24, 1),
(114, 3, 25, 1),
(115, 3, 27, 0),
(116, 3, 28, 0),
(117, 3, 29, 0),
(118, 3, 30, 0),
(119, 3, 31, 0),
(120, 3, 32, 0),
(121, 3, 33, 0),
(122, 3, 34, 0),
(123, 3, 35, 0),
(124, 3, 36, 0),
(125, 3, 37, 0),
(126, 3, 38, 0),
(127, 3, 39, 0),
(128, 3, 40, 0),
(129, 3, 41, 0),
(130, 3, 42, 0),
(131, 3, 43, 0),
(132, 3, 44, 0),
(133, 3, 45, 0),
(134, 4, 1, 0),
(135, 4, 2, 0),
(136, 4, 3, 0),
(137, 4, 4, 0),
(138, 4, 5, 0),
(139, 4, 6, 0),
(140, 4, 7, 0),
(141, 4, 8, 0),
(142, 4, 9, 0),
(143, 4, 10, 0),
(144, 4, 11, 0),
(145, 4, 12, 0),
(146, 4, 13, 0),
(147, 4, 14, 0),
(148, 4, 15, 0),
(149, 4, 17, 1),
(150, 4, 18, 1),
(151, 4, 19, 1),
(152, 4, 20, 1),
(153, 4, 21, 1),
(154, 4, 22, 1),
(155, 4, 23, 1),
(156, 4, 24, 1),
(157, 4, 25, 1),
(158, 5, 1, 1),
(159, 5, 2, 1),
(160, 5, 3, 1),
(161, 5, 4, 1),
(162, 5, 5, 1),
(163, 5, 6, 1),
(164, 5, 7, 1),
(165, 5, 8, 1),
(166, 5, 9, 1),
(167, 5, 10, 1),
(168, 5, 11, 1),
(169, 5, 12, 1),
(170, 5, 13, 1),
(171, 5, 14, 1),
(172, 5, 15, 1),
(173, 5, 16, 1),
(174, 5, 17, 1),
(175, 5, 18, 1),
(176, 5, 19, 1),
(177, 5, 21, 0),
(178, 5, 22, 0),
(179, 5, 23, 0),
(180, 5, 24, 0),
(181, 5, 25, 0),
(182, 5, 26, 0),
(183, 5, 27, 0),
(184, 5, 28, 0),
(185, 5, 29, 0),
(186, 5, 30, 0),
(187, 5, 31, 0),
(188, 5, 32, 0),
(189, 5, 33, 0),
(190, 5, 34, 0),
(191, 5, 35, 0),
(192, 5, 36, 0),
(193, 5, 37, 0),
(194, 5, 38, 0),
(195, 5, 39, 0);

--
-- Триггеры `specSubjects`
--
DROP TRIGGER IF EXISTS `countSubjects`;
DELIMITER //
CREATE TRIGGER `countSubjects` AFTER INSERT ON `specSubjects`
 FOR EACH ROW UPDATE `variantSp` SET `countSubjects` = `countSubjects` + 1 WHERE `id` = NEW.`idSp`
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `studyCost`
--

DROP TABLE IF EXISTS `studyCost`;
CREATE TABLE IF NOT EXISTS `studyCost` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cost` smallint(5) unsigned NOT NULL,
  `idTech` tinyint(3) unsigned DEFAULT NULL,
  `idEv` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idTech` (`idTech`),
  KEY `idEv` (`idEv`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `studyCost`
--

INSERT INTO `studyCost` (`id`, `cost`, `idTech`, `idEv`) VALUES
(2, 2500, 1, NULL),
(3, 2800, 2, 1),
(4, 3000, 2, 3),
(5, 3000, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `studyOption`
--

DROP TABLE IF EXISTS `studyOption`;
CREATE TABLE IF NOT EXISTS `studyOption` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(3002) NOT NULL,
  `canPassed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `studyOption`
--

INSERT INTO `studyOption` (`id`, `name`, `description`, `canPassed`) VALUES
(1, 'Полный учебный курс', '<p>Выберите вариант, если Вашим документом о предыдущем образовании, является:</p><ul><li>аттестат о среднем (полном) общем образовании</li><li>диплом о начальном профессиональном образовании</li></ul>', 0),
(2, 'Сокращенный учебный курс', '<p>Выберите вариант, если хотите чтобы Вам были перезачтены несколько учебных дисциплин освоенных в другом ВУЗе или Техникуме</p>', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Дамп данных таблицы `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(1, 'subject1'),
(2, 'subject2'),
(3, 'subject3'),
(4, 'subject4'),
(5, 'subject5'),
(6, 'subject6'),
(7, 'subject7'),
(8, 'subject8'),
(9, 'subject9'),
(10, 'subject10'),
(11, 'subject11'),
(12, 'subject12'),
(13, 'subject13'),
(14, 'subject14'),
(15, 'subject15'),
(16, 'subject16'),
(17, 'subject17'),
(18, 'subject18'),
(19, 'subject19'),
(20, 'subject20'),
(21, 'subject21'),
(22, 'subject22'),
(23, 'subject23'),
(24, 'subject24'),
(25, 'subject25'),
(26, 'subject26'),
(27, 'subject27'),
(28, 'subject28'),
(29, 'subject29'),
(30, 'subject30'),
(31, 'subject31'),
(32, 'subject32'),
(33, 'subject33'),
(34, 'subject34'),
(35, 'subject35'),
(36, 'subject36'),
(37, 'subject37'),
(38, 'subject38'),
(39, 'subject39'),
(40, 'subject40'),
(41, 'subject41'),
(42, 'subject42'),
(43, 'subject43'),
(44, 'subject44'),
(45, 'subject45'),
(46, 'subject46'),
(47, 'subject47'),
(48, 'subject48'),
(49, 'subject49'),
(50, 'subject50'),
(51, 'subject51'),
(52, 'subject52'),
(53, 'subject53'),
(54, 'subject54'),
(55, 'subject55'),
(56, 'subject56'),
(57, 'subject57'),
(58, 'subject58'),
(59, 'subject59'),
(60, 'subject60'),
(61, 'subject61'),
(62, 'subject62'),
(63, 'subject63'),
(64, 'subject64'),
(65, 'subject65'),
(66, 'subject66'),
(67, 'subject67'),
(68, 'subject68'),
(69, 'subject69'),
(70, 'subject70'),
(71, 'subject71'),
(72, 'subject72'),
(73, 'subject73'),
(74, 'subject74'),
(75, 'subject75'),
(76, 'subject76'),
(77, 'subject77'),
(78, 'subject78'),
(79, 'subject79'),
(80, 'subject80'),
(81, 'subject81'),
(82, 'subject82'),
(83, 'subject83'),
(84, 'subject84'),
(85, 'subject85'),
(86, 'subject86'),
(87, 'subject87'),
(88, 'subject88'),
(89, 'subject89'),
(90, 'subject90'),
(91, 'subject91'),
(92, 'subject92'),
(93, 'subject93'),
(94, 'subject94'),
(95, 'subject95'),
(96, 'subject96'),
(97, 'subject97'),
(98, 'subject98'),
(99, 'subject99');

-- --------------------------------------------------------

--
-- Структура таблицы `subjectsPassed`
--

DROP TABLE IF EXISTS `subjectsPassed`;
CREATE TABLE IF NOT EXISTS `subjectsPassed` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idApp` smallint(5) unsigned NOT NULL,
  `idSp` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idSb` (`idSp`),
  KEY `idApp` (`idApp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=112 ;

--
-- Дамп данных таблицы `subjectsPassed`
--

INSERT INTO `subjectsPassed` (`id`, `idApp`, `idSp`) VALUES
(95, 36, 7),
(96, 36, 8),
(97, 36, 9),
(98, 36, 10),
(99, 36, 11),
(100, 37, 54),
(101, 37, 55),
(102, 37, 56),
(103, 38, 10),
(104, 38, 11),
(105, 38, 12),
(106, 38, 13),
(107, 38, 14),
(108, 38, 15),
(109, 39, 155),
(110, 39, 156),
(111, 39, 157);

-- --------------------------------------------------------

--
-- Структура таблицы `technology`
--

DROP TABLE IF EXISTS `technology`;
CREATE TABLE IF NOT EXISTS `technology` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(3002) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `technology`
--

INSERT INTO `technology` (`id`, `name`, `description`) VALUES
(1, 'Интернет технология', '<p>Как учиться</p><ul><li>Образовательный портал предоставляет Вам круглосуточный доступ к учебным материалам, включающим в себя полный курс методического обеспечения: курс лекций, практические задания, контрольные работы, тестовые задания, электронную библиотеку.<li><li>Все экзамены сдаются в онлайн режиме.</li><li>Консультации с преподавателями по телефону или email.</li></ul><p>Технические требования</p><ul><li>Обязательное наличие ПК</li><li>Постоянное наличие скоростного Интернета</li></ul><p>На кого ориентирована</p><ul><li>Вы активный пользователь Интернет или хотите им стать.</li></ul>'),
(2, 'Кейсовая технология', '<p>Как учиться</p><ul><li>Региональная служба поддержки обеспечивает Вас учебным материалом, включающего в себя полный курс методического обеспечения: курс лекций, практические задания, контрольные работы, тестовые задания.</li><li>Текущая и итоговая успеваемость выполняется и сдается студентом в распечатанном виде на бумажных носителях, в Региональную службу доставки, от куда она отправляется на проверку в ВУЗ.</li><li>За студентом закрепляется персональный куратор, который ведет и контролирует студента до конца обучения, вплоть до получения им диплома.</li></ul><p>На кого ориентирована</p><ul><li>кейсовую технологию выбирают те - кому удобно работать с бумажными носителями, либо имеющие ограничения по доступу в Интернет.</li></ul>');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `idUsr` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(51) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `mail` varchar(121) NOT NULL,
  `curCheck` varchar(41) DEFAULT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL,
  `resetToken` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idUsr`),
  UNIQUE KEY `login` (`login`,`mail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`idUsr`, `login`, `password`, `mail`, `curCheck`, `role`, `date`, `resetToken`) VALUES
(2, 'topor', '$2y$13$Q0gPqcBw/YWFnbcWoMw54.B9tbPlbBQHETcXXGAYQUz9lr5trQLIq', 'topor434@mail.ru', '6e2b3dd70b6f695630685b064a55f97a4b3e9462', 1, 1428551655, NULL),
(7, 'user1', '$2y$13$jwjD5LbKsxqOCf2XNsi4Cef3AKPrTUc1TsOa/R6UMFl1u5Dxj421.', 'example11111@mail.ru', '4721ab673f7f76d66853fbb31022f67f29d4d269', 0, 1438192860, NULL),
(8, 'user2', '$2y$13$uZJ5.VpdrNwEE.XADovwr.LRou9g6//Kqo/Frpiath81xVUcOOXt6', 'example2222222@mail.ru', 'e4bedb288964955c706081c2fde4a328202b999d', 0, 1438225685, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `variantSp`
--

DROP TABLE IF EXISTS `variantSp`;
CREATE TABLE IF NOT EXISTS `variantSp` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(3002) NOT NULL,
  `idSp` smallint(5) unsigned NOT NULL,
  `countSubjects` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `variantsp_ibfk_1` (`idSp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `variantSp`
--

INSERT INTO `variantSp` (`id`, `name`, `description`, `idSp`, `countSubjects`) VALUES
(1, 'Бухгалтерский учет, анализ и аудит', '<p>Если Вы решили получить профессию:<br />Бухгалтер, Главный бухгалтер, Аудитор, Специалист по МФСО (Международные стандарты бухгалтерской отчетности)</p>', 1, 45),
(2, 'Финансы и кредит', '<p>Если Вы решили получить профессию:<br />Кассир-операционист банка, Начальник Казначейства, Начальник Отдела банка, Управляющий Отделением банка</p>', 1, 44),
(3, 'Государственное и муниципальное управление', '<p>Если Вы решили реализовать себя:</p><ul><li>в структурных подразделениях государственных и муниципальных органов управления;</li><li>в органах местного самоуправления;</li><li>в государственных и муниципальных учреждениях и организациях;</li><li>в институтах гражданского общества;</li><li>в организациях общественного сектора;</li><li>в некоммерческих организациях;</li><li>в научно-исследовательских и образовательных организациях.</li></ul>', 2, 44),
(4, 'Управление малым бизнесом', '<p>Если Вы решили найти себя:<br />на предприятиях малого и среднего бизнеса, в акционерных организациях, в представительствах зарубежных фирм, в финансово-промышленных группах, в консалтинговых фирмах и т.д.</p>', 2, 24),
(5, 'Информационный бизнес', '<p>Прекрасный выбор, если Вы решили освоить профессии:<br/>администратора информационных бизнес-систем, менеджера по продажам технических решений и систем, менеджера в области информационных технологий, специалиста по информационным ресурсам.</p>', 3, 38);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`idSp`) REFERENCES `variantSp` (`id`),
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`idSo`) REFERENCES `studyOption` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `application_ibfk_3` FOREIGN KEY (`idEv`) REFERENCES `educationVariant` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `application_ibfk_4` FOREIGN KEY (`idTech`) REFERENCES `technology` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `application_ibfk_5` FOREIGN KEY (`idUsr`) REFERENCES `users` (`idUsr`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `specSubjects`
--
ALTER TABLE `specSubjects`
  ADD CONSTRAINT `specsubjects_ibfk_1` FOREIGN KEY (`idSp`) REFERENCES `variantSp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `specsubjects_ibfk_2` FOREIGN KEY (`idSub`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `studyCost`
--
ALTER TABLE `studyCost`
  ADD CONSTRAINT `studycost_ibfk_2` FOREIGN KEY (`idEv`) REFERENCES `educationVariant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studycost_ibfk_1` FOREIGN KEY (`idTech`) REFERENCES `technology` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subjectsPassed`
--
ALTER TABLE `subjectsPassed`
  ADD CONSTRAINT `subjectspassed_ibfk_2` FOREIGN KEY (`idSp`) REFERENCES `specSubjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subjectspassed_ibfk_3` FOREIGN KEY (`idApp`) REFERENCES `application` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `variantSp`
--
ALTER TABLE `variantSp`
  ADD CONSTRAINT `variantsp_ibfk_1` FOREIGN KEY (`idSp`) REFERENCES `speciality` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
