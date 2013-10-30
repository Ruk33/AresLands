<?php

class Skill_Task
{
	public function run($arguments)
	{
		$characterSkills = CharacterSkill::join('skills as skill', function($join) {
			$join->on('skill.id', '=', 'character_skills.skill_id');
			$join->on('skill.level', '=', 'character_skills.level');
		})
			->where('end_time', '<>', 0)
			->where('skill.timeout', '>', 0)
			->or_where('end_time', '<', time())
		->get(array('*', 'skill.timeout'));
		
		$time = time();
		
		foreach ( $characterSkills as $characterSkill )
		{
			if ( $characterSkill->end_time != 0 )
			{
				if ( $characterSkill->end_time <= $time || ($characterSkill->timeout != 0 && $characterSkill->timeout >= $time - $characterSkill->last_execution_time) )
				{
					$characterSkill->skill->periodic($characterSkill->character);
				}
			}
		}
	}
}