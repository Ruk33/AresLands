<?php

class BattleLog extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'battle_logs';
	public static $key = 'id';
}