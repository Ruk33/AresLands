<?php

abstract class Authenticated_Base extends Base_Controller
{
	/**
	 *
	 * @var string
	 */
    public $layout = 'layouts.default';
	
	/**
	 *
	 * @var boolean
	 */
	public $restful = true;
	
	/**
	 *
	 * @var Character
	 */
	protected $character;
	
	/**
	 * 
	 */
	public function pass_variables_to_layout()
	{
		$character = $this->character()->get_character_of_logged_user();
		
		$startedQuests = array_merge(
			 $character->started_quests()->get(), 
			 $character->reward_quests()->get()
		 );

		$npcs = Merchant::get_from_zone($character->zone, $character)->get();

		$tournament = null;

		 if ( Tournament::is_active() )
		 {
			 $tournament = Tournament::get_active()->first();
		 }
		 else if ( Tournament::is_upcoming() )
		 {
			 $tournament = Tournament::get_upcoming()->first();
		 }

		 $this->layout->with('coins', $character->get_divided_coins());
		 $this->layout->with('character', $character);
		 $this->layout->with('startedQuests', $startedQuests);
		 $this->layout->with('npcs', $npcs);
		 $this->layout->with('tournament', $tournament);
	}
}