<?php

class Monster extends Npc
{
	public $current_life = null;
    
    public function get_combat_behavior()
    {
        if (! $this->combatBehavior) {
            $this->set_combat_behavior(
                new AttackableBehavior(
                    $this, 
                    new MonsterDamage($this), 
                    new MonsterArmor($this)
                )
            );
        }
        
        return $this->combatBehavior;
    }

    protected function inject_query($query)
    {
        return $query->where('type', '=', 'monster');
    }
    
	/**
	 * Query para obtener bichos disponibles para personaje
	 * @param  Character $character 
	 * @return Eloquent
	 * @deprecated
	 */
	public static function get_available_for(Character $character)
	{
		return static::where('zone_id', '=', $character->zone_id);
	}

	/**
	 * Obtenemos clase (css/estilo) dependiendo de la diferencia
	 * de nivel entre el personaje y el bicho
	 * @param  Character $character 
	 * @return string
	 */
	public function get_color_class(Character $character)
	{
		$monsterCharacterDifference = $this->level - $character->level;

		if ( $monsterCharacterDifference >= 6 )
			return 'level-very-high';
		elseif ( $monsterCharacterDifference >= 4 )
			return 'level-high';
		elseif ( $monsterCharacterDifference >= 2 )
			return 'level-normal';
		elseif ( $monsterCharacterDifference >= -2 )
			return 'level-low';
		else
			return 'level-very-low';
	}

	public function get_attack($magical = false)
	{
		return ( $magical ) ? $this->stat_magic : $this->_stat_strength;
	}

	public function get_resistance($magical = false)
	{
		return ( $magical ) ? $this->stat_magic_resistance : $this->stat_resistance;
	}

	public function get_reflected_damage($magical = false)
	{
		return 0;
	}

	public function get_cd()
	{
		if ( $this->attacks_with_magic() )
		{
			return 800 / ($this->stat_magic_skill + 1);
		}
		else
		{
			return 1000 / ($this->stat_dexterity + 1);
		}
	}

	public function get_current_life()
	{
		if ( is_null($this->current_life) )
		{
			$this->current_life = $this->life;
		}

		return $this->current_life;
	}

	public function set_current_life($value)
	{
		$this->current_life = min($value, $this->life);
	}

	public function check_skills_time()
	{

	}

    /**
     * 
     * @param integer $dungeon Nivel del dungeon
     * @return array
     */
	public function drops($dungeon = 1)
	{
        $list = $this->has_many("MonsterDrop", "monster_id")->get();
        $drops = array();
        
		foreach ($list as $drop) {
            if (mt_rand(0, 100) <= $drop->chance * $dungeon) {
				$drops[] = $drop->to_array();
			}
        }
        
        // Compatibilidad con lo viejo
        $drops[] = array(
            'item_id' => Config::get('game.xp_item_id'), 
            'amount' => $this->xp
        );
        
		$drops[] = array(
            'item_id' => Config::get('game.coin_id'), 
            'amount' => $this->level * 50
        );
        
        return $drops;
	}

	/**
	 * @param  integer $dungeon Nivel de dungeon
     * @deprecated
	 * @return array
	 */
	public function get_rewards($dungeon = 1)
	{
		return $this->drops($dungeon);
	}

	public function save()
	{
		unset($this->current_life);
		return parent::save();
	}

    /**
     * @deprecated
     */
	public function get_text_for_tooltip()
	{
		return $this->get_tooltip();
	}
}