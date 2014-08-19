<?php

class PvpBattle extends Battle
{
    /**
     * Porcentaje de chance que tiene la pareja en atacar
     */
    const CHANCE_PAIR_ATTACK = 33;
    
    /**
     * ¿Cuanta cantidad de barra de actividad se le llena al atacante?
     */
    const REWARD_ACTIVITY_BAR = 2;
    
    /**
     * Cantidad maxima de turnos
     */
    const MAXIMUM_TURNS = 20;
    
    /**
     *
     * @var Character
     */
    protected $pair;
    
    /**
     *
     * @var BattleReport
     */
    protected $pairReport;
    
    /**
     * 
     * @return Character|null
     */
    public function getPair()
    {
        return $this->pair;
    }
    
    /**
     * 
     * @return BattleReport
     */
    public function getPairReport()
    {
        return $this->pairReport;
    }
    
    /**
     * 
     * @param Character $unit
     * @return BattleReport|null
     */
    public function getReportOf(Unit $unit) {
        $report = parent::getReportOf($unit);
        
        if (! $report) {
            $report = $this->getPairReport();
        }
        
        return $report;
    }
    
    /**
     * Verificamos si deberia atacar o no la pareja
     * @return boolean
     */
    protected function shouldAttackPair()
    {
        $pair = $this->getPair();
        
        if (! $pair) {
            return false;
        }
        
        // Verificamos que el ataque anterior no fue de la pareja
        if ($this->getPreviousAttacker()->id == $pair->id) {
            return false;
        }
        
        return mt_rand(1, 100) <= self::CHANCE_PAIR_ATTACK;
    }
    
    protected function getNextAttacker() {
        if ($this->shouldAttackPair()) {
            return $this->getPair();
        }
        
        return parent::getNextAttacker();
    }
    
    protected function getNextTarget(Unit $attacker) {
        $pair = $this->getPair();
        
        if ($pair) {
            // Si el atacante es la pareja, entonces evitamos que se
            // devuelva a su compañero (el que inicio la batalla)
            if ($pair->id == $attacker->id) {
                return $this->getTarget();
            }
        }
        
        return parent::getNextTarget($attacker);
    }
    
    protected function shouldContinue() {
        return parent::shouldContinue() && $this->getTurn() < self::MAXIMUM_TURNS;
    }
    
    protected function damageShouldBeMagic(Unit $attacker, Unit $target)
    {
        return $attacker->get_final_magic() > $attacker->get_final_strength();
    }
    
    /**
     * Verificamos si el ganador debe o no recibir recompensas
     * Recordar que el oro sera otorgado de igual forma
     * @return boolean
     */
    protected function winnerShouldReceiveRewards()
    {
        return $this->getWinner()->level - 2 < $this->getLoser()->level;
    }

    /**
     * Verificamos orbe (damos o aplicamos proteccion segun sea el caso)
     */
    protected function checkOrb()
    {
        $targetOrb = $this->getTarget()->orbs()->first();

        if ($targetOrb) {
            if ($targetOrb->can_be_stolen_by($this->getAttacker())) {
                if ($this->getWinner() == $this->getAttacker()) {
                    $targetOrb->give_to($this->getAttacker());
                    $this->getAttackerReport()->registerOrb($targetOrb);
                } else {
                    $targetOrb->failed_robbery($this->getAttacker());
                }
            }
        }
    }
    
    /**
     * Otorgamos recompensas al ganador
     */
    protected function giveRewards()
    {
        $this->getWinner()->pvp_points++;
        ActivityBar::add($this->getAttacker(), self::REWARD_ACTIVITY_BAR);
        
        foreach ($this->getLoser()->drops() as $reward) {            
            $item = Laravel\IoC::resolve('Item')->find($reward['item_id']);
            
            if (! $item) {
                continue;
            }
            
            if ($this->getWinner()->add_item($item, $reward['amount'])) {
                $this->getReportOf($this->getWinner())
                     ->registerReward($item, $reward['amount']);
            }
        }
    }
    
    /**
     * Verificamos si hay que colocar protecciones
     */
    protected function checkProtections()
    {
        if ($this->getAttacker()->level > $this->getTarget()->level) {
            AttackProtection::add(
                $this->getAttacker(), 
                $this->getTarget(), 
                Config::get('game.protection_time_on_lower_level_pvp')
            );
        }
    }
    
    protected function onStart() {
        $this->getAttacker()->check_buffs_time();
        $this->getTarget()->check_buffs_time();
        
        if ($pair = $this->getPair()) {
            $pair->check_buffs_time();
            $pair->regenerate_life(true);
            
            $pairInitialLife = $pair->get_current_life();
            $this->getPairReport()->registerInitialLife($pairInitialLife);
        }
        
        $this->getAttacker()->regenerate_life(true);
        $this->getTarget()->regenerate_life(true);
        
        parent::onStart();
    }
    
    protected function onFinish() {
        $this->giveRewards();
        $this->checkOrb();
        $this->checkProtections();
        
        $this->getAttacker()->after_pvp_battle($this->getTarget());
        
        $this->getAttacker()->save();
        $this->getTarget()->save();
    }
    
    /**
     * 
     * @param Character $attacker
     * @param Character $target
     * @param Character $pair
     */
    public function __construct(Character $attacker, 
                                Character $target, 
                                Character $pair = null)
    {
        $this->attacker = $attacker;
        $this->target = $target;
        $this->pair = $pair;
        
        $this->attackerReport = new BattleReport($attacker, $this);
        $this->targetReport = new BattleReport($target, $this);
        
        if ($pair) {
            $this->pairReport = new BattleReport($pair, $this);
        }
        
        $this->start();
    }
}