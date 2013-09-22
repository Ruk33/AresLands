<?php

class Quest_AyudaATuPueblo extends Quest_Template_Fight_Against_Monster
{
	protected static $questId = 28;

	const MONSTER_AMOUNT = 5;

	const PHASE_BATTLE_MONSTER = 0;

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::$questId )
		{
			$characterQuest = self::get_character_quest($character);

			if ( $characterQuest )
			{
				self::update_progress_for_view($characterQuest, self::PHASE_BATTLE_MONSTER, '0/' . self::MONSTER_AMOUNT . ' - Mounstruo de cualquier ciudad');
				self::add_phase($characterQuest, self::PHASE_BATTLE_MONSTER);

				$characterQuest->save();

				return true;
			}
		}

		return false;
	}

	public static function onPveBattleWin(Character $character, Npc $monster)
	{
		if ( $character->has_quest(self::$questId) )
		{
			$characterQuest = self::get_character_quest($character);

			if ( $characterQuest )
			{
				self::add_count($characterQuest, 0, self::MONSTER_AMOUNT);

				$counting = self::get_counting($characterQuest, 0);

				self::update_progress_for_view($characterQuest, self::PHASE_BATTLE_MONSTER, $counting . '/' . self::MONSTER_AMOUNT . ' - Mounstruo de cualquier ciudad');

				if ( $counting == self::MONSTER_AMOUNT )
				{
					self::mark_phase_as_completed($characterQuest, self::PHASE_BATTLE_MONSTER);
					
					$characterQuest->progress = 'reward';

					$characterQuest->save();

					return true;
				}

				$characterQuest->save();
			}
		}

		return false;
	}
}