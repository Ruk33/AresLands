<?php

abstract class Unit extends Base_Model
{
	/**
	 * Necesario que sea public por Eloquent
	 *
	 * @var float
	 */
	//public $current_life;

	/**
	 * @param float $value
	 */
	public function set_current_life($value)
	{
		$this->current_life = $value;
	}

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

	/**
	 * Obtenemos la vida actual final (sumando atributos).
	 *
	 * @return float
	 */
	abstract public function get_current_life();
}