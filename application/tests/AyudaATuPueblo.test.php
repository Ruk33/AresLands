<?php

class AyudaATuPuebloTest extends PHPUnit_Framework_TestCase
{
	public $character;
	public $quest;

	public function __construct()
	{
		Session::started() or Session::load();

		Auth::login(1);

		$this->character = Character::get_character_of_logged_user();
		$this->quest = Quest::find(2);

		$this->quest->accept();
	}

	public function testCompletarMision()
	{
		$monster = Npc::find(9);

		for ($i = 0, $max = 6; $i <= $max; $i++)
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$questProgress = $this->character->quests()->where('quest_id', '=', 2)->first();

		$this->assertEquals($questProgress->progress, 'reward');

		$questProgress->delete();
	}
}