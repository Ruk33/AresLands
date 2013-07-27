<?php

class Message extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'messages';
	public static $key = 'id';

	protected $rules = array(
		//'receiver_id' => 'exists:characters,id',
		'subject' => 'required|between:3,100',
		'content' => 'required'
	);

	protected $messages = array(
		//'receiver_id_exists' => 'El destinatario no existe',

		'subject_required' => 'El asunto del mensaje es requerido',
		'subject_between' => 'El asunto del mensaje debe tener entre 3 y 100 carácteres',

		'content_required' => 'El contenido del mensaje es requerido',
	);

	public function sender()
	{
		return $this->belongs_to('Character', 'sender_id');
	}

	public static function defense_report($receiver, $attacker, $battleMessage, $winner)
	{
		$message = new Message();

		$message->sender_id = $attacker->id;
		$message->receiver_id = $receiver->id;

		$message->subject = '¡Te han atacado!';
		$message->content = '<p>El jugador ' . $attacker->get_link() . ' te ha atacado.</p>' .
		'<p>El ganador ha sido ' . $winner->get_link() . '</p>' . 
		'<p>Dessarrollo de la pelea:</p>' .
		'<p>' . $battleMessage . '</p>';

		$message->unread = true;
		$message->date = time();
		$message->type = 'defense';

		$message->is_special = true;

		$message->save();
	}

	public static function attack_report($receiver, $target, $battleMessage, $winner)
	{
		$message = new Message();

		$message->sender_id = $receiver->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Atacaste a ' . $target->name;
		$message->content = '<p>Atacaste al jugador ' . $target->get_link() . '.</p>' .
		'<p>El ganador ha sido ' . $winner->get_link() . '</p>' . 
		'<p>Dessarrollo de la pelea:</p>' .
		'<p>' . $battleMessage . '</p>';

		$message->unread = true;
		$message->date = time();
		$message->type = 'attack';

		$message->is_special = true;

		$message->save();
	}

	public static function exploration_finished($receiver, $monster, $battleMessage, $winner)
	{
		$message = new Message();

		$message->sender_id = $receiver->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Terminaste la exploración';

		if ( $winner )
		{
			$message->content = '<p>Terminaste de explorar, luchaste contra ' . $monster->name . ' y el ganador fue ' . $winner->name . '</p><p>Dessarrollo de la pelea:</p><p>' . $battleMessage . '</p>';
		}
		else
		{
			$message->content = '<p>Terminaste de explorar, luchaste contra ' . $monster->name . '. El resultado ha sido un empate!.</p><p>Dessarrollo de la pelea:</p><p>' . $battleMessage . '</p>';	
		}

		$message->unread = true;
		$message->date = time();

		$message->is_special = true;
		$message->type = 'defense';

		$message->save();
	}

	/**
	 *	@param <Character> $sender
	 *	@param <Character> $receiver
	 */
	public static function clan_expulsion_message($sender, $receiver)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Haz sido expulsado del grupo';
		$message->content = 'Se te ha expulsado del grupo';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function clan_accept_message($sender, $receiver, $clan)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Tu solicitud ha sido aceptada';
		$message->content = 'Tu solicitud para ingresar al grupo ' . $clan->name . ' ha sido aceptada';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function clan_reject_message($sender, $receiver, $clan)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Tu solicitud ha sido rechazada';
		$message->content = 'Tu solicitud para ingresar al grupo ' . $clan->name . ' ha sido rechazada';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function clan_new_petition($sender, $receiver)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Nueva petición';
		$message->content = 'El personaje ' . $sender->name . ' ha solicitado inclusión en tu grupo';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function trade_new($sender, $receiver)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Nuevo comercio';
		$message->content = 'El personaje ' . $sender->name . ' ha iniciado un nuevo comercio contigo';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}
}