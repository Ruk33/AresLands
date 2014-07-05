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
	public static function register_routes(){}
	
	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'authenticated_layout_variables', array('controller' => $this));
	}
}