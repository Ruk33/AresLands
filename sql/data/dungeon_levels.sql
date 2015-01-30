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
-- Volcando datos para la tabla ironfist_areslands.dungeon_levels: ~5 rows (aproximadamente)
/*!40000 ALTER TABLE `dungeon_levels` DISABLE KEYS */;
INSERT INTO `dungeon_levels` (`id`, `dungeon_id`, `dungeon_level`, `monster_id`, `big_image_path`, `required_item_id`, `required_level`, `type`) VALUES
	(1, 1, 1, 195, 'dungeon/1.png', 0, 10, 2),
	(2, 1, 2, 196, 'dungeon/2.png', 0, 15, 0),
	(3, 1, 3, 197, 'dungeon/3.png', 0, 30, 0),
	(4, 1, 4, 198, 'dungeon/4.png', 0, 45, 2),
	(5, 1, 5, 199, 'dungeon/5.png', 0, 50, 2);
/*!40000 ALTER TABLE `dungeon_levels` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
