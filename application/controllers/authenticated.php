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
