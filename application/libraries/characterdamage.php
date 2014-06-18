<?php

class CharacterDamage extends Damage
{
    protected function get_damage()
    {
        if ( $this->is_magical() )
        {
            return $this->get_attacker()->get_final_magic() / 12;
        }
        else
        {
            return $this->get_attacker()->get_final_strength() / 14;
        }
    }
}