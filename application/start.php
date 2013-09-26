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
	'Base_Controller' => path('app').'controllers/base.php',
	'Base_Model' => path('app').'models/base_model.php',
	
	'QuestAction' => path('app').'libraries/questaction.php',
	'QuestActionPve' => path('app').'libraries/questactionpve.php',
	'QuestActionPveWin' => path('app').'libraries/questactionpvewin.php',
	'QuestActionNpcTalk' => path('app').'libraries/questactionnpctalk.php',
	'QuestActionNpcTalkAndGiveItem' => path('app').'libraries/questactionnpctalkandgiveitem.php',

	/*
	 *	Registramos las misiones
	 */
	'Quest_Starting' => path('app').'libraries/quest_starting.php',
	
	'Quest_AyudaATuPueblo' => path('app').'libraries/quest_ayudaatupueblo.php',
	'Quest_PlagaDeSerpientes' => path('app').'libraries/quest_plagadeserpientes.php',
	'Quest_CazadoresImplacables' => path('app').'libraries/quest_cazadoresimplacables.php',
	'Quest_PlagaDeRatas' => path('app').'libraries/quest_plagaderatas.php',
	
	'Quest_LasMinasEstanBajoAtaque' => path('app').'libraries/quest_lasminasestanbajoataque.php',
	'Quest_CuidadoLaCosecha' => path('app').'libraries/quest_cuidadolacosecha.php',
	'Quest_LaTribuDeOrcos' => path('app').'libraries/quest_latribudeorcos.php',
	'Quest_SuenoPerturbado' => path('app').'libraries/quest_suenoperturbado.php',
	
	'Quest_VoladoresNocturnos' => path('app').'libraries/quest_voladoresnocturnos.php',
	'Quest_EnemigoEscondido' => path('app').'libraries/quest_enemigoescondido.php',
	'Quest_BandidosAlAcecho' => path('app').'libraries/quest_bandidosalacecho.php',
	
	/*
	'Quest_HieloYRoca' => path('app').'libraries/quest_hieloyroca.php',
	'Quest_NuevosProblemas' => path('app').'libraries/quest_nuevosproblemas.php',
	'Quest_RecuperarLoPerdido' => path('app').'libraries/quest_recuperarloperdido.php',
	'Quest_ApaciguarALosMuertos' => path('app').'libraries/quest_apaciguaralosmuertos.php',
	'Quest_ElLlantoDeUnPadre' => path('app').'libraries/quest_elllantodeunpadre.php',
	'Quest_MonedaPorMoneda' => path('app').'libraries/quest_monedapormoneda.php',
	'Quest_MejorArmamento' => path('app').'libraries/quest_mejorarmamento.php',
	'Quest_GranError' => path('app').'libraries/quest_granerror.php',
	'Quest_AyudaAlMago' => path('app') . 'libraries/quest_ayudaalmago.php',
	'Quest_MagiaSuprema' => path('app') . 'libraries/quest_magiasuprema.php',
	'Quest_NegociosTurbios' => path('app') . 'libraries/quest_negociosturbios.php',
	'Quest_Traicion' => path('app') . 'libraries/quest_traicion.php',
	'Quest_Urgente' => path('app') . 'libraries/quest_urgente.php',
	'Quest_MuchoTrabajo' => path('app') . 'libraries/quest_muchotrabajo.php',
	'Quest_PrimerMisionFallida' => path('app') . 'libraries/quest_primermisionfallida.php',
	'Quest_SegundaMisionFallida' => path('app') . 'libraries/quest_segundamisionfallida.php',
	'Quest_PocionesYPociones' => path('app') . 'libraries/quest_pocionesypociones.php',
	'Quest_LaVenganzaDelAlquimista' => path('app') . 'libraries/quest_lavenganzadelalquimista.php',
	'Quest_AsegurarZona' => path('app') . 'libraries/quest_asegurarzona.php',
	*/
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