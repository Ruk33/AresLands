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
		$this->assertHasFilter("get", "authenticated/dungeon", "before", "auth");
		$this->assertHasFilter("get", "authenticated/dungeon", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->dungeon->shouldReceive("available_for")->once()->with($this->character)->andReturnSelf();
		$this->dungeon->shouldReceive("get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/dungeon");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Mazmorras",
			"character" => $this->character,
			"dungeons" => array()
		));
	}
	
	public function testPostIndex()
	{
		$this->assertHasFilter("post", "authenticated/dungeon", "before", "auth");
		$this->assertHasFilter("post", "authenticated/dungeon", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1));
		
		$this->dungeon
			 ->shouldReceive("find_or_die")
			 ->times(3)
			 ->with(1)
			 ->andReturnSelf();
		
		$this->character
			 ->shouldReceive("get_logged")
			 ->times(3)
			 ->andReturnSelf();
		
		$this->dungeon
			 ->shouldReceive("can_character_do_dungeon")
			 ->times(3)
			 ->with($this->character)
			 ->andReturn("foo", true);
		
		$response = $this->post("authenticated/dungeon");
		
		$this->assertRedirect(
			URL::to_route("get_authenticated_index"), 
			$response
		);
		
		$this->assertSessionHas("error", "foo");
		
		$dungeonBattle = m::mock("DungeonBattle");
		
		$this->character
			 ->shouldReceive("do_dungeon")
			 ->twice()
			 ->with($this->dungeon)
			 ->andReturn($dungeonBattle);
		
		$dungeonBattle->shouldReceive("get_completed")
					  ->twice()
					  ->andReturn(false, true);
		
		$response = $this->post("authenticated/dungeon");
		
		$this->assertRedirect(
			URL::to_route("get_authenticated_index"), 
			$response
		);
		
		$this->assertSessionHas(
			"error", 
			"Uno de los monstruos de la mazmorra te ha derrotado"
		);
		
		$response = $this->post("authenticated/dungeon");
		
		$this->assertRedirect(
			URL::to_route("get_authenticated_index"), 
			$response
		);
		
		$this->assertSessionHas("success", "¡Haz completado la mazmorra!");
	}
}