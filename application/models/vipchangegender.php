<?php

class VipChangeGender implements IVipObject
{
	public function get_name()
	{
		return 'Cambio de genero';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/change_gender.jpg';
	}
	
	public function get_description()
	{
		return 'Cambias el genero de tu personaje.';
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