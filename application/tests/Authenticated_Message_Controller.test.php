<?php

use Mockery as m;

class Authenticated_Message_Controller_Test extends Tests\TestHelper
{
	protected $character;
	protected $message;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->character = m::mock("Character");
		IoC::instance("Character", $this->character);
		
		$this->message = m::mock("Message");
		IoC::instance("Message", $this->message);
        
        $this->purify = m::mock("HTMLPurifier");
        IoC::instance("HTMLPurifier", $this->purify);
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		IoC::unregister("Character");
		IoC::unregister("Message");
        IoC::unregister("HTMLPurifier");
	}
	
	public function testIndex()
	{
		$this->assertHasFilter("get", "authenticated/message", "before", "auth");
		$this->assertHasFilter("get", "authenticated/message", "before", "hasNoCharacter");
		
		$response = $this->get("authenticated/message");
		$this->assertRedirect(URL::to_route("get_authenticated_message_category", array("received")), $response);
	}
	
	public function testCategoria()
	{
		$this->assertHasFilter("get", "authenticated/message/category/received", "before", "auth");
		$this->assertHasFilter("get", "authenticated/message/category/received", "before", "hasNoCharacter");
		
		$response = $this->get("authenticated/message/category/foo");
		$this->assertRedirect(URL::to_route("get_authenticated_message_index", array("received")), $response);
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("messages")->once()->andReturn($this->message);
		
		$this->message->shouldReceive("where_type")->once()->with("received")->andReturnSelf();
		$this->message->shouldReceive("order_by")->once()->with("unread", "desc")->andReturnSelf();
		$this->message->shouldReceive("order_by")->once()->with("date", "desc")->andReturnSelf();
		$this->message->shouldReceive("get")->once()->andReturn(array());
		
		$response = $this->get("authenticated/message/category/received");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "Mensajes",
			"character" => $this->character,
			"messages" => array(),
			"type" => "received"
		));
	}
	
	public function testLeer()
	{
		$this->assertHasFilter("get", "authenticated/message/read/1", "before", "auth");
		$this->assertHasFilter("get", "authenticated/message/read/1", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("messages")->once()->andReturn($this->message);
		
		$this->message->shouldReceive("find_or_die")->once()->with(1)->andReturnSelf();
		$this->message->shouldReceive("set_unread")->once()->with(false);
		$this->message->shouldReceive("save")->once();
		$this->message->shouldReceive("get_subject")->once()->andReturn("foo");
		
		$response = $this->get("authenticated/message/read/1");
		
		$this->assertResponseOk($response);
		$this->assertViewHasAll($response, array(
			"title" => "foo",
			"message" => $this->message
		));
	}
	
	public function testBorrar()
	{
		$this->assertHasFilter("post", "authenticated/message/delete", "before", "auth");
		$this->assertHasFilter("post", "authenticated/message/delete", "before", "hasNoCharacter");
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("messages")->once()->andReturn($this->message);
		
		$this->message->shouldReceive("where_in")->with("id", array())->andReturnSelf();
		$this->message->shouldReceive("delete")->once();
		
		$response = $this->post("authenticated/message/delete");
		$this->assertRedirect(URL::base()."/", $response);
	}
	
	public function testBorrarTodos()
	{
		$this->assertHasFilter("post", "authenticated/message/clear", "before", "auth");
		$this->assertHasFilter("post", "authenticated/message/clear", "before", "hasNoCharacter");
		
		Input::replace(array("type" => "foo"));
		
		$response = $this->post("authenticated/message/clear");
		$this->assertRedirect(URL::to_route("get_authenticated_message_index"), $response);
		
		Input::replace(array("type" => "received"));
		
		$this->character->shouldReceive("get_logged")->once()->andReturnSelf();
		$this->character->shouldReceive("messages")->once()->andReturn($this->message);
		
		$this->message->shouldReceive("where_type")->once()->with("received")->andReturnSelf();
		$this->message->shouldReceive("delete")->once();
		
		$response = $this->post("authenticated/message/clear");
		$this->assertRedirect(URL::to_route("get_authenticated_message_index"), $response);
	}
	
	public function testEnviar()
	{
		$this->assertHasFilter("get", "authenticated/message/send", "before", "auth");
		$this->assertHasFilter("get", "authenticated/message/send", "before", "hasNoCharacter");
		
        $this->purify->shouldReceive("purify")->atLeast(1);
        
		$response = $this->get("authenticated/message/send");
		
		$this->assertResponseOk($response);
		$this->assertViewHas($response, "title", "Enviar mensaje");
		
		$this->assertHasFilter("post", "authenticated/message/send", "before", "auth");
		$this->assertHasFilter("post", "authenticated/message/send", "before", "hasNoCharacter");
		
		Input::replace(array("to" => "foo"));
		
		$receiver = m::mock("Character");
		$this->character->shouldReceive("where_name")->twice()->with("foo")->andReturn($receiver);
		$receiver->shouldReceive("first_or_empty")->twice()->andReturnSelf();
		$receiver->shouldReceive("get_id")->twice()->andReturn(2);
		
		$this->character->shouldReceive("get_logged")->twice()->andReturnSelf();
		$this->character->shouldReceive("get_id")->twice()->andReturn(1);
		
		$this->message->shouldReceive("create_instance")->twice()->andReturnSelf();
		$this->message->shouldReceive("validate")->once()->andReturn(false);
		$this->message->shouldReceive("errors->all")->once()->andReturn(array());
		
		$response = $this->post("authenticated/message/send");
		
		$this->assertRedirect(URL::to_route("get_authenticated_message_send"), $response);
		$this->assertWithInputs();
		$this->assertSessionHas("errors", array());
		
		$this->message->shouldReceive("validate")->once()->andReturn(true);
		$this->message->shouldReceive("save")->once();
		
		$response = $this->post("authenticated/message/send");
		
		$this->assertViewHas($response, "title", "¡Mensaje enviado exitosamente!");
	}
}