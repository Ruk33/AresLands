<?php

/**
 * StatBag tiene como tarea calcular la cantidad de atributos reales de las
 * unidades.
 * 
 * Con esta clase, es posible obtener tanto los atributos finales (sumando 
 * objetos, habilidades, porcentajes, etc.) como los extras (agregados por 
 * objetos y otros modificadores).
 * 
 * Ejemplo:
 * 
 * $statBag = new StatBag(unit);
 * 
 * // Cargamos los atributos
 * $statBag->updateStats();
 * 
 * // Obtenemos la fuerza final
 * $statBag->getStrength();
 * 
 * // Obtenemos la fuerza extra
 * $statBag->getExtraStrength();
 */
class StatBag
{
    /**
     *
     * @var Unit
     */
    protected $unit;
    
    /**
     *
     * @var float
     */
    protected $strength = 0.00;
    
    /**
     *
     * @var float
     */
    protected $extraStrength = 0.00;
    
    /**
     *
     * @var float
     */
    protected $dexterity = 0.00;
    
    /**
     *
     * @var float
     */
    protected $extraDexterity = 0.00;
    
    /**
     *
     * @var float
     */
    protected $resistance = 0.00;
    
    /**
     *
     * @var float
     */
    protected $extraResistance = 0.00;
    
    /**
     *
     * @var float
     */
    protected $magic = 0.00;
    
    /**
     *
     * @var float
     */
    protected $extraMagic = 0.00;
    
    /**
     *
     * @var float
     */
    protected $magicSkill = 0.00;
    
    /**
     *
     * @var float
     */
    protected $extraMagicSkill = 0.00;
    
    /**
     *
     * @var float
     */
    protected $magicResistance = 0.00;
    
    /**
     *
     * @var float
     */
    protected $extraMagicResistance = 0.00;
    
    /**
     * 
     * @return float
     */
    public function getStrength()
    {
        return $this->strength;
    }
    
    /**
     * 
     * @return float
     */
    public function getExtraStrength()
    {
        return $this->extraStrength;
    }
    
    /**
     * 
     * @return float
     */
    public function getDexterity()
    {
        return $this->dexterity;
    }
    
    /**
     * 
     * @return float
     */
    public function getExtraDexterity()
    {
        return $this->extraDexterity;
    }
    
    /**
     * 
     * @return float
     */
    public function getResistance()
    {
        return $this->resistance;
    }
    
    /**
     * 
     * @return float
     */
    public function getExtraResistance()
    {
        return $this->extraResistance;
    }
    
    /**
     * 
     * @return float
     */
    public function getMagic()
    {
        return $this->magic;
    }
    
    /**
     * 
     * @return float
     */
    public function getExtraMagic()
    {
        return $this->extraMagic;
    }
    
    /**
     * 
     * @return float
     */
    public function getMagicSkill()
    {
        return $this->magicSkill;
    }
    
    /**
     * 
     * @return float
     */
    public function getExtraMagicSkill()
    {
        return $this->extraMagicSkill;
    }
    
    /**
     * 
     * @return float
     */
    public function getMagicResistance()
    {
        return $this->magicResistance;
    }
    
    /**
     * 
     * @return float
     */
    public function getExtraMagicResistance()
    {
        return $this->extraMagicResistance;
    }
    
    /**
     * 
     */
    public function updateStats()
    {
        $this->strength = $this->unit->stat_strength;
        $this->dexterity = $this->unit->stat_dexterity;
        $this->resistance = $this->unit->stat_resistance;
        $this->magic = $this->unit->stat_magic;
        $this->magicSkill = $this->unit->stat_magic_skill;
        $this->magicResistance = $this->unit->stat_magic_resistance;
        
        $items = array();
        
        $items[] = $this->unit->get_weapon();
        $items[] = $this->unit->get_shield();
        
        if ($this->unit instanceof Character) {
            $items[] = $this->unit->get_mercenary();
            $items[] = $this->unit->get_second_mercenary();
        }
        
        foreach ($items as $item) {
            if ($item) {
                $this->extraStrength += $item->stat_strength;
                $this->extraDexterity += $item->stat_dexterity;
                $this->extraResistance += $item->stat_resistance;
                $this->extraMagic += $item->stat_magic;
                $this->extraMagicSkill += $item->stat_magic_skill;
                $this->extraMagicResistance += $item->stat_magic_resistance;
            }
        }
        
        $strengthPercentage = 1;
        $dexterityPercentage = 1;
        $resistancePercentage = 1;
        $magicPercentage = 1;
        $magicSkillPercentage = 1;
        $magicResistancePercentage = 1;
        
        foreach ($this->unit->get_buffs() as $buff)
        {
            $skill = $buff['skill'];
            $amount = $buff['amount'];
            
            if ($skill->percent) {
                $strengthPercentage += $skill->stat_strength * $amount / 100;
                $dexterityPercentage += $skill->stat_dexterity * $amount / 100;
                $resistancePercentage += $skill->stat_resistance * $amount / 100;
                $magicPercentage += $skill->stat_magic * $amount / 100;
                $magicSkillPercentage += $skill->stat_magic_skill * $amount / 100;
                $magicResistancePercentage += $skill->stat_magic_resistance * $amount / 100;
            } else {
                $this->extraStrength += $skill->stat_strength * $amount;
                $this->extraDexterity += $skill->stat_dexterity * $amount;
                $this->extraResistance += $skill->stat_resistance * $amount;
                $this->extraMagic += $skill->stat_magic * $amount;
                $this->extraMagicSkill += $skill->stat_magic_skill * $amount;
                $this->extraMagicResistance += $skill->stat_magic_resistance * $amount;
            }
        }
        
        $this->strength += $this->extraStrength;
        $this->dexterity += $this->extraDexterity;
        $this->resistance += $this->extraResistance;
        $this->magic += $this->extraMagic;
        $this->magicSkill += $this->extraMagicSkill;
        $this->magicResistance += $this->extraMagicResistance;
        
        $this->strength *= $strengthPercentage;
        $this->dexterity *= $dexterityPercentage;
        $this->resistance *= $resistancePercentage;
        $this->magic *= $magicPercentage;
        $this->magicSkill *= $magicSkillPercentage;
        $this->magicResistance *= $magicResistancePercentage;
    }
    
    /**
     * 
     * @param Unit $unit
     * @param boolean $load ¿Cargamos los atributos?
     */
    public function __construct(Unit $unit, $load = false)
    {
        $this->unit = $unit;
        
        if ($load) {
            $this->updateStats();
        }
    }
}