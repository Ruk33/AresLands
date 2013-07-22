<?php

class AsegurarZonaTest extends PHPUnit_Framework_TestCase
{
	const QUEST_ID = 25;

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

		Event::fire('pveBattle', array($this->character, Npc::find(147), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(148), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(149), $this->character));
		Event::fire('pveBattle', array($this->character, Npc::find(150), $this->character));
		
		$monster = Npc::find(151);

		Event::fire('pveBattle', array($this->character, $monster, $this->character));

		for ( $i = 0, $max = 10; $i <= $max; $i++ )
		{
			Event::fire('pveBattle', array($this->character, $monster, $this->character));
		}

		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		$this->assertEquals($questProgress, null);
	}
}