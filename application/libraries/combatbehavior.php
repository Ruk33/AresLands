<?php

interface CombatBehavior
{
    /**
     * @return Unit
     */
    public function get_unit();
    
    /**
     * @return Damage
     */
    public function get_damage();
    
    /**
     * @return Armor
     */
    public function get_armor();
    
    /**
     * Metodo que se ejecuta antes del turno en las batallas
     * 
     * @param Battle $battle
     */
    public function before_turn(Battle $battle);
    
    
    /**
     * Metodo que se ejecuta despues del turno en las batallas
     * 
     * @param Battle $battle
     */
    public function after_turn(Battle $battle);
    
    /**
     * @return real
     */
    public function get_attack_speed();
}