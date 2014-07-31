<?php

class Character extends Unit
{
	public static $softDelete = true;
	public static $timestamps = false;
	public static $table = "characters";
    
    /**
     * Cache para el segundo mercenario
     * @var Item
     */
    protected $secondMercenary;
    
    /**
     * Cache para cuando se calcula la fuerza final
     * @var float
     */
    protected $finalStrength;
    
    /**
     * Cache para cuando se calcula la destreza final
     * @var float
     */
    protected $finalDexterity;
    
    /**
     * Cache para cuando se calcula la resistencia final
     * @var float
     */
    protected $finalResistance;
    
    /**
     * Cache para cuando se calcula la magia final
     * @var float
     */
    protected $finalMagic;
    
    /**
     * Cache para cuando se calcula la habilidad magica final
     * @var float
     */
    protected $finalMagicSkill;
    
    /**
     * Cache para cuando se calcula la resistencia magica final
     * @var float
     */
    protected $finalMagicResistance;
    
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

    public static $factory = array(
        "user_id" => "integer|2",
        "ip" => "127.0.0.1",
        "name" => "string",
        "level" => "integer|2",
        "max_life" => "integer|5",
        "current_life" => "integer|5",
        "gender" => "male",
        "pvp_points" => "integer|2",
        "race" => "dwarf",
        "clan_id" => "factory|Clan",
        "clan_permission" => 0,
        "zone_id" => "factory|Zone",
        "stat_dexterity" => "integer|4",
        "stat_magic" => "integer|4",
        "stat_strength" => "integer|4",
        "stat_resistance" => "integer|4",
        "stat_magic_skill" => "integer|4",
        "stat_magic_resistance" => "integer|4",
        "stat_strength_extra" => "integer|4",
        "stat_dexterity_extra" => "integer|4",
        "stat_resistance_extra" => "integer|4",
        "stat_magic_extra" => "integer|4",
        "stat_magic_skill_extra" => "integer|4",
        "stat_magic_resistance_extra" => "integer|4",
        "language" => "es",
        "xp" => "integer|5",
        "xp_next_level" => "integer|5",
        "is_traveling" => 0,
        "created_at" => "date",
        "updated_at" => "date",
        "deleted_at" => "date",
        "last_regeneration_time" => 0,
        "points_to_change" => "integer|2",
        "is_exploring" => 0,
        "last_activity_time" => 0,
        "last_logged" => 0,
        "registered_in_tournament" => 0,
        "characteristics" => "",
        "regeneration_per_second" => 0,
        "regeneration_per_second_extra" => 0,
        "evasion" => 0,
        "evasion_extra" => 0,
        "critical_chance" => 0,
        "critical_chance_extra" => 0,
        "attack_speed" => 0,
        "attack_speed_extra" => 0,
        "magic_defense" => 0,
        "magic_defense_extra" => 0,
        "physical_defense" => 0,
        "physical_defense_extra" => 0,
        "magic_damage" => 0,
        "magic_damage_extra" => 0,
        "physical_damage" => 0,
        "physical_damage_extra" => 0,
        "reflect_magic_damage" => 0,
        "reflect_magic_damage_extra" => 0,
        "reflect_physical_damage" => 0,
        "reflect_physical_damage_extra" => 0,
        "travel_time" => 0,
        "travel_time_extra" => 0,
        "battle_rest_time" => 0,
        "battle_rest_time_extra" => 0,
        "skill_cd_time" => 0,
        "skill_cd_time_extra" => 0,
        "luck" => 0,
        "luck_extra" => 0,
        "xp_rate" => 0,
        "xp_rate_extra" => 0,
        "quest_xp_rate" => 0,
        "quest_xp_rate_extra" => 0,
        "drop_rate" => 0,
        "drop_rate_extra" => 0,
        "explore_reward_rate" => 0,
        "explore_reward_rate_extra" => 0,
        "coin_rate" => 0,
        "coin_rate_extra" => 0,
        "quest_coin_rate" => 0,
        "quest_coin_rate_extra" => 0,
        "talent_points" => 0,
        "invisible_until" => 0,
        "second_mercenary" => 0,
    );
    
    public function get_combat_behavior()
    {
        if (! $this->combatBehavior) {
            switch ($this->get_attribute("race")) {
                case "dwarf":
                    $damage = new DwarfCharacterDamage($this);
                    $armor = new DwarfCharacterArmor($this);
                    break;
                
                case "human":
                    $damage = new HumanCharacterDamage($this);
                    $armor = new HumanCharacterArmor($this);
                    break;
                
                case "elf":
                    $damage = new ElfCharacterDamage($this);
                    $armor = new ElfCharacterArmor($this);
                    break;
                
                case "drow":
                    $damage = new DrowCharacterDamage($this);
                    $armor = new DrowCharacterArmor($this);
                    break;
            }
            
            $this->combatBehavior = new AttackableBehavior($this, $damage, $armor);
        }
        
        return parent::get_combat_behavior();
    }
	
