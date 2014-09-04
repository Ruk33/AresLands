<?php

class CharacterArmor extends Armor
{
    /**
     * Pequeño cache para evitar hacer multiples peticiones
     * innecesarias a la base de datos
     * @var Item|boolean
     */
    protected $shield = false;
    
    /**
     * 
     * @return Item
     */
    protected function get_cached_shield()
    {
        if ( $this->shield === false )
        {
            $this->shield = $this->defender->get_shield();
        }
        
        return $this->shield;
    }
    
    public function get_block_chance(Damage $damage)
    {        
        if ( ! $this->get_cached_shield() )
        {
            return 0;
        }
        
        $attacker = $damage->get_attacker();
        $attackerStat = ( $damage->is_magical() ) ? $attacker->get_final_magic() : $attacker->get_final_strength();
        
        return 5 + ($this->defender->get_final_strength() - $attackerStat) * 0.01;
    }
    
    public function get_miss_chance(Damage $damage)
    {
        if ( $damage->is_magical() )
        {
            return 0;
        }
        
        return ($this->defender->get_final_dexterity() - $damage->get_attacker()->get_final_dexterity() * 0.75) * 0.04;
    }

    public function get_defense(Damage $damage)
    {
        if ( $damage->is_magical() )
        {
            return ($this->defender->get_final_magic_resistance() / ($damage->get_attacker()->level * 5)) * 0.75;
        }
        else
        {
            $defense = $this->defender->get_final_resistance() + $this->defender->get_final_strength();
            return $defense / ($defense + 85 * $damage->get_attacker()->level + 400);
        }
    }
}