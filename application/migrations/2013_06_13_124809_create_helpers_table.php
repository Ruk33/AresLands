<?php

class Create_Helpers_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('helpers', function($table) {
			$table->increments('id');
			$table->string('name', 80);

			/*
			 *	Daño físico y mágico
			 */
			$table->float('p_damage');
			$table->float('m_damage');

			/*
			 *	Precio para comprarlo
			 *	y por día (en cobre)
			 */
			$table->integer('price');
			$table->integer('price_day');

			/*
			 *	Estadísticas que otorga
			 *	al usar el ayudante
			 */
			$table->integer('stat_life');
			$table->integer('stat_dexterity');
			$table->integer('stat_magic');
			$table->integer('stat_strength');
			$table->integer('stat_luck');

			/*
			 *	Habilidad y su nivel que
			 *	se otorga al comprar el acompañante
			 */
			$table->integer('skill_id');
			$table->integer('skill_level');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('helpers');
	}

}