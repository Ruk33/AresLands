<?php

class Orb_Task
{
	public function run($arguments)
	{
		$orbs = Orb::where_not_null('owner_character')->get();

		foreach ( $orbs as $orb )
		{
			$orb->give_periodic_reward();
		}
	}
}