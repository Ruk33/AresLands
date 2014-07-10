<?php namespace AresLands\Tests;

class ActivityBarTest extends \Tests\TestHelper
{
    public function testCharacter()
    {
        $bar = $this->factory->create("ActivityBar");
        $this->assertInstanceOf("Character", $bar->character);
    }
    
    
}