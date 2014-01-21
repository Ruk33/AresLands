<?php

class QuestModelTest extends PHPUnit_Framework_TestCase
{
	/*
	const QUEST_ID = 2;

	public function __construct()
	{
		Session::started() or Session::load();

		Auth::login(1);

		$this->character = Character::get_character_of_logged_user();
		$this->quest = Quest::find(self::QUEST_ID);
	}

	public function testAceptarMision()
	{
		// aceptamos la mision
		$this->quest->accept();
		$questProgress = $this->character->quests()->where('quest_id', '=', self::QUEST_ID)->first();

		// nos aseguramos de que la tenga aceptada
		$this->assertEquals($questProgress->progress, 'started');

		// borramos
		$this->character->quests()->delete();
		$this->character->triggers()->delete();
	}
	 * 
	 */
}