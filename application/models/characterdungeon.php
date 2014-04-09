<?php

class CharacterDungeon extends Eloquent
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_dungeons';
	public static $key = 'id';

	/**
	 * Verificamos si personaje tiene progreso en dungeon
	 * @param  Character $character 
	 * @param  Dungeon   $dungeon   
	 * @param  integer   $level     
	 * @param  Monster   $monster   
	 * @return boolean              
	 */
	public static function has_progress(Character $character, Dungeon $dungeon, $level, Monster $monster)
	{
		return $character->dungeons()
						 ->where('dungeon_id', '=', $dungeon->id)
						 ->where('dungeon_level', '=', $level)
						 ->where('monster_id', '=', $monster->id)
						 ->take(1)
						 ->count() == 1;
	}

	/**
	 * Hacemos progreso en una dungeon de un personaje
	 * @param  Character $character 
	 * @param  Dungeon   $dungeon   
	 * @param  integer   $level     
	 * @param  Monster   $monster   
	 */
	public static function make_progress(Character $character, Dungeon $dungeon, $level, Monster $monster)
	{
		if ( ! self::has_progress($character, $dungeon, $level, $monster) )
		{
			$characterDungeon = new self(array(
				'character_id'  => $character->id,
				'dungeon_id'    => $dungeon->id,
				'dungeon_level' => $level,
				'monster_id'    => $monster->id
			));

			$characterDungeon->save();
		}
	}
}