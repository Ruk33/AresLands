<?php

class HumanCharacterArmor extends CharacterArmor
{
    public function get_miss_chance(Damage $damage)
    {
        return parent::get_miss_chance($damage) + 1;
    }
}