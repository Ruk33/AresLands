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
		
	}
}