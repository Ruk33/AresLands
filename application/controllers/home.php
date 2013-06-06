<?php

class Home_Controller extends Base_Controller {
	public $layout = 'layouts.default';
	public $restful = true;

	/* Index */
	public function get_index()
	{
		$this->layout->title = 'Tu comunidad de juego on-line';
		$this->layout->content = View::make('home.index');
	}
}