<?php

class CharacterQuest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_quests';
	public static $key = 'id';

	public function get_data()
	{
		if ( is_array($this->get_attribute('data')) )
		{
			return $this->get_attribute('data');
		}

		return unserialize($this->get_attribute('data'));
	}

	public function set_data($data)
	{
		$this->set_attribute('data', serialize($data));
	}

	public function get_progress_for_view()
	{
		$progresses = $this->data['progress_for_view'];
		$stringToView = "";

		foreach ( $progresses as $progress )
		{
			$stringToView .= "<li>$progress</li>";
		}

		return "<ul>$stringToView</ul>";
	}

	public function quest()
	{
		return $this->belongs_to('Quest', 'quest_id');
	}
}