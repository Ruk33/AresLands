<?php

class TradeItemValidation extends Laravel\Validator
{	
	/**
	 * 
	 * @return CharacterItem
	 */
	protected function get_character_item($id)
	{
		return IoC::resolve("CharacterItem")->where_id($id)->first_or_empty();
	}
	
	/**
	 * Verificamos que el dueño del objeto a vender sea correcto
	 * 
	 * @param string $attribute
	 * @param string $value
	 * @param array $parameters
	 * @return boolean
	 */
	public function validate_tradeowner($attribute, $value, $parameters)
	{
		$sellerId = $this->attributes[$parameters[0]];
		
		return $this->get_character_item($value)->owner_id == $sellerId;
	}
	
	/**
	 * Verificamos que el objeto a vender sea valido (este en inventario
	 * y que realmente pueda ser vendido)
	 * 
	 * @param string $attribute
	 * @param string $value
	 * @param array $parameters
	 * @return boolean
	 */
	public function validate_tradeitem($attribute, $value, $parameters)
	{
		$characterItem = $this->get_character_item($value);
		
		if ( $characterItem->location != "inventory" )
		{
			return false;
		}
		
		if ( ! $characterItem->item->selleable )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Verificamos que la cantidad sea correcta
	 * 
	 * @param string $attribute
	 * @param string $value
	 * @param array $parameters
	 * @return boolean
	 */
	public function validate_tradeitemamount($attribute, $value, $parameters)
	{
		$characterItem = $this->get_character_item($parameters[0]);
		
		if ( ! $characterItem->item->stackable && $value > 1 )
		{
			return false;
		}
		
		if ( $characterItem->count < $value )
		{
			return false;
		}
		
		return true;
	}
}