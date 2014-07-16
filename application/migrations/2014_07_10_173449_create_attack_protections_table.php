<?php

class Create_Attack_Protections_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("attack_protections", function($table){
            $table->increments("id");
            $table->integer("attacker_id");
            $table->integer("target_id");
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