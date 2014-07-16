<?php namespace Tests;

use PHPUnit_Framework_TestCase;
use Request;
use Router;
use URL;
use Laravel\Response;
use Laravel\CLI\Command;
use Session;
use Auth;
use Filter;
use Mockery;
use View;
use Zizaco\FactoryMuff\FactoryMuff;

abstract class TestHelper extends PHPUnit_Framework_TestCase
{
	private $auth;
    
    protected $factory;
	
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::migrate();
    }
    
	/**
	 * Habilitamos (iniciamos) las sessiones para su uso
	 */
	private function useSessions()
	{
		Session::started() || Session::load();
	}
	
	private function setAuth()
	{
		$this->auth = Auth::driver();
	}
	
	/**
	 * Desactivamos los filtros ya que no son
	 * necesarios en las pruebas unitarias
	 */
	private function disableFilters()
	{
		Filter::$filters = array();
	}
	
	public function setUp()
	{
		parent::setUp();
		
		$this->disableFilters();
		$this->useSessions();
        
        $this->factory = new FactoryMuff();
	}
	
	public function tearDown()
	{
		parent::tearDown();
		Mockery::close();
	}
	
	/**
	 * Logueamos usuario
	 * @param mixed $user
	 */
	public function logIn($user)
	{
		if ( ! $this->auth )
		{
			$this->setAuth();
		}

		$this->logOut();
		$this->auth->user = $user;
	}
	
	/**
	 * Deslogueamos usuario
	 */
	public function logOut()
	{
		if ( ! $this->auth )
		{
			return;
		}

		$this->auth->user = null;
	}
	
	/**
	 * 
	 * @param Response $response
	 * @param integer $status
	 */
	public function assertResponseStatus(Response $response, $status)
	{
		$this->assertEquals($status, $response->status());
	}
	
	/**
	 * 
	 * @param Response $response
	 */
	public function assertResponseOk(Response $response)
	{
		$this->assertResponseStatus($response, 200);
	}
	
	/**
	 * Verificamos que Response tenga cierta informacion
	 * 
	 * @param Response $response
	 * @param array $bindings
	 */
	public function assertViewHasAll(Response $response, Array $bindings)
	{
		foreach ( $bindings as $key => $value )
		{
			$this->assertViewHas($response, $key, $value);
		}
	}
	
	/**
	 * Verificamos que Response tenga cierta informacion
	 * 
	 * @param Response $response
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function assertViewHas(Response $response, $key, $value = null)
	{
		$contentData = array();
		
		if ( $response->content instanceof View )
		{
			$view = $response->content;
			$data = $view->data;
			
			// Unimos el data del layout y del contenido 
			$data = array_merge($data, $data['content']->data);
		}
		else
		{
			$data = json_decode($response->content, true);
		}
		
		$this->assertArrayHasKey($key, $data);
		
		if ( ! is_null($value) )
		{
			$this->assertEquals($value, $data[$key]);
		}
	}
	
	/**
	 * 
	 * @param array $bindings
	 */
	public function assertSessionHasAll(Array $bindings)
	{
		foreach ( $bindings as $key => $value )
		{
			$this->assertSessionHas($key, $value);
		}
	}
	
	/**
	 * Verificamos que session tenga key
	 * @param string $key
	 * @param mixed $value
	 */
	public function assertSessionHas($key, $value = null)
	{
		$this->assertEquals(true, Session::has($key));
		
		if ( ! is_null($value) )
		{
			$this->assertEquals($value, Session::get($key));
		}
	}
	
	/**
	 * Verificamos que se esten pasando los inputs
	 */
	public function assertWithInputs()
	{
		$this->assertSessionHas(\Input::old_input);
	}
	
	/**
	 * 
	 * @param string $expected
	 * @param Response $actual
	 * @param array $with
	 */
	public function assertRedirect($expected, Response $actual, array $with = array())
	{
		$this->assertInstanceOf("Laravel\Redirect", $actual);
		$this->assertEquals($expected, $actual->headers()->get('location'));
		
		if ( count($with) > 0 )
		{
			$this->assertSessionHasAll($with);
		}
	}
	
	/**
	 * 
	 * @param string $expected
	 * @param Response $actual
	 * @param array $with
	 */
	public function assertRedirectTo($expected, Response $actual, array $with = array())
	{
		$this->assertRedirect(URL::to($expected), $actual, $with);
	}
	
	/**
	 * 
	 * @param string $expected
	 * @param Response $actual
	 * @param array $with
	 */
	public function assertRedirectToRoute($expected, Response $actual, array $with = array())
	{
		$this->assertRedirect(URL::to_route($expected), $actual, $with);
	}

	/**
	 * Run the migrations in the test database
	 * Thanks to Zizaco from http://www.forums.laravel.io/viewtopic.php?id=2521
	 */
	public static function migrate()
	{
		// If there is not a declaration that migrations have been run'd
		if( ! isset($GLOBALS["testhelper_migrated"]) )
		{
            require path('sys').'cli/dependencies'.EXT;
            
			// Run migrations
			Command::run(array('migrate:install'));
			Command::run(array('migrate'));

			// Declare that migrations have been run'd
			$GLOBALS["testhelper_migrated"] = true;
		}
	}
	
	/**
	 * 
	 * @param string $method
	 * @param string $uri
	 * @return Response
	 */
	public function route($method, $uri)
	{
		Request::foundation()->setMethod($method);
		$response = Router::route($method, $uri);
		
		if ( ! is_object($response) )
		{
			return $this->assertTrue(false, "El route {$uri} no existe");
		}
		
		return $response->call();
	}

	/**
	 * Verificamos si uri tiene filtro
	 * 
	 * @param string $method get|post|put|delete
	 * @param string $uri
	 * @param string $event
	 * @param string $filterName
	 * @param array $args
	 * @return boolean
	 */
	public function hasFilter($method, $uri, $event, $filterName, Array $args = array())
	{
		if ( $filterName )
		{
			$route = Router::route(strtoupper($method), $uri);
			$action = $route->action;

			if ( isset($action[$event]) )
			{
				$filters = explode('|', $action[$event]);

				foreach ( $filters as $filter )
				{
					if ( $filter == $filterName || strstr($filter, ':', true) == $filterName )
					{
						if ( count($args) > 0 )
						{
							$arguments = explode(',', substr($filter, strpos($filter, ':') + 1));

							foreach ( $arguments as $argument )
							{
								$index = array_search($argument, $args);

								if ( $index !== false )
								{
									unset($args[$index]);
								}
							}

							if ( count($args) > 0 )
							{
								return false;
							}
						}

						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Wraper para facilitar verificacion y uso de hasFilter
	 * 
	 * @param string $method
	 * @param string $uri
	 * @param string $event
	 * @param string $filterName
	 * @param array $args
	 */
	public function assertHasFilter($method, $uri, $event, $filterName, array $args = array())
	{
		$this->assertTrue($this->hasFilter($method, $uri, $event, $filterName, $args), "[{$method}] El uri {$uri} no tiene el filtro {$filterName} en el evento {$event}");
	}
	
	public function __call($name, $arguments)
	{
		if ( in_array(strtolower($name), array('get', 'post', 'put', 'delete')) )
		{
			return $this->route(strtoupper($name), $arguments[0]);
		}
	}
}