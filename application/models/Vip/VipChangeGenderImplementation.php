<?php namespace Models\Vip;

use Character;

class VipChangeGenderImplementation implements VipImplementation
{
    /**
     *
     * @var Character
     */
    protected $entity;
    
    /**
     *
     * @var array
     */
    protected $attributes;
    
    public function __construct(Character $entity, array $attributes)
    {
        $this->entity = $entity;
        $this->attributes = $attributes;
    }

    public function execute()
    {
        if ($this->entity->gender == "male") {
            $this->entity->gender = "female";
        } else {
            $this->entity->gender = "male";
        }
        
        return $this->entity->save();
    }

    public function getInputs()
    {
        return "";
    }

    public function getValidator()
    {
        return \Laravel\Validator::make(
            $this->attributes, 
            array(),
            array()
        );
    }

}