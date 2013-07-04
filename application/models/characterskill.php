<?php

class CharacterSkill extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_skills';
	public static $key = 'id';

	public function skill()
	{
		return $this->belongs_to('Skill', 'skill_id');
	}

	public function character()
	{
		return $this->belongs_to('Character', 'character_id');
	}

	public function update_time()
	{
		if ( $this->end_time != 0 )
		{
			if ( $this->end_time <= time() )
			{
				$this->delete();
			}
		}
	}
}