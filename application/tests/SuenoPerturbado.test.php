<?php

class SuenoPerturbadoTest extends PHPUnit_Framework_TestCase
{
	const QUEST_ID = 6;

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
		$monster = Npc::find(33);

		for ($i = 0, $max = 4; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(34);

		for ($i = 0, $max = 5; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(35);

		for ($i = 0, $max = 3; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(36);

		for ($i = 0, $max = 2; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress, null);
	}
}