<?php

/**
 * @deprecated
 */
class Skill_Task
{
	public function run($arguments)
	{        
        $time = time();
        
        $characterSkills = DB::query(
            "SELECT `character_skill`.* " .
            "FROM `character_skills` as `character_skill` " .

            "JOIN `skills` as `skill` " .
                "ON " .
                    "( " .
                        "`skill`.`id` = `character_skill`.`skill_id` " .
                        "AND " .
                        "`skill`.`level` = `character_skill`.`level` " .
                    ") " .
                
            "JOIN `characters` as `character`" .
                "ON " .
                    "( " .
                        "`character`.`id` = `character_skill`.`character_id` " .
                    ") " .

            "WHERE " .
                "( " .
                    "`character_skill`.`end_time` <> 0 " .
                    "AND " .
                    "`character_skill`.`end_time` <= $time " .
                ") " .

                "OR " .

                "( " .
                    "`skill`.`timeout` > 0 " .
                    "AND " .
                    "`skill`.`timeout` >= $time - `character_skill`.`last_execution_time` " .
                ") "
        );
        
		foreach ( $characterSkills as $characterSkill )
		{
            $characterSkill = new CharacterSkill((array) $characterSkill, true);
			Skill::periodic($characterSkill);
		}
	}
}