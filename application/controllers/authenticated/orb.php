<?php

class Authenticated_Orb_Controller extends Authenticated_Base
{
	protected $orb;
	
	public static function register_routes()
	{
		Route::get("authenticated/orb", array(
			"uses" => "authenticated.orb@index",
			"as"   => "get_authenticated_orb_index"
		));
		
		Route::get("authenticated/orb/(:num)/claim", array(
			"uses" => "authenticated.orb@claim",
			"as"   => "get_authenticated_orb_claim"
		));
	}
	
	public function __construct(Orb $orb, Character $character)
	{
		$this->orb = $orb;
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index()
	{
		$character = $this->character->get_logged();
		$orbs = $this->orb->order_by("min_level", "asc")->get();

		$this->layout->title = "Orbes";
		$this->layout->content = View::make("authenticated.orbs", compact("character", "orbs"));
	}
	
	public function get_claim($id)
	{
		$orb = $this->orb->find_or_die($id);
		
		if ( ! $orb->owner_id )
		{
			$character = $this->character->get_logged();
			
			if ( $orb->can_be_stolen_by($character) )
			{
				$orb->give_to($character);
			}
		}
		
		return Laravel\Redirect::to_route("get_authenticated_orb_index");
	}
}