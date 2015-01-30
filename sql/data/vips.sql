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
-- Volcando datos para la tabla ironfist_areslands.vips: ~7 rows (aproximadamente)
/*!40000 ALTER TABLE `vips` DISABLE KEYS */;
INSERT INTO `vips` (`id`, `name`, `description`, `icon`, `price`, `enabled`) VALUES
	(1, 'Cambio de nombre', 'Cambias el nombre a tu personaje', '/img/icons/vip/change_name.jpg', 125, 1),
	(2, 'Cambio de genero', 'Cambias el genero de tu personaje', '/img/icons/vip/change_gender.jpg', 80, 1),
	(3, 'Cambio de raza', 'Cambias la raza de tu personaje', '/img/icons/vip/change_race.jpg', 175, 1),
	(4, 'Multiplicador de monedas', 'Aprovecha mejor los combates, exploraciones y misiones consiguiendo un 30% de oro extra durante 3 dias', '/img/icons/vip/coin_multiplier.jpg', 40, 1),
	(5, 'Reductor de tiempos', 'Reduce tus tiempos de viaje y descanzos en un 20% durante 3 dias', '/img/icons/vip/reduction_time.jpg', 20, 1),
	(6, 'Multiplicador de experiencia', 'Aprovecha mejor los combates, exploraciones y misiones consiguiendo un 20% de experiencia extra durante 3 dias', '/img/icons/vip/xp_multiplier.jpg', 20, 1),
	(7, 'Halloween - Guadaña', 'Obtienes una misteriosa guadaña que al equiparla te transforma en un subdito de la muerte... ESPECIAL HALLOWEEN', '/img/icons/vip/halloween_scythe.jpg', 50, 1);
/*!40000 ALTER TABLE `vips` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
