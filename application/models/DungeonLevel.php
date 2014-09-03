<?php

class DungeonLevel extends Base_Model
{
    public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'dungeon_levels';
    
    const TYPE_SPECIAL = 2;
    const TYPE_AGAINST_KING = 3;
    
    /**
     * 
     * @param Character $character
     */
    public function consume_required_item(Character $character)
    {
        if ($item = $this->required_item) {
            $characterItem = $character->items()
                                       ->where_item_id($item->id)
                                       ->first();
            
            if ($characterItem) {
                $characterItem->count--;
                $characterItem->save();
            }
        }
    }
    
    /**
	 * Obtenemos la ruta de la imagen del objeto
	 * @return string
	 */
	public function get_image_path()
	{
        if ($this->is_against_king()) {
            return $this->dungeon->king->get_image_path();
        } else {
            return URL::base() . '/img/' . $this->big_image_path;
        }
	}
    
    /**
     * 
     * @return Relationship
     */
    public function dungeon()
    {
        return $this->belongs_to("Dungeon", "dungeon_id");
    }
    
    /**
     * 
     * @return Relationship
     */
    public function target()
    {
        if ($this->is_against_king()) {
            return $this->dungeon->king();
        } else {
            return $this->belongs_to("Monster", "monster_id");
        }
    }
    
    /**
     * 
     * @return Relationship
     */
    public function required_item()
    {
        return $this->belongs_to("Item", "required_item_id");
    }
    
    /**
     * Verificamos si el nivel de la mazmorra tiene requerimientos
     * 
     * @return boolean
     */
    public function has_requirements()
    {
        return $this->required_item_id || $this->required_level;
    }
    
    /**
     * @return string
     */
    public function get_requirements_for_view()
    {
        $formatedString = '';
        $item = $this->required_item;
        
        if ($item) {
            $tooltip = $item->get_text_for_tooltip();
            $tinyBox = $item->get_tiny_box_for_view($tooltip);
            $formatedString .= "<li>{$tinyBox}</li>";
        }
        
        if ($this->required_level) {
            $item = Item::find(Config::get("game.xp_item_id"));
            $tooltip = "Nivel {$this->required_level}";
            $tinyBox = $item->get_tiny_box_for_view($tooltip);
            $formatedString .= "<li>{$tinyBox}</li>";
        }
        
        return '<ul class="inline">' . $formatedString . '</ul>';
    }
    
    /**
     * Verificamos si el nivel es especial (esto generalmente para las
     * recompensas)
     * 
     * @return boolean
     */
    public function is_special()
    {
        return $this->type == self::TYPE_SPECIAL;
    }
    
    /**
     * Verificamos si este nivel es contra el rey
     * 
     * @return boolean
     */
    public function is_against_king()
    {
        return $this->type == self::TYPE_AGAINST_KING;
    }
    
    /**
     * Obtenemos el nivel previo
     * 
     * @return DungeonLevel|null
     */
    public function prev()
    {
        return self::where_dungeon_id($this->dungeon_id)
                   ->where_dungeon_level($this->dungeon_level - 1)
                   ->first();
    }
    
    /**
     * Obtenemos el siguiente nivel
     * Importante hacer notar que si el siguiente nivel no existe, entonces
     * se creara un nivel "falso" en donde batallaremos contra el rey
     * 
     * @return DungeonLevel
     */
    public function next()
    {
        $next = self::where_dungeon_id($this->dungeon_id)
                    ->where_dungeon_level($this->dungeon_level + 1)
                    ->first();
        
        // Si no hay siguiente monstruo, entonces batallamos contra el rey
        if (! $next) {
            $next = new self(array(
                "dungeon_id" => $this->dungeon_id,
                "dungeon_level" => $this->dungeon_level + 1,
                "type" => self::TYPE_AGAINST_KING
            ));
        }
        
        return $next;
    }
}
