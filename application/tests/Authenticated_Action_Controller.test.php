<?php

use Mockery as m;

class Authenticated_Action_Controller_Test extends \Tests\TestHelper
{
	protected $character;
	protected $zone;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		Laravel\IoC::instance("Character", $this->character);
		
		$this->zone = m::mock("Zone");
		Laravel\IoC::instance("Zone", $this->zone);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		Laravel\IoC::unregister("Character");
		Laravel\IoC::unregister("Zone");
	}
	
	public function testExplorar()
	{
		$this->assertHasFilter("get", "authenticated/action/explore", "before", "auth");
		$this->assertHasFilter("get", "authenticated/action/explore", "before", "hasNoCharacter");
		
		$response = $this->get("authenticated/action/explore");
		
		$this->assertResponseOk($response);
		$this->assertViewHas($response, "title", "Explorar");
	}
	
	public function testPostExplorar()
	{
		$this->assertHasFilter("post", "authenticated/action/explore", "before", "auth");
		$this->assertHasFilter("post", "authenticated/action/explore", "before", "hasNoCharacter");
		
		Input::replace(array("time" => 5));
		
		$this->character->shouldReceive("get_logged")->times(3)->andReturnSelf();
		$this->character->shouldReceive("can_explore")->times(3)->andReturn(false, true);
		
		$response = $this->post("authenticated/action/explore");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("error", "Aun no puedes explorar");
		
		$this->character->shouldReceive("explore")->once()->with(5*60);
		
		$response = $this->post("authenticated/action/explore");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		
		// tiempo invalido
		Input::replace(array("time" => -9));
		
		$response = $this->post("authenticated/action/explore");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
	
	public function testViajar()
	{
		$this->assertHasFilter("get", "authenticated/action/travel", "before", "auth");
		$this->assertHasFilter("get", "authenticated/action/travel", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("get_travel_zones")->once()->andReturn(array());
		$this->character->shouldReceive("exploring_times->lists")->once()->with("time", "zone_id")->andReturn(array());
		
		$response = $this->get("authenticated/action/travel");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Viajar",
			"character" => $this->character,
			"zones" => array(),
			"exploringTime" => array()
		));
	}
	
	public function testPostViajar()
	{
		$this->assertHasFilter("post", "authenticated/action/travel", "before", "auth");
		$this->assertHasFilter("post", "authenticated/action/travel", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1));
		
		$this->zone->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("can_travel")->twice()->with($this->zone)->andReturn("foo", true);
		
		$response = $this->post("authenticated/action/travel");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("error", "foo");
		
		$this->character->shouldReceive("travel_to")->once()->with($this->zone);
		$this->zone->shouldReceive("get_name")->once()->andReturn("bar");
		
		$response = $this->post("authenticated/action/travel");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("success", "Haz comenzado tu viaje a bar");
	}
}