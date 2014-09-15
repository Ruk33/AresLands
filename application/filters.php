<?php

Route::filter('unfinishedDungeon', function()
{
    if (Laravel\IoC::resolve("Dungeon")->has_finished()) {
        return Laravel\Redirect::to_route("get_authenticated_index");
    }
});

/*
 *	Antes de todo, verificamos
 *	si el usuario está logueado
 *	y traemos su personaje para
 *	guardarlo en session
 */
Route::filter('before', function() {
	if (Config::get('application.maintenance')) {
		if (Auth::guest() || Auth::user()->name != 'Ruke') {
			return Response::error('503');
		}
	}
	
	if (Auth::check()) {
		$character = Character::get_character_of_logged_user(array(
            'id', 'last_activity_time'
        ));
		
		if ($character) {
            $time = time();
            $fiveMinDifference = $time - $character->last_activity_time >= 300;
            
			if (! $character->last_activity_time || $fiveMinDifference) {
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