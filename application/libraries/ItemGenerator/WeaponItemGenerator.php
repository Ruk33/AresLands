<?php namespace Libraries\ItemGenerator;

use Item;

/**
 * @todo
 */
abstract class WeaponItemGenerator extends ItemGenerator
{
    protected function getClass()
    {
        return 'weapon';
    }

    protected function getBodyPart()
    {
        return 'rhand';
    }
    
    /**
     * 
     * @param Item $item
     * @param integer $target
     */
    protected function getStatsForWarrior(Item $item, $target)
    {
        return array();
    }
    
    /**
     * 
     * @param Item $item
     * @param integer $target
     */
    protected function getStatsForWizard(Item $item, $target)
    {
        return array();
    }
    
    /**
     * 
     * @param Item $item
     * @param integer $target
     */
    protected function getStatsForMixed(Item $item, $target)
    {
        return array();
    }
    
    protected function getStats(Item $item, $target)
    {
        $stats = array();
        
        switch ($target) {
            case self::TARGET_WARRIOR:
                $stats = $this->getStatsForWarrior($item, $target);
                break;
            
            case self::TARGET_WIZARD:
                $stats = $this->getStatsForWizard($item, $target);
                break;
            
            case self::TARGET_MIXED:
                $stats = $this->getStatsForMixed($item, $target);
                break;
        }
        
        return $stats;
    }
} 