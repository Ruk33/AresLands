<?php

class Authenticated_Controller extends Base_Controller
{
	public $layout = 'layouts.default';
	public $restful = true;

	public function __construct()
	{
		parent::__construct();
        
		/*
		 *	Solo queremos logueados
		 */
		$this->filter('before', 'auth');

		/*
		 *	Si no tiene personaje lo
		 *	redireccionamos a la página
		 *	para que se pueda crear uno
		 */
		$this->filter('before', 'hasNoCharacter');
		
		// Prevenimos csrf
		$this->filter('before', 'csrf')
		->on(array('post'))
		->except(array(
			'addstat',
			'characters',
			'editclanmessage'
		));

		/*
		 *	Si, supuestamente usamos
		 *	un filtro para comprobar esto
		 *	('auth') pero si se da el caso
		 *	en que el usuario se deslogueo
		 *	y recarga la página tirará error
		 *	(puesto que la variable de session
		 *	no estará definida)
		 */
		$character = Character::get_character_of_logged_user(array(
			'id',
			'name',
			'level',
			'gender',
			'race',
			'clan_id',
			'is_exploring',
			'is_traveling'
		));

		if ( Auth::check() && $character )
		{
			/*
			 * Obtenemos las misiones del personaje
			 */
		   $startedQuests = $character->started_quests()->get();
           $startedQuests = array_merge($startedQuests, $character->reward_quests()->get());

            /*
			 *	Debemos pasar las monedas directamente
			 *	al layout, puesto que es ahí donde
			 *	las utilizamos
			 */
			$this->layout->with('coins', $character->get_divided_coins());
			$this->layout->with('character', $character);
			$this->layout->with('startedQuests', $startedQuests);
		}
	}
    
	public function get_index()
	{
		$character = Character::get_character_of_logged_user(array(
			'id',
			'name',
			'zone_id',
			'race',
			'gender',
			'stat_strength',
			'stat_strength_extra',
			'stat_dexterity',
			'stat_dexterity_extra',
			'stat_resistance',
			'stat_resistance_extra',
			'stat_magic',
			'stat_magic_extra',
			'stat_magic_skill',
			'stat_magic_skill_extra',
			'stat_magic_resistance',
			'stat_magic_resistance_extra',
			'points_to_change',
			'current_life',
			'max_life',
			'last_logged',
			'level',
			'xp',
			'xp_next_level'
		));

		/*
		 *	Verificamos logueada del día
		 */
		if ( $character->last_logged + 24 * 60 * 60 < time() )
		{
			$character->give_logged_of_day_reward();

			$character->last_logged = time();
			$character->save();
		}

		/*
		 *	Obtenemos los objetos del personaje
		 */
		$items = $character->items;
		$itemsToView = array();

		/*
		 *	Los ordenamos solo para que sea
		 *	más cómodo de trabajar en la vista
		 */
		foreach ( $items as $item )
		{
			$itemsToView[$item->location][] = $item;
		}

		/*
		 *	Obtenemos todos los skills (buffs)
		 *	del personaje
		 */
		$skills = $character->skills()->get();

		/*
		 *	Obtenemos todas las actividades
		 *	del personaje
		 */
		$activities = $character->activities()->select(array('end_time', 'name', 'data'))->get();

		/*
		 *	Obtenemos los orbes
		 */
		$orbs = $character->orbs()->select(array('id', 'name', 'description'))->get();

		$zone = $character->zone()->select(array('id', 'name', 'description'))->first();

		/*
		 *	Obtenemos todos los npcs
		 *	(no mounstros) de la zona
		 *	en la que está el usuario
		 */
		$npcs = Npc::get_npcs_from_zone($zone);

		$exploringTime = $character->exploring_times()->where('zone_id', '=', $zone->id)->first();
		$blockedNpcs = Npc::select('id')
		->where('zone_id', '=', $zone->id)
		->where('type', '=', 'npc')
		->where('time_to_appear', '>', ( isset($exploringTime->time) ) ? $exploringTime->time : 0)
		->order_by('time_to_appear', 'asc')
		->get();

		$this->layout->title = 'Inicio';
		$this->layout->content = View::make('authenticated.index')
		->with('character', $character)
		->with('activities', $activities)
		->with('items', $itemsToView)
		->with('skills', $skills)
		->with('orbs', $orbs)
		->with('zone', $zone)
		->with('npcs', $npcs)
		->with('exploringTime', $exploringTime)
		->with('blockedNpcs', $blockedNpcs);
	}
	
	public function get_claimOrb($orbId = 0)
	{
		if ( $orbId > 0 )
		{
			$orb = Orb::find((int) $orbId);
			
			if ( $orb )
			{
				$character = Character::get_character_of_logged_user(array('id'));
				
				if ( ! $orb->owner_id && $orb->can_be_stolen_by($character) )
				{
					$orb->give_to($character);
				}
			}
		}
		
		return Laravel\Redirect::to('authenticated/orbs/');
	}

	public function get_learnClanSkill($skillId = '', $level = 1)
	{
		if ( $skillId && $level > 0 )
		{
			$character = Character::get_character_of_logged_user(array('id', 'clan_id', 'clan_permission'));

			if ( $character->clan_id > 0 )
			{
				$clan = $character->clan()->select(array('id', 'leader_id', 'points_to_change', 'level'))->first();

				if ( $clan )
				{
					if ( $clan->leader_id == $character->id || $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL) )
					{
						$skill = Skill::where('id', '=', (int) $skillId)->where('level', '=', (int) $level)->first();
						
						if ( $skill )
						{
							if ( $clan->points_to_change > 0 && $skill->can_be_learned_by_clan($clan) )
							{
								$clan->learn_skill($skill);

								$clan->points_to_change--;
								$clan->save();

								return Redirect::to('authenticated/clan/' . $clan->id);
							}
						}
					}
				}
			}
		}

