<?php

class Quest_NuevosProblemas
{
	const QUEST_ID = 8;
	public static $monstersId = array(103, 104, 105, 106, 107, 108, 109, 110, 111);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 10 monstruos del Cementerio Naval.';

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

				if ( ! is_array($data) )
				{
					$data = array();
				}

				if ( isset($data['count']) )
				{
					$data['count']++;
				}
				else
				{
					$data['count'] = 1;
				}

				if ( $data['count'] == 10 )
				{
					$characterQuest->quest->give_reward();
					$characterQuest->delete();

					return true;
				}
				else
				{
					$data['progress_for_view'] = 'Mata ' . (10 - $data['count']) . ' monstruo(s) del Cementerio Naval.';

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}