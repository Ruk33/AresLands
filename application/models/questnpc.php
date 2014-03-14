<?php

/**
 * Class QuestNpc
 * Modelo encargado de guardar los npcs con los que se tiene
 * que interactuar (hablar, matar, etc.)
 */
class QuestNpc extends Eloquent
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table      = 'quest_npcs';
	public static $key        = 'id';

	const TALK_ACTION = 'talk';
	const KILL_ACTION = 'kill';

	/**
	 * @return \Laravel\Database\Eloquent\Relationship
	 */
	public function quest()
	{
		return $this->belongs_to("Quest", "quest_id");
	}

	/**
	 * @return \Laravel\Database\Eloquent\Relationship
	 */
	public function npc()
	{
		return $this->belongs_to("Npc", "npc_id");
	}
}