<?php

class Create_Character_Dungeons_Table
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		\Laravel\Database\Schema::create('character_dungeons', function($table)
        {
            $table->increments("id");
            $table->integer("character_id");
            $table->integer("dungeon_id");
            $table->integer("dungeon_level");
            $table->integer("last_attempt");
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		\Laravel\Database\Schema::drop('character_dungeons');
	}

}