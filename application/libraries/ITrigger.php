<?php namespace Libraries;

use \Npc;
use \Character;
use \CharacterItem;
use \CharacterActivity;
use \Quest;

interface ITrigger
{
	/**
	 * Antes del daño
	 * @param \Libraries\Damage $damage
	 */
	public function onBeforeDamage(Damage $damage);
	
	/**
	 * Despues del daño
	 * @param \Libraries\Damage $damage
	 */
	public function onAfterDamage(Damage $damage);
	
	/**
	 * Cuando se habla con NPC
	 * @param Character $character
	 * @param Npc $npc
	 */
	public function onNpcTalk(Character $character, Npc $npc);
	
	/**
	 * Cuando se equipa objeto
	 * @param Character $character
	 * @param CharacterItem $characterItem
	 */
	public function onEquipItem(Character $character, CharacterItem $characterItem);
	
	/**
	 * Cuando se desquipa objeto
	 * @param Character $character
	 * @param CharacterItem $characterItem
	 */
	public function onUnEquipItem(Character $character, CharacterItem $characterItem);
	
	/**
	 * Al finalizar batalla contra monstruo
	 * @param \Libraries\Battle $battle
	 */
	public function onPveBattle(Battle $battle);
	
	/**
	 * Al finalizar batalla contra personaje
	 * @param \Libraries\Battle $battle
	 */
	public function onPvpBattle(Battle $battle);
	
	/**
	 * Al aceptar mision
	 * @param Character $character
	 * @param Quest $quest
	 */
	public function onAcceptQuest(Character $character, Quest $quest);
	
	/**
	 * Al viajar
	 * @param Character $character
	 * @param CharacterActivity $activity
	 */
	public function onTravel(Character $character, CharacterActivity $activity);
	
	/**
	 * Al explorar
	 * @param Character $character
	 * @param CharacterActivity $activity
	 */
	public function onExplore(Character $character, CharacterActivity $activity);
	
	/**
	 * Al descanzar de batalla
	 * @param Character $character
	 * @param CharacterActivity $activity
	 */
	public function onBattleRest(Character $character, CharacterActivity $activity);
}