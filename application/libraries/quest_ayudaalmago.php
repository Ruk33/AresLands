<?php

class Quest_AyudaAlMago
{
	const QUEST_ID = 15;
	public static $npcId = 159;
	public static $itemId = 30;

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::QUEST_ID )
		{
			$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

			$data = $characterQuest->data;

			$data['progress_for_view'] = 'Consigue 10 pociones ligeras, y hablame.';

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
			$characterItem = $character->items()->select(array('id', 'count'))->where('item_id', '=', self::$itemId)->first();

			if ( $characterItem && $characterItem->count >= 10 )
			{
				$characterQuest = $character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

				if ( $characterQuest )
				{
					$characterItem->count -= 10;

					if ( $characterItem->count > 0 )
					{
						$characterItem->save();
					}
					else
					{
						$characterItem->delete();
					}

					$characterQuest->progress = 'reward';
					$characterQuest->save();

					return true;
				}
			}
		}
	}
}