<?php

class TournamentClanScore extends Base_Model
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
	public static $table = 'tournament_clan_scores';

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
	 * Query para obtener el clan rival
	 * @return Eloquent
	 */
	public function rival_clan()
	{
		return $this->belongs_to('Clan', 'clan_rival_id');
	}

	/**
	 * Query para obtener scores de un clan
	 * en un especifico torneo
	 * @param  integer $tournament Id del torneo
	 * @param  integer $clan       Id del clan
	 * @return Eloquent
	 */
	private static function get_from_tournament_and_clan($tournament, $clan)
	{
		return self::where('tournament_id', '=', (int) $tournament)
				   ->where('clan_id', '=', (int) $clan);
	}

	/**
	 * Obtener cantidad de victorias totales
	 * @param  integer $tournament Id del torneo
	 * @param  integer $clan       Id del clan
	 * @return integer             Cantidad de victorias
	 */
	public static function get_victories($tournament, $clan)
	{
		$scores = self::get_from_tournament_and_clan($tournament, $clan)->get();
		$victories = 0;

		foreach ( $scores as $score )
		{
			$victories += $score->win_score;
		}

		return $victories;
	}

	/**
	 * Obtener cantidad de derrotas totales
	 * @param  integer $tournament Id del torneo
	 * @param  integer $clan       Id del clan
	 * @return integer             Cantidad de derrotas
	 */
	public static function get_defeats($tournament, $clan)
	{
		$scores = self::get_from_tournament_and_clan($tournament, $clan)->get();
		$defeats = 0;

		foreach ( $scores as $score )
		{
			$defeats += $score->defeat_score;
		}

		return $defeats;
	}

	/**
	 * Obtener porcentaje de victorias de un clan
	 * en un torneo
	 * @param  integer $tournament Id del torneo
	 * @param  integer $clan       Id del clan
	 * @return integer             Porcentaje de victorias
	 */
	public static function get_victory_percentage($tournament, $clan)
	{
		$victories = self::get_victories($tournament, $clan);
		$defeats = self::get_defeats($tournament, $clan);

		return (int) (100 / ($victories + $defeats + 1) * $victories);
	}

	/**
	 * Obtenemos el puntaje especifico de un clan contra
	 * su rival en un torneo. Si el mismo no existe, se crea
	 * @param  integer $tournament Id del torneo
	 * @param  integer $clan       Id del clan
	 * @param  integer $clanRival  Id del clan rival
	 * @return TournamentClanScore
	 */
	private static function get_from_tournament_clan_rival($tournament, $clan, $clanRival)
	{
		$score = self::get_from_tournament_and_clan($tournament, $clan)
					 ->where('clan_rival_id', '=', (int) $clanRival)
					 ->first();

		if ( ! $score )
		{
			$score = new self;

			$score->tournament_id = (int) $tournament;
			$score->clan_id = (int) $clan;
			$score->clan_rival_id = (int) $clanRival;
			$score->win_score = 0;
			$score->defeat_score = 0;

			$score->save();
		}

		return $score;
	}

	/**
	 * Se actualiza el puntaje del clan
	 * @param  integer   $tournament Id del torneo
	 * @param  Character $member     Miembro que perdio
	 * @param  Character $winner     Personaje contra el que perdio
	 */
	public static function member_is_defeated($tournament, Character $member, Character $winner)
	{
		$score = self::get_from_tournament_clan_rival($tournament, $member->clan_id, $winner->clan_id);

		$score->defeat_score += Tournament::get_defeat_score($member, $winner);
		$score->save();

		$victories = self::get_victories($tournament, $member->clan_id);
		$defeats = self::get_defeats($tournament, $member->clan_id);

		if ( $victories - $defeats < 25 )
		{
			TournamentRegisteredClan::disqualify(Tournament::find((int) $tournament), $member->clan);
		}
	}

	/**
	 * Se actualiza el puntaje de un clan
	 * @param  integer   $tournament Id del torneo
	 * @param  Character $member     Miembro que gano
	 * @param  Character $defeated   Personaje que perdio
	 */
	public static function member_is_victorius($tournament, Character $member, Character $defeated)
	{
		$score = self::get_from_tournament_clan_rival($tournament, $member->clan_id, $winner->clan_id);

		$score->win_score += Tournament::get_victory_score($member, $defeated);
		$score->save();
	}
}