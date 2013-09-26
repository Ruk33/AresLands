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
		/*
		$progresses = $this->data['progress_for_view'];
		$stringToView = "";

		foreach ( $progresses as $progress )
		{
			$stringToView .= "<li>$progress</li>";
		}

		return "<ul>$stringToView</ul>";
		*/
		
		$data = $this->data;
		$stringToView = '';
		
		if ( ! is_array($data) )
		{
			return;
		}
		
		foreach ( $data as $text_for_view )
		{
			if ( isset($text_for_view['text_for_view']) )
			{
				// Evitamos cadenas vacias
				if ( $text_for_view['text_for_view'] ) 
				{
					$stringToView .= '<li>' . $text_for_view['text_for_view'] . '</li>';
				}
			}
		}
		
		return "<ul>$stringToView</ul>";
	}
	
	public function set_var($key, $value, $textForView = null, $strike = false)
	{
		$data = $this->data;
		
		if ( ! is_array($data) )
		{
			$data = array();
		}
		
		if ( ! isset($data[$key]) )
		{
			$data[$key] = array();
		}
		
		$data[$key]['value'] = $value;
		
		if ( $textForView )
		{
			$data[$key]['text_for_view'] = $textForView;
		}
		
		if ( $strike )
		{
			if ( $data[$key]['text_for_view'] )
			{
				$data[$key]['text_for_view'] = '<s>' . $data[$key]['text_for_view'] . '</s>';
			}
		}
		
		$this->data = $data;
		$this->save();
	}
	
	public function get_var($key)
	{
		$data = $this->data;
		
		if ( ! is_array($data) )
		{
			return null;
		}
		
		if ( ! isset($data[$key]) )
		{
			return null;
		}
		
		if ( ! isset($data[$key]['value']) )
		{
			return null;
		}
		
		return $data[$key]['value'];
	}

	public function quest()
	{
		return $this->belongs_to('Quest', 'quest_id');
	}
	
	public function character()
	{
		return $this->belongs_to('Character', 'character_id');
	}
}