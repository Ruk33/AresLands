<?php

class Authenticated_Controller extends Base_Controller
{
	public $layout = 'layouts.default';
	public $restful = true;

	public function __construct()
	{
		/*
		 *	Solo queremos logueados
		 */
		$this->filter('before', 'auth');

		/*
		 *	Si no tiene personaje lo
		 *	redireccionamos a la página
		 *	para que se pueda crear uno
		 */
		$this->filter('before', 'hasNoCharacter');
	}

	public function get_index()
	{
		$this->layout->title = 'ho';
		$this->layout->content = View::make('home.index');
	}
}