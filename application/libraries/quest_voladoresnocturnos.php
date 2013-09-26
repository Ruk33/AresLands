<?php

class Quest_VoladoresNocturnos extends QuestAction
{
	protected static $questId = 38;
	
	protected function setup()
	{
		$this->actionPveBattleWin = new QuestActionPveWin($this->characterQuest, array(37, 38), array(4, 7));
		//$this->actionNpcTalk = new QuestActionNpcTalk($this->characterQuest, array(6));
		//$this->actionNpcTalk = new QuestActionNpcTalkAndGiveItem($this->characterQuest, array(6), array(6 => array(16)), array(16 => 3));
	}
}