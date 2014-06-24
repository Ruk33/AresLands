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
}