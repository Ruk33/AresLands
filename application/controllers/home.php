<?php

class Home_Controller extends Base_Controller 
{
	public $layout = 'layouts.default';
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		/*
		 *	Si está logueado, entonces
		 *	lo llevamos a la página de logueados
		 */
		$this->filter('before','logged', ['authenticated/index']);
	}

	/* Index */
	public function get_index()
	{
		$this->layout->title = 'El renacimiento de Tierras de Leyenda';
		$this->layout->content = View::make('home.index');
	}
}