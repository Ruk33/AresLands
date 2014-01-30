<?php

class CharacteristicShy implements ICharacteristic
{
	public function get_bonusses() {
		return array(
			'Aumentas tu evasión',
			'Aumentas tu posibilidad de crítico',
			'Los demás personajes no pueden saber cuál es tu raza'
		);
	}

	public function get_description() {
		return 'Eres temeroso, no te relacionas mucho con los demás. Esto te deja tiempo para pensar en nuevas técnicas de batalla.';
	}

	public function get_name() {
		return 'Timido';
	}

	public function get_skills() {
		return array(
			64,
			65
		);
	}
}