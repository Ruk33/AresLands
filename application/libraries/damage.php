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
     * @var real
     */
    protected $blocked;
    
    /**
     *
     * @var real
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
        $this->blocked = false;
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
     * @return real
     */
    public function get_blocked()
    {
        return $this->blocked;
    }
    
    /**
     * 
     * @return real
     */
    public function get_amount()
    {
        return $this->amount;
    }
    
    /**
     * 
     * @return real
     */
    protected function get_damage()
    {
        return 0.00;
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
        
        if ( $target->get_combat_behavior() instanceof NonAttackableBehavior )
        {
            return false;
        }
        
        $this->magical = $magical;
        $this->critical = mt_rand(1, 2) == 1;
        $this->miss = mt_rand(1, 2) == 1;
        
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