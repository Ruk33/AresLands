<?php

class CharacterQuest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_quests';

	public function get_data()
	{
		return unserialize($this->get_attribute('data'));
	}

	public function set_data($data)
	{
		$this->set_attribute('data', serialize($data));
	}

	public function get_progress_for_view()
	{
		return $this->data['progress_for_view'];
	}

	public function quest()
	{
		return $this->belongs_to('Quest', 'quest_id');
	}
}