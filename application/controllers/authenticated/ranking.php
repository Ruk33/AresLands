<?php

class Authenticated_Ranking_Controller extends Authenticated_Base
{
	protected $kingOfTheHill;
	protected $clan;

	public static function register_routes()
	{
		Route::get("authenticated/ranking/(:any?)", array(
			"uses" => "authenticated.ranking@index",
			"as"   => "get_authenticated_ranking_index"
		));
	}
	
	public function __construct(KingOfTheHill $kingOfTheHill, 
								Clan $clan, 
								Character $character)
	{
		$this->kingOfTheHill = $kingOfTheHill;
		$this->clan = $clan;
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index($rank = "pvp")
	{
        $elements = array();
        $pagination = null;
        
		switch ( $rank )
		{
//			case "kingOfTheHill":
//				$elements = $this->kingOfTheHill->get_list();
//				break;

			case "pvp":
				$pagination = $this->character
                                   ->get_characters_for_pvp_ranking()
                                   ->paginate(50);
                
                $elements = $pagination->results;
                
				break;

			case "clan":
				$pagination = $this->clan
                                   ->get_clans_for_ranking()
                                   ->paginate(50);
                
                $elements = $pagination->results;
                
				break;

			default:
				return Redirect::to_route("get_authenticated_ranking_index");
				break;
		}
		
		$this->layout->title = "Ranking";
		$this->layout->content = View::make('authenticated.ranking', compact(
			"rank", "pagination", "elements"
		));
	}
}