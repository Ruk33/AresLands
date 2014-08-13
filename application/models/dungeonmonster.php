<?php

class DungeonMonster extends Monster
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'dungeon_monsters';
    
    const BOSS_NESSY = 1515;
    const BOSS_LOHG  = 1516;
    const BOSS_SETH  = 1517;
    const BOSS_ONIX  = 1518;
    
    public function get_combat_behavior() {
        if (! $this->combatBehavior) {
            $damage = null;
            $armor  = null;
            $combat = null;
            
            switch ($this->id) {
                case self::BOSS_NESSY:
                    $damage = new MonsterDamage($this);
                    $armor  = new MonsterArmor($this);
                    $combat = new AttackableBehavior($this, $damage, $armor);
                    break;
                
                case self::BOSS_NESSY:
                    $damage = new MonsterDamage($this);
                    $armor  = new MonsterArmor($this);
                    $combat = new AttackableBehavior($this, $damage, $armor);
                    break;
                
                case self::BOSS_SETH:
                    $damage = new MonsterDamage($this);
                    $armor  = new MonsterArmor($this);
                    $combat = new AttackableBehavior($this, $damage, $armor);
                    break;
                
                case self::BOSS_ONIX:
                    $damage = new MonsterDamage($this);
                    $armor  = new MonsterArmor($this);
                    $combat = new AttackableBehavior($this, $damage, $armor);
                    break;

                default:
                    $combat = parent::get_combat_behavior();
            }
            
            $this->set_combat_behavior($combat);
        }
        
        return $this->combatBehavior;
    }
    
	/**
	 * Query para obtener mounstruo
	 * @return Eloquent
	 */
	public function monster()
	{
		return $this->belongs_to("Monster", "monster_id");
	}
}