<?php

class Create_Tournaments_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("tournaments", function($table) {
            $table->increments("id");
            $table->string("name");
            $table->integer("clan_winner_id");
            $table->integer("starts_at");
            $table->integer("ends_at");
            $table->integer("all_clans");
            $table->integer("min_members");
            $table->integer("mvp_id");
            $table->boolean("mvp_received_reward");
            $table->boolean("clan_leader_received_reward");
            $table->integer("battle_counter");
            $table->integer("life_potion_counter");
            $table->integer("potion_counter");
            $table->boolean("allow_potions");
            $table->integer("character_counter");
            $table->integer("coin_reward");
            $table->integer("mvp_coin_reward");
            $table->integer("item_reward");
            $table->integer("item_reward_amount");
            $table->boolean("active");
            $table->boolean("cleaned_potions");
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