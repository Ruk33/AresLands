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

-- Volcando estructura para tabla ironfist_areslands.helpers
CREATE TABLE IF NOT EXISTS `helpers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8_bin NOT NULL,
  `p_damage` float NOT NULL,
  `m_damage` float NOT NULL,
  `price` int(11) NOT NULL,
  `price_per_period` int(11) NOT NULL,
  `stat_life` mediumint(9) NOT NULL,
  `stat_dexterity` mediumint(9) NOT NULL,
  `stat_magic` mediumint(9) NOT NULL,
  `stat_strength` mediumint(9) NOT NULL,
  `stat_luck` mediumint(9) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `skill_level` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
