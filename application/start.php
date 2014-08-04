<?php

/*
|--------------------------------------------------------------------------
| PHP Display Errors Configuration
|--------------------------------------------------------------------------
|
| Since Laravel intercepts and displays all errors with a detailed stack
| trace, we can turn off the display_errors ini directive. However, you
| may want to enable this option if you ever run into a dreaded white
| screen of death, as it can provide some clues.
|
*/

ini_set('display_errors', 'On');

// --------------------------------------------------------------
// Autoload composer vendors.
// --------------------------------------------------------------
require path('composer').'autoload.php';

/*
|--------------------------------------------------------------------------
| Laravel Configuration Loader
|--------------------------------------------------------------------------
|
| The Laravel configuration loader is responsible for returning an array
| of configuration options for a given bundle and file. By default, we
| use the files provided with Laravel; however, you are free to use
| your own storage mechanism for configuration arrays.
|
*/

Laravel\Event::listen(Laravel\Config::loader, function($bundle, $file)
{
	return Laravel\Config::file($bundle, $file);
});

/*
|--------------------------------------------------------------------------
| Register Class Aliases
|--------------------------------------------------------------------------
|
| Aliases allow you to use classes without always specifying their fully
| namespaced path. This is convenient for working with any library that
| makes a heavy use of namespace for class organization. Here we will
| simply register the configured class aliases.
|
*/

$aliases = Laravel\Config::get('application.aliases');

Laravel\Autoloader::$aliases = $aliases;

/*
|--------------------------------------------------------------------------
| Auto-Loader Mappings
|--------------------------------------------------------------------------
|
| Registering a mapping couldn't be easier. Just pass an array of class
| to path maps into the "map" function of Autoloader. Then, when you
| want to use that class, just use it. It's simple!
|
*/

Autoloader::map(array(
	'Base_Controller'                     => path('app').'controllers/base.php',
	
    'Authenticated_Base'                  => path('app').'controllers/authenticated_base.php',
	
	'Authenticated_Controller'            => path('app').'controllers/authenticated.php',
	'Authenticated_Character_Controller'  => path('app').'controllers/authenticated/character.php',
	'Authenticated_Talent_Controller'     => path('app').'controllers/authenticated/talent.php',
	'Authenticated_Clan_Controller'       => path('app').'controllers/authenticated/clan.php',
	'Authenticated_SecretShop_Controller' => path('app').'controllers/authenticated/secretshop.php',
	'Authenticated_Tournament_Controller' => path('app').'controllers/authenticated/tournament.php',
	'Authenticated_Quest_Controller'      => path('app').'controllers/authenticated/quest.php',
	'Authenticated_Orb_Controller'        => path('app').'controllers/authenticated/orb.php',
	'Authenticated_Message_Controller'    => path('app').'controllers/authenticated/message.php',
	'Authenticated_Trade_Controller'      => path('app').'controllers/authenticated/trade.php',
	'Authenticated_Inventory_Controller'  => path('app').'controllers/authenticated/inventory.php',
	'Authenticated_Npc_Controller'        => path('app').'controllers/authenticated/npc.php',
	'Authenticated_Battle_Controller'     => path('app').'controllers/authenticated/battle.php',
	'Authenticated_Action_Controller'     => path('app').'controllers/authenticated/action.php',
	'Authenticated_Dungeon_Controller'    => path('app').'controllers/authenticated/dungeon.php',
	'Authenticated_Ranking_Controller'    => path('app').'controllers/authenticated/ranking.php',
    
	'Base_Model'                          => path('app').'models/base_model.php',
));

/*
|--------------------------------------------------------------------------
| Auto-Loader Directories
|--------------------------------------------------------------------------
|
| The Laravel auto-loader can search directories for files using the PSR-0
| naming convention. This convention basically organizes classes by using
| the class namespace to indicate the directory structure.
|
*/

Autoloader::directories(array(
	path('app').'models',
	path('app').'libraries',
));

