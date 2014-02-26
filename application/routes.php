<?php

/*
|--------------------------------------------------------------------------
| Application Controllers
|--------------------------------------------------------------------------
*/

Route::controller('Authenticated');
Route::controller('Game');
Route::controller('CharacterCreation');
Route::controller('Home');
Route::controller('Api');
Route::controller('Chat');
Route::controller('Cron');
Route::controller('Admin');


/*
|--------------------------------------------------------------------------
| Application Events
|--------------------------------------------------------------------------
*/

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
	/*
	 *	No nos olvidamos de trabajar con los
	 *	triggers que tengan de evento 'npcTalk'
	 */
	$characterTriggers = $character->triggers()->where('event', '=', 'npcTalk')->select(array('id', 'class_name'))->get();
	$className = null;

	$obj;

	foreach ($characterTriggers as $characterTrigger) {
		$className = $characterTrigger->class_name;
		$obj = new $className($character);
		
		/*
		if ( $className::onNpcTalk($character, $npc) )
		{
			$characterTrigger->delete();
		}
		*/
		
		if ( $obj->run('npcTalk', $npc) )
		{
			$characterTrigger->delete();
		}
	}
});

Event::listen('acceptQuest', function(Character $character, Quest $quest)
{
	ActivityBar::add($character, 1);
	
	/*
	 *	No nos olvidamos de trabajar con los
	 *	triggers que tengan de evento 'acceptQuest'
	 */
	$characterTriggers = $character->triggers()->where('event', '=', 'acceptQuest')->select(array('id', 'class_name'))->get();
	$className = null;
	
	$obj;

	foreach ($characterTriggers as $characterTrigger) {
		$className = $characterTrigger->class_name;
		$obj = new $className($character);
		
		/*if ( $className::onAcceptQuest($character, $quest) )
		{
			$characterTrigger->delete();
		}*/
		
		if ( $obj->run('acceptQuest', $quest) )
		{
			$characterTrigger->delete();
		}
	}
});

Event::listen('unequipItem', function(CharacterItem $characterItem)
{
	$character = Character::get_character_of_logged_user();

	/*
	 *	Nos aseguramos de que character
	 *	y characterItem estén definidos y no
	 *	sean null
	 */
	if ( $character && $characterItem )
	{
		$item = $character->items()->select(array('item_id'))->find($characterItem->id);
		/*
		 *	Nos aseguramos de que el personaje
		 *	tenga el objeto
		 */
		if ( $item )
		{
			$item = $item->item()->select(array('skill'))->first();
			/*
			 *	Nos aseguramos de que el objeto
			 *	en si exista
			 */
			if ( $item )
			{
				/*
				 *	Nos fijamos si tiene una habilidad
				 */
				if ( $item->skill != '0-0' )
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
						 *	y las removemos del registro
						 */
						foreach ( $skills as $skill )
						{
							$characterSkill = $character->skills()->where('skill_id', '=', $skill['skill_id'])->where('level', '=', $skill['level'])->select(array('id'))->first();

							if ( $characterSkill )
							{
								$characterSkill->delete();
							}
						}
					}
				}

				/*
				 *	No nos olvidamos de trabajar con los
				 *	triggers que tengan de evento 'equipItem'
				 */
				$characterTriggers = $character->triggers()->where('event', '=', 'unequipItem')->select(array('id', 'class_name'))->get();
				$className = null;

				foreach ($characterTriggers as $characterTrigger) {
					$className = $characterTrigger->class_name;
					if ( $className::onEquipItem($item) )
					{
						$characterTrigger->delete();
					}
				}
			}
		}
	}
});

Event::listen('battle', function($character_one, $character_two, $winner = null)
{
	//$character = Character::get_character_of_logged_user();
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

	/*
	 *	No nos olvidamos de trabajar con los
	 *	triggers que tengan de evento 'battle'
	 */
	$characterTriggers = $character->triggers()->where('event', '=', 'battle')->select(array('id', 'class_name'))->get();
	$className = null;

	foreach ($characterTriggers as $characterTrigger) {
		$className = $characterTrigger->class_name;
		if ( $className::onBattle($character_one, $character_two) )
		{
			$characterTrigger->delete();
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

				/*
				 *	No nos olvidamos de trabajar con los
				 *	triggers que tengan de evento 'equipItem'
				 */
				$characterTriggers = $character->triggers()->where('event', '=', 'equipItem')->select(array('id', 'class_name'))->get();
				$className = null;

				foreach ($characterTriggers as $characterTrigger) {
					$className = $characterTrigger->class_name;
					if ( $className::onEquipItem($character, $item) )
					{
						$characterTrigger->delete();
					}
				}
			}
		}
	}
});

