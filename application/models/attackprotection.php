<?php

class AttackProtection extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'attack_protections';
	public static $key = 'id';

	public function get_attacker()
	{
		return $this->belongs_to('Character', 'attacker_id');
	}

	public function get_target()
	{
		return $this->belongs_to('Character', 'target_id');
	}

	public static function add($attacker, $target, $time)
	{
		$protection = AttackProtection::where('attacker_id', '=', $attacker->id)->where('target_id', '=', $target->id)->first();
		
		if ( ! $protection )
		{
			$protection = new AttackProtection();

			$protection->attacker_id = $attacker->id;
			$protection->target_id = $target->id;
			$protection->time = time();
		}

		$protection->time += $time;

		$protection->save();
	}
}