<?php

class Authenticated_Controller extends Authenticated_Base
{
	public static function register_routes()
	{
		Route::get("authenticated", array(
			"uses" => "authenticated@index",
			"as"   => "get_authenticated_index"
		));
	}
	
	public function __construct()
	{
		parent::__construct();
		
		$this->filter('before', 'csrf')
			 ->on(array('post'))
			 ->except(array(
				'addstat',
				'characters',
				'editclanmessage'
			 ));
	}
	
	public function get_index()
	{        
        $character = Character::get_character_of_logged_user(array_merge(
            Character::$COLUMNS_BASIC,
            Character::$COLUMNS_STATS,
            Character::$COLUMNS_STATS_EXTRA,
            Character::$COLUMNS_LIFE,
            Character::$COLUMNS_OTHER,
            array('last_logged')
        ));
        
		/*
		 *	Verificamos logueada del día
		 */
		if ( $character->check_logged_of_day() )
		{
			$character->give_logged_of_day_reward();
		}

		/*
		 *	Obtenemos todos los skills (buffs)
		 *	del personaje
		 */
		$skills = $character->skills()->get();

		/*
		 *	Obtenemos todas las actividades
		 *	del personaje
		 */
		$activities = $character->activities()->select(array('end_time', 'name', 'data'))->get();

		/*
		 *	Obtenemos el orbe del personaje
		 */
		$orb = $character->orbs()->select(array('id', 'name', 'description'))->first();

		/*
		 *  Talentos que se puede lanzar a el mismo
		 */
		$talents = $character->get_castable_talents($character);

		/*
		 * Zona en donde se encuentra el personaje y la cantidad
		 * de tiempo que lleva explorando en ella
		 */
		$zone = $character->zone()->select(array('id', 'name', 'description'))->first();
		$exploringTime = $character->exploring_times()->where('zone_id', '=', $zone->id)->first();

		/*
		 * Arma, escudo y mercenarios
		 */
		$weapon = $character->get_weapon();
		$shield = $character->get_shield();

		$mercenary = $character->get_mercenary();
		$mercenary = ( $mercenary ) ? $mercenary->item : null;

		$secondMercenary = null;
		if ( $character->has_second_mercenary() )
		{
			$secondMercenary = $character->get_second_mercenary()->first();
		}

		/*
		 * Objetos del inventario
		 */
		$inventoryItems = array();
		
		foreach ( $character->get_inventory_items()->with('item')->get() as $inventoryItem )
		{
			$inventoryItems[$inventoryItem->slot] = $inventoryItem;
		}

		$this->layout->title = 'Inicio';
		$this->layout->content = View::make('authenticated.index')->with(array(
			'character'       => $character,
			'weapon'          => $weapon,
			'shield'          => $shield,
			'mercenary'       => $mercenary,
			'secondMercenary' => $secondMercenary,
			'activities'      => $activities,
			'skills'          => $skills,
			'orb'             => $orb,
			'talents'         => $talents,
			'zone'            => $zone,
			'exploringTime'   => $exploringTime,
			'inventoryItems'  => $inventoryItems
		));
	}

	public function post_explore()
	{
		$character = Character::get_character_of_logged_user(array('id', 'is_traveling', 'is_exploring', 'level', 'xp', 'points_to_change', 'clan_id'));
		$time = Input::get('time');

		if ( ($time <= Config::get('game.max_explore_time') && $time >= Config::get('game.min_explore_time')) && $character->can_explore() )
		{
			/*
			 *	Damos dos puntos a la barra
			 *	de actividad si se explora
			 *	30 minutos o mas
			 */
			if ( $time >= 30 )
			{
				ActivityBar::add($character, 2);
			}

			$character->explore($time * 60);
		}

		return Redirect::to('authenticated/index/');
	}

	public function get_explore()
	{
		$this->layout->title = 'Explorar';
		$this->layout->content = View::make('authenticated.explore');
	}

	public function get_ranking($rank = 'kingOfTheHill')
	{
		switch ( $rank )
		{
			case 'kingOfTheHill':
				$elements = KingOfTheHill::get_list();
				break;

			case 'pvp':
				$select = array('id', 'name', 'gender', 'race', 'pvp_points', 'characteristics');
				$elements = Character::with('clan')->get_characters_for_pvp_ranking()->select($select)->paginate(50);

				break;

			case 'clan':
				$elements = ClanOrbPoint::with('clan')->order_by('points', 'desc')->paginate(50);
				break;

			default:
				return Redirect::to('authenticated/ranking');
				break;
		}
		
		$this->layout->title = 'Ranking';
		$this->layout->content = View::make('authenticated.ranking')->with('rank', $rank)->with('elements', $elements);
	}

