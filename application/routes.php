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
| Application Events
|--------------------------------------------------------------------------
*/

Event::listen('unequipItem', function(CharacterItem $characterItem)
{
	$character = Session::get('character');
	$item = $characterItem->item;

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
				$characterSkill = $character->skills()->where('skill_id', '=', $skill->id)->where('level', '=', $skill->level)->first();

				if ( $characterSkill )
				{
					$characterSkill->delete();
				}
			}
		}
	}
});

Event::listen('equipItem', function(CharacterItem $characterItem, $amount = 1)
{
	$character = Session::get('character');
	$item = $characterItem->item;

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
			 *	y las agregamos al registro
			 */
			foreach ( $skills as $skill )
			{
				$characterSkill = new CharacterSkill();

				$characterSkill->skill_id = $skill->id;
				$characterSkill->character_id = $character->id;
				$characterSkill->level = $skill->level;
				$characterSkill->end_time = ($skill->duration != -1) ? time() + $skill->duration : 0;
				$characterSkill->amount = $amount;

				$characterSkill->save();
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