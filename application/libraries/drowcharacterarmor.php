<?php

class DrowCharacterArmor extends CharacterArmor
{
    public function get_block_chance(Damage $damage)
    {
        return 0;
    }
    
    public function get_miss_chance(Damage $damage)
    {
        return parent::get_miss_chance($damage) - 2;
    }
}