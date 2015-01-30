<?php namespace Tests\Achievement;

use Models\Achievement\AchievementCharacterProgress;
use Mockery;
use Models\Achievement\AchievementCharacterProgressRepository;
use Models\Achievement\AchievementRepository;

/**
 * @group Achievement/AchievementCharacterProgress
 */
class AchievementCharacterProgressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AchievementRepository
     */
    protected $achievementRepository;

    /**
     * @var AchievementCharacterProgressRepository
     */
    protected $progressRepository;

    /**
     *
     * @var AchievementCharacterProgress
     */
    protected $progress;
    
    public function setUp()
    {
        $this->progress = new AchievementCharacterProgress();

        $this->achievementRepository = Mockery::mock("Models\\Achievement\\Achievement");
        $this->progressRepository = Mockery::mock("Models\\Achievement\\AchievementCharacterProgressRepository");

        $this->progress->setAchievementRepository($this->achievementRepository);
        $this->progress->setAchievementCharacterProgressRepository($this->progressRepository);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    // TODO
    public function testGetCharacter()
    {

    }

    public function testGetCharacterRelationship()
    {
        $this->assertInstanceOf(
            "Laravel\\Database\\Eloquent\\Relationships\\Belongs_To",
            $this->progress->getCharacterRelationship()
        );
    }

    public function testMarkAsCompleted()
    {
        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->progress->setQuery($query);

        // Aseguramos (aproximadamente ya que este metodo es llamado dentro
        // de save) que estemos llamando el metodo save()
        $query->shouldReceive("insert_get_id");

        $this->assertFalse($this->progress->isCompleted());
        $this->progress->markAsCompleted();
        $this->assertTrue($this->progress->isCompleted());
    }

    public function testGetInstance()
    {
        $character = Mockery::mock("\\Character");
        $achievement = Mockery::mock("Models\\Achievement\\AchievementRepository");

        $character->shouldReceive("get_id")->times(3)->andReturn(1);
        $achievement->shouldReceive("getId")->times(3)->andReturn(2);

        $this->progressRepository->shouldReceive("where_character_id")->twice()->with(1)->andReturnSelf();
        $this->progressRepository->shouldReceive("where_achievement_id")->twice()->with(2)->andReturnSelf();
        $this->progressRepository->shouldReceive("first")->twice()->andReturn($this->progress, null);

        $instance = $this->progress->getInstance($character, $achievement);

        $this->assertEquals($this->progress, $instance);

        $instance = $this->progress->getInstance($character, $achievement);

        $this->assertInstanceOf("Models\\Achievement\\AchievementCharacterProgressRepository", $instance);
        $this->assertEquals($instance->character_id, 1);
        $this->assertEquals($instance->achievement_id, 2);
    }

    public function testCompleteAchievements()
    {
        $character = Mockery::mock("\\Character");
        $achievements = array($this->achievementRepository);

        $this->progressRepository->shouldReceive("getInstance")->once()->with($character, $this->achievementRepository)->andReturnSelf();
        $this->progressRepository->shouldReceive("markAsCompleted")->once();

        $this->progress->completeAchievements($character, $achievements);
    }

    public function testCompletedQuest()
    {
        $character = Mockery::mock("\\Character");
        $quest = Mockery::mock("\\Quest");

        $this->achievementRepository->shouldReceive("getAchievementsFromQuest")->once()->with($quest)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn(array(1));

        $character->shouldReceive("getCompletedQuestsAmount")->once()->andReturn(999);

        $this->achievementRepository->shouldReceive("getAchievementsFromQuestAmount")->once()->with(999)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn(array(2));

        $this->progressRepository->shouldReceive("completeAchievements")->once()->with($character, array(1) + array(2));

        $this->progress->completedQuest($character, $quest);
    }

    public function testObtainedOrb()
    {
        $character = Mockery::mock("\\Character");
        $orb = Mockery::mock("\\Orb");
        $achievements = array($this->achievementRepository);

        $this->achievementRepository->shouldReceive("getAchievementsFromOrb")->once()->with($orb)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn($achievements);

        $this->progressRepository->shouldReceive("completeAchievements")->once()->with($character, $achievements);

        $this->progress->obtainedOrb($character, $orb);
    }

    public function testLevelUp()
    {
        $character = Mockery::mock("\\Character");
        $achievements = array($this->achievementRepository);

        $character->shouldReceive("get_level")->once()->andReturn(5);

        $this->achievementRepository->shouldReceive("getAchievementsFromLevel")->once()->with(5)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn($achievements);

        $this->progressRepository->shouldReceive("completeAchievements")->once()->with($character, $achievements);

        $this->progress->levelUp($character);
    }

    public function testTravel()
    {
        $character = Mockery::mock("\\Character");
        $zone = Mockery::mock("\\Zone");
        $achievements = array($this->achievementRepository);

        $this->achievementRepository->shouldReceive("getAchievementsFromTravel")->once()->with($zone)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn($achievements);

        $this->progressRepository->shouldReceive("completeAchievements")->once()->with($character, $achievements);

        $this->progress->travel($character, $zone);
    }

    public function testWinPve()
    {
        $character = Mockery::mock("\\Character");
        $target = Mockery::mock("\\Monster");

        $character->shouldReceive("getPves")->once()->andReturn(5);

        $this->achievementRepository->shouldReceive("getAchievementsFromMonster")->once()->with($target)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn(array(2));

        $this->achievementRepository->shouldReceive("getAchievementsFromPveAmount")->once()->with(5)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn(array(1));

        $this->progressRepository->shouldReceive("completeAchievements")->once()->with($character, array(2) + array(1));

        $this->progress->winPve($character, $target);
    }

    public function testWinPvp()
    {
        $character = Mockery::mock("\\Character");

        $character->shouldReceive("getPvps")->once()->andReturn(5);

        $this->achievementRepository->shouldReceive("getAchievementsFromPvpAmount")->once()->with(5)->andReturnSelf();
        $this->achievementRepository->shouldReceive("get")->once()->andReturn(array());

        $this->progressRepository->shouldReceive("completeAchievements")->once()->with($character, array());

        $this->progress->winPvp($character, $character);
    }

    public function testHasCompleted()
    {
        $this->assertFalse($this->progress->hasCompletedQuest());
        $this->progress->quest_completed = 1;
        $this->assertTrue($this->progress->hasCompletedQuest());

        $this->assertFalse($this->progress->hasCompletedLevelUp());
        $this->progress->level_up_completed = 1;
        $this->assertTrue($this->progress->hasCompletedLevelUp());

        $this->assertFalse($this->progress->hasCompletedKillNpc());
        $this->progress->npc_killed = 1;
        $this->assertTrue($this->progress->hasCompletedKillNpc());

        $this->assertFalse($this->progress->hasCompletedRank());
        $this->progress->rank_completed = 1;
        $this->assertTrue($this->progress->hasCompletedRank());

        $this->assertFalse($this->progress->hasCompletedVipObject());
        $this->progress->vip_completed = 1;
        $this->assertTrue($this->progress->hasCompletedVipObject());

        $this->assertFalse($this->progress->hasCompletedTravel());
        $this->progress->travel_completed = 1;
        $this->assertTrue($this->progress->hasCompletedTravel());

        $this->assertFalse($this->progress->hasCompletedExplore());
        $this->progress->explore_completed = 1;
        $this->assertTrue($this->progress->hasCompletedExplore());

        $this->assertFalse($this->progress->hasCompletedOrb());
        $this->progress->orb_completed = 1;
        $this->assertTrue($this->progress->hasCompletedOrb());

        $this->assertFalse($this->progress->hasCompletedPvp());
        $this->progress->completed_pvp = 1;
        $this->assertTrue($this->progress->hasCompletedPvp());

        $this->assertFalse($this->progress->hasCompletedPve());
        $this->progress->completed_pve = 1;
        $this->assertTrue($this->progress->hasCompletedPve());
    }
}
