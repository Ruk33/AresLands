<?php

class Npc extends Unit
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npcs';
	
    public function get_image_path()
    {
        return URL::base() . "/img/icons/npcs/{$this->id}.png";
    }
    
    /**
     * Obtenemos todos los npcs de una zona
     * 
     * @param Zone $zone
     * @param Character $character
     * @return Eloquent
     */
    public static function get_from_zone(Zone $zone, Character $character = null)
    {
        return static::where('zone_id', '=', $zone->id);
    }
    
    /**
     * Detectamos si npc esta bloqueado para personaje
     * 
     * @param Character $character
     * @return boolean
     */
    public function is_blocked_to(Character $character)
    {
        return false;
    }
}