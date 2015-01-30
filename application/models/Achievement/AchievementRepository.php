<?php namespace Models\Achievement;

use Laravel\Database\Eloquent\Query;

interface AchievementRepository
{
    const TYPE_LEVEL_UP = 1;
    const TYPE_QUEST_COMPLETED = 2;
    const TYPE_REACH_LEVEL = 3;
    const TYPE_KILL_MONSTER = 4;
    const TYPE_WIN_PVP = 5;
    const TYPE_WIN_PVE = 6;
    const TYPE_RANK = 7;
    const TYPE_VIP = 8;
    const TYPE_TRAVEL = 9;
    const TYPE_EXPLORATION = 10;
    const TYPE_ORB = 11;

    /**
     * Obtenemos un array con los objetos
     *
     * @return array
     */
    public function getRewards();

    /**
     * Obtenemos cantidad de misiones a completar
     *
     * @return integer
     */
    public function getQuestAmountToComplete();

    /**
     * Obtenemos todos los logros que requieran igual o menor
     * cantidad de PVPs
     *
     * @param integer $pvp
     * @return Query
     */
    public function getAchievementsFromPvpAmount($pvp);

    /**
     * Obtenemos todos los logros que requieran igual o menor cantidad
     * de PVEs
     *
     * @param integer $pve
     * @return Query
     */
    public function getAchievementsFromPveAmount($pve);

    /**
     * @param integer $level
     * @return Query
     */
    public function getAchievementsFromLevel($level);

    /**
     * @param \Orb $orb
     * @return Query
     */
    public function getAchievementsFromOrb(\Orb $orb);

    /**
     * Obtenemos logros por viajar a zona
     *
     * @param \Zone $zone
     * @return Query
     */
    public function getAchievementsFromTravel(\Zone $zone);

    /**
     * Obtenemos todos los logros que tengan que ver con una zona
     *
     * @param \Zone $zone
     * @return Query
     */
    public function getAchievementsFromZone(\Zone $zone);

    /**
     * Obtenemos todos los logros que tengan que ver con un monstruo
     *
     * @param \Monster $monster
     * @return Query
     */
    public function getAchievementsFromMonster(\Monster $monster);

    /**
     * Obtenemos todos los logros que tengan que ver con una mision
     *
     * @param \Quest $quest
     * @return Query
     */
    public function getAchievementsFromQuest(\Quest $quest);

    /**
     * Obtenemos todos los logros que requieran una cantidad de misiones completadas
     *
     * @param integer $amount
     * @return Query
     */
    public function getAchievementsFromQuestAmount($amount);

    /**
     * Obtenemos todos los logros que sean de tipo $type
     *
     * @param integer $type Ver constantes
     * @return Query
     */
    public function getType($type);

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return string
     */
    public function getDescription();
    
    /**
     * @return string
     */
    public function getIcon();
    
    /**
     * @return boolean
     */
    public function mustCompleteQuest();
    
    /**
     * @return \Quest
     */
    public function getQuestToBeCompleted();
    
    /**
     * @return boolean
     */
    public function mustLevelUp();
    
    /**
     * @return integer
     */
    public function getLevelToBeReached();
    
    /**
     * @return boolean
     */
    public function mustKillMonster();
    
    /**
     * @return boolean
     */
    public function mustWinPvp();
    
    /**
     * @return integer
     */
    public function getPvpWinNumberRequired();
    
    /**
     * @return boolean
     */
    public function mustWinPve();
    
    /**
     * @return integer
     */
    public function getPveWinNumberRequired();
    
    /**
     * @return boolean
     */
    public function mustReachRank();
    
    /**
     * @return integer
     */
    public function getRankToBeReached();
    
    /**
     * @return boolean
     */
    public function mustBuyVipObject();
    
    /**
     * @return boolean
     */
    public function mustTravel();
    
    /**
     * @return \Zone
     */
    public function getZoneToTravel();
    
    /**
     * @return boolean
     */
    public function mustExplore();
    
    /**
     * @return \Zone
     */
    public function getZoneToExplore();
    
    /**
     * @return integer
     */
    public function getExploringTimeRequired();
    
    /**
     * @return boolean
     */
    public function mustGetOrb();
}
