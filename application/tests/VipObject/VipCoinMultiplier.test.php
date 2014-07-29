<?php

use Mockery as m;

class VipCoinMultiplierTest extends \Tests\TestHelper
{
    private $skill;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->skill = m::mock("Skill");
        \Laravel\IoC::instance("Skill", $this->skill);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        \Laravel\IoC::unregister("Skill");
    }
    
    public function testExecute()
    {
        $character = new Character;
        $vip = new VipCoinMultiplier;
        
        $this->assertFalse($vip->execute());
        
        $vip->setBuyer($character);
        
        $this->skill
             ->shouldReceive("find")
             ->once()
             ->with(Config::get('game.vip_multiplier_coin_rate_skill'))
             ->andReturnSelf();
        
        $this->skill
             ->shouldReceive("cast")
             ->once()
             ->with($character, $character)
             ->andReturn(true);
        
        $this->assertTrue($vip->execute());
    }
}