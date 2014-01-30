<?php

class CharacteristicClumsy implements ICharacteristic
{
	public function get_bonusses() {
		return array(
			'Tu suerte aumenta drásticamente'
		);
	}

	public function get_description() {
		return 'Eres muy torpe, ¡pero con mucha suerte!.';
	}

	public function get_name() {
		return 'Torpe';
	}

	public function get_skills() {
		return array(
			68,
			69
		);
	}
}