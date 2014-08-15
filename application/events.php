<?php

Event::listen('fullActivityBar', function(Character $character)
{
	Session::flash('modalMessage', 'activityBar');
});

Event::listen('loggedOfDayReward', function(Character $character)
{
	Session::flash('modalMessage', 'loggedOfDay');
});

Event::listen('npcTalk', function(Character $character, Npc $npc)
{
	$characterQuests = $character->quests_with_action('talk')
								 ->where_progress('started')
								 ->get();

	foreach ( $characterQuests as $characterQuest )
	{
		$characterQuest->talk_to_npc($npc);
	}
    
    // Damos la recompensa a aquellos que tenian completada la mision
    // antes de subir la actualizacion
    $characterQuests = $character->quests()
								 ->where_progress('reward')
								 ->get();
    
    foreach ( $characterQuests as $characterQuest )
	{
		$characterQuest->finish();
	}
});

Event::listen('acceptQuest', function(Character $character, Quest $quest)
{
	ActivityBar::add($character, 1);
});

Event::listen('unequipItem', function(CharacterItem $characterItem)
{
	
});

Event::listen('battle', function($character_one, $character_two, $winner = null)
{
	$character = $character_one;

	if ( Tournament::is_active() )
	{
		if ( $winner && $character_one )
		{
			$tournament = Tournament::get_active()->select(array('id'))->first();

			$characterOneScore = TournamentCharacterScore::get_score($tournament->id, $character_one->id)->first();
			$characterTwoScore = TournamentCharacterScore::get_score($tournament->id, $character_two->id)->first();
			
			if ( $characterOneScore && $characterTwoScore )
			{
				if ( $character_one->id == $winner->id )
				{
					$characterOneScore->register_victory_and_update_clan_score($tournament->id, $character_two);
					$characterTwoScore->register_lose_and_update_clan_score($tournament->id, $character_one);
				}
				else
				{
					$characterOneScore->register_lose_and_update_clan_score($tournament->id, $character_two);
					$characterTwoScore->register_victory_and_update_clan_score($tournament->id, $character_one);
				}

				$tournament->update_battle_counter();
			}
		}
	}
});

Event::listen('equipItem', function(CharacterItem $characterItem, $amount = 1)
{
	$character = Character::get_character_of_logged_user();

	/*
	 *	Nos aseguramos de que character
	 *	y el objeto existan
	 */
	if ( $character && $characterItem )
	{
		/*
		 *	Buscamos el objeto para 
		 *	confirmar que el usuario logueado
		 *	realmente lo tiene
		 */
		$item = $character->items()->select(array('id', 'item_id'))->find($characterItem->id);

		if ( $item )
		{
			/*
			 *	Obtenemos la información del objeto
			 */
			$item = $item->item()->select(array('id', 'skill'))->first();

			/*
			 *	Confirmamos que el objeto
			 *	en si, exista
			 */
			if ( $item )
			{
				/*
				 *	Nos fijamos si tiene una habilidad
				 */
				if ( $item->skill != '0' )
				{
					/*
					 *	Obtenemos las habilidades
					 */
					$skills = $item->get_skills();

					/*
					 *	¿No existen?
					 */
					if ( count($skills) > 0 )
					{
						$characterSkill = null;

						/*
						 *	Recorremos todas las habilidades
						 *	y las agregamos al registro
						 */
						foreach ( $skills as $skill )
						{
							if ( $skill['duration'] == -1 )
							{
								/*
								$data = $skill->data;

								if ( isset($data['heal_amount']) )
								{
									$character->current_life += $data['heal_amount'];
									$character->save();
								}
								*/
							}
							else
							{
								$characterSkill = new CharacterSkill();

								$characterSkill->skill_id = $skill['skill_id'];
								$characterSkill->character_id = $character->id;
								$characterSkill->level = $skill['level'];
								$characterSkill->end_time = ($skill['duration'] != 0) ? time() + $skill['duration'] : 0;
								$characterSkill->amount = $amount;

								$characterSkill->save();
							}
						}
					}
				}
			}
		}
	}
});

Event::listen('pveBattle', function(Character $character, Monster $monster, $winner)
{
	if ( $winner == $character )
	{
		$characterQuests = $character->quests_with_action('kill')
									 ->where('progress', '=', 'started')
									 ->get();

		foreach ( $characterQuests as $characterQuest )
		{
			$characterQuest->kill_npc($monster);
		}
	}
});

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function($exception)
{
	return Response::error('500');
});