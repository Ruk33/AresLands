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
		return 'Aprovecha mejor los combates, exploraciones y misiones consiguiendo 20% de experiencia extra.';
	}

	public function get_price()
	{
		return 20;
	}
	
	public function execute()
	{
		$character = Character::get_character_of_logged_user();

		if ( $character )
		{
			Skill::find(Config::get('game.vip_multiplier_xp_rate_skill'))->cast($character, $character);
		}
	}
}