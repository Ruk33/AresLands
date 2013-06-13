<?php

class CharacterCreation_Controller extends Base_Controller
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
		 *	Solo queremos usuarios
		 *	que no tengan un personaje
		 */
		$this->filter('before', 'hasCharacter');
	}

	public function get_index()
	{
		$this->layout->title = '¿Con qué raza comenzarás tu aventura?';
		$this->layout->content = View::make('charactercreation.home');
	}
}