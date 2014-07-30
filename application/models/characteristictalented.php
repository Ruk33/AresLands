<?php

class CharacteristicTalented implements ICharacteristic
{
    public static function get_id()
    {
        return "talentoso";
    }
    
	public function get_bonusses() {
		return array(
			'Tu ataque aumenta reducidamente',
			'Tu defensa aumenta reducidamente'
		);
	}

	public function get_description() {
		return 'Tienes mucho potencial y talento en muchas disciplinas.';
	}

	public function get_name() {
		return 'Talentoso';
	}

	public function get_skills() {
		return array(
			66,
			67
		);
	}
}