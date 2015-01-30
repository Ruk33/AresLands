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

-- Volcando estructura para tabla ironfist_areslands.characters
CREATE TABLE IF NOT EXISTS `characters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `battle_all_servers` tinyint(4) NOT NULL DEFAULT '0',
  `server_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(10) COLLATE utf8_bin NOT NULL,
  `level` int(11) NOT NULL DEFAULT '1',
  `max_life` decimal(11,2) NOT NULL,
  `current_life` decimal(11,2) NOT NULL,
  `gender` enum('male','female') COLLATE utf8_bin NOT NULL,
  `pvp_points` smallint(6) NOT NULL DEFAULT '0',
  `race` enum('dwarf','human','elf','drow','none') COLLATE utf8_bin NOT NULL,
  `clan_id` int(11) NOT NULL,
  `clan_permission` int(11) NOT NULL,
  `zone_id` tinyint(4) NOT NULL,
  `stat_dexterity` mediumint(9) NOT NULL,
  `stat_magic` mediumint(9) NOT NULL,
  `stat_strength` mediumint(9) NOT NULL,
  `stat_resistance` int(11) NOT NULL,
  `stat_magic_skill` int(11) NOT NULL,
  `stat_magic_resistance` int(11) NOT NULL,
  `stat_strength_extra` int(11) NOT NULL,
  `stat_dexterity_extra` int(11) NOT NULL,
  `stat_resistance_extra` int(11) NOT NULL,
  `stat_magic_extra` int(11) NOT NULL,
  `stat_magic_skill_extra` int(11) NOT NULL,
  `stat_magic_resistance_extra` int(11) NOT NULL,
  `language` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT 'es',
  `xp` decimal(11,2) NOT NULL,
  `xp_next_level` int(11) NOT NULL,
  `is_traveling` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_regeneration_time` int(11) DEFAULT NULL,
  `pvps` int(11) DEFAULT NULL,
  `pves` int(11) DEFAULT NULL,
  `completed_quests` int(11) DEFAULT NULL,
  `points_to_change` int(11) NOT NULL,
  `is_exploring` tinyint(1) NOT NULL,
  `last_activity_time` int(11) NOT NULL,
  `last_logged` int(11) NOT NULL,
  `registered_in_tournament` tinyint(4) NOT NULL,
  `characteristics` text COLLATE utf8_bin NOT NULL,
  `regeneration_per_second` float NOT NULL,
  `regeneration_per_second_extra` float NOT NULL,
  `evasion` float NOT NULL,
  `evasion_extra` float NOT NULL,
  `critical_chance` float NOT NULL,
  `critical_chance_extra` float NOT NULL,
  `attack_speed` float NOT NULL,
  `attack_speed_extra` float NOT NULL,
  `magic_defense` float NOT NULL,
  `magic_defense_extra` float NOT NULL,
  `physical_defense` float NOT NULL,
  `physical_defense_extra` float NOT NULL,
  `magic_damage` float NOT NULL,
  `magic_damage_extra` float NOT NULL,
  `physical_damage` float NOT NULL,
  `physical_damage_extra` float NOT NULL,
  `reflect_magic_damage` float NOT NULL,
  `reflect_magic_damage_extra` float NOT NULL,
  `reflect_physical_damage` float NOT NULL,
  `reflect_physical_damage_extra` float NOT NULL,
  `travel_time` float NOT NULL,
  `travel_time_extra` float NOT NULL,
  `battle_rest_time` float NOT NULL,
  `battle_rest_time_extra` float NOT NULL,
  `skill_cd_time` float NOT NULL,
  `skill_cd_time_extra` float NOT NULL,
  `luck` float NOT NULL,
  `luck_extra` float NOT NULL,
  `xp_rate` tinyint(4) NOT NULL,
  `xp_rate_extra` tinyint(4) NOT NULL,
  `quest_xp_rate` tinyint(4) NOT NULL,
  `quest_xp_rate_extra` tinyint(4) NOT NULL,
  `drop_rate` tinyint(4) NOT NULL,
  `drop_rate_extra` tinyint(4) NOT NULL,
  `explore_reward_rate` tinyint(4) NOT NULL,
  `explore_reward_rate_extra` tinyint(4) NOT NULL,
  `coin_rate` tinyint(4) NOT NULL,
  `coin_rate_extra` tinyint(4) NOT NULL,
  `quest_coin_rate` tinyint(4) NOT NULL,
  `quest_coin_rate_extra` tinyint(4) NOT NULL,
  `talent_points` tinyint(4) NOT NULL,
  `invisible_until` int(11) NOT NULL,
  `second_mercenary` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
