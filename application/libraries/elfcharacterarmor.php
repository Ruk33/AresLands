<?php

class ElfCharacterArmor extends CharacterArmor
{
    public function get_miss_chance(Damage $damage)
    {
        return parent::get_miss_chance($damage) + 3;
    }
}