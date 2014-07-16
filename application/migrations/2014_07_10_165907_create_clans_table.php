<?php

class Create_Clans_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("clans", function($table){
            $table->increments("id");
            $table->integer("leader_id");
            $table->string("name");
            $table->string("message");
            $table->integer("xp");
            $table->integer("xp_next_level");
            $table->integer("level");
            $table->integer("points_to_change");
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