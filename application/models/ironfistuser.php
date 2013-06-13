<?php

class IronFistUser extends Eloquent 
{
	public static $timestamps = false;
	public static $connection = 'ironfist';
	public static $table = 'users';
}