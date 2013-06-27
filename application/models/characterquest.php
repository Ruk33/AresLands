<?php

class CharacterQuest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_quests';

	public function quest()
	{
		return $this->belongs_to('Quest', 'quest_id');
	}
}