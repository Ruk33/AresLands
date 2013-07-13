<?php

class Quest_Urgente
{
	const QUEST_ID = 19;
	public static $npcId = 164;
	public static $daggerId = 2;
	public static $bowId = 25;

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Consígueme una Daga de Hueso y un Arco de Iniciante.';

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

			if ( ! $characterQuest )
			{
				return false;
			}
			
			$data = $characterQuest->data;

			if ( ! isset($data['dagger']) )
			{
				$dagger = $character->items()->select(array('id'))->where('item_id', '=', self::$daggerId)->first();

				if ( $dagger )
				{
					$data['progress_for_view'] = 'Consígueme un Arco de Iniciante.';

					$data['dagger'] = true;
					$dagger->delete();
				}
			}

			if ( ! isset($data['bow']) )
			{
				$bow = $character->items()->select(array('id'))->where('item_id', '=', self::$bowId)->first();

				if ( $bow )
				{
					$data['progress_for_view'] = 'Consígueme una Daga de Iniciante.';

					$data['bow'] = true;
					$bow->delete();
				}
			}

			if ( isset($data['dagger']) && isset($data['bow']) )
			{
				$characterQuest->progress = 'reward';
				$characterQuest->save();

				return true;
			}
			else
			{
				$characterQuest->data = $data;
				$characterQuest->save();
			}
		}
	}
}