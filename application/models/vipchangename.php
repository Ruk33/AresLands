<?php

class VipChangeName implements IVipObject
{
	public function get_name()
	{
		return 'Cambio de nombre';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/coin_multiplier.png';
	}
	
	public function get_description()
	{
		return 'Cambias el nombre a tu personaje.';
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