<?php

class Monster extends Attackable
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npcs';
	public static $key = 'id';

	public $current_life = null;

	/**
	 * Query para obtener bichos disponibles para personaje
	 * @param  Character $character 
	 * @return Eloquent
	 * @deprecated
	 */
	public static function get_available_for(Character $character)
	{
		return static::where('zone_id', '=', $character->zone_id)
					 ->where('type', '=', 'monster');
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
		$this->current_life = $value;
	}

	public function check_skills_time()
	{

	}

	public function drops()
	{
		return $this->has_many("MonsterDrop", "monster_id");
	}

	/**
	 * @param  integer $dungeonLevel Nivel de dungeon
	 * @return array
	 */
	public function get_rewards($dungeonLevel = 1)
	{
		$rewards = array();

		foreach ( $this->drops as $drop )
		{
			if ( mt_rand(0, 100) <= $drop->chance * $dungeonLevel )
			{
				$drops[] = array('item_id' => $drop->item_id, 'amount' => $drop->amount);
			}
		}

		// compatibilidad con el viejo sistema
		$rewards[] = array('item_id' => Config::get('game.xp_item_id'), 'amount' => $this->xp);
		$rewards[] = array('item_id' => Config::get('game.coin_id'), 'amount' => $this->level * 50);

		return $rewards;
	}

	public function get_evasion_chance()
	{
		return mt_rand(0, 10);
	}

	public function get_critical_chance()
	{
		return mt_rand(0, 20);
	}

	public function save()
	{
		unset($this->current_life);
		return parent::save();
	}

	public function get_text_for_tooltip()
	{
		$message = "<div style='min-width: 250px; text-align: left;'>";

		$message .= "<img src='" . URL::base() . "/img/icons/npcs/$this->id.png' class='pull-left' width='32px' height='32px' style='margin-right: 10px;'>";

		$message .= "<strong style='color: orange; margin-top: 10px;'>$this->name</strong>";
		$message .= "<br>Nivel: $this->level";
		$message .= "<p><small><em>$this->dialog</em></small></p>";

		$message .= "<ul class='unstyled'>";

		if ( $this->stat_strength != 0 )
		{
			$message .= "<li>Fuerza física: $this->stat_strength</li>";
		}

		if ( $this->stat_dexterity != 0 )
		{
			$message .= "<li>Destreza física: $this->stat_dexterity</li>";
		}

		if ( $this->stat_resistance != 0 )
		{
			$message .= "<li>Resistencia: $this->stat_resistance</li>";
		}

		if ( $this->stat_magic != 0 )
		{
			$message .= "<li>Poder mágico: $this->stat_magic</li>";
		}

		if ( $this->stat_magic_skill != 0 )
		{
			$message .= "<li>Habilidad mágica: $this->stat_magic_skill</li>";
		}

		if ( $this->stat_magic_resistance != 0 )
		{
			$message .= "<li>Contraconjuro: $this->stat_magic_resistance</li>";
		}

		$message .= '</ul>';

		$message .= '</div>';

		return $message;
	}
}