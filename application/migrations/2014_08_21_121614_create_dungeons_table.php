<?php

class Create_Dungeons_Table
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		\Laravel\Database\Schema::create('dungeons', function($table)
        {
            $table->increments("id");
            $table->integer("zone_id");
            $table->integer("king_id");
            $table->integer("king_since");
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		\Laravel\Database\Schema::drop('dungeons');
	}

}