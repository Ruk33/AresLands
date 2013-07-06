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

	public function empty_slot()
	{
		for ( $i = 1, $max = 6; $i <= $max; $i++ )
		{
			if ( $this->items()->where('slot', '=', $i)->count() == 0 )
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

	public function unequip_item(CharacterItem $item)
	{
		if ( ! $item )
		{
			return false;
		}

		$emptySlot = $this->empty_slot();

		if ( $emptySlot )
		{
			$item->location = 'inventory';
			$item->slot = $emptySlot;

			$item->save();

			return true;
		}

		return false;
	}

	public function equip_weapon(CharacterItem $item)
	{
		if ( ! $this || ! $item )
		{
			return false;
		}

		if ( $item->item->body_part == 'lrhand' )
		{
			$equippedShield = $this->get_equipped_shield();

			if ( $equippedShield )
			{
				if ( ! $this->unequip_item($equippedShield) )
				{
					return false;
				}
			}
		}

		$equippedWeapon = $this->get_equipped_weapon();

		if ( $equippedWeapon )
		{
			if ( ! $this->unequip_item($equippedWeapon) )
			{
				return false;
			}
		}

		$item->location = $item->item->body_part;
		$item->slot = 0;
		$item->save();

		return true;
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

	public function battle_against_monster(Npc $monster)
	{
		/*
		 *	No queremos trabajar con null
		 *	we hate null >:v
		 */
		if ( ! $monster || ! $this )
		{
			return;
		}

		$fighter_one['object'] = $this;
		$fighter_one['stats'] = $this->get_stats();
		$fighter_one['object']->current_life += $fighter_one['stats']['stat_life'] * 1.25;
		$fighter_one['is_warrior'] = $this->stat_strength > $this->stat_magic;

		/*
		 *	Daños
		 */
		$fighter_one['min_damage'] = $fighter_one['stats']['p_damage'] + mt_rand(5, 15);
		$fighter_one['max_damage'] = $fighter_one['min_damage'] * 1.25 + mt_rand(5, 15);
		$fighter_one['average_damage'] = ($fighter_one['max_damage'] + $fighter_one['min_damage']) / 2  + mt_rand(5, 15);

		/*
		 *	Defensas
		 */
		$fighter_one['max_defense'] = ( $fighter_one['is_warrior'] ) ? $fighter_one['stats']['p_defense'] : $fighter_one['stats']['m_defense'];
		$fighter_one['max_defense'] = $fighter_one['max_defense'] * 1.25;

		$fighter_one['min_defense'] = ( $fighter_one['is_warrior'] ) ? $fighter_one['stats']['p_defense'] : $fighter_one['stats']['m_defense'];
		$fighter_one['min_defense'] = $fighter_one['min_defense'] * 0.75;

		$fighter_one['normal_defense'] = ( $fighter_one['is_warrior'] ) ? $fighter_one['stats']['p_defense'] : $fighter_one['stats']['m_defense'];

		/*
		 *	Mounstro
		 */
		$fighter_two['object'] = $monster;
		$fighter_two['stats'] = $monster->get_stats();
		$fighter_two['object']->current_life = $monster->life;

		$fighter_two['is_warrior'] = $monster->stat_strength > $monster->stat_magic;

		$fighter_two['min_damage'] = $monster->p_damage + mt_rand(5, 15);
		$fighter_two['max_damage'] = $fighter_two['min_damage'] * 1.25 + mt_rand(5, 15);
		$fighter_two['average_damage'] = ($fighter_two['max_damage'] + $fighter_two['min_damage']) / 2 + mt_rand(5, 15);

		$fighter_two['max_defense'] = ( $fighter_two['is_warrior'] ) ? $fighter_two['stats']['p_defense'] : $fighter_two['stats']['m_defense'];
		$fighter_two['max_defense'] = $fighter_two['max_defense'] * 1.25;

		$fighter_two['min_defense'] = ( $fighter_two['is_warrior'] ) ? $fighter_two['stats']['p_defense'] : $fighter_two['stats']['m_defense'];
		$fighter_two['min_defense'] = $fighter_two['min_defense'] * 0.75;

		$fighter_two['normal_defense'] = ( $fighter_two['is_warrior'] ) ? $fighter_two['stats']['p_defense'] : $fighter_two['stats']['m_defense'];	

		/*
		 *	Definimos aleatoriamente quién
		 *	golpeará primero
		 */
		$attacker = ( mt_rand( 1, 2) == 1 ) ? $fighter_one : $fighter_two;
		$defenser = ( $attacker == $fighter_one ) ? $fighter_two : $fighter_one;

		$messages = array(
			'<b>%1$s</b> logra asestar un gran golpe a <b>%2$s</b>. <b>%2$s</b> no puede evitar soltar un pequeño alarido',
			'<b>%1$s</b> golpea con majestuocidad a <b>%2$s</b>. <b>%2$s</b> se queja',
			'<b>%1$s</b> lanza un feroz ataque a <b>%2$s</b> que sufre algunas heridas',
			'<b>%1$s</b> ataca salvajemente a <b>%2$s</b> que sufre dolorosas heridas',
			'<b>%1$s</b> se mueve ágil y velozmente hacia <b>%2$s</b> para propinarle un gran golpe',
			'<b>%2$s</b> se ve algo cansado, intenta esquivar pero el ataque <b>%1$s</b> lo alcanza',
		);

		/*
		 *	Guardaremos el resúmen
		 *	de la batalla
		 */
		$message = '';

		while ( $fighter_two['object']->current_life > 0 && $fighter_two['object']->current_life > 0 )
		{
			// ----------------------------------------------
			// CALCULAMOS EL DAÑO
			// ----------------------------------------------

			/*
			 *	Si tiene suerte, entonces
			 *	el daño será el máximo posible
			 */
			$damage = ( mt_rand( 0, 100) <= $attacker['stats']['stat_luck'] / 2 ) ? $attacker['max_damage'] : false;

			/*
			 *	Si damage está en false
			 *	entonces trataremos de buscar 
			 *	el siguiente daño (average)
			 */
			$damage = ( ! $damage && mt_rand( 0, 100) <= $attacker['stats']['stat_luck'] ) ? $attacker['average_damage'] : false;
			
			/*
			 *	Verificamos si tuvo suerte
			 *	con alguno de los daños anteriores
			 */
			if ( ! $damage )
			{
				/*
				 *	Si no tuvo, entonces daño mínimo
				 */
				$damage = $attacker['min_damage'];
			}

			// ----------------------------------------------
			// FIN CALCULO DAÑO
			// ----------------------------------------------

			// ----------------------------------------------
			// CALCULAMOS EFECTO DEFENSA
			// ----------------------------------------------

			/*
			 *	Si tiene suerte, entonces
			 *	será la máxima defensa
			 */
			$defense = ( mt_rand( 0, 100) <= $defenser['stats']['stat_luck'] ) ? $defenser['max_defense'] : false;

			/*
			 *	Si tiene mala suerte
			 *	la defensa será la menor
			 */
			$defense = ( ! $defense && mt_rand( 0, 100) <= $defenser['stats']['stat_luck'] / 2 ) ? $defenser['min_defense'] : false;
			
			/*
			 *	Si no hay defensa...
			 */
			if ( ! $defense )
			{
				/*
				 *	Defenderá normal
				 */
				$defense = $defenser['normal_defense'];
			}

			// ----------------------------------------------
			// FIN CALCULO EFECTO DEFENSA
			// ----------------------------------------------

			// ----------------------------------------------
			// VALOR DE IMPACTO
			// ----------------------------------------------
			
			/*
			 *	Evitamos el error division by zero
			 */
			$normal_defense = ( $defenser['normal_defense'] == 0 ) ? 1 : $defenser['normal_defense'];
			$real_damage = $damage - (($attacker['min_damage'] / 2) * $defense / $normal_defense) / 100;
			$real_damage = round($real_damage, 2);

			// ----------------------------------------------
			// FIN VALOR DE IMPACTO
			// ----------------------------------------------
			
			/*
			 *	Golpeamos
			 */
			$defenser['object']->current_life -= $real_damage;

			// agregamos cd
			//$attacker['actual_cd'] = $attacker['cd'];

			/*
			 *	Registramos el movimiento
			 */
			$message .= sprintf($messages[mt_rand(0, 5)], $attacker['object']->name, $defenser['object']->name) . ' (daño: '. $real_damage .', defendido: '. round($damage-$real_damage, 2) .')<br>';

			
			/* 
			 *	Se invierten los papeles, 
			 *	ahora el defensor pasa a ser 
			 *	el atacante y el atacante el defensor
			 */
			$tmp = $attacker;
			
			$attacker = $defenser;
			$defenser = $tmp;
		}

		$winner = null;

		/*
		 *	Vemos quién es el ganador
		 */
		if ( $fighter_one['object']->current_life > 0 )
		{
			$winner = $fighter_one;
		}

		if ( $fighter_two['object']->current_life > 0 )
		{
			$winner = $fighter_two;
		}

		if ( $winner )
		{
			$winner = $winner['object'];
		}

		/*
		 *	Volvemos la vida a la normalidad
		 *	(sacando el bonus que da el atributo life)
		 */
		$fighter_one['object']->current_life -= $fighter_one['stats']['stat_life'] * 1.25;

		/*
		 *	Evitamos que tengan vida por debajo de 0
		 */
		if ( $fighter_one['object']->current_life <= 0 )
		{
			$fighter_one['object']->current_life = 1;
		}
		$fighter_one['object']->save();

		Event::fire('pveBattle', array($this, $monster, $winner));

		Message::exploration_finished($this, $monster, $message, $winner);
	}

	public function give_explore_reward($reward)
	{
		$characterCoins = $this->get_coins();

		if ( $characterCoins )
		{
			$characterCoins->count += $reward * Config::get('game.coins_rate');
		}
		else
		{
			$characterCoins = new CharacterItem();

			$characterCoins->owner_id = $character->id;
			$characterCoins->item_id = Config::get('game.coin_id');
			$characterCoins->location = 'none';
			$characterCoins->count = $reward * Config::get('game.coins_rate');
		}
		
		$characterCoins->save();
	}

	public function get_link()
	{
		return '<a href="' . URL::to('authenticated/character/' . $this->name ) . '">' . $this->name . '</a>';
	}

	public function get_stats()
	{
		$stats = array();

		$positive_stats = $this->get_bonifications(true);
		$negative_stats = $this->get_bonifications(false);

		$stats['p_damage'] = $positive_stats['p_damage'] - $negative_stats['p_damage'];
		$stats['m_damage'] = $positive_stats['m_damage'] - $negative_stats['m_damage'];

		$stats['p_defense'] = $positive_stats['p_defense'] - $negative_stats['p_defense'];
		$stats['m_defense'] = $positive_stats['m_defense'] - $negative_stats['m_defense'];

		$stats['stat_life'] = $this->stat_life + $positive_stats['stat_life'] - $negative_stats['stat_life'];
		$stats['stat_dexterity'] = $this->stat_dexterity + $positive_stats['stat_dexterity'] - $negative_stats['stat_dexterity'];
		$stats['stat_magic'] = $this->stat_magic + $positive_stats['stat_magic'] - $negative_stats['stat_magic'];
		$stats['stat_strength'] = $this->stat_strength + $positive_stats['stat_strength'] - $negative_stats['stat_strength'];
		$stats['stat_luck'] = $this->stat_luck + $positive_stats['stat_luck'] - $negative_stats['stat_luck'];

		return $stats;
	}

	/**
	 *	@param <bool> $positive Si es true, traemos bonificaciones positivas, si es false negativas
	 *	@return <array>
	 */
	public function get_bonifications($positive = true)
	{
		$characterItems = null;
		$characterSkills = null;

		$item = null;
		$skill = null;

		$bonification = array();

		$bonification['p_damage'] = 0;
		$bonification['m_damage'] = 0;

		$bonification['p_defense'] = 0;
		$bonification['m_defense'] = 0;

		$bonification['stat_life'] = 0;
		$bonification['stat_dexterity'] = 0;
		$bonification['stat_magic'] = 0;
		$bonification['stat_strength'] = 0;
		$bonification['stat_luck'] = 0;

		/*
		 *	Obtenemos todos los objetos
		 *	que no estén en inventario por supuesto
		 */
		$characterItems = $this->items()->select(array('item_id'))->where('location', '<>', 'inventory')->get();

		foreach ( $characterItems as $characterItem )
		{
			$item = $characterItem->item()->select(array(
				'p_damage', 
				'm_damage', 
				'm_defense', 
				'p_defense', 
				'stat_life', 
				'stat_dexterity',
				'stat_magic',
				'stat_strength',
				'stat_luck'
			))->first();

			if ( $positive )
			{
				$bonification['p_damage']		+= ( $item->p_damage > 0 )			? $item->p_damage : 0;
				$bonification['m_damage']		+= ( $item->m_damage > 0 )			? $item->m_damage : 0;

				$bonification['p_defense']		+= ( $item->p_defense > 0 )			? $item->p_defense : 0;
				$bonification['m_defense']		+= ( $item->m_defense > 0 )			? $item->m_defense : 0;

				$bonification['stat_life']		+= ( $item->stat_life > 0 )			? $item->stat_life : 0;
				$bonification['stat_dexterity']	+= ( $item->stat_dexterity > 0 )	? $item->stat_dexterity : 0;
				$bonification['stat_magic']		+= ( $item->stat_magic > 0 )		? $item->stat_magic : 0;
				$bonification['stat_strength']	+= ( $item->stat_strength > 0 )		? $item->stat_strength : 0;
				$bonification['stat_luck']		+= ( $item->stat_luck > 0 )			? $item->stat_luck : 0;
			}
			else
			{
				$bonification['p_damage']		+= ( $item->p_damage < 0 )			? -$item->p_damage : 0;
				$bonification['m_damage']		+= ( $item->m_damage < 0 )			? -$item->m_damage : 0;

				$bonification['p_defense']		+= ( $item->p_defense < 0 )			? -$item->p_defense : 0;
				$bonification['m_defense']		+= ( $item->m_defense < 0 )			? -$item->m_defense : 0;

				$bonification['stat_life']		+= ( $item->stat_life < 0 )			? -$item->stat_life : 0;
				$bonification['stat_dexterity']	+= ( $item->stat_dexterity < 0 )	? -$item->stat_dexterity : 0;
				$bonification['stat_magic']		+= ( $item->stat_magic < 0 )		? -$item->stat_magic : 0;
				$bonification['stat_strength']	+= ( $item->stat_strength < 0 )		? -$item->stat_strength : 0;
				$bonification['stat_luck']		+= ( $item->stat_luck < 0 )			? -$item->stat_luck : 0;
			}
		}

		/*
		 *	Ahora revisamos las bonificaciones
		 *	de los skills que están activos
		 */
		$characterSkills = $this->skills()->get();

		foreach ( $characterSkills as $characterSkill )
		{
			$skill = $characterSkill->skill()->select(array('data'))->first()->data;

			if ( $positive )
			{
				$bonification['p_damage']		+= ( isset($skill['p_damage']) && $skill['p_damage'] > 0 )				? $skill['p_damage'] * $characterSkill->amount : 0;
				$bonification['m_damage']		+= ( isset($skill['m_damage']) && $skill['m_damage'] > 0 )				? $skill['m_damage'] * $characterSkill->amount : 0;

				$bonification['p_defense']		+= ( isset($skill['p_defense']) && $skill['p_defense'] > 0 )			? $skill['p_defense'] * $characterSkill->amount : 0;
				$bonification['m_defense']		+= ( isset($skill['m_defense']) && $skill['m_defense'] > 0 )			? $skill['m_defense'] * $characterSkill->amount : 0;

				$bonification['stat_life']		+= ( isset($skill['stat_life']) && $skill['stat_life'] > 0 )			? $skill['stat_life'] * $characterSkill->amount : 0;
				$bonification['stat_dexterity']	+= ( isset($skill['stat_dexterity']) && $skill['stat_dexterity'] > 0 )	? $skill['stat_dexterity'] * $characterSkill->amount : 0;
				$bonification['stat_magic']		+= ( isset($skill['stat_magic']) && $skill['stat_magic'] > 0 )			? $skill['stat_magic'] * $characterSkill->amount : 0;
				$bonification['stat_strength']	+= ( isset($skill['stat_strength']) && $skill['stat_strength'] > 0 )	? $skill['stat_strength'] * $characterSkill->amount : 0;
				$bonification['stat_luck']		+= ( isset($skill['stat_luck']) && $skill['stat_luck'] > 0 )			? $skill['stat_luck'] * $characterSkill->amount : 0;
			}
			else
			{
				$bonification['p_damage']		+= ( isset($skill['p_damage']) && $skill['p_damage'] < 0 )				? -$skill['p_damage'] * $characterSkill->amount : 0;
				$bonification['m_damage']		+= ( isset($skill['m_damage']) && $skill['m_damage'] < 0 )				? -$skill['m_damage'] * $characterSkill->amount : 0;

				$bonification['p_defense']		+= ( isset($skill['p_defense']) && $skill['p_defense'] < 0 )			? -$skill['p_defense'] * $characterSkill->amount : 0;
				$bonification['m_defense']		+= ( isset($skill['m_defense']) && $skill['m_defense'] < 0 )			? -$skill['m_defense'] * $characterSkill->amount : 0;

				$bonification['stat_life']		+= ( isset($skill['stat_life']) && $skill['stat_life'] < 0 )			? -$skill['stat_life'] * $characterSkill->amount : 0;
				$bonification['stat_dexterity']	+= ( isset($skill['stat_dexterity']) && $skill['stat_dexterity'] < 0 )	? -$skill['stat_dexterity'] * $characterSkill->amount : 0;
				$bonification['stat_magic']		+= ( isset($skill['stat_magic']) && $skill['stat_magic'] < 0 )			? -$skill['stat_magic'] * $characterSkill->amount : 0;
				$bonification['stat_strength']	+= ( isset($skill['stat_strength']) && $skill['stat_strength'] < 0 )	? -$skill['stat_strength'] * $characterSkill->amount : 0;
				$bonification['stat_luck']		+= ( isset($skill['stat_luck']) && $skill['stat_luck'] < 0 )			? -$skill['stat_luck'] * $characterSkill->amount : 0;
			}
		}

		return $bonification;
	}

	/**
	 *	Obtenemos la cantidad de monedas
	 *	en cobre de un personaje
	 *
	 *	@return <CharacterItem> 
	 */
	public function get_coins()
	{
		return $this->items()->where('item_id', '=', Config::get('game.coin_id'))->first();
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
		return $this->is_traveling == false && $this->activities()->count() == 0;
	}

	public function can_be_attacked()
	{
		return true;
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
		$characterExploringTime = $this->exploring_times()->where('zone_id', '=', $zone->id)->first();

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

	public function explore($time)
	{
		$this->is_exploring = true;
		$this->save();

		$characterActivity = new CharacterActivity();

		$characterActivity->character_id = $this->id;
		$characterActivity->name = 'explore';
		$characterActivity->data = array( 'reward' => $this->level * ($time / 60) * Config::get('game.explore_reward_rate'), 'time' => $time );
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
}