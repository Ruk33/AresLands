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
-- Volcando datos para la tabla ironfist_areslands.zones: ~11 rows (aproximadamente)
/*!40000 ALTER TABLE `zones` DISABLE KEYS */;
INSERT INTO `zones` (`id`, `name`, `description`, `type`, `belongs_to`, `min_level`) VALUES
	(1, 'Montes Bárbaros', 'Hogar de los enanos, montes forjadores de guerreros natos.', 'city', 0, 0),
	(2, 'Valle de la Sangre', 'La magia flota en el aire, pues los Drow viven alli.', 'city', 0, 0),
	(3, 'Lago Subterraneo', 'La última gran ciudad humana, recibe su agua del lago subterraneo que fluye bajo ella.', 'city', 0, 0),
	(4, 'Piramides', 'Antigua ciudad elfica, con edificaciones piramidales. Actual hogar de los Elfos.', 'city', 0, 0),
	(5, 'La niebla eterna', 'Llamada así por la perenne niebla que inunda la zona.', 'city', 0, 20),
	(6, 'El corazón del invierno', 'El hielo y la nieve no serán obstáculo para tus exploraciones.', 'city', 0, 20),
	(7, 'Las laderas de Kaiala', 'El volcán Kaiala se encuentra siempre en continua erupción, expulsando energía desde el interior de la tierra.', 'city', 0, 35),
	(8, 'El santuario arcano', 'Todo es oscuridad cerca del santuario arcano. Aquí los hijos de la oscuridad se encuentran en su hogar.', 'city', 0, 20),
	(9, 'El desierto de la luz', 'La luz invade el desierto y se refleja en sus arenas, dando sensación de bienestar a los hijos de la luz.', 'city', 0, 20),
	(10, 'Las aguas mansas', 'El mar de AresLands, poco profundo, absorbe las emanaciones mágicas, haciendo inútiles pociones y deseos.', 'city', 0, 20),
	(33, 'AresLands', 'Antigua Tierra de leyendas actualmente Dominada por Ares', 'land', 0, 0);
/*!40000 ALTER TABLE `zones` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
