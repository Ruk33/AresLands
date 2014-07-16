<?php

class Create_Messages_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("messages", function($table){
            $table->increments("id");
            $table->integer("sender_id");
            $table->integer("receiver_id");
            $table->string("subject");
            $table->string("content");
            $table->boolean("unread");
            $table->integer("date");
            $table->boolean("is_special");
            $table->string("type");
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