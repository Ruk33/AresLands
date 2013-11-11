<?php

class Dailyquest_Task
{
	public function run($arguments)
	{
		DB::query(
			"DELETE FROM character_quests 
				WHERE quest_id IN (
					SELECT id
					FROM quests
					WHERE daily = 1
				)"	
		);
	}
}