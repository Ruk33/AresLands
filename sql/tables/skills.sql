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

-- Volcando estructura para tabla ironfist_areslands.skills
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` tinyint(4) NOT NULL DEFAULT '1',
  `name` text COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `requirements_text` text COLLATE utf8_bin NOT NULL,
  `direct_magic_damage` smallint(6) NOT NULL DEFAULT '0',
  `direct_physical_damage` smallint(6) NOT NULL DEFAULT '0',
  `physical_damage` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Daño físico que va a la vida',
  `magical_damage` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Daño mágico que va a la vida',
  `stat_strength` smallint(6) NOT NULL DEFAULT '0',
  `stat_dexterity` smallint(6) NOT NULL DEFAULT '0',
  `stat_resistance` smallint(6) NOT NULL DEFAULT '0',
  `stat_magic` smallint(6) NOT NULL DEFAULT '0',
  `stat_magic_skill` smallint(6) NOT NULL DEFAULT '0',
  `stat_magic_resistance` smallint(6) NOT NULL DEFAULT '0',
  `luck` smallint(6) NOT NULL DEFAULT '0',
  `evasion` float NOT NULL DEFAULT '0',
  `magic_defense` float NOT NULL DEFAULT '0',
  `physical_defense` float NOT NULL DEFAULT '0',
  `critical_chance` float NOT NULL DEFAULT '0',
  `attack_speed` float NOT NULL DEFAULT '0',
  `life` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Vida que regenera',
  `max_life` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Aumenta vida máxima',
  `regeneration_per_second` float NOT NULL DEFAULT '0',
  `reflect_damage` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Daño que devuelve al recibir daño físico',
  `reflect_magic_damage` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Daño que devuelve al recibir daño mágico',
  `travel_time` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Modifica el tiempo de viaje',
  `battle_rest` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Modifica el tiempo de descanso luego de batallar',
  `life_required` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Vida requerida para que la habilidad esté activa',
  `xp_rate` smallint(6) NOT NULL DEFAULT '0',
  `quest_xp_rate` smallint(6) NOT NULL DEFAULT '0',
  `drop_rate` smallint(6) NOT NULL DEFAULT '0',
  `explore_reward_rate` smallint(6) NOT NULL DEFAULT '0',
  `coin_rate` smallint(6) NOT NULL DEFAULT '0',
  `quest_coin_rate` smallint(6) NOT NULL DEFAULT '0',
  `skill_cd_time` float NOT NULL DEFAULT '0' COMMENT 'Modifica el CD de los skills',
  `percent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '¿Los valores están en porcentaje?',
  `timeout` tinyint(1) NOT NULL DEFAULT '0' COMMENT '¿Cada cuántos minutos se ejecuta?',
  `duration` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Duración en minutos',
  `chance` tinyint(4) NOT NULL DEFAULT '100' COMMENT 'Porcentaje de que la habilidad haga efecto',
  `triggered_by` enum('onHit','onCriticalHit','onPhysicalHit','onCriticalPhysicalHit','onMagicHit','onCriticalMagicHit','onEnemyHit','onEnemyCriticalHit','onEnemyPhysicalHit','onEnemyCriticalPhysicalHit','onEnemyMagicHit','onEnemyCriticalMagicHit','onCast') COLLATE utf8_bin NOT NULL DEFAULT 'onCast' COMMENT 'El evento/acción necesitada para disparar los beneficios de la habilidad',
  `required_object_type_equiped` text COLLATE utf8_bin NOT NULL COMMENT 'El tipo de objeto (shield, blunt, etc.) que se debe tener equipado para que la habilidad esté activa (se pueden usar varios valores separados por coma)',
  `type` enum('buff','debuff','heal','dummy','passive','reflect','physicalDamage','magicalDamage','physicalDamageOverTime','magicalDamageOverTime','bleed','poison','healOverTime') COLLATE utf8_bin NOT NULL COMMENT 'El tipo de habilidad',
  `target` enum('none','one','self','clan','notself','allClan') COLLATE utf8_bin NOT NULL DEFAULT 'self' COMMENT 'Objetivo de la habilidad',
  `dwarf` enum('none','male','female','both') COLLATE utf8_bin NOT NULL DEFAULT 'both' COMMENT '¿Pueden los enanos ser afectados por la habilidad?',
  `elf` enum('none','male','female','both') COLLATE utf8_bin NOT NULL DEFAULT 'both' COMMENT '¿Pueden los elfos ser afectados por la habilidad?',
  `human` enum('none','male','female','both') COLLATE utf8_bin NOT NULL DEFAULT 'both' COMMENT '¿Pueden los humanos ser afectados por la habilidad?',
  `drow` enum('none','male','female','both') COLLATE utf8_bin NOT NULL DEFAULT 'both' COMMENT '¿Pueden los drows ser afectados por la habilidad?',
  `stackable` tinyint(4) NOT NULL,
  `min_level_required` tinyint(4) NOT NULL DEFAULT '0',
  `clan_level` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Columna solo para habilidades de clan. Nivel de clan que requiere',
  `required_skills` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0-0' COMMENT 'Columna solo para habilidades de clan. Aca guardamos los skills (y los niveles) que requiere para poder ser aprendida',
  `cd` int(11) NOT NULL DEFAULT '0',
  `can_be_random` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Si es true, entonces es candidato para cuando se necesite un skill aleatorio',
  PRIMARY KEY (`id`,`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
