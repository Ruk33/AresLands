<?php
/**
 *	@deprecated
 */
class Create_Armors_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('armors', function($table) {
			$table->increments('id');
			$table->string('name', 80);
			$table->string('description');

			/*
			 *	Lugar en donde se "pone" la armadura
			 */
			$table->enum('body_part', ['chest', 'legs', 'feet', 'head', 'hands']);

			/*
			 *	Defensa física y mágica
			 */
			$table->float('p_def');
			$table->float('m_def');

			$table->integer('price');

			/*
			 *	¿Se permite vender, destruir o comerciar?
			 */
			$table->boolean('selleable');
			$table->boolean('destroyable');
			$table->boolean('tradeable');

			/*
			 *	Habilidad y su nivel que
			 *	se otorga al equiparse el arma
			 */
			$table->integer('skill_id');
			$table->integer('skill_level');

			/*
			 *	Cantidad de slots para agregar
			 *	piedras
			 */
			$table->integer('slot_amount', 3);
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('armors');
	}

}