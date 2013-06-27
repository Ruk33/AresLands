<?php

class Quest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'quests';

	public function get_data()
	{
		return unserialize($this->get_attribute('data'));
	}

	public function set_data($data)
	{
		$this->set_attribute('data', serialize($data));
	}

	private function get_value_from_data($valueName)
	{
		$data = $this->data;
		$value = [];

		if ( isset($data[$valueName]) )
		{
			$value = $data[$valueName];
		}

		return $value;
	}

	public function get_triggers_array()
	{
		return $this->get_value_from_data('triggers');
	}

	/**
	 *	Devolvemos en forma de array
	 *	las recompensas
	 *
	 *	@return <array>
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
	 */
	private function add_value_to_data($valueName, $value)
	{
		$data = $this->data;

		if ( ! is_array($data) )
		{
			$data = [];
		}

		/*
		 *	Verificamos que exista el índice
		 *	$valueName, de lo contrario lo creamos
		 */
		if ( ! isset($data[$valueName]) )
		{
			$data[$valueName] = [];
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
	public function get_reward_for_view()
	{
		$rewards = $this->get_rewards_array();
		$formatedString = '';

		foreach ( $rewards as $reward )
		{
			if ( isset($reward['text_for_view']) )
			{
				$formatedString .= $reward['text_for_view'] . ' | ';
			}
		}

		/*
		 *	Removemos el último " | "
		 */
		return substr($formatedString, 0, -3);
	}

	/*
	 *	Damos la recompensa al personaje
	 *	que está logueado
	 */
	public function give_reward()
	{
		$character = Session::get('character');

		/*
		 *	Obtenemos todas las recompensas
		 *	de la misión
		 */
		$rewards = $this->get_rewards_array();

		$characterItem = null;

		foreach ( $rewards as $reward )
		{
			/*
			 *	Nos fijamos si el personaje
			 *	ya tiene uno de los items
			 *	que le vamos a recompensar
			 */
			$characterItem = $character->items()->where('item_id', '=', $reward['item_id'])->first();

			/*
			 *	Si lo tiene, y el mismo puede
			 *	ser acumulado...
			 */
			if ( $characterItem && $characterItem->item->stackable )
			{
				/*
				 *	Solamente aumentamos la cantidad
				 */
				$characterItem->count += $reward['amount'];
			}
			else
			{
				/*
				 *	Aparentemente el personaje
				 *	no tiene uno de estos objetos
				 *	o el mismo no se puede acumular
				 */
				$characterItem = new CharacterItem();

				$characterItem->owner_id = $character->id;
				$characterItem->item_id = $reward['item_id'];
				$characterItem->count = $reward['amount'];
				$characterItem->location = 'inventory';
				$characterItem->slot = $characterItem->get_empty_slot();
			}

			/*
			 *	¡Guardamos!
			 */
			$characterItem->save();
		}
	}

	/*
	 *	Aceptamos la misión para
	 *	el personaje que esté logueado
	 */
	public function accept()
	{
		$character = Session::get('character');

		/*
		 *	Registramos los triggers
		 */
		$triggers = $this->get_triggers_array();
		$characterTrigger = null;

		foreach ( $triggers as $trigger )
		{
			$characterTrigger = new characterTrigger();

			$characterTrigger->character_id = $character->id;
			$characterTrigger->event = $trigger;
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
	}
}