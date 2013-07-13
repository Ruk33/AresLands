<?php

class Quest_MagiaSuprema
{
	const QUEST_ID = 16;
	public static $npcId = 158;

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Habla con el Supremo Arcano.';

			$characterQuest->data = $data;
			$characterQuest->save();

			return true;
		}
	}

	public static function onNpcTalk(Character $character, Npc $npc)
	{
		if ( ! $character || ! $npc )
		{
			return false;
		}

		if ( $npc->id == self::$npcId )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			if ( $characterQuest )
			{
				$characterQuest->quest->give_reward();
				$characterQuest->delete();

				return true;
			}
		}
	}
}