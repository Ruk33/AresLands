<?php

class CharacterSkill extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_skills';

	public function skill()
	{
		return $this->belongs_to('Skill', 'skill_id');
	}
}