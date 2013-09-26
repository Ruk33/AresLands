<?php

class Quest_BandidosAlAcecho extends QuestAction
{
	protected static $questId = 40;
	
	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(49, 50, 51), array(1, 4, 2));
		//$this->actionNpcTalk = new QuestActionNpcTalk($this->characterQuest, array(6));
		//$this->actionNpcTalk = new QuestActionNpcTalkAndGiveItem($this->characterQuest, array(6), array(6 => array(16)), array(16 => 3));
	}
}