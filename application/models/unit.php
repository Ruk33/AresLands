<?php

/**
 * Unit es la clase madre de todas las unidades que se podran encontrar
 * Las unidades se dividen en dos: personajes y personajes no controlables por jugadores (npcs)
 */
abstract class Unit extends Widget
{
    /**
     *
     * @var CombatBehavior
     */
    protected $combatBehavior;
    
    /**
     * Lanzamos habilidad a objetivo
     * 
     * @param Unit $target
     * @param integer $skillId
     * @param integer $level
     * @param integer $times ¿Cuantas veces?
     * @return boolean
     */
    public function cast(Unit $target, $skillId, $level, $times = 1)
    {
        if ($skill = Skill::where_id($skillId)->where_level($level)->first()) {
            return $skill->cast($this, $target, $times);
        }
        
        return false;
    }
    
    /**
     * 
     * @param float $amount
     */
    public function heal($amount)
    {
        $this->set_current_life($this->get_current_life() + $amount);
    }
    
    /**
     * Verificamos si la unidad puede ser atacada
     * @return boolean
     */
    public function is_attackable()
    {
        return ! $this->combatBehavior instanceof NonAttackableBehavior;
    }
    
    /**
     * 
     * @param CombatBehavior $combatBehavior
     */
    public function set_combat_behavior(CombatBehavior $combatBehavior)
    {
        $this->combatBehavior = $combatBehavior;
    }
    
    /**
     * 
     * @return CombatBehavior
     */
    public function get_combat_behavior()
    {
        return $this->combatBehavior;
    }
    
    public function get_tooltip()
    {
        $message = "<div class='unit-tooltip'>"
                    . "<img src='{$this->get_image_path()}' class='pull-left' width='32px' height='32px'>"
                    . "<h3>{$this->name} <small>Nivel: {$this->level}</small></h3>"
                    . "<p>{$this->dialog}</p>"
                    . "<ul class='unstyled'>"
                            . "<li>Fuerza fisica: {$this->stat_strength}</li>"
                            . "<li>Destreza fisica: {$this->stat_dexterity}</li>"
                            . "<li>Resistencia fisica: {$this->stat_resistance}</li>"
                            . "<li>Poder magico: {$this->stat_magic}</li>"
                            . "<li>Habilidad magica: {$this->stat_magic_skill}</li>"
                            . "<li>Resistencia magica: {$this->stat_magic_resistance}</li>"
                    . "</ul>"
                 . "</div>";

		return $message;
    }
    
	/**
	 * Obtenemos la vida actual
	 *
	 * @return float
	 */
	public function get_current_life()
    {
        return 0.00;
    }

	/**
     * Modificamos vida actual de la unidad
     * 
	 * @param float $value
	 */
	public function set_current_life($value)
    {
        
    }
    
    /**
     * Obtenemos los drops de la unidad
     * 
     * @return array
     */
    public function drops()
    {
        return array();
    }
    
    /**
     * Obtenemos los objetos que una unidad puede arrojar para
     * un personaje en especifico
     * 
     * @return array
     */
    public function drops_for(Character $character)
    {
        $drops = array();
        
        // Compatibilidad con lo viejo        
		$drops[] = array(
            'item_id' => Config::get('game.coin_id'), 
            'amount' => $this->level * 15 * $character->get_coins_rate()
        );
        
        return $drops;
    }
    
    /**
	 * Verificamos si widget tiene buff activo
	 *
	 * @param integer $skillId
	 * @return boolean
	 */
	public function has_buff($skillId)
	{
		return false;
	}
    
    /**
     * Obtenemos array con buffs activos
     * @return array
     */
    public function get_buffs()
    {
        return array();
    }
    
    /**
     * Verificamos el tiempo de los buffs activos de la unidad
     */
    public function check_buffs_time()
    {
        
    }
    
    /**
     * Regeneramos vida por segundo
     * @param boolean $save
     */
    public function regenerate_life($save = false)
    {
        
    }
    
    /**
     * 
     * @return Item|null
     */
    public function get_weapon()
    {
        return null;
    }
    
    /**
     * 
     * @return Item|null
     */
    public function get_shield()
    {
        return null;
    }
    
    /**
     * Query para obtener zone en donde esta la unidad
     * 
     * @return Eloquent
     */
    public function zone()
	{
		return $this->belongs_to("Zone", "zone_id");
	}
}