<?php

class VipNull implements IVipObject
{
	protected $attributes;
	
	public function execute()
	{
		
	}

	public function get_description()
	{
		return "Objeto desconocido del infierno";
	}

	public function get_icon()
	{
		return "";
	}

	public function get_name()
	{
		return "Desconocido";
	}

	public function get_price()
	{
		return 0;
	}

	public function get_validator()
	{
		return Validator::make(
			$this->attributes,
			array(
				"id" => "array" // hacemos que siempre falle
			),
			array(
				"array" => "El objeto VIP no es valido"
			)
		);
	}

	public function set_attributes(array $attributes)
	{
		$this->attributes = $attributes;
	}

	public function set_buyer(Character $buyer)
	{
		
	}

}