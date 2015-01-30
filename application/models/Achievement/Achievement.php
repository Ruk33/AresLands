<?php namespace Models\Achievement;

use Laravel\Database\Eloquent\Query;
use Laravel\IoC;
use Laravel\URL;
use Item;
use Libraries\Config;

/**
 * @property integer $id Primary key
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property integer $reward_coins
 * @property integer $reward_xp
 * @property integer $reward_talent_points
 * @property integer $reward_clan_xp
 * @property integer $required_quest_id
 * @property integer $required_level
 * @property integer $kill_npc Id del NPC que debe matar
 * @property integer $required_pvps
 * @property integer $required_pves
 * @property integer $required_rank
 * @property integer $required_vip
 * @property integer $travel_zone_id
 * @property integer $required_exploration_time
 * @property integer $zone_explore_id Id de la zona a explorar
 * @property boolean $requires_orb
 * @property integer $required_quests Cantidad de misiones a completar
 */
class Achievement extends \Base_Model implements AchievementRepository
{
    /**
     * @var string
     */
    public static $table = "achievements";

    /**
     *
     * @var \Quest
     */
    protected $questRepository;
    
    /**
     *
     * @var \Zone
     */
    protected $zoneRepository;

    /**
     * @param Item $itemRepository
     */
    public function setItemRepository(Item $itemRepository)
    {
        $this->setDependency("Item", $itemRepository);
    }

    /**
     * @return Item
     */
    protected function getItemRepository()
    {
        return $this->getDependency("Item");
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRewards()
    {
        $rewards = array();

        if ($this->reward_talent_points > 0) {
            $rewards[] = $this->getItemRepository()->find(Config::get("game.talent_point_item_id"));
        }

        if ($this->reward_clan_xp > 0) {
            $rewards[] = $this->getItemRepository()->find(Config::get("game.clan_xp_item_id"));
        }

        if ($this->reward_coins > 0) {
            $rewards[] = $this->getItemRepository()->find(Config::get("game.coin_id"));
        }

        if ($this->reward_xp > 0) {
            $rewards[] = $this->getItemRepository()->find(Config::get("game.xp_item_id"));
        }

        return $rewards;
    }

    public function getAchievementsFromQuestAmount($amount)
    {
        return $this->where('required_quests', '<=', $amount);
    }

    public function getAchievementsFromPvpAmount($pvp)
    {
        return $this->where('required_pvps', '<=', $pvp);
    }

    public function getAchievementsFromPveAmount($pve)
    {
        return $this->where('required_pves', '<=', $pve);
    }

    public function getAchievementsFromLevel($level)
    {
        return $this->where_required_level($level);
    }

    public function getAchievementsFromOrb(\Orb $orb)
    {
        return $this->where_requires_orb($orb->id);
    }

    public function getAchievementsFromTravel(\Zone $zone)
    {
        return $this->where_travel_zone_id($zone->id);
    }

    public function getAchievementsFromZone(\Zone $zone)
    {
        return $this->where_travel_zone_id($zone->id)
                    ->or_where_zone_explore_id($zone->id);
    }

    public function getAchievementsFromMonster(\Monster $monster)
    {
        return $this->where_kill_npc($monster->id);
    }

    public function getAchievementsFromQuest(\Quest $quest)
    {
        return $this->where_required_quest_id($quest->id);
    }

    public function getType($type)
    {
        $query = null;

        switch ($type) {
            case self::TYPE_LEVEL_UP:
                $query = $this->where('required_level', '>', 0);
                break;

            case self::TYPE_EXPLORATION:
                $query = $this->where('required_exploration_time', '>', 0);
                break;

            case self::TYPE_KILL_MONSTER:
                $query = $this->where('kill_npc', '>', 0);
                break;

            case self::TYPE_ORB:
                $query = $this->where('requires_orb', '>', 0);
                break;

            case self::TYPE_QUEST_COMPLETED:
                $query = $this->where('required_quest_id', '>', 0)
                              ->or_where('required_quests', '>', 0);
                break;

            case self::TYPE_RANK:
                $query = $this->where('required_rank', '>', 0);
                break;

            case self::TYPE_REACH_LEVEL:
                $query = $this->where('required_level', '>', 0);
                break;

            case self::TYPE_TRAVEL:
                $query = $this->where('travel_zone_id', '>', 0);
                break;

            case self::TYPE_VIP:
                $query = $this->where('required_vip', '>', 0);
                break;

            case self::TYPE_WIN_PVE:
                $query = $this->where('required_pves', '>', 0);
                break;

            case self::TYPE_WIN_PVP:
                $query = $this->where('required_pvps', '>', 0);
                break;

            default:
                $query = $this->getQuery();
        }

        return $query;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getDescription()
    {
        return $this->description;
    }

    public function getExploringTimeRequired()
    {
        return $this->required_exploring_time;
    }

    public function getIcon()
    {
        return URL::base() . $this->icon;
    }

    public function getLevelToBeReached()
    {
        return $this->required_level;
    }
    
    public function mustKillMonster()
    {
        return $this->kill_npc > 0;
    }

    public function getPveWinNumberRequired()
    {
        return $this->required_pves;
    }

    public function getPvpWinNumberRequired()
    {
        return $this->required_pvps;
    }

    /**
     * 
     * @param \Quest $questRepository
     */
    public function setQuestRepository(\Quest $questRepository)
    {
        $this->questRepository = $questRepository;
    }
    
    /**
     * @return \Quest
     */
    protected function getQuestRepository()
    {
        if (! $this->questRepository) {
            $this->setQuestRepository(IoC::resolve("Quest"));
        }
        
        return $this->questRepository;
    }

    public function getQuestToBeCompleted()
    {
        return $this->getQuestRepository()->find($this->required_quest_id);
    }

    public function getQuestAmountToComplete()
    {
        return $this->required_quests;
    }

    public function getRankToBeReached()
    {
        return $this->required_rank;
    }
    
    /**
     * 
     * @param \Zone $zoneRepository
     */
    public function setZoneRepository(\Zone $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }
    
    /**
     * @return \Zone
     */
    protected function getZoneRepository()
    {
        if (! $this->zoneRepository) {
            $this->setZoneRepository(IoC::resolve("Zone"));
        }
        
        return $this->zoneRepository;
    }

    public function getZoneToExplore()
    {
        return $this->getZoneRepository()->find($this->zone_explore_id);
    }

    public function getZoneToTravel()
    {
        return $this->getZoneRepository()->find($this->travel_zone_id);
    }

    public function mustBuyVipObject()
    {
        return $this->required_vip > 0;
    }

    public function mustCompleteQuest()
    {
        return $this->required_quest_id > 0 || $this->required_quests > 0;
    }

    public function mustExplore()
    {
        return $this->required_exploration_time > 0;
    }

    public function mustGetOrb()
    {
        return (bool) $this->requires_orb;
    }

    public function mustLevelUp()
    {
        return $this->required_level > 0;
    }

    public function mustReachRank()
    {
        return $this->required_rank > 0;
    }

    public function mustTravel()
    {
        return $this->travel_zone_id > 0;
    }

    public function mustWinPve()
    {
        return $this->required_pves > 0;
    }

    public function mustWinPvp()
    {
        return $this->required_pvps > 0;
    }
}