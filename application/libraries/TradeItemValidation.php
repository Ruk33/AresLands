<?php

class TradeItemValidation extends Laravel\Validator
{	
	/**
	 * Se devuelve dependencia
	 * @return CharacterItem
	 */
	protected function character_item()
	{
		return IoC::resolve("CharacterItem");
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
        $count = $this->character_item()
                      ->where_owner_id($sellerId)
                      ->where_item_id($value)
                      ->count();
        
		return $count > 0;
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
		$characterItem = $this->character_item()->where_item_id($value)->first();
		
        if (! $characterItem) {
            return false;
        }
        
		if ($characterItem->location != "inventory") {
			return false;
		}
		
		if (! $characterItem->item->selleable) {
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
        $characterItemId = $this->attributes[$parameters[0]];
		$characterItem = $this->character_item()
                              ->where_item_id($characterItemId)
                              ->first();
		
        if (! $characterItem) {
            return false;
        }
        
		if (! $characterItem->item->stackable && $value > 1) {
			return false;
		}
		
		if ($characterItem->count < $value) {
			return false;
		}
		
		return true;
	}
}