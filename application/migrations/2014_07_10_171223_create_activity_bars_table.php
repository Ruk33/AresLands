<?php

class Create_Activity_Bars_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Laravel\Database\Schema::create("activity_bars", function($table){
            $table->increments("id");
            $table->integer("character_id");
            $table->integer("filled_amount");
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