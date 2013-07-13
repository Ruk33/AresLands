<?php

class Quest_LasMinasEstanBajoAtaque
{
	const QUEST_ID = 3;
	public static $monstersId = array(21, 22, 23, 24);

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Mata 3 Grembling, 4 Grembling Verdugo, 2 Grembling Shaman y 1 Grembling Berseker.';

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
						21 => 3,
						22 => 4,
						23 => 2,
						24 => 1
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
					3-$data['count'][21] . ' Grembling,' .
					4-$data['count'][22] . ' Grembling Verdugo, ' .
					2-$data['count'][23] . ' Grembling Shaman y ' .
					1-$data['count'][24] . ' Grembling Berseker.';

					$characterQuest->data = $data;
					$characterQuest->save();
				}
			}
		}
	}
}