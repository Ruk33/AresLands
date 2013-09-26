<?php

class QuestActionPve
{
	/**
	 * Progreso de la mision del personaje.
	 * 
	 * @var CharacterQuest
	 */
	protected $characterQuest;
	
	/**
	 * El id de los monstruos con los que
	 * hay que batallar.
	 * 
	 * @var Array
	 */
	protected $monstersId = array();
	
	/**
	 * Cantidad de monstruos a batallar.
	 * Los índices deben ir en sincronia
	 * con $monstersId.
	 * 
	 * @var Array
	 */
	protected $monstersAmount = array();
	
	/**
	 * Devolvemos el key con el que guardamos
	 * informacion en el progreso del personaje.
	 * 
	 * @return String
	 */
	protected function get_key()
	{
		return get_class($this);
	}
	
	/**
	 * Cuando el evento pve se dispare, este
	 * sera el metodo que se ejecute.
	 * 
	 * @param Npc $monster
	 * @return bool
	 */
	public function execute(Npc $monster)
	{
		// Queremos trabajar solamente
		// con monstruos
		if ( $monster->type != 'npc' )
		{
			// Vemos si el monstruo esta en la lista
			// con los que debemos batallar
			$monsterKey = array_search($monster->id, $this->monstersId);
			
			if ( $monsterKey !== false )
			{				
				$newValue = $this->characterQuest->get_var($this->get_key() . ".$monsterKey") + 1;
				
				// Evitamos pasarnos del limite
				if ( $newValue <= $this->monstersAmount[$monsterKey] )
				{
					$this->characterQuest->set_var($this->get_key() . ".$monsterKey", $newValue, $newValue . '/' . $this->monstersAmount[$monsterKey] . ' ' . $monster->name);
				}
				
				$completed = true;
				
				// Verificamos si esta accion (pve) ya finalizo
				foreach ( $this->monstersId as $key => $value )
				{
					if ( $this->characterQuest->get_var($this->get_key() . ".$key") < $this->monstersAmount[$key] )
					{
						$completed = false;
						break;
					}
				}
				
				if ( $completed )
				{
					$this->characterQuest->set_var($this->get_key(), true, null);
					
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Solo se ejecuta al aceptar la mision.
	 * Esta funcion debe ejecutarse 1 sola vez.
	 */
	public function set_variables()
	{
		$this->characterQuest->set_var($this->get_key(), false, null);
		
		foreach ( $this->monstersId as $key => $value )
		{
			$monster = Npc::where_id($value)->select('name')->first();
			$this->characterQuest->set_var($this->get_key() . ".$key", 0, '0/' . $this->monstersAmount[$key] . ' ' . $monster->name);
		}
	}
	
	/**
	 * Verificamos si ha completado los
	 * requisitos de estas acciones
	 * 
	 * @return bool
	 */
	public function is_completed()
	{		
		return $this->characterQuest->get_var($this->get_key());
	}
	
	/**
	 * @param CharacterQuest $characterQuest
	 * @param Array $monstersId
	 * @param Array $monstersAmount
	 */
	public function __construct(CharacterQuest $characterQuest, $monstersId, $monstersAmount)
	{
		$this->characterQuest = $characterQuest;
		$this->monstersId = $monstersId;
		$this->monstersAmount = $monstersAmount;
	}
}