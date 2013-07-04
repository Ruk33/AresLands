<?php

class IronFistUser extends Eloquent 
{
	public static $timestamps = false;
	public static $connection = 'ironfist';
	public static $table = 'users';
	public static $key = 'id';

	public function character()
	{
		return $this->has_one('Character', 'user_id');
	}
}