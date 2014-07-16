<?php

class Create_Character_Items_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("character_items", function($table){
            $table->increments("id");
            $table->integer("owner_id");
            $table->integer("item_id");
            $table->integer("count");
            $table->string("location");
            $table->string("data");
            $table->integer("slot");
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