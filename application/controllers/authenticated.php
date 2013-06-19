<?php

class Authenticated_Controller extends Base_Controller
{
	public $layout = 'layouts.default';
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

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
		$items = CharacterItem::where('owner_id', '=', Session::get('character')->id)->get();
		$itemsToView = [];

		foreach ( $items as $item )
		{
			$itemsToView[$item->location][] = $item;
		}

		$this->layout->title = 'Inicio';
		$this->layout->content = View::make('authenticated.index')->with('items', $itemsToView);
	}
}