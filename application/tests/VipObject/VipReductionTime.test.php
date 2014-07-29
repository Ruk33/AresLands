<?php

class VipReductionTimeTest extends \Tests\TestHelper
{
    private $skill;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->skill = Mockery::mock("Skill");
        \Laravel\IoC::instance("Skill", $this->skill);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        \Laravel\IoC::unregister("Skill");
    }
    
    public function testExecute()
    {
        $buyer = Mockery::mock("Character");
        $vip = new VipReductionTime;
        
        $this->assertFalse($vip->execute());
        
        $vip->setBuyer($buyer);
        
        $this->skill
             ->shouldReceive("find")
             ->once()
             ->with(Config::get('game.vip_reduction_time_skill'))
             ->andReturnSelf();
        
        $this->skill
             ->shouldReceive("cast")
             ->once()
             ->with($buyer, $buyer)
             ->andReturn(true);
        
        $this->assertTrue($vip->execute());
    }
}