<?php

class Item_Controller extends Base_Controller
{
	public $restful = true;

	public function get_index($itemId = '')
	{
		return json_encode(array('saludo' => $itemId));
	}
}