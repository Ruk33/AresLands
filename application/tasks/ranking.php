<?php

class Ranking_Task
{
	public function run($arguments)
	{
		$rankingClans = ClanOrbPoint::order_by('points', 'desc')->get();
		$multiplier = 3;
		
		foreach ( $rankingClans as $rankingClan )
		{
			if ( $multiplier != 0 && $rankingClan->points > 0 )
			{
				$clan = $rankingClan->clan;
				$members = $clan->members()->select(array('id', 'clan_id'))->get();

				foreach ( $members as $member )
				{
					$reward = $member->level * $multiplier * 500;
					$member->add_coins($reward);
					Message::group_tournament($member, 4 - $multiplier, Item::get_divided_coins($reward)['text']);
				}

				$multiplier--;
			}
			
			$rankingClan->points = 0;
			$rankingClan->save();
		}
	}
}