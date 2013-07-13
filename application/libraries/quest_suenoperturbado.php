<?php

class Quest_SuenoPerturbado
{
	const QUEST_ID = 6;
	public static $monstersId = array(33, 34, 35, 36);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 3 Fantasma, 4 Alma en Pena, 2 Ente Fantasmal y 1 Dulces Sueños.';

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

				if ( ! is_array($data) || ! isset($data['count']) )
				{
					$data = array();
					$data['count'] = array(
						33 => 3,
						34 => 4,
						35 => 2,
						36 => 1
					);
				}

				if ( $data['count'][$monster->id] > 0 )
				{
					$data['count'][$monster->id]--;
				}

				$finished = true;
				foreach ( $data['count'] as $monsterCount )
				{
					if ( $monsterCount != 0 )
					{
						$finished = false;
						break;
					}
				}

				if ( $finished )
				{
					$characterQuest->quest->give_reward();
					$characterQuest->delete();

					return true;
				}
				else
				{
					$data['progress_for_view'] = 'Mata ' .
					3-$data['count'][33] . ' Fantasma,' .
					4-$data['count'][34] . ' Alma en Pena, ' .
					2-$data['count'][35] . ' Ente Fantasmal y ' .
					1-$data['count'][36] . ' Dulces Sueños.';

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}