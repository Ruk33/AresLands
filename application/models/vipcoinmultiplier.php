<?php

class VipCoinMultiplier extends VipObject
{
	public function getName()
	{
		return 'Multiplicador de monedas';
	}
	
	public function getIcon()
	{
		return URL::base() . '/img/icons/vip/coin_multiplier.jpg';
	}
	
	public function getDescription()
	{
		return 'Aprovecha mejor los combates, exploraciones y misiones ' .
               'consiguiendo un 30% de oro extra durante 3 dias.';
	}

	public function getPrice()
	{
		return 40;
	}
	
	public function execute()
	{
		if ($this->buyer) {
            $buffId = Config::get('game.vip_multiplier_coin_rate_skill');
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