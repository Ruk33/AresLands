<?php

class VipXpMultiplier implements IVipObject
{
	public function get_name()
	{
		return 'Multiplicador de experiencia';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/xp_multiplier.jpg';
	}
	
	public function get_description()
	{
		return 'Ganas mas experiencia en misiones, batallas y demas.';
	}

	public function get_price()
	{
		return 2;
	}
	
	public function execute()
	{
		// TODO: dar buffs al usuario logueado
	}
}