	/**
	 * Hacemos mazmorra con personaje
	 * 
	 * @param Dungeon $dungeon
	 * @return \DungeonBattle
	 */
	public function do_dungeon(Dungeon $dungeon)
	{
		return new DungeonBattle($this, $dungeon, $dungeon->get_level($this));
	}
	
	/**
	 * Obtenemos posibles zonas a donde el personaje pueda viajar
	 * 
	 * @return array
	 */
	public function get_travel_zones()
	{
		return Zone::where_type("city")
				   ->where("min_level", "<=", $this->level)
				   ->where("id", "<>", $this->zone_id)
				   ->get();
	}
	
	/**
	 * 
	 * @param CharacterItem $chest
	 * @return string|bool
	 */
	public function open_chest(CharacterItem $chest)
	{
		if ( $chest->item_id != Config::get('game.chest_item_id') )
		{
			return "¡Ese objeto no es un cofre!";
		}
		
		if ( ! $this->empty_slot() )
		{
			return "No tienes espacio en el inventario";
		}
		
		$item = $this->get_item_from_chest()->first();

		$this->add_item($item->id, 1);

		Session::flash('modalMessage', 'chest');
		Session::flash('chest', $item->id);

		$chest->count--;
		$chest->save();
		
		return true;
	}
	
	/**
	 * 
	 * @param CharacterItem $characterItem
	 * @param type $amount
	 * @return string|boolean
	 */
	public function use_inventory_item(CharacterItem $characterItem, $amount)
	{		
		if ( $characterItem->count < $amount )
		{
			return "No tienes esa cantidad";
		}
		
		$item = $characterItem->item;
		
		if ( $item->class == "consumible" )
		{
			return $this->use_consumable_of_inventory($characterItem, $amount);
		}
		
		if ( $item->id == Config::get('game.chest_item_id') )
		{
			return $this->open_chest($characterItem);
		}
        
        if ( $item->class == "none" )
		{
			return "Ese objeto no puede ser usado";
		}
		
		if ( $characterItem->location == "inventory" )
		{
			return $this->equip_item($characterItem);
		}
		else
		{
			if ( ! $this->unequip_item($characterItem) )
			{
				return "No tienes espacio en el inventario";
			}
		}
		
		return true;
	}
    
    public function get_image_path()
    {
        return URL::base() . "/img/characters/{$this->race}_{$this->gender}_999.png";
    }

	/**
	 * Obtenemos el arma del personaje
	 * @return CharacterItem
	 */
	public function get_weapon()
	{
		return $this->items()->where_in('location', array('lrhand', 'rhand'))->first();
	}

	/**
	 * Obtenemos el escudo del personaje
	 * @return CharacterItem
	 */
	public function get_shield()
	{
		return $this->items()->where('location', '=', 'lhand')->first();
	}

	/**
	 * Query para obtener los objetos del inventario
	 * @return Eloquent
	 */
	public function get_inventory_items()
	{
		return $this->items()->where('location', '=', 'inventory');
	}

	/**
	 * Obtenemos mercenario de personaje
	 * @return CharacterItem
	 */
	public function get_mercenary()
	{
		return $this->items()->where('location', '=', 'mercenary')->first();
	}

	/**
	 * Query para obtener el segundo mercenario del personaje
	 * @return Item|null
	 */
	public function get_second_mercenary()
	{
		if ( ! $this->has_second_mercenary() )
		{
			return null;
		}
        
        if (! $this->secondMercenary) {
            $this->secondMercenary = 
                Item::find($this->get_attribute('second_mercenary'));
        }
		
		return $this->secondMercenary;
	}

	/**
	 * Verificamos si personaje tiene equipada arma de dos manos
	 * @return boolean
	 */
	public function has_two_handed_weapon()
	{
		return $this->items()->with(array('item' => function($query)
		{
			$query->where('body_part', '=', 'lrhand');
		}))->take(1)->count() == 0;
	}

	/**
	 * Verificamos si merece la recompensa "Logueada del dia"
	 * @return boolean
	 */
	public function check_logged_of_day()
	{
		return $this->last_logged + 24 * 60 * 60 < time();
	}
	
	/**
	 * Actualizamos los tiempos de sus actividades
	 */
	public function update_activities_time()
	{
		$activities = $this->activities()->where('end_time', '<=', time())->get();

		foreach ( $activities as $activity )
		{
			$activity->update_time();
		}
	}

	/**
	 * Burlamos a la muerte (usado en batalla)
	 */
	public function cheat_death()
	{
		$skill = $this->skills()->where('skill_id', '=', Config::get('game.cheat_death_skill'))->first();

		if ( $skill )
		{
			$data = $skill->data;

			if ( ! isset($data['cheat_death']) )
			{
				$data['cheat_death'] = 5;
			}

			$data['cheat_death']--;

			if ( $data['cheat_death'] <= 0 )
			{
				$this->remove_buff($skill);
			}
			else
			{
				$skill->data = $data;
				$skill->save();
			}
		}
	}

