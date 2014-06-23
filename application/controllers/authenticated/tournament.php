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
	}
	
	public function __construct(Tournament $tournament, Character $character)
	{
		parent::__construct();
		
		$this->tournament = $tournament;
		$this->character = $character;
 	}
	
	public function get_index()
	{
		$tournaments = $this->tournament->all();
		
		$this->layout->title = "Torneos";
		$this->layout->content = View::make("authenticated.alltournaments", compact("tournaments"));
	}

	public function get_show($id)
	{
		$tournament = $this->tournament->find_or_die($id);
		$character = $this->character->get_logged();
		
		/*
		$tournament = null;
		$canRegisterClan = false;
		$canUnRegisterClan = false;
		$canReclaimMvpReward = false;
		$canReclaimClanLiderReward = false;

		$registeredClans = array();

		if ( $tournament )
		{
			$canReclaimMvpReward = $tournament->can_reclaim_mvp_reward($character);
			$canReclaimClanLiderReward = $tournament->can_reclaim_clan_lider_reward($character);

			$registeredClans = TournamentRegisteredClan::left_join('tournament_clan_scores', function($join)
			{
				$join->on('tournament_clan_scores.clan_id', '=', 'tournament_registered_clans.clan_id');
				$join->on('tournament_clan_scores.tournament_id', '=', 'tournament_registered_clans.tournament_id');
			})
				->where('tournament_registered_clans.tournament_id', '=', $tournament->id)
				->group_by('tournament_registered_clans.id')
				->order_by('total_win_score', 'desc')
				->distinct()
				->select(array('tournament_registered_clans.*', DB::raw('sum(tournament_clan_scores.win_score) as total_win_score')))
				->get();
		}

		$this->layout->title = 'Torneos';
		$this->layout->content = View::make('authenticated.tournaments')
									 ->with('tournament', $tournament)
									 ->with('canRegisterClan', $canRegisterClan)
									 ->with('canUnRegisterClan', $canUnRegisterClan)
									 ->with('canReclaimMvpReward', $canReclaimMvpReward)
									 ->with('canReclaimClanLiderReward', $canReclaimClanLiderReward)
									 ->with('registeredClans', $registeredClans);
		 * 
		 */
	}

	public function get_registerClanInTournament($tournament)
	{
		$tournament = Tournament::find((int) $tournament);

		if ( $tournament )
		{
			$character = Character::get_character_of_logged_user(array('id', 'clan_id', 'clan_permission'));

			if ( $tournament->can_register_clan($character) )
			{
				$tournament->register_clan($character->clan);
			}
		}

		return Redirect::to('authenticated/tournaments');
	}

	public function get_unregisterClanFromTournament($tournament)
	{
		$tournament = Tournament::find((int) $tournament);

		if ( $tournament )
		{
			$character = Character::get_character_of_logged_user(array('id', 'clan_id', 'clan_permission'));

			if ( $tournament->can_unregister_clan($character) )
			{
				$tournament->unregister_clan($character->clan);
			}
		}

		return Redirect::to('authenticated/tournaments');
	}

	public function get_claimTournamentMvpReward($tournament)
	{
		$tournament = Tournament::find((int) $tournament);

		if ( $tournament )
		{
			$character = Character::get_character_of_logged_user(array('id', 'clan_id'));

			if ( $tournament->can_reclaim_mvp_reward($character) )
			{
				$tournament->give_mvp_reward_and_send_message();
			}
		}

		return Redirect::to('authenticated/tournaments');
	}

	public function get_claimTournamentClanLeaderReward($tournament)
	{
		$tournament = Tournament::find((int) $tournament);

		if ( $tournament )
		{
			$character = Character::get_character_of_logged_user(array('id', 'clan_id'));

			if ( $tournament->can_reclaim_clan_lider_reward($character) )
			{
				$tournament->give_clan_leader_reward_and_send_message();
			}
		}

		return Redirect::to('authenticated/tournaments');
	}
}