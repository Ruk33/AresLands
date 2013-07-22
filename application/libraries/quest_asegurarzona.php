<?php

class Quest_AsegurarZona
{
	public static $questsId = array(25, 26);
	public static $monstersId = array(147, 148, 149, 150, 151);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( in_array($quest->id, self::$questsId) )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', $quest->id)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 15 monstruos del Valle de Ceniza.';

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
			$characterQuest = $character->quests()->where_in('quest_id', self::$questsId)->first();

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

				if ( $data['count'] == 15 )
				{
					$characterQuest->quest->give_reward();
					$characterQuest->delete();

					return true;
				}
				else
				{
					$data['progress_for_view'] = sprintf('Mata %d/15 monstruos del Valle de Ceniza.', 15-$data['count']);

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}