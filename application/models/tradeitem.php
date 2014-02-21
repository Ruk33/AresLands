<?php

class TradeItem extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'trade_items';
	
	/**
	 * @return Eloquent
	 */
	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
	}
}