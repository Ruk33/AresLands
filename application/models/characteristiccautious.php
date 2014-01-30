<?php

class CharacteristicCautious implements ICharacteristic
{
	public function get_bonusses() {
		return array(
			'Aumenta tu defensa',
			'Aumenta tu evasión',
			'Aumenta ligeramente tu vida'
		);
	}

	public function get_description() {
		return 'Eres muy cauteloso y analizas las situaciones antes de saltar hacia ellas.';
	}

	public function get_name() {
		return 'Cauto';
	}

	public function get_skills() {
		return array(
			72,
			73
		);
	}
}