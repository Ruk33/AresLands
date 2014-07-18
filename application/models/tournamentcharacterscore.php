<?php

class TournamentCharacterScore extends Base_Model
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
	public static $table = 'tournament_character_scores';

	/**
	 * Primery key
	 * @var string
	 */
	public static $key = 'id';

	/**
	 * Query para obtener el personaje
	 * @return Eloquent
	 */
	public function character()
	{
		return $this->belongs_to('Character', 'character_id');
	}

	/**
	 * Query para obtener puntuacion del torneo de un personaje
	 * @param  integer $tournament Id del torneo
	 * @param  integer $character  Id del personaje
	 * @return Eloquent
	 */
	public static function get_score($tournament, $character)
	{        
		return self::where('character_id', '=', (int) $character)
				   ->where('tournament_id', '=', (int) $tournament);
	}

	/**
	 * Verificamos si un personaje ya tiene el registro
	 * de puntacion creado para un torneo
	 * @param  Tournament $tournament 
	 * @param  Character  $character  
	 * @return boolean
	 */
	public static function has_score(Tournament $tournament, Character $character)
	{
		return self::get_score($tournament->id, $character->id)->take(1)->count() > 0;
	}

	/**
	 * Crea registro de puntuacion de personaje en torneo
	 * @param  Tournament $tournament 
	 * @param  Character  $character  
	 */
	public static function register(Tournament $tournament, Character $character)
	{
		if ( self::has_score($tournament, $character) )
		{
			return;
		}

		$score = new self;

		$score->character_id = $character->id;
		$score->tournament_id = $tournament->id;
		$score->win_score = 0;
		$score->defeat_score = 0;

		$score->save();

		$character->registered_in_tournament = true;
		$character->save();
	}

	/**
	 * Borramos el registro de puntuacion de personaje en torneo
	 * @param  Tournament $tournament 
	 * @param  Character  $character  
	 */
	public static function unregister(Tournament $tournament, Character $character)
	{
		if ( ! self::has_score($tournament, $character) )
		{
			return;
		}

		self::get_score($tournament->id, $character->id)->delete();

		$character->registered_in_tournament = false;
		$character->save();
	}

	/**
	 * Se registra victoria contra personaje
	 * @param  Character $character
	 */
	public function register_victory_against(Character $character)
	{
		$this->win_score += Tournament::get_victory_score($this->character, $character);
		$this->save();
	}

	/**
	 * Se registra derrota contra personaje
	 * @param  Character $character
	 */
	public function register_lose_against(Character $character)
	{
		$this->defeat_score += Tournament::get_defeat_score($this->character, $character);
		$this->save();
	}

	/**
	 * Registramos la perdida y ademas actualizamos
	 * el puntaje del clan
	 * @param  integer   $tournament Id del torneo
	 * @param  Character $character  Personaje contra el que perdio
	 */
	public function register_lose_and_update_clan_score($tournament, Character $character)
	{
		$this->register_lose_against($character);
		TournamentClanScore::member_is_defeated((int) $tournament, $this->character, $character);
	}

	/**
	 * Registramos la victoria y ademas actualizamos
	 * el puntaje del clan
	 * @param  integer   $tournament Id del torneo
	 * @param  Character $character  Personaje contra el que gano
	 */
	public function register_victory_and_update_clan_score($tournament, Character $character)
	{
		$this->register_victory_against($character);
		TournamentClanScore::member_is_victorius((int) $tournament, $this->character, $character);
	}
}