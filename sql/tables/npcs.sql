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

-- Volcando estructura para tabla ironfist_areslands.npcs
CREATE TABLE IF NOT EXISTS `npcs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_bin NOT NULL,
  `dialog` text COLLATE utf8_bin NOT NULL,
  `tooltip_dialog` varchar(50) COLLATE utf8_bin NOT NULL,
  `zone_id` tinyint(4) NOT NULL,
  `level_to_appear` int(11) NOT NULL COMMENT 'Tiempo para aparecer (en mínutos)',
  `type` enum('npc','boss','monster') COLLATE utf8_bin NOT NULL,
  `level` tinyint(4) NOT NULL,
  `life` float NOT NULL,
  `stat_strength` mediumint(9) NOT NULL,
  `stat_dexterity` mediumint(9) NOT NULL,
  `stat_resistance` mediumint(9) NOT NULL,
  `stat_magic` mediumint(9) NOT NULL,
  `stat_magic_skill` mediumint(9) NOT NULL,
  `stat_magic_resistance` mediumint(9) NOT NULL,
  `lhand` int(11) NOT NULL,
  `rhand` int(11) NOT NULL,
  `xp` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
