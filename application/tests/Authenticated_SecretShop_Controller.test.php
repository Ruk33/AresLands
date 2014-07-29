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
		
		$this->vipFactory->shouldReceive("getAll")->once()->andReturn(array());
		
		$response = $this->get("authenticated/secretShop");
		
		$this->assertViewHasAll($response, array(
			"title" => "Mercado secreto",
			"vipObjects" => array()
		));
		
		$this->assertResponseOk($response);
	}
	
	public function testComprar()
	{
		$this->assertHasFilter(
            "post", "authenticated/secretShop/buy", "before", "auth"
        );
        
		$this->assertHasFilter(
            "post", "authenticated/secretShop/buy", "before", "hasNoCharacter"
        );
		
        $buyer = m::mock("Character");
        
        $this->character
             ->shouldReceive("get_logged")
             ->times(4)
             ->andReturn($buyer);
        
        $attributes = array("id" => 7);
        
        $validator = m::mock("Validator");
        $validator->shouldReceive("fails")->times(4)->andReturn(true, false);
        $validator->errors = $validator;
        $validator->shouldReceive("all")->once()->andReturn(array());
        
        $price = 5;
        
        $vip = m::mock("VipObject");
        $vip->shouldReceive("setBuyer")->times(4)->with($buyer);
        $vip->shouldReceive("setAttributes")->times(4)->with($attributes);
        $vip->shouldReceive("getValidator")->times(4)->andReturn($validator);
        $vip->shouldReceive("getPrice")->times(3)->andReturn($price);
        $vip->shouldReceive("execute")->twice()->andReturn(false, true);
        
        $this->vipFactory
             ->shouldReceive("get")
             ->times(4)
             ->with($attributes["id"])
             ->andReturn($vip);
        
        $user = m::mock("IronFistUser");
        $user->shouldReceive("consume_coins")
             ->times(3)
             ->with($price)
             ->andReturn(false, true);
        
        $this->logIn($user);
        
        // Cuando se consumen las ironcoins pero execute falla, entonces
        // se hace un log del error, el cual requiere lo siguiente
        $buyer->shouldReceive("get_name")->once();
        $vip->shouldReceive("getName")->once();
        
		Input::replace($attributes);
		
		// El validador falla
		$response = $this->post("authenticated/secretShop/buy");        
		
		$this->assertSessionHas("errors", array());
		$this->assertRedirect(
            URL::to_route("get_authenticated_secret_shop_index"), $response
        );
        
        // Consumir las IronCoins falla
        $response = $this->post("authenticated/secretShop/buy");        
		
		$this->assertSessionHas("errors", array(
            "No tienes suficientes IronCoins para comprar este objeto"
        ));
		$this->assertRedirect(
            URL::to_route("get_authenticated_secret_shop_index"), $response
        );
        
        // $vip->execute falla
        $response = $this->post("authenticated/secretShop/buy");        
		
		$this->assertSessionHas("errors", array(
            "Hubo un error al procesar la peticion, por favor notifica a " . 
            "los administradores en el foro."
        ));
		$this->assertRedirect(
            URL::to_route("get_authenticated_secret_shop_index"), $response
        );
		
		// Todo va bien
		$response = $this->post("authenticated/secretShop/buy");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "¡Compra exitosa!",
			"vipObject" => $vip
		));
	}
}