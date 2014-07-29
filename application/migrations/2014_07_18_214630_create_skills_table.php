<?php

class Create_Skills_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Laravel\Database\Schema::create("skills", function($table) {
            $table->integer("id")->key();
            $table->integer("level")->key();
            $table->string("name");
            $table->string("description");
            $table->string("requirements_text");
            $table->integer("direct_magic_damage");
            $table->integer("direct_physical_damage");
            $table->integer("physical_damage");
            $table->integer("magical_damage");
            $table->integer("stat_strength");
            $table->integer("stat_dexterity");
            $table->integer("stat_resistance");
            $table->integer("stat_magic");
            $table->integer("stat_magic_skill");
            $table->integer("stat_magic_resistance");
            $table->float("luck");
            $table->float("evasion");
            $table->float("magic_defense");
            $table->float("physical_defense");
            $table->float("critical_chance");
            $table->float("attack_speed");
            $table->integer("life");
            $table->integer("max_life");
            $table->float("renegeration_per_second");
            $table->integer("reflect_damage");
            $table->integer("reflect_magic_damage");
            $table->integer("travel_time");
            $table->integer("battle_rest");
            $table->integer("life_required");
            $table->integer("xp_rate");
            $table->integer("quest_xp_rate");
            $table->integer("drop_rate");
            $table->integer("explore_reward_rate");
            $table->integer("coin_rate");
            $table->integer("quest_coin_rate");
            $table->float("skill_cd_time");
            $table->boolean("percent");
            $table->integer("timeout");
            $table->integer("duration");
            $table->integer("chance");
            $table->string("triggered_by");
            $table->string("required_object_type");
            $table->string("type");
            $table->string("target");
            $table->string("dwarf");
            $table->string("elf");
            $table->string("human");
            $table->string("drow");
            $table->boolean("stackable");
            $table->integer("min_level_required");
            $table->integer("clan_level");
            $table->string("required_skills");
            $table->integer("cd");
            $table->boolean("can_be_random");
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