<?php

class Npc extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npcs';

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
		return Npc::select(array('id', 'name', 'dialog', 'tooltip_dialog'))->where('zone_id', '=', $zone->id)->where('type', '=', 'npc')->get();
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