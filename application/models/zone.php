<?php

class Zone extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'zones';
	public static $key = 'id';

	public function cities()
	{
		return $this->has_many('Zone', 'belongs_to')->where('type', '=', 'city');
	}

	public function villages()
	{
		return $this->has_many('Zone', 'belongs_to')->where('type', '=', 'village');
	}

	public function farm_zones()
	{
		return $this->has_many('Zone', 'belongs_to')->where('type', '=', 'farmzone');
	}

	public function npcs()
	{
		return $this->has_many('Npc', 'zone_id');
	}
}