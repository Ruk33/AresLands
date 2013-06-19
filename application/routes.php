<?php

/*
|--------------------------------------------------------------------------
| Application Controllers
|--------------------------------------------------------------------------
*/

Route::controller('Authenticated');
Route::controller('CharacterCreation');
Route::controller('Home');


/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
*/

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
	$character = null;

	if ( Auth::check() )
	{
		$character = Character::where('user_id', '=', Auth::user()->id)->first();
	}

	Session::put('character', $character);
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
	if (Auth::guest()) 
	{
		return Redirect::to($redirectTo);
	}
});

/*
 *	Logueado
 */
Route::filter('logged', function($redirectTo) {
	if (Auth::check()) 
	{
		return Redirect::to($redirectTo);
	}
});

/*
 *	¿No tiene personaje?
 */
Route::filter('hasNoCharacter', function($redirectTo = 'charactercreation/race')
{
	if ( is_null(Session::get('character')) ) 
	{
		return Redirect::to($redirectTo);
	}
});

/*
 *	¿Tiene personaje?
 */
Route::filter('hasCharacter', function($redirectTo = 'home/index')
{
	if ( ! is_null(Session::get('character')) ) 
	{
		return Redirect::to($redirectTo);
	}
});