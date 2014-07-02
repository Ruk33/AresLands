<?php

use Mockery as m;

class Authenticated_Npc_Controller_Test extends Tests\TestHelper
{
	protected $character;
	protected $npc;
	protected $npcMerchandise;
	protected $npcRandomMerchandise;

	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->npc = m::mock("Npc");
		IoC::instance("Npc", $this->npc);
		
		$this->npcMerchandise = m::mock("NpcMerchandise");
		IoC::instance("NpcMerchandise", $this->npcMerchandise);
		
		$this->npcRandomMerchandise = m::mock("NpcRandomMerchandise");
		IoC::instance("NpcRandomMerchandise", $this->npcRandomMerchandise);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("Npc");
		IoC::unregister("NpcMerchandise");
		IoC::unregister("NpcRandomMerchandise");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/npc/1", "before", "auth");
		$this->assertHasFilter("get", "authenticated/npc/1", "before", "hasNoCharacter");

		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("get_zone_id")->twice()->andReturn(2);
		
		$this->npc->shouldReceive("where_id")->twice()->with(1)->andReturnSelf();
		$this->npc->shouldReceive("where_zone_id")->twice()->with(2)->andReturnSelf();
		$this->npc->shouldReceive("first_or_die")->twice()->andReturnSelf();
		
		$this->npc->shouldReceive("is_blocked_to")->once()->with($this->character)->andReturn(true);
		
		$response = $this->get("authenticated/npc/1");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		
		$this->npc->shouldReceive("is_blocked_to")->once()->with($this->character)->andReturn(false);
		
		$this->npc->shouldReceive("fire_global_event")->with("npcTalk", array($this->character, $this->npc))->once();
		
		$this->npc->shouldReceive("get_merchandises_for")->once()->with($this->character)->andReturnSelf();
		$this->npc->shouldReceive("with")->once()->with("item")->andReturnSelf();
		$this->npc->shouldReceive("get")->once()->andReturn(array());
		
		$this->npc->shouldReceive("available_quests_of")->once()->with($this->character)->andReturnSelf();
		$this->npc->shouldReceive("order_by")->once()->with("max_level", "asc")->andReturnSelf();
		$this->npc->shouldReceive("get")->once()->andReturn(array());
		
		$this->npc->shouldReceive("repeatable_quests_of")->once()->with($this->character)->andReturnSelf();
		$this->npc->shouldReceive("get")->once()->andReturn(array());
		
		$this->npc->shouldReceive("started_quests_of")->once()->with($this->character)->andReturnSelf();
		$this->npc->shouldReceive("get")->once()->andReturn(array());
		
		$this->npc->shouldReceive("reward_quests_of")->once()->with($this->character)->andReturnSelf();
		$this->npc->shouldReceive("get")->once()->andReturn(array());
		
		$coins = m::mock("Item");
		$this->character->shouldReceive("get_coins")->once()->andReturn($coins);
		$coins->shouldReceive("get_count")->once()->andReturn(0);
		
		$this->npc->shouldReceive("get_name")->once()->andReturn("foo");
		
		$response = $this->get("authenticated/npc/1");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"character" => $this->character,
			"characterCoinsCount" => 0,
			"merchandises" => array(),
			"rewardQuests" => array(),
			"startedQuests" => array(),
			"repeatableQuests" => array(),
			"quests" => array()
		));
	}
	
	public function testComprar()
	{
		$this->assertHasFilter("post", "authenticated/npc/buy", "before", "auth");
		$this->assertHasFilter("post", "authenticated/npc/buy", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1, "amount" => 3));
		
		$this->npcMerchandise->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->npcMerchandise->shouldReceive("npc->first_or_die")->twice()->andReturn($this->npc);
		
		$this->character->shouldReceive("get_logged")->times(4)->andReturnSelf();
		
		$this->npc->shouldReceive("try_buy")->once()->with($this->character, $this->npcMerchandise, 3)->andReturn("foo");
		$this->npc->shouldReceive("get_id")->times(4)->andReturn(1);
		$this->npc->shouldReceive("get_name")->times(4)->andReturn("bar");
		
		$response = $this->post("authenticated/npc/buy");
		
		$this->assertRedirect(URL::to_route("get_authenticated_npc_index", array(1, "bar")), $response);
		$this->assertSessionHas("error", "foo");
		
		$this->npc->shouldReceive("try_buy")->once()->with($this->character, $this->npcMerchandise, 3)->andReturn(true);
		
		$response = $this->post("authenticated/npc/buy");
		
		$this->assertRedirect(URL::to_route("get_authenticated_npc_index", array(1, "bar")), $response);
		$this->assertSessionHas("success", "Gracias por tu compra, ¿te interesa algo mas?");
		
		Input::replace(array("id" => 1, "amount" => 3, "random_merchandise" => true));
		
		$this->npcRandomMerchandise->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->npcRandomMerchandise->shouldReceive("npc->first_or_die")->twice()->andReturn($this->npc);
		
		$this->npc->shouldReceive("try_buy")->once()->with($this->character, $this->npcRandomMerchandise, 3)->andReturn("foo");
		
		$response = $this->post("authenticated/npc/buy");
		
		$this->assertRedirect(URL::to_route("get_authenticated_npc_index", array(1, "bar")), $response);
		$this->assertSessionHas("error", "foo");
		
		$this->npc->shouldReceive("try_buy")->once()->with($this->character, $this->npcRandomMerchandise, 3)->andReturn(true);
		
		$response = $this->post("authenticated/npc/buy");
		
		$this->assertRedirect(URL::to_route("get_authenticated_npc_index", array(1, "bar")), $response);
		$this->assertSessionHas("success", "Gracias por tu compra, ¿te interesa algo mas?");
	}
}