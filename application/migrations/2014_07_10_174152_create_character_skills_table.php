<?php

class Create_Character_Skills_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Laravel\Database\Schema::create("character_skills", function($table){
            $table->increments("id");
            $table->integer("skill_id");
            $table->integer("character_id");
            $table->integer("level");
            $table->integer("end_time");
            $table->integer("last_execution_time");
            $table->integer("amount");
            $table->string("data");
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}