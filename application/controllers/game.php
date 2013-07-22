<?php

class Game_Controller extends Base_Controller
{
	public $layout = 'layouts.game';
	public $restful = true;

	public function get_index()
	{
		$this->layout->title = 'Guía oficial de AresLands';
		$this->layout->content = View::make('game.index');
	}
}