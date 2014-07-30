<?php

class CharacteristicBrave implements ICharacteristic
{
    public static function get_id()
    {
        return "valiente";
    }
    
	public function get_bonusses() {
		return array(
			'Tus ataques hacen mas daño',
			'Tu defensa aumenta',
			'Tus puntos de vida aumentan'
		);
	}

	public function get_description() {
		return 'No le temes a nada, y esto puede resultar a veces en que te metas en problemas.';
	}

	public function get_name() {
		return 'Valiente';
	}

	public function get_skills() {
		return array(
			62,
			63
		);
	}
}