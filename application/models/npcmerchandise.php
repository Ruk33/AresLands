<?php

class NpcMerchandise extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npc_merchandises';

	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
	}
}