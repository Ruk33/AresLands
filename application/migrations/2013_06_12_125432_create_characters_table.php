<?php

class Create_Characters_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('characters', function($table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('name', 10);
			$table->integer('level', 3);
			$table->float('max_life');
			$table->float('current_life');
			$table->enum('gender', ['male', 'female']);
			$table->bigInteger('exp');
			
			/*
			 *	Puntos de PVP que obtiene al ganar 
			 *	combates de jugador contra jugador
			 */
			$table->integer('pvp_points', 5);
			
			$table->enum('race', ['dwarf', 'human', 'elf', 'drow']);
			
			/*
			 *	Id del clan al que pertenece
			 */
			$table->integer('clan_id');
			
			/*
			 *	Id de la zona en la que se encuentra
			 */
			$table->integer('zone_id', 3);
			
			/*
			 *	Estadísticas
			 */
			$table->integer('stat_life');
			$table->integer('stat_dexterity');
			$table->integer('stat_magic');
			$table->integer('stat_strength');
			$table->integer('stat_luck');
			
			/*
			 *	Lenguaje, como: es, en, etc.
			 */
			$table->string('language', 2);

			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('characters');
	}

}