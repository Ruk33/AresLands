<?php namespace Tests\Achievement;

use PHPUnit_Framework_TestCase;
use Models\Achievement\Achievement;
use Models\Achievement\AchievementRepository;
use Mockery;
use Libraries\Config;

/**
 * @group Achievement/Achievement
 */
class AchievementTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Achievement
     */
    protected $achievement;
    
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
    
    public function setUp()
    {
        $this->achievement = new Achievement();
        
        $this->questRepository = Mockery::mock("\Quest");
        $this->zoneRepository = Mockery::mock("\Zone");
        
        $this->achievement->setQuestRepository($this->questRepository);
        $this->achievement->setZoneRepository($this->zoneRepository);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetRewards()
    {
        $this->assertEquals($this->achievement->getRewards(), array());

        $itemRepository = Mockery::mock("\\Item");
        $this->achievement->setItemRepository($itemRepository);

        // recompensas punto de talento
        $this->achievement->reward_talent_points = 5;

        $itemRepository->shouldReceive("find")->once()->with(Config::get("game.talent_point_item_id"))->andReturnSelf();
        $this->assertEquals($this->achievement->getRewards(), array($itemRepository));

        $this->achievement->reward_talent_points = 0;

        // recompensas experiencia de clan
        $this->achievement->reward_clan_xp = 15;

        $itemRepository->shouldReceive("find")->once()->with(Config::get("game.clan_xp_item_id"))->andReturnSelf();
        $this->assertEquals($this->achievement->getRewards(), array($itemRepository));

        $this->achievement->reward_clan_xp = 0;

        // recompensas moneda
        $this->achievement->reward_coins = 50;

        $itemRepository->shouldReceive("find")->once()->with(Config::get("game.coin_id"))->andReturnSelf();
        $this->assertEquals($this->achievement->getRewards(), array($itemRepository));

        $this->achievement->reward_coins = 0;

        // recompensa experiencia
        $this->achievement->reward_xp = 100;

        $itemRepository->shouldReceive("find")->once()->with(Config::get("game.xp_item_id"))->andReturnSelf();
        $this->assertEquals($this->achievement->getRewards(), array($itemRepository));

        $this->achievement->reward_xp = 0;
    }

    public function testGetters()
    {
        $this->achievement->name = 'Lorem';
        $this->assertEquals($this->achievement->getName(), 'Lorem');
        
        $this->achievement->description = 'Something cool';
        $this->assertEquals($this->achievement->getDescription(), 'Something cool');
        
        $this->achievement->icon = '/img/something.jpg';
        $this->assertEquals($this->achievement->getIcon(), \URL::base() . $this->achievement->icon);
        
        $this->achievement->required_exploring_time = 5;
        $this->assertEquals($this->achievement->getExploringTimeRequired(), 5);
        
        $this->achievement->required_level = 9;
        $this->assertEquals($this->achievement->getLevelToBeReached(), 9);
        
        $this->achievement->required_pves = 15;
        $this->assertEquals($this->achievement->getPveWinNumberRequired(), 15);
        
        $this->achievement->required_pvps = 14;
        $this->assertEquals($this->achievement->getPvPWinNumberRequired(), 14);
        
        $this->achievement->required_rank = 4;
        $this->assertEquals($this->achievement->getRankToBeReached(), 4);

        $this->achievement->required_quests = 50;
        $this->assertEquals($this->achievement->getQuestAmountToComplete(), 50);
    }
    
    public function testTasks()
    {
        $this->assertEquals($this->achievement->mustBuyVipObject(), false);
        $this->achievement->required_vip = 1;
        $this->assertEquals($this->achievement->mustBuyVipObject(), true);
        
        $this->assertEquals($this->achievement->mustCompleteQuest(), false);
        $this->achievement->required_quest_id = 3;
        $this->assertEquals($this->achievement->mustCompleteQuest(), true);
        $this->achievement->required_quest_id = 0;
        $this->achievement->required_quests = 3;
        $this->assertEquals($this->achievement->mustCompleteQuest(), true);
        
        $this->assertEquals($this->achievement->mustExplore(), false);
        $this->achievement->required_exploration_time = 33;
        $this->assertEquals($this->achievement->mustExplore(), true);
        
        $this->assertEquals($this->achievement->mustGetOrb(), false);
        $this->achievement->requires_orb = 1;
        $this->assertEquals($this->achievement->mustGetOrb(), true);
        
        $this->assertEquals($this->achievement->mustLevelUp(), false);
        $this->achievement->required_level = 5;
        $this->assertEquals($this->achievement->mustLevelUp(), true);
        
        $this->assertEquals($this->achievement->mustReachRank(), false);
        $this->achievement->required_rank = 7;
        $this->assertEquals($this->achievement->mustReachRank(), true);
        
        $this->assertEquals($this->achievement->mustTravel(), false);
        $this->achievement->travel_zone_id = 2;
        $this->assertEquals($this->achievement->mustTravel(), true);
        
        $this->assertEquals($this->achievement->mustKillMonster(), false);
        $this->achievement->kill_npc = 5;
        $this->assertEquals($this->achievement->mustKillMonster(), true);
        
        $this->assertEquals($this->achievement->mustWinPve(), false);
        $this->achievement->required_pves = 3;
        $this->assertEquals($this->achievement->mustWinPve(), true);
        
        $this->assertEquals($this->achievement->mustWinPvP(), false);
        $this->achievement->required_pvps = 8;
        $this->assertEquals($this->achievement->mustWinPvP(), true);
    }
    
    public function testQuest()
    {
        $this->achievement->required_quest_id = 5;
        $this->questRepository->shouldReceive('find')->once()->with(5)->andReturnSelf();
        
        $this->assertEquals($this->achievement->getQuestToBeCompleted(), $this->questRepository);
    }
    
    public function testZone()
    {
        $this->achievement->travel_zone_id = 6;
        $this->achievement->zone_explore_id = 7;
        
        $this->zoneRepository->shouldReceive('find')->once()->with(6)->andReturnSelf();
        $this->zoneRepository->shouldReceive('find')->once()->with(7)->andReturnSelf();
        
        $this->assertEquals($this->achievement->getZoneToTravel(), $this->zoneRepository);
        $this->assertEquals($this->achievement->getZoneToExplore(), $this->zoneRepository);
    }
    
    public function testGetAchievementType()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->achievement->setQuery($query);
        
        // Tipo invalido
        $this->assertEquals($this->achievement->getType(-1), $query);

        // Nivel
        $query->shouldReceive("where")->once()->with("required_level", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_LEVEL_UP), $query);
        
        // Exploracion
        $query->shouldReceive("where")->once()->with("required_exploration_time", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_EXPLORATION), $query);
        
        // Matar monstruo
        $query->shouldReceive("where")->once()->with("kill_npc", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_KILL_MONSTER), $query);
        
        // Orbe
        $query->shouldReceive("where")->once()->with("requires_orb", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_ORB), $query);
        
        // Completar quest
        $query->shouldReceive("where")->once()->with("required_quest_id", ">", 0)->andReturnSelf();
        $query->shouldReceive("or_where")->once()->with("required_quests", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_QUEST_COMPLETED), $query);
        
        // Rank
        $query->shouldReceive("where")->once()->with("required_rank", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_RANK), $query);
        
        // Alcanzar nivel
        $query->shouldReceive("where")->once()->with("required_level", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_REACH_LEVEL), $query);
        
        // Viajar
        $query->shouldReceive("where")->once()->with("travel_zone_id", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_TRAVEL), $query);
        
        // VIP
        $query->shouldReceive("where")->once()->with("required_vip", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_VIP), $query);
        
        // PvE
        $query->shouldReceive("where")->once()->with("required_pves", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_WIN_PVE), $query);
        
        // PvP
        $query->shouldReceive("where")->once()->with("required_pvps", ">", 0)->andReturnSelf();
        $this->assertEquals($this->achievement->getType(AchievementRepository::TYPE_WIN_PVP), $query);
    }

    public function testGetAchievementsFromQuest()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $quest = Mockery::mock("\\Quest");

        $this->achievement->setQuery($query);

        $quest->shouldReceive("get_id")->once()->andReturn(5);
        $query->shouldReceive("where_required_quest_id")->once()->with(5)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromQuest($quest), $query);
    }

    public function testGetAchievementsFromQuestAmount()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->achievement->setQuery($query);

        $query->shouldReceive("where")->once()->with("required_quests", "<=", 50)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromQuestAmount(50), $query);
    }

    public function testGetAchievementsFromMonster()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $monster = Mockery::mock("\\Monster");

        $this->achievement->setQuery($query);

        $monster->shouldReceive("get_id")->once()->andReturn(5);
        $query->shouldReceive("where_kill_npc")->once()->with(5)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromMonster($monster), $query);
    }

    public function testGetAchievementsFromZone()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $zone = Mockery::mock("\\Zone");

        $this->achievement->setQuery($query);

        $zone->shouldReceive("get_id")->twice()->andReturn(5);

        $query->shouldReceive("where_travel_zone_id")->once()->with(5)->andReturnSelf();
        $query->shouldReceive("or_where_zone_explore_id")->once()->with(5)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromZone($zone), $query);
    }

    public function testGetAchievementsFromOrb()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $orb = Mockery::mock("\\Orb");

        $this->achievement->setQuery($query);

        $orb->shouldReceive("get_id")->once()->andReturn(5);

        $query->shouldReceive("where_requires_orb")->once()->with(5)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromOrb($orb), $query);
    }

    public function testGetFromLevel()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->achievement->setQuery($query);

        $query->shouldReceive("where_required_level")->once()->with(5)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromLevel(5), $query);
    }

    public function testGetFromPves()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->achievement->setQuery($query);

        $query->shouldReceive("where")->once()->with('required_pves', '<=', 5)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromPveAmount(5), $query);
    }

    public function testGetFromPvps()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->achievement->setQuery($query);

        $query->shouldReceive("where")->once()->with('required_pvps', '<=', 5)->andReturnSelf();

        $this->assertEquals($this->achievement->getAchievementsFromPvpAmount(5), $query);
    }
}