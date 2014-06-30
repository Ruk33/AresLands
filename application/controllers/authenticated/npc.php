<?php

class Authenticated_Npc_Controller extends Authenticated_Base
{
	public function get_index($id, $name = "")
	{
		$character = $this->character->get_logged();
		$npc = Npc::where_id($id)->where_zone_id($character->zone_id)->first_or_die();

		if ( $npc->is_blocked_to($character) )
		{
			return \Laravel\Redirect::to_route("get_authenticated_index");
		}

		Event::fire('npcTalk', array($character, $npc));

		$merchandises = $npc->get_merchandises_for($character)->with('item')->get();
		
		$quests = $npc->available_quests_of($character)->order_by('max_level', 'asc')->get();
		$repeatableQuests = $npc->repeatable_quests_of($character)->get();
		$startedQuests = $npc->started_quests_of($character)->get();
		$rewardQuests = $npc->reward_quests_of($character)->get();

		$characterCoinsCount = $character->get_coins()->count;

		$this->layout->title = $npc->name;
		$this->layout->content = View::make('authenticated.npc', compact(
			"npc", "characterCoinsCount", "merchandises", "rewardQuests",
			"startedQuests", "repeatableQuests", "quests", "character"
		));
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
}