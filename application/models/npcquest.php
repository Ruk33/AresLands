<?php

class NpcQuest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npc_quests';
	public static $key = 'id';

	public function quest()
	{
		return $this->belongs_to('Quest', 'quest_id');
	}
}