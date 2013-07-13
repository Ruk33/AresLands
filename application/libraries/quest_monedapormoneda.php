<?php

class Quest_MonedaPorMoneda
{
	const QUEST_ID = 12;
	public static $npcId = 164;
	public static $monsterId = 98;

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Contacta con Espia para conocer la localización exacta de Sooju y luego asesinarlo.';

			$characterQuest->data = $data;
			$characterQuest->save();

			return true;
		}
	}

	public static function onNpcTalk(Character $character, Npc $npc)
	{
		if ( ! $character || ! $npc )
		{
			return false;
		}

		if ( $npc->id == self::$npcId )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			if ( $characterQuest )
			{
				$data = $characterQuest->data;

				$data['alreadyTalk'] = true;
				$data['progress_for_view'] = 'Ahora que conoces la localización, asesina a Sooju';

				$characterQuest->data = $data;
				$characterQuest->save();

				return true;
			}
		}
	}

	public static function onPveBattleWin(Character $character, Npc $monster)
	{
		if ( ! $character || ! $monster )
		{
			return false;
		}

		if ( $monster->id == self::$monsterId )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			if ( $characterQuest )
			{
				$data = $characterQuest->data;

				if ( isset($data['alreadyTalk']) )
				{
					$characterQuest->progress = 'reward';
					$characterQuest->save();

					return true;
				}
			}
		}
	}
}