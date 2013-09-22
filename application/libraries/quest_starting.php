<?php
/* Iniciando, ¡bienvenido a AresLands! */
class Quest_Starting extends Quest_Template_Fight_Against_Monster
{
	protected static $questId = 27;

	const MONSTER_ID = 9; // Topo
	const MONSTER_AMOUNT = 2; // Cantidad de topos a pelear

	const AMOUNT_OF_COINS = 5000; // 5 plata

	const DROW_WEAPON_ID = 21; // Báculo de Iniciante
	const ELF_WEAPON_ID = 25; // Arco de Iniciante
	const DWARF_WEAPON_ID = 16; // Martillo de Herrero
	const HUMAN_WEAPON_ID = 13; // Espada de iniciante

	const PHASE_EQUIP_ITEM = 0;
	const PHASE_BATTLE_MONSTER = 1;

	private static function onProgress(CharacterQuest $characterQuest)
	{
		$phases = $characterQuest->data['phases'];

		if ( $phases[self::PHASE_EQUIP_ITEM] && $phases[self::PHASE_BATTLE_MONSTER] )
		{
			$characterQuest->progress = 'reward';
			$characterQuest->save();
		}
	}

	public static function onAcceptQuest(Character $character, Quest $quest)
	{
		if ( $quest->id == self::$questId )
		{
			$characterQuest = self::get_character_quest($character);

			if ( $characterQuest )
			{
				$character->add_coins(self::AMOUNT_OF_COINS);

				switch ( $character->race )
				{
					case 'drow':
						$character->add_item(self::DROW_WEAPON_ID);
						break;

					case 'elf':
						$character->add_item(self::ELF_WEAPON_ID);
						break;

					case 'dwarf':
						$character->add_item(self::DWARF_WEAPON_ID);
						break;

					case 'human':
						$character->add_item(self::HUMAN_WEAPON_ID);
						break;
				}

				self::update_progress_for_view($characterQuest, self::PHASE_EQUIP_ITEM, 'Equípate el arma');
				self::update_progress_for_view($characterQuest, self::PHASE_BATTLE_MONSTER, '0/' . self::MONSTER_AMOUNT . ' - Batalla Topo');

				self::add_phase($characterQuest, self::PHASE_EQUIP_ITEM);
				self::add_phase($characterQuest, self::PHASE_BATTLE_MONSTER);

				$characterQuest->save();				

				return true;
			}
		}

		return false;
	}

	public static function onEquipItem(Character $character, Item $item)
	{
		$characterQuest = self::get_character_quest($character);

		if ( $characterQuest )
		{
			if ( in_array($item->id, array(self::DROW_WEAPON_ID, self::ELF_WEAPON_ID, self::DWARF_WEAPON_ID, self::HUMAN_WEAPON_ID)) )
			{
				self::mark_phase_as_completed($characterQuest, self::PHASE_EQUIP_ITEM);
				self::onProgress($characterQuest);

				$characterQuest->save();

				return true;
			}
		}

		return false;
	}

	public static function onPveBattle(Character $character, Npc $monster)
	{
		if ( $monster->id == self::MONSTER_ID )
		{
			if ( $character->has_quest(self::$questId) )
			{
				$characterQuest = self::get_character_quest($character);

				self::add_count($characterQuest, self::MONSTER_ID, self::MONSTER_AMOUNT);

				$counting = self::get_counting($characterQuest, self::MONSTER_ID);

				self::update_progress_for_view($characterQuest, self::PHASE_BATTLE_MONSTER, $counting . '/' . self::MONSTER_AMOUNT . ' - Batalla Topo');

				if ( $counting == self::MONSTER_AMOUNT )
				{
					self::mark_phase_as_completed($characterQuest, self::PHASE_BATTLE_MONSTER);
					self::onProgress($characterQuest);

					$characterQuest->save();

					return true;
				}

				$characterQuest->save();
			}
		}

		return false;
	}
}