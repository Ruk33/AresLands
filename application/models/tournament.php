<?php

class Tournament extends Base_Model
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
	public static $table = 'tournaments';

	/**
	 * Primery key
	 * @var string
	 */
	public static $key = 'id';

	/**
	 * Query para obtener el clan ganador
	 * @return Eloquent
	 */
	public function winner()
	{
		return $this->belongs_to('Clan', 'clan_winner_id');
	}

	/**
	 * Query para obtener el jugador mvp
	 * @return Eloquent
	 */
	public function mvp()
	{
		return $this->belongs_to('Character', 'mvp_id');
	}

	/**
	 * Query para obtener todos los
	 * clanes registrados
	 * @return Eloquent
	 */
	public function get_registered_clans()
	{
		return $this->has_many('TournamentRegisteredClan', 'tournament_id');
	}

	/**
	 * Query para obtener el objeto recompensa
	 * @return Eloquent
	 */
	public function reward_item()
	{
		return $this->belongs_to('Item', 'item_reward');
	}

	/**
	 * Query para obtener el torneo
	 * que se esta ejecutando
	 * @return Eloquent
	 */
	public static function get_active()
	{
		$time = time();

		return self::where('starts_at', '<=', $time)
				   ->where('ends_at', '>=', $time);
	}

	/**
	 * Query para obtener torneo
	 * que esta proximo a ejecutarse
	 * @return Eloquent
	 */
	public static function get_upcoming()
	{
		return self::where('starts_at', '>', time());
	}

	/**
	 * Query para obtener el ultimo torneo
	 * @return Eloquent
	 */
	public static function get_last()
	{
		return self::order_by('starts_at', 'desc');
	}

	/**
	 * ¿Estamos en un torneo?
	 * @return boolean
	 */
	public static function is_active()
	{
		return self::get_active()->take(1)->count() > 0;
	}

	/**
	 * Comprobamos si hay un torneo que esta proximo
	 * @return boolean
	 */
	public static function is_upcoming()
	{
		return self::get_upcoming()->take(1)->count() > 0;
	}

	/**
	 * Obtener la cantidad de jugadores registrados
	 * @return integer
	 */
	public function get_registered_characters_count()
	{
		$count = 0;

		$registeredClans = $this->get_registered_clans()->get();

		foreach ( $registeredClans as $registeredClan )
		{
			$clan = $registeredClan->clan()->select(array('id'))->first();
			$count += $clan->members()->count();
		}

		return $count;
	}

	/**
	 * Obtener cuanto puntaje da una victoria
	 * @param  Character $winner
	 * @param  Character $loser
	 * @return integer
	 */
	public static function get_victory_score(Character $winner, Character $loser)
	{
		if ( Tournament::is_active() )
		{
			$loserClan = $loser->clan;
			
			if ( ! $loserClan || TournamentRegisteredClan::is_disqualified(Tournament::get_active()->first(), $loserClan) )
			{
				return 0;
			}
		}
		
		if ( $winner->level <= $loser->level )
		{
			return 1;
		}
		
		if ( $winner->level - $loser->level < 3  )
		{
			return 0.3;
		}

		return 0.1;
	}

	/**
	 * Obtener cuanto puntaje da una derrota
	 * @param  Character $loser
	 * @param  Character $winner
	 * @return integer
	 */
	public static function get_defeat_score(Character $loser, Character $winner)
	{
		if ( Tournament::is_active() )
		{
			if ( TournamentRegisteredClan::is_disqualified(Tournament::get_active()->first(), $loser->clan) )
			{
				return 0;
			}
		}

		return 1;
	}

	/**
	 * Actualizamos el contador de batallas
	 * @param  integer $add Cantidad a sumar
	 */
	public function update_battle_counter($add = 1)
	{
		$this->battle_counter += $add;
		$this->save();
	}

	/**
	 * Actualizamos el contador de pociones de vida
	 * @param  integer $add Cantidad a sumar
	 */
	public function update_life_potions_counter($add = 1)
	{
		$this->life_potion_counter += $add;
		$this->save();
	}

	/**
	 * Actualizamos el contador de pociones
	 * @param  integer $add Cantidad a sumar
	 */
	public function update_potions_counter($add = 1)
	{
		$this->potion_counter += $add;
		$this->save();
	}

	/**
	 * Se verifica si un clan esta registrado
	 * @param  Clan    $clan
	 * @return boolean
	 */
	public function is_clan_registered(Clan $clan)
	{
		return $this->get_registered_clans()->where('clan_id', '=', $clan->id)->take(1)->count() > 0;
	}

	/**
	 * Verificamos si personaje puede registrar a su clan
	 * @param  Character $character
	 * @return boolean
	 */
	public function can_register_clan(Character $character)
	{
		$clan = $character->clan()->select(array('id', 'leader_id'))->first();

		if ( ! $clan )
		{
			return false;
		}

		if ( ! $character->has_permission(Clan::PERMISSION_REGISTER_TOURNAMENT) )
		{
			return false;
		}

		if ( $this->starts_at < time() )
		{
			return false;
		}

		if ( $this->is_clan_registered($clan) )
		{
			return false;
		}

		if ( $clan->members()->count() < $this->min_members )
		{
			return false;
		}

		return true;
	}

	/**
	 * Verificamos si un personaje puede sacar
	 * del torneo a su clan
	 * @param  Character $character 
	 * @return boolean
	 */
	public function can_unregister_clan(Character $character)
	{
		$clan = $character->clan()->select(array('id', 'leader_id'))->first();

		if ( ! $clan )
		{
			return false;
		}

		if ( ! $character->has_permission(Clan::PERMISSION_REGISTER_TOURNAMENT) )
		{
			return false;
		}

		if ( $this->starts_at < time() )
		{
			return false;
		}

		if ( ! $this->is_clan_registered($clan) )
		{
			return false;
		}

		if ( $this->all_clans )
		{
			return false;
		}

		return true;
	}

	/**
	 * Registramos clan en torneo
	 * @param  Clan   $clan Clan a registrar
	 * @return boolean
	 */
	public function register_clan(Clan $clan)
	{
		return TournamentRegisteredClan::register($this, $clan);
	}

	/**
	 * Registramos todos los grupos
	 */
	public function register_all_clans()
	{
		foreach ( Clan::all() as $clan )
		{
			$this->register_clan($clan);
		}
	}

	/**
	 * Sacamos del torneo a un clan
	 * @param  Clan   $clan
	 * @return boolean
	 */
	public function unregister_clan(Clan $clan)
	{
		return TournamentRegisteredClan::unregister($this, $clan);
	}

	/**
	 * Obtenemos clan ganador de un torneo
	 * @return Clan
	 */
	public function get_clan_winner()
	{
		if ( $this->clan_winner_id )
		{
			return $this->winner()->first();
		}

		$registeredClans = $this->get_registered_clans()->get();

		$clanWinner = null;
		$clanWinnerScore = -1;

		$tmpClan = null;
		$tmpScore = 0;

		foreach ( $registeredClans as $registeredClan )
		{
			$tmpClan = $registeredClan->clan;
			$tmpScore = TournamentClanScore::get_victory_percentage($this->id, $tmpClan->id);

			if ( $clanWinnerScore < $tmpScore )
			{
				$clanWinner = $tmpClan;
				$clanWinnerScore = $tmpScore;
			}
		}

		return $clanWinner;
	}

	/**
	 * Obtenemos MVP de torneo
	 * @return Character
	 */
	public function get_character_mvp()
	{
		if ( $this->mvp_id )
		{
			return $this->mvp()->first();
		}

		$clanWinner = $this->get_clan_winner();
		$members = $clanWinner->members;
		$mvp = $members[mt_rand(0, count($members) - 1)];

		return $mvp;
	}

	/**
	 * Verificamos si personaje puede reclamar
	 * el premio MVP
	 * @param  Character $character 
	 * @return boolean
	 */
	public function can_reclaim_mvp_reward(Character $character)
	{
		return $character->id == $this->mvp_id && $this->mvp_received_reward == false;
	}

	/**
	 * Verificamos si personaje puede reclamar
	 * premio de lider de grupo
	 * @param  Character $character 
	 * @return boolean
	 */
	public function can_reclaim_leader_reward(Character $character)
	{
		$clan = $character->clan()->select(array('id', 'leader_id'))->first();

		if ( ! $clan )
		{
			return false;
		}

		return $character->id == $clan->leader_id && $clan->id == $this->clan_winner_id && $this->clan_leader_received_reward == false;
	}

	/**
	 * Damos recompensa al MVP del torneo y le mostrados
	 * las mismas en un mensaje
	 * @return mixed Mensaje de error o true en caso de exito
	 */
	public function give_mvp_reward_and_send_message()
	{
		if ( $this->mvp_received_reward )
		{
			return 'Ya has reclamado tu premio.';
		}

		$mvp = $this->get_character_mvp();

		$rewards = array(
			array(
				'id' => Config::get('game.coin_id'),
				'name' => 'Monedas',
				'amount' => $mvp->xp * 3 + $this->mvp_coin_reward
			),

			array(
				'id' => Config::get('game.chest_item_id'),
				'name' => 'Cofre',
				'amount' => 2,
			)
		);

		foreach ( $rewards as $reward )
		{
			if ( ! $mvp->can_take_item($reward['id'], $reward['amount']) )
			{
				return 'No tienes espacio en el inventario';
			}
		}

		foreach ( $rewards as $reward )
		{
			$mvp->add_item($reward['id'], $reward['amount']);
		}

		$this->mvp_received_reward = true;
		$this->save();

		Message::tournament_mvp_reward($mvp, $rewards);

		return true;
	}

	/**
	 * Damos recompensa a lider de clan
	 * y enviamos mensaje para notificarle
	 * @return mixed Mensaje de error o true en caso de exito
	 */
	public function give_clan_leader_reward_and_send_message()
	{
		if ( $this->clan_leader_received_reward )
		{
			return 'Ya has reclamado tu premio.';
		}

		$clan = $this->get_clan_winner();
		$lider = $clan->lider;

		$reward = $this->reward_item;

		if ( ! $lider->can_take_item($reward, $this->item_reward_amount) )
		{
			return 'No tienes espacio en el inventario';
		}

		$lider->add_item($reward, $this->item_reward_amount);

		$this->clan_leader_received_reward = true;
		$this->save();

		Message::tournament_clan_lider_reward($lider, $reward, $this->item_reward_amount);

		return true;
	}

	/**
	 * Devolvemos la cantidad de monedas que recibe
	 * un jugador dependiendo de su participacion
	 * en el torneo
	 * @param  Character $character 
	 * @return integer
	 */
	public function get_character_coin_reward(Character $character)
	{
		$score = TournamentCharacterScore::get_score($this->id, $character->id)->first();

		return ($score->win_score + $score->defeat_score) * $character->level + $this->coin_reward;
	}

	/**
	 * Damos monedas a los participantes del torneo,
	 * les notificamos de la recompensa y actualizamos su
	 * estado (porque el torneo ya termino)
	 */
	public function give_coin_to_characters_and_exit_from_tournament()
	{
		$registeredClans = $this->get_registered_clans()->get();

		foreach ( $registeredClans as $registeredClan )
		{
			$clan = $registeredClan->clan;
			$members = $clan->members()->get();

			foreach ( $members as $member )
			{
				$amount = $this->get_character_coin_reward($member);
				$member->add_coins($amount);
				Message::tournament_coin_reward($member, $amount);

				$member->registered_in_tournament = false;
				$member->save();
			}
		}
	}

	/**
	 * Se verifica por los torneos que han
	 * finalizado pero que aun no se han
	 * actualizado los ganadores ni dadas
	 * las recompensas
	 */
	public static function check_for_finished()
	{
		$tournaments = self::where('active', '=', 1)
						   ->where('ends_at', '<', time())
						   ->get();

		foreach ( $tournaments as $tournament )
		{
			$tournament->active = 0;
			$tournament->clan_winner_id = $tournament->get_clan_winner()->id;
			$tournament->mvp_id = $tournament->get_character_mvp()->id;
			$tournament->save();

			$tournament->give_coin_to_characters_and_exit_from_tournament();
		}
	}

	/**
	 * Iniciamos todos los torneos que tengan que ser
	 * iniciados
	 */
	public static function check_for_started()
	{
		$tournaments = self::where('active', '=', 0)
						   ->where('starts_at', '<', time())
						   ->where('ends_at', '>', time())
						   ->get();

		foreach ( $tournaments as $tournament )
		{
			// Si, podemos hacerlo con una simple query
			// pero como no deberia haber muchos no
			// hay problema
			$tournament->active = 1;
			$tournament->save();
		}
	}

	/**
	 * Revisamos y removemos los buffs activos de los personajes.
	 * Esto en el caso de que haya un torneo activo y que
	 * el mismo no acepte pociones
	 */
	public static function check_for_potions()
	{
		$tournament = self::get_active()->first();

		if ( $tournament && ! $tournament->allow_potions && ! $tournament->cleaned_potions )
		{
			$characters = Character::where('registered_in_tournament', '=', true)->get();

			foreach ( $characters as $character )
			{
				$characterSkills = $character->get_non_clan_skills()->select(array('character_skills.*'))->get();

				foreach ( $characterSkills as $characterSkill )
				{
					$character->remove_buff($characterSkill);
				}
			}

			$tournament->cleaned_potions = true;
			$tournament->save();
		}
	}
}