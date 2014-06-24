<?php

use Mockery as m;

class Authenticated_Orb_Controller_Test extends \Tests\TestHelper
{
	protected $character;
	protected $orb;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->orb = m::mock("Orb");
		IoC::instance("Orb", $this->orb);
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/orb", "before", "auth");
		$this->assertHasFilter("get", "authenticated/orb", "before", "hasNoCharacter");
		
		$this->orb->shouldReceive("order_by")->with("min_level", "asc")->once()->andReturnSelf();
		$this->orb->shouldReceive("get")->once()->andReturn(array());
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		
		$response = $this->get("authenticated/orb");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Orbes",
			"character" => $this->character,
			"orbs" => array()
		));
	}
	
	public function testReclamar()
	{
		$this->assertHasFilter("get", "authenticated/orb/1/claim", "before", "auth");
		$this->assertHasFilter("get", "authenticated/orb/1/claim", "before", "hasNoCharacter");
		
		$this->orb->shouldReceive("find_or_die")->with(1)->times(3)->andReturnSelf();
		
		$this->orb->shouldReceive("get_owner_id")->once()->andReturn(1);
		
		$response = $this->get("authenticated/orb/1/claim");
		$this->assertRedirect(URL::to_route("get_authenticated_orb_index"), $response);
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$this->orb->shouldReceive("get_owner_id")->twice()->andReturnNull();
		$this->orb->shouldReceive("can_be_stolen_by")->with($this->character)->once()->andReturn(false);
		
		$response = $this->get("authenticated/orb/1/claim");
		$this->assertRedirect(URL::to_route("get_authenticated_orb_index"), $response);
		
		$this->orb->shouldReceive("can_be_stolen_by")->with($this->character)->once()->andReturn(true);
		$this->orb->shouldReceive("give_to")->with($this->character)->once();
		
		$response = $this->get("authenticated/orb/1/claim");
		$this->assertRedirect(URL::to_route("get_authenticated_orb_index"), $response);
	}
}