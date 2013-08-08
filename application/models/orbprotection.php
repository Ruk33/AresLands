<?php

class OrbProtection extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'orb_protections';
	public static $key = 'id';

	public function get_attacker()
	{
		return $this->belongs_to('Character', 'attacker_id');
	}

	public function get_target()
	{
		return $this->belongs_to('Character', 'target_id');
	}

	public static function add_protection($attacker, $target, $time)
	{
		$protection = OrbProtection::where('attacker_id', '=', $attacker->id)->where('target_id', '=', $target->id)->first();
		
		if ( ! $protection )
		{
			$protection = new OrbProtection();

			$protection->attacker_id = $attacker->id;
			$protection->target_id = $target->id;
		}

		$protection->time = time() + $time;

		$protection->save();
	}
}