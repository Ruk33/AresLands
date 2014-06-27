<?php

class Trade extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'trades';
	public static $key = 'id';

	protected $rules = array(
		'trade_item_id' => 'required|exists:character_items|tradeitem|tradeowner:seller_id',
		'amount' => 'required|numeric|min:1|tradeitemamount:trade_item_id',
		'price_copper' => 'required|numeric|min:1',
		'time' => 'in:8,16,24'
	);

	protected $messages = array(
		'amount_required' => 'El monto es requerido',
		'amount_numeric' => 'El monto es incorrecto (solo números)',
		'amount_min' => 'La cantidad mínima debe ser 1',

		'price_copper_required' => 'El precio es requerido',
		'price_copper_numeric' => 'El precio es incorrecto (solo números)',
		'price_copper_min' => 'El precio mínimo debe ser 1'
	);

	/**
	 * Query para obtener los trades que son validos.
	 * Valido = que puede ser comprado o que son del personaje que esta conectado
	 *
	 * @return Eloquent
	 */
	public static function get_valid()
	{
		$character = Character::get_character_of_logged_user(array('id', 'clan_id'));

		return static::where(function($query) use ($character)
					 {
					 	$query->where('until', '>', time());
					 	$query->or_where('seller_id', '=', $character->id);
					 })
					 ->where_in('clan_id', array(0, $character->clan_id));
	}
	
	/**
	 * Filtramos trades por clase de objeto
	 * @param string $class weapon|armor|consumible
	 * @return Eloquent|boolean
	 */
	public static function filter_by_item_class($class)
	{
		if ( ! in_array($class, array('weapon', 'armor', 'consumible')))
		{
			return false;
		}
		
		return static::get_valid()
					 ->join('trade_items', 'trade_items.id', '=', 'trade_item_id')
					 ->join('items', 'items.id', '=', 'trade_items.item_id')
					 ->where('items.class', '=', $class);
	}

	public function seller()
	{
		return $this->belongs_to('Character', 'seller_id');
	}

	public function trade_item()
	{
		return $this->belongs_to('TradeItem', 'trade_item_id');
	}
	
	/**
	 * Verificamos si el trade expiro
	 * @return boolean
	 */
	public function has_expired()
	{
		return $this->until < time();
	}
	
	/**
	 * Verificamos si trade puede ser comprado por personaje
	 * @param Character $character
	 * @return boolean
	 */
	public function can_be_buyed_by(Character $character)
	{
		if ( $this->has_expired() )
		{
			return false;
		}
		
		if ( $character->id == $this->seller_id )
		{
			return false;
		}

		if ( $this->clan_id > 0 )
		{
			if ( $character->clan_id != $this->clan_id )
			{
				return false;
			}
		}
		
		$coins = $character->get_coins();
		
		if ( ! $coins )
		{
			return false;
		}
		
		if ( $coins->count < $this->price_copper )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Verificamos si personaje puede cancelar comercio
	 * @param Character $character
	 * @return boolean
	 */
	public function can_be_cancelled_by(Character $character)
	{
		return $this->seller_id == $character->id;
	}

	/**
	 *
	 * @return float|int
	 */
	public function get_commission_percentage()
	{
		$commission = 0;

		switch ( $this->duration )
		{
			case 8:
				// 5% comision
				$commission = 5;
				break;

			case 16:
				// 9% comision
				$commission = 9;
				break;

			case 24:
				// 14% comision
				$commission = 14;
				break;
		}

		return $commission;
	}
	
	/**
	 * Intentamos comprar trade con personaje
	 * @param Character $character
	 * @return boolean
	 */
	public function buy(Character $character)
	{		
		if ( ! $this->can_be_buyed_by($character) )
		{
			return false;
		}
		
		if ( ! $character->add_item($this->trade_item->item, $this->amount) )
		{
			return false;
		}
		
		$character->add_coins(-$this->price_copper);
		$this->seller->add_coins($this->price_copper * ($this->get_commission_percentage() / 100));
		
		Message::trade_buy($this, $character);
		
		$this->delete();
		
		return true;
	}
	
	/**
	 * Cancelamos el trade devolviendo el objeto al vendedor y borrando el registro
	 * @return boolean
	 */
	public function cancel()
	{
		// Verificamos que podamos devolver el objeto
		if ( ! $this->seller->add_item($this->trade_item->item, $this->amount) )
		{
			return false;
		}
		
		$this->delete();
		
		return true;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function delete()
	{
		$tradeItem = $this->trade_item;
		
		if ( $tradeItem )
		{
			$tradeItem->delete();
		}
		
		return parent::delete();
	}
}