Event::listen('pveBattle', function(Character $character, Npc $monster, $winner)
{
	if ( $character && $monster )
	{
		/*
		 *	No nos olvidamos de trabajar con los
		 *	triggers que tengan de evento 'equipItem'
		 */
		$characterTriggers = $character->triggers()->where('event', '=', 'pveBattle')->select(array('id', 'class_name'))->get();
		$className = null;

		/*
		foreach ($characterTriggers as $characterTrigger) {
			$className = $characterTrigger->class_name;
			if ( $className::onPveBattle($character, $monster) )
			{
				$characterTrigger->delete();
			}
		}
		*/
		
		$obj;

		foreach ($characterTriggers as $characterTrigger) {
			$className = $characterTrigger->class_name;
			$obj = new $className($character);
			
			if ( $obj->run('pveBattle', $monster) )
			{
				$characterTrigger->delete();
			}
		}

		if ( $winner )
		{
			if ( $winner->id == $character->id )
			{
				$characterTriggers = $character->triggers()->where('event', '=', 'pveBattleWin')->select(array('id', 'class_name'))->get();
				$className = null;

				/*
				foreach ($characterTriggers as $characterTrigger) {
					$className = $characterTrigger->class_name;
					
					if ( $className::onPveBattleWin($character, $monster) )
					{
						$characterTrigger->delete();
					}
				}
				*/
				
				foreach ($characterTriggers as $characterTrigger) {
					$className = $characterTrigger->class_name;
					$obj = new $className($character);
					
					if ( $obj->run('pveBattleWin', $monster) )
					{
						$characterTrigger->delete();
					}
				}
			}
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


/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
*/

/*
 *	Antes de todo, verificamos
 *	si el usuario está logueado
 *	y traemos su personaje para
 *	guardarlo en session
 */
Route::filter('before', function() {
	if ( Config::get('application.maintenance') )
	{
		if ( Auth::guest() || Auth::user()->name != 'Ruke' )
		{
			return Response::error('503');
		}
	}
	
	$time = time();
	$isAuth = Auth::check();
	$requestUri = Request::uri();

	$grandOpeningDate = new DateTime("24 hours 28 February", new DateTimeZone("America/Argentina/Buenos_Aires"));
	if ( $time < $grandOpeningDate->getTimestamp() )
	{
		if ( ! $isAuth || Auth::user()->name != 'Ruke' )
		{
			return Response::view('chronometer.index', array('grandOpeningDate' => $grandOpeningDate));
		}
	}
	
	// Evitamos que estas acciones se ejecutan
	// si solamente necesitamos algo del chat
	// o api
	if ( substr($requestUri, 0, 4) != 'chat' && substr($requestUri, 0, 3) != 'api' )
	{
		Tournament::check_for_started();
		Tournament::check_for_finished();
		Tournament::check_for_potions();

		if ( $isAuth )
		{
			/*
			 *	Obtenemos al personaje logueado
			 */
			$character = Character::get_character_of_logged_user(array(
				'id',
				'name',
				'current_life',
				'max_life',
				'last_regeneration_time',
				'xp',
				'xp_next_level',
				'level',
				'points_to_change',
				'ip',
				'characteristics',
				'regeneration_per_second',
				'regeneration_per_second_extra'
			));

			if ( $character )
			{
				$character->check_skills_time();
				$character->regenerate_life();

				/*
				 *	Además vamos a actualizar tiempos
				 *	de sus actividades
				 */
				$characterActivities = $character->activities()->where('end_time', '<=', $time)->get();

				foreach ( $characterActivities as $characterActivity )
				{
					$characterActivity->update_time();
				}
				
				$ip = Request::ip();
				
				if ( ! $character->ip )
				{
					$character->ip = $ip;
				}
				else
				{
					if ( $character->ip != $ip )
					{
						Character::update_ip($character->ip, $ip);
					}
				}
				
				$character->save();
			}
		}
	}
	
	if ( $isAuth )
	{
		/*
		 * No importa si la consulta fue desde el chat,
		 * significa que el usuario todavía esta.
		 */
		$character = Character::get_character_of_logged_user(array('id', 'last_activity_time'));
		
		if ( $character )
		{
			if ( ! $character->last_activity_time || $time - $character->last_activity_time >= 300 )
			{
				$character->last_activity_time = $time;
			}

			$character->save();
		}
	}
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

/*
 *	No logueado
 */
Route::filter('auth', function($redirectTo = 'home/index')
{
	if ( Auth::guest() ) 
	{
		return Redirect::to($redirectTo);
	}
});

/*
 *	hard-coded, solo para salir del paso
 */
Route::filter('admin', function()
{
	if ( Auth::guest() || (Auth::user()->name != 'Ruke' && Auth::user()->name != 'Nerv'))
	{
		return Redirect::to('home/index');
	}
});

/*
 *	Logueado
 */
Route::filter('logged', function($redirectTo) {
	if ( Auth::check() ) 
	{
		return Redirect::to($redirectTo);
	}
});

/*
 *	¿No tiene personaje?
 */
Route::filter('hasNoCharacter', function($redirectTo = 'charactercreation/race')
{
	if ( ! Character::logged_user_has_character() ) 
	{
		return Redirect::to($redirectTo);
	}
});

/*
 *	¿Tiene personaje?
 */
Route::filter('hasCharacter', function($redirectTo = 'home/index')
{
	if ( Character::logged_user_has_character() )
	{
		return Redirect::to($redirectTo);
	}
});