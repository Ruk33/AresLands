<?php

class Dungeon extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'dungeons';
    
    /**
     * Precio de monedas vip que tiene que gastar para evitar el CD
     */
    const VIP_PRICE = 10;
    
    /**
     * Obtenemos los dias que todavía le quedan al evento antes de que termine
     * 
     * @return integer
     */
    public function days_left()
    {
        return Carbon\Carbon::createFromDate(null, 10, 4)->diffInDays();
    }
    
    /**
     * Verificamos si el evento ha finalizado
     * 
     * @return boolean
     */
    public function has_finished()
    {
        return $this->days_left() == 0;
    }
    
    /**
     * 
     * @param Character $character
     * @return CharacterDungeon
     */
    public function get_character_progress(Character $character)
    {
        return $character->dungeons()->where_dungeon_id($this->id)->first();
    }
    
    /**
     * 
     * @param Character $character
     * @return \CharacterDungeon
     */
    protected function get_character_progress_or_create(Character $character)
    {
        $progress = $this->get_character_progress($character);
        
        if (! $progress) {
            $progress = new CharacterDungeon(array(
                "character_id" => $character->id,
                "dungeon_id" => $this->id,
                "dungeon_level" => 1,
                "last_attempt" => time(),
            ));
            
            $progress->save();
        }
        
        return $progress;
    }
    
    /**
     * 
     * @param Character $character
     * @param DungeonLevel $dungeonLevel
     */
    public function after_battle(Character $character, DungeonLevel $dungeonLevel)
    {
        $progress = $this->get_character_progress_or_create($character);
        
        $progress->last_attempt = time() + 15 * 60 * 60;
        $progress->save();
    }
    
    /**
     * Verificamos si personaje es rey
     * 
     * @param Character $character
     * @return boolean
     */
    public function is_character_king(Character $character)
    {
        return $character->id == $this->king_id;
    }
    
    /**
     * Reiniciamos el progreso de la mazmorra al personaje
     * 
     * @param Character $character
     */
    public function reset_progress(Character $character)
    {
        // Evitamos borrar el progreso ya que si es nulo mostramos un mensaje
        // de ayuda (por ser la primera vez)
        $progress = $character->dungeons()->where_dungeon_id($this->id)->first();
        
        if ($progress) {
            $progress->dungeon_level = 1;
            $progress->save();
        }
    }
    
    /**
     * Convertimos personaje a rey
     * 
     * @param Character $character
     */
    public function convert_into_king(Character $character)
    {
        Message::king_of_dungeon($character);
        
        Laravel\IoC::resolve("Skill")
                   ->find(Config::get("game.vip_reduction_time_skill"))
                   ->cast($character, $character);
        
        $this->king_id = $character->id;
        $this->king_since = time();
        
        $this->save();
    }
    
    /**
     * Verificamos si personaje puede ser rey
     * 
     * @param Character $character
     * @return boolean
     */
    public function can_be_king(Character $character)
    {
        $characterIsAlreadyKing = $this->where_king_id($character->id)
                                       ->take(1)
                                       ->count() == 1;
        
        if ($characterIsAlreadyKing) {
            return false;
        }
        
        $progress = $this->get_character_progress($character);
        
        if (! $progress) {
            return false;
        }
        
        if ($progress->dungeon_level < $this->get_last_level()->dungeon_level) {
            return false;
        }
        
        return true;
    }    
    
    /**
     * Llamar este metodo cuando el personaje logra pasar un nivel del dungeon
     * 
     * @param Character $character
     * @param DungeonLevel $dungeonLevel Nivel del dungeon que logro pasar
     */
    public function do_progress(Character $character, DungeonLevel $dungeonLevel)
    {
        $progress = $this->get_character_progress_or_create($character);
        
        $progress->dungeon_level++;
        $progress->save();
        
        if ($this->can_be_king($character)) {
            $this->convert_into_king($character);
        }
    }
    
    /**
     * 
     * @param Character $character
     * @param DungeonLevel $dungeonLevel
     * @return boolean
     */
    public function has_character_completed_level(Character $character, 
                                                  DungeonLevel $dungeonLevel)
    {
        $progress = $this->get_character_progress($character);
        
        if ($progress) {
            return $progress->dungeon_level > $dungeonLevel->dungeon_level;
        }
        
        return false;
    }
    
    /**
     * 
     * @param Character $character
     * @param boolean $formatted
     * @return integer|string
     */
    public function get_character_cd(Character $character, $formatted = false) {
        $cd = 0;
        
        if ($progress = $this->get_character_progress($character)) {
            $cd = max(0, $progress->last_attempt - time());
        }
        
        if ($formatted) {
            $cd = Carbon\Carbon::createFromTime(0, 0, 0)
                               ->addSeconds($cd)
                               ->toTimeString();
        }
        
        return $cd;
    }
    
    /**
     * 
     * @params Character $character
     * @return boolean
     */
    public function has_character_cd(Character $character)
    {
        return $this->get_character_cd($character) > 0;
    }
    
    /**
     * Verificamos si personaje puede hacer nivel
     * 
     * @param Character $character
     * @param DungeonLevel $dungeonLevel
     * @return boolean
     */
    public function can_character_do_level(Character $character, DungeonLevel $dungeonLevel)
    {
        if ($this->has_character_cd($character)) {
            return false;
        }
        
        if ($this->is_character_king($character)) {
            return false;
        }
        
        $prev = $dungeonLevel->prev();
        
        if ($prev && ! $this->has_character_completed_level($character, $prev)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 
     * @param Character $character
     * @param DungeonLevel $dungeonLevel
     * @return Battle
     */
    public function do_level(Character $character, DungeonLevel $dungeonLevel)
    {
        $dungeonLevel->consume_required_item($character);
        
        return new DungeonBattle(
            $character, 
            $dungeonLevel->target()->first(), 
            $dungeonLevel
        );
    }
    
    /**
     * 
     * @param Character $character
     * @return null|DungeonLevel
     */
    public function get_character_level(Character $character)
    {
        $progress = $this->get_character_progress_or_create($character);
        $dungeonLevel = $this->levels()
                             ->where_dungeon_level($progress->dungeon_level)
                             ->first();

        // Si el nivel no existe, o el personaje ya es rey
        // entonces pasamos directamente el nivel del rey
        if (! $dungeonLevel || $this->is_character_king($character)) {
            $dungeonLevel = $this->get_last_level()->next();
        }
        
        return $dungeonLevel;
    }
    
    /**
     * 
     * @param Character $character
     * @param DungeonLevel $dungeonLevel
     * @return string|DungeonBattle
     */
    public function do_level_or_error(Character $character, 
                                      DungeonLevel $dungeonLevel)
    {
        if ($character->zone_id != $this->zone_id) {
            return "¡Las trampas no son bienvenidas en las mazmorras!";
        }
        
        if ($character->level < $dungeonLevel->required_level) {
            return "No tienes suficiente nivel";
        }
        
        if ($this->has_character_completed_level($character, $dungeonLevel)) {
            return "Ya tienes ese nivel completo";
        }
        
        // El nivel previo puede ser null
        $prevDungeonLevel = $dungeonLevel->prev();
        
        if ($prevDungeonLevel) {            
            if (! $this->has_character_completed_level($character, $prevDungeonLevel)) {
                return "Debes completar el nivel previo";
            }
        }
        
        if ($this->has_character_cd($character)) {
            if (! $character->user->consume_coins(self::VIP_PRICE)) {
                return "No tienes suficientes IronCoins";
            }
        }
        
        if ($item = $dungeonLevel->required_item) {
            $hasItem = $character->items()
                                 ->where_item_id($item->id)
                                 ->take(1)
                                 ->count() == 1;
            
            if (! $hasItem) {
                return "No tienes el objeto requerido";
            }
        }
        
        return $this->do_level($character, $dungeonLevel);
    }
    
    /**
     * Obtenemos el ultimo nivel de la mazmorra
     * 
     * @return DungeonLevel
     */
    public function get_last_level()
    {
        return $this->levels()->order_by("dungeon_level", "desc")->first();
    }
    
    /**
     * 
     * @return Relationship
     */
    public function levels()
    {
        return $this->has_many("DungeonLevel", "dungeon_id");
    }
    
    /**
     * 
     * @return Relationship
     */
	public function king()
    {
        return $this->belongs_to("Character", "king_id");
    }
    
    /**
     * 
     * @return Relationship
     */
    public function zone()
    {
        return $this->belongs_to("Zone", "zone_id");
    }
}