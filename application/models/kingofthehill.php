<?php

/**
 * Esta clase tiene como objetivo manejar todo el comportamiento y la logica
 * del evento Rey de la Colina
 * 
 * @property integer $character_id Id del personaje
 * @property integer $position Posicion en el evento
 */
class KingOfTheHill extends Eloquent
{
    public static $table = 'king_of_the_hill';
    public static $softDelete = false;
	public static $timestamps = false;
	public static $key = 'id';
    
    /**
     * @const integer Id del buff de reduccion de tiempo para el primer puesto
     */
    const FIRST_TIME_REDUCTION_BUFF_ID = 5;
    
    /**
     * @const integer Id del buff de reduccion de tiempo para el segundo puesto
     */
    const SECOND_TIME_REDUCTION_BUFF_ID = 5;
    
    /**
     * @const integer Id del buff de reduccion de tiempo para el tercer puesto
     */
    const THIRD_TIME_REDUCTION_BUFF_ID = 5;
    
    /**
     * @const Maxima cantidad de posiciones
     */
    const MAX_POSITIONS = 5;
    
    /**
     * Obtenemos la lista de personajes (o monstruos) de la lista (ranking)
     * @return array
     */
    public static function get_list()
    {
        $list = array();
        
        for ( $i = 1; $i < self::MAX_POSITIONS; $i++ )
        {
            $list[$i] = self::where_position($i)->first();
            
            if ( ! $list[$i] )
            {
                $list[$i] = Monster::order_by(DB::raw("RAND()"))->first();
            }
        }
        
        return $list;
    }
    
    /**
     * Damos recompensa periodica al jugador
     */
    public function give_periodic_reward()
    {
        $character = $this->character;
        $character->add_coins($character->level * 50 * (100 / $this->position));
        
        switch ( $this->position )
        {
            case 1:
                // 33% chance
                if ( mt_rand(1, 3) == 1 )
                {
                    $character->ironfist_user->add_coins(3);
                }
                
                Skill::find(self::FIRST_TIME_REDUCTION_BUFF_ID)->cast($character, $character);
                
                break;
            
            case 2:
                // 16% chance
                if ( mt_rand(1, 6) == 1 )
                {
                    $character->ironfist_user->add_coins(1);
                }
                
                Skill::find(self::SECOND_TIME_REDUCTION_BUFF_ID)->cast($character, $character);
                
                break;
            
            case 3:
                Skill::find(self::THIRD_TIME_REDUCTION_BUFF_ID)->cast($character, $character);
                break;
        }
    }
    
    /**
     * Damos recompensa periodica a todos los puestos
     */
    public static function give_periodic_reward_all()
    {
        $kings = static::where_in('position', range(1, self::MAX_POSITIONS))->get();
        
        foreach ( $kings  as $king )
        {
            $king->give_periodic_reward();
        }
    }
    
    /**
     * Obtenemos la posicion de un personaje
     * @param Character $character
     * @return integer Se devuelve -1 si el personaje no esta en ningun puesto
     */
    public static function get_character_position(Character $character)
    {
        $position = static::where('character_id', '=', $character->id)->first();
        
        if ( ! $position )
        {
            return -1;
        }
        
        return $position->position;
    }
    
    /**
     * Obtenemos la clase css (para el marco) dependiendo de la posicion del personaje
     * @param Character $character
     * @return string
     */
    public static function get_character_css_frame_class(Character $character)
    {
        $css = '';
        
        switch ( self::get_character_position($character) )
        {
            case 1;
                $css = 'king_of_the_hill_frame_first';
                break;
            
            case 2:
                $css = 'king_of_the_hill_frame_second';
                break;
            
            case 3:
                $css = 'king_of_the_hill_frame_third';
                break;
        }
        
        return $css;
    }
    
    /**
     * Obtenemos la clase css (para el aura) dependiendo de la posicion del personaje
     * @param Character $character
     * @return string
     */
    public static function get_character_css_aura_class(Character $character)
    {
        $css = '';
        
        switch ( self::get_character_position($character) )
        {
            case 1;
                $css = 'king_of_the_hill_aura_first';
                break;
            
            case 2:
                $css = 'king_of_the_hill_aura_second';
                break;
            
            case 3:
                $css = 'king_of_the_hill_aura_third';
                break;
        }
        
        return $css;
    }
    
    /**
     * Reiniciamos las posiciones
     */
    public static function reset()
    {
        DB::query("TRUNCATE TABLE " . self::$table);
    }
    
    /**
     * Obtenemos el personaje
     * @return Eloquent
     */
    public function character()
    {
        return $this->belongs_to("Character", "character_id");
    }
}