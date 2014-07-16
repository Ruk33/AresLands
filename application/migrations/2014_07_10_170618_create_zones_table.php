<?php

class Create_Zones_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Laravel\Database\Schema::create("zones", function($table){
            $table->increments("id");
            $table->string("name");
            $table->string("description");
            $table->string("type");
            $table->integer("belongs_to");
            $table->integer("min_level");
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