<?php

use Mockery as m;

class Authenticated_Tournament_Controller_Test extends Tests\TestHelper
{
	private $character;
	private $tournament;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->tournament = m::mock("Tournament");
		IoC::instance("Tournament", $this->tournament);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("Tournament");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/tournament", "before", "auth");
		$this->assertHasFilter("get", "authenticated/tournament", "before", "hasNoCharacter");
		
		$this->tournament->shouldReceive("all")->once()->andReturn(array());
		
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
		$this->tournament->shouldReceive("find_or_die")->with(1)->andReturnSelf();
	}
}