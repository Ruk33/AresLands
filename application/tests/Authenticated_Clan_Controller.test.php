<?php 

use Mockery as m;

class Authenticated_Clan_Controller_Test extends \Tests\TestHelper
{
	protected $character;
	protected $clan;
	protected $skill;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		$this->clan = m::mock("Clan");
		$this->skill = m::mock("Skill");
		
		IoC::instance("Character", $this->character);
		IoC::instance("Clan", $this->clan);
		IoC::instance("Skill", $this->skill);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("Clan");
		IoC::unregister("Skill");
	}
	
	public function testAprenderHabilidad()
	{
		$this->assertHasFilter("post", "authenticated/clan/learnSkill", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/learnSkill", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/learnSkill", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->times(3)->andReturnSelf();
		$this->character->shouldReceive("clan->first_or_die")->times(3)->andReturn($this->clan);
		
		$this->clan->shouldReceive("get_id")->times(3)->andReturn(1);
		$this->clan->shouldReceive("has_permission")->once()->with($this->character, Clan::PERMISSION_LEARN_SPELL)->andReturn(false);
		
		$response = $this->post("authenticated/clan/learnSkill");
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		
		$this->clan->shouldReceive("has_permission")->twice()->with($this->character, Clan::PERMISSION_LEARN_SPELL)->andReturn(true);
		
		Input::replace(array("skill_id" => 1, "skill_level" => 1));
		
		$this->skill->shouldReceive("where_id")->twice()->with(1)->andReturnSelf();
		$this->skill->shouldReceive("where_level")->twice()->with(1)->andReturnSelf();
		$this->skill->shouldReceive("first_or_die")->twice()->andReturnSelf();
		
		$this->clan->shouldReceive("can_learn_skill")->once()->with($this->skill)->andReturn(false);
		
		$response = $this->post("authenticated/clan/learnSkill");
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		
		$this->clan->shouldReceive("can_learn_skill")->once()->with($this->skill)->andReturn(true);
		
		$this->clan->shouldReceive("learn_skill")->once()->with($this->skill);
		$this->skill->shouldReceive("get_name")->once()->andReturn("foo");
		
		$response = $this->post("authenticated/clan/learnSkill");
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("message", "Has aprendido la habilidad foo para tu grupo");
	}
	
	public function testEditarMensaje()
	{
		$this->assertHasFilter("post", "authenticated/clan/editMessage", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/editMessage", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/editMessage", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("has_permission")->once()->with($this->character, Clan::PERMISSION_EDIT_MESSAGE)->andReturn(false);
		
		$this->post("authenticated/clan/editMessage");
		
		Input::replace(array("message" => "foo"));
		
		$this->clan->shouldReceive("has_permission")->once()->with($this->character, Clan::PERMISSION_EDIT_MESSAGE)->andReturn(true);
		$this->clan->shouldReceive("set_message")->once()->with("foo");
		$this->clan->shouldReceive("save")->once();
		
		$this->post("authenticated/clan/editMessage");
	}
	
	public function testGetCrear()
	{
		$this->assertHasFilter("get", "authenticated/clan/create", "before", "auth");
		$this->assertHasFilter("get", "authenticated/clan/create", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("get_clan_id")->twice()->andReturn(1);
		
		$response = $this->get("authenticated/clan/create");
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		
		$this->character->shouldReceive("get_clan_id")->once()->andReturn(0);
		
		$this->clan->shouldReceive("create_instance")->once()->andReturnSelf();
		
		$response = $this->get("authenticated/clan/create");
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Crear grupo",
			"clan" => $this->clan
		));
	}
	
	public function testPostCrear()
	{
		$this->assertHasFilter("post", "authenticated/clan/create", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/create", "before", "hasNoCharacter");
				
		$this->character->shouldReceive("get_logged")->times(3)->andReturnSelf();
        $this->character->shouldReceive("get_id")->times(2)->andReturn(5);
		$this->character->shouldReceive("get_clan_id")->once()->andReturn(1);
		
		$response = $this->post("authenticated/clan/create");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		
		$this->character->shouldReceive("get_clan_id")->twice()->andReturn(0);
		
		$attributes = array("name" => "foo", "message" => "bar");
		
		Input::replace($attributes);
		
		$this->clan->shouldReceive("create_instance")->with(array_merge($attributes, array("leader_id" => 5)))->twice()->andReturnSelf();
		$this->clan->shouldReceive("validate")->once()->andReturn(false);
		$this->clan->shouldReceive("errors->all")->once()->andReturn(array("lorem ipsum dolor amet"));
		
		$response = $this->post("authenticated/clan/create");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_create"), $response);
		$this->assertSessionHas("error", array("lorem ipsum dolor amet"));
		
		$this->clan->shouldReceive("validate")->once()->andReturn(true);
		$this->clan->shouldReceive("save")->once();
		$this->clan->shouldReceive("join")->once()->with($this->character);
		$this->clan->shouldReceive("get_id")->once()->andReturn(9);
		
		$response = $this->post("authenticated/clan/create");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(9)), $response);
	}
	
	public function testSalir()
	{
		$this->assertHasFilter("post", "authenticated/clan/leave", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/leave", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/leave", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("can_leave_clan")->once()->andReturn(false);
		
		$response = $this->post("authenticated/clan/leave");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("error", "En este momento no puedes salir del grupo");
		
		$this->character->shouldReceive("can_leave_clan")->once()->andReturn(true);
		$this->character->shouldReceive("leave_clan")->once();
		
		$response = $this->post("authenticated/clan/leave");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("message", "Haz salido del grupo");
	}
	
	public function testBorrar()
	{
		$this->assertHasFilter("post", "authenticated/clan/delete", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/delete", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/delete", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("can_delete_clan")->once()->andReturn(false);
		
		$response =$this->post("authenticated/clan/delete");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("error", "No puedes borrar el grupo en este momento");
		
		$this->character->shouldReceive("can_delete_clan")->once()->andReturn(true);
		$this->character->shouldReceive("delete_clan")->once();
		
		$response =$this->post("authenticated/clan/delete");
		
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("message", "Haz borrado el grupo con exito");
	}
	
	public function testExpulsar()
	{
		$this->assertHasFilter("post", "authenticated/clan/kick", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/kick", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/kick", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$member = m::mock("Character");
		
		Input::replace(array("name" => "foo"));
		
		$this->clan->shouldReceive("members")->twice()->andReturnSelf();
		$this->clan->shouldReceive("where_name")->twice()->with("foo")->andReturnSelf();
		$this->clan->shouldReceive("first_or_die")->twice()->andReturn($member);
		
		$this->clan->shouldReceive("can_kick_member")->with($this->character, $member)->once()->andReturn(false);
		
		$this->clan->shouldReceive("get_id")->twice()->andReturn(1);
		
		$response = $this->post("authenticated/clan/kick");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("error", "No puedes expulsar miembros en este momento");
		
		$this->clan->shouldReceive("can_kick_member")->with($this->character, $member)->once()->andReturn(true);
		$this->clan->shouldReceive("kick_member")->with($this->character, $member)->once();
		
		$member->shouldReceive("get_name")->once()->andReturn("bar");
		
		$response = $this->post("authenticated/clan/kick");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("success", "Expulsaste a bar del grupo exitosamente");
	}
	
	public function testRechazarPeticion()
	{
		$this->assertHasFilter("post", "authenticated/clan/petitions/reject", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/petitions/reject", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/petitions/reject", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("can_reject_petitions")->with($this->character)->once()->andReturn(false);
		
		$this->clan->shouldReceive("get_id")->twice()->andReturn(1);
		
		$response = $this->post("authenticated/clan/petitions/reject");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("error", "No puedes rechazar peticiones");
		
		$this->clan->shouldReceive("can_reject_petitions")->with($this->character)->once()->andReturn(true);
		
		$petition = m::mock("ClanPetition");
		
		Input::replace(array("id" => 1));
		
		$this->clan->shouldReceive("petitions->find_or_die")->with(1)->andReturn($petition);
		$this->clan->shouldReceive("reject_petition")->with($this->character, $petition);
		
		$response = $this->post("authenticated/clan/petitions/reject");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("success", "Rechazaste la peticion exitosamente");
	}
	
	public function testAceptarPeticion()
	{
		$this->assertHasFilter("post", "authenticated/clan/petitions/accept", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/petitions/accept", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/petitions/accept", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("can_accept_petitions")->with($this->character)->once()->andReturn(false);
		
		$this->clan->shouldReceive("get_id")->twice()->andReturn(1);
		
		$response = $this->post("authenticated/clan/petitions/accept");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("error", "No puedes aceptar peticiones");
		
		$this->clan->shouldReceive("can_accept_petitions")->with($this->character)->once()->andReturn(true);
		
		$petition = m::mock("ClanPetition");
		
		Input::replace(array("id" => 1));
		
		$this->clan->shouldReceive("petitions->find_or_die")->with(1)->andReturn($petition);
		$this->clan->shouldReceive("accept_petition")->with($this->character, $petition);
		
		$response = $this->post("authenticated/clan/petitions/accept");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("success", "Aceptaste la peticion exitosamente");
	}
	
	public function testNuevaPeticion()
	{
		$this->assertHasFilter("post", "authenticated/clan/petitions/new", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/petitions/new", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->times(3)->andReturnSelf();
		$this->character->shouldReceive("can_enter_in_clan")->once()->andReturn(false);
		
		$response = $this->post("authenticated/clan/petitions/new");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("error", "No puedes entrar en un grupo en este momento");
		
		$this->character->shouldReceive("can_enter_in_clan")->twice()->andReturn(true);
		
		Input::replace(array("id" => 2));
		
		$this->clan->shouldReceive("find_or_die")->with(2)->twice()->andReturnSelf();
		$this->clan->shouldReceive("can_send_petition")->with($this->character)->once()->andReturn(false);
		
		$response = $this->post("authenticated/clan/petitions/new");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("error", "Ya tienes una peticion pendiente con este grupo, debes esperar a que sea respondida");
		
		$this->clan->shouldReceive("can_send_petition")->with($this->character)->once()->andReturn(true);
		$this->clan->shouldReceive("send_petition")->with($this->character)->once();
		
		$response = $this->post("authenticated/clan/petitions/new");
		$this->assertRedirect(URL::to_route("get_authenticated_index"), $response);
		$this->assertSessionHas("success", "Haz enviado exitosamente la peticion para la inclusion en este grupo");
	}
	
	public function testModificarPermisos()
	{
		$this->assertHasFilter("post", "authenticated/clan/permissions", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/permissions", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/permissions", "before", "hasClan");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$this->clan->shouldReceive("can_modify_permissions")->with($this->character)->once()->andReturn(false);
		$this->clan->shouldReceive("get_id")->twice()->andReturn(1);
		
		$response = $this->post("authenticated/clan/permissions");
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		
		Input::replace(array("id" => 5));
		
		$this->clan->shouldReceive("can_modify_permissions")->with($this->character)->once()->andReturn(true);
		
		$member = m::mock("Character");
		$this->clan->shouldReceive("members->find_or_die")->with(5)->once()->andReturn($member);
		$member->shouldReceive("set_permission")->times(6);
		$member->shouldReceive("save")->once();
		
		$response = $this->post("authenticated/clan/permissions");
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("success", "Permisos modificados correctamente");
	}
	
	public function testMostrar()
	{
		$this->assertHasFilter("get", "authenticated/clan/show/1", "before", "auth");
		$this->assertHasFilter("get", "authenticated/clan/show/1", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		
		$this->clan->shouldReceive("same_server")->twice()->andReturnSelf();
		$this->clan->shouldReceive("find_or_die")->with(1)->twice()->andReturnSelf();
		$this->clan->shouldReceive("members->get")->twice()->andReturn(array());
		$this->clan->shouldReceive("get_skills")->twice()->andReturnSelf();
		$this->clan->shouldReceive("where_level")->with(1)->twice()->andReturnSelf();
		$this->clan->shouldReceive("get")->twice()->andReturn(array());
		$this->clan->shouldReceive("can_see_petitions")->with($this->character)->once()->andReturn(false);
		$this->clan->shouldReceive("get_name")->twice()->andReturn("foo");
		
		$response = $this->get("authenticated/clan/show/1");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"character" => $this->character,
			"clan" => $this->clan,
			"members" => array(),
			"skills" => array()
		));
		
		$this->clan->shouldReceive("can_see_petitions")->with($this->character)->once()->andReturn(true);
		$this->clan->shouldReceive("petitions->get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/clan/show/1");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"character" => $this->character,
			"clan" => $this->clan,
			"members" => array(),
			"skills" => array()
		));
	}
	
	public function testDarLiderazgo()
	{
		$this->assertHasFilter("post", "authenticated/clan/leader", "before", "auth");
		$this->assertHasFilter("post", "authenticated/clan/leader", "before", "hasNoCharacter");
		$this->assertHasFilter("post", "authenticated/clan/leader", "before", "hasClan");
		
		Input::replace(array("id" => 3));
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("clan->first_or_die")->twice()->andReturn($this->clan);
		
		$member = m::mock("Character");
		$this->clan->shouldReceive("members->find_or_die")->twice()->with(3)->andReturn($member);
		
		$this->character->shouldReceive("can_give_leadership_to")->once()->with($member)->andReturn(false);
		
		$this->clan->shouldReceive("get_id")->twice()->andReturn(1);
		
		$response = $this->post("authenticated/clan/leader");
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		
		$this->character->shouldReceive("can_give_leadership_to")->once()->with($member)->andReturn(true);
		$this->character->shouldReceive("give_leadership_to")->once()->with($member);
		
		$member->shouldReceive("get_name")->once()->andReturn("foo");
		
		$response = $this->post("authenticated/clan/leader");
		
		$this->assertRedirect(URL::to_route("get_authenticated_clan_show", array(1)), $response);
		$this->assertSessionHas("success", "Le haz dado el liderazgo del grupo a foo");
	}
}