Autoloader::namespaces(array(
	'Libraries' => path('app').'libraries',
	'Tests' => path('app').'tests',
));

/*
|--------------------------------------------------------------------------
| Laravel View Loader
|--------------------------------------------------------------------------
|
| The Laravel view loader is responsible for returning the full file path
| for the given bundle and view. Of course, a default implementation is
| provided to load views according to typical Laravel conventions but
| you may change this to customize how your views are organized.
|
*/

Event::listen(View::loader, function($bundle, $view)
{
	return View::file($bundle, $view, Bundle::path($bundle).'views');
});

/*
|--------------------------------------------------------------------------
| Laravel Language Loader
|--------------------------------------------------------------------------
|
| The Laravel language loader is responsible for returning the array of
| language lines for a given bundle, language, and "file". A default
| implementation has been provided which uses the default language
| directories included with Laravel.
|
*/

Event::listen(Lang::loader, function($bundle, $language, $file)
{
	return Lang::file($bundle, $language, $file);
});

/*
|--------------------------------------------------------------------------
| Attach The Laravel Profiler
|--------------------------------------------------------------------------
|
| If the profiler is enabled, we will attach it to the Laravel events
| for both queries and logs. This allows the profiler to intercept
| any of the queries or logs performed by the application.
|
*/

if (Config::get('application.profiler'))
{
	Profiler::attach();
}

/*
|--------------------------------------------------------------------------
| Enable The Blade View Engine
|--------------------------------------------------------------------------
|
| The Blade view engine provides a clean, beautiful templating language
| for your application, including syntax for echoing data and all of
| the typical PHP control structures. We'll simply enable it here.
|
*/

Blade::sharpen();

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
|
| We need to set the default timezone for the application. This controls
| the timezone that will be used by any of the date methods and classes
| utilized by Laravel or your application. The timezone may be set in
| your application configuration file.
|
*/

date_default_timezone_set(Config::get('application.timezone'));

/*
|--------------------------------------------------------------------------
| Start / Load The User Session
|--------------------------------------------------------------------------
|
| Sessions allow the web, which is stateless, to simulate state. In other
| words, sessions allow you to store information about the current user
| and state of your application. Here we'll just fire up the session
| if a session driver has been configured.
|
*/

if ( ! Request::cli() and Config::get('session.driver') !== '')
{
	Session::load();
}

Laravel\Database\Eloquent\Pivot::$timestamps = false;

/*
 * Resolvemos las dependencias de los controladores
 * automaticamente en lugar de estar haciendo:
 * 
 * IoC::register('nombre_controlador', function()
 * { 
 *		return new nombre_controlador(dependencias);
 * });
 * 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Prueba ferviente de que la magia existe
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
Event::listen(Controller::factory, function($class)
{
	$reflection = new ReflectionClass($class);
	$reflectionParams = $reflection->getMethod('__construct')->getParameters();
	$params = array();
	
	foreach ( $reflectionParams as $param )
	{
		$params[] = IoC::resolve($param->getClass()->name);
	}
	
	return $reflection->newInstanceArgs($params);
});

IoC::instance('Character', new Character());
IoC::instance('CharacterItem', new CharacterItem());
IoC::instance('Item', new Item());
IoC::instance('Clan', new Clan());
IoC::instance('Skill', new Skill());
IoC::instance('VipFactory', new VipFactory());
IoC::instance('CharacterTalent', new CharacterTalent());
IoC::instance('Tournament', new Tournament());
IoC::instance('TournamentClanScore', new TournamentClanScore());
IoC::instance('TournamentRegisteredClan', new TournamentRegisteredClan());
IoC::instance('Quest', new Quest());
IoC::instance('Message', new Message());
IoC::instance('Trade', new Trade());
IoC::instance('Merchant', new Merchant());
IoC::instance('NpcMerchandise', new NpcMerchandise());
IoC::instance('NpcRandomMerchandise', new NpcRandomMerchandise());
IoC::instance('Monster', new Monster());