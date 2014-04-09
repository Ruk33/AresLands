<?php

return array(
	'dungeon_time_per_monster' => 300,

	'cheat_death_skill' => 65,
	'overload_skill' =>	54,
	'silence_skill' => 72,
	'reflect_skill' => 73,
	'stone_skill' => 68,
	'stun_skill' => 74,
	'root_skill' => 50,
	'clean_skill' => 52,
	'cure_skill' => 49,
	'anti_magic_skill' => 48,
	'confusion_skill' => 51,
	'invisibility_skill' => 58,
	'reveal_invisibility_skill' => 59,
	'mastery_skill' => 66,
	'ready_for_new_adventure_skill' => 70,
	'ongoing_skill' => 71,
	'trap_skill' => 64,
	'concede_skill' => 55,
	'concede_member_skill' => 75,
	'invocation' => 62,
	
	'trap_skills' => array(
		47, // grito
		50, // root
		51, // confusion
		56, // pereza
		72, // silencio
	),
	
	'max_talents' => 8,
	
	'racial_skills' => array(
		'dwarf' => array(42,46,47),
		'elf' => array(43,48,49),
		'drow' => array(44,50,51),
		'human' => array(45,52,53)
	),
	
	'vip_multiplier_coin_rate_skill' => 76,
	'vip_multiplier_xp_rate_skill' => 77,
	'vip_reduction_time_skill' => 78,
	
	'registration_url' => '//ironfist.com.ar/register',
	'login_url' => '//ironfist.com.ar/login',

	/*
	 *	Cantidad maxima
	 *	de pociones activas
	 */
	'max_potions' => 9999999,

	/*
	 *	Cantidad de pociones
	 *	que se pueden llevar
	 *	en el inventario
	 *
	 *	Formula
	 *	xp del personaje * bag_size
	 */
	'bag_size' => 0.60,

	/*
	 *	Multiplicadores
	 *	de los stats
	 *	
	 *	Formula:
	 *	cantidad de stat * multiplicador
	 */
	'strength_price_multiplier' => 0.3,
	'dexterity_price_multiplier' => 0.4,
	'resistance_price_multiplier' => 0.2,
	'magic_price_multiplier' => 0.4,
	'magic_skill_price_multiplier' => 0.5,
	'magic_resistance_price_multiplier' => 0.2,

	/*
	 *	Multiplicadores de stat
	 *	según la raza
	 */
	'dwarf_strength_price_multiplier' => 0.5,
	'dwarf_dexterity_price_multiplier' => 0.45,
	'dwarf_resistance_price_multiplier' => 0.35,
	'dwarf_magic_price_multiplier' => 1.5,
	'dwarf_magic_skill_price_multiplier' => 1.7,
	'dwarf_magic_resistance_price_multiplier' => 1.65,

	'human_strength_price_multiplier' => 0.5,
	'human_dexterity_price_multiplier' => 0.7,
	'human_resistance_price_multiplier' => 0.65,
	'human_magic_price_multiplier' => 0.55,
	'human_magic_skill_price_multiplier' => 0.85,
	'human_magic_resistance_price_multiplier' => 0.95,

	'drow_strength_price_multiplier' => 1.5,
	'drow_dexterity_price_multiplier' => 1.8,
	'drow_resistance_price_multiplier' => 1.65,
	'drow_magic_price_multiplier' => 0.4,
	'drow_magic_skill_price_multiplier' => 0.5,
	'drow_magic_resistance_price_multiplier' => 0.9,

	'elf_strength_price_multiplier' => 0.65,
	'elf_dexterity_price_multiplier' => 0.75,
	'elf_resistance_price_multiplier' => 0.65,
	'elf_magic_price_multiplier' => 0.95,
	'elf_magic_skill_price_multiplier' => 0.55,
	'elf_magic_resistance_price_multiplier' => 0.9,

	/*
	 *	Cantidad de slots de inventario
	 */
	'inventory_slot_amount' => 6,

	/*
	 *	Id del objeto que hace
	 *	de cofre
	 */
	'chest_item_id' => 136,

	/*
	 *	Id del objeto que hace
	 *	de experiencia
	 */
	'xp_item_id' => 2,

	/*
	 *	Id del objeto que hace de moneda
	 *	(cobre)
	 */
	'coin_id' => 1,

	/*
	 *	Nivel máximo de los clanes
	 */
	'clan_max_level' => 20,

	/*
	 *	Cantidad máxima de la barra
	 *	de actividad (cuando
	 *	llegan a este valor, la barra
	 *	está completa)
	 */
	'activity_bar_max' => 50,

	/*
	 *	Protección (en segundos) para un personaje
	 *	que pierde en un pvp teniendo menor nivel
	 *	que el ganador
	 *	240 = battle_rest_time
	 */
	'protection_time_on_lower_level_pvp' => 240 + 1800,

	/*
	 *	Cantidad de monedas (en cobre)
	 *	que cuesta viajar
	 */
	'travel_cost' => 10,

	/*
	 *	Tiempo que se tarda
	 *	en finalizar el viaje
	 *	(en segundos)
	 */
	'travel_time' => 300,

	/*
	 *	Tiempo de descanzo (en segundos)
	 *	después de una pelea
	 */
	'battle_rest_time' => 240,

	/*
	 *	Puntos que se otorgan
	 *	al pasar de nivel
	 */
	'points_per_level' => 5,

	/*
	 *	Mínima cantidad (en minutos)
	 *	de tiempo para explorar
	 */
	'min_explore_time' => 5,

	/*
	 *	Máxima cantidad (en minutos)
	 *	de tiempo para explorar
	 */
	'max_explore_time' => 240,

	/*
	 *	Rates
	 */
	'xp_rate' => 0.7,
	'quest_xp_rate' => 0.8,
	'drop_rate' => 1,
	'explore_reward_rate' => 1,
	'coins_rate' => 2,
	'quest_coins_rate' => 4,
);