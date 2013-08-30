<?php

class Skill
{
	/**
	 *	Guardamos todas las habilidades en este
	 *	array.
	 *
	 *	@var array
	 */
	private static $SKILL_LIST = null;

	/**
	 *	Obtenemos una habilidad por medio de su id y su nivel.
	 *
	 *	@param integer $skillId Id de la habilidad
	 *	@param integer $level Nivel de la habilidad (default: 1)
	 *	@return mixed La habilidad o null en caso de no encontrarla
	 */
	static function get($skillId, $level = 1)
	{
		if ( ! self::$SKILL_LIST )
		{
			self::$SKILL_LIST = json_decode(file_get_contents(__DIR__ . '/Skill.json'), true);
		}

		if ( isset(self::$SKILL_LIST[$skillId][$level]) )
		{
			$skill = array();

			$skill = self::$SKILL_LIST[$skillId][$level];
			$skill['name'] = self::$SKILL_LIST[$skillId]['name'];
			$skill['skill_id'] = $skillId;
			$skill['level'] = $level;

			return $skill;
		}

		return null;
	}

	/**
	 *	
	 *
	 *	@deprecated
	 *	@param array $keys
	 */
	static function getFromRelation($keys)
	{
		$values = array();

		foreach ( $keys as $key )
		{
			if ( isset($key->skill_id) )
			{
				$values[] = self::get($key->skill_id, ( isset($key->level) ) ? $key->level : 1);
			}
		}

		return $values;
	}
}