<?php namespace Libraries\Buffs;

use Libraries\Trigger;

class ExplorerMaster extends Trigger
{
	const SKILL_ID = 1;
	
	public function onExplore(\Character $character, \CharacterActivity $activity)
	{
		if ( $character->has_skill(self::SKILL_ID) )
		{
			$activity->end_time /= 2;
			$activity->save();
		}
	}
}