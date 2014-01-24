<?php

/**
 * @author Franco Montenegro <area51ruke@gmail.com>
 * @version 0.0.1
 * @deprecated
 */
class IronFist extends Eloquent 
{
	/**
	 * @var string
	 */
	public static $connection = 'ironfist';
	
	/**
	 * @var string
	 */
	public static $table = 'users';
	
	/**
	 * @var IronFist
	 */
	private static $instance;
	
	/**
	 * @var boolean
	 */
	public static $timestamps = false;
		
	/**
	 * Obtenemos al usuario que esta logueado
	 * @return IronFist | null
	 */
	public static function get_instance()
	{
		if ( ! self::$instance && Auth::check() )
		{
			self::$instance = self::find(Auth::user()->id);
		}
		
		return self::$instance; 
	}
}