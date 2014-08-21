<?php

class Create_Dungeon_Levels_Table
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		\Laravel\Database\Schema::create('dungeon_levels', function($table)
        {
            $table->increments("id");
            $table->integer("dungeon_level");
            $table->integer("monster_id");
            $table->integer("required_item_id");
            $table->integer("required_level");
            
            // Tipo de monstruo (normal, jefe, final)
            $table->integer("type");
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		\Laravel\Database\Schema::drop('dungeon_levels');
	}

}