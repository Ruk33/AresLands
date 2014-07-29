<?php

class VipChangeRaceTest extends \Tests\TestHelper
{
    public function testExecute()
    {
        $buyer = Mockery::mock("Character");
        $vip = new VipChangeRace;
        $race = "dwarf";
        
        $this->assertFalse($vip->execute());
        
        $vip->setAttributes(compact("race"));
        $vip->setBuyer($buyer);
        
        $buyer->shouldReceive("set_race")->once()->with($race);
        $buyer->shouldReceive("save")->once()->andReturn(true);
        
        $this->assertTrue($vip->execute());
    }
}