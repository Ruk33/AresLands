<?php

use Mockery as m;

class Authenticated_Controller_Test extends Tests\TestHelper
{
	protected $character;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		\Laravel\IoC::instance("Character", $this->character);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		\Laravel\IoC::unregister("Character");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated", "before", "auth");
		$this->assertHasFilter("get", "authenticated", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		
		$this->character
			 ->shouldReceive("give_logged_of_day_reward")
			 ->once()
			 ->with(true);
		
		$this->character
			 ->shouldReceive("skills->get")
			 ->once()
			 ->andReturn(array());
		
		$this->character
			 ->shouldReceive("activities->get")
			 ->once()
			 ->andReturn(array());
		
		$this->character->shouldReceive("orbs->first")->once()->andReturn(null);
		
		$this->character
			 ->shouldReceive("get_castable_talents")
			 ->once()
			 ->with($this->character)
			 ->andReturn(array());
		
		$zone = m::mock("Zone");
		$zone->shouldReceive("get_id")->once()->andReturn(1);
		
		$this->character->shouldReceive("zone->first")->once()->andReturn($zone);
		$this->character->shouldReceive("exploring_times")->once()->andReturnSelf();
		$this->character->shouldReceive("where_zone_id")->once()->with(1)->andReturnSelf();
		$this->character->shouldReceive("first")->once()->andReturn(null);
		
		$this->character->shouldReceive("get_weapon")->once()->andReturn(null);
		$this->character->shouldReceive("get_shield")->once()->andReturn(null);
		$this->character->shouldReceive("get_mercenary")->once()->andReturn(null);
		$this->character
			 ->shouldReceive("get_second_mercenary")
			 ->once()
			 ->andReturn(null);
		
		$this->character->shouldReceive("get_inventory_items")->once()->andReturnSelf();
		$this->character->shouldReceive("with")->once()->with("item")->andReturnSelf();
		$this->character->shouldReceive("get")->once()->andReturn(array());
		
		$this->character->shouldReceive("get_name")->once()->andReturn("foo");
		
		$response = $this->get("authenticated");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"character" => $this->character,
			"weapon" => null,
			"shield" => null,
			"mercenary" => null,
			"secondMercenary" => null,
			"activities" => array(),
			"skills" => array(),
			"orb" => null,
			"talents" => array(),
			"zone" => null,
			"exploringTime" => null,
			"inventoryItems" => array()
		));
	}
	
	public function testLogout()
	{
		$this->assertHasFilter("get", "authenticated/logout", "before", "auth");
		$this->assertHasFilter("get", "authenticated/logout", "before", "hasNoCharacter");
		
		$this->logIn(m::mock("IronFistUser"));
		
		$response = $this->get("authenticated/logout");
		$this->assertRedirect(URL::to_route("get_home_index"), $response);
		
		$this->assertTrue(Auth::guest());
	}
}