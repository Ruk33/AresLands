<?php namespace Libraries;

abstract class Trigger implements ITrigger
{
	/**
	 * @var Trigger
	 */
	protected static $instance;
	
	/**
	 * @return Trigger
	 */
	public static function getInstance()
	{
		if ( ! static::$instance )
		{
			static::$instance = new static;
		}
		
		return static::$instance;
	}
	
	public function onAcceptQuest(\Character $character, \Quest $quest) {
		
	}

	public function onAfterDamage(Damage $damage) {
		
	}

	public function onBeforeDamage(Damage $damage) {
		
	}

	public function onEquipItem(\Character $character, \CharacterItem $characterItem) {
		
	}

	public function onNpcTalk(\Character $character, \Npc $npc) {
		
	}

	public function onPveBattle(Battle $battle) {
		
	}

	public function onPvpBattle(Battle $battle) {
		
	}

	public function onUnEquipItem(\Character $character, \CharacterItem $characterItem) {
		
	}

	public function onBattleRest(\Character $character, \CharacterActivity $activity) {
		
	}

	public function onExplore(\Character $character, \CharacterActivity $activity) {
		
	}

	public function onTravel(\Character $character, \CharacterActivity $activity) {
		
	}
}