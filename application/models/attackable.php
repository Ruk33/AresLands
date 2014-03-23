<?php

abstract class Attackable extends Unit
{
	/**
	 * Obtenemos el ataque fisico/magico (del cual determinamos
	 * el daño minimo/maximo)
	 *
	 * @param bool $magical
	 * @return float
	 */
	abstract public function get_attack($magical = false);

	/**
	 * Obtenemos la resistencia fisica/magica
	 *
	 * @param bool $magical
	 * @return float
	 */
	abstract public function get_resistance($magical = false);

	/**
	 * Obtenemos el daño fisico/magico que refleja
	 *
	 * @param bool $magical
	 * @return float
	 */
	abstract public function get_reflected_damage($magical = false);

	/**
	 * Obtenemos la chance de evadir de la unidad
	 *
	 * @return float
	 */
	abstract public function get_evasion_chance();

	/**
	 * Obtenemos la chance de pegar con critica de la unidad
	 *
	 * @return float
	 */
	abstract public function get_critical_chance();

	/**
	 * Obtenemos el cooldown entre ataques
	 *
	 * @return float
	 */
	abstract public function get_cd();

	/**
	 * Actualizamos los tiempos de todos los skills activos
	 */
	abstract public function check_skills_time();

	/**
	 * Obtenemos un array con el id del objeto y la cantidad
	 * que se obtiene al derrotar a esta unidad
	 *
	 * @return Array
	 */
	abstract public function get_rewards();

	/**
	 * Verificamos si unidad ataca con magia
	 *
	 * @return boolean
	 */
	public function attacks_with_magic()
	{
		return $this->get_attack(true) > $this->get_attack(false);
	}

	/**
	 * Obtenemos el daño fisico/magico minimo
	 *
	 * @param bool $magical
	 * @return float
	 */
	public function get_min_damage($magical = false)
	{
		$minDamage = $this->get_attack($magical);

		if ( $magical )
		{
			$minDamage *= 1.25;
		}

		return max(0, $minDamage);
	}

	/**
	 * Obtenemos el daño fisico/magico maximo
	 *
	 * @param bool $magical
	 * @return float
	 */
	public function get_max_damage($magical = false)
	{
		$maxDamage = $this->get_attack($magical);

		if ( $magical )
		{
			$maxDamage *= 2.85;
		}
		else
		{
			$maxDamage *= 2.60;
		}

		return max($this->get_min_damage($magical), $maxDamage);
	}

	/**
	 * Obtenemos la armadura fisica/magica minima
	 *
	 * @param bool $magical
	 * @return float
	 */
	public function get_min_armor($magical = false)
	{
		return max(0, $this->get_resistance($magical) * 0.75);
	}

	/**
	 * Obtenemos la armadura fisica/magica maxima
	 *
	 * @param bool $magical
	 * @return float
	 */
	public function get_max_armor($magical = false)
	{
		return max($this->get_min_armor($magical), $this->get_resistance($magical) * 1.25);
	}

	/**
	 * Obtenemos el daño de la unidad
	 *
	 * @return float
	 */
	public function get_damage()
	{
		$magicAttack = $this->attacks_with_magic();
		$damage = mt_rand($this->get_min_damage($magicAttack), $this->get_min_damage($magicAttack)) + 1;

		// 40% chance maxima de crítico físico
		if ( ! $magicAttack && mt_rand(0, 100) <= min($this->get_critical_chance(), 40) )
		{
			$damage *= 1.35;
		}
		// 25% chance maxima de crítico mágico
		elseif ( $magicAttack && mt_rand(0, 100) <= min($this->get_critical_chance(), 25) )
		{
			$damage *= 2;
		}
		// 10% chance de golpe fallido
		elseif ( mt_rand(0, 100) <= 10 )
		{
			$damage *= 0.75;
		}

		return $damage;
	}

	/**
	 * Obtenemos la armadura para ataques fisicos/magicos
	 *
	 * @param bool $magical
	 * @return float
	 */
	public function get_armor($magical = false)
	{
		$defense = mt_rand($this->get_min_armor($magical), $this->get_max_armor($magical));

		// 30% de defensa exitosa
		if ( mt_rand(0, 100) <= 30 )
		{
			$defense *= 1.75;
		}
		// 10% de defensa fallida
		elseif ( mt_rand(0, 100) <= 10 )
		{
			$defense *= 0.75;
		}

		return $defense * 0.4;
	}
}