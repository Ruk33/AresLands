<?php

class Quest_CazadoresImplacables extends QuestAction
{
	protected static $questId = 33;

	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(15), array(3));
	}
}