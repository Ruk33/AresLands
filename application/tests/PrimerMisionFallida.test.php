<?php

class PrimerMisionFallidaTest extends PHPUnit_Framework_TestCase
{
	const QUEST_ID = 21;

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
		$monster = Npc::find(127);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(128);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(129);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(130);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(131);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(132);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(133);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		for ($i = 0, $max = 43; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress->progress, 'reward');

		$questProgress->delete();
	}
}