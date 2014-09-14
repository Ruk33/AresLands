<?php

use Mockery as m;

class Authenticated_Ranking_Controller_Test extends \Tests\TestHelper
{
	protected $character;
	protected $kingOfTheHill;
	protected $clan;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		$this->kingOfTheHill = m::mock("KingOfTheHill");
		$this->clan = m::mock("Clan");
		
		\Laravel\IoC::instance("Character", $this->character);
		\Laravel\IoC::instance("KingOfTheHill", $this->kingOfTheHill);
		\Laravel\IoC::instance("Clan", $this->clan);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		\Laravel\IoC::unregister("Character");
		\Laravel\IoC::unregister("KingOfTheHill");
		\Laravel\IoC::unregister("Clan");
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
                
        $this->clan
             ->shouldReceive("get_clans_for_ranking")
             ->once()
             ->andReturnSelf();
        
        $this->clan
             ->shouldReceive("paginate")
             ->once()
             ->with(50)
             ->andReturnSelf();
        
        $this->clan
             ->shouldReceive("get_results")
             ->once()
             ->andReturn(array());
        
        $response = $this->get("authenticated/ranking/clan");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Ranking", 
            "pagination" => $this->clan,
			"elements" => array()
		));
    }
}