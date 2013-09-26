<?php

class Quest_EnemigoEscondido extends QuestAction
{
	protected static $questId = 39;
	
	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(46, 47, 48), array(2, 3, 2));
		//$this->actionNpcTalk = new QuestActionNpcTalk($this->characterQuest, array(6));
		//$this->actionNpcTalk = new QuestActionNpcTalkAndGiveItem($this->characterQuest, array(6), array(6 => array(16)), array(16 => 3));
	}
}