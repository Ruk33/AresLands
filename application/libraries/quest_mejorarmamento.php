<?php

class Quest_MejorArmamento
{
	const QUEST_ID = 14;
	public static $npcId = 157;

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Habla con Ruf el Solitario.';

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
				$characterQuest->progress = 'reward';
				$characterQuest->save();

				return true;
			}
		}
	}
}