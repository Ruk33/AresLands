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
	 * Registramos las rutas del controlador
	 */
	public static function register_routes()
	{
		
	}
    
    public function before()
    {        
        Tournament::check_for_started();
		Tournament::check_for_finished();
		Tournament::check_for_potions();
        
        $character = $this->character->get_logged();
        
        $character->check_activities();
        $character->check_skills_time();
        $character->check_ip(Request::ip());
        $character->regenerate_life();
        
        $character->save();
    }
    
    public function after($response)
    {
        $character = $this->character->get_logged();

        $startedQuests = array_merge(
            $character->started_quests()->get(), 
            $character->reward_quests()->get()
         );

        $npcs = IoC::resolve("Merchant")->get_from_zone($character->zone, $character)->get();

        $tournament = null;

        if (Tournament::is_active()) {
            $tournament = Tournament::get_active()->first();
        } else if (Tournament::is_upcoming()) {
            $tournament = Tournament::get_upcoming()->first();
        }

        $view = $this->layout;
        
        $view->coins = $character->get_divided_coins();
        $view->character = $character;
        $view->startedQuests = $startedQuests;
        $view->npcs = $npcs;
        $view->tournament = $tournament;
    }
}