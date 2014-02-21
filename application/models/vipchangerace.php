<?php

class VipChangeRace implements IVipObject
{
	public function get_name()
	{
		return 'Cambio de raza';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/change_race.jpg';
	}
	
	public function get_description()
	{
		return 'Cambias la raza a tu personaje.';
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