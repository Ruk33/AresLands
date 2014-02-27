<?php

class VipChangeName implements IVipObject
{
	public $name;

	public function get_name()
	{
		return 'Cambio de nombre';
	}
	
	public function get_icon()
	{
		return URL::base() . '/img/icons/vip/change_name.jpg';
	}
	
	public function get_description()
	{
		return 'Cambias el nombre a tu personaje.';
	}

	public function get_price()
	{
		return 125;
	}
	
	public function execute()
	{
		$character = Character::get_character_of_logged_user(array('id', 'name'));

		if ( $character )
		{
			$character->name = $this->name;
			$character->save();
		}
	}
}