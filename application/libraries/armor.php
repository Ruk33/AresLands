<?php

abstract class Armor
{
    protected $defender;
    
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
     * @param Unit $defender
     */
    public function __construct(Unit $defender)
    {
        $this->defender = $defender;
    }
}