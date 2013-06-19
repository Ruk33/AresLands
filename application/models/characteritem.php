<?php

class CharacterItem extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_items';

	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
	}
}