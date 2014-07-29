<?php

class Authenticated_Inventory_Controller extends Authenticated_Base
{
	public static function register_routes()
	{
		Route::post("authenticated/inventory/destroy", array(
			"uses" => "authenticated.inventory@destroy",
			"as"   => "post_authenticated_inventory_destroy"
		));
		
		Route::post("authenticated/inventory/use", array(
			"uses" => "authenticated.inventory@use",
			"as"   => "post_authenticated_inventory_use"
		));
	}
	
	public function __construct(Character $character)
	{
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function post_destroy()
	{		
		$character = $this->character->get_logged();
		$characterItem = $character->items()->find_or_die(Input::get("id"));
		
		if ( $characterItem->item->destroyable )
		{
			$characterItem->delete();
		}

		return \Laravel\Redirect::to_route("get_authenticated_index");
	}
	
	public function post_use()
	{
		$character = $this->character->get_logged();
		$characterItem = $character->items()->find_or_die(Input::get("id"));
		$valid = $character->use_inventory_item($characterItem, Input::get("amount", 1));

        if ( is_string($valid) )
		{
			Session::flash("error", $valid);
		}
		
		return \Laravel\Redirect::to_route("get_authenticated_index");
	}
}