<?php

class Quest_HieloYRoca
{
	const QUEST_ID = 7;
	public static $monsterId = 138;

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 10 Golem de Hielo';

			$characterQuest->data = $data;
			$characterQuest->save();

			return true;
		}
	}

	public static function onPveBattleWin(Character $character, Npc $monster)
	{
		if ( $monster->id == self::$monsterId )
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

					if ( $data['count'] == 10 )
					{
						$characterQuest->quest->give_reward();
						$characterQuest->delete();
						return true;
					}
				}
				else
				{
					$data['count'] = 1;
				}

				$data['progress_for_view'] = sprintf('Mata %d/10 Golems de Hielo.', 10-$data['count']);

				$characterQuest->data = $data;
				$characterQuest->save();
			}
		}
	}
}