<?php 

require("./application/controllers/authenticated/clan.php");

abstract class Authenticated_Clan_Controller_Test extends \Tests\AuthenticatedHelper
{
	protected $clan;
	protected $skill;
	protected $controller;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->auth->character = Mockery::mock("Character");
		
		$this->clan = Mockery::mock("Clan");
		$this->skill = Mockery::mock("Skill");
		
		IoC::instance('Clan', $this->clan);
		IoC::instance('Skill', $this->skill);
		
		$this->controller = IoC::resolve('Authenticated_Clan_Controller');
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister('Clan');
		IoC::unregister('Skill');
	}
	
	public function testMostrar()
	{
		$this->controller->layout->title = null;
		$this->controller->layout->content = null;
		
		$this->auth->shouldReceive("user")->twice()->andReturn($this->auth);
		
		$this->clan->shouldReceive("find_or_die")->twice()->with(1)->andReturnSelf();
		$this->clan->shouldReceive("members->get")->twice()->andReturn(array());
		
		$this->clan->shouldReceive("get_skills")->twice()->andReturnSelf();
		$this->clan->shouldReceive("where_level")->with(1)->twice()->andReturnSelf();
		$this->clan->shouldReceive("get")->twice()->andReturn(array());
		
		$this->clan->shouldReceive("can_accept_petitions")->once()->andReturn(false);
		$this->clan->shouldReceive("can_reject_petitions")->once()->andReturn(false);
		
		$this->clan->shouldReceive("get_name")->twice()->andReturn("foo");
		
		$this->controller->get_show(1);
		
		$this->assertEquals($this->controller->layout->title, "foo");
		
		$data = $this->controller->layout->content->data;
		
		$this->assertArrayHasKey('clan', $data);
		$this->assertArrayHasKey('members', $data);
		$this->assertArrayHasKey('character', $data);
		$this->assertArrayHasKey('skills', $data);
		$this->assertArrayHasKey('petitions', $data);
		$this->assertEmpty($data['petitions']);
		
		$this->clan->shouldReceive("can_accept_petitions")->once()->andReturn(false);
		$this->clan->shouldReceive("can_reject_petitions")->once()->andReturn(true);
		
		$this->clan->shouldReceive("petitions->get")->once()->andReturn(array(1, 2));
		
		$this->controller->get_show(1);
		
		$this->assertEquals(array(1, 2), $this->controller->layout->content->data['petitions']);
	}
	
	public function testAprenderHabilidad()
	{		
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("get_clan_id")->twice()->andReturn(5);
		
		$this->clan->shouldReceive("find_or_die")->twice()->andReturnSelf();
		$this->clan->shouldReceive("has_permission")->once()->andReturn(true);
		
		$this->skill->shouldReceive("where_id")->with(1)->once()->andReturnSelf();
		$this->skill->shouldReceive("where_level")->with(3)->once()->andReturnSelf();
		$this->skill->shouldReceive("first_or_die")->once()->andReturnSelf();
		
		$this->clan->shouldReceive("can_learn_skill")->once()->andReturn(true);
		$this->clan->shouldReceive("learn_skill")->once();
		
		$this->clan->shouldReceive("get_id")->once()->andReturn(5);
		
		$this->assertEquals($this->controller->get_learnSkill(1, 3), Redirect::to('authenticated/clan/5'));
		
		$this->clan->shouldReceive("has_permission")->once()->andReturn(false);
		
		$this->assertEquals($this->controller->get_learnSkill(1, 3), Redirect::to('authenticated/index/'));
	}
	
	public function testEditarMensaje()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->clan->shouldReceive("set_message")->with('foo')->once();
		$this->clan->shouldReceive("save")->once();
		
		$this->auth->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		$this->auth->character->shouldReceive("has_permission")->once()->andReturn(true);
		
		Input::replace(array('message' => 'foo'));
		
		$this->controller->post_editMessage();
		
		$this->auth->character->shouldReceive("has_permission")->once()->andReturn(false);
		
		$this->controller->post_editMessage();
	}
	
	public function testGetCrear()
	{
		$this->controller->layout->title = null;
		$this->controller->layout->content = null;
		
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("get_clan_id")->twice()->andReturn(1);
		
		$this->assertEquals($this->controller->get_create(), Redirect::to('authenticated/clan/1'));
		
		$this->auth->character->shouldReceive("get_clan_id")->once()->andReturn(0);
		
		$this->controller->get_create();
		
		$data = $this->controller->layout;
		
		$this->assertArrayHasKey('title', $data);
		$this->assertArrayHasKey('content', $data);
	}
	
	public function testPostCrear()
	{
		$this->auth->shouldReceive("user")->times(3)->andReturnSelf();
		
		$this->auth->character->shouldReceive("get_id")->twice()->andReturn(5);
		$this->auth->character->shouldReceive("get_clan_id")->once()->andReturn(1);
		
		$this->assertEquals($this->controller->post_create(), Redirect::to("authenticated/index/"));
		
		$this->auth->character->shouldReceive("get_clan_id")->twice()->andReturn(0);
		
		Input::replace(array('name' => 'foo', 'message' => 'bar', 'leader_id' => 5));
		
		$this->clan->shouldReceive("set_name")->with("foo")->twice();
		$this->clan->shouldReceive("set_message")->with("bar")->twice();
		$this->clan->shouldReceive("set_leader_id")->with(5)->twice();
		
		$this->clan->shouldReceive("errors->all")->once();
		$this->clan->shouldReceive("validate")->once()->andReturn(false);
		
		$this->session->shouldReceive("flash")->once();
		
		$this->assertEquals($this->controller->post_create(), Redirect::to('authenticated/createClan'));
		
		$this->clan->shouldReceive("validate")->once()->andReturn(true);
		
		$this->clan->shouldReceive("save")->once();
		$this->clan->shouldReceive("join")->once();
		$this->clan->shouldReceive("get_id")->once()->andReturn(6);
		
		$this->assertEquals($this->controller->post_create(), Redirect::to('authenticated/clan/6'));
	}
	
	public function testSalir()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("can_leave_clan")->once()->andReturn(false);
		
		$this->session->shouldReceive("flash")->once();
		
		$this->assertEquals($this->controller->post_leave(), Redirect::to('authenticated/index/'));
		
		$this->auth->character->shouldReceive("can_leave_clan")->once()->andReturn(true);
		
		$this->auth->character->shouldReceive("leave_clan")->once();
		
		$this->assertEquals($this->controller->post_leave(), Redirect::to('authenticated/clan/'));
	}
	
	public function testBorrar()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("can_delete_clan")->once()->andReturn(false);
		
		$this->session->shouldReceive("flash")->once();
		
		$this->assertEquals($this->controller->post_delete(), Redirect::to('authenticated/index/'));
		
		$this->auth->character->shouldReceive("can_delete_clan")->once()->andReturn(true);
		
		$this->auth->character->shouldReceive("delete_clan")->once();
		
		$this->assertEquals($this->controller->post_delete(), Redirect::to('authenticated/index/'));
	}
	
	public function testExpulsarMiembro()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		$this->clan->shouldReceive("members->where_name->first_or_die")->twice()->andReturn($this->auth->character);
		
		$this->clan->shouldReceive("can_kick_member")->once()->andReturn(false);
		
		$this->auth->character->shouldReceive("get_name")->twice();
		
		$this->session->shouldReceive("flash")->twice();
		
		$this->assertEquals(Redirect::to('authenticated/index/'), $this->controller->post_kickMember("foo"));
		
		$this->clan->shouldReceive("can_kick_member")->once()->andReturn(true);
		$this->clan->shouldReceive("kick_member")->once();
		
		$this->clan->shouldReceive("get_id")->once()->andReturn(1);
		
		$this->assertEquals(Redirect::to('authenticated/clan/' . 1), $this->controller->post_kickMember("foo"));
	}
	
	public function testRechazarPeticiones()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("petitions->where_id->first_or_die")->twice()->andReturn(Mockery::mock("ClanPetition"));
		
		$this->clan->shouldReceive("can_reject_petitions")->once()->andReturn(false);
		
		$this->assertEquals(Redirect::to('authenticated/index/'), $this->controller->post_rejectPetition(1));
		
		$this->clan->shouldReceive("can_reject_petitions")->once()->andReturn(true);
		
		$this->clan->shouldReceive("reject_petition")->once();
		
		$this->session->shouldReceive("flash")->once();
		
		$this->clan->shouldReceive("get_id")->once()->andReturn(2);
		
		$this->assertEquals(Redirect::to('authenticated/clan/2'), $this->controller->post_rejectPetition(1));
	}
	
	public function testAceptarPeticiones()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("petitions->where_id->first_or_die")->twice()->andReturn(Mockery::mock("ClanPetition"));
		
		$this->clan->shouldReceive("can_accept_petitions")->once()->andReturn(false);
		
		$this->assertEquals(Redirect::to('authenticated/index/'), $this->controller->post_acceptPetition(1));
		
		$this->clan->shouldReceive("can_accept_petitions")->once()->andReturn(true);
		
		$this->clan->shouldReceive("accept_petition")->once()->andReturn(true);
		
		$this->session->shouldReceive("flash")->once();
		
		$this->clan->shouldReceive("get_id")->once()->andReturn(2);
		
		$this->assertEquals(Redirect::to('authenticated/clan/2'), $this->controller->post_acceptPetition(1));
	}
	
	public function testNuevaPeticion()
	{
		$this->auth->shouldReceive("user")->times(3)->andReturnSelf();
		$this->session->shouldReceive("flash")->times(3);
		
		$this->auth->character->shouldReceive("can_enter_in_clan")->once()->andReturn(false);
		
		$this->assertEquals(Redirect::to('authenticated/index/'), $this->controller->post_newPetition(1));
		
		$this->auth->character->shouldReceive("can_enter_in_clan")->twice()->andReturn(true);
		
		$this->clan->shouldReceive("find_or_die")->twice()->andReturnSelf();
		
		$this->clan->shouldReceive("can_send_petition")->once()->andReturn(false);
		
		$this->clan->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->assertEquals(Redirect::to('authenticated/clan/1'), $this->controller->post_newPetition(1));
		
		$this->clan->shouldReceive("can_send_petition")->once()->andReturn(true);
		$this->clan->shouldReceive("send_petition")->once();
		
		$this->assertEquals(Redirect::to('authenticated/clan/1'), $this->controller->post_newPetition(1));
	}
	
	public function testModificarPermisos()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("can_modify_permissions")->once()->andReturn(false);
		
		$this->clan->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->assertEquals(Redirect::to('authenticated/clan/1'), $this->controller->post_modifyMemberPermissions(1));
		
		$this->clan->shouldReceive("can_modify_permissions")->once()->andReturn(true);
		
		$this->clan->shouldReceive("members->find_or_die")->once()->andReturn($this->auth->character);
		
		$this->auth->character->shouldReceive("set_permission")->times(6);
		$this->auth->character->shouldReceive("save")->once();
		
		Input::replace(array('id' => 5));
		
		$this->assertEquals(Redirect::to('authenticated/clan/1'), $this->controller->post_modifyMemberPermissions(1));
	}
	
	public function testDarLider()
	{
		$this->auth->shouldReceive("user")->twice()->andReturnSelf();
		
		$this->auth->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("members->find_or_die")->twice()->andReturn($this->auth->character);
		
		$this->auth->character->shouldReceive("can_give_leadership_to")->once()->andReturn(false);
		
		Input::replace(array('id' => 5));
		
		$this->assertEquals(Redirect::back(), $this->controller->post_giveLeaderShip());
		
		$this->auth->character->shouldReceive("can_give_leadership_to")->once()->andReturn(true);
		
		$this->auth->character->shouldReceive("give_leadership_to")->once();
		
		$this->auth->character->shouldReceive("get_name")->once();
		
		$this->session->shouldReceive("flash")->once();
		
		$this->assertEquals(Redirect::to('authenticated/index'), $this->controller->post_giveLeaderShip());
	}
}