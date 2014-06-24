<?php

class Authenticated_Quest_Controller extends Authenticated_Base
{
	protected $quest;
	
	public static function register_routes()
	{
		Route::post("authenticated/quest/accept", array(
			"uses" => "authenticated.quest@accept",
			"as"   => "post_authenticated_quest_accept"
		));
	}
	
	public function __construct(Quest $quest, Character $character)
	{
		parent::__construct();
		
		$this->quest = $quest;
		$this->character = $character;
	}
	
	public function post_accept()
	{
		$quest = $this->quest->find_or_die(Input::get("id"));
		$character = $this->character->get_logged();
		
		$quest->accept($character);

		return Redirect::to_route("get_authenticated_index");
	}
}