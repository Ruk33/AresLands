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

	public static function logged_user_has_character()
	{
		$user = Auth::user();

		if ( ! $user )
		{
			return false;
		}

		return Character::where('user_id', '=', $user->id)->count() != 0;
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

		$this->clan->add_xp(1);

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

	public function get_item_from_body_part($body_part)
	{
		if ( ! $this || ! $body_part )
		{
			return null;
		}

		return $this->items()->where('location', '=', $body_part)->first();
	}

	public function unequip_item(CharacterItem $item)
	{
		if ( ! $this || ! $item )
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

	public function equip_item(CharacterItem $characterItem)
	{
		if ( ! $this || ! $characterItem )
		{
			return false;
		}

		$item = $characterItem->item()->select(array('id', 'body_part', 'level'))->first();

		if ( ! $item )
		{
			return false;
		}

		if ( $item->level > $this->level )
		{
			return false;
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
						return false;
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
						return false;
					}
				}

				if ( $rhand )
				{
					if ( ! $this->unequip_item($rhand) )
					{
						return false;
					}
				}
				break;
		}

		$equippedItem = $this->get_item_from_body_part($itemBodyPart);

		if ( $equippedItem )
		{
			if ( ! $this->unequip_item($equippedItem) )
			{
				return false;
			}
		}

		$characterItem->location = $itemBodyPart;
		$characterItem->slot = 0;
		$characterItem->save();

		return true;
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

		$fighter_one = array();
		$fighter_two = array();

		// ----------------------------------------------
		// PRIMER LUCHADOR
		// ----------------------------------------------
		$fighter_one['character'] = $this;
		$fighter_one['is_player'] = true;

		$fighter_one['stats'] = $fighter_one['character']->get_stats();
		$fighter_one['character']->current_life += ($fighter_one['stats']['stat_life'] * 1.25);
		$fighter_one['cd'] = (1000 / ($fighter_one['stats']['stat_dexterity']+1))+1;
		$fighter_one['character']->current_cd = $fighter_one['cd'];
		$fighter_one['is_warrior'] = $fighter_one['character']->stat_strength > $fighter_one['character']->stat_magic;

		/*
		 *	Daños
		 */
		$fighter_one['min_damage'] = ( $fighter_one['is_warrior'] ) ? $fighter_one['stats']['stat_strength'] : $fighter_one['stats']['stat_magic'];
		$fighter_one['min_damage'] *= 0.75;

		$fighter_one['max_damage'] = ( $fighter_one['is_warrior'] ) ? $fighter_one['stats']['stat_strength'] : $fighter_one['stats']['stat_magic'];
		$fighter_one['max_damage'] *= 1.25;

		/*
		 *	Defensas
		 */
		$fighter_one['max_defense'] = ( $fighter_one['is_warrior'] ) ? $fighter_one['stats']['p_defense'] : $fighter_one['stats']['m_defense'];
		$fighter_one['max_defense'] = ( $fighter_one['max_defense'] * 1.25 > 0 ) ? $fighter_one['max_defense'] * 1.25 : 0;

		$fighter_one['min_defense'] = ( $fighter_one['is_warrior'] ) ? $fighter_one['stats']['p_defense'] : $fighter_one['stats']['m_defense'];
		$fighter_one['min_defense'] = $fighter_one['min_defense'] * 0.75;

		$fighter_one['character']->damage_done = 0;
		$fighter_one['initial_life'] = $fighter_one['character']->current_life;
		// ----------------------------------------------
		// FIN PRIMER LUCHADOR
		// ----------------------------------------------

		// ----------------------------------------------
		// SEGUNDO LUCHADOR
		// ----------------------------------------------
		$fighter_two['character'] = $target;
		$fighter_two['is_player'] = $target instanceof Character;

		$fighter_two['stats'] = $fighter_two['character']->get_stats();

		if ( ! $fighter_two['is_player'] )
		{
			$fighter_two['character']->current_life = $target->life;
		}

		$fighter_two['character']->current_life += $fighter_two['stats']['stat_life'] * 1.25;

		$fighter_two['cd'] = (1000 / ($fighter_two['stats']['stat_dexterity']+1))+1;
		$fighter_two['character']->current_cd = $fighter_two['cd'];
		$fighter_two['is_warrior'] = $fighter_two['stats']['stat_strength'] > $fighter_two['stats']['stat_magic'];

		/*
		 *	Daños
		 */
		$fighter_two['min_damage'] = ( $fighter_two['is_warrior'] ) ? $fighter_two['stats']['stat_strength'] : $fighter_two['stats']['stat_magic'];
		$fighter_two['min_damage'] *= 0.75;

		$fighter_two['max_damage'] = ( $fighter_two['is_warrior'] ) ? $fighter_two['stats']['stat_strength'] : $fighter_two['stats']['stat_magic'];
		$fighter_two['max_damage'] *= 1.25;

		/*
		 *	Defensas
		 */
		$fighter_two['max_defense'] = ( $fighter_two['is_warrior'] ) ? $fighter_two['stats']['p_defense'] : $fighter_two['stats']['m_defense'];
		$fighter_two['max_defense'] = ( $fighter_two['max_defense'] * 1.25 > 0 ) ? $fighter_two['max_defense'] * 1.25 : 0;

		$fighter_two['min_defense'] = ( $fighter_two['is_warrior'] ) ? $fighter_two['stats']['p_defense'] : $fighter_two['stats']['m_defense'];
		$fighter_two['min_defense'] = $fighter_two['min_defense'] * 0.75;

		$fighter_two['character']->damage_done = 0;
		$fighter_two['initial_life'] = $fighter_two['character']->current_life;
		// ----------------------------------------------
		// FIN SEGUNDO LUCHADOR
		// ----------------------------------------------

		$messages = array(
			'%1$s logra asestar un gran golpe a %2$s. %2$s no puede evitar soltar un pequeño alarido',
			'%1$s golpea con majestuocidad a %2$s. %2$s se queja',
			'%1$s lanza un feroz ataque a %2$s que sufre algunas heridas',
			'%1$s ataca salvajemente a %2$s que sufre dolorosas heridas',
			'%1$s se mueve ágil y velozmente hacia %2$s para propinarle un gran golpe',
			'%2$s se ve algo cansado, intenta esquivar pero el ataque %1$s lo alcanza',
		);

		/*
		 *	Guardaremos el resúmen
		 *	de la batalla
		 */
		$message = '';

		/*
		 *	Mientras los personajes que están
		 *	peleando tengan vida...
		 */
		while ( $fighter_one['character']->current_life > 0 && $fighter_two['character']->current_life > 0 )
		{			
			if ( $fighter_one['character']->current_cd <= $fighter_two['character']->current_cd )
			{
				$attacker = &$fighter_one;
				$defenser = &$fighter_two;
			}
			else
			{
				$attacker = &$fighter_two;
				$defenser = &$fighter_one;
			}

			$attacker['character']->current_cd += $attacker['cd'];

			// ----------------------------------------------
			// CALCULAMOS EL DAÑO
			// ----------------------------------------------

			$attacker['average_damage'] = mt_rand($attacker['min_damage'], $attacker['max_damage']);

			if ( $attacker['is_warrior'] && mt_rand(0, 100) <= $attacker['stats']['stat_luck'] * 0.35 )
			{
				$damage = $attacker['average_damage'] * 1.75;
			}
			elseif ( ! $attacker['is_warrior'] && mt_rand(0, 100) <= $attacker['stats']['stat_luck'] * 0.25 )
			{
				$damage = $attacker['average_damage'] * 2.75;
			}
			elseif ( mt_rand(0, 100) <= 10 )
			{
				$damage = $attacker['average_damage'] * 0.75;
			}
			else
			{
				$damage = $attacker['average_damage'];
			}

			// ----------------------------------------------
			// FIN CALCULO DAÑO
			// ----------------------------------------------

			// ----------------------------------------------
			// CALCULAMOS EFECTO DEFENSA
			// ----------------------------------------------

			$defenser['normal_defense'] = mt_rand($defenser['min_defense'], $defenser['max_defense']);

			if ( mt_rand(0, 100) <= $defenser['stats']['stat_luck'] * 0.30 )
			{
				$defense = $defenser['normal_defense'] * 1.75;
			}
			elseif ( mt_rand(0, 100) <= 10 )
			{
				$defense = $defenser['normal_defense'] * 0.75;
			}
			else
			{
				$defense = $defenser['normal_defense'];
			}

			// ----------------------------------------------
			// FIN CALCULO EFECTO DEFENSA
			// ----------------------------------------------

			// ----------------------------------------------
			// VALOR DE IMPACTO
			// ----------------------------------------------
			
			$realDamage = $damage - ($defense * 0.4);

			if ( $realDamage <= 0 )
			{
				$realDamage = 1;
			}

			// ----------------------------------------------
			// FIN VALOR DE IMPACTO
			// ----------------------------------------------
			
			/*
			 *	Golpeamos
			 */
			$defenser['character']->current_life -= $realDamage;

			$attacker['character']->damage_done += $realDamage;

			/*
			 *	Registramos el movimiento
			 */
			$message .= '<li>' . sprintf($messages[mt_rand(0, 5)], $attacker['character']->name, $defenser['character']->name) . ' (daño: '. number_format($realDamage, 2) .', defendido: '. number_format($damage - $realDamage, 2) .')</li>';
		}

		$winner = null;
		$loser = null;

		$fighter_one['damage_done'] = $fighter_one['character']->damage_done;
		$fighter_two['damage_done'] = $fighter_two['character']->damage_done;

		unset($fighter_one['character']->current_cd);
		unset($fighter_two['character']->current_cd);

		unset($fighter_one['character']->damage_done);
		unset($fighter_two['character']->damage_done);

		/*
		 *	Vemos quién es el ganador
		 */
		if ( $fighter_one['character']->current_life > 0 )
		{
			$winner = &$fighter_one;
			$loser = &$fighter_two;
		}
		else
		{
			$winner = &$fighter_two;
			$loser = &$fighter_one;
		}

		/*
		 *	Aumentamos los puntos de pvp
		 *	del ganador
		 */
		if ( $winner['is_player'] && $loser['is_player'] )
		{
			$winner['character']->pvp_points++;
		}

		/*
		 *	Volvemos la vida a la normalidad
		 *	(sacando el bonus que da el atributo life)
		 */
		$fighter_one['character']->current_life -= $fighter_one['stats']['stat_life'] * 1.25;

		if ( $fighter_two['is_player'] )
		{
			$fighter_two['character']->current_life -= $fighter_two['stats']['stat_life'] * 1.25;
		}

		/*
		 *	Evitamos que tengan vida por debajo de 0
		 */
		if ( $fighter_one['character']->current_life <= 0 )
		{
			$fighter_one['character']->current_life = 1;
		}

		if ( $fighter_two['is_player'] && $fighter_two['character']->current_life <= 0 )
		{
			$fighter_two['character']->current_life = 1;
		}

		/*
		 *	¡Experiencia!
		 *	Solo si el ganador tiene el mismo o menor nivel que el perdedor
		 */
		if ( $winner['is_player'] && $winner['character']->level <= $loser['character']->level )
		{
			if ( $loser['is_player'] )
			{
				$winner['character']->xp += 1 * Config::get('game.xp_rate');
				$winner['character']->points_to_change += 1 * Config::get('game.xp_rate');
			}
			else
			{
				$winner['character']->xp += $target->xp * Config::get('game.xp_rate');
				$winner['character']->points_to_change += $target->xp * Config::get('game.xp_rate');
			}

			/*
			 *	Si el ganador tiene menor nivel que
			 *	el perdedor, se le da mas experiencia
			 */
			if ( $winner['character']->level < $loser['character']->level )
			{
				$winner['character']->xp += 1 * Config::get('game.xp_rate');
				$winner['character']->points_to_change += 1 * Config::get('game.xp_rate');
			}
		}

		/*
		 *	El perdedor recibe experiencia (si es jugador) si o si
		 */
		if ( $loser['is_player'] )
		{
			$loser['character']->xp += 1 * Config::get('game.xp_rate');
			$loser['character']->points_to_change += 1 * Config::get('game.xp_rate');

			/*
			 *	Revisamos si el perdedor necesita
			 *	protección para evitar abusos
			 */
			if ( $winner['is_player'] && $winner['character']->level > $loser['character']->level )
			{
				AttackProtection::add($winner['character'], $loser['character'], Config::get('game.protection_time_on_lower_level_pvp'));
			}
		}

		/*
		 *	Guardamos
		 */
		$fighter_one['character']->save();

		if ( $fighter_two['is_player'] )
		{
			$fighter_two['character']->save();
		}

		/*
		 *	Ganancia de monedas
		 */
		$stolenCoins = 0;
		if ( $winner['is_player'] )
		{
			$stolenCoins = (15 + $loser['character']->level) * Config::get('game.coins_rate');

			$winnerCoins = $winner['character']->get_coins();
			$loserCoins = $loser['character']->get_coins();

			if ( $loserCoins )
			{
				$stolenCoins += $loserCoins->count * 0.10;

				$loserCoins->count -= $loserCoins->count * 0.10;

				if ( $loserCoins->count > 0 )
				{
					$loserCoins->save();
				}
				else
				{
					$loserCoins->delete();
				}
			}

			if ( $winnerCoins )
			{
				$winnerCoins->count += $stolenCoins;
			}
			else
			{
				$winnerCoins = new CharacterItem();

				$winnerCoins->item_id = Config::get('game.coin_id');
				$winnerCoins->owner_id = $winner['character']->id;
				$winnerCoins->count = $stolenCoins;
				$winnerCoins->location = 'none';
			}

			$winnerCoins->save();
		}

		/*
		 *	Creamos el mensaje de la batalla
		 */
		$message = '<ul class="unstyled">' . $message . '</ul>';
		$message = sprintf('
			<p>
				<b>Ganador:</b> %7$s
				<br>
				<b>%7$s</b> obtiene %8$d cobre (parte robada del perdedor)
			</p>

			<p>
				<b>Vida inicial de %1$s:</b> %3$d
				<br>
				<b>Vida inicial de %2$s:</b> %4$d
			</p>

			<p>
				<b>Daño realizado por %1$s:</b> %5$d
				<br>
				<b>Daño realizado por %2$s:</b> %6$d
			</p>',

			$fighter_one['character']->name,
			$fighter_two['character']->name,

			$fighter_one['initial_life'],
			$fighter_two['initial_life'],

			$fighter_one['damage_done'],
			$fighter_two['damage_done'],

			$winner['character']->name,
			$stolenCoins
		) . $message;

		/*
		 *	Orbes
		 */
		if ( $winner['is_player'] && $loser['is_player'] )
		{
			if ( $winner['character']->has_orb() )
			{
				$winnerOrbs = $winner['character']->orbs;

				foreach ( $winnerOrbs as $winnerOrb )
				{
					if ( $winnerOrb->can_be_stolen_by($loser['character']) )
					{
						$winnerOrb->failed_robbery($loser['character']);
					}
				}
			}

			if ( $loser['character']->has_orb() && $winner['character']->orbs()->count() < 2 )
			{
				$loserOrbs = $loser['character']->orbs;
				$stolenOrb = null;

				foreach ( $loserOrbs as $loserOrb )
				{
					if ( $loserOrb->can_be_stolen_by($winner['character']) )
					{
						$loserOrb->give_to($winner['character']);
						$stolenOrb = $loserOrb;
						break;
					}
				}

				if ( $stolenOrb )
				{
					$message = '<p>¡Haz robado ' . $stolenOrb->name . ' de ' . $loser['character']->name . '!</p>' . $message;
				}
			}
		}

		/*
		 *	Barra de actividad
		 */
		ActivityBar::add($this, 2);

		/*
		 *	Notificamos al atacado
		 */
		if ( $fighter_two['is_player'] )
		{
			Message::attack_report($fighter_one['character'], $fighter_two['character'], $message, $winner['character']);
			Message::defense_report($fighter_two['character'], $fighter_one['character'], $message, $winner['character']);
		}
		else
		{
			Message::exploration_finished($fighter_one['character'], $fighter_two['character'], $message, $winner['character']);
		}

		/*
		 *	Agregamos tiempo de descanzo
		 *	luego de la batalla
		 */
		if ( $fighter_two['is_player'] )
		{
			$this->after_battle();
		}

		/*
		 *	Disparamos el evento de batalla
		 */	
		if ( $fighter_two['is_player'] )
		{
			Event::fire('battle', array($fighter_one['character'], $fighter_two['character']));
		}
		else
		{
			Event::fire('pveBattle', array($fighter_one['character'], $fighter_two['character'], $winner['character']));
		}

		return array('winner' => $winner['character'], 'message' => $message);
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

			$characterCoins->owner_id = $this->id;
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
		$characterItems = $this->items()->select(array('item_id'))->where_not_in('location', array('inventory', 'none'))->get();

		foreach ( $characterItems as $characterItem )
		{
			$item = $characterItem->item()->select(array(
				'm_defense', 
				'p_defense', 
				'stat_life', 
				'stat_dexterity',
				'stat_magic',
				'stat_strength',
				'stat_luck'
			))->first();

			if ( ! $item )
			{
				continue;
			}

			if ( $positive )
			{
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
				$bonification['p_defense']		-= ( $item->p_defense < 0 )			? $item->p_defense : 0;
				$bonification['m_defense']		-= ( $item->m_defense < 0 )			? $item->m_defense : 0;

				$bonification['stat_life']		-= ( $item->stat_life < 0 )			? $item->stat_life : 0;
				$bonification['stat_dexterity']	-= ( $item->stat_dexterity < 0 )	? $item->stat_dexterity : 0;
				$bonification['stat_magic']		-= ( $item->stat_magic < 0 )		? $item->stat_magic : 0;
				$bonification['stat_strength']	-= ( $item->stat_strength < 0 )		? $item->stat_strength : 0;
				$bonification['stat_luck']		-= ( $item->stat_luck < 0 )			? $item->stat_luck : 0;
			}
		}

		/*
		 *	Ahora revisamos las bonificaciones
		 *	de los skills que están activos
		 */
		$characterSkills = $this->skills()->get();

		foreach ( $characterSkills as $characterSkill )
		{
			$skill = $characterSkill->skill()->select(array('data'))->first();

			if ( ! $skill )
			{
				continue;
			}

			$skill = $skill->data;

			if ( $positive )
			{
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
				$bonification['p_defense']		-= ( isset($skill['p_defense']) && $skill['p_defense'] < 0 )			? $skill['p_defense'] * $characterSkill->amount : 0;
				$bonification['m_defense']		-= ( isset($skill['m_defense']) && $skill['m_defense'] < 0 )			? $skill['m_defense'] * $characterSkill->amount : 0;

				$bonification['stat_life']		-= ( isset($skill['stat_life']) && $skill['stat_life'] < 0 )			? $skill['stat_life'] * $characterSkill->amount : 0;
				$bonification['stat_dexterity']	-= ( isset($skill['stat_dexterity']) && $skill['stat_dexterity'] < 0 )	? $skill['stat_dexterity'] * $characterSkill->amount : 0;
				$bonification['stat_magic']		-= ( isset($skill['stat_magic']) && $skill['stat_magic'] < 0 )			? $skill['stat_magic'] * $characterSkill->amount : 0;
				$bonification['stat_strength']	-= ( isset($skill['stat_strength']) && $skill['stat_strength'] < 0 )	? $skill['stat_strength'] * $characterSkill->amount : 0;
				$bonification['stat_luck']		-= ( isset($skill['stat_luck']) && $skill['stat_luck'] < 0 )			? $skill['stat_luck'] * $characterSkill->amount : 0;
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
		return $this->items()->select(array('id', 'count'))->where('item_id', '=', Config::get('game.coin_id'))->first();
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