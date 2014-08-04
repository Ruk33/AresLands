<?php

use Mockery as m;

class Authenticated_Trade_Controller_Test extends Tests\TestHelper
{
	protected $trade;
	protected $character;

	public function setUp()
	{
		parent::setUp();
		
		$this->trade = m::mock("Trade");
		IoC::instance("Trade", $this->trade);
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Trade");
		IoC::unregister("Character");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/trade", "before", "auth");
		$this->assertHasFilter("get", "authenticated/trade", "before", "hasNoCharacter");
		
		$response = $this->get("authenticated/trade");
		$this->assertRedirect(URL::to_route("get_authenticated_trade_category", array("all")), $response);
	}
	
	public function testCategoria()
	{
		$this->assertHasFilter("get", "authenticated/trade/category/foo", "before", "auth");
		$this->assertHasFilter("get", "authenticated/trade/category/foo", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->times(3)->andReturnSelf();
		
		// no importa que la categoria sea cualquier cosa, ya que si no es valida
		// siempre sera "all"
		$this->trade->shouldReceive("with")->once()->with(array("item"))->andReturnSelf();
		$this->trade->shouldReceive("get_valid->get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/trade/category/foo");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Comercios",
			"trades" => array(),
			"character" => $this->character
		));
		
		$this->character->shouldReceive("trades")->once()->andReturn($this->trade);
		$this->trade->shouldReceive("with")->once()->with(array("item"))->andReturnSelf();
		$this->trade->shouldReceive("get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/trade/category/self");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Comercios",
			"trades" => array(),
			"character" => $this->character
		));
		
		$this->trade->shouldReceive("filter_by_item_class")->once()->with("weapon")->andReturnSelf();
		$this->trade->shouldReceive("select")->once()->with(array("trades.*"))->andReturnSelf();
		$this->trade->shouldReceive("get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/trade/category/weapon");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Comercios",
			"trades" => array(),
			"character" => $this->character
		));
	}
	
	public function testNuevo()
	{
		$this->assertHasFilter("get", "authenticated/trade/new", "before", "auth");
		$this->assertHasFilter("get", "authenticated/trade/new", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("can_trade")->once()->andReturn(false);
		
		$response = $this->get("authenticated/trade/new");
		
		$this->assertRedirect(URL::to_route("get_authenticated_trade_index"), $response);
		$this->assertSessionHas("error", "No tienes ningun objeto para comerciar");
		
		$this->character->shouldReceive("can_trade")->once()->andReturn(true);
		$this->character->shouldReceive("tradeable_items->select->get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/trade/new");

		$this->assertViewHasAll($response, array(
			"title" => "Nuevo comercio",
			"character" => $this->character,
			"characterItems" => array()
		));
	}
	
	public function testPostNuevo()
	{
		$this->assertHasFilter("post", "authenticated/trade/new", "before", "auth");
		$this->assertHasFilter("post", "authenticated/trade/new", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->times(3)->andReturnSelf();
		$this->character->shouldReceive("can_trade")->once()->andReturn(false);
		
		$response = $this->post("authenticated/trade/new");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		
		$attributes = array(
			"gold"          => 1,
			"silver"        => 2,
			"copper"        => 0,
			"duration"      => 8,
			"only_clan"     => 0,
			"amount"        => array(9 => 1),
			"trade_item_id" => 9,
		);
		
        $characterItem = m::mock("CharacterItem");
        
        $this->character->shouldReceive("items->where_id")->twice()->with($attributes['trade_item_id'])->andReturn($characterItem);
        
        $characterItem->shouldReceive("first_or_empty")->twice()->andReturn($characterItem);
        $characterItem->shouldReceive("get_item_id")->twice()->andReturn($attributes['trade_item_id']);
        $characterItem->shouldReceive("get_attribute")->twice()->with("data")->andReturn("foo");
        
		$this->character->shouldReceive("can_trade")->twice()->andReturn(true);
		
		Input::replace($attributes);
		
		$this->trade->shouldReceive("create_instance")->twice()->andReturnSelf();
		
		$this->character->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->trade->shouldReceive("validate")->once()->andReturn(false);
		$this->trade->shouldReceive("errors->all")->once()->andReturn(array());
		
		$response = $this->post("authenticated/trade/new");
		
		$this->assertRedirect(URL::to_route("get_authenticated_trade_new"), $response);
		$this->assertWithInputs();
		$this->assertSessionHas("errors", array());
		
		$this->trade->shouldReceive("validate")->once()->andReturn(true);
		$this->trade->shouldReceive("save")->once();
		
		$characterItem->shouldReceive("get_count")->once()->andReturn(5);
		$characterItem->shouldReceive("set_count")->once()->with(4);
		$characterItem->shouldReceive("save")->once();
		
		$response = $this->post("authenticated/trade/new");
		
		$this->assertRedirect(URL::to_route("get_authenticated_trade_index"), $response);
		$this->assertSessionHas("success", "Comercio creado con exito");
	}
	
	public function testCancelar()
	{
		$this->assertHasFilter("post", "authenticated/trade/cancel", "before", "auth");
		$this->assertHasFilter("post", "authenticated/trade/cancel", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1));
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("trades")->twice()->andReturn($this->trade);
		
		$this->trade->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->trade->shouldReceive("cancel")->once()->andReturn(false);
		
		$response = $this->post("authenticated/trade/cancel");
		
		$this->assertRedirect(URL::to_route("get_authenticated_trade_index"), $response);
		$this->assertSessionHas("error", "El comercio no pudo ser cancelado. Verifica que tengas espacio en tu inventario");
		
		$this->trade->shouldReceive("cancel")->once()->andReturn(true);
		
		$response = $this->post("authenticated/trade/cancel");
		
		$this->assertRedirect(URL::to_route("get_authenticated_trade_index"), $response);
		$this->assertSessionHas("success", "El comercio ha sido cancelado");
	}
	
	public function testComprar()
	{
		$this->assertHasFilter("post", "authenticated/trade/buy", "before", "auth");
		$this->assertHasFilter("post", "authenticated/trade/buy", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1));
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->trade->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		
		$this->trade->shouldReceive("buy")->once()->with($this->character)->andReturn(false);
		
		$response = $this->post("authenticated/trade/buy");
		
		$this->assertRedirect(URL::to_route("get_authenticated_trade_index"), $response);
		$this->assertSessionHas("error", "No puedes comprar el objeto porque o no tienes espacio en tu inventario o no tienes suficientes monedas");
		
		$this->trade->shouldReceive("buy")->once()->with($this->character)->andReturn(true);
		
		$response = $this->post("authenticated/trade/buy");
		
		$this->assertRedirect(URL::to_route("get_authenticated_trade_index"), $response);
		$this->assertSessionHas("success", "Compraste el objeto exitosamente");
	}
}