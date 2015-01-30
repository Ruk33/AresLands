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

-- Volcando estructura para tabla ironfist_areslands.tournaments
CREATE TABLE IF NOT EXISTS `tournaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `clan_winner_id` int(11) NOT NULL DEFAULT '0',
  `starts_at` int(11) NOT NULL DEFAULT '0',
  `ends_at` int(11) NOT NULL DEFAULT '0',
  `all_clans` tinyint(4) NOT NULL DEFAULT '0',
  `min_members` int(11) NOT NULL DEFAULT '0',
  `mvp_id` int(11) NOT NULL DEFAULT '0',
  `mvp_received_reward` tinyint(4) NOT NULL DEFAULT '0',
  `clan_leader_received_reward` tinyint(4) NOT NULL DEFAULT '0',
  `battle_counter` int(11) NOT NULL DEFAULT '0',
  `life_potion_counter` int(11) NOT NULL DEFAULT '0',
  `potion_counter` int(11) NOT NULL DEFAULT '0',
  `allow_potions` tinyint(4) NOT NULL DEFAULT '0',
  `character_counter` int(11) NOT NULL DEFAULT '0',
  `coin_reward` int(11) NOT NULL DEFAULT '0',
  `mvp_coin_reward` int(11) NOT NULL DEFAULT '0',
  `item_reward` int(11) NOT NULL DEFAULT '0',
  `item_reward_amount` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `cleaned_potions` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
