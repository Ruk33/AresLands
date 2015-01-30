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
-- Volcando datos para la tabla ironfist_areslands.orbs: ~20 rows (aproximadamente)
/*!40000 ALTER TABLE `orbs` DISABLE KEYS */;
INSERT INTO `orbs` (`id`, `name`, `description`, `coins`, `points`, `min_level`, `max_level`, `owner_character`, `acquisition_time`, `last_attacker`, `last_attack_time`, `image`) VALUES
	(1, 'Orbe de dragones', 'Reliquia de antiguos y legendarios dragones. Se dice que su poseedor recibira monedas por cada minuto que lleve con sigo a esta magica pieza.', 1700, 15, 35, 150, 11, 1407283510, NULL, NULL, 'icons/orbs/1.png'),
	(2, 'Orbe de la luz', 'Destellante y magica reliquia resguardada por los elfos de mayor categoria. Quien posea esta reliquia nunca estara perdido en el manto de la oscuridad.', 750, 4, 5, 20, 78, 1404738467, NULL, NULL, 'icons/orbs/2.png'),
	(3, 'Orbe de la oscuridad', 'Esta oscura roca parece llevar en su interior un gran mal, lo mas problematico es notar que tiene algunos rasguños, ¿acaso algo estará queriendo salir?.', 650, 6, 8, 25, 22, 1404392327, NULL, NULL, 'icons/orbs/3.png'),
	(4, 'Orbe de hada cristal', 'Fría y cálida al mismo tiempo. El tiempo parece ir mas despacio mientras posas tu mirada sobre este cristal.', 150, 8, 9, 25, 23, 1402018557, 40, 1404640523, 'icons/orbs/4.png'),
	(5, 'Orbe de la naturalez', 'Antiguos y puros espíritus de la naturaleza parecen habitar aquí. La tranquilidad y serenidad parece emanar de esta hermosa piedra.', 512, 9, 10, 20, 47, 1402602765, NULL, NULL, 'icons/orbs/5.png'),
	(6, 'Orbe de fuego', 'Fuego... un poderoso purificador natural. El núcleo de esta esfera está constituído enteramente por ese elemento.', 1200, 5, 10, 20, 37, 1402413141, NULL, NULL, 'icons/orbs/6.png'),
	(7, 'Orbe de la vida', 'Una parte de la vitalidad de una mágica criatura reside en este contenedor...', 240, 2, 1, 5, 67, 1408627087, NULL, NULL, 'icons/orbs/7.png'),
	(8, 'Orbe de la fortuna', 'El portador de este destellante orbe recibira grandes cantidades de monedas', 2700, 1, 15, 40, 27, 1410829166, NULL, NULL, 'icons/orbs/8.png'),
	(9, 'Orbe de Ares', 'Solo maldad pura podras encontrar en este maldito objeto', 2200, 8, 15, 150, 55, 1411147584, NULL, NULL, 'icons/orbs/9.png'),
	(10, 'Orbe acorazado', 'Es un extraño orbe, con un claro aspecto oscuro...', 820, 4, 20, 65, 16, 1411109434, NULL, NULL, 'icons/orbs/10.png'),
	(14, 'Orbe de Itabon', '', 10, 2, 10, 20, 40, 1404939159, NULL, NULL, 'icons/orbs/14.png'),
	(15, 'Orbe de Nabiuma', '', 110, 1, 5, 15, 82, 1406451462, 87, 1410369312, 'icons/orbs/15.png'),
	(17, 'Orbe de Baisa', '', 30, 1, 5, 15, 80, 1404235796, NULL, NULL, 'icons/orbs/17.png'),
	(19, 'Orbe de Elin', '', 90, 1, 5, 15, 81, 1404341495, 53, 1411130987, 'icons/orbs/19.png'),
	(25, 'Orbe de Athmell', '', 160, 2, 10, 20, NULL, NULL, NULL, NULL, 'icons/orbs/25.png'),
	(36, 'Orbe de Dretio', '', 400, 2, 15, 50, 20, 1410423775, 16, 1411055706, 'icons/orbs/36.png'),
	(44, 'Orbe de Athgel', '', 500, 1, 30, 70, 14, 1408481587, NULL, NULL, 'icons/orbs/44.png'),
	(46, 'Orbe de Collie', '', 670, 2, 30, 65, 30, 1404416868, NULL, NULL, 'icons/orbs/46.png'),
	(49, 'Orbe de Dutua', '', 950, 3, 40, 150, 10, 1412057311, NULL, NULL, 'icons/orbs/49.png'),
	(53, 'Orbe de Cracha', '', 1000, 3, 40, 150, 8, 1411535713, 10, 1411631342, 'icons/orbs/53.png');
/*!40000 ALTER TABLE `orbs` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
