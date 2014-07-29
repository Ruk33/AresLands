<?php

class VipFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testObtener()
    {
        $factory = new VipFactory();
        
        $this->assertInstanceOf(
            "VipCoinMultiplier", 
            $factory->get(VipFactory::COIN_MULTIPLIER)
        );
        
        $this->assertInstanceOf(
            "VipXpMultiplier", 
            $factory->get(VipFactory::XP_MULTIPLIER)
        );
        
        $this->assertInstanceOf(
            "VipReductionTime", 
            $factory->get(VipFactory::REDUCTION_TIME)
        );
        
        $this->assertInstanceOf(
            "VipChangeGender", 
            $factory->get(VipFactory::CHANGE_GENDER)
        );
        
        $this->assertInstanceOf(
            "VipChangeName", 
            $factory->get(VipFactory::CHANGE_NAME)
        );
        
        $this->assertInstanceOf(
            "VipChangeRace", 
            $factory->get(VipFactory::CHANGE_RACE)
        );
        
        $this->assertInstanceOf(
            "VipNull", 
            $factory->get(-1)
        );
    }
    
    public function testObtenerTodos()
    {
        $factory = new VipFactory();
        $objects = $factory->getAll();
        
        $this->assertEquals(
            $factory->get(VipFactory::COIN_MULTIPLIER), 
            $objects[VipFactory::COIN_MULTIPLIER]
        );
        
        $this->assertEquals(
            $factory->get(VipFactory::XP_MULTIPLIER), 
            $objects[VipFactory::XP_MULTIPLIER]
        );
        
        $this->assertEquals(
            $factory->get(VipFactory::REDUCTION_TIME), 
            $objects[VipFactory::REDUCTION_TIME]
        );
        
        $this->assertEquals(
            $factory->get(VipFactory::CHANGE_GENDER), 
            $objects[VipFactory::CHANGE_GENDER]
        );
        
        $this->assertEquals(
            $factory->get(VipFactory::CHANGE_NAME), 
            $objects[VipFactory::CHANGE_NAME]
        );
        
        $this->assertEquals(
            $factory->get(VipFactory::CHANGE_RACE), 
            $objects[VipFactory::CHANGE_RACE]
        );
    }
}