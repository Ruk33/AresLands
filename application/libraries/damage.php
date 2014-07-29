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
    protected function get_damage()
    {
        return 0.00;
    }
    
    /**
     * 
     * @return float
     */
    public function get_double_hit_chance()
    {
        return 33.00;
    }
    
    /**
     * 
     * @param Unit $target
     * @param boolean $magical
     * @return boolean
     */
    public function to(Unit $target, $magical)
    {
        $this->reset();
        
        if ($target->get_combat_behavior() instanceof NonAttackableBehavior) {
            return false;
        }
        
        $armor = $target->get_combat_behavior()->get_armor();
        
        $this->magical = $magical;
        $this->miss = mt_rand(1, 100) <= $armor->get_miss_chance($this);
        
        if (! $this->miss) {
            if (mt_rand(1, 100) <= $armor->get_block_chance($this)) {
                $this->blocked = $armor->get_block_amount($this);
            }
            
            $criticalChance = $this->get_critical_chance($target);
            $this->critical = mt_rand(1, 100) <= $criticalChance;

            $this->amount = (int) max(
                0,
                $this->get_damage() - $armor->get_defense($this) - $this->blocked
            );

            if ($this->critical) {
                $this->amount *= $this->get_critical_multiplier($target);
            }

            $life = $target->get_current_life() - $this->amount;
            $target->set_current_life($life);
        }
        
        return true;
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