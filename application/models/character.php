<?php

class Character extends Base_Model
{
	public static $softDelete = true;
	public static $timestamps = true;
	public static $table = 'characters';

	protected $rules = [
		'name' => 'required|unique:Characters|between:3,10',
		'race' => ['required', 'match:/^(dwarf|human|drow|elf)$/'],
		'gender' => ['required', 'match:/^(male|female)$/'],
	];

	protected $messages = [
		'name_required' => 'El nombre del personaje es requerido',
		'name_unique' => 'Ya existe otro personaje con ese nombre',
		'name_between' => 'El nombre del personaje debe tener entre 3 y 10 carácteres',

		'race_required' => 'La raza es requerida',
		'race_match' => 'La raza es incorrecta',

		'gender_required' => 'El género es requerido',
		'gender_match' => 'El género es incorrecto',
	];

	/**
	 *	Devolvemos el personaje del usuario
	 *	que esté logueado
	 *
	 *	@return <Character>
	 */
	public static function get_character_of_logged_user()
	{
		if ( Auth::guest() )
		{
			return null;
		}

		return Character::where('user_id', '=', Auth::user()->id)->first();
	}

	public function get_link()
	{
		return '<a href="' . URL::to('authenticated/character/' . $this->name ) . '">' . $this->name . '</a>';
	}

	/**
	 *	@param <bool> $positive Si es true, traemos bonificaciones positivas, si es false negativas
	 *	@return <array>
	 */
	public function get_bonifications($positive = true)
	{
		$character = Session::get('character');

		$characterItems = null;
		$characterSkills = null;

		$item = null;
		$skill = null;

		$bonification = [];

		$bonification['p_damage'] = 0;
		$bonification['m_damage'] = 0;

		$bonification['p_defense'] = 0;
		$bonification['m_defense'] = 0;

		$bonification['stat_life'] = 0;
		$bonification['stat_dexterity'] = 0;
		$bonification['stat_magic'] = 0;
		$bonification['stat_strength'] = 0;
		$bonification['stat_luck'] = 0;

		/*
		 *	Obtenemos todos los objetos
		 *	que no estén en inventario por supuesto
		 */
		$characterItems = $character->items()->where('location', '<>', 'inventory')->get();

		foreach ( $characterItems as $characterItem )
		{
			$item = $characterItem->item;

			if ( $positive )
			{
				$bonification['p_damage']		+= ( $item->p_damage > 0 )			? $item->p_damage : 0;
				$bonification['m_damage']		+= ( $item->m_damage > 0 )			? $item->m_damage : 0;

				$bonification['p_defense']		+= ( $item->p_defense > 0 )			? $item->p_defense : 0;
				$bonification['m_defense']		+= ( $item->m_defense > 0 )			? $item->m_defense : 0;

				$bonification['stat_life']		+= ( $item->stat_life > 0 )			? $item->stat_life : 0;
				$bonification['stat_dexterity']	+= ( $item->stat_dexterity > 0 )	? $item->stat_dexterity : 0;
				$bonification['stat_magic']		+= ( $item->stat_magic > 0 )		? $item->stat_magic : 0;
				$bonification['stat_strength']	+= ( $item->stat_strength > 0 )		? $item->stat_strength : 0;
				$bonification['stat_luck']		+= ( $item->stat_luck > 0 )			? $item->stat_luck : 0;
			}
			else
			{
				$bonification['p_damage']		+= ( $item->p_damage < 0 )			? -$item->p_damage : 0;
				$bonification['m_damage']		+= ( $item->m_damage < 0 )			? -$item->m_damage : 0;

				$bonification['p_defense']		+= ( $item->p_defense < 0 )			? -$item->p_defense : 0;
				$bonification['m_defense']		+= ( $item->m_defense < 0 )			? -$item->m_defense : 0;

				$bonification['stat_life']		+= ( $item->stat_life < 0 )			? -$item->stat_life : 0;
				$bonification['stat_dexterity']	+= ( $item->stat_dexterity < 0 )	? -$item->stat_dexterity : 0;
				$bonification['stat_magic']		+= ( $item->stat_magic < 0 )		? -$item->stat_magic : 0;
				$bonification['stat_strength']	+= ( $item->stat_strength < 0 )		? -$item->stat_strength : 0;
				$bonification['stat_luck']		+= ( $item->stat_luck < 0 )			? -$item->stat_luck : 0;
			}
		}

		/*
		 *	Ahora revisamos las bonificaciones
		 *	de los skills que están activos
		 */
		$characterSkills = $character->skills()->get();

		foreach ( $characterSkills as $characterSkill )
		{
			$skill = $characterSkill->skill->data;

			if ( $positive )
			{
				$bonification['p_damage']		+= ( isset($skill['p_damage']) && $skill['p_damage'] > 0 )				? $skill['p_damage'] * $characterSkill->amount : 0;
				$bonification['m_damage']		+= ( isset($skill['m_damage']) && $skill['m_damage'] > 0 )				? $skill['m_damage'] * $characterSkill->amount : 0;

				$bonification['p_defense']		+= ( isset($skill['p_defense']) && $skill['p_defense'] > 0 )			? $skill['p_defense'] * $characterSkill->amount : 0;
				$bonification['m_defense']		+= ( isset($skill['m_defense']) && $skill['m_defense'] > 0 )			? $skill['m_defense'] * $characterSkill->amount : 0;

				$bonification['stat_life']		+= ( isset($skill['stat_life']) && $skill['stat_life'] > 0 )			? $skill['stat_life'] * $characterSkill->amount : 0;
				$bonification['stat_dexterity']	+= ( isset($skill['stat_dexterity']) && $skill['stat_dexterity'] > 0 )	? $skill['stat_dexterity'] * $characterSkill->amount : 0;
				$bonification['stat_magic']		+= ( isset($skill['stat_magic']) && $skill['stat_magic'] > 0 )			? $skill['stat_magic'] * $characterSkill->amount : 0;
				$bonification['stat_strength']	+= ( isset($skill['stat_strength']) && $skill['stat_strength'] > 0 )	? $skill['stat_strength'] * $characterSkill->amount : 0;
				$bonification['stat_luck']		+= ( isset($skill['stat_luck']) && $skill['stat_luck'] > 0 )			? $skill['stat_luck'] * $characterSkill->amount : 0;
			}
			else
			{
				$bonification['p_damage']		+= ( isset($skill['p_damage']) && $skill['p_damage'] < 0 )				? -$skill['p_damage'] * $characterSkill->amount : 0;
				$bonification['m_damage']		+= ( isset($skill['m_damage']) && $skill['m_damage'] < 0 )				? -$skill['m_damage'] * $characterSkill->amount : 0;

				$bonification['p_defense']		+= ( isset($skill['p_defense']) && $skill['p_defense'] < 0 )			? -$skill['p_defense'] * $characterSkill->amount : 0;
				$bonification['m_defense']		+= ( isset($skill['m_defense']) && $skill['m_defense'] < 0 )			? -$skill['m_defense'] * $characterSkill->amount : 0;

				$bonification['stat_life']		+= ( isset($skill['stat_life']) && $skill['stat_life'] < 0 )			? -$skill['stat_life'] * $characterSkill->amount : 0;
				$bonification['stat_dexterity']	+= ( isset($skill['stat_dexterity']) && $skill['stat_dexterity'] < 0 )	? -$skill['stat_dexterity'] * $characterSkill->amount : 0;
				$bonification['stat_magic']		+= ( isset($skill['stat_magic']) && $skill['stat_magic'] < 0 )			? -$skill['stat_magic'] * $characterSkill->amount : 0;
				$bonification['stat_strength']	+= ( isset($skill['stat_strength']) && $skill['stat_strength'] < 0 )	? -$skill['stat_strength'] * $characterSkill->amount : 0;
				$bonification['stat_luck']		+= ( isset($skill['stat_luck']) && $skill['stat_luck'] < 0 )			? -$skill['stat_luck'] * $characterSkill->amount : 0;
			}
		}

		return $bonification;
	}

