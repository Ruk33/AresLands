<?php

/*
|--------------------------------------------------------------------------
| Application Controllers
|--------------------------------------------------------------------------
*/

Route::controller(Controller::detect());


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

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

/*
 *	No logueado
 */
Route::filter('auth', function($redirectTo = 'home/index')
{
	if (Auth::guest()) {
		return Redirect::to($redirectTo);
	}
});

/*
 *	Logueado
 */
Route::filter('logged', function($redirectTo) {
	if (Auth::check()) {
		return Redirect::to($redirectTo);
	}
});

/*
 *	¿No tiene personaje?
 */
Route::filter('hasNoCharacter', function($redirectTo = 'characterCreation/index')
{
	if (!Character::where('account_id', Auth::user()->id)) {
		return Redirect::to($redirectTo);
	}
});

/*
 *	¿Tiene personaje?
 */
Route::filter('hasCharacter', function($redirectTo = 'home/index')
{
	if (Character::where('account_id', Auth::user()->id)) {
		return Redirect::to($redirectTo);
	}
});