<?php namespace Tests;

use Character;
use Mockery;

/**
 * @group Character
 */
class CharacterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Character
     */
    protected $character;

    public function setUp()
    {
        $this->character = new Character();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testFinishQuest()
    {
        $this->character->completed_quests = 0;

        $quest = Mockery::mock("\\Quest");

        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->character->setQuery($query);

        // Aseguramos (aproximadamente ya que este metodo es llamado dentro
        // de save) que estemos llamando el metodo save()
        $query->shouldReceive("insert_get_id")->once();

        $achievementProgress = Mockery::mock("Models\\Achievement\\AchievementCharacterProgress");
        $this->character->setAchievementCharacterProgressRepository($achievementProgress);

        $achievementProgress->shouldReceive("completedQuest")->once()->with($this->character, $quest);

        $this->character->afterFinishQuest($quest);

        $this->assertEquals($this->character->getCompletedQuestsAmount(), 1);
    }

    public function testWinPvp()
    {
        $this->character->pvps = 0;

        $target = Mockery::mock("\\Character");

        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->character->setQuery($query);

        // Aseguramos (aproximadamente ya que este metodo es llamado dentro
        // de save) que estemos llamando el metodo save()
        $query->shouldReceive("insert_get_id")->once();

        $achievementProgress = Mockery::mock("Models\\Achievement\\AchievementCharacterProgress");
        $this->character->setAchievementCharacterProgressRepository($achievementProgress);

        $achievementProgress->shouldReceive("winPvp")
                            ->once()
                            ->with($this->character, $target);

        $this->character->afterWinPvp($target);

        $this->assertEquals($this->character->getPvps(), 1);
    }

    public function testWinPve()
    {
        $this->character->pves = 0;

        $target = Mockery::mock("\\Monster");

        $query = Mockery::mock("Laravel\\Database\\Eloquent\\Query");
        $this->character->setQuery($query);

        // Aseguramos (aproximadamente ya que este metodo es llamado dentro
        // de save) que estemos llamando el metodo save()
        $query->shouldReceive("insert_get_id")->once();

        $achievementProgress = Mockery::mock("Models\\Achievement\\AchievementCharacterProgress");
        $this->character->setAchievementCharacterProgressRepository($achievementProgress);

        $achievementProgress->shouldReceive("winPve")
            ->once()
            ->with($this->character, $target);

        $this->character->afterWinPve($target);

        $this->assertEquals($this->character->getPves(), 1);
    }

    public function testGetCompleteQuestAmount()
    {
        $this->character->completed_quests = 50;
        $this->assertEquals($this->character->getCompletedQuestsAmount(), 50);
    }

    public function testGetPvps()
    {
        $this->character->pvps = 50;
        $this->assertEquals($this->character->getPvps(), 50);
    }

    public function testGetPves()
    {
        $this->character->pves = 50;
        $this->assertEquals($this->character->getPves(), 50);
    }
}