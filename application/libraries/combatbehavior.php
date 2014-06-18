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
     * @return real
     */
    public function get_attack_speed();
}