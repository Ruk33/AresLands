<?php

interface IVipObject
{
	/**
	 * Se obtiene el nombre del objeto vip
	 * @return string
	 */
	public function get_name();
	
	/**
	 * Se obtiene el icono del objeto vip
	 * @return string
	 */
	public function get_icon();
	
	/**
	 * Se obtiene la descripcion del objeto vip
	 * @return string
	 */
	public function get_description();
	
	/**
	 * Se obtiene el precio (en ironcoins) del objeto vip
	 * @return float
	 */
	public function get_price();
	
	/**
	 * 
	 * @param array $attributes
	 * @return Validator
	 */
	public function get_validator(Array $attributes);
	
	/**
	 * Se ejecutan las acciones del objeto vip
	 */
	public function execute(Character $buyer, Array $attributes);
}