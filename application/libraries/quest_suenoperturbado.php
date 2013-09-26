<?php

class Quest_SuenoPerturbado extends QuestAction
{
	protected static $questId = 37;
	
	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(33, 34, 35, 36), array(3, 4, 2, 1));
		//$this->actionNpcTalk = new QuestActionNpcTalk($this->characterQuest, array(6));
		//$this->actionNpcTalk = new QuestActionNpcTalkAndGiveItem($this->characterQuest, array(6), array(6 => array(16)), array(16 => 3));
	}
}