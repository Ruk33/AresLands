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

	/**
	 *	Buscamos un slot en el inventario que esté vacío
	 *
	 *	@return <mixed> Id del slot que está vacío o false en caso de no haber ninguno
	 */
	public static function get_empty_slot()
	{
		$character = Session::get('character');

		for ( $i = 1, $max = 6; $i <= $max; $i++ )
		{
			if ( ! CharacterItem::where('owner_id', '=', $character->id)->where('slot', '=', $i)->first() )
			{
				return $i;
			}
		}

		return false;
	}
}