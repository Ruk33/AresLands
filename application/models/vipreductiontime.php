<?php

class VipReductionTime extends VipObject
{	
	public function getName()
	{
		return 'Reductor de tiempos';
	}
	
	public function getIcon()
	{
		return URL::base() . '/img/icons/vip/reduction_time.jpg';
	}
	
	public function getDescription()
	{
		return 'Reduce tus tiempos de viaje y descanzos ' .
               'en un 20% durante 3 dias.';
	}

	public function getPrice()
	{
		return 20;
	}
	
	public function execute()
	{
		if ($this->buyer) {
            $buffId = Config::get('game.vip_reduction_time_skill');
            $buff = \Laravel\IoC::resolve("Skill")->find($buffId);
			
            return $buff->cast($this->buyer, $this->buyer);
		}
        
        return false;
	}

	public function getValidator()
	{
		return \Laravel\Validator::make($this->attributes, array());
	}
}