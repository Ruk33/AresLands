<?php

class ClanSkill extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'clan_skills';
	public static $key = 'id';

	public function skill()
	{
		//return $this->belongs_to('Skill', 'skill_id');
		return Skill::get($this->skill_id, $this->level);
	}
}