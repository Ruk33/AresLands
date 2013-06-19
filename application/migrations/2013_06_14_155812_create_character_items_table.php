<?php

class Create_Character_Items_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_items', function($table) {
			$table->increments('id');

			$table->integer('owner_id');
			$table->integer('item_id');

			$table->integer('count');

			/*
			 *	Location tendrá el valor
			 *	de body_part (en caso de estar equipado).
			 *	En caso de estar en inventario, su valor será none
			 */
			$table->string('location');

			/*
			 *	La columna data se encargará
			 *	de guardar información extra
			 *	sobre el objeto
			 */
			$table->string('data');

			/*
			 *	Slot del inventario en el que
			 *	está el objeto (si no está
			 *	en el inventario, entonces 0)
			 */
			$table->integer('slot');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('character_items');
	}

}