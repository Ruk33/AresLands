<?php

use Mockery as m;

class Authenticated_Character_Controller_Test extends \Tests\TestHelper
{
	protected $character;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = Mockery::mock("Character");		
		IoC::instance('Character', $this->character);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		IoC::unregister('Character');
	}
	
	public function testSeguir()
	{
		$this->assertHasFilter("post", "authenticated/character/follow", "before", "auth");
		$this->assertHasFilter("post", "authenticated/character/follow", "before", "hasNoCharacter");
		
		Input::replace(array('id' => 1));
		
		$target = m::mock("Character");
		$this->character->shouldReceive("find_or_die")->with(1)->twice()->andReturn($target);
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("can_follow")->once()->with($target)->andReturn(false);
		$this->character->shouldReceive("follow")->once()->with($target);
		
		$response = $this->post("authenticated/character/follow");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		
		$this->character->shouldReceive("can_follow")->once()->with($target)->andReturn(true);
		
		$response = $this->post("authenticated/character/follow");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
	
	public function testAsignarCaracteristicas()
	{
		$this->assertHasFilter("post", "authenticated/character/characteristics", "before", "auth");
		$this->assertHasFilter("post", "authenticated/character/characteristics", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("get_characteristics")->once()->andReturn(array(1, 2, 3));
		
		$response = $this->post("authenticated/character/characteristics");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		
		$characteristicsInput = array();
		Input::replace(array("characteristics" => $characteristicsInput));
		
		$this->character->shouldReceive("get_characteristics")->once()->andReturnNull();
		$this->character->shouldReceive("set_characteristics_from_array")->once()->with($characteristicsInput);
		
		$response = $this->post("authenticated/character/characteristics");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
	
	public function testAgregarAtributo()
	{
		$this->assertHasFilter("post", "authenticated/character/addStat", "before", "auth");
		$this->assertHasFilter("post", "authenticated/character/addStat", "before", "hasNoCharacter");
		
		$stat_name = 'stat_strength';
		$stat_amount = 7;
		
		Input::replace(compact("stat_name", "stat_amount"));
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("can_add_stat")->once()->with($stat_name, $stat_amount)->andReturn(false);
		
		$this->assertFalse($this->post("authenticated/character/addStat")->content);
		
		$this->character->shouldReceive("can_add_stat")->once()->with($stat_name, $stat_amount)->andReturn(true);
		$this->character->shouldReceive("add_stat")->once()->with($stat_name, $stat_amount);
		
		$this->assertTrue($this->post("authenticated/character/addStat")->content);
	}
	
	public function testMostrar()
	{
		$this->assertHasFilter("get", "authenticated/character/show/1/foo", "before", "auth");
		$this->assertHasFilter("get", "authenticated/character/show/1/foo", "before", "hasNoCharacter");
		
		$name = "foo";
		$target = m::mock("Character");
		
		$this->character->shouldReceive("where_server_id")->twice()->with(1)->andReturnSelf();
		$this->character->shouldReceive("where_name")->twice()->with($name)->andReturnSelf();
		$this->character->shouldReceive("first_or_die")->twice()->andReturn($target);
		
		$orb = m::mock("Orb");
		$target->shouldReceive("orbs->first")->twice()->andReturn($orb);
		
        $target->shouldReceive("check_buffs_time")->twice();
		$target->shouldReceive("update_activities_time")->twice();
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("is_admin")->once()->andReturn(false);
		$this->character->shouldReceive("can_attack_in_pairs")->once()->andReturn(false);
		
		$weapon = m::mock("Item");
		$target->shouldReceive("get_weapon")->twice()->andReturn($weapon);
		
		$shield = m::mock("Item");
		$target->shouldReceive("get_shield")->twice()->andReturn($shield);
		
		$mercenary = m::mock("Item");
		$target->shouldReceive("get_mercenary")->twice()->andReturn($mercenary);
		
		$target->shouldReceive("has_characteristic")->with(Characteristic::RESERVED)->once()->andReturn(false);
		
		$this->character->shouldReceive("get_castable_talents")->twice()->with($target)->andReturn(array());
		
		$target->shouldReceive("get_name")->twice()->andReturn($name);
		
		$response = $this->get("authenticated/character/show/1/{$name}");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => $name,
			"character" => $this->character,
			"orb" => $orb,
			"skills" => array(),
			"characterToSee" => $target,
			"weapon" => $weapon,
			"shield" => $shield,
			"mercenary" => $mercenary,
			"hideStats" => false,
			"castableSkills" => array(),
			"pairs" => array(),
		));
		
		$this->character->shouldReceive("is_admin")->once()->andReturn(true);
		$target->shouldReceive("skills->get")->once()->andReturn(array());
		
		$this->character->shouldReceive("can_attack_in_pairs")->once()->andReturn(true);
		$this->character->shouldReceive("get_pairs_to")->once()->with($target)->andReturn(array($this->character));
		
		$target->shouldReceive("has_characteristic")->with(Characteristic::RESERVED)->once()->andReturn(true);
		
		$response = $this->get("authenticated/character/show/1/{$name}");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => $name,
			"character" => $this->character,
			"orb" => $orb,
			"skills" => array(),
			"characterToSee" => $target,
			"weapon" => $weapon,
			"shield" => $shield,
			"mercenary" => $mercenary,
			"hideStats" => true,
			"castableSkills" => array(),
			"pairs" => array($this->character),
		));
	}
}