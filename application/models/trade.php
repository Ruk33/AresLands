<?php

class Trade extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'trades';

	protected $rules = array(
        'seller_id' => 'required|exists:characters,id',
        
        // id del objeto del personaje (NO del item)
		'item_id' => 'required|exists:character_items,id|tradeitem|tradeowner:seller_id',
		'amount' => 'required|numeric|min:1|tradeitemamount:item_id',
		'price_copper' => 'required|numeric|min:1',
		'duration' => 'in:8,16,24'
	);

	protected $messages = array(
        'seller_id_required' => 'El vendedor es requerido',
        'seller_id_exists' => '¡El vendedor no existe!',
        
        'item_id_required' => 'El objeto a comerciar es requerido',
        'item_id_exists' => '¡El objeto a comerciar no existe!',
        'tradeitem' => 'El objeto a comerciar no es valido',
        'tradeowner' => '¡No eres dueño de ese objeto!',
        
		'amount_required' => 'El monto es requerido',
		'amount_numeric' => 'El monto es incorrecto (solo números)',
		'amount_min' => 'La cantidad mínima debe ser 1',
        'tradeitemamount' => 'No posees esa cantidad para comerciar',

		'price_copper_required' => 'El precio es requerido',
		'price_copper_numeric' => 'El precio es incorrecto (solo números)',
		'price_copper_min' => 'El precio mínimo debe ser 1',
        
        'duration_in' => 'La duracion es incorrecta',
	);
    
    public function save()
    {
        // Inicialmente el campo 'item_id' guarda el id del objeto del personaje
        // (no el id del objeto en si), esto para que el validador pueda funcionar
        // de forma correcta. Sabiendo esto, antes de guardar y si el row NO
        // existe, entonces actualizamos 'item_id' con el id del objeto en si
        if (! $this->exists) {
            $characterItem = $this->seller->items()->find($this->item_id);
            $this->item_id = $characterItem->item_id;
        }
        
        return parent::save();
    }
    
    public function get_validator($attributes, $rules, $messages = array())
    {
        return TradeItemValidation::make($attributes, $rules, $messages);
    }

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
					 ->join('items', 'items.id', '=', 'trades.item_id')
					 ->where('items.class', '=', $class);
	}

	public function seller()
	{
		return $this->belongs_to('Character', 'seller_id');
	}

	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
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
		
		if ( ! $character->add_item($this->item, $this->amount) )
		{
			return false;
		}
		
		$character->add_coins(-$this->price_copper);
		
		$comission = $this->price_copper * ($this->get_commission_percentage() / 100);
		$this->seller->add_coins($this->price_copper - $comission);
		
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
		if ( ! $this->seller->add_item($this->item, $this->amount) )
		{
			return false;
		}
		
		$this->delete();
		
		return true;
	}
}