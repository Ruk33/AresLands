<?php

class Authenticated_Battle_Controller extends Authenticated_Base
{
	protected $monster;
	
	public static function register_routes()
	{
		Route::post("authenticated/battle/monster", array(
			"uses" => "authenticated.battle@monster",
			"as"   => "post_authenticated_battle_monster"
		));
		
		Route::post("authenticated/battle/character", array(
			"uses" => "authenticated.battle@character",
			"as"   => "post_authenticated_battle_character"
		));
		
		Route::get("authenticated/battle", array(
			"uses" => "authenticated.battle@index",
			"as"   => "get_authenticated_battle_index"
		));
		
		Route::post("authenticated/battle/search", array(
			"uses" => "authenticated.battle@search",
			"as"   => "post_authenticated_battle_search"
		));
	}
	
	public function __construct(Monster $monster, Character $character)
	{
		$this->character = $character;
		$this->monster = $monster;
		
		parent::__construct();
	}
	
	public function get_index()
	{
		$character = $this->character->get_logged();
		$monsters = $this->monster
						 ->get_from_zone($character->zone, $character)
						 ->order_by("level", "asc")
						 ->get();

		$this->layout->title = "Batallar";
		$this->layout->content = View::make("authenticated.battle", compact(
			"character", 
			"monsters"
		));
	}
	
	/**
	 * Batallamos contra personaje
	 * 
	 * @return Laravel\Redirect
	 */
	public function post_character()
	{
		$target = $this->character->where_name(Input::get("name"))->first_or_die();
		$character = $this->character->get_logged();
		$pair = $this->character->find(Input::get("pair", 0));
		
		$battle = $character->battle_or_error($target, $pair);
		
		if ( is_string($battle) )
		{
			Session::flash("error", $battle);
			return \Laravel\Redirect::to_route("get_authenticated_battle_index");
		}
		
		$reportMessage = $battle->get_attacker_notification_message();
		
		return \Laravel\Redirect::to_route("get_authenticated_message_read", array(
			$reportMessage->id
		));
	}
	
	/**
	 * Batallamos contra monstruo
	 * 
	 * @return Laravel\Redirect
	 */
	public function post_monster()
	{
		$monster = $this->monster->find_or_die(Input::get("monster_id"));
		$character = $this->character->get_logged();
		
		$battle = $character->battle_or_error($monster);
		
		if ( is_string($battle) )
		{
			Session::flash("error", $battle);
			return \Laravel\Redirect::to_route("get_authenticated_battle_index");
		}
		
		$reportMessage = $battle->get_attacker_notification_message();
		
		return \Laravel\Redirect::to_route("get_authenticated_message_read", array(
			$reportMessage->id
		));
	}
	
	public function post_search()
	{
		$character = $this->character->get_logged();
		$result = null;

		switch ( Input::get("search_method") ) 
		{
			case "name":
				$result = $character->get_opponent()
									->where_name(Input::get("character_name"))
									->first();
				break;

			case "random":
				// Verificamos que el operador sea correcto, de lo contrario
				// asignamos el operador a igualdad (=)
				if ( in_array(Input::get("operation"), array("<", ">", "=")) )
				{
					$operation = Input::get("operation");
				}
				else
				{
					$operation = "=";
				}
				
				$races = explode(",", Input::get("race"));
				
				$result = $character->get_opponent($races)
									->where("level", $operation, Input::get("level"))
									->order_by(DB::raw("RAND()"))
									->first();
				
				break;

			case "group":
				$result = $character->get_opponent()
									->where_clan_id(Input::get("clan"))
									->order_by(DB::raw("RAND()"))
									->first();
				
				break;
			
			default:
				Session::flash("error", "Metodo de busqueda incorrecto");
				return \Laravel\Redirect::to_route("get_authenticated_battle_index");
		}
		
		if ( ! $result )
		{
			Session::flash("error", "No se encontro ningun personaje para batallar");
			return Laravel\Redirect::to_route("get_authenticated_battle_index");
		}
		
		$characterToSee = $result;
		
		$character->sees($characterToSee);

		$weapon = $characterToSee->get_weapon();
		$shield = $characterToSee->get_shield();
		$mercenary = $characterToSee->get_mercenary();

		$castableSkills = $character->get_castable_talents($characterToSee);

		$orbs = $characterToSee->orbs()->get();
		
		$skills = array();

		// Parejas con las que puede atacar el personaje
		$pairs = $character->get_pairs_to($characterToSee);
		
		$hideStats = $characterToSee->has_characteristic(Characteristic::RESERVED);

		$this->layout->title = $result->name;
		$this->layout->content = View::make("authenticated.character", compact(
			"character", "weapon", "shield", "mercenary", "orbs", "skills",
			"characterToSee", "hideStats", "castableSkills", "pairs"
		));
	}
}