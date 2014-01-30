<?php

class CharacteristicEnergetic implements ICharacteristic
{
	public function get_bonusses() {
		return array(
			'Tus puntos de vida se regeneran mas rápido',
			'Golpeas mas rápido',
			'Los tiempos de viaje/descanso se reducen'
		);
	}

	public function get_description() {
		return 'Eres un personaje enérgico, ¡no te gusta quedarte quieto!';
	}

	public function get_name() {
		return 'Energico';
	}

	public function get_skills() {
		return array(
			54,
			55
		);
	}
}