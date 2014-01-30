<?php

class CharacteristicAdventurous implements ICharacteristic
{
	public function get_bonusses() {
		return array(
			'Ganas mas recompensas',
			'Aumenta tu ataque',
			'Aumenta ligeramente tu crítico'
		);
	}

	public function get_description() {
		return 'No pierdes tiempo en balbucear, ¡siempre estás en busca de una nueva aventura!.';
	}

	public function get_name() {
		return 'Aventurero';
	}

	public function get_skills() {
		return array(
			70,
			71
		);
	}
}