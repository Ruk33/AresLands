<?php

class DungeonMonsterDamage extends MonsterDamage
{
    public function get_critical_chance(Unit $target)
    {
        return 33;
    }
    
    public function get_critical_multiplier(Unit $target)
    {
        return $target->level / 15 + 2;
    }
    
    public function get_damage()
    {
        return parent::get_damage() * 13;
    }
}
