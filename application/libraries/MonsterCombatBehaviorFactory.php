<?php

class MonsterCombatBehaviorFactory
{
    const DUNGEON_BOSS_NESSY = 56556;
    const DUNGEON_BOSS_LOHG = 565657;
    const DUNGEON_BOSS_SETH = 535958;
    const DUNGEON_BOSS_ONIX = 595959;
    
    /**
     * Obtenemos el comportamiento de batalla segun el monstruo
     * 
     * @param Monster $monster
     * @return CombatBehavior
     */
    public function get(Monster $monster)
    {
        $combatBehavior = null;
        
        switch ($monster->id) {
            case self::DUNGEON_BOSS_NESSY:
                $combatBehavior = new NessyCombatBehavior(
                    $monster, 
                    new NessyDamage($monster),
                    new NessyArmor($monster)
                );
                break;
            
            case self::DUNGEON_BOSS_LOHG:
                $combatBehavior = new LohgCombatBehavior(
                    $monster, 
                    new LohgDamage($monster),
                    new LohgArmor($monster)
                );
                break;
            
            case self::DUNGEON_BOSS_SETH:
                $combatBehavior = new AttackableBehavior(
                    $monster, 
                    new MonsterDamage($monster),
                    new MonsterArmor($monster)
                );
                break;
            
            case self::DUNGEON_BOSS_ONIX:
                $combatBehavior = new AttackableBehavior(
                    $monster, 
                    new MonsterDamage($monster),
                    new MonsterArmor($monster)
                );
                break;
            
            default:
                $combatBehavior = new AttackableBehavior(
                    $monster, 
                    new MonsterDamage($monster),
                    new MonsterArmor($monster)
                );
        }
        
        return $combatBehavior;
    }
}