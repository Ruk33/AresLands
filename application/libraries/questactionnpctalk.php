<?php

class QuestActionNpcTalk
{
	/**
	 * @var CharacterQuest
	 */
	protected $characterQuest = null;
	
	/**
	 * Id de los npcs a los que hay
	 * que hablar
	 * 
	 * @var Array
	 */
	protected $npcsToTalk;
	
	/**
	 * Marcamos la accion como completada
	 */
	protected function mark_as_completed()
	{
		$this->characterQuest->set_var('actionNpcTalk', true);
	}
	
	/**
	 * Chequeamos si esta accion (talk) ya finalizo
	 * 
	 * @return bool
	 */
	protected function check()
	{
		foreach ( $this->npcsToTalk as $key => $value )
		{
			if ( $this->characterQuest->get_var("actionNpcTalk.$key") === false )
			{
				return false;
			}
		}
		
		return true;
	}

	/**
	 * @param Npc $npc
	 */
	public function execute(Npc $npc)
	{
		$npcKey = array_search($npc->id, $this->npcsToTalk);
		
		if ( $npcKey !== false )
		{
			$this->characterQuest->set_var("actionNpcTalk.$npcKey", true, null, true);
			
			if ( $this->check() )
			{
				$this->mark_as_completed();
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Establecemos las variables
	 * de esta accion
	 */
	public function set_variables()
	{
		$this->characterQuest->set_var('actionNpcTalk', false);
		
		foreach ( $this->npcsToTalk as $key => $value )
		{
			$npc = Npc::where_id($value)->select('name')->first();
			$this->characterQuest->set_var("actionNpcTalk.$key", false, 'Habla con ' . $npc->name);
		}
	}
	
	/**
	 * Verificamos si la accion ha sido completada
	 * 
	 * @return bool
	 */
	public function is_completed()
	{
		return $this->characterQuest->get_var('actionNpcTalk');
	}
	
	/**
	 * @param CharacterQuest $characterQuest
	 * @param Array $npcsToTalk
	 */
	public function __construct(CharacterQuest $characterQuest, $npcsToTalk)
	{
		$this->characterQuest = $characterQuest;
		$this->npcsToTalk = $npcsToTalk;
	}
}	