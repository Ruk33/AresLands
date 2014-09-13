<?php

class DungeonLevelSimulation extends DungeonLevel
{
    /**
     *
     * @var Unit
     */
    public $target;
    
    /**
     *
     * @var DungeonSimulation
     */
    public $dungeon;
    
    /**
     * 
     * @param Unit $target
     * @return DungeonLevelSimulation
     */
    public function setTarget(Unit $target)
    {
        $this->target = $target;
        return $this;
    }
    
    public function target()
    {
        return $this->target;
    }
    
    /**
     * 
     * @param DungeonSimulation $dungeon
     * @return \DungeonLevelSimulation
     */
    public function setDungeon(DungeonSimulation $dungeon)
    {
        $this->dungeon = $dungeon;
        return $this;
    }
    
    public function dungeon()
    {
        return $this->dungeon;
    }
}
