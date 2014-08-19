<?php

class Orb extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'orbs';
	public static $key = 'id';
    
    /**
     * Obtenemos el path de la imagen del orbe
     * @return string
     */
    public function get_image_path()
    {
        return URL::base() . '/img/' . $this->image;
    }
    
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

    public function get_text_for_tooltip()
    {
        return $this->get_tooltip();
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
        $this->acquisition_time = null;
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
		$owner = $this->owner;
        
		if ( $owner )
		{
            $coins = $this->coins + $owner->level;
            
            // Si lo mantiene por 4 dias, comienza a obtener nuevas recompensas
            if ( $this->acquisition_time + (60 * 60 * 24 * 4) < time() )
            {
                $coins *= 1.5;
                
                // 3% de posibilidad para obtener cofre
                if ( mt_rand(1, 33) == 1 )
                {
                    if ( $owner->add_item(Config::get('game.chest_item_id')) )
                    {
                        Message::orb_chest_reward($owner, 1);
                    }
                }
                
                // 1% de posibilidad de obtener ironcoins
                if ( mt_rand(1, 100) == 1 )
                {
                    if ( $owner->ironfist_user->add_coins(3) )
                    {
                        Message::orb_ironcoins_reward($owner, 3);
                    }
                }
                
                if ( $cureSkill = Skill::find(Config::get('game.cure_skill')) )
                {
                    $cureSkill->cast($owner, $owner);
                }
                
                if ( $reflectSkill = Skill::find(Config::get('game.reflect_skill')) )
                {
                    $reflectSkill->cast($owner, $owner);
                }
            }
            
            $owner->add_coins($coins);

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