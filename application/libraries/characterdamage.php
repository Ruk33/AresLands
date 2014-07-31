<?php

class CharacterDamage extends Damage
{
    public function get_critical_chance(Unit $target)
    {
        $dex = $this->get_attacker()->get_final_dexterity();
        $msk = $this->get_attacker()->get_final_magic_skill();
        
        $lvl = $this->get_attacker()->level + $target->level - 1;
        
        $crt = 
            $this->get_attacker()->critical_chance + 
            $this->get_attacker()->critical_chance_extra;
        
        return max(50, ($dex + $msk) / $lvl / 5 + $crt);
    }
    
    protected function get_damage()
    {        
        if ( $this->is_magical() )
        {
            return 
                $this->get_attacker()->magic_damage +
                $this->get_attacker()->magic_damage_extra +
                $this->get_attacker()->get_final_magic() / 12;
        }
        else
        {
            return 
                $this->get_attacker()->physical_damage +
                $this->get_attacker()->physical_damage_extra +
                $this->get_attacker()->get_final_strength() / 14;
        }
    }
}