<?php

class Authenticated_Dungeon_Controller extends Authenticated_Base
{
	protected $dungeon;
	
	public static function register_routes()
	{
		Route::get("authenticated/dungeon", array(
			"uses" => "authenticated.dungeon@index",
			"as"   => "get_authenticated_dungeon_index"
		));
		
		Route::post("authenticated/dungeon", array(
			"uses" => "authenticated.dungeon@index",
			"as"   => "post_authenticated_dungeon_index"
		));
	}
	
	public function __construct(Dungeon $dungeon, Character $character)
	{
		$this->dungeon = $dungeon;
		$this->character = $character;
		
		parent::__construct();
	}

	public function get_index()
	{
		$character = $this->character->get_logged();
		$dungeons = $this->dungeon->available_for($character)->get();

		$this->layout->title = "Mazmorras";
		$this->layout->content = View::make("authenticated.dungeon", compact(
			"character", "dungeons"
		));
	}
	
	public function post_index()
	{
		$dungeon = $this->dungeon->find_or_die(Input::get("id"));
		$character = $this->character->get_logged();
		$canDungeon = $dungeon->can_character_do_dungeon($character);
		
		if ( $canDungeon === true )
		{
			$dungeonBattle = $character->do_dungeon($dungeon);
			
			if ( $dungeonBattle->get_completed() )
			{
				Session::flash("success", "¡Haz completado la mazmorra!");
			}
			else
			{
				Session::flash("error", "Uno de los monstruos de la mazmorra te ha derrotado");
			}
		}
		else
		{
			Session::flash("error", $canDungeon);
		}
		
		return Laravel\Redirect::to_route("get_authenticated_index");
	}
}