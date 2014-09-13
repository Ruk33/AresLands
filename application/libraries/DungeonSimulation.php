<?php

class DungeonSimulation extends Dungeon
{
    public $dungeonLevel;
    
    public function can_be_king(Character $character)
    {
        return false;
    }
    
    public function after_battle(Character $character, DungeonLevel $dungeonLevel)
    {
        
    }
    
    public function do_progress(Character $character, DungeonLevel $dungeonLevel)
    {
        
    }
    
    /**
     * 
     * @return DungeonLevelSimulation
     */
    public function getLevel()
    {
        return $this->dungeonLevel;
    }
    
    public function levels()
    {
        return array($this->dungeonLevel);
    }
    
    /**
     * 
     * @param DungeonLevelSimulation $dungeonLevel
     */
    public function __construct(DungeonLevelSimulation $dungeonLevel = null)
    {
        $this->dungeonLevel = $dungeonLevel ?: new DungeonLevelSimulation();
        $this->dungeonLevel->setDungeon($this);
    }
}
