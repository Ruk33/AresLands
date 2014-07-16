<?php

class Create_Character_Activities_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Laravel\Database\Schema::create("character_activities", function($table){
            $table->increments("id");
            $table->integer("character_id");
            $table->string("name");
            $table->string("data");
            $table->integer("end_time");
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