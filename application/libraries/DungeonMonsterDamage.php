<?php

class DungeonMonsterDamage extends MonsterDamage
{    
    public function get_critical_chance(Unit $target)
    {
        return mt_rand(26, 45);
    }
    
    public function get_critical_multiplier(Unit $target)
    {
        return max(1.35, $target->level / 40);
    }
    
    public function get_damage(Unit $target)
    {
        $targetLevel = $target->level;
        $monsterLevel = $this->get_attacker()->level;
        $levelDiff = max(1, $targetLevel - $monsterLevel);
        
        // Cada nivel de diferencia hace un 3% mas fuerte al monstruo
        // Sumamos 0.85 para evitar daño menor en caso de que la diferencia sea 1
        return ($levelDiff * 0.03 + 0.85) * parent::get_damage($target);
    }
}
