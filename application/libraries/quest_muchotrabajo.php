<?php

class Quest_MuchoTrabajo
{
	const QUEST_ID = 20;
	public static $monstersId = array(112, 113, 114, 115, 116);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 10 Ember, 6 Salamandra, 3 Espectro de Fuego, 2 Mastin de Fuego y 1 Golem de Magma';

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
						112 => 10,
						113 => 6,
						114 => 3,
						115 => 2,
						116 => 1,
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
					10-$data['count'][112] . ' Ember,' .
					6-$data['count'][113] . ' Salamandra, ' .
					3-$data['count'][114] . ' Especto de Fuego, ' .
					2-$data['count'][115] . ' Mastin de Fuego y ' .
					1-$data['count'][116] . ' Golem de Magma';

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}