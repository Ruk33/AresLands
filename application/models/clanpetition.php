<?php

class ClanPetition extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'clan_petitions';
	public static $key = 'id';

	public function clan()
	{
		return $this->belongs_to('Clan', 'clan_id');
	}

	public function character()
	{
		return $this->belongs_to('Character', 'character_id');
	}
}