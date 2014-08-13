<?php

class NonAttackableBehavior implements CombatBehavior
{
    public function get_armor()
    {
        return null;
    }

    public function get_damage()
    {
        return null;
    }

    public function get_unit()
    {
        return null;
    }

    public function after_turn(Battle $battle)
    {
        
    }

    public function before_turn(Battle $battle)
    {
        
    }

    public function get_attack_speed()
    {
        
    }

}