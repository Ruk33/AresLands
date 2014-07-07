<?php

abstract class Authenticated_Base extends Base_Controller
{
	/**
	 *
	 * @var string
	 */
    public $layout = 'layouts.default';
	
	/**
	 *
	 * @var boolean
	 */
	public $restful = true;
	
	/**
	 *
	 * @var Character
	 */
	protected $character;
	
	/**
	 * Registramos las rutas del controlador
	 */
	public static function register_routes()
	{
		
	}
}