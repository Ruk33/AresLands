<?php

class MonsterArmor extends Armor
{
    public function get_miss_chance(Damage $damage)
    {
        if ( $damage->is_magical() )
        {
            return 0;
        }
        
        return 5 + ($this->defender->level - $damage->get_attacker()->level) * 1.3;
    }

    public function get_defense(Damage $damage)
    {
        if ( $damage->is_magic() )
        {
            return ($this->defender->get_final_magic_resistance() / ($damage->get_attacker()->level * 3)) * 0.75;
        }
        else
        {
            $defense = $this->defender->get_final_resistance() + $this->defender->get_final_strength();
            return $defense / ($defense + 50 * $damage->get_attacker()->level + 400);
        }
    }
}