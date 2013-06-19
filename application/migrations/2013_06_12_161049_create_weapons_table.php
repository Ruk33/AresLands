<?php
/**
 *	@deprecated
 */
class Create_Weapons_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('weapons', function($table) {
			$table->increments('id');
			$table->string('name', 80);
			$table->string('description');

			/*
			 *	Daño físico y mágico
			 */
			$table->float('p_damage');
			$table->float('m_damage');

			/*
			 *	Tipo de arma
			 */
			$table->enum('type', [
				'blunt',
				'bigblunt',
				'sword',
				'bigsword',
				'bow',
				'dagger',
				'staff',
				'bigstaff',
				'shield',
				'etc',
				'none',
			]);

			/*
			 *	Lugar en donde se "pone" el arma
			 */
			$table->enum('body_part', ['lhand', 'rhand', 'lrhand', 'none']);

			/*
			 *	Estadísticas que otorga
			 *	al usar el arma
			 */
			$table->integer('stat_life');
			$table->integer('stat_dexterity');
			$table->integer('stat_magic');
			$table->integer('stat_strength');
			$table->integer('stat_luck');

			$table->float('attack_speed');
			
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
			$table->integer('skill_level', 3);

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
		Schema::drop('weapons');
	}

}