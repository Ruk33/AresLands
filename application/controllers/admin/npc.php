<?php

class Admin_Npc_Controller extends Base_Controller
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
		$this->layout->title = "Admin - NPCs";
		$this->layout->content = View::make("admin.npc.index")->with(array(
			'merchants' => Npc::where('type', '=', 'npc')->get(),
			'monsters' => Npc::where('type', '<>', 'npc')->get(),
		));
	}

	public function get_edit($id)
	{
		$npc = Npc::find($id);

		if ( ! $npc )
		{
			return Response::error("404");
		}

		$drops = array();

		foreach ( $npc->drops as $drop )
		{
			$drops[$drop->item_id] = $drop->to_array();
		}

		$merchandises = array();

		foreach ( $npc->merchandises as $merchandise )
		{
			$merchandises[$merchandise->item_id] = $merchandise->to_array();
		}

		$this->layout->title = "Admin - NPC - {$npc->name}";
		$this->layout->content = View::make("admin.npc.edit")->with(array(
			"npc" => $npc,
			"zones" => Zone::lists("name", "id"),
			"items" => Item::all(),
			"quests" => Quest::all(),
			"drops" => $drops,
			"merchandises" => $merchandises,
			"npc_quests" => $npc->quests()->lists("name", "id")
		));
	}

	public function post_edit()
	{
		if ( Input::get("type") == "npc" )
			$npc = Npc::find(Input::get("id"));
		else
			$npc = Monster::find(Input::get("id"));

		if ( ! $npc )
		{
			return Response::error("404");
		}

		$npc->fill_raw(Input::except(array("__method", "merchandises", "merchandises_prices", "quests", "drops", "drop_chance", "drop_amount")));

		if ( $npc->validate() )
		{
			if ( $npc instanceof Npc )
			{
				$npc->quests()->delete();
				$npc->merchandises()->delete();

				foreach ( Input::get("quests", array()) as $quest )
				{
					$npc->quests()->attach($quest);
				}

				$prices = Input::get("merchandises_prices");

				foreach ( Input::get("merchandises", array()) as $merchandise )
				{
					$npc->merchandises()->insert(new NpcMerchandise(array(
						'item_id' => $merchandise,
						'price_copper' => $prices[$merchandise]
					)));
				}
			}
			else
			{
				$npc->drops()->delete();

				$chances = Input::get("drop_chance");
				$amounts = Input::get("drop_amount");

				foreach ( Input::get("drops", array()) as $drop )
				{
					$npc->drops()->insert(new MonsterDrop(array(
						'item_id' => $drop,
						'amount' => $amounts[$drop],
						'chance' => $chances[$drop]
					)));
				}
			}

			$npc->save();

			return Redirect::to_route("get_admin_npc_edit", array($npc->id));
		}
		else
		{
			return Redirect::back()->with("error", $npc->errors()->all())->with_inputs();
		}
	}

	public function get_create()
	{
		$this->layout->title = "Admin - NPC - Crear";
		$this->layout->content = View::make("admin.npc.edit")->with(array(
			"npc" => new Npc,
			"zones" => Zone::lists("name", "id"),
			"items" => Item::all(),
			"quests" => Quest::all(),
			"drops" => array(),
			"merchandises" => array(),
			"npc_quests" => array()
		));
	}

	public function post_create()
	{
		$npc = new Npc();
		$npc->fill_raw(Input::except("__method"));

		if ( $npc->validate() )
		{
			$npc->save();
			Input::merge(array('id' => $npc->id));

			return $this->post_edit();
		}
		else
		{
			return Redirect::back()->with("error", $npc->errors()->all())->with_inputs();
		}
	}

	public function get_delete($id)
	{
		$npc = Npc::find($id);

		if ( ! $npc )
		{
			return Response::error("404");
		}

		$npc->quests()->delete();
		$npc->drops()->delete();
		$npc->merchandises()->delete();

		$npc->delete();

		return Redirect::to_route("get_admin_npc_index");
	}
}