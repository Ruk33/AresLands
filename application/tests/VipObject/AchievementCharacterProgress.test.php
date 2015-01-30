<?php namespace Tests;

use Models\Achievement\AchievementCharacterProgress;
use Models\Achievement\Achievement;
use Character;
use Mockery;

/**
 * @group achievement_character_progress
 */
class AchievementCharacterProgressTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var AchievementCharacterProgress
     */
    protected $progress;
    
    public function setUp()
    {
        $character = Mockery::mock("Character");
        $achievement = Mockery::mock("Achievement");
        
        $this->progress = new AchievementCharacterProgress($character, $achievement);
    }
    
    public function testCheckQuestCompleted()
    {
        
    }
}
