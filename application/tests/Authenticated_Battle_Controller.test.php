<?php

use Mockery as m;
use Laravel\IoC as IoC;

class Authenticated_Battle_Controller_Test extends Tests\TestHelper
{
	protected $character;
	protected $monster;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->monster = m::mock("Monster");
		IoC::instance("Monster", $this->monster);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("Monster");
	}
	
	public function testIndex()
	{	
		$this->assertHasFilter("get", "authenticated/battle", "before", "auth");
		$this->assertHasFilter("get", "authenticated/battle", "before", "hasNoCharacter");
		
		$zone = m::mock("Zone");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("zone->results")->once()->andReturn($zone);
		
		$this->monster->shouldReceive("get_from_zone")->once()->with($zone, $this->character)->andReturnSelf();
		$this->monster->shouldReceive("order_by")->once()->with("level", "asc")->andReturnSelf();
		$this->monster->shouldReceive("get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/battle");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Batallar",
			"character" => $this->character,
			"monsters" => array()
		));
	}
	
	public function testBuscarTieneFiltros()
	{
		$this->assertHasFilter("post", "authenticated/battle/search", "before", "auth");
		$this->assertHasFilter("post", "authenticated/battle/search", "before", "hasNoCharacter");
	}
	
	public function testMetodoInvalidoBuscar()
	{
		Input::replace(array("search_method" => "foo"));
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		
		$response = $this->post("authenticated/battle/search");
		
		$this->assertRedirect(URL::to_route("get_authenticated_battle_index"), $response);
		$this->assertSessionHas("error", "Metodo de busqueda incorrecto");
	}
	
	public function testBuscarPorNombre()
	{
		$target = m::mock("Character");
		$weapon = m::mock("Item");
		$shield = m::mock("Item");
		$mercenary = m::mock("Item");
		
		Input::replace(array("search_method" => "name", "character_name" => "foo"));

		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("first")->twice()->andReturn(null, $target);

		$this->character->shouldReceive("get_opponent")->twice()->andReturnSelf();
		$this->character->shouldReceive("where_name")->twice()->with("foo")->andReturnSelf();

		$response = $this->post("authenticated/battle/search");

		$this->assertRedirect(URL::to_route("get_authenticated_battle_index"), $response);
		$this->assertSessionHas("error", "No se encontro ningun personaje para batallar");

		$this->character->shouldReceive("sees")->once()->with($target);

        $target->shouldReceive("check_buffs_time")->once();
		$target->shouldReceive("get_weapon")->once()->andReturn($weapon);
		$target->shouldReceive("get_shield")->once()->andReturn($shield);
		$target->shouldReceive("get_mercenary")->once()->andReturn($mercenary);

		$this->character->shouldReceive("get_castable_talents")->once()->with($target)->andReturn(array());

		$target->shouldReceive("orbs->first")->once()->andReturn(null);

		$this->character->shouldReceive("get_pairs_to")->once()->with($target)->andReturn(array());

		$target->shouldReceive("has_characteristic")->once()->with(Characteristic::RESERVED)->andReturn(false);
		$target->shouldReceive("get_name")->once()->andReturn("foo");

		$response = $this->post("authenticated/battle/search");

		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"character" => $this->character, 
			"weapon" => $weapon, 
			"shield" => $shield, 
			"mercenary" => $mercenary,
			"orb" => null, 
			"skills" => array(),
			"characterToSee" => $target, 
			"hideStats" => false, 
			"castableSkills" => array(), 
			"pairs" => array()
		));
	}
	
	public function testBuscarAleatoriamente()
	{
		$target = m::mock("Character");
		$weapon = m::mock("Item");
		$shield = m::mock("Item");
		$mercenary = m::mock("Item");
		
		Input::replace(array(
			"search_method" => "random",
			"operator" => "invalid", // debe ser reemplazado por =
			"race" => "human,dwarf",
			"level" => 5
		));

		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("first")->twice()->andReturn(null, $target);

		$this->character->shouldReceive("get_opponent")->twice()->with(array("human", "dwarf"))->andReturnSelf();
		$this->character->shouldReceive("where")->twice()->with("level", "=", 5)->andReturnSelf();
		$this->character->shouldReceive("order_by")->twice()->andReturnSelf();

		$response = $this->post("authenticated/battle/search");

		$this->assertRedirect(URL::to_route("get_authenticated_battle_index"), $response);
		$this->assertSessionHas("error", "No se encontro ningun personaje para batallar");

		$this->character->shouldReceive("sees")->once()->with($target);

		$target->shouldReceive("check_buffs_time")->once();
		$target->shouldReceive("get_weapon")->once()->andReturn($weapon);
		$target->shouldReceive("get_shield")->once()->andReturn($shield);
		$target->shouldReceive("get_mercenary")->once()->andReturn($mercenary);

		$this->character->shouldReceive("get_castable_talents")->once()->with($target)->andReturn(array());

		$target->shouldReceive("orbs->first")->once()->andReturn(null);

		$this->character->shouldReceive("get_pairs_to")->once()->with($target)->andReturn(array());

		$target->shouldReceive("has_characteristic")->once()->with(Characteristic::RESERVED)->andReturn(false);
		$target->shouldReceive("get_name")->once()->andReturn("foo");

		$response = $this->post("authenticated/battle/search");

		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"character" => $this->character, 
			"weapon" => $weapon, 
			"shield" => $shield, 
			"mercenary" => $mercenary,
			"orb" => null, 
			"skills" => array(),
			"characterToSee" => $target, 
			"hideStats" => false, 
			"castableSkills" => array(), 
			"pairs" => array()
		));
	}
	
	public function testBuscarEnGrupo()
	{
		$target = m::mock("Character");
		$weapon = m::mock("Item");
		$shield = m::mock("Item");
		$mercenary = m::mock("Item");
		
		Input::replace(array(
			"search_method" => "group",
			"clan" => 5
		));

		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("first")->twice()->andReturn(null, $target);

		$this->character->shouldReceive("get_opponent")->twice()->andReturnSelf();
		$this->character->shouldReceive("where_clan_id")->twice()->with(5)->andReturnSelf();
		$this->character->shouldReceive("order_by")->twice()->andReturnSelf();

		$response = $this->post("authenticated/battle/search");

		$this->assertRedirect(URL::to_route("get_authenticated_battle_index"), $response);
		$this->assertSessionHas("error", "No se encontro ningun personaje para batallar");

		$this->character->shouldReceive("sees")->once()->with($target);

		$target->shouldReceive("check_buffs_time")->once();
		$target->shouldReceive("get_weapon")->once()->andReturn($weapon);
		$target->shouldReceive("get_shield")->once()->andReturn($shield);
		$target->shouldReceive("get_mercenary")->once()->andReturn($mercenary);

		$this->character->shouldReceive("get_castable_talents")->once()->with($target)->andReturn(array());

		$target->shouldReceive("orbs->first")->once()->andReturn(null);

		$this->character->shouldReceive("get_pairs_to")->once()->with($target)->andReturn(array());

		$target->shouldReceive("has_characteristic")->once()->with(Characteristic::RESERVED)->andReturn(false);
		$target->shouldReceive("get_name")->once()->andReturn("foo");

		$response = $this->post("authenticated/battle/search");

		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"character" => $this->character, 
			"weapon" => $weapon, 
			"shield" => $shield, 
			"mercenary" => $mercenary,
			"orb" => null, 
			"skills" => array(),
			"characterToSee" => $target, 
			"hideStats" => false, 
			"castableSkills" => array(), 
			"pairs" => array()
		));
	}
	
	public function testBatallarPersonaje()
	{
		$this->assertHasFilter("post", "authenticated/battle/character", "before", "auth");
		$this->assertHasFilter("post", "authenticated/battle/character", "before", "hasNoCharacter");
		
		Input::replace(array("name" => "foo", "pair" => 1));
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$target = m::mock("Character");
		$this->character->shouldReceive("where_name")->twice()->with("foo")->andReturnSelf();
		$this->character->shouldReceive("first_or_die")->twice()->andReturn($target);
		
		$pair = m::mock("Character");
		$this->character->shouldReceive("find")->twice()->with(1)->andReturn($pair);
		
		$battle = m::mock("Battle");
		$this->character->shouldReceive("battle_or_error")->twice()->with($target, $pair)->andReturn("bar", $battle);
		
		$response = $this->post("authenticated/battle/character");
		
		$this->assertRedirect(URL::to_route("get_authenticated_battle_index"), $response);
		$this->assertSessionHas("error", "bar");
		
		$message = m::mock("Message");
		$battle->shouldReceive("getAttackerReport->getMessage")->once()->andReturn($message);
		
		$message->shouldReceive("get_id")->once()->andReturn(1);
		
		$response = $this->post("authenticated/battle/character");
		$this->assertRedirect(URL::to_route("get_authenticated_message_read", array(1)), $response);
	}
	
	public function testBatallarMonstruo()
	{
		$this->assertHasFilter("post", "authenticated/battle/monster", "before", "auth");
		$this->assertHasFilter("post", "authenticated/battle/monster", "before", "hasNoCharacter");
		
		Input::replace(array("monster_id" => 1));
		
		$this->monster->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$battle = m::mock("Battle");
		$this->character->shouldReceive("battle_or_error")->twice()->with($this->monster)->andReturn("bar", $battle);
		
		$response = $this->post("authenticated/battle/monster");
		
		$this->assertRedirect(URL::to_route("get_authenticated_battle_index"), $response);
		$this->assertSessionHas("error", "bar");
		
		$message = m::mock("Message");
		$battle->shouldReceive("getAttackerReport->getMessage")->once()->andReturn($message);
		
		$message->shouldReceive("get_id")->once()->andReturn(1);
		
		$response = $this->post("authenticated/battle/monster");
		$this->assertRedirect(URL::to_route("get_authenticated_message_read", array(1)), $response);
	}
}