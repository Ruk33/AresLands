<?php

// TODO

class BattleTest extends PHPUnit_Framework_TestCase
{
	private $attacker;
	private $target;
	private $battle;
	
	public function __construct()
	{
		Session::started() or Session::load();
		Auth::login(1);
		
		$this->attacker = Character::find(1);
		$this->target = Character::find(2);
		
		$this->attacker->current_life = $this->attacker->max_life;
		$this->target->current_life = $this->target->max_life;
		
		$this->battle = new Battle($this->attacker, $this->target);
	}
	
	public function test121()
	{
		$this->assertEquals(1, 1);
	}
}