	/**
	 *	Obtenemos la cantidad de monedas
	 *	en cobre de un personaje
	 *
	 *	@return <CharacterItem> 
	 */
	public function get_coins()
	{
		return $this->items()->where('item_id', '=', Config::get('game.coin_id'))->first();
	}

	/**
	 *	Obtenemos las monedas dividas en
	 *	oro, plata y cobre de un personaje
	 *
	 *	@return <Array> Monedas dividas en oro, plata y cobre
	 */
	public function get_divided_coins()
	{
		$coins = $this->get_coins();

		if ( $coins )
		{
			$coins = $coins->count;
		}

		return [
			'gold' => substr($coins, 0, -4) ? substr($coins, 0, -4) : 0,
			'silver' => substr($coins, -4, -2) ? substr($coins, -4, -2) : 0,
			'copper' => substr($coins, -2) ? substr($coins, -2) : 0,
		];
	}

	/**
	 *	Iniciamos el viaje de un
	 *	personaje a una zona
	 *
	 *	@param <Zone> $zone
	 */
	public function travel_to(Zone $zone)
	{
		if ( $zone )
		{
			$characterActivity = new CharacterActivity();

			$characterActivity->character_id = $this->id;
			$characterActivity->name = 'travel';
			$characterActivity->data = [ 'zone' => $zone ];
			$characterActivity->end_time = time() + Config::get('game.travel_time');

			$characterActivity->save();
		}
	}

	/**
	 *	Verificamos si el personaje está habilitado
	 *	para viajar
	 *
	 *	@return <mixed> True si puede, de lo contrario el mensaje de error
	 */
	public function can_travel()
	{
		/*
		 *	Si ya está viajando...
		 */
		if ( $this->activities()->where('name', '=', 'travel')->first() )
		{
			return 'Ya estás viajando, no puedes volver a hacerlo.';
		}

		/*
		 *	¿Le alcanzan las monedas?
		 */
		$coins = $this->items()->where('item_id', '=', Config::get('game.coin_id'))->first();
		if ( $coins && $coins->count < Config::get('game.travel_cost') )
		{
			return 'No tienes suficientes monedas.';
		}

		return true;
	}

	public function get_unread_messages_count()
	{
		$count = $this->messages()->where('unread', '=', true)->count();
		return ( $count > 0 ) ? $count : '';
	}

	public function can_enter_in_clan()
	{
		return $this->clan_id == 0;
	}

	public function zone()
	{
		return $this->belongs_to('Zone', 'zone_id');
	}

	public function activities()
	{
		return $this->has_many('CharacterActivity', 'character_id');
	}

	public function items()
	{
		return $this->has_many('CharacterItem', 'owner_id');
	}

	public function skills()
	{
		return $this->has_many('CharacterSkill', 'character_id');
	}

	public function quests()
	{
		return $this->has_many('CharacterQuest', 'character_id');
	}

	public function triggers()
	{
		return $this->has_many('CharacterTrigger', 'character_id');
	}

	public function messages()
	{
		return $this->has_many('Message', 'receiver_id');
	}

	public function clan()
	{
		return $this->belongs_to('Clan', 'clan_id');
	}

	public function petitions()
	{
		return $this->has_many('ClanPetition', 'character_id');
	}

	public function trades()
	{
		return $this->has_many('Trade', 'seller_id');
	}
}