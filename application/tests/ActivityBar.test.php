<?php namespace AresLands\Tests;

class ActivityBarTest extends \Tests\TestHelper
{    
    public function testCharacter()
    {
        $bar = $this->factory->create("ActivityBar");
        $this->assertInstanceOf("Character", $bar->character);
    }
    
    public function testFull()
    {
        $bar = $this->factory->create("ActivityBar");
        $bar->filled_amount = 0;
        
        $this->assertFalse($bar->is_full());
        
        $bar->filled_amount = \Config::get("game.activity_bar_max");
        
        $this->assertTrue($bar->is_full());
    }
    
    public function testReset()
    {
        $bar = $this->factory->create("ActivityBar");
        
        $bar->reset();
        
        $this->assertEquals(0, $bar->filled_amount);
    }
    /*
    public function testAgregar()
    {
        $bar = $this->factory->create("ActivityBar");
        
        $bar->filled_amount = \Config::get("game.activity_bar_max") - 2;
        
        $bar->add(1);
        
        $this->assertEquals(\Config::get("game.activity_bar_max")-1, $bar->filled_amount);
        
        $bar->add(1);
        
        $this->assertEquals(0, $bar->filled_amount);
    }*/
}