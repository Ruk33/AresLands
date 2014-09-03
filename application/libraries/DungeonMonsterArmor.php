<?php

class DungeonMonsterArmor extends MonsterArmor
{
    public function before(Damage $damage, Battle $battle)
    {
        $damage->set_amount($damage->get_amount() * 0.75);
    }
    
    public function get_miss_chance(Damage $damage)
    {
        return 33;
    }
    
    public function get_defense(Damage $damage)
    {
        return (parent::get_defense($damage)+5) * $damage->get_attacker()->level;
    }
}
