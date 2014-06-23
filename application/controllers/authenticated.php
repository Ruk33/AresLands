<?php

class Authenticated_Controller extends Authenticated_Base
{
	public static function register_routes()
	{
		Route::get("authenticated", array(
			"uses" => "authenticated@index",
			"as"   => "get_authenticated_index"
		));
	}
	
	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'csrf')->on(array('post'))->except(array(
            'addstat',
            'characters',
            'editclanmessage'
        ));
	}
	
	public function get_index()
	{        
        $character = Character::get_character_of_logged_user(array_merge(
            Character::$COLUMNS_BASIC,
            Character::$COLUMNS_STATS,
            Character::$COLUMNS_STATS_EXTRA,
            Character::$COLUMNS_LIFE,
            Character::$COLUMNS_OTHER,
            array('last_logged')
        ));
        
		/*
		 *	Verificamos logueada del día
		 */
		if ( $character->check_logged_of_day() )
		{
			$character->give_logged_of_day_reward();
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
		 *	Obtenemos el orbe del personaje
		 */
		$orb = $character->orbs()->select(array('id', 'name', 'description'))->first();

		/*
		 *  Talentos que se puede lanzar a el mismo
		 */
		$talents = $character->get_castable_talents($character);

		/*
		 * Zona en donde se encuentra el personaje y la cantidad
		 * de tiempo que lleva explorando en ella
		 */
		$zone = $character->zone()->select(array('id', 'name', 'description'))->first();
		$exploringTime = $character->exploring_times()->where('zone_id', '=', $zone->id)->first();

		/*
		 * Arma, escudo y mercenarios
		 */
		$weapon = $character->get_weapon();
		$shield = $character->get_shield();

		$mercenary = $character->get_mercenary();
		$mercenary = ( $mercenary ) ? $mercenary->item : null;

		$secondMercenary = null;
		if ( $character->has_second_mercenary() )
		{
			$secondMercenary = $character->get_second_mercenary()->first();
		}

		/*
		 * Objetos del inventario
		 */
		$inventoryItems = array();
		
		foreach ( $character->get_inventory_items()->with('item')->get() as $inventoryItem )
		{
			$inventoryItems[$inventoryItem->slot] = $inventoryItem;
		}

		$this->layout->title = 'Inicio';
		$this->layout->content = View::make('authenticated.index')->with(array(
			'character'       => $character,
			'weapon'          => $weapon,
			'shield'          => $shield,
			'mercenary'       => $mercenary,
			'secondMercenary' => $secondMercenary,
			'activities'      => $activities,
			'skills'          => $skills,
			'orb'             => $orb,
			'talents'         => $talents,
			'zone'            => $zone,
			'exploringTime'   => $exploringTime,
			'inventoryItems'  => $inventoryItems
		));
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

	public function get_orbs()
	{
		$character = Character::get_character_of_logged_user(array('id', 'level'));
		$orbs = Orb::order_by('min_level', 'asc')->get();

		$this->layout->title = 'Orbes';
		$this->layout->content = View::make('authenticated.orbs')
									 ->with('character', $character)
									 ->with('orbs', $orbs);
	}

	public function get_destroyItem($characterItemId = false)
	{
        if ( $characterItemId )
        {
            $character = Character::get_character_of_logged_user(array('id'));
            $characterItem = $character->items()->with(array('item'))->find($characterItemId);
            
            if ( $characterItem && $characterItem->item->destroyable )
            {
                $characterItem->delete();
            }
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

	public function get_ranking($rank = 'kingOfTheHill')
	{
		switch ( $rank )
		{
			case 'kingOfTheHill':
				$elements = KingOfTheHill::get_list();
				break;

			case 'pvp':
				$select = array('id', 'name', 'gender', 'race', 'pvp_points', 'characteristics');
				$elements = Character::with('clan')->get_characters_for_pvp_ranking()->select($select)->paginate(50);

				break;

			case 'clan':
				$elements = ClanOrbPoint::with('clan')->order_by('points', 'desc')->paginate(50);
				break;

			default:
				return Redirect::to('authenticated/ranking');
				break;
		}
		
		$this->layout->title = 'Ranking';
		$this->layout->content = View::make('authenticated.ranking')->with('rank', $rank)->with('elements', $elements);
	}

	public function post_buyTrade()
	{
		$character = Character::get_character_of_logged_user();
		$trade = Trade::where('id', '=', Input::get('id'))->first();

		if ( $trade )
		{
			if ( $trade->buy($character) )
			{
				return Redirect::to('authenticated/trades')->with('success', array(
					'Compraste el objeto exitosamente.'
				));
			}
			else
			{
				return Redirect::to('authenticated/trades')->with('error', array(
					'No puedes comprar el objeto porque o no tienes espacio en tu inventario o no tienes suficientes monedas.'
				));
			}
		}
		
		return Redirect::to('authenticated/trades');
	}

	public function post_cancelTrade()
	{
		$character = Character::get_character_of_logged_user();
		$trade = $character->trades()->where('id', '=', Input::get('id'))->first();

		if ( $trade )
		{
			if ( ! $trade->cancel() )
			{
				return Redirect::to('authenticated/trades')->with('error', array('El comercio no se pudo cancelar. Verifica que tengas espacio en tu inventario.'));
			}
			else
			{
				return Redirect::to('authenticated/trades')->with('success', 'El comercio ha sido cancelado.');
			}
		}
		
		return Redirect::to('authenticated/trades');
	}
	
	public function post_newTrade()
	{
		$sellerCharacter = Character::get_character_of_logged_user(array('id', 'clan_id'));
		
		if ( ! $sellerCharacter->can_trade() )
		{
			return Redirect::to('authenticated/index');
		}
		
		$amount = Input::get('amount');
		
		if ( ! isset($amount[Input::get('item')]) )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		$amount = $amount[Input::get('item')];
		
		$time = Input::get('time');
		
		if ( ! in_array($time, array(8, 16, 24)) )
		{
			return Redirect::to('authenticated/newTrade/');
		}

		$sellerCharacterItem = $sellerCharacter->items()
											   ->where('id', '=', Input::get('item'))
											   ->where('count', '>=', $amount)
											   ->where('location', '=', 'inventory')
											   ->first();

		if ( ! $sellerCharacterItem )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		$item = $sellerCharacterItem->item()
									->select(array('id', 'stackable', 'selleable'))
									->first();
		
		if ( ! $item )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		if ( ! $item->selleable )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		/*
		 *	Evitamos que intenten comerciar
		 *	cantidad en un objeto que no puede ser acumulado
		 */
		if ( ! $item->stackable )
		{
			$amount = 1;
		}
		
		$price = '';
		
		foreach ( array(Input::get('gold', '00'), Input::get('silver', '00'), Input::get('copper', '00')) as $coin )
		{
			if ( ! is_numeric($coin) )
			{
				$coin = '00';
			}
			
			if ( strlen($coin) == 1 )
			{
				$coin = '0' . $coin;
			}
			
			$price .= $coin;
		}

		$trade = new Trade();

		$trade->seller_id = $sellerCharacter->id;
		$trade->amount = $amount;
		$trade->price_copper = $price;
		$trade->until = time() + $time * 60 * 60;
		$trade->duration = $time;

		if ( $sellerCharacter->clan_id > 0 && Input::get('only_clan') != 0 )
		{
			$trade->clan_id = $sellerCharacter->clan_id;
		}

		if ( $trade->validate() )
		{
			$tradeItem = new TradeItem();
			
			$tradeItem->item_id = $sellerCharacterItem->item_id;
			$tradeItem->data = $sellerCharacterItem->get_attribute('data');
			
			$tradeItem->save();
			
			$trade->trade_item_id = $tradeItem->id;
			
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

			Session::flash('successMessage', 'Comercio creado con éxito');
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
			$characterItems = $character->tradeable_items()->select(array('character_items.*'))->get();

			$this->layout->title = 'Nuevo comercio';
			$this->layout->content = View::make('authenticated.newtrade')
			->with('character', $character)
			->with('characterItems', $characterItems);
		}
		else
		{
			return Redirect::to('authenticated/trades')
			->with('errorMessages', array('No tienes ningún objeto para comerciar.'));
		}
	}

	public function get_trades($filter = 'all')
	{
		$character = Character::get_character_of_logged_user(array('id'));
		
		switch ( $filter )
		{
			case 'self':
			case 'weapon':
			case 'armor':
			case 'consumible':
			case 'all':
				break;
			
			default:
				$filter = 'all';
		}
		
		if ( $filter == 'all' )
		{
			$trades = Trade::with(array('trade_item', 'trade_item.item'))->get_valid()->get();
		}
		elseif ( $filter == 'self' )
		{
			$trades = $character->trades()->with(array('trade_item', 'trade_item.item'))->get();
		}
		else
		{
			$trades = Trade::filter_by_item_class($filter)
						   ->select(array('trades.*'))
						   ->get();
		}

		$this->layout->title = 'Comercios';
		$this->layout->content = View::make('authenticated.trades')
									 ->with('trades', $trades)
									 ->with('character', $character);
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

	public function post_clearAllMessages()
	{
		switch ( Input::get('type') )
		{
			case 'received':
			case 'attack':
			case 'defense':
				$character = Character::get_character_of_logged_user(array('id'));
				$character->messages()->where('type', '=', Input::get('type'))->delete();
				
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
	
	public function get_messages($type = 'received')
	{
		$type = strtolower($type);
		
		if ( ! in_array($type, array('received', 'attack', 'defense')) )
		{
			return Redirect::to('authenticated/messages');
		}
		
		$character = Character::get_character_of_logged_user(array('id'));
		$messages = $character->messages()->where('type', '=', $type)->order_by('unread', 'desc')->order_by('date', 'desc')->get();
		
		$this->layout->title = 'Mensajes';
		$this->layout->content = View::make('authenticated.messages')->with('messages', $messages)->with('type', $type);
	}

	public function post_toBattleMonster()
	{
		$monsterId = Input::get('monster_id');
		
		$character = Character::get_character_of_logged_user();
		$monster = ( $monsterId ) ? Monster::where('type', '=', 'monster')->where('zone_id', '=', $character->zone_id)->find((int) $monsterId) : false;
		
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

			return Redirect::to('authenticated/readMessage/' . $battle->get_attacker_notification_message()->id);
		}
		else
		{
			return Redirect::to('authenticated/battle/');
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
			$pair = ( Input::get('pair') ) ? Character::find((int) Input::get('pair')) : false;
			if ( $pair )
			{
				if ( $character->can_attack_in_pairs() )
				{
					if ( $character->can_attack_with($pair) )
					{
						if ( $pair->id != $target->id )
						{
							$battle = $character->battle_against($target, $pair);
						}
						else
						{
							return Redirect::to('authenticated/battle');
						}
					}
					else
					{
						Session::flash('errorMessage', 'No puedes atacar en pareja con ' . $pair->name);
						return Redirect::to('authenticated/battle');
					}
				}
				else
				{
					Session::flash('errorMessage', 'No puedes atacar en parejas');
					return Redirect::to('authenticated/battle');
				}
			}
			else
			{
				$battle = $character->battle_against($target);
			}
		}
		else
		{
			Session::flash('errorMessage', 'Aún no puedes pelear');
			return Redirect::to('authenticated/battle');
		}

		return Redirect::to('authenticated/readMessage/' . $battle->get_attacker_notification_message()->id);
	}

	public function post_battle()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'name', 'clan_id', 'registered_in_tournament', 'is_traveling'));
		$characterFinded = null;

		switch ( Input::get('search_method') ) 
		{
			case 'name':
				$characterFinded = $character->get_opponent()->where('name', '=', Input::get('character_name'));
				break;

			case 'random':
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
				
				if ( Input::get('race') != 'any' )
				{
					$characterFinded = $character->get_opponent(array(Input::get('race')));
				}
				else
				{
					$characterFinded = $character->get_opponent();
				}
				
				$characterFinded = $characterFinded->where('level', $operation, (int) Input::get('level'))
												   ->order_by(DB::raw('RAND()'));
				
				break;

			case 'group':
				$characterFinded = $character->get_opponent()
											 ->where('clan_id', '=', (int) Input::get('clan'))
											 ->order_by(DB::raw('RAND()'));
				
				break;
		}

		$characterFinded = $characterFinded->select(array(
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
			'registered_in_tournament',
			'characteristics',
			'invisible_until',
			'is_traveling'
		))->first();

		/*
		 *	Verificamos si encontramos personaje
		 */
		if ( $characterFinded )
		{
			if ( $characterFinded->has_skill(Config::get('game.trap_skill')) )
			{
				$characterFinded->cast_random_trap_to($character, true);
			}
			
			if ( $character->can_remove_invisibility_of($characterFinded) )
			{
				$characterFinded->remove_invisibility();
			}
			
			$weapon = $characterFinded->get_weapon();
			$shield = $characterFinded->get_shield();

			$mercenary = $characterFinded->get_mercenary();
			$mercenary = ( $mercenary ) ? $mercenary->item : null;
            
            $castableSkills = $character->get_castable_talents($characterFinded);

			/*
			 *	Obtenemos los orbes
			 */
			$orbs = $characterFinded->orbs()->select(array('id', 'name', 'description'))->get();
			
			$skills = array();
			
			if ( $character->is_admin() )
			{
				$skills = $characterFinded->skills;
			}
			
			// Posibles parejas con las que puede
			// atacar el personaje
			$pairs = array();
			
			if ( $character->can_attack_in_pairs() )
			{
				$pairs = $character->get_pairs_to($characterFinded);
			}

			$this->layout->title = $characterFinded->name;
			$this->layout->content = View::make('authenticated.character', array(
                'character' => $character,
                'weapon' => $weapon,
                'shield' => $shield,
                'mercenary' => $mercenary,
                'orbs' => $orbs,
                'skills' => $skills,
                'characterToSee' => $characterFinded,
                'hideStats' => $characterFinded->has_characteristic(Characteristic::RESERVED),
                'castableSkills' => $character->get_castable_talents($characterFinded),
                'pairs' => $pairs
            ));
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

	public function post_dungeon()
	{
		$dungeon = Dungeon::find(Input::get('id'));
		$redirection = Redirect::to('authenticated');

		if ( $dungeon )
		{
			$character = Character::get_character_of_logged_user();

			if ( $dungeon->can_character_do_dungeon($character) )
			{
				$dungeonBattle = new DungeonBattle($character, $dungeon, $dungeon->get_level($character));

				if ( $dungeonBattle->get_completed() )
				{
					return $redirection->with('message', '¡Haz completado la mazmorra!');
				}
				else
				{
					return $redirection->with('error', 'Uno de los monstruos de la mazmorra te ha derrotado');
				}
			}
		}

		return $redirection;
	}

	public function get_dungeon()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level'));
		$dungeons = Dungeon::available_for($character)->get();

		$this->layout->title = 'Mazmorras';
		$this->layout->content = View::make('authenticated.dungeon', compact('character', 'dungeons'));
	}

	public function get_battle()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level'));
		$monsters = Monster::get_from_zone($character->zone, $character)->order_by('level', 'asc')->get();

		$this->layout->title = '¡Batallar!';
		$this->layout->content = View::make('authenticated.battle', compact('character', 'monsters'));
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

		$zones = Zone::where('type', '=', 'city')
                     ->where('min_level', '<=', $character->level)
                     ->where('id', '<>', $character->zone_id)
                     ->select(array('id', 'name', 'description', 'min_level'))
                     ->get();

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
		$npc = Npc::select(array('id', 'name', 'dialog', 'level_to_appear', 'zone_id'))->where('id', '=', (int) $npcId)->where('zone_id', '=', $character->zone_id)->first();

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
		$quests = $npc->available_quests_of($character)->order_by('max_level', 'asc')->get();

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
		$merchandises = $npc->get_merchandises_for($character)->with('item')->get();

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
        $merchandise = null;

        if ( $merchandiseId )
        {
            if ( Input::get('random_merchandise', false) )
            {
                $merchandise = NpcRandomMerchandise::find((int) $merchandiseId);
            }
            else
            {
                $merchandise = NpcMerchandise::find((int) $merchandiseId);
            }
        }

		if ( $merchandise )
		{
			$character = Character::get_character_of_logged_user(array('id', 'xp', 'xp_next_level'));
			$npc = $merchandise->npc()->select(array('id', 'zone_id', 'level_to_appear'))->first();

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
					if ( $item->class == 'consumible' )
					{
						$skills = $character->get_non_clan_skills()
											->select(array('end_time', 'duration', 'amount'))
											->get();
						$skillsCount = 0;
						$time = time();

						foreach ( $skills as $skill )
						{
							// Solo se suma si no ha pasado
							// la mitad de la duracion
							if ( $skill->end_time - $time > $skill->duration * 60 / 2 )
							{
								$skillsCount += $skill->amount;
							}
						}

						// Objetos que no se cuentan
						$invalidItems = array(
							Config::get('game.coin_id'), 
							Config::get('game.xp_item_id')
						);

						$characterItems = $character->items()
													->join('items as item', 'item.id', '=', 'character_items.item_id')
													->where_not_in('item_id', $invalidItems)
													->where('location', '=', 'inventory')
													->where('class', '=', 'consumible')
													->select(array('count'))
													->get();
						$characterItemAmount = 0;

						foreach ( $characterItems as $characterItem )
						{
							$characterItemAmount += $characterItem->count;
						}

						$limit = (int) ($character->xp_next_level * Config::get('game.bag_size'));

						if ( $characterItemAmount + $skillsCount + $amount > $limit )
						{
							return Redirect::to('authenticated/index')->with('error', 'Tienes la mochila muy llena. Recuerda que tu límite es ' . $limit . '.');
						}
					}
					
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
							return Redirect::to('authenticated/index')->with('error', 'No tienes espacio en el inventario.');
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

		Session::flash('buyed', "Gracias por comprar {$amount} {$item->name}, ¿no te interesa algo mas?");
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
					if ( $characterItem->item_id == Config::get('game.chest_item_id') )
					{
						$slot = $character->empty_slot();

						if ( $slot )
						{
							$item = $character->get_item_from_chest()->select(array('id'))->first();

							// Damos el objeto y le damos
							// notificamos al usuario qué objeto
							// obtuvo del tan preciado cofre :)
							$character->add_item($item->id, 1);
							Session::flash('modalMessage', 'chest');
							Session::flash('chest', $item->id);

							$characterItem->count--;

							if ( $characterItem->count > 0 )
							{
								$characterItem->save();
							}
							else
							{
								$characterItem->delete();
							}
						}
						else
						{
							Session::flash('error', 'No tenes espacio en el inventario.');
						}
					}
					else
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
		}

		return Redirect::to('authenticated/index');
	}

	public function get_logout()
	{
		Auth::logout();
		return Redirect::to('home/index/');
	}
}
