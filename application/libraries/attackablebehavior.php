<?php

class AttackableBehavior implements CombatBehavior
{
    /**
     *
     * @var Unit
     */
    protected $unit;
    
    /**
     *
     * @var Damage
     */
    protected $damage;
    
    /**
     *
     * @var Armor
     */
    protected $armor;
    
    /**
     * Cache
     * @var real
     */
    protected $finalStrength;
    
    /**
     * Cache
     * @var real
     */
    protected $finalDexterity;
    
    /**
     * Cache
     * @var real
     */
    protected $finalResistance;
    
    /**
     * Cache
     * @var real
     */
    protected $finalMagic;
    
    /**
     * Cache
     * @var real
     */
    protected $finalMagicSkill;
    
    /**
     * Cache
     * @var real
     */
    protected $finalMagicResistance;
    
    /**
     * Cache
     * Usamos -1 para detectar si todavia no hemos traido un escudo
     * @var Item
     */
    protected $shield = -1;
    
    /**
     * Cache
     * Usamos -1 para detectar si todavia no hemos traido un escudo
     * @var Item
     */
    protected $weapon = -1;
    
    /**
     * 
     * @param Unit $unit
     * @param Damage $damage
     * @param Armor $armor
     */
    public function __construct(Unit $unit, Damage $damage, Armor $armor)
    {
        $this->unit = $unit;
        $this->damage = $damage;
        $this->armor = $armor;
    }
    
    /**
     * 
     * @return Unit
     */
    public function get_unit()
    {
        return $this->unit;
    }
    
    /**
     * 
     * @return Damage
     */
    public function get_damage()
    {
        return $this->damage;
    }
    
    /**
     * 
     * @return Armor
     */
    public function get_armor()
    {
        return $this->armor;
    }
    
    /**
     * 
     * @return real
     */
    public function get_final_strength()
    {
        if ( ! $this->finalStrength )
        {
            $this->finalStrength = $this->unit->get_final_strength();
        }
        
        return $this->finalStrength;
    }
    
    /**
     * 
     * @return real
     */
    public function get_final_dexterity()
    {
        if ( ! $this->finalDexterity )
        {
            $this->finalDexterity = $this->unit->get_final_dexterity();
        }
        
        return $this->finalDexterity;
    }
    
    /**
     * 
     * @return real
     */
    public function get_final_resistance()
    {
        if ( ! $this->finalResistance )
        {
            $this->finalResistance = $this->unit->get_final_resistance();
        }
        
        return $this->finalResistance;
    }
    
    /**
     * 
     * @return real
     */
    public function get_final_magic()
    {
        if ( ! $this->finalMagic )
        {
            $this->finalMagic = $this->unit->get_final_magic();
        }
        
        return $this->finalMagic;
    }
    
    /**
     * 
     * @return real
     */
    public function get_final_magic_skill()
    {
        if ( ! $this->finalMagicSkill )
        {
            $this->finalMagicSkill = $this->unit->get_final_magic_skill();
        }
        
        return $this->finalMagicSkill;
    }
    
    /**
     * 
     * @return real
     */
    public function get_final_magic_resistance()
    {
        if ( ! $this->finalMagicResistance )
        {
            $this->finalMagicResistance = $this->unit->get_final_magic_resistance();
        }
        
        return $this->finalMagicResistance;
    }
    
    /**
     * 
     * @return Item
     */
    public function get_shield()
    {
        if ( $this->shield === -1 )
        {
            $this->shield = $this->unit->get_shield();
        }
        
        return $this->shield;
    }
    
    /**
     * 
     * @return Item
     */
    public function get_weapon()
    {
        if ( $this->weapon === -1 )
        {
            $this->weapon = $this->unit->get_weapon();
        }
        
        return $this->weapon;
    }
    
    /**
     * @deprecated
     * @return float
     */
    public function get_attack_speed()
    {
        return 800 / ($this->get_final_dexterity() + $this->get_final_magic_skill() + 1);
    }
}