<?php

class Quest_AyudaATuPueblo extends QuestAction
{
	protected static $questId = 28;

	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(9), array(3));
	}
}