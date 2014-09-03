<?php

class Monster extends Npc
{
	public $current_life = null;
    
    public function get_combat_behavior()
    {
        if (! $this->combatBehavior) {
            $factory = new MonsterCombatBehaviorFactory;
            $this->set_combat_behavior($factory->get($this));
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
		$this->current_life = min(max(0, $value), $this->life);
	}

	public function check_skills_time()
	{

	}

	public function drops()
	{
        return $this->has_many("MonsterDrop", "monster_id");
	}
    
    public function drops_for(Character $character) {
        $drops = parent::drops_for($character);
        
        foreach ($this->drops()->get() as $drop) {
            if (mt_rand(0, 100) <= $drop->chance * $character->get_drop_rate()) {
				$drops[] = $drop->to_array();
			}
        }
        
        $xp = $this->xp + (0.13 * ($this->level + 2 - $character->level));
        
        $drops[] = array(
            'item_id' => Config::get('game.xp_item_id'), 
            'amount' => $xp * $character->get_xp_rate()
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