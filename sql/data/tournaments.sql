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
-- Volcando datos para la tabla ironfist_areslands.tournaments: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `tournaments` DISABLE KEYS */;
INSERT INTO `tournaments` (`id`, `name`, `clan_winner_id`, `starts_at`, `ends_at`, `all_clans`, `min_members`, `mvp_id`, `mvp_received_reward`, `clan_leader_received_reward`, `battle_counter`, `life_potion_counter`, `potion_counter`, `allow_potions`, `character_counter`, `coin_reward`, `mvp_coin_reward`, `item_reward`, `item_reward_amount`, `active`, `cleaned_potions`) VALUES
	(1, 'Inicial', 15, 1397319927, 1397406327, 0, 2, 57, 1, 1, 124, 59, 15, 1, 0, 1000000, 500000, 136, 3, 0, 0),
	(2, 'El legado', 15, 1402102656, 1402361856, 0, 3, 57, 0, 1, 1022, 68, 53, 1, 0, 2000000, 5000000, 136, 5, 0, 0),
	(3, 'Renacimiento', 15, 1408071600, 1408071600, 0, 1, 80, 0, 0, 0, 6, 2, 1, 0, 0, 5000000, 136, 5, 0, 0);
/*!40000 ALTER TABLE `tournaments` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
