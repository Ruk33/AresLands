<?php

class Character extends Base_Model
{
	public static $softDelete = true;
	public static $timestamps = false;
	public static $table = 'characters';
	public static $key = 'id';

	protected $rules = array(
		'name' => 'required|unique:characters|between:3,10|alpha_num',
		'race' => array('required', 'match:/^(dwarf|human|drow|elf)$/'),
		'gender' => array('required', 'match:/^(male|female)$/'),
	);

	protected $messages = array(
		'name_required' => 'El nombre del personaje es requerido',
		'name_unique' => 'Ya existe otro personaje con ese nombre',
		'name_between' => 'El nombre del personaje debe tener entre 3 y 10 carácteres',
		'name_alpha_num' => 'El nombre solamente debe contener letras y números',

		'race_required' => 'La raza es requerida',
		'race_match' => 'La raza es incorrecta',

		'gender_required' => 'El género es requerido',
		'gender_match' => 'El género es incorrecto',
	);

	/**
	 * @return Eloquent
	 */
	public static function get_characters_for_xp_ranking()
	{
		return static::with('clan')
					 ->order_by('level', 'desc')
					 ->order_by('xp', 'desc');
	}

	/**
	 * @return Eloquent
	 */
	public static function get_characters_for_pvp_ranking()
	{
		return static::with('clan')
					 ->order_by('pvp_points', 'desc');
	}

	/**
	 * @return array
	 */
	public function get_rules()
	{
		return $this->rules;
	}

	/**
	 * Obtenemos los mensajes de error
	 * @return array
	 */
	public function get_messages()
	{
		return $this->messages;
	}

	/**
	 * Obtenemos un validador especifico.
	 * Ejemplo: $character->get_specific_rule('name');
	 *
	 * @param $rule
	 * @return array
	 */
	public function get_specific_rule($rule)
	{
		if ( ! isset($this->rules[$rule]) )
		{
			return array();
		}

		return array(
			$rule => $this->rules[$rule]
		);
	}

	/**
	 * Obtenemos los mensajes de error de un validador (regla) especifico.
	 *
	 * @param $rule
	 * @return array
	 */
	public function get_messages_from_specific_rule($rule)
	{
		$ruleLen = strlen($rule);
		$messages = array();

		foreach ( $this->messages as $key => $value )
		{
			if ( substr($key, 0, $ruleLen) == $rule )
			{
				$messages[$key] = $value;
			}
		}

		return $messages;
	}
	
	/**
	 * Lanzamos una habilidad de trampa aleatoria a personaje
	 * @param Character $target
	 * @param boolean $removeTrapSkill ¿Remover la skill de trampa (del caster) en caso de haberla?
	 * @return boolean
	 */
	public function cast_random_trap_to(Character $target, $removeTrapSkill = false)
	{
		if ( $this->clan_id > 0 && $this->clan_id == $target->clan_id )
		{
			return false;
		}
		
		$trapSkillsIds = Config::get('game.trap_skills');
		$trapSkill = Skill::find($trapSkillsIds[mt_rand(0, count($trapSkillsIds) - 1)]);
		
		if ( $trapSkill )
		{
			$trapSkill->cast($this, $target);
		}
		
		if ( $removeTrapSkill )
		{
			$trapSkill = $this->skills()->where('skill_id', '=', Config::get('game.trap_skill'))->first();

			if ( $trapSkill )
			{
				$this->remove_buff($trapSkill);
			}
		}
		
		return true;
	}
	
	/**
	 * Recargamos talento de personaje
	 * @param CharacterTalent $talent
	 */
	public function refresh_talent(CharacterTalent $talent)
	{
		$talent->usable_at = 0;
		$talent->save();
	}
	
	/**
	 * Obtenemos query para talento aleatorio de personaje
	 * @return Eloquent
	 */
	public function get_random_talent()
	{
		return $this->talents()->order_by(DB::raw('RAND()'));
	}
	
	/**
	 * Verificamos si personaje es invisible
	 * @return boolean
	 */
	public function is_invisible()
	{
		return $this->invisible_until > time();
	}
	
	/**
	 * Sacamos invisibilidad a personaje
	 */
	public function remove_invisibility()
	{
		if ( $this->is_invisible() )
		{
			$invisibilitySkill = $this->skills()
									  ->where('skill_id', '=', Config::get('game.invisibility_skill'))
									  ->first();

			if ( $invisibilitySkill )
			{
				$this->remove_buff($invisibilitySkill);
			}
		}
	}
	
	/**
	 * Verificamos si personaje puede sacarle la invisibilidad a otro
	 * @param Character $target
	 * @return boolean
	 */
	public function can_remove_invisibility_of(Character $target)
	{
		if ( ! $target->is_invisible() )
		{
			return false;
		}
		
		if ( ! $this->has_skill(Config::get('game.reveal_invisibility_skill')) )
		{
			return false;
		}
		
		return true;
	}
	
	public function set_invisible_until($value)
	{
		if ( ! $this->invisible_until )
		{
			$value += time();
		}
		
		return parent::set_invisible_until($value);
	}
	
	/**
	 * Obtenemos posible(s) contrincante(s) para un personaje
	 * @param array $races Razas posibles
	 * @return Eloquent
	 */
	public function get_opponent($races = array('dwarf', 'elf', 'drow', 'human'))
	{
		$eloquent = self::where('zone_id', '=', $this->zone_id)
						->where_in('race', $races)
						->where('is_traveling', '=', false)
						->where('name', '<>', $this->name)
						->where('registered_in_tournament', '=', $this->registered_in_tournament);
		
		// Si estamos en torneo, evitamos que toquen de nuestro clan
		if ( Tournament::is_active() && $this->registered_in_tournament )
		{
			$eloquent = $eloquent->where('clan_id', '<>', $this->clan_id);
		}
		
		// Si no tiene para revelar, entonces no puede ver invisibles
		if ( ! $this->has_skill(Config::get('game.reveal_invisibility_skill')) )
		{
			$eloquent = $eloquent->where('invisible_until', '<', time());
		}
		
		return $eloquent;
	}
	
	public function get_name()
	{
		$character = Character::get_character_of_logged_user(array('id'));
		
		if ( $character->id != $this->id )
		{
			$characterHasConfusion = $character->has_skill(Config::get('game.confusion_skill'));

			if ( $characterHasConfusion )
			{
				return '????';
			}
		}
		
		return parent::get_name();
	}
	
	/**
	 * 
	 * @return string
	 */
	public function get_race()
	{
		$character = Character::get_character_of_logged_user(array('id'));
		
		if ( $character->id != $this->id )
		{
			if ( $this->has_characteristic(Characteristic::SHY) )
			{
				switch ( mt_rand(1, 4) )
				{
					case 1:
						return 'dwarf';
						
					case 2:
						return 'elf';
						
					case 3:
						return 'drow';
						
					case 4:
						return 'human';
				}
			}
		}
		
		return parent::get_race();
	}
	
