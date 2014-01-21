<?php

class SegundaMisionFallidaTest extends PHPUnit_Framework_TestCase
{
	/*
	const QUEST_ID = 22;

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

		$monster = Npc::find(122);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(123);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(124);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		$monster = Npc::find(125);
		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		for ( $i = 0, $max = 46; $i <= $max; $i++ )
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress->progress, 'reward');

		$questProgress->delete();
	}
	 * 
	 */
}