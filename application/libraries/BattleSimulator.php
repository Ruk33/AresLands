<?php

/**
 * Esta clase tiene como tarea simular multiples veces todo tipo de batallas
 * (pvp, pve, dungeon, etc.)
 * 
 * @todo El tipo de batalla Dungeon requiere DungeonLevel, el cual requiere Dungeon
 */
class BattleSimulator
{
    const PVP_BATTLE = 1;
    const PVE_BATTLE = 2;
    const DUNGEON_BATTLE = 3;
    
    const UNIT_TYPE_DWARF = 'dwarf';
    const UNIT_TYPE_HUMAN = 'human';
    const UNIT_TYPE_DROW = 'drow';
    const UNIT_TYPE_ELF = 'elf';
    const UNIT_TYPE_MONSTER = 5;
    
    const AMOUNT_INPUT = "amount";
    const BATTLE_TYPE_INPUT = "battle_type";
    const ATTACKER_INPUT = "attacker";
    const TARGET_INPUT = "target";
    
    const UNIT_NAME_INPUT = "name";
    const UNIT_TYPE_INPUT = "unit_type";
    const LIFE_INPUT = "life";
    const LEVEL_INPUT = "level";
    const STRENGTH_INPUT = "stat_strength";
    const DEXTERITY_INPUT = "stat_dexterity";
    const RESISTANCE_INPUT = "stat_resistance";
    const MAGIC_INPUT = "stat_magic";
    const MAGIC_SKILL_INPUT = "stat_magic_skill";
    const MAGIC_RESISTANCE_INPUT = "stat_magic_resistance";
    
    /**
     *
     * @var integer
     */
    protected $battleType;
    
