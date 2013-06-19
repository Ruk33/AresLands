<?php

class Create_Items_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function($table) {
			$table->increments('id');
			
			$table->string('name');
			$table->string('description');

			/*
			 *	Daño físico y mágico
			 */
			$table->float('p_damage');
			$table->float('m_damage');

			/*
			 *	Defensa mágica y física
			 */
			$table->float('p_defense');
			$table->float('m_defense');

			$table->float('attack_speed');

			/*
			 *	¿Dónde se equipa?
			 */
			$table->enum('body_part', [
				/*
				 *	Armor
				 */
				'chest',
				'legs',
				'feet',
				'head',
				'hands',

				/*
				 *	Weapon
				 */
				'lhand',
				'rhand',
				'lrhand',

				'none',
			]);

			/*
			 *	Tipo de objeto
			 */
			$table->enum('type', [
				/*
				 *	Weapon
				 */
				'blunt',
				'bigblunt',
				'sword',
				'bigsword',
				'bow',
				'dagger',
				'staff',
				'bigstaff',
				'shield',

				/*
				 *	Etc
				 */
				'potion',
				'arrow',

				/*
				 *	Extras
				 */
				'etc',
				'none',
			]);

			/*
			 *	Precio (en cobre)
			 */
			$table->integer('price');

			/*
			 *	Estadísticas
			 */
			$table->integer('stat_life');
			$table->integer('stat_dexterity');
			$table->integer('stat_magic');
			$table->integer('stat_strength');
			$table->integer('stat_luck');

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
			$table->integer('slot_amount');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('items');
	}

}