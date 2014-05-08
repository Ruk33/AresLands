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
		$level = Input::get('level');
		$toWho = Input::get('to_who');
		$type = Input::get('type');

		$generatedItems = array();

		for ( $i = 0, $max = Input::get('amount', 1); $i < $max; $i++ )
		{
			$generatedItems[] = Item::generate_random($level, $level, $toWho, mt_rand(0, 1) == 1, $type)->to_array();
		}

		die(var_dump($generatedItems));
	}

	public function get_item()
	{
		$this->layout->title = "Admin - Generador - Objetos";
		$this->layout->content = View::make("admin.generator.item");
	}
}