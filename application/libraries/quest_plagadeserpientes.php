<?php

class Quest_PlagaDeSerpientes extends QuestAction
{
	protected static $questId = 32;

	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(12), array(3));
	}
}