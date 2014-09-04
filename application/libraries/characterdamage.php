<?php

class CharacterDamage extends Damage
{
    public function get_double_hit_chance(Unit $target)
    {
        $attacker = $this->get_attacker();
        return ($attacker->luck + $attacker->luck_extra) * 0.75 + 5;
    }
    
    public function get_critical_chance(Unit $target)
    {
        $dex = $this->get_attacker()->get_final_dexterity();
        $msk = $this->get_attacker()->get_final_magic_skill();
        
        $lvl = $this->get_attacker()->level + $target->level - 1;
        
        $crt = 
            $this->get_attacker()->critical_chance + 
            $this->get_attacker()->critical_chance_extra;
        
        return min(50, ($dex + $msk) / $lvl / 5 + $crt);
    }
    
    public function get_damage(Unit $target)
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