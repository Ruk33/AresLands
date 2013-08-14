<?php

class ClanOrbPoint extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'clan_orb_points';
	public static $key = 'id';

	public function clan()
	{
		return $this->belongs_to('Clan', 'clan_id');
	}
}