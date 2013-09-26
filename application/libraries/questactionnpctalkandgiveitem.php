<?php

class QuestActionNpcTalkAndGiveItem extends QuestActionNpcTalk
{
	/**
	 * Almacemanos el id de los objetos
	 * que debemos dar. Estos deben
	 * estar organizados de tal forma
	 * que concuerden con $npcsToTalk
	 * 
	 * @var Array $itemsToGive
	 */
	protected $itemsToGive;
	
	/**
	 * La cantidad de objetos que debemos
	 * dar. Este array debe estar en
	 * concordancia con $itemsToGive.
	 * 
	 * @var Array $amount
	 */
	protected $amount;
	
	/**
	 * @return bool
	 */
	protected function check()
	{
		foreach ( $this->npcsToTalk as $npcToTalk )
		{
			foreach ( $this->itemsToGive[$npcToTalk] as $itemToGive )
			{
				if ( $this->characterQuest->get_var("actionNpcTalk.$npcToTalk.$itemToGive") === false )
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * @param Npc $npc
	 * @return bool
	 */
	public function execute(Npc $npc)
	{
		$npcKey = array_search($npc->id, $this->npcsToTalk);
		
		if ( $npcKey !== false )
		{
			$character = $this->characterQuest->character;
			
			foreach ( $this->itemsToGive[$npc->id] as $itemToGive )
			{
				// Saltamos aquellos que ya han sido completados
				if ( $this->characterQuest->get_var("actionNpcTalk.$npc->id.$itemToGive") )
				{
					continue;
				}
				
				$item = Item::where_id($itemToGive)->select(array('stackable'))->first();
				
				if ( $item->stackable )
				{
					$characterItem = $character->items()->where_item_id($itemToGive)->select('id', 'count')->first();
					
					// Nos fijamos si ya no completo este requisito y que tenga el objeto por supuesto
					if ( $characterItem && $characterItem->count >= $this->amount[$itemToGive] )
					{
						if ( $characterItem->count - $this->amount[$itemToGive] > 0 )
						{
							$characterItem->count -= $this->amount[$itemToGive];
							$characterItem->save();
						}
						else
						{
							$characterItem->delete();	
						}
						
						$this->characterQuest->set_var("actionNpcTalk.$npc->id.$itemToGive", true, null, true);
					}
				}
				else
				{
					$characterItems = $character->items()->where_item_id($itemToGive)->take($this->amount[$itemToGive])->select(array('id'))->get();
					if ( count($characterItems) == $this->amount[$itemToGive] )
					{
						foreach ( $characterItems as $characterItem )
						{
							$characterItem->delete();
						}
						
						$this->characterQuest->set_var("actionNpcTalk.$npc->id.$itemToGive", true, null, true);
					}
				}
			}
			
			if ( $this->check() )
			{
				$this->mark_as_completed();
				return true;
			}
		}
		
		return false;
	}
	
	public function set_variables()
	{
		$this->characterQuest->set_var('actionNpcTalk', false);
		
		foreach ( $this->npcsToTalk as $npcToTalk )
		{
			$npc = Npc::where_id($npcToTalk)->select('name')->first();
			
			foreach ( $this->itemsToGive[$npcToTalk] as $itemToGive )
			{
				$item = Item::where_id($itemToGive)->select('name')->first();
				$this->characterQuest->set_var("actionNpcTalk.$npcToTalk.$itemToGive", false, 'Llevale ' . $this->amount[$itemToGive] . ' ' . $item->name . ' a ' . $npc->name);	
			}
		}
	}
	
	/**
	 * Ejemplo
	 * 
	 * new self($characterQuest, array(1), array(1 => array(3, 6)), array(3 => 1, 6 => 7));
	 * 
	 * Decimos que debemos llevarle al npc con el id 1, los objetos
	 * con los id 3 y 6. La cantidad del objeto con el id 3 debe ser 1, 
	 * y la cantidad del objeto con id 6 debe ser 7.
	 * 
	 * @param CharacterQuest $characterQuest
	 * @param Array $npcsToTalk
	 * @param Array $itemsToGive
	 * @param Array $amount
	 */
	public function __construct(CharacterQuest $characterQuest, $npcsToTalk, $itemsToGive, $amount)
	{
		parent::__construct($characterQuest, $npcsToTalk);
		$this->itemsToGive = $itemsToGive;
		$this->amount = $amount;
	}
}