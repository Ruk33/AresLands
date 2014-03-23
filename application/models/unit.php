<?php

abstract class Unit extends Base_Model
{
	/**
	 * Obtenemos la vida actual final (sumando atributos).
	 *
	 * @return float
	 */
	abstract public function get_current_life();

	/**
	 * @param float $value
	 */
	abstract public function set_current_life($value);

	/**
	 * Verificamos si unidad tiene buff (skill)
	 *
	 * @param integer $skillId
	 * @return boolean
	 */
	public function has_skill($skillId)
	{
		return false;
	}
}