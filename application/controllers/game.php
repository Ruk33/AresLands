<?php

use Laravel\Redirect;

class Game_Controller extends Base_Controller
{
	public $layout = 'layouts.default';
	public $restful = true;

	/**
	 * @deprecated
	 */
	public function get_index()
	{
		$this->layout->title = 'Guía oficial de AresLands';
		$this->layout->content = View::make('game.guide.index');
	}

	/**
	 * @param string $section
	 * @return Redirect|void
	 */
	public function get_guide($section = 'index')
	{
		// TODO Esta no es la forma de hacerlo, pero estamos apurados y las ganas no acompañan <.<
		switch ( $section )
		{
			case 'index':
				$this->layout->title = 'Guia oficial de AresLands';
				$this->layout->content = \Laravel\View::make('game.guide.index');
				break;

			case 'basic':
				$this->layout->title = 'Guia oficial de AresLands - Basico';
				$this->layout->content = \Laravel\View::make('game.guide.basic');
				break;

			case 'medium':
				$this->layout->title = 'Guia oficial de AresLands - Medio';
				$this->layout->content = \Laravel\View::make('game.guide.medium');
				break;

			case 'advanced':
				$this->layout->title = 'Guia oficial de AresLands - Avanzado';
				$this->layout->content = \Laravel\View::make('game.guide.advanced');
				break;

			default:
				return Redirect::to('guide/index');
		}
	}
}