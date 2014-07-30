<?php

class Authenticated_Controller extends Authenticated_Base
{
	public static function register_routes()
	{
		Route::get("authenticated", array(
			"uses" => "authenticated@index",
			"as"   => "get_authenticated_index"
		));
        
        Route::get("authenticated/index", array(
			"uses" => "authenticated@index",
			"as"   => "get_authenticated_index"
		));
		
		Route::get("authenticated/logout", array(
			"uses" => "authenticated@logout",
			"as"   => "get_authenticated_logout"
		));
	}
	
	public function __construct(Character $character)
	{
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index()
	{        
        $character = $this->character->get_logged();
                
		$character->give_logged_of_day_reward(true);

		$skills = $character->skills()->get();
		$activities = $character->activities()->get();
		$orb = $character->orbs()->first();
		$talents = $character->get_castable_talents($character);
		
		$zone = $character->zone()->first();
		$exploringTime = $character->exploring_times()
								   ->where_zone_id($zone->id)
								   ->first();
		
		$weapon = $character->get_weapon();
		$shield = $character->get_shield();
		$mercenary = $character->get_mercenary();
		$secondMercenary = $character->get_second_mercenary();

		// Agregamos los objetos del inventario en un array cuyo indice (key)
		// sera la posicion del objeto
		$inventoryItems = array();
		
		foreach ( $character->get_inventory_items()->with("item")->get() as $inventoryItem )
		{
			$inventoryItems[$inventoryItem->slot] = $inventoryItem;
		}

		$this->layout->title = $character->name;
		$this->layout->content = View::make("authenticated.index", compact(
			"character", "weapon", "shield", "mercenary", "secondMercenary",
			"activities", "skills", "orb", "talents", "zone", "exploringTime",
			"inventoryItems"
		));
	}

	public function get_logout()
	{
		Auth::logout();
		return Redirect::to_route("get_home_index");
	}
}
