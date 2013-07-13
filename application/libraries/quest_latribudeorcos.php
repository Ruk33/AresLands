<?php

class Quest_LaTribuDeOrcos
{
	const QUEST_ID = 5;
	public static $monstersId = array(29, 30, 31, 32);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 3 Orco, 4 Orco Guerrero, 2 Orco Shaman y 1 Orco lider.';

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
						29 => 3,
						30 => 4,
						31 => 2,
						32 => 1
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
					3-$data['count'][29] . ' Orco,' .
					4-$data['count'][30] . ' Orco Guerrero, ' .
					2-$data['count'][31] . ' Orco Shaman y ' .
					1-$data['count'][32] . ' Orco lider.';

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}