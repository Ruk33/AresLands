<?php

class ActivityBarTest extends PHPUnit_Framework_TestCase
{
    public function testCharacter()
    {
        $bar = new ActivityBar();
        $this->assertInstanceOf("Character", $bar->character);
    }
    
    
}