<?php

class NuevosProblemasTest extends PHPUnit_Framework_TestCase
{
	/*
	const QUEST_ID = 8;

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
		Event::fire('pveBattle', array($this->character, Npc::find(103), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(103), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(104), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(105), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(106), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(107), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(108), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(109), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(110), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(111), $this->character));

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress, null);
	}
	 * 
	 */
}