		return Redirect::to('authenticated/index/');
	}

	public function get_orbs()
	{
		$this->layout->title = 'Orbes';
		$this->layout->content = View::make('authenticated.orbs')
		->with('character', Character::get_character_of_logged_user(array('id', 'level')))
		->with('orbs', Orb::order_by('min_level', 'asc')->get());
	}

	public function get_destroyItem($characterItemId = false)
	{
		$character = Character::get_character_of_logged_user(array('id'));
		$characterItem = ( $characterItemId ) ? $character->items()->select(array('id'))->find($characterItemId) : false;

		if ( $characterItem )
		{
			$characterItem->delete();
		}

		return Redirect::to('authenticated/index/');
	}

	public function post_explore()
	{
		$character = Character::get_character_of_logged_user(array('id', 'is_traveling', 'is_exploring', 'level', 'xp', 'points_to_change', 'clan_id'));
		$time = Input::get('time');

		if ( ($time <= Config::get('game.max_explore_time') && $time >= Config::get('game.min_explore_time')) && $character->can_explore() )
		{
			/*
			 *	Damos dos puntos a la barra
			 *	de actividad si se explora
			 *	30 minutos o mas
			 */
			if ( $time >= 30 )
			{
				ActivityBar::add($character, 2);
			}

			$character->explore($time * 60);
		}

		return Redirect::to('authenticated/index/');
	}

	public function get_explore()
	{
		$this->layout->title = 'Explorar';
		$this->layout->content = View::make('authenticated.explore');
	}

	public function post_addStat()
	{
		$character = Character::get_character_of_logged_user(array(
			'id',
			'points_to_change',
			'stat_strength',
			'stat_dexterity',
			'stat_resistance',
			'stat_magic',
			'stat_magic_skill',
			'stat_magic_resistance',
		));

		$stat = Input::json()->stat_name;
		$amount = Input::json()->stat_amount;

		if ( $character->points_to_change >= $amount )
		{
			switch ( $stat ) 
			{
				case 'stat_strength':
					$character->stat_strength += $amount;
					break;

				case 'stat_dexterity':
					$character->stat_dexterity += $amount;
					break;
				
				case 'stat_resistance':
					$character->stat_resistance += $amount;
					break;

				case 'stat_magic':
					$character->stat_magic += $amount;
					break;

				case 'stat_magic_skill':
					$character->stat_magic_skill += $amount;
					break;

				case 'stat_magic_resistance':
					$character->stat_magic_resistance += $amount;
					break;

				default:
					return false;
			}

			$character->points_to_change -= $amount;
			$character->save();
			
			return true;
		}
		return false;
	}

	public function get_ranking($type = '')
	{
		//switch ( $type ) {
			//case 'xp':
				$characters_xp = Character::order_by('xp', 'desc')->select(array('id', 'name', 'gender', 'race', 'xp', 'level'))->get();
				//$characters_xp = DB::table('characters')->order_by('xp', 'desc')->select(array('id', 'name', 'gender', 'race', 'pvp_points'))->skip($from)->take(50)->get();
				//return json_encode($characters);

				//break;

			//case 'pvp_points':
				$characters_pvp = Character::order_by('pvp_points', 'desc')->select(array('id', 'name', 'gender', 'race', 'pvp_points'))->get();
				//$characters_pvp = DB::table('characters')->order_by('pvp_points', 'desc')->select(array('id', 'name', 'gender', 'race', 'pvp_points'))->skip($from)->take(50)->get();
				//return json_encode($characters);

				//break;

			//case 'clan':
				$clansPuntuation = ClanOrbPoint::order_by('points', 'desc')->get();
				//$clans = DB::table('clan_orb_points')->order_by('points', 'desc')->join('clans', 'clans.id', '=', 'clan_orb_points.clan_id')->get();
				//return json_encode($clans);

				//break;
		//}

		$this->layout->title = 'Ranking';
		$this->layout->content = View::make('authenticated.ranking')
		->with('characters_pvp', $characters_pvp)
		->with('characters_xp', $characters_xp)
		->with('clansPuntuation', $clansPuntuation);
	}

	public function get_acceptTrade($tradeId = false)
	{
		$character = Character::get_character_of_logged_user();
		$trade = ( $tradeId ) ? Trade::where('id', '=', $tradeId)->where('buyer_id', '=', $character->id)->where('status', '=', 'pending')->first() : false;

		if ( $trade )
		{
			$characterCoins = $character->get_coins();

			if ( $characterCoins && $characterCoins->count >= $trade->price_copper )
			{
				$characterItem = $character->items()->find($trade->item_id);

				if ( $characterItem && $characterItem->item->stackable )
				{
					$characterItem->count += $trade->amount;
					$characterItem->save();

					$characterCoins->count -= $trade->price_copper;
					$characterCoins->save();

					$characterSellerCoins = $trade->seller->get_coins();
					$characterSellerCoins->count += $trade->price_copper;
					$characterSellerCoins->save();

					$trade->delete();

					Session::flash('successMessages', array('El comercio fue un éxito'));
				}
				else
				{
					$slotInInventory = CharacterItem::get_empty_slot();

					if ( $slotInInventory )
					{
						$characterItem = new CharacterItem();

						$characterItem->owner_id = $character->id;
						$characterItem->item_id = $trade->item_id;
						$characterItem->count = $trade->amount;
						$characterItem->location = 'inventory';
						$characterItem->data = $trade->data;
						$characterItem->slot = $slotInInventory;

						$characterItem->save();

						$characterCoins->count -= $trade->price_copper;
						$characterCoins->save();

						$characterSellerCoins = $trade->seller->get_coins();
						$characterSellerCoins->count += $trade->price_copper;
						$characterSellerCoins->save();

						$trade->delete();

						Session::flash('successMessages', array('El comercio fue un éxito'));
					}
					else
					{
						Session::flash('errorMessages', array('No tienes espacio en el inventario'));
					}
				}
			}
			else
			{
				Session::flash('errorMessages', array('No tienes suficientes monedas para aceptar el comercio'));
			}
		}

		return Redirect::to('authenticated/trade/');
	}

	public function get_cancelTrade($tradeId = false)
	{
		$character = Character::get_character_of_logged_user();
		$trade = ( $tradeId ) ? Trade::where('id', '=', $tradeId)->where('seller_id', '=', $character->id)->or_where('buyer_id', '=', $character->id)->first() : false;

		if ( $trade )
		{
			if ( $trade->status == 'pending' )
			{
				// notificamos que se canceló

				Session::flash('successMessages', array('El comercio ha sido cancelado'));

				$trade->status = 'canceled';
				$trade->save();
			}

			if ( $trade->seller_id == $character->id )
			{
				$characterItem = $character->items()->find($trade->item_id);

				if ( $characterItem && $characterItem->item->stackable )
				{
					$characterItem->count += $trade->amount;
					$characterItem->save();

					$trade->delete();
				}
				else
				{
					$slotInInventory = CharacterItem::get_empty_slot();

					if ( $slotInInventory )
					{
						$characterItem = new CharacterItem();

						$characterItem->owner_id = $character->id;
						$characterItem->item_id = $trade->item_id;
						$characterItem->count = $trade->amount;
						$characterItem->location = 'inventory';
						$characterItem->data = $trade->data;
						$characterItem->slot = $slotInInventory;

						$characterItem->save();

						$trade->delete();
					}
					else
					{
						Session::flash('errorMessages', array('Debes tener espacio en el inventario'));
					}
				}
			}
		}

		return Redirect::to('authenticated/trade/');
	}

	public function post_newTrade()
	{
		/*
		 *	Obtenemos al vendedor y "comprador"
		 */
		$sellerCharacter = Character::get_character_of_logged_user();
		$buyerCharacter = Character::where('name', '=', Input::get('name'))->first();

		/*
		 *	'amount' es array, esto es para
		 *	saber la cantidad del objeto que se seleccionó
		 */
		$amount = Input::get('amount');
		$amount = $amount[Input::get('item')];

		/*
		 *	Objeto que se va a comerciar
		 */
		$sellerCharacterItem = $sellerCharacter->items()->where('id', '=', Input::get('item'))->where('count', '>=', $amount)->where('location', '=', 'inventory')->first();

		/*
		 *	Evitamos que intenten comerciar
		 *	cantidad en un objeto que no puede ser acumulado
		 */
		if ( $sellerCharacterItem && $sellerCharacterItem->item->stackable )
		{
			$amount = 1;
		}

		/*
		 *	"Creamos" el nuevo comercio
		 */
		$trade = new Trade();

		$trade->seller_id = ( $sellerCharacter ) ? $sellerCharacter->id : -1;
		$trade->buyer_id = ( $buyerCharacter ) ? $buyerCharacter->id : -1;
		$trade->item_id = ( $sellerCharacterItem ) ? $sellerCharacterItem->item_id : -1;
		$trade->amount = $amount;
		$trade->data = ( $sellerCharacterItem ) ? $sellerCharacterItem->data : '';
		$trade->price_copper = Input::get('price');
		$trade->status = 'pending';

		/*
		 *	Validamos los datos...
		 */
		if ( $trade->validate() )
		{
			/*
			 *	Si pasó la validación,
			 *	guardamos
			 */
			$trade->save();

			/*
			 *	Sacamos el objeto al personaje
			 *	vendedor y/o únicamente restamos cantidad
			 */
			$sellerCharacterItem->count -= $amount;

			if ( $sellerCharacterItem->count > 0 )
			{
				$sellerCharacterItem->save();
			}
			else
			{
				/*
				 *	La cantidad es menor a 0
				 *	así que borramos
				 */
				$sellerCharacterItem->delete();
			}

			/*
			 *	Notificamos al "comprador"
			 *	que le ofertaron
			 */
			Message::trade_new($sellerCharacter, $buyerCharacter);

			Session::flash('successMessage', 'Oferta realizada con éxito');
			return Redirect::to('authenticated/newTrade/');
		}
		else
		{
			Session::flash('errorMessages', $trade->errors()->all());
			return Redirect::to('authenticated/newTrade/')->with_input();
		}
	}

	public function get_newTrade()
	{
		$character = Character::get_character_of_logged_user();

		if ( $character->can_trade() )
		{
			$characterItems = $character->items()->where('location', '=', 'inventory')->where('count', '>', 0)->get();

			$this->layout->title = 'Nuevo comercio';
			$this->layout->content = View::make('authenticated.newtrade')
			->with('characterItems', $characterItems);
		}
		else
		{
			return Redirect::to('authenticated/trade')
			->with('errorMessages', array('No tienes ningún objeto para comerciar.'));
		}
	}

	public function get_trade()
	{
		$character = Character::get_character_of_logged_user();
		$trades = Trade::where('seller_id', '=', $character->id)->or_where('buyer_id', '=', $character->id)->get();

		$this->layout->title = 'Comerciar';
		$this->layout->content = View::make('authenticated.trade')
		->with('character', $character)
		->with('trades', $trades);
	}

	public function post_characters()
	{
		$result = DB::table('characters')
		->left_join('clans', 'characters.clan_id', '=', 'clans.id')
		->get(array(
			'characters.name', 
			'characters.pvp_points', 
			'characters.clan_id', 
			'characters.race', 
			'characters.gender', 
			'clans.name as clan_name'
		));

		return json_encode($result);
	}

	public function get_characters()
	{
		$this->layout->title = 'Jugadores';
		$this->layout->content = View::make('authenticated.characters');
		//->with('characters', Character::order_by('pvp_points', 'desc')->get());
	}

	public function post_editClanMessage()
	{
		$character = Character::get_character_of_logged_user(array('id', 'clan_id', 'clan_permission'));

		if ( $character )
		{
			$clan = $character->clan;
			
			if ( $clan && $character->id == $clan->leader_id || $character->has_permission(Clan::PERMISSION_EDIT_MESSAGE) )
			{
				$clan->message = Input::json()->message;
				$clan->save();
			}
		}
	}

	public function post_createClan()
	{
		$character = Character::get_character_of_logged_user(array(
			'id',
			'clan_id',
		));

		$clan = new Clan();

		$clan->leader_id = $character->id;
		$clan->name = Input::get('name');
		$clan->message = Input::get('message');

		if ( $clan->validate() )
		{
			/*
			 *	Borramos todas las peticiones
			 *	pendientes del personaje
			 */
			$petitions = $character->petitions()->select(array('id'))->get();

			foreach ( $petitions as $petition )
			{
				$petition->delete();
			}

			/*
			 *	Creamos el clan
			 */
			$clan->save();

			/*
			 *	Agregamos al personaje
			 */
			$character->clan_id = $clan->id;
			$character->save();

			return Redirect::to('authenticated/clan/' . $clan->id);
		}
		else
		{
			Session::flash('errorMessages', $clan->errors()->all());
			return Redirect::to('authenticated/createClan');
		}
	}

	public function get_createClan()
	{
		$character = Character::get_character_of_logged_user(array('id', 'clan_id'));

		if ( $character->clan_id != 0 )
		{
			return Redirect::to('authenticated/clan/');
		}

		$this->layout->title = 'Crear grupo';
		$this->layout->content = View::make('authenticated.createclan');
	}

	public function get_leaveFromClan()
	{
		$character = Character::get_character_of_logged_user(array('id', 'clan_id'));
		
		$character->leave_clan();		

		return Redirect::to('authenticated/clan/');
	}

	public function get_deleteClan()
	{
		$character = Character::get_character_of_logged_user(array('id', 'clan_id'));
		$clan = $character->clan()->select(array('id', 'leader_id'))->first();

		/*
		 *	Verificamos que el clan exista y
		 *	que el personaje sea lider
		 */
		if ( $clan && $clan->leader_id == $character->id )
		{
			/*
			 *	Solamente podemos borrar el clan
			 *	si no hay ningún miembro
			 */
			if ( $clan->members()->count() == 1 )
			{
				$character->clan_id = 0;
				$character->save();

				$clan->delete();
			}
		}

		return Redirect::to('authenticated/index/');
	}

	public function get_clanRemoveMember($memberName = '')
	{
		$character = Character::get_character_of_logged_user(array('id', 'name', 'clan_id', 'clan_permission'));
		$clan = $character->clan()->select(array('id', 'leader_id'))->first();

		/*
		 *	Verificamos que el clan exista
		 */
		if ( $clan )
		{
			/*
			 *	Verificamos que el personaje
			 *	es el lider o tenga permisos
			 */
			if ( $character->id == $clan->leader_id || $character->has_permission(Clan::PERMISSION_KICK_MEMBER) )
			{
				/*
				 *	No se puede sacar a él mismo, 
				 *	así que verificamos
				 */
				if ( $character->name != $memberName )
				{
					/*
					 *	Obtenemos el miembro por su nombre
					 *	y el id de clan
					 */
					$member = ( $memberName ) ? Character::where('name', '=', $memberName)->where('clan_id', '=', $clan->id)->select(array('id', 'clan_id'))->first() : false;

					/*
					 *	Verificamos que el miembro exista
					 */
					if ( $member )
					{
						/*
						 *	Finalmente, lo sacamos del clan
						 */
						$clan->leave($member);
						$member->clan_id = 0;
						$member->save();

						/*
						 *	Le notificamos al miembro expulsado
						 *	mediante un mensaje privado
						 */
						Message::clan_expulsion_message($character, $member);

						Session::flash('successMessage', 'El miembro ' . $member->name . ' fue expulsado del grupo');
						return Redirect::to('authenticated/clan/' . $clan->id);
					}
				}
			}
		}

		return Redirect::to('authenticated/index/');
	}

	public function get_clanRejectPetition($petitionId = false)
	{
		$petition = ( $petitionId ) ? ClanPetition::find($petitionId) : false;

		if ( $petition )
		{
			$character = Character::get_character_of_logged_user(array('id', 'clan_id', 'clan_permission'));
			$clan = $petition->clan;

			if ( $clan )
			{
				if ( $character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_DECLINE_PETITION) )
				{
					Message::clan_reject_message($character, $petition->character()->select(array('id'))->first(), $clan);
					$petition->delete();

					/*
					 *	Notificamos que todo fue bien
					 *	y que el usuario fue agregado con éxito
					 */
					Session::flash('successMessage', 'La petición ha sido rechazada');
					return Redirect::to('authenticated/clan/' . $clan->id);
				}
			}
		}

		return Redirect::to('authenticated/index/');
	}

	public function get_clanAcceptPetition($petitionId = false)
	{
		$petition = ( $petitionId ) ? ClanPetition::find($petitionId) : false;

		if ( $petition )
		{
			$character = Character::get_character_of_logged_user(array('id', 'clan_id', 'clan_permission'));
			$clan = $petition->clan;
			
			/*
			 *	Verificamos que el clan exista
			 */
			if ( $clan )
			{
				/*
				 *	Verificamos que el usuario tenga los permisos
				 *	para realizar esta accion
				 */
				if ( $character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_ACCEPT_PETITION) )
				{
					/*
					 *	Obtenemos la información del personaje
					 *	que vamos a aceptar
					 */
					$characterToAccept = $petition->character()->select(array('id', 'clan_id', 'name'))->first();

					if ( $characterToAccept->clan_id == 0 )
					{
						/*
						 *	Todo bien, así que lo aceptamos
						 *	en el clan y borramos todas sus peticiones
						 */
						$petitions = $characterToAccept->petitions()->select(array('id'))->get();

						foreach ($petitions as $petition) {
							$petition->delete();
						}

						/*
						 *	Le notificamos por mensaje privado
						 */
						Message::clan_accept_message($character, $characterToAccept, $clan);

						/*
						 *	¡Y lo agregamos!
						 */
						$characterToAccept->clan_id = $clan->id;
						$characterToAccept->save();

						$clan->join($characterToAccept);

						/*
						 *	Notificamos que todo fue bien
						 *	y que el usuario fue agregado con éxito
						 */
						Session::flash('successMessage', 'El personaje ' . $characterToAccept->name . ' ha sido aceptado exitosamente');
						return Redirect::to('authenticated/clan/' . $clan->id);
					}
					else
					{
						/*
						 *	Ya tiene un clan
						 */
						Session::flash('errorMessage', 'No puedes aceptar a ' . $characterToAccept->name . ' porque ya pertenece a un grupo');
						return Redirect::to('authenticated/clan/' . $clan->id);
					}
				}
			}
			else
			{
				/*
				 *	El clan ya no existe mas, 
				 *	así que borramos la petición
				 */
				$petition->delete();
			}
		}

		return Redirect::to('authenticated/index/');
	}

	public function get_clanJoinRequest($clanId = false)
	{
		/*
		 *	Buscamos el clan al que queremos
		 *	enviar la solicitud
		 */
		$clan = ( $clanId ) ? Clan::find($clanId) : false;

		if ( $clan )
		{
			$character = Character::get_character_of_logged_user(array('id', 'name'));

			if ( $character->can_enter_in_clan() )
			{
				$petition = $character->petitions()->where('clan_id', '=', $clan->id)->first();

				if ( $petition )
				{
					/*
					 *	Ya tiene una petición pendiente
					 */
					Session::flash('errorMessage', 'Ya tienes una petición pendiente con este grupo, debes esperar a que sea respondida');
					return Redirect::to('authenticated/clan/' . $clan->id);
				}
				else
				{
					/*
					 *	Si el personaje puede ingresar
					 *	en un clan, entonces enviamos la petición
					 */
					$petition = new ClanPetition();

					$petition->clan_id = $clan->id;
					$petition->character_id = $character->id;

					$petition->save();

					/*
					 *	Notificamos al lider de clan
					 *	que tiene una nueva petición
					 */
					Message::clan_new_petition($character, $clan->lider);

					Session::flash('successMessage', 'Haz enviado exitosamente la petición para la inclusión en este grupo');
					return Redirect::to('authenticated/clan/' . $clan->id);
				}
			}
			else
			{
				/*
				 *	Error, no puede ingresar
				 *	a este clan
				 */
				Session::flash('errorMessage', 'No puedes ingresar a este grupo');
				return Redirect::to('authenticated/clan/' . $clan->id);
			}
		}

		/*
		 *	El clan no existe, redireccionamos
		 *	a la lista de clanes
		 */
		return Redirect::to('authenticated/clan/');
	}
	
	public function post_clanModifyMemberPermissions()
	{		
		$character = Character::get_character_of_logged_user(array('id', 'clan_id'));
		$clan = $character->clan;
		
		if ( $character->id == $clan->leader_id )
		{
			$input = Input::all();
			$member = Character::select(array('id', 'clan_id', 'clan_permission'))->find((int) $input['id']);
			
			if ( $member )
			{
				if ( $member->clan_id == $clan->id )
				{
					$member->set_permission(Clan::PERMISSION_ACCEPT_PETITION, isset($input['can_accept_petition']), false);
					$member->set_permission(Clan::PERMISSION_DECLINE_PETITION, isset($input['can_decline_petition']), false);
					$member->set_permission(Clan::PERMISSION_KICK_MEMBER, isset($input['can_kick_member']), false);
					$member->set_permission(Clan::PERMISSION_LEARN_SPELL, isset($input['can_learn_spell']), false);
					$member->set_permission(Clan::PERMISSION_EDIT_MESSAGE, isset($input['can_edit_message']), false);

					$member->save();
				}
			}
		}
		
		return Redirect::to('authenticated/clan/' . $clan->id);
	}

	public function get_clan($clanId = false)
	{
		$clan = ( $clanId ) ? Clan::find((int) $clanId) : false;
		$character = Character::get_character_of_logged_user(array('id', 'clan_id', 'clan_permission'));

		if ( $clan )
		{
			$dataToView = array();

			$dataToView['clan'] = $clan;
			$dataToView['members'] = $clan->members()->select(array('id' ,'name', 'race', 'gender', 'level', 'clan_id', 'clan_permission'))->get();
			$dataToView['character'] = $character;
			$dataToView['clanSkills'] = $clan->skills;

			$dataToView['skills'] = Skill::clan_skills()->
					where('level', '=', 1)->
					where('id', 'NOT IN', DB::raw("( SELECT skill_id FROM clan_skills WHERE clan_id = $clan->id )"))->
					get();

			if ( $character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_ACCEPT_PETITION) || $clan->has_permission($character, Clan::PERMISSION_DECLINE_PETITION) )
			{
				$dataToView['petitions'] = $clan->petitions()->get();
			}

			$this->layout->title = $clan->name;
			$this->layout->content = View::make('authenticated.viewclan', $dataToView);
		}
		else
		{
			$this->layout->title = 'Grupos';
			$this->layout->content = View::make('authenticated.viewclans')
			->with('clans', Clan::all())
			->with('character', $character);
		}
	}

	public function post_sendMessage()
	{
		$to = Input::get('to');
		$toCharacter = Character::where('name', '=', $to)->select(array('id'))->first();
		$errorMessages = array();

		/*
		 *	Verificamos que
		 *	el destinatario exista
		 */
		if ( $toCharacter )
		{
			/*
			 *	Preparamos para crear un
			 *	nuevo mensaje
			 */
			$message = new Message();

			$message->sender_id = Character::get_character_of_logged_user(array('id'))->id;
			$message->receiver_id = $toCharacter->id;
			$message->subject = Input::get('subject');
			$message->content = Input::get('content');
			$message->unread = true;
			$message->date = time();

			/*
			 *	Validamos
			 */
			if ( $message->validate() )
			{
				/*
				 *	Si todo está bien, guardamos
				 */
				$message->save();
			}
			else
			{
				/*
				 *	De lo contrario, notificamos
				 *	los errores
				 */
				$errorMessages = $message->errors()->all();
			}
		}
		else
		{
			$errorMessages[] = 'El destinatario no existe';
		}

		if ( count($errorMessages) > 0 )
		{
			Session::flash('errorMessages', $errorMessages);
			return Redirect::to('authenticated/sendMessage')->with_input();
		}
		else
		{
			$this->layout->title = '¡Mensaje enviado exitosamente!';
			$this->layout->content = View::make('authenticated.messagesent');
		}
	}

	public function get_sendMessage($to = '')
	{
		$this->layout->title = 'Enviar mensaje';
		$this->layout->content = View::make('authenticated.sendmessage')
		->with('to', $to);
	}

	public function get_clearAllMessages($type = '')
	{
		switch ( $type )
		{
			case 'received':
			case 'attack':
			case 'defense':
				$character = Character::get_character_of_logged_user(array('id'));
				$character->messages()->where('type', '=', $type)->delete();
				break;
		}

		return Redirect::to('authenticated/messages/');
	}

	public function post_deleteMessage()
	{
		/*
		 *	Obtenemos el mensaje
		 */
		$character = Character::get_character_of_logged_user(array('id'));
		$selectedMessages = Input::get('messages');

		if ( is_array($selectedMessages) )
		{
			$character->messages()->where_in('id', $selectedMessages)->delete();
		}

		return Redirect::to('authenticated/messages/');
	}

	public function get_readMessage($messageId = 0)
	{
		/*
		 *	Obtenemos el mensaje que vamos a leer
		 */
		$character = Character::get_character_of_logged_user(array('id'));
		$message = ( $messageId > 0 ) ? $character->messages()->find($messageId) : false;

		/*
		 *	Verificamos que exista
		 */
		if ( $message )
		{
			/*
			 *	Ya vamos a leer el mensaje
			 *	así que lo marcamos como leído
			 */
			$message->unread = false;
			$message->save();

			$this->layout->title = $message->subject;
			$this->layout->content = View::make('authenticated.readmessage')
			->with('message', $message);
		}
		else
		{
			/*
			 *	Si no existe, redireccionamos
			 *	a la bandeja de entrada
			 */
			return Redirect::to('authenticated/messages/');
		}
	}

	public function get_messages($messageId = 0)
	{
		$character = Character::get_character_of_logged_user(array('id'));
		
		/*
		 *	Obtenemos todos los mensajes del personaje
		 */
		$messages = array();
		$messages['received'] = $character->messages()->select(array('id', 'sender_id', 'subject', 'unread', 'date'))->where('type', '=', 'received')->order_by('date', 'desc')->get();
		//$messages['sent'] = Message::select(array('id', 'sender_id', 'subject', 'unread', 'date'))->where('sender_id', '=', $character->id)->where('type', '=', 'received')->order_by('date', 'desc')->get();
		$messages['attack'] = $character->messages()->select(array('id', 'sender_id', 'subject', 'unread', 'date'))->where('type', '=', 'attack')->order_by('date', 'desc')->get();
		$messages['defense'] = $character->messages()->select(array('id', 'sender_id', 'subject', 'unread', 'date'))->where('type', '=', 'defense')->order_by('date', 'desc')->get();

		$this->layout->title = 'Mensajes';
		$this->layout->content = View::make('authenticated.messages')
		->with('messages', $messages);
	}

	public function get_character($characterName = '')
	{
		$characterToSee = ( $characterName ) ? Character::where('name', '=', $characterName)->select(array(
			'id', 
			'name', 
			'level', 
			'clan_id', 
			'race', 
			'gender', 
			'zone_id',
			'stat_strength',
			'stat_dexterity',
			'stat_resistance',
			'stat_magic',
			'stat_magic_skill',
			'stat_magic_resistance',
		))->first() : false;

		if ( $characterToSee )
		{
            $characterActivities = $characterToSee->activities()->where('end_time', '<=', time())->get();

			foreach ( $characterActivities as $characterActivity )
			{
				$characterActivity->update_time();
			}
            
			/*
			 *	Obtenemos los objetos del personaje
			 */
			$items = $characterToSee->items()->select(array('id', 'item_id', 'location', 'data'))->where_not_in('location', array('inventory', 'none'))->get();
			$itemsToView = array();

			/*
			 *	Los ordenamos solo para que sea
			 *	más cómodo de trabajar en la vista
			 */
			foreach ( $items as $item )
			{
				$itemsToView[$item->location][] = $item;
			}

			/*
			 *	Obtenemos los orbes
			 */
			$orbs = $characterToSee->orbs()->select(array('id', 'name', 'description'))->get();
			
			$character = Character::get_character_of_logged_user(array('id', 'name', 'zone_id', 'clan_id'));
			$skills = array();
			
			if ( $character->is_admin() )
			{
				$skills = $characterToSee->skills;
			}

			$this->layout->title = $characterToSee->name;
			$this->layout->content = View::make('authenticated.character')
			->with('character', $character)
			->with('items', $itemsToView)
			->with('orbs', $orbs)
			->with('skills', $skills)
			->with('characterToSee', $characterToSee);
		}
		else
		{
			$this->layout->title = 'Desconocido';
			$this->layout->content = View::make('authenticated.character');
		}
	}

	public function get_toBattleMonster()
	{
		if ( Session::has('character_id') && Session::has('monster_id') && Session::has('winner_id') && Session::has('log_id') )
		{
			$character = Character::find((int) Session::get('character_id'));
			$monster = Npc::find((int) Session::get('monster_id'));
			$winner = ( $character->id == Session::get('winner_id') ) ? $character : $monster;
			$log = BattleLog::find((int) Session::get('log_id'));
			
			$message = $log->message;
			
			$log->delete();
			
			$this->layout->title = '¡Ganador ' . $winner->name . '!';
			$this->layout->content = View::make('authenticated.finishedbattlemonster')
			->with('character', $character)
			->with('monster', $monster)
			->with('winner', $winner)
			->with('message', $message);
		}
		else
		{
			return Redirect::to('authenticated/index/');
		}
	}

	public function post_toBattleMonster()
	{
		$monsterId = Input::get('monster_id');
		
		$character = Character::get_character_of_logged_user();
		$monster = ( $monsterId ) ? Npc::where('type', '=', 'monster')->where('zone_id', '=', $character->zone_id)->find((int) $monsterId) : false;
		
		if ( $monster )
		{
			if ( $character->can_fight() )
			{
				$battle = $character->battle_against($monster);
			}
			else
			{
				Session::flash('errorMessage', 'Aún no puedes pelear');
				return Redirect::to('authenticated/battle/');
			}
			
			$winner = $battle->get_winner();
			
			return Redirect::to('authenticated/toBattleMonster')
			->with('character_id', $character->id)
			->with('monster_id', $monster->id)
			->with('winner_id', $winner->id)
			->with('log_id', $battle->log->id);
		}
		else
		{
			return Redirect::to('authenticated/battle/');
		}
	}
	
	public function get_toBattle()
	{
		if ( Session::has('winner_id') && Session::has('loser_id') && Session::has('log_id') )
		{
			$fieldsToSelect = array('id', 'name', 'race', 'gender');
			
			$winner = Character::select($fieldsToSelect)->find((int) Session::get('winner_id'));
			$loser = Character::select($fieldsToSelect)->find((int) Session::get('loser_id'));
			$log = BattleLog::find((int) Session::get('log_id'));
			
			$message = $log->message;
			
			// Ya usamos el log, lo borramos
			$log->delete();
			
			$this->layout->title = '¡Ganador ' . $winner->name . '!';
			$this->layout->content = View::make('authenticated.finishedbattle')
			->with('winner', $winner)
			->with('loser', $loser)
			->with('message', $message);
		}
		else
		{
			return Redirect::to('authenticated/index/');
		}
	}

	public function post_toBattle()
	{
		$characterName = Input::get('name');

		if ( $characterName )
		{			
			// Verificamos que el personaje exista
			if ( Character::where_name($characterName)->take(1)->count() == 0 )
			{
				Session::flash('errorMessage', 'Ese personaje no existe.');
				return Redirect::to('authenticated/battle');
			}
			
			$character = Character::get_character_of_logged_user();
			
			$target = Character::where('name', '=', $characterName)->where('zone_id', '=', $character->zone_id)->first();

			/*
			 *	Verificamos que el personaje este en la zona
			 */
			if ( ! $target )
			{
				Session::flash('errorMessage', $characterName . ' está en otra zona.');
				return Redirect::to('authenticated/battle');
			}
			
			if ( $target->is_traveling )
			{
				Session::flash('errorMessage', $target->name . ' acaba de irse de esta zona.');
				return Redirect::to('authenticated/battle');
			}

			/*
			 *	Verificamos que el personaje
			 *	pueda ser atacado
			 */
			if ( ! $target->can_be_attacked($character) )
			{
				Session::flash('errorMessage', $target->name . ' aún no puede ser atacado/a todavía.');
				return Redirect::to('authenticated/battle');
			}
		}
		else
		{
			/*
			 *	No hay siquiera nombre...
			 */
			return Redirect::to('authenticated/battle');
		}

		if ( $character->can_fight() )
		{
			$battle = $character->battle_against($target);
		}
		else
		{
			Session::flash('errorMessage', 'Aún no puedes pelear');
			return Redirect::to('authenticated/battle');
		}
		
		$winner = $battle->get_winner();
		$loser = $battle->get_loser();
		
		return Redirect::to('authenticated/toBattle')
		->with('winner_id', $winner->id)
		->with('loser_id', $loser->id)
		->with('log_id', $battle->log->id);
	}

	public function post_battle()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'name'));
		$searchMethod = Input::get('search_method');

		$valuesToTake = array(
			'id', 
			'name', 
			'level', 
			'clan_id', 
			'race', 
			'gender', 
			'zone_id',
			'stat_strength',
			'stat_dexterity',
			'stat_resistance',
			'stat_magic',
			'stat_magic_skill',
			'stat_magic_resistance',
		);

		$characterFinded = null;

		switch ( $searchMethod ) 
		{
			case 'name':
				$characterFinded = Character::where('name', '=', Input::get('character_name'))->where('name', '<>', $character->name)->where('is_traveling', '=', false)->where('zone_id', '=', $character->zone_id)->select($valuesToTake)->first();
				break;

			case 'random':
				$race = array();

				switch ( Input::get('race') )
				{
					case 'dwarf':
					case 'human':
					case 'drow':
					case 'elf':
						$race[] = Input::get('race');
						break;

					default:
						$race[] = 'dwarf';
						$race[] = 'human';
						$race[] = 'drow';
						$race[] = 'elf';
						break;
				}

				switch ( Input::get('operation') )
				{
					case 'greaterThan':
						$operation = '>';
						break;

					case 'lowerThan':
						$operation = '<';
						break;

					default:
						$operation = '=';
						break;
				}

				$level = (int) Input::get('level');

				if ( ! $level || $level <= 0 )
				{
					$level = 1;
				}

				$characterFinded = Character::where_in('race', $race)->where('level', $operation, $level)->where('name', '<>', $character->name)->where('is_traveling', '=', false)->where('zone_id', '=', $character->zone_id)->select($valuesToTake)->order_by(DB::raw('RAND()'))->first();
				break;

			case 'group':
				$characterFinded = Character::where('clan_id', '=', Input::get('clan'))->where('name', '<>', $character->name)->where('is_traveling', '=', false)->where('zone_id', '=', $character->zone_id)->select($valuesToTake)->order_by(DB::raw('RAND()'))->first();
				break;
		}

		/*
		 *	Verificamos si encontramos personaje
		 */
		if ( $characterFinded )
		{
			/*
			 *	Obtenemos los objetos del personaje
			 */
			$items = $characterFinded->items()->select(array('item_id', 'location', 'data'))->where_not_in('location', array('inventory', 'none'))->get();
			$itemsToView = array();

			/*
			 *	Los ordenamos solo para que sea
			 *	más cómodo de trabajar en la vista
			 */
			foreach ( $items as $item )
			{
				$itemsToView[$item->location][] = $item;
			}

			/*
			 *	Obtenemos los orbes
			 */
			$orbs = $characterFinded->orbs()->select(array('id', 'name', 'description'))->get();

			$this->layout->title = $characterFinded->name;
			$this->layout->content = View::make('authenticated.character')
			->with('character', Character::get_character_of_logged_user(array('id', 'zone_id', 'clan_id')))
			->with('items', $itemsToView)
			->with('orbs', $orbs)
			->with('characterToSee', $characterFinded);
		}
		else
		{
			/*
			 *	No encontramos :\
			 */
			Session::flash('errorMessage', 'No se encontró ningún personaje para batallar');
			return Redirect::to('authenticated/battle');
		}
	}

	public function get_battle()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level'));

		$monsters = Npc::where('zone_id', '=', $character->zone_id)
		->where('type', '=', 'monster')
		->order_by('level', 'asc')
		->get();

		$this->layout->title = '¡Batallar!';
		$this->layout->content = View::make('authenticated.battle')
		->with('monsters', $monsters)
		->with('character', $character);
	}

	public function get_rewardFromQuest($questId = false)
	{
		$quest = ( $questId ) ? Quest::select(array('id', 'repeatable', 'repeatable_after'))->find((int) $questId) : false;

		if ( $quest )
		{
			$character = Character::get_character_of_logged_user(array('id'));

			/*
			 *	Obtenemos el progreso de la quest
			 *	del personaje
			 */
			$characterQuest = $character->quests()->where('quest_id', '=', $quest->id)->where('progress', '=', 'reward')->first();

			/*
			 *	Si existe...
			 */
			if ( $characterQuest )
			{
				/*
				 *	Recompensamos
				 */
				$characterQuest->quest->give_reward();

				/*
				 *	Y no nos olvidamos de guardar
				 *	el progreso a finalizado
				 */
				$characterQuest->finished_time = time();

				if ( $quest->repeatable )
				{
					$characterQuest->repeatable_at = time() + $quest->repeatable_after;
				}
				$characterQuest->progress = 'finished';

				$characterQuest->save();
			}
		}

		return Redirect::to('authenticated/index');
	}

	public function get_acceptQuest($questId = false)
	{
		$quest = ( $questId ) ? Quest::find((int) $questId) : false;

		if ( $quest )
		{
			$quest->accept(Character::get_character_of_logged_user());
		}

		return Redirect::to('authenticated/index');
	}

	public function get_travel($zoneId = '')
	{
		$character = Character::get_character_of_logged_user(array('id', 'is_traveling', 'zone_id', 'name', 'level', 'xp', 'clan_id'));

		/*
		 *	Si zoneId está definido quiere
		 *	decir que el personaje ya eligió
		 *	la zona a la que quiere viajar
		 */
		$zone = ( is_numeric($zoneId) ) ? Zone::where('min_level', '<=', $character->level)->find((int) $zoneId) : false;

		$error = false;

		if ( $zone )
		{
			// Evitamos que viajen a zonas bloqueadas
			if ( $zone->type != 'city' )
			{
				return Redirect::to('authenticated/travel/');
			}
			
			/*
			 *	Antes de hacer nada, nos fijamos
			 *	si el personaje realmente puede viajar
			 */
			$canTravel = $character->can_travel();

			if ( $canTravel === true )
			{
				if ( $zone->id != $character->zone_id )
				{
					/*
					 *	Cobramos el costo del viaje
					 */
					$characterCoins = $character->get_coins();
					$characterCoins->count -= Config::get('game.travel_cost');
					$characterCoins->save();

					/*
					 *	¡Iniciamos el viaje!
					 */
					$character->travel_to($zone);

					return Redirect::to('authenticated/index');
				}
				else
				{
					$error = 'Ya te encuentras en esa zona';
				}
			}
			else
			{
				/*
				 *	El personaje no puede viajar 
				 *	así que lo notificamos
				 */
				$error = $canTravel;
			}
		}

		$zones = Zone::where('type', '=', 'city')->select(array('id', 'name', 'description', 'min_level'))->get();

		$exploringTime = $character->exploring_times()->lists('time', 'zone_id');

		$this->layout->title = 'Viajar';
		$this->layout->content = View::make('authenticated.travel')
		->with('character', $character)
		->with('zones', $zones)
		->with('exploringTime', $exploringTime)
		->with('error', $error);
	}

	public function get_npc($npcId, $npcName = '')
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level', 'race', 'gender'));

		/*
		 *	Traemos al npc que tenga el nombre
		 *	y que esté ubicado en la zona
		 *	en donde está el personaje
		 */
		$npc = Npc::select(array('id', 'name', 'dialog', 'time_to_appear', 'zone_id'))->where('id', '=', (int) $npcId)->where('zone_id', '=', $character->zone_id)->first();

		/*
		 *	Si no existe, redireccionamos
		 */
		if ( ! $npc )
		{
			return Redirect::to('authenticated/index');
		}

		if ( $npc->is_blocked_to($character) )
		{
			return Redirect::to('authenticated/index');
		}

		/*
		 *	Disparamos el evento de hablar
		 */
		Event::fire('npcTalk', array($character, $npc));

		/*
		 *	Obtenemos todas las misiones del npc
		 *	que el personaje pueda realmente realizar
		 */
		$quests = $npc->available_quests_of($character)->get();

		/*
		 *	Obtenemos las misiones que el personaje
		 *	ha finalizado y sean repetibles
		 */
		$repeatableQuests = $npc->repeatable_quests_of($character)->get();

		/*
		 *	En este array vamos a guardar
		 *	todas las misiones que están iniciadas
		 */
		$startedQuests = $npc->started_quests_of($character)->get();

		/*
		 *	En este array vamos a guardar
		 *	las misiones que están hechas
		 *	pero aún necesitan pedir la recompensa
		 */
		$rewardQuests = $npc->reward_quests_of($character)->get();

		/*
		 *	Monedas del personaje
		 */
		$characterCoins = $character->get_coins();

		/*
		 *	Obtenemos las mercancías del npc
		 */
		$merchandises = $npc->merchandises()
		->left_join('items', 'items.id', '=', 'npc_merchandises.item_id')
		->get(array(
			'npc_merchandises.id', 
			'npc_merchandises.item_id', 
			'npc_merchandises.price_copper', 

			'items.stackable',
			'items.type',
			'items.zone_to_explore',
			'items.time_to_appear'
		));

		$this->layout->title = $npc->name;
		$this->layout->content = View::make('authenticated.npc')
		->with('npc', $npc)
		->with('characterCoinsCount', ( $characterCoins ) ? $characterCoins->count : 0)
		->with('merchandises', $merchandises)
		->with('rewardQuests', $rewardQuests)
		->with('startedQuests', $startedQuests)
		->with('repeatableQuests', $repeatableQuests)
		->with('quests', $quests)
		->with('character', $character);
	}

	public function post_buyMerchandise()
	{
		$merchandiseId = Input::get('merchandise_id', false);
		$amount = Input::get('amount', 1);

		$merchandise = ( $merchandiseId ) ? NpcMerchandise::find((int) $merchandiseId) : false;

		if ( $merchandise )
		{
			$character = Character::get_character_of_logged_user(array('id'));
			$npc = $merchandise->npc()->select(array('id', 'zone_id', 'time_to_appear'))->first();

			// Verificamos si el vendedor está desbloqueado
			if ( ! $npc || $npc->is_blocked_to($character) )
			{
				// En caso de no estarlo, redireccionamos impidiendo
				// así la compra
				return Redirect::to('authenticated/index/');
			}

			/*
			 *	Obtenemos la información del objeto
			 *	a comprar
			 */
			$item = $merchandise->item;

			/*
			 *	Si el objeto no es acumulable
			 *	y se quiere comprar mas de uno,
			 *	lo evitamos
			 */
			if ( ! $item->stackable )
			{
				$amount = 1;
			}

			/*
			 *	Obtenemos las monedas del personaje
			 */
			$characterCoins = $character->get_coins();

			/*
			 *	Verificamos que el personaje tenga
			 *	la cantidad necesaria de monedas para 
			 *	realizar la compra
			 */
			if ( $characterCoins && $merchandise->price_copper * $amount <= $characterCoins->count )
			{
				$characterItem = null;

				if ( $item->type == 'mercenary' )
				{
					if ( $item->level > $character->level )
					{
						return Redirect::to('authenticated/index/')->with('error', 'No tienes suficiente nivel para ese mercenario.');
					}
					
					// Si no se cumplen los requerimientos del mercenario...
					if ( $item->zone_to_explore && $item->time_to_appear && $character->exploring_times()->where('zone_id', '=', $item->zone_to_explore)->where('time', '>=', $item->time_to_appear)->take(1)->count() == 0 )
					{
						return Redirect::to('authenticated/index/');
					}

					// Buscamos su mercenario actual (en caso de tener)
					// para reemplazarlo con este nuevo
					$characterItem = $character->items()
					->left_join('items', 'items.id', '=', 'character_items.item_id')
					->where('items.type', '=', 'mercenary')
					->first(array('character_items.*'));

					if ( ! $characterItem )
					{
						$characterItem = new CharacterItem();

						$characterItem->owner_id = $character->id;
						$characterItem->location = 'mercenary';
					}
					else
					{
						$character->update_extra_stat($characterItem->item->to_array(), false);
					}

					$characterItem->item_id = $item->id;
					$characterItem->count = 0;
					
					$character->update_extra_stat($item->to_array(), true);
				}
				else
				{
					/*
					 *	Verificamos si el objeto
					 *	a comprar se puede acumular
					 */
					if ( $item->stackable )
					{
						/*
						 *	Se puede acumular, busquemos entonces
						 *	si el personaje ya tiene un objeto igual
						 */
						$characterItem = $character->items()->select(array('id', 'count'))->where('item_id', '=', $item->id)->first();
					}

					/*
					 *	O no se puede acumular, o bien
					 *	el personaje no tiene un objeto igual
					 */
					if ( ! $characterItem )
					{
						/*
						 *	Buscamos un slot en el inventario
						 */
						$slotInInventory = CharacterItem::get_empty_slot();

						/*
						 *	Verificamos que exista
						 */
						if ( $slotInInventory )
						{
							$characterItem = new CharacterItem();

							$characterItem->owner_id = $character->id;
							$characterItem->item_id = $item->id;
							$characterItem->location = 'inventory';
							$characterItem->slot = $slotInInventory;
						}
						else
						{
							/*
							 *	No hay espacio en el inventario
							 */
							return Redirect::to('authenticated/index');
						}
					}
				}

				/*
				 *	Si llegamos a este punto,
				 *	todo está bien
				 */

				/*
				 *	Otorgamos el objeto (y su cantidad)
				 *	al personaje
				 */
				$characterItem->count += $amount;
				$characterItem->save();

				/*
				 *	Restamos las monedas al personaje
				 */
				$characterCoins->count -= $merchandise->price_copper * $amount;
				$characterCoins->save();
			}
		}

		return Redirect::back();
	}
	
	/**
	 * Se usa POST para cuando la cantidad es mayor a 1
	 * 
	 * @return Redirect
	 */
	public function post_manipulateItem()
	{
		$inputs = Input::get();
		$error = null;
		
		if ( isset($inputs['id']) && isset($inputs['amount']) )
		{
			$character = Character::get_character_of_logged_user(array('id'));
			$characterItem = $character->items()->find((int) $inputs['id']);
			$amount = (int) $inputs['amount'];
			
			$error = $character->use_consumable_of_inventory($characterItem, $amount);
		}
		
		if ( $error )
		{
			return Redirect::to('authenticated/index/')->with('error', $error);
		}
		else
		{
			return Redirect::to('authenticated/index/');
		}
	}
	
	/**
	 * Se usa GET para cuando la cantidad es 1
	 * 
	 * @param type $id Id del objeto del personaje (no del item)
	 * @param type $count deprecated
	 * @return Redirect
	 */
	public function get_manipulateItem($id = 0, $count = 1)
	{
		if ( $id > 0 && $count > 0 )
		{
			$character = Character::get_character_of_logged_user(array('id', 'current_life', 'max_life', 'level'));

			if ( $character )
			{
				$characterItem = $character->items()->find((int) $id);

				if ( $characterItem )
				{
					/*
					 *	Verificamos si el objeto no lo
					 *	tiene en alguno de estos lugares
					 */
					if ( in_array($characterItem->location, array('chest', 'legs', 'feet', 'head', 'hands', 'lhand', 'rhand', 'lrhand')) )
					{
						/*
						 *	Si lo tiene, entonces intentamos guardar en inventario
						 */
						if ( $character->unequip_item($characterItem) )
						{
							Event::fire('unequipItem', array($characterItem));
						}
						else
						{
							/*
							 *	No tiene espacio, lo notificamos
							 */
							Session::flash('error', 'No tenes espacio en el inventario');
						}
					}
					else
					{
						/*
						 *	Verificamos que tenga la cantidad
						 */
						if ( $characterItem->count >= $count )
						{
							$item = $characterItem->item()->first();

							switch ( $item->body_part )
							{
								case 'lhand':
								case 'rhand':
								case 'lrhand':
									$error = $character->equip_item($characterItem);
									
									if ( $error )
									{
										Session::flash('error', $error);
									}
									else
									{
										Event::fire('equipItem', array($characterItem));
									}

									break;
							}
						}
						else
						{
							Session::flash('error', 'No posees esa cantidad');
						}
					}
				}
			}
		}

		return Redirect::to('authenticated/index');
	}

	public function get_logout()
	{
		Auth::logout();
		return Redirect::to('home/index/');
	}
}
