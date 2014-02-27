<?php

class VipCoinMultiplier implements IVipObject
{
	public function get_name()
	{
		return 'Multiplicador de monedas';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/coin_multiplier.jpg';
	}
	
	public function get_description()
	{
		return 'Aprovecha mejor los combates, exploraciones y misiones consiguiendo un 30% de oro extra.';
	}

	public function get_price()
	{
		return 40;
	}
	
	public function execute()
	{
		$character = Character::get_character_of_logged_user();

		if ( $character )
		{
			Skill::find(Config::get('game.vip_multiplier_coin_rate_skill'))->cast($character, $character);
		}
	}
}