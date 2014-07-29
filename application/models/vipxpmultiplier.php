<?php

class VipXpMultiplier extends VipObject
{
	public function getName()
	{
		return 'Multiplicador de experiencia';
	}
	
	public function getIcon()
	{
		return URL::base() . '/img/icons/vip/xp_multiplier.jpg';
	}
	
	public function getDescription()
	{
		return 'Aprovecha mejor los combates, exploraciones y misiones ' .
               'consiguiendo 20% de experiencia extra durante 3 dias.';
	}

	public function getPrice()
	{
		return 20;
	}
	
	public function execute()
	{
		if ($this->buyer) {
            $buffId = Config::get('game.vip_multiplier_xp_rate_skill');
			$buff = \Laravel\IoC::resolve("Skill")->find($buffId);
    
            return $buff->cast($this->buyer, $this->buyer);
		}
        
        return false;
	}
    
    public function getValidator()
    {
        return Laravel\Validator::make($this->attributes, array());
    }
}