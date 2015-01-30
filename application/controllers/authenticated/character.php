<?php

class Authenticated_Character_Controller extends Authenticated_Base
{
    /**
     * @var Models\Achievement\AchievementRepository
     */
    protected $achievementRepository;

    /**
     * @var \Models\Achievement\AchievementCharacterProgressRepository
     */
    protected $achievementCharacterProgressRepository;

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
		
		Route::get("authenticated/character/show/(:num)/(:any)", array(
			"uses" => "authenticated.character@show",
			"as"   => "get_authenticated_character_show"
		));

        Route::get("authenticated/character/show/(:num)/(:any)/achievements", array(
            "uses" => "authenticated.character@achievements",
            "as" => "get_authenticated_character_achievements"
        ));
	}
	
	public function __construct(Character $character,
                                \Models\Achievement\AchievementRepository $achievementRepository,
                                \Models\Achievement\AchievementCharacterProgressRepository $achievementCharacterProgressRepository)
	{
		$this->character = $character;
        $this->achievementRepository = $achievementRepository;
		$this->achievementCharacterProgressRepository = $achievementCharacterProgressRepository;

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
            $characteristics = array();
            $input = (array) Input::get("characteristics");
            
            foreach (Characteristic::get_all() as $pack) {
                foreach ($pack as $characteristic) {
                    // Si el indice existe, significa que el usuario eligio
                    // esa caracteristica
                    if (isset($input[$characteristic->get_name()])) {
                        $characteristics[] = $characteristic;
                    }
                }
            }
            
			$character->set_characteristics_from_array($characteristics);
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
		
        $json = Input::json(true);
        
		$stat = Input::get("stat_name", $json["stat_name"]);
		$amount = Input::get("stat_amount", $json["stat_amount"]);
        
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
     * @param integer $server
	 * @param string $name
	 */
	public function get_show($server, $name)
	{
		$characterToSee = $this->character
                               ->where_server_id($server)
                               ->where_name($name)
                               ->first_or_die();
		
		$orb = $characterToSee->orbs()->first();
		$skills = array();
		
        $characterToSee->check_buffs_time();
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

    /**
     * Mostramos los logros de un personaje
     *
     * @param integer $server
     * @param string $name
     */
    public function get_achievements($server, $name)
    {
        $character = $this->character->getCharacterFromServerByName($server, $name)->first_or_die();

        $achievements = $this->achievementRepository->all();
        $characterAchievements = $character->getAchievementsRelationship()->lists("is_completed", "achievement_id");
        $progressHelper = array();

        foreach ($achievements as $achievement) {
            $progressHelper[$achievement->getId()] = new \Models\Achievement\AchievementProgressHelper($achievement, $this->achievementCharacterProgressRepository->getInstance($character, $achievement));
        }

        $this->layout->title = "Logros";
        $this->layout->content = \Laravel\View::make("authenticated.characterachievements", compact("achievements", "characterAchievements", "progressHelper"));
    }
}