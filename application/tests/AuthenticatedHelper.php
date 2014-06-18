<?php namespace Tests;

/**
 * @deprecated
 */
abstract class AuthenticatedHelper extends TestHelper
{
	protected $auth;
	protected $session;
	
	public function setUp()
	{
		parent::setUp();
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
}