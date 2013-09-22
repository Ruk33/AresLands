<?php

class QuestReward extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'quest_rewards';
	public static $key = 'id';

	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
	}
}