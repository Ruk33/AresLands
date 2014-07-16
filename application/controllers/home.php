<?php

class Home_Controller extends Base_Controller 
{
	public $layout = 'layouts.game';
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		/*
		 *	Si está logueado, enviamos
		 *	al usuario a la página de autentificados
		 */
		$this->filter('before', 'logged')->only('index');
	}

	/* Index */
	public function get_index()
	{
		$this->layout->title = 'El renacimiento de Tierras de Leyenda';
		$this->layout->content = View::make('home.index');
	}

	public function get_thanks()
	{
		$this->layout->title = 'Muchísimas gracias a todos';
		$this->layout->content = View::make('home.thanks');
	}
}