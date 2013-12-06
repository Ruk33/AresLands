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
				$price = $this->stat_strength * $this->level * Config::get('game.strength_price_multiplier');
				break;

			case 'stat_dexterity':
				$price = $this->stat_dexterity * $this->level * Config::get('game.dexterity_price_multiplier');
				break;

			case 'stat_resistance':
				$price = $this->stat_resistance * $this->level * Config::get('game.resistance_price_multiplier');
				break;

			case 'stat_magic':
				$price = $this->stat_magic * $this->level * Config::get('game.magic_price_multiplier');
				break;

			case 'stat_magic_skill':
				$price = $this->stat_magic_skill * $this->level * Config::get('game.magic_skill_price_multiplier');
				break;

			case 'stat_magic_resistance':
				$price = $this->stat_magic_resistance * $this->level * Config::get('game.magic_resistance_price_multiplier');
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
		
		$this->save();
	}
	
	public function set_xp($value)
	{
		if ( $value >= $this->xp_next_level )
		{
			$this->level++;
			$this->xp_next_level = $this->xp_next_level + 10 * $this->level;

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
						$orb->give_to_random();
					}
				}
			}

			/* 
			 *	Aumentamos la vida
			 */
			$this->max_life = $this->max_life + $this->level * 40;

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
			
			foreach ( $skills as $skill )
			{
				$skill->cast($this, $this, $amount);
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
			return 'Ese objeto no es de tipo consumible o ya alcanzaste el límite de habilidades activas.';
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
		if ( $value <= 0 )
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
		$xpAmount = $this->level / 2 + 5;
		$coinsAmount = $this->level * 15;

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
			//return Character::select($select)->where('user_id', '=', Auth::user()->id)->first();
			return $user->character()->select($select)->first();
		}

		//return Character::where('user_id', '=', Auth::user()->id)->first();
		return $user->character;
	}

	public function battle_against($target)
	{
		if ( ! $target || ! $this )
		{
			return;
		}

		$battle = new Battle($this, $target);
		return $battle;
	}

	public function give_explore_reward($reward)
	{
		$this->add_coins($reward * Config::get('game.coins_rate'));
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
			$characterItem = $this->items()->where('item_id', '=', $item->id)->get();
			
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
		return $this->is_traveling == false && $this->is_exploring == false;
	}

	public function can_fight()
	{
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
		return $this->has_protection($attacker) == false;
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
		$characterActivity->data = array( 'reward' => ($this->level * ($time/60)/2) * Config::get('game.explore_reward_rate'), 'time' => $time );
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
		//if ( $this->activities()->where('name', '=', 'travel')->first() )
		if ( $this->is_traveling )
		{
			return 'Ya estás viajando, no puedes volver a hacerlo.';
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