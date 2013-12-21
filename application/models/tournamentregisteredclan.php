<?php

class TournamentRegisteredClan extends Base_Model
{
	/**
	 * Soft delete no borra el registro
	 * solo agrega otra columna mostrando
	 * si el mismo esta o no borrado
	 * @var boolean
	 */
	public static $softDelete = false;

	/**
	 * ¿timestamps? (created_at, etc)
	 * @var boolean
	 */
	public static $timestamps = false;

	/**
	 * Nombre de la tabla
	 * @var string
	 */
	public static $table = 'tournament_registered_clans';

	/**
	 * Primery key
	 * @var string
	 */
	public static $key = 'id';

	/**
	 * Query para obtener el clan
	 * @return Eloquent
	 */
	public function clan()
	{
		return $this->belongs_to('Clan', 'clan_id');
	}

	/**
	 * Query para obtener el torneo
	 * @return Eloquent
	 */
	public function tournament()
	{
		return $this->belongs_to('Tournament', 'tournament_id');
	}

	/**
	 * Query para obtener registro de clan en torneo
	 * @param  Tournament $tournament 
	 * @param  Clan       $clan       
	 * @return Eloquent
	 */
	public static function get_registration(Tournament $tournament, Clan $clan)
	{
		return self::where('tournament_id', '=', $tournament->id)
				   ->where('clan_id', '=', $clan->id);
	}

	/**
	 * Verificamos si clan esta registrado en torneo
	 * @param  Tournament $tournament 
	 * @param  Clan       $clan       
	 * @return boolean
	 */
	public static function is_registered(Tournament $tournament, Clan $clan)
	{
		return self::get_registration($tournament, $clan)->take(1)->count() > 0;
	}

	/**
	 * Registramos un clan en torneo (junto con sus miembros)
	 * @param  Tournament $tournament 
	 * @param  Clan       $clan     
	 * @return boolean
	 */
	public static function register(Tournament $tournament, Clan $clan)
	{
		if ( self::is_registered($tournament, $clan) )
		{
			return false;
		}

		$registeredClan = new self;

		$registeredClan->tournament_id = $tournament->id;
		$registeredClan->clan_id = $clan->id;

		$registeredClan->save();

		foreach ( $clan->members()->select(array('id', 'clan_id'))->get() as $member )
		{
			TournamentCharacterScore::register($tournament, $member);
		}

		return true;
	}

	/**
	 * Sacamos del registro a un clan (y a sus miembros)
	 * @param  Tournament $tournament 
	 * @param  Clan       $clan       
	 * @return boolean
	 */
	public static function unregister(Tournament $tournament, Clan $clan)
	{
		$registration = self::get_registration($tournament, $clan)->first();

		if ( ! $registration )
		{
			return false;
		}

		$clan = $registration->clan()->select(array('id'))->first();

		foreach ( $clan->members()->select(array('id', 'clan_id'))->get() as $member )
		{
			TournamentCharacterScore::unregister($tournament, $member);
		}

		$registration->delete();

		return true;
	}

	/**
	 * Revisar si un clan esta descalificado de un torneo
	 * @param  Tournament $tournament 
	 * @param  Clan       $clan       
	 * @return boolean
	 */
	public static function is_disqualified(Tournament $tournament, Clan $clan)
	{
		return self::get_registration($tournament, $clan)->first()->disqualified;
	}

	/**
	 * Descalificamos a grupo
	 * @param  Tournament $tournament 
	 * @param  Clan       $clan       
	 */
	public static function disqualify(Tournament $tournament, Clan $clan)
	{
		if ( self::is_registered($tournament, $clan) )
		{
			$registration = self::get_registration($tournament, $clan)->first();
			$registration->disqualified = true;
			$registration->save();

			Message::tournament_disquialify($clan->lider);
		}
	}
}