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
		$items = array(
			Item::generate_random(1, 1, Item::WARRIOR, true, "blunt"),
			Item::generate_random(1, 1, Item::WIZARD, true, "blunt"),
			Item::generate_random(1, 1, Item::MIXED, true, "blunt"),
		);

		foreach ( $items as $item )
		{
			echo "<img src='" . $item->get_image_path() . "' />";
			echo $item->name;
		}

		die();
	}

	public function get_item()
	{
		$this->layout->title = "Admin - Generador - Objetos";
		$this->layout->content = View::make("admin.generator.item");
	}
}