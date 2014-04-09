<?php

class Dungeon extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'dungeons';
	public static $key = 'id';

	/**
	 * Niveles de dungeon
	 * En cada nivel, los atributos de los bichos se multiplicaran
	 * asi como las chances de drop.
	 * El multiplicador sera el nivel (por ejemplo en nivel elite
	 * se multiplica por 6)
	 */
	const NOOB_LEVEL   = 1;
	const NORMAL_LEVEL = 2;
	const EXPERT_LEVEL = 4;
	const ELITE_LEVEL  = 6;

	/**
	 * Verificamos si personaje puede realizar dungeon
	 * @param  Character $character 
	 * @return boolean
	 */
	public function can_character_do_dungeon(Character $character)
	{
		if ( $character->level < $this->min_level )
		{
			return false;
		}

		if ( $character->zone_id != $this->zone_id )
		{
			return false;
		}

		if ( $character->activities()->take(1)->count() != 0 )
		{
			return false;
		}

		return true;
	}

	/**
	 * Obtenemos el porcentaje (1-100) de progreso
	 * @param  Character $character 
	 * @param  integer   $level      Nivel del dungeon
	 * @return integer
	 */
	public function get_progress_percent_of(Character $character, $level)
	{
		$monstersKilled = $character->dungeons()
									->where('dungeon_id', '=', $this->id)
									->where('dungeon_level', '=', $level)
									->count();
		$totalMonsters = $this->monsters()->count();

		return (int) ($monstersKilled * 100 / $totalMonsters);
	}

	/**
	 * Obtenemos el nivel de dungeon del personaje
	 * @param  Character $character 
	 * @return integer
	 */
	public function get_level(Character $character)
	{
		$levels = array(self::NOOB_LEVEL, self::NORMAL_LEVEL, self::EXPERT_LEVEL, self::ELITE_LEVEL);

		foreach ( $levels as $level )
		{
			if ( $this->get_progress_percent_of($character, $level) != 100 )
			{
				return $level;
			}
		}

		return self::ELITE_LEVEL;
	}

	/**
	 * Query para obtener dungeons disponibles para personaje
	 * @param  Character
	 * @return Eloquent
	 */
	public static function available_for(Character $character)
	{
		return self::where('zone_id', '=', $character->zone_id)
				   ->where('min_level', '<=', $character->level);
	}

	/**
	 * Obtenemos nivel promedio de dungeon
	 * @return integer
	 */
	public function get_average_level()
	{
		$monsters = $this->monsters()->select(array('level'))->get();
		$levelSum = 0;

		foreach ( $monsters as $monster )
		{
			$levelSum += $monster->level;
		}

		return (int) ($levelSum / max(count($monsters), 1));
	}

	/**
	 * Verificamos si personaje ha derrotado a mounstruo en dungeon
	 * @param  Character $character 
	 * @param  Monster   $monster   
	 * @return boolean
	 */
	public function character_has_defeated_monster(Character $character, Monster $monster)
	{
		return $character->dungeons()
						 ->where('dungeon_id', '=', $this->id)
						 ->where('monster_id', '=', $monster->id)
						 ->take(1)
						 ->count() > 0;
	}

	/**
	 * Verificamos si personaje puede ver el stat de un mounstruo
	 * @param  Character $character 
	 * @return boolean
	 */
	public function can_character_see_stats_of_monster(Character $character, Monster $monster)
	{
		if ( (bool) $this->show_monsters_stats )
		{
			return true;
		}

		return $this->character_has_defeated_monster($character, $monster);
	}

	/**
	 * Query para obtener mounstruos de dungeon
	 * @return Eloquent
	 */
	public function monsters()
	{
		return $this->has_many_and_belongs_to("Monster", "dungeon_monsters");
	}
}