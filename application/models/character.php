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

	public function items()
	{
		return $this->has_many('CharacterItem', 'owner_id');
	}

	public function skills()
	{
		return $this->has_many('CharacterSkill', 'character_id');
	}
}