<?php namespace Models\Achievement;

use Character;
use Laravel\IoC;
use Models\Vip\VipRepository;

/**
 * @property integer $id Primary key
 * @property integer $character_id Id del personaje
 * @property integer $achievement_id Id del logro
 * @property boolean $quest_completed
 * @property boolean $level_up_completed
 * @property boolean $npc_killed
 * @property boolean $rank_completed
 * @property boolean $vip_completed
 * @property boolean $travel_completed
 * @property boolean $explore_completed
 * @property boolean $orb_completed
 * @property boolean $pvp_completed
 * @property boolean $pve_completed
 * @property boolean $is_completed
 */
class AchievementCharacterProgress extends \Base_Model implements AchievementCharacterProgressRepository
{
    /**
     * @var string
     */
    public static $table = "character_achievements";

    /**
     * @var bool
     */
    public static $timestamps = false;

    /**
     * @var AchievementRepository
     */
    protected $achievementRepository;

    /**
     * @var AchievementCharacterProgressRepository
     */
    protected $achievementCharacterProgressRepository;

    public function getAchievement()
    {
        return $this->getAchievementRelationship()->first();
    }

    public function getCharacter()
    {
        return $this->getCharacterRelationship()->first();
    }

    public function hasCompletedQuest()
    {
        return (bool) $this->quest_completed;
    }

    public function hasCompletedLevelUp()
    {
        return (bool) $this->level_up_completed;
    }

    public function hasCompletedKillNpc()
    {
        return (bool) $this->npc_killed;
    }

    public function hasCompletedRank()
    {
        return (bool) $this->rank_completed;
    }

    public function hasCompletedVipObject()
    {
        return (bool) $this->vip_completed;
    }

    public function hasCompletedTravel()
    {
        return (bool) $this->travel_completed;
    }

    public function hasCompletedExplore()
    {
        return (bool) $this->explore_completed;
    }

    public function hasCompletedOrb()
    {
        return (bool) $this->orb_completed;
    }

    public function hasCompletedPvp()
    {
        return (bool) $this->completed_pvp;
    }

    public function hasCompletedPve()
    {
        return (bool) $this->completed_pve;
    }

    /**
     * @param AchievementCharacterProgressRepository $achievementCharacterProgressRepository
     */
    public function setAchievementCharacterProgressRepository(AchievementCharacterProgressRepository $achievementCharacterProgressRepository)
    {
        $this->achievementCharacterProgressRepository = $achievementCharacterProgressRepository;
    }

    /**
     * @return AchievementCharacterProgressRepository
     */
    protected function getAchievementCharacterProgressRepository()
    {
        if (! $this->achievementCharacterProgressRepository) {
            $this->setAchievementCharacterProgressRepository(IoC::resolve("Models\\Achievement\\AchievementCharacterProgressRepository"));
        }

        return $this->achievementCharacterProgressRepository;
    }

    public function getInstance(Character $character, AchievementRepository $achievement)
    {
        $instance = $this->getAchievementCharacterProgressRepository()
                         ->where_character_id($character->id)
                         ->where_achievement_id($achievement->getId())
                         ->first();

        if (! $instance) {
            $instance = new self(array(
                "character_id" => $character->id,
                "achievement_id" => $achievement->getId()
            ));
        }

        return $instance;
    }

    /**
     * @param AchievementRepository $achievementRepository
     */
    public function setAchievementRepository(AchievementRepository $achievementRepository)
    {
        $this->achievementRepository = $achievementRepository;
    }

    /**
     * @return AchievementRepository
     */
    protected function getAchievementRepository()
    {
        if (! $this->achievementRepository) {
            $this->setAchievementRepository(IoC::resolve("Models\\Achievement\\AchievementRepository"));
        }

        return $this->achievementRepository;
    }

    public function getAchievementRelationship()
    {
        return $this->belongs_to("Models\\Achievement\\Achievement", "achievement_id");
    }
    
    public function getCharacterRelationship()
    {
        return $this->belongs_to("Character", "character_id");
    }

    public function markAsCompleted()
    {
        foreach ($this->getAchievement()->getRewards() as $reward) {

        }

        $this->is_completed = true;
        $this->save();
    }
    
    public function checkRank()
    {
        
    }

    public function completedQuest(Character $character, \Quest $quest)
    {
        $achievements = $this->getAchievementRepository()
                             ->getAchievementsFromQuest($quest)
                             ->get();

        $achievements += $this->getAchievementRepository()
                              ->getAchievementsFromQuestAmount($character->getCompletedQuestsAmount())
                              ->get();

        $this->getAchievementCharacterProgressRepository()
             ->completeAchievements($character, $achievements);
    }

    public function explore(Character $character, \Zone $zone)
    {

    }

    public function isCompleted()
    {
        return (bool) $this->is_completed;
    }

    public function levelUp(Character $character)
    {
        $achievements = $this->getAchievementRepository()
                             ->getAchievementsFromLevel($character->level)
                             ->get();

        $this->getAchievementCharacterProgressRepository()
             ->completeAchievements($character, $achievements);
    }

    public function obtainedOrb(Character $character, \Orb $orb)
    {
        $achievements = $this->getAchievementRepository()
                             ->getAchievementsFromOrb($orb)
                             ->get();

        $this->getAchievementCharacterProgressRepository()
             ->completeAchievements($character, $achievements);
    }

    public function purchasedVipObject(Character $character, VipRepository $vip)
    {
        
    }

    public function travel(Character $character, \Zone $zone)
    {
        $achievements = $this->getAchievementRepository()
                             ->getAchievementsFromTravel($zone)
                             ->get();

        $this->getAchievementCharacterProgressRepository()
             ->completeAchievements($character, $achievements);
    }

    public function winPve(Character $character, \Monster $target)
    {
        $achievements = $this->getAchievementRepository()
                             ->getAchievementsFromMonster($target)
                             ->get();

        $achievements += $this->getAchievementRepository()
                              ->getAchievementsFromPveAmount($character->getPves())
                              ->get();

        $this->getAchievementCharacterProgressRepository()
             ->completeAchievements($character, $achievements);
    }

    public function winPvp(Character $character, Character $target)
    {
        $achievements = $this->getAchievementRepository()
                             ->getAchievementsFromPvpAmount($character->getPvps())
                             ->get();

        $this->getAchievementCharacterProgressRepository()
             ->completeAchievements($character, $achievements);
    }

    /**
     * @param Character $character
     * @param array $achievements
     */
    public function completeAchievements(Character $character, array $achievements)
    {
        foreach ($achievements as $achievement) {
            $this->getAchievementCharacterProgressRepository()
                 ->getInstance($character, $achievement)
                 ->markAsCompleted();
        }
    }
}
