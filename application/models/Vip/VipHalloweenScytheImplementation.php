<?php namespace Models\Vip;

use Laravel\Validator;
use Laravel\Config;
use Laravel\IoC;

class VipHalloweenScytheImplementation implements VipImplementation
{
    protected $entity;
    
    protected $attributes;
    
    protected $itemRepository;
    
    public function __construct(\Character $entity, array $attributes)
    {
        $this->entity = $entity;
        $this->attributes = $attributes;
    }
    
    public function setItemRepository(Item $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }
    
    protected function getItemRepository()
    {
        if (! $this->itemRepository) {
            $this->itemRepository = IoC::resolve("Item");
        }
        
        return $this->itemRepository;
    }

    public function execute()
    {
        $item = $this->getItemRepository()->find(13588);
        return $this->entity->add_item($item);
    }

    public function getInputs()
    {
        return "";
    }

    public function getValidator()
    {
        return Validator::make($this->attributes, array());
    }
}