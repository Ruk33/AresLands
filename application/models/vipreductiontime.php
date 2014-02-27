<?php

class VipReductionTime implements IVipObject
{
	public function get_name()
	{
		return 'Reductor de tiempos';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/reduction_time.jpg';
	}
	
	public function get_description()
	{
		return 'Reduce tus tiempos.';
	}

	public function get_price()
	{
		return 20;
	}
	
	public function execute()
	{
		// TODO: dar buffs al usuario logueado
	}
}