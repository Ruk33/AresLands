<?php

class Orb extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'orbs';
	public static $key = 'id';

	public function get_tooltip()
	{
		$orb = $this;

		$coins = Item::get_divided_coins($orb->coins);
		$coins = $coins['text'];

		$tooltip = "<h6>$orb->name</h6>";
		$tooltip .= "<p class='text-left'>$orb->description</p>";

		$tooltip .= "<ul class='unstyled'>";
		$tooltip .= "<li class='positive'><b>Recompensas que otorga este orbe:</b></li>";
		$tooltip .= "<li><strong>Monedas:</strong>$coins</li>";
		$tooltip .= "<li><strong>Puntos:</strong>$orb->points</li>";
		$tooltip .= "</ul>";

		$tooltip .= "<ul class='unstyled'>";
		$tooltip .= "<li class='negative'><b>Requerimientos:</b></li>";
		$tooltip .= "<li><strong>Nivel mínimo:</strong> $orb->min_level</li>";
		$tooltip .= "<li><b>Nivel máximo:</b> $orb->max_level</li>";
		$tooltip .= "</ul>";

		return $tooltip;
	}

	public function owner()
	{
		return $this->belongs_to('Character', 'owner_character');
	}

	public function attacker()
	{
		return $this->belongs_to('Character', 'last_attacker');
	}
	
	/**
	 * Reinicia los valores del orbe a null
	 */
	public function reset()
	{
		$this->owner_character = null;
		$this->last_attacker = null;
		$this->last_attack_time = null;

		$this->save();
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
			( ! $character->has_orb() );
	}

	public function give_to(Character $character)
	{
		$this->owner_character = $character->id;
        $this->acquisition_time = time();
		$this->last_attacker = null;
		$this->last_attack_time = null;

		$this->save();
	}

	/**
	 *	Dar orbe a candidato aleatorio
	 */
	public function give_to_random()
	{
		$characters = Character::select(array('id'))->
		where('level', '>=', $this->min_level)->
		where('level', '<=', $this->max_level)->
		order_by(DB::raw('RAND()'))->
		get();

		foreach ( $characters as $character )
		{
			if ( ! $character->has_orb() )
			{
				$this->give_to($character);
				break;
			}
		}
	}

	public function give_periodic_reward()
	{
		$owner = $this->owner()->select(array('id', 'clan_id', 'level'))->first();
		$clanOrbPoint = null;

		if ( $owner )
		{
			$owner->add_coins($this->coins + $owner->level);

			/*
			 *	Verificamos que esté en un grupo
			 */
			if ( $owner->clan_id > 0 )
			{
				$clanOrbPoint = ClanOrbPoint::where('clan_id', '=', $owner->clan_id)->select(array('id', 'points'))->first();

				if ( ! $clanOrbPoint )
				{
					$clanOrbPoint = new ClanOrbPoint();

					$clanOrbPoint->clan_id = $owner->clan_id;
					$clanOrbPoint->points = $this->points;
				}
				else
				{
					$clanOrbPoint->points += $this->points;
				}

				$clanOrbPoint->save();
			}
		}
	}

	public function failed_robbery(Character $character)
	{
		/*
		 *	Protección de 45 minutos
		 */
		AttackProtection::add($character, $this->owner()->select(array('id'))->first(), 45 * 60);

		$this->last_attacker = $character->id;
		$this->last_attack_time = time();

		$this->save();
	}
}