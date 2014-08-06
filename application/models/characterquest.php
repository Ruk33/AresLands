<?php

class CharacterQuest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_quests';
	public static $key = 'id';

	/**
	 * Finalizamos progreso y damos recompensa al personaje
	 */
	public function finish()
	{
		$this->quest->give_reward($this->character);
		
		if ( $this->quest->repeatable )
		{
			$this->repeatable_at = time() + $this->quest->repeatable_after;
		}
		
		$this->finished_time = time();
		$this->progress = "finished";
		
		$this->save();
	}
	
	public function get_data()
	{
		$data = $this->get_attribute('data');

		if ( is_array($data) )
		{
			return $data;
		}

		return unserialize($data);
	}

	public function set_data($data)
	{
		$this->set_attribute('data', serialize($data));
	}

	/**
	 * Obtenemos el data con los valores iniciales que son requeridos
	 * tales como nombre del npc, la accion que hay que realizar con
	 * el mismo (hablar, matar, etc.) y la cantidad de veces que
	 * tenemos que realizar esta accion
	 *
	 * @param Quest $quest
	 * @return array
	 */
	public function get_initial_data_for_quest(Quest $quest)
	{
		$questNpcs = $quest->quest_npcs;
		$data = array();

		foreach ( $questNpcs as $questNpc )
		{
			$npc = $questNpc->npc()->select(array('id', 'name'))->first();

			if ( $npc )
			{
				if ( ! isset($data[$questNpc->action]) )
				{
					$data[$questNpc->action] = array();
				}

				$data[$questNpc->action][$questNpc->npc_id] = array(
					'name' => $npc->name,
					'amount' => 0,
					'needed_amount' => $questNpc->amount,
					'completed' => false,
				);
			}
		}

		return $data;
	}

	/**
	 * Obtenemos un string con formato del progreso
	 * de la mision del personaje
	 *
	 * @return string
	 */
	public function get_progress_for_view()
	{
		$data = $this->data;
		$stringToView = '';
		
		foreach ( $data as $action => $npcsId )
		{
			foreach ( $npcsId as $npcId )
			{
				$stringToView .= '<li>';

				switch ( $action )
				{
					case 'talk':
						$stringToView .= 'Hablar con ' . $npcId['name'];
						break;

					case 'kill':
						$stringToView .= 'Derrotar ' . $npcId['name'];
						break;
				}

				$stringToView .= ': ' . $npcId['amount'] . '/' . $npcId['needed_amount'];

				if ( $npcId['completed'] )
				{
					$stringToView .= ' (Completado)';
				}

				$stringToView .= '</li>';
			}
		}
		
		return "<ul>$stringToView</ul>";
	}

	/**
	 * Verificamos si personaje termino mision
	 *
	 * @return bool
	 */
	function is_completed()
	{
		if ( $this->progress != 'started' )
		{
			return true;
		}

		$data = $this->data;

		foreach ( $data as $actions )
		{
			foreach ( $actions as $npc )
			{
				if ( ! $npc['completed'] )
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Actualizamos progreso en mision de personaje
	 *
	 * @param $action
	 * @param $npc      Id del NPC o instancia de Npc
	 */
	private function make_npc_progress($action, $npc)
	{
		if ( ! $npc instanceof Npc && ! $npc instanceof Monster )
		{
			$npc = Npc::select(array('id'))->where('id', '=', $npc)->first();
		}

		if ( ! $npc )
		{
			return;
		}

		$data = $this->data;

		if ( ! isset($data[$action][$npc->id]) )
		{
			return;
		}

		$data[$action][$npc->id]['amount']++;

		if ( $data[$action][$npc->id]['amount'] >= $data[$action][$npc->id]['needed_amount'] )
		{
			$data[$action][$npc->id]['amount'] = $data[$action][$npc->id]['needed_amount'];
			$data[$action][$npc->id]['completed'] = true;
		}

		$this->data = $data;

		if ( $this->is_completed() )
		{
			$this->progress = 'finished';
            $this->quest->give_reward($this->character);
		}

		$this->save();
	}

	/**
	 * Actualizamos progreso de hablar con Npc
	 *
	 * @param $npc Instance de Npc o id de npc
	 */
	public function talk_to_npc($npc)
	{
		$this->make_npc_progress('talk', $npc);
	}

	/**
	 * Actualizamos progreso de matar Npc
	 *
	 * @param $npc Instancia de Npc o id de npc
	 */
	public function kill_npc($npc)
	{
		$this->make_npc_progress('kill', $npc);
	}

	public function quest()
	{
		return $this->belongs_to('Quest', 'quest_id');
	}
	
	public function character()
	{
		return $this->belongs_to('Character', 'character_id');
	}
}