<?php

View::composer("layouts.default", function($view)
{
	if ( \Laravel\Auth::guest() )
	{
		return;
	}
	
	$character = IoC::resolve("Character")->get_logged();
	
	if ( ! $character )
	{
		return;
	}
	
	$startedQuests = array_merge(
		$character->started_quests()->get(), 
		$character->reward_quests()->get()
	 );

	$npcs = IoC::resolve("Merchant")->get_from_zone($character->zone, $character)->get();

	$tournament = null;

	if ( Tournament::is_active() )
	{
		$tournament = Tournament::get_active()->first();
	}
	else if ( Tournament::is_upcoming() )
	{
		$tournament = Tournament::get_upcoming()->first();
	}

	$view->coins = $character->get_divided_coins();
	$view->character = $character;
	$view->startedQuests = $startedQuests;
	$view->npcs = $npcs;
	$view->tournament = $tournament;
});

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
			$character = Character::get_character_of_logged_user(array_merge(
                Character::$COLUMNS_BASIC,
                Character::$COLUMNS_LIFE,
                Character::$COLUMNS_LOG_TIMES,
                Character::$COLUMNS_OTHER
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
	if (Request::forged()) {
        return Response::error('500');
    }
});

/*
 *	No logueado
 */
Route::filter('auth', function($redirectTo = '')
{
	if ( ! $redirectTo )
	{
		$redirectTo = URL::to_route("get_home_index");
	}
	
	if ( Auth::guest() ) 
	{
		return Redirect::to($redirectTo);
	}
});

Route::filter("hasClan", function()
{
	$character = IoC::resolve("Character");
	$loggedCharacter = $character->get_logged();
	
	if ( ! $loggedCharacter || ! $loggedCharacter->clan_id > 0 )
	{
		return Response::error("403");
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
Route::filter('logged', function() {
	if ( Auth::check() ) 
	{
		return Redirect::to_route("get_authenticated_index");
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