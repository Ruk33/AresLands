<?php

abstract class Damage
{
	/**
	 * Hacemos un daño especificando cantidad y si es magico o no
	 *
	 * @param Attackable $source
	 * @param Attackable $target
	 * @param float      $amount
	 * @param boolean    $isMagic
	 *
	 * @return float
	 */
	public static function to_target(Attackable $source, Attackable $target, $amount, $isMagic)
	{
		$damage = max(0, $amount - $target->get_armor($isMagic));
        $damage = min($damage, $target->get_current_life());
        
		$target->set_current_life($target->get_current_life() - $damage);

		return $damage;
	}

	/**
	 * Hacemos el daño de un ataque normal
	 *
	 * @param Attackable $source
	 * @param Attackable $target
	 *
	 * @return float
	 */
	public static function normal(Attackable $source, Attackable $target)
	{
		return self::to_target($source, $target, $source->get_damage(), $source->attacks_with_magic());
	}
}