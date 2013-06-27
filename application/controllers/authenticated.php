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
		if ( Auth::check() && Session::get('character') )
		{
			/*
			 *	Debemos pasar las monedas directamente
			 *	al layout, puesto que es ahí donde
			 *	las utilizamos
			 */
			$this->layout->with('coins', Session::get('character')->get_divided_coins());
		}
	}

	/*
	public function get_setSkillData()
	{
		$skill = new Skill();

		$skill->name = 'Might';
		$skill->level = 1;
		$skill->duration = -1;
		$skill->data = [
			'stat_strength' => 15,
		];

		$skill->save();
	}
	*/

	/*
	public function get_setQuestData()
	{
		$quest = new Quest();

		$quest->name = 'just test';
		$quest->npc_id = 1;
		$quest->class_name = 'Quest_Starting';

		$quest->add_triggers([
			'equipItem'
		]);

		$quest->add_rewards([
			[
				'item_id' => 2,
				'amount' => 50,
				'text_for_view' => '<img src="/img/copper.gif" style="vertical-align: text-top;"> 50'
			],
		]);

		$quest->save();
	}
	*/

	public function get_index()
	{
		$character = Session::get('character');

		/*
		 *	Obtenemos los objetos del personaje
		 */
		$items = $character->items;
		$itemsToView = [];

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
		$activities = $character->activities()->get();

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

	public function get_cancelTrade($tradeId = false)
	{
		$character = Session::get('character');
		$trade = ( $tradeId ) ? Trade::where('id', '=', $tradeId)->where('seller_id', '=', $character->id)->or_where('buyer_id', '=', $character->id)->first() : false;

		if ( $trade )
		{
			if ( $trade->status == 'pending' )
			{
				// notificamos que se canceló
			}

			if ( $trade->seller_id == $character->id )
			{
				$characterItem = $character->items()->find($trade->item_id);

				$slotInInventory = $characterItem->get_empty_slot();

				if ( $slotInInventory )
				{
					
				}
				$trade->delete();
			}
		}

		return Redirect::to('authenticated/trade/');
	}

	public function post_newTrade()
	{
		/*
		 *	Obtenemos al vendedor y "comprador"
		 */
		$sellerCharacter = Session::get('character');
		$buyerCharacter = Character::where('name', '=', Input::get('name'))->first();

		/*
		 *	'amount' es array, esto es para
		 *	saber la cantidad del objeto que se seleccionó
		 */
		$amount = Input::get('amount')[Input::get('item')];

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
		$trade->item_id = ( $sellerCharacterItem ) ? $sellerCharacterItem->id : -1;
		$trade->amount = $amount;
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
			 *	Guardamos el objeto
			 *	que se va a comerciar
			 */
			$tradeItem = new TradeItem();

			$tradeItem->trade_id = $trade->id;
			$tradeItem->item_id = $sellerCharacterItem->item_id;
			$tradeItem->count = $amount;
			$tradeItem->data = $sellerCharacterItem->data;

			$tradeItem->save();

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

			Session::flash('successMessages', 'Oferta realizada con éxito');
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
		$character = Session::get('character');
		$characterItems = $character->items()->where('location', '=', 'inventory')->where('count', '>', 0)->get();

		$this->layout->title = 'Nuevo comercio';
		$this->layout->content = View::make('authenticated.newtrade')
		->with('characterItems', $characterItems);
	}

	public function get_trade()
	{
		$character = Session::get('character');
		$trades = Trade::where('seller_id', '=', $character->id)->or_where('buyer_id', '=', $character->id)->get();

		$this->layout->title = 'Comerciar';
		$this->layout->content = View::make('authenticated.trade')
		->with('character', $character)
		->with('trades', $trades);
	}

	public function post_characters()
	{
		$result = DB::table('characters')
		->join('clans', 'characters.clan_id', '=', 'clans.id')
		->get([
			'characters.name', 
			'characters.pvp_points', 
			'characters.clan_id', 
			'characters.race', 
			'characters.gender', 
			'clans.name as clan_name'
		]);

		return json_encode($result);
	}

	public function get_characters()
	{
		$this->layout->title = 'Jugadores';
		$this->layout->content = View::make('authenticated.characters');
		//->with('characters', Character::order_by('pvp_points', 'desc')->get());
	}

	public function post_createClan()
	{
		$character = Session::get('character');

		$clan = new Clan();

		$clan->leader_id = $character->id;
		$clan->name = Input::get('name');

		if ( $clan->validate() )
		{
			/*
			 *	Borramos todas las peticiones
			 *	pendientes del personaje
			 */
			$petitions = $character->petitions()->get();

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
		$character = Session::get('character');

		if ( $character->clan_id != 0 )
		{
			return Redirect::to('authenticated/clan/');
		}

		$this->layout->title = 'Crear grupo';
		$this->layout->content = View::make('authenticated.createclan');
	}

	public function get_leaveFromClan()
	{
		$character = Session::get('character');
		$clan = $character->clan;
		
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
		$character = Session::get('character');
		$clan = $character->clan;

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
		$character = Session::get('character');
		$clan = $character->clan;

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
					$member = ( $memberName ) ? Character::where('name', '=', $memberName)->where('clan_id', '=', $clan->id)->first() : false;

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
			$character = Session::get('character');
			$clan = $petition->clan;

			if ( $clan )
			{
				if ( $character->id == $clan->leader_id )
				{
					Message::clan_reject_message($character, $petition->character, $clan);
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
			$character = Session::get('character');
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
					$characterToAccept = $petition->character;

					if ( $characterToAccept->clan_id == 0 )
					{
						/*
						 *	Todo bien, así que lo aceptamos
						 *	en el clan y borramos todas sus peticiones
						 */
						$petitions = $characterToAccept->petitions()->get();

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
			$character = Session::get('character');

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
		$character = Session::get('character');

		if ( $clan )
		{

			$dataToView = [];

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
		$toCharacter = Character::where('name', '=', $to)->first();
		$errorMessages = [];

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

			$message->sender_id = Session::get('character')->id;
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

	public function get_deleteMessage($messageId = 0)
	{
		/*
		 *	Obtenemos el mensaje
		 */
		$character = Session::get('character');
		$message = ( $messageId > 0 ) ? $character->messages()->find($messageId) : false;

		/*
		 *	Verificamos que exista
		 */
		if ( $message )
		{
			/*
			 *	Si así es, borramos
			 */
			$message->delete();
		}

		return Redirect::to('authenticated/messages/');
	}

	public function get_readMessage($messageId = 0)
	{
		/*
		 *	Obtenemos el mensaje que vamos a leer
		 */
		$character = Session::get('character');
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
		$character = Session::get('character');
		
		/*
		 *	Obtenemos todos los mensajes del personaje
		 */
		$messages = $character->messages()->order_by('date', 'desc')->get();

		$this->layout->title = 'Mensajes privados';
		$this->layout->content = View::make('authenticated.messages')
		->with('messages', $messages);
	}

	public function get_character($characterName = '')
	{
		$characterToSee = ( $characterName ) ? Character::where('name', '=', $characterName)->first() : false;

		if ( $characterToSee )
		{
			/*
			 *	Obtenemos los objetos del personaje
			 */
			$items = $characterToSee->items;
			$itemsToView = [];

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
			->with('character', Session::get('character'))
			->with('items', $itemsToView)
			->with('characterToSee', $characterToSee);
		}
		else
		{
			$this->layout->title = 'Desconocido';
			$this->layout->content = View::make('authenticated.character');
		}
	}

	public function post_battle()
	{
		$character = Session::get('character');
		$searchMethod = Input::get('search_method');

		$characterFinded = null;

		switch ( $searchMethod ) 
		{
			case 'name':
				$characterFinded = Character::where('name', '=', Input::get('character_name'))->where('name', '<>', $character->name)->where('zone_id', '=', $character->zone_id)->first();
				break;

			case 'random':
				$race = [];

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

				if ( ! $level && $level <= 0 )
				{
					$level = 1;
				}

				$characterFinded = Character::where_in('race', $race)->where('level', $operation, $level)->where('name', '<>', $character->name)->where('zone_id', '=', $character->zone_id)->order_by(DB::raw('RAND()'))->first();
				break;

			case 'group':
				
				break;
		}

		/*
		 *	Verificamos si encontramos personaje
		 */
		if ( $characterFinded )
		{
			return Redirect::to('authenticated/character/' . $characterFinded->name);
		}
		else
		{
			/*
			 *	No encontramos :\
			 */
			return Redirect::to('authenticated/battle');
		}
	}

	public function get_battle($characterToBattle = '')
	{
		$character = Session::get('character');
		$characterToBattle = ( $characterToBattle ) ? Character::where('name', '=', $characterToBattle)->first() : false;

		if ( $characterToBattle && $character->id != $characterToBattle->id )
		{

		}
		else
		{
			$this->layout->title = '¡Batallar!';
			$this->layout->content = View::make('authenticated.battle');
		}
	}

	public function get_rewardFromQuest($questId = false)
	{
		$quest = ( $questId ) ? Quest::find((int) $questId) : false;

		if ( $quest )
		{
			$character = Session::get('character');

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
			$character = Session::get('character');

			/*
			 *	Nos fijamos si el personaje
			 *	ya tiene algún progreso con esta quest
			 */
			$characterQuest = $character->quests()->where('quest_id', '=', $quest->id)->first();

			/*
			 *	Si no lo tiene, entonces lo creamos
			 *	y aceptamos la quest exitosamente
			 */
			if ( ! $characterQuest )
			{
				$quest->accept();
			}
		}

		return Redirect::to('authenticated/index');
	}

	public function get_travel($zoneId = '')
	{
		$character = Session::get('character');

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
				/*
				 *	¡Iniciamos el viaje!
				 */
				$character->travel_to($zone);

				return Redirect::to('authenticated/index');
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

		/*
		 *	Solamente queremos las zonas
		 *	en donde el personaje no está
		 */
		$zones = Zone::where('id', '<>', $character->zone_id)->get();

		$this->layout->title = 'Viajar';
		$this->layout->content = View::make('authenticated.travel')
		->with('character', $character)
		->with('zones', $zones)
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

		$character = Session::get('character');

		/*
		 *	Traemos al npc que tenga el nombre
		 *	y que esté ubicado en la zona
		 *	en donde está el personaje
		 */
		$npc = Npc::where('name', '=', $npcName)->where('zone_id', '=', $character->zone_id)->first();

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
		$quests = $npc->quests()->where('min_level', '<=', $character->level)->where('max_level', '>=', $character->level)->get();
		
		/*
		 *	En este array vamos a guardar
		 *	las misiones que están hechas
		 *	pero aún necesitan pedir la recompensa
		 */
		$rewardQuests = [];

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
				if ( $characterQuest->progress == 'finished' )
				{
					/*
					 *	Si está marcado como finished
					 *	lo sacamos
					 */
					unset($quests[$i]);
				}
				else
				{
					$rewardQuests[$i] = $quests[$i];
				}
			}
		}

		/*
		 *	Obtenemos las mercancías del npc
		 */
		$merchandises = $npc->merchandises()->get();

		$this->layout->title = $npc->name;
		$this->layout->content = View::make('authenticated.npc')
		->with('npc', $npc)
		->with('characterCoinsCount', $character->get_coins()->count)
		->with('merchandises', $merchandises)
		->with('rewardQuests', $rewardQuests)
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
			$item = $merchandise->item;

			/*
			 *	Si el objeto no es acumulable
			 *	y se quiere comprar mas de uno,
			 *	lo evitamos
			 */
			if ( ! $item->stackable && $amount > 1 )
			{
				$amount = 1;
			}

			$character = Session::get('character');

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
					$characterItem = $character->items()->where('item_id', '=', $item->id)->first();
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
		/*
		 *	No queremos ejecutar acciones innecesariamente
		 */
		if ( $id > 0 && $count > 0 ) 
		{
			$character = Session::get('character');
			$characterItem = $character->items()->where('id', '=', $id)->first();

			/*
			 *	¿Existe el objeto?
			 */
			if ( $characterItem )
			{
				/*
				 *	Primero vamos a verificar
				 *	si lo tiene equipado, puesto
				 *	que si así es quiere decir
				 *	que quiere sacarse el objeto
				 */
				if ( $characterItem->location != 'inventory' )
				{
					$emptySlot = CharacterItem::get_empty_slot();

					/*
					 *	¿No hay espacio en el inventario?
					 */
					if ( ! $emptySlot )
					{
						/*
						 *	Redireccionamos notificándolo
						 */
						return Redirect::to('authenticated/index');
					}

					/*
					 *	Si hay espacio, entonces ponemos
					 *	el objeto en el inventario y guardamos
					 */
					$characterItem->location = 'inventory';
					$characterItem->slot = $emptySlot;

					$characterItem->save();

					/*
					 *	Disparamos el evento de desequipar objeto
					 */
					Event::fire('unequipItem', [$characterItem]);

					return Redirect::to('authenticated/index');
				}

				/*
				 *	¿Tiene la cantidad?
				 */
				if ( $characterItem->count >= $count )
				{
					$item = $characterItem->item;

					switch ( $item->body_part )
					{
						case 'lhand':
						case 'rhand':
							/*
							 *	Obtenemos el objeto que tiene equipado
							 *	que será reemplazado con $characterItem
							 */
							$equippedItem = $character->items()->where('location', '=', $item->body_part)->first();

							/*
							 *	Evitamos acciones innecesarias
							 *	verificando que el personaje de hecho
							 *	tenga un objeto equipado
							 */
							if ( $equippedItem )
							{
								/*
								 *	Si tiene, movemos este objeto
								 *	equipado al inventario, al slot
								 *	de $characterItem
								 */
								$equippedItem->location = 'inventory';
								$equippedItem->slot = $characterItem->slot;

								$equippedItem->save();

								/*
								 *	Disparamos el evento de desequipar objeto
								 */
								Event::fire('unequipItem', [$equippedItem]);
							}

							/*
							 *	¡Le equipamos el objeto al personaje!
							 */
							$characterItem->location = $item->body_part;
							$characterItem->slot = 0;

							$characterItem->save();

							break;

						case 'lrhand':
							/*
							 *	En caso de que $characterItem sea de dos manos
							 *	entonces tenemos que buscar los objetos
							 *	que tiene equipados en ambas manos
							 */
							$equippedItems = $character->items()->where('location', '=', 'lhand')->or_where('location', '=', 'rhand')->get();

							/*
							 *	Evitamos acciones innecesarias...
							 */
							if ( count($equippedItems) > 0 )
							{
								/*
								 *	Array en el que guardaremos los slots
								 *	que están vacíos
								 */
								$emptySlots = [];

								/*
								 *	¿Hay espacio en el inventario?
								 */
								$spaceInInventory = true;

								foreach ( $equippedItems as $equippedItem )
								{
									if ( $spaceInInventory )
									{
										/*
										 *	Buscamos un slot en el inventario para los objetos
										 */
										$spaceInInventory = CharacterItem::get_empty_slot();

										if ( $spaceInInventory )
										{
											$emptySlots[$equippedItem->id] = $spaceInInventory;
											$spaceInInventory = true;
										}
									}
								}

								/*
								 *	Verificamos si no hay espacio en el inventario
								 *	para guardar los objetos que vamos a sacar
								 */
								if ( ! $spaceInInventory )
								{
									/*
									 *	Si no hay, redirigimos notificándolo
									 */
									return Redirect::to('authenticated/index');
								}

								/*
								 *	Ahora que sabemos que tenemos
								 *	espacio en el inventario para los objetos
								 *	los situamos en el inventario
								 */
								foreach ( $equippedItems as $equippedItem )
								{
									$equippedItem->location = 'inventory';
									$equippedItem->slot = $emptySlots[$equippedItem->id];

									$equippedItem->save();

									/*
									 *	Disparamos el evento de desequipar objeto
									 */
									Event::fire('unequipItem', [$equippedItem]);
								}
							}

							/*
							 *	Finalmente, ¡equipamos el objeto!
							 */
							$characterItem->location = 'lrhand';
							$characterItem->slot = 0;

							$characterItem->save();

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
			}
		}

		/*
		 *	Disparamos el evento de equipar objeto
		 */
		Event::fire('equipItem', [$characterItem]);

		return Redirect::to('authenticated/index');
	}
}