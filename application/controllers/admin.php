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

	public function get_tournament($id = 0)
	{
		if ( $id )
		{
			if ( $id == 'create' )
			{
				$tournament = new Tournament;
			}
			else
			{
				$tournament = Tournament::find((int) $id);

				if ( ! $tournament )
				{
					return Response::error('404');
				}
			}

			$this->layout->title = 'Torneo';
			$this->layout->content = View::make('admin.tournament')->with('tournament', $tournament);
		}
		else
		{
			$this->layout->title = 'Torneos';
			$this->layout->content = View::make('admin.tournaments')->with('tournaments', Tournament::all());
		}
	}

	public function post_tournament()
	{
		$id = Input::get('id');
		$tournament = null;

		if ( $id )
		{
			$tournament = Tournament::find((int) $id);
		}
		else
		{
			$tournament = new Tournament;
		}

		if ( ! $tournament )
		{
			return Response::error('404');
		}

		$tournament->fill_raw(Input::except('id', '_method'));
		$tournament->save();

		return Redirect::to('admin/tournament/' . $tournament->id);
	}

	public function get_fixAllCharactersClanSkills()
	{
		$clans = Clan::all();

		foreach ( $clans as $clan )
		{
			$members = $clan->members()->select(array('id', 'clan_id'))->get();

			foreach ( $members as $member )
			{
				$clan->remove_clan_skills_from_member($member);
				$clan->give_clan_skills_to_member($member);
			}
		}
	}
	
	public function get_removeCharacterSkill($characterSkill = 0)
	{
		$characterSkill = CharacterSkill::find((int) $characterSkill);
		
		if ( $characterSkill )
		{
			$characterSkill->end_time = 1;
			$characterSkill->skill->periodic($characterSkill);
		}
		
		return Redirect::back();
	}
	
	public function post_modifyCharacterStats()
	{
		$inputs = Input::get();
		
		$character = Character::find((int) $inputs['character']);
		
		if ( $character )
		{
			$character->stat_strength = $inputs['stat_strength'];
			$character->stat_dexterity = $inputs['stat_dexterity'];
			$character->stat_resistance = $inputs['stat_resistance'];
			
			$character->stat_magic = $inputs['stat_magic'];
			$character->stat_magic_skill = $inputs['stat_magic_skill'];
			$character->stat_magic_resistance = $inputs['stat_magic_resistance'];
			
			$character->save();
		}
		
		return Redirect::back();
	}
	
	public function post_modifyCharacterExtraStats()
	{
		$inputs = Input::get();
		
		$character = Character::find((int) $inputs['character']);
		
		if ( $character )
		{
			$character->stat_strength_extra = $inputs['stat_strength_extra'];
			$character->stat_dexterity_extra = $inputs['stat_dexterity_extra'];
			$character->stat_resistance_extra = $inputs['stat_resistance_extra'];
			
			$character->stat_magic_extra = $inputs['stat_magic_extra'];
			$character->stat_magic_skill_extra = $inputs['stat_magic_skill_extra'];
			$character->stat_magic_resistance_extra = $inputs['stat_magic_resistance_extra'];
			
			$character->save();
		}
		
		return Redirect::back();
	}
	
	public function get_removeEquippedCharacterItem($characterItem = 0)
	{
		$characterItem = CharacterItem::find((int) $characterItem);
		
		if ( $characterItem )
		{
			$characterItem->character->update_extra_stat($characterItem->item->to_array(), false);
			$characterItem->delete();
		}
		
		return Redirect::back();
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
				->with('quest', new Quest())
				->with('npcs', Npc::order_by('level', 'asc')->get())
				->with('rewards', array())
				->with('actions', array())
				->with('actionAmount', array())
				->with('items', Item::all());

				break;

			case 'edit':
				$quest = Quest::find((int) $questId);
				$npcActions = $quest->quest_npcs()->lists('action', 'npc_id');
				$npcActionAmount = $quest->quest_npcs()->lists('amount', 'npc_id');
				$questRewards = $quest->rewards()->lists('amount', 'item_id');

				$this->layout->title = 'Admin';
				$this->layout->content = View::make('admin.createeditquest')
				->with('quest', $quest)
				->with('npcs', Npc::order_by('level', 'asc')->get())
				->with('actions', $npcActions)
				->with('actionAmount', $npcActionAmount)
				->with('rewards', $questRewards)
				->with('items', Item::all());

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

			$quest->quest_npcs()->delete();
			$quest->rewards()->delete();
		}
		else
		{
			$quest = new Quest();
		}

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

		if ( isset($inputs['action']) )
		{
			foreach ( $inputs['action'] as $npcId => $action )
			{
				if ( $action && isset($inputs['actionAmount'][$npcId]) )
				{
					$questNpc = new QuestNpc();

					$questNpc->quest_id = $quest->id;
					$questNpc->npc_id = $npcId;
					$questNpc->quest_id = $quest->id;
					$questNpc->action = $action;
					$questNpc->amount = $inputs['actionAmount'][$npcId];

					$questNpc->save();
				}
			}
		}

		if ( isset($inputs['rewards']) )
		{
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