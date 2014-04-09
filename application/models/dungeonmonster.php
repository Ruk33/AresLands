<?php

class DungeonMonster extends Eloquent
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'dungeon_monsters';
	public static $key = 'id';

	/**
	 * Query para obtener mounstruo
	 * @return Eloquent
	 */
	public function monster()
	{
		return $this->belongs_to("Monster", "monster_id");
	}
}