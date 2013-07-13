<?php

class AyudaAlMagoTest extends PHPUnit_Framework_TestCase
{
	const QUEST_ID = 15;

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

		$characterItem->item_id = 30;
		$characterItem->owner_id = $this->character->id;
		$characterItem->count = 10;

		$characterItem->save();

		Event::fire('npcTalk', array($this->character, Npc::find(159)));

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress->progress, 'reward');

		$questProgress->delete();
	}
}