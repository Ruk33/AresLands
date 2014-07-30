<?php

class CharacteristicReliant implements ICharacteristic
{
    public static function get_id()
    {
        return "confiado";
    }
    
	public function get_bonusses() {
		return array(
			'Muestras tus atributos',
			'Golpeas mas fuerte'
		);
	}

	public function get_description() {
		return 'Eres confiado, te gusta decir las cosas de frente y no andar con vueltas.';
	}

	public function get_name() {
		return 'Confiado';
	}

	public function get_skills() {
		return array(
			60,
			61
		);
	}
}