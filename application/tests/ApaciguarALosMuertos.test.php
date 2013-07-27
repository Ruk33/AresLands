<?php

class ApaciguarALosMuertosTest extends PHPUnit_Framework_TestCase
{
	const QUEST_ID = 10;

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
		
		$monster = Npc::find(99);

		for ($i = 0, $max = 4; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(100);

		for ($i = 0, $max = 3; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(101);

		for ($i = 0, $max = 2; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(102);

		for ($i = 0, $max = 1; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress, null);
	}
}