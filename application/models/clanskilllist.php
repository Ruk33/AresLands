<?php

/*
 *	Estructura
 *
 *	skill_id: int
 *		|
 *		|______ level: int
 *				  |
 *				  |______ clan_level: int
 *				  |______ skill_requirements: mixed array
 *								   |
 *						           |____________ skill_id: int
 *								   |____________ level: int
 *
 */

class ClanSkillList
{
	private static $_instance = null;

	private $_json;

	private function __construct()
	{
		$this->_json = json_decode(file_get_contents(__DIR__ . '\ClanSkillList.json'), true);
	}

	public function get_skills()
	{
		return $this->_json;
	}

	public function get_skill($skillId, $level)
	{
		return isset($this->_json[$skillId][$level]) ? $this->_json[$skillId][$level] : null;
	}

	public function can_learn(Clan $clan, $skillId, $level)
	{
		$skill = isset($this->_json[$skillId][$level]) ? $this->_json[$skillId][$level] : false;

		if ( $skill )
		{
			if ( $clan->level < $skill['clan_level'] )
			{
				return false;
			}

			if ( isset($skill['skill_requirements']) )
			{
				foreach ( $skill['skill_requirements'] as $skillRequirement )
				{
					if ( $clan->skills()->where('skill_id', '=', $skillRequirement['id'])->where('level', '>=', $skillRequirement['level'])->take(1)->count() == 0 )
					{
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}

	public static function get_instance()
	{
		if ( ! self::$_instance )
		{
			self::$_instance = new ClanSkillList();
		}

		return self::$_instance;
	}
}