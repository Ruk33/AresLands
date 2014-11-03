<?php namespace Models\Vip;

use Character;
use Laravel\Form;
use Laravel\Validator;

class VipChangeRaceImplementation implements VipImplementation
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
        $this->entity->race = $this->attributes['race'];
        return $this->entity->save();
    }

    public function getInputs()
    {
        $races = array(
            "dwarf" => "Enano", 
            "human" => "Humano", 
            "elf" => "Elfo", 
            "drow" => "Drow"
        );
        
        unset($races[$this->entity->race]);
        
        return Form::label("race", "Raza") . 
               Form::select("race", $races, "", array("class" => "span12"));
    }

    public function getValidator()
    {
        return Validator::make(
            $this->attributes,
            array(
                "race" => "in:dwarf,elf,human,drow|not_in:{$this->entity->race}",
            ),
            array(
                "race_in" => "La raza no es valida",
                "race_not_in" => "Elige otra raza"
            )
        );
    }

}