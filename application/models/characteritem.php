<?php

use Laravel\IoC;

class CharacterItem extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_items';
	
    /**
     * Damos los buffs que otorga un objeto al personaje
     * que lo posea
     */
    public function give_skills()
    {
        $character = $this->character;
        $item = $this->item;
        
        if ($item->skill != '0-0') {            
            foreach ((array) $item->get_skills() as $data) {
                $skill = IoC::resolve("Skill")->where_id($data["id"])
                                              ->where_level($data["level"])
                                              ->first();
                
                if ($skill) {
                    $skill->cast($character, $character);
                }
            }
        }
    }
    
    /**
     * Removemos los buffs que otorga un objeto al personaje
     * que lo posea
     */
    public function remove_skills()
    {
        $character = $this->character;
        $item = $this->item;
        
        if ($item->skill != '0-0') {
            $skills = $item->get_skills();

            foreach ((array) $skills as $skill) {
                $character->skills()
                          ->where_skill_id($skill->id)
                          ->where_level($skill->level)
                          ->delete();
            }
        }
    }
    
	public function save()
	{
		// Evitamos tener negativos como cantidad
		$this->count = max(0, $this->count);
		
		// Si la cantidad es menor o igual a 0 y el objeto no son monedas...
		if ( $this->count == 0 && $this->item_id != Config::get('game.coin_id') )
		{
			return $this->delete();
		}
		
		return parent::save();
	}
	
	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
	}

	public function character()
	{
		return $this->belongs_to('Character', 'owner_id');
	}

	/**
	 *	Buscamos un slot en el inventario que esté vacío
	 *
	 *	@return <mixed> Id del slot que está vacío o false en caso de no haber ninguno
	 */
	public static function get_empty_slot()
	{
		$character = Character::get_character_of_logged_user(array('id'));

		for ( $i = 1, $max = 6; $i <= $max; $i++ )
		{
			if ( $character->items()->where('slot', '=', $i)->count() == 0 )
			{
				return $i;
			}
		}

		return false;
	}

	public function save_in_inventory()
	{
		$emptySlot = self::get_empty_slot();

		if ( $emptySlot )
		{
			$this->location = 'inventory';
			$this->slot = $emptySlot;

			$this->save();

			Event::fire('unequipItem', array($this));
		}

		return $emptySlot;
	}
}