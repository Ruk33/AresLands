<?php

class Orb extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'orbs';
	public static $key = 'id';

	public function owner()
	{
		return $this->belongs_to('Character', 'owner_character');
	}
}