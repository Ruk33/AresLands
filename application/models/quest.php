<?php

class Quest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'quests';
	public static $key = 'id';

	/**
	 *	@deprecated
	 */
	public function get_data()
	{
		return unserialize($this->get_attribute('data'));
	}

	/**
	 *	@deprecated
	 */
	public function set_data($data)
	{
		$this->set_attribute('data', serialize($data));
	}

	/**
	 *	@deprecated
	 */
	private function get_value_from_data($valueName)
	{
		$data = $this->data;
		$value = array();

		if ( isset($data[$valueName]) )
		{
			$value = $data[$valueName];
		}

		return $value;
	}

	/**
	 *	@deprecated
	 */
	public function get_triggers_array()
	{
		return $this->get_value_from_data('triggers');
	}

	/**
	 *	Devolvemos en forma de array
	 *	las recompensas
	 *
	 *	@return <array>
	 *	@deprecated
	 */
	public function get_rewards_array()
	{
		return $this->get_value_from_data('rewards');
	}

	/**
	 *	Agregamos un valor a la columna data
	 *	
	 *	@param <string> $valueName
	 *	@param <array> $value
	 *	@deprecated
	 */
	private function add_value_to_data($valueName, $value)
	{
		$data = $this->data;

		if ( ! is_array($data) )
		{
			$data = array();
		}

		/*
		 *	Verificamos que exista el índice
		 *	$valueName, de lo contrario lo creamos
		 */
		if ( ! isset($data[$valueName]) )
		{
			$data[$valueName] = array();
		}

		$data[$valueName] = $value;

		$this->data = $data;
	}

	/**
	 *	Agregamos recompensas a la misión
	 *	El array debe tener los índices:
	 *		item_id 		-> item id de la recompensa
	 *		amount 			-> cantidad de la recompensa
	 *		text_for_view 	-> texto con formato justo para mostrar en las vistas
	 *
	 *	@param <array> $rewards
	 *	@return <bool>
	 *	@deprecated
	 */
	public function add_rewards($rewards)
	{
		foreach ( $rewards as $reward )
		{
			if ( ! (isset($reward['item_id']) && isset($reward['amount']) && isset($reward['text_for_view'])) )
			{
				return false;
			}
		}

		$this->add_value_to_data('rewards', $rewards);

		return true;
	}

	/**
	 *	Agregamos uno o varios triggers
	 *	que deberán ser registrados en
	 *	la tabla character_triggers al 
	 *	momento de aceptar la quest
	 *	
	 *	@param <array> $triggers
	 *	@deprecated
	 */
	public function add_triggers($triggers)
	{
		$this->add_value_to_data('triggers', $triggers);
	}

	/**
	 *	Devolvemos un string con formato
	 *	para mostrar la recompensa en las vistas
	 *
	 *	@return <string>
	 */
	public function get_rewards_for_view()
	{
		$rewards = $this->rewards;
		$formatedString = '';

		foreach ( $rewards as $reward )
		{
			switch ( $reward->item_id )
			{
				case Config::get('game.coin_id'):
					$coins = Item::get_divided_coins($reward->amount);
					
					$text = '<i class="coin coin-copper"></i>';
					$text = '<span data-toggle="tooltip" data-original-title="Cantidad: 
						<ul class=\'inline\' style=\'margin: 0;\'>
						<li><i class=\'coin coin-gold pull-left\'></i> ' . $coins['gold'] . '</li>
						<li><i class=\'coin coin-silver pull-left\'></i> ' . $coins['silver'] . '</li>
						<li><i class=\'coin coin-copper pull-left\'></i> ' . $coins['copper'] . '</li>
					</ul>">' . $text . '</span>';
					break;
				
				case Config::get('game.xp_item_id'):
					$text = '<img src="' . URL::base() . '/img/xp.png" width="22px" height="18px" />';
					$text = '<span data-toggle="tooltip" data-original-title="Cantidad: ' . $reward->amount . '">' . $text . '</span>';
					break;

				default:
					$text = '<img src="' . URL::base() . '/img/icons/items/'. $reward->item_id .'.png" />';
					$item = $reward->item;

					if ( $item )
					{
						$text = '<span data-toggle="tooltip" data-original-title="' . $item->get_text_for_tooltip() . '<p>Cantidad: ' . $reward->amount . '</p>">' . $text . '</span>';
					}
					break;
			}

			$formatedString .= '<li style="vertical-align: top;"><div class="quest-reward-item">' . $text . '</div></li>';
		}

		return '<ul class="inline">' . $formatedString . '</ul>';
	}

	/*
	 *	Damos la recompensa al personaje
	 *	que está logueado
	 */
	public function give_reward()
	{
		$character = Character::get_character_of_logged_user(array('id', 'xp', 'points_to_change', 'level', 'clan_id'));

		ActivityBar::add($character, 3);

		/*
		 *	Obtenemos todas las recompensas
		 *	de la misión
		 */
		$rewards = $this->rewards;

		$characterItem = null;

		foreach ( $rewards as $reward )
		{
			$character->add_item($reward->item_id, $reward->amount);
		}
	}

	/**
	 *	Aceptamos la misión para un personaje
	 *
	 *	Se devuelve false en caso de que
	 *	no se haya podido aceptar.
	 *
	 *	@return <bool>
	 */
	public function accept(Character $character)
	{
		$characterQuest = null;

		if ( $this->complete_required )
		{
			if ( ! $character->has_quest_completed(Quest::find($this->complete_required)) )
			{
				return false;
			}
		}

		// Nos fijamos si ya no tiene pedida la mision
		// y no la ha completado
		if ( $character->has_unfinished_quest($this) )
		{
			return false;
		}

		if ( $character->has_quest_completed($this) )
		{
			if ( $this->repeatable )
			{
				$characterQuest = $character->quests()->where('quest_id', '=', $this->id)->first();

				// Verificamos si ha pasado el tiempo requerido
				// para volver a pedir nuevamente la misión
				if ( $this->repeatable_after > time() - $characterQuest->time )
				{
					return false;
				}
				else
				{
					// Borramos así creamos de nuevo
					// (porque recordemos, el progreso se guarda)
					$characterQuest->delete();
				}
			}
			else
			{
				// Si no es repetible, y el personaje
				// ya la ha completado...
				return false;
			}
		}

		/*
		 *	Registramos los triggers
		 */
		$triggers = $this->triggers;
		$characterTrigger = null;

		foreach ( $triggers as $trigger )
		{
			$characterTrigger = new CharacterTrigger();

			$characterTrigger->character_id = $character->id;
			$characterTrigger->event = $trigger->event;
			$characterTrigger->class_name = $this->class_name;

			$characterTrigger->save();
		}

		/*
		 *	Creamos el progreso
		 *	para el personaje
		 */
		$characterQuest = new CharacterQuest();

		$characterQuest->character_id = $character->id;
		$characterQuest->quest_id = $this->id;
		$characterQuest->progress = 'started';
		$characterQuest->save();

		/*
		 *	Disparamos el evento de aceptar
		 *	misiones
		 */
		Event::fire('acceptQuest', array($character, $this));

		return true;
	}

	public function npcs()
	{
		//return $this->has_many('NpcQuest', 'npc_id');
		return $this->has_many_and_belongs_to('Npc', 'npc_quests');
	}

	public function triggers()
	{
		return $this->has_many('QuestTrigger', 'quest_id');
	}

	public function rewards()
	{
		return $this->has_many('QuestReward', 'quest_id');
	}
}