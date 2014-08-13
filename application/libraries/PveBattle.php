<?php

class PveBattle extends Battle
{    
    protected function onStart() {
        $this->getAttacker()->regenerate_life(true);
        $this->getAttacker()->check_buffs_time();
        
        parent::onStart();
    }
    
    protected function onFinish() {
        $this->getAttacker()->after_battle();
        $this->getAttacker()->save();
    }
    
    public function __construct(Character $attacker, Monster $target)
    {
        $this->attacker = $attacker;
        $this->target = $target;
        
        $this->attackerReport = new BattleReport($attacker, $this);
        
        $this->start();
    }
}