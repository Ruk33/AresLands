<?php

use Mockery as m;

class Authenticated_Tournament_Controller_Test extends Tests\TestHelper
{
	private $character;
	private $tournament;
	private $tournamentClanScore;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->tournament = m::mock("Tournament");
		IoC::instance("Tournament", $this->tournament);
		
		$this->tournamentClanScore = m::mock("TournamentClanScore");
		IoC::instance("TournamentClanScore", $this->tournamentClanScore);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("Tournament");
		IoC::unregister("TournamentClanScore");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/tournament", "before", "auth");
		$this->assertHasFilter("get", "authenticated/tournament", "before", "hasNoCharacter");
		
		$this->tournament->shouldReceive("order_by")->once()->with("starts_at", "desc")->andReturnSelf();
		$this->tournament->shouldReceive("get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/tournament");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Torneos",
			"tournaments" => array()
		));
	}
	
	public function testMostrar()
	{
		$this->assertHasFilter("get", "authenticated/tournament/1", "before", "auth");
		$this->assertHasFilter("get", "authenticated/tournament/1", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		
		$this->tournament->shouldReceive("find_or_die")->with(1)->once()->andReturnSelf();
		$this->tournament->shouldReceive("get_id")->andReturn(1);
		$this->tournament->shouldReceive("can_register_clan")->with($this->character)->once()->andReturn(false);
		$this->tournament->shouldReceive("can_unregister_clan")->with($this->character)->once()->andReturn(false);
		$this->tournament->shouldReceive("can_reclaim_mvp_reward")->with($this->character)->once()->andReturn(false);
		$this->tournament->shouldReceive("can_reclaim_leader_reward")->with($this->character)->once()->andReturn(false);
		
		$clanA = m::mock("TournamentRegisteredClan");
		$clanB = m::mock("TournamentRegisteredClan");
		
		$clanA->shouldReceive("get_clan_id")->andReturn(3);
		$clanB->shouldReceive("get_clan_id")->andReturn(4);
		
		$this->tournament->shouldReceive("get_registered_clans->get")->once()->andReturn(array($clanA, $clanB));
		
		$this->tournamentClanScore->shouldReceive("get_victory_percentage")->with(1, 3)->once()->andReturn(1);
		$this->tournamentClanScore->shouldReceive("get_victory_percentage")->with(1, 4)->once()->andReturn(2);
		
		$response = $this->get("authenticated/tournament/1");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Torneo",
			"tournament" => $this->tournament,
			"canRegisterClan" => false,
			"canUnRegisterClan" => false,
			"canReclaimMvpReward" => false,
			"canReclaimClanLiderReward" => false,
			"registeredClans" => array($clanB, $clanA)
		));
	}
	
	public function testRegistrarGrupo()
	{
		$this->assertHasFilter("post", "authenticated/tournament/register/clan", "before", "auth");
		$this->assertHasFilter("post", "authenticated/tournament/register/clan", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/tournament/register/clan", "before", "hasClan");
		
		Input::replace(array("id" => 1));
		
		$this->tournament->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->tournament->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$this->tournament->shouldReceive("can_register_clan")->once()->with($this->character)->andReturn(false);
		
		$response = $this->post("authenticated/tournament/register/clan");
		
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
		$this->assertSessionHas("error", "No puedes registrar tu grupo en este momento");
		
		$this->tournament->shouldReceive("can_register_clan")->once()->with($this->character)->andReturn(true);
		
		$clan = m::mock("Clan");
		
		$this->character->shouldReceive("clan->results")->once()->andReturn($clan);
		$this->tournament->shouldReceive("register_clan")->once()->with($clan);
		
		$response = $this->post("authenticated/tournament/register/clan");
		
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
		$this->assertSessionHas("success", "Haz registrado tu grupo exitosamente");
	}
	
	public function testSacarGrupo()
	{
		$this->assertHasFilter("post", "authenticated/tournament/unregister/clan", "before", "auth");
		$this->assertHasFilter("post", "authenticated/tournament/unregister/clan", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/tournament/unregister/clan", "before", "hasClan");
		
		Input::replace(array("id" => 1));
		
		$this->tournament->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->tournament->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$this->tournament->shouldReceive("can_unregister_clan")->once()->with($this->character)->andReturn(false);
		
		$response = $this->post("authenticated/tournament/unregister/clan");
		
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
		$this->assertSessionHas("error", "No puedes sacar a tu grupo en este momento");
		
		$this->tournament->shouldReceive("can_unregister_clan")->once()->with($this->character)->andReturn(true);
		
		$clan = m::mock("Clan");
		
		$this->character->shouldReceive("clan->results")->once()->andReturn($clan);
		$this->tournament->shouldReceive("unregister_clan")->once()->with($clan);
		
		$response = $this->post("authenticated/tournament/unregister/clan");
		
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
		$this->assertSessionHas("success", "Haz sacado a tu grupo exitosamente");
	}
	
	public function testReclamarPremioMvp()
	{
		$this->assertHasFilter("get", "authenticated/tournament/1/claim/mvp", "before", "auth");
		$this->assertHasFilter("get", "authenticated/tournament/1/claim/mvp", "before", "hasNoCharacter");
		
		$this->tournament->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->tournament->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$this->tournament->shouldReceive("can_reclaim_mvp_reward")->once()->with($this->character)->andReturn(false);
		
		$response = $this->get("authenticated/tournament/1/claim/mvp");
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
		
		$this->tournament->shouldReceive("can_reclaim_mvp_reward")->once()->with($this->character)->andReturn(true);
		$this->tournament->shouldReceive("give_mvp_reward_and_send_message")->once();
		
		$response = $this->get("authenticated/tournament/1/claim/mvp");
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
	}
	
	public function testReclamarPremioLider()
	{
		$this->assertHasFilter("get", "authenticated/tournament/1/claim/leader", "before", "auth");
		$this->assertHasFilter("get", "authenticated/tournament/1/claim/leader", "before", "hasNoCharacter");
		$this->assertHasFilter("get", "authenticated/tournament/1/claim/leader", "before", "hasClan");
		
		$this->tournament->shouldReceive("find_or_die")->with(1)->twice()->andReturnSelf();
		$this->tournament->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$this->tournament->shouldReceive("can_reclaim_leader_reward")->with($this->character)->once()->andReturn(false);
		
		$response = $this->get("authenticated/tournament/1/claim/leader");
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
		
		$this->tournament->shouldReceive("can_reclaim_leader_reward")->with($this->character)->once()->andReturn(true);
		$this->tournament->shouldReceive("give_clan_leader_reward_and_send_message")->once();
		
		$response = $this->get("authenticated/tournament/1/claim/leader");
		$this->assertRedirect(URL::to_route("get_authenticated_tournament_show", array(1)), $response);
	}
}