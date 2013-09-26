<?php

class Quest_PlagaDeRatas extends QuestAction
{
	protected static $questId = 34;

	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(18), array(3));
	}
}