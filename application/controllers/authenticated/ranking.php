<?php

class Authenticated_Ranking_Controller extends Authenticated_Base
{
	protected $kingOfTheHill;
	protected $clanOrbPoint;

	public static function register_routes()
	{
		Route::get("authenticated/ranking/(:any?)", array(
			"uses" => "authenticated.ranking@index",
			"as"   => "get_authenticated_ranking_index"
		));
	}
	
	public function __construct(KingOfTheHill $kingOfTheHill, 
								ClanOrbPoint $clanOrbPoint, 
								Character $character)
	{
		$this->kingOfTheHill = $kingOfTheHill;
		$this->clanOrbPoint = $clanOrbPoint;
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
                                   ->with("clan")
                                   ->get_characters_for_pvp_ranking()
                                   ->paginate(50);
                
                $elements = $pagination->results;
                
				break;

			case "clan":
				$pagination = $this->clanOrbPoint
                                   ->with("clan")
                                   ->order_by("points", "desc")
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