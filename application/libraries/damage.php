<?php

class Damage
{
    /**
     *
     * @var Unit
     */
    protected $attacker;
    
    /**
     *
     * @var boolean
     */
    protected $critical;
    
    /**
     *
     * @var boolean
     */
    protected $magical;
    
    /**
     *
     * @var boolean
     */
    protected $miss;
    
    /**
     *
     * @var float
     */
    protected $blocked;
    
    /**
     *
     * @var float
     */
    protected $amount;
    
    /**
     * Reiniciamos los valores
     */
    protected function reset()
    {
        $this->critical = false;
        $this->magical = false;
        $this->miss = false;
        $this->blocked = 0.00;
        $this->amount = 0.00;
    }
    
    /**
     * 
     * @return Unit
     */
    public function get_attacker()
    {
        return $this->attacker;
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_critical()
    {
        return $this->critical;
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_magical()
    {
        return $this->magical;
    }
    
    /**
     * Verificamos si fallo el daño
     * @return boolean
     */
    public function is_miss()
    {
        return $this->miss;
    }
    
    /**
     * Obtenemos la cantidad del daño que ha sido bloqueada
     * @return float
     */
    public function get_blocked()
    {
        return $this->blocked;
    }
    
    /**
     * 
     * @return float
     */
    public function get_amount()
    {
        return $this->amount;
    }
    
    /**
     * 
     * @param float $value
     */
    public function set_amount($value)
    {
        $this->amount = $value;
    }
    
    /**
     * Obtenemos chance de critico
     * 
     * @param Unit $target
     * @return float
     */
    public function get_critical_chance(Unit $target)
    {
        return 5;
    }
    
    /**
     * Obtenemos multiplicador de critico
     * 
     * @param Unit $target
     * @return float
     */
    public function get_critical_multiplier(Unit $target)
    {
        return 1.66;
    }
    
    /**
     * 
     * @return float
     */
    public function get_damage(Unit $target)
    {
        return 0.00;
    }
    
    /**
     * Metodo de ayuda para las vistas (authenticated.index)
     * 
     * @param Unit $target
     * @return float
     */
    public function get_magical_damage(Unit $target)
    {
        $prev = $this->magical;
        $this->magical = true;
        
        $magicalDamage = $this->get_damage($target);
        
        $this->magical = $prev;
        
        return $magicalDamage;
    }
    
    /**
     * 
     * @return float
     */
    public function get_double_hit_chance(Unit $target)
    {
        return 33.00;
    }
    
    /**
     * Antes de hacer daño
     * 
     * @param Unit $target
     * @param Battle $battle
     */
    protected function before(Unit $target, Battle $battle)
    {
        
    }
    
    /**
     * Despues de hacer daño
     * 
     * @param Unit $target
     * @param Battle $battle
     */
    protected function after(Unit $target, Battle $battle)
    {
        
    }
    
    /**
     * 
     * @param Unit $target
     * @param float $amount
     * @param boolean $magical
     * @param boolean $canMiss
     * @param boolean $canBlock
     * @param boolean $canCritical
     * @param boolean $ignoreDefense
     * @param Battle $battle
     * @return boolean
     */
    public function to(Unit $target, 
                       $amount, 
                       $magical,
                       $canMiss,
                       $canBlock,
                       $canCritical,
                       $ignoreDefense, 
                       Battle $battle = null)
    {
        $this->reset();
        
        if ($target->get_combat_behavior() instanceof NonAttackableBehavior) {
            return false;
        }
        
        $armor = $target->get_combat_behavior()->get_armor();
        
        $this->magical = $magical;
        $this->miss = $canMiss && mt_rand(1, 100) <= $armor->get_miss_chance($this);
        
        if (! $this->miss) {
            if ($canBlock) {
                if (mt_rand(1, 100) <= $armor->get_block_chance($this)) {
                    $this->blocked = $armor->get_block_amount($this);
                }
            }
            
            if ($ignoreDefense) {
                $this->amount = $amount;
            } else {
                if ($canCritical) {
                    $criticalChance = $this->get_critical_chance($target);
                    $this->critical = mt_rand(1, 100) <= $criticalChance;
                }
                
                $this->amount = (int) max(
                    0,
                    $amount - $armor->get_defense($this) - $this->blocked
                );
                
                if ($this->critical) {
                    $this->amount *= $this->get_critical_multiplier($target);
                }
                
                $this->before($target, $battle);
                $armor->before($this, $battle);

                $life = $target->get_current_life() - $this->amount;
                $target->set_current_life($life);

                $this->after($target, $battle);
            }
        }
        
        return true;
    }
    
    /**
     * 
     * @param Unit $target
     * @param boolean $magical
     * @param float $amount
     * @param Battle $battle
     */
    public function normal_with_amount(Unit $target, 
                                       $magical, 
                                       $amount, 
                                       Battle $battle = null)
    {
        return $this->to(
            $target, 
            $amount, 
            $magical, 
            true, 
            true, 
            true, 
            false, 
            $battle
        );
    }
    
    /**
     * 
     * @param Unit $target
     * @param boolean $magical
     * @param Battle $battle
     * @return boolean
     */
    public function normal(Unit $target, $magical, Battle $battle = null)
    {
        return $this->normal_with_amount(
            $target, 
            $magical, 
            $this->get_damage($target),
            $battle
        );
    }
    
    /**
     * 
     * @param Unit $attacker
     */
    public function __construct(Unit $attacker)
    {
        $this->attacker = $attacker;
    }
}