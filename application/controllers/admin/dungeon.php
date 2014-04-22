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
			'dungeon'           => $dungeon,
			'zones'             => Zone::lists("name", "id"),
			'monsters'          => Monster::where_type("monster")->order_by("level", "asc")->get(),
			'items'             => Item::order_by("level", "asc")->get(),
			'monstersInDungeon' => $dungeon->monsters()->lists("name", "id"),
			'rewardsDungeon'    => $rewardsDungeon,
		));
	}

	public function post_edit()
	{
		$dungeon = Dungeon::find(Input::get("id"));

		if ( ! $dungeon )
		{
			return Response::error('404');
		}

		$dungeon->fill_raw(Input::except(array("__method", "monsters", "chance", "amount", "rewards")));

		if ( $dungeon->validate() )
		{
			$dungeon->monsters()->delete();
			$dungeon->rewards()->delete();

			$chances = Input::get('chance');
			$amounts = Input::get('amount');

			foreach ( Input::get('rewards', array()) as $reward )
			{
				$dungeon->rewards()->insert(new DungeonReward(array(
					'item_id' => $reward, 
					'amount' => $amounts[$reward], 
					'chance' => $chances[$reward]
				)));
			}

			foreach ( Input::get('monsters', array()) as $monster )
			{
				$dungeon->monsters()->attach($monster);
			}

			$dungeon->save();

			return Redirect::to_route("get_admin_dungeon_edit", array($dungeon->id));
		}
		else
		{
			return Redirect::back()->with("error", $dungeon->errors()->all())->with_inputs();
		}
	}

	public function get_create()
	{
		$this->layout->title = "Admin - Dungeon - Crear";
		$this->layout->content = View::make("admin.dungeon.edit")->with(array(
			'dungeon'           => new Dungeon,
			'zones'             => Zone::lists("name", "id"),
			'monsters'          => Monster::where_type("monster")->order_by("level", "asc")->get(),
			'items'             => Item::order_by("level", "asc")->get(),
			'monstersInDungeon' => array(),
			'rewardsDungeon'    => array(),
		));
	}

	public function post_create()
	{
		$dungeon = new Dungeon();
		$dungeon->fill_raw(Input::except(array("__method", "id", "monsters", "chance", "amount", "rewards")));

		if ( $dungeon->validate() )
		{
			$dungeon->save();
			Input::merge(array('id' => $dungeon->id));

			return $this->post_edit();
		}
		else
		{
			return Redirect::back()->with("error", $dungeon->errors()->all())->with_inputs();
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