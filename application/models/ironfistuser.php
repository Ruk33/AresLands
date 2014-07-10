<?php

class IronFistUser extends Eloquent
{
	/**
	 * @var string
	 */
	public static $connection = 'ironfist';
	
	/**
	 * @var boolean
	 */
	public static $timestamps = false;
	
	/**
	 * @var string
	 */
	public static $table = 'users';
	
    /**
     * @var array
     */
    public static $factory = array(
        "name" => "string",
        "password" => "string",
        "email" => "email",
        "gender" => "male",
        "birth_date" => "date",
        "registration_date" => "date",
        "language" => "es",
        "permission" => 0,
        "last_mark_time" => 0,
        "coins" => 0,
        "avatar" => ""
    );
    
	/**
	 * @return Eloquent
	 */
	public function character()
	{
		return $this->has_one('Character', 'user_id');
	}
	
	/**
	 * Consumimos IronCoins al usuario que este logueado
	 * 
	 * @param integer $amount Cantidad de IronCoins a consumir
	 * @return boolean True si la operacion se realizo exitosamente
	 */
	public function consume_coins($amount)
	{
		if ( $this->coins < $amount )
		{
			return false;
		}
		
		$this->coins -= $amount;
		
		return $this->save();
	}
    
    /**
     * Agregamos IronCoins al usuario
     * 
     * @param integer $amount
     * @return boolean
     */
    public function add_coins($amount)
    {
        $this->coins += $amount;
        return $this->save();
    }
}