    public function drops()
    {
        return array(
			array(
                'item_id' => Config::get('game.xp_item_id'), 
                'amount' => 1,
            ),
			array(
                'item_id' => Config::get('game.coin_id'), 
                'amount' => mt_rand(20 * $this->level, 60 * $this->level),
            )
		);
    }
    
	public function get_rewards()
	{
		return $this->drops();
	}
    
	public function get_attack($magical = false)
	{
		$attack = 0;

		if ( $magical )
		{
			$attack = $this->stat_magic + $this->stat_magic_extra + $this->magic_damage + $this->magic_damage_extra;
		}
		else
		{
			$attack = $this->stat_strength + $this->stat_strength_extra + $this->physical_damage + $this->physical_damage_extra;
		}

		return $attack;
	}

	public function get_resistance($magical = false)
	{
		$resistence = 0;

		if ( $magical )
		{
			$resistence = $this->stat_magic_resistance + $this->stat_magic_resistance_extra + $this->magic_defense + $this->magic_defense_extra;
		}
		else
		{
			$resistence = $this->stat_resistance + $this->stat_resistance_extra + $this->physical_defense + $this->physical_defense_extra;
		}

		return $resistence;
	}

	public function get_reflected_damage($magical = false)
	{
		return ( $magical ) ? $this->reflect_magic_damage + $this->reflect_magic_damage_extra : $this->reflect_physical_damage + $this->reflect_physical_damage_extra;
	}

    /**
     * 
     * @deprecated
     * @return float
     */
	public function get_cd()
	{
		if ( $this->attacks_with_magic() )
		{
			return 1000 / ($this->stat_magic_skill + $this->stat_magic_skill_extra + $this->attack_speed + $this->attack_speed_extra + 1);
		}
		else
		{
			return 800 / ($this->stat_dexterity + $this->stat_dexterity_extra + $this->attack_speed + $this->attack_speed_extra + 1);
		}
	}

	public function get_critical_chance()
	{
		return $this->get_attribute('critical_chance') + $this->critical_chance_extra;
	}

	public function get_evasion_chance()
	{
		return $this->evasion + $this->evasion_extra;
	}

	public function get_current_life()
	{
		return $this->get_attribute('current_life');
	}

	/**
	 * Verificamos si tiene segundo mercenario
	 * @return bool
	 */
	public function has_second_mercenary()
	{
		if ( ! $this->has_skill(Config::get('game.invocation')) )
		{
			return false;
		}

		if ( ! $this->get_attribute('second_mercenary') )
		{
			return false;
		}

		return true;
	}

	/**
	 * Verificamos si personaje puede pasarle el liderazgo del grupo
	 * a otro personaje
	 *
	 * @param Character $newLider
	 * @return bool
	 */
	public function can_give_leadership_to(Character $newLider)
	{
		if ( ! $this->clan_id )
		{
			return false;
		}

		if ( $newLider->clan_id != $this->clan_id )
		{
			return false;
		}

		if ( $this->id != $this->clan->leader_id )
		{
			return false;
		}

		return true;
	}

	/**
	 * Damos liderazgo del grupo a un personaje
	 * @param Character $newLider
	 */
	public function give_leadership_to(Character $newLider)
	{
		$clan = $this->clan;

		if ( $clan )
		{
			$clan->leader_id = $newLider->id;
			$clan->save();
		}
	}

	/**
	 * Cancelamos (deshabilitamos) todos los comercios de clan de un personaje (util cuando sale de un clan)
	 */
	public function cancel_all_clan_trades()
	{
		foreach ( $this->trades()->where('clan_id', '<>', 0)->get() as $clanTrade )
		{
			$clanTrade->until = 0;
			$clanTrade->clan_id = 0;

			$clanTrade->save();
		}
	}

	/**
	 * @return bool
	 */
	public function is_online()
	{
		return $this->last_activity_time > time() - 300;
	}

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
	 * Verificamos si personaje puede seguir a otro
	 *
	 * @param Character $character
	 * @return bool
	 */
	public function can_follow(Character $character)
	{
		if ( ! $character->is_traveling )
		{
			return false;
		}

		if ( $this->can_travel() !== true )
		{
			return false;
		}

		return true;
	}

	/**
	 * Perseguimos a un personaje
	 *
	 * @param Character $character
	 * @return bool
	 */
	public function follow(Character $character)
	{
		$travelActivity = $character->activities()->where('name', '=', 'travel')->first();

		if ( $travelActivity )
		{
			$this->travel_to($travelActivity->data['zone']);
			return true;
		}

		return false;
	}
	
	/**
	 * Ejecutar este metodo cuando un personaje ve a otro
	 * @param Character $target
	 */
	public function sees(Character $target)
	{
		if ( $target->has_skill(Config::get("game.trap_skill")) )
		{
			$target->cast_random_trap_to($this, true);
		}

		if ( $this->can_remove_invisibility_of($target) )
		{
			$target->remove_invisibility();
		}
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

		if ( $character && $character->id != $this->id )
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
		
		if ( $character && $character->id != $this->id )
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
		return $this->can_fight() === true && $this->clan_id;
	}
	
