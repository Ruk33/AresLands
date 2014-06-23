<?php

class VipReductionTime implements IVipObject
{
	protected $buyer;
	protected $attributes;
	
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
		return 'Reduce tus tiempos de viaje y descanzos en un 20% durante 3 dias.';
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
			Skill::find(Config::get('game.vip_reduction_time_skill'))->cast($character, $character);
		}
	}

	public function get_validator()
	{
		$rules = array();
		$messages = array();
		
		return Validator::make($this->attributes, $rules, $messages);
	}

	public function set_attributes(Character $buyer, array $attributes)
	{
		$this->buyer = $buyer;
		$this->attributes = $attributes;
	}
}