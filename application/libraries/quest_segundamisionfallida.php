<?php

class Quest_SegundaMisionFallida
{
	const QUEST_ID = 22;
	public static $monstersId = array(122, 123, 124, 125);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 50 monstruos del Bosque Espejo.';

			$characterQuest->data = $data;
			$characterQuest->save();

			return true;
		}
	}

	public static function onPveBattleWin(Character $character, Npc $monster)
	{
		if ( in_array($monster->id, self::$monstersId) )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			if ( $characterQuest )
			{
				$data = $characterQuest->data;

				if ( isset($data['count']) )
				{
					$data['count']++;
				}
				else
				{
					$data['count'] = 1;
				}

				if ( $data['count'] == 50 )
				{
					$characterQuest->progress = 'reward';
					$characterQuest->save();

					return true;
				}

				$characterQuest->data = $data;
				$characterQuest->save();
			}
		}
	}
}