<?php

class Create_Orbs_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Laravel\Database\Schema::create("orbs", function($table){
            $table->increments("id");
            $table->string("name");
            $table->string("description");
            $table->integer("coins");
            $table->integer("points");
            $table->integer("min_level");
            $table->integer("max_level");
            $table->integer("owner_character");
            $table->integer("acquisition_time");
            $table->integer("last_attacker");
            $table->integer("last_attack_time");
            $table->string("image");
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