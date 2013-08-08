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

	public function attacker()
	{
		return $this->belongs_to('Character', 'last_attacker');
	}

	/**
	 *	Verificamos si el orbe puede ser robado por el personaje
	 *
	 *	@param <Character> $character Personaje que intenta robar
	 *	@return <Bool>
	 */
	public function can_be_stolen_by(Character $character)
	{
		return 
			/*
			 *	Niveles
			 */
			( $character->level >= $this->min_level && $character->level <= $this->max_level )
			&&
			/*
			 *	Cantidad de orbes
			 */
			( $character->orbs()->count() < 2 );
	}

	public function give_to(Character $character)
	{
		$this->owner_character = $character->id;
		$this->last_attacker = null;
		$this->last_attack_time = null;

		$this->save();
	}

	public function failed_robbery(Character $character)
	{
		/*
		 *	Protección de 12 horas
		 */
		OrbProtection::add_protection($character, $this->owner()->select(array('id'))->first(), 12 * 60 * 60);

		$this->last_attacker = $character->id;
		$this->last_attack_time = time();

		$this->save();
	}
}