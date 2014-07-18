<?php

class Authenticated_Tournament_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var Tournament
	 */
	protected $tournament;
	
	/**
	 *
	 * @var Character
	 */
	protected $character;
	
	/**
	 *
	 * @var TournamentClanScore
	 */
	protected $tournamentClanScore;
	
	public static function register_routes()
	{
		Route::get("authenticated/tournament", array(
			"uses" => "authenticated.tournament@index",
			"as"   => "get_authenticated_tournament_index"
		));
		
		Route::get("authenticated/tournament/(:num)", array(
			"uses" => "authenticated.tournament@show",
			"as"   => "get_authenticated_tournament_show"
		));
		
		Route::post("authenticated/tournament/register/clan", array(
			"uses"   => "authenticated.tournament@registerClan",
			"as"     => "post_authenticated_tournament_register_clan",
			"before" => "auth|hasNoCharacter|hasClan"
		));
		
		Route::post("authenticated/tournament/unregister/clan", array(
			"uses"   => "authenticated.tournament@unregisterClan",
			"as"     => "post_authenticated_tournament_unregister_clan",
			"before" => "auth|hasNoCharacter|hasClan"
		));
		
		Route::get("authenticated/tournament/(:num)/claim/mvp", array(
			"uses" => "authenticated.tournament@claimMvpReward",
			"as"   => "get_authenticated_tournament_claim_mvp_reward"
		));
		
		Route::get("authenticated/tournament/(:num)/claim/leader", array(
			"uses"   => "authenticated.tournament@claimLeaderReward",
			"as"     => "get_authenticated_tournament_claim_leader_reward",
			"before" => "auth|hasNoCharacter|hasClan"
		));
	}
	
	/**
	 * 
	 * @param Tournament $tournament
	 * @param Character $character
	 * @param TournamentClanScore $tournamentClanScore
	 */
	public function __construct(Tournament $tournament, Character $character, TournamentClanScore $tournamentClanScore)
	{
		$this->tournament = $tournament;
		$this->character = $character;
		$this->tournamentClanScore = $tournamentClanScore;
		
		parent::__construct();
 	}
	
	public function get_index()
	{
		$tournaments = $this->tournament->order_by("starts_at", "desc")->get();
		
		$this->layout->title = "Torneos";
		$this->layout->content = View::make("authenticated.alltournaments", compact("tournaments"));
	}

	public function get_show($id)
	{
		$tournament = $this->tournament->find_or_die($id);
		$character = $this->character->get_logged();
		$canRegisterClan = $tournament->can_register_clan($character);
		$canUnRegisterClan = $tournament->can_unregister_clan($character);
		$canReclaimMvpReward = $tournament->can_reclaim_mvp_reward($character);
		$canReclaimClanLiderReward = $tournament->can_reclaim_leader_reward($character);
		$registeredClans = $tournament->get_registered_clans()->get();
		
		// Ordenamos los grupos registrados de acuerdo a su porcentaje de victoria
		usort($registeredClans, function($a, $b) use ($tournament)
		{
			$aScore = $this->tournamentClanScore->get_victory_percentage($tournament->id, $a->clan_id);
			$bScore = $this->tournamentClanScore->get_victory_percentage($tournament->id, $b->clan_id);
			
			if ( $aScore == $bScore )
			{
				return 0;
			}
			
			return ($aScore > $bScore) ? -1 : 1;
		});
		
		$this->layout->title = "Torneo";
		$this->layout->content = View::make('authenticated.tournaments', compact(
			"tournament",
			"character",
			"canRegisterClan",
			"canUnRegisterClan",
			"canReclaimMvpReward",
			"canReclaimClanLiderReward",
			"registeredClans"
		));
	}

	public function post_registerClan()
	{
		$tournament = $this->tournament->find_or_die(Input::get("id"));
		$character = $this->character->get_logged();
		
		if ( $tournament->can_register_clan($character) )
		{
			$tournament->register_clan($character->clan);
			Session::flash("success", "Haz registrado tu grupo exitosamente");
		}
		else
		{
			Session::flash("error", "No puedes registrar tu grupo en este momento");
		}
		
		return \Laravel\Redirect::to_route("get_authenticated_tournament_show", array($tournament->id));
	}

	public function post_unregisterClan()
	{
		$tournament = $this->tournament->find_or_die(Input::get("id"));
		$character = $this->character->get_logged();
		
		if ( $tournament->can_unregister_clan($character) )
		{
			$tournament->unregister_clan($character->clan);
			Session::flash("success", "Haz sacado a tu grupo exitosamente");
		}
		else
		{
			Session::flash("error", "No puedes sacar a tu grupo en este momento");
		}
		
		return \Laravel\Redirect::to_route("get_authenticated_tournament_show", array($tournament->id));
	}

	public function get_claimMvpReward($id)
	{
		$tournament = $this->tournament->find_or_die($id);
		$character = $this->character->get_logged();
		
		if ( $tournament->can_reclaim_mvp_reward($character) )
		{
			$tournament->give_mvp_reward_and_send_message();
		}
		
		return Laravel\Redirect::to_route("get_authenticated_tournament_show", array($tournament->id));
	}

	public function get_claimLeaderReward($id)
	{
		$tournament = $this->tournament->find_or_die($id);
		$character = $this->character->get_logged();
		
		if ( $tournament->can_reclaim_leader_reward($character) )
		{
			$tournament->give_clan_leader_reward_and_send_message();
		}
		
		return Laravel\Redirect::to_route("get_authenticated_tournament_show", array($tournament->id));
	}
}