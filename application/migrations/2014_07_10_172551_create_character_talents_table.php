<?php

class Create_Character_Talents_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Laravel\Database\Schema::create("character_talents", function($table){
            $table->increments("id");
            $table->integer("character_id");
            $table->integer("skill_id");
            $table->integer("usable_at");
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