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

-- Volcando estructura para tabla ironfist_areslands.quests
CREATE TABLE IF NOT EXISTS `quests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` varchar(9000) COLLATE utf8_bin NOT NULL,
  `min_level` tinyint(4) NOT NULL,
  `max_level` tinyint(4) NOT NULL,
  `repeatable` tinyint(1) NOT NULL,
  `repeatable_after` int(11) NOT NULL,
  `daily` tinyint(1) NOT NULL,
  `dwarf` enum('none','male','female','both') COLLATE utf8_bin NOT NULL,
  `drow` enum('none','male','female','both') COLLATE utf8_bin NOT NULL,
  `elf` enum('none','male','female','both') COLLATE utf8_bin NOT NULL,
  `human` enum('none','male','female','both') COLLATE utf8_bin NOT NULL,
  `complete_required` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
