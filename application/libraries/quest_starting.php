<?php

class Quest_Starting
{
	const QUEST_ID = 7;

	public static function onEquipItem(Item $item)
	{
		$character = Session::get('character');

		$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$characterQuest->progress = 'reward';

		Quest::find(self::QUEST_ID)->give_reward();

		$characterQuest->save();

		return true;
	}
}