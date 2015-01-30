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

-- Volcando estructura para tabla ironfist_areslands.character_achievements
CREATE TABLE IF NOT EXISTS `character_achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_id` int(11) NOT NULL DEFAULT '0',
  `achievement_id` int(11) NOT NULL DEFAULT '0',
  `quest_completed` tinyint(1) NOT NULL DEFAULT '0',
  `level_up_completed` tinyint(1) NOT NULL DEFAULT '0',
  `npc_killed` tinyint(1) NOT NULL DEFAULT '0',
  `rank_completed` tinyint(1) NOT NULL DEFAULT '0',
  `vip_completed` tinyint(1) NOT NULL DEFAULT '0',
  `travel_completed` tinyint(1) NOT NULL DEFAULT '0',
  `explore_completed` tinyint(1) NOT NULL DEFAULT '0',
  `orb_completed` tinyint(1) NOT NULL DEFAULT '0',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_pvp` tinyint(1) NOT NULL DEFAULT '0',
  `completed_pve` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
