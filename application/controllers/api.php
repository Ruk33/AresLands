<?php

interface Api_Interface
{
	public function get_statPrice($stat);
	public function get_dividedCoin($amount);
	public function get_characterOfLoggedUser();
	public function get_character($name, $tooltip);
	public function get_item($id, $price, $tooltip);
	public function get_clan($id);
	public function get_membersOfClan($clanId);
	public function get_npc($name);
	public function get_monster($name);
	public function get_quest($id);
	public function get_orb($id);
	public function get_zone($id);
	public function get_skill($id, $level, $tooltip);
	public function get_mercenary($id);
}

class Api_Controller extends Base_Controller implements Api_Interface
{
	public $restful = true;

	public function get_statPrice($stat)
	{
		$character = Character::get_character_of_logged_user(array(
			'id', 
            'race',
			'level', 
			'stat_strength',
			'stat_dexterity',
			'stat_resistance',
			'stat_magic',
			'stat_magic_skill',
			'stat_magic_resistance'
		));

		if ( $character )
		{
			return Response::json(array('price' => $character->get_stat_price($stat)));
		}
	}
	
	public function get_dividedCoin($amount)
	{
		return Response::json(Item::get_divided_coins($amount));
	}

	public function get_characterOfLoggedUser()
	{
		$character = Character::get_character_of_logged_user();

		if ( ! $character )
		{
			return Response::json(null);
		}

		return Response::json($character->attributes);
	}

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
			'stat_strength',
			'stat_dexterity',
			'stat_resistance',
			'stat_magic',
			'stat_magic_skill',
			'stat_magic_resistance',
		);

		$character = Character::where_name($name)->select($selectableFields)->first();

		/*
		 *	Si el personaje no existe devolvemos null
		 */
		if ( ! $character )
		{
			return Response::json(null);
		}

		if ( $character->has_characteristic(Characteristic::RESERVED) )
		{
			$character->stat_strength = mt_rand($character->stat_strength, $character->stat_strength * 1.3);
			$character->stat_dexterity = mt_rand($character->stat_dexterity, $character->stat_dexterity * 1.3);
			$character->stat_resistance = mt_rand($character->stat_resistance, $character->stat_resistance * 1.3);
			$character->stat_magic = mt_rand($character->stat_magic, $character->stat_magic * 1.3);
			$character->stat_magic_skill = mt_rand($character->stat_magic_skill, $character->stat_magic_skill * 1.3);
			$character->stat_magic_resistance = mt_rand($character->stat_magic_resistance, $character->stat_magic_resistance * 1.3);
		}
		
		if ( $tooltip )
		{
			return Response::json(array('tooltip' => $character->get_tooltip()));
		}
		else
		{
			return Response::json($character->attributes);
		}
	}

	public function get_item($id, $price = 0, $tooltip = false)
	{
		$item = Item::find((int) $id);
		$result = array();

		if ( $item )
		{
			if ( $tooltip )
			{
				$result['tooltip'] = $item->get_text_for_tooltip();
			}
			
			if ( $price > 0 )
			{
				$result['price'] = Item::get_divided_coins((int) $price);
				$result['price_string'] = "<ul class='inline' style='margin: 0;'>
					<li><i class='coin coin-gold pull-left'></i> " . $result['price']['gold'] . "</li>
					<li><i class='coin coin-silver pull-left'></i> " . $result['price']['silver'] . "</li>
					<li><i class='coin coin-copper pull-left'></i> " . $result['price']['copper'] . "</li>
				</ul>";
			}
			
			$result['item'] = $item;
			
			return Response::json($result);
		}
		else
		{
			return json_encode(null);
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

	public function get_skill($id, $level, $tooltip = false)
	{
		$skill = Skill::where('id', '=', (int) $id)->where('level', '=', (int) $level)->first();

		if ( $skill )
		{
			if ( $tooltip )
			{
				return json_encode(array('tooltip' => $skill->get_tooltip()));
			}
			else
			{
				return json_encode($skill->attributes);
			}
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