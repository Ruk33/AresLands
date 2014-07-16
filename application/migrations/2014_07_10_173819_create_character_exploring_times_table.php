<?php

class Create_Character_Exploring_Times_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("character_exploring_times", function($table){
            $table->increments("id");
            $table->integer("character_id");
            $table->integer("zone_id");
            $table->integer("time");
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