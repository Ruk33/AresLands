<?php

class VipChangeNameTest extends \Tests\TestHelper
{
    public function testExecute()
    {
        $buyer = Mockery::mock("Character");
        $vip = new VipChangeName;
        $name = "foo";
        
        $this->assertFalse($vip->execute());
        
        $vip->setAttributes(compact("name"));
        $vip->setBuyer($buyer);
        
        $buyer->shouldReceive("save")->once()->andReturn(true);
        $buyer->shouldReceive("set_name")->once()->with($name);
        
        $this->assertTrue($vip->execute());
    }
}