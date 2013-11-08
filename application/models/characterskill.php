<?php

class CharacterSkill extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_skills';
	public static $key = 'id';
	
	public function get_data()
	{
		$data = $this->get_attribute('data');
		
		if ( ! $data )
		{
			return array();
		}
		else
		{
			if ( is_array($data) )
			{
				return $data;
			}
			else
			{
				return unserialize($data);
			}
		}
	}
	
	public function save()
	{
		$this->data = serialize($this->data);
		return parent::save();
	}
	
	public function skill()
	{
		return $this->belongs_to('Skill', 'skill_id');
	}
	
	/**
	 * Registramos (o actualizamos) un skill en la db
	 * @param Character $character
	 * @param Skill $skill
	 * @param integer $amount
	 */
	public static function register(Character $character, Skill $skill, $amount = 1)
	{
		$characterSkill = new CharacterSkill();
		
		$characterSkill->character_id = $character->id;
		$characterSkill->skill_id = $skill->id;
		$characterSkill->level = $skill->level;
		$characterSkill->amount = $amount;
		
		if ( $skill->duration > 0 )
		{
			$characterSkill->end_time = time() + $skill->duration * 60;
		}
		else
		{
			$characterSkill->end_time = 0;
		}
		
		$characterSkill->data = array(
			'extra_stat' => array(
				'stat_strength' => 0,
				'stat_dexterity' => 0,
				'stat_resistance' => 0,
				'stat_magic' => 0,
				'stat_magic_skill' => 0,
				'stat_magic_resistance' => 0
			)
		);
		
		$characterSkill->save();
        
        Skill::periodic($characterSkill);
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