	/**
	 * Obtenemos debuffs (ids de CharacterSkill) de personaje
	 * @return array
	 */
	public function get_debuffs()
	{
		$debuffs = array();
		$characterSkills = $this->skills()->select(array('id', 'skill_id', 'level', 'character_id'))->get();
		
		foreach ( $characterSkills as $characterSkill )
		{
			$skill = $characterSkill->skill()->select(array('id', 'level', 'type'))->first();
			
			if ( $skill->type == 'debuff' )
			{
				$debuffs[] = $characterSkill->id;
			}
		}
		
		return $debuffs;
	}
	
	/**
	 * Removemos una cantidad de debuffs a personaje
	 * @param integer $amount
	 */
	public function remove_debuffs($amount)
	{
		$debuffs = $this->get_debuffs();
			
		if ( count($debuffs) > 0 )
		{
			for ( $i = 0; $i < $amount; $i++ )
			{
				if ( isset($debuffs[$i]) )
				{
					$debuff = CharacterSkill::find($debuffs[$i]);

					if ( $debuff )
					{
						$this->remove_buff($debuff);
					}
				}
			}
		}
	}
	
	/**
	 * Verificamos si personaje puede usar talento
	 * @param CharacterTalent $talent
	 * @return boolean
	 */
	public function can_use_talent(CharacterTalent $talent)
	{
		return $talent->usable_at < time();
	}
	
