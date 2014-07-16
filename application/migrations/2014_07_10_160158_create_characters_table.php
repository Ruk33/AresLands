<?php

class Create_Characters_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("characters", function($table){
            $table->increments("id");
            $table->integer("user_id");
            $table->string("ip");
            $table->string("name");
            $table->integer("level");
            $table->decimal("max_life", 11, 2);
            $table->decimal("current_life", 11, 2);
            $table->string("gender");
            $table->integer("pvp_points");
            $table->string("race");
            $table->integer("clan_id");
            $table->integer("clan_permission");
            $table->integer("zone_id");
            $table->integer("stat_dexterity");
            $table->integer("stat_magic");
            $table->integer("stat_strength");
            $table->integer("stat_resistance");
            $table->integer("stat_magic_skill");
            $table->integer("stat_magic_resistance");
            $table->integer("stat_strength_extra");
            $table->integer("stat_dexterity_extra");
            $table->integer("stat_resistance_extra");
            $table->integer("stat_magic_extra");
            $table->integer("stat_magic_skill_extra");
            $table->integer("stat_magic_resistance_extra");
            $table->string("language");
            $table->decimal("xp", 11, 2);
            $table->decimal("xp_next_level", 11, 2);
            $table->boolean("is_traveling");
            $table->integer("last_regeneration_time");
            $table->integer("points_to_change");
            $table->boolean("is_exploring");
            $table->integer("last_activity_time");
            $table->integer("last_logged");
            $table->boolean("registered_in_tournament");
            $table->string("characteristics");
            $table->float("regeneration_per_second");
            $table->float("regeneration_per_second_extra");
            $table->float("evasion");
            $table->float("evasion_extra");
            $table->float("critical_chance");
            $table->float("critical_chance_extra");
            $table->float("attack_speed");
            $table->float("attack_speed_extra");
            $table->float("magic_defense");
            $table->float("magic_defense_extra");
            $table->float("physical_defense");
            $table->float("physical_defense_extra");
            $table->float("magic_damage");
            $table->float("magic_damage_extra");
            $table->float("physical_damage");
            $table->float("physical_damage_extra");
            $table->float("reflect_magic_damage");
            $table->float("reflect_magic_damage_extra");
            $table->float("reflect_physical_damage");
            $table->float("reflect_physical_damage_extra");
            $table->float("travel_time");
            $table->float("travel_time_extra");
            $table->float("battle_rest_time");
            $table->float("battle_rest_time_extra");
            $table->float("skill_cd_time");
            $table->float("skill_cd_time_extra");
            $table->float("luck");
            $table->float("luck_extra");
            $table->integer("xp_rate");
            $table->integer("xp_rate_extra");
            $table->integer("quest_xp_rate");
            $table->integer("quest_xp_rate_extra");
            $table->integer("drop_rate");
            $table->integer("drop_rate_extra");
            $table->integer("explore_reward_rate");
            $table->integer("explore_reward_rate_extra");
            $table->integer("coin_rate");
            $table->integer("coin_rate_extra");
            $table->integer("quest_coin_rate");
            $table->integer("quest_coin_rate_extra");
            $table->integer("talent_points");
            $table->integer("invisible_until");
            $table->integer("second_mercenary");
            
            $table->timestamps();
            $table->timestamp("deleted_at");
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("characters");
	}

}