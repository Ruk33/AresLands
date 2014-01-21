<?php

class UrgenteTest extends PHPUnit_Framework_TestCase
{
	/*
	const QUEST_ID = 19;

	public $character;
	public $quest;

	public function __construct()
	{
		Session::started() or Session::load();

		Auth::login(1);

		$this->character = Character::get_character_of_logged_user();
		$this->quest = Quest::find(self::QUEST_ID);
	}

	public function testCompletarMision()
	{
		$this->quest->accept();

		$characterItem = new CharacterItem();

		$characterItem->item_id = 2;
		$characterItem->owner_id = $this->character->id;
		$characterItem->location = 'inventory';
		$characterItem->count = 1;

		$characterItem->save();

		$characterItem = new CharacterItem();

		$characterItem->item_id = 25;
		$characterItem->owner_id = $this->character->id;
		$characterItem->location = 'inventory';
		$characterItem->count = 1;

		$characterItem->save();

		Event::fire('npcTalk', array($this->character, Npc::find(164)));
		Event::fire('npcTalk', array($this->character, Npc::find(164)));

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress->progress, 'reward');

		$questProgress->delete();
	}
	 * 
	 */
}