	/**
	 * Usamos talento de personaje
	 * @param CharacterTalent $talent
	 * @param Character $target
	 * @return boolean
	 */
	public function use_talent(CharacterTalent $talent, Character $target)
	{
		$skill = $talent->skill;
		
		if ( $skill->cast($this, $target) )
		{
			$talent->usable_at = time() + $skill->cd - ($this->skill_cd_time + $this->skill_cd_time_extra);
			$talent->save();
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Obtenemos todos los talentos (skill ids) que 
	 * pueden ser lanzados a un objetivo
	 * @param Character $target
	 * @return array
	 */
	public function get_castable_talents(Character $target)
	{
		$castableSkills = array();
		$talents = $this->talents;
		
		foreach ( $talents as $talent )
		{
			if ( $this->can_use_talent($talent) )
			{
				$skill = Skill::find($talent->skill_id);
			
				if ( $skill && $skill->can_be_casted($this, $target) )
				{
					$castableSkills[] = $skill->id;
				}
			}
		}
		
		return $castableSkills;
	}
	
	/**
	 * Verificamos si un personaje puede seguir aprendiendo talentos
	 * @return boolean
	 */
	public function can_learn_more_talents()
	{
		return $this->talents()->count() < Config::get('game.max_talents');
	}
	
	/**
	 * Verificamos si personaje puede aprender talento
	 * @param Skill $skill
	 * @return boolean
	 */
	public function can_learn_talent(Skill $skill)
	{
		if ( $this->talent_points <= 0 )
		{
			return false;
		}
		
		if ( ! $this->can_learn_more_talents() )
		{
			return false;
		}
		
		if ( $this->has_talent($skill) )
		{
			return false;
		}
		
		if ( in_array($skill->id, Config::get('game.racial_skills')[$this->race]) )
		{
			return true;
		}
		
		$characteristics = $this->characteristics;
		
		foreach ( $characteristics as $characteristicName )
		{
			$characteristic = Characteristic::get($characteristicName);
			if ( in_array($skill->id, $characteristic->get_skills()) )
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Aprendemos talento a personaje
	 * @param Skill $skill
	 * @return boolean
	 */
	public function learn_talent(Skill $skill)
	{
		$characterTalent = new CharacterTalent();
		
		$characterTalent->character_id = $this->id;
		$characterTalent->skill_id = $skill->id;
		$characterTalent->save();
		
		$this->talent_points--;
		$this->save();
				
		return true;
	}
	
	public function talents()
	{
		return $this->has_many('CharacterTalent', 'character_id');
	}
	
	/**
	 * Verifica si personaje tiene talento aprendido
	 * @param Skill $skill
	 * @return boolean
	 */
	public function has_talent(Skill $skill)
	{
		return $this->talents()->where('skill_id', '=', $skill->id)->take(1)->count() > 0;
	}
	
	/**
	 * @return array
	 */
	public function get_characteristics()
	{
		if ( ! $this->get_attribute('characteristics') )
		{
			return null;
		}
		
		$characteristics = explode(',', $this->get_attribute('characteristics'));
		return $characteristics;
		
		/*$characteristicsArray = array();
		
		foreach ( $characteristics as $characteristic )
		{
			$characteristicsArray[] = Characteristic::get($characteristic);
		}
		
		return $characteristicsArray;*/
	}
	
	/**
	 * Verificamos si personaje tiene cierta caracteristica
	 * @param integer $characteristic Caracteristica a revisar (usar constante de Characteristic)
	 * @return boolean
	 */
	public function has_characteristic($characteristic)
	{
		$characterCharacteristics = $this->characteristics;
		
		if ( ! $characterCharacteristics )
		{
			return false;
		}
		
		return in_array($characteristic, $characterCharacteristics);
	}
	
	/**
	 * Regeneramos vida al jugador en caso de ser necesario
	 * @param boolean $save True para guardar el row (UPDATE)
	 */
	public function regenerate_life($save = false)
	{
	   if ( $this->current_life < $this->max_life )
	   {
		   $time = time();
		   
		   if ( ! $this->last_regeneration_time )
		   {
			   $this->last_regeneration_time = $time;
		   }

		   $regeneration = ($this->regeneration_per_second + $this->regeneration_per_second_extra) * ($time - $this->last_regeneration_time);

		   if ( $regeneration > 0 )
		   {
			   $this->current_life += $regeneration;
			   $this->last_regeneration_time = $time;
		   }
	   }
	   else
	   {
		   // Evitamos que si el usuario tiene una regeneracion
		   // muy antigua y luego recibe daño que sea curado
		   // completamente
		   $this->last_regeneration_time = null;
	   }
	   
	   if ( $save )
	   {
		   $this->save();
	   }
	}
	
	/**
	 * @return float
	 */
	public function get_xp_rate()
	{
		$rate = Config::get('game.xp_rate');
		
		if ( $this->has_skill(Config::get('game.vip_multiplier_xp_rate_skill')) )
		{
			$rate *= 1.2;
		}
		
		return $rate;
	}
	
	/**
	 * @return float
	 */
	public function get_xp_quest_rate()
	{
		$rate = Config::get('game.quest_xp_rate');
		
		if ( $this->has_skill(Config::get('game.vip_multiplier_xp_rate_skill')) )
		{
			$rate *= 1.2;
		}
		
		return $rate;
	}
	
	/**
	 * @return float
	 */
	public function get_drop_rate()
	{
		return Config::get('game.drop_rate');
	}
	
	/**
	 * @return float
	 */
	public function get_explore_reward_rate()
	{
		$rate = Config::get('game.explore_reward_rate');
		
		if ( $this->has_skill(Config::get('game.vip_multiplier_coin_rate_skill')) )
		{
			$rate *= 1.3;
		}
		
		return $rate;
	}
	
	/**
	 * @return float
	 */
	public function get_coins_rate()
	{
		$rate = Config::get('game.coins_rate');
		
		if ( $this->has_skill(Config::get('game.vip_multiplier_coin_rate_skill')) )
		{
			$rate *= 1.3;
		}
		
		return $rate;
	}
	
	/**
	 * @return float
	 */
	public function get_quest_coins_rate()
	{
		$rate = Config::get('game.quest_coins_rate');
		
		if ( $this->has_skill(Config::get('game.vip_multiplier_coin_rate_skill')) )
		{
			$rate *= 1.3;
		}
		
		return $rate;
	}
	
	/**
	 * Verificamos si personaje esta registrado en torneo
	 * @param Tournament $tournament
	 * @return boolean
	 */
	public function is_registered_in_tournament(Tournament $tournament)
	{
		if ( ! $this->clan_id )
		{
			return false;
		}
		
		if ( ! $tournament )
		{
			return false;
		}
		
		return $tournament->is_clan_registered($this->clan);
	}
	
	/**
	 * Verificamos los skills del personaje
	 * (en caso de que tengan que ser removidos)
	 */
	public function check_skills_time()
	{
		foreach ( $this->skills()->get() as $skill )
		{
			$skill->update_time();
		}
	}
	
	/**
	 * @param mixed $skill Puede ser el id del skill o instancia de Skill
	 * @return boolean
	 */
	public function has_skill($skill)
	{
		if ( $skill instanceof Skill )
		{
			$skill = $skill->id;
		}
		
		return $this->skills()->where('skill_id', '=', (int) $skill)->take(1)->count() > 0;
	}
	
	/**
	 * Averiguamos si la cuenta del usuario logueado
	 * es VIP
	 * @return boolean
	 */
	public function is_vip()
	{
		if ( Auth::guest() )
		{
			return false;
		}
		
		return Auth::user()->vip_until > time();
	}

	/**
	 * Verificar si personaje puede atacar en pareja
	 * @return boolean
	 */
	public function can_attack_in_pairs()
	{
		return $this->can_fight() && $this->clan_id;
	}
	
	/**
	 * Verificar si dos personajes pueden atacar como pareja
	 * @param Character $pair
	 * @return boolean
	 */
	public function can_attack_with(Character $pair)
	{
		return	$this->can_attack_in_pairs() && 
				$pair->can_attack_in_pairs() && 
				$this->clan_id == $pair->clan_id &&
				$this->id != $pair->id &&
				$this->zone_id == $pair->zone_id;
	}
	
	/**
	 * Obtenemos las posibles parejas
	 * @param array $select Columnas a seleccionar
	 * @return array
	 */
	public function get_pairs($select = array('*'))
	{
		$pairs = array();
		
		$select = (array) $select + array('id', 'clan_id');
		
		$characters = static::select($select)
							->where('zone_id', '=', $this->zone_id)
							->where('clan_id', '=', $this->clan_id)
							->where('id', '<>', $this->id)
							->get();
		
		foreach ( $characters as $pair )
		{
			if ( $this->can_attack_with($pair) )
			{
				$pairs[] = $pair;
			}
		}
		
		return $pairs;
	}
	
	/**
	 * Obtener el precio de un atributo
	 *
	 * @param string $stat
	 * @return integer/Exception
	 */
	public function get_stat_price($stat)
	{
		$price = 0;

		switch ( $stat )
		{
			case 'stat_strength':
				$price = $this->stat_strength * Config::get("game.{$this->race}_strength_price_multiplier") * Config::get('game.strength_price_multiplier');
				break;

			case 'stat_dexterity':
				$price = $this->stat_dexterity * Config::get("game.{$this->race}_dexterity_price_multiplier") * Config::get('game.dexterity_price_multiplier');
				break;

			case 'stat_resistance':
				$price = $this->stat_resistance * Config::get("game.{$this->race}_resistance_price_multiplier") * Config::get('game.resistance_price_multiplier');
				break;

			case 'stat_magic':
				$price = $this->stat_magic * Config::get("game.{$this->race}_magic_price_multiplier") * Config::get('game.magic_price_multiplier');
				break;

			case 'stat_magic_skill':
				$price = $this->stat_magic_skill * Config::get("game.{$this->race}_magic_skill_price_multiplier") * Config::get('game.magic_skill_price_multiplier');
				break;

			case 'stat_magic_resistance':
				$price = $this->stat_magic_resistance * Config::get("game.{$this->race}_magic_resistance_price_multiplier") * Config::get('game.magic_resistance_price_multiplier');
				break;

			default:
				throw new Exception("El atributo {$stat} no es válido.");
				break;
		}

		return (int) $price;
	}
	
	/**
	 * Obtenemos todas las habilidades
	 * que no sean de clan de un personaje
	 * @return Eloquent
	 */
	public function get_non_clan_skills()
	{
		return $this->skills()
					->left_join('skills as skill', function($join) {
						$join->on('skill.id', '=', 'character_skills.skill_id')
							 ->on('skill.level', '=', 'character_skills.level');
					})
					->where('target', '<>', 'clan');
	}
	
	/**
	 * Verificamos si un personaje es admin
	 * @return boolean
	 */
	public function is_admin()
	{
		// hard-coded, hacerlo de la misma
		// forma que estan los privilegios
		// de los clanes
		return $this->name == 'Ruke';
	}
	
	/**
	 * Actualizamos los stats extra
	 * restando o agregando
	 * @param array $stats
	 * @param boolean $add true para sumar, false para restar
	 */
	public function update_extra_stat($stats, $add)
	{
		if ( ! is_array($stats) )
		{
			return;
		}
		
		if ( isset($stats['stat_strength']) )
		{
			if ( $add )
				$this->stat_strength_extra += $stats['stat_strength'];
			else
				$this->stat_strength_extra -= $stats['stat_strength'];
		}
		
		if ( isset($stats['stat_dexterity']) )
		{
			if ( $add )
				$this->stat_dexterity_extra += $stats['stat_dexterity'];
			else
				$this->stat_dexterity_extra -= $stats['stat_dexterity'];
		}
		
		if ( isset($stats['stat_resistance']) )
		{
			if ( $add )
				$this->stat_resistance_extra += $stats['stat_resistance'];
			else
				$this->stat_resistance_extra -= $stats['stat_resistance'];
		}
		
		if ( isset($stats['stat_magic']) )
		{
			if ( $add )
				$this->stat_magic_extra += $stats['stat_magic'];
			else
				$this->stat_magic_extra -= $stats['stat_magic'];
		}
		
		if ( isset($stats['stat_magic_skill']) )
		{
			if ( $add )
				$this->stat_magic_skill_extra += $stats['stat_magic_skill'];
			else
				$this->stat_magic_skill_extra -= $stats['stat_magic_skill'];
		}
		
		if ( isset($stats['stat_magic_resistance']) )
		{
			if ( $add )
				$this->stat_magic_resistance_extra += $stats['stat_magic_resistance'];
			else
				$this->stat_magic_resistance_extra -= $stats['stat_magic_resistance'];
		}
		
		if ( isset($stats['regeneration_per_second']) )
		{
			if ( $add )
			{
				$this->regeneration_per_second_extra += $stats['regeneration_per_second'];
			}
			else
			{
				$this->regeneration_per_second_extra -= $stats['regeneration_per_second'];
			}
		}
		
		if ( isset($stats['evasion']) )
		{
			if ( $add )
				$this->evasion_extra += $stats['evasion'];
			else
				$this->evasion_extra -= $stats['evasion'];
		}
		
		if ( isset($stats['critical_chance']) )
		{
			if ( $add )
				$this->critical_chance_extra += $stats['critical_chance'];
			else
				$this->critical_chance_extra -= $stats['critical_chance'];
		}
		
		if ( isset($stats['attack_speed']) )
		{
			if ( $add )
				$this->attack_speed_extra += $stats['attack_speed'];
			else
				$this->attack_speed_extra -= $stats['attack_speed'];
		}
		
		if ( isset($stats['magic_defense']) )
		{
			if ( $add )
				$this->magic_defense_extra += $stats['magic_defense'];
			else
				$this->magic_defense_extra -= $stats['magic_defense'];
		}
		
		if ( isset($stats['physical_defense']) )
		{
			if ( $add )
				$this->physical_defense_extra += $stats['physical_defense'];
			else
				$this->physical_defense_extra -= $stats['physical_defense'];
		}
		
		if ( isset($stats['magic_damage']) )
		{
			if ( $add )
				$this->magic_damage_extra += $stats['magic_damage'];
			else
				$this->magic_damage_extra -= $stats['magic_damage'];
		}
		
		if ( isset($stats['physical_damage']) )
		{
			if ( $add )
				$this->physical_damage_extra += $stats['physical_damage'];
			else
				$this->physical_damage_extra -= $stats['physical_damage'];
		}
		
		if ( isset($stats['reflect_magic_damage']) )
		{
			if ( $add )
				$this->reflect_magic_damage_extra += $stats['reflect_magic_damage'];
			else
				$this->reflect_magic_damage_extra -= $stats['reflect_magic_damage'];
		}
		
		if ( isset($stats['reflect_physical_damage']) )
		{
			if ( $add )
				$this->reflect_physical_damage_extra += $stats['reflect_physical_damage'];
			else
				$this->reflect_physical_damage_extra -= $stats['reflect_physical_damage'];
		}
		
		if ( isset($stats['travel_time']) )
		{
			if ( $add )
				$this->travel_time_extra += $stats['travel_time'];
			else
				$this->travel_time_extra -= $stats['travel_time'];
		}
		
		if ( isset($stats['battle_rest_time']) )
		{
			if ( $add )
				$this->battle_rest_time_extra += $stats['battle_rest_time'];
			else
				$this->battle_rest_time_extra -= $stats['battle_rest_time'];
		}
		
		if ( isset($stats['skill_cd_time']) )
		{
			if ( $add )
				$this->skill_cd_time_extra += $stats['skill_cd_time'];
			else
				$this->skill_cd_time_extra -= $stats['skill_cd_time'];
		}
		
		if ( isset($stats['luck']) )
		{
			if ( $add )
				$this->luck_extra += $stats['luck'];
			else
				$this->luck_extra -= $stats['luck'];
		}
		
		if ( isset($stats['xp_rate']) )
		{
			if ( $add )
				$this->xp_rate_extra += $stats['xp_rate'];
			else
				$this->xp_rate_extra -= $stats['xp_rate'];
		}
		
		if ( isset($stats['quest_xp_rate']) )
		{
			if ( $add )
				$this->quest_xp_rate_extra += $stats['quest_xp_rate'];
			else
				$this->quest_xp_rate_extra -= $stats['quest_xp_rate'];
		}
		
		if ( isset($stats['drop_rate']) )
		{
			if ( $add )
				$this->drop_rate_extra += $stats['drop_rate'];
			else
				$this->drop_rate_extra -= $stats['drop_rate'];
		}
		
		if ( isset($stats['explore_reward_rate']) )
		{
			if ( $add )
				$this->explore_reward_rate_extra += $stats['explore_reward_rate'];
			else
				$this->explore_reward_rate_extra -= $stats['explore_reward_rate'];
		}
		
		if ( isset($stats['coin_rate']) )
		{
			if ( $add )
				$this->coin_rate_extra += $stats['coin_rate'];
			else
				$this->coin_rate_extra -= $stats['coin_rate'];
		}
		
		if ( isset($stats['quest_coin_rate']) )
		{
			if ( $add )
				$this->quest_coin_rate_extra += $stats['quest_coin_rate'];
			else
				$this->quest_coin_rate_extra -= $stats['quest_coin_rate'];
		}
		
		$this->save();
	}
	
	public function set_xp($value)
	{
		if ( $value >= $this->xp_next_level )
		{
			$this->level++;
			$this->xp_next_level = (int) (5 * $this->level);
			$value = 0;
			
			if ( $this->level % 5 == 0 && $this->can_learn_more_talents() )
			{
				$this->talent_points++;
			}

			/*
			 *	Verificamos que siga cumpliendo
			 *	con los requerimientos de sus orbes
			 *	(en caso de tener alguno)
			 */
			if ( $this->has_orb() )
			{
				$orbs = $this->orbs;

				foreach ( $orbs as $orb )
				{
					// Si no cumple con los requerimientos...
					if ( $this->level < $orb->min_level || $this->level > $orb->max_level )
					{
						$orb->reset();
					}
				}
			}

			/* 
			 *	Aumentamos la vida y restauramos
			 */
			$this->max_life = $this->max_life + $this->level * 40;
			$this->current_life = $this->max_life;
			
			$this->points_to_change += Config::get('game.points_per_level');
		}
		
		return parent::set_xp($value);
	}
	
	/**
	 * Usar consumible (pocion, etc.)
	 * 
	 * @param Item $consumable
	 * @param integer $amount
	 * @return boolean
	 */
	public function use_consumable(Item $consumable, $amount)
	{
		if ( ! $consumable )
		{
			return false;
		}
		
		if ( $consumable->class != 'consumible' )
		{
			return false;
		}
		
		if ( $amount <= 0 )
		{
			return false;
		}
		
		if ( $consumable->skill != '0-0' )
		{
			$skills = $consumable->get_skills();
			
			$nonClanSkills = $this->get_non_clan_skills()->select(array('amount'))->get();
			$activeSkills = 0;
			foreach ( $nonClanSkills as $nonClanSkill )
			{
				$activeSkills += $nonClanSkill->amount;
			}

			if ( $activeSkills + $amount > Config::get('game.max_potions') )
			{
				foreach ( $skills as $skill )
				{
					if ( $skill->type != 'heal' )
					{
						return false;
					}
				}
			}

			$tournament = Tournament::get_active()->first();
			$healPotionCounter = 0;
			$otherPotionCounter = 0;

			if ( $tournament )
			{
				foreach ( $skills as $skill )
				{
					// Si hay un torneo activo y el mismo no acepta pociones...
					if ( ! $tournament->allow_potions && $skill->type != 'heal' )
					{
						return false;
					}
				}
			}
			
			foreach ( $skills as $skill )
			{
				if ( $skill->type == 'heal' )
				{
					$healPotionCounter++;
				}
				else
				{
					$otherPotionCounter++;
				}

				$skill->cast($this, $this, $amount);
			}

			if ( $tournament )
			{
				if ( $healPotionCounter )
				{
					$tournament->update_life_potions_counter($healPotionCounter);
				}

				if ( $otherPotionCounter )
				{
					$tournament->update_potions_counter($otherPotionCounter);
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Usar consumible de inventario
	 * 
	 * @param CharacterItem $consumable
	 * @param integer $amount
	 * @return string
	 */
	public function use_consumable_of_inventory(CharacterItem $consumable, $amount)
	{
		if ( ! $consumable )
		{
			return 'Ese consumible no existe.';
		}
		
		if ( $amount <= 0 )
		{
			return '¿Usar una cantidad igual o menor a 0?.';
		}
		
		if ( $consumable->count < $amount )
		{
			return 'No tienes esa cantidad.';
		}
		
		$item = $consumable->item;
		
		if ( ! $item )
		{
			return 'El objeto no existe. Por favor, repórtalo a la administración.';
		}
		
		if ( $item->level > $this->level )
		{
			return 'No tienes suficiente nivel para usar ese consumible.';
		}
		
		if ( ! $this->use_consumable($item, $amount) )
		{
			if ( Tournament::is_active() )
			{
				return 'El torneo no acepta pociones que no sean unicamente de vida.';
			}
			else
			{
				return 'Ese objeto no es de tipo consumible o ya alcanzaste el límite de habilidades activas.';
			}
		}
		
		$consumable->count -= $amount;
		$consumable->save();
		
		return '';
	}
	
	/**
	 * Obtenemos el tooltip del personaje
	 * 
	 * @return <string>
	 */
	public function get_tooltip()
	{
		$message = "<div style='width: 350px; text-align: left;'>";
		$message .= "<div class='pull-left icon-race-30 icon-race-30-".$this->race."_".$this->gender."' style='margin-right: 10px;'></div>";
		$message .= $this->name . ' - Nivel ' . $this->level;
		
		$message .= "<small class='pull-right' style='color: #AFAFAF;'>";
		
		switch ( $this->race )
		{
			case 'dwarf':
				$message .= 'Enano';
				break;
			
			case 'human':
				$message .= 'Humano';
				break;
			
			case 'drow':
				$message .= 'Drow';
				break;
			
			case 'elf':
				$message .= 'Elfo';
				break;
			
			default:
				$message .= 'Desconocido';
				break;
		}
		$message .= '</small>';
		
		if ( $this->clan_id != 0 )
		{
			$message .= '<p>Miembro de: ' . $this->clan->name . '</p>';
		}
		
		$message .= "<ul class='unstyled text-center' style='width: 340px;'>";
		
		$message .= "
		<li>
			<span class='ui-button button'>
				<i class='button-icon hearth'></i>
				<span class='button-content' style='width: 200px;'>
					<b class='pull-left'>Fuerza física:</b>
					<div class='pull-right'>" . mt_rand($this->stat_strength, $this->stat_strength * 1.3) . "</div>
				</span>
			</span>
		</li>
		";
		
		$message .= "
		<li>
			<span class='ui-button button'>
				<i class='button-icon boot'></i>
				<span class='button-content' style='width: 200px;'>
					<b class='pull-left'>Destreza física:</b>
					<div class='pull-right'>" . mt_rand($this->stat_dexterity, $this->stat_dexterity * 1.3) . "</div>
				</span>
			</span>
		</li>
		";
		
		$message .= "
		<li>
			<span class='ui-button button'>
				<i class='button-icon fire'></i>
				<span class='button-content' style='width: 200px;'>
					<b class='pull-left'>Resistencia:</b>
					<div class='pull-right'>" . mt_rand($this->stat_resistance, $this->stat_resistance * 1.3) . "</div>
				</span>
			</span>
		</li>
		";
		
		$message .= "
		<li>
			<span class='ui-button button'>
				<i class='button-icon axe'></i>
				<span class='button-content' style='width: 200px;'>
					<b class='pull-left'>Poder mágico:</b>
					<div class='pull-right'>" . mt_rand($this->stat_magic, $this->stat_magic * 1.3) . "</div>
				</span>
			</span>
		</li>
		";
		
		$message .= "
		<li>
			<span class='ui-button button'>
				<i class='button-icon thunder'></i>
				<span class='button-content' style='width: 200px;'>
					<b class='pull-left'>Habilidad mágica:</b>
					<div class='pull-right'>" . mt_rand($this->stat_magic_skill, $this->stat_magic_skill * 1.3) . "</div>
				</span>
			</span>
		</li>
		";
		
		$message .= "
		<li>
			<span class='ui-button button'>
				<i class='button-icon thunder'></i>
				<span class='button-content' style='width: 200px;'>
					<b class='pull-left'>Contraconjuro:</b>
					<div class='pull-right'>" . mt_rand($this->stat_magic_resistance, $this->stat_magic_resistance * 1.3) . "</div>
				</span>
			</span>
		</li>
		";
		
		$message .= "</ul>";
		
		$message .= "</div>";
		
		return $message;
	}
	
	public function has_permission($permission)
	{
		if ( $this->clan_id == 0 )
		{
			return false;
		}
		
		return $this->clan->has_permission($this, $permission);
	}
	
	public function add_permission($permission, $save = true)
	{
		$this->clan->add_permission($this, $permission, $save);
	}
	
	public function revoke_permission($permission, $save = true)
	{
		$this->clan->revoke_permission($this, $permission, $save);
	}
	
	/**
	 * Wraper. Si $value es true, agregamos el permiso,
	 * de lo contrario lo removemos.
	 * 
	 * @param <integer> $permission
	 * @param <boolean> $value
	 * @param <boolean> $save
	 */
	public function set_permission($permission, $value, $save = true)
	{
		if ( $value )
		{
			$this->add_permission($permission, $save);
		}
		else
		{
			$this->revoke_permission($permission, $save);
		}
	}
	
	// Evitamos vida por debajo de 0 o mayor a max_life
	public function set_current_life($value)
	{		
		if ( $value < 0 )
		{
			$value = 0;
		}
		else
		{
			if ( $this->max_life < $value )
			{
				$value = $this->max_life;
			}
		}

		return parent::set_current_life($value);
	}

	public static function logged_user_has_character()
	{
		$user = Auth::user();

		if ( ! $user )
		{
			return false;
		}

		return Character::where('user_id', '=', $user->id)->count() != 0;
	}

	/**
	 *	Verificamos si el personaje tiene
	 *	una quest completada
	 *
	 *	@param $questId <integer>
	 *	@return <bool>
	 */
	public function has_quest_completed(Quest $quest)
	{
		$characterQuest = $this->quests()->where('quest_id', '=', $quest->id)->first();

		if ( ! $characterQuest )
		{
			return false;
		}

		return $characterQuest->progress == 'finished';
	}

	/**
	 *	Nos fijamos si el personaje tiene una misión.
	 *	
	 *	@param <mixed> $quest Puede ser directamente el id o una instancia de Quest.
	 *	@return <bool> false si no tiene la mision (ya sea aceptada, completa, etc.) true de lo contrario.
	 */
	public function has_quest($quest)
	{
		$questId;

		if ( $quest instanceof Quest )
		{
			$questId = $quest->id;
		}
		else
		{
			$questId = (int) $quest;
		}

		return $this->quests()->where('quest_id', '=', $questId)->count() > 0;
	}

	/**
	 *	Nos fijamos si el personaje tiene
	 *	una mision actualmente pedida pero que
	 *	no la ha finalizado (es decir, su progreso
	 *	no es finished)
	 *
	 *	@param <mixed> $quest
	 *	@return <bool> true en caso de tener mision sin completar, false de lo contrario
	 */
	public function has_unfinished_quest($quest)
	{
		$questId;

		if ( $quest instanceof Quest )
		{
			$questId = $quest->id;
		}
		else
		{
			$questId = (int) $quest;
		}

		return $this
		->quests()
		->where('quest_id', '=', $questId)
		->where('progress', '<>', 'finished')
		->count() > 0;
	}

	public function get_progress_for_view(Quest $quest)
	{
		$characterProgress = $this->quests()->where('quest_id', '=', $quest->id)->first();

		if ( $characterProgress )
		{
			return $characterProgress->get_progress_for_view();
		}

		return null;
	}

	public function leave_clan()
	{
		$clan = $this->clan;
		if ( $clan )
		{
			/*
			 *	El lider de clan no puede salir
			 *	del mismo
			 */
			if ( $this->id != $clan->leader_id )
			{
				$this->clan_id = 0;
				$this->save();

				$clan->leave($this);
			}
		}
	}

	public function give_full_activity_bar_reward()
	{
		$xpAmount = (int) ($this->level / 3);
		$coinsAmount = $this->level * 50 * $this->get_coins_rate();

		$this->add_coins($coinsAmount);

		$this->xp += $xpAmount;
		$this->points_to_change += $xpAmount;

		if ( $this->clan_id > 0 )
		{
			$this->clan->add_xp(1);
		}

		$this->save();

		$rewards = array(
			array(
				'amount' => $coinsAmount,
				'name' => 'Monedas'
			),

			array(
				'amount' => $xpAmount,
				'name' => 'Experiencia'
			)
		);

		// if ( 10% chance )
		// n ironcoins

		Message::activity_bar_reward($this, $rewards);
	}

	public function give_logged_of_day_reward()
	{
		$this->add_coins(mt_rand($this->level * 10, $this->level * 20));
		Event::fire('loggedOfDayReward', array($this));
	}

	public function is_in_clan_of(Character $character)
	{
		return $this->clan_id > 0 && $this->clan_id == $character->clan_id;
	}

	/**
	 *	¿Tiene orbe el personaje?, true en caso de afirmativo
	 *
	 *	@return <Bool>
	 */
	public function has_orb()
	{
		return $this->orbs()->count() > 0;
	}

	public function empty_slot()
	{
		for ( $i = 1, $max = 6; $i <= $max; $i++ )
		{
			if ( $this->items()->where('slot', '=', $i)->take(1)->count() == 0 )
			{
				return $i;
			}
		}

		return false;
	}

	/**
	 *	@return <CharacterItem>
	 */
	public function get_equipped_weapon()
	{
		if ( ! $this )
		{
			return null;
		}

		return $this->items()
		->where_in('location', array('lhand', 'rhand', 'lrhand'))
		->with('item', function($query) 
		{
			$query->where_in('type', array('blunt', 'bigblunt', 'sword', 'bigsword', 'bow', 'dagger', 'staff', 'bigstaff'));
		})
		->first();
	}

	public function get_equipped_shield()
	{
		if ( ! $this )
		{
			return null;
		}

		return $this->items()
		->where('location', '=', 'lhand')
		->with('item', function($query) 
		{
			$query->where('type', '=', 'shield');
		})
		->first();
	}

	public function get_item_from_body_part($body_part)
	{
		if ( ! $this || ! $body_part )
		{
			return null;
		}

		return $this->items()->where('location', '=', $body_part)->first();
	}

	public function unequip_item(CharacterItem $characterItem)
	{
		if ( ! $this || ! $characterItem )
		{
			return false;
		}

		$emptySlot = $this->empty_slot();

		if ( $emptySlot )
		{
			$characterItem->location = 'inventory';
			$characterItem->slot = $emptySlot;
			
			$this->update_extra_stat($characterItem->item->to_array(), false);

			$characterItem->save();

			return true;
		}

		return false;
	}

	public function equip_item(CharacterItem $characterItem)
	{
		if ( ! $this || ! $characterItem )
		{
			return 'El personaje o el objeto del personaje no existe.';
		}

		$item = $characterItem->item;

		if ( ! $item )
		{
			return 'El objeto no existe. Por favor, repórtalo a la administración.';
		}

		if ( $item->level > $this->level )
		{
			return 'No tienes suficiente nivel equipar este objeto.';
		}
				
		$itemBodyPart = $item->body_part;

		switch ( $itemBodyPart ) {
			case 'lhand':
			case 'rhand':
				$lrhand = $this->get_item_from_body_part('lrhand');

				if ( $lrhand )
				{
					if ( ! $this->unequip_item($lrhand) )
					{
						return 'No tienes espacio en el inventario para desequiparte el objeto de dos manos que ya tienes equipado.';
					}
				}
				break;

			case 'lrhand':
				$lhand = $this->get_item_from_body_part('lhand');
				$rhand = $this->get_item_from_body_part('rhand');

				if ( $lhand )
				{
					if ( ! $this->unequip_item($lhand) )
					{
						return 'No tienes espacio en el inventario para desequiparte el objeto que ya tienes en la mano izquierda.';
					}
				}

				if ( $rhand )
				{
					if ( ! $this->unequip_item($rhand) )
					{
						return 'No tienes espacio en el inventario para desequiparte el objeto que ya tienes en la mano derecha.';
					}
				}
				break;
		}

		$equippedItem = $this->get_item_from_body_part($itemBodyPart);

		if ( $equippedItem )
		{
			if ( ! $this->unequip_item($equippedItem) )
			{
				return 'No tienes espacio en el inventario para desequiparte el objeto que ya tienes equipado.';
			}
		}
		
		$this->update_extra_stat($item->to_array(), true);

		$characterItem->location = $itemBodyPart;
		$characterItem->slot = 0;
		$characterItem->save();

		return '';
	}
	
	/**
	 * Obtenemos los objetos del personaje que pueden ser vendidos
	 * @return Eloquent
	 */
	public function tradeable_items()
	{
		return $this->items()->join('items', 'items.id', '=', 'character_items.item_id')
							 ->where('items.selleable', '=', 1)
							 ->where('location', '=', 'inventory')
							 ->where('count', '>', 0);
	}

	/**
	 *	¿Puede el personaje iniciar un comercio?
	 *	
	 *	@return <Bool>
	 */
	public function can_trade()
	{
		return $this->items()->where('location', '=', 'inventory')->where('count', '>', 0)->count() > 0;
	}

	public function user()
	{
		return $this->belongs_to('IronFistUser', 'user_id');
	}

	/**
	 *	Devolvemos el personaje del usuario
	 *	que esté logueado
	 *
	 *	@return <Character>
	 */
	public static function get_character_of_logged_user($select = array())
	{
		if ( Auth::guest() )
		{
			return null;
		}

		$user = Auth::user();

		if ( count($select) > 0 )
		{
			return $user->character()->select($select)->first();
		}
		
		return $user->character;
	}

	public function battle_against($target, $pair = null)
	{
		if ( ! $target || ! $this )
		{
			return;
		}

		$battle = new Battle($this, $target, $pair);
		return $battle;
	}

	public function give_explore_reward($reward)
	{
		$this->add_coins($reward * $this->get_coins_rate());
	}

	public function get_link()
	{
		return '<a href="' . URL::to('authenticated/character/' . $this->name ) . '">' . $this->name . '</a>';
	}

	public function get_stats()
	{
		$stats = array();

		$stats['stat_strength'] = $this->stat_strength + $this->stat_strength_extra;
		$stats['stat_dexterity'] = $this->stat_dexterity + $this->stat_dexterity_extra;
		$stats['stat_resistance'] = $this->stat_resistance + $this->stat_resistance_extra;
		$stats['stat_magic'] = $this->stat_magic + $this->stat_magic_extra;
		$stats['stat_magic_skill'] = $this->stat_magic_skill + $this->stat_magic_skill_extra;
		$stats['stat_magic_resistance'] = $this->stat_magic_resistance + $this->stat_magic_resistance_extra;

		return $stats;
	}

	/**
	 *	Obtenemos la cantidad de monedas
	 *	en cobre de un personaje
	 *
	 *	@return <CharacterItem> 
	 */
	public function get_coins()
	{
		return $this->items()->select(array('id', 'count'))->where('item_id', '=', Config::get('game.coin_id'))->first();
	}
	
	/**
	 *	Contamos la cantidad de slots disponibles
	 * 
	 *	@return bool
	 */
	public function get_available_slots()
	{
		return Config::get('game.inventory_slot_amount') - $this->items()->where_location('inventory')->count();
	}

	/**
	 * Obtenemos un objeto que puede
	 * venir del cofre
	 * 
	 * @return Eloquent
	 */
	public function get_item_from_chest()
	{
		// Oro y experiencia fuera
		$invalidItems = array(
			Config::get('game.coin_id'),
			Config::get('game.xp_item_id')
		);

		return Item::where('level', '<=', $this->level + 5)
				   ->where('class', '<>', 'mercenary')
				   ->where('class', '<>', 'consumible')
				   ->where_not_in('id', $invalidItems)
				   ->order_by(DB::raw('RAND()'));
	}

	/**
	 * Verificamos si un personaje puede
	 * obtener una cierca cantidad de un objeto
	 * @param  integer/Item  $item   Id del item o instancia de Item
	 * @param  integer       $amount Cantidad del objeto
	 * @return boolean
	 */
	public function can_take_item($item, $amount = 1)
	{
		if ( $amount < 0 )
		{
			return false;
		}

		if ( ! $item instanceof Item )
		{
			$item = Item::select(array('id', 'stackable'))->find((int) $item);
			
			if ( ! $item )
			{
				return false;
			}
		}

		if ( $item->id == Config::get('game.coin_id') || $item->id == Config::get('game.xp_item_id') )
		{
			return true;
		}

		if ( $item->stackable )
		{
			$characterItem = $this->items()->where('item_id', '=', $item->id)->get();
			
			if ( $characterItem )
			{
				return true;
			}
			else
			{
				// Si tiene espacio true, si no false
				return $this->empty_slot();
			}
		}
		else
		{
			return $this->get_available_slots() >= $amount;
		}

		return false;
	}

	/**
	 *	Agregamos un objeto al personaje.
	 *
	 *	@param <mixed> $item Id del objeto o instancia de Item
	 *	@param <int> $amount
	 *	@return <bool> false si no se pudo agregar el item
	 */
	public function add_item($item, $amount = 1)
	{
		if ( $amount <= 0 )
		{
			return false;
		}
		
		if ( ! $item instanceof Item )
		{
			$item = Item::select(array('id', 'stackable'))->find((int) $item);
			
			if ( ! $item )
			{
				return false;
			}
		}
		
		if ( $item->id == Config::get('game.coin_id') )
		{
			$this->add_coins($amount);
			
			return true;
		}
		
		if ( $item->id == Config::get('game.xp_item_id') )
		{			
			$this->xp += $amount;
			$this->points_to_change += $amount;
			
			$this->save();
			
			return true;
		}
		
		if ( $item->stackable )
		{
			$characterItem = $this->items()->where('item_id', '=', $item->id)->first();
			
			if ( $characterItem )
			{
				$characterItem->count += $amount;
				$characterItem->save();
				
				return true;
			}
			else
			{
				$slot = $this->empty_slot();
				
				if ( $slot )
				{
					$characterItem = new CharacterItem();
					
					$characterItem->owner_id = $this->id;
					$characterItem->item_id = $item->id;
					$characterItem->count = $amount;
					$characterItem->location = 'inventory';
					$characterItem->slot = $slot;
					
					$characterItem->save();
					
					return true;
				}
			}
		}
		else
		{
			if ( $this->get_available_slots() >= $amount )
			{
				while ( $amount > 0 )
				{
					$slot = $this->empty_slot();
					
					$characterItem = new CharacterItem();
					
					$characterItem->owner_id = $this->id;
					$characterItem->item_id = $item->id;
					$characterItem->count = 1;
					$characterItem->location = 'inventory';
					$characterItem->slot = $slot;
					
					$characterItem->save();
					
					$amount--;
				}
				
				return true;
			}
		}
		
		return false;
	}

	public function add_coins($amount)
	{
		$coins = $this->get_coins();

		if ( ! $coins )
		{
			$coins = new CharacterItem();

			$coins->owner_id = $this->id;
			$coins->item_id = Config::get('game.coin_id');
			$coins->count = $amount;
		}
		else
		{
			$coins->count += $amount;
		}

		$coins->save();
	}

	/**
	 *	Obtenemos las monedas dividas en
	 *	oro, plata y cobre de un personaje
	 *
	 *	@return <Array> Monedas dividas en oro, plata y cobre
	 */
	public function get_divided_coins()
	{
		$coins = $this->get_coins();

		if ( $coins )
		{
			$coins = $coins->count;
		}

		return array(
			'gold' => substr($coins, 0, -4) ? substr($coins, 0, -4) : 0,
			'silver' => substr($coins, -4, -2) ? substr($coins, -4, -2) : 0,
			'copper' => substr($coins, -2) ? substr($coins, -2) : 0,
		);
	}

	public function can_explore()
	{
		if ( $this->has_skill(Config::get('game.stun_skill')) )
		{
			return false;
		}
		
		return $this->is_traveling == false && $this->is_exploring == false;
	}

	public function can_fight()
	{
		if ( $this->has_skill(Config::get('game.stun_skill')) )
		{
			return false;
		}
		
		if ( $this->has_skill(Config::get('game.confusion_skill')) )
		{
			return false;
		}
		
		return $this->is_traveling == false && $this->activities()->take(1)->count() == 0;
	}

	public function has_protection(Character $attacker)
	{
		$protectionTime = $this->attack_protections()->where('attacker_id', '=', $attacker->id)->first();

		if ( ! $protectionTime )
		{
			return false;
		}

		return $protectionTime->time > time();
	}

	public function can_be_attacked(Character $attacker)
	{
		if ( Tournament::is_active() )
		{
			if ( $this->registered_in_tournament != $attacker->registered_in_tournament )
			{
				return false;
			}

			if ( $this->clan_id != 0 && $this->clan_id == $attacker->clan_id )
			{
				return false;
			}
		}

		return $this->has_protection($attacker) == false && $attacker->zone_id == $this->zone_id && $attacker->id != $this->id;
	}
	
	public function after_battle()
	{
		$characterActivity = new CharacterActivity();

		$characterActivity->character_id = $this->id;
		$characterActivity->name = 'battlerest';
		$characterActivity->end_time = time() + Config::get('game.battle_rest_time');

		$characterActivity->save();
	}
	
	/**
	 * Si la batalla fue contra otro personaje
	 * entonces DEBEMOS compartir el cd despues
	 * de la batalla para evitar ataques consecutivos
	 */
	public function after_pvp_battle()
	{
		$characters = static::get_sharing_ip($this->ip)->select(array('id'))->get();
		
		foreach ( $characters as $character )
		{
			$character->after_battle();
		}
	}
	
	/**
	 * Devolvemos query para obtener todos los
	 * personajes que compartan ip
	 * @param string $ip
	 * @return Eloquent
	 */
	public static function get_sharing_ip($ip)
	{
		return static::where('ip', '=', $ip);
	}
	
	/**
	 * Actualizamos todos los personajes
	 * que tengan el ip == $ip con $newIp
	 * @param string $ip
	 * @param string $newIp
	 */
	public static function update_ip($ip, $newIp)
	{
		$characters = static::get_sharing_ip($ip)->select(array('id', 'ip'))->get();
		
		foreach ( $characters as $character )
		{
			$character->ip = $newIp;
			$character->save();
		}
	}

	/**
	 *	Iniciamos el viaje de un
	 *	personaje a una zona
	 *
	 *	@param <Zone> $zone
	 */
	public function travel_to(Zone $zone)
	{
		if ( $zone )
		{
			ActivityBar::add($this, 1);

			$this->is_traveling = true;
			$this->save();

			$characterActivity = new CharacterActivity();

			$characterActivity->character_id = $this->id;
			$characterActivity->name = 'travel';
			$characterActivity->data = array( 'zone' => $zone );
			$characterActivity->end_time = time() + Config::get('game.travel_time');

			$characterActivity->save();
		}
	}

	public function add_exploring_time(Zone $zone, $time)
	{
		$characterExploringTime = $this->exploring_times()->select(array('id', 'time'))->where('zone_id', '=', $zone->id)->first();

		if ( $characterExploringTime )
		{
			$characterExploringTime->time += $time;
		}
		else
		{
			$characterExploringTime = new CharacterExploringTime();

			$characterExploringTime->character_id = $this->id;
			$characterExploringTime->zone_id = $zone->id;
			$characterExploringTime->time = $time;
		}

		$characterExploringTime->save();
	}

	/**
	 * Explorar. Tiempo en segundos.
	 * @param integer $time
	 */
	public function explore($time)
	{
		$this->is_exploring = true;
		$this->save();

		$characterActivity = new CharacterActivity();

		$characterActivity->character_id = $this->id;
		$characterActivity->name = 'explore';
		$characterActivity->data = array( 'reward' => ($this->level * ($time/60)/2) * $this->get_explore_reward_rate(), 'time' => $time );
		$characterActivity->end_time = time() + $time;

		$characterActivity->save();
	}

	/**
	 *	Verificamos si el personaje está habilitado
	 *	para viajar
	 *
	 *	@return <mixed> True si puede, de lo contrario el mensaje de error
	 */
	public function can_travel()
	{
		if ( $this->is_exploring )
		{
			return 'Estás explorando';
		}
		/*
		 *	Si ya está viajando...
		 */
		if ( $this->is_traveling )
		{
			return 'Ya estás viajando, no puedes volver a hacerlo.';
		}
		
		if ( $this->has_skill(Config::get('game.root_skill')) )
		{
			return 'Estas bajo un efecto el cual no te permite viajar.';
		}
		
		if ( $this->has_skill(Config::get('game.stun_skill')) )
		{
			return 'Estas bajo un efecto el cual no te permite viajar.';
		}

		/*
		 *	¿Le alcanzan las monedas?
		 */
		$coins = $this->get_coins();
		if ( ! $coins || $coins->count < Config::get('game.travel_cost') )
		{
			return 'No tienes suficientes monedas.';
		}

		return true;
	}

	public function get_unread_messages_count()
	{
		$count = $this->messages()->where('unread', '=', true)->count();
		return ( $count > 0 ) ? $count : '';
	}

	public function can_enter_in_clan()
	{
		return $this->clan_id == 0;
	}

	public function remove_buff(CharacterSkill $characterSkill)
	{
		$characterSkill->end_time = 1;
		$characterSkill->skill->periodic($characterSkill);
	}

	public function started_quests()
	{
		return $this->quests()->where('progress', '=', 'started');
	}

	public function reward_quests()
	{
		return $this->quests()->where('progress', '=', 'reward');
	}

	public function zone()
	{
		return $this->belongs_to('Zone', 'zone_id');
	}

	public function activities()
	{
		return $this->has_many('CharacterActivity', 'character_id');
	}

	public function items()
	{
		return $this->has_many('CharacterItem', 'owner_id');
	}

	public function skills()
	{
		return $this->has_many('CharacterSkill', 'character_id');
	}

	public function quests()
	{
		return $this->has_many('CharacterQuest', 'character_id');
	}

	public function triggers()
	{
		return $this->has_many('CharacterTrigger', 'character_id');
	}

	public function messages()
	{
		return $this->has_many('Message', 'receiver_id');
	}

	public function clan()
	{
		return $this->belongs_to('Clan', 'clan_id');
	}

	public function petitions()
	{
		return $this->has_many('ClanPetition', 'character_id');
	}

	public function trades()
	{
		return $this->has_many('Trade', 'seller_id');
	}

	public function exploring_times()
	{
		return $this->has_many('CharacterExploringTime', 'character_id');
	}

	public function orbs()
	{
		return $this->has_many('Orb', 'owner_character');
	}

	public function attack_protections()
	{
		return $this->has_many('AttackProtection', 'target_id');
	}

	public function activity_bar()
	{
		return $this->has_one('ActivityBar', 'character_id');
	}
}