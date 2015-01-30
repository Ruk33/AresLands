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

-- Volcando estructura para tabla ironfist_areslands.items
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `level` int(11) NOT NULL,
  `quality` tinyint(4) NOT NULL DEFAULT '1',
  `attack_speed` enum('very-slow','slow','normal','fast','very-fast') COLLATE utf8_bin NOT NULL,
  `body_part` enum('chest','legs','feet','head','hands','lhand','rhand','lrhand','mercenary','none') COLLATE utf8_bin NOT NULL,
  `type` enum('blunt','bigblunt','sword','bigsword','bow','dagger','staff','bigstaff','hammer','bighammer','ring','axe','shield','potion','arrow','etc','mercenary','none') COLLATE utf8_bin NOT NULL,
  `class` enum('armor','weapon','mercenary','consumible','none') COLLATE utf8_bin NOT NULL,
  `price` int(11) NOT NULL,
  `stat_strength` mediumint(9) NOT NULL,
  `stat_dexterity` mediumint(9) NOT NULL,
  `stat_resistance` mediumint(9) NOT NULL,
  `stat_magic` mediumint(9) NOT NULL,
  `stat_magic_skill` mediumint(9) NOT NULL,
  `stat_magic_resistance` mediumint(9) NOT NULL,
  `selleable` tinyint(1) NOT NULL,
  `destroyable` tinyint(1) NOT NULL,
  `tradeable` tinyint(1) NOT NULL,
  `stackable` tinyint(1) NOT NULL,
  `skill` varchar(70) COLLATE utf8_bin NOT NULL DEFAULT '0-0',
  `time_to_appear` int(11) DEFAULT NULL COMMENT 'Tiempo para aparecer/destrabar en minutos',
  `zone_to_explore` int(11) DEFAULT NULL,
  `image` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
