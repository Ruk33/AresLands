<?php

class CharacterTalent extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_talents';
	public static $key = 'id';
	
	public function skill()
	{
		return $this->belongs_to('Skill', 'skill_id')->where('level', '=', 1);
	}
}