<?php

class Admin_Controller extends Base_Controller
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
		$this->layout->title = 'Admin';
		$this->layout->content = View::make('admin.index');
	}

	public function get_quest($questId = 0, $action = 'all')
	{
		switch ( $action )
		{
			case 'all':
				$this->layout->title = 'Admin';
				$this->layout->content = View::make('admin.showallquests')
				->with('quests', Quest::all());
				
				break;

			case 'delete':
				$quest = Quest::find((int) $questId);

				CharacterQuest::where('quest_id', '=', $quest->id)->delete();
				CharacterTrigger::where('class_name', '=', $quest->class_name)->delete();

				$quest->triggers()->delete();
				$quest->rewards()->delete();
				$quest->delete();

				return Redirect::to('admin/quest');

				break;

			case 'create':
				$this->layout->title = 'Admin';
				$this->layout->content = View::make('admin.createeditquest')
				->with('items', Item::all());

				break;

			case 'edit':
				$quest = Quest::find((int) $questId);
				$questTriggers = $quest->triggers()->lists('id', 'event');
				$questRewards = $quest->rewards()->lists('amount', 'item_id');

				$this->layout->title = 'Admin';
				$this->layout->content = View::make('admin.createeditquest')
				->with('quest', $quest)
				->with('triggers', $questTriggers)
				->with('rewards', $questRewards)
				->with('items', Item::all())
				->render();

				break;
		}
	}

	public function post_quest()
	{
		$inputs = Input::get();
		$rewardsAmount = $inputs['rewardsAmount'];

		if ( $inputs['questId'] > 0 )
		{
			$quest = Quest::find((int) $inputs['questId']);

			$quest->triggers()->delete();
			$quest->rewards()->delete();
		}
		else
		{
			$quest = new Quest();
		}

		$quest->class_name = $inputs['class_name'];
		$quest->name = $inputs['name'];
		$quest->description = $inputs['description'];
		$quest->min_level = $inputs['min_level'];
		$quest->max_level = $inputs['max_level'];
		$quest->repeatable = ( isset($inputs['repeatable']) ) ? true : false;
		$quest->repeatable_after = $inputs['repeatable_after'];
		$quest->daily = ( isset($inputs['daily']) ) ? true : false;
		$quest->dwarf = $inputs['dwarf'];
		$quest->drow = $inputs['drow'];
		$quest->elf = $inputs['elf'];
		$quest->human = $inputs['human'];
		$quest->complete_required = $inputs['complete_required'];

		$quest->save();

		$questTrigger = null;
		foreach ( $inputs['events'] as $event )
		{
			$questTrigger = new QuestTrigger();

			$questTrigger->quest_id = $quest->id;
			$questTrigger->event = $event;

			$questTrigger->save();
		}

		if ( isset($inputs['rewards']) )
		{
			$questReward = null;
			foreach ( $inputs['rewards'] as $reward )
			{
				$questReward = new QuestReward();

				$questReward->quest_id = $quest->id;
				$questReward->item_id = $reward;
				$questReward->amount = $rewardsAmount[$reward];

				$questReward->save();
			}
		}

		return Redirect::to('admin/quest');
	}
}