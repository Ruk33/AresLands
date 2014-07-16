<?php

class Create_Clan_Orb_Points_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("clan_orb_points", function($table){
            $table->increments("id");
            $table->integer("clan_id");
            $table->integer("points");
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