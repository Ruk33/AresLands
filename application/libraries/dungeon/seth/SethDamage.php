<?php

class SethDamage extends MonsterDamage
{
    const COLMILLO_VENENOSO_EVERY_HOW_MANY_TURNS = 5;
    const COLMILLO_VENENOSO_ID = 232323;
    const COLMILLO_VENENOSO_LEVEL = 1;
    const COLMILLO_VENENOSO_CHANCE = 90;
    
    const APRIETE_EVERY_HOW_MANY_TURNS = 3;
    const APRIETE_ID = 232323;
    const APRIETE_LEVEL = 1;
    const APRIETE_CHANCE = 90;
    
    /**
     * 
     * @param Unit $target
     * @param float $chance
     * @param integer $id Id del skill (debuff)
     * @param integer $level
     */
    protected function castDebuff(Unit $target, $chance, $id, $level)
    {
        if (mt_rand(1, 100) <= $chance) {
            if (! $target->has_buff($id)) {
                $seth = $this->get_attacker();
                $seth->cast($target, $id, $level);
            }
        }
    }
    
    protected function before(Unit $target, Battle $battle)
    {
        if ($battle->getTurn() % self::COLMILLO_VENENOSO_EVERY_HOW_MANY_TURNS == 0) {
            $this->amount *= 2;
            $this->castDebuff(
                $target, 
                self::COLMILLO_VENENOSO_CHANCE, 
                self::COLMILLO_VENENOSO_ID, 
                self::COLMILLO_VENENOSO_LEVEL
            );
        }
        
        if ($battle->getTurn() % self::APRIETE_EVERY_HOW_MANY_TURNS == 0) {
            $this->amount *= 2;
            $this->castDebuff(
                $target, 
                self::APRIETE_CHANCE, 
                self::APRIETE_ID, 
                self::APRIETE_LEVEL
            );
        }
    }
}
