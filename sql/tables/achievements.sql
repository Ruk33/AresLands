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

-- Volcando estructura para tabla ironfist_areslands.achievements
CREATE TABLE IF NOT EXISTS `achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `icon` text COLLATE utf8_bin NOT NULL,
  `reward_coins` int(11) NOT NULL DEFAULT '0',
  `reward_xp` int(11) NOT NULL DEFAULT '0',
  `reward_talent_points` int(11) NOT NULL DEFAULT '0',
  `reward_clan_xp` int(11) NOT NULL DEFAULT '0',
  `required_quest_id` int(11) NOT NULL DEFAULT '0',
  `required_quests` int(11) NOT NULL DEFAULT '0',
  `required_level` int(11) NOT NULL DEFAULT '0',
  `kill_npc` int(11) NOT NULL DEFAULT '0',
  `required_pves` int(11) NOT NULL DEFAULT '0',
  `required_pvps` int(11) NOT NULL DEFAULT '0',
  `required_rank` int(11) NOT NULL DEFAULT '0',
  `required_vip` int(11) NOT NULL DEFAULT '0',
  `travel_zone_id` int(11) NOT NULL DEFAULT '0',
  `required_exploration_time` int(11) NOT NULL DEFAULT '0',
  `zone_explore_id` int(11) NOT NULL DEFAULT '0',
  `requires_orb` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
