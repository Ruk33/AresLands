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
-- Volcando datos para la tabla ironfist_areslands.achievements: ~27 rows (aproximadamente)
/*!40000 ALTER TABLE `achievements` DISABLE KEYS */;
INSERT INTO `achievements` (`id`, `name`, `description`, `icon`, `reward_coins`, `reward_xp`, `reward_talent_points`, `reward_clan_xp`, `required_quest_id`, `required_quests`, `required_level`, `kill_npc`, `required_pves`, `required_pvps`, `required_rank`, `required_vip`, `travel_zone_id`, `required_exploration_time`, `zone_explore_id`, `requires_orb`) VALUES
	(1, 'Asesino de monstruos', 'Acaba con 100 monstruos', '/img/npcs/24.jpg', 50, 20, 1, 1, 0, 0, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0),
	(2, 'Exterminador de monstruos', 'Acaba con 200 monstruos', '/img/npcs/91.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 200, 0, 0, 0, 0, 0, 0, 0),
	(3, 'Verdugo', 'Acaba con 500 monstruos', '/img/npcs/101.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 500, 0, 0, 0, 0, 0, 0, 0),
	(4, 'Adicto a las misiones', 'Completa 50 misiones', '/img/npcs/174.jpg', 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(5, 'Maestro de las misiones', 'Completa 100 misiones', '/img/npcs/169.jpg', 0, 0, 0, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(6, 'Iluminado en misiones', 'Completa 200 misiones', '/img/npcs/170.jpg', 0, 0, 0, 0, 0, 200, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(7, 'Asesino de Demonios', 'Acaba con el Demonio que acecha en El Lago Subterráneo', '/img/npcs/54.jpg', 0, 0, 0, 0, 0, 0, 0, 54, 0, 0, 0, 0, 0, 0, 0, 0),
	(8, 'Elimina Rocas', 'Acaba con el Golem que habita en Los Montes Bárbaros', '/img/npcs/42.jpg', 0, 0, 0, 0, 0, 0, 0, 42, 0, 0, 0, 0, 0, 0, 0, 0),
	(9, 'Aplasta alimañas', 'Acaba con la Tectite Reina que se encuentra en el Valle de la Sangre', '/img/npcs/45.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(10, 'Anti-renegados', 'Aplasta al Elfo Líder Renegado que se merodea en las Pirámides', '/img/npcs/60.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(11, 'Viajero: Montes Bárbaros', 'Viaja a los Montes Bárbaros', '/img/zones/1.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(12, 'Viajero: Lagos Subterráneos', 'Viaja a los Lagos Subterráneos', '/img/zones/3.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(13, 'Viajero: Valle de la Sangre', 'Viaja al Valle de la Sangre', '/img/zones/2.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(14, 'Viajero: Pirámides', 'Viaja a las Pirámides', '/img/zones/4.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(15, 'Iniciante: nivel 5', 'Alcanza el nivel 5', '/img/npcs/21.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(16, 'Junior: nivel 10', 'Alcanza el nivel 10', '/img/npcs/2.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(17, 'Aprendiz: nivel 20', 'Alcanza el nivel 20', '/img/npcs/51.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(18, 'Experimentado: nivel 50', 'Alcanza el nivel 50', '/img/npcs/63.jpg', 0, 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(19, 'Maestro: nivel 80', 'Alcanza el nivel 80', '/img/npcs/71.jpg', 0, 0, 0, 0, 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(20, 'Elite: nivel 150', 'Alcanza el último nivel, 150', '/img/npcs/116.jpg', 0, 0, 0, 0, 0, 0, 150, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(21, 'Iniciante luchador', 'Vence a 50 personajes en batalla', '/img/icons/items/39.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(22, 'Asesino a suelto', 'Vence a 100 personajes en batalla', '/img/icons/items/58.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(23, 'Lacayo de la muerte', 'Vence a 500 personajes en batalla', '/img/icons/items/61.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(24, 'It\'s Bronce Time', 'Consigue el tercer puesto en el ranking de PvP', '/img/icons/crown-bronze-icon.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(25, '¿Alguien dijo plata?', 'Consigue el segundo puesto en el ranking de PvP', '/img/icons/crown-silver-icon.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(26, '¡Aquí llega el oro!', '¡Hazte con el primer puesto en el ranking de PvP!', '/img/icons/crown-gold-icon.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(27, 'Una parte de Ares...', 'Consigue el Orbe de Ares', '/img/icons/orbs/9.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
/*!40000 ALTER TABLE `achievements` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
