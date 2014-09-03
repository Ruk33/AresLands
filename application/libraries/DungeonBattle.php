<?php

class DungeonBattle extends Battle
{
    /**
     *
     * @var DungeonLevel
     */
    protected $dungeonLevel;
    
    protected function giveRewards()
    {
        $attackerWin = $this->getWinner()->id == $this->getAttacker()->id;
        
        if ($this->getWinner() instanceof Character) {
            if (! $attackerWin) {
                $chance = 0;
            } elseif ($this->dungeonLevel->is_special()) {
                $chance = 90;
            } else {
                $chance = 20;
            }

            if (mt_rand(1, 100) <= $chance) {
                $amount = 1;
                $item = \Laravel\IoC::resolve('Item')
                                    ->where_quality(Item::QUALITY_LEGENDARY)
                                    ->where('level', '>=', $this->getAttacker()->level)
                                    ->order_by(DB::raw("RAND()"))
                                    ->first();
            } else {
                $amount = mt_rand(10, 15);
                $item = \Laravel\IoC::resolve('Item')
                                    ->find(Config::get('game.coin_id'));
            }

            if ($item && $this->getAttacker()->add_item($item, $amount)) {
                $this->getAttackerReport()->registerReward($item, $amount);
            }
        }
        
        if ($attackerWin) {
            $this->dungeonLevel->dungeon->do_progress(
                $this->getAttacker(), 
                $this->dungeonLevel
            );
        }
    }
    
    protected function checkForNewKingOrReset()
    {        
        if ($this->dungeonLevel->is_against_king()) {
            $winner = $this->getWinner();
            
            if ($this->getAttacker()->id == $winner->id) {
                if ($this->dungeonLevel->dungeon->can_be_king($winner)) {
                    $this->dungeonLevel->dungeon->convert_into_king($winner);
                }
            } else {
                $this->dungeonLevel->dungeon->reset_progress($this->getAttacker());
            }
        }
    }
    
    protected function onStart() {
        $this->getAttacker()->check_buffs_time();
        $this->getTarget()->check_buffs_time();
        
        $this->getAttacker()->regenerate_life(true);
        $this->getTarget()->regenerate_life(true);
        
        parent::onStart();
    }
    
    protected function onFinish()
    {        
        $this->getAttacker()->save();
        
        if ($this->getTarget() instanceof Character) {
            $this->getTarget()->save();
        }
        
        $this->giveRewards();
        $this->checkForNewKingOrReset();
        
        $this->dungeonLevel->dungeon->after_battle(
            $this->getAttacker(), 
            $this->dungeonLevel
        );
    }
    
    protected function damageShouldBeMagic(Unit $attacker, Unit $target)
    {
        return $attacker->get_final_magic() > $attacker->get_final_strength();
    }
    
    public function __construct(Character $attacker, 
                                Unit $target, 
                                DungeonLevel $dungeonLevel)
    {
        $this->attacker = $attacker;
        $this->target = $target;
        $this->dungeonLevel = $dungeonLevel;
        
        $this->attackerReport = new BattleReport($attacker, $this);
        $this->targetReport = new BattleReport($target, $this);
        
        $this->start();
    }
}
