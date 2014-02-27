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
		return 80;
	}
	
	public function execute()
	{
		$character = Character::get_character_of_logged_user(array('id', 'gender'));

		if ( $character )
		{
			$character->gender = ( $character->gender == 'male' ) ? 'female' : 'male';
			$character->save();
		}
	}
}