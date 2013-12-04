<?php

return array(
	'registration_url' => '//ironfist.com.ar/register',
	'login_url' => '//ironfist.com.ar/login',

	/*
	 *	Cantidad maxima
	 *	de pociones activas
	 */
	'max_potions' => 50,

	/*
	 *	Cantidad de slots de inventario
	 */
	'inventory_slot_amount' => 6,

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
	'clan_max_level' => 10,

	/*
	 *	Cantidad máxima de la barra
	 *	de actividad (cuando
	 *	llegan a este valor, la barra
	 *	está completa)
	 */
	'activity_bar_max' => 35,

	/*
	 *	Protección (en segundos) para un personaje
	 *	que pierde en un pvp teniendo menor nivel
	 *	que el ganador
	 *	240 = battle_rest_time
	 */
	'protection_time_on_lower_level_pvp' => 240 + 300,

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
	'points_per_level' => 7,

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
	'xp_rate' => 1,
	'drop_rate' => 1,
	'explore_reward_rate' => 1,
	'coins_rate' => 1,
);