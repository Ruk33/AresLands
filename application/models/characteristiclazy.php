<?php

class CharacteristicLazy implements ICharacteristic
{
    public static function get_id()
    {
        return "perezoso";
    }
    
	public function get_bonusses() {
		return array(
			'Golpeas mas fuerte... zZz',
			'Tienes una mayor probabilidad de... zZz... crítico',
			'Los tiempos de viaje/descanso aumentan'
		);
	}

	public function get_description() {
		return 'Te describiría cómo es tu personaje... pero me da pereza... zZz';
	}

	public function get_name() {
		return 'Perezoso';
	}

	public function get_skills() {
		return array(
			56,
			57
		);
	}
}