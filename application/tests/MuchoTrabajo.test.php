<?php

class MuchoTrabajoTest extends PHPUnit_Framework_TestCase
{
	/*
	const QUEST_ID = 20;

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
		
		$monster = Npc::find(112);

		for ($i = 0, $max = 10; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(113);

		for ($i = 0, $max = 6; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(114);

		for ($i = 0, $max = 3; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(115);

		for ($i = 0, $max = 2; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$monster = Npc::find(116);

		for ($i = 0, $max = 1; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress, null);
	}
	 * 
	 */
}