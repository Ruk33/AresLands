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
    /*
	public function get_index()
	{
		$character = $this->character->get_logged();
		$dungeon = $character->zone->dungeon;
        $actualDungeonLevel = null;
        
        if ($dungeon) {
            $actualDungeonLevel = $dungeon->get_character_level($character);
        }

		$this->layout->title = "Mazmorra";
		$this->layout->content = View::make("authenticated.dungeon", compact(
			"character", "dungeon", "actualDungeonLevel"
		));
	}
	
	public function post_index()
	{
		$dungeon = $this->dungeon->find_or_die(Input::get("dungeon_id"));
		$character = $this->character->get_logged();
        $dungeonLevel = $dungeon->get_character_level($character);
		$dungeonBattle = $dungeon->do_level_or_error($character, $dungeonLevel);
		
        if (is_string($dungeonBattle)) {
            Session::flash("error", $dungeonBattle);
            return Laravel\Redirect::to_route("get_authenticated_dungeon_index");
        }
        
		$reportMessage = $dungeonBattle->getAttackerReport()->getMessage();
		
		return \Laravel\Redirect::to_route("get_authenticated_message_read", array(
			$reportMessage->id
		));
	}
     * 
     */
}