<?php

class VipCoinMultiplier implements IVipObject
{
	public function get_name()
	{
		return 'Multiplicador de monedas';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/coin_multiplier.png';
	}
	
	public function get_description()
	{
		return 'Ganas un 50% mas de monedas en exploraciones, misiones y batallas.';
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