    /**
     *
     * @var BattleSimulatorReport
     */
    protected $battleSimulatorReport;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->battleSimulatorReport = new BattleSimulatorReport();
    }
    
    /**
     * 
     * @param integer $battleType
     * @return Battle
     * @throws Exception
     */
    protected function getBattleInstance($battleType)
    {
        $attacker = $this->battleSimulatorReport->getAttacker();
        $target = $this->battleSimulatorReport->getTarget();
        
        switch ($battleType) {
            case self::PVP_BATTLE:
                return new PvpBattle($attacker, $target);
                
            case self::PVE_BATTLE:
                return new PveBattle($attacker, $target);
                
            case self::DUNGEON_BATTLE:
                $dungeonLevel = new DungeonLevel;
                return new DungeonBattle($attacker, $target, $dungeonLevel);
                
            default:
                throw new Exception("El tipo de batalla {$battleType} no es soportado");
        }
    }
    
    /**
     * 
     * @param integer $unitType
     * @return Unit
     * @throws Exception
     */
    protected function getUnitInstance($unitType)
    {
        switch ($unitType) {
            case self::UNIT_TYPE_DROW:
            case self::UNIT_TYPE_DWARF:
            case self::UNIT_TYPE_HUMAN:
            case self::UNIT_TYPE_ELF:
                return new Character;
                
            case self::UNIT_TYPE_MONSTER:
                return new Monster;
                
            default:
                throw new Exception("Unidad {$unitType} no soportada");
        }
    }
    
    /**
     * 
     * @return BattleSimulatorReport
     */
    public function getBattleSimulatorReport()
    {
        return $this->battleSimulatorReport;
    }
    
    /**
     * 
     */
    public function startSimulations()
    {
        $attacker = $this->battleSimulatorReport->getAttacker();
        $target = $this->battleSimulatorReport->getTarget();
        $amount = $this->battleSimulatorReport->getSimulationAmount();
        
        for ($i = 0; $i < $amount; $i++) {
            $battle = $this->getBattleInstance($this->battleType);
            $this->battleSimulatorReport->registerBattle($battle);
            
            $attacker->heal($battle->getAttackerReport()->getInitialLife());
            $target->heal($battle->getTargetReport()->getInitialLife());
        }
    }
    
    /**
     * 
     * @param Unit $attacker
     * @return BattleSimulator
     */
    public function registerAttacker(Unit $attacker)
    {
        $this->battleSimulatorReport->registerAttacker($attacker);
        return $this;
    }
    
    /**
     * 
     * @param Unit $target
     * @return BattleSimulator
     */
    public function registerTarget(Unit $target)
    {
        $this->battleSimulatorReport->registerTarget($target);
        return $this;
    }
    
    /**
     * 
     * @param array $input
     * @param boolean $isAttacker
     */
    protected function loadUnitFromInput(array $input, $isAttacker)
    {
        $unit = $this->getUnitInstance($input[self::UNIT_TYPE_INPUT]);
        
        $unit->name = $input[self::UNIT_NAME_INPUT];
        $unit->level = $input[self::LEVEL_INPUT];
        $unit->stat_strength = $input[self::STRENGTH_INPUT];
        $unit->stat_dexterity = $input[self::DEXTERITY_INPUT];
        $unit->stat_resistance = $input[self::RESISTANCE_INPUT];
        $unit->stat_magic = $input[self::MAGIC_INPUT];
        $unit->stat_magic_skill = $input[self::MAGIC_SKILL_INPUT];
        $unit->stat_magic_resistance = $input[self::MAGIC_RESISTANCE_INPUT];
        
        if ($unit instanceof Monster) {
            $unit->life = $input[self::LIFE_INPUT];
        } else {
            $unit->race = $input[self::UNIT_TYPE_INPUT];
            $unit->max_life = $input[self::LIFE_INPUT];
            $unit->current_life = $unit->max_life;
        }
        
        if ($isAttacker) {
            $this->registerAttacker($unit);
        } else {
            $this->registerTarget($unit);
        }
    }
    
    /**
     * 
     * @param array $input
     */
    public function loadDataFromInput(array $input)
    {
        $this->loadUnitFromInput($input[self::ATTACKER_INPUT], true);
        $this->loadUnitFromInput($input[self::TARGET_INPUT], false);
        
        $amount = $input[self::AMOUNT_INPUT];
        $this->battleSimulatorReport->registerSimulationAmount($amount);
        
        $this->battleType = $input[self::BATTLE_TYPE_INPUT];
    }
    
    /**
     * 
     * @return string
     */
    public function getAmountInput()
    {
        $form = '';
        
        $form .= \Laravel\Form::label(self::AMOUNT_INPUT, "Cantidad de simulaciones");
        $form .= \Laravel\Form::number(self::AMOUNT_INPUT, 1, array("class" => "input-block-level"));
        
        return $form;
    }
    
    /**
     * 
     * @return string
     */
    public function getBattleTypeInput()
    {
        $battles = array(
            self::PVP_BATTLE => "Jugador contra jugador (PvP)",
            self::PVE_BATTLE => "Jugador contra entorno (PvE) (monstruos)",
            self::DUNGEON_BATTLE => "Jugador contra mazmorra (PvE)",
        );
        
        $form = '';
        
        $form .= \Laravel\Form::label(self::BATTLE_TYPE_INPUT, "Tipo de batalla");
        $form .= \Laravel\Form::select(self::BATTLE_TYPE_INPUT, $battles, null, array("class" => "input-block-level"));
        
        return $form;
    }
    
    /**
     * 
     * @param string $unitType Posibles valores: ATTACKER_INPUT|TARGET_INPUT
     * @return string
     */
    public function getUnitInputs($unitType)
    {
        $unitTypes = array(
            self::UNIT_TYPE_DWARF => "Enano",
            self::UNIT_TYPE_DROW => "Drow",
            self::UNIT_TYPE_HUMAN => "Humano",
            self::UNIT_TYPE_ELF => "Elfo",
            self::UNIT_TYPE_MONSTER => "Monstruo",
        );
        
        $form = '';
        
        $form .= \Laravel\Form::label("{$unitType}[".self::UNIT_NAME_INPUT."]", "Nombre (opcional)");
        $form .= \Laravel\Form::text("{$unitType}[".self::UNIT_NAME_INPUT."]", "", array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::UNIT_TYPE_INPUT."]", "Tipo de unidad");
        $form .= \Laravel\Form::select("{$unitType}[".self::UNIT_TYPE_INPUT."]", $unitTypes, null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::LEVEL_INPUT."]", "Nivel");
        $form .= \Laravel\Form::number("{$unitType}[".self::LEVEL_INPUT."]", null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::LIFE_INPUT."]", "Vida");
        $form .= \Laravel\Form::number("{$unitType}[".self::LIFE_INPUT."]", null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::STRENGTH_INPUT."]", "Fuerza");
        $form .= \Laravel\Form::number("{$unitType}[".self::STRENGTH_INPUT."]", null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::DEXTERITY_INPUT."]", "Destreza");
        $form .= \Laravel\Form::number("{$unitType}[".self::DEXTERITY_INPUT."]", null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::RESISTANCE_INPUT."]", "Resistencia fisica");
        $form .= \Laravel\Form::number("{$unitType}[".self::RESISTANCE_INPUT."]", null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::MAGIC_INPUT."]", "Magia");
        $form .= \Laravel\Form::number("{$unitType}[".self::MAGIC_INPUT."]", null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::MAGIC_SKILL_INPUT."]", "Habilidad magica");
        $form .= \Laravel\Form::number("{$unitType}[".self::MAGIC_SKILL_INPUT."]", null, array("class" => "input-block-level"));
        
        $form .= \Laravel\Form::label("{$unitType}[".self::MAGIC_RESISTANCE_INPUT."]", "Contraconjuros");
        $form .= \Laravel\Form::number("{$unitType}[".self::MAGIC_RESISTANCE_INPUT."]", null, array("class" => "input-block-level"));
        
        return $form;
    }
    
    /**
     * 
     * @return string
     */
    public function getSubmitInput()
    {
        return \Laravel\Form::submit("Comenzar simulaciones", array("class" => "btn btn-primary"));
    }
}
