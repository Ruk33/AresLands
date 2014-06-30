<?php

class Authenticated_Character_Controller extends Authenticated_Base
{
	public static function register_routes()
	{
		Route::post("authenticated/character/follow", array(
			"uses" => "authenticated.character@follow",
			"as"   => "post_authenticated_character_follow"
		));
		
		Route::post("authenticated/character/characteristics", array(
			"uses" => "authenticated.character@characteristics",
			"as"   => "post_authenticated_character_characteristics"
		));
		
		Route::post("authenticated/character/addStat", array(
			"uses" => "authenticated.character@addStat",
			"as"   => "post_authenticated_character_add_stat"
		));
		
		Route::get("authenticated/character/show/(:any)", array(
			"uses" => "authenticated.character@show",
			"as"   => "post_authenticated_character_show"
		));
	}
	
	public function __construct(Character $character)
	{
		$this->character = $character;
		
		parent::__construct();
	}
	
	/**
	 * Intetamos perseguir personaje
	 * @return Response
	 */
	public function post_follow()
	{
		$characterToFollow = $this->character->find_or_die(Input::get('id'));
		$character = $this->character->get_logged();

		if ( $character->can_follow($characterToFollow) )
		{
			$character->follow($characterToFollow);
		}

		return Redirect::to_route("get_authenticated_index");
	}
	
	/**
	 * Asignamos caracteristicas a personaje
	 * 
	 * @return Redirect
	 */
	public function post_characteristics()
	{
		$character = $this->character->get_logged();
		
		if ( ! $character->characteristics )
		{
			$character->set_characteristics_from_array(Input::get("characteristics"));
		}
		
		return \Laravel\Redirect::to_route("get_authenticated_index");
	}
	
	/**
	 * Agregamos atributos a personaje
	 * 
	 * @return boolean
	 */
	public function post_addStat()
	{
		$character = $this->character->get_logged();
		
		$stat = Input::get("stat_name");
		$amount = Input::get("stat_amount");
		
		if ( $character->can_add_stat($stat, $amount) )
		{
			$character->add_stat($stat, $amount);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Mostramos personaje
	 * 
	 * @param string $name
	 */
	public function get_show($name)
	{
		$characterToSee = $this->character->where_name($name)->first_or_die();
		
		$orb = $characterToSee->orbs()->first();
		$skills = array();
		
		$characterToSee->update_activities_time();
		
		$character = $this->character->get_logged();
		
		if ( $character->is_admin() )
		{
			$skills = $characterToSee->skills()->get();
		}
		
		// Posibles parejas con las que puede
		// atacar el personaje
		$pairs = array();

		if ( $character->can_attack_in_pairs() )
		{
			$pairs = $character->get_pairs_to($characterToSee);
		}

		$weapon = $characterToSee->get_weapon();
		$shield = $characterToSee->get_shield();
		$mercenary = $characterToSee->get_mercenary();

		$hideStats = $characterToSee->has_characteristic(Characteristic::RESERVED);
		$castableSkills = $character->get_castable_talents($characterToSee);

		$this->layout->title = $characterToSee->name;
		$this->layout->content = View::make('authenticated.character')->with(compact(
			'character', 
			'orb', 
			'skills', 
			'characterToSee',
			'weapon', 
			'shield', 
			'mercenary', 
			'hideStats', 
			'castableSkills', 
			'pairs'
		));
	}
}