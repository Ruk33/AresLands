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

	public function get_logout()
	{
		Auth::logout();
		return Redirect::to('home/index/');
	}
}
