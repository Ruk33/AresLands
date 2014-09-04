<?php

class DungeonMonsterArmor extends MonsterArmor
{
    public function before(Damage $damage, Battle $battle)
    {
        $damage->set_amount($damage->get_amount() * 0.75);
    }
    
    public function get_miss_chance(Damage $damage)
    {
        return 25;
    }
    
    public function get_defense(Damage $damage)
    {
        $defense = parent::get_defense($damage) * 1.3;
        
        return $damage->get_attacker()->level / 3 + $defense;
    }
}
