<?php

class Quest_RecuperarLoPerdido
{
	const QUEST_ID = 9;
	public static $monstersId = array(65, 66, 67, 68, 69, 70, 71, 72);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 10 monstruos de Camit.';

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
					$data['progress_for_view'] = sprintf('Mata %d/10 monstruos de Camit.', 10-$data['count']);

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}