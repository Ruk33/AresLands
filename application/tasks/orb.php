<?php

class Orb_Task
{
	public function run($arguments)
	{
		$orbs = Orb::where_not_null('owner_character')->select(array('id', 'coins', 'points', 'owner_character'))->get();
		$owner = null;

		foreach ( $orbs as $orb )
		{
			$orb->give_periodic_reward();
		}
	}
}