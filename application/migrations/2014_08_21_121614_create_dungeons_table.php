<?php

use Laravel\Database\Schema;

class Create_Dungeons_Table
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dungeons', function(Table $table)
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
		Schema::drop('dungeons');
	}

}