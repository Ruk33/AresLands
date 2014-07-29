<?php

class VipChangeName extends VipObject
{
	public function getName()
	{
		return 'Cambio de nombre';
	}
	
	public function getIcon()
	{
		return URL::base() . '/img/icons/vip/change_name.jpg';
	}
	
	public function getDescription()
	{
		return 'Cambias el nombre a tu personaje.';
	}

	public function getPrice()
	{
		return 125;
	}
	
	public function execute()
	{
		if ($this->buyer) {
			$this->buyer->name = $this->attributes['name'];
			return $this->buyer->save();
		}
        
        return false;
	}
    
    public function getValidator()
    {
        return Laravel\Validator::make(
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
    
    public function getInput()
    {
        return "Nombre " . Form::text("name", "", array("class" => "span12"));
    }
}