<?php

class Quest_LasMinasEstanBajoAtaque extends QuestAction
{
	protected static $questId = 31;
	
	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(21, 22, 23, 24), array(3, 4, 2, 1));
		//$this->actionNpcTalk = new QuestActionNpcTalk($this->characterQuest, array(6));
		//$this->actionNpcTalk = new QuestActionNpcTalkAndGiveItem($this->characterQuest, array(6), array(6 => array(16)), array(16 => 3));
	}
}