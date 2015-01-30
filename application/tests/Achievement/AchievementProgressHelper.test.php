<?php namespace Tests\Achievement;

use Mockery;
use Models\Achievement\AchievementProgressHelper;

/**
 * @group Achievement/AchievementProgressHelper
 */
class AchievementProgressHelperTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetFinalProgress()
    {
        $achievement = Mockery::mock("Models\\Achievement\\AchievementRepository");
        $progress = Mockery::mock("Models\\Achievement\\AchievementCharacterProgress");

        $progressHelper = new AchievementProgressHelper($achievement, $progress);

        $achievement->shouldReceive("mustExplore")->atLeast()->once()->andReturn(true, false);
        $achievement->shouldReceive("getExploringTimeRequired")->times(1)->andReturn(1);
        $this->assertEquals(1, $progressHelper->getFinalProgress());

        $achievement->shouldReceive("mustLevelUp")->atLeast()->once()->andReturn(true, false);
        $achievement->shouldReceive("getLevelToBeReached")->times(1)->andReturn(1);
        $this->assertEquals(1, $progressHelper->getFinalProgress());

        $achievement->shouldReceive("mustReachRank")->atLeast()->once()->andReturn(true, false);
        $achievement->shouldReceive("getRankToBeReached")->times(1)->andReturn(1);
        $this->assertEquals(1, $progressHelper->getFinalProgress());

        $achievement->shouldReceive("mustWinPve")->atLeast()->once()->andReturn(true, false);
        $achievement->shouldReceive("getPveWinNumberRequired")->times(1)->andReturn(1);
        $this->assertEquals(1, $progressHelper->getFinalProgress());

        $achievement->shouldReceive("mustWinPvp")->atLeast()->once()->andReturn(true, false);
        $achievement->shouldReceive("getPvpWinNumberRequired")->times(1)->andReturn(1);
        $this->assertEquals(1, $progressHelper->getFinalProgress());

        $achievement->shouldReceive("getQuestAmountToComplete")->atLeast()->once()->andReturn(5, 5, 0);
        $this->assertEquals(5, $progressHelper->getFinalProgress());

        // Cuando ya no se requieren los anteriores, los demas solamente utilizan 1
        $this->assertEquals(1, $progressHelper->getFinalProgress());
    }

    public function testGetCurrentProgress()
    {
        $achievement = Mockery::mock("Models\\Achievement\\AchievementRepository");
        $progress = Mockery::mock("Models\\Achievement\\AchievementCharacterProgress");
        $character = Mockery::mock("\\Character");

        $progressHelper = new AchievementProgressHelper($achievement, $progress);

        $achievement->shouldReceive("mustBuyVipObject")->atLeast()->once()->andReturn(true, false);
        $progress->shouldReceive("hasCompletedVipObject")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        $achievement->shouldReceive("mustCompleteQuest")->atLeast()->once()->andReturn(true, true, false);

        $achievement->shouldReceive("getQuestAmountToComplete")->twice()->andReturn(5, 0);
        $progress->shouldReceive("getCharacter")->once()->andReturn($character);
        $character->shouldReceive("getCompletedQuestsAmount")->once()->andReturn(2);
        $this->assertEquals(2, $progressHelper->getCurrentProgress());

        $progress->shouldReceive("hasCompletedQuest")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        // TODO
        $achievement->shouldReceive("mustExplore")->atLeast()->once()->andReturn(true, false);
        $this->assertEquals(0, $progressHelper->getCurrentProgress());

        $achievement->shouldReceive("mustGetOrb")->atLeast()->once()->andReturn(true, false);
        $progress->shouldReceive("hasCompletedOrb")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        $achievement->shouldReceive("mustKillMonster")->atLeast()->once()->andReturn(true, false);
        $progress->shouldReceive("hasCompletedKillNpc")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        $achievement->shouldReceive("mustLevelUp")->atLeast()->once()->andReturn(true, false);
        $progress->shouldReceive("getCharacter")->once()->andReturn($character);
        $character->shouldReceive("get_level")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        // TODO
        $achievement->shouldReceive("mustReachRank")->atLeast()->once()->andReturn(true, false);
        $this->assertEquals(0, $progressHelper->getCurrentProgress());

        $achievement->shouldReceive("mustTravel")->atLeast()->once()->andReturn(true, false);
        $progress->shouldReceive("hasCompletedTravel")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        $achievement->shouldReceive("mustWinPve")->atLeast()->once()->andReturn(true, false);
        $progress->shouldReceive("getCharacter")->once()->andReturn($character);
        $character->shouldReceive("getPves")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        $achievement->shouldReceive("mustWinPvp")->atLeast()->once()->andReturn(true, false);
        $progress->shouldReceive("getCharacter")->once()->andReturn($character);
        $character->shouldReceive("getPvps")->once()->andReturn(1);
        $this->assertEquals(1, $progressHelper->getCurrentProgress());

        // por defecto 0
        $this->assertEquals(0, $progressHelper->getCurrentProgress());
    }

    public function testGetPercentage()
    {
        $achievement = Mockery::mock("Models\\Achievement\\AchievementRepository");
        $progress = Mockery::mock("Models\\Achievement\\AchievementCharacterProgress");
        $character = Mockery::mock("\\Character");

        $achievement->shouldReceive("mustWinPve")->twice()->andReturn(true);
        $achievement->shouldReceive("getPveWinNumberRequired")->once()->andReturn(200);
        $achievement->shouldIgnoreMissing(false);

        $progress->shouldReceive("getCharacter")->once()->andReturn($character);

        $character->shouldReceive("getPves")->once()->andReturn(75);

        $progressHelper = new AchievementProgressHelper($achievement, $progress);

        $percentage = 75 * 100 / 200;
        $this->assertEquals($percentage, $progressHelper->getPercentage());
    }
}