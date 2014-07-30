<?php

use Mockery as m;

class Authenticated_Ranking_Controller_Test extends \Tests\TestHelper
{
	protected $character;
	protected $kingOfTheHill;
	protected $clanOrbPoint;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		$this->kingOfTheHill = m::mock("KingOfTheHill");
		$this->clanOrbPoint = m::mock("ClanOrbPoint");
		
		\Laravel\IoC::instance("Character", $this->character);
		\Laravel\IoC::instance("KingOfTheHill", $this->kingOfTheHill);
		\Laravel\IoC::instance("ClanOrbPoint", $this->clanOrbPoint);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		\Laravel\IoC::unregister("Character");
		\Laravel\IoC::unregister("KingOfTheHill");
		\Laravel\IoC::unregister("ClanOrbPoint");
	}
	
//	public function testValorPorDefectoOIncorrectoRedireccionaAKingOfTheHill()
//	{
//		$this->kingOfTheHill->shouldReceive("get_list")->once();
//		
//		$response = $this->get("authenticated/ranking");
//		$this->assertResponseOk($response);
//		
//		$response = $this->get("authenticated/ranking/foo");
//		$this->assertRedirect(URL::to_route("get_authenticated_ranking_index"), $response);
//	}
	
//	public function testKingOfTheHill()
//	{
//		$this->assertHasFilter("get", "authenticated/ranking/kingOfTheHill", "before", "auth");
//		$this->assertHasFilter("get", "authenticated/ranking/kingOfTheHill", "before", "hasNoCharacter");
//		
//		$this->kingOfTheHill->shouldReceive("get_list")->once()->andReturn(array());
//		
//		$response = $this->get("authenticated/ranking/kingOfTheHill");
//		
//		$this->assertResponseOk($response);
//		$this->assertViewHasAll($response, array(
//			"title" => "Ranking",
//            "pagination" => null,
//			"elements" => array()
//		));
//	}
	
	public function testPvp()
	{
		$this->assertHasFilter("get", "authenticated/ranking/pvp", "before", "auth");
		$this->assertHasFilter("get", "authenticated/ranking/pvp", "before", "hasNoCharacter");
		
		$this->character
			 ->shouldReceive("with")
			 ->once()
			 ->with("clan")
			 ->andReturnSelf();
		
		$this->character
			 ->shouldReceive("get_characters_for_pvp_ranking")
			 ->once()
			 ->andReturnSelf();
        
        $this->character
             ->shouldReceive("paginate")
             ->once()
             ->with(50)
             ->andReturnSelf();
        
        $this->character
             ->shouldReceive("get_results")
             ->once()
             ->andReturn(array());
		
		$response = $this->get("authenticated/ranking/pvp");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Ranking", 
            "pagination" => $this->character,
			"elements" => array()
		));
	}
    
    public function testClan()
    {
        $this->assertHasFilter("get", "authenticated/ranking/clan", "before", "auth");
		$this->assertHasFilter("get", "authenticated/ranking/clan", "before", "hasNoCharacter");
        
        $this->clanOrbPoint
             ->shouldReceive("with")
             ->once()
             ->with("clan")
             ->andReturnSelf();
        
        $this->clanOrbPoint
             ->shouldReceive("order_by")
             ->once()
             ->with("points", "desc")
             ->andReturnSelf();
        
        $this->clanOrbPoint
             ->shouldReceive("paginate")
             ->once()
             ->with(50)
             ->andReturnSelf();
        
        $this->clanOrbPoint
             ->shouldReceive("get_results")
             ->once()
             ->andReturn(array());
        
        $response = $this->get("authenticated/ranking/clan");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Ranking", 
            "pagination" => $this->clanOrbPoint,
			"elements" => array()
		));
    }
}