<?php

class Quest_ApaciguarALosMuertos
{
	const QUEST_ID = 10;
	public static $monstersId = array(99, 100, 101, 102);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 4 Esqueleto Arcano, 3 Criatura Arcana, 2 Bestia Fantasmal y 1 Brujo Arcano.';

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
						99 => 4,
						100 => 3,
						101 => 2,
						102 => 1
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
					4-$data['count'][99] . ' Esqueleto Arcano,' .
					3-$data['count'][100] . ' Criatura Arcana, ' .
					2-$data['count'][101] . ' Bestia Fantasmal y ' .
					1-$data['count'][102] . ' Brujo Arcano.';

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}