<?php

class Damage
{
	const ATTACK_TYPE_NORMAL = 1;
	const ATTACK_TYPE_MAGIC = 2;
	
	const DAMAGE_TYPE_NORMAL = 1;
	const DAMAGE_TYPE_MAGIC = 2;
	
	public static function unitDamageTarget(Eloquent $attacker, Eloquent $target, $amount, $attackType, $damageType, $weaponType)
	{
		
	}
}