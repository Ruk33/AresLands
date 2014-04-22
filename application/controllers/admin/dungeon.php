<?php

class Admin_Dungeon_Controller extends Base_Controller
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
		$this->layout->title = "Admin - Dungeon";
		$this->layout->content = View::make("admin.dungeon.index")->with('dungeons', Dungeon::all());
	}

	public function get_create()
	{
		$this->layout->title = "Admin - Dungeon - Crear";
		$this->layout->content = View::make("admin.dungeon.edit")->with('dungeon', new Dungeon());
	}

	public function post_create()
	{
		// todo
	}

	public function get_edit($id)
	{
		$dungeon = Dungeon::find($id);

		if ( ! $dungeon )
		{
			return Response::error('404');
		}

		$rewardsDungeon = array();

		foreach ( $dungeon->rewards as $reward )
		{
			$rewardsDungeon[$reward->item_id] = $reward->to_array();
		}

		$this->layout->title = "Admin - Dungeon - {$dungeon->name}";
		$this->layout->content = View::make("admin.dungeon.edit")->with(array(
			'dungeon' => $dungeon,
			'zones' => Zone::lists("name", "id"),
			'monsters' => Monster::where_type("monster")->order_by("level", "asc")->get(),
			'items' => Item::order_by("level", "asc")->get(),
			'monstersInDungeon' => $dungeon->monsters()->lists("name", "id"),
			'rewardsDungeon' => $rewardsDungeon,
		));
	}

	public function post_edit()
	{
		$dungeonExists = Dungeon::where("id", Input::get("id"))->take(1)->count() != 0;

		if ( ! $dungeonExists )
		{
			return Response::error("404");
		}

		$dungeon = new Dungeon();
		$dungeon->fill_raw(Input::except("__method"));

		if ( $dungeon->validate() )
		{
			$dungeon->save();
			return Redirect::to_route("get_admin_dungeon_show", array($dungeon->id));
		}
		else
		{
			return Redirect::back()->with("error", $dungeon->errors()->all());
		}
	}

	public function get_delete($id)
	{
		$dungeon = Dungeon::find($id);

		if ( ! $dungeon )
		{
			return Response::error('404');
		}

		$dungeon->delete();

		return Redirect::to_route('get_admin_dungeon_index');
	}
}