<?php

class NessyDamage extends MonsterDamage
{
    const EVERY_HOW_MANY_TURNS = 3;
    const DEBUFF_ID = 232323;
    const DEBUFF_LEVEL = 1;
    const DEBUFF_CHANCE = 40;
    
    /**
     * 
     * @param Unit $target
     */
    protected function castDebuff(Unit $target)
    {
        if (mt_rand(1, 100) <= self::DEBUFF_CHANCE) {
            if (! $target->has_buff(self::DEBUFF_ID)) {
                $nessy = $this->get_attacker();
                $nessy->cast($target, self::DEBUFF_ID, self::DEBUFF_LEVEL);
            }
        }
    }
    
    protected function before(Unit $target, Battle $battle)
    {
        if ($battle->getTurn() % self::EVERY_HOW_MANY_TURNS == 0) {
            $this->amount *= 3;
            $this->castDebuff($target);
        }
    }
}