	public function post_toBattleMonster()
	{
		$monsterId = Input::get('monster_id');
		
		$character = Character::get_character_of_logged_user();
		$monster = ( $monsterId ) ? Monster::where('type', '=', 'monster')->where('zone_id', '=', $character->zone_id)->find((int) $monsterId) : false;
		
		if ( $monster )
		{
			if ( $character->can_fight() )
			{
				$battle = $character->battle_against($monster);
			}
			else
			{
				Session::flash('errorMessage', 'Aún no puedes pelear');
				return Redirect::to('authenticated/battle/');
			}

			return Redirect::to('authenticated/readMessage/' . $battle->get_attacker_notification_message()->id);
		}
		else
		{
			return Redirect::to('authenticated/battle/');
		}
	}

	public function post_toBattle()
	{
		$characterName = Input::get('name');

		if ( $characterName )
		{
			// Verificamos que el personaje exista
			if ( Character::where_name($characterName)->take(1)->count() == 0 )
			{
				Session::flash('errorMessage', 'Ese personaje no existe.');
				return Redirect::to('authenticated/battle');
			}
			
			$character = Character::get_character_of_logged_user();
			$target = Character::where('name', '=', $characterName)->where('zone_id', '=', $character->zone_id)->first();

			/*
			 *	Verificamos que el personaje este en la zona
			 */
			if ( ! $target )
			{
				Session::flash('errorMessage', $characterName . ' está en otra zona.');
				return Redirect::to('authenticated/battle');
			}
			
			if ( $target->is_traveling )
			{
				Session::flash('errorMessage', $target->name . ' acaba de irse de esta zona.');
				return Redirect::to('authenticated/battle');
			}

			/*
			 *	Verificamos que el personaje
			 *	pueda ser atacado
			 */
			if ( ! $target->can_be_attacked($character) )
			{
				Session::flash('errorMessage', $target->name . ' aún no puede ser atacado/a todavía.');
				return Redirect::to('authenticated/battle');
			}
		}
		else
		{
			/*
			 *	No hay siquiera nombre...
			 */
			return Redirect::to('authenticated/battle');
		}

		if ( $character->can_fight() )
		{
			$pair = ( Input::get('pair') ) ? Character::find((int) Input::get('pair')) : false;
			if ( $pair )
			{
				if ( $character->can_attack_in_pairs() )
				{
					if ( $character->can_attack_with($pair) )
					{
						if ( $pair->id != $target->id )
						{
							$battle = $character->battle_against($target, $pair);
						}
						else
						{
							return Redirect::to('authenticated/battle');
						}
					}
					else
					{
						Session::flash('errorMessage', 'No puedes atacar en pareja con ' . $pair->name);
						return Redirect::to('authenticated/battle');
					}
				}
				else
				{
					Session::flash('errorMessage', 'No puedes atacar en parejas');
					return Redirect::to('authenticated/battle');
				}
			}
			else
			{
				$battle = $character->battle_against($target);
			}
		}
		else
		{
			Session::flash('errorMessage', 'Aún no puedes pelear');
			return Redirect::to('authenticated/battle');
		}

		return Redirect::to('authenticated/readMessage/' . $battle->get_attacker_notification_message()->id);
	}

