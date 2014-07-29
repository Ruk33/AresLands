<?php

class VipChangeGender extends VipObject
{
	public function getName()
	{
		return 'Cambio de genero';
	}
	
	public function getIcon()
	{
		return URL::base() . '/img/icons/vip/change_gender.jpg';
	}
	
	public function getDescription()
	{
		return 'Cambias el genero de tu personaje.';
	}

	public function getPrice()
	{
		return 80;
	}
	
	public function execute()
	{
		if ($this->buyer) {
            if ($this->buyer->gender == "male") {
                $this->buyer->gender = "female";
            } else {
                $this->buyer->gender = "male";
            }
            
			return $this->buyer->save();
		}
        
        return false;
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