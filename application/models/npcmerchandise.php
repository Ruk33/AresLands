<?php

class NpcMerchandise extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npc_merchandises';
	public static $key = 'id';

	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
	}

	public function npc()
	{
		return $this->belongs_to('Merchant', 'npc_id');
	}
}