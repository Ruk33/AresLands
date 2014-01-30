<?php

class CharacteristicReserved implements ICharacteristic
{
	public function get_bonusses() {
		return array(
			'No muestras tus atributos'
		);
	}

	public function get_description() {
		return 'Eres muy reservado con tus cosas y compartes casi nada.';
	}

	public function get_name() {
		return 'Reservado';
	}

	public function get_skills() {
		return array(
			58,
			59
		);
	}
}