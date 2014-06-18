<?php namespace Tests;

use Response;
use Redirect;
use URL;
use Session;
use Router;

abstract class TestHelperTest extends TestHelper
{
	public function setUp()
	{
		parent::setUp();
		
		$this->use_sessions();
	}
	
	public function testResponseStatus()
	{
		$this->assertResponseStatus(new Response(null, 301), 301);
	}
	
	public function testResponseOk()
	{
		$this->assertResponseStatus(new Response(null, 200), 200);
	}
	
	public function testViewHasAll()
	{
		$bindings = array(
			'foo' => 'bar',
			'baz' => '3',
			5
		);
		
		$this->assertViewHasAll(Response::json($bindings), $bindings);
	}
	
	public function testViewHas()
	{
		$this->assertViewHas(Response::json(array('name' => 'Batman')), 'name');
		$this->assertViewHas(Response::json(array('name' => 'Batman')), 'name', 'Batman');
	}
	
	public function testSessionHas()
	{
		Session::flash('foo', 'bar');
		
		$this->assertSessionHas('foo');
		$this->assertSessionHas('foo', 'bar');
		
		Session::forget('foo');
	}
	
	public function testSessionHasAll()
	{
		Session::flash('foo', 'bar');
		Session::flash('3', 'baz');
		
		$this->assertSessionHasAll(array('foo' => 'bar', '3' => 'baz'));
		
		Session::forget('foo');
		Session::forget('3');
	}
	
	public function testRedirect()
	{
		$this->assertRedirect(URL::to("foo/bar"), Redirect::to("foo/bar"));
		
		$bindings = array(
			'foo' => 'bar'
		);
		
		$this->assertRedirect(URL::to("foo/bar"), Redirect::to("foo/bar")->with('foo', 'bar'), $bindings);
	}
	
	public function testRedirectTo()
	{
		$this->assertRedirectTo("foo/bar", Redirect::to("foo/bar"));
	}
	
	public function testRoute()
	{
		Router::register('GET', 'foo/bar', function() { return 'Home!'; });
		$this->assertInstanceOf("Response", $this->get("foo/bar"));
	}
}