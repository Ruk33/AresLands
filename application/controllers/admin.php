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
	
	public function get_npc($npcId = 0, $action = 'all')
	{
		if ( $npcId > 0 )
		{
			$npc = Npc::find($npcId);
		}
		
		switch ( $action )
		{
			case 'all':
				$this->layout->title = 'Admin';
				$this->layout->content = View::make('admin.showallnpcs')
				->with('npcs', Npc::all());
				
				break;
			
			case 'delete':
				$npc->quests()->delete();
				$npc->merchandises()->delete();
				$npcs->delete();
				
				return Redirect::to('admin/npc/');
				
				break;
				
			case 'create':
				$this->layout->title = 'Admin';
				$this->layout->content = View::make('admin.createeditnpc')
				->with('quests', Quest::all())
				->with('items', Item::all());
				
				break;
				
			case 'edit':
				$this->layout->title = 'Admin';
				$this->layout->content = View::make('admin.createeditnpc')
				->with('npc', $npc)
				->with('items', Item::all())
				->with('merchandisesPrice', $npc->merchandises()->lists('price_copper', 'item_id'))
				->with('quests', Quest::all())
				->with('npcQuests', $npc->quests()->lists('name', 'id'))
				->with('zones', Zone::all())
				->render();
				
				break;
		}
	}

	public function post_npc()
	{
		$inputs = Input::get();
		$merchandisesPrice = $inputs['merchandisesPrice'];
		
		if ( $inputs['npcId'] )
		{
			$npc = Npc::find((int) $inputs['npcId']);
			
			$npc->merchandises()->delete();
			$npc->quests()->delete();
		}
		else
		{
			$npc = new Npc();
		}
		
		$npc->name = $inputs['name'];
		$npc->dialog = $inputs['dialog'];
		$npc->tooltip_dialog = $inputs['tooltip_dialog'];
		$npc->zone_id = $inputs['zone_id'];
		$npc->time_to_appear = $inputs['time_to_appear'];
		$npc->type = $inputs['type'];
		$npc->level = $inputs['level'];
		$npc->life = $inputs['life'];
		$npc->stat_strength = $inputs['stat_strength'];
		$npc->stat_dexterity = $inputs['stat_dexterity'];
		$npc->stat_resistance = $inputs['stat_resistance'];
		$npc->stat_magic = $inputs['stat_magic'];
		$npc->stat_magic_skill = $inputs['stat_magic_skill'];
		$npc->stat_magic_resistance = $inputs['stat_magic_resistance'];
		$npc->lhand = $inputs['lhand'];
		$npc->rhand = $inputs['rhand'];
		$npc->xp = $inputs['xp'];
		
		$npc->save();
		
		if ( isset($inputs['merchandises']) )
		{
			$npcMerchandise = null;
			foreach ($inputs['merchandises'] as $merchandise)
			{
				$npcMerchandise = new NpcMerchandise();
				
				$npcMerchandise->npc_id = $npc->id;
				$npcMerchandise->item_id = $merchandise;
				$npcMerchandise->price_copper = $merchandisesPrice[$merchandise];
				
				$npcMerchandise->save();
			}
		}
		
		if ( isset($inputs['quests']) )
		{
			$npcQuest = null;
			foreach ($inputs['quests'] as $quest)
			{
				$npcQuest = new NpcQuest();
				
				$npcQuest->npc_id = $npc->id;
				$npcQuest->quest_id = $quest;
				
				$npcQuest->save();
			}
		}
		
		return Redirect::to('admin/npc');
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