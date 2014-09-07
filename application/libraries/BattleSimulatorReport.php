<?php

class BattleSimulatorReport
{
    /**
     * @var integer Cantidad de simulaciones realizadas
     */
    protected $amount = 0;
    
    /**
     *
     * @var Unit
     */
    protected $attacker;
    
    /**
     *
     * @var Unit
     */
    protected $target;
    
    /**
     *
     * @var integer
     */
    protected $attackerVictories = 0;
        
    /**
     *
     * @var integer
     */
    protected $targetVictories = 0;
    
    /**
     *
     * @var array<Battle>
     */
    protected $battles = array();
    
    /**
     * 
     * @return Unit
     */
    public function getAttacker()
    {
        return $this->attacker;
    }
    
    /**
     * 
     * @return Unit
     */
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * 
     * @param Battle $battle
     */
    public function registerBattle(Battle $battle)
    {
        if ($battle->getWinner() == $this->getAttacker()) {
            $this->attackerVictories++;
        } else {
            $this->targetVictories++;
        }
        
        $this->battles[] = $battle;
    }
    
    /**
     * 
     * @return integer
     */
    public function getAttackerVictories()
    {
        return $this->attackerVictories;
    }
    
    /**
     * 
     * @return integer
     */
    public function getAttackerLoses()
    {
        return $this->getSimulationAmount() - $this->attackerVictories;
    }
    
    /**
     * 
     * @return integer
     */
    public function getTargetVictories()
    {
        return $this->targetVictories;
    }
    
    /**
     * 
     * @return integer
     */
    public function getTargetLoses()
    {
        return $this->getSimulationAmount() - $this->targetVictories;
    }
    
    /**
     * 
     * @return array
     */
    public function getBattles()
    {
        return $this->battles;
    }
    
    /**
     * 
     * @param Unit $attacker
     * @return BattleSimulatorReport
     */
    public function registerAttacker(Unit $attacker)
    {
        if (! $this->attacker) {
            $this->attacker = $attacker;
        }
        
        return $this;
    }
    
    /**
     * 
     * @param Unit $target
     * @return BattleSimulatorReport
     */
    public function registerTarget(Unit $target)
    {
        if (! $this->target) {
            $this->target = $target;
        }
        
        return $this;
    }
    
    /**
     * 
     * @param integer $amount
     * @return BattleSimulatorReport
     */
    public function registerSimulationAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getSimulationAmount()
    {
        return $this->amount;
    }
}
