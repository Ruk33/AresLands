<?php

abstract class QuestAction
{
	/**
	 * Id de la mision
	 * 
	 * @var int
	 */
	protected static $questId = 0;
	
	/**
	 * Progreso del personaje
	 * 
	 * @var CharacterQuest
	 */
	protected $characterQuest = null;
	
	/**
	 * @var QuestActionPve
	 */
	protected $actionPveBattle = null;
	
	/**
	 * @var QuestActionPveWin
	 */
	protected $actionPveBattleWin = null;
	
	/**
	 * @var QuestActionNpcTalk
	 */
	protected $actionNpcTalk = null;
	
	/**
	 * @var QuestActionAccept
	 */
	protected $actionAcceptQuest = null;
	
	/**
	 * Donde se establezcan las acciones
	 * de la mision.
	 */
	abstract protected function setup();
	
	/**
	 * Verificamos si el personaje tiene la mision
	 * y el estado de la misma es iniciado.
	 * 
	 * @param Character $character
	 * @return bool
	 */
	private static function has_quest(Character $character)
	{
		return $character
		->quests()
		->where_quest_id(static::$questId)
		->where_progress('started')
		->take(1)
		->count() > 0;
	}
	
	/**
	 * Inicializamos variables para
	 * el progreso del personaje
	 */
	private function set_variables()
	{
		// Nos aseguramos de que el siguiente
		// codigo solamente corra una vez
		if ( ! $this->characterQuest->get_var('setVariables') )
		{
			if ( $this->actionPveBattle )
			{
				$this->actionPveBattle->set_variables();				
			}
			
			if ( $this->actionPveBattleWin )
			{
				$this->actionPveBattleWin->set_variables();
			}
			
			if ( $this->actionNpcTalk )
			{
				$this->actionNpcTalk->set_variables();
			}
			
			if ( $this->actionAcceptQuest )
			{
				$this->actionAcceptQuest->set_variables();
			}
			
			$this->characterQuest->set_var('setVariables', true, null);
		}
	}
	
	/**
	 * Verificamos si ya ha completado con todos
	 * los requisitos de la mision
	 * 
	 * @return bool
	 */
	private function is_completed()
	{
		if ( $this->actionPveBattle )
		{
			if ( ! $this->actionPveBattle->is_completed() )
			{
				return false;
			}
		}
		
		if ( $this->actionPveBattleWin )
		{
			if ( ! $this->actionPveBattleWin->is_completed() )
			{
				return false;
			}
		}
		
		if ( $this->actionNpcTalk )
		{
			if ( ! $this->actionNpcTalk->is_completed() )
			{
				return false;
			}
		}
		
		if ( $this->actionAcceptQuest )
		{
			if ( ! $this->actionAcceptQuest->is_completed() )
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * El primer parametro debe ser el evento, el segundo el personaje.
	 * 
	 * @param string $event
	 * @param mixed $data
	 * @return bool
	 */
	public function run($event, $data)
	{
		$destroyTrigger = true;
		
		// Si el personaje no tiene la mision...
		if ( ! $this->characterQuest )
		{
			return $destroyTrigger;
		}
		
		$this->setup();
		$this->set_variables();
		
		switch ( $event )
		{
			case 'acceptQuest':
				if ( $this->actionAcceptQuest )
				{
					$this->actionAcceptQuest->execute($data);
				}
				
				break;
			
			case 'pveBattle':
				if ( $this->actionPveBattle )
				{
					$destroyTrigger = $this->actionPveBattle->execute($data);
				}
				
				break;
				
			case 'pveBattleWin':
				if ( $this->actionPveBattleWin )
				{
					$destroyTrigger = $this->actionPveBattleWin->execute($data);
				}
				
				break;
				
			case 'npcTalk':
				if ( $this->actionNpcTalk )
				{
					$destroyTrigger = $this->actionNpcTalk->execute($data);
				}
				
				break;
		}
		
		if ( $this->is_completed() )
		{
			$this->characterQuest->progress = 'reward';
		}
		
		$this->characterQuest->save();
		
		return $destroyTrigger;
	}
	
	/**
	 * @param Character $character
	 */
	public function __construct(Character $character)
	{
		if ( self::has_quest($character) )
		{
			$this->characterQuest = $character->quests()->where_quest_id(static::$questId)->first();
		}
	}
}