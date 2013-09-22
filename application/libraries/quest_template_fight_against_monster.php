<?php

abstract class Quest_Template_Fight_Against_Monster
{
	protected static $questId = 0;

	public static function get_character_quest(Character $character)
	{
		return $character->quests()->where('quest_id', '=', static::$questId)->first();
	}

	public static function add_phase(CharacterQuest $characterQuest, $phase, $status = false)
	{
		if ( ! $characterQuest )
		{
			return;
		}

		$data = $characterQuest->data;

		if ( ! is_array($data) )
		{
			$data = array();
		}

		if ( ! isset($data['phases']) )
		{
			$data['phases'] = array();
		}

		$data['phases'][$phase] = $status;

		$characterQuest->data = $data;
	}

	public static function update_progress_for_view(CharacterQuest $characterQuest, $phase, $newText)
	{
		if ( ! $characterQuest )
		{
			return;
		}

		$data = $characterQuest->data;

		if ( ! is_array($data) )
		{
			$data = array();
		}

		if ( ! isset($data['progress_for_view']) )
		{
			$data['progress_for_view'] = array();
		}

		$data['progress_for_view'][$phase] = $newText;

		$characterQuest->data = $data;
	}

	public static function mark_phase_as_completed(CharacterQuest $characterQuest, $phase)
	{
		if ( ! $characterQuest )
		{
			return;
		}

		$data = $characterQuest->data;

		if ( ! is_array($data) )
		{
			$data = array();
		}

		if ( ! is_array($data['phases']) )
		{
			$data['phases'] = array();
		}

		$data['phases'][$phase] = true;

		$characterQuest->data = $data;

		self::update_progress_for_view($characterQuest, $phase, '<s>' . $characterQuest->data['progress_for_view'][$phase] . '</s>');
	}

	public static function get_counting(CharacterQuest $characterQuest, $monsterId)
	{
		if ( ! $characterQuest )
		{
			return;
		}

		$data = $characterQuest->data;

		if ( ! is_array($data) )
		{
			$data = array();
		}

		if ( isset($data['count']) && isset($data['count'][$monsterId]) )
		{
			return $data['count'][$monsterId];
		}

		return 0;
	}

	public static function add_count(CharacterQuest $characterQuest, $monsterId, $limit, $amount = 1)
	{
		if ( ! $characterQuest )
		{
			return;
		}

		$data = $characterQuest->data;

		if ( ! is_array($data) )
		{
			$data = array();
		}

		if ( ! isset($data['count']) )
		{
			$data['count'] = array();
		}

		if ( ! isset($data['count'][$monsterId]) )
		{
			$data['count'][$monsterId] = 0;
		}

		$data['count'][$monsterId] += $amount;

		if ( $data['count'][$monsterId] > $limit )
		{
			$data['count'][$monsterId] = $limit;
		}

		$characterQuest->data = $data;
	}
}