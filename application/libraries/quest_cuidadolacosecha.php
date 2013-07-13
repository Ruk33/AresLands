<?php

class Quest_CuidadoLaCosecha
{
	const QUEST_ID = 4;
	public static $monstersId = array(25, 26, 27, 28);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 3 Tirx, 4 Tirx de Agua, 2 Tirx de Tierra y 1 Tirx de Fuego.';

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
						25 => 3,
						26 => 4,
						27 => 2,
						28 => 1
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
					3-$data['count'][25] . ' Tirx,' .
					4-$data['count'][26] . ' Tirx de Agua, ' .
					2-$data['count'][27] . ' Tirx de Tierra y ' .
					1-$data['count'][28] . ' Tirx de Fuego.';

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}