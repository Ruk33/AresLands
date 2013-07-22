<?php

class Quest_AyudaATuPueblo
{
	const QUEST_ID = 2;
	public static $monstersId = array(9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 5 monstruos de cualquier ciudad';

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

				if ( is_array($data) && isset($data['count']) )
				{
					$data['count']++;

					if ( $data['count'] == 5 )
					{
						$characterQuest->progress = 'reward';
						$characterQuest->save();
						return true;
					}
				}
				else
				{
					$data['count'] = 1;
				}

				$data['progress_for_view'] = sprintf('Mata %d/5 monstruos de cualquier ciudad.', 5-$data['count']);

				$characterQuest->data = $data;
				$characterQuest->save();
			}
		}
	}
}