<?php

class VipChangeRace extends VipObject
{
	public function getName()
	{
		return 'Cambio de raza';
	}
	
	public function getIcon()
	{
		return URL::base() . '/img/icons/vip/change_race.jpg';
	}
	
	public function getDescription()
	{
		return 'Cambias la raza a tu personaje.';
	}

	public function getPrice()
	{
		return 175;
	}
	
	public function execute()
	{
		if ($this->buyer) {
			$this->buyer->race = $this->attributes['race'];
			return $this->buyer->save();
		}
        
        return false;
	}
    
    public function getValidator()
    {
        return Laravel\Validator::make(
            $this->attributes,
            array(
                "race" => "in:dwarf,elf,human,drow|not_in:{$this->buyer->race}",
            ),
            array(
                "race_in" => "La raza no es valida",
                "race_not_in" => "Elige otra raza"
            )
        );
    }
    
    public function getInput()
    {
        $races = array(
            "dwarf" => "Enano", 
            "human" => "Humano", 
            "elf" => "Elfo", 
            "drow" => "Drow"
        );
        
        return "Raza " . 
               Form::select("race", $races, "", array("class" => "span12"));
    }
}