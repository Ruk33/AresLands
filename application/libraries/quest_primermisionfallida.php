<?php

class Quest_PrimerMisionFallida
{
	const QUEST_ID = 21;
	public static $monstersId = array(127, 128, 129, 130, 131, 132, 133);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 50 monstruos de las Ruinas Enanas.';

			$characterQuest->data = $data;
			$characterQuest->save();

			return true;
		}
	}

	public static function onPveBattleWin(Character $character, Npc $monster)
	{
		if ( ! $character || ! $monster )
		{
			return false;
		}

		if ( in_array($monster->id, self::$monstersId) )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			if ( $characterQuest )
			{
				$data = $characterQuest->data;

				if ( isset($data['count']) )
				{
					$data['count']++;

					if ( $data['count'] == 50 )
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

				$data['progress_for_view'] = sprintf('Mata %d/50 monstruos de las Ruinas Enanas.', 50-$data['count']);

				$characterQuest->data = $data;
				$characterQuest->save();
			}
		}
	}
}