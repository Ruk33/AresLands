<?php namespace Tests;

use Mockery as m;
use IoC;
use Input;
use Response;
use Redirect;

require("./application/controllers/authenticated/secretshop.php");

abstract class Authenticated_SecretShop_Controller_Test extends AuthenticatedHelper
{
	protected $vipFactory;
	protected $controller;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->auth->character = m::mock("Character");
		
		$this->vipFactory = m::mock("VipFactory");
		IoC::instance("VipFactory", $this->vipFactory);
		
		$this->controller = IoC::resolve("Authenticated_SecretShop_Controller");
	}
	
	public function testIndex()
	{
		$this->vipFactory->shouldReceive("get_all")->once();
		
		$this->controller->get_index();
		
		$this->assertEquals("Mercado secreto", $this->controller->layout->title);
		$this->assertArrayHasKey("vipObjects", $this->controller->layout->content->data);
	}
	
	/*public function testPostComprar()
	{
		$attributes = array('id' => 1);
		Input::replace($attributes);
	}*/
}