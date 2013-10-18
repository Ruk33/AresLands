<?php

interface Api_Interface
{
	public function get_character($name, $tooltip);
	public function get_item($id);
	public function get_itemTooltip($id);
	public function get_clan($id);
	public function get_membersOfClan($clanId);
	public function get_npc($name);
	public function get_monster($name);
	public function get_quest($id);
	public function get_orb($id);
	public function get_zone($id);
	public function get_skill($id, $level);
	public function get_mercenary($id);
}

class Api_Controller extends Base_Controller implements Api_Interface
{
	public $restful = true;

	public function get_character($name, $tooltip = false)
	{
		$selectableFields = array(
			'name',
			'level',
			'max_life',
			'gender',
			'pvp_points',
			'race',
			'clan_id',
			'stat_life',
			'stat_dexterity',
			'stat_magic',
			'stat_strength',
			'stat_luck',
		);

		$character = Character::where_name($name)->select($selectableFields)->first();

		/*
		 *	Si el personaje no existe devolvemos null
		 */
		if ( ! $character )
		{
			return Response::json(null);
		}

		/*
		 *	Nada de stats reales, solo aproximados BOAJAJA >:3
		 */
		$character->stat_life = mt_rand($character->stat_life, $character->stat_life * 1.3);
		$character->stat_dexterity = mt_rand($character->stat_dexterity, $character->stat_dexterity * 1.3);
		$character->stat_magic = mt_rand($character->stat_magic, $character->stat_magic * 1.3);
		$character->stat_strength = mt_rand($character->stat_strength, $character->stat_strength * 1.3);
		$character->stat_luck = mt_rand($character->stat_luck, $character->stat_luck * 1.3);
		
		if ( $tooltip )
		{
			return Response::json(array('tooltip' => $character->get_tooltip()));
		}
		else
		{
			return Response::json($character->attributes);
		}
	}

	public function get_item($id)
	{
		$item = Item::find((int) $id);

		if ( $item )
		{
			return json_encode($item->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_itemTooltip($id)
	{
		$item = Item::find((int) $id);

		if ( $item )
		{
			return $item->get_text_for_tooltip();
		}
		else
		{
			return 'Objeto no encontrado';
		}
	}

	public function get_clan($id)
	{
		$clan = Clan::find((int) $id);

		if ( $clan )
		{
			return json_encode($clan->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_membersOfClan($clanId)
	{
		$clan = Clan::where_id((int) $clanId)->take(1)->count();

		if ( $clan > 0 )
		{
			$members = Character::where_clan_id((int) $clanId)->select(array('id'))->get();

			if ( $members )
			{
				return json_encode(
					array_map(
						function($val)
						{
							return $val->attributes;
						},
						$members
					)
				);
			}
		}

		return json_encode(null);
	}

	public function get_npc($name)
	{
		$npc = Npc::where_name($name)->where_type('npc')->first();

		if ( $npc )
		{
			return json_encode($npc->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_monster($id)
	{
		$monster = Npc::where_id($id)->where_type('monster')->first();

		if ( $monster )
		{
			return json_encode($monster->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_quest($id)
	{
		$selectableFields = array(
			'id',
			'npc_id',
			'name',
			'description',
			'min_level',
			'max_level',
		);

		$quest = Quest::where_id((int) $id)->select($selectableFields)->first();

		if ( $quest )
		{
			return json_encode($quest->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_orb($id)
	{
		$orb = Orb::find((int) $id);

		if ( $orb )
		{
			return json_encode($orb->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_zone($id)
	{
		$zone = Zone::find((int) $id);

		if ( $zone )
		{
			return json_encode($zone->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_skill($id, $level)
	{
		$skill = Skill::get($id, $level);

		if ( $skill )
		{
			return json_encode($skill);
		}
		else
		{
			return json_encode(null);
		}
	}

	public function get_mercenary($id)
	{
		$mercenary = Item::where('type', '=', 'mercenary')->find((int) $id);

		if ( $mercenary )
		{
			return json_encode($mercenary->attributes);
		}
		else
		{
			return json_encode(null);
		}
	}
}