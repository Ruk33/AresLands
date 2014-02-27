<?php

class VipChangeRace implements IVipObject
{
	public $race;

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
		return 175;
	}
	
	public function execute()
	{
		$character = Character::get_character_of_logged_user(array('id', 'race'));

		if ( $character )
		{
			$character->race = $this->race;
			$character->save();
		}
	}
}