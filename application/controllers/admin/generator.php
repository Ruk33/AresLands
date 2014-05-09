<?php

class Admin_Generator_Controller extends Base_Controller
{
	public $layout = 'layouts.game';
	public $restful = true;

	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'admin');
	}

	public function get_index()
	{
		$this->layout->title = "Admin - Generador";
		$this->layout->content = View::make("admin.generator.index");
	}

	public function post_item()
	{
		$levels = explode(",", Input::get('level'));
		$targets = Input::get('to_who', array());
		$types = Input::get('type', array());

		foreach ( $levels as $level )
		{
			for ( $i = 0, $max = Input::get('amount', 1); $i < $max; $i++ )
			{
				foreach ( $types as $type )
				{
					foreach ( $targets as $toWho )
					{
						Item::generate_random($level, $level, $toWho, mt_rand(0, 1) == 1, $type)->save();
					}
				}
			}
		}
		
		return \Laravel\Redirect::to_route('get_admin_generator_item');
	}

	public function get_item()
	{
		$this->layout->title = "Admin - Generador - Objetos";
		$this->layout->content = View::make("admin.generator.item");
	}
}