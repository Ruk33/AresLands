<?php namespace Tests;

use Mockery as m;
use IoC;
use Input;
use URL;
use Config;

class Authenticated_Talent_Controller_Test extends TestHelper
{
	private $character;
	private $skill;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->skill = m::mock("Skill");
		IoC::instance("Skill", $this->skill);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		IoC::unregister('Character');
		IoC::unregister('Skill');
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/talent", "before", "auth");
		$this->assertHasFilter("get", "authenticated/talent", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("get_race")->once()->andReturn("foo");
		$this->character->shouldReceive("get_characteristics")->once()->andReturn(array());
		
		$this->skill->shouldReceive("get_racials")->once()->with("foo")->andReturn(array());
		$this->skill->shouldReceive("get_talents")->once()->with(array())->andReturn(array());
		
		$response = $this->get("authenticated/talent");
		
		$this->assertResponseOk($response);
		$this->assertViewHas($response, "title", "Talentos");
		$this->assertViewHasAll($response, array(
			"character" => $this->character,
			"racials"   => array(),
			"talents"   => array(),
		));
	}
	
	public function testAprender()
	{
		$this->assertHasFilter("post", "authenticated/talent/learn", "before", "auth");
		$this->assertHasFilter("post", "authenticated/talent/learn", "before", "hasNoCharacter");
		
		Input::replace(array('id' => 5));
		
		$this->skill->shouldReceive("find_or_die")->with(5)->andReturnSelf();
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("can_learn_talent")->twice()->with($this->skill)->andReturn(false, true);
		
		$response = $this->post("authenticated/talent/learn");
		
		$this->assertRedirect(URL::to_route("get_authenticated_talent_index"), $response);
		
		$this->character->shouldReceive("learn_talent")->once()->with($this->skill)->andReturn(true);
		$this->skill->shouldReceive("get_name")->once()->andReturn("bar");
		
		$response = $this->post("authenticated/talent/learn");
		
		$this->assertSessionHas("message", "Aprendiste el talento bar");
		
		$this->assertRedirect(URL::to_route("get_authenticated_talent_index"), $response);
	}
	
	public function testLanzar()
	{
		$this->assertHasFilter("post", "authenticated/talent/cast", "before", "auth");
		$this->assertHasFilter("post", "authenticated/talent/cast", "before", "hasNoCharacter");
	}
	
	public function testLanzarNoPuedeUsarTalento()
	{		
		$skillId = 7;
		$targetId = 3;
		
		Input::replace(array('skill_id' => $skillId, 'id' => $targetId));
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		
		$characterTalent = m::mock("CharacterTalent");
		
		$this->character->shouldReceive("talents")->once()->andReturn($characterTalent);
		
		$characterTalent->shouldReceive("where_skill_id")->with($skillId)->once()->andReturnSelf();
		$characterTalent->shouldReceive("first_or_die")->once()->andReturnSelf();
		
		$this->character->shouldReceive("can_use_talent")->with($characterTalent)->once()->andReturn(false);
		
		$response = $this->post("authenticated/talent/cast");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
	
	public function testLanzarPuedeUsarTalento()
	{
		$skillId = 7;
		$targetId = 3;
		
		Input::replace(array('skill_id' => $skillId, 'id' => $targetId));
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$characterTalent = m::mock("CharacterTalent");
		
		$this->character->shouldReceive("talents")->twice()->andReturn($characterTalent);
		
		$characterTalent->shouldReceive("where_skill_id")->with($skillId)->twice()->andReturnSelf();
		$characterTalent->shouldReceive("first_or_die")->twice()->andReturnSelf();
		
		$this->character->shouldReceive("can_use_talent")->with($characterTalent)->twice()->andReturn(true);
		
		$target = m::mock("Character");
		$this->character->shouldReceive("find_or_die")->twice()->with($targetId)->andReturn($target);
		
		// la primera vez no tendra reflect, la segunda si
		$target->shouldReceive("has_skill")->with(Config::get('game.reflect_skill'))->twice()->andReturn(false, true);
		
		$this->character->shouldReceive("use_talent")->twice()->with($characterTalent, $target)->andReturn(true);
		
		$characterTalent->shouldReceive("skill->results")->once()->andReturn($this->skill);
		
		$target->shouldReceive("get_name")->twice()->andReturn("bar");
		
		$this->skill->shouldReceive("get_name")->twice()->andReturn("foo");
		
		// si hasReflect === true, entonces no pedira el tipo de habilidad
		$this->skill->shouldReceive("get_type")->once()->andReturn("debuff");
		
		$response = $this->post("authenticated/talent/cast");
		
		$this->assertSessionHas("message", "Lanzaste la habilidad foo a bar");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		
		$response = $this->post("authenticated/talent/cast");
		
		$this->assertSessionHas("error", "¡Oh no!, bar te ha reflejado el hechizo foo");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
	}
}