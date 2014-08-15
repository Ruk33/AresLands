<?php

abstract class Armor
{
    protected $defender;
    
    /**
     * Metodo que se ejecuta antes de recibir daño
     * 
     * @param Damage $damage
     * @param Battle $battle
     */
    public function before(Damage $damage, Battle $battle)
    {
        
    }
    
    /**
     * 
     * @param Damage $damage
     * @return real
     */
    public function get_miss_chance(Damage $damage)
    {
        return 0.00;
    }
    
    /**
     * 
     * @param Damage $damage
     * @return real
     */
    public function get_block_amount(Damage $damage)
    {
        return 0.00;
    }
    
    /**
     * 
     * @param Damage $damage
     * @return real
     */
    public function get_block_chance(Damage $damage)
    {
        return 0.00;
    }
    
    /**
     * 
     * @param Damage $damage
     * @return real
     */
    public abstract function get_defense(Damage $damage);
    
    /**
     * 
     * @return Unit
     */
    public function get_defender()
    {
        return $this->defender;
    }
    
    /**
     * 
     * @param Unit $defender
     */
    public function __construct(Unit $defender)
    {
        $this->defender = $defender;
    }
}