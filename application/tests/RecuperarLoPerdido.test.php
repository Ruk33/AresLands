<?php

class RecuperarLoPerdidoTest extends PHPUnit_Framework_TestCase
{
	const QUEST_ID = 9;

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
		Event::fire('pveBattle', array($this->character, Npc::find(65), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(65), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(65), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(66), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(67), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(68), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(69), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(70), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(71), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(72), $this->character));

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress, null);
	}
}