<?php namespace Models\Achievement;

use Models\Vip\VipRepository;
use Quest;
use Character;
use Monster;
use Zone;
use Orb;

interface AchievementCharacterProgressRepository
{
    /**
     * Creamos una instancia, útil ya que nos garantiza una
     * instancia inclusive si el personaje no tiene ningun
     * progreso en el logro especificado
     *
     * @param Character $character
     * @param AchievementRepository $achievement
     * @return AchievementCharacterProgressRepository
     */
    public function getInstance(Character $character, AchievementRepository $achievement);

    /**
     * @return AchievementRepository
     */
    public function getAchievement();

    /**
     * @return \Character
     */
    public function getCharacter();

    /**
     * @return boolean
     */
    public function hasCompletedQuest();

    /**
     * @return boolean
     */
    public function hasCompletedLevelUp();

    /**
     * @return boolean
     */
    public function hasCompletedKillNpc();

    /**
     * @return boolean
     */
    public function hasCompletedRank();

    /**
     * @return boolean
     */
    public function hasCompletedVipObject();

    /**
     * @return boolean
     */
    public function hasCompletedTravel();

    /**
     * @return boolean
     */
    public function hasCompletedExplore();

    /**
     * @return boolean
     */
    public function hasCompletedOrb();

    /**
     * @return boolean
     */
    public function hasCompletedPvp();

    /**
     * @return boolean
     */
    public function hasCompletedPve();

    /**
     *
     */
    public function markAsCompleted();

    /**
     *
     * @param Character $character
     * @param Quest $quest
     */
    public function completedQuest(Character $character, Quest $quest);
    
    /**
     * @param Character $character
     */
    public function levelUp(Character $character);
    
    /**
     * @param Character $character
     * @param Character $target
     */
    public function winPvp(Character $character, Character $target);
    
    /**
     * @param Character $character
     * @param Monster $target
     */
    public function winPve(Character $character, Monster $target);
    
    /**
     * 
     */
    public function checkRank();
    
    /**
     * @param Character $character
     * @param VipRepository $vip
     */
    public function purchasedVipObject(Character $character, VipRepository $vip);
    
    /**
     * @param Character $character
     * @param Zone $zone
     */
    public function travel(Character $character, Zone $zone);
    
    /**
     * Cuando personaje termina de explorar se verifica si ha completado
     * algun logro
     *
     * @param Character $character
     * @param Zone $zone
     */
    public function explore(Character $character, Zone $zone);

    /**
     *
     * @param Character $character
     * @param Orb $orb
     */
    public function obtainedOrb(Character $character, Orb $orb);
    
    /**
     * @return boolean
     */
    public function isCompleted();
}
