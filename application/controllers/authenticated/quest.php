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
		
		Route::get("authenticated/quest/reward/(:num)", array(
			"uses" => "authenticated.quest@reward",
			"as"   => "get_authenticated_quest_reward"
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
	
	public function get_reward($id)
	{
		$character = $this->character->get_logged();
		$progress = $character->quests()->where_quest_id($id)->where_progress("reward")->first_or_die();
				
		$progress->finish();
		
		return Laravel\Redirect::to_route("get_authenticated_index");
	}
}