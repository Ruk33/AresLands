-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         5.6.17 - MySQL Community Server (GPL)
-- SO del servidor:              Win64
-- HeidiSQL Versión:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- Volcando datos para la tabla ironfist_areslands.tournament_registered_clans: ~7 rows (aproximadamente)
/*!40000 ALTER TABLE `tournament_registered_clans` DISABLE KEYS */;
INSERT INTO `tournament_registered_clans` (`id`, `tournament_id`, `clan_id`, `disqualified`) VALUES
	(1, 1, 15, 0),
	(2, 1, 2, 1),
	(3, 1, 9, 0),
	(4, 2, 15, 0),
	(6, 2, 9, 0),
	(7, 2, 2, 0),
	(8, 3, 15, 0);
/*!40000 ALTER TABLE `tournament_registered_clans` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