	public function post_battle()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'name', 'clan_id', 'registered_in_tournament', 'is_traveling'));
		$characterFinded = null;

		switch ( Input::get('search_method') ) 
		{
			case 'name':
				$characterFinded = $character->get_opponent()->where('name', '=', Input::get('character_name'));
				break;

			case 'random':
				switch ( Input::get('operation') )
				{
					case 'greaterThan':
						$operation = '>';
						break;

					case 'lowerThan':
						$operation = '<';
						break;

					default:
						$operation = '=';
						break;
				}
				
				if ( Input::get('race') != 'any' )
				{
					$characterFinded = $character->get_opponent(array(Input::get('race')));
				}
				else
				{
					$characterFinded = $character->get_opponent();
				}
				
				$characterFinded = $characterFinded->where('level', $operation, (int) Input::get('level'))
												   ->order_by(DB::raw('RAND()'));
				
				break;

			case 'group':
				$characterFinded = $character->get_opponent()
											 ->where('clan_id', '=', (int) Input::get('clan'))
											 ->order_by(DB::raw('RAND()'));
				
				break;
		}

		$characterFinded = $characterFinded->select(array(
			'id', 
			'name', 
			'level', 
			'clan_id', 
			'race', 
			'gender', 
			'zone_id',
			'stat_strength',
			'stat_dexterity',
			'stat_resistance',
			'stat_magic',
			'stat_magic_skill',
			'stat_magic_resistance',
			'registered_in_tournament',
			'characteristics',
			'invisible_until',
			'is_traveling'
		))->first();

		/*
		 *	Verificamos si encontramos personaje
		 */
		if ( $characterFinded )
		{
			if ( $characterFinded->has_skill(Config::get('game.trap_skill')) )
			{
				$characterFinded->cast_random_trap_to($character, true);
			}
			
			if ( $character->can_remove_invisibility_of($characterFinded) )
			{
				$characterFinded->remove_invisibility();
			}
			
			$weapon = $characterFinded->get_weapon();
			$shield = $characterFinded->get_shield();

			$mercenary = $characterFinded->get_mercenary();
			$mercenary = ( $mercenary ) ? $mercenary->item : null;
            
            $castableSkills = $character->get_castable_talents($characterFinded);

			/*
			 *	Obtenemos los orbes
			 */
			$orbs = $characterFinded->orbs()->select(array('id', 'name', 'description'))->get();
			
			$skills = array();
			
			if ( $character->is_admin() )
			{
				$skills = $characterFinded->skills;
			}
			
			// Posibles parejas con las que puede
			// atacar el personaje
			$pairs = array();
			
			if ( $character->can_attack_in_pairs() )
			{
				$pairs = $character->get_pairs_to($characterFinded);
			}

			$this->layout->title = $characterFinded->name;
			$this->layout->content = View::make('authenticated.character', array(
                'character' => $character,
                'weapon' => $weapon,
                'shield' => $shield,
                'mercenary' => $mercenary,
                'orbs' => $orbs,
                'skills' => $skills,
                'characterToSee' => $characterFinded,
                'hideStats' => $characterFinded->has_characteristic(Characteristic::RESERVED),
                'castableSkills' => $character->get_castable_talents($characterFinded),
                'pairs' => $pairs
            ));
		}
		else
		{
			/*
			 *	No encontramos :\
			 */
			Session::flash('errorMessage', 'No se encontró ningún personaje para batallar');
			return Redirect::to('authenticated/battle');
		}
	}

	public function post_dungeon()
	{
		$dungeon = Dungeon::find(Input::get('id'));
		$redirection = Redirect::to('authenticated');

		if ( $dungeon )
		{
			$character = Character::get_character_of_logged_user();

			if ( $dungeon->can_character_do_dungeon($character) )
			{
				$dungeonBattle = new DungeonBattle($character, $dungeon, $dungeon->get_level($character));

				if ( $dungeonBattle->get_completed() )
				{
					return $redirection->with('message', '¡Haz completado la mazmorra!');
				}
				else
				{
					return $redirection->with('error', 'Uno de los monstruos de la mazmorra te ha derrotado');
				}
			}
		}

		return $redirection;
	}

	public function get_dungeon()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level'));
		$dungeons = Dungeon::available_for($character)->get();

		$this->layout->title = 'Mazmorras';
		$this->layout->content = View::make('authenticated.dungeon', compact('character', 'dungeons'));
	}

	public function get_battle()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level'));
		$monsters = Monster::get_from_zone($character->zone, $character)->order_by('level', 'asc')->get();

		$this->layout->title = '¡Batallar!';
		$this->layout->content = View::make('authenticated.battle', compact('character', 'monsters'));
	}

	public function get_travel($zoneId = '')
	{
		$character = Character::get_character_of_logged_user(array('id', 'is_traveling', 'zone_id', 'name', 'level', 'xp', 'clan_id'));

		/*
		 *	Si zoneId está definido quiere
		 *	decir que el personaje ya eligió
		 *	la zona a la que quiere viajar
		 */
		$zone = ( is_numeric($zoneId) ) ? Zone::where('min_level', '<=', $character->level)->find((int) $zoneId) : false;

		$error = false;

		if ( $zone )
		{
			// Evitamos que viajen a zonas bloqueadas
			if ( $zone->type != 'city' )
			{
				return Redirect::to('authenticated/travel/');
			}
			
			/*
			 *	Antes de hacer nada, nos fijamos
			 *	si el personaje realmente puede viajar
			 */
			$canTravel = $character->can_travel();

			if ( $canTravel === true )
			{
				if ( $zone->id != $character->zone_id )
				{
					/*
					 *	Cobramos el costo del viaje
					 */
					$characterCoins = $character->get_coins();
					$characterCoins->count -= Config::get('game.travel_cost');
					$characterCoins->save();

					/*
					 *	¡Iniciamos el viaje!
					 */
					$character->travel_to($zone);

					return Redirect::to('authenticated/index');
				}
				else
				{
					$error = 'Ya te encuentras en esa zona';
				}
			}
			else
			{
				/*
				 *	El personaje no puede viajar 
				 *	así que lo notificamos
				 */
				$error = $canTravel;
			}
		}

		$zones = Zone::where('type', '=', 'city')
                     ->where('min_level', '<=', $character->level)
                     ->where('id', '<>', $character->zone_id)
                     ->select(array('id', 'name', 'description', 'min_level'))
                     ->get();

		$exploringTime = $character->exploring_times()->lists('time', 'zone_id');

		$this->layout->title = 'Viajar';
		$this->layout->content = View::make('authenticated.travel')
		->with('character', $character)
		->with('zones', $zones)
		->with('exploringTime', $exploringTime)
		->with('error', $error);
	}
	

	public function get_logout()
	{
		Auth::logout();
		return Redirect::to('home/index/');
	}
}
