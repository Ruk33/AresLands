<?php

class Authenticated_Message_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var Message
	 */
	protected $message;

	public static function register_routes()
	{
		Route::get("authenticated/message", array(
			"uses" => "authenticated.message@index",
			"as"   => "get_authenticated_message_index"
		));

		Route::get("authenticated/message/category/(:any)", array(
			"uses" => "authenticated.message@category",
			"as"   => "get_authenticated_message_category"
		));
		
		Route::get("authenticated/message/read/(:num)", array(
			"uses" => "authenticated.message@read",
			"as"   => "get_authenticated_message_read"
		));
		
		Route::post("authenticated/message/delete", array(
			"uses" => "authenticated.message@delete",
			"as"   => "post_authenticated_message_delete"
		));
		
		Route::post("authenticated/message/clear", array(
			"uses" => "authenticated.message@clear",
			"as"   => "post_authenticated_message_clear"
		));
		
		Route::get("authenticated/message/send/(:any?)", array(
			"uses" => "authenticated.message@send",
			"as"   => "get_authenticated_message_send"
		));
		
		Route::post("authenticated/message/send", array(
			"uses" => "authenticated.message@send",
			"as"   => "post_authenticated_message_send"
		));
	}
	
	/**
	 * 
	 * @param Message $message
	 * @param Character $character
	 */
	public function __construct(Message $message, Character $character)
	{
		parent::__construct();
		
		$this->message = $message;
		$this->character = $character;
	}
	
	/**
	 * Hacemos que el index redireccione a la categoria "recibidos"
	 * 
	 * @return Redirect
	 */
	public function get_index()
	{
		return Laravel\Redirect::to_route("get_authenticated_message_category", array("received"));
	}

	/**
	 * Mostramos los mensajes que hay en
	 * una categoria del personaje logueado
	 * 
	 * @param string $type
	 * @return Response
	 */
	public function get_category($type)
	{
		$type = strtolower($type);
		
		if ( ! in_array($type, array("received", "attack", "defense")) )
		{
			return Laravel\Redirect::to_route("get_authenticated_message_index");
		}
		
		$character = $this->character->get_logged();
		$messages = $character->messages()
							  ->where_type($type)
							  ->order_by("unread", "desc")
							  ->order_by("date", "desc")
							  ->get();
		
		$this->layout->title = "Mensajes";
		$this->layout->content = View::make('authenticated.messages', compact("character", "messages", "type"));	
	}
	
	public function get_read($id)
	{
		$character = $this->character->get_logged();
		$message = $character->messages()->find_or_die($id);
		
		$message->unread = false;
		$message->save();
		
		$this->layout->title = $message->subject;
		$this->layout->content = View::make('authenticated.readmessage', compact("message"));
	}
	
	public function post_delete()
	{
		$character = $this->character->get_logged();
		$messages = $character->messages()->where_in("id", (array) Input::get("messages"));
		
		$messages->delete();
		
		return Laravel\Redirect::to_route("get_authenticated_message_index");
	}
	
	/**
	 * Borramos todos los mensajes del personaje logueado
	 * de un especifico tipo
	 * 
	 * @return Redirect
	 */
	public function post_clear()
	{
		$type = strtolower(Input::get("type"));
		
		if ( in_array($type, array("received", "attack", "defense")) )
		{
			$character = $this->character->get_logged();
			$character->messages()->where_type($type)->delete();
		}

		return Laravel\Redirect::to_route("get_authenticated_message_index");
	}
	
	public function get_send()
	{
		$to = Input::get("to", "");
		$subject = Input::get("subject", "");
		
		$this->layout->title = "Enviar mensaje";
		$this->layout->content = View::make("authenticated.sendmessage", compact("to", "subject"));
	}
	
	public function post_send()
	{		
		$sender = $this->character->get_logged();
		$receiver = $this->character->where_name(Input::get("to"))->first_or_empty();
		
		$attributes = array_merge(
			array(
				"sender_id"   => $sender->id,
				"receiver_id" => $receiver->id,
				"date"        => time(),
				"unread"      => true
			), 
			Input::only(array("subject", "content"))
		);
		
		$message = $this->message->create_instance($attributes);
		
		if ( ! $message->validate() )
		{
			Session::flash("errors", $message->errors->all());
			return Laravel\Redirect::to_route("get_authenticated_message_send")->with_input();
		}
		
		$message->save();
		
		$this->layout->title = "¡Mensaje enviado exitosamente!";
		$this->layout->content = View::make("authenticated.messagesent");
	}
}