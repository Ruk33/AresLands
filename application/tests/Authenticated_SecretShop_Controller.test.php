<?php

use Mockery as m;

class Authenticated_SecretShop_Controller_Test extends Tests\TestHelper
{
	protected $character;
	protected $vipFactory;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->vipFactory = m::mock("VipFactory");
		IoC::instance("VipFactory", $this->vipFactory);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("VipFactory");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/secretShop", "before", "auth");
		$this->assertHasFilter("get", "authenticated/secretShop", "before", "hasNoCharacter");
		
		$this->vipFactory->shouldReceive("get_all")->once()->andReturn(array());
		
		$response = $this->get("authenticated/secretShop");
		
		$this->assertViewHasAll($response, array(
			"title" => "Mercado secreto",
			"vipObjects" => array()
		));
		
		$this->assertResponseOk($response);
	}
	
	public function testComprar()
	{
		$this->assertHasFilter("post", "authenticated/secretShop/buy", "before", "auth");
		$this->assertHasFilter("post", "authenticated/secretShop/buy", "before", "hasNoCharacter");
		
		$attributes = array("id" => 7);
		Input::replace($attributes);
		
		$vipObject = m::mock("IVipObject");
		
		$this->character->shouldReceive("get_logged")->times(3)->andReturnSelf();
		$this->vipFactory->shouldReceive("get")->times(3)->with($attributes["id"])->andReturn($vipObject);
		
		$validator = m::mock("Validator");
		
		$vipObject->shouldReceive("set_attributes")->times(3)->with($this->character, $attributes);
		$vipObject->shouldReceive("get_validator")->times(3)->andReturn($validator);
		
		$validator->shouldReceive("fails")->once()->andReturn(true);
		$validator->errors = $validator;
		$validator->shouldReceive("all")->once()->andReturn(array());
		
		$response = $this->post("authenticated/secretShop/buy");
		
		$this->assertSessionHas("errors", array());
		$this->assertRedirect(URL::to_route("get_authenticated_secret_shop_index"), $response);
		
		$validator->shouldReceive("fails")->twice()->andReturn(false);
		
		$vipObject->shouldReceive("execute")->once()->andReturn(false);
		
		$response = $this->post("authenticated/secretShop/buy");
		
		$this->assertSessionHas("errors", array("No tienes suficientes IronCoins o hubo un error al procesar la peticion"));
		$this->assertRedirect(URL::to_route("get_authenticated_secret_shop_index"), $response);
		
		$vipObject->shouldReceive("execute")->once()->andReturn(true);
		
		$response = $this->post("authenticated/secretShop/buy");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "¡Compra exitosa!",
			"vipObject" => $vipObject
		));
	}
}