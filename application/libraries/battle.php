<?php

abstract class Battle
{
    /**
     *
     * @var Unit
     */
    protected $attacker;
    
    /**
     *
     * @var BattleReport
     */
    protected $attackerReport;
    
    /**
     *
     * @var Unit
     */
    protected $target;
    
    /**
     *
     * @var BattleReport
     */
    protected $targetReport;
    
    /**
     *
     * @var Unit
     */
    protected $winner;
    
    /**
     *
     * @var Unit
     */
    protected $loser;
    
    /**
     *
     * @var integer
     */
    protected $turn;
    
    /**
     *
     * @var Unit
     */
    protected $previousAttacker;
    
    /**
     *
     * @var Unit
     */
    protected $previousTarget;
    
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
     * @return BattleReport
     */
    public function getAttackerReport()
    {
        return $this->attackerReport;
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
     * @return BattleReport
     */
    public function getTargetReport()
    {
        return $this->targetReport;
    }
    
    /**
     * 
     * @param Unit $unit
     * @return BattleReport|null
     */
    public function getReportOf(Unit $unit)
    {
        if ($unit->id == $this->getAttacker()->id) {
            return $this->getAttackerReport();
        } elseif ($unit->id == $this->getTarget()->id) {
            return $this->getTargetReport();
        }
        
        return null;
    }
    
    /**
     * 
     * @return Unit
     */
    public function getWinner()
    {
        $attackerIsAlive = $this->getAttacker()->get_current_life() > 0;
        $targetIsAlive = $this->getTarget()->get_current_life() > 0;
        $damageByAttacker = $this->getAttackerReport()->getDamageDone();
        $damageByTarget = $this->getTargetReport()->getDamageDone();
        $attackerHasDoneMoreDamage = $damageByAttacker > $damageByTarget;
        
        if ($attackerIsAlive) {
            if ($targetIsAlive) {
                if ($attackerHasDoneMoreDamage) {
                    $this->winner = $this->getAttacker();
                } else {
                    $this->winner = $this->getTarget();
                }
            } else {
                $this->winner = $this->getAttacker();
            }
        } else {
            $this->winner = $this->getTarget();
        }
        
        return $this->winner;
    }
    
    /**
     * 
     * @return Unit
     */
    public function getLoser()
    {
        $attackerIsWinner = $this->getWinner()->id == $this->getAttacker()->id;
        
        if ($attackerIsWinner) {
            $this->loser = $this->getTarget();
        } else {
            $this->loser = $this->getAttacker();
        }
        
        return $this->loser;
    }
    
    /**
     * ¿El combate debe continuar?
     * @return boolean
     */
    protected function shouldContinue()
    {
        $attackerIsAlive = $this->getAttacker()->get_current_life() > 0;
        $targetIsAlive = $this->getTarget()->get_current_life() > 0;
        
        return $attackerIsAlive && $targetIsAlive;
    }
    
    /**
     * Este metodo sera ejecutado antes de hacer el ataque
     * 
     * @param Unit $attacker
     * @param Unit $target
     * @return boolean Devolver false para cancelar y que no se haga el ataque
     */
    protected function beforeAttack(Unit $attacker, Unit $target)
    {
        return true;
    }
    
    /**
     * Este metodo sera ejecutado despues de hacer el ataque
     * 
     * @param Unit $attacker
     * @param Unit $target
     * @param Damage $damage
     */
    protected function afterAttack(Unit $attacker, Unit $target, Damage $damage)
    {
        $this->getReportOf($attacker)->registerDoneDamage($target, $damage);
        $this->getReportOf($target)->registerTakenDamage($attacker, $damage);
    }
    
    /**
     * Determinamos si el ataque debe ser magico o fisico
     * 
     * @param Unit $attacker
     * @param Unit $target
     * @return boolean
     */
    protected abstract function damageShouldBeMagic(Unit $attacker, Unit $target);
    
    /**
     * Este metodo sera ejecutado antes del turno
     */
    protected function beforeTurn()
    {
        $this->getAttacker()->get_combat_behavior()->before_turn($this);
        $this->getTarget()->get_combat_behavior()->before_turn($this);
        
        $attacker = $this->getNextAttacker();
        $target   = $this->getNextTarget($attacker);
        $damage   = $attacker->get_combat_behavior()->get_damage();
        
        if ($this->beforeAttack($attacker, $target)) {
            $damage->normal(
                $target, $this->damageShouldBeMagic($attacker, $target), $this
            );
            $this->afterAttack($attacker, $target, $damage);
        }
        
        $this->setPreviousAttacker($attacker);
        $this->setPreviousTarget($target);
    }
    
    /**
     * Este metodo sera ejecutado al finalizar el turno
     */
    protected function afterTurn()
    {
        $this->getAttacker()->get_combat_behavior()->after_turn($this);
        $this->getTarget()->get_combat_behavior()->after_turn($this);
        
        $this->turn++;
    }
    
    /**
     * ¿En que turno estamos?
     * @return integer
     */
    public function getTurn()
    {
        return $this->turn;
    }
    
    /**
     * Obtenemos la unidad que va a ser quien ataque en el proximo ataque
     * (valga la redundancia)
     * @return Unit
     */
    protected function getNextAttacker()
    {
        $prev = $this->getPreviousAttacker();
        $attacker = $this->getAttacker();
        
        if ($prev != null) {
            if ($attacker->id == $prev->id) {
                $attacker = $this->getTarget();
            }
        }
        
        return $attacker;
    }
    
    /**
     * Obtenemos la unidad que va a ser el proximo objetivo del ataque
     * @param Unit $attacker
     * @return Unit
     */
    protected function getNextTarget(Unit $attacker)
    {
        $prev = $this->getPreviousTarget();
        $target = $this->getTarget();
        
        if ($prev != null) {
            if ($target->id == $prev->id) {
                $target = $this->getAttacker();
            }
        }
        
        return $target;
    }
    
    /**
     * 
     * @param Unit $attacker
     */
    protected function setPreviousAttacker(Unit $attacker)
    {
        $this->previousAttacker = $attacker;
    }
    
    /**
     * Obtenemos la unidad que fue quien ataco en el ataque (valga
     * la redundancia) anterior
     * @return Unit
     */
    protected function getPreviousAttacker()
    {
        return $this->previousAttacker;
    }
    
    /**
     * 
     * @param Unit $target
     */
    protected function setPreviousTarget(Unit $target)
    {
        $this->previousTarget = $target;
    }
    
    /**
     * Obtenemos la unidad que fue objetivo del ataque anterior
     * @return Unit
     */
    protected function getPreviousTarget()
    {
        return $this->previousTarget;
    }
    
    /**
     * Antes de comenzar el combate este metodo sera llamado
     */
    protected function onStart()
    {
        $attackerInitialLife = $this->getAttacker()->get_current_life();
        $this->getAttackerReport()->registerInitialLife($attackerInitialLife);
        
        $targetInitialLife = $this->getTarget()->get_current_life();
        $this->getTargetReport()->registerInitialLife($targetInitialLife);
    }
    
    /**
     * Al finalizar el combate este metodo sera ejecutado
     */
    protected function onFinish()
    {
        
    }
    
    /**
     * Comenzar el combate
     */
    protected final function start()
    {
        $attackerIsAttackable = $this->getAttacker()->is_attackable();
        $targetIsAttackable = $this->getTarget()->is_attackable();
        
        if (! $attackerIsAttackable || ! $targetIsAttackable) {
            return;
        }
        
        $this->onStart();
        
        while ($this->shouldContinue()) {
            $this->beforeTurn();
            $this->afterTurn();
        }
        
        $this->onFinish();
    }
}