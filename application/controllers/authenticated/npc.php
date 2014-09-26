<?php

class Authenticated_Npc_Controller extends Authenticated_Base
{
	protected $npc;
	protected $npcMerchandise;
	protected $npcRandomMerchandise;
	
	public static function register_routes()
	{
		Route::get("authenticated/npc/(:num)/(:any?)", array(
			"uses" => "authenticated.npc@index",
			"as"   => "get_authenticated_npc_index"
		));
		
		Route::post("authenticated/npc/buy", array(
			"uses" => "authenticated.npc@buy",
			"as"   => "post_authenticated_npc_buy"
		));
	}
	
	public function __construct(Merchant $npc, 
                                NpcMerchandise $npcMerchandise, 
                                NpcRandomMerchandise $npcRandomMerchandise, 
                                Character $character)
	{
		$this->npc = $npc;
		$this->npcMerchandise = $npcMerchandise;
		$this->npcRandomMerchandise = $npcRandomMerchandise;
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index($id)
	{
		$character = $this->character->get_logged();
		$npc = $this->npc->where_id($id)->where_zone_id($character->zone_id)->first_or_die();

		if ( $npc->is_blocked_to($character) )
		{
			return \Laravel\Redirect::to_route("get_authenticated_index");
		}
		
		$npc->fire_global_event('npcTalk', array($character, $npc));

		$merchandises = $npc->get_merchandises_for($character)->with('item')->get();
		$quests = $npc->available_quests_of($character)->order_by('min_level', 'asc')->get();
        $characterQuests = $character->quests()->lists("repeatable_at", "quest_id");
		$characterCoinsCount = $character->get_coins()->count;

		$this->layout->title = $npc->name;
		$this->layout->content = View::make('authenticated.npc', compact(
			"npc", 
            "characterCoinsCount", 
            "merchandises", 
            "characterQuests",
            "quests", 
            "character"
		));
	}
	
	public function post_buy()
	{
		if ( Input::get("random_merchandise", false) )
		{
			$merchandise = $this->npcRandomMerchandise->find_or_die(Input::get("id"));
		}
		else
		{
			$merchandise = $this->npcMerchandise->find_or_die(Input::get("id"));
		}
		
		$character = $this->character->get_logged();
		$npc = $merchandise->npc()->first_or_die();
		
		$result = $npc->try_buy($character, $merchandise, Input::get("amount"));
		
		if ( is_string($result) )
		{
			Session::flash("error", $result);
		}
		else
		{
			Session::flash("success", "Gracias por tu compra, ¿te interesa algo mas?");
		}
		
		return \Laravel\Redirect::to_route("get_authenticated_npc_index", array($npc->id, $npc->name));
	}
}