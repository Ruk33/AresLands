<?php

class VipChangeGenderTest extends \Tests\TestHelper
{
    public function testExecute()
    {
        $buyer = Mockery::mock("Character");
        $vip = new VipChangeGender;
        
        $this->assertFalse($vip->execute());
        
        $vip->setBuyer($buyer);
        
        $buyer->shouldReceive("save")->twice()->andReturn(true);
        
        $buyer->shouldReceive("get_gender")->once()->andReturn("female");
        $buyer->shouldReceive("set_gender")->once()->with("male");
        
        $this->assertTrue($vip->execute());
        
        $buyer->shouldReceive("get_gender")->once()->andReturn("male");
        $buyer->shouldReceive("set_gender")->once()->with("female");
        
        $this->assertTrue($vip->execute());
    }
}