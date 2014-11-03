<?php namespace Models\Vip;

use Character;
use Laravel\URL;

class Vip extends \Base_Model implements VipRepository
{
    public function getDescription()
    {
        return $this->description;
    }

    public function getIcon()
    {
        return URL::base() . $this->icon;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function isEnabled()
    {
        return (bool) $this->enabled;
    }
    
    public function getVipImplementation(Character $buyer = null, 
                                         array $attributes = array())
    {
        $buyer = $buyer ?: new Character();
        
        switch ($this->id) {
            case 1:
                return new VipChangeNameImplementation($buyer, $attributes);
                
            case 2:
                return new VipChangeGenderImplementation($buyer, $attributes);
                
            case 3:
                return new VipChangeRaceImplementation($buyer, $attributes);
                
            case 4:
                return new VipCoinMultiplierImplementation($buyer, $attributes);
                
            case 5:
                return new VipReductionTimeImplementation($buyer, $attributes);
                
            case 6:
                return new VipXpMultiplierImplementation($buyer, $attributes);
                
            case 7:
                return new VipHalloweenScytheImplementation($buyer, $attributes);
        }
    }
}