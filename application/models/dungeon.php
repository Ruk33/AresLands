<?php

class Dungeon extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'dungeons';
	public static $key = 'id';
}