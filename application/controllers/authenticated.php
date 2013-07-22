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
			'xp',
			'xp_next_level',
			'clan_id',
			'is_exploring',
			'is_traveling'
		));

		if ( Auth::check() && $character )
		{
			/*
			 *	Debemos pasar las monedas directamente
			 *	al layout, puesto que es ahí donde
			 *	las utilizamos
			 */
			$this->layout->with('coins', $character->get_divided_coins());
			$this->layout->with('character', $character);
		}
	}

	/*
	public function get_setSkillData()
	{
		$skill = new Skill();

		$skill->name = 'Heal';
		$skill->level = 1;
		$skill->duration = 0;
		$skill->data = [
			'heal_amount' => 15,
		];

		$skill->save();
	}
	*/

	/*
	public function get_setQuestData()
	{
		$quest = new Quest();

		$quest->id = 26;
		$quest->npc_id = 162;
		$quest->class_name = 'Quest_AsegurarZona';
		$quest->name = 'Asegurar zona';
		$quest->description = '<p>Estamos en busqueda de combatientes que nos ayuden a despejar el camino hacia Valle de Cenisa. Desconozco cuál sea la razón, pero el valle ya no es el mismo, no con todos esos espectros merodeando y acechando. ¿Te unes?, hay buena paga.</p><p>Derrota a 15 monstruos de Valle de Cenisa.</p>';
		$quest->min_level = 1;
		$quest->max_level = 5;

		$quest->add_triggers(array(
			'acceptQuest',
			'pveBattleWin'
		));

		$quest->add_rewards(array(
			array(
				'item_id' => 27,
				'amount' => 1,
				'text_for_view' => '<img src="/img/icons/items/27.png" style="vertical-align: text-top;">'
			),
			array(
				'item_id' => Config::get('game.coin_id'),
				'amount' => 7500,
				'text_for_view' => '<img src="/img/copper.gif">'
			)
		));

		$quest->save();
	}
	*/

	public function get_index()
	{
		$character = Character::get_character_of_logged_user(array(
			'id',
			'name',
			'zone_id',
			'race',
			'gender',
			'stat_life',
			'stat_dexterity',
			'stat_magic',
			'stat_strength',
			'stat_luck',
			'points_to_change',
			'current_life',
			'max_life',
		));

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
		 *	Obtenemos las bonificaciones
		 *	tanto negativas como positivas
		 */
		$positiveBonifications = $character->get_bonifications(true);
		$negativeBonifications = $character->get_bonifications(false);

		/*
		 *	Obtenemos todos los npcs
		 *	(no mounstros) de la zona
		 *	en la que está el usuario
		 */
		$npcs = Npc::get_npcs_from_zone($character->zone);

		$this->layout->title = 'Inicio';
		$this->layout->content = View::make('authenticated.index')
		->with('character', $character)
		->with('activities', $activities)
		->with('items', $itemsToView)
		->with('skills', $skills)
		->with('positiveBonifications', $positiveBonifications)
		->with('negativeBonifications', $negativeBonifications)
		->with('npcs', $npcs);
	}

	public function post_getItemTextForTooltip()
	{
		sleep(1);

		$item = Item::find(Input::get('item_id'));
		$text = 'El objeto no existe';

		if ( $item )
		{
			$text = $item->get_text_for_tooltip();
		}

		return json_encode($text);
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
		$character = Character::get_character_of_logged_user(array('id', 'is_traveling', 'is_exploring', 'level'));
		$time = Input::get('time');

		if ( ($time <= Config::get('game.max_explore_time') && $time >= Config::get('game.min_explore_time')) && $character->can_explore() )
		{
			$character->explore($time);
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
			'stat_life',
			'stat_dexterity',
			'stat_magic',
			'stat_strength',
			'stat_luck',
		));

		$stat = Input::json()->stat_name;
		$amount = Input::json()->stat_amount;

		if ( $character->points_to_change >= $amount )
		{
			switch ( $stat ) 
			{
				case 'stat_life':
					$character->stat_life += $amount;
					break;

				case 'stat_dexterity':
					$character->stat_dexterity += $amount;
					break;

				case 'stat_magic':
					$character->stat_magic += $amount;
					break;

				case 'stat_strength':
					$character->stat_strength += $amount;
					break;

				case 'stat_luck':
					$character->stat_luck += $amount;
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

	public function get_ranking($from = 0)
	{
		//$characters = Character::order_by('pvp_points', 'desc')->select(['name', 'pvp_points', 'gender', 'race'])->take(50)->get();

		$characters = Character::order_by('pvp_points', 'desc')->select(array('id', 'name', 'gender', 'race', 'pvp_points'))->paginate(50, array('name', 'pvp_points', 'gender', 'race'));

		$this->layout->title = 'Ranking';
		$this->layout->content = View::make('authenticated.ranking')
		->with('characters', $characters);
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
		$characterItems = $character->items()->where('location', '=', 'inventory')->where('count', '>', 0)->get();

		$this->layout->title = 'Nuevo comercio';
		$this->layout->content = View::make('authenticated.newtrade')
		->with('characterItems', $characterItems);
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
		$character = Character::get_character_of_logged_user(array('id', 'clan_id'));

		if ( $character )
		{
			$clan = $character->clan;
			
			if ( $clan && $character->id == $clan->leader_id )
			{
				$clan->message = Input::json()->message;
				$clan->save();
			}
		}

		die();
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
		$clan = $character->clan()->select(array('leader_id'))->first();
		
		if ( $clan )
		{
			/*
			 *	El lider de clan no puede salir
			 *	del mismo
			 */
			if ( $character->id != $clan->leader_id )
			{
				$character->clan_id = 0;
				$character->save();
			}
		}
		else
		{
			/*
			 *	El clan no existe
			 */
			$character->clan_id = 0;
			$character->save();
		}

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
			if ( count($clan->get_members()) == 1 )
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
		$character = Character::get_character_of_logged_user(array('id', 'name', 'clan_id'));
		$clan = $character->clan()->select(array('id', 'leader_id'))->first();

		/*
		 *	Verificamos que el clan exista
		 */
		if ( $clan )
		{
			/*
			 *	Verificamos que el personaje
			 *	es el lider
			 */
			if ( $character->id == $clan->leader_id )
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
			$character = Character::get_character_of_logged_user(array('id'));
			$clan = $petition->clan;

			if ( $clan )
			{
				if ( $character->id == $clan->leader_id )
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
			$character = Character::get_character_of_logged_user(array('id'));
			$clan = $petition->clan;
			
			/*
			 *	Verificamos que el clan exista
			 */
			if ( $clan )
			{
				/*
				 *	Esta operación solamente
				 *	la pueden realizar los líderes de clan
				 */
				if ( $character->id == $clan->leader_id )
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

	public function get_clan($clanId = false)
	{
		$clan = ( $clanId ) ? Clan::find($clanId) : false;
		$character = Character::get_character_of_logged_user(array('id', 'clan_id'));

		if ( $clan )
		{
			$dataToView = array();

			$dataToView['clan'] = $clan;
			$dataToView['members'] = $clan->get_members();
			$dataToView['character'] = $character;

			if ( $character->id == $clan->leader_id )
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

	public function get_clearAllMessages()
	{
		$character = Character::get_character_of_logged_user();
		$character->messages()->delete();

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
		$messages = $character->messages()->select(array('id', 'sender_id', 'subject', 'unread', 'date'))->order_by('date', 'desc')->get();

		$this->layout->title = 'Mensajes privados';
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
			'stat_life',
			'stat_dexterity',
			'stat_magic',
			'stat_strength',
			'stat_luck',
		))->first() : false;

		if ( $characterToSee )
		{
			/*
			 *	Obtenemos los objetos del personaje
			 */
			$items = $characterToSee->items()->select(array('item_id', 'location', 'data'))->where_not_in('location', array('inventory', 'none'))->get();
			$itemsToView = array();

			/*
			 *	Los ordenamos solo para que sea
			 *	más cómodo de trabajar en la vista
			 */
			foreach ( $items as $item )
			{
				$itemsToView[$item->location][] = $item;
			}

			$this->layout->title = $characterToSee->name;
			$this->layout->content = View::make('authenticated.character')
			->with('character', Character::get_character_of_logged_user(array('id', 'zone_id')))
			->with('items', $itemsToView)
			->with('characterToSee', $characterToSee);
		}
		else
		{
			$this->layout->title = 'Desconocido';
			$this->layout->content = View::make('authenticated.character');
		}
	}

	public function get_toBattle($characterName = false)
	{
		if ( $characterName )
		{
			$target = Character::where('name', '=', $characterName)->where('is_traveling', '=', false)->first();

			/*
			 *	Verificamos que el personaje
			 *	exista
			 */
			if ( ! $target )
			{
				return Redirect::to('authenticated/battle');
			}

			/*
			 *	Verificamos que el personaje
			 *	pueda ser atacado
			 */
			if ( ! $target->can_be_attacked() )
			{
				Session::flash('errorMessage', $target->name . ' aún no puede ser atacado');
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

		$character = Character::get_character_of_logged_user();

		if ( $character->can_fight() )
		{
			$battle = $character->battle_against($target);
		}
		else
		{
			Session::flash('errorMessage', 'Aún no puedes pelear');
			return Redirect::to('authenticated/battle');
		}

		$this->layout->title = '¡Ganador ' . $battle['winner']->name . '!';
		$this->layout->content = View::make('authenticated.finishedbattle')
		->with('character_one', $character)
		->with('character_two', $target)
		->with('winner', $battle['winner'])
		->with('message', $battle['message']);
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
			'stat_life',
			'stat_dexterity',
			'stat_magic',
			'stat_strength',
			'stat_luck',
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

			$this->layout->title = $characterFinded->name;
			$this->layout->content = View::make('authenticated.character')
			->with('character', Character::get_character_of_logged_user(array('id', 'zone_id')))
			->with('items', $itemsToView)
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

	public function get_battle($characterToBattle = '')
	{
		/*
		$character = Character::get_character_of_logged_user();
		$characterToBattle = ( $characterToBattle ) ? Character::where('name', '=', $characterToBattle)->first() : false;

		if ( $characterToBattle && $character->id != $characterToBattle->id )
		{

		}
		else
		{
		*/
			$this->layout->title = '¡Batallar!';
			$this->layout->content = View::make('authenticated.battle')
			->with('character', Character::get_character_of_logged_user(array('level')));
		//}
	}

	public function get_rewardFromQuest($questId = false)
	{
		$quest = ( $questId ) ? Quest::select(array('id'))->find((int) $questId) : false;

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
				$characterQuest->quest()->select(array('data'))->first()->give_reward();

				/*
				 *	Y no nos olvidamos de guardar
				 *	el progreso a finalizado
				 */
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
			$character = Character::get_character_of_logged_user();

			/*
			 *	Nos fijamos si el personaje
			 *	ya tiene algún progreso con esta quest
			 */
			$characterQuest = $character->quests()->select(array('id'))->where('quest_id', '=', $quest->id)->first();

			/*
			 *	Si no lo tiene, entonces lo creamos
			 *	y aceptamos la quest exitosamente
			 */
			if ( ! $characterQuest )
			{
				$quest->accept();

				/*
				 *	Disparamos el evento de aceptar
				 *	misiones
				 */
				Event::fire('acceptQuest', array($character, $quest));
			}
		}

		return Redirect::to('authenticated/index');
	}

	public function get_travel($zoneId = '')
	{
		$character = Character::get_character_of_logged_user(array('id', 'is_traveling', 'zone_id', 'name'));

		/*
		 *	Si zoneId está definido quiere
		 *	decir que el personaje ya eligió
		 *	la zona a la que quiere viajar
		 */
		$zone = ( $zoneId ) ? Zone::find((int) $zoneId) : false;

		$error = false;

		if ( $zone )
		{
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

		$cities = Zone::select(array('id', 'name', 'description'))->where('type', '=', 'city')->get();

		foreach ($cities as $city)
		{
			$city->villages = Zone::select(array('id', 'name', 'description'))->where('type', '=', 'village')->where('belongs_to', '=', $city->id)->get();
			$city->farm_zones = Zone::select(array('id', 'name', 'description'))->where('type', '=', 'farmzone')->where('belongs_to', '=', $city->id)->get();
		}

		$dungeons = Zone::select(array('id', 'name', 'description'))->where('type', '=', 'dungeon')->get();

		$this->layout->title = 'Viajar';
		$this->layout->content = View::make('authenticated.travel')
		->with('character', $character)
		->with('cities', $cities)
		->with('dungeons', $dungeons)
		->with('error', $error);
	}

	public function get_npc($npcName = '')
	{
		/*
		 *	Si no hay nombre, redireccionamos
		 *	al index
		 */
		if ( ! $npcName )
		{
			return Redirect::to('authenticated/index');
		}

		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level'));

		/*
		 *	Traemos al npc que tenga el nombre
		 *	y que esté ubicado en la zona
		 *	en donde está el personaje
		 */
		$npc = Npc::select(array('id', 'name', 'dialog'))->where('name', '=', $npcName)->where('zone_id', '=', $character->zone_id)->first();

		/*
		 *	Si no existe, redireccionamos
		 */
		if ( ! $npc )
		{
			return Redirect::to('authenticated/index');
		}

		/*
		 *	Obtenemos todas las misiones del npc
		 *	que estén acorde con el nivel del personaje
		 */
		$quests = $npc->quests()->select(array('id', 'name', 'description', 'data'))->where('min_level', '<=', $character->level)->where('max_level', '>=', $character->level)->get();
		
		/*
		 *	En este array vamos a guardar
		 *	todas las misiones que están iniciadas
		 */
		$startedQuests = array();

		/*
		 *	En este array vamos a guardar
		 *	las misiones que están hechas
		 *	pero aún necesitan pedir la recompensa
		 */
		$rewardQuests = array();

		$characterQuest = null;

		/*
		 *	Ahora las filtramos para que no
		 *	aparezcan aquellas misiones que el
		 *	jugador ya haya aceptado o finalizado
		 */
		for ( $i = 0, $max = count($quests); $i < $max; $i++ )
		{
			/*
			 *	Vemos de obtener el progreso
			 *	de la misión
			 */
			$characterQuest = $character->quests()->where('quest_id', '=', $quests[$i]->id)->first();

			/*
			 *	Verificamos si el resultado
			 *	de la query existe y si el mismo
			 *	aparece como finalizado (finished)
			 */
			if ( $characterQuest )
			{
				switch ( $characterQuest->progress )
				{
					case 'started':
						$startedQuests[] = array('quest' => $quests[$i], 'characterQuest' => $characterQuest);
						unset($quests[$i]);
						break;

					case 'reward':
						$rewardQuests[] = $quests[$i];
						unset($quests[$i]);
						break;

					case 'finished':
						unset($quests[$i]);
						break;
				}
			}
		}

		$characterCoins = $character->get_coins();

		/*
		 *	Obtenemos las mercancías del npc
		 */
		$merchandises = $npc->merchandises()->get();

		/*
		 *	Disparamos el evento de hablar
		 */
		Event::fire('npcTalk', array($character, $npc));

		$this->layout->title = $npc->name;
		$this->layout->content = View::make('authenticated.npc')
		->with('npc', $npc)
		->with('characterCoinsCount', ( $characterCoins ) ? $characterCoins->count : 0)
		->with('merchandises', $merchandises)
		->with('rewardQuests', $rewardQuests)
		->with('startedQuests', $startedQuests)
		->with('quests', $quests);
	}

	public function post_buyMerchandise()
	{
		$merchandiseId = Input::get('merchandise_id', false);
		$amount = Input::get('amount', 1);

		$merchandise = ( $merchandiseId ) ? NpcMerchandise::find((int) $merchandiseId) : false;

		if ( $merchandise )
		{
			/*
			 *	Obtenemos la información del objeto
			 *	a comprar
			 */
			$item = $merchandise->item()->select(array('id', 'stackable'))->first();

			/*
			 *	Si el objeto no es acumulable
			 *	y se quiere comprar mas de uno,
			 *	lo evitamos
			 */
			if ( ! $item->stackable && $amount > 1 )
			{
				$amount = 1;
			}

			$character = Character::get_character_of_logged_user(array('id'));

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
					$characterItem = $character->items()->select(array('count'))->where('item_id', '=', $item->id)->first();
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

		return Redirect::to('authenticated/index');
	}

	public function get_manipulateItem($id = 0, $count = 1)
	{
		if ( $id > 0 && $count > 0 )
		{
			$character = Character::get_character_of_logged_user(array('id'));

			if ( $character )
			{
				$characterItem = $character->items()->find($id);

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
							Session::flash('error', 'No tienes espacio en el inventario');
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
									if ( $character->equip_item($characterItem) )
									{
										Event::fire('equipItem', array($characterItem));
									}

									break;

								case 'none':
									/*
									 *	Que sea none no significa que sea
									 *	poción, así que nos aseguramos
									 */
									if ( $item->type == 'potion' )
									{
										/*
										 *	Restamos la cantidad que vamos a usar
										 */
										$characterItem->count -= $count;

										/*
										 *	Si se quedó con cero o menos simplemente
										 *	borramos el registro
										 */
										if ( $characterItem->count <= 0 )
										{
											$characterItem->delete();
										}
										else
										{
											$characterItem->save();
										}
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
