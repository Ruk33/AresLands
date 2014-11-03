<?php namespace Models\Vip;

use Character;
use Laravel\Validator;
use Laravel\Form;

class VipChangeNameImplementation implements VipImplementation
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
    
    public function execute()
    {
        $this->entity->name = $this->attributes['name'];
        return $this->entity->save();
    }

    public function getValidator()
    {
        return Validator::make(
            $this->attributes,
            array(
                "name" => "required|unique:characters|between:3,10|alpha_num"
            ),
            array(
                "name_required" => "El nombre es requerido",
                "name_unique" => "Ya existe otro personaje con ese nombre",
                "name_between" => "El nombre debe contener entre 3 y 10 caracteres",
                "name_alpha_num" => "El nombre es invalido, por favor solo letras y numeros"
            )
        );
    }
    
    public function getInputs()
    {
        return Form::label("name", "Nombre") . 
               Form::text("name", "", array("class" => "span12"));
    }

    public function __construct(Character $entity, array $attributes)
    {
        $this->entity = $entity;
        $this->attributes = $attributes;
    }
}