	/**
	 * Verificar si dos personajes pueden atacar como pareja
	 * 
	 * @param Character $pair
	 * @return string|boolean
	 */
	public function can_attack_with(Character $pair)
	{
		if ( ! $pair )
		{
			return "Intentas atacar en parejas, pero tu compañero parece no existir";
		}
		
		if ( ! $this->can_attack_in_pairs() )
		{
			return "Aun no puedes atacar en parejas";
		}
		
		if ( ! $pair->can_attack_in_pairs() )
		{
			return "Tu compañero aun no puede atacar en pareja";
		}
		
		if ( $this->clan_id != $pair->clan_id )
		{
			return "Para atacar en pareja, debes estar en el mismo grupo que tu compañero";
		}
		
		if ( $this->id == $pair->id )
		{
			return "¿Intentas atacar en pareja contigo mismo?";
		}
		
		if ( $this->zone_id != $pair->zone_id )
		{
			return "Para atacar en parejas ambos deben estar en la misma zona";
		}
		
		return true;
	}
	
	/**
	 * Obtenemos las posibles parejas para un enemigo
	 * @param array $select Columnas a seleccionar
	 * @return array
	 */
	public function get_pairs_to(Character $enemy, $select = array('*'))
	{
		$pairs = array();
		
		if ( $this->can_attack_in_pairs() )
		{
			$select = (array) $select + array('id', 'clan_id');

			$characters = static::select($select)
								->where('zone_id', '=', $this->zone_id)
								->where('clan_id', '=', $this->clan_id)
								->where('id', '<>', $this->id)
								->get();

			foreach ( $characters as $pair )
			{
				if ( $this->can_attack_with($pair) && $this->level + $pair->level < $enemy->level )
				{
					$pairs[] = $pair;
				}
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
		if ( $value <= 0 ) {
            // Verificamos si debe burlar a la muerte
            if ($this->has_buff(Config::get('game.cheat_death_skill'))) {
                $this->cheat_death();
                $value = 1;
            } else {
                $value = 0;
            }
		} else {
			$maxLife = $this->max_life;

			if ( $maxLife < $value ) {
				$value = $maxLife;
			}
		}

		return $this->set_attribute('current_life', $value);
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
	 *	@param Quest $quest
	 *	@return boolean
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
	
	/**
	 * Verificamos si personaje puede salirse de su clan
	 * @return boolean
	 */
	public function can_leave_clan()
	{
		if ( $clan = $this->clan )
		{
			return $clan->can_leave($this);
		}
		
		return false;
	}
	
	/**
	 * Se saca al personaje de su grupo
	 */
	public function leave_clan()
	{
		if ( $clan = $this->clan )
		{
			$clan->leave($this);
		}
	}
	
	/**
	 * Verificamos si personaje puede borrar el grupo en el que esta
	 * @return boolean
	 */
	public function can_delete_clan()
	{
		if ( $clan = $this->clan )
		{
			return $clan->can_delete($this);
		}
		
		return false;
	}
	
	/**
	 * Borramos el clan en el que esta el personaje
	 */
	public function delete_clan()
	{
		if ( $clan = $this->clan )
		{
			$clan->delete();
		}
	}
	
	/**
	 * Verificamos si personaje puede sacar a otro de su grupo
	 * @param Character $member
	 * @return boolean
	 */
	public function can_kick_clan_member(Character $member)
	{
		if ( $clan = $this->clan )
		{
			return $clan->can_kick_member($this, $member);
		}
		
		return false;
	}
	
	/**
	 * Sacamos personaje del grupo
	 * @param Character $member
	 */
	public function kick_clan_member(Character $member)
	{
		if ( $clan = $this->clan )
		{
			$clan->kick_member($this, $member);
		}
	}

	public function give_full_activity_bar_reward()
	{
		$xpAmount = (int) ($this->level / 3) * $this->get_xp_quest_rate();
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

		Message::activity_bar_reward($this, $rewards);
	}

	/**
	 * Damos recompensa del dia al personaje
	 * 
	 * @param boolean $checkBefore True para primero verificar si la 
	 *							   condicion se cumple
	 */
	public function give_logged_of_day_reward($checkBefore = false)
	{
		if ( $checkBefore && ! $this->check_logged_of_day() )
		{
			return;
		}
		
		$this->add_coins(mt_rand($this->level * 10, $this->level * 20));
		Event::fire('loggedOfDayReward', array($this));
		$this->last_logged = time();
		$this->save();
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

    /**
     * 
     * @param CharacterItem $characterItem
     * @param integer $slot Slot servido para dejar el objeto (util cuando se
     * quiere equipar y debe desequiparse automaticamente primero, se usa
     * el slot de lo que se quiere equipar)
     * @return boolean
     */
	public function unequip_item(CharacterItem $characterItem, $slot = 0)
	{
		if (! $this || ! $characterItem) {
			return false;
		}

        if ($slot) {
            $emptySlot = $slot;
        } else {
            $emptySlot = $this->empty_slot();
        }

		if (! $emptySlot) {
            return false;
        }
        
        $characterItem->location = 'inventory';
        $characterItem->slot = $emptySlot;

        $this->update_extra_stat($characterItem->item->to_array(), false);
        $characterItem->remove_skills();
        
        $characterItem->save();

        Event::fire('unequipItem', array($characterItem));

		return true;
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
					if ( ! $this->unequip_item($lrhand, $characterItem->slot) )
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
					if ( ! $this->unequip_item($lhand, $characterItem->slot) )
					{
						return 'No tienes espacio en el inventario para desequiparte el objeto que ya tienes en la mano izquierda.';
					}
				}

				if ( $rhand )
				{
					if ( ! $this->unequip_item($rhand, $characterItem->slot) )
					{
						return 'No tienes espacio en el inventario para desequiparte el objeto que ya tienes en la mano derecha.';
					}
				}
				break;
		}

		$equippedItem = $this->get_item_from_body_part($itemBodyPart);

		if ( $equippedItem )
		{
			if ( ! $this->unequip_item($equippedItem, $characterItem->slot) )
			{
				return 'No tienes espacio en el inventario para desequiparte el objeto que ya tienes equipado.';
			}
		}
		
		$this->update_extra_stat($item->to_array(), true);

		$characterItem->location = $itemBodyPart;
		$characterItem->slot = 0;
		$characterItem->save();
		
		Event::fire('equipItem', array($characterItem));

		return true;
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
	
    /**
     * 
     * @param array $select
     * @return Character
     */
	public function get_logged($select = array())
	{
		return static::get_character_of_logged_user($select);
	}

	public function battle_against($target, $pair = null)
	{
		return new Battle($this, $target, $pair);
	}

	public function give_explore_reward($reward)
	{
		$this->add_coins($reward * $this->get_coins_rate());
	}

	public function get_link()
	{
        $href = URL::to_route("get_authenticated_character_show", array(
            $this->name
        ));
        
		return "<a href='{$href}'>{$this->name}</a>";
	}
    
    public function get_final_strength()
    {
        if (! $this->finalStrength) {
            $extra = 0;

            if ($this->has_second_mercenary()) {
                $extra = $this->get_second_mercenary()->stat_strength;
            }
            
            $this->finalStrength = 
                $this->stat_strength + $this->stat_strength_extra + $extra;
        }
        
        return $this->finalStrength;
    }
    
    public function get_final_dexterity()
    {
        if (! $this->finalDexterity) {
            $extra = 0;

            if ($this->has_second_mercenary()) {
                $extra = $this->get_second_mercenary()->stat_dexterity;
            }
            
            $this->finalDexterity = 
                $this->stat_dexterity + $this->stat_dexterity_extra + $extra;
        }
        
        return $this->finalDexterity;
    }
    
    public function get_final_resistance()
    {
        if (! $this->finalResistance) {
            $extra = 0;

            if ($this->has_second_mercenary()) {
                $extra = $this->get_second_mercenary()->stat_resistance;
            }
            
            $this->finalResistance = 
                $this->stat_resistance + $this->stat_resistance_extra + $extra;
        }
        
        return $this->finalResistance;
    }
    
    public function get_final_magic()
    {
        if (! $this->finalMagic) {
            $extra = 0;

            if ($this->has_second_mercenary()) {
                $extra = $this->get_second_mercenary()->stat_magic;
            }
            
            $this->finalMagic = 
                $this->stat_magic + $this->stat_magic_extra + $extra;
        }
        
        return $this->finalMagic;
    }
    
    public function get_final_magic_skill()
    {
        if (! $this->finalMagicSkill) {
            $extra = 0;

            if ($this->has_second_mercenary()) {
                $extra = $this->get_second_mercenary()->stat_magic_skill;
            }
            
            $this->finalMagicSkill = 
                $this->stat_magic_skill + $this->stat_magic_skill_extra + $extra;
        }
        
        return $this->finalMagicSkill;
    }
    
    public function get_final_magic_resistance()
    {
        if (! $this->finalMagicResistance) {
            $extra = 0;

            if ($this->has_second_mercenary()) {
                $extra = $this->get_second_mercenary()->stat_magic_resistance;
            }
            
            $this->finalMagicResistance = 
                $this->stat_magic_resistance + 
                $this->stat_magic_resistance_extra + 
                $extra;
        }
        
        return $this->finalMagicResistance;
    }

    /**
     * @deprecated
     * @return array
     */
	public function get_stats()
	{
		$stats = array();

		$stats['stat_strength'] = $this->stat_strength + $this->stat_strength_extra;
		$stats['stat_dexterity'] = $this->stat_dexterity + $this->stat_dexterity_extra;
		$stats['stat_resistance'] = $this->stat_resistance + $this->stat_resistance_extra;
		$stats['stat_magic'] = $this->stat_magic + $this->stat_magic_extra;
		$stats['stat_magic_skill'] = $this->stat_magic_skill + $this->stat_magic_skill_extra;
		$stats['stat_magic_resistance'] = $this->stat_magic_resistance + $this->stat_magic_resistance_extra;

		if ( $this->has_second_mercenary() )
		{
			$second_mercenary = Item::find($this->second_mercenary);

			if ( $second_mercenary )
			{
				$stats['stat_strength'] += $second_mercenary->stat_strength;
				$stats['stat_dexterity'] += $second_mercenary->stat_dexterity;
				$stats['stat_resistance'] += $second_mercenary->stat_resistance;
				$stats['stat_magic'] += $second_mercenary->stat_magic;
				$stats['stat_magic_skill'] += $second_mercenary->stat_magic_skill;
				$stats['stat_magic_resistance'] += $second_mercenary->stat_magic_resistance;
			}
		}

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
		$coins = $this->items()->select(array('id', 'count'))->where('item_id', '=', Config::get('game.coin_id'))->first();
		
		if ( ! $coins )
		{
			$coins = new CharacterItem();
			
			$coins->owner_id = $this->id;
			$coins->item_id = Config::get('game.coin_id');
			
			$coins->save();
		}
		
		return $coins;
	}
	
	/**
	 * Verificamos si personaje puede agregar atributos
	 * @param string $stat stat_strength|stat_dexterity|...
	 * @param integer $amount
	 */
	public function can_add_stat($stat, $amount)
	{
		if ( !in_array($stat, array('stat_strength', 'stat_dexterity', 'stat_resistance', 'stat_magic', 'stat_magic_skill', 'stat_magic_resistance')) )
		{
			return false;
		}
		
		if ( $this->points_to_change <= 0 )
		{
			return false;
		}
		
		if ( $this->get_coins()->count < $this->get_stat_price($stat) * $amount )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Agregamos atributos al personaje
	 * @param string $stat stat_strength|stat_dexterity|...
	 * @param integer $amount
	 */
	public function add_stat($stat, $amount)
	{
		if ( !in_array($stat, array('stat_strength', 'stat_dexterity', 'stat_resistance', 'stat_magic', 'stat_magic_skill', 'stat_magic_resistance')) )
		{
			return;
		}
		
		$this->{$stat} += $amount;
		$this->points_to_change -= $amount;
		
		$this->add_coins(-($this->get_stat_price($stat) * $amount));
		
		$this->save();
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

		return Item::where('level', '>=', $this->level - 5)
                   ->where('level', '<=', $this->level + 5)
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
	 * Intentamos dar mercenario.
	 * 
	 * @param Item $mercenary
	 * @return string|boolean Se devuelve el mensaje de error o true si todo esta bien
	 */
	public function give_mercenary(Item $mercenary)
	{
		if ( $mercenary->type != "mercenary" )
		{
			return "Ese no es un mercenario";
		}
		
		if ( $mercenary->level > $this->level )
		{
			return "El mercenario requiere mas nivel";
		}
		
		if ( $mercenary->zone_to_explore && $mercenary->time_to_appear )
		{
			$hasExploredTime = $this->exploring_times()
									->where_zone_id($mercenary->zone_to_explore)
									->where("time", ">=", $mercenary->time_to_appear)
									->take(1)
									->count();

			if ( ! $hasExploredTime )
			{
				return "Aun necesitas explorar mas para poder adquirir ese mercenario";
			}
		}
		
		$slot = $this->get_mercenary();
		
		if ( $slot )
		{
			// Vamos a reemplazar, asi que sacamos stats anteriores
			$this->update_extra_stat($slot->item->to_array(), false);
		}
		else
		{
			$slot = new CharacterItem(array(
				"owner_id" => $this->id,
				"count"    => 1,
				"location" => "mercenary"
			));
		}
		
		$slot->item_id = $mercenary->id;
		$slot->save();
		
		$this->update_extra_stat($mercenary->to_array(), true);
		
		return true;
	}
	
	/**
	 * Obtenemos el limite de la mochila
	 * @return integer
	 */
	public function get_bag_limit()
	{	
		return (int) ($this->xp_next_level * Config::get('game.bag_size'));
	}
	
	/**
	 * Verificamos si puede agregar $amount pociones a mochila
	 * Se recuerda, mochila != inventario
	 * Mochila es un limitador especifico para pociones
	 * 
	 * @param Item $consumible
	 * @param integer $amount
	 * @return boolean
	 */
	public function can_add_to_bag(Item $consumible, $amount)
	{
		if ( $consumible->type != "consumible" )
		{
			return false;
		}
		
		if ( $amount <= 0 )
		{
			return false;
		}
		
		$skills = $this->get_non_clan_skills()->get();
		$skillsCount = 0;
		$time = time();

		foreach ( $skills as $skill )
		{
			// Solo se suma si no ha pasado
			// la mitad de la duracion
			if ( $skill->end_time - $time > $skill->duration * 60 / 2 )
			{
				$skillsCount += $skill->amount;
			}
		}

		// Objetos que no se cuentan
		$invalidItems = array(
			Config::get('game.coin_id'), 
			Config::get('game.xp_item_id')
		);

		$characterItems = $this->items()
							   ->join('items as item', 'item.id', '=', 'character_items.item_id')
							   ->where_not_in('item_id', $invalidItems)
							   ->where_location('inventory')
							   ->where_class('consumible')
							   ->select(array('count'))
							   ->get();
		$characterItemAmount = 0;

		foreach ( $characterItems as $characterItem )
		{
			$characterItemAmount += $characterItem->count;
		}

		return $characterItemAmount + $skillsCount + $amount < $this->get_bag_limit();
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

	/**
	 * Verificamos si personaje puede batallar
	 * 
	 * @return string|boolean
	 */
	public function can_fight()
	{
		if ( $this->has_skill(Config::get('game.stun_skill')) )
		{
			return "No puedes batallar mientras estas aturdido";
		}
		
		if ( $this->has_skill(Config::get('game.confusion_skill')) )
		{
			return "No puedes atacar aun, ¡estas confundido!";
		}
		
		if ( $this->is_traveling )
		{
			return "No puedes batallar mientras estas viajando";
		}
		
		if ( $this->activities()->take(1)->count() == 1 )
		{
			return "No puedes batallar mientras aun estes realizando otras actividades";
		}
		
		return true;
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

	/**
	 * Verificamos si personaje puede atacar a objetivo
	 * 
	 * @param Attackable $target
	 * @return string|boolean
	 */
	public function can_attack(Unit $target)
	{
		if ( ! $target )
		{
			return "El objetivo no existe";
		}
		
		if ( $target instanceof Character )
		{
			if ( Tournament::is_active() )
			{
				if ( $this->registered_in_tournament != $target->registered_in_tournament )
				{
					return "Solamente puedes atacar a personajes que tengan tu mismo estado en el torneo (registrado o no-registrado)";
				}

				if ( $this->clan_id && $this->registered_in_tournament && $this->clan_id == $target->clan_id )
				{
					return "No puedes atacar a miembros de tu grupo cuando hay un torneo activo";
				}
			}
			
			if ( $target->has_protection($this) )
			{
				return "No puedes atacar a ese objetivo mientras tenga proteccion";
			}
			
			if ( $target->is_traveling )
			{
				return "No puedes atacar a un objetivo que esta viajando";
			}
			
			if ( $this->id == $target->id )
			{
				return "¡No te puedes atacar a ti mismo!";
			}
		}

		if ( $target->zone_id != $this->zone_id )
		{
			return "Solamente puedes atacar objetivos que esten en tu misma zona";
		}

		return true;
	}
	
	/**
	 * Se intenta batallar contra un objetivo valido. Si la batalla
	 * no puede ser, se devuelve un mensaje de error, de lo contrario
	 * se devuelve la batalla (instancia Battle)
	 * 
	 * @param Unit $target
	 * @param Character $pair
	 * @return string|Battle
	 */
	public function battle_or_error(Unit $target, Character $pair = null)
	{
		if ( ! $target->is_attackable() )
		{
			return "Ese objeto no puede ser atacado";
		}
		
		if ( $pair )
		{
			if ( $target instanceof Monster )
			{
				return "No puedes atacar en parejas a un monstruo";
			}
			
			if ( ! $this->can_attack_in_pairs() )
			{
				return "Aun no puedes atacar en parejas";
			}
			
			$message = $this->can_attack_with($pair);
			
			if ( is_string($message) )
			{
				return $message;
			}
		}
		
		$message = $this->can_fight();
		
		if ( is_string($message) )
		{
			return $message;
		}
		
		if ( $target instanceof Character )
		{
			$message = $this->can_attack($target);
			
			if ( is_string($message) )
			{
				return $message;
			}
		}
		
		return $this->battle_against($target, $pair);
	}
	
	/**
	 * @deprecated
	 */
	public function after_battle()
	{
        $baseTime = Config::get('game.battle_rest_time');
        $restTime = min(
            $baseTime,
            $baseTime * (($this->level + $this->xp + $this->xp_next_level / 2) / 100) + 5
        );
        
		$characterActivity = new CharacterActivity();

		$characterActivity->character_id = $this->id;
		$characterActivity->name = 'battlerest';
		$characterActivity->end_time = time() + $restTime;

		$characterActivity->save();
	}

	public function after_dungeon(Dungeon $dungeon, $level)
	{
		$characterActivity = new CharacterActivity();

		$characterActivity->character_id = $this->id;
		$characterActivity->name = 'battlerest';
		$characterActivity->end_time = time() + $dungeon->rest_time * ($level / 2 + 0.5);

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
			
			$this->add_coins(-Config::get('game.travel_cost'));

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
		// Si es mas de 30 minutos, entonces llenamos barra de actividad
		if ( $time / 60 >= 30 )
		{
			ActivityBar::add($character, 2);
		}
		
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
	 * Verificamos si el personaje está habilitado
	 * para viajar
	 *
	 * @param Zone|null $zone Pasamos instancia de Zone en caso de querer verificar
	 * tambien si puede viajar a la misma
	 * @return string|boolean True si puede, de lo contrario el mensaje de error
	 */
	public function can_travel($zone = null)
	{
		if ( $zone instanceof Zone )
		{
			if ( $this->zone_id == $zone->id )
			{
				return 'Ya te encuentras en esa zona';
			}
			
			if ( $this->level < $zone->min_level )
			{
				return 'Esa zona requiere que tengas mas nivel';
			}
		}
		
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
		if ( $this->get_coins()->count < Config::get('game.travel_cost') )
		{
			return 'No tienes suficientes monedas.';
		}

		return true;
	}
    
    /**
     * Query para obtener el usuario de ironfist del personaje
     * @return Eloquent
     */
    public function ironfist_user()
    {
        return $this->belongs_to("IronFistUser", "user_id");
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

	/**
	 * Obtenemos todas las quest del personaje con una accion especifica
	 *
	 * @param $action
	 * @return Eloquent
	 */
	public function quests_with_action($action)
	{
		return $this->quests()
					->join('quest_npcs', 'quest_npcs.quest_id', '=', 'character_quests.quest_id')
					->where('action', '=', $action)
					->select(array('character_quests.*'));
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

	public function dungeons()
	{
		return $this->has_many('CharacterDungeon', 'character_id');
	}

	public function activity_bar()
	{
		return $this->has_one('ActivityBar', 'character_id');
	}
	
	/**
	 * Asignamos caracteristicas del personaje desde array
	 * @param array $characteristics
	 */
	public function set_characteristics_from_array(array $newCharacteristics)
	{
        $finalCharacteristics = array();
        $characteristics = Characteristic::get_all();
        
        foreach ($newCharacteristics as $characteristic) {
            // Todas las caracteristicas vienen en grupos
            // (por ejemplo energetico y perezoso estan juntos)
            foreach ($characteristics as $key => $pack) {
                // Iteramos sobre las caracteristicas de un grupo
                foreach ($pack as $instance) {
                    // Si una de las nuevas caracteristicas coincide con uno 
                    // de los elementos del grupo, entonces quitamos el mismo
                    // del array de todas las caracteristicas para que se eviten
                    // combinaciones invalidas (por ejemplo, asignar energetico
                    // y perezoso no es valido, los dos estan en el mismo grupo)
                    if ($characteristic->get_name() == $instance->get_name()) {
                        $finalCharacteristics[] = $instance->get_name();
                        unset($characteristics[$key]);
                        
                        break;
                    }
                }
            }
        }
        
        // Solamente se aceptan 5 caracteristicas
        if (count($finalCharacteristics) != 5) {
            return;
        }
        
        $this->characteristics = strtolower(implode(",", $finalCharacteristics));
        
		if ($this->has_characteristic(Characteristic::CLUMSY)) {
			$this->luck += 6;
		}
		
		$this->save();
	}
	
	public function save()
	{
		if ( ! $this->exists )
		{
			switch ( $this->race )
			{
				case 'dwarf':
					$this->regeneration_per_second = 0.25;
					$this->evasion = -1;
					$this->critical_chance = 3;
					$this->attack_speed = -2;
					$this->magic_defense = 3;
					$this->physical_defense = 5;
					$this->magic_damage = -10;
					$this->physical_damage = 15;
					$this->luck = 5;

					break;

				case 'elf':
					$this->regeneration_per_second = 0.14;
					$this->evasion = 2;
					$this->critical_chance = 5;
					$this->attack_speed = 3;
					$this->magic_defense = 2;
					$this->physical_defense = 2;
					$this->magic_damage = 5;
					$this->physical_damage = 10;
					$this->luck = 5;

					break;

				case 'drow':
					$this->regeneration_per_second = 0.12;
					$this->evasion = -1;
					$this->critical_chance = 6;
					$this->attack_speed = 2;
					$this->magic_defense = 5;
					$this->physical_defense = 1;
					$this->magic_damage = 15;
					$this->physical_damage = -5;
					$this->luck = 5;

					break;

				case 'human':
					$this->regeneration_per_second = 0.19;
					$this->evasion = 1;
					$this->critical_chance = 2;
					$this->attack_speed = 1;
					$this->magic_defense = 1;
					$this->physical_defense = 3;
					$this->magic_damage = 1;
					$this->physical_damage = 6;
					$this->luck = 5;

					break;
			}
		}
		
		return parent::save();
	}
}