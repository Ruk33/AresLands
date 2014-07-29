<?php

class VipNull extends VipObject
{
	public function execute()
	{
		
	}

	public function getDescription()
	{
		return "Objeto desconocido del infierno";
	}

	public function getIcon()
	{
		return "";
	}

	public function getName()
	{
		return "Desconocido";
	}

	public function getPrice()
	{
		return 0;
	}

	public function getValidator()
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
}