<?php

use Mockery as m;

class Authenticated_Inventory_Controller_Test extends Tests\TestHelper
{
	protected $character;
	protected $characterItem;
	protected $item;

	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->characterItem = m::mock("CharacterItem");
		IoC::instance("CharacterItem", $this->characterItem);
		
		$this->item = m::mock("Item");
		IoC::instance("Item", $this->item);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("CharacterItem");
		IoC::unregister("Item");
	}
	
	public function testDestruir()
	{
		$this->assertHasFilter("post", "authenticated/inventory/destroy", "before", "auth");
		$this->assertHasFilter("post", "authenticated/inventory/destroy", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1));
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("items->find_or_die")->once()->with(1)->andReturn($this->characterItem);
		
		$this->characterItem->shouldReceive("item->results")->once()->andReturn($this->item);
		$this->item->shouldReceive("get_destroyable")->once()->andReturn(false);
		
		$response = $this->post("authenticated/inventory/destroy");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
	
	public function testUsar()
	{
		$this->assertHasFilter("post", "authenticated/inventory/use", "before", "auth");
		$this->assertHasFilter("post", "authenticated/inventory/use", "before", "hasNoCharacter");
		
		Input::replace(array("id" => 1, "amount" => 333));
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("items->find_or_die")->twice()->with(1)->andReturn($this->characterItem);
		$this->character->shouldReceive("use_inventory_item")->once()->with($this->characterItem, 333)->andReturn("foo");
		
		$response = $this->post("authenticated/inventory/use");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("error", "foo");
		
		$this->character->shouldReceive("use_inventory_item")->once()->with($this->characterItem, 333)->andReturn(true);
		$response = $this->post("authenticated/inventory/use");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
}