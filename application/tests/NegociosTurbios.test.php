<?php

class NegociosTurbiosTest extends PHPUnit_Framework_TestCase
{
	/*
	const QUEST_ID = 17;

	public $character;
	public $quest;

	public function __construct()
	{
		Session::started() or Session::load();

		Auth::login(1);

		$this->character = Character::get_character_of_logged_user();
		$this->quest = Quest::find(self::QUEST_ID);

		$this->quest->accept();
	}

	public function testCompletarMision()
	{
		Event::fire('npcTalk', array($this->character, Npc::find(1)));

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress->progress, 'reward');
		
		$questProgress->delete();
	}
	 * 
	 */
}