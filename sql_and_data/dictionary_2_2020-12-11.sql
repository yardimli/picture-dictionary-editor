# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.20)
# Database: dictionary_2
# Generation Time: 2020-12-11 11:50:15 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table dictionary
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dictionary`;

CREATE TABLE `dictionary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `word_EN` varchar(255) DEFAULT NULL,
  `word_TR` varchar(255) DEFAULT NULL,
  `word_CH` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `audio_EN` varchar(255) DEFAULT NULL,
  `audio_CH` varchar(255) DEFAULT NULL,
  `audio_TR` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `QUESTION_EN` varchar(255) DEFAULT NULL,
  `QUESTION_TR` varchar(255) DEFAULT NULL,
  `QUESTION_CH` varchar(255) DEFAULT NULL,
  `audio_Q_EN` varchar(255) DEFAULT NULL,
  `audio_Q_TR` varchar(255) DEFAULT NULL,
  `audio_Q_CH` varchar(255) DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dictionary` WRITE;
/*!40000 ALTER TABLE `dictionary` DISABLE KEYS */;

INSERT INTO `dictionary` (`id`, `word_EN`, `word_TR`, `word_CH`, `picture`, `audio_EN`, `audio_CH`, `audio_TR`, `category`, `QUESTION_EN`, `QUESTION_TR`, `QUESTION_CH`, `audio_Q_EN`, `audio_Q_TR`, `audio_Q_CH`, `deleted`, `dateadded`)
VALUES
	(1,'cat','kedi',NULL,'kedi.png',NULL,NULL,NULL,'animal',NULL,NULL,NULL,NULL,NULL,NULL,0,'2020-12-07 11:30:19'),
	(2,'dog','kopek',NULL,'kopek.png',NULL,NULL,NULL,'animal',NULL,NULL,NULL,NULL,NULL,NULL,0,'2020-12-07 11:41:07');

/*!40000 ALTER TABLE `dictionary` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `useremail` varchar(50) DEFAULT NULL,
  `userpassword` varchar(255) DEFAULT NULL,
  `deleted` smallint(1) NOT NULL DEFAULT '0',
  `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tag` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `username`, `useremail`, `userpassword`, `deleted`, `dateadded`, `tag`)
VALUES
	(1,'admin','admin@localhost.com','$2y$10$PAz/sEyrdRupGQj6FE/abujen347VbZHqzknLYcI8qRNGQKJdg/Z6',0,'2017-06-18 20:10:16',1),
	(2,'joserizal','jose@localhost.com','$2y$10$579PFta9hqqxMsoBvLhrduRm/sT3H/pP9b2itilHumWEkwRJo987G',0,'2017-06-18 21:43:15',0),
	(6,'andres2','andres@gmail.com','$2y$10$RQDjfjYIKB0S0yoatHqNSeVNdizoZklA5R5.X.0RDy8Fw.M8zq6XS',0,'2017-06-18 22:22:15',0),
	(7,'merriam','merriam@gmail.com','$2y$10$/fAcy5XcfN4Sahm1VO3DD..aIR9kulrQ.0X9u3TT1ONCdKXIfEbQq',0,'2017-06-18 22:54:08',0);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
