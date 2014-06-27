<?php

use Mockery as m;

class Authenticated_Quest_Controller_Test extends Tests\TestHelper
{
	protected $character;
	protected $quest;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->quest = m::mock("Quest");
		IoC::instance("Quest", $this->quest);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("Quest");
	}
	
	public function testAceptar()
	{
		$this->assertHasFilter("post", "authenticated/quest/accept", "before", "auth");
		$this->assertHasFilter("post", "authenticated/quest/accept", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1));
		
		$this->quest->shouldReceive("find_or_die")->with(1)->once()->andReturnSelf();
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->quest->shouldReceive("accept")->with($this->character)->once();
		
		$response = $this->post("authenticated/quest/accept");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
	
	public function testObtenerRecompensa()
	{
		$this->assertHasFilter("get", "authenticated/quest/reward/1", "before", "auth");
		$this->assertHasFilter("get", "authenticated/quest/reward/1", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		
		$progress = m::mock("CharacterQuest");
		
		$this->character->shouldReceive("quests")->once()->andReturn($progress);
		$progress->shouldReceive("where_quest_id")->once()->with(1)->andReturnSelf();
		$progress->shouldReceive("where_progress")->once()->with("reward")->andReturnSelf();
		$progress->shouldReceive("first_or_die")->once()->andReturnSelf();
		
		$progress->shouldReceive("finish")->once();
		
		$response = $this->get("authenticated/quest/reward/1");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
}