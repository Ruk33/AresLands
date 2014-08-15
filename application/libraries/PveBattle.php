<?php

class PveBattle extends Battle
{
    const MAX_ATTACKS = 50;
    
    protected function onStart() {
        $this->getAttacker()->regenerate_life(true);
        $this->getAttacker()->check_buffs_time();
        
        parent::onStart();
    }
    
    protected function onFinish() {
        $this->getAttacker()->after_battle();
        $this->getAttacker()->save();
    }
    
    protected function damageShouldBeMagic(Unit $attacker, Unit $target)
    {
        return $attacker->get_final_magic() > $attacker->get_final_strength();
    }
    
    protected function shouldContinue() {
        return parent::shouldContinue() && $this->getTurn() < self::MAX_ATTACKS;
    }
    
    public function __construct(Character $attacker, Monster $target)
    {
        $this->attacker = $attacker;
        $this->target = $target;
        
        $this->attackerReport = new BattleReport($attacker, $this);
        $this->targetReport = new BattleReport($target, $this);
        
        $this->start();
    }
}