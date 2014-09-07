<?php

class DungeonMonsterArmor extends MonsterArmor
{
    public function get_block_chance(Damage $damage)
    {
        return 15;
    }
    
    public function get_block_amount(Damage $damage)
    {
        // Bloqueamos 37% del daño
        return $damage->get_amount() * 0.37;
    }
    
    public function before(Damage $damage, Battle $battle)
    {
        $damage->set_amount($damage->get_amount() * 0.75);
    }
    
    public function get_miss_chance(Damage $damage)
    {
        return 43;
    }
    
    public function get_defense(Damage $damage)
    {
        $defense = parent::get_defense($damage) * 1.1;
        
        return $damage->get_attacker()->level / 3 + $defense;
    }
}
