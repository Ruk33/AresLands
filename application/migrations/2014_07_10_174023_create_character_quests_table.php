<?php

class Create_Character_Quests_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Laravel\Database\Schema::create("character_quests", function($table){
            $table->increments("id");
            $table->integer("character_id");
            $table->integer("quest_id");
            $table->string("data");
            $table->string("progress");
            $table->integer("finished_at");
            $table->integer("repeatable_at");
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