<?php

/**
 * @deprecated
 */
class CharacterDungeonProgress extends Eloquent
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_dungeons_progress';
	public static $key = 'id';
}