<?php

use Mockery as m;

class Authenticated_Dungeon_Controller_Test extends Tests\TestHelper
{
	protected $character;
	protected $dungeon;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		$this->dungeon = m::mock("Dungeon");
		
		Laravel\IoC::instance("Character", $this->character);
		Laravel\IoC::instance("Dungeon", $this->dungeon);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		Laravel\IoC::unregister("Character");
		Laravel\IoC::unregister("Dungeon");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/event/portal", "before", "auth");
		$this->assertHasFilter("get", "authenticated/event/portal", "before", "hasNoCharacter");
		
        $zone = m::mock("Zone");
        
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
        $this->character->shouldReceive("zone->results")->once()->andReturn($zone);
        
        $zone->shouldReceive("dungeon->results")->once()->andreturn($this->dungeon);
        
        $this->dungeon->shouldReceive("get_character_level")->once()->with($this->character)->andReturn(null);
        
        $this->dungeon->shouldReceive("get_character_progress")->once()->with($this->character)->andReturnNull();
        
        $response = $this->get("authenticated/event/portal");
        
        $this->assertResponseOk($response);
        $this->assertViewHasAll($response, array(
            "title" => "Portal Oscuro",
            "dungeon" => $this->dungeon,
            "character" => $this->character,
            "actualDungeonLevel" => null,
            "firstTime" => true
        ));
	}
	
	public function testPostIndex()
	{
		$this->assertHasFilter("post", "authenticated/event/portal", "before", "auth");
		$this->assertHasFilter("post", "authenticated/event/portal", "before", "hasNoCharacter");
        
        $dungeonId = 5;
        $dungeonLevel = m::mock("DungeonLevel");
        $dungeonBattle = m::mock("DungeonBattle");
        $reportMessage = m::mock("Message");
        $reportMessageId = 6;
        
        Input::replace(array("dungeon_id" => $dungeonId));
        
        $this->dungeon->shouldReceive("find_or_die")->twice()->with($dungeonId)->andReturnSelf();
        
        $this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
        
        $this->dungeon->shouldReceive("get_character_level")->twice()->with($this->character)->andReturn($dungeonLevel);
        
        $this->dungeon->shouldReceive("do_level_or_error")->twice()->with($this->character, $dungeonLevel)->andReturn("sponge bob knows", $dungeonBattle);
        
        $response = $this->post("authenticated/event/portal");
        
        $this->assertSessionHas("error", "sponge bob knows");
        $this->assertRedirectToRoute("get_authenticated_dungeon_index", $response);
        
        $dungeonBattle->shouldReceive("getAttackerReport->getMessage")->once()->andReturn($reportMessage);
        
        $reportMessage->shouldReceive("get_id")->once()->andReturn($reportMessageId);
        
        $response = $this->post("authenticated/event/portal");
        
        $this->assertRedirectTo(URL::to_route("get_authenticated_message_read", array($reportMessageId)), $response);
	}
}