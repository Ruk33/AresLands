<?php

class DungeonMonster extends Monster
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'dungeon_monsters';
    
	/**
	 * Query para obtener mounstruo
	 * @return Eloquent
	 */
	public function monster()
	{
		return $this->belongs_to("Monster", "monster_id");
	}
}