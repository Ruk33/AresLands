<?php

class Npc extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npcs';
	public static $key = 'id';

	public function get_stats()
	{
		$stats = array();

		$stats['p_damage'] = $this->p_damage;
		$stats['m_damage'] = $this->m_damage;

		$stats['p_defense'] = $this->p_defense;
		$stats['m_defense'] = $this->m_defense;

		$stats['stat_life'] = $this->stat_life;
		$stats['stat_dexterity'] = $this->stat_dexterity;
		$stats['stat_magic'] = $this->stat_magic;
		$stats['stat_strength'] = $this->stat_strength;
		$stats['stat_luck'] = $this->stat_luck;

		return $stats;
	}

	/**
	 *	Obtenemos los npcs (no mounstros) de
	 *	una zona
	 *
	 *	@param <Zone> $zone Zona de donde queremos los npcs
	 *	@return <array> Npcs (no mounstros) que se encuentran en $zone
	 */
	public static function get_npcs_from_zone(Zone $zone)
	{
		if ( ! $zone )
		{
			return array();
		}

		$character = Character::get_character_of_logged_user(array('id'));
		$exploringTime = $character->exploring_times()->select(array('character_id', 'time'))->where('zone_id', '=', $zone->id)->first();

		return Npc::select(array('id', 'name', 'dialog', 'tooltip_dialog'))
		->where('zone_id', '=', $zone->id)
		->where('type', '=', 'npc')
		->where('time_to_appear', '<=', ( isset($exploringTime->time) ) ? $exploringTime->time : 0 )
		->get();
	}

	/**
	 *	Detectamos si un npc está bloqueado para un personaje
	 *
	 *	@param <Character> $character
	 *	@return <bool> true en caso de estar bloqueado, false de lo contrario
	 */
	public function is_blocked_to(Character $character)
	{
		if ( ! $character )
		{
			return true;
		}

		$exploringTime = $character->exploring_times()->select(array('character_id', 'time'))->where('zone_id', '=', $this->zone_id)->first();

		return $this->time_to_appear > $exploringTime->time;
	}

	public function quests()
	{
		return $this->has_many('Quest', 'npc_id');
	}

	public function merchandises()
	{
		return $this->has_many('NpcMerchandise', 'npc_id');
	}
}